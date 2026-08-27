<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Pacotes de composição por passeio.
 * Permite configurar preços por combinação (pessoas + unidades/veículos).
 */
class TripCompositionPackage extends Model
{
    protected string $table = 'trip_composition_packages';
    protected array $fillable = [
        'trip_id', 'label', 'pax', 'units', 'unit_label',
        'pax_per_unit', 'price', 'sort_order', 'status',
    ];

    /**
     * Retorna todos os pacotes ativos de um passeio, ordenados.
     */
    public function getByTrip(int $tripId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE trip_id = ? AND status = 'active' ORDER BY sort_order ASC, pax ASC",
            [$tripId]
        );
    }

    /**
     * Retorna todos os pacotes de um passeio (incluindo inativos), para admin.
     */
    public function getAllByTrip(int $tripId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE trip_id = ? ORDER BY sort_order ASC, pax ASC",
            [$tripId]
        );
    }

    /**
     * Busca pacote por trip e número de passageiros.
     * Retorna todos os pacotes que atendem àquela quantidade de pax.
     */
    public function getByTripAndPax(int $tripId, int $pax): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} WHERE trip_id = ? AND pax = ? AND status = 'active' ORDER BY sort_order ASC",
            [$tripId, $pax]
        );
    }

    /**
     * Busca pacote específico por ID.
     */
    public function findById(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE id = ?",
            [$id]
        );
    }

    /**
     * Sincroniza pacotes de composição de um passeio.
     * Remove os antigos e insere os novos.
     */
    public function syncForTrip(int $tripId, array $packages): void
    {
        $this->db->delete($this->table, 'trip_id = ?', [$tripId]);

        foreach ($packages as $i => $pkg) {
            if (empty($pkg['pax']) || empty($pkg['price'])) continue;

            $this->db->insert($this->table, [
                'trip_id' => $tripId,
                'label' => $pkg['label'] ?? '',
                'pax' => (int) $pkg['pax'],
                'units' => (int) ($pkg['units'] ?? 1),
                'unit_label' => $pkg['unit_label'] ?? null,
                'pax_per_unit' => !empty($pkg['pax_per_unit']) ? (int) $pkg['pax_per_unit'] : null,
                'price' => (float) $pkg['price'],
                'sort_order' => (int) ($pkg['sort_order'] ?? $i),
                'status' => $pkg['status'] ?? 'active',
            ]);
        }
    }
}
