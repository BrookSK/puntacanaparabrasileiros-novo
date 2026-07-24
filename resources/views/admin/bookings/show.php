<div class="card-header">
    <h2>Reserva: <?= e($booking['booking_number'] ?? '') ?></h2>
    <a href="/admin/reservas" class="btn btn-sm btn-outline">&larr; Voltar</a>
</div>

<div class="admin-grid-2">
    <!-- Informações do Cliente -->
    <div class="admin-card">
        <h3>Dados do Cliente</h3>
        <table class="table-info">
            <tr><td><strong>Nome:</strong></td><td><?= e(($booking['billing_first_name'] ?? '') . ' ' . ($booking['billing_last_name'] ?? '')) ?></td></tr>
            <tr><td><strong>E-mail:</strong></td><td><?= e($booking['billing_email'] ?? '-') ?></td></tr>
            <tr><td><strong>Telefone:</strong></td><td><?= e($booking['billing_phone'] ?? '-') ?></td></tr>
            <tr><td><strong>País:</strong></td><td><?= e($booking['billing_country'] ?? '-') ?></td></tr>
        </table>
    </div>

    <!-- Informações da Reserva -->
    <div class="admin-card">
        <h3>Detalhes da Reserva</h3>
        <table class="table-info">
            <tr><td><strong>Nº Reserva:</strong></td><td><?= e($booking['booking_number'] ?? '') ?></td></tr>
            <tr><td><strong>Status:</strong></td><td>
                <?php
                    $statusLabels = ['pending' => 'Pendente', 'booked' => 'Confirmado', 'partially_paid' => 'Parc. Pago', 'completed' => 'Concluído', 'cancelled' => 'Cancelado', 'refunded' => 'Reembolsado'];
                    $st = $booking['status'] ?? 'pending';
                ?>
                <span class="badge badge-<?= $st === 'booked' || $st === 'completed' ? 'success' : ($st === 'cancelled' ? 'danger' : 'warning') ?>"><?= $statusLabels[$st] ?? $st ?></span>
            </td></tr>
            <tr><td><strong>Subtotal:</strong></td><td>$<?= number_format((float)($booking['subtotal'] ?? 0), 2) ?></td></tr>
            <tr><td><strong>Total:</strong></td><td><strong>$<?= number_format((float)($booking['total'] ?? $booking['subtotal'] ?? 0), 2) ?></strong></td></tr>
            <tr><td><strong>Pago:</strong></td><td>$<?= number_format((float)($booking['paid_amount'] ?? 0), 2) ?></td></tr>
            <tr><td><strong>Pendente:</strong></td><td>$<?= number_format((float)($booking['due_amount'] ?? 0), 2) ?></td></tr>
            <tr><td><strong>Criado em:</strong></td><td><?= !empty($booking['created_at']) ? date('d/m/Y H:i', strtotime($booking['created_at'])) : '-' ?></td></tr>
        </table>

        <!-- Alterar Status -->
        <form method="POST" action="/admin/reservas/<?= (int)$booking['id'] ?>/status" class="inline-form" style="margin-top:12px;">
            <?= csrf_field() ?>
            <select name="status" class="form-control" style="width:auto;display:inline-block;">
                <option value="pending" <?= $st === 'pending' ? 'selected' : '' ?>>Pendente</option>
                <option value="booked" <?= $st === 'booked' ? 'selected' : '' ?>>Confirmado</option>
                <option value="partially_paid" <?= $st === 'partially_paid' ? 'selected' : '' ?>>Parc. Pago</option>
                <option value="completed" <?= $st === 'completed' ? 'selected' : '' ?>>Concluído</option>
                <option value="cancelled" <?= $st === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                <option value="refunded" <?= $st === 'refunded' ? 'selected' : '' ?>>Reembolsado</option>
            </select>
            <button type="submit" class="btn btn-sm btn-primary">Atualizar Status</button>
        </form>
    </div>
