<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\VideoCallBooking;
use App\Models\Trip;
use App\Services\VideoCallNotifier;

/**
 * Agendamento de chamadas de vídeo pelo cliente (na página do passeio).
 */
class VideoCallController extends Controller
{
    private VideoCallBooking $model;

    public function __construct()
    {
        parent::__construct();
        $this->model = new VideoCallBooking();
    }

    /**
     * Retorna (JSON) os horários disponíveis para uma data específica.
     * GET /api/videocall/slots?date=YYYY-MM-DD
     */
    public function slots(Request $request, Response $response): void
    {
        if ($this->setting('videocall_enabled', '0') !== '1') {
            $this->json(['success' => false, 'message' => 'Agendamento indisponível.'], 403);
            return;
        }

        $date = (string) $request->input('date', '');
        $ts = strtotime($date);
        if (!$ts || $date === '') {
            $this->json(['success' => false, 'message' => 'Data inválida.'], 422);
            return;
        }

        // Não permitir datas passadas
        if (strtotime(date('Y-m-d', $ts)) < strtotime(date('Y-m-d'))) {
            $this->json(['success' => true, 'slots' => []]);
            return;
        }

        // Dia da semana permitido?
        $allowedDays = $this->allowedDays();
        $dow = (int) date('w', $ts); // 0=domingo..6=sábado
        if (!in_array($dow, $allowedDays, true)) {
            $this->json(['success' => true, 'slots' => []]);
            return;
        }

        $slots = $this->generateSlots($date);

        // Remover horários já ocupados
        $booked = $this->model->bookedSlotsFrom(date('Y-m-d 00:00:00', $ts));
        $bookedTimes = array_map(static fn($s) => date('H:i', strtotime($s)), array_filter($booked, static fn($s) => date('Y-m-d', strtotime($s)) === date('Y-m-d', strtotime($date))));

        // Se for hoje, remover horários que já passaram
        $isToday = date('Y-m-d', $ts) === date('Y-m-d');
        $nowTime = date('H:i');

        $available = [];
        foreach ($slots as $slot) {
            if (in_array($slot, $bookedTimes, true)) continue;
            if ($isToday && $slot <= $nowTime) continue;
            $available[] = $slot;
        }

        $this->json(['success' => true, 'slots' => $available]);
    }

