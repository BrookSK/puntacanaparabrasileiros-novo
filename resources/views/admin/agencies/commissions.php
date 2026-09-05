<?php
$statusLabels = ['pending' => 'Pendente', 'paid' => 'Paga', 'cancelled' => 'Cancelada'];
$statusColors = ['pending' => 'warning', 'paid' => 'success', 'cancelled' => 'danger'];
?>

<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card"><div class="stat-value"><?= money((float)($counts['pending'] ?? 0)) ?></div><div class="stat-label">Pendente de Pagamento</div></div>
    <div class="stat-card"><div class="stat-value"><?= money((float)($counts['paid'] ?? 0)) ?></div><div class="stat-label">Já Pago</div></div>
</div>

<div class="card-header">
    <div class="header-actions"><a href="/admin/agencias" class="btn btn-outline">← Voltar para Agências</a></div>
    <form method="GET" class="filter-form">
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="all" <?= ($currentStatus ?? 'all') === 'all' ? 'selected' : '' ?>>Todos os Status</option>
            <option value="pending" <?= ($currentStatus ?? '') === 'pending' ? 'selected' : '' ?>>Pendentes</option>
            <option value="paid" <?= ($currentStatus ?? '') === 'paid' ? 'selected' : '' ?>>Pagas</option>
            <option value="cancelled" <?= ($currentStatus ?? '') === 'cancelled' ? 'selected' : '' ?>>Canceladas</option>
        </select>
    </form>
</div>

<table class="table">
    <thead>
        <tr><th>#</th><th>Agência</th><th>Reserva</th><th>Base</th><th>Comissão</th><th>Status</th><th>Data</th><th>Ações</th></tr>
    </thead>
    <tbody>
        <?php if (empty($commissions['items'])): ?>
        <tr><td colspan="8" class="text-center">Nenhuma comissão encontrada.</td></tr>
        <?php else: ?>
        <?php foreach ($commissions['items'] as $c): ?>
        <?php $st = $c['status'] ?? 'pending'; ?>
        <tr>
            <td>#<?= (int)$c['id'] ?></td>
            <td>
                <strong><?= e($c['trade_name'] ?: $c['company_name'] ?: '—') ?></strong>
                <?php if (!empty($c['ref_code'])): ?><br><small style="color:#636e72;"><?= e($c['ref_code']) ?></small><?php endif; ?>
            </td>
            <td><?= e($c['booking_number'] ?? '—') ?></td>
            <td><?= money((float)$c['base_amount']) ?></td>
            <td><strong><?= money((float)$c['amount']) ?></strong> <small style="color:#636e72;">(<?= rtrim(rtrim(number_format((float)$c['rate'], 2), '0'), '.') ?>%)</small></td>
            <td>
                <span class="badge badge-<?= $statusColors[$st] ?? 'secondary' ?>"><?= $statusLabels[$st] ?? $st ?></span>
                <?php if ($st === 'cancelled' && !empty($c['notes'])): ?><br><small style="color:#636e72;" title="<?= e($c['notes']) ?>"><?= e(mb_strimwidth($c['notes'], 0, 30, '...')) ?></small><?php endif; ?>
                <?php if ($st === 'paid' && !empty($c['payout_reference'])): ?><br><small style="color:#636e72;">Ref: <?= e($c['payout_reference']) ?></small><?php endif; ?>
            </td>
            <td style="font-size:12px;"><?= !empty($c['created_at']) ? date('d/m/Y', strtotime($c['created_at'])) : '' ?></td>
            <td class="actions-cell" style="white-space:nowrap;">
                <?php if ($st === 'pending'): ?>
                <button type="button" class="btn btn-sm btn-success" onclick="agPay(<?= (int)$c['id'] ?>)">Pagar</button>
                <button type="button" class="btn btn-sm btn-danger" onclick="agCancel(<?= (int)$c['id'] ?>)">Cancelar</button>
                <?php else: ?>
                <span style="color:#aaa;">—</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($commissions['total_pages']) && $commissions['total_pages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $commissions['total_pages']; $p++): ?>
    <a href="?page=<?= $p ?>&status=<?= e($currentStatus ?? 'all') ?>" class="pagination-btn <?= $p === ($commissions['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Modal de motivo/referência -->
<div class="ag-modal-overlay" id="agModalOverlay">
    <div class="ag-modal">
        <h3 id="agModalTitle">Ação</h3>
        <p id="agModalDesc"></p>
        <input type="text" id="agModalInput" placeholder="">
        <div class="ag-modal-actions">
            <button type="button" class="btn btn-outline" id="agModalCancel">Voltar</button>
            <button type="button" class="btn btn-primary" id="agModalConfirm">Confirmar</button>
        </div>
    </div>
</div>

<style>
.ag-modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);display:none;align-items:center;justify-content:center;z-index:9999;padding:16px}
.ag-modal-overlay.open{display:flex}
.ag-modal{background:#fff;border-radius:12px;max-width:440px;width:100%;padding:24px;box-shadow:0 20px 50px rgba(0,0,0,.25)}
.ag-modal h3{margin:0 0 8px;font-size:18px;color:#0f172a}
.ag-modal p{margin:0 0 14px;font-size:13.5px;color:#64748b}
.ag-modal input{width:100%;padding:11px 13px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:14px;box-sizing:border-box}
.ag-modal input:focus{outline:none;border-color:#1B6F00}
.ag-modal-actions{display:flex;gap:10px;justify-content:flex-end;margin-top:16px}
</style>

<script>
var AG_CSRF = '<?= e(csrf_token()) ?>';
(function(){
    var overlay = document.getElementById('agModalOverlay');
    var titleEl = document.getElementById('agModalTitle');
    var descEl = document.getElementById('agModalDesc');
    var inputEl = document.getElementById('agModalInput');
    var confirmBtn = document.getElementById('agModalConfirm');
    var cancelBtn = document.getElementById('agModalCancel');
    var pending = null;

    function open(cfg){
        pending = cfg;
        titleEl.textContent = cfg.title;
        descEl.textContent = cfg.desc;
        inputEl.placeholder = cfg.placeholder || '';
        inputEl.value = '';
        overlay.classList.add('open');
        setTimeout(function(){ inputEl.focus(); }, 50);
    }
    function close(){ overlay.classList.remove('open'); pending = null; }

    cancelBtn.addEventListener('click', close);
    overlay.addEventListener('click', function(e){ if(e.target === overlay) close(); });

    confirmBtn.addEventListener('click', function(){
        if (!pending) return;
        var val = inputEl.value.trim();
        if (pending.required && !val){ inputEl.style.borderColor = '#dc2626'; inputEl.focus(); return; }
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = pending.action;
        var fields = pending.fields(val);
        fields._token = AG_CSRF;
        for (var k in fields){
            var i = document.createElement('input'); i.name = k; i.value = fields[k]; form.appendChild(i);
        }
        document.body.appendChild(form);
        form.submit();
    });

    window.agPay = function(id){
        open({
            title: 'Registrar pagamento',
            desc: 'Informe uma referência do pagamento (opcional). Ex: comprovante, data, PIX.',
            placeholder: 'Referência (opcional)',
            required: false,
            action: '/admin/agencias/comissoes/' + id + '/pagar',
            fields: function(v){ return {payout_reference: v}; }
        });
    };
    window.agCancel = function(id){
        open({
            title: 'Cancelar comissão',
            desc: 'Informe o motivo do cancelamento.',
            placeholder: 'Motivo',
            required: true,
            action: '/admin/agencias/comissoes/' + id + '/cancelar',
            fields: function(v){ return {reason: v}; }
        });
    };
})();
</script>
