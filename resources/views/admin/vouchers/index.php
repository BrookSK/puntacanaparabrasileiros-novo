<div class="card-header">
    <h2>Gerenciar Vouchers</h2>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Reserva</th>
            <th>Cliente</th>
            <th>Tipo</th>
            <th>Status</th>
            <th>Enviado</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($vouchers['data'])): ?>
        <tr>
            <td colspan="7" class="text-center">Nenhum voucher encontrado.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($vouchers['data'] as $voucher): ?>
        <tr>
            <td><strong><?= e($voucher['reference_code'] ?? '-') ?></strong></td>
            <td>
                <?php if (!empty($voucher['booking_id'])): ?>
                <a href="/admin/reservas/<?= (int)$voucher['booking_id'] ?>">#<?= e($voucher['booking_number'] ?? $voucher['booking_id']) ?></a>
                <?php else: ?>
                -
                <?php endif; ?>
            </td>
            <td><?= e(($voucher['billing_first_name'] ?? '') . ' ' . ($voucher['billing_last_name'] ?? '')) ?></td>
            <td><?= e(ucfirst($voucher['type'] ?? 'trip')) ?></td>
            <td>
                <span class="badge badge-<?= ($voucher['status'] ?? '') === 'active' ? 'success' : 'secondary' ?>">
                    <?= ($voucher['status'] ?? '') === 'active' ? 'Ativo' : e($voucher['status'] ?? '-') ?>
                </span>
            </td>
            <td>
                <?php if (!empty($voucher['email_sent_at'])): ?>
                <span class="badge badge-success">Sim</span>
                <?php else: ?>
                <span class="badge badge-warning">Não</span>
                <?php endif; ?>
            </td>
            <td class="actions-cell">
                <a href="/admin/vouchers/<?= (int)$voucher['id'] ?>/visualizar" class="btn btn-sm btn-outline" target="_blank">Ver</a>
                <a href="/admin/vouchers/<?= (int)$voucher['id'] ?>/download" class="btn btn-sm btn-outline">Download</a>
                <form method="POST" action="/admin/vouchers/<?= (int)$voucher['id'] ?>/enviar" class="inline-form">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-primary" onclick="return confirm('Enviar voucher por e-mail?')">Enviar</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($vouchers['totalPages']) && $vouchers['totalPages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $vouchers['totalPages']; $p++): ?>
    <a href="?page=<?= $p ?>" class="pagination-btn <?= $p === ($vouchers['currentPage'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
