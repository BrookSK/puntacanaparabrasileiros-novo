<div class="card-header">
    <div class="header-actions" style="display:flex;gap:8px;align-items:center;">
        <?php if ($pendingCount > 0): ?>
        <span class="badge badge-warning" style="font-size:13px;padding:6px 14px;"><?= $pendingCount ?> pendente<?= $pendingCount > 1 ? 's' : '' ?></span>
        <?php endif; ?>
        <?php if (function_exists('is_manager') && is_manager()): ?>
        <a href="/admin/cancelamentos/regras" class="btn btn-outline"><svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px;margin-right:6px;"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>Regras de Reembolso</a>
        <?php endif; ?>
    </div>
    <form method="GET" class="filter-form">
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Todos os Status</option>
            <option value="pending" <?= ($currentStatus ?? '') === 'pending' ? 'selected' : '' ?>>Pendentes</option>
            <option value="approved" <?= ($currentStatus ?? '') === 'approved' ? 'selected' : '' ?>>Aprovados</option>
            <option value="rejected" <?= ($currentStatus ?? '') === 'rejected' ? 'selected' : '' ?>>Rejeitados</option>
        </select>
        <button type="submit" class="btn btn-outline">Filtrar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Reserva</th>
            <th>Motivo</th>
            <th>Status</th>
            <th>Reembolso</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($cancellations['items'])): ?>
        <tr>
            <td colspan="8" class="text-center">Nenhuma solicitação de cancelamento encontrada.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($cancellations['items'] as $c): ?>
        <tr>
            <td><strong>#<?= (int)$c['id'] ?></strong></td>
            <td>
                <span style="font-size:13px;"><?= e(($c['user_first_name'] ?? '') . ' ' . ($c['user_last_name'] ?? '')) ?></span>
                <br><small style="color:#636e72;"><?= e($c['user_email'] ?? '') ?></small>
            </td>
            <td><strong><?= e($c['booking_number'] ?? '-') ?></strong></td>
            <td style="max-width:200px;font-size:12px;color:#374151;"><?= e(mb_strimwidth($c['reason'] ?? '', 0, 80, '...')) ?></td>
            <td>
                <?php
                $statusColors = ['pending' => 'warning', 'approved' => 'success', 'rejected' => 'danger'];
                $statusLabels = ['pending' => 'Pendente', 'approved' => 'Aprovado', 'rejected' => 'Rejeitado'];
                $st = $c['status'] ?? 'pending';
                ?>
                <span class="badge badge-<?= $statusColors[$st] ?? 'secondary' ?>"><?= $statusLabels[$st] ?? $st ?></span>
            </td>
            <td>
                <?php if ($c['refund_status'] === 'refunded'): ?>
                <span class="badge badge-info">$<?= number_format((float)($c['refund_amount'] ?? 0), 2) ?></span>
                <?php elseif ($c['status'] === 'approved' && $c['refund_status'] === 'none'): ?>
                <span class="badge badge-warning">Pendente</span>
                <?php else: ?>
                <span style="color:#aaa;font-size:12px;">—</span>
                <?php endif; ?>
            </td>
            <td style="font-size:12px;"><?= !empty($c['created_at']) ? date('d/m/Y H:i', strtotime($c['created_at'])) : '-' ?></td>
            <td class="actions-cell">
                <a href="/admin/cancelamentos/<?= (int)$c['id'] ?>" class="btn btn-sm btn-outline">Gerenciar</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($cancellations['total_pages']) && $cancellations['total_pages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $cancellations['total_pages']; $p++): ?>
    <a href="?page=<?= $p ?>&status=<?= e($currentStatus ?? '') ?>" class="pagination-btn <?= $p === ($cancellations['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
