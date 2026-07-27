<div class="card-header">
    <div class="header-actions">
        <a href="/admin/categorias/criar" class="btn btn-primary">+ Nova Categoria</a>
    </div>
</div>

<div class="categories-grid">
    <?php if (empty($categories)): ?>
    <div class="admin-card" style="text-align:center;padding:40px;">
        <p style="color:#94a3b8;font-size:15px;">Nenhuma categoria cadastrada.</p>
        <a href="/admin/categorias/criar" class="btn btn-primary" style="margin-top:12px;">Criar Primeira Categoria</a>
    </div>
    <?php endif; ?>

    <?php foreach ($categories as $cat): ?>
    <div class="category-card">
        <div class="category-card-img">
            <?php if (!empty($cat['image'])): ?>
            <img src="<?= e($cat['image']) ?>" alt="<?= e($cat['name']) ?>">
            <?php else: ?>
            <div class="category-card-placeholder">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            </div>
            <?php endif; ?>
        </div>
        <div class="category-card-body">
            <h4 class="category-card-name"><?= e($cat['name']) ?></h4>
            <span class="category-card-slug">/<?= e($cat['slug']) ?></span>
            <div class="category-card-meta">
                <span class="category-card-order">Ordem: <?= (int)$cat['sort_order'] ?></span>
            </div>
        </div>
        <div class="category-card-actions">
            <a href="/admin/categorias/<?= (int)$cat['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
            <form method="POST" action="/admin/categorias/<?= (int)$cat['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir a categoria <?= e($cat['name']) ?>?')">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-danger">Excluir</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
