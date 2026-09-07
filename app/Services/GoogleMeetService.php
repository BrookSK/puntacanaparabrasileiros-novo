<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Integração real com o Google Meet via Google Calendar API.
 *
 * O Google Meet não permite criar salas por URL simples (como o Jitsi).
 * O caminho oficial é criar um evento no Google Calendar com
 * `conferenceData.createRequest` (type `hangoutsMeet`); o Google devolve
 * então o `hangoutLink` (o link do Meet) e o `id` do evento.
 *
 * Autenticação: OAuth2 com refresh token de longa duração (obtido uma única
 * vez pelo admin no painel). A cada operação trocamos o refresh_token por um
 * access_token de curta duração.
 *
 * Padrão de cliente HTTP idêntico ao restante dos Services (cURL nativo).
 * Todos os métodos são resilientes: em caso de falha logam e retornam null/false,
 * nunca lançam exceção fatal — assim o agendamento nunca quebra por causa da API.
 */
class GoogleMeetService
{
    private const TOKEN_URL = 'https://oauth2.googleapis.com/token';
    private const CALENDAR_BASE = 'https://www.googleapis.com/calendar/v3';
    private const AUTH_URL = 'https://accounts.google.com/o/oauth2/v2/auth';
    private const SCOPE = 'https://www.googleapis.com/auth/calendar.events';

    private string $clientId;
    private string $clientSecret;
    private string $refreshToken;
    private string $calendarId;
    private ?string $accessToken = null;

    public function __construct()
    {
        $app = App::getInstance();
        $this->clientId = trim((string) $app->setting('google_meet_client_id', ''));
        $this->clientSecret = trim((string) $app->setting('google_meet_client_secret', ''));
        $this->refreshToken = trim((string) $app->setting('google_meet_refresh_token', ''));
        $this->calendarId = trim((string) $app->setting('google_meet_calendar_id', 'primary')) ?: 'primary';
    }

    /**
     * O módulo está habilitado E possui as credenciais mínimas para operar?
     */
    public function isConfigured(): bool
    {
        $app = App::getInstance();
        if ($app->setting('google_meet_enabled', '0') !== '1') {
            return false;
        }
        return $this->clientId !== ''
            && $this->clientSecret !== ''
            && $this->refreshToken !== '';
    }

