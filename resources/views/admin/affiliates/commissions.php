<div class="card-header">
    <div class="header-actions">
        <a href="/admin/afiliados" class="btn btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar para Afiliados
        </a>
    </div>
    <form method="GET" class="filter-form">
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="pending" <?= ($currentStatus ?? '') === 'pending' ? 'selected' : '' ?>>Pendentes</option>
            <option value="paid" <?= ($currentStatus ?? '') === 'paid' ? 'selected' : '' ?>>Pagas</option>
            <option value="cancelled" <?= ($currentStatus ?? '') === 'cancelled' ? 'selected' : '' ?>>Canceladas</option>
        </select>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Afiliado</th>
            <th>Reserva</th>
            <th>Valor</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($commissions['data'])): ?>
        <tr>
            <td colspan="6" class="text-center">Nenhuma comissão encontrada.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($commissions['data'] as $comm): ?>
        <tr>
            <td><?= e($comm['affiliate_name'] ?? 'Afiliado #' . ($comm['affiliate_id'] ?? '?')) ?></td>
            <td>
                <?php if (!empty($comm['booking_id'])): ?>
                <a href="/admin/reservas/<?= (int)$comm['booking_id'] ?>">#<?= (int)$comm['booking_id'] ?></a>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
            <td><strong>$<?= number_format((float)($comm['amount'] ?? 0), 2) ?></strong></td>
            <td>
                <?php
                    $cst = $comm['status'] ?? 'pending';
                    $cstColor = $cst === 'paid' ? 'success' : ($cst === 'cancelled' ? 'danger' : 'warning');
                    $cstLabel = $cst === 'paid' ? 'Pago' : ($cst === 'cancelled' ? 'Cancelado' : 'Pendente');
                ?>
                <span class="badge badge-<?= $cstColor ?>"><?= $cstLabel ?></span>
            </td>
            <td><?= !empty($comm['created_at']) ? date('d/m/Y', strtotime($comm['created_at'])) : '-' ?></td>
            <td class="actions-cell">
                <?php if (($comm['status'] ?? '') === 'pending'): ?>
                <form method="POST" action="/admin/afiliados/comissoes/<?= (int)$comm['id'] ?>/pagar" class="inline-form">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-primary" onclick="return confirm('Marcar como pago?')">Pagar</button>
                </form>
                <?php else: ?>
                <span class="text-muted"><?= e($comm['payout_reference'] ?? '-') ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($commissions['totalPages']) && $commissions['totalPages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $commissions['totalPages']; $p++): ?>
    <a href="?page=<?= $p ?>&status=<?= e($currentStatus ?? 'pending') ?>" class="pagination-btn <?= $p === ($commissions['currentPage'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
