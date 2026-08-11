<?php
$isEdit = !empty($trip);
$action = $isEdit ? '/admin/passeios/' . $trip['id'] . '/editar' : '/admin/passeios/criar';
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
                <div class="admin-card-header"><div class="admin-card-icon admin-card-icon-blue"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/></svg></div><div><h3>Informações Básicas</h3><p class="admin-card-subtitle">Dados principais do passeio</p></div></div>
                <div class="form-group"><label>Título <span class="required">*</span></label><input type="text" name="title" value="<?= e($trip['title'] ?? '') ?>" class="form-control" placeholder="Ex: Saona Island VIP Tour" required></div>
                <div class="form-row"><div class="form-group col-6"><label>Duração</label><input type="number" name="duration" value="<?= e($trip['duration'] ?? '') ?>" class="form-control" min="1" placeholder="Ex: 8"></div><div class="form-group col-6"><label>Unidade</label><select name="duration_unit" class="form-control"><option value="hours" <?= ($trip['duration_unit'] ?? '') === 'hours' ? 'selected' : '' ?>>Horas</option><option value="days" <?= ($trip['duration_unit'] ?? '') === 'days' ? 'selected' : '' ?>>Dias</option></select></div></div>
                <div class="form-row"><div class="form-group col-6"><label>Mín. Passageiros</label><input type="number" name="min_pax" value="<?= e($trip['min_pax'] ?? '1') ?>" class="form-control" min="1"></div><div class="form-group col-6"><label>Máx. Passageiros</label><input type="number" name="max_pax" value="<?= e($trip['max_pax'] ?? '') ?>" class="form-control" min="1"></div></div>
                <div class="form-group"><label>Descrição Curta</label><textarea name="short_description" class="form-control" rows="3" placeholder="Resumo do passeio"><?= e($trip['short_description'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Descrição Completa</label><textarea name="description" class="form-control" rows="8" placeholder="Descrição detalhada..."><?= e($trip['description'] ?? '') ?></textarea></div>
                <div class="form-group"><label>Ponto de Encontro</label><input type="text" name="meeting_point" value="<?= e($trip['meeting_point'] ?? '') ?>" class="form-control" placeholder="Ex: Lobby do hotel"></div>
                <div class="form-group"><label>Notas Importantes</label><textarea name="important_notes" class="form-control" rows="3" placeholder="Informações importantes..."><?= e($trip['important_notes'] ?? '') ?></textarea></div>
            </div>

            <!-- Categorias - Multi-select com pesquisa v2 -->
            <div class="admin-card">
                <div class="admin-card-header"><div class="admin-card-icon admin-card-icon-green"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg></div><div><h3>Categorias</h3><p class="admin-card-subtitle">Selecione as categorias deste passeio</p></div></div>
                <div class="multiselect-wrapper" id="categoriesSelect">
                    <div class="multiselect-selected" id="categoriesSelected">
                        <?php
                        $selectedCats = $tripCategories ?? [];
                        foreach ($categories as $cat):
                            if (in_array($cat['id'], $selectedCats)):
                        ?>
                        <span class="multiselect-tag" data-value="<?= (int)$cat['id'] ?>"><?= e($cat['name']) ?><button type="button" class="multiselect-tag-remove" onclick="removeCategory(this, <?= (int)$cat['id'] ?>)">&times;</button></span>
                        <?php endif; endforeach; ?>
                    </div>
                    <input type="text" class="multiselect-search" id="categoriesSearch" placeholder="Buscar categorias..." autocomplete="off">
                    <div class="multiselect-dropdown" id="categoriesDropdown">
                        <?php foreach ($categories as $cat): ?>
                        <label class="multiselect-option <?= in_array($cat['id'], $selectedCats) ? 'selected' : '' ?>" data-value="<?= (int)$cat['id'] ?>" data-name="<?= e(mb_strtolower($cat['name'])) ?>">
                            <input type="checkbox" name="categories[]" value="<?= (int)$cat['id'] ?>" <?= in_array($cat['id'], $selectedCats) ? 'checked' : '' ?>>
                            <span><?= e($cat['name']) ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Inclui / Não Inclui -->
            <div class="admin-card">
                <div class="admin-card-header"><div class="admin-card-icon admin-card-icon-blue"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div><div><h3>O que Inclui / Não Inclui</h3><p class="admin-card-subtitle">Itens inclusos e não inclusos</p></div></div>
                <div class="form-group"><label>Inclui</label><div id="includes-list" class="repeater-list"><?php $includes = $isEdit && !empty($trip['includes']) ? json_decode($trip['includes'], true) : ['']; foreach ($includes as $inc): ?><div class="repeater-item"><input type="text" name="includes[]" value="<?= e($inc) ?>" class="form-control" placeholder="Ex: Almoço incluso"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button></div><?php endforeach; ?></div><button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('includes-list', 'includes[]', 'Ex: Almoço incluso')">+ Adicionar</button></div>
                <div class="form-group"><label>Não Inclui</label><div id="excludes-list" class="repeater-list"><?php $excludes = $isEdit && !empty($trip['excludes']) ? json_decode($trip['excludes'], true) : ['']; foreach ($excludes as $exc): ?><div class="repeater-item"><input type="text" name="excludes[]" value="<?= e($exc) ?>" class="form-control" placeholder="Ex: Bebidas alcoólicas"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button></div><?php endforeach; ?></div><button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('excludes-list', 'excludes[]', 'Ex: Bebidas alcoólicas')">+ Adicionar</button></div>
            </div>

            <!-- Pacotes -->
            <div class="admin-card">
                <div class="admin-card-header"><div class="admin-card-icon admin-card-icon-orange"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/></svg></div><div><h3>Pacotes</h3><p class="admin-card-subtitle">Pacotes de preço disponíveis</p></div></div>
                <div id="packages-list"><?php $pkgs = $packages ?? [['title' => '', 'description' => '', 'categories' => []]]; foreach ($pkgs as $i => $pkg): ?><div class="package-item card-inner"><div class="form-row"><div class="form-group col-6"><label>Nome</label><input type="text" name="packages[<?= $i ?>][title]" value="<?= e($pkg['title'] ?? '') ?>" class="form-control"></div><div class="form-group col-6"><label>Descrição</label><input type="text" name="packages[<?= $i ?>][description]" value="<?= e($pkg['description'] ?? '') ?>" class="form-control"></div></div><div class="form-group"><label>Categorias de Viajante</label><div class="checkbox-grid"><?php foreach ($travelerCategories as $tc): ?><label class="checkbox-label"><input type="checkbox" name="packages[<?= $i ?>][categories][]" value="<?= (int)$tc['id'] ?>" <?= in_array($tc['id'], array_column($pkg['categories'] ?? [], 'traveler_category_id')) ? 'checked' : '' ?>> <?= e($tc['name']) ?></label><?php endforeach; ?></div></div></div><?php endforeach; ?></div>
                <button type="button" class="btn btn-outline" id="addPackageBtn">+ Adicionar Pacote</button>
            </div>

            <!-- SEO -->
            <div class="admin-card">
                <div class="admin-card-header"><div class="admin-card-icon admin-card-icon-green"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg></div><div><h3>SEO</h3><p class="admin-card-subtitle">Otimização para buscadores</p></div></div>
                <div class="form-group"><label>Meta Title</label><input type="text" name="meta_title" value="<?= e($trip['meta_title'] ?? '') ?>" class="form-control" placeholder="Título para Google"></div>
                <div class="form-group"><label>Meta Description</label><textarea name="meta_description" class="form-control" rows="2" placeholder="Descrição para Google"><?= e($trip['meta_description'] ?? '') ?></textarea></div>
            </div>

            <?php if ($isEdit): ?>
            <!-- Horários por Hotel -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-orange">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </div>
                    <div>
                        <h3>Horários por Hotel</h3>
                        <p class="admin-card-subtitle">Hotéis e horários de busca para este passeio</p>
                    </div>
                </div>

                <div class="schedule-actions" style="display:flex;gap:12px;margin-bottom:20px;flex-wrap:wrap;">
                    <a href="/admin/passeios/<?= (int)$trip['id'] ?>/horarios/hotel/criar" class="btn-schedule btn-schedule--primary">+ Adicionar Hotel</a>
                    <a href="/admin/passeios/<?= (int)$trip['id'] ?>/horarios/importar" class="btn-schedule btn-schedule--default">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Importar Planilha
                    </a>
                    <?php if (!empty($tripHotels ?? [])): ?>
                    <form method="POST" action="/admin/passeios/<?= (int)$trip['id'] ?>/horarios/limpar" class="inline-form" style="margin-left:auto;" onsubmit="return confirm('Remover todos os hotéis e horários deste passeio?')">
                        <?= csrf_field() ?>
                        <button class="btn-schedule btn-schedule--danger">Limpar Tudo</button>
                    </form>
                    <?php endif; ?>
                </div>

                <?php if (empty($tripHotels ?? [])): ?>
                <div style="text-align:center;padding:30px 10px;color:#94a3b8;">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom:8px;opacity:0.5;"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <p style="margin:0;font-size:13px;">Nenhum hotel/horário cadastrado ainda.</p>
                </div>
                <?php else: ?>
                <div class="hotels-schedule-list">
                    <?php foreach ($tripHotels as $th): ?>
                    <div class="hotel-schedule-row">
                        <div class="hotel-schedule-row__info">
                            <strong class="hotel-schedule-row__name">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-3px;opacity:0.6;"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
                                <?= e($th['hotel_name']) ?>
                            </strong>
                            <?php if (!$th['is_active']): ?>
                            <span class="badge badge-secondary">Inativo</span>
                            <?php endif; ?>
                            <div class="hotel-schedule-row__times">
                                <?php if (!empty($th['schedules'])): ?>
                                    <?php foreach ($th['schedules'] as $sch): ?>
                                    <span class="schedule-chip"><?= substr($sch['pickup_time'], 0, 5) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span style="font-size:13px;color:#94a3b8;">Sem horários</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="hotel-schedule-row__actions">
                            <a href="/admin/passeios/<?= (int)$trip['id'] ?>/horarios/hotel/<?= (int)$th['id'] ?>/editar" class="btn-schedule btn-schedule--default">Editar</a>
                            <form method="POST" action="/admin/passeios/<?= (int)$trip['id'] ?>/horarios/hotel/<?= (int)$th['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir este hotel e seus horários?')">
                                <?= csrf_field() ?>
                                <button class="btn-schedule btn-schedule--danger">&#10005;</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <p style="margin:12px 0 0;font-size:12px;color:#94a3b8;"><?= count($tripHotels) ?> hotel(éis) cadastrado(s)</p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
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
                    <div class="form-group">
                        <label>Galeria de Fotos</label>
                        <p style="font-size:11px;color:#94a3b8;margin-bottom:10px;">Adicione imagens ao carrossel do passeio (upload ou URL)</p>
                        
                        <!-- Upload de arquivos -->
                        <div style="margin-bottom:14px;">
                            <label style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:6px;display:block;text-transform:uppercase;">Enviar novas imagens</label>
                            <input type="file" name="gallery_files[]" multiple accept="image/*" class="form-control" style="padding:8px;">
                            <small style="font-size:10px;color:#94a3b8;">JPG, PNG, WebP, GIF, SVG, AVIF — Máx. 10MB cada</small>
                        </div>

                        <!-- URLs existentes -->
                        <?php
                        $galleryImages = ($isEdit && !empty($trip['gallery'])) ? json_decode($trip['gallery'], true) : [];
                        ?>
                        <?php if (!empty($galleryImages)): ?>
                        <label style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:6px;display:block;text-transform:uppercase;">Imagens atuais (URL)</label>
                        <div id="gallery-list" class="repeater-list">
                            <?php foreach ($galleryImages as $gImg): ?>
                            <div class="repeater-item">
                                <input type="text" name="gallery_images[]" value="<?= e($gImg) ?>" class="form-control" placeholder="URL da imagem">
                                <button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php else: ?>
                        <div id="gallery-list" class="repeater-list">
                            <div class="repeater-item">
                                <input type="text" name="gallery_images[]" value="" class="form-control" placeholder="URL da imagem (opcional)">
                                <button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>
                            </div>
                        </div>
                        <?php endif; ?>
                        <button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('gallery-list', 'gallery_images[]', 'URL da imagem')">+ Adicionar URL</button>
                    </div>
                    <div class="form-group"><label class="checkbox-label"><input type="checkbox" name="partial_payment_enabled" <?= !empty($trip['partial_payment_enabled']) ? 'checked' : '' ?>> Pagamento Parcial</label></div>
                    <div class="form-group"><label>% Depósito</label><input type="number" name="partial_payment_percent" value="<?= e($trip['partial_payment_percent'] ?? '50') ?>" class="form-control" min="1" max="99"></div>
                </div>
                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg> <?= $isEdit ? 'Salvar Alterações' : 'Criar Passeio' ?></button>
                    <a href="/admin/passeios" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function addRepeater(listId, fieldName, placeholder) { const list = document.getElementById(listId); const div = document.createElement('div'); div.className = 'repeater-item'; div.innerHTML = `<input type="text" name="${fieldName}" value="" class="form-control" placeholder="${placeholder}"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>`; list.appendChild(div); }
document.addEventListener('click', function(e) { if (e.target.classList.contains('repeater-remove')) { e.target.closest('.repeater-item, .package-item').remove(); } });
document.getElementById('addPackageBtn')?.addEventListener('click', function() { const list = document.getElementById('packages-list'), i = list.children.length; const cats = <?= json_encode($travelerCategories ?? []) ?>; let ch = ''; cats.forEach(tc => { ch += `<label class="checkbox-label"><input type="checkbox" name="packages[${i}][categories][]" value="${tc.id}"> ${tc.name}</label>`; }); const d = document.createElement('div'); d.className = 'package-item card-inner'; d.innerHTML = `<div class="form-row"><div class="form-group col-6"><label>Nome</label><input type="text" name="packages[${i}][title]" class="form-control"></div><div class="form-group col-6"><label>Descrição</label><input type="text" name="packages[${i}][description]" class="form-control"></div></div><div class="form-group"><label>Categorias</label><div class="checkbox-grid">${ch}</div></div><button type="button" class="btn btn-sm btn-danger repeater-remove">&times; Remover</button>`; list.appendChild(d); });

// Categories Multi-select
(function() {
    const search = document.getElementById('categoriesSearch');
    const dropdown = document.getElementById('categoriesDropdown');
    const selected = document.getElementById('categoriesSelected');
    if (!search || !dropdown) return;

    search.addEventListener('focus', () => dropdown.classList.add('open'));
    search.addEventListener('input', () => {
        const q = search.value.toLowerCase();
        dropdown.querySelectorAll('.multiselect-option').forEach(opt => {
            opt.style.display = opt.dataset.name.includes(q) ? '' : 'none';
        });
        dropdown.classList.add('open');
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('#categoriesSelect')) dropdown.classList.remove('open');
    });

    dropdown.querySelectorAll('.multiselect-option').forEach(opt => {
        opt.addEventListener('click', (e) => {
            if (e.target.tagName === 'BUTTON') return;
            const cb = opt.querySelector('input[type="checkbox"]');
            cb.checked = !cb.checked;
            opt.classList.toggle('selected', cb.checked);
            updateTags();
        });
    });

    window.removeCategory = function(btn, val) {
        const opt = dropdown.querySelector(`.multiselect-option[data-value="${val}"]`);
        if (opt) { opt.querySelector('input').checked = false; opt.classList.remove('selected'); }
        btn.closest('.multiselect-tag').remove();
    };

    function updateTags() {
        selected.innerHTML = '';
        dropdown.querySelectorAll('.multiselect-option.selected').forEach(opt => {
            const val = opt.dataset.value;
            const name = opt.querySelector('span').textContent;
            selected.innerHTML += `<span class="multiselect-tag" data-value="${val}">${name}<button type="button" class="multiselect-tag-remove" onclick="removeCategory(this, ${val})">&times;</button></span>`;
        });
    }
})();
</script>

<style>
.hotels-schedule-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.hotel-schedule-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 18px;
    background: var(--bg-elevated, #f8fafc);
    border: 1px solid var(--border, #e2e8f0);
    border-radius: 10px;
    gap: 16px;
}
.hotel-schedule-row__info {
    display: flex;
    flex-direction: column;
    gap: 8px;
    min-width: 0;
    flex: 1;
}
.hotel-schedule-row__name {
    font-size: 15px;
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.hotel-schedule-row__times {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.hotel-schedule-row__actions {
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}
.schedule-chip {
    display: inline-flex;
    align-items: center;
    padding: 5px 14px;
    background: rgba(14, 165, 233, 0.08);
    color: var(--primary, #0ea5e9);
    border: 1px solid rgba(14, 165, 233, 0.18);
    border-radius: 14px;
    font-size: 13px;
    font-weight: 700;
    font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
    letter-spacing: 0.3px;
}

/* Botões clean da seção de horários */
.btn-schedule {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 22px;
    font-size: 13px;
    font-weight: 600;
    font-family: inherit;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    line-height: 1.3;
}
.btn-schedule--primary {
    background: transparent;
    color: #0ea5e9;
    border: 1.5px solid #0ea5e9;
}
.btn-schedule--primary:hover {
    background: #0ea5e9;
    color: #fff;
}
.btn-schedule--default {
    background: transparent;
    color: #64748b;
    border: 1.5px solid #cbd5e1;
}
.btn-schedule--default:hover {
    background: #f1f5f9;
    color: #334155;
    border-color: #94a3b8;
}
.btn-schedule--danger {
    background: transparent;
    color: #ef4444;
    border: 1.5px solid #ef4444;
}
.btn-schedule--danger:hover {
    background: #ef4444;
    color: #fff;
}
</style>
