<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Model para etiquetas (labels) de contatos WhatsApp.
 */
class WhatsappLabel extends Model
{
    protected string $table = 'whatsapp_labels';
    protected array $fillable = ['name', 'color', 'created_by'];

    /**
     * Lista todas as etiquetas.
     */
    public function listAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM {$this->table} ORDER BY name ASC"
        );
    }

    /**
     * Busca etiquetas de um contato.
     */
    public function getByContact(int $contactId): array
    {
        return $this->db->fetchAll(
            "SELECT wl.* FROM {$this->table} wl
             INNER JOIN whatsapp_contact_labels wcl ON wcl.label_id = wl.id
             WHERE wcl.contact_id = ?
             ORDER BY wl.name ASC",
            [$contactId]
        );
    }

    /**
     * Adiciona etiqueta a um contato.
     */
    public function addToContact(int $contactId, int $labelId): void
    {
        $exists = $this->db->fetchOne(
            "SELECT id FROM whatsapp_contact_labels WHERE contact_id = ? AND label_id = ?",
            [$contactId, $labelId]
        );

        if (!$exists) {
            $this->db->insert('whatsapp_contact_labels', [
                'contact_id' => $contactId,
                'label_id' => $labelId,
            ]);
        }
    }

    /**
     * Remove etiqueta de um contato.
     */
    public function removeFromContact(int $contactId, int $labelId): void
    {
        $this->db->delete(
            'whatsapp_contact_labels',
            'contact_id = ? AND label_id = ?',
            [$contactId, $labelId]
        );
    }
}
