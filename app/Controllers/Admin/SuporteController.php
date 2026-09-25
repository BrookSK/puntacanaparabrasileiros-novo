<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\SupportTicket;
use App\Services\LrvService;

/**
 * Área de Suporte do painel.
 *
 * Fluxo: o usuário cria uma demanda -> ela é gravada localmente (sempre) ->
 * em seguida é enviada automaticamente ao LRV (helpdeskON) via LrvService.
 * Se o envio ao LRV falhar, a demanda fica com sync_status='pending'/'failed'
 * para reenvio posterior (o external_ref garante idempotência no LRV).
 *
 * Visível a todos os papéis admin (o grupo /admin já aplica AuthMiddleware +
 * AdminMiddleware nas rotas).
 */
class SuporteController extends Controller
{
    private SupportTicket $ticketModel;

    public function __construct()
    {
        parent::__construct();
        $this->ticketModel = new SupportTicket();
    }

    /**
     * Lista as demandas de suporte criadas no painel.
     */
    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));

        $tickets = $this->ticketModel->paginate($page, 20, '1=1', [], 'created_at DESC');

        $lrv = new LrvService();

        $this->view('admin/suporte/index', [
            'tickets' => $tickets,
            'lrvConfigured' => $lrv->isConfigured(),
            'pageTitle' => 'Suporte',
        ], 'admin');
    }

    /**
     * Formulário de nova demanda.
     */
    public function create(Request $request, Response $response): void
    {
        $this->view('admin/suporte/form', [
            'priorities' => SupportTicket::PRIORITIES,
            'pageTitle' => 'Nova Demanda de Suporte',
        ], 'admin');
    }

    /**
     * Grava a demanda localmente e envia ao LRV.
     */
    public function store(Request $request, Response $response): void
    {
        $title = trim((string) $request->input('title', ''));
        $description = trim((string) $request->input('description', ''));
        $priority = (string) $request->input('priority', 'medium');
        $category = trim((string) $request->input('category', ''));

        // Validação (espelha as regras obrigatórias da API do LRV).
        $errors = [];
        if ($title === '' || mb_strlen($title) > 255) {
            $errors['title'] = 'O título é obrigatório (até 255 caracteres).';
        }
        if ($description === '') {
            $errors['description'] = 'A descrição é obrigatória.';
        }
        if (!in_array($priority, SupportTicket::PRIORITIES, true)) {
            $priority = 'medium';
        }

        if (!empty($errors)) {
            $this->flash('errors', $errors);
            $this->flash('old', compact('title', 'description', 'priority', 'category'));
            $this->redirect('/admin/suporte/criar');
            return;
        }

        $user = $this->currentUser();
        $requesterName = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: null;

        // 1) Grava localmente SEMPRE (nasce como pendente de sincronização).
        $id = $this->ticketModel->create([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'category' => $category !== '' ? $category : null,
            'requester_name' => $requesterName,
            'requester_company' => 'Punta Cana',
            'external_ref' => 'PENDING', // substituído logo abaixo pelo ref real
            'sync_status' => 'pending',
            'created_by' => isset($user['id']) ? (int) $user['id'] : null,
        ]);

        // external_ref depende do id gerado -> atualiza agora.
        $externalRef = SupportTicket::buildExternalRef($id);
        $this->ticketModel->update($id, ['external_ref' => $externalRef]);

        // 2) Envia ao LRV automaticamente.
        $lrv = new LrvService();
        $result = $lrv->createTicket([
            'title' => $title,
            'description' => $description,
            'priority' => $priority,
            'category' => $category !== '' ? $category : null,
            'requester_name' => $requesterName,
            'requester_company' => 'Punta Cana',
            'external_ref' => $externalRef,
        ]);

        if ($result['ok']) {
            $this->ticketModel->markSynced($id, $result['data']);
            $this->flash('success', 'Demanda criada e enviada ao LRV com sucesso.');
        } else {
            // Mantém a demanda local; fica pendente de reenvio.
            $this->ticketModel->markFailed($id, (string) ($result['error'] ?? 'Falha desconhecida.'));
            $this->flash('info', 'Demanda registrada localmente, mas o envio ao LRV falhou: '
                . ($result['error'] ?? 'erro desconhecido') . '. Ela ficará pendente de sincronização.');
        }

        $this->redirect('/admin/suporte');
    }

    /**
     * Reenvia ao LRV uma demanda que ficou pendente/falha.
     */
    public function resend(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $ticket = $this->ticketModel->find($id);

        if (!$ticket) {
            $this->flash('error', 'Demanda não encontrada.');
            $this->redirect('/admin/suporte');
            return;
        }

        if (($ticket['sync_status'] ?? '') === 'synced') {
            $this->flash('info', 'Esta demanda já está sincronizada com o LRV.');
            $this->redirect('/admin/suporte');
            return;
        }

        $lrv = new LrvService();
        $result = $lrv->createTicket([
            'title' => $ticket['title'],
            'description' => $ticket['description'],
            'priority' => $ticket['priority'],
            'category' => $ticket['category'] ?: null,
            'requester_name' => $ticket['requester_name'] ?: null,
            'requester_company' => $ticket['requester_company'] ?: null,
            'external_ref' => $ticket['external_ref'],
        ]);

        if ($result['ok']) {
            $this->ticketModel->markSynced($id, $result['data']);
            $this->flash('success', 'Demanda sincronizada com o LRV.');
        } else {
            $this->ticketModel->markFailed($id, (string) ($result['error'] ?? 'Falha desconhecida.'));
            $this->flash('error', 'Reenvio ao LRV falhou: ' . ($result['error'] ?? 'erro desconhecido'));
        }

        $this->redirect('/admin/suporte');
    }
}
