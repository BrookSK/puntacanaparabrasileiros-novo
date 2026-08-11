<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Affiliate;
use App\Models\AffiliateRequest;
use App\Models\Commission;
use App\Models\User;

class AffiliatesController extends Controller
{
    private Affiliate $affiliateModel;
    private AffiliateRequest $requestModel;
    private Commission $commissionModel;

    public function __construct()
    {
        parent::__construct();
        $this->affiliateModel = new Affiliate();
        $this->requestModel = new AffiliateRequest();
        $this->commissionModel = new Commission();
    }

    /**
     * Página principal com abas: Solicitações | Ativos | Bloqueados
     */
    public function index(Request $request, Response $response): void
    {
        $tab = $request->query('tab', 'solicitacoes');
        $page = max(1, (int) $request->query('page', '1'));

        // Contadores baseados no status real do banco
        $pendingCount = $this->requestModel->countByStatus('pending');
        $activeCount = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM affiliates WHERE status = 'active'");
        // Bloqueados = afiliados inativos + solicitações rejeitadas
        $blockedAffiliates = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM affiliates WHERE status = 'inactive'");
        $blockedRequests = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM affiliate_requests WHERE status = 'rejected'");
        $blockedCount = $blockedAffiliates + $blockedRequests;

        $data = [
            'tab' => $tab,
            'pendingCount' => $pendingCount,
            'activeCount' => $activeCount,
            'blockedCount' => $blockedCount,
            'pageTitle' => 'Gerenciar Afiliados',
        ];

        if ($tab === 'solicitacoes') {
            $data['requests'] = $this->requestModel->getPending($page);
        } elseif ($tab === 'ativos') {
            $data['affiliates'] = $this->affiliateModel->getWithUserData($page, 20, 'active');
        } elseif ($tab === 'bloqueados') {
            $data['blocked'] = $this->getBlockedItems($page);
        }

        $this->view('admin/affiliates/index', $data, 'admin');
    }

    /**
     * Retorna lista combinada de bloqueados: afiliados inativos + solicitações rejeitadas.
     */
    private function getBlockedItems(int $page = 1, int $perPage = 20): array
    {
        // Buscar afiliados inativos (já foram aprovados e depois bloqueados)
        $blockedAffiliates = $this->db->fetchAll(
            "SELECT a.id, u.first_name, u.last_name, u.email, 'affiliate' AS source, a.created_at, a.updated_at
             FROM affiliates a
             LEFT JOIN users u ON a.user_id = u.id
             WHERE a.status = 'inactive'
             ORDER BY a.updated_at DESC"
        );

        // Buscar solicitações rejeitadas (nunca foram aprovadas)
        $blockedRequests = $this->db->fetchAll(
            "SELECT id, first_name, last_name, email, 'request' AS source, created_at, rejected_at AS updated_at
             FROM affiliate_requests
             WHERE status = 'rejected'
             ORDER BY rejected_at DESC"
        );

        // Combinar e ordenar por data de bloqueio (mais recente primeiro)
        $allBlocked = array_merge($blockedAffiliates, $blockedRequests);
        usort($allBlocked, function ($a, $b) {
            return strtotime($b['updated_at'] ?? $b['created_at']) - strtotime($a['updated_at'] ?? $a['created_at']);
        });

        // Paginação manual
        $total = count($allBlocked);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($allBlocked, $offset, $perPage);

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    /**
     * Detalhes de uma solicitação de afiliação.
     */
    public function showRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $req = $this->requestModel->find($id);

        if (!$req) {
            $this->abort(404);
        }

        $this->view('admin/affiliates/request-detail', [
            'request' => $req,
            'pageTitle' => 'Solicitação: ' . $req['first_name'] . ' ' . $req['last_name'],
        ], 'admin');
    }

    /**
     * Aprovar solicitação: cria user + afiliado.
     */
    public function approveRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $req = $this->requestModel->find($id);

        if (!$req || $req['status'] !== 'pending') {
            $this->flash('error', 'Solicitação não encontrada ou já processada.');
            $this->redirect('/admin/afiliados');
            return;
        }

        $adminNotes = $request->input('admin_notes', '');

