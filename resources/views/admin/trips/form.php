<?php
$isEdit = !empty($trip);
$action = $isEdit ? '/admin/passeios/' . $trip['id'] . '/atualizar' : '/admin/passeios/salvar';
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Passeio' : 'Novo Passeio' ?></h2>
    <a href="/admin/passeios" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Passeios
    </a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <!-- Coluna Principal -->
        <div>
            <!-- Informações Básicas -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/></svg>
                    </div>
                    <div>
                        <h3>Informações Básicas</h3>
                        <p class="admin-card-subtitle">Dados principais do passeio</p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Título <span class="required">*</span></label>
                    <input type="text" name="title" value="<?= e($trip['title'] ?? '') ?>" class="form-control" placeholder="Ex: Saona Island VIP Tour" required>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Duração</label>
                        <input type="number" name="duration" value="<?= e($trip['duration'] ?? '') ?>" class="form-control" min="1" placeholder="Ex: 8">
                    </div>
                    <div class="form-group col-6">
                        <label>Unidade</label>
                        <select name="duration_unit" class="form-control">
                            <option value="hours" <?= ($trip['duration_unit'] ?? '') === 'hours' ? 'selected' : '' ?>>Horas</option>
                            <option value="days" <?= ($trip['duration_unit'] ?? '') === 'days' ? 'selected' : '' ?>>Dias</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Mín. Passageiros</label>
                        <input type="number" name="min_pax" value="<?= e($trip['min_pax'] ?? '1') ?>" class="form-control" min="1">
                    </div>
                    <div class="form-group col-6">
                        <label>Máx. Passageiros</label>
                        <input type="number" name="max_pax" value="<?= e($trip['max_pax'] ?? '') ?>" class="form-control" min="1">
                    </div>
                </div>

                <div class="form-group">
                    <label>Descrição Curta</label>
                    <textarea name="short_description" class="form-control" rows="3" placeholder="Resumo do passeio (aparece nos cards)"><?= e($trip['short_description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Descrição Completa</label>
                    <textarea name="description" class="form-control" rows="8" placeholder="Descrição detalhada do passeio..."><?= e($trip['description'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label>Ponto de Encontro</label>
                    <input type="text" name="meeting_point" value="<?= e($trip['meeting_point'] ?? '') ?>" class="form-control" placeholder="Ex: Lobby do hotel">
                </div>

                <div class="form-group">
                    <label>Notas Importantes</label>
                    <textarea name="important_notes" class="form-control" rows="3" placeholder="Informações importantes para o viajante..."><?= e($trip['important_notes'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Categorias -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-green">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                    </div>
                    <div>
                        <h3>Categorias</h3>
                        <p class="admin-card-subtitle">Selecione as categorias deste passeio</p>
                    </div>
                </div>
                <div class="checkbox-grid">
                    <?php foreach ($categories as $cat): ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="categories[]" value="<?= (int)$cat['id'] ?>"
                            <?= in_array($cat['id'], $tripCategories ?? []) ? 'checked' : '' ?>>
                        <?= e($cat['name']) ?>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- O que Inclui / Não Inclui -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div>
                        <h3>O que Inclui / Não Inclui</h3>
                        <p class="admin-card-subtitle">Itens inclusos e não inclusos no passeio</p>
                    </div>
                </div>
                <div class="form-group">
                    <label>Inclui</label>
                    <div id="includes-list" class="repeater-list">
                        <?php
                        $includes = $isEdit && !empty($trip['includes']) ? json_decode($trip['includes'], true) : [''];
                        foreach ($includes as $inc):
                        ?>
                        <div class="repeater-item">
                            <input type="text" name="includes[]" value="<?= e($inc) ?>" class="form-control" placeholder="Ex: Almoço incluso">
                            <button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('includes-list', 'includes[]', 'Ex: Almoço incluso')">+ Adicionar</button>
                </div>
                <div class="form-group">
                    <label>Não Inclui</label>
                    <div id="excludes-list" class="repeater-list">
                        <?php
                        $excludes = $isEdit && !empty($trip['excludes']) ? json_decode($trip['excludes'], true) : [''];
                        foreach ($excludes as $exc):
                        ?>
                        <div class="repeater-item">
                            <input type="text" name="excludes[]" value="<?= e($exc) ?>" class="form-control" placeholder="Ex: Bebidas alcoólicas">
                            <button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('excludes-list', 'excludes[]', 'Ex: Bebidas alcoólicas')">+ Adicionar</button>
                </div>
            </div>

            <!-- Pacotes -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-orange">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/></svg>
                    </div>
                    <div>
                        <h3>Pacotes</h3>
                        <p class="admin-card-subtitle">Pacotes disponíveis para este passeio</p>
                    </div>
                </div>
                <div id="packages-list">
                    <?php
                    $pkgs = $packages ?? [['title' => '', 'description' => '', 'categories' => []]];
                    foreach ($pkgs as $i => $pkg):
                    ?>
                    <div class="package-item card-inner">
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label>Nome do Pacote</label>
                                <input type="text" name="packages[<?= $i ?>][title]" value="<?= e($pkg['title'] ?? '') ?>" class="form-control" placeholder="Ex: Pacote Completo">
                            </div>
                            <div class="form-group col-6">
                                <label>Descrição</label>
                                <input type="text" name="packages[<?= $i ?>][description]" value="<?= e($pkg['description'] ?? '') ?>" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Categorias de Viajante (preços)</label>
                            <div class="checkbox-grid">
                                <?php foreach ($travelerCategories as $tc): ?>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="packages[<?= $i ?>][categories][]" value="<?= (int)$tc['id'] ?>"
                                        <?= in_array($tc['id'], array_column($pkg['categories'] ?? [], 'traveler_category_id')) ? 'checked' : '' ?>>
                                    <?= e($tc['name']) ?>
                                </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-outline" id="addPackageBtn">+ Adicionar Pacote</button>
            </div>

            <!-- Itinerário -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div>
                        <h3>Itinerário</h3>
                        <p class="admin-card-subtitle">Programação dia a dia</p>
                    </div>
                </div>
                <div id="itinerary-list">
                    <?php foreach ($itinerary ?? [] as $i => $item): ?>
                    <div class="itinerary-item card-inner">
                        <div class="form-row">
                            <div class="form-group col-6">
                                <label>Dia</label>
                                <input type="number" name="itinerary[<?= $i ?>][day_number]" value="<?= (int)($item['day_number'] ?? ($i+1)) ?>" class="form-control" min="1">
                            </div>
                            <div class="form-group col-6">
                                <label>Título</label>
                                <input type="text" name="itinerary[<?= $i ?>][title]" value="<?= e($item['title'] ?? '') ?>" class="form-control">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Descrição</label>
                            <textarea name="itinerary[<?= $i ?>][description]" class="form-control" rows="2"><?= e($item['description'] ?? '') ?></textarea>
                        </div>
                        <button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-outline" id="addItineraryBtn">+ Adicionar Dia</button>
            </div>

            <!-- SEO -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-green">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <div>
                        <h3>SEO</h3>
                        <p class="admin-card-subtitle">Otimização para mecanismos de busca</p>
                    </div>
                </div>
                <div class="form-group">
                    <label>Meta Title</label>
                    <input type="text" name="meta_title" value="<?= e($trip['meta_title'] ?? '') ?>" class="form-control" placeholder="Título para Google (máx 60 caracteres)">
                </div>
                <div class="form-group">
                    <label>Meta Description</label>
                    <textarea name="meta_description" class="form-control" rows="2" placeholder="Descrição para Google (máx 160 caracteres)"><?= e($trip['meta_description'] ?? '') ?></textarea>
                </div>
            </div>
        </div>

        <!-- Coluna Direita: Configurações -->
        <div>
            <div class="admin-card admin-card-sticky summary-card">
                <div class="summary-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg>
                    <h3>Publicação</h3>
                </div>
                <div class="summary-card-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="draft" <?= ($trip['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
                            <option value="published" <?= ($trip['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
                            <option value="disabled" <?= ($trip['status'] ?? '') === 'disabled' ? 'selected' : '' ?>>Desativado</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Dificuldade</label>
                        <select name="difficulty" class="form-control">
                            <option value="easy" <?= ($trip['difficulty'] ?? '') === 'easy' ? 'selected' : '' ?>>Fácil</option>
                            <option value="moderate" <?= ($trip['difficulty'] ?? '') === 'moderate' ? 'selected' : '' ?>>Moderado</option>
                            <option value="hard" <?= ($trip['difficulty'] ?? '') === 'hard' ? 'selected' : '' ?>>Difícil</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ordem de Exibição</label>
                        <input type="number" name="sort_order" value="<?= e($trip['sort_order'] ?? '0') ?>" class="form-control" min="0">
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label"><input type="checkbox" name="featured" <?= !empty($trip['featured']) ? 'checked' : '' ?>> Passeio em Destaque</label>
                    </div>
                    <div class="form-group">
                        <label>Imagem Destacada</label>
                        <div class="file-upload-area">
                            <input type="file" name="featured_image" id="featuredImage" class="file-input-hidden" accept="image/*">
                            <label for="featuredImage" class="file-upload-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span>Escolher imagem</span>
                            </label>
                            <?php if ($isEdit && !empty($trip['featured_image'])): ?>
                            <div class="file-upload-preview">
                                <img src="<?= e($trip['featured_image']) ?>" alt="Preview">
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $isEdit ? 'Salvar Alterações' : 'Criar Passeio' ?>
                    </button>
                    <a href="/admin/passeios" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>

            <!-- Pagamento -->
            <div class="admin-card" style="margin-top:24px;">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-orange">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                    </div>
                    <div>
                        <h3>Pagamento</h3>
                        <p class="admin-card-subtitle">Opções de pagamento</p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="checkbox-label"><input type="checkbox" name="partial_payment_enabled" <?= !empty($trip['partial_payment_enabled']) ? 'checked' : '' ?>> Permitir Pagamento Parcial</label>
                </div>
                <div class="form-group">
                    <label>% Depósito</label>
                    <input type="number" name="partial_payment_percent" value="<?= e($trip['partial_payment_percent'] ?? '50') ?>" class="form-control" min="1" max="99">
                </div>
                <div class="form-group">
                    <label class="checkbox-label"><input type="checkbox" name="group_discount_enabled" <?= !empty($trip['group_discount_enabled']) ? 'checked' : '' ?>> Desconto de Grupo</label>
                </div>
                <div class="form-group">
                    <label>Regras (JSON)</label>
                    <input type="text" name="group_discount_rules" value="<?= e($trip['group_discount_rules'] ?? '') ?>" class="form-control" placeholder='[{"min_pax":5,"discount":10}]'>
                    <small class="form-hint">Formato: [{"min_pax":5,"discount":10}]</small>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function addRepeater(listId, fieldName, placeholder) {
    const list = document.getElementById(listId);
    const div = document.createElement('div');
    div.className = 'repeater-item';
    div.innerHTML = `<input type="text" name="${fieldName}" value="" class="form-control" placeholder="${placeholder}"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>`;
    list.appendChild(div);
}

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('repeater-remove')) {
        e.target.closest('.repeater-item, .itinerary-item, .service-item, .fixed-date-item, .package-item').remove();
    }
});

document.getElementById('addPackageBtn')?.addEventListener('click', function() {
    const list = document.getElementById('packages-list');
    const i = list.children.length;
    const cats = <?= json_encode($travelerCategories ?? []) ?>;
    let catsHtml = '';
    cats.forEach(tc => { catsHtml += `<label class="checkbox-label"><input type="checkbox" name="packages[${i}][categories][]" value="${tc.id}"> ${tc.name}</label>`; });
    const div = document.createElement('div');
    div.className = 'package-item card-inner';
    div.innerHTML = `<div class="form-row"><div class="form-group col-6"><label>Nome do Pacote</label><input type="text" name="packages[${i}][title]" class="form-control"></div><div class="form-group col-6"><label>Descrição</label><input type="text" name="packages[${i}][description]" class="form-control"></div></div><div class="form-group"><label>Categorias de Viajante</label><div class="checkbox-grid">${catsHtml}</div></div><button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>`;
    list.appendChild(div);
});

document.getElementById('addItineraryBtn')?.addEventListener('click', function() {
    const list = document.getElementById('itinerary-list');
    const i = list.children.length;
    const div = document.createElement('div');
    div.className = 'itinerary-item card-inner';
    div.innerHTML = `<div class="form-row"><div class="form-group col-6"><label>Dia</label><input type="number" name="itinerary[${i}][day_number]" value="${i+1}" class="form-control" min="1"></div><div class="form-group col-6"><label>Título</label><input type="text" name="itinerary[${i}][title]" class="form-control"></div></div><div class="form-group"><label>Descrição</label><textarea name="itinerary[${i}][description]" class="form-control" rows="2"></textarea></div><button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>`;
    list.appendChild(div);
});
</script>

            <!-- Categorias -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-green">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                    </div>
                    <div><h3>Categorias</h3><p class="admin-card-subtitle">Selecione as categorias deste passeio</p></div>
                </div>
                <div class="checkbox-grid">
                    <?php foreach ($categories as $cat): ?>
                    <label class="checkbox-label"><input type="checkbox" name="categories[]" value="<?= (int)$cat['id'] ?>" <?= in_array($cat['id'], $tripCategories ?? []) ? 'checked' : '' ?>> <?= e($cat['name']) ?></label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Inclui / Não Inclui -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div><h3>O que Inclui / Não Inclui</h3><p class="admin-card-subtitle">Itens inclusos e não inclusos</p></div>
                </div>
                <div class="form-group">
                    <label>Inclui</label>
                    <div id="includes-list" class="repeater-list">
                        <?php $includes = $isEdit && !empty($trip['includes']) ? json_decode($trip['includes'], true) : ['']; foreach ($includes as $inc): ?>
                        <div class="repeater-item"><input type="text" name="includes[]" value="<?= e($inc) ?>" class="form-control" placeholder="Ex: Almoço incluso"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button></div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('includes-list', 'includes[]', 'Ex: Almoço incluso')">+ Adicionar</button>
                </div>
                <div class="form-group">
                    <label>Não Inclui</label>
                    <div id="excludes-list" class="repeater-list">
                        <?php $excludes = $isEdit && !empty($trip['excludes']) ? json_decode($trip['excludes'], true) : ['']; foreach ($excludes as $exc): ?>
                        <div class="repeater-item"><input type="text" name="excludes[]" value="<?= e($exc) ?>" class="form-control" placeholder="Ex: Bebidas alcoólicas"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button></div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('excludes-list', 'excludes[]', 'Ex: Bebidas alcoólicas')">+ Adicionar</button>
                </div>
            </div>

            <!-- Pacotes -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-orange">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/></svg>
                    </div>
                    <div><h3>Pacotes</h3><p class="admin-card-subtitle">Pacotes de preço disponíveis</p></div>
                </div>
                <div id="packages-list">
                    <?php $pkgs = $packages ?? [['title' => '', 'description' => '', 'categories' => []]]; foreach ($pkgs as $i => $pkg): ?>
                    <div class="package-item card-inner">
                        <div class="form-row"><div class="form-group col-6"><label>Nome</label><input type="text" name="packages[<?= $i ?>][title]" value="<?= e($pkg['title'] ?? '') ?>" class="form-control"></div><div class="form-group col-6"><label>Descrição</label><input type="text" name="packages[<?= $i ?>][description]" value="<?= e($pkg['description'] ?? '') ?>" class="form-control"></div></div>
                        <div class="form-group"><label>Categorias de Viajante</label><div class="checkbox-grid"><?php foreach ($travelerCategories as $tc): ?><label class="checkbox-label"><input type="checkbox" name="packages[<?= $i ?>][categories][]" value="<?= (int)$tc['id'] ?>" <?= in_array($tc['id'], array_column($pkg['categories'] ?? [], 'traveler_category_id')) ? 'checked' : '' ?>> <?= e($tc['name']) ?></label><?php endforeach; ?></div></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-outline" id="addPackageBtn">+ Adicionar Pacote</button>
            </div>

            <!-- SEO -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-green">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </div>
                    <div><h3>SEO</h3><p class="admin-card-subtitle">Otimização para buscadores</p></div>
                </div>
                <div class="form-group"><label>Meta Title</label><input type="text" name="meta_title" value="<?= e($trip['meta_title'] ?? '') ?>" class="form-control" placeholder="Título para Google"></div>
                <div class="form-group"><label>Meta Description</label><textarea name="meta_description" class="form-control" rows="2" placeholder="Descrição para Google"><?= e($trip['meta_description'] ?? '') ?></textarea></div>
            </div>
        </div>

        <!-- Coluna Direita -->
        <div>
            <div class="admin-card admin-card-sticky summary-card">
                <div class="summary-card-header"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4"/></svg><h3>Publicação</h3></div>
                <div class="summary-card-body">
                    <div class="form-group"><label>Status</label><select name="status" class="form-control"><option value="draft" <?= ($trip['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Rascunho</option><option value="published" <?= ($trip['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option><option value="disabled" <?= ($trip['status'] ?? '') === 'disabled' ? 'selected' : '' ?>>Desativado</option></select></div>
                    <div class="form-group"><label>Dificuldade</label><select name="difficulty" class="form-control"><option value="easy" <?= ($trip['difficulty'] ?? '') === 'easy' ? 'selected' : '' ?>>Fácil</option><option value="moderate" <?= ($trip['difficulty'] ?? '') === 'moderate' ? 'selected' : '' ?>>Moderado</option><option value="hard" <?= ($trip['difficulty'] ?? '') === 'hard' ? 'selected' : '' ?>>Difícil</option></select></div>
                    <div class="form-group"><label>Ordem</label><input type="number" name="sort_order" value="<?= e($trip['sort_order'] ?? '0') ?>" class="form-control" min="0"></div>
                    <div class="form-group"><label class="checkbox-label"><input type="checkbox" name="featured" <?= !empty($trip['featured']) ? 'checked' : '' ?>> Passeio em Destaque</label></div>
                    <div class="form-group"><label>Imagem Destacada</label><div class="file-upload-area"><input type="file" name="featured_image" id="featImg" class="file-input-hidden" accept="image/*"><label for="featImg" class="file-upload-label"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg><span>Escolher imagem</span></label><?php if ($isEdit && !empty($trip['featured_image'])): ?><div class="file-upload-preview"><img src="<?= e($trip['featured_image']) ?>" alt=""></div><?php endif; ?></div></div>
                    <div class="form-group"><label class="checkbox-label"><input type="checkbox" name="partial_payment_enabled" <?= !empty($trip['partial_payment_enabled']) ? 'checked' : '' ?>> Pagamento Parcial</label></div>
                    <div class="form-group"><label>% Depósito</label><input type="number" name="partial_payment_percent" value="<?= e($trip['partial_payment_percent'] ?? '50') ?>" class="form-control" min="1" max="99"></div>
                </div>
                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg><?= $isEdit ? 'Salvar Alterações' : 'Criar Passeio' ?></button>
                    <a href="/admin/passeios" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function addRepeater(listId, fieldName, placeholder) {
    const list = document.getElementById(listId);
    const div = document.createElement('div');
    div.className = 'repeater-item';
    div.innerHTML = `<input type="text" name="${fieldName}" value="" class="form-control" placeholder="${placeholder}"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>`;
    list.appendChild(div);
}
document.addEventListener('click', function(e) { if (e.target.classList.contains('repeater-remove')) { e.target.closest('.repeater-item, .itinerary-item, .service-item, .fixed-date-item, .package-item').remove(); } });
document.getElementById('addPackageBtn')?.addEventListener('click', function() {
    const list = document.getElementById('packages-list'), i = list.children.length;
    const cats = <?= json_encode($travelerCategories ?? []) ?>;
    let ch = ''; cats.forEach(tc => { ch += `<label class="checkbox-label"><input type="checkbox" name="packages[${i}][categories][]" value="${tc.id}"> ${tc.name}</label>`; });
    const d = document.createElement('div'); d.className = 'package-item card-inner';
    d.innerHTML = `<div class="form-row"><div class="form-group col-6"><label>Nome</label><input type="text" name="packages[${i}][title]" class="form-control"></div><div class="form-group col-6"><label>Descrição</label><input type="text" name="packages[${i}][description]" class="form-control"></div></div><div class="form-group"><label>Categorias</label><div class="checkbox-grid">${ch}</div></div><button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>`;
    list.appendChild(d);
});
</script>
