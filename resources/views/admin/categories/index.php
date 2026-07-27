<div class="card-header">
    <div class="header-actions">
        <a href="/admin/categorias/criar" class="btn btn-primary">+ Nova Categoria</a>
    </div>
    <form method="GET" class="filter-form">
        <input type="text" name="busca" value="<?= e($currentSearch ?? '') ?>" placeholder="Buscar por nome..." class="form-control">
        <select name="ordenar" class="form-control" onchange="this.form.submit()">
            <option value="sort_order" <?= ($currentSort ?? 'sort_order') === 'sort_order' ? 'selected' : '' ?>>Ordenar por Ordem</option>
            <option value="name" <?= ($currentSort ?? '') === 'name' ? 'selected' : '' ?>>Ordenar por Nome</option>
            <option value="newest" <?= ($currentSort ?? '') === 'newest' ? 'selected' : '' ?>>Mais Recentes</option>
        </select>
        <button type="submit" class="btn btn-outline">Filtrar</button>
    </form>
</div>

<div class="trips-list">
    <?php if (empty($categories)): ?>
    <div class="trip-list-item" style="justify-content:center;padding:40px;">
        <p style="color:#94a3b8;font-size:15px;">Nenhuma categoria encontrada.</p>
    </div>
    <?php endif; ?>

    <?php foreach ($categories as $cat): ?>
    <div class="trip-list-item">
        <div class="trip-list-img">
            <?php if (!empty($cat['image'])): ?>
            <img src="<?= e($cat['image']) ?>" alt="<?= e($cat['name']) ?>">
            <?php else: ?>
            <div style="width:100%;height:100%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
            </div>
            <?php endif; ?>
        </div>
        <div class="trip-list-info">
            <h4 class="trip-list-title"><?= e($cat['name']) ?></h4>
            <span class="trip-list-slug">/<?= e($cat['slug']) ?></span>
        </div>
        <div class="trip-list-status">
            <span class="badge badge-info">Ordem: <?= (int)$cat['sort_order'] ?></span>
        </div>
        <div class="trip-list-actions">
            <a href="/admin/categorias/<?= (int)$cat['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
            <form method="POST" action="/admin/categorias/<?= (int)$cat['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir a categoria <?= e($cat['name']) ?>?')">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-danger">Excluir</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
