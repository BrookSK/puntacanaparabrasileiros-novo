<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TransferVehicle extends Model
{
    protected string $table = 'transfer_vehicles';
    protected array $fillable = [
        'title', 'slug', 'description', 'image', 'vehicle_type',
        'max_passengers', 'max_adults', 'max_children', 'max_infants',
        'max_luggage', 'amenities', 'sort_order', 'status',
    ];

    public function getActive(): array
    {
        return $this->where("status = 'active'", [], 'sort_order ASC');
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->findWhere('slug', $slug);
    }

    public function getRoutes(int $vehicleId): array
    {
        return $this->db->fetchAll(
            "SELECT tr.*, tlo.title as origin_title, tld.title as destination_title
             FROM transfer_routes tr
             INNER JOIN transfer_locations tlo ON tr.origin_id = tlo.id
             INNER JOIN transfer_locations tld ON tr.destination_id = tld.id
             WHERE tr.vehicle_id = ?
             ORDER BY tlo.title ASC",
            [$vehicleId]
        );
    }

    public function getTariffs(int $routeId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM transfer_tariffs WHERE route_id = ? ORDER BY min_pax ASC",
            [$routeId]
        );
    }

    /**
     * Busca veículos disponíveis para uma rota específica.
     */
    public function searchAvailable(int $originId, int $destinationId, int $totalPax, string $serviceType): array
    {
        $sql = "SELECT tv.*, tr.id as route_id, tr.base_price, tr.duration, tr.distance_km
                FROM transfer_vehicles tv
                INNER JOIN transfer_routes tr ON tv.id = tr.vehicle_id
                WHERE tr.origin_id = ? AND tr.destination_id = ?
                AND tv.status = 'active' AND tr.status = 1
                AND tv.max_passengers >= ?
                ORDER BY tv.sort_order ASC";

        $vehicles = $this->db->fetchAll($sql, [$originId, $destinationId, $totalPax]);

        // Tentar rota inversa se não encontrou
        if (empty($vehicles)) {
            $sql = str_replace(
                'tr.origin_id = ? AND tr.destination_id = ?',
                'tr.origin_id = ? AND tr.destination_id = ?',
                $sql
            );
            $vehicles = $this->db->fetchAll($sql, [$destinationId, $originId, $totalPax]);
        }

        // Calcular preço por faixa para cada veículo
        $result = [];
        foreach ($vehicles as $vehicle) {
            $price = $this->calculatePrice((int) $vehicle['route_id'], $totalPax, $serviceType);
            if ($price !== null) {
                $vehicle['calculated_price'] = $price;
                $result[] = $vehicle;
            }
        }

        return $result;
    }

    /**
     * Calcula o preço de transfer baseado na lógica de faixa.
     */
    public function calculatePrice(int $routeId, int $totalPax, string $serviceType): ?float
    {
        // Buscar tarifas por faixa
        $tariffs = $this->db->fetchAll(
            "SELECT * FROM transfer_tariffs
             WHERE route_id = ? AND service_type = ?
             ORDER BY min_pax ASC",
            [$routeId, $serviceType]
        );

        if (!empty($tariffs)) {
            foreach ($tariffs as $tariff) {
                if ($totalPax >= $tariff['min_pax'] && $totalPax <= $tariff['max_pax']) {
                    return (float) $tariff['price'];
                }
            }
            // Nenhuma faixa corresponde
            return null;
        }

        // Sem tarifas por faixa — usar preço base da rota
        $route = $this->db->fetchOne("SELECT base_price FROM transfer_routes WHERE id = ?", [$routeId]);
        if ($route) {
            return (float) $route['base_price'];
        }

        return null;
    }

    public function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = mb_strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        $baseSlug = $slug;
        $counter = 1;
        while ($this->exists('slug', $slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
