<?php
declare(strict_types=1);

/**
 * Funções auxiliares globais do sistema.
 */

/**
 * Retorna uma configuração do sistema.
 */
function setting(string $key, mixed $default = null): mixed
{
    return \Core\App::getInstance()->setting($key, $default);
}

/**
 * Gera URL completa.
 */
function url(string $path = '/'): string
{
    $baseUrl = setting('site_url', '');
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Gera URL para asset (CSS, JS, imagem).
 */
function asset(string $path): string
{
    return '/assets/' . ltrim($path, '/');
}

/**
 * Retorna a URL absoluta atual (sem query string) — usada para canonical/OG.
 */
function current_url(bool $withQuery = false): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['SERVER_PORT'] ?? '') == 443 ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    if (!$withQuery) {
        $uri = strtok($uri, '?');
    }
    return $scheme . '://' . $host . $uri;
}

/**
 * Retorna a URL base do site (a partir da setting site_url, com fallback no host atual).
 */
function site_base_url(): string
{
    $url = (string) setting('site_url', '');
    if ($url !== '') return rtrim($url, '/');
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}

/**
 * Gera todas as meta tags de SEO do <head>: description, canonical, robots,
 * Open Graph e Twitter Cards. Consome as variáveis já passadas pelas views
 * (pageTitle, metaDescription) e aceita overrides opcionais.
 *
 * @param array $opts title, description, image, type (website|article|product), noindex (bool)
 */
function seo_meta_tags(array $opts = []): string
{
    $siteName = (string) setting('site_name', 'Punta Cana para Brasileiros');
    $title = trim((string) ($opts['title'] ?? $siteName));
    $description = trim((string) ($opts['description'] ?? setting('meta_description', '')));
    $type = $opts['type'] ?? 'website';
    $canonical = $opts['canonical'] ?? current_url();
    $noindex = !empty($opts['noindex']);

    // Imagem: usa a informada, senão a logo/imagem padrão do site
    $image = trim((string) ($opts['image'] ?? ''));
    if ($image !== '' && !preg_match('#^https?://#', $image)) {
        $image = site_base_url() . '/' . ltrim($image, '/');
    }
    if ($image === '') {
        $image = site_base_url() . '/assets/images/layout/PUNTA-CANA-1.png';
    }

    // Se o chamador (ex.: layout) já montou o título completo, usa verbatim
    // para não duplicar a marca no og:title/twitter:title.
    if (!empty($opts['fullTitle'])) {
        $fullTitle = trim((string) $opts['fullTitle']);
    } else {
        $fullTitle = $title !== $siteName ? ($title . ' | ' . $siteName) : $siteName;
    }

    $out = [];
    $out[] = '<meta name="description" content="' . e($description) . '">';
    $out[] = '<link rel="canonical" href="' . e($canonical) . '">';
    $out[] = $noindex
        ? '<meta name="robots" content="noindex, nofollow">'
        : '<meta name="robots" content="index, follow, max-image-preview:large">';

    // Open Graph
    $out[] = '<meta property="og:site_name" content="' . e($siteName) . '">';
    $out[] = '<meta property="og:type" content="' . e($type) . '">';
    $out[] = '<meta property="og:title" content="' . e($fullTitle) . '">';
    $out[] = '<meta property="og:description" content="' . e($description) . '">';
    $out[] = '<meta property="og:url" content="' . e($canonical) . '">';
    $out[] = '<meta property="og:image" content="' . e($image) . '">';
    $out[] = '<meta property="og:locale" content="pt_BR">';

    // Twitter Cards
    $out[] = '<meta name="twitter:card" content="summary_large_image">';
    $out[] = '<meta name="twitter:title" content="' . e($fullTitle) . '">';
    $out[] = '<meta name="twitter:description" content="' . e($description) . '">';
    $out[] = '<meta name="twitter:image" content="' . e($image) . '">';

    return implode("\n    ", $out);
}

/**
 * Renderiza um bloco JSON-LD (Schema.org) a partir de um array.
 */
