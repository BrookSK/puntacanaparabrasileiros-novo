<!-- Abas -->
<div class="affiliate-tabs">
    <a href="/admin/agencias?tab=solicitacoes" class="affiliate-tab <?= ($tab ?? '') === 'solicitacoes' ? 'active' : '' ?>">
        Solicitações de Agência
        <?php if (($pendingCount ?? 0) > 0): ?>
        <span class="affiliate-tab-badge"><?= (int)$pendingCount ?></span>
        <?php endif; ?>
    </a>
    <a href="/admin/agencias?tab=ativas" class="affiliate-tab <?= ($tab ?? '') === 'ativas' ? 'active' : '' ?>">
        Agências Ativas
        <span class="affiliate-tab-count">(<?= (int)($activeCount ?? 0) ?>)</span>
    </a>
    <a href="/admin/agencias?tab=bloqueadas" class="affiliate-tab <?= ($tab ?? '') === 'bloqueadas' ? 'active' : '' ?>">
        Bloqueadas
        <span class="affiliate-tab-count">(<?= (int)($blockedCount ?? 0) ?>)</span>
    </a>
    <a href="/admin/agencias/comissoes" class="affiliate-tab">Comissões</a>
    <a href="/admin/agencias/criar" class="affiliate-tab" style="margin-left:auto;">+ Nova Agência</a>
</div>

<?php if (($tab ?? '') === 'solicitacoes'): ?>
<!-- Tab: Solicitações Pendentes -->
<table class="table">
    <thead>
        <tr>
            <th>Agência</th>
            <th>Contato</th>
            <th>Email</th>
            <th>WhatsApp</th>
            <th>Cidade</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($requests['items'])): ?>
        <tr><td colspan="7" class="text-center" style="padding:30px;color:#94a3b8;">Nenhuma solicitação pendente.</td></tr>
        <?php else: ?>
        <?php foreach ($requests['items'] as $req): ?>
        <tr>
            <td><strong><?= e($req['company_name']) ?></strong><?php if (!empty($req['trade_name'])): ?><br><small style="color:#636e72;"><?= e($req['trade_name']) ?></small><?php endif; ?></td>
            <td><?= e($req['contact_name'] ?? '-') ?></td>
            <td><?= e($req['email']) ?></td>
            <td><?= function_exists('phone_with_flag') ? phone_with_flag($req['phone'] ?? '') : e($req['phone'] ?? '-') ?></td>
            <td><?= e($req['city'] ?? '-') ?></td>
            <td><?= date('d/m/Y', strtotime($req['created_at'])) ?></td>
            <td class="actions-cell" style="display:flex;gap:6px;flex-wrap:wrap;">
                <a href="/admin/agencias/solicitacao/<?= (int)$req['id'] ?>" class="btn btn-sm btn-primary">Ver Detalhes</a>
                <form method="POST" action="/admin/agencias/solicitacao/<?= (int)$req['id'] ?>/aprovar" style="display:inline;" onsubmit="return confirm('Aprovar a agência <?= e($req['company_name']) ?>? Será criado o acesso ao painel.')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-success">Aprovar</button>
                </form>
                <button type="button" class="btn btn-sm btn-danger" data-req-id="<?= (int)$req['id'] ?>" data-req-name="<?= e($req['company_name']) ?>" onclick="openAgencyReject(this.dataset.reqId, this.dataset.reqName)">Recusar</button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($requests['total_pages']) && $requests['total_pages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $requests['total_pages']; $p++): ?>
    <a href="?tab=solicitacoes&page=<?= $p ?>" class="page-link <?= $p === ($requests['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Modal de recusa -->
<div id="agencyRejectModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:10px;padding:24px;max-width:440px;width:90%;">
        <h3 style="margin:0 0 12px;font-size:18px;">Recusar solicitação</h3>
        <p style="font-size:14px;color:#64748b;margin-bottom:14px;">Informe o motivo da recusa de <strong id="agencyRejectName"></strong>. A agência será notificada.</p>
        <form id="agencyRejectForm" method="POST">
            <?= csrf_field() ?>
            <textarea name="block_reason" rows="3" required class="form-control" placeholder="Motivo da recusa" style="width:100%;padding:10px;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:14px;"></textarea>
            <div style="display:flex;gap:8px;justify-content:flex-end;">
                <button type="button" class="btn btn-outline" onclick="document.getElementById('agencyRejectModal').style.display='none'">Cancelar</button>
                <button type="submit" class="btn btn-danger">Recusar</button>
            </div>
        </form>
    </div>
</div>
<script>
function openAgencyReject(id, name){
    document.getElementById('agencyRejectName').textContent = name;
    document.getElementById('agencyRejectForm').action = '/admin/agencias/solicitacao/' + id + '/recusar';
    document.getElementById('agencyRejectModal').style.display = 'flex';
}
</script>

<?php elseif (($tab ?? '') === 'bloqueadas'): ?>
<!-- Tab: Agências Bloqueadas/Inativas -->
<table class="table">
    <thead>
        <tr><th>Agência</th><th>Contato</th><th>Email</th><th>Código</th><th>Status</th><th>Ações</th></tr>
    </thead>
    <tbody>
        <?php if (empty($blocked['items'])): ?>
        <tr><td colspan="6" class="text-center" style="padding:30px;color:#94a3b8;">Nenhuma agência bloqueada.</td></tr>
        <?php else: ?>
        <?php foreach ($blocked['items'] as $a): ?>
        <tr>
            <td><strong><?= e($a['trade_name'] ?: $a['company_name']) ?></strong></td>
            <td><?= e($a['contact_name'] ?? '-') ?></td>
            <td><?= e($a['email'] ?? '-') ?></td>
            <td><span class="badge badge-info"><?= e($a['ref_code']) ?></span></td>
            <td><span class="badge badge-secondary">Inativa</span></td>
            <td class="actions-cell" style="white-space:nowrap;">
                <a href="/admin/agencias/<?= (int)$a['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php else: ?>
<!-- Tab: Agências Ativas -->
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
        <tr><td colspan="7" class="text-center" style="padding:30px;color:#94a3b8;">Nenhuma agência ativa.</td></tr>
        <?php else: ?>
        <?php foreach ($agencies['items'] as $a): ?>
        <tr>
            <td>
                <strong><?= e($a['trade_name'] ?: $a['company_name']) ?></strong>
                <?php if (!empty($a['trade_name'])): ?><br><small style="color:#636e72;"><?= e($a['company_name']) ?></small><?php endif; ?>
                <?php if (!empty($a['contact_name'])): ?><br><small style="color:#636e72;">Contato: <?= e($a['contact_name']) ?></small><?php endif; ?>
            </td>
            <td style="font-size:13px;"><?= e($a['cnpj'] ?: '—') ?></td>
            <td><span class="badge badge-info"><?= e($a['ref_code']) ?></span></td>
            <td><?= rtrim(rtrim(number_format((float)$a['commission_rate'], 2), '0'), '.') ?>%</td>
            <td style="font-size:13px;"><?= money((float)$a['total_sales']) ?><br><small style="color:#636e72;">comissão: <?= money((float)$a['total_commission']) ?></small></td>
            <td><span class="badge badge-success">Ativa</span></td>
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
    <a href="?tab=ativas&page=<?= $p ?>" class="page-link <?= $p === ($agencies['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
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
