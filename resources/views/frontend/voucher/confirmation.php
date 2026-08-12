<section class="voucher-confirmation-page">
    <div class="container">
        <div class="voucher-confirm-card">
            <!-- Status Icon -->
            <?php
                $isConfirmed = in_array($status, ['booked', 'confirmed', 'completed', 'partially_paid']);
                $isPending = in_array($status, ['pending']);
                $isCancelled = in_array($status, ['cancelled', 'refunded']);
            ?>

            <div class="voucher-confirm-icon <?= $isConfirmed ? 'confirmed' : ($isCancelled ? 'cancelled' : 'pending') ?>">
                <?php if ($isConfirmed): ?>
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <?php elseif ($isCancelled): ?>
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="15" y1="9" x2="9" y2="15"/>
                    <line x1="9" y1="9" x2="15" y2="15"/>
                </svg>
                <?php else: ?>
                <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <?php endif; ?>
            </div>

            <!-- Status Text -->
            <h1 class="voucher-confirm-title">
                <?php if ($isConfirmed): ?>
                    Reserva Confirmada
                <?php elseif ($isCancelled): ?>
                    Reserva Cancelada
                <?php else: ?>
                    Reserva Pendente
                <?php endif; ?>
            </h1>

            <p class="voucher-confirm-subtitle">
                <?php if ($isConfirmed): ?>
                    Este voucher foi validado com sucesso. A reserva abaixo est&aacute; confirmada.
                <?php elseif ($isCancelled): ?>
                    Esta reserva foi cancelada e n&atilde;o &eacute; mais v&aacute;lida.
                <?php else: ?>
                    Esta reserva est&aacute; pendente de confirma&ccedil;&atilde;o ou pagamento.
                <?php endif; ?>
            </p>

            <!-- Voucher Details -->
            <div class="voucher-confirm-details">
                <div class="voucher-confirm-type">
                    <?php if ($type === 'transfer'): ?>
                    <span class="voucher-type-badge transfer">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><polygon points="23 7 16 12 16 3"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="13.5" cy="18.5" r="2.5"/></svg>
                        Transfer
                    </span>
                    <?php else: ?>
                    <span class="voucher-type-badge trip">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                        Passeio
                    </span>
                    <?php endif; ?>
                </div>

                <table class="voucher-confirm-table">
                    <tr>
                        <th>C&oacute;digo do Voucher</th>
                        <td><strong><?= e($reference) ?></strong></td>
                    </tr>
                    <?php if ($tripName): ?>
                    <tr>
                        <th><?= $type === 'transfer' ? 'Rota' : 'Passeio' ?></th>
                        <td><?= e($tripName) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($customerName): ?>
                    <tr>
                        <th>Cliente</th>
                        <td><?= e($customerName) ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($date): ?>
                    <tr>
                        <th>Data</th>
                        <td><?= format_date($date) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="voucher-status-pill <?= $isConfirmed ? 'confirmed' : ($isCancelled ? 'cancelled' : 'pending') ?>">
                                <?php if ($isConfirmed): ?>
                                    Confirmado
                                <?php elseif ($isCancelled): ?>
                                    Cancelado
                                <?php elseif ($status === 'partially_paid'): ?>
                                    Parcialmente Pago
                                <?php else: ?>
                                    Pendente
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Footer -->
            <div class="voucher-confirm-footer">
                <p>Punta Cana para Brasileiros</p>
                <p class="voucher-confirm-contact">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72"/></svg>
                    +1 (829) 458-2170
                </p>
            </div>
        </div>
    </div>
</section>

<style>
.voucher-confirmation-page {
    min-height: 70vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    background: var(--light-bg, #f7f8fa);
}
.voucher-confirm-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.08);
    max-width: 520px;
    width: 100%;
    padding: 48px 40px;
    text-align: center;
}
.voucher-confirm-icon {
    margin-bottom: 20px;
}
.voucher-confirm-icon.confirmed svg {
    stroke: #16a34a;
}
.voucher-confirm-icon.pending svg {
    stroke: #d97706;
}
.voucher-confirm-icon.cancelled svg {
    stroke: #dc2626;
}
.voucher-confirm-title {
    font-size: 24px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 8px;
}
.voucher-confirm-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 32px;
    line-height: 1.6;
}
.voucher-confirm-details {
    text-align: left;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
}
.voucher-confirm-type {
    margin-bottom: 16px;
}
.voucher-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.voucher-type-badge.trip {
    background: #f0fdf4;
    color: #16a34a;
}
.voucher-type-badge.transfer {
    background: #eff6ff;
    color: #2563eb;
}
.voucher-confirm-table {
    width: 100%;
    border-collapse: collapse;
}
.voucher-confirm-table tr {
    border-bottom: 1px solid #f3f4f6;
}
.voucher-confirm-table tr:last-child {
    border-bottom: none;
}
.voucher-confirm-table th {
    text-align: left;
    font-size: 12px;
    font-weight: 500;
    color: #6b7280;
    padding: 12px 0;
    width: 40%;
    vertical-align: top;
}
.voucher-confirm-table td {
    font-size: 14px;
    color: #1f2937;
    padding: 12px 0;
    font-weight: 500;
}
.voucher-status-pill {
    display: inline-block;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
}
.voucher-status-pill.confirmed {
    background: #dcfce7;
    color: #16a34a;
}
.voucher-status-pill.pending {
    background: #fef3c7;
    color: #d97706;
}
.voucher-status-pill.cancelled {
    background: #fee2e2;
    color: #dc2626;
}
.voucher-confirm-footer {
    padding-top: 20px;
    border-top: 1px solid #e5e7eb;
}
.voucher-confirm-footer p {
    font-size: 13px;
    color: #6b7280;
    margin-bottom: 6px;
}
.voucher-confirm-contact {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
}
@media (max-width: 560px) {
    .voucher-confirm-card {
        padding: 32px 24px;
    }
    .voucher-confirm-title {
        font-size: 20px;
    }
}
</style>
