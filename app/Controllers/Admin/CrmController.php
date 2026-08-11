<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\CrmBoard;
use App\Models\CrmColumn;
use App\Models\CrmCard;
use App\Middleware\WhatsAppMiddleware;

/**
 * Controller do módulo CRM.
 * Gerencia boards, colunas, cards, dashboard e comissões.
 */
class CrmController extends Controller
{
    private CrmBoard $boardModel;
    private CrmColumn $columnModel;
    private CrmCard $cardModel;

    public function __construct()
    {
        parent::__construct();
        $this->boardModel = new CrmBoard();
        $this->columnModel = new CrmColumn();
        $this->cardModel = new CrmCard();
    }

    // ═══════════════════════════════════════════════
    // BOARDS
    // ═══════════════════════════════════════════════

    /**
     * Lista de boards (tela principal do CRM).
     */
    public function index(Request $request, Response $response): void
    {
        $boards = $this->boardModel->listActive();

        $this->view('admin/crm/index', [
            'boards' => $boards,
            'pageTitle' => 'CRM',
        ], 'admin');
    }

    /**
     * Criar board.
     */
    public function createBoard(Request $request, Response $response): void
    {
        $name = $request->input('name', '');
        $description = $request->input('description', '');

        if (empty($name)) {
            $this->json(['success' => false, 'error' => 'Nome é obrigatório.'], 400);
            return;
        }

        $user = $this->currentUser();
        $boardId = $this->boardModel->createWithDefaults([
            'name' => $name,
            'description' => $description,
            'created_by' => (int) $user['id'],
        ]);

        $this->json(['success' => true, 'id' => $boardId]);
    }

    /**
     * Excluir board (super_admin only, soft-delete).
     */
    public function deleteBoard(Request $request, Response $response): void
    {
        if (!WhatsAppMiddleware::isSuperAdmin()) {
            $this->json(['success' => false, 'error' => 'Apenas super admin.'], 403);
            return;
        }

        $id = (int) $request->param('id');
        $this->boardModel->softDelete($id);
        $this->json(['success' => true]);
    }

    /**
     * API: Listar boards com colunas (para dropdowns).
     */
    public function listBoards(Request $request, Response $response): void
    {
        $boards = $this->boardModel->listWithColumns();
        $this->json(['boards' => $boards]);
    }

    // ═══════════════════════════════════════════════
    // KANBAN (Board)
    // ═══════════════════════════════════════════════

    /**
     * Tela do kanban de um board.
     */
    public function board(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $board = $this->boardModel->findWithColumns($id);

        if (!$board) {
            $this->flash('error', 'Board não encontrado.');
            $this->redirect('/crm');
            return;
        }

        // Processar follow-ups vencidos
        $this->cardModel->processFollowUps();

        $teamMembers = $this->db->fetchAll(
            "SELECT id, first_name, last_name FROM users 
             WHERE role IN ('superadmin','admin','attendant','whatsapp_agent','comercial') 
             AND status = 'active' ORDER BY first_name"
        );

        $labels = $this->db->fetchAll("SELECT * FROM whatsapp_labels ORDER BY name");

        $this->view('admin/crm/board', [
            'board' => $board,
            'teamMembers' => $teamMembers,
            'labels' => $labels,
            'pageTitle' => $board['name'],
        ], 'admin');
    }

    // ═══════════════════════════════════════════════
    // COLUNAS
    // ═══════════════════════════════════════════════

    /**
     * Criar coluna.
     */
    public function createColumn(Request $request, Response $response): void
    {
        $boardId = (int) $request->input('board_id', '0');
        $name = $request->input('name', '');
        $color = $request->input('color', '#6c757d');
        $labelId = $request->input('label_id') ? (int) $request->input('label_id') : null;
        $status = $request->input('status') ?: null;

        if (empty($name) || !$boardId) {
            $this->json(['success' => false, 'error' => 'Nome e board são obrigatórios.'], 400);
            return;
        }

        $position = $this->columnModel->nextPosition($boardId);
        $id = $this->columnModel->create([
            'board_id' => $boardId,
            'name' => $name,
            'color' => $color,
            'label_id' => $labelId,
            'status' => $status,
            'position' => $position,
        ]);

        $this->json(['success' => true, 'id' => $id]);
    }

    /**
     * Renomear/atualizar coluna.
     */
    public function updateColumn(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $data = [];

        $name = $request->input('name');
        $color = $request->input('color');
        if ($name !== null) $data['name'] = $name;
        if ($color !== null) $data['color'] = $color;

        if (!empty($data)) {
            $this->columnModel->update($id, $data);
        }

        $this->json(['success' => true]);
    }

