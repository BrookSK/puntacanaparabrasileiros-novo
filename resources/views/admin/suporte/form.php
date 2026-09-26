<?php
$old = flash('old') ?? [];
$errors = flash('errors') ?? [];
$priorityLabels = ['low' => 'Baixa', 'medium' => 'Média', 'high' => 'Alta', 'urgent' => 'Urgente'];
?>

<div class="card-header">
    <h2>Nova Demanda de Suporte</h2>
    <a href="/admin/suporte" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Suporte
    </a>
</div>

<form method="POST" action="/admin/suporte/criar" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18.36 6.64a9 9 0 11-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>
            </div>
            <div>
                <h3>Detalhes da Demanda</h3>
                <p class="admin-card-subtitle">Ao enviar, a demanda é registrada aqui e enviada automaticamente ao LRV.</p>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-8">
                <label>Título <span class="required">*</span></label>
                <input type="text" name="title" class="form-control" maxlength="255" value="<?= e($old['title'] ?? '') ?>" required>
                <?php if (!empty($errors['title'])): ?><small class="text-danger"><?= e($errors['title']) ?></small><?php endif; ?>
            </div>
            <div class="form-group col-4">
                <label>Prioridade</label>
                <select name="priority" class="form-control">
                    <?php foreach ($priorities as $p): ?>
                    <option value="<?= e($p) ?>" <?= ($old['priority'] ?? 'medium') === $p ? 'selected' : '' ?>><?= $priorityLabels[$p] ?? $p ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-12">
                <label>Categoria</label>
                <?php
                    $categories = ['Design', 'Desenvolvimento', 'Marketing', 'Suporte', 'Outro'];
                    $selectedCategory = $old['category'] ?? '';
                ?>
                <select name="category" class="form-control">
                    <option value="">Selecione</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat) ?>" <?= $selectedCategory === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-12">
                <label>Descrição <span class="required">*</span></label>
                <textarea name="description" class="form-control" rows="6" required><?= e($old['description'] ?? '') ?></textarea>
                <?php if (!empty($errors['description'])): ?><small class="text-danger"><?= e($errors['description']) ?></small><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="/admin/suporte" class="btn btn-outline">Cancelar</a>
        <button type="submit" class="btn btn-primary">Criar e Enviar ao LRV</button>
    </div>
</form>
