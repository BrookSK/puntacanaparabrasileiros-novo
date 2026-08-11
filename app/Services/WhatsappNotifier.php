<?php
declare(strict_types=1);

namespace App\Services;

use Core\App;
use Core\Database;

/**
 * Serviço de notificações WhatsApp via Evolution API.
 * 
 * Envia mensagens automáticas do sistema para contatos individuais ou grupos.
 * Registra as mensagens no histórico do chat (aparecem na interface).
 * 
 * Prioridade de instância para envio:
 * 1. Instância padrão SEM vínculo a usuário
 * 2. Qualquer instância SEM vínculo
 * 3. Instância padrão (mesmo com vínculo)
 * 4. Qualquer instância disponível
 */
class WhatsappNotifier
{
    private Database $db;
    private App $app;
    private bool $groupNotifyEnabled;
    private string $defaultGroupJid;

    public function __construct()
    {
        $this->app = App::getInstance();
        $this->db = Database::getInstance();
        $this->groupNotifyEnabled = $this->app->setting('whatsapp_group_notify_enabled', '0') === '1';
        $this->defaultGroupJid = $this->app->setting('whatsapp_default_group_jid', '');
    }

    // ─────────────────────────────────────────────
    // MÉTODOS PÚBLICOS
    // ─────────────────────────────────────────────

    /**
     * Envia mensagem de texto para um grupo específico.
     *
     * @param string $groupJid JID do grupo (ex: 123456789@g.us)
     * @param string $message Texto da mensagem
     * @return bool True se enviado com sucesso
     */
    public function sendToGroup(string $groupJid, string $message): bool
    {
        if (empty($groupJid) || empty($message)) {
            return false;
        }

        // Buscar instância vinculada ao grupo ou fallback para padrão
        $instance = $this->getInstanceForGroup($groupJid);
        if (!$instance) {
            error_log("[WhatsappNotifier] Nenhuma instância disponível para envio ao grupo: {$groupJid}");
            return false;
        }

        $api = EvolutionApi::fromInstance($instance);
        $result = $api->sendText($groupJid, $message);

        if ($result === null) {
            error_log("[WhatsappNotifier] Falha ao enviar para grupo {$groupJid}");
            return false;
        }

        // Registrar no histórico de mensagens
        $this->registerMessage($instance, $groupJid, $message, $result);

        return true;
    }

    /**
     * Envia mensagem para o grupo padrão configurado nas Settings.
     * Só funciona se whatsapp_group_notify_enabled = 1.
     *
     * @param string $message Texto da mensagem
     * @return bool True se enviado com sucesso
     */
    public function sendToDefaultGroup(string $message): bool
    {
        if (!$this->groupNotifyEnabled) {
            return false;
        }

        if (empty($this->defaultGroupJid)) {
            error_log("[WhatsappNotifier] Grupo padrão não configurado (whatsapp_default_group_jid)");
            return false;
        }

        return $this->sendToGroup($this->defaultGroupJid, $message);
    }

    /**
     * Envia mensagem individual para um número de telefone.
     * Cria/atualiza o contato automaticamente.
     * A mensagem aparece no chat como "Sistema".
     *
     * @param string $phone Número de telefone (com DDI, ex: 5517999999999)
     * @param string $message Texto da mensagem
     * @param string|null $contactName Nome do contato (opcional, para criar/atualizar)
     * @return bool True se enviado com sucesso
     */
    public function sendToPhone(string $phone, string $message, ?string $contactName = null): bool
    {
        if (empty($phone) || empty($message)) {
            return false;
        }

        $phone = EvolutionApi::normalizePhone($phone);
        if (strlen($phone) < 10) {
            error_log("[WhatsappNotifier] Telefone inválido: {$phone}");
            return false;
        }

        // Buscar instância com prioridade correta
        $instance = $this->getInstanceForSending();
        if (!$instance) {
            error_log("[WhatsappNotifier] Nenhuma instância disponível para envio individual");
            return false;
        }

        $jid = EvolutionApi::phoneToJid($phone);
        $api = EvolutionApi::fromInstance($instance);

        $result = $api->sendText($jid, $message);

        if ($result === null) {
            error_log("[WhatsappNotifier] Falha ao enviar para {$phone}");
            return false;
        }

        // Criar/atualizar contato no banco
        $contactId = $this->upsertContact($instance, $jid, $phone, $contactName);

        // Registrar mensagem no histórico
        $this->registerMessage($instance, $jid, $message, $result, $contactId);

        return true;
    }

