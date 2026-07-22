<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Bem-vindo(a), <?= e($user['first_name']) ?>!</h2>

        <div class="account-cards">
            <div class="account-card">
                <h4>Reservas Recentes</h4>
                <?php if (!empty($recentBookings['items'])): ?>
                <table class="table table-sm">
                    <thead><tr><th>Número</th><th>Data</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentBookings['items'] as $b): ?>
                    <tr>
                        <td><a href="/minha-conta/reservas/<?= (int)$b['id'] ?>"><?= e($b['booking_number']) ?></a></td>
                        <td><?= format_date($b['created_at']) ?></td>
                        <td><?= money((float)$b['total']) ?></td>
                        <td><span class="badge badge-<?= booking_status_class($b['status']) ?>"><?= booking_status_label($b['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p>Nenhuma reserva ainda. <a href="/passeios">Explore nossos passeios!</a></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
