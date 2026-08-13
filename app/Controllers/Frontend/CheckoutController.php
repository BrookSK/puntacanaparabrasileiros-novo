<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Services\CartService;
use App\Services\PaymentService;
use App\Services\PayPalService;
use App\Services\StripeService;
use App\Services\VoucherService;
use App\Services\EmailService;
use App\Services\WhatsAppService;
use App\Services\AffiliateService;
use App\Models\Booking;
use App\Models\TransferBooking;

class CheckoutController extends Controller
{
    private CartService $cartService;
    private PaymentService $paymentService;
    private Booking $bookingModel;

    public function __construct()
    {
        parent::__construct();
        $this->cartService = new CartService();
        $this->paymentService = new PaymentService();
        $this->bookingModel = new Booking();
    }

    public function index(Request $request, Response $response): void
    {
        if ($this->cartService->isEmpty()) {
            $this->flash('error', 'Seu carrinho está vazio.');
            $this->redirect('/carrinho');
            return;
        }

        $summary = $this->cartService->getSummary();
        $gateways = $this->paymentService->getAvailableGateways();
        $partialEnabled = $this->paymentService->isPartialPaymentEnabled();

        $paypalService = new PayPalService();
        $stripeService = new StripeService();

        // Calcular valor parcial efetivo considerando % por passeio
        $globalPercent = (float) $this->setting('partial_payment_percent', '50');
        $effectivePayAmount = 0;
        $tripModel = new \App\Models\Trip();
        foreach ($summary['trips'] as $tripItem) {
            $trip = $tripModel->find((int) $tripItem['trip_id']);
            if ($trip && !empty($trip['partial_payment_enabled']) && (float)($trip['partial_payment_percent'] ?? 0) > 0) {
                $itemPercent = (float) $trip['partial_payment_percent'];
            } else {
                $itemPercent = $globalPercent;
            }
            $effectivePayAmount += round((float)$tripItem['total'] * ($itemPercent / 100), 2);
        }
        foreach ($summary['transfers'] as $transferItem) {
            $effectivePayAmount += round((float)$transferItem['total'] * ($globalPercent / 100), 2);
        }
        if ($effectivePayAmount <= 0) {
            $effectivePayAmount = round($summary['grand_total'] * ($globalPercent / 100), 2);
        }
        // Calcular percentual efetivo para exibição
        $effectivePercent = $summary['grand_total'] > 0 ? round(($effectivePayAmount / $summary['grand_total']) * 100) : $globalPercent;

        $this->view('frontend/checkout/index', [
            'cart' => $summary,
            'gateways' => $gateways,
            'partialEnabled' => $partialEnabled,
            'partialPercent' => $effectivePercent,
            'partialAmount' => $effectivePayAmount,
            'paypalClientId' => $paypalService->getClientId(),
            'stripePublishableKey' => $stripeService->getPublishableKey(),
            'pageTitle' => 'Checkout',
        ], 'app');
    }

