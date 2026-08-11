<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Model para contatos WhatsApp (individuais e grupos).
 */
class WhatsappContact extends Model
{
    protected string $table = 'whatsapp_contacts';
    protected array $fillable = [
        'instance_id', 'remote_jid', 'phone', 'contact_name', 'push_name',
        'profile_picture_url', 'is_group', 'internal_notes', 'assigned_to',
        'service_status', 'last_message_at', 'is_archived', 'unread_count',
    ];

    /**
     * Lista contatos com filtros, agrupados por status.
     */
    public function listFiltered(int $instanceId, array $filters = [], bool $isGroup = false): array
    {
        $where = "wc.instance_id = ? AND wc.is_group = ? AND wc.is_archived = 0";
        $params = [$instanceId, $isGroup ? 1 : 0];

        // Filtro por busca (nome/telefone)
        if (!empty($filters['search'])) {
            $search = '%' . $filters['search'] . '%';
            $where .= " AND (wc.contact_name LIKE ? OR wc.push_name LIKE ? OR wc.phone LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        // Filtro por atendente
        if (!empty($filters['assigned_to'])) {
            if ($filters['assigned_to'] === 'none') {
                $where .= " AND wc.assigned_to IS NULL";
            } else {
                $where .= " AND wc.assigned_to = ?";
                $params[] = (int) $filters['assigned_to'];
            }
        }

        // Filtro por etiqueta
        if (!empty($filters['label_id'])) {
            $where .= " AND EXISTS (SELECT 1 FROM whatsapp_contact_labels wcl WHERE wcl.contact_id = wc.id AND wcl.label_id = ?)";
            $params[] = (int) $filters['label_id'];
        }

        // Filtro por status
        if (!empty($filters['service_status'])) {
            $where .= " AND wc.service_status = ?";
            $params[] = $filters['service_status'];
        }

        $sql = "SELECT wc.*, 
                    u.first_name as assigned_name,
                    (SELECT message_text FROM whatsapp_messages wm 
                     WHERE wm.contact_id = wc.id AND wm.is_deleted = 0 
                     ORDER BY wm.timestamp DESC LIMIT 1) as last_message_text,
                    (SELECT message_type FROM whatsapp_messages wm 
                     WHERE wm.contact_id = wc.id AND wm.is_deleted = 0 
                     ORDER BY wm.timestamp DESC LIMIT 1) as last_message_type,
                    (SELECT from_me FROM whatsapp_messages wm 
                     WHERE wm.contact_id = wc.id AND wm.is_deleted = 0 
                     ORDER BY wm.timestamp DESC LIMIT 1) as last_message_from_me
                FROM {$this->table} wc
                LEFT JOIN users u ON u.id = wc.assigned_to
                WHERE {$where}
                ORDER BY wc.last_message_at DESC";

        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Busca contato com suas etiquetas.
     */
    public function findWithLabels(int $id): ?array
    {
        $contact = $this->find($id);
        if (!$contact) return null;

        $contact['labels'] = $this->db->fetchAll(
            "SELECT wl.* FROM whatsapp_labels wl
             INNER JOIN whatsapp_contact_labels wcl ON wcl.label_id = wl.id
             WHERE wcl.contact_id = ?",
            [$id]
        );

        // CRM info
        $contact['crm_card'] = $this->db->fetchOne(
            "SELECT cc.*, col.name as column_name, b.name as board_name
             FROM crm_cards cc
             INNER JOIN crm_columns col ON col.id = cc.column_id
             INNER JOIN crm_boards b ON b.id = col.board_id
             WHERE cc.contact_id = ? AND b.is_active = 1
             LIMIT 1",
            [$id]
        );

        return $contact;
    }

    /**
     * Zera contagem de não lidas.
     */
    public function markAsRead(int $contactId): void
    {
        $this->db->update($this->table, ['unread_count' => 0], 'id = ?', [$contactId]);
    }

    /**
     * Incrementa não lidas.
     */
    public function incrementUnread(int $contactId): void
    {
        $this->db->query(
            "UPDATE {$this->table} SET unread_count = unread_count + 1 WHERE id = ?",
            [$contactId]
        );
    }

    /**
     * Conta contatos por tipo (para tabs).
     */
    public function countByType(int $instanceId): array
    {
        $contacts = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE instance_id = ? AND is_group = 0 AND is_archived = 0",
            [$instanceId]
        );
        $groups = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM {$this->table} WHERE instance_id = ? AND is_group = 1 AND is_archived = 0",
            [$instanceId]
        );

        return ['contacts' => $contacts, 'groups' => $groups];
    }

    /**
     * Busca contato por JID e instância.
     */
    public function findByJid(int $instanceId, string $remoteJid): ?array
    {
        return $this->db->fetchOne(
            "SELECT * FROM {$this->table} WHERE instance_id = ? AND remote_jid = ? LIMIT 1",
            [$instanceId, $remoteJid]
        );
    }
}
