<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class InternalChatConversation extends Model
{
    protected string $table = 'internal_chat_conversations';
    protected array $fillable = [
        'type', 'title', 'created_by', 'related_contact_id', 'related_booking_id', 'last_message_at',
    ];

    /**
     * Lista as conversas de um usuário (as que ele participa), com dados para exibição:
     * título calculado, outro participante (direct), contador de não lidas, última msg.
     */
    public function listForUser(int $userId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT c.*, p.last_read_message_id
             FROM internal_chat_conversations c
             INNER JOIN internal_chat_participants p ON p.conversation_id = c.id
             WHERE p.user_id = ?
             ORDER BY (c.last_message_at IS NULL), c.last_message_at DESC, c.id DESC",
            [$userId]
        );

        foreach ($rows as &$conv) {
            $conv['participants'] = $this->participants((int) $conv['id']);

            // Título de exibição
            if ($conv['type'] === 'group') {
                $conv['display_title'] = $conv['title'] ?: 'Grupo';
            } else {
                // Conversa direta: mostrar o nome do outro participante
                $other = null;
                foreach ($conv['participants'] as $pp) {
                    if ((int) $pp['user_id'] !== $userId) { $other = $pp; break; }
                }
                $conv['display_title'] = $other
                    ? trim(($other['first_name'] ?? '') . ' ' . ($other['last_name'] ?? ''))
                    : 'Conversa';
            }

            // Cliente vinculado (se houver)
            $conv['related_contact'] = null;
            if (!empty($conv['related_contact_id'])) {
                $conv['related_contact'] = $this->db->fetchOne(
                    "SELECT id, contact_name, push_name, phone FROM whatsapp_contacts WHERE id = ?",
                    [(int) $conv['related_contact_id']]
                );
            }

            // Última mensagem + não lidas
            $last = $this->db->fetchOne(
                "SELECT m.id, m.body, m.message_type, m.created_at, u.first_name
                 FROM internal_chat_messages m
                 LEFT JOIN users u ON u.id = m.user_id
                 WHERE m.conversation_id = ?
                 ORDER BY m.id DESC LIMIT 1",
                [(int) $conv['id']]
            );
            $conv['last_message'] = $last;

            $conv['unread'] = (int) $this->db->fetchColumn(
                "SELECT COUNT(*) FROM internal_chat_messages
                 WHERE conversation_id = ? AND id > ? AND user_id != ?",
                [(int) $conv['id'], (int) $conv['last_read_message_id'], $userId]
            );
        }
        unset($conv);

        return $rows;
    }

    /**
     * Participantes de uma conversa (com nome/role do usuário).
     */
    public function participants(int $conversationId): array
    {
        return $this->db->fetchAll(
            "SELECT p.user_id, p.role AS participant_role, u.first_name, u.last_name, u.role AS user_role
             FROM internal_chat_participants p
             LEFT JOIN users u ON u.id = p.user_id
             WHERE p.conversation_id = ?
             ORDER BY u.first_name ASC",
            [$conversationId]
        );
    }

    public function isParticipant(int $conversationId, int $userId): bool
    {
        return (bool) $this->db->fetchColumn(
            "SELECT id FROM internal_chat_participants WHERE conversation_id = ? AND user_id = ? LIMIT 1",
            [$conversationId, $userId]
        );
    }

    /**
     * Encontra uma conversa direta entre dois usuários (se existir).
     */
    public function findDirectBetween(int $userA, int $userB): ?array
    {
        return $this->db->fetchOne(
            "SELECT c.* FROM internal_chat_conversations c
             INNER JOIN internal_chat_participants p1 ON p1.conversation_id = c.id AND p1.user_id = ?
             INNER JOIN internal_chat_participants p2 ON p2.conversation_id = c.id AND p2.user_id = ?
             WHERE c.type = 'direct'
             LIMIT 1",
            [$userA, $userB]
        );
    }

    /**
     * Cria uma conversa direta entre dois usuários (ou retorna a existente).
     */
    public function createDirect(int $userA, int $userB): int
    {
        $existing = $this->findDirectBetween($userA, $userB);
        if ($existing) return (int) $existing['id'];

        $id = $this->create([
            'type' => 'direct',
            'created_by' => $userA,
            'last_message_at' => date('Y-m-d H:i:s'),
        ]);
        $this->addParticipant($id, $userA, 'admin');
        $this->addParticipant($id, $userB, 'member');
        return $id;
    }

    /**
     * Cria um grupo com um conjunto de participantes.
     * @param int[] $userIds
     */
    public function createGroup(string $title, int $creatorId, array $userIds, ?int $relatedContactId = null): int
    {
        $id = $this->create([
            'type' => 'group',
            'title' => $title !== '' ? $title : 'Grupo',
            'created_by' => $creatorId,
            'related_contact_id' => $relatedContactId,
            'last_message_at' => date('Y-m-d H:i:s'),
        ]);

        $this->addParticipant($id, $creatorId, 'admin');
        foreach ($userIds as $uid) {
            $uid = (int) $uid;
            if ($uid > 0 && $uid !== $creatorId) {
                $this->addParticipant($id, $uid, 'member');
            }
        }
        return $id;
    }

    public function addParticipant(int $conversationId, int $userId, string $role = 'member'): void
    {
        // Evita duplicidade
        if ($this->isParticipant($conversationId, $userId)) return;
        $this->db->insert('internal_chat_participants', [
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'role' => $role === 'admin' ? 'admin' : 'member',
        ]);
    }

    /**
     * Marca a conversa como lida para um usuário (até a última mensagem).
     */
    public function markRead(int $conversationId, int $userId): void
    {
        $lastId = (int) $this->db->fetchColumn(
            "SELECT COALESCE(MAX(id),0) FROM internal_chat_messages WHERE conversation_id = ?",
            [$conversationId]
        );
        $this->db->update(
            'internal_chat_participants',
            ['last_read_message_id' => $lastId],
            'conversation_id = ? AND user_id = ?',
            [$conversationId, $userId]
        );
    }

    public function touch(int $conversationId): void
    {
        $this->db->update($this->table, ['last_message_at' => date('Y-m-d H:i:s')], 'id = ?', [$conversationId]);
    }
}
