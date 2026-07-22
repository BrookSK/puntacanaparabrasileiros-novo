<!DOCTYPE html>
<html><head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;margin:0;padding:20px">
<div style="max-width:600px;margin:0 auto;background:#fff;border-radius:8px;overflow:hidden">
    <div style="background:#0077b6;color:#fff;padding:25px;text-align:center">
        <h1 style="margin:0;font-size:22px">Seus Vouchers</h1>
    </div>
    <div style="padding:25px">
        <p>Olá <strong><?= e($booking['billing_first_name']) ?></strong>,</p>
        <p>Seguem em anexo os vouchers da sua reserva <strong><?= e($booking['booking_number']) ?></strong>.</p>
        <p>Apresente os vouchers (impressos ou no celular) no ponto de encontro.</p>
        <p style="margin-top:25px">Boa viagem!<br><strong><?= e(setting('site_name', '')) ?></strong></p>
    </div>
</div>
</body></html>
