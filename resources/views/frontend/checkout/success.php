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
        <div style="max-width:600px;margin:0 auto;text-align:center;background:#fff;border-radius:20px;padding:50px 40px;box-shadow:0 4px 24px rgba(0,0,0,0.06);border:1px solid #f3f4f6;">

            <!-- Check Icon -->
            <div style="margin-bottom:28px;">
                <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,#10b981,#059669);display:inline-flex;align-items:center;justify-content:center;box-shadow:0 10px 30px rgba(16,185,129,0.3);">
                    <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
            </div>

            <?php if (!empty($booking)): ?>
            <!-- Título -->
            <h2 style="font-size:26px;font-weight:700;color:#1f2937;margin-bottom:8px;">Obrigado, <?= e($booking['billing_first_name'] ?? 'Cliente') ?>!</h2>
            <p style="font-size:15px;color:#6b7280;margin-bottom:36px;line-height:1.6;">Sua reserva foi confirmada com sucesso.<br>Você receberá todos os detalhes por e-mail.</p>

            <!-- Resumo -->
            <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:14px;overflow:hidden;margin-bottom:28px;text-align:left;">
                <div style="display:flex;justify-content:space-between;align-items:center;padding:14px 24px;background:#f3f4f6;border-bottom:1px solid #e5e7eb;">
                    <span style="background:#d1fae5;color:#065f46;font-size:12px;font-weight:600;padding:4px 12px;border-radius:20px;">Confirmada</span>
                    <span style="font-size:13px;color:#6b7280;font-weight:500;"><?= e($booking['booking_number']) ?></span>
                </div>
                <div style="padding:12px 24px;">
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid #f3f4f6;">
                        <div style="width:36px;height:36px;background:#eff6ff;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <span style="font-size:12px;color:#9ca3af;">Número da Reserva</span>
                            <strong style="font-size:14px;color:#1f2937;"><?= e($booking['booking_number']) ?></strong>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid #f3f4f6;">
                        <div style="width:36px;height:36px;background:#f0fdf4;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <span style="font-size:12px;color:#9ca3af;">Valor Total</span>
                            <strong style="font-size:14px;color:#1f2937;">$<?= number_format((float)($booking['total'] ?? 0), 2) ?> USD</strong>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid #f3f4f6;">
                        <div style="width:36px;height:36px;background:#d1fae5;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <span style="font-size:12px;color:#9ca3af;">Valor Pago</span>
                            <strong style="font-size:14px;color:#059669;">$<?= number_format((float)($booking['paid_amount'] ?? 0), 2) ?> USD</strong>
                        </div>
                    </div>
                    <?php if ((float)($booking['due_amount'] ?? 0) > 0): ?>
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 0;border-bottom:1px solid #f3f4f6;">
                        <div style="width:36px;height:36px;background:#fef3c7;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <span style="font-size:12px;color:#9ca3af;">Saldo Pendente</span>
                            <strong style="font-size:14px;color:#d97706;">$<?= number_format((float)($booking['due_amount'] ?? 0), 2) ?> USD</strong>
                        </div>
                    </div>
                    <?php endif; ?>
                    <div style="display:flex;align-items:center;gap:14px;padding:14px 0;">
                        <div style="width:36px;height:36px;background:#fef3c7;border-radius:8px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </div>
                        <div style="display:flex;flex-direction:column;gap:2px;">
                            <span style="font-size:12px;color:#9ca3af;">E-mail de Confirmação</span>
                            <strong style="font-size:14px;color:#1f2937;"><?= e($booking['billing_email'] ?? '') ?></strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passeios -->
            <?php if (!empty($items)): ?>
            <div style="text-align:left;margin-bottom:24px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;">
                <h4 style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#374151;margin-bottom:14px;text-transform:uppercase;letter-spacing:0.5px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/></svg>
                    Passeios Reservados
                </h4>
                <?php foreach ($items as $item): ?>
                <div style="padding:12px 0;border-bottom:1px solid #f3f4f6;">
                    <strong style="display:block;font-size:14px;color:#1f2937;margin-bottom:2px;"><?= e($item['trip_title'] ?? 'Passeio') ?></strong>
                    <span style="font-size:13px;color:#6b7280;"><?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '' ?><?php if (!empty($item['trip_time'])): ?> às <?= e($item['trip_time']) ?><?php endif; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($transfers)): ?>
            <div style="text-align:left;margin-bottom:24px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:12px;padding:20px 24px;">
                <h4 style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#374151;margin-bottom:14px;text-transform:uppercase;letter-spacing:0.5px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    Transfers
                </h4>
                <?php foreach ($transfers as $tr): ?>
                <div style="padding:12px 0;border-bottom:1px solid #f3f4f6;">
                    <strong style="display:block;font-size:14px;color:#1f2937;margin-bottom:2px;"><?= e(($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? '')) ?></strong>
                    <span style="font-size:13px;color:#6b7280;"><?= !empty($tr['date']) ? date('d/m/Y', strtotime($tr['date'])) : '' ?><?php if (!empty($tr['time'])): ?> às <?= e($tr['time']) ?><?php endif; ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Documentos Extras -->
            <?php if (!empty($tripDocuments)): ?>
            <div style="text-align:left;margin-bottom:28px;background:#fffbeb;border:1px solid #fbbf24;border-radius:12px;padding:20px 24px;">
                <h4 style="display:flex;align-items:center;gap:8px;font-size:13px;font-weight:600;color:#92400e;margin-bottom:14px;text-transform:uppercase;letter-spacing:0.5px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Documentos do Passeio
                </h4>
                <p style="font-size:13px;color:#78350f;margin-bottom:14px;line-height:1.6;">Baixe os documentos abaixo antes da data do passeio.</p>
                <?php foreach ($tripDocuments as $tDoc): ?>
                <a href="<?= e($tDoc['path']) ?>" target="_blank" download style="display:flex;align-items:center;gap:12px;padding:10px 14px;background:#fff;border:1px solid #fef3c7;border-radius:8px;margin-bottom:8px;text-decoration:none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#92400e" stroke-width="2" style="flex-shrink:0;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span style="flex:1;">
                        <strong style="font-size:13px;color:#1f2937;display:block;"><?= e($tDoc['name'] ?? 'Documento') ?></strong>
                        <span style="font-size:11px;color:#6b7280;"><?= e($tDoc['trip_name'] ?? '') ?> • <?= e(strtoupper($tDoc['type'] ?? '')) ?></span>
                    </span>
                    <span style="font-size:11px;color:#1B6F00;font-weight:600;">Baixar</span>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- Info -->
            <div style="display:flex;gap:14px;align-items:flex-start;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:18px 22px;margin-bottom:32px;text-align:left;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" style="flex-shrink:0;margin-top:2px;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                <div>
                    <p style="font-size:13px;color:#166534;line-height:1.7;margin:0 0 6px;">Enviamos um e-mail de confirmação para <strong><?= e($booking['billing_email'] ?? '') ?></strong> com todos os detalhes e vouchers.</p>
                    <p style="font-size:13px;color:#166534;line-height:1.7;margin:0;">Dúvidas? Fale conosco pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170" style="color:#15803d;font-weight:600;">+1 (829) 458-2170</a></p>
                </div>
            </div>

            <?php else: ?>
            <h2 style="font-size:26px;font-weight:700;color:#1f2937;margin-bottom:8px;">Reserva Confirmada!</h2>
            <p style="font-size:15px;color:#6b7280;margin-bottom:36px;line-height:1.6;">Sua reserva foi processada com sucesso.<br>Você receberá um e-mail com os detalhes em breve.</p>
            <?php endif; ?>

            <!-- Botões -->
            <div style="display:flex;gap:14px;justify-content:center;flex-wrap:wrap;">
                <a href="/passeios" class="btn btn-primary btn-lg">Ver Mais Passeios</a>
                <a href="/minha-conta/reservas" class="btn btn-outline btn-lg">Minhas Reservas</a>
            </div>
        </div>
    </div>
</section>
