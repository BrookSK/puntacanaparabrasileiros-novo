<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Serviço para buscar posts do Instagram via Graph API.
 * Usa cache em arquivo para evitar chamadas excessivas à API.
 */
class InstagramService
{
    private string $accessToken;
    private string $username;
    private int $postCount;
    private bool $enabled;
    private string $cacheFile;
    private int $cacheTtl = 3600; // 1 hora

    public function __construct()
    {
        $app = App::getInstance();
        $this->enabled = $app->setting('instagram_enabled', '0') === '1';
        $this->accessToken = $app->setting('instagram_access_token', '');
        $this->username = $app->setting('instagram_username', 'puntacanaparabrasileiros');
        $this->postCount = (int) $app->setting('instagram_post_count', '5');
        $this->cacheFile = BASE_PATH . '/storage/cache/instagram_feed.json';
    }

    /**
     * Retorna os últimos posts do Instagram.
     */
    public function getLatestPosts(): array
    {
        if (!$this->enabled) {
            return [];
        }

        // Verificar cache
        $cached = $this->getFromCache();
        if ($cached !== null) {
            return $cached;
        }

        // Buscar da API
        $posts = $this->fetchFromApi();

        // Salvar no cache
        if (!empty($posts)) {
            $this->saveToCache($posts);
        }

        return $posts;
    }

    /**
     * Busca posts via Instagram Graph API.
     */
    private function fetchFromApi(): array
    {
        if (empty($this->accessToken)) {
            return [];
        }

        $url = 'https://graph.instagram.com/me/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink,timestamp,username&limit=' . $this->postCount . '&access_token=' . $this->accessToken;

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || !$response) {
            return [];
        }

        $data = json_decode($response, true);
        if (!isset($data['data'])) {
            return [];
        }

        $posts = [];
        foreach ($data['data'] as $item) {
            $posts[] = [
                'id' => $item['id'],
                'caption' => $item['caption'] ?? '',
                'media_type' => $item['media_type'], // IMAGE, VIDEO, CAROUSEL_ALBUM
                'media_url' => $item['media_url'] ?? '',
                'thumbnail_url' => $item['thumbnail_url'] ?? $item['media_url'] ?? '',
                'permalink' => $item['permalink'] ?? '',
                'timestamp' => $item['timestamp'] ?? '',
                'username' => $item['username'] ?? $this->username,
                'date' => isset($item['timestamp']) ? date('M d', strtotime($item['timestamp'])) : '',
            ];
        }

        return $posts;
    }

    /**
     * Retorna dados do cache se válido.
     */
    private function getFromCache(): ?array
    {
        if (!file_exists($this->cacheFile)) {
            return null;
        }

        $mtime = filemtime($this->cacheFile);
        if (time() - $mtime > $this->cacheTtl) {
            return null; // Cache expirado
        }

        $content = file_get_contents($this->cacheFile);
        $data = json_decode($content, true);
        return is_array($data) ? $data : null;
    }

    /**
     * Salva posts no cache.
     */
    private function saveToCache(array $posts): void
    {
        $dir = dirname($this->cacheFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($this->cacheFile, json_encode($posts));
    }

    /**
     * Limpa o cache (útil após atualizar o token).
     */
    public function clearCache(): void
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}
