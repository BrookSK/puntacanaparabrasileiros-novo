<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;
use Core\Database;
use Core\View;

/**
 * Serviço de envio de emails via SMTP.
 * Usa PHPMailer-like socket direto (sem dependência externa).
 */
class EmailService
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $encryption;
    private string $fromEmail;
    private string $fromName;
    private Database $db;

    public function __construct()
    {
        $app = App::getInstance();
        $this->host = $app->setting('smtp_host', '');
        $this->port = (int) $app->setting('smtp_port', '587');
        $this->username = $app->setting('smtp_username', '');
        $this->password = $app->setting('smtp_password', '');
        $this->encryption = $app->setting('smtp_encryption', 'tls');
        $this->fromEmail = $app->setting('mail_from_email', 'noreply@puntacanaparabrasileiros.com');
        $this->fromName = $app->setting('mail_from_name', 'Punta Cana para Brasileiros');
        $this->db = Database::getInstance();
    }

    /**
     * Envia um email usando template de view.
     */
    public function sendTemplate(string $to, string $toName, string $subject, string $template, array $data = [], array $attachments = []): bool
    {
        $body = View::render('emails/' . $template, $data);
        return $this->send($to, $toName, $subject, $body, $attachments);
    }

    /**
     * Envia um email com corpo HTML direto.
     */
    public function send(string $to, string $toName, string $subject, string $body, array $attachments = []): bool
    {
        try {
            $result = $this->sendViaSMTP($to, $toName, $subject, $body, $attachments);

            // Log de sucesso
            $this->logEmail($to, $toName, $subject, 'sent');

            return $result;
        } catch (\Throwable $e) {
            // Log de falha
            $this->logEmail($to, $toName, $subject, 'failed', $e->getMessage());
            return false;
        }
    }

    /**
     * Envia email via SMTP usando fsockopen.
     */
    private function sendViaSMTP(string $to, string $toName, string $subject, string $body, array $attachments = []): bool
    {
        // Construir mensagem MIME
        $boundary = md5(uniqid((string) time()));
        $headers = $this->buildHeaders($to, $toName, $subject, $boundary, !empty($attachments));
        $message = $this->buildMessage($body, $attachments, $boundary);

        // Conectar via SMTP
        $socket = $this->connectSMTP();
        if (!$socket) {
            throw new \RuntimeException('Não foi possível conectar ao servidor SMTP.');
        }

        try {
            $this->smtpCommand($socket, '', 220);

            // EHLO
            $this->smtpCommand($socket, "EHLO " . gethostname(), 250);

            // STARTTLS se necessário
            if ($this->encryption === 'tls') {
                $this->smtpCommand($socket, "STARTTLS", 220);
                stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT);
                $this->smtpCommand($socket, "EHLO " . gethostname(), 250);
            }

            // AUTH LOGIN
            if ($this->username) {
                $this->smtpCommand($socket, "AUTH LOGIN", 334);
                $this->smtpCommand($socket, base64_encode($this->username), 334);
                $this->smtpCommand($socket, base64_encode($this->password), 235);
            }

            // MAIL FROM
            $this->smtpCommand($socket, "MAIL FROM:<{$this->fromEmail}>", 250);

            // RCPT TO
            $this->smtpCommand($socket, "RCPT TO:<{$to}>", 250);

            // DATA
            $this->smtpCommand($socket, "DATA", 354);

            // Enviar headers + body
            $fullMessage = $headers . "\r\n\r\n" . $message . "\r\n.";
            $this->smtpCommand($socket, $fullMessage, 250);

            // QUIT
            $this->smtpCommand($socket, "QUIT", 221);

            fclose($socket);
            return true;
        } catch (\Throwable $e) {
            fclose($socket);
            throw $e;
        }
    }

    private function connectSMTP()
    {
        $host = $this->encryption === 'ssl' ? 'ssl://' . $this->host : $this->host;
        $socket = @fsockopen($host, $this->port, $errno, $errstr, 30);
        return $socket ?: null;
    }

    private function smtpCommand($socket, string $command, int $expectedCode): string
    {
        if ($command !== '') {
            fwrite($socket, $command . "\r\n");
        }

        $response = '';
        while ($line = fgets($socket, 515)) {
            $response .= $line;
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }

        $code = (int) substr($response, 0, 3);
        if ($code !== $expectedCode) {
            throw new \RuntimeException("SMTP Error: esperado {$expectedCode}, recebido {$code}. Resposta: {$response}");
        }

        return $response;
    }

    private function buildHeaders(string $to, string $toName, string $subject, string $boundary, bool $hasAttachments): string
    {
        $headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n";
        $headers .= "To: {$toName} <{$to}>\r\n";
        $headers .= "Subject: {$subject}\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Date: " . date('r') . "\r\n";

        if ($hasAttachments) {
            $headers .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n";
        } else {
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $headers .= "Content-Transfer-Encoding: base64\r\n";
        }

        return $headers;
    }

    private function buildMessage(string $body, array $attachments, string $boundary): string
    {
        if (empty($attachments)) {
            return chunk_split(base64_encode($body));
        }

        $message = "--{$boundary}\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($body)) . "\r\n";

        foreach ($attachments as $attachment) {
            $filename = basename($attachment);
            $content = file_get_contents($attachment);
            $message .= "--{$boundary}\r\n";
            $message .= "Content-Type: application/octet-stream; name=\"{$filename}\"\r\n";
            $message .= "Content-Disposition: attachment; filename=\"{$filename}\"\r\n";
            $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $message .= chunk_split(base64_encode($content)) . "\r\n";
        }

        $message .= "--{$boundary}--\r\n";
        return $message;
    }

    private function logEmail(string $to, string $toName, string $subject, string $status, ?string $error = null): void
    {
        try {
            $this->db->insert('email_log', [
                'to_email' => $to,
                'to_name' => $toName,
                'subject' => $subject,
                'status' => $status,
                'error_message' => $error,
                'sent_at' => $status === 'sent' ? date('Y-m-d H:i:s') : null,
            ]);
        } catch (\Throwable $e) {
            // Falha silenciosa no log
        }
    }

    /**
     * Envia email de teste (usado na tela de settings).
     */
    public function sendTestEmail(string $to): bool
    {
        return $this->send(
            $to,
            'Administrador',
            'Email de Teste - Punta Cana para Brasileiros',
            '<h1>Email de teste</h1><p>Este é um email de teste do sistema. Se você está recebendo isso, o SMTP está configurado corretamente!</p>'
        );
    }
}
