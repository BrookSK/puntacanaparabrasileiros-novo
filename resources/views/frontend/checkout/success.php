<section class="passeios-hero">
    <div class="container">
        <div class="passeios-hero-content">
            <h1>Reserva Confirmada!</h1>
            <p>Sua reserva foi realizada com sucesso.</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="success-card">
            <div class="success-icon">
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5"/></svg>
            </div>

            <?php if (!empty($booking)): ?>
            <h2>Obrigado, <?= e($booking['billing_first_name'] ?? '') ?>!</h2>
            <p class="success-subtitle">Sua reserva <strong><?= e($booking['booking_number']) ?></strong> foi confirmada.</p>

            <div class="success-details">
                <div class="success-detail-row">
                    <span>Número da Reserva:</span>
                    <strong><?= e($booking['booking_number']) ?></strong>
                </div>
                <div class="success-detail-row">
                    <span>Status:</span>
                    <strong style="color:#16a34a;">Confirmado</strong>
                </div>
                <div class="success-detail-row">
                    <span>Total:</span>
                    <strong>$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?> USD</strong>
                </div>
            </div>

            <?php if (!empty($items)): ?>
            <div class="success-items">
                <h4>Passeios Reservados</h4>
                <?php foreach ($items as $item): ?>
                <div class="success-item">
                    <strong><?= e($item['trip_title'] ?? 'Passeio') ?></strong>
                    <span><?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '' ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($transfers)): ?>
            <div class="success-items">
                <h4>Transfers</h4>
                <?php foreach ($transfers as $tr): ?>
                <div class="success-item">
                    <strong><?= e(($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? '')) ?></strong>
                    <span><?= !empty($tr['date']) ? date('d/m/Y', strtotime($tr['date'])) : '' ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="success-info">
                <p>Enviamos um e-mail de confirmação para <strong><?= e($booking['billing_email'] ?? '') ?></strong> com todos os detalhes e vouchers.</p>
                <p>Se tiver alguma dúvida, entre em contato pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170">+1 (829) 458-2170</a></p>
            </div>

            <?php else: ?>
            <h2>Reserva Confirmada!</h2>
            <p class="success-subtitle">Sua reserva foi processada com sucesso. Você receberá um e-mail com os detalhes.</p>
            <?php endif; ?>

            <div class="success-actions">
                <a href="/passeios" class="btn btn-primary">Ver Mais Passeios</a>
                <a href="/minha-conta/reservas" class="btn btn-outline">Minhas Reservas</a>
            </div>
        </div>
    </div>
</section>