    /**
     * Excluir coluna (super_admin only).
     */
    public function deleteColumn(Request $request, Response $response): void
    {
        if (!WhatsAppMiddleware::isSuperAdmin()) {
            $this->json(['success' => false, 'error' => 'Apenas super admin.'], 403);
            return;
        }

        $id = (int) $request->param('id');
        $this->columnModel->delete($id);
        $this->json(['success' => true]);
    }

    // ═══════════════════════════════════════════════
    // CARDS
    // ═══════════════════════════════════════════════

    /**
     * Criar card.
     */
    public function createCard(Request $request, Response $response): void
    {
        $columnId = (int) $request->input('column_id', '0');
        $title = $request->input('title', '');

        if (empty($title) || !$columnId) {
            $this->json(['success' => false, 'error' => 'Título e coluna são obrigatórios.'], 400);
            return;
        }

        $user = $this->currentUser();
        $cardId = $this->cardModel->create([
            'column_id' => $columnId,
            'title' => $title,
            'description' => $request->input('description', ''),
            'phone' => $request->input('phone', ''),
            'value' => $request->input('value') ? (float) str_replace(',', '.', preg_replace('/[^0-9.,]/', '', $request->input('value'))) : null,
            'label_id' => $request->input('label_id') ? (int) $request->input('label_id') : null,
            'status' => $request->input('status') ?: null,
            'assigned_to' => $request->input('assigned_to') ? (int) $request->input('assigned_to') : null,
            'lead_outcome' => 'open',
            'position' => 0,
            'created_by' => (int) $user['id'],
        ]);

        $this->cardModel->addActivity($cardId, (int) $user['id'], 'create', 'Card criado');

        $this->json(['success' => true, 'id' => $cardId]);
    }

    /**
     * Atualizar card.
     */
    public function updateCard(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $data = [];

        $fields = ['title', 'description', 'phone', 'status'];
        foreach ($fields as $field) {
            $val = $request->input($field);
            if ($val !== null) $data[$field] = $val;
        }

        if ($request->input('value') !== null) {
            $data['value'] = (float) str_replace(',', '.', preg_replace('/[^0-9.,]/', '', $request->input('value')));
        }
        if ($request->input('assigned_to') !== null) {
            $data['assigned_to'] = $request->input('assigned_to') ? (int) $request->input('assigned_to') : null;
        }
        if ($request->input('label_id') !== null) {
            $data['label_id'] = $request->input('label_id') ? (int) $request->input('label_id') : null;
        }

        if (!empty($data)) {
            $this->cardModel->update($id, $data);
        }

        // Sincronizar valor com briefing (se card tem contact_id)
        if (isset($data['value'])) {
            $card = $this->cardModel->find($id);
            if ($card && $card['contact_id']) {
                $this->db->query(
                    "UPDATE commercial_briefings SET investment_range = ? WHERE contact_id = ?",
                    ['R$ ' . number_format($data['value'], 2, ',', '.'), (int) $card['contact_id']]
                );
            }
        }

        $this->json(['success' => true]);
    }

    /**
     * Mover card (drag-and-drop).
     */
    public function moveCard(Request $request, Response $response): void
    {
        $cardId = (int) $request->input('card_id', '0');
        $columnId = (int) $request->input('column_id', '0');
        $position = (int) $request->input('position', '0');

        if (!$cardId || !$columnId) {
            $this->json(['success' => false, 'error' => 'Dados inválidos.'], 400);
            return;
        }

        $this->cardModel->moveToColumn($cardId, $columnId, $position);

        // Registrar atividade
        $columnName = $this->db->fetchColumn(
            "SELECT name FROM crm_columns WHERE id = ?", [$columnId]
        );
        $user = $this->currentUser();
        $this->cardModel->addActivity($cardId, (int) $user['id'], 'move', "Movido para {$columnName}");

        $this->json(['success' => true]);
    }

    /**
     * Excluir card.
     */
    public function deleteCard(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->cardModel->delete($id);
        $this->json(['success' => true]);
    }

    /**
     * Detalhes do card (AJAX).
     */
    public function cardDetail(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $card = $this->cardModel->findWithDetails($id);

        if (!$card) {
            $this->json(['success' => false, 'error' => 'Card não encontrado.'], 404);
            return;
        }

        // Briefing (se tem contact_id)
        $card['briefing'] = null;
        if ($card['contact_id']) {
            $card['briefing'] = $this->db->fetchOne(
                "SELECT * FROM commercial_briefings WHERE contact_id = ?",
                [(int) $card['contact_id']]
            );
        }

        $this->json(['success' => true, 'card' => $card]);
    }

    /**
     * Adicionar nota/atividade ao card.
     */
    public function addNote(Request $request, Response $response): void
    {
        $cardId = (int) $request->param('id');
        $description = $request->input('description', '');

        if (empty($description)) {
            $this->json(['success' => false, 'error' => 'Descrição é obrigatória.'], 400);
            return;
        }

        $user = $this->currentUser();
        $this->cardModel->addActivity($cardId, (int) $user['id'], 'note', $description);

        $this->json(['success' => true]);
    }

