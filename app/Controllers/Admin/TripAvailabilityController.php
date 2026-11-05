<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Trip;
use App\Services\TripAvailabilityService;

/**
 * Tela "Dias e Horários" de cada passeio:
 *  - dias da semana em que o passeio não funciona;
 *  - exceções por data (fechar uma data/período ou abrir excepcionalmente);
 *  - horários de saída, cada um com os dias da semana em que vale.
 */
class TripAvailabilityController extends Controller
{
    private Trip $tripModel;
    private TripAvailabilityService $availability;

    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
        $this->tripModel = new Trip();
        $this->availability = new TripAvailabilityService();
    }

    public function show(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $trip = $this->tripModel->find($id);
        if (!$trip) {
            $this->abort(404, 'Passeio não encontrado.');
        }

        $this->view('admin/trips/availability', [
            'trip' => $trip,
            'ready' => $this->availability->isReady(),
            'closedWeekdays' => $this->availability->getClosedWeekdays($id),
            'departureTimes' => $this->availability->getDepartureTimes($id),
            'exceptions' => $this->availability->getExceptions($id, true),
            'pageTitle' => 'Dias e Horários: ' . $trip['title'],
        ], 'admin');
    }

    public function save(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $trip = $this->tripModel->find($id);
        if (!$trip) {
            $this->abort(404, 'Passeio não encontrado.');
        }

        if (!$this->availability->isReady()) {
            $this->flash('error', 'O banco de dados ainda não foi atualizado (migration 036). Peça para a equipe técnica rodar a atualização.');
            $this->redirect('/admin/passeios/' . $id . '/disponibilidade');
            return;
        }

        // 1) Dias fechados
        $closed = TripAvailabilityService::parseWeekdays(implode(',', (array) $request->input('closed_weekdays', [])));
        $this->db->update('trips', ['closed_weekdays' => $closed ? implode(',', $closed) : null], 'id = ?', [$id]);

        // 2) Horários de saída (limpa e recria)
        $this->db->delete('trip_departure_times', 'trip_id = ?', [$id]);
        $order = 0;
        foreach ((array) $request->input('times', []) as $row) {
            if (!is_array($row)) continue;
            $time = TripAvailabilityService::normalizeTime($row['time'] ?? '');
            if ($time === null) continue;
            $days = TripAvailabilityService::parseWeekdays(implode(',', (array) ($row['weekdays'] ?? [])));
            if (empty($days)) continue; // horário sem nenhum dia marcado não vale
            $this->db->insert('trip_departure_times', [
                'trip_id' => $id,
                'departure_time' => $time . ':00',
                'label' => mb_substr(trim((string) ($row['label'] ?? '')), 0, 100) ?: null,
                'weekdays' => implode(',', $days),
                'sort_order' => $order++,
                'is_active' => 1,
            ]);
        }

        // 3) Exceções por data (mantém as passadas; recria as futuras enviadas)
        $this->db->delete('trip_date_exceptions', 'trip_id = ? AND date_end >= CURDATE()', [$id]);
        foreach ((array) $request->input('exceptions', []) as $row) {
            if (!is_array($row)) continue;
            $start = $this->parseDate($row['date_start'] ?? '');
            if (!$start) continue;
            $end = $this->parseDate($row['date_end'] ?? '') ?: $start;
            if ($end < $start) [$start, $end] = [$end, $start];
            $type = ($row['type'] ?? 'closed') === 'open' ? 'open' : 'closed';
            $this->db->insert('trip_date_exceptions', [
                'trip_id' => $id,
                'exception_type' => $type,
                'date_start' => $start,
                'date_end' => $end,
                'note' => mb_substr(trim((string) ($row['note'] ?? '')), 0, 255) ?: null,
            ]);
        }

        $this->flash('success', 'Dias e horários salvos! O site já está usando as novas regras.');
        $this->redirect('/admin/passeios/' . $id . '/disponibilidade');
    }

    private function parseDate(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') return null;
        $d = \DateTime::createFromFormat('Y-m-d', $value) ?: \DateTime::createFromFormat('d/m/Y', $value);
        return $d ? $d->format('Y-m-d') : null;
    }
}
