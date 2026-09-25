<div class="card-header">
    <div class="header-actions">
        <a href="/admin/suporte/criar" class="btn btn-primary">+ Nova Demanda</a>
    </div>
</div>

<?php if (empty($lrvConfigured)): ?>
<div class="alert alert-info">
    <span>A integração com o LRV ainda não está configurada. As demandas serão gravadas localmente e ficarão pendentes de sincronização. Configure em <strong>Configurações → Integração LRV</strong>.</span>
</div>
<?php endif; ?>

<?php
    $priorityLabels = ['low' => 'Baixa', 'medium' => 'Média', 'high' => 'Alta', 'urgent' => 'Urgente'];
    $priorityColors = ['low' => 'secondary', 'medium' => 'info', 'high' => 'warning', 'urgent' => 'danger'];
    $syncLabels = ['synced' => 'Sincronizado', 'pending' => 'Pendente', 'failed' => 'Falhou'];
    $syncColors = ['synced' => 'success', 'pending' => 'warning', 'failed' => 'danger'];
?>

<table class="table">
    <thead>
        <tr>
            <th>Título</th>
            <th>Prioridade</th>
            <th>Categoria</th>
            <th>Ref. Externa</th>
            <th>LRV</th>
            <th>Status</th>
            <th>Criado em</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($tickets['items'])): ?>
        <tr>
            <td colspan="8" class="text-center">Nenhuma demanda de suporte criada.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($tickets['items'] as $t): ?>
        <tr>
            <td>
                <strong><?= e($t['title']) ?></strong>
                <?php if (!empty($t['description'])): ?>
                <br><small style="color:#636e72;"><?= e(mb_strimwidth((string)$t['description'], 0, 60, '...')) ?></small>
                <?php endif; ?>
            </td>
            <td>
                <?php $p = $t['priority'] ?? 'medium'; ?>
                <span class="badge badge-<?= $priorityColors[$p] ?? 'secondary' ?>"><?= $priorityLabels[$p] ?? $p ?></span>
            </td>
            <td><?= e($t['category'] ?? '-') ?: '-' ?></td>
            <td><small><?= e($t['external_ref'] ?? '-') ?></small></td>
            <td>
                <?php if (!empty($t['lrv_client_ticket_number'])): ?>
                    #<?= (int) $t['lrv_client_ticket_number'] ?>
                <?php else: ?>
                    -
                <?php endif; ?>
            </td>
            <td>
                <?php $s = $t['sync_status'] ?? 'pending'; ?>
                <span class="badge badge-<?= $syncColors[$s] ?? 'secondary' ?>"><?= $syncLabels[$s] ?? $s ?></span>
                <?php if ($s === 'failed' && !empty($t['sync_error'])): ?>
                <br><small style="color:#636e72;" title="<?= e((string)$t['sync_error']) ?>"><?= e(mb_strimwidth((string)$t['sync_error'], 0, 40, '...')) ?></small>
                <?php endif; ?>
            </td>
            <td><?= !empty($t['created_at']) ? date('d/m/Y H:i', strtotime((string)$t['created_at'])) : '-' ?></td>
            <td class="actions-cell">
                <?php if (($t['sync_status'] ?? '') !== 'synced'): ?>
                <form method="POST" action="/admin/suporte/<?= (int)$t['id'] ?>/reenviar" class="inline-form" onsubmit="return confirm('Reenviar esta demanda ao LRV?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline">Reenviar ao LRV</button>
                </form>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($tickets['total_pages']) && $tickets['total_pages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $tickets['total_pages']; $p++): ?>
    <a href="?page=<?= $p ?>" class="pagination-btn <?= $p === ($tickets['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
