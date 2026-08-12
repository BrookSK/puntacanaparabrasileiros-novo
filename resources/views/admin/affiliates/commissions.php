<div class="card-header">
    <div class="header-actions">
        <a href="/admin/afiliados" class="btn btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Afiliados
        </a>
    </div>
</div>

<!-- Tabs de Status -->
<div class="settings-tabs" style="margin-bottom:20px;">
    <a href="/admin/afiliados/comissoes?status=all" class="tab-btn <?= ($currentStatus ?? '') === 'all' ? 'active' : '' ?>">Todas</a>
    <a href="/admin/afiliados/comissoes?status=pending" class="tab-btn <?= ($currentStatus ?? '') === 'pending' ? 'active' : '' ?>">Pendentes</a>
    <a href="/admin/afiliados/comissoes?status=paid" class="tab-btn <?= ($currentStatus ?? '') === 'paid' ? 'active' : '' ?>">Pagas</a>
    <a href="/admin/afiliados/comissoes?status=rejected" class="tab-btn <?= ($currentStatus ?? '') === 'rejected' ? 'active' : '' ?>">Canceladas</a>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Afiliado</th>
            <th>Reserva</th>
            <th>Valor Base</th>
            <th>Comissão</th>
            <th>Taxa</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($commissions)): ?>
        <tr>
            <td colspan="8" style="text-align:center;padding:30px;color:#94a3b8;">Nenhuma comissão encontrada.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($commissions as $comm): ?>
        <tr>
            <td>
                <strong><?= e($comm['affiliate_name'] ?? 'Afiliado #' . ($comm['affiliate_id'] ?? '?')) ?></strong>
                <?php if (!empty($comm['affiliate_email'])): ?>
                <br><small style="color:#94a3b8;"><?= e($comm['affiliate_email']) ?></small>
                <?php endif; ?>
            </td>
            <td>
                <?php if (!empty($comm['booking_id'])): ?>
                <a href="/admin/reservas/<?= (int)$comm['booking_id'] ?>">#<?= (int)$comm['booking_id'] ?></a>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
            <td>$<?= number_format((float)($comm['base_amount'] ?? 0), 2) ?></td>
            <td><strong style="color:#16a34a;">$<?= number_format((float)($comm['amount'] ?? 0), 2) ?></strong></td>
            <td><?= number_format((float)($comm['rate'] ?? 0), 0) ?>%</td>
            <td>
                <?php
                    $cst = $comm['status'] ?? 'pending';
                    $statusMap = [
                        'pending' => ['warning', 'Pendente'],
                        'approved' => ['info', 'Aprovada'],
                        'paid' => ['success', 'Paga'],
                        'rejected' => ['danger', 'Cancelada'],
                    ];
                    $badge = $statusMap[$cst] ?? ['secondary', ucfirst($cst)];
                ?>
                <span class="badge badge-<?= $badge[0] ?>"><?= $badge[1] ?></span>
                <?php if ($cst === 'paid' && !empty($comm['paid_at'])): ?>
                <br><small style="color:#94a3b8;"><?= date('d/m/Y', strtotime($comm['paid_at'])) ?></small>
                <?php endif; ?>
            </td>
            <td><?= !empty($comm['created_at']) ? date('d/m/Y H:i', strtotime($comm['created_at'])) : '-' ?></td>
            <td class="actions-cell">
                <?php if ($cst === 'pending'): ?>
                <button type="button" class="btn btn-sm btn-success" onclick="openPayModal(<?= (int)$comm['id'] ?>)">Pagar</button>
                <button type="button" class="btn btn-sm btn-danger" onclick="openCancelModal(<?= (int)$comm['id'] ?>)" style="margin-left:4px;">Cancelar</button>
                <?php elseif ($cst === 'paid'): ?>
                <span style="font-size:11px;color:#64748b;"><?= e($comm['payout_reference'] ?? '-') ?></span>
                <?php elseif ($cst === 'rejected' && !empty($comm['notes'])): ?>
                <button type="button" class="btn btn-sm btn-outline" onclick="showReasonModal('<?= e(addslashes($comm['notes'])) ?>')">Ver motivo</button>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (($totalPages ?? 0) > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
    <a href="?page=<?= $p ?>&status=<?= e($currentStatus ?? 'all') ?>" class="page-link <?= $p === ($currentPage ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<!-- Modal Cancelar Comissão -->
<div class="modal-overlay" id="cancelModal" style="display:none;">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Cancelar Comissão</h3>
            <button class="modal-close" onclick="closeCancelModal()">&times;</button>
        </div>
        <form id="cancelForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="modal-body-scroll">
                <div class="form-group">
                    <label>Motivo do cancelamento *</label>
                    <textarea name="reason" id="cancelReason" class="form-control" rows="4" placeholder="Informe o motivo do cancelamento..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeCancelModal()">Cancelar</button>
                <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pagar Comissão -->
<div class="modal-overlay" id="payModal" style="display:none;">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Registrar Pagamento</h3>
            <button class="modal-close" onclick="closePayModal()">&times;</button>
        </div>
        <form id="payForm" method="POST" action="">
            <?= csrf_field() ?>
            <div class="modal-body-scroll">
                <div class="form-group">
                    <label>Referência do pagamento</label>
                    <input type="text" name="payout_reference" id="payReference" class="form-control" placeholder="Ex: PIX, comprovante, código de transação...">
                    <small>Opcional. Serve para identificar o pagamento depois.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline" onclick="closePayModal()">Cancelar</button>
                <button type="submit" class="btn btn-success">Confirmar Pagamento</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Motivo do Cancelamento -->
<div class="modal-overlay" id="reasonModal" style="display:none;">
    <div class="modal-box modal-sm">
        <div class="modal-header">
            <h3>Motivo do Cancelamento</h3>
            <button class="modal-close" onclick="closeReasonModal()">&times;</button>
        </div>
        <div class="modal-body-scroll">
            <p id="reasonText" style="color:#334155;line-height:1.7;white-space:pre-wrap;margin:0;"></p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeReasonModal()">Fechar</button>
        </div>
    </div>
</div>

<script>
function openCancelModal(commissionId) {
    const modal = document.getElementById('cancelModal');
    const form = document.getElementById('cancelForm');
    form.action = '/admin/afiliados/comissoes/' + commissionId + '/cancelar';
    document.getElementById('cancelReason').value = '';
    modal.style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

function openPayModal(commissionId) {
    const modal = document.getElementById('payModal');
    const form = document.getElementById('payForm');
    form.action = '/admin/afiliados/comissoes/' + commissionId + '/pagar';
    document.getElementById('payReference').value = '';
    modal.style.display = 'flex';
}

function closePayModal() {
    document.getElementById('payModal').style.display = 'none';
}

// Fechar modais ao clicar fora
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) closeCancelModal();
});
document.getElementById('payModal').addEventListener('click', function(e) {
    if (e.target === this) closePayModal();
});
document.getElementById('reasonModal').addEventListener('click', function(e) {
    if (e.target === this) closeReasonModal();
});

// Fechar modais com ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeCancelModal();
        closePayModal();
        closeReasonModal();
    }
});

function showReasonModal(reason) {
    document.getElementById('reasonText').textContent = reason;
    document.getElementById('reasonModal').style.display = 'flex';
}

function closeReasonModal() {
    document.getElementById('reasonModal').style.display = 'none';
}
</script>
