<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden">
    <div style="background:#0077b6;color:#fff;padding:25px;text-align:center">
        <h1 style="margin:0;font-size:22px">Reserva Confirmada!</h1>
    </div>
    <div style="padding:25px">
        <p>Olá <strong><?= e($booking['billing_first_name']) ?></strong>,</p>
        <p>Sua reserva foi confirmada com sucesso!</p>

        <div style="background:#f8f9fa;padding:15px;border-radius:5px;margin:20px 0">
            <p style="margin:5px 0"><strong>Número da Reserva:</strong> <?= e($booking['booking_number']) ?></p>
            <p style="margin:5px 0"><strong>Total:</strong> <?= money((float)$booking['total']) ?></p>
            <p style="margin:5px 0"><strong>Pago:</strong> <?= money((float)$booking['paid_amount']) ?></p>
            <?php if ((float)$booking['due_amount'] > 0): ?>
            <p style="margin:5px 0"><strong>Restante:</strong> <?= money((float)$booking['due_amount']) ?></p>
            <?php endif; ?>
        </div>

        <p>Seus vouchers estão em anexo neste email. Apresente-os no dia do passeio/transfer.</p>
        <p style="margin-top:25px">Boa viagem!<br><strong><?= e(setting('site_name', 'Punta Cana para Brasileiros')) ?></strong></p>
    </div>
    <div style="background:#f1f1f1;padding:15px;text-align:center;font-size:12px;color:#666">
        <?= e(setting('voucher_footer_text', '')) ?>
    </div>
</div>
</body></html>
