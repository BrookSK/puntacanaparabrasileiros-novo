<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Avaliações reais do Google para a página de passeios (item 5 da validação).
 *
 * Fonte dos dados (nesta ordem de prioridade):
 *  1. Google Places API (Place Details) — quando o passeio tem google_place_id
 *     cadastrado E a chave global google_places_api_key está configurada em
 *     Configurações → Integrações. Retorna nota média, total de avaliações,
 *     link oficial e até 5 avaliações individuais (limite da API).
 *  2. Valores manuais do passeio (google_rating / google_reviews_count /
 *     google_reviews_url) — fallback quando não há integração. NÃO inventa
 *     avaliações: só mostra o resumo (estrelas + total + link para o Google).
 *
 * Resiliente: qualquer falha de rede/API retorna o fallback manual (ou null),
 * nunca lança exceção — a página do passeio nunca quebra por causa disto.
 *
 * Cache: a resposta da Places API é cacheada em arquivo por 24h para não
 * estourar a cota e deixar a página rápida.
 *
 * Padrão de cliente HTTP idêntico ao restante dos Services (cURL nativo).
 */
class GoogleReviewsService
{
    private const DETAILS_URL = 'https://maps.googleapis.com/maps/api/place/details/json';
    private const CACHE_TTL = 86400; // 24h

    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = trim((string) App::getInstance()->setting('google_places_api_key', ''));
    }

    /**
     * Retorna os dados de avaliação para um passeio, ou null se não houver nada
     * a exibir. Estrutura:
     *  [
     *    'rating' => float,            // nota média (0..5)
     *    'count' => int,               // total de avaliações
     *    'url' => string|null,         // link para ver todas no Google
     *    'reviews' => array,           // avaliações individuais (pode ser vazio)
     *    'source' => 'google'|'manual' // origem dos dados
     *  ]
     */
    public function getForTrip(array $trip): ?array
    {
        $placeId = trim((string) ($trip['google_place_id'] ?? ''));

        // 1) Tenta a Places API quando há place_id + chave configurada.
        if ($placeId !== '' && $this->apiKey !== '') {
            $fromApi = $this->fetchFromPlacesApi($placeId);
            if ($fromApi !== null) {
                return $fromApi;
            }
        }

        // 2) Fallback: valores manuais cadastrados no passeio.
        return $this->buildManualFallback($trip);
    }

    /**
     * Busca os detalhes do lugar na Places API (com cache de 24h).
     */
    private function fetchFromPlacesApi(string $placeId): ?array
    {
        $cached = $this->readCache($placeId);
        if ($cached !== null) {
            return $cached;
        }

        $query = http_build_query([
            'place_id' => $placeId,
            'fields' => 'rating,user_ratings_total,url,reviews',
            'reviews_sort' => 'newest',
            'language' => 'pt-BR',
            'key' => $this->apiKey,
        ]);

        $raw = $this->httpGet(self::DETAILS_URL . '?' . $query);
        if ($raw === null) {
            return null;
        }

        $json = json_decode($raw, true);
        if (!is_array($json) || ($json['status'] ?? '') !== 'OK' || empty($json['result'])) {
            return null;
        }

        $result = $json['result'];
        $reviews = [];
        foreach (($result['reviews'] ?? []) as $rv) {
            $reviews[] = [
                'author' => (string) ($rv['author_name'] ?? 'Cliente do Google'),
                'author_url' => (string) ($rv['author_url'] ?? ''),
                'profile_photo' => (string) ($rv['profile_photo_url'] ?? ''),
                'rating' => (int) ($rv['rating'] ?? 5),
                'text' => trim((string) ($rv['text'] ?? '')),
                'relative_time' => (string) ($rv['relative_time_description'] ?? ''),
            ];
        }

        $data = [
            'rating' => (float) ($result['rating'] ?? 0),
            'count' => (int) ($result['user_ratings_total'] ?? 0),
            'url' => !empty($result['url']) ? (string) $result['url'] : null,
            'reviews' => $reviews,
            'source' => 'google',
        ];

        // Só vale a pena exibir se há nota.
        if ($data['rating'] <= 0 && $data['count'] <= 0 && empty($data['reviews'])) {
            return null;
        }

        $this->writeCache($placeId, $data);
        return $data;
    }

    /**
     * Monta o resumo a partir dos valores manuais do passeio.
     */
    private function buildManualFallback(array $trip): ?array
    {
        $rating = (float) ($trip['google_rating'] ?? 0);
        $count = (int) ($trip['google_reviews_count'] ?? 0);
        $url = trim((string) ($trip['google_reviews_url'] ?? ''));

        if ($rating <= 0 && $count <= 0 && $url === '') {
            return null;
        }

        return [
            'rating' => $rating,
            'count' => $count,
            'url' => $url !== '' ? $url : null,
            'reviews' => [], // sem avaliações individuais no modo manual
            'source' => 'manual',
        ];
    }

    // ─────────────────────────── Cache em arquivo ───────────────────────────

    private function cacheFile(string $placeId): string
    {
        $dir = BASE_PATH . '/storage/cache/google-reviews';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir . '/' . md5($placeId) . '.json';
    }

    private function readCache(string $placeId): ?array
    {
        $file = $this->cacheFile($placeId);
        if (!is_file($file)) {
            return null;
        }
        if (time() - filemtime($file) > self::CACHE_TTL) {
            return null; // expirado
        }
        $json = json_decode((string) @file_get_contents($file), true);
        return is_array($json) ? $json : null;
    }

    private function writeCache(string $placeId, array $data): void
    {
        @file_put_contents($this->cacheFile($placeId), json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    // ─────────────────────────── Cliente HTTP ───────────────────────────────

    private function httpGet(string $url): ?string
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response = curl_exec($ch);
        $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $err = curl_error($ch);
        curl_close($ch);

        if ($response === false || $code < 200 || $code >= 300) {
            error_log('GoogleReviewsService: HTTP ' . $code . ' ' . $err);
            return null;
        }
        return (string) $response;
    }
}
