<?php
declare(strict_types=1);

namespace Core;

/**
 * Abstração da requisição HTTP.
 * Encapsula $_GET, $_POST, $_FILES, headers e parâmetros de rota.
 */
class Request
{
    private array $params = [];
    private array $query;
    private array $post;
    private array $files;
    private array $server;
    private array $headers;

    public function __construct()
    {
        $this->query = $_GET;
        $this->post = $_POST;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->headers = $this->parseHeaders();
    }

    /**
     * Retorna o método HTTP (GET, POST, PUT, DELETE).
     */
    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    /**
     * Retorna a URI limpa (sem query string).
     */
    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rawurldecode($uri);

        // Remove trailing slash (exceto raiz)
        if ($uri !== '/') {
            $uri = rtrim($uri, '/');
        }

        return $uri;
    }

    /**
     * Retorna a URL completa.
     */
    public function fullUrl(): string
    {
        $scheme = $this->isSecure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        return $scheme . '://' . $host . ($this->server['REQUEST_URI'] ?? '/');
    }

    /**
     * Verifica se a conexão é HTTPS.
     */
    public function isSecure(): bool
    {
        return (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off')
            || ($this->server['SERVER_PORT'] ?? 80) == 443;
    }

    /**
     * Retorna um parâmetro de input (POST > GET > default).
     */
    public function input(string $key, mixed $default = null): mixed
    {
        return $this->post[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * Retorna todos os dados de input (POST + GET mesclados).
     */
    public function all(): array
    {
        return array_merge($this->query, $this->post);
    }

    /**
     * Retorna apenas os campos especificados.
     */
    public function only(array $keys): array
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Retorna dados de query string ($_GET).
     */
    public function query(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    /**
     * Retorna dados POST.
     */
    public function post(?string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->post;
        }
        return $this->post[$key] ?? $default;
    }

    /**
     * Retorna o body raw da requisição (para JSON).
     */
    public function rawBody(): string
    {
        return file_get_contents('php://input') ?: '';
    }

    /**
     * Retorna o body como JSON decodificado.
     */
    public function json(): array
    {
        $body = $this->rawBody();
        return json_decode($body, true) ?: [];
    }

    /**
     * Retorna informações de arquivo enviado.
     */
    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Verifica se há um arquivo enviado.
     */
    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    /**
     * Retorna um header HTTP.
     */
    public function header(string $key, mixed $default = null): mixed
    {
        $key = strtolower($key);
        return $this->headers[$key] ?? $default;
    }

    /**
     * Retorna o IP do cliente.
     */
    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR']
            ?? $this->server['HTTP_X_REAL_IP']
            ?? $this->server['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    /**
     * Retorna o User Agent.
     */
    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Verifica se a requisição é AJAX (XMLHttpRequest).
     */
    public function isAjax(): bool
    {
        return strtolower($this->header('x-requested-with', '')) === 'xmlhttprequest';
    }

    /**
     * Verifica se espera resposta JSON.
     */
    public function expectsJson(): bool
    {
        return $this->isAjax()
            || str_contains($this->header('accept', ''), 'application/json');
    }

    /**
     * Define parâmetros de rota (preenchido pelo Router).
     */
    public function setParams(array $params): void
    {
        $this->params = $params;
    }

    /**
     * Retorna um parâmetro de rota.
     */
    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    /**
     * Retorna todos os parâmetros de rota.
     */
    public function params(): array
    {
        return $this->params;
    }

    /**
     * Faz parse dos headers HTTP.
     */
    private function parseHeaders(): array
    {
        $headers = [];
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $headerKey = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$headerKey] = $value;
            }
        }
        return $headers;
    }
}
