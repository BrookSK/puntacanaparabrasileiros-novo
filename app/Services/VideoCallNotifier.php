<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;
use Core\Database;

/**
 * Notificações do módulo de agendamento de chamadas de vídeo.
 * Envia WhatsApp (via EvolutionApi) e email (via EmailService) para o cliente
 * e alertas WhatsApp/email para a empresa.
 *
 * Todos os métodos são resilientes: nunca lançam exceção fatal.
 */
class VideoCallNotifier
{
    private Database $db;
    private ?EvolutionApi $api = null;
    private bool $apiResolved = false;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    private function app(): App
    {
        return App::getInstance();
    }

    private function siteName(): string
    {
        return $this->app()->setting('site_name', 'Punta Cana para Brasileiros');
    }

    private function siteUrl(): string
    {
        return rtrim((string) $this->app()->setting('site_url', 'https://puntacananovo.lrvweb.com.br'), '/');
    }

    /**
     * Resolve (uma única vez) a instância WhatsApp conectada — mesmo padrão do checkout.
     */
    private function getApi(): ?EvolutionApi
    {
        if ($this->apiResolved) {
            return $this->api;
        }
        $this->apiResolved = true;

        $instance = $this->db->fetchOne("SELECT * FROM whatsapp_instances WHERE connection_status = 'open' LIMIT 1");
        if (!$instance) {
            $instance = $this->db->fetchOne("SELECT * FROM whatsapp_instances WHERE is_default = 1 LIMIT 1");
        }
        if (!$instance) {
            error_log('[VideoCallNotifier] Nenhuma instância WhatsApp disponível (nem conectada, nem default).');
            $this->api = null;
            return null;
        }

        $this->api = EvolutionApi::fromInstance($instance);
        error_log('[VideoCallNotifier] Instância WhatsApp resolvida: ' . ($instance['instance_name'] ?? '?'));
        return $this->api;
    }

    /**
     * Envia WhatsApp de forma segura. Loga cada etapa para diagnóstico.
     */
    private function sendWhatsApp(?string $phone, string $message): void
    {
        if (empty($phone)) {
            error_log('[VideoCallNotifier] Telefone vazio, envio ignorado.');
            return;
        }

        $api = $this->getApi();
        if (!$api) {
            return;
        }

        try {
            $normalizedPhone = EvolutionApi::normalizePhone($phone);
            $result = $api->sendText($normalizedPhone, $message);
            if ($result === null) {
                error_log('[VideoCallNotifier] sendText retornou null para ' . $normalizedPhone . ' (falha no envio).');
            } else {
                error_log('[VideoCallNotifier] Mensagem enviada para ' . $normalizedPhone . '.');
            }
            usleep(500000); // 0,5s entre mensagens (padrão do checkout)
        } catch (\Throwable $e) {
            error_log('[VideoCallNotifier] Erro ao enviar WhatsApp: ' . $e->getMessage());
        }
    }

    private function sendEmailTemplate(?string $to, string $toName, string $subject, string $template, array $data): void
    {
        if (empty($to)) return;
        try {
            (new EmailService())->sendTemplate($to, $toName, $subject, $template, $data);
        } catch (\Throwable $e) {
            error_log('[VideoCallNotifier] Erro ao enviar email: ' . $e->getMessage());
        }
    }

    /**
     * Números de WhatsApp da empresa (CSV nas settings).
     * @return string[]
     */
    private function companyPhones(): array
    {
        $raw = $this->app()->setting('admin_whatsapp_numbers', '18294582170');
        return array_values(array_filter(array_map('trim', explode(',', (string) $raw))));
    }

    private function companyEmail(): string
    {
        return (string) $this->app()->setting('admin_email', '');
    }

    private function formatDateTime(string $scheduledAt): string
    {
        $ts = strtotime($scheduledAt);
        if (!$ts) return $scheduledAt;
        return date('d/m/Y \à\s H:i', $ts);
    }

    // ─────────────────────────────────────────────
    // EVENTOS
    // ─────────────────────────────────────────────