function json_ld(array $data): string
{
    return '<script type="application/ld+json">'
        . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        . '</script>';
}

/**
 * JSON-LD da organização (dados da empresa) — usado no site inteiro.
 */
function json_ld_organization(): string
{
    $base = site_base_url();
    return json_ld([
        '@context' => 'https://schema.org',
        '@type' => 'TravelAgency',
        'name' => setting('site_name', 'Punta Cana para Brasileiros'),
        'url' => $base,
        'logo' => $base . '/assets/images/layout/PUNTA-CANA-1.png',
        'image' => $base . '/assets/images/layout/PUNTA-CANA-1.png',
        'telephone' => '+1 829 458 2170',
        'areaServed' => 'Punta Cana, República Dominicana',
        'sameAs' => [
            'https://www.instagram.com/puntacanaparabrasileiros',
        ],
    ]);
}

/**
 * Escapa HTML (previne XSS).
 */
function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Gera campo hidden com CSRF token.
 */
function csrf_field(): string
{
    $token = \Core\App::getInstance()->getSession()->csrfToken();
    return '<input type="hidden" name="_token" value="' . e($token) . '">';
}

/**
 * Retorna o CSRF token.
 */
function csrf_token(): string
{
    return \Core\App::getInstance()->getSession()->csrfToken();
}

/**
 * Gera campo hidden para method override (PUT, DELETE).
 */
function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . e(strtoupper($method)) . '">';
}

/**
 * Verifica se há flash message.
 */
function has_flash(string $key): bool
{
    return \Core\App::getInstance()->getSession()->hasFlash($key);
}

/**
 * Retorna flash message.
 */
function flash(string $key, mixed $default = null): mixed
{
    return \Core\App::getInstance()->getSession()->getFlash($key, $default);
}

/**
 * Retorna dados antigos (old input) do flash.
 */
function old(string $key, string $default = ''): string
{
    $old = flash('old', []);
    return (string) ($old[$key] ?? $default);
}

/**
 * Retorna erro de validação para um campo.
 */
function error(string $key): string
{
    $errors = flash('errors', []);
    return (string) ($errors[$key] ?? '');
}

/**
 * Verifica se há erro para um campo.
 */
function has_error(string $key): bool
{
    $errors = flash('errors', []);
    return isset($errors[$key]);
}

/**
 * Formata moeda (USD).
 */
function money(float $amount, string $symbol = '$'): string
{
    return $symbol . number_format($amount, 2, '.', ',');
}

/**
 * Formata data para exibição (dd/mm/yyyy).
 */
function format_date(?string $date, string $format = 'd/m/Y'): string
{
    if (!$date) return '';
    $dt = new \DateTime($date);
    return $dt->format($format);
}

/**
 * Formata data e hora.
 */
function format_datetime(?string $datetime, string $format = 'd/m/Y H:i'): string
{
    if (!$datetime) return '';
    $dt = new \DateTime($datetime);
    return $dt->format($format);
}

/**
 * Trunca texto com "...".
 */
function truncate(?string $text, int $length = 100): string
{
    if (!$text) return '';
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '...';
}

/**
 * Retorna classe CSS para status de booking.
 */
function booking_status_class(string $status): string
{
    return match ($status) {
        'booked', 'completed' => 'success',
        'pending' => 'warning',
        'partially_paid' => 'info',
        'cancelled', 'refunded' => 'danger',
        default => 'secondary',
    };
}

/**
 * Retorna label em PT para status de booking.
 */
function booking_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Pendente',
        'booked' => 'Confirmado',
        'partially_paid' => 'Parcialmente Pago',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado',
        'refunded' => 'Reembolsado',
        default => ucfirst($status),
    };
}

/**
 * Retorna label para status de transfer.
 */
function transfer_status_label(string $status): string
{
    return match ($status) {
        'pending' => 'Pendente',
        'confirmed' => 'Confirmado',
        'completed' => 'Concluído',
        'cancelled' => 'Cancelado',
        default => ucfirst($status),
    };
}

