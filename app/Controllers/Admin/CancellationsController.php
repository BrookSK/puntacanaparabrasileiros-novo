<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\CancellationRequest;
use App\Models\Booking;
use App\Services\EmailService;

class CancellationsController extends Controller
{
    private CancellationRequest $cancellationModel;
    private Booking $bookingModel;

    public function __construct()
    {
        parent::__construct();
        $this->cancellationModel = new CancellationRequest();
        $this->bookingModel = new Booking();
    }

    /**
     * Lista todas as solicitações de cancelamento.
     */
    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $status = $request->query('status', '');

        $where = '1=1';
        $params = [];

        if ($status) {
            $where .= ' AND cr.status = ?';
            $params[] = $status;
        }

        // Buscar cancelamentos com dados do booking e do cliente
        $perPage = 20;
        $countRow = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM cancellation_requests cr WHERE " . str_replace('cr.status', 'status', $where),
            $params
        );
        $total = (int) ($countRow['total'] ?? 0);
        $totalPages = max(1, (int) ceil($total / $perPage));
        $offset = ($page - 1) * $perPage;

        $cancellations = $this->db->fetchAll(
            "SELECT cr.*, b.booking_number, b.total as booking_total, b.paid_amount,
                    b.billing_first_name, b.billing_last_name, b.billing_email, b.status as booking_status,
                    u.first_name as user_first_name, u.last_name as user_last_name, u.email as user_email
             FROM cancellation_requests cr
             INNER JOIN bookings b ON cr.booking_id = b.id
             INNER JOIN users u ON cr.user_id = u.id
             WHERE $where
             ORDER BY cr.created_at DESC
             LIMIT $perPage OFFSET $offset",
            $params
        );

        $this->view('admin/cancellations/index', [
            'cancellations' => ['items' => $cancellations, 'total_pages' => $totalPages, 'current_page' => $page],
            'currentStatus' => $status,
            'pendingCount' => $this->cancellationModel->getPendingCount(),
            'pageTitle' => 'Cancelamentos',
        ], 'admin');
    }

    /**
     * Exibe detalhes de uma solicitação de cancelamento.
     */
    public function show(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $cancellation = $this->cancellationModel->find($id);

        if (!$cancellation) {
            $this->abort(404, 'Solicitação não encontrada.');
        }

        $booking = $this->bookingModel->find((int) $cancellation['booking_id']);
        $items = $this->bookingModel->getItems((int) $cancellation['booking_id']);
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = ?", [(int) $cancellation['user_id']]);

        $this->view('admin/cancellations/show', [
            'cancellation' => $cancellation,
            'booking' => $booking,
            'items' => $items,
            'client' => $user,
            'pageTitle' => 'Cancelamento #' . $id,
        ], 'admin');
    }

    /**
     * Aprovar a solicitação de cancelamento.
     */
    public function approve(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $adminResponse = trim($request->input('admin_response', ''));
        $cancellation = $this->cancellationModel->find($id);

        if (!$cancellation) {
            $this->flash('error', 'Solicitação não encontrada.');
            $this->redirect('/admin/cancelamentos');
            return;
        }

        if ($cancellation['status'] !== 'pending') {
            $this->flash('error', 'Esta solicitação já foi processada.');
            $this->redirect('/admin/cancelamentos/' . $id);
            return;
        }

        $admin = $this->currentUser();
        $this->cancellationModel->approve($id, (int) $admin['id'], $adminResponse);

        // Atualizar status do booking para cancelled
        $this->bookingModel->updateStatus((int) $cancellation['booking_id'], 'cancelled');

        // Enviar email ao cliente
        $booking = $this->bookingModel->find((int) $cancellation['booking_id']);
        $clientEmail = $booking['billing_email'] ?? '';
        $clientName = ($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? '');
        $bookingItems = $this->db->fetchAll(
            "SELECT t.title FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.booking_id = ?",
            [(int) $cancellation['booking_id']]
        );
        $serviceName = implode(', ', array_column($bookingItems, 'title')) ?: '';

        if ($clientEmail) {
            $emailService = new EmailService();
            $emailService->sendTemplate(
                $clientEmail, trim($clientName),
                'Cancelamento Aprovado - ' . ($booking['booking_number'] ?? ''),
                'cancellation',
                [
                    'emailTitle' => 'Cancelamento Aprovado',
                    'clientName' => trim($clientName),
                    'emailMessage' => 'Sua solicitação de cancelamento para a reserva abaixo foi <strong style="color:#1B6F00">aprovada</strong>.',
                    'bookingNumber' => $booking['booking_number'] ?? '',
                    'serviceName' => $serviceName,
                    'bookingTotal' => $booking['total'] ?? 0,
                    'statusLabel' => 'Aprovado',
                    'statusColor' => '#1B6F00',
                    'blockquoteLabel' => 'Mensagem da Equipe',
                    'blockquoteText' => $adminResponse ?: null,
                    'blockquoteColor' => '#1B6F00',
                    'blockquoteBg' => '#f0fdf4',
                    'additionalMessage' => 'Caso tenha direito a reembolso, você será notificado assim que o processamento for concluído.',
                    'ctaUrl' => url('/minha-conta/cancelamentos'),
                    'ctaText' => 'Ver Meus Cancelamentos',
                ]
            );
        }

        $this->flash('success', 'Cancelamento aprovado com sucesso. O cliente foi notificado por e-mail.');
        $this->redirect('/admin/cancelamentos/' . $id);
    }

    /**
     * Rejeitar a solicitação de cancelamento.
     */
    public function reject(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $adminResponse = trim($request->input('admin_response', ''));
        $cancellation = $this->cancellationModel->find($id);

        if (!$cancellation) {
            $this->flash('error', 'Solicitação não encontrada.');
            $this->redirect('/admin/cancelamentos');
            return;
        }

        if ($cancellation['status'] !== 'pending') {
            $this->flash('error', 'Esta solicitação já foi processada.');
            $this->redirect('/admin/cancelamentos/' . $id);
            return;
        }

        if (empty($adminResponse)) {
            $this->flash('error', 'Por favor, informe o motivo da recusa ao cliente.');
            $this->redirect('/admin/cancelamentos/' . $id);
            return;
        }

        $admin = $this->currentUser();
        $this->cancellationModel->reject($id, (int) $admin['id'], $adminResponse);

        // Enviar email ao cliente
        $booking = $this->bookingModel->find((int) $cancellation['booking_id']);
        $clientEmail = $booking['billing_email'] ?? '';
        $clientName = ($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? '');
        $bookingItems = $this->db->fetchAll(
            "SELECT t.title FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.booking_id = ?",
            [(int) $cancellation['booking_id']]
        );
        $serviceName = implode(', ', array_column($bookingItems, 'title')) ?: '';

        if ($clientEmail) {
            $emailService = new EmailService();
            $emailService->sendTemplate(
                $clientEmail, trim($clientName),
                'Cancelamento Não Autorizado - ' . ($booking['booking_number'] ?? ''),
                'cancellation',
                [
                    'emailTitle' => 'Cancelamento Não Autorizado',
                    'clientName' => trim($clientName),
                    'emailMessage' => 'Infelizmente, sua solicitação de cancelamento para a reserva abaixo <strong style="color:#dc2626">não foi autorizada</strong>.',
                    'bookingNumber' => $booking['booking_number'] ?? '',
                    'serviceName' => $serviceName,
                    'bookingTotal' => $booking['total'] ?? 0,
                    'statusLabel' => 'Não Autorizado',
                    'statusColor' => '#dc2626',
                    'blockquoteLabel' => 'Motivo da Recusa',
                    'blockquoteText' => $adminResponse,
                    'blockquoteColor' => '#dc2626',
                    'blockquoteBg' => '#fef2f2',
                    'additionalMessage' => 'Se tiver dúvidas, entre em contato com nossa equipe pelo WhatsApp.',
                    'ctaUrl' => url('/minha-conta/cancelamentos'),
                    'ctaText' => 'Ver Meus Cancelamentos',
                ]
            );
        }

        $this->flash('success', 'Cancelamento rejeitado. O cliente foi notificado por e-mail.');
        $this->redirect('/admin/cancelamentos/' . $id);
    }

    /**
     * Marcar como reembolsado.
     */
    public function refund(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $refundAmount = (float) $request->input('refund_amount', '0');
        $refundNotes = trim($request->input('refund_notes', ''));
        $cancellation = $this->cancellationModel->find($id);

        if (!$cancellation) {
            $this->flash('error', 'Solicitação não encontrada.');
            $this->redirect('/admin/cancelamentos');
            return;
        }

        if ($cancellation['status'] !== 'approved') {
            $this->flash('error', 'Somente cancelamentos aprovados podem ser reembolsados.');
            $this->redirect('/admin/cancelamentos/' . $id);
            return;
        }

        if ($refundAmount <= 0) {
            $this->flash('error', 'Informe um valor de reembolso válido.');
            $this->redirect('/admin/cancelamentos/' . $id);
            return;
        }

        $this->cancellationModel->markRefunded($id, $refundAmount, $refundNotes);

        // Atualizar status do booking para refunded
        $this->bookingModel->updateStatus((int) $cancellation['booking_id'], 'refunded');

        // Enviar email ao cliente
        $booking = $this->bookingModel->find((int) $cancellation['booking_id']);
        $clientEmail = $booking['billing_email'] ?? '';
        $clientName = ($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? '');
        $bookingItems = $this->db->fetchAll(
            "SELECT t.title FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.booking_id = ?",
            [(int) $cancellation['booking_id']]
        );
        $serviceName = implode(', ', array_column($bookingItems, 'title')) ?: '';

        if ($clientEmail) {
            $emailService = new EmailService();
            $emailService->sendTemplate(
                $clientEmail, trim($clientName),
                'Reembolso Processado - ' . ($booking['booking_number'] ?? ''),
                'cancellation',
                [
                    'emailTitle' => 'Reembolso Processado',
                    'clientName' => trim($clientName),
                    'emailMessage' => 'O reembolso referente à sua reserva foi processado com sucesso.',
                    'bookingNumber' => $booking['booking_number'] ?? '',
                    'serviceName' => $serviceName,
                    'bookingTotal' => $booking['total'] ?? 0,
                    'refundAmount' => $refundAmount,
                    'statusLabel' => 'Reembolsado',
                    'statusColor' => '#1B6F00',
                    'blockquoteLabel' => 'Observações',
                    'blockquoteText' => $refundNotes ?: null,
                    'blockquoteColor' => '#3772C0',
                    'blockquoteBg' => '#eff6ff',
                    'additionalMessage' => 'O valor será creditado de acordo com o meio de pagamento utilizado na compra.',
                    'ctaUrl' => url('/minha-conta/cancelamentos'),
                    'ctaText' => 'Ver Meus Cancelamentos',
                ]
            );
        }

        $this->flash('success', 'Reembolso registrado com sucesso. O cliente foi notificado por e-mail.');
        $this->redirect('/admin/cancelamentos/' . $id);
    }
}
