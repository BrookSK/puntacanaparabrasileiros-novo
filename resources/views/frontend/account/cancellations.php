<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <div class="cancel-page-header">
            <h2>Cancelamentos</h2>
            <p class="account-subtitle">Gerencie suas reservas e solicite cancelamentos quando necessário.</p>
        </div>

        <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <svg width="56" height="56" fill="none" viewBox="0 0 24 24" stroke="#1B6F00" stroke-width="1.2" style="opacity:.4;margin-bottom:16px">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3>Nenhuma reserva encontrada</h3>
            <p>Você ainda não tem reservas para gerenciar.</p>
            <a href="/passeios" class="btn btn-primary">Explorar Passeios</a>
        </div>
        <?php else: ?>

        <?php
        $totalBookings = count($bookings);
        $cancelledCount = count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled'));
        $activeCount = count(array_filter($bookings, fn($b) => in_array($b['status'], ['booked', 'pending', 'partially_paid'])));
        $refundedCount = count(array_filter($bookings, fn($b) => $b['status'] === 'refunded'));
        ?>

        <!-- Stats Cards -->
        <div class="cancel-stats">
            <div class="cancel-stat-card">
                <div class="cancel-stat-icon cancel-stat-icon--total">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                </div>
                <div class="cancel-stat-content">
                    <span class="cancel-stat-number"><?= $totalBookings ?></span>
                    <span class="cancel-stat-label">Total</span>
                </div>
            </div>
            <div class="cancel-stat-card">
                <div class="cancel-stat-icon cancel-stat-icon--active">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="cancel-stat-content">
                    <span class="cancel-stat-number"><?= $activeCount ?></span>
                    <span class="cancel-stat-label">Ativas</span>
                </div>
            </div>
            <div class="cancel-stat-card">
                <div class="cancel-stat-icon cancel-stat-icon--cancelled">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </div>
                <div class="cancel-stat-content">
                    <span class="cancel-stat-number"><?= $cancelledCount ?></span>
                    <span class="cancel-stat-label">Canceladas</span>
                </div>
            </div>
            <div class="cancel-stat-card">
                <div class="cancel-stat-icon cancel-stat-icon--refunded">
                    <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                </div>
                <div class="cancel-stat-content">
                    <span class="cancel-stat-number"><?= $refundedCount ?></span>
                    <span class="cancel-stat-label">Reembolsadas</span>
                </div>
            </div>
        </div>

        <!-- Booking Cards -->
        <div class="cancel-booking-list">
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
            $statusColor = match($booking['status']) {
                'cancelled' => '#dc2626',
                'refunded' => '#7c3aed',
                'completed' => '#1B6F00',
                'booked' => '#3772C0',
                'pending' => '#d97706',
                default => '#6b7280',
            };
            $statusBg = match($booking['status']) {
                'cancelled' => '#fef2f2',
                'refunded' => '#f5f3ff',
                'completed' => '#ecfdf5',
                'booked' => '#eff6ff',
                'pending' => '#fffbeb',
                default => '#f9fafb',
            };
            ?>
            <div class="cancel-booking-card" style="border-left: 4px solid <?= $statusColor ?>">
                <div class="cancel-booking-top">
                    <div class="cancel-booking-info">
                        <h3 class="cancel-booking-title"><?= e($booking['trip_title']) ?></h3>
                        <div class="cancel-booking-meta">
                            <span class="cancel-booking-meta-item">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                                Reserva #<?= (int)$booking['id'] ?>
                            </span>
                            <span class="cancel-booking-meta-item">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <?= $booking['trip_date'] ? format_date($booking['trip_date']) : 'Sem data' ?>
                            </span>
                        </div>
                    </div>
                    <span class="cancel-booking-badge" style="background:<?= $statusBg ?>;color:<?= $statusColor ?>">
                        <?= $statusLabel ?>
                    </span>
                </div>

                <div class="cancel-booking-bottom">
                    <?php if ($booking['status'] === 'cancelled'): ?>
                    <div class="cancel-booking-msg cancel-booking-msg--warning">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Cancelamento já solicitado. Aguardando processamento.
                    </div>
                    <?php elseif ($booking['status'] === 'refunded'): ?>
                    <div class="cancel-booking-msg cancel-booking-msg--success">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Reserva reembolsada com sucesso.
                    </div>
                    <?php elseif ($booking['status'] === 'completed'): ?>
                    <div class="cancel-booking-msg cancel-booking-msg--info">
                        <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Passeio já realizado. Cancelamento indisponível.
                    </div>
                    <?php elseif (in_array($booking['status'], ['booked', 'pending', 'partially_paid'])): ?>
                    <form method="POST" action="/minha-conta/cancelamentos/solicitar" onsubmit="return confirm('Tem certeza que deseja solicitar o cancelamento desta reserva?')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                        <button type="submit" class="cancel-booking-btn">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            Solicitar Cancelamento
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
