<div class="card-header">
    <div class="header-actions">
        <a href="/admin/afiliados/comissoes" class="btn btn-outline">Gerenciar Comissões</a>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Afiliado</th>
            <th>E-mail</th>
            <th>Código</th>
            <th>Comissão (%)</th>
            <th>Total Vendas</th>
            <th>Total Pago</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($affiliates['data'])): ?>
        <tr>
            <td colspan="8" class="text-center">Nenhum afiliado cadastrado.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($affiliates['data'] as $affiliate): ?>
        <tr>
            <td><strong><?= e(($affiliate['first_name'] ?? '') . ' ' . ($affiliate['last_name'] ?? '')) ?></strong></td>
            <td><?= e($affiliate['email'] ?? '-') ?></td>
            <td><code><?= e($affiliate['referral_code'] ?? '-') ?></code></td>
            <td><?= number_format((float)($affiliate['commission_rate'] ?? 0), 1) ?>%</td>
            <td>$<?= number_format((float)($affiliate['total_sales'] ?? 0), 2) ?></td>
            <td>$<?= number_format((float)($affiliate['total_paid'] ?? 0), 2) ?></td>
            <td>
                <?php
                    $st = $affiliate['status'] ?? 'pending';
                    $stColor = $st === 'active' ? 'success' : ($st === 'rejected' ? 'danger' : 'warning');
                    $stLabel = $st === 'active' ? 'Ativo' : ($st === 'rejected' ? 'Rejeitado' : 'Pendente');
                ?>
                <span class="badge badge-<?= $stColor ?>"><?= $stLabel ?></span>
            </td>
            <td class="actions-cell">
                <?php if (($affiliate['status'] ?? '') === 'pending'): ?>
                <form method="POST" action="/admin/afiliados/<?= (int)$affiliate['id'] ?>/aprovar" class="inline-form">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-primary">Aprovar</button>
                </form>
                <form method="POST" action="/admin/afiliados/<?= (int)$affiliate['id'] ?>/rejeitar" class="inline-form">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Rejeitar este afiliado?')">Rejeitar</button>
                </form>
                <?php else: ?>
                <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($affiliates['totalPages']) && $affiliates['totalPages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $affiliates['totalPages']; $p++): ?>
    <a href="?page=<?= $p ?>" class="pagination-btn <?= $p === ($affiliates['currentPage'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