    public function process(Request $request, Response $response): void
    {
        // Validar CSRF (aceita _token do body ou X-CSRF-TOKEN do header)
        $token = $request->input('_token', '') ?: ($request->header('X-CSRF-TOKEN') ?? '');
        if (empty($token) || !$this->session->validateCsrfToken($token)) {
            $this->json(['success' => false, 'error' => 'Sessão expirada. Recarregue a página e tente novamente.'], 419);
            return;
        }

        if ($this->cartService->isEmpty()) {
            $this->json(['error' => 'Carrinho vazio.'], 400);
            return;
        }

        $this->db->beginTransaction();

        try {
            $summary = $this->cartService->getSummary();

            // Dados de billing
            $billing = $request->only([
                'first_name', 'last_name', 'email', 'phone',
                'address', 'city', 'country',
            ]);

            // Criar booking
            $bookingNumber = $this->bookingModel->generateBookingNumber();
            $paymentMode = 'partial';
            $gateway = $request->input('gateway', 'paypal');
            $total = $summary['grand_total'];
            $globalPercent = (float) $this->setting('partial_payment_percent', '50');

            // Calcular valor parcial considerando % específico de cada passeio
            $payAmount = 0;
            $tripModel = new \App\Models\Trip();
            foreach ($summary['trips'] as $tripItem) {
                $trip = $tripModel->find((int) $tripItem['trip_id']);
                if ($trip && !empty($trip['partial_payment_enabled']) && (float)($trip['partial_payment_percent'] ?? 0) > 0) {
                    $itemPercent = (float) $trip['partial_payment_percent'];
                } else {
                    $itemPercent = $globalPercent;
                }
                $payAmount += round((float)$tripItem['total'] * ($itemPercent / 100), 2);
            }
            // Transfers usam o percentual global
            foreach ($summary['transfers'] as $transferItem) {
                $payAmount += round((float)$transferItem['total'] * ($globalPercent / 100), 2);
            }
            // Se não calculou nada (carrinho vazio?), fallback pro global
            if ($payAmount <= 0) {
                $payAmount = round($total * ($globalPercent / 100), 2);
            }

            // Verificar afiliado
            $affiliateService = new AffiliateService();
            $affiliateId = $affiliateService->getActiveAffiliateId();

            $bookingId = $this->bookingModel->create([
                'user_id' => $this->currentUser() ? (int) $this->currentUser()['id'] : null,
                'booking_number' => $bookingNumber,
                'status' => 'pending',
                'subtotal' => $total,
                'total' => $total,
                'paid_amount' => 0,
                'due_amount' => $total,
                'payment_mode' => $paymentMode,
                'currency' => 'USD',
                'billing_first_name' => $billing['first_name'],
                'billing_last_name' => $billing['last_name'],
                'billing_email' => $billing['email'],
                'billing_phone' => $billing['phone'] ?? null,
                'billing_address' => $billing['address'] ?? null,
                'billing_city' => $billing['city'] ?? null,
                'billing_country' => $billing['country'] ?? null,
                'affiliate_id' => $affiliateId,
                'ip_address' => $request->ip(),
            ]);

            // Criar booking items (trips)
            foreach ($summary['trips'] as $tripItem) {
                $itemId = $this->db->insert('booking_items', [
                    'booking_id' => $bookingId,
                    'trip_id' => (int) $tripItem['trip_id'],
                    'package_id' => (int) $tripItem['package_id'],
                    'trip_date' => $tripItem['date'],
                    'trip_time' => $tripItem['time'] ?? null,
                    'hotel_name' => $tripItem['hotel_name'] ?? null,
                    'pickup_time' => $tripItem['pickup_time'] ?? null,
                    'pax' => json_encode($tripItem['pax']),
                    'extra_services' => !empty($tripItem['extra_services']) ? json_encode($tripItem['extra_services']) : null,
                    'price' => $tripItem['total'],
                    'group_discount' => $tripItem['group_discount'] ?? 0,
                ]);

                // Salvar dados dos viajantes
                $travelers = $request->input('travelers_' . $tripItem['id'], []);
                foreach ($travelers as $traveler) {
                    if (!empty($traveler['name'])) {
                        $this->db->insert('booking_travelers', [
                            'booking_item_id' => $itemId,
                            'full_name' => $traveler['name'],
                            'age_group' => $traveler['age_group'] ?? null,
                            'traveler_category_id' => $traveler['category_id'] ?? null,
                        ]);
                    }
                }
            }

            // Criar transfer bookings
            foreach ($summary['transfers'] as $transfer) {
                // Verificar capacidade antes de criar
                $transferPax = (int) ($transfer['adults'] ?? 1) + (int) ($transfer['children'] ?? 0) + (int) ($transfer['infants'] ?? 0);
                $bookedPax = (int) $this->db->fetchColumn(
                    "SELECT COALESCE(SUM(adults + children + infants), 0) FROM transfer_bookings WHERE vehicle_id = ? AND date = ? AND status != 'cancelled'",
                    [(int) $transfer['vehicle_id'], $transfer['date']]
                );
                $vehicleData = $this->db->fetchOne("SELECT max_passengers, title FROM transfer_vehicles WHERE id = ?", [(int) $transfer['vehicle_id']]);
                $maxCap = (int) ($vehicleData['max_passengers'] ?? 99);
                if ($bookedPax + $transferPax > $maxCap) {
                    $this->db->rollback();
                    $this->json(['success' => false, 'error' => 'O veículo "' . ($vehicleData['title'] ?? '') . '" está lotado para o dia ' . $transfer['date'] . '. Capacidade máxima atingida.'], 400);
                    return;
                }

                $this->db->insert('transfer_bookings', [
                    'booking_id' => $bookingId,
                    'group_id' => $transfer['group_id'] ?? null,
                    'vehicle_id' => (int) $transfer['vehicle_id'],
                    'origin_id' => (int) $transfer['origin_id'],
                    'destination_id' => (int) $transfer['destination_id'],
                    'date' => $transfer['date'],
                    'time' => $transfer['time'],
                    'type' => $transfer['type'],
                    'service_type' => $transfer['service_type'],
                    'price' => (float) $transfer['price'],
                    'adults' => (int) ($transfer['adults'] ?? 1),
                    'children' => (int) ($transfer['children'] ?? 0),
                    'infants' => (int) ($transfer['infants'] ?? 0),
                    'customer_name' => $billing['first_name'] . ' ' . $billing['last_name'],
                    'customer_email' => $billing['email'],
                    'customer_phone' => $billing['phone'] ?? null,
                    'passengers' => isset($transfer['passengers']) ? json_encode($transfer['passengers']) : null,
                    'flight_number' => $transfer['flight_number'] ?? null,
                    'flight_time' => $transfer['flight_time'] ?? null,
                    'status' => 'pending',
                ]);
            }

            // Criar pagamento pendente
            $paymentId = $this->paymentService->createPendingPayment($bookingId, $gateway, $payAmount, $paymentMode === 'partial' ? 'partial' : 'full');

            $this->db->commit();

            // Retornar dados para o frontend processar o pagamento
            $responseData = [
                'success' => true,
                'booking_id' => $bookingId,
                'booking_number' => $bookingNumber,
                'payment_id' => $paymentId,
                'amount' => $payAmount,
                'gateway' => $gateway,
            ];

            // Se Stripe, criar PaymentIntent
            if ($gateway === 'stripe') {
                try {
                    $stripeService = new StripeService();
                    $intent = $stripeService->createPaymentIntent($payAmount, 'usd', [
                        'booking_id' => $bookingId,
                        'payment_id' => $paymentId,
                    ]);
                    $responseData['stripe_client_secret'] = $intent['client_secret'];
                } catch (\Throwable $stripeError) {
                    $this->json([
                        'success' => false,
                        'error' => 'Erro ao processar cartão. Verifique se o Stripe está configurado. Detalhes: ' . $stripeError->getMessage(),
                    ], 400);
                    return;
                }
            }

            // Se PayPal, criar order
            if ($gateway === 'paypal') {
                try {
                    $paypalService = new PayPalService();
                    $order = $paypalService->createOrder($payAmount, 'USD', 'Reserva ' . $bookingNumber);
                    $responseData['paypal_order_id'] = $order['id'];
                } catch (\Throwable $paypalError) {
                    $this->json([
                        'success' => false,
                        'error' => 'Erro ao processar PayPal. Verifique se o PayPal está configurado. Detalhes: ' . $paypalError->getMessage(),
                    ], 400);
                    return;
                }
            }

            // Se PIX (PagBank), criar cobrança
            if ($gateway === 'pix') {
                try {
                    $pagBankService = new \App\Services\PagBankService();
                    $customer = [
                        'name' => ($billing['first_name'] ?? '') . ' ' . ($billing['last_name'] ?? ''),
                        'email' => $billing['email'] ?? '',
                        'cpf' => preg_replace('/\D/', '', $request->input('cpf', '')),
                    ];
                    $pixCharge = $pagBankService->createPixCharge(
                        $payAmount,
                        'Reserva ' . $bookingNumber,
                        $customer,
                        $bookingNumber
                    );
                    $responseData['pix'] = $pixCharge;

                    // Salvar charge_id no pagamento para consulta posterior
                    $this->db->update('payments', [
                        'transaction_id' => $pixCharge['charge_id'],
                    ], 'id = ?', [$paymentId]);
                } catch (\Throwable $pixError) {
                    // Se falhar ao criar PIX, retornar erro amigável
                    $this->json([
                        'success' => false,
                        'error' => 'Erro ao gerar PIX. Verifique se o PagBank está configurado corretamente. Detalhes: ' . $pixError->getMessage(),
                    ], 400);
                    return;
                }
            }

            // Se Simulação (apenas SuperAdmin), aprovar instantaneamente
            if ($gateway === 'simulate') {
                $currentUser = $this->currentUser();
                if (!$currentUser || ($currentUser['role'] ?? '') !== 'superadmin') {
                    $this->json(['success' => false, 'error' => 'Acesso negado.'], 403);
                    return;
                }

                // Confirmar pagamento imediatamente
                try {
                    $this->paymentService->confirmPayment(
                        $paymentId,
                        'SIM-' . strtoupper(bin2hex(random_bytes(8))),
                        json_encode(['gateway' => 'simulate', 'approved_at' => date('c')])
                    );
                } catch (\Throwable $simErr) {
                    $this->json(['success' => false, 'error' => 'Erro na simulação: ' . $simErr->getMessage()]);
                    return;
                }

                // Executar ações pós-pagamento (vouchers, emails, WhatsApp, comissões)
                try {
                    $this->postPaymentActions($bookingId);
                } catch (\Throwable $e) {
                    // Não bloquear por erro em notificações
                }

                // Limpar carrinho
                $this->cartService->clearAll();

                $responseData['gateway'] = 'simulate';
                $responseData['redirect'] = '/checkout/sucesso/' . $bookingNumber;
                $this->json($responseData);
                return;
            }

            $this->json($responseData);

        } catch (\Throwable $e) {
            try { $this->db->rollback(); } catch (\Throwable $rb) {}
            $this->json(['success' => false, 'error' => 'Erro ao processar reserva: ' . $e->getMessage()], 500);
        }
    }