    /**
     * Salva um agendamento.
     * POST /passeios/{slug}/agendar-chamada
     */
    public function schedule(Request $request, Response $response): void
    {
        if ($this->setting('videocall_enabled', '0') !== '1') {
            $this->json(['success' => false, 'message' => 'Agendamento indisponível.'], 403);
            return;
        }

        $slug = (string) $request->param('slug', '');
        $trip = (new Trip())->findBySlug($slug);
        $tripId = $trip['id'] ?? null;

        $name = trim((string) $request->input('customer_name', ''));
        $email = trim((string) $request->input('email', ''));
        $phone = trim((string) $request->input('phone', ''));
        $date = (string) $request->input('date', '');
        $time = (string) $request->input('time', '');
        $notes = trim((string) $request->input('notes', ''));

        // Validação
        $errors = [];
        if ($name === '') $errors[] = 'Informe seu nome.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'E-mail inválido.';
        if ($phone === '') $errors[] = 'Informe seu WhatsApp.';
        if (!strtotime($date)) $errors[] = 'Data inválida.';
        if (!preg_match('/^\d{2}:\d{2}$/', $time)) $errors[] = 'Horário inválido.';

        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => implode(' ', $errors)], 422);
            return;
        }

        $scheduledAt = date('Y-m-d H:i:s', strtotime($date . ' ' . $time));

        // Não permitir passado
        if (strtotime($scheduledAt) < time()) {
            $this->json(['success' => false, 'message' => 'Escolha um horário futuro.'], 422);
            return;
        }

        // Dia/horário precisa estar dentro da disponibilidade e livre
        $dow = (int) date('w', strtotime($scheduledAt));
        if (!in_array($dow, $this->allowedDays(), true) || !in_array($time, $this->generateSlots($date), true)) {
            $this->json(['success' => false, 'message' => 'Este horário não está disponível.'], 422);
            return;
        }
        if ($this->model->slotTaken($scheduledAt)) {
            $this->json(['success' => false, 'message' => 'Este horário acabou de ser reservado. Escolha outro.'], 409);
            return;
        }

        $duration = (int) $this->setting('videocall_duration', '30');
        $meetingLink = $this->generateMeetingLink($name, $scheduledAt);

        $id = $this->model->create([
            'trip_id' => $tripId,
            'customer_name' => $name,
            'email' => $email,
            'phone' => $phone,
            'scheduled_at' => $scheduledAt,
            'duration_minutes' => $duration > 0 ? $duration : 30,
            'meeting_link' => $meetingLink,
            'status' => 'pending',
            'notes' => $notes,
        ]);

        // Notificar cliente + empresa (não bloqueia a resposta em caso de falha)
        try {
            (new VideoCallNotifier())->notifyScheduled([
                'customer_name' => $name,
                'email' => $email,
                'phone' => $phone,
                'scheduled_at' => $scheduledAt,
                'meeting_link' => $meetingLink,
                'trip_title' => $trip['title'] ?? '',
                'notes' => $notes,
            ]);
        } catch (\Throwable $e) {
            error_log('[VideoCallController] Falha ao notificar: ' . $e->getMessage());
        }

        $this->json([
            'success' => true,
            'message' => 'Chamada agendada com sucesso! Enviamos os detalhes por WhatsApp e e-mail.',
            'meeting_link' => $meetingLink,
            'scheduled_at' => date('d/m/Y H:i', strtotime($scheduledAt)),
            'add_to_calendar' => $this->googleCalendarUrl($name, $scheduledAt, $duration, $meetingLink, $trip['title'] ?? ''),
        ]);
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    /**
     * @return int[] dias da semana permitidos (0=dom..6=sáb)
     */
    private function allowedDays(): array
    {
        $raw = (string) $this->setting('videocall_days', '1,2,3,4,5');
        $days = array_map('intval', array_filter(array_map('trim', explode(',', $raw)), static fn($v) => $v !== ''));
        return $days ?: [1, 2, 3, 4, 5];
    }

    /**
     * Gera a lista de horários (HH:MM) para uma data, com base nas settings.
     * @return string[]
     */
    private function generateSlots(string $date): array
    {
        $start = (string) $this->setting('videocall_hour_start', '09:00');
        $end = (string) $this->setting('videocall_hour_end', '18:00');
        $duration = (int) $this->setting('videocall_duration', '30');
        if ($duration < 5) $duration = 30;

        $startTs = strtotime($date . ' ' . $start);
        $endTs = strtotime($date . ' ' . $end);
        if (!$startTs || !$endTs || $endTs <= $startTs) return [];

        $slots = [];
        for ($t = $startTs; $t + $duration * 60 <= $endTs; $t += $duration * 60) {
            $slots[] = date('H:i', $t);
        }
        return $slots;
    }

    /**
     * Gera o link da reunião no Jitsi Meet (sem necessidade de API/auth).
     */
    private function generateMeetingLink(string $name, string $scheduledAt): string
    {
        $slug = 'PuntaCana-' . date('Ymd-His', strtotime($scheduledAt)) . '-' . substr(bin2hex(random_bytes(3)), 0, 6);
        // Remove caracteres problemáticos
        $slug = preg_replace('/[^A-Za-z0-9\-]/', '', $slug);
        return 'https://meet.jit.si/' . $slug;
    }

    /**
     * Gera URL "Adicionar ao Google Agenda".
     */
    private function googleCalendarUrl(string $name, string $scheduledAt, int $duration, string $link, string $tripTitle): string
    {
        $duration = $duration > 0 ? $duration : 30;
        $startUtc = gmdate('Ymd\THis\Z', strtotime($scheduledAt));
        $endUtc = gmdate('Ymd\THis\Z', strtotime($scheduledAt) + $duration * 60);
        $title = 'Chamada de vídeo - ' . $this->setting('site_name', 'Punta Cana para Brasileiros');
        $details = "Sua chamada de vídeo.\n";
        if ($tripTitle !== '') {
            $details .= "Passeio: {$tripTitle}\n";
        }
        $details .= "Link da reunião: {$link}";

        return 'https://calendar.google.com/calendar/render?action=TEMPLATE'
            . '&text=' . rawurlencode($title)
            . '&dates=' . $startUtc . '/' . $endUtc
            . '&details=' . rawurlencode($details)
            . '&location=' . rawurlencode($link);
    }
}
