<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\InternalChatConversation;
use App\Models\InternalChatMessage;

/**
 * Chat interno entre atendentes/equipe.
 * Conversas diretas e grupos, com atualização por polling.
 */
class InternalChatController extends Controller
{
    private InternalChatConversation $conversationModel;
    private InternalChatMessage $messageModel;

    private const TEAM_ROLES = ['superadmin', 'admin', 'attendant', 'whatsapp_agent', 'comercial'];

    public function __construct()
    {
        parent::__construct();
        $this->conversationModel = new InternalChatConversation();
        $this->messageModel = new InternalChatMessage();
    }

    // ── Página principal ────────────────────────────────────────
    public function index(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $openId = $request->param('id') ? (int) $request->param('id') : null;

        $this->view('admin/internal-chat/index', [
            'currentUser' => $user,
            'teamMembers' => $this->teamMembers((int) $user['id']),
            'openConversationId' => $openId,
            'pageTitle' => 'Chat Interno',
        ], 'admin');
    }

    // ── Lista de conversas (JSON) ───────────────────────────────
    public function conversations(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $conversations = $this->conversationModel->listForUser((int) $user['id']);
        $this->json(['conversations' => $conversations]);
    }

    // ── Mensagens de uma conversa (JSON) ────────────────────────
    public function messages(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $conversationId = (int) $request->param('id');

        if (!$this->conversationModel->isParticipant($conversationId, (int) $user['id'])) {
            $this->json(['error' => 'Acesso negado.'], 403);
            return;
        }

        $messages = $this->messageModel->getByConversation($conversationId, 200);
        $this->conversationModel->markRead($conversationId, (int) $user['id']);

        $this->json([
            'messages' => $messages,
            'participants' => $this->conversationModel->participants($conversationId),
        ]);
    }

    // ── Polling de novas mensagens ──────────────────────────────
    public function poll(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $conversationId = (int) $request->param('id');
        $afterId = (int) $request->input('after_id', '0');

        if (!$this->conversationModel->isParticipant($conversationId, (int) $user['id'])) {
            $this->json(['messages' => []], 403);
            return;
        }

        $messages = $this->messageModel->getNewAfter($conversationId, $afterId);
        if (!empty($messages)) {
            $this->conversationModel->markRead($conversationId, (int) $user['id']);
        }
        $this->json(['messages' => $messages]);
    }

    // ── Enviar mensagem ─────────────────────────────────────────
    public function send(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $conversationId = (int) $request->input('conversation_id', '0');
        $body = trim((string) $request->input('message', ''));

        if ($conversationId <= 0 || $body === '') {
            $this->json(['success' => false, 'error' => 'Mensagem inválida.'], 400);
            return;
        }
        if (!$this->conversationModel->isParticipant($conversationId, (int) $user['id'])) {
            $this->json(['success' => false, 'error' => 'Acesso negado.'], 403);
            return;
        }

        $id = $this->messageModel->post($conversationId, (int) $user['id'], $body, 'text');
        $this->conversationModel->markRead($conversationId, (int) $user['id']);

        $this->json(['success' => true, 'id' => $id]);
    }

    // ── Criar conversa (direta ou grupo) ────────────────────────
    public function createConversation(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $type = $request->input('type', 'direct');

        if ($type === 'group') {
            $title = trim((string) $request->input('title', ''));
            $members = $request->input('members', []);
            if (!is_array($members)) $members = [];
            $members = array_map('intval', array_filter($members, fn($m) => (int) $m > 0));

            if ($title === '') {
                $this->json(['success' => false, 'error' => 'Informe um nome para o grupo.'], 400);
                return;
            }
            if (count($members) < 1) {
                $this->json(['success' => false, 'error' => 'Selecione ao menos um participante.'], 400);
                return;
            }

            // Validar que os membros são da equipe
            $members = $this->filterTeam($members);
            $relatedContactId = (int) $request->input('related_contact_id', '0') ?: null;

            $id = $this->conversationModel->createGroup($title, (int) $user['id'], $members, $relatedContactId);
            $this->messageModel->post($id, null, trim(($user['first_name'] ?? 'Alguém')) . ' criou o grupo.', 'system');
            $this->json(['success' => true, 'id' => $id]);
            return;
        }

        // Conversa direta
        $targetId = (int) $request->input('user_id', '0');
        if ($targetId <= 0 || $targetId === (int) $user['id'] || !$this->isTeam($targetId)) {
            $this->json(['success' => false, 'error' => 'Selecione um membro da equipe válido.'], 400);
            return;
        }

        $id = $this->conversationModel->createDirect((int) $user['id'], $targetId);
        $this->json(['success' => true, 'id' => $id]);
    }

