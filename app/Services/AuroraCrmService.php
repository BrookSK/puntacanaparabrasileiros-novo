<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\CrmBoard;
use App\Models\CrmCard;
use App\Models\CrmColumn;
use Core\Database;

/**
 * Integração da Aurora com o CRM.
 *
 * Cria o card do lead na primeira interação e move o card pelas etapas
 * conforme a conversa evolui:
 *   - 1ª mensagem do cliente        → cria card em "Novo Lead"
 *   - Aurora respondeu / em conversa → "Contato Feito"
 *   - Intenção de compra (handoff)   → "Em Negociação" (aciona atendente humano)
 *
 * As etapas são resolvidas por NOME dentro do board configurado (aurora_crm_board_id).
 * Todas as atividades são registradas com user_id NULL (autoria: Aurora).
 */
class AuroraCrmService
{
    /** Nomes das etapas alvo (casam com as colunas padrão do board). */
    private const STAGE_NEW = 'Novo Lead';
    private const STAGE_CONTACTED = 'Contato Feito';
    private const STAGE_NEGOTIATION = 'Em Negociação';

    private Database $db;
    private CrmCard $cardModel;
    private CrmColumn $columnModel;
    private CrmBoard $boardModel;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->cardModel = new CrmCard();
        $this->columnModel = new CrmColumn();
        $this->boardModel = new CrmBoard();
    }

    /**
     * Processa o lead no CRM a partir de uma interação da Aurora.
     *
     * @param array $contact  Linha de whatsapp_contacts
     * @param bool  $handoff  true se a Aurora sinalizou intenção de compra
     */
    public function processLead(array $contact, bool $handoff): void
    {
        try {
            $contactId = (int) ($contact['id'] ?? 0);
            if ($contactId <= 0) {
                return;
            }

            $boardId = $this->resolveBoardId();
            if ($boardId === null) {
                return; // Sem board de CRM configurado/disponível.
            }

            $card = $this->findCardForContact($contactId);

            if (!$card) {
                // Primeira interação: cria o card em "Novo Lead" e move conforme o estágio.
                $card = $this->createCard($boardId, $contact);
                if (!$card) {
                    return;
                }
            }

            // Determinar etapa alvo.
            $targetStage = $handoff ? self::STAGE_NEGOTIATION : self::STAGE_CONTACTED;
            $this->moveToStage($boardId, (int) $card['id'], $targetStage, $handoff);
        } catch (\Throwable $e) {
            error_log('[Aurora][CRM] Falha ao processar lead: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────
    // INTERNOS
    // ─────────────────────────────────────────────

    /**
     * Resolve o board a usar: setting aurora_crm_board_id ou o primeiro board ativo.
     */
    private function resolveBoardId(): ?int
    {
        $configured = (int) (setting('aurora_crm_board_id', '') ?: 0);
        if ($configured > 0) {
            $exists = $this->db->fetchColumn(
                "SELECT id FROM crm_boards WHERE id = ? AND is_active = 1",
                [$configured]
            );
            if ($exists) {
                return (int) $exists;
            }
        }

        $first = $this->db->fetchColumn(
            "SELECT id FROM crm_boards WHERE is_active = 1 ORDER BY id ASC LIMIT 1"
        );
        return $first ? (int) $first : null;
    }

    /**
     * Busca o card existente do contato no board ativo.
     */
    private function findCardForContact(int $contactId): ?array
    {
        return $this->db->fetchOne(
            "SELECT cc.* FROM crm_cards cc
             INNER JOIN crm_columns col ON col.id = cc.column_id
             INNER JOIN crm_boards b ON b.id = col.board_id
             WHERE cc.contact_id = ? AND b.is_active = 1
             ORDER BY cc.id DESC LIMIT 1",
            [$contactId]
        );
    }

    /**
     * Cria o card na primeira coluna ("Novo Lead" ou a primeira do board).
     */
    private function createCard(int $boardId, array $contact): ?array
    {
        $column = $this->findColumnByName($boardId, self::STAGE_NEW)
            ?? $this->columnModel->getFirst($boardId);

        if (!$column) {
            return null;
        }

        $title = $contact['contact_name']
            ?? $contact['push_name']
            ?? $contact['phone']
            ?? 'Lead WhatsApp';

        $cardId = $this->db->insert('crm_cards', [
            'column_id' => (int) $column['id'],
            'contact_id' => (int) $contact['id'],
            'title' => $title,
            'phone' => $contact['phone'] ?? null,
            'lead_outcome' => 'open',
            'assigned_to' => $contact['assigned_to'] ?? null,
            'created_by' => null,
            'position' => 0,
        ]);

        $this->cardModel->addActivity(
            (int) $cardId,
            null,
            'create',
            'Lead criado automaticamente pela Aurora (WhatsApp).'
        );

        return $this->db->fetchOne("SELECT * FROM crm_cards WHERE id = ?", [(int) $cardId]);
    }

    /**
     * Move o card para a etapa alvo (se ainda não estiver nela e a nova for mais avançada).
     */
    private function moveToStage(int $boardId, int $cardId, string $stageName, bool $handoff): void
    {
        $target = $this->findColumnByName($boardId, $stageName);
        if (!$target) {
            return;
        }
        $targetId = (int) $target['id'];

        $card = $this->db->fetchOne("SELECT column_id FROM crm_cards WHERE id = ?", [$cardId]);
        if (!$card) {
            return;
        }
        $currentId = (int) $card['column_id'];

        // Não retroceder: só move se a etapa alvo tiver posição maior (mais avançada).
        $currentPos = (int) $this->db->fetchColumn(
            "SELECT position FROM crm_columns WHERE id = ?",
            [$currentId]
        );
        $targetPos = (int) ($target['position'] ?? 0);

        if ($currentId === $targetId || $targetPos <= $currentPos) {
            return;
        }

        $this->cardModel->moveToColumn($cardId, $targetId, 0);

        $desc = $handoff
            ? 'Aurora identificou intenção de compra e moveu o lead para "' . $stageName . '". Atendente humano acionado.'
            : 'Aurora avançou o lead para "' . $stageName . '".';

        $this->cardModel->addActivity($cardId, null, 'move', $desc);
    }

    /**
     * Encontra uma coluna do board pelo nome (case-insensitive, tolerante a acento/variação).
     */
    private function findColumnByName(int $boardId, string $name): ?array
    {
        // Match exato primeiro.
        $col = $this->db->fetchOne(
            "SELECT * FROM crm_columns WHERE board_id = ? AND LOWER(name) = LOWER(?) LIMIT 1",
            [$boardId, $name]
        );
        if ($col) {
            return $col;
        }

        // Match por prefixo/contém (ex.: "Em Negociação (novos)").
        return $this->db->fetchOne(
            "SELECT * FROM crm_columns WHERE board_id = ? AND LOWER(name) LIKE LOWER(?) ORDER BY position ASC LIMIT 1",
            [$boardId, '%' . $name . '%']
        );
    }
}
