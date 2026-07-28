<h2>Nova Reserva Recebida!</h2>

<p>Uma nova reserva foi realizada no sistema.</p>

<table style="width:100%;border-collapse:collapse;margin:20px 0;">
    <tr>
        <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Nº Reserva:</td>
        <td style="padding:8px;border-bottom:1px solid #eee;"><?= e($booking['booking_number'] ?? '-') ?></td>
    </tr>
    <tr>
        <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Cliente:</td>
        <td style="padding:8px;border-bottom:1px solid #eee;"><?= e(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? '')) ?></td>
    </tr>
    <tr>
        <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Email:</td>
        <td style="padding:8px;border-bottom:1px solid #eee;"><?= e($booking['billing_email'] ?? '-') ?></td>
    </tr>
    <tr>
        <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Telefone:</td>
        <td style="padding:8px;border-bottom:1px solid #eee;"><?= e($booking['billing_phone'] ?? '-') ?></td>
    </tr>
    <tr>
        <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Total:</td>
        <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;color:#16a34a;">$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?> USD</td>
    </tr>
    <tr>
        <td style="padding:8px;border-bottom:1px solid #eee;font-weight:bold;">Status:</td>
        <td style="padding:8px;border-bottom:1px solid #eee;"><?= e($booking['status'] ?? 'pending') ?></td>
    </tr>
</table>

<?php if (!empty($items)): ?>
<h3>Passeios:</h3>
<ul>
    <?php foreach ($items as $item): ?>
    <li><?= e($item['trip_title'] ?? 'Passeio') ?> — <?= e($item['trip_date'] ?? '') ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<?php if (!empty($transfers)): ?>
<h3>Transfers:</h3>
<ul>
    <?php foreach ($transfers as $tr): ?>
    <li><?= e(($tr['origin_title'] ?? '') . ' → ' . ($tr['destination_title'] ?? '')) ?> — <?= e($tr['transfer_date'] ?? $tr['date'] ?? '') ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>

<p style="margin-top:20px;">
    <a href="<?= e($siteUrl ?? '') ?>/admin/reservas" style="background:#0077b6;color:white;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;">Ver no Painel Admin</a>
</p>
