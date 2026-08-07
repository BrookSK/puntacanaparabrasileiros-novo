<!DOCTYPE html>
<html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
<body style="font-family:'Segoe UI',Arial,sans-serif;background:#f4f7f6;margin:0;padding:30px 20px;">
<div style="max-width:600px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,0.08);">

    <!-- Header -->
    <div style="background:linear-gradient(135deg,#1B6F00 0%,#2a8f10 100%);padding:32px 30px;text-align:center;">
        <img src="<?= url('/assets/images/layout/PUNTA-CANA-1.png') ?>" alt="Punta Cana para Brasileiros" style="height:48px;margin-bottom:14px;display:inline-block;">
        <h1 style="margin:0;font-size:22px;color:#ffffff;font-weight:700;"><?= e($emailTitle ?? 'Cancelamento') ?></h1>
    </div>

    <!-- Body -->
    <div style="padding:32px 30px;">
        <p style="font-size:15px;color:#1e293b;margin:0 0 20px;line-height:1.6;">
            Olá, <strong><?= e($clientName ?? 'Cliente') ?></strong>!
        </p>

        <p style="font-size:15px;color:#374151;margin:0 0 24px;line-height:1.6;">
            <?= $emailMessage ?? '' ?>
        </p>

        <!-- Info Box -->
        <div style="background:#f8fafb;border:1px solid #e8eeec;border-radius:8px;padding:20px;margin:0 0 24px;">
            <table style="width:100%;border-collapse:collapse;">
                <tr>
                    <td style="padding:8px 0;font-size:13px;color:#64748b;width:140px;">Nº da Reserva</td>
                    <td style="padding:8px 0;font-size:14px;color:#1e293b;font-weight:600;"><?= e($bookingNumber ?? '') ?></td>
                </tr>
                <?php if (!empty($serviceName)): ?>
                <tr>
                    <td style="padding:8px 0;font-size:13px;color:#64748b;border-top:1px solid #f0f0f0;">Passeio</td>
                    <td style="padding:8px 0;font-size:14px;color:#1e293b;font-weight:600;border-top:1px solid #f0f0f0;"><?= e($serviceName) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($bookingTotal)): ?>
                <tr>
                    <td style="padding:8px 0;font-size:13px;color:#64748b;border-top:1px solid #f0f0f0;">Valor da Reserva</td>
                    <td style="padding:8px 0;font-size:14px;color:#1e293b;font-weight:600;border-top:1px solid #f0f0f0;">$<?= number_format((float)$bookingTotal, 2) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($refundAmount)): ?>
                <tr>
                    <td style="padding:8px 0;font-size:13px;color:#64748b;border-top:1px solid #f0f0f0;">Valor Reembolsado</td>
                    <td style="padding:8px 0;font-size:14px;color:#1B6F00;font-weight:700;border-top:1px solid #f0f0f0;">$<?= number_format((float)$refundAmount, 2) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($statusLabel)): ?>
                <tr>
                    <td style="padding:8px 0;font-size:13px;color:#64748b;border-top:1px solid #f0f0f0;">Status</td>
                    <td style="padding:8px 0;font-size:14px;color:<?= $statusColor ?? '#1e293b' ?>;font-weight:600;border-top:1px solid #f0f0f0;"><?= e($statusLabel) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Blockquote (motivo ou resposta) -->
        <?php if (!empty($blockquoteText)): ?>
        <div style="border-left:4px solid <?= $blockquoteColor ?? '#1B6F00' ?>;background:<?= $blockquoteBg ?? '#f0fdf4' ?>;padding:16px 20px;border-radius:0 8px 8px 0;margin:0 0 24px;">
            <p style="margin:0 0 6px;font-size:12px;color:#64748b;text-transform:uppercase;letter-spacing:0.5px;font-weight:600;"><?= e($blockquoteLabel ?? 'Mensagem') ?></p>
            <p style="margin:0;font-size:14px;color:#374151;line-height:1.6;"><?= nl2br(e($blockquoteText)) ?></p>
        </div>
        <?php endif; ?>

        <!-- Mensagem adicional -->
        <?php if (!empty($additionalMessage)): ?>
        <p style="font-size:14px;color:#64748b;margin:0 0 24px;line-height:1.6;">
            <?= $additionalMessage ?>
        </p>
        <?php endif; ?>

        <!-- CTA Button -->
        <?php if (!empty($ctaUrl) && !empty($ctaText)): ?>
        <div style="text-align:center;margin:28px 0;">
            <a href="<?= e($ctaUrl) ?>" style="display:inline-block;padding:14px 32px;background:#1B6F00;color:#ffffff;border-radius:8px;font-size:14px;font-weight:600;text-decoration:none;"><?= e($ctaText) ?></a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <div style="background:#f8fafb;border-top:1px solid #e8eeec;padding:24px 30px;text-align:center;">
        <p style="margin:0 0 8px;font-size:13px;color:#64748b;">Equipe Punta Cana para Brasileiros</p>
        <p style="margin:0;font-size:11px;color:#94a3b8;">Este é um e-mail automático. Em caso de dúvidas, entre em contato pelo WhatsApp.</p>
    </div>

</div>
</body></html>
