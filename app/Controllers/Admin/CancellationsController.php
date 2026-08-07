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

        if ($clientEmail) {
            $emailService = new EmailService();
            $emailService->send(
                $clientEmail, trim($clientName),
                'Cancelamento Aprovado - ' . ($booking['booking_number'] ?? ''),
                '<h2>Cancelamento Aprovado</h2>'
                . '<p>Olá, <strong>' . e(trim($clientName)) . '</strong>!</p>'
                . '<p>Sua solicitação de cancelamento para a reserva <strong>' . e($booking['booking_number'] ?? '') . '</strong> foi <strong style="color:#1B6F00">aprovada</strong>.</p>'
                . ($adminResponse ? '<p><strong>Mensagem da equipe:</strong></p><blockquote style="border-left:4px solid #1B6F00;padding:12px;background:#f0fdf4;margin:10px 0;">' . nl2br(e($adminResponse)) . '</blockquote>' : '')
                . '<p>Caso tenha direito a reembolso, você será notificado em breve sobre o processamento.</p>'
                . '<p style="color:#636e72;font-size:13px;">Equipe Punta Cana para Brasileiros</p>'
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

        if ($clientEmail) {
            $emailService = new EmailService();
            $emailService->send(
                $clientEmail, trim($clientName),
                'Cancelamento Não Autorizado - ' . ($booking['booking_number'] ?? ''),
                '<h2>Cancelamento Não Autorizado</h2>'
                . '<p>Olá, <strong>' . e(trim($clientName)) . '</strong>!</p>'
                . '<p>Infelizmente, sua solicitação de cancelamento para a reserva <strong>' . e($booking['booking_number'] ?? '') . '</strong> <strong style="color:#e74c3c">não foi autorizada</strong>.</p>'
                . '<p><strong>Motivo:</strong></p>'
                . '<blockquote style="border-left:4px solid #e74c3c;padding:12px;background:#fef2f2;margin:10px 0;">' . nl2br(e($adminResponse)) . '</blockquote>'
                . '<p>Se tiver dúvidas, entre em contato com nossa equipe.</p>'
                . '<p style="color:#636e72;font-size:13px;">Equipe Punta Cana para Brasileiros</p>'
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

        if ($clientEmail) {
            $emailService = new EmailService();
            $emailService->send(
                $clientEmail, trim($clientName),
                'Reembolso Processado - ' . ($booking['booking_number'] ?? ''),
                '<h2>Reembolso Processado</h2>'
                . '<p>Olá, <strong>' . e(trim($clientName)) . '</strong>!</p>'
                . '<p>O reembolso referente à reserva <strong>' . e($booking['booking_number'] ?? '') . '</strong> foi processado.</p>'
                . '<p><strong>Valor reembolsado:</strong> $' . number_format($refundAmount, 2) . '</p>'
                . ($refundNotes ? '<p><strong>Observações:</strong></p><blockquote style="border-left:4px solid #3772C0;padding:12px;background:#eff6ff;margin:10px 0;">' . nl2br(e($refundNotes)) . '</blockquote>' : '')
                . '<p>O valor será creditado de acordo com o meio de pagamento utilizado.</p>'
                . '<p style="color:#636e72;font-size:13px;">Equipe Punta Cana para Brasileiros</p>'
            );
        }

        $this->flash('success', 'Reembolso registrado com sucesso. O cliente foi notificado por e-mail.');
        $this->redirect('/admin/cancelamentos/' . $id);
    }
}
