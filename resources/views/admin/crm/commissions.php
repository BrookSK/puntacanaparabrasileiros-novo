<div class="admin-page-header">
    <h2>CRM — Comissões</h2>
    <div>
        <a href="/crm" class="btn btn-outline btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg> Boards</a>
        <a href="/crm/dashboard" class="btn btn-outline btn-sm"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"/><path d="M12 20V4"/><path d="M6 20v-6"/></svg> Dashboard</a>
    </div>
</div>

<!-- Filtros -->
<div class="admin-card" style="margin-bottom:16px;">
    <form method="GET" action="/crm/commissions" class="filters-row">
        <div class="form-group">
            <label>Mês</label>
            <input type="month" name="month" class="form-control" value="<?= e($month) ?>">
        </div>
        <?php if ($isSuperAdmin && !empty($commercials)): ?>
        <div class="form-group">
            <label>Comercial</label>
            <select name="user_id" class="form-control">
                <option value="">Todos</option>
                <?php foreach ($commercials as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $filterUserId == $c['id'] ? 'selected' : '' ?>><?= e($c['first_name'] . ' ' . $c['last_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary btn-sm">Filtrar</button>
    </form>
</div>

<!-- Totalizadores -->
<div class="stats-grid stats-3">
    <div class="stat-card stat-success">
        <div class="stat-value">R$ <?= number_format($totalCommission, 2, ',', '.') ?></div>
        <div class="stat-label"><?= $isSuperAdmin ? 'Total a Pagar' : 'Sua Comissão' ?> (mês)</div>
    </div>
    <div class="stat-card">
        <div class="stat-value">R$ <?= number_format($totalConverted, 2, ',', '.') ?></div>
        <div class="stat-label">Total Convertido</div>
    </div>
    <?php if ($isSuperAdmin): ?>
    <div class="stat-card stat-info">
        <div class="stat-value"><?= $commercialCount ?></div>
        <div class="stat-label">Comerciais Cadastrados</div>
    </div>
    <?php endif; ?>
</div>

<!-- Tabela -->
<div class="admin-card" style="margin-top:16px;">
    <?php if (empty($commissions)): ?>
    <p class="text-muted" style="padding:20px;">Nenhuma comissão no período selecionado.</p>
    <?php else: ?>
    <table class="table">
        <thead>
            <tr>
                <th>Comercial</th>
                <th>% Comissão</th>
                <th>Leads Convertidos</th>
                <th>Valor Total</th>
                <th>Comissão a Pagar</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($commissions as $comm): ?>
            <tr class="commission-row" onclick="toggleLeads(<?= $comm['user_id'] ?>, '<?= e($month) ?>', this)">
                <td><strong><?= e($comm['first_name'] . ' ' . $comm['last_name']) ?></strong></td>
                <td><?= number_format((float)$comm['commission_percent'], 1) ?>%</td>
                <td><span class="badge badge-success"><?= (int)$comm['leads_count'] ?></span></td>
                <td>R$ <?= number_format((float)$comm['total_value'], 2, ',', '.') ?></td>
                <td class="text-success"><strong>R$ <?= number_format((float)$comm['commission_value'], 2, ',', '.') ?></strong></td>
                <td>▼</td>
            </tr>
            <tr class="commission-detail" id="leads-<?= $comm['user_id'] ?>" style="display:none;">
                <td colspan="6"><div class="commission-leads-list">Carregando...</div></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

async function toggleLeads(userId, month, row) {
    const detailRow = document.getElementById(`leads-${userId}`);
    if (detailRow.style.display !== 'none') {
        detailRow.style.display = 'none';
        return;
    }
    detailRow.style.display = '';
    const res = await fetch(`/crm/commissionLeads/${userId}?month=${month}`);
    const json = await res.json();
    if (!json.success) return;

    const leads = json.leads;
    if (!leads.length) {
        detailRow.querySelector('.commission-leads-list').innerHTML = '<p>Nenhum lead encontrado.</p>';
        return;
    }

    let html = '<table class="table table-sm"><thead><tr><th>Lead</th><th>Telefone</th><th>Valor</th><th>Data Conversão</th></tr></thead><tbody>';
    leads.forEach(l => {
        html += `<tr><td>${l.contact_name || l.title}</td><td>${l.phone || '—'}</td><td>R$ ${parseFloat(l.value || 0).toFixed(2).replace('.', ',')}</td><td>${l.outcome_at || '—'}</td></tr>`;
    });
    html += '</tbody></table>';
    detailRow.querySelector('.commission-leads-list').innerHTML = html;
}
</script>
