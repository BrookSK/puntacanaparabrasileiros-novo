<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Minhas Reservas</h2>
        <?php if (empty($bookings['items'])): ?>
        <div class="empty-state"><p>Nenhuma reserva encontrada.</p><a href="/passeios" class="btn btn-primary">Ver Passeios</a></div>
        <?php else: ?>
        <table class="table">
            <thead><tr><th>Número</th><th>Data</th><th>Total</th><th>Pago</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
            <?php foreach ($bookings['items'] as $b): ?>
            <tr>
                <td><a href="/minha-conta/reservas/<?= (int)$b['id'] ?>"><?= e($b['booking_number']) ?></a></td>
                <td><?= format_date($b['created_at']) ?></td>
                <td><?= money((float)$b['total']) ?></td>
                <td><?= money((float)$b['paid_amount']) ?></td>
                <td><span class="badge badge-<?= booking_status_class($b['status']) ?>"><?= booking_status_label($b['status']) ?></span></td>
                <td><a href="/minha-conta/reservas/<?= (int)$b['id'] ?>" class="btn btn-sm btn-outline">Detalhes</a></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($bookings['total_pages'] > 1): ?>
        <nav class="pagination">
            <?php for ($i = 1; $i <= $bookings['total_pages']; $i++): ?>
            <a href="?page=<?= $i ?>" class="page-link <?= $i === $bookings['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
        </nav>
        <?php endif; ?>
        <?php endif; ?>
    </div>
</div>
