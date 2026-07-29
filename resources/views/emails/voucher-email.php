<div style="max-width:600px;margin:0 auto;font-family:'Segoe UI',Arial,sans-serif;color:#1a1a1a;">
    <!-- Header -->
    <div style="background:#2d3748;padding:30px;text-align:center;border-radius:10px 10px 0 0;">
        <img src="https://puntacananovo.lrvweb.com.br/assets/images/layout/PUNTA-CANA-1.png" alt="Punta Cana para Brasileiros" style="max-height:60px;margin-bottom:12px;">
        <h1 style="color:#fff;font-size:22px;margin:0;">Seus Vouchers</h1>
    </div>

    <!-- Body -->
    <div style="background:#fff;padding:30px 40px;border:1px solid #e5e7eb;border-top:none;">
        <p style="font-size:16px;margin-bottom:6px;">Olá <strong><?= e($booking['billing_first_name'] ?? 'Cliente') ?></strong>,</p>
        <p style="font-size:14px;color:#555;line-height:1.7;margin-bottom:24px;">
            Sua reserva <strong><?= e($booking['booking_number'] ?? '') ?></strong> foi confirmada com sucesso!
            Seguem em anexo os vouchers da sua viagem.
        </p>

        <!-- Resumo da Reserva -->
        <div style="background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;padding:20px;margin-bottom:24px;">
            <h3 style="font-size:14px;margin:0 0 12px;color:#1B6F00;text-transform:uppercase;letter-spacing:0.5px;">Resumo da Reserva</h3>
            <table style="width:100%;font-size:14px;">
                <tr><td style="padding:6px 0;color:#666;">Número:</td><td style="padding:6px 0;font-weight:600;"><?= e($booking['booking_number'] ?? '') ?></td></tr>
                <tr><td style="padding:6px 0;color:#666;">Total:</td><td style="padding:6px 0;font-weight:600;">$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?> USD</td></tr>
                <tr><td style="padding:6px 0;color:#666;">Status:</td><td style="padding:6px 0;font-weight:600;color:#16a34a;">Confirmado</td></tr>
            </table>
        </div>

        <!-- Instruções -->
        <div style="background:#fffbeb;border:1px solid #fbbf24;border-radius:8px;padding:16px 20px;margin-bottom:24px;">
            <p style="font-size:13px;color:#78350f;margin:0;line-height:1.7;">
                <strong>Importante:</strong> Apresente os vouchers (impressos ou no celular) no ponto de encontro.
                O horário exato será confirmado 1 dia antes via WhatsApp.
            </p>
        </div>

        <!-- Vouchers listados -->
        <h3 style="font-size:14px;margin:0 0 12px;color:#333;">Vouchers Anexados:</h3>
        <?php foreach ($vouchers as $v): ?>
        <div style="display:flex;align-items:center;gap:10px;padding:12px 14px;background:#f3f4f6;border-radius:6px;margin-bottom:8px;">
            <span style="font-size:20px;">📄</span>
            <div>
                <strong style="font-size:13px;color:#1a1a1a;">
                    <?php if (($v['type'] ?? '') === 'transfer'): ?>
                        VOUCHER TRANSFER
                    <?php else: ?>
                        VOUCHER PASSEIO<?php if (!empty($v['trip_name'])): ?> — <?= e($v['trip_name']) ?><?php endif; ?>
                    <?php endif; ?>
                </strong>
                <span style="display:block;font-size:11px;color:#888;margin-top:2px;">Código: <?= e($v['reference_code'] ?? '') ?></span>
            </div>
        </div>
        <?php endforeach; ?>

        <p style="font-size:13px;color:#666;margin-top:20px;line-height:1.7;">
            Dúvidas? Fale conosco pelo WhatsApp: <a href="https://api.whatsapp.com/send?phone=18294582170" style="color:#1B6F00;font-weight:600;">+1 (829) 458-2170</a>
        </p>
    </div>

    <!-- Footer -->
    <div style="background:#f8f8f8;padding:20px 40px;text-align:center;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 10px 10px;">
        <p style="font-size:11px;color:#888;margin:2px 0;"><strong>Punta Cana para Brasileiros Oliveira & Ramos SRL</strong></p>
        <p style="font-size:11px;color:#888;margin:2px 0;">RNC: 1-33-28776-5 | Punta Cana, República Dominicana</p>
        <p style="font-size:11px;color:#888;margin:2px 0;">contato@puntacanaparabrasileiros.com | +1 (829) 458-2170</p>
    </div>
</div>
