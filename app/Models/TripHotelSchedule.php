<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TripHotelSchedule extends Model
{
    protected string $table = 'trip_hotel_schedules';
    protected array $fillable = [
        'trip_hotel_id', 'pickup_time', 'notes', 'is_active',
    ];

    /**
     * Retorna horários de um hotel.
     */
    public function getByHotel(int $tripHotelId, bool $onlyActive = false): array
    {
        $where = "trip_hotel_id = ?";
        $params = [$tripHotelId];

        if ($onlyActive) {
            $where .= " AND is_active = 1";
        }

        return $this->where($where, $params, 'pickup_time ASC');
    }

    /**
     * Verifica se um horário já existe para o hotel.
     */
    public function existsForHotel(int $tripHotelId, string $time, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) FROM `{$this->table}` WHERE trip_hotel_id = ? AND pickup_time = ?";
        $params = [$tripHotelId, $time];

        if ($excludeId !== null) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        return (int) $this->db->fetchColumn($sql, $params) > 0;
    }

    /**
     * Remove todos os horários de um hotel.
     */
    public function deleteByHotel(int $tripHotelId): int
    {
        return $this->db->delete($this->table, "trip_hotel_id = ?", [$tripHotelId]);
    }
}
