<div class="card-header">
    <div class="header-actions">
        <h3 class="section-subtitle">Selecione um passeio para gerenciar os horários de pickup por hotel</h3>
    </div>
    <form method="GET" class="filter-form">
        <input type="text" name="busca" value="<?= e($currentSearch ?? '') ?>" placeholder="Buscar passeio..." class="form-control">
        <button type="submit" class="btn btn-outline">Filtrar</button>
    </form>
</div>

<div class="table-responsive">
    <table class="admin-table">
        <thead>
            <tr>
                <th>Passeio</th>
                <th class="text-center">Hotéis Cadastrados</th>
                <th class="text-center">Status</th>
                <th class="text-right">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($trips['items'])): ?>
            <tr>
                <td colspan="4" class="text-center text-muted">Nenhum passeio encontrado.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($trips['items'] as $trip): ?>
            <tr>
                <td>
                    <strong><?= e($trip['title']) ?></strong>
                    <br><small class="text-muted">/passeios/<?= e($trip['slug']) ?></small>
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
                <td class="text-right">
                    <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-sm btn-primary">Gerenciar Horários</a>
                    <a href="/admin/horarios/<?= (int) $trip['id'] ?>/importar" class="btn btn-sm btn-outline">Importar Excel</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php if ($trips['total_pages'] > 1): ?>
<nav class="pagination">
    <?php for ($i = 1; $i <= $trips['total_pages']; $i++): ?>
    <a href="?page=<?= $i ?>&busca=<?= e($currentSearch ?? '') ?>" class="page-link <?= $i === $trips['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>
