<div class="card-header">
    <div class="header-actions">
        <a href="/admin/reservas/criar" class="btn btn-primary">+ Nova Reserva Manual</a>
    </div>
    <form method="GET" class="filter-form">
        <select name="status" class="form-control" onchange="this.form.submit()">
            <option value="">Todos os Status</option>
            <option value="pending" <?= ($currentStatus ?? '') === 'pending' ? 'selected' : '' ?>>Pendente</option>
            <option value="booked" <?= ($currentStatus ?? '') === 'booked' ? 'selected' : '' ?>>Confirmado</option>
            <option value="partially_paid" <?= ($currentStatus ?? '') === 'partially_paid' ? 'selected' : '' ?>>Parcialmente Pago</option>
            <option value="completed" <?= ($currentStatus ?? '') === 'completed' ? 'selected' : '' ?>>Concluído</option>
            <option value="cancelled" <?= ($currentStatus ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
            <option value="refunded" <?= ($currentStatus ?? '') === 'refunded' ? 'selected' : '' ?>>Reembolsado</option>
        </select>
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nº, e-mail ou nome..." value="<?= e($currentSearch ?? '') ?>">
        <button type="submit" class="btn btn-outline">Filtrar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Nº Reserva</th>
            <th>Cliente</th>
            <th>E-mail</th>
            <th>Total</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($bookings['data'])): ?>
        <tr>
            <td colspan="7" class="text-center">Nenhuma reserva encontrada.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($bookings['data'] as $booking): ?>
        <tr>
            <td><strong><?= e($booking['booking_number'] ?? '-') ?></strong></td>
            <td><?= e(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? '')) ?></td>
            <td><?= e($booking['billing_email'] ?? '-') ?></td>
            <td>$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?></td>
            <td>
                <?php
                    $statusColors = [
                        'pending' => 'warning',
                        'booked' => 'success',
                        'partially_paid' => 'info',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'refunded' => 'secondary',
                    ];
                    $statusLabels = [
                        'pending' => 'Pendente',
                        'booked' => 'Confirmado',
                        'partially_paid' => 'Parc. Pago',
                        'completed' => 'Concluído',
                        'cancelled' => 'Cancelado',
                        'refunded' => 'Reembolsado',
                    ];
                    $st = $booking['status'] ?? 'pending';
                ?>
                <span class="badge badge-<?= $statusColors[$st] ?? 'secondary' ?>"><?= $statusLabels[$st] ?? $st ?></span>
            </td>
            <td><?= !empty($booking['created_at']) ? date('d/m/Y H:i', strtotime($booking['created_at'])) : '-' ?></td>
            <td class="actions-cell">
                <a href="/admin/reservas/<?= (int)$booking['id'] ?>" class="btn btn-sm btn-outline">Ver</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($bookings['totalPages']) && $bookings['totalPages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $bookings['totalPages']; $p++): ?>
    <a href="?page=<?= $p ?>&status=<?= e($currentStatus ?? '') ?>&busca=<?= e($currentSearch ?? '') ?>" class="pagination-btn <?= $p === ($bookings['currentPage'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
