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
                <form method="POST" action="/admin/afiliados/comissoes/<?= (int)$comm['id'] ?>/pagar" class="inline-form" onsubmit="return confirm('Marcar como paga?')" style="display:inline;">
                    <?= csrf_field() ?>
                    <input type="text" name="payout_reference" placeholder="Ref. pagamento" class="form-control" style="width:120px;display:inline-block;font-size:11px;padding:5px 8px;margin-right:4px;">
                    <button class="btn btn-sm btn-success">Pagar</button>
                </form>
                <button type="button" class="btn btn-sm btn-danger" onclick="openCancelModal(<?= (int)$comm['id'] ?>)" style="margin-left:4px;">Cancelar</button>
                <?php elseif ($cst === 'paid'): ?>
                <span style="font-size:11px;color:#64748b;"><?= e($comm['payout_reference'] ?? '-') ?></span>
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
<div id="cancelModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#1e293b;border-radius:12px;padding:30px;width:100%;max-width:450px;box-shadow:0 20px 60px rgba(0,0,0,0.5);">
        <h3 style="margin:0 0 15px;color:#f1f5f9;font-size:18px;">Cancelar Comissão</h3>
        <p style="color:#94a3b8;margin:0 0 15px;font-size:14px;">Informe o motivo do cancelamento:</p>
        <form id="cancelForm" method="POST" action="">
            <?= csrf_field() ?>
            <textarea name="reason" id="cancelReason" rows="4" class="form-control" style="width:100%;resize:vertical;margin-bottom:15px;" placeholder="Motivo do cancelamento..." required></textarea>
            <div style="display:flex;gap:10px;justify-content:flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeCancelModal()">Fechar</button>
                <button type="submit" class="btn btn-danger">Confirmar Cancelamento</button>
            </div>
        </form>
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

// Fechar modal ao clicar fora
document.getElementById('cancelModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCancelModal();
    }
});
</script>
