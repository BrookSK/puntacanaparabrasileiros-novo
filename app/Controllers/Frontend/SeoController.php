<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use Core\Database;

/**
 * Gera sitemap.xml e robots.txt dinamicamente para SEO.
 */
class SeoController extends Controller
{
    /**
     * Sitemap XML com todas as URLs públicas indexáveis.
     */
    public function sitemap(Request $request, Response $response): void
    {
        $db = Database::getInstance();
        $base = site_base_url();
        $urls = [];

        // Páginas fixas principais
        $staticPages = [
            ['/', '1.0', 'daily'],
            ['/passeios', '0.9', 'daily'],
            ['/transfers', '0.8', 'weekly'],
            ['/blog', '0.7', 'daily'],
            ['/sobre-nos', '0.5', 'monthly'],
            ['/contato', '0.5', 'monthly'],
            ['/catalogo', '0.6', 'weekly'],
        ];
        foreach ($staticPages as [$path, $priority, $freq]) {
            $urls[] = ['loc' => $base . $path, 'priority' => $priority, 'changefreq' => $freq];
        }

        // Passeios publicados
        try {
            $trips = $db->fetchAll("SELECT slug, updated_at FROM trips WHERE status = 'published'");
            foreach ($trips as $t) {
                $urls[] = [
                    'loc' => $base . '/passeios/' . $t['slug'],
                    'lastmod' => !empty($t['updated_at']) ? date('Y-m-d', strtotime($t['updated_at'])) : null,
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                ];
            }
        } catch (\Throwable $e) {}

        // Categorias de passeios
        try {
            $cats = $db->fetchAll("SELECT slug FROM trip_categories");
            foreach ($cats as $c) {
                $urls[] = ['loc' => $base . '/passeios/categoria/' . $c['slug'], 'priority' => '0.6', 'changefreq' => 'weekly'];
            }
        } catch (\Throwable $e) {}

        // Posts do blog
        try {
            $posts = $db->fetchAll("SELECT slug, updated_at FROM blog_posts WHERE status = 'published'");
            foreach ($posts as $p) {
                $urls[] = [
                    'loc' => $base . '/blog/' . $p['slug'],
                    'lastmod' => !empty($p['updated_at']) ? date('Y-m-d', strtotime($p['updated_at'])) : null,
                    'priority' => '0.6',
                    'changefreq' => 'monthly',
                ];
            }
        } catch (\Throwable $e) {}

        // Páginas dinâmicas publicadas
        try {
            $pages = $db->fetchAll("SELECT slug FROM pages WHERE status = 'published'");
            foreach ($pages as $pg) {
                $urls[] = ['loc' => $base . '/pagina/' . $pg['slug'], 'priority' => '0.4', 'changefreq' => 'monthly'];
            }
        } catch (\Throwable $e) {}

        // Monta o XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc'], ENT_XML1) . "</loc>\n";
            if (!empty($u['lastmod'])) {
                $xml .= "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
            }
            $xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        $xml .= '</urlset>';

        header('Content-Type: application/xml; charset=UTF-8');
        echo $xml;
        exit;
    }

    /**
     * robots.txt apontando para o sitemap.
     */
    public function robots(Request $request, Response $response): void
    {
        $base = site_base_url();
        $out = "User-agent: *\n";
        $out .= "Allow: /\n";
        // Bloqueia áreas que não devem ser indexadas
        $out .= "Disallow: /admin\n";
        $out .= "Disallow: /painel-afiliado\n";
        $out .= "Disallow: /painel-agencia\n";
        $out .= "Disallow: /minha-conta\n";
        $out .= "Disallow: /checkout\n";
        $out .= "Disallow: /carrinho\n";
        $out .= "Disallow: /api/\n";
        $out .= "\n";
        $out .= "Sitemap: " . $base . "/sitemap.xml\n";

        header('Content-Type: text/plain; charset=UTF-8');
        echo $out;
        exit;
    }
}
