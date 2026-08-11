<!-- Abas -->
<div class="affiliate-tabs">
    <a href="/admin/afiliados?tab=solicitacoes" class="affiliate-tab <?= ($tab ?? '') === 'solicitacoes' ? 'active' : '' ?>">
        Solicitações de Afiliação
        <?php if ($pendingCount > 0): ?>
        <span class="affiliate-tab-badge"><?= $pendingCount ?></span>
        <?php endif; ?>
    </a>
    <a href="/admin/afiliados?tab=ativos" class="affiliate-tab <?= ($tab ?? '') === 'ativos' ? 'active' : '' ?>">
        Afiliados Ativos
        <span class="affiliate-tab-count">(<?= $activeCount ?>)</span>
    </a>
    <a href="/admin/afiliados?tab=bloqueados" class="affiliate-tab <?= ($tab ?? '') === 'bloqueados' ? 'active' : '' ?>">
        Bloqueados
        <span class="affiliate-tab-count">(<?= $blockedCount ?>)</span>
    </a>
    <a href="/admin/afiliados/comissoes" class="affiliate-tab">Comissões</a>
</div>

<?php if (($tab ?? '') === 'solicitacoes'): ?>
<!-- Tab: Solicitações Pendentes -->
<table class="table">
    <thead>
        <tr>
            <th>Nome</th>
            <th>Email</th>
            <th>WhatsApp</th>
            <th>Seguidores</th>
            <th>Nicho</th>
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
            <td><strong><?= e($req['first_name'] . ' ' . $req['last_name']) ?></strong><br><small style="color:#94a3b8;">@<?= e($req['username'] ?? '') ?></small></td>
            <td><?= e($req['email']) ?></td>
            <td><?= e($req['phone']) ?></td>
            <td><span class="badge badge-info"><?= e($req['followers_count'] ?? '-') ?></span></td>
            <td><?= e(ucfirst($req['niche'] ?? '-')) ?></td>
            <td><?= date('d/m/Y', strtotime($req['created_at'])) ?></td>
            <td class="actions-cell">
                <a href="/admin/afiliados/solicitacao/<?= (int)$req['id'] ?>" class="btn btn-sm btn-primary">Ver Detalhes</a>
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

<?php elseif (($tab ?? '') === 'ativos'): ?>
<!-- Tab: Afiliados Ativos -->
<table class="table">
    <thead>
        <tr>
            <th>Afiliado</th>
            <th>Email</th>
            <th>Comissão (%)</th>
            <th>Total Vendas</th>
            <th>Total Ganhos</th>
            <th>Total Pago</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($affiliates['items'])): ?>
        <tr><td colspan="7" class="text-center" style="padding:30px;color:#94a3b8;">Nenhum afiliado ativo.</td></tr>
        <?php else: ?>
        <?php foreach ($affiliates['items'] as $aff): ?>
        <tr>
            <td><strong><?= e(($aff['first_name'] ?? '') . ' ' . ($aff['last_name'] ?? '')) ?></strong></td>
            <td><?= e($aff['email'] ?? '') ?></td>
            <td><?= number_format((float)($aff['commission_rate'] ?? 20), 1) ?>%</td>
            <td><?= money((float)($aff['total_sales'] ?? 0)) ?></td>
            <td><?= money((float)($aff['total_earnings'] ?? 0)) ?></td>
            <td><?= money((float)($aff['total_paid'] ?? 0)) ?></td>
            <td class="actions-cell">
                <form method="POST" action="/admin/afiliados/<?= (int)$aff['id'] ?>/suspender" class="inline-form" onsubmit="return confirm('Bloquear este afiliado?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger">Bloquear</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php elseif (($tab ?? '') === 'bloqueados'): ?>
<!-- Tab: Bloqueados -->
<table class="table">
    <thead>
        <tr>
            <th>Afiliado</th>
            <th>Email</th>
            <th>Origem</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($blocked['items'])): ?>
        <tr><td colspan="5" class="text-center" style="padding:30px;color:#94a3b8;">Nenhum afiliado bloqueado.</td></tr>
        <?php else: ?>
        <?php foreach ($blocked['items'] as $aff): ?>
        <tr>
            <td><strong><?= e(($aff['first_name'] ?? '') . ' ' . ($aff['last_name'] ?? '')) ?></strong></td>
            <td><?= e($aff['email'] ?? '') ?></td>
            <td>
                <?php if (($aff['source'] ?? '') === 'affiliate'): ?>
                    <span class="badge badge-warning">Afiliado Bloqueado</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Solicitação Recusada</span>
                <?php endif; ?>
            </td>
            <td><span class="badge badge-danger">Bloqueado</span></td>
            <td class="actions-cell">
                <?php if (($aff['source'] ?? '') === 'affiliate'): ?>
                <form method="POST" action="/admin/afiliados/<?= (int)$aff['id'] ?>/reativar" class="inline-form">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-primary">Reativar</button>
                </form>
                <?php else: ?>
                <form method="POST" action="/admin/afiliados/solicitacao/<?= (int)$aff['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir permanentemente esta solicitação?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($blocked['total_pages']) && $blocked['total_pages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $blocked['total_pages']; $p++): ?>
    <a href="?tab=bloqueados&page=<?= $p ?>" class="page-link <?= $p === ($blocked['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
<?php endif; ?>

<style>
.affiliate-tabs { display: flex; gap: 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; }
.affiliate-tab { display: inline-flex; align-items: center; gap: 8px; padding: 14px 24px; font-size: 14px; font-weight: 600; color: #64748b; text-decoration: none; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all .2s; }
.affiliate-tab:hover { color: #334155; }
.affiliate-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
.affiliate-tab-badge { background: #ef4444; color: #fff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 10px; min-width: 20px; text-align: center; }
.affiliate-tab-count { font-size: 12px; color: #94a3b8; font-weight: 400; }
</style>
