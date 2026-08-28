<?php
declare(strict_types=1);

namespace App\Services;

use Core\Database;

/**
 * Serviço de precificação dinâmica.
 * Implementa todas as regras de preço por data com prioridade:
 * 1. Data específica (DD/MM/YYYY)
 * 2. Feriado (DD/MM)
 * 3. Dia da semana (0-6, 0=domingo)
 * 4. Preço mensal (dias do mês)
 * 5. Preço anual (meses)
 * 6. Preço padrão do pacote
 */
class PricingService
{
    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Calcula o preço para uma data específica.
     * Retorna array com preço por categoria de viajante.
     */
    public function getPriceForDate(int $packageId, string $date): array
    {
        $dateObj = new \DateTime($date);
        $dayOfWeek = (int) $dateObj->format('w'); // 0=dom, 6=sab
        $dayMonth = $dateObj->format('d/m'); // DD/MM
        $fullDate = $dateObj->format('d/m/Y'); // DD/MM/YYYY
        $dayOfMonth = (int) $dateObj->format('j'); // 1-31
        $month = (int) $dateObj->format('n'); // 1-12

        // Buscar todas as categorias do pacote com preço padrão
        $categories = $this->db->fetchAll(
            "SELECT tpc.*, tc.name as category_name, tc.slug as category_slug, tc.age_group
             FROM trip_package_categories tpc
             INNER JOIN traveler_categories tc ON tpc.traveler_category_id = tc.id
             WHERE tpc.package_id = ?
             ORDER BY tc.sort_order ASC",
            [$packageId]
        );

        $result = [];
        foreach ($categories as $cat) {
            $categoryId = (int) $cat['traveler_category_id'];
            $price = $this->resolvePriceForCategory($packageId, $categoryId, $cat, $fullDate, $dayMonth, $dayOfWeek, $dayOfMonth, $month);
            $result[] = [
                'traveler_category_id' => $categoryId,
                'category_name' => $cat['category_name'],
                'category_slug' => $cat['category_slug'],
                'age_group' => $cat['age_group'],
                'price' => $price['price'],
                'sale_price' => $price['sale_price'],
                'effective_price' => $price['sale_price'] ?? $price['price'],
                'rule_applied' => $price['rule_applied'],
                'min_pax' => (int) $cat['min_pax'],
                'max_pax' => $cat['max_pax'] ? (int) $cat['max_pax'] : null,
            ];
        }

        return $result;
    }

    /**
     * Resolve o preço de uma categoria específica seguindo a prioridade.
     */
    private function resolvePriceForCategory(
        int $packageId,
        int $categoryId,
        array $defaultCat,
        string $fullDate,
        string $dayMonth,
        int $dayOfWeek,
        int $dayOfMonth,
        int $month
    ): array {
        // 1. Data específica (DD/MM/YYYY) — MAIOR PRIORIDADE
        $specific = $this->getDayPricing($packageId, $categoryId, 'specific', $fullDate);
        if ($specific) {
            return ['price' => (float) $specific['price'], 'sale_price' => $specific['sale_price'] ? (float) $specific['sale_price'] : null, 'rule_applied' => 'specific'];
        }

        // 2. Feriado (DD/MM)
        $holiday = $this->getDayPricing($packageId, $categoryId, 'holiday', $dayMonth);
        if ($holiday) {
            return ['price' => (float) $holiday['price'], 'sale_price' => $holiday['sale_price'] ? (float) $holiday['sale_price'] : null, 'rule_applied' => 'holiday'];
        }

        // 3. Dia da semana (0-6)
        $weekday = $this->getDayPricing($packageId, $categoryId, 'weekday', (string) $dayOfWeek);
        if ($weekday) {
            return ['price' => (float) $weekday['price'], 'sale_price' => $weekday['sale_price'] ? (float) $weekday['sale_price'] : null, 'rule_applied' => 'weekday'];
        }

        // 4. Preço mensal (dia do mês)
        $monthly = $this->getDayPricing($packageId, $categoryId, 'monthly', (string) $dayOfMonth);
        if ($monthly) {
            return ['price' => (float) $monthly['price'], 'sale_price' => $monthly['sale_price'] ? (float) $monthly['sale_price'] : null, 'rule_applied' => 'monthly'];
        }

        // 5. Preço anual (mês)
        $annual = $this->getDayPricing($packageId, $categoryId, 'annual', (string) $month);
        if ($annual) {
            return ['price' => (float) $annual['price'], 'sale_price' => $annual['sale_price'] ? (float) $annual['sale_price'] : null, 'rule_applied' => 'annual'];
        }

        // 6. Preço padrão do pacote
        return [
            'price' => (float) $defaultCat['price'],
            'sale_price' => $defaultCat['sale_price'] ? (float) $defaultCat['sale_price'] : null,
            'rule_applied' => 'default',
        ];
    }

