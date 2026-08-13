<?php
/**
 * Booking View - Admin Panel
 * Redesigned for better UX, modern layout, and visual hierarchy
 */

// Status helpers
$statusLabels = ['pending' => 'Pendente', 'booked' => 'Confirmado', 'partially_paid' => 'Parc. Pago', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado'];
$statusColors = ['pending' => 'warning', 'booked' => 'success', 'partially_paid' => 'info', 'completed' => 'success', 'cancelled' => 'danger', 'refunded' => 'secondary'];
$paymentStatusLabels = ['completed' => 'Aprovado', 'pending' => 'Pendente', 'failed' => 'Falhou', 'refunded' => 'Reembolsado'];

$st = $booking['status'] ?? 'pending';
$stLabel = $statusLabels[$st] ?? $st;
$stColor = $statusColors[$st] ?? 'secondary';

// Determine service name for header
$serviceName = '';
$serviceType = '';
if (!empty($items)) {
    $serviceType = 'Passeio';
    $serviceName = $items[0]['trip_title'] ?? '';
    if (count($items) > 1) $serviceName .= ' +' . (count($items) - 1);
} elseif (!empty($transfers)) {
    $serviceType = 'Transfer';
    $serviceName = ($transfers[0]['origin_title'] ?? '') . ' → ' . ($transfers[0]['destination_title'] ?? '');
}

$customerName = trim(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? ''));
?>

<!-- Page Header -->
<div class="booking-page-header">
    <div class="booking-page-header-left">
        <div class="booking-page-titles">
            <?php if ($serviceName): ?>
            <h1 class="booking-page-service">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <?php if ($serviceType === 'Transfer'): ?>
                    <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>
                    <?php else: ?>
                    <circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/>
                    <?php endif; ?>
                </svg>
                <?= e($serviceType . ': ' . $serviceName) ?>
            </h1>
            <?php endif; ?>
            <p class="booking-page-code">Reserva: <strong>#<?= e($booking['booking_number'] ?? '') ?></strong></p>
        </div>
    </div>
    <div class="booking-page-header-right">
        <span class="badge badge-lg badge-<?= $stColor ?>"><?= e($stLabel) ?></span>
        <span class="booking-page-date">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            <?= !empty($booking['created_at']) ? date('d/m/Y H:i', strtotime($booking['created_at'])) : '-' ?>
        </span>
        <a href="/admin/reservas" class="btn-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            Voltar
        </a>
    </div>
</div>

