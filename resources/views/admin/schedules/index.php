<div class="card-header">
    <div class="header-actions">
        <p style="margin:0;color:#94a3b8;font-size:0.9rem;">Selecione um passeio para gerenciar os horários de pickup por hotel</p>
    </div>
    <form method="GET" class="filter-form">
        <input type="text" name="busca" value="<?= e($currentSearch ?? '') ?>" placeholder="Buscar passeio..." class="form-control">
        <button type="submit" class="btn btn-outline">Filtrar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Passeio</th>
            <th class="text-center">Hotéis</th>
            <th class="text-center">Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($trips['items'])): ?>
        <tr>
            <td colspan="4" class="text-center">Nenhum passeio encontrado.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($trips['items'] as $trip): ?>
        <tr>
            <td>
                <strong><?= e($trip['title']) ?></strong>
                <br><span style="font-size:12px;color:#64748b;">/passeios/<?= e($trip['slug']) ?></span>
            </td>
            <td class="text-center">
                <?php if ($trip['hotels_count'] > 0): ?>
                <span class="badge badge-success"><?= (int) $trip['hotels_count'] ?> hotel(éis)</span>
                <?php else: ?>
                <span class="badge badge-secondary">Nenhum</span>
                <?php endif; ?>
            </td>
            <td class="text-center">
                <?php
                    $statusLabel = ['published' => 'Publicado', 'draft' => 'Rascunho', 'disabled' => 'Desativado'];
                    $statusColor = ['published' => 'success', 'draft' => 'warning', 'disabled' => 'secondary'];
                    $st = $trip['status'] ?? 'draft';
                ?>
                <span class="badge badge-<?= $statusColor[$st] ?? 'secondary' ?>"><?= $statusLabel[$st] ?? $st ?></span>
            </td>
            <td class="actions-cell">
                <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-sm btn-primary">Gerenciar Horários</a>
                <a href="/admin/horarios/<?= (int) $trip['id'] ?>/importar" class="btn btn-sm btn-outline">Importar Excel</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($trips['total_pages']) && $trips['total_pages'] > 1): ?>
<div class="pagination" style="margin-top:1.5rem;display:flex;align-items:center;gap:6px;">
    <?php if ($trips['current_page'] > 1): ?>
    <a href="?page=<?= $trips['current_page'] - 1 ?>&busca=<?= e($currentSearch ?? '') ?>" class="page-link">&laquo;</a>
    <?php endif; ?>
    <?php for ($p = 1; $p <= $trips['total_pages']; $p++): ?>
    <a href="?page=<?= $p ?>&busca=<?= e($currentSearch ?? '') ?>" class="page-link <?= $p === ($trips['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
    <?php if ($trips['current_page'] < $trips['total_pages']): ?>
    <a href="?page=<?= $trips['current_page'] + 1 ?>&busca=<?= e($currentSearch ?? '') ?>" class="page-link">&raquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>
