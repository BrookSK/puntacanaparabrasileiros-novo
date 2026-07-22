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

        $this->view('frontend/checkout/index', [
            'cart' => $summary,
            'gateways' => $gateways,
            'partialEnabled' => $partialEnabled,
            'partialPercent' => (float) $this->setting('partial_payment_percent', '50'),
            'paypalClientId' => $paypalService->getClientId(),
            'stripePublishableKey' => $stripeService->getPublishableKey(),
            'pageTitle' => 'Checkout',
        ], 'app');
    }

    public function process(Request $request, Response $response): void
    {
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
            $paymentMode = $request->input('payment_mode', 'full');
            $gateway = $request->input('gateway', 'paypal');
            $total = $summary['grand_total'];
            $payAmount = $total;

            if ($paymentMode === 'partial' && $this->paymentService->isPartialPaymentEnabled()) {
                $payAmount = $this->paymentService->calculatePartialAmount($total);
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
                $stripeService = new StripeService();
                $intent = $stripeService->createPaymentIntent($payAmount, 'usd', [
                    'booking_id' => $bookingId,
                    'payment_id' => $paymentId,
                ]);
                $responseData['stripe_client_secret'] = $intent['client_secret'];
            }

            // Se PayPal, criar order
            if ($gateway === 'paypal') {
                $paypalService = new PayPalService();
                $order = $paypalService->createOrder($payAmount, 'USD', 'Reserva ' . $bookingNumber);
                $responseData['paypal_order_id'] = $order['id'];
            }

            $this->json($responseData);

        } catch (\Throwable $e) {
            $this->db->rollback();
            $this->json(['error' => 'Erro ao processar reserva: ' . $e->getMessage()], 500);
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

        $this->view('frontend/checkout/success', [
            'booking' => $booking,
            'items' => $items,
            'transfers' => $transfers,
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
     */
    private function postPaymentActions(int $bookingId): void
    {
        $booking = $this->bookingModel->find($bookingId);
        if (!$booking || $booking['status'] === 'pending') return;

        // Gerar vouchers para cada item de trip
        $voucherService = new VoucherService();
        $items = $this->bookingModel->getItems($bookingId);
        foreach ($items as $item) {
            try {
                $voucherService->generateTripVoucher($bookingId, (int) $item['id']);
            } catch (\Throwable $e) {
                // Log error but continue
            }
        }

        // Gerar vouchers para transfers
        $transfers = $this->bookingModel->getTransferBookings($bookingId);
        foreach ($transfers as $transfer) {
            try {
                $voucherService->generateTransferVoucher((int) $transfer['id']);
            } catch (\Throwable $e) {
                // Log error but continue
            }
        }

        // Enviar vouchers por email
        $voucherService->sendVouchersByEmail($bookingId);

        // Enviar notificação admin
        $emailService = new EmailService();
        $adminEmail = $this->setting('admin_email', '');
        if ($adminEmail) {
            $emailService->sendTemplate(
                $adminEmail, 'Admin',
                'Nova Reserva: ' . $booking['booking_number'],
                'admin-notification',
                ['booking' => $booking, 'items' => $items, 'transfers' => $transfers]
            );
        }

        // WhatsApp
        $whatsappService = new WhatsAppService();
        if (!empty($items)) {
            $whatsappService->sendTripConfirmation($booking, [
                'title' => $items[0]['trip_title'] ?? '',
                'date' => $items[0]['trip_date'] ?? '',
                'time' => $items[0]['trip_time'] ?? '',
                'pax_info' => '',
                'reference' => $booking['booking_number'],
            ]);
        }

        // Comissão de afiliado
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
