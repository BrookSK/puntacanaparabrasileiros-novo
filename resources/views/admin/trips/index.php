<div class="card-header">
    <div class="header-actions">
        <a href="/admin/passeios/criar" class="btn btn-primary">+ Novo Passeio</a>
    </div>
    <form method="GET" class="filter-form">
        <input type="text" name="busca" value="<?= e($currentSearch ?? '') ?>" placeholder="Buscar..." class="form-control form-control-sm">
        <select name="status" class="form-control form-control-sm">
            <option value="">Todos</option>
            <option value="published" <?= ($currentStatus ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
            <option value="draft" <?= ($currentStatus ?? '') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
            <option value="disabled" <?= ($currentStatus ?? '') === 'disabled' ? 'selected' : '' ?>>Desativado</option>
        </select>
        <button type="submit" class="btn btn-sm btn-outline">Filtrar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr><th>Imagem</th><th>Título</th><th>Status</th><th>Destaque</th><th>Ações</th></tr>
    </thead>
    <tbody>
    <?php foreach ($trips['items'] as $trip): ?>
    <tr>
        <td><img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="" class="table-thumb"></td>
        <td>
            <strong><?= e($trip['title']) ?></strong><br>
            <small class="text-muted">/passeios/<?= e($trip['slug']) ?></small>
        </td>
        <td><span class="badge badge-<?= $trip['status'] === 'published' ? 'success' : 'secondary' ?>"><?= e($trip['status']) ?></span></td>
        <td><?= $trip['featured'] ? '&#9733;' : '' ?></td>
        <td class="actions-cell">
            <a href="/admin/passeios/<?= (int)$trip['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
            <a href="/admin/passeios/<?= (int)$trip['id'] ?>/precos" class="btn btn-sm btn-outline">Preços</a>
            <form method="POST" action="/admin/passeios/<?= (int)$trip['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir este passeio?')">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-danger">Excluir</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>

<?php if ($trips['total_pages'] > 1): ?>
<nav class="pagination">
    <?php for ($i = 1; $i <= $trips['total_pages']; $i++): ?>
    <a href="?page=<?= $i ?>" class="page-link <?= $i === $trips['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>
