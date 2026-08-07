<div class="account-layout">
    <?= partial('account-sidebar') ?>
    <div class="account-content">
        <h2>Cancelamentos</h2>
        <?php if (empty($bookings)): ?>
        <div class="empty-state"><p>Nenhuma reserva encontrada.</p><a href="/passeios" class="btn btn-primary">Ver Passeios</a></div>
        <?php else: ?>
        <table class="table">
            <thead><tr><th>Número</th><th>Serviço</th><th>Data</th><th>Total</th><th>Pago</th><th>Status</th><th>Ações</th></tr></thead>
            <tbody>
            <?php foreach ($bookings as $b): ?>
            <tr>
                <td><?= e($b['booking_number'] ?? '#' . (int)$b['id']) ?></td>
                <td style="font-size:13px;color:#374151;max-width:200px;"><?= e($b['trip_title']) ?></td>
                <td><?= $b['trip_date'] ? format_date($b['trip_date']) : format_date($b['created_at']) ?></td>
                <td><?= money((float)($b['total'] ?? 0)) ?></td>
                <td><?= money((float)($b['paid_amount'] ?? 0)) ?></td>
                <td><span class="badge badge-<?= booking_status_class($b['status']) ?>"><?= booking_status_label($b['status']) ?></span></td>
                <td>
                    <?php if (in_array($b['status'], ['booked', 'pending', 'partially_paid'])): ?>
                    <form method="POST" action="/minha-conta/cancelamentos/solicitar" onsubmit="return confirm('Tem certeza que deseja solicitar o cancelamento desta reserva?')" style="display:inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                        <button type="submit" class="btn btn-sm btn-danger">Cancelar</button>
                    </form>
                    <?php elseif ($b['status'] === 'cancelled'): ?>
                    <span class="btn btn-sm btn-outline" style="pointer-events:none;opacity:.6;">Solicitado</span>
                    <?php elseif ($b['status'] === 'refunded'): ?>
                    <span class="btn btn-sm btn-outline" style="pointer-events:none;opacity:.6;">Reembolsado</span>
                    <?php else: ?>
                    <span class="btn btn-sm btn-outline" style="pointer-events:none;opacity:.6;">Indisponível</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
</div>
