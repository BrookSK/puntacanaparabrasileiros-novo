<?php
declare(strict_types=1);

namespace App\Controllers\Api;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Services\PaymentService;
use App\Services\PayPalService;
use App\Services\StripeService;

class WebhookController extends Controller
{
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
}
