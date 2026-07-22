<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Integração com Stripe API.
 * Usa PaymentIntent para processamento de pagamentos com cartão.
 */
class StripeService
{
    private string $publishableKey;
    private string $secretKey;
    private string $baseUrl = 'https://api.stripe.com/v1';
    private bool $testMode;

    public function __construct()
    {
        $app = App::getInstance();
        $this->publishableKey = $app->setting('stripe_publishable_key', '');
        $this->secretKey = $app->setting('stripe_secret_key', '');
        $this->testMode = $app->setting('stripe_mode', 'test') === 'test';
    }

    public function getPublishableKey(): string
    {
        return $this->publishableKey;
    }

    public function isTestMode(): bool
    {
        return $this->testMode;
    }

    /**
     * Cria um PaymentIntent.
     */
    public function createPaymentIntent(float $amount, string $currency = 'usd', array $metadata = []): array
    {
        $params = [
            'amount' => (int) round($amount * 100), // Stripe usa centavos
            'currency' => strtolower($currency),
            'automatic_payment_methods[enabled]' => 'true',
        ];

        if (!empty($metadata)) {
            foreach ($metadata as $key => $value) {
                $params["metadata[{$key}]"] = (string) $value;
            }
        }

        return $this->request('POST', '/payment_intents', $params);
    }

    /**
     * Busca um PaymentIntent pelo ID.
     */
    public function retrievePaymentIntent(string $paymentIntentId): array
    {
        return $this->request('GET', '/payment_intents/' . $paymentIntentId);
    }

    /**
     * Confirma um PaymentIntent.
     */
    public function confirmPaymentIntent(string $paymentIntentId, array $params = []): array
    {
        return $this->request('POST', '/payment_intents/' . $paymentIntentId . '/confirm', $params);
    }

    /**
     * Cria um reembolso.
     */
    public function createRefund(string $paymentIntentId, ?float $amount = null): array
    {
        $params = ['payment_intent' => $paymentIntentId];
        if ($amount !== null) {
            $params['amount'] = (int) round($amount * 100);
        }
        return $this->request('POST', '/refunds', $params);
    }

    /**
     * Valida assinatura de webhook do Stripe.
     */
    public function verifyWebhookSignature(string $payload, string $sigHeader, string $webhookSecret): bool
    {
        $elements = explode(',', $sigHeader);
        $timestamp = null;
        $signatures = [];

        foreach ($elements as $element) {
            [$key, $value] = explode('=', $element, 2);
            if ($key === 't') {
                $timestamp = $value;
            } elseif ($key === 'v1') {
                $signatures[] = $value;
            }
        }

        if (!$timestamp || empty($signatures)) {
            return false;
        }

        // Verificar tolerância de tempo (5 minutos)
        if (abs(time() - (int) $timestamp) > 300) {
            return false;
        }

        $signedPayload = $timestamp . '.' . $payload;
        $expectedSig = hash_hmac('sha256', $signedPayload, $webhookSecret);

        foreach ($signatures as $sig) {
            if (hash_equals($expectedSig, $sig)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Request genérica à API do Stripe.
     */
    private function request(string $method, string $endpoint, array $params = []): array
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init();

        $headers = [
            'Authorization: Bearer ' . $this->secretKey,
        ];

        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            $options[CURLOPT_POSTFIELDS] = http_build_query($params);
        }

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true) ?? [];

        if ($httpCode >= 400) {
            $errorMsg = $decoded['error']['message'] ?? 'Stripe API error';
            throw new \RuntimeException('Stripe: ' . $errorMsg . ' (HTTP ' . $httpCode . ')');
        }

        return $decoded;
    }
}
