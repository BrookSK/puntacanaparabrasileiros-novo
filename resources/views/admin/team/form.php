<?php
$isEdit = !empty($member);
$action = $isEdit ? '/admin/equipe/' . $member['id'] . '/editar' : '/admin/equipe/criar';
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Membro' : 'Novo Membro' ?></h2>
    <a href="/admin/equipe" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Equipe
    </a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <!-- Coluna Principal -->
        <div>
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <h3>Dados do Membro</h3>
                        <p class="admin-card-subtitle">Informações exibidas no carrossel "Nossa equipe"</p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nome <span class="required">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?= e($member['name'] ?? '') ?>" placeholder="Ex: Anna Amélia" required>
                </div>

                <div class="form-group">
                    <label>Cargo / Função</label>
                    <input type="text" name="role" class="form-control" value="<?= e($member['role'] ?? '') ?>" placeholder="Ex: Cofundadora">
                </div>

                <div class="form-group">
                    <label>Apresentação</label>
                    <textarea name="bio" class="form-control" rows="6" placeholder="Breve apresentação deste integrante..."><?= e($member['bio'] ?? '') ?></textarea>
                    <small class="form-hint">Texto que aparece no cartão do carrossel.</small>
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
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="published" <?= ($member['status'] ?? 'draft') === 'published' ? 'selected' : '' ?>>Publicado</option>
                            <option value="draft" <?= ($member['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
                        </select>
                        <small class="form-hint">Só membros publicados aparecem no site.</small>
                    </div>

                    <div class="form-group">
                        <label>Ordem de Exibição</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)($member['sort_order'] ?? 0) ?>" min="0">
                        <small class="form-hint">Menor número = aparece primeiro.</small>
                    </div>

                    <div class="form-group">
                        <label>Foto</label>
                        <div class="file-upload-area">
                            <input type="file" name="photo" id="memberPhoto" class="file-input-hidden" accept="image/jpeg,image/png,image/webp">
                            <label for="memberPhoto" class="file-upload-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span>Escolher imagem</span>
                            </label>
                            <?php if ($isEdit && !empty($member['photo'])): ?>
                            <div class="file-upload-preview">
                                <img src="<?= e($member['photo']) ?>" alt="<?= e($member['name']) ?>">
                            </div>
                            <?php endif; ?>
                        </div>
                        <small class="form-hint">JPG, PNG ou WebP. Recomendado: 600x600px (quadrada).</small>
                    </div>
                </div>

                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $isEdit ? 'Salvar Alterações' : 'Criar Membro' ?>
                    </button>
                    <a href="/admin/equipe" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>
