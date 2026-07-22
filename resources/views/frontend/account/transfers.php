<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Meus Transfers</h2>
        <?php if (empty($transfers)): ?>
        <div class="empty-state"><p>Nenhum transfer reservado.</p><a href="/transfers" class="btn btn-primary">Reservar Transfer</a></div>
        <?php else: ?>
        <table class="table">
            <thead><tr><th>Veículo</th><th>Rota</th><th>Data</th><th>Tipo</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($transfers as $t): ?>
            <tr>
                <td><?= e($t['vehicle_title']) ?></td>
                <td><?= e($t['origin_title']) ?> &rarr; <?= e($t['destination_title']) ?></td>
                <td><?= format_date($t['date']) ?> <?= e($t['time']) ?></td>
                <td><?= $t['type'] === 'arrival' ? 'Chegada' : 'Partida' ?></td>
                <td><span class="badge badge-<?= booking_status_class($t['status']) ?>"><?= transfer_status_label($t['status']) ?></span></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
