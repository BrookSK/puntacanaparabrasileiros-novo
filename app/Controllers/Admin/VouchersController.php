<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Voucher;
use App\Services\VoucherService;
use App\Services\EmailService;

class VouchersController extends Controller
{
    private Voucher $voucherModel;

    public function __construct()
    {
        parent::__construct();
        $this->voucherModel = new Voucher();
    }

    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $vouchers = $this->voucherModel->getAllWithDetails($page, 20);

        $this->view('admin/vouchers/index', [
            'vouchers' => $vouchers,
            'pageTitle' => 'Gerenciar Vouchers',
        ], 'admin');
    }

    public function view(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $voucher = $this->voucherModel->find($id);
        if (!$voucher) $this->abort(404);

        $filePath = BASE_PATH . '/public/uploads/vouchers/' . $voucher['file_path'];
        if (!file_exists($filePath)) {
            $this->abort(404, 'Arquivo do voucher não encontrado.');
        }

        // Servir o HTML direto
        $response->setHeader('Content-Type', 'text/html; charset=utf-8');
        $response->setBody(file_get_contents($filePath));
        $response->send();
    }

    public function download(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $voucher = $this->voucherModel->find($id);
        if (!$voucher) $this->abort(404);

        $filePath = BASE_PATH . '/public/uploads/vouchers/' . $voucher['file_path'];
        if (!file_exists($filePath)) {
            $this->abort(404, 'Arquivo não encontrado.');
        }

        $response->download($filePath, 'voucher-' . $voucher['reference_code'] . '.html');
    }

    public function send(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $voucher = $this->voucherModel->find($id);
        if (!$voucher) $this->abort(404);

        $email = $request->input('email');
        if (!$email && $voucher['booking_id']) {
            $booking = $this->db->fetchOne("SELECT billing_email FROM bookings WHERE id = ?", [(int) $voucher['booking_id']]);
            $email = $booking['billing_email'] ?? null;
        }

        if (!$email) {
            $this->flash('error', 'Email não encontrado.');
            $this->redirect('/admin/vouchers');
            return;
        }

        $filePath = BASE_PATH . '/public/uploads/vouchers/' . $voucher['file_path'];
        $emailService = new EmailService();
        $sent = $emailService->send(
            $email, 'Cliente',
            'Seu Voucher - Punta Cana para Brasileiros',
            '<p>Segue seu voucher em anexo.</p>',
            file_exists($filePath) ? [$filePath] : []
        );

        if ($sent) {
            $this->voucherModel->markEmailSent($id);
            $this->flash('success', 'Voucher enviado para ' . $email);
        } else {
            $this->flash('error', 'Falha ao enviar email.');
        }

        $this->redirect('/admin/vouchers');
    }
}
