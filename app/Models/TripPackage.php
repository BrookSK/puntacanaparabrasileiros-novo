<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TripPackage extends Model
{
    protected string $table = 'trip_packages';
    protected array $fillable = ['trip_id', 'title', 'description', 'sort_order', 'status'];

    public function getByTrip(int $tripId): array
    {
        return $this->where('trip_id = ?', [$tripId], 'sort_order ASC');
    }

    /**
     * Retorna categorias de preço do pacote com dados do traveler_category.
     */
    public function getCategories(int $packageId): array
    {
        return $this->db->fetchAll(
            "SELECT tpc.*, tc.name as category_name, tc.slug as category_slug, tc.age_group
             FROM trip_package_categories tpc
             INNER JOIN traveler_categories tc ON tpc.traveler_category_id = tc.id
             WHERE tpc.package_id = ?
             ORDER BY tc.sort_order ASC",
            [$packageId]
        );
    }

    /**
     * Salva/atualiza categorias de preço de um pacote.
     */
    public function syncCategories(int $packageId, array $categories): void
    {
        $this->db->delete('trip_package_categories', 'package_id = ?', [$packageId]);
        foreach ($categories as $cat) {
            $this->db->insert('trip_package_categories', [
                'package_id' => $packageId,
                'traveler_category_id' => (int) $cat['traveler_category_id'],
                'price' => (float) $cat['price'],
                'sale_price' => isset($cat['sale_price']) ? (float) $cat['sale_price'] : null,
                'min_pax' => (int) ($cat['min_pax'] ?? 0),
                'max_pax' => isset($cat['max_pax']) ? (int) $cat['max_pax'] : null,
            ]);
        }
    }

    /**
     * Retorna o preço base (menor preço entre as categorias, considerando regras dinâmicas).
     */
    public function getBasePrice(int $packageId): float
    {
        // "A partir de" = MENOR preço da semana da faixa principal (a 1ª faixa paga,
        // normalmente Adulto), comparando o preço normal com as regras por dia/data.
        // Assim a listagem e a página mostram o mesmo valor; o preço do dia escolhido
        // (ex.: sexta/sábado mais caro) só aparece ao selecionar a data.
        $cats = $this->getCategories($packageId);
        foreach ($cats as $c) {
            $base = self::effective($c['price'] ?? 0, $c['sale_price'] ?? null);
            if ($base <= 0) continue;
            $minRule = $this->minRulePrice($packageId, (int) $c['traveler_category_id']);
            return ($minRule !== null && $minRule < $base) ? $minRule : $base;
        }
        return 0.0;
    }

    /** Menor preço (>0) das regras por dia/data de uma faixa do pacote, ou null. */
    public function minRulePrice(int $packageId, int $categoryId): ?float
    {
        try {
            $v = $this->db->fetchColumn(
                "SELECT MIN(CASE WHEN sale_price IS NOT NULL AND sale_price > 0 THEN sale_price ELSE price END)
                 FROM trip_day_pricing WHERE package_id = ? AND traveler_category_id = ? AND active = 1
                   AND COALESCE(NULLIF(sale_price, 0), price) > 0",
                [$packageId, $categoryId]
            );
        } catch (\Throwable $e) {
            return null;
        }
        return ($v !== null && $v !== false && (float) $v > 0) ? (float) $v : null;
    }

    private static function effective(mixed $price, mixed $sale): float
    {
        $p = (float) $price;
        $s = ($sale !== null && $sale !== '') ? (float) $sale : 0.0;
        return ($s > 0 && $s < $p) ? $s : $p;
    }

    /**
     * Retorna o preço "de" (maior preço regular para mostrar "de X por Y").
     */
    public function getRegularPrice(int $packageId): float
    {
        $price = $this->db->fetchColumn(
            "SELECT MIN(price) FROM trip_package_categories WHERE package_id = ? AND price > 0",
            [$packageId]
        );
        return (float) ($price ?: 0);
    }
}
