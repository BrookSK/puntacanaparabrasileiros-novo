<?php
declare(strict_types=1);

namespace Core;

/**
 * Gerenciador de sessão PHP nativa.
 * Suporta flash messages, CSRF tokens e dados persistentes.
 */
class Session
{
    private bool $started = false;

    public function start(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            $this->started = true;
            return;
        }

        $savePath = BASE_PATH . '/storage/sessions';
        if (is_dir($savePath) && is_writable($savePath)) {
            session_save_path($savePath);
        }

        session_set_cookie_params([
            'lifetime' => 86400 * 7, // 7 dias
            'path' => '/',
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
        $this->started = true;

        // Gerar CSRF token se não existir
        if (!isset($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }

        // Processar flash messages (remover as já exibidas)
        $this->ageFlashData();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function destroy(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
        $this->started = false;
    }

    /**
     * Define uma mensagem flash (disponível apenas na próxima requisição).
     */
    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash_new'][$key] = $value;
    }

    /**
     * Obtém uma mensagem flash.
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        return $_SESSION['_flash_old'][$key] ?? $default;
    }

    /**
     * Verifica se há uma mensagem flash.
     */
    public function hasFlash(string $key): bool
    {
        return isset($_SESSION['_flash_old'][$key]);
    }

    /**
     * Retorna todas as mensagens flash.
     */
    public function allFlash(): array
    {
        return $_SESSION['_flash_old'] ?? [];
    }

    /**
     * Envelhece dados flash (move new → old, limpa old anterior).
     */
    private function ageFlashData(): void
    {
        $_SESSION['_flash_old'] = $_SESSION['_flash_new'] ?? [];
        $_SESSION['_flash_new'] = [];
    }

    /**
     * Retorna o CSRF token atual.
     */
    public function csrfToken(): string
    {
        return $_SESSION['_csrf_token'] ?? '';
    }

    /**
     * Valida um CSRF token.
     */
    public function validateCsrfToken(string $token): bool
    {
        return hash_equals($this->csrfToken(), $token);
    }

    /**
     * Regenera o session ID (após login).
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    /**
     * Retorna o session ID.
     */
    public function getId(): string
    {
        return session_id();
    }
}
