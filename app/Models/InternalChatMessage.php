<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class InternalChatMessage extends Model
{
    protected string $table = 'internal_chat_messages';
    protected array $fillable = [
        'conversation_id', 'user_id', 'body', 'message_type',
    ];

    /**
     * Mensagens de uma conversa (com nome do autor).
     */
    public function getByConversation(int $conversationId, int $limit = 100): array
    {
        return $this->db->fetchAll(
            "SELECT m.*, u.first_name, u.last_name, u.role AS user_role
             FROM internal_chat_messages m
             LEFT JOIN users u ON u.id = m.user_id
             WHERE m.conversation_id = ?
             ORDER BY m.id ASC
             LIMIT " . (int) $limit,
            [$conversationId]
        );
    }

    /**
     * Novas mensagens após um determinado id (para polling).
     */
    public function getNewAfter(int $conversationId, int $afterId): array
    {
        return $this->db->fetchAll(
            "SELECT m.*, u.first_name, u.last_name, u.role AS user_role
             FROM internal_chat_messages m
             LEFT JOIN users u ON u.id = m.user_id
             WHERE m.conversation_id = ? AND m.id > ?
             ORDER BY m.id ASC",
            [$conversationId, $afterId]
        );
    }

    /**
     * Insere uma mensagem e atualiza o last_message_at da conversa.
     */
    public function post(int $conversationId, ?int $userId, string $body, string $type = 'text'): int
    {
        $id = $this->create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'body' => $body,
            'message_type' => $type === 'system' ? 'system' : 'text',
        ]);
        $this->db->update(
            'internal_chat_conversations',
            ['last_message_at' => date('Y-m-d H:i:s')],
            'id = ?',
            [$conversationId]
        );
        return $id;
    }

    /**
     * Total de mensagens não lidas do usuário (todas as conversas dele).
     */
    public function totalUnreadForUser(int $userId): int
    {
        return (int) $this->db->fetchColumn(
            "SELECT COUNT(*)
             FROM internal_chat_messages m
             INNER JOIN internal_chat_participants p
                ON p.conversation_id = m.conversation_id AND p.user_id = ?
             WHERE m.id > p.last_read_message_id AND m.user_id != ?",
            [$userId, $userId]
        );
    }
}
