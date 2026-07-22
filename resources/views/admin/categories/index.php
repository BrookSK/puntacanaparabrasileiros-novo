<div class="card-header">
    <div class="header-actions">
        <a href="/admin/categorias/criar" class="btn btn-primary">+ Nova Categoria</a>
    </div>
</div>

<table class="table">
    <thead>
        <tr><th>Imagem</th><th>Nome</th><th>Slug</th><th>Ordem</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php if (empty($categories)): ?>
    <tr><td colspan="5" class="text-center">Nenhuma categoria cadastrada.</td></tr>
    <?php endif; ?>
    <?php foreach ($categories as $cat): ?>
    <tr>
        <td>
            <?php if ($cat['image']): ?>
            <img src="<?= e($cat['image']) ?>" alt="<?= e($cat['name']) ?>" class="table-thumb">
            <?php else: ?>
            <span class="text-muted">Sem imagem</span>
            <?php endif; ?>
        </td>
        <td><strong><?= e($cat['name']) ?></strong></td>
        <td><small class="text-muted"><?= e($cat['slug']) ?></small></td>
        <td><?= (int) $cat['sort_order'] ?></td>
        <td class="actions-cell">
            <a href="/admin/categorias/<?= (int)$cat['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
            <form method="POST" action="/admin/categorias/<?= (int)$cat['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir esta categoria?')">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-danger">Excluir</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
