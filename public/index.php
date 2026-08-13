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

// Rastreamento global de links de afiliado
// Registra a visita inicial (com ?ref=) E também páginas subsequentes enquanto o cookie existir
$refParam = $_GET['ref'] ?? null;
$affiliateCookie = $_COOKIE['pcb_ref'] ?? null;

if ($refParam && ctype_digit($refParam)) {
    // Primeira visita com ?ref= — registra e seta cookie
    $affiliateService = new \App\Services\AffiliateService();
    $affiliateService->trackVisit(
        (int) $refParam,
        $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
        $_SERVER['HTTP_REFERER'] ?? null,
        $_SERVER['REQUEST_URI'] ?? '/',
        $_SERVER['HTTP_USER_AGENT'] ?? null
    );
} elseif ($affiliateCookie && ctype_digit($affiliateCookie)) {
    // Navegação subsequente com cookie ativo — registra cada página visitada
    $pageUri = $_SERVER['REQUEST_URI'] ?? '/';
    // Não registrar assets, API calls, admin ou painel do afiliado
    if (!str_starts_with($pageUri, '/assets/') && !str_starts_with($pageUri, '/api/') && !str_starts_with($pageUri, '/admin') && !str_starts_with($pageUri, '/painel-afiliado')) {
        $db = \Core\Database::getInstance();
        // Evitar duplicatas: não registrar mesma página + mesmo IP no último minuto
        $recentVisit = $db->fetchOne(
            "SELECT id FROM affiliate_visits WHERE affiliate_id = ? AND page_url = ? AND ip_address = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE) LIMIT 1",
            [(int) $affiliateCookie, $pageUri, $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0']
        );
        if (!$recentVisit) {
            $db->insert('affiliate_visits', [
                'affiliate_id' => (int) $affiliateCookie,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
                'referrer' => $_SERVER['HTTP_REFERER'] ?? null,
                'page_url' => $pageUri,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ]);
        }
    }
}

// Executar aplicação
$app->run();