/**
 * Verifica se o usuário atual é admin.
 */
function is_admin(): bool
{
    $user = \Core\App::getInstance()->getSession()->get('user');
    return in_array($user['role'] ?? '', ['superadmin', 'admin', 'editor']);
}

/**
 * Verifica se o usuário atual é superadmin.
 */
function is_superadmin(): bool
{
    $user = \Core\App::getInstance()->getSession()->get('user');
    return ($user['role'] ?? '') === 'superadmin';
}

/**
 * Gestor = pode gerenciar áreas sensíveis (superadmin/admin/editor).
 * Alias semântico de is_admin() para uso no controle de acesso da equipe.
 */
function is_manager(): bool
{
    return is_admin();
}

/**
 * Verifica se o usuário é da equipe de atendimento (sem poder de gestão).
 */
function is_team(): bool
{
    $user = \Core\App::getInstance()->getSession()->get('user');
    return in_array($user['role'] ?? '', ['attendant', 'whatsapp_agent', 'comercial'], true);
}

/**
 * Retorna o usuário logado.
 */
function current_user(): ?array
{
    return \Core\App::getInstance()->getSession()->get('user');
}

/**
 * Renderiza um componente parcial.
 */
function partial(string $view, array $data = []): string
{
    return \Core\View::partial($view, $data);
}

/**
 * Gera string de tempo relativo (ex: "há 2 horas").
 */
function time_ago(string $datetime): string
{
    $now = new \DateTime();
    $past = new \DateTime($datetime);
    $diff = $now->diff($past);

    if ($diff->y > 0) return 'há ' . $diff->y . ' ano' . ($diff->y > 1 ? 's' : '');
    if ($diff->m > 0) return 'há ' . $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '');
    if ($diff->d > 0) return 'há ' . $diff->d . ' dia' . ($diff->d > 1 ? 's' : '');
    if ($diff->h > 0) return 'há ' . $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
    if ($diff->i > 0) return 'há ' . $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
    return 'agora';
}

/**
 * Exibe telefone com bandeira do país baseado no DDI.
 * Retorna HTML com <img> da bandeira + número formatado.
 */
function phone_with_flag(?string $phone): string
{
    if (!$phone) return '-';

    // Mapa de DDI para código de país (para a bandeira)
    $ddiMap = [
        '+55' => 'br', '+1' => 'us', '+351' => 'pt', '+54' => 'ar',
        '+57' => 'co', '+56' => 'cl', '+52' => 'mx', '+51' => 'pe',
        '+598' => 'uy', '+595' => 'py', '+593' => 'ec', '+591' => 'bo',
        '+58' => 've', '+507' => 'pa', '+506' => 'cr', '+502' => 'gt',
        '+53' => 'cu', '+34' => 'es', '+33' => 'fr', '+49' => 'de',
        '+39' => 'it', '+44' => 'gb', '+81' => 'jp', '+61' => 'au',
        '+972' => 'il', '+244' => 'ao', '+258' => 'mz',
    ];

    $countryCode = '';
    $cleanPhone = trim($phone);

    // Tentar detectar DDI no início do número
    if (str_starts_with($cleanPhone, '+')) {
        // Testar DDIs de 4, 3 e 2 dígitos (+ incluído)
        foreach ([4, 3, 2] as $len) {
            $prefix = substr($cleanPhone, 0, $len + 1); // +XXX
            if (isset($ddiMap[$prefix])) {
                $countryCode = $ddiMap[$prefix];
                break;
            }
        }
    }

    if (!$countryCode) {
        // Padrão: Brasil
        $countryCode = 'br';
    }

    $flagUrl = 'https://flagcdn.com/20x15/' . $countryCode . '.png';
    $flagHtml = '<img src="' . $flagUrl . '" alt="' . $countryCode . '" style="width:20px;height:15px;border-radius:2px;box-shadow:0 0 1px rgba(0,0,0,0.2);vertical-align:middle;margin-right:6px;">';

    return $flagHtml . e($cleanPhone);
}
