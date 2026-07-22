<?php
declare(strict_types=1);

/**
 * PUNTA CANA PARA BRASILEIROS
 * Front Controller — Entry Point da Aplicação
 *
 * Todas as requisições passam por aqui via .htaccess rewrite.
 */

// Definir constantes de path
define('BASE_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);

// Carregar configuração da aplicação
$appConfig = require BASE_PATH . '/config/app.php';

// Debug mode
define('APP_DEBUG', $appConfig['debug'] ?? false);

// Configurações PHP
error_reporting(APP_DEBUG ? E_ALL : 0);
ini_set('display_errors', APP_DEBUG ? '1' : '0');
date_default_timezone_set($appConfig['timezone'] ?? 'America/Santo_Domingo');
mb_internal_encoding('UTF-8');

// Autoloader simples (sem Composer)
spl_autoload_register(function (string $class): void {
    // Mapear namespaces para diretórios
    $namespaceMap = [
        'Core\\' => BASE_PATH . '/core/',
        'App\\Controllers\\' => BASE_PATH . '/app/Controllers/',
        'App\\Models\\' => BASE_PATH . '/app/Models/',
        'App\\Services\\' => BASE_PATH . '/app/Services/',
        'App\\Middleware\\' => BASE_PATH . '/app/Middleware/',
        'App\\Helpers\\' => BASE_PATH . '/app/Helpers/',
    ];

    foreach ($namespaceMap as $prefix => $baseDir) {
        if (str_starts_with($class, $prefix)) {
            $relativeClass = substr($class, strlen($prefix));
            $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
                return;
            }
        }
    }
});

// Carregar helpers globais
require_once BASE_PATH . '/app/Helpers/functions.php';

// Configurar path das views
\Core\View::setViewsPath($appConfig['views_path']);

// Inicializar aplicação
$app = \Core\App::getInstance();
$router = $app->getRouter();

// Carregar rotas
require BASE_PATH . '/config/routes.php';

// Compartilhar dados globais com as views
\Core\View::share('app', $app);
\Core\View::share('session', $app->getSession());
\Core\View::share('currentUser', $app->getSession()->get('user'));
\Core\View::share('csrfToken', $app->getSession()->csrfToken());

// Executar aplicação
$app->run();