    /**
     * Busca regra de pricing ativa para os parâmetros dados.
     */
    private function getDayPricing(int $packageId, int $categoryId, string $ruleType, string $dayKey): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM trip_day_pricing
             WHERE package_id = ? AND traveler_category_id = ? AND rule_type = ? AND day_key = ? AND active = 1
             LIMIT 1",
            [$packageId, $categoryId, $ruleType, $dayKey]
        );
    }

    /**
     * Calcula preço total de um booking item (trip + pax + extras + desconto grupo).
     * @param int|null $compositionPackageId ID do pacote de composição selecionado (opcional)
     * @param int $companionCount Quantidade de acompanhantes (opcional)
     */
    public function calculateItemTotal(int $packageId, string $date, array $paxByCategory, array $extraServiceIds = [], ?int $compositionPackageId = null, int $companionCount = 0): array
    {
        $totalPax = array_sum(array_map('intval', $paxByCategory));

        // Buscar dados do trip
        $package = $this->db->fetchOne("SELECT trip_id FROM trip_packages WHERE id = ?", [$packageId]);
        $trip = $package ? $this->db->fetchOne(
            "SELECT group_pricing_enabled, group_pricing, composition_pricing_enabled FROM trips WHERE id = ?",
            [$package['trip_id']]
        ) : null;

        // ─── MODO COMPOSITION PRICING ───
        // Se composition pricing está ativo e um pacote foi selecionado
        if ($trip && $trip['composition_pricing_enabled'] && $compositionPackageId) {
            $compPkg = $this->db->fetchOne(
                "SELECT * FROM trip_composition_packages WHERE id = ? AND trip_id = ? AND status = 'active'",
                [$compositionPackageId, $package['trip_id']]
            );

            if ($compPkg) {
                $subtotal = (float) $compPkg['price'];
                $breakdown = [[
                    'category_name' => $compPkg['label'] ?: ($compPkg['pax'] . ' pessoa(s), ' . $compPkg['units'] . ' ' . ($compPkg['unit_label'] ?: 'unidade(s)')),
                    'quantity' => (int) $compPkg['pax'],
                    'unit_price' => $subtotal / max(1, (int) $compPkg['pax']),
                    'total' => $subtotal,
                    'pricing_mode' => 'composition',
                    'composition_package_id' => (int) $compPkg['id'],
                    'units' => (int) $compPkg['units'],
                    'unit_label' => $compPkg['unit_label'],
                ]];

                // Serviços extras
                $extrasTotal = $this->calculateExtrasTotal($extraServiceIds, $totalPax);

                // Acompanhantes
                $companionTotal = $this->calculateCompanionTotal($package['trip_id'], $companionCount);

                $total = $subtotal + $extrasTotal + $companionTotal;

                return [
                    'subtotal' => $subtotal,
                    'extras_total' => $extrasTotal,
                    'companion_total' => $companionTotal,
                    'companion_count' => $companionCount,
                    'group_discount' => 0.0,
                    'total' => max(0, $total),
                    'breakdown' => $breakdown,
                    'total_pax' => (int) $compPkg['pax'],
                    'pricing_mode' => 'composition',
                    'composition_package_id' => (int) $compPkg['id'],
                ];
            }
        }

        // ─── MODO GROUP PRICING ───
        // Buscar categorias do pacote para identificar quem é adulto/criança/infantil
        $prices = $this->getPriceForDate($packageId, $date);

        $groupPricingEnabled = $trip && $trip['group_pricing_enabled'] && ($trip['group_pricing'] ?? null);

        if ($groupPricingEnabled) {
            $gpRules = json_decode($trip['group_pricing'], true);
            if (!is_array($gpRules) || empty($gpRules)) {
                $groupPricingEnabled = false;
            }
        }

        if ($groupPricingEnabled) {
            // Modo GROUP PRICING: tabela de grupo aplica APENAS para adultos
            // Criança e infantil usam preço por pessoa normal
            $adultPax = 0;
            $childTotal = 0.0;
            $breakdown = [];

            foreach ($prices as $catPrice) {
                $catId = $catPrice['traveler_category_id'];
                $quantity = (int) ($paxByCategory[$catId] ?? 0);
                if ($quantity <= 0) continue;

                $slug = strtolower($catPrice['category_slug'] ?? '');

                if ($slug === 'adulto') {
                    $adultPax += $quantity;
                } else {
                    // Criança, infantil, etc. — preço normal por pessoa
                    $lineTotal = $catPrice['effective_price'] * $quantity;
                    $childTotal += $lineTotal;
                    $breakdown[] = [
                        'category_name' => $catPrice['category_name'],
                        'quantity' => $quantity,
                        'unit_price' => $catPrice['effective_price'],
                        'total' => $lineTotal,
                    ];
                }
            }

            // Resolver preço de grupo para os adultos
            $groupPrice = $this->resolveGroupPriceFromRules($gpRules, $adultPax);
            if ($groupPrice !== null && $adultPax > 0) {
                $breakdown = array_merge([[
                    'category_name' => 'Adulto' . ($adultPax > 1 ? 's' : '') . ' (' . $adultPax . ')',
                    'quantity' => $adultPax,
                    'unit_price' => $groupPrice / max(1, $adultPax),
                    'total' => $groupPrice,
                    'pricing_mode' => 'group_fixed',
                ]], $breakdown);

                $subtotal = $groupPrice + $childTotal;
            } else {
                // Fallback: adultos com preço normal por pessoa
                foreach ($prices as $catPrice) {
                    $catId = $catPrice['traveler_category_id'];
                    $quantity = (int) ($paxByCategory[$catId] ?? 0);
                    if ($quantity <= 0) continue;
                    $slug = strtolower($catPrice['category_slug'] ?? '');
                    if ($slug === 'adulto') {
                        $lineTotal = $catPrice['effective_price'] * $quantity;
                        $childTotal += $lineTotal;
                        $breakdown = array_merge([[
                            'category_name' => $catPrice['category_name'],
                            'quantity' => $quantity,
                            'unit_price' => $catPrice['effective_price'],
                            'total' => $lineTotal,
                        ]], $breakdown);
                    }
                }
                $subtotal = $childTotal;
            }

            // Serviços extras
            $extrasTotal = $this->calculateExtrasTotal($extraServiceIds, $totalPax);

            // Acompanhantes
            $companionTotal = $this->calculateCompanionTotal($package['trip_id'], $companionCount);

            $total = $subtotal + $extrasTotal + $companionTotal;

            return [
                'subtotal' => $subtotal,
                'extras_total' => $extrasTotal,
                'companion_total' => $companionTotal,
                'companion_count' => $companionCount,
                'group_discount' => 0.0,
                'total' => max(0, $total),
                'breakdown' => $breakdown,
                'total_pax' => $totalPax,
                'pricing_mode' => 'group_fixed',
            ];
        }

        // Modo PER-PERSON: preço por categoria × quantidade
        $subtotal = 0.0;
        $breakdown = [];

        foreach ($prices as $catPrice) {
            $catId = $catPrice['traveler_category_id'];
            $quantity = (int) ($paxByCategory[$catId] ?? 0);
            if ($quantity <= 0) continue;

            $lineTotal = $catPrice['effective_price'] * $quantity;
            $subtotal += $lineTotal;
            $breakdown[] = [
                'category_name' => $catPrice['category_name'],
                'quantity' => $quantity,
                'unit_price' => $catPrice['effective_price'],
                'total' => $lineTotal,
            ];
        }

        // Serviços extras
        $extrasTotal = $this->calculateExtrasTotal($extraServiceIds, $totalPax);

        // Desconto de grupo (percentual)
        $groupDiscount = $this->calculateGroupDiscount($packageId, $totalPax, $subtotal);

        // Acompanhantes
        $companionTotal = $this->calculateCompanionTotal($package['trip_id'], $companionCount);

        $total = $subtotal + $extrasTotal - $groupDiscount + $companionTotal;

        return [
            'subtotal' => $subtotal,
            'extras_total' => $extrasTotal,
            'companion_total' => $companionTotal,
            'companion_count' => $companionCount,
            'group_discount' => $groupDiscount,
            'total' => max(0, $total),
            'breakdown' => $breakdown,
            'total_pax' => $totalPax,
            'pricing_mode' => 'per_person',
        ];
    }

    /**
     * Resolve o preço fixo de grupo a partir das regras JSON para X adultos.
     */
    private function resolveGroupPriceFromRules(array $rules, int $adultPax): ?float
    {
        if ($adultPax <= 0) return null;

        // Match exato
        foreach ($rules as $rule) {
            if ((int) ($rule['pax'] ?? 0) === $adultPax) {
                return (float) $rule['price'];
            }
        }

        // Nearest lower
        usort($rules, fn($a, $b) => (int) ($a['pax'] ?? 0) - (int) ($b['pax'] ?? 0));
        $bestMatch = null;
        foreach ($rules as $rule) {
            if ((int) ($rule['pax'] ?? 0) <= $adultPax) {
                $bestMatch = $rule;
            }
        }

        if ($bestMatch) {
            return (float) $bestMatch['price'];
        }

        // Fallback: menor regra
        return !empty($rules[0]) ? (float) $rules[0]['price'] : null;
    }

    /**
     * Resolve o preço fixo de grupo para o número de passageiros dado.
     * Retorna null se group pricing não estiver ativo ou não houver regra para esse número.
     */
    public function resolveGroupPricing(int $packageId, int $totalPax): ?array
    {
        $package = $this->db->fetchOne("SELECT trip_id FROM trip_packages WHERE id = ?", [$packageId]);
        if (!$package) return null;

        $trip = $this->db->fetchOne(
            "SELECT group_pricing_enabled, group_pricing FROM trips WHERE id = ?",
            [$package['trip_id']]
        );

        if (!$trip || !$trip['group_pricing_enabled'] || !$trip['group_pricing']) {
            return null;
        }

        $rules = json_decode($trip['group_pricing'], true);
        if (!is_array($rules) || empty($rules)) return null;

        // Buscar regra exata para o número de passageiros
        foreach ($rules as $rule) {
            if ((int) ($rule['pax'] ?? 0) === $totalPax) {
                return ['price' => (float) $rule['price'], 'pax' => $totalPax, 'match' => 'exact'];
            }
        }

        // Se não encontrou exato, usar a regra do maior pax menor ou igual ao total
        usort($rules, fn($a, $b) => (int) ($a['pax'] ?? 0) - (int) ($b['pax'] ?? 0));

        $bestMatch = null;
        foreach ($rules as $rule) {
            $rulePax = (int) ($rule['pax'] ?? 0);
            if ($rulePax <= $totalPax) {
                $bestMatch = $rule;
            }
        }

        if ($bestMatch) {
            return ['price' => (float) $bestMatch['price'], 'pax' => (int) $bestMatch['pax'], 'match' => 'nearest_lower'];
        }

        // Fallback: usar a menor regra disponível (para 1 passageiro se existir)
        $smallest = $rules[0] ?? null;
        if ($smallest) {
            return ['price' => (float) $smallest['price'], 'pax' => (int) $smallest['pax'], 'match' => 'fallback'];
        }

        return null;
    }

    /**
     * Calcula o total dos serviços extras.
     */
    private function calculateExtrasTotal(array $extraServiceIds, int $totalPax): float
    {
        if (empty($extraServiceIds)) return 0.0;

        $placeholders = implode(',', array_fill(0, count($extraServiceIds), '?'));
        $extras = $this->db->fetchAll(
            "SELECT * FROM trip_extra_services WHERE id IN ({$placeholders})",
            array_values($extraServiceIds)
        );

        $extrasTotal = 0.0;
        foreach ($extras as $extra) {
            $extraPrice = match ($extra['price_type']) {
                'per_person' => (float) $extra['price'] * $totalPax,
                'per_group' => (float) $extra['price'],
                'fixed' => (float) $extra['price'],
                default => (float) $extra['price'],
            };
            $extrasTotal += $extraPrice;
        }

        return $extrasTotal;
    }

    /**
     * Calcula o total de acompanhantes.
     */
    private function calculateCompanionTotal(int $tripId, int $companionCount): float
    {
        if ($companionCount <= 0) return 0.0;

        $trip = $this->db->fetchOne(
            "SELECT companion_enabled, companion_price FROM trips WHERE id = ?",
            [$tripId]
        );

        if (!$trip || !$trip['companion_enabled'] || !$trip['companion_price']) {
            return 0.0;
        }

        return (float) $trip['companion_price'] * $companionCount;
    }

    /**
     * Retorna a tabela de group pricing de um trip (via packageId).
     * Retorna null se não estiver ativo.
     */
    public function getGroupPricingTable(int $packageId): ?array
    {
        $package = $this->db->fetchOne("SELECT trip_id FROM trip_packages WHERE id = ?", [$packageId]);
        if (!$package) return null;

        $trip = $this->db->fetchOne(
            "SELECT group_pricing_enabled, group_pricing FROM trips WHERE id = ?",
            [$package['trip_id']]
        );

        if (!$trip || !$trip['group_pricing_enabled'] || !$trip['group_pricing']) {
            return null;
        }

        $rules = json_decode($trip['group_pricing'], true);
        if (!is_array($rules) || empty($rules)) return null;

        // Ordenar por pax
        usort($rules, fn($a, $b) => (int) ($a['pax'] ?? 0) - (int) ($b['pax'] ?? 0));

        return $rules;
    }

    /**
     * Retorna os pacotes de composição ativos de um trip (via packageId).
     * Retorna null se composition pricing não estiver ativo.
     */
    public function getCompositionPackages(int $packageId): ?array
    {
        $package = $this->db->fetchOne("SELECT trip_id FROM trip_packages WHERE id = ?", [$packageId]);
        if (!$package) return null;

        $trip = $this->db->fetchOne(
            "SELECT composition_pricing_enabled FROM trips WHERE id = ?",
            [$package['trip_id']]
        );

        if (!$trip || !$trip['composition_pricing_enabled']) {
            return null;
        }

        $packages = $this->db->fetchAll(
            "SELECT id, label, pax, units, unit_label, pax_per_unit, price, sort_order
             FROM trip_composition_packages
             WHERE trip_id = ? AND status = 'active'
             ORDER BY sort_order ASC, pax ASC",
            [$package['trip_id']]
        );

        return !empty($packages) ? $packages : null;
    }

    /**
     * Calcula desconto de grupo baseado nas regras do trip.
     */
    private function calculateGroupDiscount(int $packageId, int $totalPax, float $subtotal): float
    {
        $package = $this->db->fetchOne("SELECT trip_id FROM trip_packages WHERE id = ?", [$packageId]);
        if (!$package) return 0.0;

        $trip = $this->db->fetchOne(
            "SELECT group_discount_enabled, group_discount_rules FROM trips WHERE id = ?",
            [$package['trip_id']]
        );

        if (!$trip || !$trip['group_discount_enabled'] || !$trip['group_discount_rules']) {
            return 0.0;
        }

        $rules = json_decode($trip['group_discount_rules'], true);
        if (!is_array($rules)) return 0.0;

        // Regras: [{"min_pax": 5, "discount_percent": 10}, ...]
        usort($rules, fn($a, $b) => ($b['min_pax'] ?? 0) - ($a['min_pax'] ?? 0));
        foreach ($rules as $rule) {
            if ($totalPax >= ($rule['min_pax'] ?? 0)) {
                $percent = (float) ($rule['discount_percent'] ?? 0);
                return $subtotal * ($percent / 100);
            }
        }

        return 0.0;
    }
}
