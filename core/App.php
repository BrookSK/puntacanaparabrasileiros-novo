<?php
declare(strict_types=1);

namespace Core;

/**
 * Classe principal da aplicação.
 * Inicializa todos os componentes e processa a requisição.
 */
class App
{
    private static ?App $instance = null;
    private Router $router;
    private Request $request;
    private Response $response;
    private Session $session;
    private Database $db;
    private array $settings = [];

    private function __construct()
    {
        $this->request = new Request();
        $this->response = new Response();
        $this->session = new Session();
        $this->db = Database::getInstance();
        $this->router = new Router($this->request, $this->response);
        $this->loadSettings();
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function run(): void
    {
        try {
            $this->session->start();
            $this->router->dispatch();
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    public function getRouter(): Router
    {
        return $this->router;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getDb(): Database
    {
        return $this->db;
    }

    /**
     * Retorna uma configuração do sistema (da tabela settings).
     */
    public function setting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Retorna todas as settings carregadas.
     */
    public function allSettings(): array
    {
        return $this->settings;
    }

    /**
     * Recarrega settings do banco (útil após update no admin).
     */
    public function reloadSettings(): void
    {
        $this->loadSettings();
    }

    private function loadSettings(): void
    {
        try {
            $stmt = $this->db->query(
                "SELECT setting_key, setting_value FROM settings WHERE autoload = 1"
            );
            $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $this->settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (\Throwable $e) {
            // Se o banco não estiver configurado ainda, ignora
            $this->settings = [];
        }
    }

    private function handleException(\Throwable $e): void
    {
        $code = $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 500;

        if (defined('APP_DEBUG') && APP_DEBUG) {
            $this->response->setStatusCode($code);
            $this->response->setBody(
                '<h1>Erro ' . $code . '</h1>' .
                '<p>' . htmlspecialchars($e->getMessage()) . '</p>' .
                '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>'
            );
        } else {
            $this->response->setStatusCode($code);
            $this->response->setBody(
                View::renderStatic('errors/' . $code, ['message' => $e->getMessage()])
                ?? '<h1>Erro ' . $code . '</h1><p>Ocorreu um erro inesperado.</p>'
            );
        }
        $this->response->send();
    }
}
