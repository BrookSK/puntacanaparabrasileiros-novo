<section class="section">
    <div class="container">
        <div style="max-width:800px;margin:0 auto;">

            <!-- Header -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;">
                <div>
                    <h1 style="font-size:24px;font-weight:700;color:#1f2937;margin-bottom:4px;">Reserva <?= e($booking['booking_number']) ?></h1>
                    <p style="font-size:14px;color:#6b7280;">Realizada em <?= !empty($booking['created_at']) ? date('d/m/Y H:i', strtotime($booking['created_at'])) : '-' ?></p>
                </div>
                <a href="/minha-conta/reservas" style="display:inline-flex;align-items:center;gap:6px;padding:8px 16px;border:1px solid #e5e7eb;border-radius:8px;font-size:13px;color:#374151;text-decoration:none;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Voltar
                </a>
            </div>

            <!-- Status Card -->
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:28px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                    <span style="background:#d1fae5;color:#065f46;font-size:12px;font-weight:600;padding:5px 14px;border-radius:20px;">
                        <?php
                        $statusLabels = ['pending' => 'Pendente', 'booked' => 'Confirmada', 'partially_paid' => 'Parc. Pago', 'completed' => 'Concluída', 'cancelled' => 'Cancelada'];
                        echo $statusLabels[$booking['status'] ?? 'pending'] ?? $booking['status'];
                        ?>
                    </span>
                    <span style="font-size:13px;color:#6b7280;"><?= e($booking['booking_number']) ?></span>
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                    <div>
                        <p style="font-size:11px;color:#9ca3af;text-transform:uppercase;margin-bottom:4px;">Total</p>
                        <p style="font-size:18px;font-weight:700;color:#1f2937;">$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?></p>
                    </div>
                    <div>
                        <p style="font-size:11px;color:#9ca3af;text-transform:uppercase;margin-bottom:4px;">Pago</p>
                        <p style="font-size:18px;font-weight:700;color:#16a34a;">$<?= number_format((float)($booking['paid_amount'] ?? 0), 2) ?></p>
                    </div>
                    <div>
                        <p style="font-size:11px;color:#9ca3af;text-transform:uppercase;margin-bottom:4px;">Pendente</p>
                        <p style="font-size:18px;font-weight:700;color:#dc2626;">$<?= number_format((float)($booking['due_amount'] ?? 0), 2) ?></p>
                    </div>
                </div>
            </div>

            <!-- Passeios -->
            <?php if (!empty($items)): ?>
            <div style="margin-bottom:28px;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:14px;">Passeios</h3>
                <?php foreach ($items as $item): ?>
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 22px;margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <strong style="font-size:15px;color:#1f2937;"><?= e($item['trip_title'] ?? 'Passeio') ?></strong>
                            <p style="font-size:13px;color:#6b7280;margin-top:4px;">
                                <?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '' ?>
                                <?php if (!empty($item['trip_time'])): ?> às <?= e($item['trip_time']) ?><?php endif; ?>
                                <?php if (!empty($item['package_title'])): ?> — Pacote: <?= e($item['package_title']) ?><?php endif; ?>
                            </p>
                        </div>
                        <strong style="font-size:15px;color:#1f2937;">$<?= number_format((float)($item['price'] ?? $item['subtotal'] ?? 0), 2) ?></strong>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Transfers -->
            <?php if (!empty($transfers)): ?>
            <div style="margin-bottom:28px;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:14px;">Transfers</h3>
                <?php foreach ($transfers as $tr): ?>
                <div style="background:#fff;border:1px solid #e5e7eb;border-radius:10px;padding:18px 22px;margin-bottom:12px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;">
                        <div>
                            <strong style="font-size:15px;color:#1f2937;"><?= e(($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? '')) ?></strong>
                            <p style="font-size:13px;color:#6b7280;margin-top:4px;">
                                <?= !empty($tr['date']) ? date('d/m/Y', strtotime($tr['date'])) : '' ?> às <?= e($tr['time'] ?? '') ?>
                                — <?= ($tr['type'] ?? '') === 'arrival' ? 'Chegada' : 'Saída' ?>
                                — <?= ($tr['service_type'] ?? '') === 'shared' ? 'Compartilhado' : 'Privativo' ?>
                            </p>
                        </div>
                        <strong style="font-size:15px;color:#1f2937;">$<?= number_format((float)($tr['price'] ?? 0), 2) ?></strong>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Vouchers -->
            <?php if (!empty($vouchers)): ?>
            <div style="margin-bottom:28px;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:14px;">Vouchers</h3>
                <?php foreach ($vouchers as $vc): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;background:#fff;border:1px solid #e5e7eb;border-radius:10px;margin-bottom:10px;">
                    <div style="display:flex;align-items:center;gap:14px;">
                        <div style="width:40px;height:40px;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;background:<?= ($vc['type'] ?? '') === 'transfer' ? '#eff6ff' : '#f0fdf4' ?>;">
                            <?php if (($vc['type'] ?? '') === 'transfer'): ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            <?php else: ?>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/></svg>
                            <?php endif; ?>
                        </div>
                        <div>
                            <strong style="font-size:14px;color:#1f2937;display:block;">
                                <?= ($vc['type'] ?? '') === 'transfer' ? 'VOUCHER TRANSFER' : 'VOUCHER PASSEIO' ?>
                            </strong>
                            <span style="font-size:13px;color:#6b7280;">
                                <?php if (!empty($vc['trip_name'])): ?>
                                    <?= e($vc['trip_name']) ?>
                                <?php elseif (!empty($vc['route_name'])): ?>
                                    <?= e($vc['route_name']) ?>
                                <?php endif; ?>
                                <span style="font-size:11px;color:#9ca3af;margin-left:6px;">— Cód: <?= e($vc['reference_code'] ?? '') ?></span>
                            </span>
                        </div>
                    </div>
                    <a href="/admin/vouchers/<?= (int)$vc['id'] ?>/visualizar" target="_blank" style="font-size:12px;color:#0077b6;font-weight:600;text-decoration:none;padding:6px 14px;border:1px solid #0077b6;border-radius:6px;">Ver Voucher</a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Pagamentos -->
            <?php if (!empty($payments)): ?>
            <div style="margin-bottom:28px;">
                <h3 style="font-size:16px;font-weight:600;color:#1f2937;margin-bottom:14px;">Pagamentos</h3>
                <?php foreach ($payments as $pay): ?>
                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 18px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:8px;">
                    <div>
                        <strong style="font-size:13px;color:#1f2937;"><?= e(ucfirst($pay['gateway'] ?? '-')) ?></strong>
                        <span style="font-size:12px;color:#6b7280;margin-left:10px;"><?= !empty($pay['created_at']) ? date('d/m/Y H:i', strtotime($pay['created_at'])) : '' ?></span>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <span style="background:<?= ($pay['status'] ?? '') === 'completed' ? '#d1fae5' : '#fef3c7' ?>;color:<?= ($pay['status'] ?? '') === 'completed' ? '#065f46' : '#92400e' ?>;font-size:11px;font-weight:600;padding:3px 10px;border-radius:12px;"><?= ($pay['status'] ?? '') === 'completed' ? 'Pago' : 'Pendente' ?></span>
                        <strong style="font-size:14px;">$<?= number_format((float)($pay['amount'] ?? 0), 2) ?></strong>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Contato -->
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:18px 22px;text-align:center;">
                <p style="font-size:13px;color:#166534;line-height:1.7;">
                    Dúvidas sobre sua reserva? Fale conosco pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170" style="color:#15803d;font-weight:600;">+1 (829) 458-2170</a>
                </p>
            </div>

        </div>
    </div>
</section>
