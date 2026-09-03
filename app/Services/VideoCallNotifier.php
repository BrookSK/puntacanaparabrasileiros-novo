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
        return rtrim($this->app()->setting('site_url', ''), '/');
    }

    /**
     * Envia WhatsApp de forma segura (mesmo padrão do checkout/AffiliateNotifier).
     */
    private function sendWhatsApp(?string $phone, string $message): void
    {
        if (empty($phone)) return;
        try {
            $instance = $this->db->fetchOne("SELECT * FROM whatsapp_instances WHERE connection_status = 'open' LIMIT 1");
            if (!$instance) {
                $instance = $this->db->fetchOne("SELECT * FROM whatsapp_instances WHERE is_default = 1 LIMIT 1");
            }
            if (!$instance) {
                error_log('[VideoCallNotifier] Nenhuma instância WhatsApp disponível');
                return;
            }
            $api = EvolutionApi::fromInstance($instance);
            $normalizedPhone = EvolutionApi::normalizePhone($phone);
            $api->sendText($normalizedPhone, $message);
        } catch (\Throwable $e) {
            error_log('[VideoCallNotifier] Erro ao enviar WhatsApp: ' . $e->getMessage());
        }
    }

    private function sendEmail(?string $to, string $toName, string $subject, string $htmlBody): void
    {
        if (empty($to)) return;
        try {
            (new EmailService())->send($to, $toName, $subject, $htmlBody);
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
     *                        scheduled_at, meeting_link, trip_title?)
     */
    public function notifyScheduled(array $booking): void
    {
        $name = (string) ($booking['customer_name'] ?? '');
        $firstName = explode(' ', trim($name))[0] ?: $name;
        $when = $this->formatDateTime((string) ($booking['scheduled_at'] ?? ''));
        $link = (string) ($booking['meeting_link'] ?? '');
        $tripTitle = trim((string) ($booking['trip_title'] ?? ''));
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

        // ── Cliente (Email)
        $htmlClient = $this->buildClientEmail($firstName, $when, $tripTitle, $link);
        $this->sendEmail(
            $booking['email'] ?? null,
            $name,
            'Sua chamada de vídeo foi agendada - ' . $site,
            $htmlClient
        );

        // ── Empresa (WhatsApp)
        $adminMsg = "🔔 *Novo agendamento de chamada de vídeo*\n\n";
        $adminMsg .= "👤 *Cliente:* {$name}\n";
        $adminMsg .= "📞 *Telefone:* " . ($booking['phone'] ?? '-') . "\n";
        $adminMsg .= "✉️ *Email:* " . ($booking['email'] ?? '-') . "\n";
        if ($tripTitle !== '') {
            $adminMsg .= "🏝️ *Passeio:* {$tripTitle}\n";
        }
        $adminMsg .= "🗓️ *Data e hora:* {$when}\n";
        $adminMsg .= "🔗 *Link:* {$link}\n";
        if (!empty($booking['notes'])) {
            $adminMsg .= "📝 *Observações:* " . $booking['notes'] . "\n";
        }
        foreach ($this->companyPhones() as $adminPhone) {
            $this->sendWhatsApp($adminPhone, $adminMsg);
        }

        // ── Empresa (Email)
        $this->sendEmail(
            $this->companyEmail() ?: null,
            'Administrador',
            'Novo agendamento de chamada - ' . $name,
            nl2br(htmlspecialchars($adminMsg))
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
        $site = $this->siteName();

        $msg = "⏰ *Lembrete: sua chamada de vídeo é logo mais!*\n\n";
        $msg .= "Olá, {$firstName}!\n\n";
        $msg .= "Passando para lembrar da sua chamada com a equipe da {$site}.\n\n";
        $msg .= "🗓️ *Quando:* {$when}\n";
        $msg .= "🔗 *Link:*\n{$link}\n\n";
        $msg .= "Nos vemos em breve! 🌴";
        $this->sendWhatsApp($booking['phone'] ?? null, $msg);

        $html = '<div style="font-family:Arial,sans-serif;max-width:560px;margin:0 auto">'
            . '<h2 style="color:#0a8">Lembrete da sua chamada de vídeo</h2>'
            . '<p>Olá, ' . htmlspecialchars($firstName) . '!</p>'
            . '<p>Sua chamada com a equipe da ' . htmlspecialchars($site) . ' acontece em breve.</p>'
            . '<p><strong>Quando:</strong> ' . htmlspecialchars($when) . '</p>'
            . '<p><a href="' . htmlspecialchars($link) . '" style="background:#0a8;color:#fff;padding:12px 20px;border-radius:8px;text-decoration:none;display:inline-block">Entrar na reunião</a></p>'
            . '</div>';
        $this->sendEmail($booking['email'] ?? null, $name, 'Lembrete: sua chamada de vídeo - ' . $site, $html);
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

    private function buildClientEmail(string $firstName, string $when, string $tripTitle, string $link): string
    {
        $tripLine = $tripTitle !== ''
            ? '<p><strong>Passeio:</strong> ' . htmlspecialchars($tripTitle) . '</p>'
            : '';
        return '<div style="font-family:Arial,sans-serif;max-width:560px;margin:0 auto;color:#333">'
            . '<h2 style="color:#0a8">Chamada de vídeo agendada 📹</h2>'
            . '<p>Olá, ' . htmlspecialchars($firstName) . '!</p>'
            . '<p>Sua chamada de vídeo com a equipe da ' . htmlspecialchars($this->siteName()) . ' está confirmada.</p>'
            . '<p><strong>Data e hora:</strong> ' . htmlspecialchars($when) . '</p>'
            . $tripLine
            . '<p style="margin:24px 0"><a href="' . htmlspecialchars($link) . '" style="background:#0a8;color:#fff;padding:12px 22px;border-radius:8px;text-decoration:none;display:inline-block">Entrar na reunião</a></p>'
            . '<p style="color:#666;font-size:13px">Ou copie e cole o link no seu navegador:<br>' . htmlspecialchars($link) . '</p>'
            . '<p>Até lá! 🌴</p>'
            . '</div>';
    }
}
