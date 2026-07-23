<?php
$isEdit = !empty($trip);
$action = $isEdit ? '/admin/passeios/' . $trip['id'] . '/atualizar' : '/admin/passeios/salvar';
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Passeio' : 'Novo Passeio' ?></h2>
    <a href="/admin/passeios" class="btn btn-sm btn-outline">&larr; Voltar</a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <!-- Informações Básicas -->
    <fieldset class="form-section">
        <legend>Informações Básicas</legend>

        <div class="form-group">
            <label for="title">Título *</label>
            <input type="text" id="title" name="title" value="<?= e($trip['title'] ?? '') ?>" class="form-control" required>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="duration">Duração</label>
                <input type="number" id="duration" name="duration" value="<?= e($trip['duration'] ?? '') ?>" class="form-control" min="1">
            </div>
            <div class="form-group">
                <label for="duration_unit">Unidade</label>
                <select id="duration_unit" name="duration_unit" class="form-control">
                    <option value="hours" <?= ($trip['duration_unit'] ?? '') === 'hours' ? 'selected' : '' ?>>Horas</option>
                    <option value="days" <?= ($trip['duration_unit'] ?? '') === 'days' ? 'selected' : '' ?>>Dias</option>
                </select>
            </div>
            <div class="form-group">
                <label for="difficulty">Dificuldade</label>
                <select id="difficulty" name="difficulty" class="form-control">
                    <option value="easy" <?= ($trip['difficulty'] ?? '') === 'easy' ? 'selected' : '' ?>>Fácil</option>
                    <option value="moderate" <?= ($trip['difficulty'] ?? '') === 'moderate' ? 'selected' : '' ?>>Moderado</option>
                    <option value="hard" <?= ($trip['difficulty'] ?? '') === 'hard' ? 'selected' : '' ?>>Difícil</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label for="min_pax">Mín. Passageiros</label>
                <input type="number" id="min_pax" name="min_pax" value="<?= e($trip['min_pax'] ?? '1') ?>" class="form-control" min="1">
            </div>
            <div class="form-group">
                <label for="max_pax">Máx. Passageiros</label>
                <input type="number" id="max_pax" name="max_pax" value="<?= e($trip['max_pax'] ?? '') ?>" class="form-control" min="1">
            </div>
        </div>

        <div class="form-group">
            <label for="short_description">Descrição Curta</label>
            <textarea id="short_description" name="short_description" class="form-control" rows="3"><?= e($trip['short_description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="description">Descrição Completa</label>
            <textarea id="description" name="description" class="form-control" rows="8"><?= e($trip['description'] ?? '') ?></textarea>
        </div>

        <div class="form-group">
            <label for="meeting_point">Ponto de Encontro</label>
            <input type="text" id="meeting_point" name="meeting_point" value="<?= e($trip['meeting_point'] ?? '') ?>" class="form-control">
        </div>

        <div class="form-group">
            <label for="important_notes">Notas Importantes</label>
            <textarea id="important_notes" name="important_notes" class="form-control" rows="3"><?= e($trip['important_notes'] ?? '') ?></textarea>
        </div>
    </fieldset>

    <!-- Categorias -->
    <fieldset class="form-section">
        <legend>Categorias</legend>
        <div class="checkbox-grid">
            <?php foreach ($categories as $cat): ?>
            <label class="checkbox-label">
                <input type="checkbox" name="categories[]" value="<?= (int)$cat['id'] ?>"
                    <?= in_array($cat['id'], $tripCategories ?? []) ? 'checked' : '' ?>>
                <?= e($cat['name']) ?>
            </label>
            <?php endforeach; ?>
        </div>
    </fieldset>

    <!-- Imagem -->
    <fieldset class="form-section">
        <legend>Imagem Destacada</legend>
        <?php if ($isEdit && !empty($trip['featured_image'])): ?>
        <div class="current-image">
            <img src="<?= e($trip['featured_image']) ?>" alt="" style="max-width:200px; border-radius:8px; margin-bottom:10px;">
        </div>
        <?php endif; ?>
        <div class="form-group">
            <input type="file" id="featured_image" name="featured_image" accept="image/jpeg,image/png,image/webp" class="form-control">
        </div>
    </fieldset>

    <!-- Inclui / Não Inclui -->
    <fieldset class="form-section">
        <legend>O que Inclui / Não Inclui</legend>
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
    </fieldset>

    <!-- Pacotes -->
    <fieldset class="form-section">
        <legend>Pacotes</legend>
        <div id="packages-list">
            <?php
            $pkgs = $packages ?? [['title' => '', 'description' => '', 'categories' => []]];
            foreach ($pkgs as $i => $pkg):
            ?>
            <div class="package-item card-inner">
                <div class="form-row">
                    <div class="form-group flex-2">
                        <label>Nome do Pacote</label>
                        <input type="text" name="packages[<?= $i ?>][title]" value="<?= e($pkg['title'] ?? '') ?>" class="form-control" placeholder="Ex: Pacote Completo">
                    </div>
                    <div class="form-group flex-1">
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
        <button type="button" class="btn btn-sm btn-outline" id="addPackageBtn">+ Adicionar Pacote</button>
    </fieldset>

    <!-- Itinerário -->
    <fieldset class="form-section">
        <legend>Itinerário</legend>
        <div id="itinerary-list">
            <?php
            $itineraryItems = $itinerary ?? [];
            foreach ($itineraryItems as $i => $item):
            ?>
            <div class="itinerary-item card-inner">
                <div class="form-row">
                    <div class="form-group" style="width:80px">
                        <label>Dia</label>
                        <input type="number" name="itinerary[<?= $i ?>][day_number]" value="<?= (int)($item['day_number'] ?? ($i+1)) ?>" class="form-control" min="1">
                    </div>
                    <div class="form-group flex-2">
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
        <button type="button" class="btn btn-sm btn-outline" id="addItineraryBtn">+ Adicionar Dia</button>
    </fieldset>

    <!-- Serviços Extras -->
    <fieldset class="form-section">
        <legend>Serviços Extras</legend>
        <div id="extra-services-list">
            <?php
            $services = $extraServices ?? [];
            foreach ($services as $i => $svc):
            ?>
            <div class="service-item card-inner">
                <div class="form-row">
                    <div class="form-group flex-2">
                        <label>Nome</label>
                        <input type="text" name="extra_services[<?= $i ?>][name]" value="<?= e($svc['name'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Preço (USD)</label>
                        <input type="number" name="extra_services[<?= $i ?>][price]" value="<?= e($svc['price'] ?? '0') ?>" class="form-control" step="0.01" min="0">
                    </div>
                    <div class="form-group">
                        <label>Tipo</label>
                        <select name="extra_services[<?= $i ?>][price_type]" class="form-control">
                            <option value="per_person" <?= ($svc['price_type'] ?? '') === 'per_person' ? 'selected' : '' ?>>Por Pessoa</option>
                            <option value="per_group" <?= ($svc['price_type'] ?? '') === 'per_group' ? 'selected' : '' ?>>Por Grupo</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" name="extra_services[<?= $i ?>][description]" value="<?= e($svc['description'] ?? '') ?>" class="form-control">
                </div>
                <label class="checkbox-label">
                    <input type="checkbox" name="extra_services[<?= $i ?>][required]" <?= !empty($svc['required']) ? 'checked' : '' ?>>
                    Obrigatório
                </label>
                <button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline" id="addServiceBtn">+ Adicionar Serviço</button>
    </fieldset>

    <!-- Datas Fixas -->
    <?php if ($isEdit): ?>
    <fieldset class="form-section">
        <legend>Datas Fixas de Saída</legend>
        <div id="fixed-dates-list">
            <?php foreach ($fixedDates ?? [] as $i => $fd): ?>
            <div class="fixed-date-item card-inner">
                <div class="form-row">
                    <div class="form-group">
                        <label>Data</label>
                        <input type="date" name="fixed_dates[<?= $i ?>][date]" value="<?= e($fd['date'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Horário</label>
                        <input type="time" name="fixed_dates[<?= $i ?>][time]" value="<?= e($fd['time'] ?? '') ?>" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Máx. Pax</label>
                        <input type="number" name="fixed_dates[<?= $i ?>][max_pax]" value="<?= e($fd['max_pax'] ?? '') ?>" class="form-control" min="1">
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select name="fixed_dates[<?= $i ?>][status]" class="form-control">
                            <option value="available" <?= ($fd['status'] ?? '') === 'available' ? 'selected' : '' ?>>Disponível</option>
                            <option value="full" <?= ($fd['status'] ?? '') === 'full' ? 'selected' : '' ?>>Lotado</option>
                            <option value="cancelled" <?= ($fd['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelado</option>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline" id="addFixedDateBtn">+ Adicionar Data</button>
    </fieldset>
    <?php endif; ?>

    <!-- Configurações -->
    <fieldset class="form-section">
        <legend>Configurações</legend>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="partial_payment_enabled" <?= !empty($trip['partial_payment_enabled']) ? 'checked' : '' ?>>
                    Pagamento Parcial
                </label>
            </div>
            <div class="form-group">
                <label for="partial_payment_percent">% Depósito</label>
                <input type="number" id="partial_payment_percent" name="partial_payment_percent" value="<?= e($trip['partial_payment_percent'] ?? '50') ?>" class="form-control" min="1" max="99">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="group_discount_enabled" <?= !empty($trip['group_discount_enabled']) ? 'checked' : '' ?>>
                    Desconto de Grupo
                </label>
            </div>
            <div class="form-group flex-2">
                <label for="group_discount_rules">Regras (JSON)</label>
                <input type="text" id="group_discount_rules" name="group_discount_rules" value="<?= e($trip['group_discount_rules'] ?? '') ?>" class="form-control" placeholder='[{"min_pax":5,"discount":10}]'>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="featured" <?= !empty($trip['featured']) ? 'checked' : '' ?>>
                    Destaque
                </label>
            </div>
            <div class="form-group">
                <label for="sort_order">Ordem</label>
                <input type="number" id="sort_order" name="sort_order" value="<?= e($trip['sort_order'] ?? '0') ?>" class="form-control" min="0">
            </div>
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status" class="form-control">
                    <option value="draft" <?= ($trip['status'] ?? 'draft') === 'draft' ? 'selected' : '' ?>>Rascunho</option>
                    <option value="published" <?= ($trip['status'] ?? '') === 'published' ? 'selected' : '' ?>>Publicado</option>
                    <option value="disabled" <?= ($trip['status'] ?? '') === 'disabled' ? 'selected' : '' ?>>Desativado</option>
                </select>
            </div>
        </div>
    </fieldset>

    <!-- SEO -->
    <fieldset class="form-section">
        <legend>SEO</legend>
        <div class="form-group">
            <label for="meta_title">Meta Title</label>
            <input type="text" id="meta_title" name="meta_title" value="<?= e($trip['meta_title'] ?? '') ?>" class="form-control">
        </div>
        <div class="form-group">
            <label for="meta_description">Meta Description</label>
            <textarea id="meta_description" name="meta_description" class="form-control" rows="2"><?= e($trip['meta_description'] ?? '') ?></textarea>
        </div>
    </fieldset>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Salvar Alterações' : 'Criar Passeio' ?></button>
        <a href="/admin/passeios" class="btn btn-outline">Cancelar</a>
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

// Add Package
document.getElementById('addPackageBtn')?.addEventListener('click', function() {
    const list = document.getElementById('packages-list');
    const i = list.children.length;
    const cats = <?= json_encode($travelerCategories ?? []) ?>;
    let catsHtml = '';
    cats.forEach(tc => {
        catsHtml += `<label class="checkbox-label"><input type="checkbox" name="packages[${i}][categories][]" value="${tc.id}"> ${tc.name}</label>`;
    });
    const div = document.createElement('div');
    div.className = 'package-item card-inner';
    div.innerHTML = `
        <div class="form-row">
            <div class="form-group flex-2"><label>Nome do Pacote</label><input type="text" name="packages[${i}][title]" class="form-control" placeholder="Ex: Pacote Completo"></div>
            <div class="form-group flex-1"><label>Descrição</label><input type="text" name="packages[${i}][description]" class="form-control"></div>
        </div>
        <div class="form-group"><label>Categorias de Viajante</label><div class="checkbox-grid">${catsHtml}</div></div>
        <button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>
    `;
    list.appendChild(div);
});

// Add Itinerary
document.getElementById('addItineraryBtn')?.addEventListener('click', function() {
    const list = document.getElementById('itinerary-list');
    const i = list.children.length;
    const div = document.createElement('div');
    div.className = 'itinerary-item card-inner';
    div.innerHTML = `
        <div class="form-row">
            <div class="form-group" style="width:80px"><label>Dia</label><input type="number" name="itinerary[${i}][day_number]" value="${i+1}" class="form-control" min="1"></div>
            <div class="form-group flex-2"><label>Título</label><input type="text" name="itinerary[${i}][title]" class="form-control"></div>
        </div>
        <div class="form-group"><label>Descrição</label><textarea name="itinerary[${i}][description]" class="form-control" rows="2"></textarea></div>
        <button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>
    `;
    list.appendChild(div);
});

// Add Extra Service
document.getElementById('addServiceBtn')?.addEventListener('click', function() {
    const list = document.getElementById('extra-services-list');
    const i = list.children.length;
    const div = document.createElement('div');
    div.className = 'service-item card-inner';
    div.innerHTML = `
        <div class="form-row">
            <div class="form-group flex-2"><label>Nome</label><input type="text" name="extra_services[${i}][name]" class="form-control"></div>
            <div class="form-group"><label>Preço (USD)</label><input type="number" name="extra_services[${i}][price]" class="form-control" step="0.01" min="0" value="0"></div>
            <div class="form-group"><label>Tipo</label><select name="extra_services[${i}][price_type]" class="form-control"><option value="per_person">Por Pessoa</option><option value="per_group">Por Grupo</option></select></div>
        </div>
        <div class="form-group"><label>Descrição</label><input type="text" name="extra_services[${i}][description]" class="form-control"></div>
        <label class="checkbox-label"><input type="checkbox" name="extra_services[${i}][required]"> Obrigatório</label>
        <button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>
    `;
    list.appendChild(div);
});

// Add Fixed Date
document.getElementById('addFixedDateBtn')?.addEventListener('click', function() {
    const list = document.getElementById('fixed-dates-list');
    const i = list.children.length;
    const div = document.createElement('div');
    div.className = 'fixed-date-item card-inner';
    div.innerHTML = `
        <div class="form-row">
            <div class="form-group"><label>Data</label><input type="date" name="fixed_dates[${i}][date]" class="form-control"></div>
            <div class="form-group"><label>Horário</label><input type="time" name="fixed_dates[${i}][time]" class="form-control"></div>
            <div class="form-group"><label>Máx. Pax</label><input type="number" name="fixed_dates[${i}][max_pax]" class="form-control" min="1"></div>
            <div class="form-group"><label>Status</label><select name="fixed_dates[${i}][status]" class="form-control"><option value="available">Disponível</option><option value="full">Lotado</option><option value="cancelled">Cancelado</option></select></div>
        </div>
        <button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>
    `;
    list.appendChild(div);
});
</script>
