<section class="section">
    <div class="container">
        <div class="success-page">
            <div class="success-icon">&#10004;</div>
            <h1><?= $isTransferOnly ?? false ? 'Transfer Reservado com Sucesso!' : 'Reserva Confirmada!' ?></h1>

            <?php if ($booking ?? null): ?>
            <div class="success-details">
                <p class="booking-number">Número da reserva: <strong><?= e($booking['booking_number']) ?></strong></p>
                <p>Um email de confirmação com seus vouchers foi enviado para <strong><?= e($booking['billing_email']) ?></strong>.</p>

                <div class="success-summary">
                    <h3>Resumo da Reserva</h3>
                    <?php if (!empty($items)): ?>
                    <?php foreach ($items as $item): ?>
                    <div class="summary-line">
                        <span><?= e($item['trip_title']) ?> - <?= format_date($item['trip_date']) ?></span>
                        <span><?= money((float)$item['price']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (!empty($transfers)): ?>
                    <?php foreach ($transfers as $t): ?>
                    <div class="summary-line">
                        <span>Transfer: <?= e($t['origin_title']) ?> &rarr; <?= e($t['destination_title']) ?></span>
                        <span><?= money((float)$t['price']) ?></span>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="summary-line total">
                        <span>Total Pago:</span>
                        <span><?= money((float)$booking['paid_amount']) ?></span>
                    </div>
                    <?php if ((float)$booking['due_amount'] > 0): ?>
                    <div class="summary-line due">
                        <span>Restante a pagar:</span>
                        <span><?= money((float)$booking['due_amount']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php else: ?>
            <p>Seus vouchers foram enviados por email. Obrigado pela reserva!</p>
            <?php endif; ?>

            <div class="success-actions">
                <a href="/minha-conta/reservas" class="btn btn-primary">Ver Minhas Reservas</a>
                <a href="/" class="btn btn-outline">Voltar para Home</a>
            </div>
        </div>
    </div>
</section>
