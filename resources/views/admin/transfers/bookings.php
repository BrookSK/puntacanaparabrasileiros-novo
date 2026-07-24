<div class="card-header">
    <div class="header-actions">
        <a href="/admin/transfers/veiculos" class="btn btn-outline">&larr; Veículos</a>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Rota</th>
            <th>Data</th>
            <th>Passageiros</th>
            <th>Valor</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($bookings['data'])): ?>
        <tr>
            <td colspan="7" class="text-center">Nenhuma reserva de transfer encontrada.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($bookings['data'] as $booking): ?>
        <tr>
            <td><?= (int)$booking['id'] ?></td>
            <td><?= e($booking['customer_name'] ?? '-') ?></td>
            <td><?= e(($booking['origin_title'] ?? '?') . ' → ' . ($booking['destination_title'] ?? '?')) ?></td>
            <td><?= e($booking['transfer_date'] ?? '-') ?></td>
            <td><?= (int)($booking['adults'] ?? 0) + (int)($booking['children'] ?? 0) ?></td>
            <td>$<?= number_format((float)($booking['total_price'] ?? 0), 2) ?></td>
            <td>
                <span class="badge badge-<?= ($booking['status'] ?? '') === 'confirmed' ? 'success' : (($booking['status'] ?? '') === 'cancelled' ? 'danger' : 'warning') ?>">
                    <?= e($booking['status'] ?? 'pending') ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
