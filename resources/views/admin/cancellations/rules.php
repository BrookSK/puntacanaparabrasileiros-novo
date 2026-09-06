<div class="card-header">
    <div>
        <h2>Regras de Cancelamento</h2>
        <p class="admin-card-subtitle">Defina o reembolso conforme a antecedência do cancelamento. O sistema calcula o tempo até a viagem e sugere o reembolso automaticamente.</p>
    </div>
    <a href="/admin/cancelamentos" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar
    </a>
</div>

<div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 16px;font-size:13px;color:#1e40af;margin-bottom:20px;">
    <strong>Como funciona:</strong> cada faixa define uma antecedência mínima (em dias ou horas) e o reembolso correspondente (% do valor pago ou valor fixo em USD).
    O sistema aplica a faixa de <strong>maior antecedência</strong> que o cliente ainda atende. Se o cancelamento for mais próximo do que todas as faixas, o reembolso é <strong>0% (sem reembolso)</strong>.
    <br>Exemplo: "a partir de 2 dias → 100%" e "a partir de 24 horas → 50%". Cancelou faltando 3 dias = 100%; faltando 30 horas = 50%; faltando 5 horas = 0%.
</div>

<!-- Regras existentes -->
<div class="admin-card">
    <div class="admin-card-header"><div><h3>Faixas cadastradas</h3></div></div>
    <table class="table">
        <thead>
            <tr><th>Ordem</th><th>Descrição</th><th>Antecedência mínima</th><th>Reembolso</th><th>Status</th><th>Ações</th></tr>
        </thead>
        <tbody>
            <?php if (empty($rules)): ?>
            <tr><td colspan="6" class="text-center">Nenhuma regra cadastrada. Adicione a primeira abaixo.</td></tr>
            <?php else: ?>
            <?php foreach ($rules as $r): ?>
            <tr>
                <td><?= (int)$r['sort_order'] ?></td>
                <td><?= e($r['label'] ?: '—') ?></td>
                <td><strong><?= (int)$r['time_value'] ?></strong> <?= $r['time_unit'] === 'days' ? 'dia(s)' : 'hora(s)' ?> antes</td>
                <td>
                    <?php if ($r['refund_type'] === 'percentage'): ?>
                        <?= rtrim(rtrim(number_format((float)$r['refund_value'], 2), '0'), '.') ?>%
                    <?php else: ?>
                        <?= money((float)$r['refund_value']) ?>
                    <?php endif; ?>
                </td>
                <td><?= (int)$r['active'] === 1 ? '<span class="badge badge-success">Ativa</span>' : '<span class="badge badge-secondary">Inativa</span>' ?></td>
                <td class="actions-cell" style="white-space:nowrap;">
                    <button type="button" class="btn btn-sm btn-outline cr-edit-btn"
                            data-id="<?= (int)$r['id'] ?>"
                            data-label="<?= e($r['label'] ?? '') ?>"
                            data-time-value="<?= (int)$r['time_value'] ?>"
                            data-time-unit="<?= e($r['time_unit']) ?>"
                            data-refund-value="<?= e((string)$r['refund_value']) ?>"
                            data-refund-type="<?= e($r['refund_type']) ?>"
                            data-sort-order="<?= (int)$r['sort_order'] ?>"
                            data-active="<?= (int)$r['active'] ?>">Editar</button>
                    <button type="button" class="btn btn-sm btn-danger" onclick="crDelete(<?= (int)$r['id'] ?>)">Excluir</button>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Form criar/editar -->
<div class="admin-card" style="margin-top:20px;">
    <div class="admin-card-header"><div><h3 id="crFormTitle">Nova faixa</h3></div></div>
    <form method="POST" id="crForm" action="/admin/cancelamentos/regras/criar">
        <?= csrf_field() ?>
        <div class="form-group">
            <label>Descrição (opcional)</label>
            <input type="text" name="label" id="cr_label" class="form-control" placeholder="Ex: A partir de 2 dias antes">
        </div>
        <div class="form-row">
            <div class="form-group col-6">
                <label>Antecedência mínima <span class="required">*</span></label>
                <div style="display:flex;gap:8px;">
                    <input type="number" name="time_value" id="cr_time_value" class="form-control" min="1" placeholder="Ex: 48" required style="max-width:120px;">
                    <select name="time_unit" id="cr_time_unit" class="form-control">
                        <option value="days">Dias</option>
                        <option value="hours">Horas</option>
                    </select>
                </div>
                <small class="form-hint">Tempo mínimo antes da viagem para esta faixa valer.</small>
            </div>
            <div class="form-group col-6">
                <label>Reembolso <span class="required">*</span></label>
                <div style="display:flex;gap:8px;">
                    <input type="number" name="refund_value" id="cr_refund_value" class="form-control" step="0.01" min="0" placeholder="Ex: 50" required style="max-width:120px;">
                    <select name="refund_type" id="cr_refund_type" class="form-control">
                        <option value="percentage">% do valor pago</option>
                        <option value="fixed">Valor fixo (USD)</option>
                    </select>
                </div>
                <small class="form-hint">Percentual (0 a 100) ou valor fixo em dólares.</small>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-6">
                <label>Ordem de exibição</label>
                <input type="number" name="sort_order" id="cr_sort_order" class="form-control" value="0" style="max-width:120px;">
            </div>
            <div class="form-group col-6">
                <label style="display:flex;align-items:center;gap:8px;font-weight:400;margin-top:28px;">
                    <input type="checkbox" name="active" id="cr_active" value="1" checked> Regra ativa
                </label>
            </div>
        </div>
        <div style="display:flex;gap:10px;">
            <button type="submit" class="btn btn-primary">Salvar faixa</button>
            <button type="button" class="btn btn-outline" id="crReset" onclick="crResetForm()" style="display:none;">Cancelar edição</button>
        </div>
    </form>
</div>

<form method="POST" id="crDeleteForm" style="display:none;"><?= csrf_field() ?></form>

<script>
function crEditFrom(btn){
    var d = btn.dataset;
    document.getElementById('crFormTitle').textContent = 'Editar faixa';
    document.getElementById('crForm').action = '/admin/cancelamentos/regras/' + d.id + '/editar';
    document.getElementById('cr_label').value = d.label || '';
    document.getElementById('cr_time_value').value = d.timeValue;
    document.getElementById('cr_time_unit').value = d.timeUnit;
    document.getElementById('cr_refund_value').value = d.refundValue;
    document.getElementById('cr_refund_type').value = d.refundType;
    document.getElementById('cr_sort_order').value = d.sortOrder;
    document.getElementById('cr_active').checked = parseInt(d.active) === 1;
    document.getElementById('crReset').style.display = 'inline-flex';
    document.getElementById('crForm').scrollIntoView({behavior: 'smooth', block: 'center'});
}
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.cr-edit-btn').forEach(function(btn){
        btn.addEventListener('click', function(){ crEditFrom(btn); });
    });
});
function crResetForm(){
    document.getElementById('crFormTitle').textContent = 'Nova faixa';
    var f = document.getElementById('crForm');
    f.action = '/admin/cancelamentos/regras/criar';
    f.reset();
    document.getElementById('cr_active').checked = true;
    document.getElementById('crReset').style.display = 'none';
}
function crDelete(id){
    if (!confirm('Excluir esta faixa de reembolso?')) return;
    var f = document.getElementById('crDeleteForm');
    f.action = '/admin/cancelamentos/regras/' + id + '/excluir';
    f.submit();
}
</script>
