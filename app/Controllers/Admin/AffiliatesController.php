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
        // Bloqueados = afiliados não-ativos + solicitações que não são pending/approved
        $blockedAffiliates = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM affiliates WHERE status != 'active'");
        $blockedRequests = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM affiliate_requests WHERE status NOT IN ('pending', 'approved')");
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
     * Retorna lista combinada de bloqueados:
     * - Afiliados que não estão ativos (inativos/bloqueados)
     * - Solicitações que não são pending nem approved (rejeitadas/bloqueadas/qualquer outro status)
     */
    private function getBlockedItems(int $page = 1, int $perPage = 20): array
    {
        // Buscar afiliados que não estão ativos (bloqueados/inativos)
        $blockedAffiliates = $this->db->fetchAll(
            "SELECT a.id, u.first_name, u.last_name, u.email, 'affiliate' AS source, a.status, a.notes AS admin_notes, a.created_at, a.updated_at
             FROM affiliates a
             LEFT JOIN users u ON a.user_id = u.id
             WHERE a.status != 'active'
             ORDER BY a.updated_at DESC"
        );

        // Extrair block_reason do campo notes JSON para afiliados
        foreach ($blockedAffiliates as &$aff) {
            $notes = json_decode($aff['admin_notes'] ?? '', true);
            $aff['block_reason'] = $notes['block_reason'] ?? '';
            unset($aff['admin_notes']);
        }
        unset($aff);

        // Buscar solicitações que NÃO são pending nem approved
        $blockedRequests = $this->db->fetchAll(
            "SELECT id, first_name, last_name, email, 'request' AS source, status, admin_notes,
                    created_at, COALESCE(rejected_at, updated_at, created_at) AS updated_at
             FROM affiliate_requests
             WHERE status NOT IN ('pending', 'approved')
             ORDER BY COALESCE(rejected_at, updated_at, created_at) DESC"
        );

        // Para solicitações, o motivo está em admin_notes
        foreach ($blockedRequests as &$req) {
            $req['block_reason'] = $req['admin_notes'] ?? '';
            unset($req['admin_notes']);
        }
        unset($req);

        // Combinar e ordenar por data mais recente primeiro
        $allBlocked = array_merge($blockedAffiliates, $blockedRequests);
        usort($allBlocked, function ($a, $b) {
            $dateA = $a['updated_at'] ?? $a['created_at'] ?? '1970-01-01';
            $dateB = $b['updated_at'] ?? $b['created_at'] ?? '1970-01-01';
            return strtotime($dateB) - strtotime($dateA);
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
                    'bank_name' => $req['bank_name'] ?? null,
                    'bank_agency' => $req['bank_agency'] ?? null,
                    'bank_account' => $req['bank_account'] ?? null,
                    'bank_account_type' => $req['bank_account_type'] ?? null,
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
     * Recusar/bloquear solicitação (com motivo obrigatório).
     */
    public function rejectRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $req = $this->requestModel->find($id);

        if (!$req) {
            $this->flash('error', 'Solicitação não encontrada.');
            $this->redirect('/admin/afiliados');
            return;
        }

        if ($req['status'] === 'approved') {
            $this->flash('error', 'Esta solicitação já foi aprovada e não pode ser bloqueada.');
            $this->redirect('/admin/afiliados');
            return;
        }

        $reason = trim($request->input('block_reason', ''));

        if (empty($reason)) {
            $this->flash('error', 'O motivo do bloqueio é obrigatório.');
            $this->redirect('/admin/afiliados?tab=solicitacoes');
            return;
        }

        // Usar query SQL direta para garantir que o status seja atualizado
        $this->db->query(
            "UPDATE affiliate_requests SET status = 'rejected', rejected_at = ?, admin_notes = ? WHERE id = ?",
            [date('Y-m-d H:i:s'), $reason, $id]
        );

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
                    'adminNotes' => $reason,
                    'siteUrl' => $this->setting('site_url', 'https://puntacananovo.lrvweb.com.br'),
                ]
            );
        } catch (\Exception $e) {
            // Silenciar erro de email
        }

        $this->flash('success', 'Solicitação de ' . $req['first_name'] . ' ' . $req['last_name'] . ' foi bloqueada.');
        $this->redirect('/admin/afiliados?tab=bloqueados');
    }

    /**
     * Bloquear afiliado ativo (com motivo obrigatório).
     */
    public function suspend(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $reason = trim($request->input('block_reason', ''));

        if (empty($reason)) {
            $this->flash('error', 'O motivo do bloqueio é obrigatório.');
            $this->redirect('/admin/afiliados?tab=ativos');
            return;
        }

        // Salvar o motivo no campo notes (preservando dados existentes)
        $affiliate = $this->affiliateModel->find($id);
        $notes = json_decode($affiliate['notes'] ?? '{}', true) ?: [];
        $notes['block_reason'] = $reason;
        $notes['blocked_at'] = date('Y-m-d H:i:s');
        $notes['previous_status'] = $affiliate['status'] ?? 'active';

        $this->db->update('affiliates', [
            'status' => 'inactive',
            'notes' => json_encode($notes, JSON_UNESCAPED_UNICODE),
        ], 'id = ?', [$id]);

        $this->flash('success', 'Afiliado bloqueado.');
        $this->redirect('/admin/afiliados?tab=bloqueados');
    }

    /**
     * Excluir solicitação/afiliado bloqueado permanentemente.
     */
    public function deleteRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $source = $request->input('source', 'request');

        if ($source === 'affiliate') {
            // Excluir afiliado bloqueado
            $affiliate = $this->affiliateModel->find($id);
            if (!$affiliate || $affiliate['status'] === 'active') {
                $this->flash('error', 'Afiliado não encontrado ou está ativo.');
                $this->redirect('/admin/afiliados?tab=bloqueados');
                return;
            }
            // Excluir afiliado, user associado e solicitação original
            $userId = (int) $affiliate['user_id'];
            $this->db->delete('affiliates', 'id = ?', [$id]);
            // Buscar email do user para limpar affiliate_requests também
            $user = $this->db->fetchOne("SELECT email FROM users WHERE id = ?", [$userId]);
            if ($user) {
                $this->db->delete('affiliate_requests', 'email = ?', [$user['email']]);
                $this->db->delete('users', 'id = ?', [$userId]);
            }
            $this->flash('success', 'Afiliado excluído permanentemente.');
        } else {
            // Excluir solicitação bloqueada
            $req = $this->requestModel->find($id);
            if (!$req) {
                $this->flash('error', 'Solicitação não encontrada.');
                $this->redirect('/admin/afiliados?tab=bloqueados');
                return;
            }
            if (in_array($req['status'], ['pending', 'approved'])) {
                $this->flash('error', 'Apenas solicitações bloqueadas podem ser excluídas.');
                $this->redirect('/admin/afiliados?tab=bloqueados');
                return;
            }
            // Limpar também user e affiliate associados ao email (caso tenha sido aprovado antes)
            $email = $req['email'];
            $existingUser = $this->db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existingUser) {
                $this->db->delete('affiliates', 'user_id = ?', [(int)$existingUser['id']]);
                $this->db->delete('users', 'id = ?', [(int)$existingUser['id']]);
            }
            $this->db->delete('affiliate_requests', 'id = ?', [$id]);
            $this->flash('success', 'Solicitação excluída permanentemente.');
        }

        $this->redirect('/admin/afiliados?tab=bloqueados');
    }

    /**
     * Reativar afiliado/solicitação bloqueada (retorna ao estado anterior).
     */
    public function reactivate(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $source = $request->input('source', 'affiliate');

        if ($source === 'request') {
            // Reativar solicitação: voltar para pending
            $req = $this->requestModel->find($id);
            if (!$req) {
                $this->flash('error', 'Solicitação não encontrada.');
                $this->redirect('/admin/afiliados?tab=bloqueados');
                return;
            }
            $this->db->query(
                "UPDATE affiliate_requests SET status = 'pending', rejected_at = NULL, admin_notes = NULL WHERE id = ?",
                [$id]
            );
            $this->flash('success', 'Solicitação de ' . $req['first_name'] . ' ' . $req['last_name'] . ' foi reativada e voltou para Solicitações.');
            $this->redirect('/admin/afiliados?tab=solicitacoes');
        } else {
            // Reativar afiliado: voltar para active
            $affiliate = $this->affiliateModel->find($id);
            if (!$affiliate) {
                $this->flash('error', 'Afiliado não encontrado.');
                $this->redirect('/admin/afiliados?tab=bloqueados');
                return;
            }
            // Limpar motivo do bloqueio
            $notes = json_decode($affiliate['notes'] ?? '{}', true) ?: [];
            unset($notes['block_reason'], $notes['blocked_at'], $notes['previous_status']);

            $this->db->update('affiliates', [
                'status' => 'active',
                'notes' => json_encode($notes, JSON_UNESCAPED_UNICODE),
            ], 'id = ?', [$id]);

            $this->flash('success', 'Afiliado reativado com sucesso.');
            $this->redirect('/admin/afiliados?tab=ativos');
        }
    }

    /**
     * Gerenciar comissões.
     */
    public function commissions(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $status = $request->query('status', 'all');
        $perPage = 20;

        // Construir query com JOIN para pegar nome do afiliado
        $whereClause = '1=1';
        $params = [];
        if ($status !== 'all') {
            $whereClause = 'c.status = ?';
            $params = [$status];
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM commissions c WHERE {$whereClause}",
            $params
        );
        $totalPages = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;

        $commissions = $this->db->fetchAll(
            "SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as affiliate_name, u.email as affiliate_email, a.notes as affiliate_notes, a.payment_email as affiliate_payment_email
             FROM commissions c
             LEFT JOIN affiliates a ON c.affiliate_id = a.id
             LEFT JOIN users u ON a.user_id = u.id
             WHERE {$whereClause}
             ORDER BY c.created_at DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        $this->view('admin/affiliates/commissions', [
            'commissions' => $commissions,
            'total' => $total,
            'totalPages' => $totalPages,
            'currentPage' => $page,
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

    /**
     * Cancelar comissão.
     */
    public function cancelCommission(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $reason = trim($request->input('reason', ''));

        if (empty($reason)) {
            $this->flash('error', 'Informe o motivo do cancelamento.');
            $this->redirect('/admin/afiliados/comissoes');
            return;
        }

        $this->commissionModel->cancel($id, $reason);

        $this->flash('success', 'Comissão cancelada com sucesso.');
        $this->redirect('/admin/afiliados/comissoes?status=rejected');
    }

    // ============================================================
    // Criativos para Afiliados
    // ============================================================

    public function creatives(Request $request, Response $response): void
    {
        $creativeModel = new \App\Models\AffiliateCreative();
        $creatives = $creativeModel->getAll();

        $this->view('admin/affiliates/creatives', [
            'creatives' => $creatives,
            'pageTitle' => 'Criativos para Afiliados',
        ], 'admin');
    }

    public function storeCreative(Request $request, Response $response): void
    {
        $creativeModel = new \App\Models\AffiliateCreative();

        $data = $request->only(['title', 'description', 'type', 'dimensions']);
        $data['status'] = 'active';

        // Auto sort_order
        $lastOrder = $this->db->fetchOne("SELECT MAX(sort_order) as max_order FROM affiliate_creatives");
        $data['sort_order'] = ($lastOrder ? (int) $lastOrder['max_order'] : 0) + 1;

        // Upload do arquivo
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $allowedTypes = [
                'image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml',
                'video/mp4', 'video/webm', 'video/quicktime',
                'application/pdf',
                'application/zip', 'application/x-zip-compressed',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/msword',
                'application/postscript',
            ];
            if (!in_array($file['type'], $allowedTypes)) {
                $this->flash('error', 'Tipo de arquivo não permitido.');
                $this->redirect('/admin/afiliados/criativos');
                return;
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'creative-' . uniqid() . '.' . $ext;
            $destination = BASE_PATH . '/public/uploads/creatives/' . $filename;

            // Criar diretório se não existir
            $dir = dirname($destination);
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            move_uploaded_file($file['tmp_name'], $destination);
            $data['image_url'] = '/uploads/creatives/' . $filename;

            // Detectar dimensões se for imagem
            if (str_starts_with($file['type'], 'image/')) {
                $imageInfo = @getimagesize($destination);
                if ($imageInfo) {
                    $data['dimensions'] = $imageInfo[0] . 'x' . $imageInfo[1];
                }
            }
        } else {
            $this->flash('error', 'É necessário enviar uma imagem.');
            $this->redirect('/admin/afiliados/criativos');
            return;
        }

        $creativeModel->create($data);
        $this->flash('success', 'Criativo adicionado!');
        $this->redirect('/admin/afiliados/criativos');
    }

    public function deleteCreative(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $creativeModel = new \App\Models\AffiliateCreative();
        $creative = $creativeModel->find($id);

        if ($creative) {
            // Remover arquivo físico
            $filePath = BASE_PATH . '/public' . $creative['image_url'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $creativeModel->delete($id);
        }

        $this->flash('success', 'Criativo excluído!');
        $this->redirect('/admin/afiliados/criativos');
    }
}
