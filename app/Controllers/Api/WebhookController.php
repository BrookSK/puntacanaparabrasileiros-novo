<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Services\PaymentService;
use App\Services\PayPalService;
use App\Services\StripeService;
use App\Services\PagBankService;
use App\Controllers\Frontend\CheckoutController;

class WebhookController extends Controller
{
    /**
     * Dispara ações pós-pagamento (emails, vouchers, WhatsApp) de forma segura.
     */
    private function triggerPostPayment(int $bookingId): void
    {
        try {
            $checkout = new CheckoutController();
            $checkout->postPaymentActions($bookingId);
        } catch (\Throwable $e) {
            // Não bloqueia a resposta ao gateway
        }
    }

    /**
     * Webhook de confirmação de pagamento (PayPal capture callback).
     */
    public function handlePayment(Request $request, Response $response): void
    {
        $data = $request->json();
        $gateway = $data['gateway'] ?? '';
        $paymentId = (int) ($data['payment_id'] ?? 0);
        $transactionId = $data['transaction_id'] ?? '';

        if (!$paymentId || !$transactionId) {
            $this->json(['error' => 'Dados inválidos.'], 400);
            return;
        }

        $paymentService = new PaymentService();

        try {
            if ($gateway === 'paypal') {
                $paypalService = new PayPalService();
                $order = $paypalService->getOrder($transactionId);

                if (($order['status'] ?? '') === 'COMPLETED') {
                    $captureId = $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? $transactionId;
                    $paymentService->confirmPayment($paymentId, $captureId, json_encode($order));

                    // Disparar emails/vouchers/WhatsApp para todos os tipos de transfer
                    $payment = $this->db->fetchOne("SELECT booking_id FROM payments WHERE id = ?", [$paymentId]);
                    if ($payment) $this->triggerPostPayment((int) $payment['booking_id']);

                    $this->json(['success' => true, 'status' => 'completed']);
                    return;
                }

                $this->json(['error' => 'Pagamento PayPal não completado.'], 400);
                return;
            }

            if ($gateway === 'stripe') {
                $stripeService = new StripeService();
                $intent = $stripeService->retrievePaymentIntent($transactionId);

                if (($intent['status'] ?? '') === 'succeeded') {
                    $paymentService->confirmPayment($paymentId, $transactionId, json_encode($intent));

                    // Disparar emails/vouchers/WhatsApp para todos os tipos de transfer
                    $payment = $this->db->fetchOne("SELECT booking_id FROM payments WHERE id = ?", [$paymentId]);
                    if ($payment) $this->triggerPostPayment((int) $payment['booking_id']);

                    $this->json(['success' => true, 'status' => 'completed']);
                    return;
                }

                $this->json(['error' => 'Pagamento Stripe não confirmado.'], 400);
                return;
            }

            $this->json(['error' => 'Gateway não suportado.'], 400);

        } catch (\Throwable $e) {
            $paymentService->failPayment($paymentId, $e->getMessage());
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Webhook do Stripe (events: payment_intent.succeeded, etc.).
     */
    public function handleStripe(Request $request, Response $response): void
    {
        $payload = $request->rawBody();
        $sigHeader = $request->header('stripe-signature', '');

        $event = json_decode($payload, true);
        if (!$event) {
            $this->json(['error' => 'Payload inválido.'], 400);
            return;
        }

        $type = $event['type'] ?? '';
        $object = $event['data']['object'] ?? [];

        switch ($type) {
            case 'payment_intent.succeeded':
                $this->handleStripePaymentSucceeded($object);
                break;

            case 'payment_intent.payment_failed':
                $this->handleStripePaymentFailed($object);
                break;
        }

        $this->json(['received' => true]);
    }

    private function handleStripePaymentSucceeded(array $intent): void
    {
        $paymentId = (int) ($intent['metadata']['payment_id'] ?? 0);
        if (!$paymentId) return;

        $paymentService = new PaymentService();
        try {
            $paymentService->confirmPayment($paymentId, $intent['id'], json_encode($intent));

            // Disparar emails/vouchers/WhatsApp
            $payment = $this->db->fetchOne("SELECT booking_id FROM payments WHERE id = ?", [$paymentId]);
            if ($payment) $this->triggerPostPayment((int) $payment['booking_id']);
        } catch (\Throwable $e) { /* log error */ }
    }

    private function handleStripePaymentFailed(array $intent): void
    {
        $paymentId = (int) ($intent['metadata']['payment_id'] ?? 0);
        if (!$paymentId) return;

        $paymentService = new PaymentService();
        $paymentService->failPayment($paymentId, json_encode($intent));
    }

    /**
     * Verifica status do pagamento PIX (polling do frontend).
     */
    public function pixStatus(Request $request, Response $response): void
    {
        $data = $request->json();
        $paymentId = (int) ($data['payment_id'] ?? 0);

        if (!$paymentId) {
            $this->json(['error' => 'Payment ID inválido.'], 400);
            return;
        }

        $payment = $this->db->fetchOne("SELECT * FROM payments WHERE id = ?", [$paymentId]);
        if (!$payment) {
            $this->json(['error' => 'Pagamento não encontrado.'], 404);
            return;
        }

        if ($payment['status'] === 'completed') {
            $this->json(['paid' => true]);
            return;
        }

        $chargeId = $payment['transaction_id'] ?? '';
        if (!$chargeId) {
            $this->json(['paid' => false, 'status' => 'waiting']);
            return;
        }

        try {
            $pagBankService = new PagBankService();
            if ($pagBankService->isChargePaid($chargeId)) {
                $paymentService = new PaymentService();
                $paymentService->confirmPayment($paymentId, $chargeId, json_encode(['status' => 'PAID']));

                // Disparar emails/vouchers/WhatsApp
                $this->triggerPostPayment((int) $payment['booking_id']);

                $this->json(['paid' => true]);
            } else {
                $this->json(['paid' => false, 'status' => 'waiting']);
            }
        } catch (\Throwable $e) {
            $this->json(['paid' => false, 'status' => 'error']);
        }
    }

    /**
     * Webhook do PagBank (notificação de pagamento PIX).
     */
    public function handlePagBank(Request $request, Response $response): void
    {
        $payload = $request->rawBody();
        $event = json_decode($payload, true);

        if (!$event) {
            $this->json(['error' => 'Payload inválido.'], 400);
            return;
        }

        $chargeId = $event['id'] ?? '';
        $status = $event['status'] ?? '';

        if ($status === 'PAID' && $chargeId) {
            $payment = $this->db->fetchOne(
                "SELECT * FROM payments WHERE transaction_id = ? AND status = 'pending'",
                [$chargeId]
            );

            if ($payment) {
                $paymentService = new PaymentService();
                $paymentService->confirmPayment((int) $payment['id'], $chargeId, $payload);

                // Disparar emails/vouchers/WhatsApp
                $this->triggerPostPayment((int) $payment['booking_id']);
            }
        }

        $this->json(['received' => true]);
    }
}

        $data = $request->json();
        $gateway = $data['gateway'] ?? '';
        $paymentId = (int) ($data['payment_id'] ?? 0);
        $transactionId = $data['transaction_id'] ?? '';

        if (!$paymentId || !$transactionId) {
            $this->json(['error' => 'Dados inválidos.'], 400);
            return;
        }

        $paymentService = new PaymentService();

        try {
            if ($gateway === 'paypal') {
                // Verificar captura no PayPal
                $paypalService = new PayPalService();
                $order = $paypalService->getOrder($transactionId);

                if (($order['status'] ?? '') === 'COMPLETED') {
                    $captureId = $order['purchase_units'][0]['payments']['captures'][0]['id'] ?? $transactionId;
                    $paymentService->confirmPayment($paymentId, $captureId, json_encode($order));
                    $this->json(['success' => true, 'status' => 'completed']);
                    return;
                }

                $this->json(['error' => 'Pagamento PayPal não completado.'], 400);
                return;
            }

            if ($gateway === 'stripe') {
                // Para Stripe, a confirmação vem via client-side (PaymentIntent succeeded)
                $stripeService = new StripeService();
                $intent = $stripeService->retrievePaymentIntent($transactionId);

                if (($intent['status'] ?? '') === 'succeeded') {
                    $paymentService->confirmPayment($paymentId, $transactionId, json_encode($intent));
                    $this->json(['success' => true, 'status' => 'completed']);
                    return;
                }

                $this->json(['error' => 'Pagamento Stripe não confirmado.'], 400);
                return;
            }

            $this->json(['error' => 'Gateway não suportado.'], 400);

        } catch (\Throwable $e) {
            $paymentService->failPayment($paymentId, $e->getMessage());
            $this->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Webhook do Stripe (events: payment_intent.succeeded, etc.).
     */
    public function handleStripe(Request $request, Response $response): void
    {
        $payload = $request->rawBody();
        $sigHeader = $request->header('stripe-signature', '');

        // Em produção, verificar assinatura do webhook
        // $stripeService = new StripeService();
        // $stripeService->verifyWebhookSignature($payload, $sigHeader, $webhookSecret);

        $event = json_decode($payload, true);
        if (!$event) {
            $this->json(['error' => 'Payload inválido.'], 400);
            return;
        }

        $type = $event['type'] ?? '';
        $object = $event['data']['object'] ?? [];

        switch ($type) {
            case 'payment_intent.succeeded':
                $this->handleStripePaymentSucceeded($object);
                break;

            case 'payment_intent.payment_failed':
                $this->handleStripePaymentFailed($object);
                break;
        }

        // Stripe espera 200 OK
        $this->json(['received' => true]);
    }

    private function handleStripePaymentSucceeded(array $intent): void
    {
        $paymentId = (int) ($intent['metadata']['payment_id'] ?? 0);
        if (!$paymentId) return;

        $paymentService = new PaymentService();
        try {
            $paymentService->confirmPayment($paymentId, $intent['id'], json_encode($intent));
        } catch (\Throwable $e) {
            // Log error
        }
    }

    private function handleStripePaymentFailed(array $intent): void
    {
        $paymentId = (int) ($intent['metadata']['payment_id'] ?? 0);
        if (!$paymentId) return;

        $paymentService = new PaymentService();
        $paymentService->failPayment($paymentId, json_encode($intent));
    }

    /**
     * Verifica status do pagamento PIX (polling do frontend).
     */
    public function pixStatus(Request $request, Response $response): void
    {
        $data = $request->json();
        $paymentId = (int) ($data['payment_id'] ?? 0);

        if (!$paymentId) {
            $this->json(['error' => 'Payment ID inválido.'], 400);
            return;
        }

        // Buscar o payment no banco
        $payment = $this->db->fetchOne("SELECT * FROM payments WHERE id = ?", [$paymentId]);
        if (!$payment) {
            $this->json(['error' => 'Pagamento não encontrado.'], 404);
            return;
        }

        // Se já está pago no nosso sistema
        if ($payment['status'] === 'completed') {
            $this->json(['paid' => true]);
            return;
        }

        // Consultar PagBank
        $chargeId = $payment['transaction_id'] ?? '';
        if (!$chargeId) {
            $this->json(['paid' => false, 'status' => 'waiting']);
            return;
        }

        try {
            $pagBankService = new PagBankService();
            if ($pagBankService->isChargePaid($chargeId)) {
                // Confirmar pagamento
                $paymentService = new PaymentService();
                $paymentService->confirmPayment($paymentId, $chargeId, json_encode(['status' => 'PAID']));
                $this->json(['paid' => true]);
            } else {
                $this->json(['paid' => false, 'status' => 'waiting']);
            }
        } catch (\Throwable $e) {
            $this->json(['paid' => false, 'status' => 'error']);
        }
    }

    /**
     * Webhook do PagBank (notificação de pagamento PIX).
     */
    public function handlePagBank(Request $request, Response $response): void
    {
        $payload = $request->rawBody();
        $event = json_decode($payload, true);

        if (!$event) {
            $this->json(['error' => 'Payload inválido.'], 400);
            return;
        }

        $chargeId = $event['id'] ?? '';
        $status = $event['status'] ?? '';

        if ($status === 'PAID' && $chargeId) {
            // Buscar payment pelo charge_id
            $payment = $this->db->fetchOne(
                "SELECT * FROM payments WHERE transaction_id = ? AND status = 'pending'",
                [$chargeId]
            );

            if ($payment) {
                $paymentService = new PaymentService();
                $paymentService->confirmPayment((int) $payment['id'], $chargeId, $payload);
            }
        }

        $this->json(['received' => true]);
    }
}
