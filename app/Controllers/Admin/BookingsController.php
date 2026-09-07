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

        // Buscar nomes dos passeios para cada reserva
        if (!empty($bookings['items'])) {
            foreach ($bookings['items'] as &$bk) {
                $items = $this->db->fetchAll(
                    "SELECT t.title FROM booking_items bi INNER JOIN trips t ON bi.trip_id = t.id WHERE bi.booking_id = ?",
                    [(int)$bk['id']]
                );
                $transfers = $this->db->fetchAll(
                    "SELECT CONCAT(tlo.title, ' → ', tld.title) as route FROM transfer_bookings tb INNER JOIN transfer_locations tlo ON tb.origin_id = tlo.id INNER JOIN transfer_locations tld ON tb.destination_id = tld.id WHERE tb.booking_id = ?",
                    [(int)$bk['id']]
                );
                $names = array_merge(array_column($items, 'title'), array_column($transfers, 'route'));
                $bk['service_names'] = implode(', ', $names) ?: '-';
            }
            unset($bk);
        }

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
        unset($item);

        // Agências ativas (para atribuição manual) + comissão já gerada para esta reserva
        $agencies = $this->db->fetchAll(
            "SELECT id, company_name, trade_name, commission_rate FROM agencies WHERE status = 'active' ORDER BY company_name ASC"
        );
        $agencyCommission = $this->db->fetchOne(
            "SELECT * FROM agency_commissions WHERE booking_id = ? ORDER BY id DESC LIMIT 1",
            [$id]
        );

        $this->view('admin/bookings/show', [
            'booking' => $booking,
            'items' => $items,
            'transfers' => $transfers,
            'payments' => $payments,
            'vouchers' => $vouchers,
            'agencies' => $agencies,
            'agencyCommission' => $agencyCommission,
            'pageTitle' => 'Reserva: ' . $booking['booking_number'],
        ], 'admin');
    }

    /**
     * Atribui (ou remove) manualmente uma agência a esta reserva e gera a comissão.
     */
    public function assignAgency(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $agencyId = (int) $request->input('agency_id', '0');

        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            $this->flash('error', 'Reserva não encontrada.');
            $this->redirect('/admin/reservas');
            return;
        }

        // Remover atribuição
        if ($agencyId <= 0) {
            $this->bookingModel->update($id, ['agency_id' => null, 'agency_ref_code' => null]);
            $this->flash('success', 'Agência desvinculada desta reserva. (Comissões já geradas permanecem no histórico e podem ser canceladas na tela de comissões.)');
            $this->redirect('/admin/reservas/' . $id);
            return;
        }

        $agency = (new \App\Models\Agency())->find($agencyId);
        if (!$agency) {
            $this->flash('error', 'Agência inválida.');
            $this->redirect('/admin/reservas/' . $id);
            return;
        }

        // Vincular a agência à reserva
        $this->bookingModel->update($id, [
            'agency_id' => $agencyId,
            'agency_ref_code' => $agency['ref_code'],
        ]);

        // A comissão só é gerada se a reserva já estiver finalizada (paga/confirmada).
        // Se ainda estiver pendente, a comissão será criada quando o pagamento for confirmado.
        $finalizedStatuses = ['booked', 'partially_paid', 'completed'];
        if (in_array($booking['status'] ?? '', $finalizedStatuses, true)) {
            $commissionId = (new \App\Services\AgencyService())->createCommission(
                $agencyId,
                $id,
                (float) $booking['total']
            );
            if ($commissionId) {
                $this->flash('success', 'Agência vinculada e comissão gerada com sucesso.');
            } else {
                $this->flash('success', 'Agência vinculada. (Já existia uma comissão para esta reserva.)');
            }
        } else {
            $this->flash('success', 'Agência vinculada. A comissão será gerada automaticamente quando a compra for finalizada (pagamento confirmado).');
        }
        $this->redirect('/admin/reservas/' . $id);
    }

    // ══════════════════════════════════════════════════════════════
    // CONTROLE DE CAUÇÃO / SINAL
    // ══════════════════════════════════════════════════════════════

    private const PAYMENT_METHODS = ['dinheiro', 'pix', 'cartao', 'transferencia', 'outro'];

    /**
     * Registra o pagamento presencial do RESTANTE (cliente mantém o sinal).
     * Soma ao paid_amount, reduz o due_amount e registra em payments.
     */
    public function registerRemainingPayment(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $amount = round((float) $request->input('amount', '0'), 2);
        $method = $request->input('method', 'dinheiro');
        $notes = trim((string) $request->input('notes', ''));

        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            $this->flash('error', 'Reserva não encontrada.');
            $this->redirect('/admin/reservas');
            return;
        }
        if (!in_array($method, self::PAYMENT_METHODS, true)) {
            $method = 'outro';
        }
        if ($amount <= 0) {
            $this->flash('error', 'Informe um valor válido.');
            $this->redirect('/admin/reservas/' . $id);
            return;
        }

        $newPaid = round((float) ($booking['paid_amount'] ?? 0) + $amount, 2);
        $newDue = round(max(0, (float) ($booking['total'] ?? 0) - $newPaid), 2);

        // Registra o pagamento manual do restante
        $this->db->insert('payments', [
            'booking_id' => $id,
            'gateway' => 'manual',
            'transaction_id' => 'MANUAL-' . strtoupper(bin2hex(random_bytes(4))),
            'amount' => $amount,
            'currency' => $booking['currency'] ?? 'USD',
            'status' => 'completed',
            'type' => 'remaining',
            'method' => $method,
            'notes' => $notes ?: 'Pagamento presencial do valor restante (sinal mantido).',
        ]);

        // Atualiza os valores da reserva; se quitou, marca como concluída
        $update = ['paid_amount' => $newPaid, 'due_amount' => $newDue];
        if ($newDue <= 0 && in_array($booking['status'] ?? '', ['pending', 'partially_paid', 'booked'], true)) {
            $update['status'] = 'completed';
        }
        $this->bookingModel->update($id, $update);

        $this->flash('success', 'Pagamento do restante registrado (' . money($amount) . '). Sinal mantido.');
        $this->redirect('/admin/reservas/' . $id);
    }

    /**
     * Devolve a caução/sinal e (opcionalmente) registra o pagamento TOTAL presencial.
     * Registra a devolução do sinal em payments (deposit_refund) e o pagamento total.
     */
    public function refundDeposit(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $method = $request->input('method', 'dinheiro');
        $notes = trim((string) $request->input('notes', ''));
        $registerFullPayment = $request->input('register_full') ? true : false;

        $booking = $this->bookingModel->find($id);
        if (!$booking) {
            $this->flash('error', 'Reserva não encontrada.');
            $this->redirect('/admin/reservas');
            return;
        }
        if (!in_array($method, self::PAYMENT_METHODS, true)) {
            $method = 'outro';
        }

        $deposit = round((float) ($booking['paid_amount'] ?? 0), 2);
        if ($deposit <= 0) {
            $this->flash('error', 'Esta reserva não possui sinal pago para devolver.');
            $this->redirect('/admin/reservas/' . $id);
            return;
        }

        // 1) Registra a devolução do sinal (valor informativo do que foi estornado)
        $this->db->insert('payments', [
            'booking_id' => $id,
            'gateway' => 'manual',
            'transaction_id' => 'REFUND-' . strtoupper(bin2hex(random_bytes(4))),
            'amount' => $deposit,
            'currency' => $booking['currency'] ?? 'USD',
            'status' => 'refunded',
            'type' => 'deposit_refund',
            'method' => $method,
            'notes' => $notes ?: 'Devolução da caução/sinal ao cliente.',
        ]);

        $total = round((float) ($booking['total'] ?? 0), 2);

        if ($registerFullPayment) {
            // 2) Cliente pagou o total presencialmente após a devolução do sinal.
            $this->db->insert('payments', [
                'booking_id' => $id,
                'gateway' => 'manual',
                'transaction_id' => 'MANUAL-' . strtoupper(bin2hex(random_bytes(4))),
                'amount' => $total,
                'currency' => $booking['currency'] ?? 'USD',
                'status' => 'completed',
                'type' => 'manual_full',
                'method' => $method,
                'notes' => 'Pagamento total presencial (após devolução do sinal).',
            ]);
            // Reserva quitada: total pago, nada pendente
            $this->bookingModel->update($id, [
                'paid_amount' => $total,
                'due_amount' => 0,
                'status' => 'completed',
            ]);
            $this->flash('success', 'Sinal de ' . money($deposit) . ' devolvido e pagamento total de ' . money($total) . ' registrado.');
        } else {
            // Só devolveu o sinal: reserva volta a dever o valor integral.
            $this->bookingModel->update($id, [
                'paid_amount' => 0,
                'due_amount' => $total,
            ]);
            $this->flash('success', 'Sinal de ' . money($deposit) . ' devolvido. A reserva volta a ter o valor integral a pagar.');
        }

        $this->redirect('/admin/reservas/' . $id);
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