    /**
     * Cria um evento no Google Calendar com sala do Google Meet.
     *
     * @param string $summary       Título do evento
     * @param string $description    Descrição
     * @param string $scheduledAt    Início "Y-m-d H:i:s" (hora local do servidor/timezone informado)
     * @param int    $durationMinutes Duração em minutos
     * @param string $timezone       Timezone IANA (ex: "America/Santo_Domingo")
     * @param string[] $attendeeEmails E-mails a convidar (opcional)
     *
     * @return array{event_id:string, meeting_link:string, calendar_id:string}|null
     */
    public function createMeeting(
        string $summary,
        string $description,
        string $scheduledAt,
        int $durationMinutes,
        string $timezone = 'America/Santo_Domingo',
        array $attendeeEmails = []
    ): ?array {
        if (!$this->isConfigured()) {
            error_log('[GoogleMeetService] Não configurado (credenciais ausentes ou módulo desativado).');
            return null;
        }

        $token = $this->getAccessToken();
        if ($token === null) {
            return null;
        }

        $startTs = strtotime($scheduledAt);
        if ($startTs === false) {
            error_log('[GoogleMeetService] scheduledAt inválido: ' . $scheduledAt);
            return null;
        }
        $duration = $durationMinutes > 0 ? $durationMinutes : 30;
        $endTs = $startTs + $duration * 60;

        // A Calendar API aceita RFC3339. Enviamos dateTime local + timeZone.
        $start = date('Y-m-d\TH:i:s', $startTs);
        $end = date('Y-m-d\TH:i:s', $endTs);

        $body = [
            'summary' => $summary,
            'description' => $description,
            'start' => ['dateTime' => $start, 'timeZone' => $timezone],
            'end' => ['dateTime' => $end, 'timeZone' => $timezone],
            'conferenceData' => [
                'createRequest' => [
                    'requestId' => bin2hex(random_bytes(8)),
                    'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                ],
            ],
        ];

        if (!empty($attendeeEmails)) {
            $body['attendees'] = array_values(array_map(
                static fn(string $e) => ['email' => $e],
                array_filter($attendeeEmails, static fn($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ));
        }

        // conferenceDataVersion=1 é obrigatório para o Google gerar o link do Meet.
        $endpoint = self::CALENDAR_BASE . '/calendars/' . rawurlencode($this->calendarId)
            . '/events?conferenceDataVersion=1&sendUpdates=all';

        $response = $this->request('POST', $endpoint, $body, $token);
        if ($response === null) {
            return null;
        }

        $meetingLink = $this->extractMeetLink($response);
        $eventId = (string) ($response['id'] ?? '');

        if ($meetingLink === null || $eventId === '') {
            error_log('[GoogleMeetService] Evento criado sem hangoutLink. Resposta: ' . json_encode($response));
            return null;
        }

        return [
            'event_id' => $eventId,
            'meeting_link' => $meetingLink,
            'calendar_id' => $this->calendarId,
        ];
    }

    /**
     * Remove um evento do Google Calendar (cancela a sala do Meet).
     *
     * @return bool true se removido (ou já inexistente), false em falha.
     */
    public function deleteMeeting(string $eventId, ?string $calendarId = null): bool
    {
        if ($eventId === '' || !$this->isConfigured()) {
            return false;
        }
        $token = $this->getAccessToken();
        if ($token === null) {
            return false;
        }

        $calendar = $calendarId !== null && $calendarId !== '' ? $calendarId : $this->calendarId;
        $endpoint = self::CALENDAR_BASE . '/calendars/' . rawurlencode($calendar)
            . '/events/' . rawurlencode($eventId) . '?sendUpdates=all';

        // DELETE bem-sucedido retorna 204 (sem corpo). request() considera 2xx = sucesso.
        $ok = $this->requestRaw('DELETE', $endpoint, null, $token, $httpCode);
        // 410 = já removido; tratamos como sucesso idempotente.
        return $ok || $httpCode === 410 || $httpCode === 404;
    }

    /**
     * Gera a URL de consentimento OAuth para o admin autorizar o acesso.
     * Usamos access_type=offline + prompt=consent para garantir um refresh_token.
     */
    public function buildAuthUrl(string $redirectUri, string $state = ''): string
    {
        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => self::SCOPE,
            'access_type' => 'offline',
            'prompt' => 'consent',
            'include_granted_scopes' => 'true',
        ];
        if ($state !== '') {
            $params['state'] = $state;
        }
        return self::AUTH_URL . '?' . http_build_query($params);
    }

    /**
     * Troca o `code` retornado pelo Google por tokens.
     * Retorna o array de tokens (contém `refresh_token`) ou null.
     *
     * @return array<string,mixed>|null
     */
    public function exchangeCodeForTokens(string $code, string $redirectUri): ?array
    {
        if ($this->clientId === '' || $this->clientSecret === '') {
            error_log('[GoogleMeetService] client_id/secret ausentes na troca de código.');
            return null;
        }

        $response = $this->requestForm(self::TOKEN_URL, [
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ]);

        if ($response === null || empty($response['access_token'])) {
            error_log('[GoogleMeetService] Falha ao trocar código por tokens.');
            return null;
        }
        return $response;
    }

    // ─────────────────────────────────────────────
    // Internos
    // ─────────────────────────────────────────────

    /**
     * Troca o refresh_token por um access_token de curta duração (cacheado na instância).
     */
    private function getAccessToken(): ?string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $response = $this->requestForm(self::TOKEN_URL, [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $this->refreshToken,
            'grant_type' => 'refresh_token',
        ]);

        if ($response === null || empty($response['access_token'])) {
            error_log('[GoogleMeetService] Falha ao obter access_token via refresh_token.');
            return null;
        }

        $this->accessToken = (string) $response['access_token'];
        return $this->accessToken;
    }

    /**
     * Extrai o link do Meet da resposta do evento (hangoutLink ou conferenceData.entryPoints).
     */
    private function extractMeetLink(array $event): ?string
    {
        if (!empty($event['hangoutLink'])) {
            return (string) $event['hangoutLink'];
        }
        $entryPoints = $event['conferenceData']['entryPoints'] ?? [];
        foreach ($entryPoints as $ep) {
            if (($ep['entryPointType'] ?? '') === 'video' && !empty($ep['uri'])) {
                return (string) $ep['uri'];
            }
        }
        return null;
    }

    /**
     * Requisição JSON autenticada por Bearer token. Retorna array decodificado ou null.
     *
     * @param array<string,mixed>|null $data
     * @return array<string,mixed>|null
     */
    private function request(string $method, string $url, ?array $data, string $token): ?array
    {
        $httpCode = 0;
        $raw = $this->curl($method, $url, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ], $data !== null ? json_encode($data) : null, $httpCode);

        if ($raw === null) {
            return null;
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("[GoogleMeetService] HTTP {$httpCode} | {$url} | {$raw}");
            return null;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Igual a request(), mas retorna apenas sucesso/falha (para DELETE 204 sem corpo).
     */
    private function requestRaw(string $method, string $url, ?array $data, string $token, ?int &$httpCode = null): bool
    {
        $httpCode = 0;
        $raw = $this->curl($method, $url, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ], $data !== null ? json_encode($data) : null, $httpCode);

        if ($raw === null) {
            return false;
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("[GoogleMeetService] HTTP {$httpCode} | {$url} | {$raw}");
            return false;
        }
        return true;
    }

    /**
     * POST application/x-www-form-urlencoded (endpoint de token do OAuth).
     *
     * @param array<string,string> $fields
     * @return array<string,mixed>|null
     */
    private function requestForm(string $url, array $fields): ?array
    {
        $httpCode = 0;
        $raw = $this->curl(
            'POST',
            $url,
            ['Content-Type: application/x-www-form-urlencoded'],
            http_build_query($fields),
            $httpCode
        );

        if ($raw === null) {
            return null;
        }
        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("[GoogleMeetService] Token HTTP {$httpCode} | {$raw}");
            return null;
        }
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Execução cURL base (mesmo padrão de EvolutionApi/PagBankService).
     *
     * @param string[] $headers
     */
    private function curl(string $method, string $url, array $headers, ?string $payload, ?int &$httpCode = null): ?string
    {
        $ch = curl_init();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_CUSTOMREQUEST => $method,
        ];
        if ($payload !== null) {
            $options[CURLOPT_POSTFIELDS] = $payload;
        }
        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error !== '') {
            error_log("[GoogleMeetService] cURL error: {$error} | URL: {$url}");
            return null;
        }
        return $response === false ? null : (string) $response;
    }
}
