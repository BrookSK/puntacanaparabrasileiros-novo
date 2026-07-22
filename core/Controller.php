<?php
declare(strict_types=1);

namespace Core;

/**
 * Controller base — todos os controllers herdam deste.
 */
abstract class Controller
{
    protected Database $db;
    protected Session $session;
    protected App $app;

    public function __construct()
    {
        $this->app = App::getInstance();
        $this->db = $this->app->getDb();
        $this->session = $this->app->getSession();
    }

    /**
     * Renderiza uma view com layout.
     */
    protected function view(string $view, array $data = [], ?string $layout = null): void
    {
        $response = $this->app->getResponse();
        $content = View::render($view, $data, $layout);
        $response->setBody($content);
        $response->send();
    }

    /**
     * Retorna resposta JSON.
     */
    protected function json(mixed $data, int $statusCode = 200): void
    {
        $response = $this->app->getResponse();
        $response->setStatusCode($statusCode);
        $response->setHeader('Content-Type', 'application/json; charset=utf-8');
        $response->setBody(json_encode($data, JSON_UNESCAPED_UNICODE));
        $response->send();
    }

    /**
     * Redireciona para uma URL.
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        $response = $this->app->getResponse();
        $response->redirect($url, $statusCode);
    }

    /**
     * Redireciona de volta (referer).
     */
    protected function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    /**
     * Retorna o usuário logado ou null.
     */
    protected function currentUser(): ?array
    {
        return $this->session->get('user');
    }

    /**
     * Verifica se o usuário está logado.
     */
    protected function isAuthenticated(): bool
    {
        return $this->session->has('user');
    }

    /**
     * Define uma mensagem flash na sessão.
     */
    protected function flash(string $type, mixed $message): void
    {
        $this->session->flash($type, $message);
    }

    /**
     * Retorna uma configuração do sistema.
     */
    protected function setting(string $key, mixed $default = null): mixed
    {
        return $this->app->setting($key, $default);
    }

    /**
     * Valida CSRF token.
     */
    protected function validateCsrf(Request $request): bool
    {
        $token = $request->input('_token') ?? $request->header('X-CSRF-TOKEN');
        return $this->session->validateCsrfToken($token ?? '');
    }

    /**
     * Aborta com código de erro HTTP.
     */
    protected function abort(int $code, string $message = ''): never
    {
        throw new \RuntimeException($message ?: "HTTP Error {$code}", $code);
    }
}
