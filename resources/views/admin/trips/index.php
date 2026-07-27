<div class="card-header">
    <div class="header-actions">
        <a href="/admin/passeios/criar" class="btn btn-primary">+ Novo Passeio</a>
    </div>
    <form method="GET" class="filter-form">
        <input type="text" name="busca" value="<?= e($currentSearch ?? '') ?>" placeholder="Buscar por nome..." class="form-control">
        <select name="status" class="form-control">
            <option value="">Todos os Status</option>
            <option value="published" <?= ($currentStatus ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
            <option value="draft" <?= ($currentStatus ?? '') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
            <option value="disabled" <?= ($currentStatus ?? '') === 'disabled' ? 'selected' : '' ?>>Desativado</option>
        </select>
        <button type="submit" class="btn btn-outline">Filtrar</button>
    </form>
</div>

<div class="trips-list">
    <?php foreach ($trips['items'] as $trip): ?>
    <div class="trip-list-item">
        <div class="trip-list-img">
            <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>">
        </div>
        <div class="trip-list-info">
            <h4 class="trip-list-title"><?= e($trip['title']) ?></h4>
            <span class="trip-list-slug">/passeios/<?= e($trip['slug']) ?></span>
        </div>
        <div class="trip-list-status">
            <?php
                $statusLabel = ['published' => 'Publicado', 'draft' => 'Rascunho', 'disabled' => 'Desativado'];
                $statusColor = ['published' => 'success', 'draft' => 'warning', 'disabled' => 'secondary'];
                $st = $trip['status'] ?? 'draft';
            ?>
            <span class="badge badge-<?= $statusColor[$st] ?? 'secondary' ?>"><?= $statusLabel[$st] ?? $st ?></span>
            <?php if ($trip['featured']): ?>
            <span class="trip-list-featured" title="Destaque">&#9733;</span>
            <?php endif; ?>
        </div>
        <div class="trip-list-actions">
            <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
            <a href="/admin/passeios/<?= (int)$trip['id'] ?>/precos" class="btn btn-sm btn-outline">Preços</a>
            <form method="POST" action="/admin/passeios/<?= (int)$trip['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir este passeio?')">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-danger">Excluir</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if ($trips['total_pages'] > 1): ?>
<nav class="pagination">
    <?php for ($i = 1; $i <= $trips['total_pages']; $i++): ?>
    <a href="?page=<?= $i ?>&busca=<?= e($currentSearch ?? '') ?>&status=<?= e($currentStatus ?? '') ?>" class="page-link <?= $i === $trips['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>
