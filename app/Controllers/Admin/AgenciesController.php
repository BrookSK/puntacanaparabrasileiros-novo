<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Agency;
use App\Models\AgencyCommission;
use App\Services\AgencyService;

class AgenciesController extends Controller
{
    private Agency $agencyModel;
    private AgencyCommission $commissionModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
        $this->agencyModel = new Agency();
        $this->commissionModel = new AgencyCommission();
    }

    // ── Listagem ────────────────────────────────────────────────
    public function index(Request $request, Response $response): void
    {
        $tab = $request->query('tab', 'solicitacoes');
        $page = max(1, (int) $request->query('page', '1'));

        $requestModel = new \App\Models\AgencyRequest();

        // Contadores
        $pendingCount = $this->tableExists('agency_requests') ? $requestModel->countByStatus('pending') : 0;
        $activeCount = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM agencies WHERE status = 'active'");
        // Bloqueadas = agências inativas (mesmo filtro usado na listagem da aba)
        $blockedCount = (int) $this->db->fetchColumn("SELECT COUNT(*) FROM agencies WHERE status = 'inactive'");

        $data = [
            'tab' => $tab,
            'pendingCount' => $pendingCount,
            'activeCount' => $activeCount,
            'blockedCount' => $blockedCount,
            'pageTitle' => 'Agências Parceiras',
        ];

        if ($tab === 'solicitacoes') {
            $data['requests'] = $this->tableExists('agency_requests')
                ? $requestModel->getPending($page)
                : ['items' => [], 'total' => 0, 'per_page' => 20, 'current_page' => 1, 'total_pages' => 0];
        } elseif ($tab === 'ativas') {
            $data['agencies'] = $this->agencyModel->getAllPaginated($page, 20, '', 'active');
        } elseif ($tab === 'bloqueadas') {
            $data['blocked'] = $this->agencyModel->getAllPaginated($page, 20, '', 'inactive');
        }

        $this->view('admin/agencies/index', $data, 'admin');
    }

    /**
     * Verifica se uma tabela existe (para ambientes sem a migração ainda aplicada).
     */
    private function tableExists(string $table): bool
    {
        try {
            $this->db->fetchColumn("SELECT 1 FROM `{$table}` LIMIT 1");
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    // ── Solicitação: detalhe ────────────────────────────────────
    public function showRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $req = (new \App\Models\AgencyRequest())->find($id);
        if (!$req) $this->abort(404);

        $this->view('admin/agencies/request-detail', [
            'request' => $req,
            'pageTitle' => 'Solicitação: ' . $req['company_name'],
        ], 'admin');
    }

    // ── Solicitação: aprovar (cria login + agência) ─────────────
    public function approveRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $requestModel = new \App\Models\AgencyRequest();
        $req = $requestModel->find($id);

        if (!$req || $req['status'] !== 'pending') {
            $this->flash('error', 'Solicitação não encontrada ou já processada.');
            $this->redirect('/admin/agencias');
            return;
        }

        $this->db->beginTransaction();
        try {
            $userModel = new \App\Models\User();

            // 1) Criar/atualizar usuário com role 'agency'
            $existingUser = $userModel->findByEmail($req['email']);
            if ($existingUser) {
                $userId = (int) $existingUser['id'];
                $this->db->update('users', ['role' => 'agency', 'status' => 'active'], 'id = ?', [$userId]);
            } else {
                // Divide o nome do contato em first/last
                $parts = explode(' ', trim((string) $req['contact_name']), 2);
                $userId = $this->db->insert('users', [
                    'first_name' => $parts[0] ?: $req['company_name'],
                    'last_name' => $parts[1] ?? '',
                    'email' => $req['email'],
                    'password' => $req['password_hash'],
                    'phone' => $req['phone'],
                    'role' => 'agency',
                    'status' => 'active',
                    'email_verified_at' => date('Y-m-d H:i:s'),
                ]);
            }

            // 2) Criar registro da agência
            $refCode = $this->agencyModel->generateRefCode();
            $agencyId = $this->agencyModel->create([
                'user_id' => $userId,
                'company_name' => $req['company_name'],
                'trade_name' => $req['trade_name'],
                'cnpj' => $req['cnpj'],
                'contact_name' => $req['contact_name'],
                'email' => $req['email'],
                'phone' => $req['phone'],
                'city' => $req['city'],
                'country' => $req['country'],
                'ref_code' => $refCode,
                'commission_rate' => 10.00,
                'status' => 'active',
            ]);

            // 3) Marcar solicitação como aprovada
            $requestModel->approve($id, $request->input('admin_notes', ''));

            $this->db->commit();

            // 4) Notificar a agência (e-mail + WhatsApp)
            $siteUrl = $this->setting('site_url', 'https://puntacananovo.lrvweb.com.br');
            try {
                (new \App\Services\EmailService())->sendTemplate(
                    $req['email'],
                    $req['contact_name'],
                    'Sua agência foi aprovada! - Punta Cana para Brasileiros',
                    'agency-approved',
                    [
                        'contactName' => $req['contact_name'],
                        'companyName' => $req['company_name'],
                        'email' => $req['email'],
                        'siteUrl' => $siteUrl,
                    ]
                );
            } catch (\Throwable $e) {}
            try {
                (new \App\Services\AgencyNotifier())->notifyApproved($req['phone'], $req['company_name'], $req['contact_name']);
            } catch (\Throwable $e) {}

            $this->flash('success', 'Agência "' . $req['company_name'] . '" aprovada! O acesso ao painel foi liberado.');
        } catch (\Throwable $e) {
            $this->db->rollback();
            $this->flash('error', 'Erro ao aprovar: ' . $e->getMessage());
        }

        $this->redirect('/admin/agencias');
    }

    // ── Solicitação: recusar (com motivo) ───────────────────────
    public function rejectRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $requestModel = new \App\Models\AgencyRequest();
        $req = $requestModel->find($id);

        if (!$req) {
            $this->flash('error', 'Solicitação não encontrada.');
            $this->redirect('/admin/agencias');
            return;
        }
        if ($req['status'] === 'approved') {
            $this->flash('error', 'Esta solicitação já foi aprovada e não pode ser recusada.');
            $this->redirect('/admin/agencias');
            return;
        }

        $reason = trim((string) $request->input('block_reason', ''));
        if ($reason === '') {
            $this->flash('error', 'O motivo da recusa é obrigatório.');
            $this->redirect('/admin/agencias?tab=solicitacoes');
            return;
        }

        $requestModel->reject($id, $reason);

        // Notificar a agência (e-mail + WhatsApp)
        $siteUrl = $this->setting('site_url', 'https://puntacananovo.lrvweb.com.br');
        try {
            (new \App\Services\EmailService())->sendTemplate(
                $req['email'],
                $req['contact_name'],
                'Atualização sobre sua solicitação de parceria - Punta Cana para Brasileiros',
                'agency-rejected',
                [
                    'contactName' => $req['contact_name'],
                    'companyName' => $req['company_name'],
                    'reason' => $reason,
                    'siteUrl' => $siteUrl,
                ]
            );
        } catch (\Throwable $e) {}
        try {
            (new \App\Services\AgencyNotifier())->notifyRejected($req['phone'], $req['company_name'], $req['contact_name'], $reason);
        } catch (\Throwable $e) {}

        $this->flash('success', 'Solicitação recusada.');
        $this->redirect('/admin/agencias');
    }

    // ── Solicitação: excluir ────────────────────────────────────
    public function deleteRequest(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->db->delete('agency_requests', 'id = ?', [$id]);
        $this->flash('success', 'Solicitação excluída.');
        $this->redirect('/admin/agencias');
    }

    // ── Criar ───────────────────────────────────────────────────
    public function create(Request $request, Response $response): void
    {
        $this->view('admin/agencies/form', [
            'agency' => null,
            'suggestedRef' => $this->agencyModel->generateRefCode(),
            'pageTitle' => 'Nova Agência',
        ], 'admin');
    }

    public function store(Request $request, Response $response): void
    {
        $data = $this->collect($request);

        if ($error = $this->validate($data)) {
            $this->flash('error', $error);
            $this->redirect('/admin/agencias/criar');
            return;
        }

        // Garante ref_code (gera se não informado)
        if ($data['ref_code'] === '') {
            $data['ref_code'] = $this->agencyModel->generateRefCode();
        }

        $this->agencyModel->create($data);
        $this->flash('success', 'Agência cadastrada com sucesso!');
        $this->redirect('/admin/agencias');
    }

    // ── Editar ──────────────────────────────────────────────────
    public function edit(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $agency = $this->agencyModel->find($id);
        if (!$agency) {
            $this->flash('error', 'Agência não encontrada.');
            $this->redirect('/admin/agencias');
            return;
        }

        $this->view('admin/agencies/form', [
            'agency' => $agency,
            'suggestedRef' => $agency['ref_code'],
            'pageTitle' => 'Editar Agência',
        ], 'admin');
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $agency = $this->agencyModel->find($id);
        if (!$agency) {
            $this->flash('error', 'Agência não encontrada.');
            $this->redirect('/admin/agencias');
            return;
        }

        $data = $this->collect($request);
        if ($data['ref_code'] === '') {
            $data['ref_code'] = $agency['ref_code'];
        }

        if ($error = $this->validate($data, $id)) {
            $this->flash('error', $error);
            $this->redirect('/admin/agencias/' . $id . '/editar');
            return;
        }

        // Não sobrescrever agregados
        unset($data['total_sales'], $data['total_commission'], $data['total_paid']);

        $this->agencyModel->update($id, $data);
        $this->flash('success', 'Agência atualizada com sucesso!');
        $this->redirect('/admin/agencias');
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->agencyModel->delete($id);
        $this->flash('success', 'Agência removida.');
        $this->redirect('/admin/agencias');
    }

    // ── Detalhe (vendas e comissões da agência) ─────────────────
    public function show(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $agency = $this->agencyModel->find($id);
        if (!$agency) {
            $this->flash('error', 'Agência não encontrada.');
            $this->redirect('/admin/agencias');
            return;
        }

        $commissions = $this->commissionModel->getByAgency($id, 1, 100);
        $pending = $this->commissionModel->getTotalPending($id);
        $link = (new AgencyService())->generateLink($agency['ref_code']);

        $this->view('admin/agencies/show', [
            'agency' => $agency,
            'commissions' => $commissions,
            'pendingTotal' => $pending,
            'refLink' => $link,
            'pageTitle' => 'Agência: ' . ($agency['trade_name'] ?: $agency['company_name']),
        ], 'admin');
    }

    // ── Comissões (todas as agências) ───────────────────────────
    public function commissions(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $status = $request->query('status', 'all');

        $commissions = $this->commissionModel->getAllWithAgency($page, 20, $status);

        $counts = [
            'pending' => (float) $this->db->fetchColumn("SELECT COALESCE(SUM(amount),0) FROM agency_commissions WHERE status = 'pending'"),
            'paid' => (float) $this->db->fetchColumn("SELECT COALESCE(SUM(amount),0) FROM agency_commissions WHERE status = 'paid'"),
        ];

        $this->view('admin/agencies/commissions', [
            'commissions' => $commissions,
            'counts' => $counts,
            'currentStatus' => $status,
            'pageTitle' => 'Comissões de Agências',
        ], 'admin');
    }

    public function payCommission(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $reference = trim((string) $request->input('payout_reference', ''));

        $commission = $this->commissionModel->find($id);
        if (!$commission || $commission['status'] !== 'pending') {
            $this->flash('error', 'Comissão não encontrada ou já processada.');
            $this->redirect('/admin/agencias/comissoes');
            return;
        }

        $this->commissionModel->markPaid($id, $reference ?: null);
        $this->db->query(
            "UPDATE agencies SET total_paid = total_paid + ? WHERE id = ?",
            [(float) $commission['amount'], (int) $commission['agency_id']]
        );

        // Notificar a agência que a comissão foi paga (WhatsApp + e-mail)
        try {
            $agency = $this->agencyModel->find((int) $commission['agency_id']);
            if ($agency) {
                (new \App\Services\AgencyNotifier())->notifyCommissionPaid(
                    $agency,
                    (float) $commission['amount'],
                    $reference ?: null
                );
            }
        } catch (\Throwable $e) {
            error_log('[Admin\\AgenciesController] Falha ao notificar pagamento: ' . $e->getMessage());
        }

        $this->flash('success', 'Comissão marcada como paga!');
        $this->redirect('/admin/agencias/comissoes');
    }

    public function cancelCommission(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $reason = trim((string) $request->input('reason', ''));

        if ($reason === '') {
            $this->flash('error', 'Informe o motivo do cancelamento.');
            $this->redirect('/admin/agencias/comissoes');
            return;
        }

        $commission = $this->commissionModel->find($id);
        if (!$commission) {
            $this->flash('error', 'Comissão não encontrada.');
            $this->redirect('/admin/agencias/comissoes');
            return;
        }

        // Reverte os agregados se estava pendente/paga
        if (in_array($commission['status'], ['pending', 'paid'], true)) {
            $this->db->query(
                "UPDATE agencies SET total_commission = GREATEST(0, total_commission - ?) WHERE id = ?",
                [(float) $commission['amount'], (int) $commission['agency_id']]
            );
            if ($commission['status'] === 'paid') {
                $this->db->query(
                    "UPDATE agencies SET total_paid = GREATEST(0, total_paid - ?) WHERE id = ?",
                    [(float) $commission['amount'], (int) $commission['agency_id']]
                );
            }
        }

        $this->commissionModel->cancel($id, $reason);

        // Notificar a agência sobre o cancelamento (WhatsApp + e-mail)
        try {
            $agency = $this->agencyModel->find((int) $commission['agency_id']);
            if ($agency) {
                (new \App\Services\AgencyNotifier())->notifyCommissionCancelled(
                    $agency,
                    (float) $commission['amount'],
                    $reason
                );
            }
        } catch (\Throwable $e) {
            error_log('[Admin\\AgenciesController] Falha ao notificar cancelamento: ' . $e->getMessage());
        }

        $this->flash('success', 'Comissão cancelada.');
        $this->redirect('/admin/agencias/comissoes?status=cancelled');
    }

    // ── Helpers ─────────────────────────────────────────────────
    private function collect(Request $request): array
    {
        return [
            'company_name' => trim((string) $request->input('company_name', '')),
            'trade_name' => trim((string) $request->input('trade_name', '')) ?: null,
            'cnpj' => trim((string) $request->input('cnpj', '')) ?: null,
            'contact_name' => trim((string) $request->input('contact_name', '')) ?: null,
            'email' => trim((string) $request->input('email', '')) ?: null,
            'phone' => trim((string) $request->input('phone', '')) ?: null,
            'address' => trim((string) $request->input('address', '')) ?: null,
            'city' => trim((string) $request->input('city', '')) ?: null,
            'country' => trim((string) $request->input('country', '')) ?: null,
            'bank_info' => trim((string) $request->input('bank_info', '')) ?: null,
            'ref_code' => strtoupper(trim((string) $request->input('ref_code', ''))),
            'commission_rate' => (float) $request->input('commission_rate', '10'),
            'status' => $request->input('status', 'active') === 'inactive' ? 'inactive' : 'active',
            'notes' => trim((string) $request->input('notes', '')) ?: null,
        ];
    }

    private function validate(array $data, ?int $excludeId = null): ?string
    {
        if ($data['company_name'] === '') {
            return 'A razão social é obrigatória.';
        }
        if ($data['ref_code'] !== '' && !preg_match('/^[A-Z0-9\-_]{2,40}$/', $data['ref_code'])) {
            return 'O código de indicação deve ter de 2 a 40 caracteres (letras, números, hífen ou underscore).';
        }
        if ($data['ref_code'] !== '' && $this->agencyModel->refCodeExists($data['ref_code'], $excludeId)) {
            return 'Já existe uma agência com este código de indicação.';
        }
        if ($data['commission_rate'] < 0 || $data['commission_rate'] > 100) {
            return 'A comissão deve estar entre 0 e 100%.';
        }
        if (!empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'E-mail inválido.';
        }
        return null;
    }
}
