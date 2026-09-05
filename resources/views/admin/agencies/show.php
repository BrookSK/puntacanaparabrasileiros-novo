<?php
$statusLabels = ['pending' => 'Pendente', 'paid' => 'Paga', 'cancelled' => 'Cancelada'];
$statusColors = ['pending' => 'warning', 'paid' => 'success', 'cancelled' => 'danger'];
?>

<div class="card-header">
    <h2><?= e($agency['trade_name'] ?: $agency['company_name']) ?></h2>
    <div style="display:flex;gap:8px;">
        <a href="/admin/agencias/<?= (int)$agency['id'] ?>/editar" class="btn btn-outline">Editar</a>
        <a href="/admin/agencias" class="btn btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar
        </a>
    </div>
</div>

<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card"><div class="stat-value"><?= money((float)$agency['total_sales']) ?></div><div class="stat-label">Total em Vendas</div></div>
    <div class="stat-card"><div class="stat-value"><?= money((float)$agency['total_commission']) ?></div><div class="stat-label">Comissão Gerada</div></div>
    <div class="stat-card"><div class="stat-value"><?= money((float)$pendingTotal) ?></div><div class="stat-label">Comissão Pendente</div></div>
    <div class="stat-card"><div class="stat-value"><?= money((float)$agency['total_paid']) ?></div><div class="stat-label">Já Pago</div></div>
</div>

<div class="admin-grid-2">
    <div>
        <div class="admin-card">
            <div class="admin-card-header"><div><h3>Dados da Empresa</h3></div></div>
            <table class="table">
                <tr><td style="width:180px;color:#636e72;">Razão Social</td><td><?= e($agency['company_name']) ?></td></tr>
                <?php if (!empty($agency['trade_name'])): ?><tr><td style="color:#636e72;">Nome Fantasia</td><td><?= e($agency['trade_name']) ?></td></tr><?php endif; ?>
                <tr><td style="color:#636e72;">CNPJ</td><td><?= e($agency['cnpj'] ?: '—') ?></td></tr>
                <tr><td style="color:#636e72;">Contato</td><td><?= e($agency['contact_name'] ?: '—') ?></td></tr>
                <tr><td style="color:#636e72;">E-mail</td><td><?= e($agency['email'] ?: '—') ?></td></tr>
                <tr><td style="color:#636e72;">Telefone</td><td><?= e($agency['phone'] ?: '—') ?></td></tr>
                <tr><td style="color:#636e72;">Endereço</td><td><?= e(trim(($agency['address'] ?? '') . ' ' . ($agency['city'] ?? '') . ' ' . ($agency['country'] ?? ''))) ?: '—' ?></td></tr>
                <tr><td style="color:#636e72;">Comissão</td><td><?= rtrim(rtrim(number_format((float)$agency['commission_rate'], 2), '0'), '.') ?>%</td></tr>
                <?php if (!empty($agency['bank_info'])): ?><tr><td style="color:#636e72;">Dados bancários</td><td style="white-space:pre-line;"><?= e($agency['bank_info']) ?></td></tr><?php endif; ?>
            </table>
        </div>
    </div>
    <div>
        <div class="admin-card">
            <div class="admin-card-header"><div><h3>Link de Indicação</h3><p class="admin-card-subtitle">Compartilhe com a agência</p></div></div>
            <div class="form-group">
                <label>Código: <span class="badge badge-info"><?= e($agency['ref_code']) ?></span></label>
                <input type="text" class="form-control" id="refLink" value="<?= e($refLink) ?>" readonly onclick="this.select()">
                <small class="form-hint">Toda venda feita por quem acessar por este link recebe a comissão desta agência (validade de 30 dias por visitante).</small>
                <button type="button" class="btn btn-outline" style="margin-top:10px;" onclick="copyRefLink()">Copiar link</button>
            </div>
        </div>
    </div>
</div>

<div class="admin-card" style="margin-top:24px;">
    <div class="admin-card-header"><div><h3>Comissões desta agência</h3></div></div>
    <table class="table">
        <thead>
            <tr><th>#</th><th>Reserva</th><th>Base</th><th>Taxa</th><th>Comissão</th><th>Status</th><th>Data</th></tr>
        </thead>
        <tbody>
            <?php if (empty($commissions)): ?>
            <tr><td colspan="7" class="text-center">Nenhuma comissão gerada ainda.</td></tr>
            <?php else: ?>
            <?php foreach ($commissions as $c): ?>
            <?php $st = $c['status'] ?? 'pending'; ?>
            <tr>
                <td>#<?= (int)$c['id'] ?></td>
                <td><?= e($c['booking_number'] ?? '—') ?></td>
                <td><?= money((float)$c['base_amount']) ?></td>
                <td><?= rtrim(rtrim(number_format((float)$c['rate'], 2), '0'), '.') ?>%</td>
                <td><strong><?= money((float)$c['amount']) ?></strong></td>
                <td><span class="badge badge-<?= $statusColors[$st] ?? 'secondary' ?>"><?= $statusLabels[$st] ?? $st ?></span></td>
                <td style="font-size:12px;"><?= !empty($c['created_at']) ? date('d/m/Y', strtotime($c['created_at'])) : '' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    <div style="margin-top:12px;"><a href="/admin/agencias/comissoes" class="btn btn-outline btn-sm">Gerenciar todas as comissões</a></div>
</div>

<script>
function copyRefLink(){
    var el = document.getElementById('refLink');
    el.select();
    document.execCommand('copy');
    alert('Link copiado!');
}
</script>