    /**
     * Converter lead.
     */
    public function convertLead(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $user = $this->currentUser();
        $this->cardModel->convertLead($id, (int) $user['id']);
        $this->json(['success' => true]);
    }

    /**
     * Marcar lead como perdido.
     */
    public function lostLead(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $user = $this->currentUser();
        $this->cardModel->markAsLost($id, (int) $user['id']);
        $this->json(['success' => true]);
    }

    /**
     * Agendar retomada de contato (follow-up).
     */
    public function setFollowUp(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $amount = (int) $request->input('amount', '1');
        $unit = $request->input('unit', 'days'); // minutes, hours, days
        $columnId = $request->input('column_id') ? (int) $request->input('column_id') : null;

        // Calcular data futura
        $modifier = match ($unit) {
            'minutes' => "+{$amount} minutes",
            'hours' => "+{$amount} hours",
            default => "+{$amount} days",
        };
        $followUpAt = date('Y-m-d H:i:s', strtotime($modifier));

        $user = $this->currentUser();
        $this->cardModel->setFollowUp($id, $followUpAt, $columnId, (int) $user['id']);

        $this->json(['success' => true, 'follow_up_at' => $followUpAt]);
    }

    /**
     * Processar retomadas vencidas manualmente.
     */
    public function runFollowUps(Request $request, Response $response): void
    {
        $count = $this->cardModel->processFollowUps();
        $this->json(['success' => true, 'processed' => $count]);
    }

    // ═══════════════════════════════════════════════
    // DASHBOARD
    // ═══════════════════════════════════════════════

    /**
     * Dashboard CRM com stats e gráficos.
     */
    public function dashboard(Request $request, Response $response): void
    {
        $stats = $this->cardModel->getDashboardStats();
        $evolution = $this->cardModel->getMonthlyEvolution();

        $this->view('admin/crm/dashboard', [
            'stats' => $stats,
            'evolution' => $evolution,
            'pageTitle' => 'CRM — Dashboard',
        ], 'admin');
    }

    // ═══════════════════════════════════════════════
    // COMISSÕES
    // ═══════════════════════════════════════════════

    /**
     * Tela de comissões.
     */
    public function commissions(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $month = $request->input('month', date('Y-m'));

        // Se comercial, só vê as próprias
        $filterUserId = null;
        if (WhatsAppMiddleware::isComercial()) {
            $filterUserId = (int) $user['id'];
        } elseif ($request->input('user_id')) {
            $filterUserId = (int) $request->input('user_id');
        }

        $commissions = $this->cardModel->getCommissions($filterUserId, $month);

        // Totalizadores
        $totalCommission = array_sum(array_column($commissions, 'commission_value'));
        $totalConverted = array_sum(array_column($commissions, 'total_value'));
        $commercialCount = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM users WHERE role = 'comercial' AND status = 'active'"
        );

        // Lista de comerciais para filtro (super_admin only)
        $commercials = [];
        if (!WhatsAppMiddleware::isComercial()) {
            $commercials = $this->db->fetchAll(
                "SELECT id, first_name, last_name, commission_percent FROM users 
                 WHERE role = 'comercial' AND status = 'active' ORDER BY first_name"
            );
        }

        $this->view('admin/crm/commissions', [
            'commissions' => $commissions,
            'month' => $month,
            'filterUserId' => $filterUserId,
            'totalCommission' => $totalCommission,
            'totalConverted' => $totalConverted,
            'commercialCount' => (int) $commercialCount,
            'commercials' => $commercials,
            'isSuperAdmin' => WhatsAppMiddleware::isSuperAdmin(),
            'pageTitle' => 'CRM — Comissões',
        ], 'admin');
    }

    /**
     * API: Leads convertidos de um comercial (expandir linha).
     */
    public function commissionLeads(Request $request, Response $response): void
    {
        $userId = (int) $request->param('id');
        $month = $request->input('month', date('Y-m'));

        $user = $this->currentUser();
        // Comercial só vê os próprios
        if (WhatsAppMiddleware::isComercial() && $userId !== (int) $user['id']) {
            $this->json(['success' => false, 'error' => 'Acesso negado.'], 403);
            return;
        }

        $leads = $this->db->fetchAll(
            "SELECT cc.title, cc.phone, cc.value, cc.outcome_at,
                    wc.contact_name
             FROM crm_cards cc
             LEFT JOIN whatsapp_contacts wc ON wc.id = cc.contact_id
             WHERE cc.converted_by = ? 
             AND cc.lead_outcome = 'converted'
             AND DATE_FORMAT(cc.outcome_at, '%Y-%m') = ?
             ORDER BY cc.outcome_at DESC",
            [$userId, $month]
        );

        $this->json(['success' => true, 'leads' => $leads]);
    }
}
