<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Trip;
use App\Models\TripHotel;
use App\Models\TripHotelSchedule;
use App\Services\ExcelReaderService;

/**
 * Controller para gerenciar horários de pickup por hotel nos passeios.
 * Funciona como sub-recurso de /admin/passeios/{id}/horarios
 */
class SchedulesController extends Controller
{
    private Trip $tripModel;
    private TripHotel $hotelModel;
    private TripHotelSchedule $scheduleModel;

    public function __construct()
    {
        parent::__construct();
        $this->tripModel = new Trip();
        $this->hotelModel = new TripHotel();
        $this->scheduleModel = new TripHotelSchedule();
    }

    /**
     * Exibe horários de um passeio específico (página dedicada).
     */
    public function show(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $trip = $this->tripModel->find($tripId);

        if (!$trip) {
            $this->abort(404);
        }

        $hotels = $this->hotelModel->getByTripWithCount($tripId);
        foreach ($hotels as &$hotel) {
            $hotel['schedules'] = $this->scheduleModel->getByHotel((int) $hotel['id']);
        }

        $this->view('admin/schedules/show', [
            'trip' => $trip,
            'hotels' => $hotels,
            'pageTitle' => 'Horários: ' . $trip['title'],
        ], 'admin');
    }

    /**
     * Formulário para adicionar hotel + horários manualmente.
     */
    public function createHotel(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $trip = $this->tripModel->find($tripId);

        if (!$trip) {
            $this->abort(404);
        }

        $this->view('admin/schedules/hotel_form', [
            'trip' => $trip,
            'hotel' => null,
            'schedules' => [],
            'pageTitle' => 'Adicionar Hotel: ' . $trip['title'],
        ], 'admin');
    }

    /**
     * Salva hotel + horários (criação).
     */
    public function storeHotel(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $trip = $this->tripModel->find($tripId);

        if (!$trip) {
            $this->abort(404);
        }

        $hotelName = trim($request->input('hotel_name', ''));
        if (empty($hotelName)) {
            $this->flash('error', 'Nome do hotel é obrigatório.');
            $this->redirect("/admin/passeios/{$tripId}/horarios/hotel/criar");
            return;
        }

        // Verificar duplicata
        $existing = $this->hotelModel->findByTripAndName($tripId, $hotelName);
        if ($existing) {
            $this->flash('error', 'Este hotel já está cadastrado para este passeio.');
            $this->redirect("/admin/passeios/{$tripId}/horarios/hotel/criar");
            return;
        }

        $sortOrder = (int) $request->input('sort_order', '0');
        $hotelId = $this->hotelModel->create([
            'trip_id' => $tripId,
            'hotel_name' => $hotelName,
            'sort_order' => $sortOrder,
            'is_active' => 1,
        ]);

        // Salvar horários
        $times = $request->input('times', []);
        $this->saveSchedules($hotelId, $times);

        $this->flash('success', "Hotel \"{$hotelName}\" adicionado com sucesso!");
        $this->redirect("/admin/passeios/{$tripId}/editar");
    }

    /**
     * Formulário para editar hotel + horários.
     */
    public function editHotel(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $hotelId = (int) $request->param('hotel_id');

        $trip = $this->tripModel->find($tripId);
        $hotel = $this->hotelModel->find($hotelId);

        if (!$trip || !$hotel || (int) $hotel['trip_id'] !== $tripId) {
            $this->abort(404);
        }

        $schedules = $this->scheduleModel->getByHotel($hotelId);

        $this->view('admin/schedules/hotel_form', [
            'trip' => $trip,
            'hotel' => $hotel,
            'schedules' => $schedules,
            'pageTitle' => 'Editar Hotel: ' . $hotel['hotel_name'],
        ], 'admin');
    }

    /**
     * Atualiza hotel + horários.
     */
    public function updateHotel(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $hotelId = (int) $request->param('hotel_id');

        $trip = $this->tripModel->find($tripId);
        $hotel = $this->hotelModel->find($hotelId);

        if (!$trip || !$hotel || (int) $hotel['trip_id'] !== $tripId) {
            $this->abort(404);
        }

        $hotelName = trim($request->input('hotel_name', ''));
        if (empty($hotelName)) {
            $this->flash('error', 'Nome do hotel é obrigatório.');
            $this->redirect("/admin/passeios/{$tripId}/horarios/hotel/{$hotelId}/editar");
            return;
        }

        // Verificar duplicata (exceto o próprio)
        $existing = $this->hotelModel->findByTripAndName($tripId, $hotelName);
        if ($existing && (int) $existing['id'] !== $hotelId) {
            $this->flash('error', 'Já existe outro hotel com este nome neste passeio.');
            $this->redirect("/admin/passeios/{$tripId}/horarios/hotel/{$hotelId}/editar");
            return;
        }

        $this->hotelModel->update($hotelId, [
            'hotel_name' => $hotelName,
            'sort_order' => (int) $request->input('sort_order', '0'),
            'is_active' => $request->input('is_active') ? 1 : 0,
        ]);

        // Atualizar horários (limpa e recria)
        $this->scheduleModel->deleteByHotel($hotelId);
        $times = $request->input('times', []);
        $this->saveSchedules($hotelId, $times);

        $this->flash('success', "Hotel \"{$hotelName}\" atualizado com sucesso!");
        $this->redirect("/admin/passeios/{$tripId}/editar");
    }