    /**
     * Envia mensagem de mídia para um número de telefone.
     *
     * @param string $phone Número de telefone
     * @param string $mediaType Tipo: image, video, document
     * @param string $mediaUrl URL ou base64 da mídia
     * @param string|null $caption Legenda opcional
     * @param string|null $fileName Nome do arquivo (para documentos)
     * @param string|null $contactName Nome do contato (opcional)
     * @return bool True se enviado com sucesso
     */
    public function sendMediaToPhone(
        string $phone,
        string $mediaType,
        string $mediaUrl,
        ?string $caption = null,
        ?string $fileName = null,
        ?string $contactName = null
    ): bool {
        if (empty($phone) || empty($mediaUrl)) {
            return false;
        }

        $phone = EvolutionApi::normalizePhone($phone);
        if (strlen($phone) < 10) {
            return false;
        }

        $instance = $this->getInstanceForSending();
        if (!$instance) {
            error_log("[WhatsappNotifier] Nenhuma instância disponível para envio de mídia");
            return false;
        }

        $jid = EvolutionApi::phoneToJid($phone);
        $api = EvolutionApi::fromInstance($instance);

        $result = $api->sendMedia($jid, $mediaType, $mediaUrl, $caption, $fileName);

        if ($result === null) {
            error_log("[WhatsappNotifier] Falha ao enviar mídia para {$phone}");
            return false;
        }

        // Criar/atualizar contato
        $contactId = $this->upsertContact($instance, $jid, $phone, $contactName);

        // Registrar mensagem de mídia no histórico
        $this->registerMediaMessage($instance, $jid, $mediaType, $mediaUrl, $caption, $fileName, $result, $contactId);

        return true;
    }

    // ─────────────────────────────────────────────
    // LÓGICA DE SELEÇÃO DE INSTÂNCIA
    // ─────────────────────────────────────────────

    /**
     * Seleciona a melhor instância para envio individual.
     * 
     * Prioridade:
     * 1. Instância padrão SEM vínculo a usuário (is_default=1 AND user_id IS NULL)
     * 2. Qualquer instância SEM vínculo (user_id IS NULL)
     * 3. Instância padrão (is_default=1, mesmo com vínculo)
     * 4. Qualquer instância conectada
     *
     * @return array|null Dados da instância ou null
     */
    private function getInstanceForSending(): ?array
    {
        // 1. Padrão sem vínculo
        $instance = $this->db->fetchOne(
            "SELECT * FROM whatsapp_instances 
             WHERE is_default = 1 AND user_id IS NULL AND connection_status = 'open' 
             LIMIT 1"
        );
        if ($instance) return $instance;

        // 2. Qualquer sem vínculo
        $instance = $this->db->fetchOne(
            "SELECT * FROM whatsapp_instances 
             WHERE user_id IS NULL AND connection_status = 'open' 
             LIMIT 1"
        );
        if ($instance) return $instance;

        // 3. Padrão (com vínculo)
        $instance = $this->db->fetchOne(
            "SELECT * FROM whatsapp_instances 
             WHERE is_default = 1 AND connection_status = 'open' 
             LIMIT 1"
        );
        if ($instance) return $instance;

        // 4. Qualquer conectada
        $instance = $this->db->fetchOne(
            "SELECT * FROM whatsapp_instances 
             WHERE connection_status = 'open' 
             LIMIT 1"
        );

        return $instance;
    }

