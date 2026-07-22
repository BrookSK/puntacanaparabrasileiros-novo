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
     */
    public function calculateItemTotal(int $packageId, string $date, array $paxByCategory, array $extraServiceIds = []): array
    {
        $prices = $this->getPriceForDate($packageId, $date);
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
        $extrasTotal = 0.0;
        $totalPax = array_sum(array_map('intval', $paxByCategory));
        if (!empty($extraServiceIds)) {
            $placeholders = implode(',', array_fill(0, count($extraServiceIds), '?'));
            $extras = $this->db->fetchAll(
                "SELECT * FROM trip_extra_services WHERE id IN ({$placeholders})",
                array_values($extraServiceIds)
            );
            foreach ($extras as $extra) {
                $extraPrice = match ($extra['price_type']) {
                    'per_person' => (float) $extra['price'] * $totalPax,
                    'per_group' => (float) $extra['price'],
                    'fixed' => (float) $extra['price'],
                    default => (float) $extra['price'],
                };
                $extrasTotal += $extraPrice;
            }
        }

        // Desconto de grupo
        $groupDiscount = $this->calculateGroupDiscount($packageId, $totalPax, $subtotal);

        $total = $subtotal + $extrasTotal - $groupDiscount;

        return [
            'subtotal' => $subtotal,
            'extras_total' => $extrasTotal,
            'group_discount' => $groupDiscount,
            'total' => max(0, $total),
            'breakdown' => $breakdown,
            'total_pax' => $totalPax,
        ];
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
