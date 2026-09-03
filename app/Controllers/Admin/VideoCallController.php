<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\VideoCallBooking;
use App\Services\VideoCallNotifier;

/**
 * Gestão dos agendamentos de chamadas de vídeo no painel admin.
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
     * Listagem dos agendamentos.
     */
    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $status = $request->query('status');

        $bookings = $this->model->getWithTrip($page, 20, $status ?: null);

        // Contadores por status
        $counts = [
            'all' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM videocall_bookings"),
            'pending' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM videocall_bookings WHERE status = 'pending'"),
            'confirmed' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM videocall_bookings WHERE status = 'confirmed'"),
            'completed' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM videocall_bookings WHERE status = 'completed'"),
            'cancelled' => (int) $this->db->fetchColumn("SELECT COUNT(*) FROM videocall_bookings WHERE status = 'cancelled'"),
        ];

        $this->view('admin/videocall/index', [
            'bookings' => $bookings,
            'counts' => $counts,
            'currentStatus' => $status,
            'moduleEnabled' => $this->setting('videocall_enabled', '0') === '1',
            'pageTitle' => 'Agendamentos de Chamadas',
        ], 'admin');
    }

    /**
     * Atualiza o status de um agendamento e notifica o cliente quando aplicável.
     */
    public function updateStatus(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $status = (string) $request->input('status', '');
        $adminNotes = trim((string) $request->input('admin_notes', ''));

        $allowed = ['pending', 'confirmed', 'completed', 'cancelled'];
        if (!in_array($status, $allowed, true)) {
            $this->flash('error', 'Status inválido.');
            $this->redirect('/admin/agendamentos');
            return;
        }

        $booking = $this->findWithTrip($id);
        if (!$booking) {
            $this->flash('error', 'Agendamento não encontrado.');
            $this->redirect('/admin/agendamentos');
            return;
        }

        $update = ['status' => $status];
        if ($adminNotes !== '') {
            $update['admin_notes'] = $adminNotes;
        }
        $this->db->update('videocall_bookings', $update, 'id = ?', [$id]);

        // Notificar o cliente (WhatsApp + e-mail) em toda mudança relevante de status
        if (in_array($status, ['confirmed', 'completed', 'cancelled'], true)) {
            try {
                $booking['admin_notes'] = $adminNotes !== '' ? $adminNotes : ($booking['admin_notes'] ?? '');
                (new VideoCallNotifier())->notifyStatusChange($booking, $status);
            } catch (\Throwable $e) {
                error_log('[Admin\\VideoCallController] Falha ao notificar status: ' . $e->getMessage());
            }
        }

        $this->flash('success', 'Status atualizado com sucesso.');
        $this->redirect('/admin/agendamentos');
    }

    /**
     * Exclui um agendamento (notificando o cliente antes de remover).
     */
    public function destroy(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');

        $booking = $this->findWithTrip($id);
        if ($booking) {
            try {
                (new VideoCallNotifier())->notifyDeleted($booking);
            } catch (\Throwable $e) {
                error_log('[Admin\\VideoCallController] Falha ao notificar exclusão: ' . $e->getMessage());
            }
        }

        $this->model->delete($id);
        $this->flash('success', 'Agendamento removido.');
        $this->redirect('/admin/agendamentos');
    }

    /**
     * Busca um agendamento com o título do passeio associado.
     */
    private function findWithTrip(int $id): ?array
    {
        return $this->db->fetchOne(
            "SELECT v.*, t.title AS trip_title
             FROM videocall_bookings v
             LEFT JOIN trips t ON v.trip_id = t.id
             WHERE v.id = ?",
            [$id]
        );
    }
}
