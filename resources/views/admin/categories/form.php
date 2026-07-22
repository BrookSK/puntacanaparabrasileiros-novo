<?php
$isEdit = !empty($category);
$action = $isEdit ? '/admin/categorias/' . $category['id'] . '/editar' : '/admin/categorias/criar';
?>

<div class="form-card">
    <form method="POST" action="<?= $action ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="form-group">
            <label for="name">Nome da Categoria *</label>
            <input type="text" id="name" name="name" class="form-control" value="<?= e($category['name'] ?? '') ?>" required>
        </div>

        <div class="form-group">
            <label for="description">Descrição</label>
            <textarea id="description" name="description" class="form-control" rows="3"><?= e($category['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="image">Imagem da Categoria</label>
            <?php if ($isEdit && ($category['image'] ?? null)): ?>
            <div class="current-image" style="margin-bottom: 10px;">
                <img src="<?= e($category['image']) ?>" alt="<?= e($category['name']) ?>" style="max-width: 200px; border-radius: 8px;">
                <p class="text-muted" style="font-size: 12px; margin-top: 5px;">Imagem atual. Envie outra para substituir.</p>
            </div>
            <?php endif; ?>
            <input type="file" id="image" name="image" class="form-control" accept="image/jpeg,image/png,image/webp">
            <small class="text-muted">JPG, PNG ou WebP. Máximo 5MB. Recomendado: 800x500px.</small>
        </div>

        <div class="form-group">
            <label for="sort_order">Ordem de Exibição</label>
            <input type="number" id="sort_order" name="sort_order" class="form-control" value="<?= (int) ($category['sort_order'] ?? 0) ?>" min="0">
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Salvar Alterações' : 'Criar Categoria' ?></button>
            <a href="/admin/categorias" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>
