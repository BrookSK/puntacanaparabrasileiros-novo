<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Serviço de notificações WhatsApp para o sistema.
 * 
 * Usa o WhatsappNotifier (Evolution API) como motor de envio principal.
 * Mantém compatibilidade com webhook legado como fallback.
 * 
 * Responsável por montar mensagens a partir de templates e dados de reservas,
 * delegando o envio efetivo para o WhatsappNotifier.
 */
class WhatsAppService
{
    private ?WhatsappNotifier $notifier = null;
    private string $legacyWebhookUrl;
    private bool $enabled;
    private App $app;

    public function __construct()
    {
        $this->app = App::getInstance();
        $this->enabled = $this->app->setting('whatsapp_enabled', '0') === '1';
        $this->legacyWebhookUrl = $this->app->setting('whatsapp_webhook_url', '');

        // Inicializa o notifier (pode falhar silenciosamente se tabelas não existem ainda)
        try {
            $this->notifier = new WhatsappNotifier();
        } catch (\Throwable $e) {
            // Notifier indisponível — fallback para webhook legado
            $this->notifier = null;
        }
    }

    /**
     * Envia mensagem de confirmação de passeio.
     */
    public function sendTripConfirmation(array $booking, array $tripData): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $phone = $this->formatPhone($booking['billing_phone'] ?? '');
        if (!$phone) return false;