    public function success(Request $request, Response $response): void
    {
        $bookingNumber = $request->param('booking_number', '');
        $booking = $this->bookingModel->findByNumber($bookingNumber);

        if (!$booking) {
            $this->redirect('/');
            return;
        }

        // Limpar carrinho
        $this->cartService->clearAll();

        // Gerar vouchers e enviar notificações (se ainda não feito)
        $this->postPaymentActions((int) $booking['id']);

        $items = $this->bookingModel->getItems((int) $booking['id']);
        $transfers = $this->bookingModel->getTransferBookings((int) $booking['id']);

        // Buscar documentos extras dos passeios
        $tripDocuments = [];
        foreach ($items as $item) {
            $trip = $this->db->fetchOne("SELECT documents FROM trips WHERE id = ?", [(int) $item['trip_id']]);
            if ($trip && !empty($trip['documents'])) {
                $docs = json_decode($trip['documents'], true);
                if (is_array($docs)) {
                    foreach ($docs as $doc) {
                        $doc['trip_name'] = $item['trip_title'] ?? '';
                        $tripDocuments[] = $doc;
                    }
                }
            }
        }

        $this->view('frontend/checkout/success', [
            'booking' => $booking,
            'items' => $items,
            'transfers' => $transfers,
            'tripDocuments' => $tripDocuments,
            'pageTitle' => 'Reserva Confirmada!',
        ], 'app');
    }

