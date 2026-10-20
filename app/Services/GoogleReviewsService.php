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
 *  2. Fallback padronizado — quando não há integração via API. Exibe em TODOS
 *     os passeios o mesmo conjunto de avaliações reais (self::DEFAULT_REVIEWS,
 *     as que apareciam em "Saona Premium Brasil - Lancha"). Os campos do admin
 *     (google_rating / google_reviews_count / google_reviews_url) continuam
 *     funcionando como sobreposição do resumo (nota / total / link). Sem eles,
 *     usa os valores padrão.
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

    /**
     * Avaliações padrão exibidas em TODOS os passeios (padronização solicitada
     * pelo cliente — as mesmas que apareciam em "Saona Premium Brasil - Lancha").
     * São usadas no modo manual (fallback). Quando o passeio tem Place ID + chave
     * da API configurados, a Places API continua tendo prioridade e substitui estas.
     */
    private const DEFAULT_RATING = 5.0;
    private const DEFAULT_REVIEWS_URL = 'https://maps.app.goo.gl/FNdA3N4EQt58CBbS6?g_st=ic';
    private const DEFAULT_REVIEWS = [
        [
            'author' => 'Agendamentos Stand',
            'author_url' => '',
            'profile_photo' => '',
            'rating' => 5,
            'text' => 'Quero deixar aqui meu elogio. Eu viajo bastante. E a Punta Cana para Brasileiros me deu um dos melhores atendimentos que já tive. Parabéns pela atenção e carinho de Anna e Danilo. Os preços dos passeios foram excelentes. E também fiquei muito surpreso positivamente com o passeio de Saona Premium. Eu já havia feito esse passeio com outra empresa, mas com a Punta Cana Para Brasileiros foi muito mais completo, almoço delicioso, praia exclusiva para um brinde com espumante no meio do mar.',
            'relative_time' => 'Avaliação no Google · Saona Premium',
        ],
        [
            'author' => 'Daniela Martha',
            'author_url' => '',
            'profile_photo' => '',
            'rating' => 5,
            'text' => 'Fiz dois passeios com eles, Saona e Parasailing! Foi incriveeelllll!!! Saona com um almoço de primeira, caipirinha, cerveja Heineken… visitamos duas praias maravilhosas! Barco super rápido.. ótimo mesmo! O Parasailing foi incrível também! Inesquecível! Indico demais!',
            'relative_time' => 'Avaliação no Google · Saona + Parasailing',
        ],
        [
            'author' => 'Laysa Soares',
            'author_url' => '',
            'profile_photo' => '',
            'rating' => 5,
            'text' => 'Agência top demais! Danilo e auxiliares dele sensacionais. Passeio incrível. Vale a pena demais! Brasileiros, só venhammmmm 🫶',
            'relative_time' => 'Avaliação no Google',
        ],
    ];

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
     *
     * Padronização (item do cliente): TODOS os passeios exibem as mesmas
     * avaliações reais que apareciam em "Saona Premium Brasil - Lancha"
     * (self::DEFAULT_REVIEWS). Os campos do admin continuam funcionando como
     * sobreposição: nota, quantidade e link informados no passeio têm prioridade
     * sobre os padrões; e o Place ID + chave da API ainda substituem tudo isto
     * (ver getForTrip). Quando o admin não informa valores, usamos os padrões.
     */
    private function buildManualFallback(array $trip): ?array
    {
        $ratingInput = (float) ($trip['google_rating'] ?? 0);
        $countInput = (int) ($trip['google_reviews_count'] ?? 0);
        $urlInput = trim((string) ($trip['google_reviews_url'] ?? ''));

        $reviews = self::DEFAULT_REVIEWS;

        // Nota: usa a informada no admin; senão, a padrão.
        $rating = $ratingInput > 0 ? $ratingInput : self::DEFAULT_RATING;

        // Quantidade: usa a informada no admin; senão, o total de avaliações padrão.
        $count = $countInput > 0 ? $countInput : count($reviews);

        // Link "Ver todas": usa o informado no admin; senão, o padrão.
        $url = $urlInput !== '' ? $urlInput : self::DEFAULT_REVIEWS_URL;

        return [
            'rating' => $rating,
            'count' => $count,
            'url' => $url !== '' ? $url : null,
            'reviews' => $reviews,
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