        try {
            // 1. Criar usuário
            $userModel = new User();

            // Verificar se email já existe como user
            $existingUser = $userModel->findByEmail($req['email']);
            if ($existingUser) {
                $userId = (int) $existingUser['id'];
                // Atualizar role para affiliate
                $this->db->update('users', ['role' => 'affiliate'], 'id = ?', [$userId]);
            } else {
                $userId = $this->db->insert('users', [
                    'first_name' => $req['first_name'],
                    'last_name' => $req['last_name'],
                    'email' => $req['email'],
                    'password' => $req['password_hash'],
                    'phone' => $req['phone'],
                    'role' => 'affiliate',
                    'status' => 'active',
                    'email_verified_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // 2. Criar registro de afiliado
            $affiliateId = $this->affiliateModel->create([
                'user_id' => $userId,
                'status' => 'active',
                'commission_rate' => 20.00,
                'cookie_days' => 30,
                'payment_email' => $req['payment_email'] ?: $req['email'],
                'payment_method' => 'pix',
                'notes' => json_encode([
                    'username' => $req['username'],
                    'pix' => $req['pix'],
                    'website' => $req['website'],
                    'followers_count' => $req['followers_count'],
                    'niche' => $req['niche'],
                    'content_type' => $req['content_type'],
                    'promotion_strategy' => $req['promotion_strategy'],
                    'social_links' => $req['social_links'],
                ]),
            ]);

            // 3. Marcar solicitação como aprovada
            $this->requestModel->approve($id, $adminNotes);

            // 4. Enviar email de aprovação ao afiliado
            try {
                $emailService = new \App\Services\EmailService();
                $emailService->sendTemplate(
                    $req['email'],
                    $req['first_name'] . ' ' . $req['last_name'],
                    'Parabéns! Sua afiliação foi aprovada - Punta Cana para Brasileiros',
                    'affiliate-approved',
                    [
                        'firstName' => $req['first_name'],
                        'email' => $req['email'],
                        'siteUrl' => $this->setting('site_url', 'https://puntacananovo.lrvweb.com.br'),
                    ]
                );
            } catch (\Exception $e) {
                // Silenciar erro de email - não impedir o fluxo
            }

            $this->flash('success', 'Afiliado aprovado com sucesso! ' . $req['first_name'] . ' ' . $req['last_name'] . ' agora tem acesso ao painel.');
        } catch (\Exception $e) {
            $this->flash('error', 'Erro ao aprovar: ' . $e->getMessage());
        }

        $this->redirect('/admin/afiliados');
    }

    /**
     * Recusar solicitação.
     */
    public function rejectRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $req = $this->requestModel->find($id);

        if (!$req || $req['status'] !== 'pending') {
            $this->flash('error', 'Solicitação não encontrada ou já processada.');
            $this->redirect('/admin/afiliados');
            return;
        }

        $adminNotes = $request->input('admin_notes', '');
        $this->requestModel->reject($id, $adminNotes);

        // Enviar email de recusa ao solicitante
        try {
            $emailService = new \App\Services\EmailService();
            $emailService->sendTemplate(
                $req['email'],
                $req['first_name'] . ' ' . $req['last_name'],
                'Atualização sobre sua solicitação de afiliação - Punta Cana para Brasileiros',
                'affiliate-rejected',
                [
                    'firstName' => $req['first_name'],
                    'adminNotes' => $adminNotes,
                    'siteUrl' => $this->setting('site_url', 'https://puntacananovo.lrvweb.com.br'),
                ]
            );
        } catch (\Exception $e) {
            // Silenciar erro de email - não impedir o fluxo
        }

        $this->flash('success', 'Solicitação de ' . $req['first_name'] . ' ' . $req['last_name'] . ' foi recusada.');
        $this->redirect('/admin/afiliados?tab=bloqueados');
    }

    /**
     * Bloquear/suspender afiliado ativo.
     */
    public function suspend(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->db->update('affiliates', ['status' => 'inactive'], 'id = ?', [$id]);
        $this->flash('success', 'Afiliado bloqueado.');
        $this->redirect('/admin/afiliados?tab=bloqueados');
    }

    /**
     * Excluir solicitação rejeitada permanentemente.
     */
    public function deleteRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $req = $this->requestModel->find($id);

        if (!$req || $req['status'] !== 'rejected') {
            $this->flash('error', 'Solicitação não encontrada ou não está rejeitada.');
            $this->redirect('/admin/afiliados?tab=bloqueados');
            return;
        }

        $this->db->delete('affiliate_requests', 'id = ?', [$id]);
        $this->flash('success', 'Solicitação de ' . $req['first_name'] . ' ' . $req['last_name'] . ' foi excluída permanentemente.');
        $this->redirect('/admin/afiliados?tab=bloqueados');
    }

    /**
     * Reativar afiliado bloqueado.
     */
    public function reactivate(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->db->update('affiliates', ['status' => 'active'], 'id = ?', [$id]);
        $this->flash('success', 'Afiliado reativado.');
        $this->redirect('/admin/afiliados?tab=ativos');
    }

    /**
     * Gerenciar comissões.
     */
    public function commissions(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $status = $request->query('status', 'pending');
        $commissions = $this->commissionModel->paginate($page, 20, 'status = ?', [$status], 'created_at DESC');

        $this->view('admin/affiliates/commissions', [
            'commissions' => $commissions,
            'currentStatus' => $status,
            'pageTitle' => 'Comissões de Afiliados',
        ], 'admin');
    }

    /**
     * Pagar comissão.
     */
    public function payCommission(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $reference = $request->input('payout_reference', '');

        $this->commissionModel->markPaid($id, $reference ?: null);

        $commission = $this->commissionModel->find($id);
        if ($commission) {
            $this->db->query(
                "UPDATE affiliates SET total_paid = total_paid + ? WHERE id = ?",
                [(float) $commission['amount'], (int) $commission['affiliate_id']]
            );
        }

        $this->flash('success', 'Comissão marcada como paga!');
        $this->redirect('/admin/afiliados/comissoes');
    }
}
