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
    <a href="/admin/afiliados/criativos" class="affiliate-tab">Criativos</a>
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
            <td><strong><?= e($req['first_name'] . ' ' . $req['last_name']) ?></strong></td>
            <td><?= e($req['email']) ?></td>
            <td><?= phone_with_flag($req['phone'] ?? '') ?></td>
            <td><span class="badge badge-info"><?= e($req['followers_count'] ?? '-') ?></span></td>
            <td><?= e(ucfirst($req['niche'] ?? '-')) ?></td>
            <td><?= date('d/m/Y', strtotime($req['created_at'])) ?></td>
            <td class="actions-cell" style="display:flex;gap:6px;flex-wrap:wrap;">
                <a href="/admin/afiliados/solicitacao/<?= (int)$req['id'] ?>" class="btn btn-sm btn-primary">Ver Detalhes</a>
                <button type="button" class="btn btn-sm btn-danger" onclick="openBlockModal('request', <?= (int)$req['id'] ?>, '<?= e($req['first_name'] . ' ' . $req['last_name']) ?>')">Bloquear</button>
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
                <button type="button" class="btn btn-sm btn-danger" onclick="openBlockModal('affiliate', <?= (int)$aff['id'] ?>, '<?= e(($aff['first_name'] ?? '') . ' ' . ($aff['last_name'] ?? '')) ?>')">Bloquear</button>
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
            <th>Motivo</th>
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
                    <span class="badge badge-warning">Afiliado</span>
                <?php else: ?>
                    <span class="badge badge-secondary">Solicitação</span>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!empty($aff['block_reason'])): ?>
                    <span style="font-size:13px;color:#334155;"><?= e($aff['block_reason']) ?></span>
                <?php else: ?>
                    <span style="color:#94a3b8;font-size:12px;">Sem motivo registrado</span>
                <?php endif; ?>
            </td>
            <td class="actions-cell" style="display:flex;gap:6px;">
                <form method="POST" action="/admin/afiliados/<?= (int)$aff['id'] ?>/reativar" class="inline-form js-confirm" data-msg="Reativar este registro?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source" value="<?= e($aff['source'] ?? 'request') ?>">
                    <button class="btn btn-sm btn-primary">Reativar</button>
                </form>
                <form method="POST" action="/admin/afiliados/solicitacao/<?= (int)$aff['id'] ?>/excluir" class="inline-form js-confirm" data-msg="Excluir permanentemente este registro?">
                    <?= csrf_field() ?>
                    <input type="hidden" name="source" value="<?= e($aff['source'] ?? 'request') ?>">
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
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

<!-- Modal de Bloqueio -->
<div id="blockModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 id="blockModalTitle">Bloquear</h3>
            <button type="button" class="modal-close" onclick="closeBlockModal()">&times;</button>
        </div>
        <form id="blockForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="modal-body">
                <p style="margin-bottom:16px;color:#64748b;">Informe o motivo do bloqueio:</p>
                <textarea id="blockReason" name="block_reason" class="form-control" rows="4" placeholder="Motivo do bloqueio (obrigatório)..." required></textarea>
                <p id="blockReasonError" style="color:#ef4444;font-size:12px;margin-top:6px;display:none;">O motivo do bloqueio é obrigatório.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeBlockModal()">Cancelar</button>
                <button type="submit" class="btn btn-danger" id="blockConfirmBtn">Confirmar bloqueio</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal de Motivo -->
<div id="reasonModal" class="modal-overlay" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Motivo do Bloqueio</h3>
            <button type="button" class="modal-close" onclick="closeReasonModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p id="reasonText" style="color:#334155;line-height:1.7;white-space:pre-wrap;"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeReasonModal()">Fechar</button>
        </div>
    </div>
</div>

<style>
.affiliate-tabs { display: flex; gap: 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 24px; }
.affiliate-tab { display: inline-flex; align-items: center; gap: 8px; padding: 14px 24px; font-size: 14px; font-weight: 600; color: #64748b; text-decoration: none; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all .2s; }
.affiliate-tab:hover { color: #334155; }
.affiliate-tab.active { color: var(--primary); border-bottom-color: var(--primary); }
.affiliate-tab-badge { background: #ef4444; color: #fff; font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 10px; min-width: 20px; text-align: center; }
.affiliate-tab-count { font-size: 12px; color: #94a3b8; font-weight: 400; }

/* Modal */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; z-index: 9999; }
.modal-content { background: #fff; border-radius: 12px; width: 100%; max-width: 480px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); }
.modal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 24px; border-bottom: 1px solid #e5e7eb; }
.modal-header h3 { margin: 0; font-size: 18px; color: #1a1a1a; }
.modal-close { background: none; border: none; font-size: 24px; color: #94a3b8; cursor: pointer; padding: 0; line-height: 1; }
.modal-close:hover { color: #334155; }
.modal-body { padding: 24px; }
.modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #e5e7eb; }

.btn-outline { background: transparent; border: 1px solid #cbd5e1; color: #475569; padding: 4px 12px; border-radius: 6px; font-size: 12px; cursor: pointer; transition: all .2s; }
.btn-outline:hover { background: #f1f5f9; border-color: #94a3b8; }
.btn-secondary { background: #f1f5f9; border: 1px solid #cbd5e1; color: #475569; padding: 8px 16px; border-radius: 6px; font-size: 13px; font-weight: 600; cursor: pointer; }
.btn-secondary:hover { background: #e2e8f0; }
</style>

<script>
function openBlockModal(source, id, name) {
    var modal = document.getElementById('blockModal');
    var form = document.getElementById('blockForm');
    var title = document.getElementById('blockModalTitle');
    var reason = document.getElementById('blockReason');
    var errorMsg = document.getElementById('blockReasonError');

    // Definir action do form baseado na source
    if (source === 'request') {
        form.action = '/admin/afiliados/solicitacao/' + id + '/recusar';
        title.textContent = 'Bloquear Solicitação';
    } else {
        form.action = '/admin/afiliados/' + id + '/suspender';
        title.textContent = 'Bloquear Afiliado';
    }

    reason.value = '';
    errorMsg.style.display = 'none';
    modal.style.display = 'flex';
    reason.focus();
}

function closeBlockModal() {
    document.getElementById('blockModal').style.display = 'none';
}

function showReason(btn) {
    var reason = btn.getAttribute('data-reason');
    document.getElementById('reasonText').textContent = reason;
    document.getElementById('reasonModal').style.display = 'flex';
}

function closeReasonModal() {
    document.getElementById('reasonModal').style.display = 'none';
}

// Validação do formulário de bloqueio
document.getElementById('blockForm').addEventListener('submit', function(e) {
    var reason = document.getElementById('blockReason').value.trim();
    var errorMsg = document.getElementById('blockReasonError');
    if (!reason) {
        e.preventDefault();
        errorMsg.style.display = 'block';
        document.getElementById('blockReason').focus();
        return false;
    }
    errorMsg.style.display = 'none';
});

// Fechar modais ao clicar fora
document.getElementById('blockModal').addEventListener('click', function(e) {
    if (e.target === this) closeBlockModal();
});
document.getElementById('reasonModal').addEventListener('click', function(e) {
    if (e.target === this) closeReasonModal();
});

// Fechar modais com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeBlockModal();
        closeReasonModal();
    }
});

// Confirmação única para forms com classe js-confirm
document.querySelectorAll('.js-confirm').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        var msg = form.getAttribute('data-msg') || 'Tem certeza?';
        if (!confirm(msg)) {
            e.preventDefault();
            e.stopImmediatePropagation();
        }
    });
});
</script>
