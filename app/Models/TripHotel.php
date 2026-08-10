<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TripHotel extends Model
{
    protected string $table = 'trip_hotels';
    protected array $fillable = [
        'trip_id', 'hotel_name', 'sort_order', 'is_active',
    ];

    /**
     * Retorna todos os hotéis de um passeio com seus horários.
     */
    public function getByTrip(int $tripId, bool $onlyActive = false): array
    {
        $where = "trip_id = ?";
        $params = [$tripId];

        if ($onlyActive) {
            $where .= " AND is_active = 1";
        }

        $hotels = $this->where($where, $params, 'sort_order ASC, hotel_name ASC');

        // Carregar horários de cada hotel
        foreach ($hotels as &$hotel) {
            $hotel['schedules'] = $this->getSchedules((int) $hotel['id'], $onlyActive);
        }

        return $hotels;
    }

    /**
     * Retorna horários de um hotel específico.
     */
    public function getSchedules(int $tripHotelId, bool $onlyActive = false): array
    {
        $sql = "SELECT * FROM trip_hotel_schedules WHERE trip_hotel_id = ?";
        $params = [$tripHotelId];

        if ($onlyActive) {
            $sql .= " AND is_active = 1";
        }

        $sql .= " ORDER BY pickup_time ASC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Busca hotel por nome dentro de um passeio.
     */
    public function findByTripAndName(int $tripId, string $hotelName): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM `{$this->table}` WHERE trip_id = ? AND hotel_name = ? LIMIT 1",
            [$tripId, $hotelName]
        );
    }

    /**
     * Remove todos os hotéis e horários de um passeio.
     */
    public function deleteByTrip(int $tripId): void
    {
        // Primeiro remove os horários dos hotéis deste passeio
        $hotels = $this->where("trip_id = ?", [$tripId]);
        foreach ($hotels as $hotel) {
            $this->db->delete('trip_hotel_schedules', "trip_hotel_id = ?", [(int) $hotel['id']]);
        }

        // Depois remove os hotéis
        $this->db->delete($this->table, "trip_id = ?", [$tripId]);
    }

    /**
     * Conta total de hotéis cadastrados para um passeio.
     */
    public function countByTrip(int $tripId): int
    {
        return $this->count("trip_id = ?", [$tripId]);
    }

    /**
     * Retorna hotéis com contagem de horários (para listagem admin).
     */
    public function getByTripWithCount(int $tripId): array
    {
        $sql = "SELECT th.*, 
                    (SELECT COUNT(*) FROM trip_hotel_schedules ths WHERE ths.trip_hotel_id = th.id) as schedules_count
                FROM `{$this->table}` th
                WHERE th.trip_id = ?
                ORDER BY th.sort_order ASC, th.hotel_name ASC";

        return $this->db->fetchAll($sql, [$tripId]);
    }

    /**
     * Importa hotéis e horários a partir de dados de planilha.
     * Formato esperado: array de ['hotel' => 'Nome', 'times' => ['07:20', '08:10']]
     */
    public function importSchedules(int $tripId, array $data, bool $clearExisting = true): array
    {
        $stats = ['hotels_added' => 0, 'schedules_added' => 0, 'errors' => []];

        if ($clearExisting) {
            $this->deleteByTrip($tripId);
        }

        $sortOrder = 0;
        foreach ($data as $row) {
            $hotelName = trim($row['hotel'] ?? '');
            if (empty($hotelName)) {
                continue;
            }

            $sortOrder++;

            // Criar ou buscar hotel
            $existing = $this->findByTripAndName($tripId, $hotelName);
            if ($existing) {
                $hotelId = (int) $existing['id'];
                $this->update($hotelId, ['sort_order' => $sortOrder]);
            } else {
                $hotelId = $this->create([
                    'trip_id' => $tripId,
                    'hotel_name' => $hotelName,
                    'sort_order' => $sortOrder,
                    'is_active' => 1,
                ]);
                $stats['hotels_added']++;
            }

            // Cadastrar horários
            $times = $row['times'] ?? [];
            foreach ($times as $time) {
                $time = trim($time);
                if (empty($time)) continue;

                // Normalizar formato de horário (aceita H:i ou H:i:s)
                if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
                    $time .= ':00';
                }

                if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
                    $stats['errors'][] = "Horário inválido para {$hotelName}: {$time}";
                    continue;
                }

                // Verificar duplicata
                $existingSchedule = $this->db->fetchOne(
                    "SELECT id FROM trip_hotel_schedules WHERE trip_hotel_id = ? AND pickup_time = ?",
                    [$hotelId, $time]
                );

                if (!$existingSchedule) {
                    $this->db->insert('trip_hotel_schedules', [
                        'trip_hotel_id' => $hotelId,
                        'pickup_time' => $time,
                        'is_active' => 1,
                    ]);
                    $stats['schedules_added']++;
                }
            }
        }

        return $stats;
    }
}