    /**
     * Seleciona instância para envio a grupo.
     * Busca a instância que possui o contato do grupo ou fallback para prioridade padrão.
     *
     * @param string $groupJid JID do grupo
     * @return array|null
     */
    private function getInstanceForGroup(string $groupJid): ?array
    {
        // Buscar instância que tem este grupo como contato
        $instance = $this->db->fetchOne(
            "SELECT wi.* FROM whatsapp_instances wi
             INNER JOIN whatsapp_contacts wc ON wc.instance_id = wi.id
             WHERE wc.remote_jid = ? AND wi.connection_status = 'open'
             LIMIT 1",
            [$groupJid]
        );

        if ($instance) return $instance;

        // Fallback: usar lógica padrão
        return $this->getInstanceForSending();
    }

    // ─────────────────────────────────────────────
    // REGISTRO NO BANCO (HISTÓRICO)
    // ─────────────────────────────────────────────

    /**
     * Cria ou atualiza contato no banco.
     *
     * @param array $instance Dados da instância
     * @param string $jid JID do contato
     * @param string $phone Número normalizado
     * @param string|null $contactName Nome do contato
     * @return int ID do contato
     */
    private function upsertContact(array $instance, string $jid, string $phone, ?string $contactName): int
    {
        $instanceId = (int) $instance['id'];

        // Buscar contato existente pelo JID exato
        $contact = $this->db->fetchOne(
            "SELECT id, contact_name FROM whatsapp_contacts 
             WHERE instance_id = ? AND remote_jid = ? LIMIT 1",
            [$instanceId, $jid]
        );

        if ($contact) {
            // Atualizar last_message_at (NUNCA sobrescreve contact_name se já definido)
            $updateData = ['last_message_at' => date('Y-m-d H:i:s')];
            if (empty($contact['contact_name']) && !empty($contactName)) {
                $updateData['contact_name'] = $contactName;
            }

            $this->db->update('whatsapp_contacts', $updateData, 'id = ?', [(int) $contact['id']]);
            return (int) $contact['id'];
        }

        // Buscar por últimos 8 dígitos (deduplicação 9° dígito)
        if (!EvolutionApi::isGroupJid($jid)) {
            $last8 = substr($phone, -8);
            $contact = $this->db->fetchOne(
                "SELECT id, contact_name FROM whatsapp_contacts 
                 WHERE instance_id = ? AND is_group = 0 AND RIGHT(phone, 8) = ?
                 LIMIT 1",
                [$instanceId, $last8]
            );

            if ($contact) {
                $updateData = [
                    'remote_jid' => $jid,
                    'phone' => $phone,
                    'last_message_at' => date('Y-m-d H:i:s'),
                ];
                if (empty($contact['contact_name']) && !empty($contactName)) {
                    $updateData['contact_name'] = $contactName;
                }

                $this->db->update('whatsapp_contacts', $updateData, 'id = ?', [(int) $contact['id']]);
                return (int) $contact['id'];
            }
        }

        // Criar novo contato
        $contactId = $this->db->insert('whatsapp_contacts', [
            'instance_id' => $instanceId,
            'remote_jid' => $jid,
            'phone' => $phone,
            'contact_name' => $contactName,
            'push_name' => $contactName,
            'is_group' => EvolutionApi::isGroupJid($jid) ? 1 : 0,
            'service_status' => 'novo',
            'last_message_at' => date('Y-m-d H:i:s'),
            'unread_count' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $contactId;
    }

    /**
     * Registra mensagem de texto no histórico.
     *
     * @param array $instance Dados da instância
     * @param string $jid JID do destinatário
     * @param string $message Texto enviado
     * @param array $apiResult Resposta da Evolution API
     * @param int|null $contactId ID do contato (se individual)
     */
    private function registerMessage(array $instance, string $jid, string $message, array $apiResult, ?int $contactId = null): void
    {
        $instanceId = (int) $instance['id'];

        // Se não temos contactId (grupo), buscar ou criar
        if ($contactId === null) {
            $contactId = $this->getOrCreateGroupContact($instanceId, $jid);
        }

        if (!$contactId) return;

        // Extrair message_id da resposta da API
        $messageId = $apiResult['key']['id'] ?? $apiResult['messageId'] ?? ('sys_' . uniqid());

        $this->db->insert('whatsapp_messages', [
            'instance_id' => $instanceId,
            'contact_id' => $contactId,
            'remote_jid' => $jid,
            'message_id' => $messageId,
            'from_me' => 1,
            'message_type' => 'text',
            'message_text' => $message,
            'sender_name' => 'Sistema',
            'timestamp' => date('Y-m-d H:i:s'),
            'is_read' => 1,
            'ack_status' => 'sent',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Registra mensagem de mídia no histórico.
     */
    private function registerMediaMessage(
        array $instance,
        string $jid,
        string $mediaType,
        string $mediaUrl,
        ?string $caption,
        ?string $fileName,
        array $apiResult,
        ?int $contactId = null
    ): void {
        $instanceId = (int) $instance['id'];

        if ($contactId === null) {
            $contactId = $this->getOrCreateGroupContact($instanceId, $jid);
        }

        if (!$contactId) return;

        $messageId = $apiResult['key']['id'] ?? $apiResult['messageId'] ?? ('sys_' . uniqid());

        // Mapear mediaType para message_type do banco
        $messageType = match ($mediaType) {
            'image' => 'image',
            'video' => 'video',
            'document' => 'document',
            default => 'document',
        };

        $this->db->insert('whatsapp_messages', [
            'instance_id' => $instanceId,
            'contact_id' => $contactId,
            'remote_jid' => $jid,
            'message_id' => $messageId,
            'from_me' => 1,
            'message_type' => $messageType,
            'message_text' => $caption,
            'media_url' => $mediaUrl,
            'media_filename' => $fileName,
            'sender_name' => 'Sistema',
            'timestamp' => date('Y-m-d H:i:s'),
            'is_read' => 1,
            'ack_status' => 'sent',
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Busca ou cria contato de grupo no banco.
     */
    private function getOrCreateGroupContact(int $instanceId, string $jid): ?int
    {
        $contact = $this->db->fetchOne(
            "SELECT id FROM whatsapp_contacts WHERE instance_id = ? AND remote_jid = ? LIMIT 1",
            [$instanceId, $jid]
        );

        if ($contact) {
            // Atualizar last_message_at
            $this->db->update(
                'whatsapp_contacts',
                ['last_message_at' => date('Y-m-d H:i:s')],
                'id = ?',
                [(int) $contact['id']]
            );
            return (int) $contact['id'];
        }

        // Criar contato de grupo
        $contactId = $this->db->insert('whatsapp_contacts', [
            'instance_id' => $instanceId,
            'remote_jid' => $jid,
            'phone' => EvolutionApi::jidToPhone($jid),
            'is_group' => EvolutionApi::isGroupJid($jid) ? 1 : 0,
            'service_status' => 'novo',
            'last_message_at' => date('Y-m-d H:i:s'),
            'unread_count' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return $contactId ?: null;
    }

    // ─────────────────────────────────────────────
    // HELPERS PÚBLICOS
    // ─────────────────────────────────────────────

    /**
     * Verifica se o sistema de notificações WhatsApp está operacional.
     * (tem pelo menos uma instância conectada)
     */
    public function isAvailable(): bool
    {
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM whatsapp_instances WHERE connection_status = 'open'"
        );
        return (int) $count > 0;
    }

    /**
     * Verifica se notificações em grupo estão habilitadas.
     */
    public function isGroupNotifyEnabled(): bool
    {
        return $this->groupNotifyEnabled && !empty($this->defaultGroupJid);
    }
}