</div>

<!-- Itens da Reserva (Passeios) -->
<?php if (!empty($items)): ?>
<div class="admin-card">
    <h3>Passeios Reservados</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Passeio</th>
                <th>Pacote</th>
                <th>Data</th>
                <th>Viajantes</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item): ?>
            <tr>
                <td><?= e($item['trip_title'] ?? $item['title'] ?? '-') ?></td>
                <td><?= e($item['package_title'] ?? '-') ?></td>
                <td><?= !empty($item['trip_date']) ? date('d/m/Y', strtotime($item['trip_date'])) : '-' ?></td>
                <td>
                    <?php if (!empty($item['travelers'])): ?>
                        <?php foreach ($item['travelers'] as $t): ?>
                        <small><?= e($t['category_name'] ?? 'Viajante') ?>: <?= (int)$t['quantity'] ?> x $<?= number_format((float)($t['unit_price'] ?? 0), 2) ?></small><br>
                        <?php endforeach; ?>
                    <?php else: ?>
                        -
                    <?php endif; ?>
                </td>
                <td>$<?= number_format((float)($item['subtotal'] ?? 0), 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Transfers -->
<?php if (!empty($transfers)): ?>
<div class="admin-card">
    <h3>Transfers</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Rota</th>
                <th>Data</th>
                <th>Hora</th>
                <th>Veículo</th>
                <th>Passageiros</th>
                <th>Valor</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transfers as $tr): ?>
            <tr>
                <td><?= e(($tr['origin_title'] ?? '?') . ' → ' . ($tr['destination_title'] ?? '?')) ?></td>
                <td><?= !empty($tr['transfer_date']) ? date('d/m/Y', strtotime($tr['transfer_date'])) : '-' ?></td>
                <td><?= e($tr['transfer_time'] ?? '-') ?></td>
                <td><?= e($tr['vehicle_title'] ?? '-') ?></td>
                <td><?= (int)($tr['adults'] ?? 0) ?> ad. + <?= (int)($tr['children'] ?? 0) ?> cr.</td>
                <td>$<?= number_format((float)($tr['price'] ?? 0), 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Pagamentos -->
<?php if (!empty($payments)): ?>
<div class="admin-card">
    <h3>Pagamentos</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Gateway</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Data</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($payments as $pay): ?>
            <tr>
                <td><?= e(ucfirst($pay['gateway'] ?? '-')) ?></td>
                <td><?= e($pay['type'] ?? '-') ?></td>
                <td>$<?= number_format((float)($pay['amount'] ?? 0), 2) ?></td>
                <td><span class="badge badge-<?= ($pay['status'] ?? '') === 'completed' ? 'success' : 'warning' ?>"><?= e($pay['status'] ?? '-') ?></span></td>
                <td><?= !empty($pay['created_at']) ? date('d/m/Y H:i', strtotime($pay['created_at'])) : '-' ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Vouchers -->
<?php if (!empty($vouchers)): ?>
<div class="admin-card">
    <h3>Vouchers Gerados</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Tipo</th>
                <th>Status</th>
                <th>PDF</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vouchers as $vc): ?>
            <tr>
                <td><?= e($vc['voucher_code'] ?? '-') ?></td>
                <td><?= e($vc['type'] ?? '-') ?></td>
                <td><span class="badge badge-<?= ($vc['status'] ?? '') === 'active' ? 'success' : 'secondary' ?>"><?= e($vc['status'] ?? '-') ?></span></td>
                <td>
                    <?php if (!empty($vc['pdf_url'])): ?>
                    <a href="<?= e($vc['pdf_url']) ?>" target="_blank" class="btn btn-sm btn-outline">Download</a>
                    <?php else: ?>
                    -
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php if (!empty($booking['notes'])): ?>
<div class="admin-card">
    <h3>Observações</h3>
    <p><?= nl2br(e($booking['notes'])) ?></p>
</div>
<?php endif; ?>
