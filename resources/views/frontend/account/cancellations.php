<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Cancelamentos</h2>

        <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <p>Nenhuma reserva encontrada.</p>
            <a href="/passeios" class="btn btn-primary">Ver Passeios</a>
        </div>
        <?php else: ?>
        <div class="cancellations-list">
            <?php foreach ($bookings as $booking): ?>
            <div class="cancellation-card">
                <!-- Barra colorida no topo -->
                <div class="cancellation-bar cancellation-bar-<?= e($booking['status']) ?>"></div>

                <div class="cancellation-card-body">
                    <h3 class="cancellation-title">Passeio: <?= e($booking['trip_title']) ?> #<?= (int)$booking['id'] ?></h3>

                    <div class="cancellation-info">
                        <p><strong>ID do Passeio:</strong> <?= (int)$booking['trip_id'] ?></p>
                        <p><strong>Data de Início:</strong> <?= $booking['trip_date'] ? format_date($booking['trip_date']) : '<span class="text-muted">Data não definida</span>' ?></p>
                    </div>

                    <!-- Status Badge -->
                    <?php
                    $statusLabel = match($booking['status']) {
                        'cancelled' => 'CANCELAMENTO SOLICITADO',
                        'refunded' => 'REEMBOLSADO',
                        'completed' => 'COMPLETO',
                        'booked' => 'CONFIRMADO',
                        'pending' => 'PENDENTE',
                        'partially_paid' => 'PARCIALMENTE PAGO',
                        default => strtoupper($booking['status']),
                    };
                    $statusClass = match($booking['status']) {
                        'cancelled' => 'cancellation-badge-red',
                        'refunded' => 'cancellation-badge-purple',
                        'completed' => 'cancellation-badge-green',
                        'booked' => 'cancellation-badge-blue',
                        'pending' => 'cancellation-badge-yellow',
                        default => 'cancellation-badge-gray',
                    };
                    ?>
                    <span class="cancellation-badge <?= $statusClass ?>"><?= $statusLabel ?></span>

                    <!-- Ação ou mensagem -->
                    <?php if ($booking['status'] === 'cancelled'): ?>
                    <div class="cancellation-notice">
                        <p>Cancelamento não disponível (o passeio já está com Cancelamento solicitado).</p>
                    </div>
                    <?php elseif ($booking['status'] === 'refunded'): ?>
                    <div class="cancellation-notice">
                        <p>Esta reserva já foi reembolsada.</p>
                    </div>
                    <?php elseif ($booking['status'] === 'completed'): ?>
                    <div class="cancellation-notice cancellation-notice-info">
                        <p>Passeio já realizado. Cancelamento não disponível.</p>
                    </div>
                    <?php elseif (in_array($booking['status'], ['booked', 'pending', 'partially_paid'])): ?>
                    <form method="POST" action="/minha-conta/cancelamentos/solicitar" class="cancellation-form" onsubmit="return confirm('Tem certeza que deseja solicitar o cancelamento desta reserva?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                        <button type="submit" class="btn-solicitar-cancelamento">Solicitar Cancelamento</button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