        $customerName = trim(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? ''));

        $template = $this->app->setting('whatsapp_trip_template', '');
        $message = $this->replaceVariables($template, [
            'customer_name' => $customerName,
            'trip_name' => $tripData['title'] ?? '',
            'trip_date' => $tripData['date'] ?? '',
            'trip_time' => $tripData['time'] ?? '',
            'pax_info' => $tripData['pax_info'] ?? '',
            'total' => '$' . number_format((float) ($booking['total'] ?? 0), 2),
            'reference' => $tripData['reference'] ?? $booking['booking_number'] ?? '',
        ]);

        if (empty($message)) {
            // Mensagem padrão se template não configurado
            $message = $this->buildDefaultTripMessage($customerName, $tripData, $booking);
        }

        return $this->send($phone, $message, $customerName);
    }

    /**
     * Envia mensagem de confirmação de transfer.
     */
    public function sendTransferConfirmation(array $transferData): bool
    {
        if (!$this->enabled) {
            return false;
        }

        $phone = $this->formatPhone($transferData['customer_phone'] ?? '');
        if (!$phone) return false;

        $customerName = $transferData['customer_name'] ?? '';

        $template = $this->app->setting('whatsapp_transfer_template', '');
        $message = $this->replaceVariables($template, [
            'customer_name' => $customerName,
            'vehicle_name' => $transferData['vehicle_title'] ?? '',
            'origin' => $transferData['origin_title'] ?? '',
            'destination' => $transferData['destination_title'] ?? '',
            'date' => $transferData['date'] ?? '',
            'time' => $transferData['time'] ?? '',
            'pax_info' => ($transferData['adults'] ?? 0) . ' adulto(s)',
            'reference' => $transferData['reference'] ?? '',
        ]);

        if (empty($message)) {
            $message = $this->buildDefaultTransferMessage($customerName, $transferData);
        }

        return $this->send($phone, $message, $customerName);
    }

    /**
     * Envia notificação para o grupo padrão (ex: nova reserva).
     */
    public function notifyGroup(string $message): bool
    {
        if (!$this->enabled) {
            return false;
        }

        // Usar WhatsappNotifier se disponível
        if ($this->notifier && $this->notifier->isGroupNotifyEnabled()) {
            return $this->notifier->sendToDefaultGroup($message);
        }

        return false;
    }

    /**
     * Envia notificação de nova reserva para o grupo do admin.
     */
    public function notifyNewBooking(array $booking, array $items = [], array $transfers = []): bool
    {
        $message = "🎉 *Nova Reserva!*\n\n";
        $message .= "📋 *Pedido:* #{$booking['booking_number']}\n";
        $message .= "👤 *Cliente:* {$booking['billing_first_name']} {$booking['billing_last_name']}\n";
        $message .= "📱 *Telefone:* {$booking['billing_phone']}\n";
        $message .= "💰 *Total:* $" . number_format((float) ($booking['total'] ?? 0), 2) . "\n";

        if (!empty($items)) {
            $message .= "\n*Passeios:*\n";
            foreach ($items as $item) {
                $message .= "• {$item['trip_title']} — {$item['trip_date']}\n";
            }
        }

        if (!empty($transfers)) {
            $message .= "\n*Transfers:*\n";
            foreach ($transfers as $transfer) {
                $origin = $transfer['origin_title'] ?? 'Origem';
                $dest = $transfer['destination_title'] ?? 'Destino';
                $message .= "• {$origin} → {$dest} — {$transfer['date']}\n";
            }
        }

        return $this->notifyGroup($message);
    }

    /**
     * Envia mensagem genérica para um número.
     * Método unificado que tenta Evolution API primeiro, fallback para webhook legado.
     */
    public function sendMessage(string $phone, string $message, ?string $contactName = null): bool
    {
        return $this->send($phone, $message, $contactName);
    }

    // ─────────────────────────────────────────────
    // PRIVADO
    // ─────────────────────────────────────────────

    /**
     * Envia mensagem usando WhatsappNotifier (Evolution API) com fallback para webhook legado.
     */
    private function send(string $phone, string $message, ?string $contactName = null): bool
    {
        if (empty($phone) || empty($message)) {
            return false;
        }

        // Tentar via WhatsappNotifier (Evolution API) primeiro
        if ($this->notifier && $this->notifier->isAvailable()) {
            return $this->notifier->sendToPhone($phone, $message, $contactName);
        }

        // Fallback: webhook legado
        return $this->sendViaLegacyWebhook($phone, $message);
    }

    /**
     * Envio via webhook legado (método original do sistema).
     */
    private function sendViaLegacyWebhook(string $phone, string $message): bool
    {
        if (empty($this->legacyWebhookUrl)) {
            return false;
        }

        $payload = json_encode([
            'numero' => $phone,
            'message' => $message,
        ]);

        $ch = curl_init($this->legacyWebhookUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ],
            CURLOPT_TIMEOUT => 15,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return $httpCode >= 200 && $httpCode < 300;
    }

    /**
     * Monta mensagem padrão de confirmação de passeio (quando template não configurado).
     */
    private function buildDefaultTripMessage(string $customerName, array $tripData, array $booking): string
    {
        $message = "✅ *Reserva Confirmada!*\n\n";
        $message .= "Olá, *{$customerName}*! 🎉\n\n";
        $message .= "Sua reserva foi confirmada:\n\n";
        $message .= "🎯 *Passeio:* {$tripData['title']}\n";

        if (!empty($tripData['date'])) {
            $message .= "📅 *Data:* {$tripData['date']}\n";
        }
        if (!empty($tripData['time'])) {
            $message .= "⏰ *Horário:* {$tripData['time']}\n";
        }
        if (!empty($tripData['pax_info'])) {
            $message .= "👥 *Passageiros:* {$tripData['pax_info']}\n";
        }

        $total = '$' . number_format((float) ($booking['total'] ?? 0), 2);
        $message .= "💰 *Total:* {$total}\n";
        $message .= "📋 *Referência:* {$booking['booking_number']}\n\n";
        $message .= "Obrigado pela preferência! 🌴";

        return $message;
    }

    /**
     * Monta mensagem padrão de confirmação de transfer (quando template não configurado).
     */
    private function buildDefaultTransferMessage(string $customerName, array $transferData): string
    {
        $message = "✅ *Transfer Confirmado!*\n\n";
        $message .= "Olá, *{$customerName}*! 🚗\n\n";
        $message .= "Seu transfer foi confirmado:\n\n";
        $message .= "🚗 *Veículo:* {$transferData['vehicle_title']}\n";
        $message .= "📍 *Origem:* {$transferData['origin_title']}\n";
        $message .= "📍 *Destino:* {$transferData['destination_title']}\n";

        if (!empty($transferData['date'])) {
            $message .= "📅 *Data:* {$transferData['date']}\n";
        }
        if (!empty($transferData['time'])) {
            $message .= "⏰ *Horário:* {$transferData['time']}\n";
        }

        $adults = $transferData['adults'] ?? 0;
        $message .= "👥 *Passageiros:* {$adults} adulto(s)\n";

        if (!empty($transferData['reference'])) {
            $message .= "📋 *Referência:* {$transferData['reference']}\n";
        }

        $message .= "\nObrigado pela preferência! 🌴";

        return $message;
    }

    /**
     * Formata número de telefone para formato internacional.
     */
    private function formatPhone(string $phone): string
    {
        // Usar a normalização do EvolutionApi (padrão unificado)
        return EvolutionApi::normalizePhone($phone);
    }

    /**
     * Substitui variáveis no template.
     */
    private function replaceVariables(string $template, array $variables): string
    {
        if (empty($template)) {
            return '';
        }

        foreach ($variables as $key => $value) {
            $template = str_replace('{' . $key . '}', (string) $value, $template);
        }

        return $template;
    }
}