    /**
     * Chamada agendada: notifica o cliente e a empresa.
     *
     * @param array $booking Dados do agendamento (customer_name, email, phone,
     *                        scheduled_at, meeting_link, trip_title?, notes?)
     */
    public function notifyScheduled(array $booking): void
    {
        $name = (string) ($booking['customer_name'] ?? '');
        $firstName = explode(' ', trim($name))[0] ?: $name;
        $when = $this->formatDateTime((string) ($booking['scheduled_at'] ?? ''));
        $link = (string) ($booking['meeting_link'] ?? '');
        $tripTitle = trim((string) ($booking['trip_title'] ?? ''));
        $notes = trim((string) ($booking['notes'] ?? ''));
        $site = $this->siteName();

        // ── Cliente (WhatsApp)
        $msg = "📹 *Chamada de vídeo agendada!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Sua chamada de vídeo com a equipe da {$site} está confirmada.\n\n";
        $msg .= "🗓️ *Data e hora:* {$when}\n";
        if ($tripTitle !== '') {
            $msg .= "🏝️ *Passeio:* {$tripTitle}\n";
        }
        $msg .= "\n🔗 *Link da reunião:*\n{$link}\n\n";
        $msg .= "É só clicar no link no horário marcado. Até lá! 🌴";
        $this->sendWhatsApp($booking['phone'] ?? null, $msg);

        // ── Empresa (WhatsApp)
        $adminMsg = "📹 *Nova chamada de vídeo agendada!*\n\n";
        $adminMsg .= "Um cliente solicitou uma chamada de vídeo pelo site.\n\n";
        $adminMsg .= "👤 *Cliente:* {$name}\n";
        $adminMsg .= "📞 *Telefone:* " . ($booking['phone'] ?? '-') . "\n";
        $adminMsg .= "✉️ *Email:* " . ($booking['email'] ?? '-') . "\n";
        if ($tripTitle !== '') {
            $adminMsg .= "🏝️ *Passeio:* {$tripTitle}\n";
        }
        $adminMsg .= "🗓️ *Data e hora:* {$when}\n";
        $adminMsg .= "🔗 *Link:* {$link}\n";
        if ($notes !== '') {
            $adminMsg .= "📝 *Observações:* {$notes}\n";
        }
        $adminMsg .= "\nGerencie em: " . $this->siteUrl() . "/admin/agendamentos";
        foreach ($this->companyPhones() as $adminPhone) {
            $this->sendWhatsApp($adminPhone, $adminMsg);
        }

        // ── Cliente (Email) — template no padrão Punta Cana
        $this->sendEmailTemplate(
            $booking['email'] ?? null,
            $name,
            'Sua chamada de vídeo foi agendada - ' . $site,
            'videocall-scheduled',
            [
                'firstName' => $firstName,
                'when' => $when,
                'tripTitle' => $tripTitle,
                'link' => $link,
                'siteUrl' => $this->siteUrl(),
                'isReminder' => false,
            ]
        );

        // ── Empresa (Email) — reaproveita o template de notificação do admin
        $this->sendEmailTemplate(
            $this->companyEmail() ?: null,
            'Administrador',
            'Nova chamada de vídeo agendada - ' . $name,
            'videocall-admin',
            [
                'name' => $name,
                'phone' => $booking['phone'] ?? '-',
                'email' => $booking['email'] ?? '-',
                'tripTitle' => $tripTitle,
                'when' => $when,
                'link' => $link,
                'notes' => $notes,
                'siteUrl' => $this->siteUrl(),
            ]
        );
    }

    /**
     * Lembrete antes da reunião (enviado ao cliente).
     */
    public function notifyReminder(array $booking): void
    {
        $name = (string) ($booking['customer_name'] ?? '');
        $firstName = explode(' ', trim($name))[0] ?: $name;
        $when = $this->formatDateTime((string) ($booking['scheduled_at'] ?? ''));
        $link = (string) ($booking['meeting_link'] ?? '');
        $tripTitle = trim((string) ($booking['trip_title'] ?? ''));
        $site = $this->siteName();

        $msg = "⏰ *Lembrete: sua chamada de vídeo é logo mais!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Passando para lembrar da sua chamada com a equipe da {$site}.\n\n";
        $msg .= "🗓️ *Quando:* {$when}\n";
        $msg .= "🔗 *Link:*\n{$link}\n\n";
        $msg .= "Nos vemos em breve! 🌴";
        $this->sendWhatsApp($booking['phone'] ?? null, $msg);

        $this->sendEmailTemplate(
            $booking['email'] ?? null,
            $name,
            'Lembrete: sua chamada de vídeo - ' . $site,
            'videocall-scheduled',
            [
                'firstName' => $firstName,
                'when' => $when,
                'tripTitle' => $tripTitle,
                'link' => $link,
                'siteUrl' => $this->siteUrl(),
                'isReminder' => true,
            ]
        );
    }

    /**
     * Status atualizado pelo admin (confirmed/cancelled/completed).
     */
    public function notifyStatusChange(array $booking, string $status): void
    {
        $name = (string) ($booking['customer_name'] ?? '');
        $firstName = explode(' ', trim($name))[0] ?: $name;
        $when = $this->formatDateTime((string) ($booking['scheduled_at'] ?? ''));
        $link = (string) ($booking['meeting_link'] ?? '');
        $site = $this->siteName();

        if ($status === 'confirmed') {
            $msg = "✅ *Chamada confirmada!*\n\nOlá, {$firstName}!\n\n";
            $msg .= "Sua chamada de vídeo com a {$site} foi confirmada.\n\n";
            $msg .= "🗓️ {$when}\n🔗 {$link}\n\nAté lá! 🌴";
        } elseif ($status === 'cancelled') {
            $msg = "⚠️ *Chamada cancelada*\n\nOlá, {$firstName}!\n\n";
            $msg .= "Sua chamada de vídeo marcada para {$when} foi *cancelada*.\n\n";
            if (!empty($booking['admin_notes'])) {
                $msg .= "*Motivo:* " . $booking['admin_notes'] . "\n\n";
            }
            $msg .= "Se quiser, é só agendar um novo horário no site. 🌴";
        } else {
            return; // completed/pending não notificam o cliente
        }
        $this->sendWhatsApp($booking['phone'] ?? null, $msg);
    }
}
