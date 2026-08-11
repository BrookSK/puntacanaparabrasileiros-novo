<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Model para mensagens WhatsApp.
 */
class WhatsappMessage extends Model
{
    protected string $table = 'whatsapp_messages';
    protected array $fillable = [
        'instance_id', 'contact_id', 'remote_jid', 'message_id',
        'from_me', 'message_type', 'message_text', 'transcription',
        'media_url', 'media_mime_type', 'media_filename',
        'quoted_message_id', 'sender_name', 'participant_jid',
        'timestamp', 'is_read', 'is_deleted', 'ack_status',
    ];

    /**
     * Busca mensagens de um contato (paginado, mais recentes primeiro).
     * Usado para carregamento inicial e infinite scroll.
     */
    public function getByContact(int $contactId, int $limit = 50, ?int $beforeId = null): array
    {
        $where = "contact_id = ?";
        $params = [$contactId];

        if ($beforeId) {
            $where .= " AND id < ?";
            $params[] = $beforeId;
        }

        $messages = $this->db->fetchAll(
            "SELECT * FROM {$this->table} 
             WHERE {$where}
             ORDER BY timestamp DESC, id DESC
             LIMIT ?",
            array_merge($params, [$limit])
        );

        // Reverter para ordem cronológica
        return array_reverse($messages);
    }

    /**
     * Polling: busca mensagens novas após um ID.
     */
    public function getNewMessages(int $contactId, int $afterId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table}
             WHERE contact_id = ? AND id > ?
             ORDER BY timestamp ASC, id ASC",
            [$contactId, $afterId]
        );
    }

    /**
     * Busca IDs de mensagens deletadas (para polling).
     */
    public function getDeletedIds(int $contactId, array $knownIds): array
    {
        if (empty($knownIds)) return [];

        $placeholders = implode(',', array_fill(0, count($knownIds), '?'));
        $params = array_merge([$contactId], $knownIds);

        return $this->db->fetchAll(
            "SELECT id FROM {$this->table}
             WHERE contact_id = ? AND id IN ({$placeholders}) AND is_deleted = 1",
            $params
        );
    }

    /**
     * Busca status de ack das últimas mensagens enviadas.
     */
    public function getMessageStatuses(int $contactId, int $limit = 50): array
    {
        return $this->db->fetchAll(
            "SELECT id, message_id, ack_status FROM {$this->table}
             WHERE contact_id = ? AND from_me = 1
             ORDER BY id DESC LIMIT ?",
            [$contactId, $limit]
        );
    }

    /**
     * Busca mensagem por message_id da Evolution API.
     */
    public function findByMessageId(int $instanceId, string $messageId): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE instance_id = ? AND message_id = ? LIMIT 1",
            [$instanceId, $messageId]
        );
    }

    /**
     * Atualiza status de ack de uma mensagem.
     */
    public function updateAck(int $instanceId, string $messageId, string $ackStatus): void
    {
        $this->db->query(
            "UPDATE {$this->table} SET ack_status = ? WHERE instance_id = ? AND message_id = ?",
            [$ackStatus, $instanceId, $messageId]
        );
    }

    /**
     * Marca mensagem como deletada.
     */
    public function markDeleted(int $instanceId, string $messageId): void
    {
        $this->db->query(
            "UPDATE {$this->table} SET is_deleted = 1 WHERE instance_id = ? AND message_id = ?",
            [$instanceId, $messageId]
        );
    }

    /**
     * Salva transcrição de áudio.
     */
    public function saveTranscription(int $id, string $transcription): void
    {
        $this->db->update($this->table, ['transcription' => $transcription], 'id = ?', [$id]);
    }
}
