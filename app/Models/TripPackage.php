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
     * Retorna o preço base (menor preço entre as categorias).
     */
    public function getBasePrice(int $packageId): float
    {
        $price = $this->db->fetchColumn(
            "SELECT MIN(COALESCE(sale_price, price)) FROM trip_package_categories WHERE package_id = ?",
            [$packageId]
        );
        return (float) ($price ?: 0);
    }

    /**
     * Retorna o preço "de" (maior preço regular para mostrar "de X por Y").
     */
    public function getRegularPrice(int $packageId): float
    {
        $price = $this->db->fetchColumn(
            "SELECT MIN(price) FROM trip_package_categories WHERE package_id = ?",
            [$packageId]
        );
        return (float) ($price ?: 0);
    }
}
