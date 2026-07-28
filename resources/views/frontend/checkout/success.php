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
            <!-- Ícone de sucesso -->
            <div style="margin-bottom:32px;">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:inline-flex;align-items:center;justify-content:center;box-shadow:0 10px 30px rgba(16,185,129,0.35);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>

            <?php if (!empty($booking)): ?>
            <!-- Título -->
            <h2>Obrigado, <?= e($booking['billing_first_name'] ?? 'Cliente') ?>!</h2>
            <p class="success-subtitle">Sua reserva foi confirmada com sucesso. Você receberá todos os detalhes por e-mail.</p>

            <!-- Card de Resumo -->
            <div class="success-summary">
                <div class="success-summary-header">
                    <span class="success-badge">Confirmada</span>
                    <span class="success-booking-number"><?= e($booking['booking_number']) ?></span>
                </div>

                <div class="success-summary-body">
                    <div class="success-row">
                        <div class="success-row-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div class="success-row-content">
                            <span class="success-row-label">Número da Reserva</span>
                            <strong><?= e($booking['booking_number']) ?></strong>
                        </div>
                    </div>

                    <div class="success-row">
                        <div class="success-row-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div class="success-row-content">
                            <span class="success-row-label">Valor Total</span>
                            <strong>$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?> USD</strong>
                        </div>
                    </div>

                    <div class="success-row">
                        <div class="success-row-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div class="success-row-content">
                            <span class="success-row-label">E-mail de Confirmação</span>
                            <strong><?= e($booking['billing_email'] ?? '') ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itens Reservados -->
            <?php if (!empty($items)): ?>
            <div class="success-items-card">
                <h4>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/></svg>
                    Passeios Reservados
                </h4>
                <?php foreach ($items as $item): ?>
                <div class="success-item-row">
                    <div>
                        <strong><?= e($item['trip_title'] ?? 'Passeio') ?></strong>
                        <span><?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '' ?><?php if (!empty($item['trip_time'])): ?> às <?= e($item['trip_time']) ?><?php endif; ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($transfers)): ?>
            <div class="success-items-card">
                <h4>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    Transfers
                </h4>
                <?php foreach ($transfers as $tr): ?>
                <div class="success-item-row">
                    <div>
                        <strong><?= e(($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? '')) ?></strong>
                        <span><?= !empty($tr['date']) ? date('d/m/Y', strtotime($tr['date'])) : '' ?> às <?= e($tr['time'] ?? '') ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Info Box -->
            <div class="success-info-box">
                <div class="success-info-icon">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                </div>
                <div>
                    <p>Enviamos um e-mail de confirmação para <strong><?= e($booking['billing_email'] ?? '') ?></strong> com todos os detalhes e vouchers.</p>
                    <p>Dúvidas? Fale conosco pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170">+1 (829) 458-2170</a></p>
                </div>
            </div>

            <?php else: ?>
            <h2>Reserva Confirmada!</h2>
            <p class="success-subtitle">Sua reserva foi processada com sucesso. Você receberá um e-mail com os detalhes em breve.</p>
            <?php endif; ?>

            <!-- Ações -->
            <div class="success-actions">
                <a href="/passeios" class="btn btn-primary btn-lg">Ver Mais Passeios</a>
                <a href="/minha-conta/reservas" class="btn btn-outline btn-lg">Minhas Reservas</a>
            </div>
        </div>
    </div>
</section>
