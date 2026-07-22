<?php
declare(strict_types=1);

namespace Core;

/**
 * Sistema de rotas da aplicação.
 * Suporta GET, POST, PUT, DELETE com parâmetros dinâmicos e middleware.
 */
class Router
{
    private array $routes = [];
    private array $namedRoutes = [];
    private Request $request;
    private Response $response;
    private string $prefix = '';
    private array $groupMiddleware = [];

    public function __construct(Request $request, Response $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function get(string $path, array|string $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('GET', $path, $handler, $middleware, $name);
    }

    public function post(string $path, array|string $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('POST', $path, $handler, $middleware, $name);
    }

    public function put(string $path, array|string $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('PUT', $path, $handler, $middleware, $name);
    }

    public function delete(string $path, array|string $handler, array $middleware = [], ?string $name = null): self
    {
        return $this->addRoute('DELETE', $path, $handler, $middleware, $name);
    }

    /**
     * Agrupa rotas com prefixo e middleware comuns.
     */
    public function group(array $options, callable $callback): void
    {
        $previousPrefix = $this->prefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->prefix .= $options['prefix'] ?? '';
        $this->groupMiddleware = array_merge(
            $this->groupMiddleware,
            $options['middleware'] ?? []
        );

        $callback($this);

        $this->prefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function addRoute(string $method, string $path, array|string $handler, array $middleware, ?string $name): self
    {
        $fullPath = $this->prefix . $path;
        $allMiddleware = array_merge($this->groupMiddleware, $middleware);

        $this->routes[] = [
            'method' => $method,
            'path' => $fullPath,
            'handler' => $handler,
            'middleware' => $allMiddleware,
            'name' => $name,
        ];

        if ($name) {
            $this->namedRoutes[$name] = $fullPath;
        }

        return $this;
    }

    /**
     * Despacha a requisição para o controller/action correto.
     */
    public function dispatch(): void
    {
        $method = $this->request->method();
        $uri = $this->request->uri();

        // Suporte a method override via _method (PUT, DELETE em forms)
        if ($method === 'POST' && $this->request->input('_method')) {
            $method = strtoupper($this->request->input('_method'));
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchRoute($route['path'], $uri);
            if ($params !== false) {
                // Executar middleware
                foreach ($route['middleware'] as $middlewareClass) {
                    $middlewareInstance = new $middlewareClass();
                    $result = $middlewareInstance->handle($this->request, $this->response);
                    if ($result === false) {
                        return;
                    }
                }

                // Executar handler
                $this->request->setParams($params);
                $this->executeHandler($route['handler']);
                return;
            }
        }

        // 404 - Rota não encontrada
        $this->response->setStatusCode(404);
        $this->response->setBody(
            View::renderStatic('errors/404', [])
            ?? '<h1>404</h1><p>Página não encontrada.</p>'
        );
        $this->response->send();
    }

    /**
     * Verifica se a URI corresponde ao padrão da rota.
     * Retorna array de parâmetros ou false.
     */
    private function matchRoute(string $routePath, string $uri): array|false
    {
        // Converter parâmetros dinâmicos: {id} → (?P<id>[^/]+)
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePath);
        // Parâmetros opcionais: {id?} → (?:/(?P<id>[^/]+))?
        $pattern = preg_replace('/\{([a-zA-Z_]+)\?\}/', '(?:/(?P<$1>[^/]+))?', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $uri, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return $params;
        }

        return false;
    }

    /**
     * Executa o handler (controller@method ou callable).
     */
    private function executeHandler(array|string $handler): void
    {
        if (is_string($handler)) {
            // Formato: "Controller@method"
            [$controllerClass, $method] = explode('@', $handler);
        } else {
            // Formato: [ControllerClass::class, 'method']
            [$controllerClass, $method] = $handler;
        }

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller não encontrado: {$controllerClass}", 500);
        }

        $controller = new $controllerClass();
        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Método não encontrado: {$controllerClass}@{$method}", 500);
        }

        $controller->$method($this->request, $this->response);
    }

    /**
     * Gera URL para uma rota nomeada.
     */
    public function url(string $name, array $params = []): string
    {
        if (!isset($this->namedRoutes[$name])) {
            return '#';
        }

        $path = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $path = str_replace('{' . $key . '}', (string) $value, $path);
        }
        // Remover parâmetros opcionais não preenchidos
        $path = preg_replace('/\{[a-zA-Z_]+\?\}/', '', $path);

        return $path;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