    /**
     * Exclui hotel e seus horários.
     */
    public function deleteHotel(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $hotelId = (int) $request->param('hotel_id');

        $hotel = $this->hotelModel->find($hotelId);
        if (!$hotel || (int) $hotel['trip_id'] !== $tripId) {
            $this->abort(404);
        }

        $this->scheduleModel->deleteByHotel($hotelId);
        $this->hotelModel->delete($hotelId);

        $this->flash('success', "Hotel \"{$hotel['hotel_name']}\" removido com sucesso!");
        $this->redirect("/admin/passeios/{$tripId}/editar");
    }

    /**
     * Exibe formulário de importação de planilha.
     */
    public function importForm(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $trip = $this->tripModel->find($tripId);

        if (!$trip) {
            $this->abort(404);
        }

        $this->view('admin/schedules/import', [
            'trip' => $trip,
            'pageTitle' => 'Importar Horários: ' . $trip['title'],
        ], 'admin');
    }

    /**
     * Processa upload e importação da planilha.
     */
    public function import(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $trip = $this->tripModel->find($tripId);

        if (!$trip) {
            $this->abort(404);
        }

        if (!$request->hasFile('schedule_file')) {
            $this->flash('error', 'Nenhum arquivo selecionado.');
            $this->redirect("/admin/passeios/{$tripId}/horarios/importar");
            return;
        }

        $file = $request->file('schedule_file');
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'Erro no upload do arquivo.');
            $this->redirect("/admin/passeios/{$tripId}/horarios/importar");
            return;
        }

        $allowedExtensions = ['xlsx', 'csv', 'txt'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $allowedExtensions)) {
            $this->flash('error', 'Formato não suportado. Use .xlsx ou .csv');
            $this->redirect("/admin/passeios/{$tripId}/horarios/importar");
            return;
        }

        if ($file['size'] > 5 * 1024 * 1024) {
            $this->flash('error', 'Arquivo muito grande. Máximo: 5MB.');
            $this->redirect("/admin/passeios/{$tripId}/horarios/importar");
            return;
        }

        try {
            $reader = new ExcelReaderService();
            $rawData = $reader->read($file['tmp_name']);

            if (empty($rawData)) {
                $this->flash('error', 'A planilha está vazia ou não foi possível ler os dados.');
                $this->redirect("/admin/passeios/{$tripId}/horarios/importar");
                return;
            }

            $parsedData = $reader->parseHotelSchedules($rawData);

            if (empty($parsedData)) {
                $this->flash('error', 'Nenhum dado válido encontrado na planilha. Verifique o formato.');
                $this->redirect("/admin/passeios/{$tripId}/horarios/importar");
                return;
            }

            $clearExisting = (bool) $request->input('clear_existing', '0');
            $stats = $this->hotelModel->importSchedules($tripId, $parsedData, $clearExisting);

            $message = "Importação concluída! {$stats['hotels_added']} hotel(éis) e {$stats['schedules_added']} horário(s) adicionados.";
            if (!empty($stats['errors'])) {
                $message .= ' Erros: ' . implode('; ', $stats['errors']);
            }

            $this->flash('success', $message);
            $this->redirect("/admin/passeios/{$tripId}/editar");

        } catch (\Exception $e) {
            $this->flash('error', 'Erro ao processar arquivo: ' . $e->getMessage());
            $this->redirect("/admin/passeios/{$tripId}/horarios/importar");
        }
    }

    /**
     * Limpa todos os horários de um passeio.
     */
    public function clearAll(Request $request, Response $response): void
    {
        $tripId = (int) $request->param('id');
        $trip = $this->tripModel->find($tripId);

        if (!$trip) {
            $this->abort(404);
        }

        $this->hotelModel->deleteByTrip($tripId);

        $this->flash('success', 'Todos os hotéis e horários foram removidos.');
        $this->redirect("/admin/passeios/{$tripId}/editar");
    }

    /**
     * Salva horários para um hotel.
     */
    private function saveSchedules(int $hotelId, array $times): void
    {
        foreach ($times as $time) {
            $time = trim($time);
            if (empty($time)) continue;

            if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
                $time = sprintf('%02d:%02d:00', ...array_map('intval', explode(':', $time)));
            }

            if (!preg_match('/^\d{2}:\d{2}:\d{2}$/', $time)) {
                continue;
            }

            if (!$this->scheduleModel->existsForHotel($hotelId, $time)) {
                $this->scheduleModel->create([
                    'trip_hotel_id' => $hotelId,
                    'pickup_time' => $time,
                    'is_active' => 1,
                ]);
            }
        }
    }
}
