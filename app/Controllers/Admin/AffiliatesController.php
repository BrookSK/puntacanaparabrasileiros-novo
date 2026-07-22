<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Affiliate;
use App\Models\Commission;

class AffiliatesController extends Controller
{
    private Affiliate $affiliateModel;
    private Commission $commissionModel;

    public function __construct()
    {
        parent::__construct();
        $this->affiliateModel = new Affiliate();
        $this->commissionModel = new Commission();
    }

    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $affiliates = $this->affiliateModel->getWithUserData($page, 20);

        $this->view('admin/affiliates/index', [
            'affiliates' => $affiliates,
            'pageTitle' => 'Gerenciar Afiliados',
        ], 'admin');
    }

    public function approve(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->affiliateModel->approve($id);

        // Atualizar role do user para affiliate
        $affiliate = $this->affiliateModel->find($id);
        if ($affiliate) {
            $this->db->update('users', ['role' => 'affiliate'], 'id = ? AND role = ?', [(int) $affiliate['user_id'], 'customer']);
        }

        $this->flash('success', 'Afiliado aprovado!');
        $this->redirect('/admin/afiliados');
    }

    public function reject(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->affiliateModel->reject($id);
        $this->flash('success', 'Afiliado rejeitado.');
        $this->redirect('/admin/afiliados');
    }

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

    public function payCommission(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $reference = $request->input('payout_reference', '');

        $this->commissionModel->markPaid($id, $reference ?: null);

        // Atualizar total_paid do afiliado
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
