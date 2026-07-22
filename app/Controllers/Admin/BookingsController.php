<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Booking;
use App\Services\VoucherService;
use App\Services\EmailService;

class BookingsController extends Controller
{
    private Booking $bookingModel;

    public function __construct()
    {
        parent::__construct();
        $this->bookingModel = new Booking();
    }

    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $status = $request->query('status');
        $search = $request->query('busca');

        $where = '1=1';
        $params = [];

        if ($status) {
            $where .= ' AND status = ?';
            $params[] = $status;
        }
        if ($search) {
            $where .= ' AND (booking_number LIKE ? OR billing_email LIKE ? OR billing_first_name LIKE ?)';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
            $params[] = '%' . $search . '%';
        }

        $bookings = $this->bookingModel->paginate($page, 20, $where, $params, 'created_at DESC');

        $this->view('admin/bookings/index', [
            'bookings' => $bookings,
            'currentStatus' => $status,
            'currentSearch' => $search,
            'pageTitle' => 'Gerenciar Reservas',
        ], 'admin');
    }

    public function show(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $booking = $this->bookingModel->find($id);
        if (!$booking) $this->abort(404);

        $items = $this->bookingModel->getItems($id);
        $transfers = $this->bookingModel->getTransferBookings($id);
        $payments = $this->bookingModel->getPayments($id);
        $vouchers = $this->bookingModel->getVouchers($id);

        // Travelers por item
        foreach ($items as &$item) {
            $item['travelers'] = $this->bookingModel->getTravelers((int) $item['id']);
        }

        $this->view('admin/bookings/show', [
            'booking' => $booking,
            'items' => $items,
            'transfers' => $transfers,
            'payments' => $payments,
            'vouchers' => $vouchers,
            'pageTitle' => 'Reserva: ' . $booking['booking_number'],
        ], 'admin');
    }

    public function updateStatus(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $status = $request->input('status', '');

        $allowedStatuses = ['pending', 'booked', 'partially_paid', 'completed', 'cancelled', 'refunded'];
        if (!in_array($status, $allowedStatuses)) {
            $this->flash('error', 'Status inválido.');
            $this->redirect('/admin/reservas/' . $id);
            return;
        }

        $this->bookingModel->updateStatus($id, $status);

        // Se confirmado, gerar vouchers se não existem
        if ($status === 'booked') {
            $vouchers = $this->bookingModel->getVouchers($id);
            if (empty($vouchers)) {
                $voucherService = new VoucherService();
                $items = $this->bookingModel->getItems($id);
                foreach ($items as $item) {
                    try {
                        $voucherService->generateTripVoucher($id, (int) $item['id']);
                    } catch (\Throwable $e) {}
                }
                $transfers = $this->bookingModel->getTransferBookings($id);
                foreach ($transfers as $transfer) {
                    try {
                        $voucherService->generateTransferVoucher((int) $transfer['id']);
                    } catch (\Throwable $e) {}
                }
            }
        }

        $this->flash('success', 'Status atualizado para: ' . $status);
        $this->redirect('/admin/reservas/' . $id);
    }

    public function create(Request $request, Response $response): void
    {
        $this->view('admin/bookings/create', [
            'pageTitle' => 'Criar Reserva Manual',
        ], 'admin');
    }

    public function store(Request $request, Response $response): void
    {
        // Criar booking manual (para vendas offline)
        $bookingNumber = $this->bookingModel->generateBookingNumber();
        $data = $request->only([
            'billing_first_name', 'billing_last_name', 'billing_email',
            'billing_phone', 'billing_country', 'total', 'notes',
        ]);

        $data['booking_number'] = $bookingNumber;
        $data['status'] = 'booked';
        $data['subtotal'] = (float) ($data['total'] ?? 0);
        $data['paid_amount'] = (float) ($data['total'] ?? 0);
        $data['due_amount'] = 0;
        $data['payment_mode'] = 'full';
        $data['currency'] = 'USD';
        $data['ip_address'] = $request->ip();

        $bookingId = $this->bookingModel->create($data);

        // Registrar pagamento manual
        $this->db->insert('payments', [
            'booking_id' => $bookingId,
            'gateway' => 'manual',
            'amount' => (float) ($data['total'] ?? 0),
            'currency' => 'USD',
            'status' => 'completed',
            'type' => 'full',
        ]);

        $this->flash('success', 'Reserva manual criada: ' . $bookingNumber);
        $this->redirect('/admin/reservas/' . $bookingId);
    }
}
