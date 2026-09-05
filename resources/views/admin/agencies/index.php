<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-value"><?= (int)($totals['active'] ?? 0) ?></div>
        <div class="stat-label">Agências Ativas</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int)($totals['total'] ?? 0) ?></div>
        <div class="stat-label">Total de Agências</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= money((float)($totals['pending_commission'] ?? 0)) ?></div>
        <div class="stat-label">Comissões Pendentes</div>
    </div>
</div>

<div class="card-header">
    <div class="header-actions" style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="/admin/agencias/criar" class="btn btn-primary">+ Nova Agência</a>
        <a href="/admin/agencias/comissoes" class="btn btn-outline">Ver Comissões</a>
    </div>
    <form method="GET" class="filter-form" style="display:flex;gap:8px;">
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome, CNPJ ou código..." value="<?= e($currentSearch ?? '') ?>" style="min-width:240px;">
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Todos</option>
            <option value="active" <?= ($currentStatus ?? '') === 'active' ? 'selected' : '' ?>>Ativas</option>
            <option value="inactive" <?= ($currentStatus ?? '') === 'inactive' ? 'selected' : '' ?>>Inativas</option>
        </select>
        <button type="submit" class="btn btn-outline">Filtrar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Agência</th>
            <th>CNPJ</th>
            <th>Código</th>
            <th>Comissão</th>
            <th>Vendas</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($agencies['items'])): ?>
        <tr><td colspan="7" class="text-center">Nenhuma agência cadastrada.</td></tr>
        <?php else: ?>
        <?php foreach ($agencies['items'] as $a): ?>
        <tr>
            <td>
                <strong><?= e($a['trade_name'] ?: $a['company_name']) ?></strong>
                <?php if (!empty($a['trade_name'])): ?>
                <br><small style="color:#636e72;"><?= e($a['company_name']) ?></small>
                <?php endif; ?>
                <?php if (!empty($a['contact_name'])): ?>
                <br><small style="color:#636e72;">Contato: <?= e($a['contact_name']) ?></small>
                <?php endif; ?>
            </td>
            <td style="font-size:13px;"><?= e($a['cnpj'] ?: '—') ?></td>
            <td><span class="badge badge-info"><?= e($a['ref_code']) ?></span></td>
            <td><?= rtrim(rtrim(number_format((float)$a['commission_rate'], 2), '0'), '.') ?>%</td>
            <td style="font-size:13px;">
                <?= money((float)$a['total_sales']) ?>
                <br><small style="color:#636e72;">comissão: <?= money((float)$a['total_commission']) ?></small>
            </td>
            <td>
                <?php if ($a['status'] === 'active'): ?>
                    <span class="badge badge-success">Ativa</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Inativa</span>
                <?php endif; ?>
            </td>
            <td class="actions-cell" style="white-space:nowrap;">
                <a href="/admin/agencias/<?= (int)$a['id'] ?>" class="btn btn-sm btn-outline">Detalhes</a>
                <a href="/admin/agencias/<?= (int)$a['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
                <button type="button" class="btn btn-sm btn-danger" onclick="agencyDelete(<?= (int)$a['id'] ?>)">Excluir</button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($agencies['total_pages']) && $agencies['total_pages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $agencies['total_pages']; $p++): ?>
    <a href="?page=<?= $p ?>&busca=<?= e($currentSearch ?? '') ?>&status=<?= e($currentStatus ?? '') ?>" class="pagination-btn <?= $p === ($agencies['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<script>
function agencyDelete(id){
    if (!confirm('Excluir esta agência? As comissões já geradas permanecem no histórico.')) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/agencias/' + id + '/excluir';
    form.innerHTML = '<input name="_token" value="<?= e(csrf_token()) ?>">';
    document.body.appendChild(form);
    form.submit();
}
</script>