    /**
     * Abre (ou cria) uma conversa interna de equipe vinculada a um cliente
     * (whatsapp_contact). Usado pelo botão "Discutir com a equipe" no chat WhatsApp.
     * Retorna JSON com a URL para redirecionar.
     */
    public function openForContact(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $contactId = (int) $request->input('contact_id', '0');
        if ($contactId <= 0) {
            $this->json(['success' => false, 'error' => 'Contato inválido.'], 400);
            return;
        }

        $contact = $this->db->fetchOne(
            "SELECT id, contact_name, push_name, phone FROM whatsapp_contacts WHERE id = ?",
            [$contactId]
        );
        if (!$contact) {
            $this->json(['success' => false, 'error' => 'Cliente não encontrado.'], 404);
            return;
        }

        // Se já existe uma conversa de equipe para este cliente, reutiliza
        $existing = $this->db->fetchOne(
            "SELECT id FROM internal_chat_conversations WHERE related_contact_id = ? ORDER BY id DESC LIMIT 1",
            [$contactId]
        );

        if ($existing) {
            $conversationId = (int) $existing['id'];
            // Garante que o usuário atual participa (intervir)
            $this->conversationModel->addParticipant($conversationId, (int) $user['id'], 'member');
        } else {
            $clientName = $contact['contact_name'] ?: ($contact['push_name'] ?: ($contact['phone'] ?: 'Cliente'));
            $conversationId = $this->conversationModel->createGroup(
                'Cliente: ' . $clientName,
                (int) $user['id'],
                [], // sem outros membros no início; a equipe entra conforme necessário
                $contactId
            );
            $this->messageModel->post(
                $conversationId,
                null,
                'Conversa criada para discutir sobre o cliente ' . $clientName . '.',
                'system'
            );
        }

        $this->json(['success' => true, 'redirect' => '/chat-interno/c/' . $conversationId]);
    }

    // ── Adicionar participante (intervir num grupo) ─────────────
    public function addParticipant(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $conversationId = (int) $request->input('conversation_id', '0');
        $newUserId = (int) $request->input('user_id', '0');

        if (!$this->conversationModel->isParticipant($conversationId, (int) $user['id'])) {
            $this->json(['success' => false, 'error' => 'Acesso negado.'], 403);
            return;
        }
        if ($newUserId <= 0 || !$this->isTeam($newUserId)) {
            $this->json(['success' => false, 'error' => 'Usuário inválido.'], 400);
            return;
        }

        $this->conversationModel->addParticipant($conversationId, $newUserId, 'member');

        $added = $this->db->fetchOne("SELECT first_name FROM users WHERE id = ?", [$newUserId]);
        $this->messageModel->post(
            $conversationId,
            null,
            trim(($user['first_name'] ?? 'Alguém')) . ' adicionou ' . ($added['first_name'] ?? 'um membro') . ' à conversa.',
            'system'
        );

        $this->json(['success' => true]);
    }

    // ── Contador global de não lidas (para o badge do menu) ─────
    public function unreadCount(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $this->json(['count' => $this->messageModel->totalUnreadForUser((int) $user['id'])]);
    }

    // ── Helpers ─────────────────────────────────────────────────
    private function teamMembers(int $excludeUserId): array
    {
        $roles = "'" . implode("','", self::TEAM_ROLES) . "'";
        return $this->db->fetchAll(
            "SELECT id, first_name, last_name, role FROM users
             WHERE role IN ({$roles}) AND status = 'active' AND id != ?
             ORDER BY first_name ASC",
            [$excludeUserId]
        );
    }

    private function isTeam(int $userId): bool
    {
        $roles = "'" . implode("','", self::TEAM_ROLES) . "'";
        return (bool) $this->db->fetchColumn(
            "SELECT id FROM users WHERE id = ? AND role IN ({$roles}) AND status = 'active' LIMIT 1",
            [$userId]
        );
    }

    /**
     * @param int[] $userIds
     * @return int[]
     */
    private function filterTeam(array $userIds): array
    {
        return array_values(array_filter($userIds, fn($id) => $this->isTeam((int) $id)));
    }
}
