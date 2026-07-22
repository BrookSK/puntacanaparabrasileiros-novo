<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Integração com PayPal REST API.
 * Usa PayPal JS SDK no frontend + server-side capture.
 */
class PayPalService
{
    private string $clientId;
    private string $secret;
    private string $baseUrl;
    private bool $sandbox;

    public function __construct()
    {
        $app = App::getInstance();
        $this->clientId = $app->setting('paypal_client_id', '');
        $this->secret = $app->setting('paypal_secret', '');
        $this->sandbox = $app->setting('paypal_mode', 'sandbox') === 'sandbox';
        $this->baseUrl = $this->sandbox
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * Cria uma order no PayPal.
     */
    public function createOrder(float $amount, string $currency = 'USD', string $description = ''): array
    {
        $accessToken = $this->getAccessToken();

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => $currency,
                    'value' => number_format($amount, 2, '.', ''),
                ],
                'description' => $description ?: 'Punta Cana para Brasileiros - Reserva',
            ]],
        ];

        $response = $this->request('POST', '/v2/checkout/orders', $payload, $accessToken);
        return $response;
    }

    /**
     * Captura uma order aprovada pelo cliente.
     */
    public function captureOrder(string $orderId): array
    {
        $accessToken = $this->getAccessToken();
        $response = $this->request('POST', "/v2/checkout/orders/{$orderId}/capture", [], $accessToken);
        return $response;
    }

    /**
     * Busca detalhes de uma order.
     */
    public function getOrder(string $orderId): array
    {
        $accessToken = $this->getAccessToken();
        return $this->request('GET', "/v2/checkout/orders/{$orderId}", [], $accessToken);
    }

    /**
     * Obtém access token via client credentials.
     */
    private function getAccessToken(): string
    {
        $ch = curl_init($this->baseUrl . '/v1/oauth2/token');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
            CURLOPT_USERPWD => $this->clientId . ':' . $this->secret,
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 30,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \RuntimeException('PayPal: falha ao obter access token. HTTP ' . $httpCode);
        }

        $data = json_decode($response, true);
        return $data['access_token'] ?? '';
    }

    /**
     * Faz request genérica à API do PayPal.
     */
    private function request(string $method, string $endpoint, array $data, string $accessToken): array
    {
        $url = $this->baseUrl . $endpoint;
        $ch = curl_init($url);

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
        ];

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ];

        if ($method === 'POST') {
            $options[CURLOPT_POST] = true;
            if (!empty($data)) {
                $options[CURLOPT_POSTFIELDS] = json_encode($data);
            }
        }

        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $decoded = json_decode($response, true) ?? [];

        if ($httpCode >= 400) {
            throw new \RuntimeException(
                'PayPal API Error: ' . ($decoded['message'] ?? 'Unknown error') . ' (HTTP ' . $httpCode . ')'
            );
        }

        return $decoded;
    }
}
