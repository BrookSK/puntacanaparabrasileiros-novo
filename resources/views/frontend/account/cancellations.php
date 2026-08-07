<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Cancelamentos</h2>
        <p class="account-subtitle">Gerencie o cancelamento das suas reservas. Reservas confirmadas ou pendentes podem ser canceladas.</p>

        <?php if (empty($bookings)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">
                <svg width="64" height="64" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3>Nenhuma reserva encontrada</h3>
            <p>Você ainda não possui reservas para gerenciar cancelamentos.</p>
            <a href="/passeios" class="btn btn-primary">Explorar Passeios</a>
        </div>
        <?php else: ?>

        <!-- Resumo rápido -->
        <div class="cancel-summary">
            <?php
            $totalBookings = count($bookings);
            $cancelledCount = count(array_filter($bookings, fn($b) => $b['status'] === 'cancelled'));
            $activeCount = count(array_filter($bookings, fn($b) => in_array($b['status'], ['booked', 'pending', 'partially_paid'])));
            $refundedCount = count(array_filter($bookings, fn($b) => $b['status'] === 'refunded'));
            ?>
            <div class="cancel-summary-item">
                <span class="cancel-summary-number"><?= $totalBookings ?></span>
                <span class="cancel-summary-label">Total de Reservas</span>
            </div>
            <div class="cancel-summary-item cancel-summary-active">
                <span class="cancel-summary-number"><?= $activeCount ?></span>
                <span class="cancel-summary-label">Ativas</span>
            </div>
            <div class="cancel-summary-item cancel-summary-cancelled">
                <span class="cancel-summary-number"><?= $cancelledCount ?></span>
                <span class="cancel-summary-label">Canceladas</span>
            </div>
            <div class="cancel-summary-item cancel-summary-refunded">
                <span class="cancel-summary-number"><?= $refundedCount ?></span>
                <span class="cancel-summary-label">Reembolsadas</span>
            </div>
        </div>

        <!-- Lista de reservas -->
        <div class="cancellations-list">
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
                'cancelled' => 'red',
                'refunded' => 'purple',
                'completed' => 'green',
                'booked' => 'blue',
                'pending' => 'yellow',
                default => 'gray',
            };
            $statusIcon = match($booking['status']) {
                'cancelled' => '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>',
                'refunded' => '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>',
                'completed' => '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>',
                'booked' => '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                'pending' => '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
                default => '<svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/></svg>',
            };
            ?>
            <div class="cancel-card cancel-card--<?= e($booking['status']) ?>">
                <div class="cancel-card-header">
                    <div class="cancel-card-title-wrap">
                        <h3 class="cancel-card-title"><?= e($booking['trip_title']) ?></h3>
                        <span class="cancel-card-id">Reserva #<?= (int)$booking['id'] ?></span>
                    </div>
                    <span class="cancel-status cancel-status--<?= $statusClass ?>">
                        <?= $statusIcon ?>
                        <?= $statusLabel ?>
                    </span>
                </div>

                <div class="cancel-card-body">
                    <div class="cancel-card-details">
                        <div class="cancel-detail-item">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <div>
                                <span class="cancel-detail-label">Data do Passeio</span>
                                <span class="cancel-detail-value"><?= $booking['trip_date'] ? format_date($booking['trip_date']) : 'Data não definida' ?></span>
                            </div>
                        </div>
                        <div class="cancel-detail-item">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/>
                            </svg>
                            <div>
                                <span class="cancel-detail-label">ID do Passeio</span>
                                <span class="cancel-detail-value">#<?= (int)$booking['trip_id'] ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Ação ou mensagem contextual -->
                    <div class="cancel-card-action">
                        <?php if ($booking['status'] === 'cancelled'): ?>
                        <div class="cancel-notice cancel-notice--warning">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <p>Cancelamento já solicitado. Aguardando processamento.</p>
                        </div>
                        <?php elseif ($booking['status'] === 'refunded'): ?>
                        <div class="cancel-notice cancel-notice--success">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>Reserva reembolsada com sucesso.</p>
                        </div>
                        <?php elseif ($booking['status'] === 'completed'): ?>
                        <div class="cancel-notice cancel-notice--info">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>Passeio já realizado. Cancelamento não disponível.</p>
                        </div>
                        <?php elseif (in_array($booking['status'], ['booked', 'pending', 'partially_paid'])): ?>
                        <form method="POST" action="/minha-conta/cancelamentos/solicitar" class="cancel-form" onsubmit="return confirm('Tem certeza que deseja solicitar o cancelamento desta reserva?\n\nEsta ação não pode ser desfeita.')">
                            <?= csrf_field() ?>
                            <input type="hidden" name="booking_id" value="<?= (int)$booking['id'] ?>">
                            <button type="submit" class="btn-cancel-request">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                Solicitar Cancelamento
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
