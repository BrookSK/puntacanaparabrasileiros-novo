<?php
$isEdit = !empty($category);
$action = $isEdit ? '/admin/categorias/' . $category['id'] . '/editar' : '/admin/categorias/criar';
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Categoria' : 'Nova Categoria' ?></h2>
    <a href="/admin/categorias" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Categorias
    </a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <!-- Coluna Principal -->
        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-green">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                    </div>
                    <div>
                        <h3>Informações da Categoria</h3>
                        <p class="admin-card-subtitle">Dados principais da categoria de passeio</p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nome da Categoria <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= e($category['name'] ?? '') ?>" placeholder="Ex: Passeios de Barco" required>
                </div>

                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Descreva brevemente esta categoria..."><?= e($category['description'] ?? '') ?></textarea>
                    <small class="form-hint">Aparece na página de categorias do site.</small>
                </div>
            </div>
        </div>

        <!-- Coluna Direita -->
        <div>
            <div class="admin-card admin-card-sticky summary-card">
                <div class="summary-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg>
                    <h3>Configurações</h3>
                </div>

                <div class="summary-card-body">
                    <div class="form-group">
                        <label>Ordem de Exibição</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)($category['sort_order'] ?? 0) ?>" min="0">
                        <small class="form-hint">Menor número = aparece primeiro.</small>
                    </div>

                    <div class="form-group">
                        <label>Imagem da Categoria</label>
                        <div class="file-upload-area">
                            <input type="file" name="image" id="catImage" class="file-input-hidden" accept="image/jpeg,image/png,image/webp">
                            <label for="catImage" class="file-upload-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span>Escolher imagem</span>
                            </label>
                            <?php if ($isEdit && !empty($category['image'])): ?>
                            <div class="file-upload-preview">
                                <img src="<?= e($category['image']) ?>" alt="<?= e($category['name']) ?>">
                            </div>
                            <?php endif; ?>
                        </div>
                        <small class="form-hint">JPG, PNG ou WebP. Recomendado: 800x500px.</small>
                    </div>
                </div>

                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $isEdit ? 'Salvar Alterações' : 'Criar Categoria' ?>
                    </button>
                    <a href="/admin/categorias" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
