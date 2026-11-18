<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\TripHotel;

/**
 * API para buscar hotéis e horários disponíveis no frontend.
 * Usado na página do passeio quando o cliente está escolhendo o pickup.
 */
class ScheduleController extends Controller
{
    private TripHotel $hotelModel;

    public function __construct()
    {
        parent::__construct();
        $this->hotelModel = new TripHotel();
    }

    /**
     * Retorna hotéis e horários de um passeio (para uso AJAX no frontend).
     * GET /api/schedules/{trip_id}
     * 
     * Response:
     * {
     *   "success": true,
     *   "hotels": [
     *     {
     *       "id": 1,
     *       "hotel_name": "Hotel Barceló",
     *       "schedules": [
     *         { "id": 1, "time": "07:20", "notes": null },
     *         { "id": 2, "time": "08:10", "notes": null }
     *       ]
     *     }
     *   ]
     * }
     */
    public function getByTrip(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('trip_id');

        if ($tripId <= 0) {
            $this->json(['success' => false, 'error' => 'Passeio inválido.'], 400);
            return;
        }

        try {
            $hotels = $this->hotelModel->getByTrip($tripId, true);

            // Formatar para resposta JSON limpa
            $result = [];
            foreach ($hotels as $hotel) {
                $schedules = [];
                foreach ($hotel['schedules'] as $schedule) {
                    $schedules[] = [
                        'id' => (int) $schedule['id'],
                        'time' => substr($schedule['pickup_time'], 0, 5),
                        'notes' => $schedule['notes'] ?? null,
                    ];
                }

                // Só incluir hotéis que têm pelo menos 1 horário
                if (!empty($schedules)) {
                    $result[] = [
                        'id' => (int) $hotel['id'],
                        'hotel_name' => $hotel['hotel_name'],
                        'schedules' => $schedules,
                    ];
                }
            }

            // Passeio sem hotéis próprios: sugere a lista geral de hotéis (de todos os
            // passeios + locais de transfer do tipo hotel/resort), sem horário de busca.
            $generic = false;
            if (empty($result)) {
                $generic = true;
                $names = [];
                try {
                    foreach ($this->db->fetchAll("SELECT DISTINCT hotel_name FROM trip_hotels WHERE is_active = 1") as $r) {
                        $names[mb_strtolower(trim($r['hotel_name']))] = trim($r['hotel_name']);
                    }
                    foreach ($this->db->fetchAll("SELECT title FROM transfer_locations WHERE status = 1 AND location_type IN ('hotel','resort')") as $r) {
                        $names[mb_strtolower(trim($r['title']))] ??= trim($r['title']);
                    }
                } catch (\Throwable $e) {
                }
                natcasesort($names);
                $i = 0;
                foreach ($names as $n) {
                    if ($n === '') continue;
                    $result[] = ['id' => -(++$i), 'hotel_name' => $n, 'schedules' => [], 'generic' => true];
                }
            }

            $this->json([
                'success' => true,
                'hotels' => $result,
                'generic' => $generic,
                'total_hotels' => count($result),
            ]);
        } catch (\Exception $e) {
            $this->json(['success' => false, 'error' => 'Erro interno: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Retorna horários de um hotel específico.
     * GET /api/schedules/hotel/{hotel_id}
     * 
     * Usado quando o cliente seleciona um hotel no dropdown
     * e precisa carregar os horários disponíveis.
     */
    public function getByHotel(Request $request, Response $response): void
    {
        $hotelId = (int) $request->param('hotel_id');

        if ($hotelId <= 0) {
            $this->json(['success' => false, 'error' => 'Hotel inválido.'], 400);
            return;
        }

        $hotel = $this->hotelModel->find($hotelId);
        if (!$hotel || !$hotel['is_active']) {
            $this->json(['success' => false, 'error' => 'Hotel não encontrado.'], 404);
            return;
        }

        $schedules = $this->hotelModel->getSchedules($hotelId, true);

        $result = [];
        foreach ($schedules as $schedule) {
            $result[] = [
                'id' => (int) $schedule['id'],
                'time' => substr($schedule['pickup_time'], 0, 5),
                'notes' => $schedule['notes'] ?? null,
            ];
        }

        $this->json([
            'success' => true,
            'hotel_name' => $hotel['hotel_name'],
            'schedules' => $result,
        ]);
    }
}