    public function transferSuccess(Request $request, Response $response): void
    {
        $this->cartService->clearAll();

        $this->view('frontend/checkout/success', [
            'booking' => null,
            'isTransferOnly' => true,
            'pageTitle' => 'Transfer Reservado com Sucesso!',
        ], 'app');
    }

    /**
     * Ações pós-pagamento: vouchers, emails, WhatsApp, comissões.
     * Método público para poder ser chamado pelo WebhookController
     * após confirmação de pagamento via PayPal/Stripe/PagBank.
     */
    public function postPaymentActions(int $bookingId): void
    {
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking || $booking['status'] === 'pending') return;

        // Verificar se vouchers já foram gerados (evitar duplicação)
        $existingVouchers = $this->db->fetchOne(
            "SELECT COUNT(*) as total FROM vouchers WHERE booking_id = ?",
            [$bookingId]
        );
        if ((int)($existingVouchers['total'] ?? 0) > 0) return;

        // Gerar vouchers para cada item de trip
        $voucherService = new VoucherService();
        $items    = $this->bookingModel->getItems($bookingId);
        $transfers = $this->bookingModel->getTransferBookings($bookingId);

        foreach ($items as $item) {
            try {
                $voucherService->generateTripVoucher($bookingId, (int) $item['id']);
            } catch (\Throwable $e) { /* continua */ }
        }

