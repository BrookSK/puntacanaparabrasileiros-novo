<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Coupon;

class CouponsController extends Controller
{
    private Coupon $couponModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
        $this->couponModel = new Coupon();
    }

    public function index(Request $request, Response $response): void
    {
        $coupons = $this->couponModel->getAllWithAffiliate();

        $this->view('admin/coupons/index', [
            'coupons' => $coupons,
            'pageTitle' => 'Cupons de Desconto',
        ], 'admin');
    }

    public function create(Request $request, Response $response): void
    {
        $this->view('admin/coupons/form', [
            'coupon' => null,
            'affiliates' => $this->getActiveAffiliates(),
            'pageTitle' => 'Novo Cupom',
        ], 'admin');
    }

    public function store(Request $request, Response $response): void
    {
        $data = $this->collect($request);

        if ($error = $this->validate($data)) {
            $this->flash('error', $error);
            $this->redirect('/admin/cupons/criar');
            return;
        }

        $this->couponModel->create($data);
        $this->flash('success', 'Cupom criado com sucesso!');
        $this->redirect('/admin/cupons');
    }

    public function edit(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            $this->flash('error', 'Cupom não encontrado.');
            $this->redirect('/admin/cupons');
            return;
        }

        $this->view('admin/coupons/form', [
            'coupon' => $coupon,
            'affiliates' => $this->getActiveAffiliates(),
            'pageTitle' => 'Editar Cupom',
        ], 'admin');
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $coupon = $this->couponModel->find($id);

        if (!$coupon) {
            $this->flash('error', 'Cupom não encontrado.');
            $this->redirect('/admin/cupons');
            return;
        }

        $data = $this->collect($request);

        if ($error = $this->validate($data, $id)) {
            $this->flash('error', $error);
            $this->redirect('/admin/cupons/' . $id . '/editar');
            return;
        }

        // used_count não é editável no formulário
        unset($data['used_count']);

        $this->couponModel->update($id, $data);
        $this->flash('success', 'Cupom atualizado com sucesso!');
        $this->redirect('/admin/cupons');
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->couponModel->delete($id);
        $this->flash('success', 'Cupom excluído.');
        $this->redirect('/admin/cupons');
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    /**
     * Coleta e normaliza os dados do formulário.
     */
    private function collect(Request $request): array
    {
        $type = $request->input('type', 'percentage') === 'fixed' ? 'fixed' : 'percentage';
        $affiliateId = (int) $request->input('affiliate_id', '0');
        $minOrder = trim((string) $request->input('min_order', ''));
        $maxUses = trim((string) $request->input('max_uses', ''));
        $startsAt = trim((string) $request->input('starts_at', ''));
        $expiresAt = trim((string) $request->input('expires_at', ''));

        return [
            'code' => strtoupper(trim((string) $request->input('code', ''))),
            'description' => trim((string) $request->input('description', '')) ?: null,
            'type' => $type,
            'value' => (float) $request->input('value', '0'),
            'affiliate_id' => $affiliateId > 0 ? $affiliateId : null,
            'min_order' => $minOrder !== '' ? (float) $minOrder : null,
            'max_uses' => $maxUses !== '' ? (int) $maxUses : null,
            'starts_at' => $startsAt !== '' ? date('Y-m-d H:i:s', strtotime($startsAt)) : null,
            'expires_at' => $expiresAt !== '' ? date('Y-m-d H:i:s', strtotime($expiresAt)) : null,
            'active' => $request->input('active') ? 1 : 0,
        ];
    }

    /**
     * Valida os dados. Retorna string de erro ou null se ok.
     */
    private function validate(array $data, ?int $excludeId = null): ?string
    {
        if ($data['code'] === '') {
            return 'O código do cupom é obrigatório.';
        }
        if (!preg_match('/^[A-Z0-9\-_]{2,50}$/', $data['code'])) {
            return 'O código deve ter de 2 a 50 caracteres (letras, números, hífen ou underscore).';
        }
        if ($this->couponModel->codeExists($data['code'], $excludeId)) {
            return 'Já existe um cupom com este código.';
        }
        if ($data['value'] <= 0) {
            return 'O valor do desconto deve ser maior que zero.';
        }
        if ($data['type'] === 'percentage' && $data['value'] > 100) {
            return 'Para desconto percentual, o valor não pode ser maior que 100.';
        }
        if ($data['starts_at'] && $data['expires_at'] && strtotime($data['expires_at']) < strtotime($data['starts_at'])) {
            return 'A data de expiração não pode ser anterior à data de início.';
        }
        return null;
    }

    /**
     * Lista os afiliados ativos para o select.
     */
    private function getActiveAffiliates(): array
    {
        return $this->db->fetchAll(
            "SELECT a.id, u.first_name, u.last_name, u.email
             FROM affiliates a
             INNER JOIN users u ON a.user_id = u.id
             WHERE a.status = 'active'
             ORDER BY u.first_name ASC"
        );
    }
}
