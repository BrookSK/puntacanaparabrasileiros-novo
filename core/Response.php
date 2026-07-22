<?php
declare(strict_types=1);

namespace Core;

/**
 * Abstração da resposta HTTP.
 * Gerencia status code, headers e body.
 */
class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $body = '';
    private bool $sent = false;

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * Envia a resposta ao cliente.
     */
    public function send(): void
    {
        if ($this->sent) {
            return;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->body;
        $this->sent = true;
    }

    /**
     * Redireciona para uma URL.
     */
    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->statusCode = $statusCode;
        header("Location: {$url}", true, $statusCode);
        exit;
    }

    /**
     * Envia resposta JSON.
     */
    public function json(mixed $data, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->setHeader('Content-Type', 'application/json; charset=utf-8');
        $this->body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->send();
    }

    /**
     * Envia arquivo para download.
     */
    public function download(string $filePath, ?string $filename = null): void
    {
        if (!file_exists($filePath)) {
            $this->setStatusCode(404);
            $this->setBody('Arquivo não encontrado.');
            $this->send();
            return;
        }

        $filename = $filename ?? basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache, must-revalidate');

        readfile($filePath);
        exit;
    }

    /**
     * Define cookie.
     */
    public function cookie(
        string $name,
        string $value,
        int $expiry = 0,
        string $path = '/',
        bool $httpOnly = true,
        bool $secure = false,
        string $sameSite = 'Lax'
    ): self {
        setcookie($name, $value, [
            'expires' => $expiry,
            'path' => $path,
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);
        return $this;
    }
}