        // Gerar vouchers para transfers
        foreach ($transfers as $transfer) {
            try {
                $voucherService->generateTransferVoucher((int) $transfer['id']);
            } catch (\Throwable $e) { /* continua */ }
        }

        // ── EMAIL AO CLIENTE ───────────────────────────────────────────────

        $emailService = new EmailService();

        // Email único com vouchers (já inclui detalhes da reserva + vouchers para passeios e transfers)
        $voucherService->sendVouchersByEmail($bookingId);

        // ── NOTIFICAÇÃO ADMIN ──────────────────────────────────────────────

        $adminEmail = $this->setting('admin_email', '');
        if ($adminEmail) {
            $emailService->sendTemplate(
                $adminEmail, 'Admin',
                'Nova Reserva: ' . $booking['booking_number'],
                'admin-notification',
                [
                    'booking'   => $booking,
                    'items'     => $items,
                    'transfers' => $transfers,
                    'siteUrl'   => $this->setting('site_url', ''),
                ]
            );
        }

        // ── WHATSAPP ───────────────────────────────────────────────────────

        $whatsappService = new WhatsAppService();

        // WhatsApp para passeios
        if (!empty($items)) {
            try {
                $whatsappService->sendTripConfirmation($booking, [
                    'title'     => $items[0]['trip_title'] ?? '',
                    'date'      => $items[0]['trip_date'] ?? '',
                    'time'      => $items[0]['trip_time'] ?? '',
                    'pax_info'  => '',
                    'reference' => $booking['booking_number'],
                ]);
            } catch (\Throwable $e) { /* continua */ }
        }

        // WhatsApp para transfers (todos os tipos)
        if (!empty($transfers)) {
            foreach ($transfers as $tr) {
                try {
                    // Detecta tipo para label amigável
                    $trType = match(strtolower($tr['type'] ?? '')) {
                        'arrival'   => 'Chegada',
                        'departure' => 'Partida',
                        default     => 'Transfer',
                    };
                    $trTitle = $trType . ': ' . ($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? '');

                    $whatsappService->sendTripConfirmation($booking, [
                        'title'     => $trTitle,
                        'date'      => $tr['date'] ?? '',
                        'time'      => $tr['time'] ?? '',
                        'pax_info'  => ($tr['adults'] ?? 1) . ' adulto(s)',
                        'reference' => $booking['booking_number'],
                    ]);
                } catch (\Throwable $e) { /* continua */ }
            }
        }

        // ── COMISSÃO DE AFILIADO ───────────────────────────────────────────

        if ($booking['affiliate_id']) {
            $affiliateService = new AffiliateService();
            $affiliateService->createCommission(
                (int) $booking['affiliate_id'],
                $bookingId,
                (float) $booking['total']
            );
        }
    }
}
