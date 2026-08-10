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

        $this->json([
            'success' => true,
            'hotels' => $result,
            'total_hotels' => count($result),
        ]);
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
