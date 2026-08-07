<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Cancelamentos</h2>
        <p class="account-subtitle">Gerencie suas reservas e solicite cancelamentos quando necessário.</p>

        <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <h3>Nenhuma reserva encontrada</h3>
            <p>Você ainda não tem reservas para gerenciar.</p>
            <a href="/passeios" class="btn btn-primary">Explorar Passeios</a>
        </div>
        <?php else: ?>

        <!-- Grid de cancelamentos em 2 colunas -->
        <div class="cancel-grid">
            <?php foreach ($bookings as $booking): ?>
            <?php
            $statusLabel = match($booking['status']) {
                'cancelled' => 'Cancelamento Solicitado',
                'refunded' => 'Reembolsado',
                'completed' => 'Concluído',
                'booked' => 'Confirmado',
                'pending' => 'Pendente',
                'partially_paid' => 'Parcialmente Pago',
                default => ucfirst($booking['status']),
            };
            $statusClass = match($booking['status']) {
                'cancelled' => 'danger',
                'refunded' => 'purple',
                'completed' => 'success',
                'booked' => 'info',
                'pending' => 'warning',
                default => 'secondary',
            };
            ?>
            <div class="cancel-item">
                <div class="cancel-item-header">
                    <h4 class="cancel-item-title"><?= e($booking['trip_title']) ?></h4>
                    <span class="badge badge-<?= $statusClass ?>"><?= $statusLabel ?></span>
                </div>

                <div class="cancel-item-details">
                    <div class="cancel-item-row">
                        <span class="cancel-item-label">Reserva</span>
                        <span class="cancel-item-value">#<?= (int)$booking['id'] ?></span>
                    </div>
                    <div class="cancel-item-row">
                        <span class="cancel-item-label">Data do Passeio</span>
                        <span class="cancel-item-value"><?= $booking['trip_date'] ? format_date($booking['trip_date']) : 'Não definida' ?></span>
                    </div>
                    <div class="cancel-item-row">
                        <span class="cancel-item-label">ID do Passeio</span>
                        <span class="cancel-item-value">#<?= (int)$booking['trip_id'] ?></span>
                    </div>
                </div>

                <div class="cancel-item-footer">
                    <?php if ($booking['status'] === 'cancelled'): ?>
                    <span class="cancel-item-msg cancel-item-msg--warning">Cancelamento já solicitado</span>
                    <?php elseif ($booking['status'] === 'refunded'): ?>
                    <span class="cancel-item-msg cancel-item-msg--success">Reembolsado com sucesso</span>
                    <?php elseif ($booking['status'] === 'completed'): ?>
                    <span class="cancel-item-msg cancel-item-msg--info">Passeio já realizado</span>
                    <?php elseif (in_array($booking['status'], ['booked', 'pending', 'partially_paid'])): ?>
                    <form method="POST" action="/minha-conta/cancelamentos/solicitar" onsubmit="return confirm('Tem certeza que deseja solicitar o cancelamento desta reserva?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                        <button type="submit" class="btn btn-danger">Solicitar Cancelamento</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
