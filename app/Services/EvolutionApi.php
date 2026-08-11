<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;

/**
 * Wrapper HTTP para a Evolution API v2 (WhatsApp via Baileys).
 * Encapsula todas as chamadas HTTP com autenticação via header apikey.
 */
class EvolutionApi
{
    private string $apiUrl;
    private string $apiKey;
    private string $instanceName;

    /**
     * @param string $apiUrl URL base da Evolution API (ex: https://evo.exemplo.com)
     * @param string $apiKey Chave de autenticação da API
     * @param string $instanceName Nome da instância para operações
     */
    public function __construct(string $apiUrl, string $apiKey, string $instanceName)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->apiKey = $apiKey;
        $this->instanceName = $instanceName;
    }

    /**
     * Cria instância a partir das configurações de uma instância do banco.
     */
    public static function fromInstance(array $instance): self
    {
        return new self(
            $instance['api_url'],
            $instance['api_key'],
            $instance['instance_name']
        );
    }

    // ─────────────────────────────────────────────
    // INSTÂNCIA
    // ─────────────────────────────────────────────

    /**
     * Cria uma nova instância na Evolution API com webhook configurado.
     */
    public function createInstance(string $instanceName, string $webhookUrl): ?array
    {
        return $this->post('/instance/create', [
            'instanceName' => $instanceName,
            'integration' => 'WHATSAPP-BAILEYS',
            'webhook' => [
                'enabled' => true,
                'url' => $webhookUrl,
                'byEvents' => false,
                'base64' => true,
                'events' => [
                    'MESSAGES_UPSERT',
                    'MESSAGES_UPDATE',
                    'MESSAGES_DELETE',
                    'CONNECTION_UPDATE',
                    'QRCODE_UPDATED',
                ],
            ],
        ]);
    }

    /**
     * Conecta a instância e retorna QR Code.
     */
    public function connect(): ?array
    {
        return $this->get("/instance/connect/{$this->instanceName}");
    }

    /**
     * Retorna o estado da conexão da instância.
     */
    public function connectionState(): ?array
    {
        return $this->get("/instance/connectionState/{$this->instanceName}");
    }

    /**
     * Reinicia a instância.
     */
    public function restart(): ?array
    {
        return $this->put("/instance/restart/{$this->instanceName}");
    }

    /**
     * Faz logout (desconecta) da instância.
     */
    public function logout(): ?array
    {
        return $this->delete("/instance/logout/{$this->instanceName}");
    }

    /**
     * Deleta a instância da Evolution API.
     */
    public function deleteInstance(): ?array
    {
        return $this->delete("/instance/delete/{$this->instanceName}");
    }

    /**
     * Lista todas as instâncias registradas.
     */
    public function fetchInstances(): ?array
    {
        return $this->get('/instance/fetchInstances');
    }

    // ─────────────────────────────────────────────
    // MENSAGENS
    // ─────────────────────────────────────────────

    /**
     * Envia mensagem de texto.
     *
     * @param string $remoteJid JID do destinatário (numero@s.whatsapp.net ou grupo@g.us)
     * @param string $text Texto da mensagem
     * @return array|null Resposta da API
     */
    public function sendText(string $remoteJid, string $text): ?array
    {
        return $this->post("/message/sendText/{$this->instanceName}", [
            'number' => $remoteJid,
            'text' => $text,
        ]);
    }

    /**
     * Envia mídia (imagem, vídeo, documento).
     *
     * @param string $remoteJid JID do destinatário
     * @param string $mediaType Tipo: image, video, document
     * @param string $media URL ou base64 da mídia
     * @param string|null $caption Legenda opcional
     * @param string|null $fileName Nome do arquivo (para documentos)
     * @param string|null $mimeType MIME type da mídia
     * @return array|null
     */
    public function sendMedia(
        string $remoteJid,
        string $mediaType,
        string $media,
        ?string $caption = null,
        ?string $fileName = null,
        ?string $mimeType = null
    ): ?array {
        $payload = [
            'number' => $remoteJid,
            'mediatype' => $mediaType,
            'media' => $media,
        ];

        if ($caption !== null) {
            $payload['caption'] = $caption;
        }
        if ($fileName !== null) {
            $payload['fileName'] = $fileName;
        }
        if ($mimeType !== null) {
            $payload['mimetype'] = $mimeType;
        }

        return $this->post("/message/sendMedia/{$this->instanceName}", $payload);
    }

    /**
     * Envia áudio como PTT (Push to Talk).
     *
     * @param string $remoteJid JID do destinatário
     * @param string $audioBase64 Áudio em base64
     * @return array|null
     */
    public function sendAudio(string $remoteJid, string $audioBase64): ?array
    {
        return $this->post("/message/sendWhatsAppAudio/{$this->instanceName}", [
            'number' => $remoteJid,
            'audio' => $audioBase64,
        ]);
    }

    // ─────────────────────────────────────────────
    // CHAT
    // ─────────────────────────────────────────────

    /**
     * Busca chats da instância.
     */
    public function findChats(): ?array
    {
        return $this->get("/chat/findChats/{$this->instanceName}");
    }

    /**
     * Busca mensagens de um JID específico.
     */
    public function findMessages(string $remoteJid, int $limit = 50): ?array
    {
        return $this->post("/chat/findMessages/{$this->instanceName}", [
            'where' => [
                'key' => ['remoteJid' => $remoteJid],
            ],
            'limit' => $limit,
        ]);
    }

    /**
     * Busca contatos da instância.
     */
    public function findContacts(): ?array
    {
        return $this->get("/chat/findContacts/{$this->instanceName}");
    }

    /**
     * Verifica se números possuem WhatsApp.
     *
     * @param array $numbers Lista de números para verificar
     * @return array|null
     */
    public function checkIsWhatsapp(array $numbers): ?array
    {
        return $this->post("/chat/whatsappNumbers/{$this->instanceName}", [
            'numbers' => $numbers,
        ]);
    }

    /**
     * Marca mensagem como lida.
     */
    public function markAsRead(string $remoteJid, string $messageId): ?array
    {
        return $this->put("/chat/markMessageAsRead/{$this->instanceName}", [
            'readMessages' => [
                ['remoteJid' => $remoteJid, 'id' => $messageId],
            ],
        ]);
    }

    /**
     * Busca a foto de perfil de um contato.
     */
    public function fetchProfilePicture(string $remoteJid): ?array
    {
        return $this->get("/chat/fetchProfilePictureUrl/{$this->instanceName}", [
            'number' => $remoteJid,
        ]);
    }

    /**
     * Download de mídia em base64.
     */
    public function getBase64FromMedia(array $messageData): ?array
    {
        return $this->post("/chat/getBase64FromMediaMessage/{$this->instanceName}", [
            'message' => $messageData,
        ]);
    }

    // ─────────────────────────────────────────────
    // GRUPOS
    // ─────────────────────────────────────────────

    /**
     * Lista todos os grupos da instância.
     */
    public function fetchAllGroups(): ?array
    {
        return $this->get("/group/fetchAllGroups/{$this->instanceName}", ['getParticipants' => 'false']);
    }

    // ─────────────────────────────────────────────
    // WEBHOOK
    // ─────────────────────────────────────────────

    /**
     * Registra/atualiza o webhook da instância.
     */
    public function setWebhook(string $webhookUrl): ?array
    {
        return $this->post("/webhook/set/{$this->instanceName}", [
            'webhook' => [
                'enabled' => true,
                'url' => $webhookUrl,
                'byEvents' => false,
                'base64' => true,
                'events' => [
                    'MESSAGES_UPSERT',
                    'MESSAGES_UPDATE',
                    'MESSAGES_DELETE',
                    'CONNECTION_UPDATE',
                    'QRCODE_UPDATED',
                ],
            ],
        ]);
    }

    // ─────────────────────────────────────────────
    // UTILIDADES
    // ─────────────────────────────────────────────

    /**
     * Normaliza número de telefone para formato de JID individual.
     *
     * @param string $phone Número de telefone (qualquer formato)
     * @return string JID no formato numero@s.whatsapp.net
     */
    public static function phoneToJid(string $phone): string
    {
        $phone = self::normalizePhone($phone);
        return $phone . '@s.whatsapp.net';
    }

    /**
     * Normaliza número de telefone (remove caracteres, adiciona DDI 55 se necessário).
     */
    public static function normalizePhone(string $phone): string
    {
        // Remove tudo exceto dígitos
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Se não começa com 55 e tem ≤11 dígitos, adiciona DDI Brasil
        if (!str_starts_with($phone, '55') && strlen($phone) <= 11) {
            $phone = '55' . $phone;
        }

        return $phone;
    }

    /**
     * Verifica se o JID é de um grupo.
     */
    public static function isGroupJid(string $jid): bool
    {
        return str_ends_with($jid, '@g.us');
    }

    /**
     * Extrai o número do telefone de um JID.
     */
    public static function jidToPhone(string $jid): string
    {
        return explode('@', $jid)[0] ?? '';
    }

    // ─────────────────────────────────────────────
    // HTTP (Privado)
    // ─────────────────────────────────────────────

    /**
     * Requisição GET.
     */
    private function get(string $endpoint, array $queryParams = []): ?array
    {
        $url = $this->apiUrl . $endpoint;
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $this->request('GET', $url);
    }

    /**
     * Requisição POST.
     */
    private function post(string $endpoint, array $data = []): ?array
    {
        $url = $this->apiUrl . $endpoint;
        return $this->request('POST', $url, $data);
    }

    /**
     * Requisição PUT.
     */
    private function put(string $endpoint, array $data = []): ?array
    {
        $url = $this->apiUrl . $endpoint;
        return $this->request('PUT', $url, $data);
    }

    /**
     * Requisição DELETE.
     */
    private function delete(string $endpoint, array $data = []): ?array
    {
        $url = $this->apiUrl . $endpoint;
        return $this->request('DELETE', $url, $data);
    }

    /**
     * Executa a requisição HTTP via cURL.
     */
    private function request(string $method, string $url, ?array $data = null): ?array
    {
        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'apikey: ' . $this->apiKey,
        ];

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ];

        switch ($method) {
            case 'POST':
                $options[CURLOPT_POST] = true;
                if ($data !== null) {
                    $options[CURLOPT_POSTFIELDS] = json_encode($data);
                }
                break;
            case 'PUT':
                $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
                if ($data !== null) {
                    $options[CURLOPT_POSTFIELDS] = json_encode($data);
                }
                break;
            case 'DELETE':
                $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
                if ($data !== null) {
                    $options[CURLOPT_POSTFIELDS] = json_encode($data);
                }
                break;
            // GET é o padrão do cURL
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("[EvolutionApi] cURL error: {$error} | URL: {$url}");
            return null;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("[EvolutionApi] HTTP {$httpCode} | URL: {$url} | Response: {$response}");
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : ['raw' => $response];
    }

    // ─────────────────────────────────────────────
    // GETTERS
    // ─────────────────────────────────────────────

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getInstanceName(): string
    {
        return $this->instanceName;
    }
}