<!-- Main Content Grid -->
<div class="booking-detail-grid">
    <!-- Left Column -->
    <div class="booking-detail-left">

        <!-- Card: Dados do Cliente -->
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <div>
                    <h3>Dados do Cliente</h3>
                    <p class="admin-card-subtitle">Informações de contato e identificação</p>
                </div>
            </div>
            <div class="booking-info-grid">
                <div class="booking-info-item">
                    <span class="booking-info-label">Nome</span>
                    <span class="booking-info-value"><?= e($customerName ?: '-') ?></span>
                </div>
                <div class="booking-info-item">
                    <span class="booking-info-label">E-mail</span>
                    <span class="booking-info-value"><?= e($booking['billing_email'] ?? '-') ?></span>
                </div>
                <div class="booking-info-item">
                    <span class="booking-info-label">Telefone</span>
                    <span class="booking-info-value"><?= e($booking['billing_phone'] ?? '-') ?></span>
                </div>
                <div class="booking-info-item">
                    <span class="booking-info-label">País</span>
                    <span class="booking-info-value"><?= e($booking['billing_country'] ?? '-') ?></span>
                </div>
                <?php if (!empty($booking['billing_city'])): ?>
                <div class="booking-info-item">
                    <span class="booking-info-label">Cidade</span>
                    <span class="booking-info-value"><?= e($booking['billing_city']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($booking['billing_address'])): ?>
                <div class="booking-info-item booking-info-item-full">
                    <span class="booking-info-label">Endereço</span>
                    <span class="booking-info-value"><?= e($booking['billing_address']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card: Passeios Reservados -->
        <?php if (!empty($items)): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/></svg>
                </div>
                <div>
                    <h3>Passeios Reservados</h3>
                    <p class="admin-card-subtitle"><?= count($items) ?> passeio<?= count($items) > 1 ? 's' : '' ?> nesta reserva</p>
                </div>
            </div>
            <?php foreach ($items as $item): ?>
            <div class="booking-trip-card">
                <div class="booking-trip-card-header">
                    <div class="booking-trip-card-info">
                        <?php if (!empty($item['featured_image'])): ?>
                        <img src="<?= e($item['featured_image']) ?>" alt="" class="booking-trip-thumb" width="48" height="34">
                        <?php endif; ?>
                        <div>
                            <h4 class="booking-trip-title"><?= e($item['trip_title'] ?? $item['title'] ?? '-') ?></h4>
                            <?php if (!empty($item['package_title'])): ?>
                            <span class="booking-trip-package">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                                <?= e($item['package_title']) ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="booking-trip-card-price">$<?= number_format((float)($item['price'] ?? $item['subtotal'] ?? 0), 2) ?></div>
                </div>
                <div class="booking-trip-card-details" style="display:grid;grid-template-columns:1fr 1fr;gap:10px 20px;margin-top:14px;padding-top:14px;border-top:1px solid #f3f4f6;">
                    <?php if (!empty($item['trip_date'])): ?>
                    <div class="booking-trip-detail-item">
                        <span class="booking-detail-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            Data
                        </span>
                        <span class="booking-detail-value"><?= date('d/m/Y', strtotime($item['trip_date'])) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($item['trip_time'])): ?>
                    <div class="booking-trip-detail-item">
                        <span class="booking-detail-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Horário Passeio
                        </span>
                        <span class="booking-detail-value"><?= e($item['trip_time']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($item['hotel_name'])): ?>
                    <div class="booking-trip-detail-item">
                        <span class="booking-detail-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                            Hotel
                        </span>
                        <span class="booking-detail-value"><?= e($item['hotel_name']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($item['pickup_time'])): ?>
                    <div class="booking-trip-detail-item">
                        <span class="booking-detail-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            Busca
                        </span>
                        <span class="booking-detail-value"><?= e($item['pickup_time']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($item['package_title'])): ?>
                    <div class="booking-trip-detail-item">
                        <span class="booking-detail-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
                            Pacote
                        </span>
                        <span class="booking-detail-value"><?= e($item['package_title']) ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($item['travelers'])): ?>
                    <div class="booking-trip-detail-item">
                        <span class="booking-detail-label">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            Passageiros
                        </span>
                        <span class="booking-detail-value"><?php
                            $travelerParts = [];
                            foreach ($item['travelers'] as $t) {
                                $travelerParts[] = (int)$t['quantity'] . ' ' . e($t['category_name'] ?? 'Viajante');
                            }
                            echo implode(', ', $travelerParts);
                        ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Card: Transfers -->
        <?php if (!empty($transfers)): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-orange">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                </div>
                <div>
                    <h3>Transfers</h3>
                    <p class="admin-card-subtitle"><?= count($transfers) ?> transfer<?= count($transfers) > 1 ? 's' : '' ?> agendado<?= count($transfers) > 1 ? 's' : '' ?></p>
                </div>
            </div>
            <?php foreach ($transfers as $tr): ?>
            <div class="booking-transfer-card">
                <div class="booking-transfer-header">
                    <div class="booking-transfer-route">
                        <div class="booking-transfer-point">
                            <div class="booking-transfer-dot booking-transfer-dot-origin"></div>
                            <span><?= e($tr['origin_title'] ?? '?') ?></span>
                        </div>
                        <div class="booking-transfer-line"></div>
                        <div class="booking-transfer-point">
                            <div class="booking-transfer-dot booking-transfer-dot-dest"></div>
                            <span><?= e($tr['destination_title'] ?? '?') ?></span>
                        </div>
                    </div>
                    <div class="booking-transfer-price">$<?= number_format((float)($tr['price'] ?? 0), 2) ?></div>
                </div>
                <div class="booking-transfer-meta">
                    <?php if (!empty($tr['transfer_date'])): ?>
                    <div class="booking-transfer-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <?= date('d/m/Y', strtotime($tr['transfer_date'])) ?>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($tr['transfer_time'])): ?>
                    <div class="booking-transfer-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <?= e($tr['transfer_time']) ?>
                    </div>
                    <?php endif; ?>
                    <div class="booking-transfer-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                        <?= (int)($tr['adults'] ?? 0) ?> ad. + <?= (int)($tr['children'] ?? 0) ?> cr.
                    </div>
                    <?php if (!empty($tr['vehicle_title'])): ?>
                    <div class="booking-transfer-meta-item">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                        <?= e($tr['vehicle_title']) ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Card: Pagamentos -->
        <?php if (!empty($payments)): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-green">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                </div>
                <div>
                    <h3>Pagamentos</h3>
                    <p class="admin-card-subtitle">Histórico de transações</p>
                </div>
            </div>
            <div class="booking-payments-list">
                <?php foreach ($payments as $pay): ?>
                <div class="booking-payment-item">
                    <div class="booking-payment-item-left">
                        <div class="booking-payment-gateway">
                            <span class="booking-payment-gateway-icon">
                                <?php if (($pay['gateway'] ?? '') === 'stripe'): ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                                <?php else: ?>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                                <?php endif; ?>
                            </span>
                            <div>
                                <strong><?= e(ucfirst($pay['gateway'] ?? '-')) ?></strong>
                                <small><?= e($pay['type'] ?? '-') ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="booking-payment-item-center">
                        <span class="badge badge-<?= ($pay['status'] ?? '') === 'completed' ? 'success' : (($pay['status'] ?? '') === 'failed' ? 'danger' : 'warning') ?>">
                            <?= e($paymentStatusLabels[$pay['status'] ?? ''] ?? $pay['status'] ?? '-') ?>
                        </span>
                    </div>
                    <div class="booking-payment-item-right">
                        <span class="booking-payment-amount">$<?= number_format((float)($pay['amount'] ?? 0), 2) ?></span>
                        <small class="booking-payment-date"><?= !empty($pay['created_at']) ? date('d/m/Y H:i', strtotime($pay['created_at'])) : '-' ?></small>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card: Vouchers -->
        <?php if (!empty($vouchers)): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/></svg>
                </div>
                <div>
                    <h3>Vouchers</h3>
                    <p class="admin-card-subtitle">Comprovantes gerados para esta reserva</p>
                </div>
            </div>
            <div class="booking-vouchers-list">
                <?php foreach ($vouchers as $vc): ?>
                <div class="booking-voucher-item">
                    <div class="booking-voucher-item-info">
                        <div class="booking-voucher-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div>
                            <strong><?= e($vc['voucher_code'] ?? '-') ?></strong>
                            <small><?= e(ucfirst($vc['type'] ?? '-')) ?></small>
                        </div>
                    </div>
                    <div class="booking-voucher-item-status">
                        <span class="badge badge-<?= ($vc['status'] ?? '') === 'active' ? 'success' : 'secondary' ?>">
                            <?= ($vc['status'] ?? '') === 'active' ? 'Ativo' : e($vc['status'] ?? '-') ?>
                        </span>
                    </div>
                    <div class="booking-voucher-item-actions">
                        <?php if (!empty($vc['pdf_url'])): ?>
                        <a href="<?= e($vc['pdf_url']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Baixar PDF">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            PDF
                        </a>
                        <a href="<?= e($vc['pdf_url']) ?>" target="_blank" class="btn btn-sm btn-outline" title="Visualizar">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Ver
                        </a>
                        <?php else: ?>
                        <span class="text-muted" style="font-size:12px;color:#94a3b8;">Sem PDF</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Card: Observações -->
        <?php if (!empty($booking['notes']) || !empty($booking['admin_notes'])): ?>
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div>
                    <h3>Observações</h3>
                    <p class="admin-card-subtitle">Notas e comentários sobre esta reserva</p>
                </div>
            </div>
            <?php if (!empty($booking['notes'])): ?>
            <div class="booking-note">
                <strong>Observações do Cliente:</strong>
                <p><?= nl2br(e($booking['notes'])) ?></p>
            </div>
            <?php endif; ?>
            <?php if (!empty($booking['admin_notes'])): ?>
            <div class="booking-note booking-note-admin">
                <strong>Notas Internas:</strong>
                <p><?= nl2br(e($booking['admin_notes'])) ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

    </div><!-- .booking-detail-left -->

    <!-- Right Column (Sidebar) -->
    <div class="booking-detail-right">

        <!-- Card: Resumo Financeiro -->
        <div class="admin-card summary-card admin-card-sticky">
            <div class="summary-card-header">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                <h3>Resumo Financeiro</h3>
            </div>
            <div class="summary-card-body">
                <div class="summary-row">
                    <span class="summary-row-label">Subtotal</span>
                    <span class="summary-row-value">$<?= number_format((float)($booking['subtotal'] ?? 0), 2) ?></span>
                </div>
                <?php if (!empty($booking['discount_amount']) && (float)$booking['discount_amount'] > 0): ?>
                <div class="summary-row">
                    <span class="summary-row-label">Desconto</span>
                    <span class="summary-row-value" style="color:#16a34a;">-$<?= number_format((float)$booking['discount_amount'], 2) ?></span>
                </div>
                <?php endif; ?>
                <div class="summary-row" style="border-bottom:2px solid #e2e8f0; padding-bottom:16px;">
                    <span class="summary-row-label" style="font-size:15px; font-weight:700;">Total</span>
                    <span class="summary-row-value" style="font-size:18px; color:var(--primary);">$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Pago</span>
                    <span class="summary-row-value" style="color:#16a34a;">$<?= number_format((float)($booking['paid_amount'] ?? 0), 2) ?></span>
                </div>
                <div class="summary-row">
                    <span class="summary-row-label">Pendente</span>
                    <span class="summary-row-value" style="color:<?= (float)($booking['due_amount'] ?? 0) > 0 ? '#ea580c' : '#16a34a' ?>;">$<?= number_format((float)($booking['due_amount'] ?? 0), 2) ?></span>
                </div>
            </div>

            <!-- Alterar Status -->
            <div class="summary-card-actions">
                <form method="POST" action="/admin/reservas/<?= (int)$booking['id'] ?>/status" class="booking-status-form">
                    <?= csrf_field() ?>
                    <label class="booking-status-label">Alterar Status</label>
                    <div class="booking-status-controls">
                        <select name="status" class="form-control form-control-sm">
                            <option value="pending" <?= $st === 'pending' ? 'selected' : '' ?>>Pendente</option>
                            <option value="booked" <?= $st === 'booked' ? 'selected' : '' ?>>Confirmado</option>
                            <option value="partially_paid" <?= $st === 'partially_paid' ? 'selected' : '' ?>>Parc. Pago</option>
                            <option value="completed" <?= $st === 'completed' ? 'selected' : '' ?>>Concluído</option>
                            <option value="cancelled" <?= $st === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                            <option value="refunded" <?= $st === 'refunded' ? 'selected' : '' ?>>Reembolsado</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Atualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Card: Detalhes da Reserva -->
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                </div>
                <div>
                    <h3>Detalhes</h3>
                </div>
            </div>
            <div class="booking-details-list">
                <div class="booking-detail-row">
                    <span class="booking-detail-label">Código</span>
                    <span class="booking-detail-value"><code><?= e($booking['booking_number'] ?? '') ?></code></span>
                </div>
                <div class="booking-detail-row">
                    <span class="booking-detail-label">Status</span>
                    <span class="booking-detail-value"><span class="badge badge-<?= $stColor ?>"><?= e($stLabel) ?></span></span>
                </div>
                <div class="booking-detail-row">
                    <span class="booking-detail-label">Moeda</span>
                    <span class="booking-detail-value"><?= e(strtoupper($booking['currency'] ?? 'USD')) ?></span>
                </div>
                <div class="booking-detail-row">
                    <span class="booking-detail-label">Modo Pagamento</span>
                    <span class="booking-detail-value"><?= e(ucfirst($booking['payment_mode'] ?? '-')) ?></span>
                </div>
                <div class="booking-detail-row">
                    <span class="booking-detail-label">Criado em</span>
                    <span class="booking-detail-value"><?= !empty($booking['created_at']) ? date('d/m/Y H:i', strtotime($booking['created_at'])) : '-' ?></span>
                </div>
                <?php if (!empty($booking['updated_at'])): ?>
                <div class="booking-detail-row">
                    <span class="booking-detail-label">Atualizado em</span>
                    <span class="booking-detail-value"><?= date('d/m/Y H:i', strtotime($booking['updated_at'])) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($booking['ip_address'])): ?>
                <div class="booking-detail-row">
                    <span class="booking-detail-label">IP</span>
                    <span class="booking-detail-value"><code style="font-size:11px;"><?= e($booking['ip_address']) ?></code></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Card: Histórico / Timeline -->
        <div class="admin-card">
            <div class="admin-card-header">
                <div class="admin-card-icon admin-card-icon-blue">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <h3>Histórico</h3>
                </div>
            </div>
            <div class="booking-timeline">
                <!-- Criação -->
                <div class="booking-timeline-item">
                    <div class="booking-timeline-dot booking-timeline-dot-blue"></div>
                    <div class="booking-timeline-content">
                        <strong>Reserva criada</strong>
                        <small><?= !empty($booking['created_at']) ? date('d/m/Y H:i', strtotime($booking['created_at'])) : '-' ?></small>
                    </div>
                </div>

                <!-- Pagamentos -->
                <?php if (!empty($payments)): ?>
                <?php foreach ($payments as $pay): ?>
                <div class="booking-timeline-item">
                    <div class="booking-timeline-dot booking-timeline-dot-<?= ($pay['status'] ?? '') === 'completed' ? 'green' : 'orange' ?>"></div>
                    <div class="booking-timeline-content">
                        <strong>Pagamento <?= ($pay['status'] ?? '') === 'completed' ? 'aprovado' : e($pay['status'] ?? '') ?></strong>
                        <small><?= !empty($pay['created_at']) ? date('d/m/Y H:i', strtotime($pay['created_at'])) : '-' ?></small>
                        <span class="booking-timeline-detail">$<?= number_format((float)($pay['amount'] ?? 0), 2) ?> via <?= e(ucfirst($pay['gateway'] ?? '-')) ?></span>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>

                <!-- Vouchers gerados -->
                <?php if (!empty($vouchers)): ?>
                <div class="booking-timeline-item">
                    <div class="booking-timeline-dot booking-timeline-dot-green"></div>
                    <div class="booking-timeline-content">
                        <strong>Voucher<?= count($vouchers) > 1 ? 's' : '' ?> gerado<?= count($vouchers) > 1 ? 's' : '' ?></strong>
                        <small><?= !empty($vouchers[0]['created_at']) ? date('d/m/Y H:i', strtotime($vouchers[0]['created_at'])) : '-' ?></small>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Status atual -->
                <?php if ($st !== 'pending'): ?>
                <div class="booking-timeline-item">
                    <div class="booking-timeline-dot booking-timeline-dot-<?= in_array($st, ['booked', 'completed']) ? 'green' : ($st === 'cancelled' ? 'red' : 'orange') ?>"></div>
                    <div class="booking-timeline-content">
                        <strong>Status: <?= e($stLabel) ?></strong>
                        <small><?= !empty($booking['updated_at']) ? date('d/m/Y H:i', strtotime($booking['updated_at'])) : '' ?></small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div><!-- .booking-detail-right -->
</div><!-- .booking-detail-grid -->
