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

            <!-- Vídeo YouTube -->
            <div class="admin-card">
                <div class="admin-card-header"><div class="admin-card-icon admin-card-icon-blue"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="5 3 19 12 5 21 5 3"/></svg></div><div><h3>Vídeo do Passeio</h3><p class="admin-card-subtitle">Link do YouTube para exibir na página do passeio</p></div></div>
                <div class="form-group"><label>URL do YouTube</label>
                    <div style="display:flex;gap:8px;align-items:center;">
                        <input type="url" name="youtube_url" id="youtubeUrlInput" value="<?= e($trip['youtube_url'] ?? '') ?>" class="form-control" placeholder="https://www.youtube.com/watch?v=XXXXXXXXX" style="flex:1;">
                        <button type="button" class="btn btn-sm btn-primary" id="addYoutubeBtn" style="white-space:nowrap;">+ Adicionar Vídeo</button>
                    </div>
                    <small style="color:#6b7280;">Cole o link do vídeo e clique em "Adicionar Vídeo"</small>
                    <div id="youtubeThumbnail" style="margin-top:12px;<?= empty($trip['youtube_url']) ? 'display:none;' : '' ?>">
                        <?php
                        $ytId = '';
                        if (!empty($trip['youtube_url'])) {
                            if (preg_match('/[?&]v=([^&]+)/', $trip['youtube_url'], $m)) $ytId = $m[1];
                            elseif (preg_match('/youtu\.be\/([^?]+)/', $trip['youtube_url'], $m)) $ytId = $m[1];
                            elseif (preg_match('/embed\/([^?]+)/', $trip['youtube_url'], $m)) $ytId = $m[1];
                        }
                        ?>
                        <div style="position:relative;display:inline-block;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
                            <img id="ytThumb" src="<?= $ytId ? 'https://img.youtube.com/vi/' . e($ytId) . '/mqdefault.jpg' : '' ?>" alt="Thumbnail" style="width:240px;height:135px;object-fit:cover;display:block;">
                            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:40px;height:40px;background:rgba(0,0,0,0.7);border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="#fff"><polygon points="5 3 19 12 5 21 5 3"/></svg>
                            </div>
                            <button type="button" id="removeYoutubeBtn" style="position:absolute;top:6px;right:6px;background:#dc2626;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:14px;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>
                        </div>
                    </div>
                </div>
                <script>
                document.getElementById('addYoutubeBtn')?.addEventListener('click', function() {
                    var url = document.getElementById('youtubeUrlInput').value.trim();
                    if (!url) { alert('Cole um link do YouTube primeiro.'); return; }
                    var ytId = '';
                    var m = url.match(/[?&]v=([^&]+)/) || url.match(/youtu\.be\/([^?]+)/) || url.match(/embed\/([^?]+)/);
                    if (m) ytId = m[1];
                    if (!ytId) { alert('Link inválido. Use um link do YouTube válido.'); return; }
                    document.getElementById('ytThumb').src = 'https://img.youtube.com/vi/' + ytId + '/mqdefault.jpg';
                    document.getElementById('youtubeThumbnail').style.display = 'block';
                });
                document.getElementById('removeYoutubeBtn')?.addEventListener('click', function() {
                    document.getElementById('youtubeUrlInput').value = '';
                    document.getElementById('youtubeThumbnail').style.display = 'none';
                });
                </script>
            </div>

            <!-- FAQs por Passeio -->
            <div class="admin-card">
                <div class="admin-card-header"><div class="admin-card-icon admin-card-icon-orange"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 015.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div><div><h3>FAQ do Passeio</h3><p class="admin-card-subtitle">Perguntas frequentes específicas deste passeio</p></div></div>
                <div id="faqsList">
                    <?php
                    $faqs = json_decode($trip['faqs'] ?? '[]', true) ?: [];
                    if (empty($faqs)) $faqs = [['question' => '', 'answer' => '']];
                    foreach ($faqs as $fi => $faq):
                    ?>
                    <div class="faq-item-admin" style="padding:14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:10px;">
                        <div class="form-group" style="margin-bottom:8px;"><label>Pergunta</label><input type="text" name="faqs[<?= $fi ?>][question]" value="<?= e($faq['question'] ?? '') ?>" class="form-control" placeholder="Ex: Preciso saber nadar?"></div>
                        <div class="form-group" style="margin-bottom:0;"><label>Resposta</label><textarea name="faqs[<?= $fi ?>][answer]" class="form-control" rows="2" placeholder="Resposta..."><?= e($faq['answer'] ?? '') ?></textarea></div>
                        <?php if ($fi > 0): ?><button type="button" class="btn btn-sm btn-danger" style="margin-top:8px;" onclick="this.closest('.faq-item-admin').remove()">Remover</button><?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <button type="button" class="btn btn-outline" id="addFaqBtn">+ Adicionar Pergunta</button>
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

            <!-- Documentos Extras -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon" style="background:#fef3c7;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    </div>
                    <div>
                        <h3>Documentos Extras</h3>
                        <p class="admin-card-subtitle">Documentos que o cliente receberá ao reservar (termos, formulários, etc.)</p>
                    </div>
                </div>

                <!-- Documentos existentes -->
                <?php
                $existingDocs = ($isEdit && !empty($trip['documents'])) ? json_decode($trip['documents'], true) : [];
                ?>
                <?php if (!empty($existingDocs)): ?>
                <div id="docs-current" style="display:flex;flex-direction:column;gap:8px;margin-bottom:16px;">
                    <?php foreach ($existingDocs as $idx => $doc): ?>
                    <div class="doc-item" style="display:flex;align-items:center;gap:12px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#64748b" stroke-width="2" style="flex-shrink:0;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        <span style="flex:1;font-size:13px;color:#334155;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= e($doc['name'] ?? basename($doc['path'] ?? '')) ?></span>
                        <span style="font-size:11px;color:#94a3b8;text-transform:uppercase;"><?= e(strtoupper($doc['type'] ?? '')) ?></span>
                        <input type="hidden" name="docs_existing[]" value="<?= e(json_encode($doc)) ?>">
                        <button type="button" onclick="this.closest('.doc-item').remove()" style="background:#ef4444;color:#fff;border:none;border-radius:50%;width:22px;height:22px;font-size:13px;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Upload novos documentos -->
                <label for="docFiles" class="btn btn-outline" style="cursor:pointer;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Adicionar Documentos
                </label>
                <input type="file" name="doc_files[]" id="docFiles" multiple accept=".pdf,.png,.jpg,.jpeg,.gif,.webp,.doc,.docx,.xls,.xlsx" style="display:none;" onchange="previewDocFiles(this)">
                <p style="font-size:10px;color:#94a3b8;margin-top:6px;">PDF, PNG, JPG, DOC, XLS — Máx. 10MB cada</p>
                <div id="docPreviews" style="display:flex;flex-direction:column;gap:6px;margin-top:10px;"></div>
            </div>

            <!-- Inclui / Não Inclui -->
            <div class="admin-card">
                <div class="admin-card-header"><div class="admin-card-icon admin-card-icon-blue"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg></div><div><h3>O que Inclui / Não Inclui</h3><p class="admin-card-subtitle">Itens inclusos e não inclusos</p></div></div>
                <div class="form-group"><label>Inclui</label><div id="includes-list" class="repeater-list"><?php $includes = $isEdit && !empty($trip['includes']) ? json_decode($trip['includes'], true) : ['']; foreach ($includes as $inc): ?><div class="repeater-item"><input type="text" name="includes[]" value="<?= e($inc) ?>" class="form-control" placeholder="Ex: Almoço incluso"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button></div><?php endforeach; ?></div><button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('includes-list', 'includes[]', 'Ex: Almoço incluso')">+ Adicionar</button></div>
                <div class="form-group"><label>Não Inclui</label><div id="excludes-list" class="repeater-list"><?php $excludes = $isEdit && !empty($trip['excludes']) ? json_decode($trip['excludes'], true) : ['']; foreach ($excludes as $exc): ?><div class="repeater-item"><input type="text" name="excludes[]" value="<?= e($exc) ?>" class="form-control" placeholder="Ex: Bebidas alcoólicas"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button></div><?php endforeach; ?></div><button type="button" class="btn btn-sm btn-outline" onclick="addRepeater('excludes-list', 'excludes[]', 'Ex: Bebidas alcoólicas')">+ Adicionar</button></div>
            </div>

            <!-- Tabela de Preços por Grupo -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon" style="background:#dbeafe;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div>
                        <h3>Tabela de Preços por Grupo</h3>
                        <p class="admin-card-subtitle">Preço fixo total por número de passageiros (não multiplicativo)</p>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="checkbox-label"><input type="checkbox" name="group_pricing_enabled" id="groupPricingEnabled" <?= !empty($trip['group_pricing_enabled']) ? 'checked' : '' ?>> Ativar tabela de preços por grupo</label>
                    <small style="display:block;color:#6b7280;margin-top:4px;">Quando ativo, o preço dos adultos será o valor fixo definido abaixo de acordo com o número de adultos. Crianças e infantis mantêm preço por pessoa.</small>
                </div>
                <div id="groupPricingSection" style="<?= empty($trip['group_pricing_enabled']) ? 'display:none;' : '' ?>">
                    <div id="groupPricingList">
                        <?php
                        $groupPricing = ($isEdit && !empty($trip['group_pricing'])) ? json_decode($trip['group_pricing'], true) : [];
                        if (empty($groupPricing)) $groupPricing = [['pax' => '1', 'price' => '']];
                        foreach ($groupPricing as $gpi => $gp):
                        ?>
                        <div class="group-pricing-item" style="display:flex;align-items:center;gap:12px;padding:12px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:8px;">
                            <div style="flex:0 0 120px;">
                                <label style="font-size:12px;font-weight:600;color:#475569;">Adultos</label>
                                <input type="number" name="group_pricing[<?= $gpi ?>][pax]" value="<?= e($gp['pax'] ?? '') ?>" class="form-control" min="1" max="50" placeholder="Qtd" required>
                            </div>
                            <div style="flex:1;">
                                <label style="font-size:12px;font-weight:600;color:#475569;">Preço Total (USD)</label>
                                <input type="number" name="group_pricing[<?= $gpi ?>][price]" value="<?= e($gp['price'] ?? '') ?>" class="form-control" min="0" step="0.01" placeholder="Ex: 120.00" required>
                            </div>
                            <?php if ($gpi > 0): ?>
                            <button type="button" class="btn btn-sm btn-danger" style="margin-top:18px;" onclick="this.closest('.group-pricing-item').remove();">&times;</button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-outline" id="addGroupPricingBtn" style="margin-top:4px;">+ Adicionar Faixa</button>
                    <p style="font-size:11px;color:#94a3b8;margin-top:8px;">Ex: 1 adulto = US$70, 2 adultos = US$120, 3 adultos = US$160. Crianças e infantis somam separadamente pelo preço por pessoa.</p>
                </div>
            </div>

            <!-- Pacotes por Composição -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon" style="background:#fce7f3;">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#db2777" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="23 7 16 12 16 2 23 7"/><line x1="1" y1="20" x2="23" y2="20"/><line x1="1" y1="23" x2="23" y2="23"/></svg>
                    </div>
                    <div>
                        <h3>Pacotes por Composição</h3>
                        <p class="admin-card-subtitle">Preço por combinação de pessoas + veículos/unidades (ex: buggies, quadriciclos)</p>
                    </div>
                </div>
                <div class="form-group" style="margin-bottom:16px;">
                    <label class="checkbox-label"><input type="checkbox" name="composition_pricing_enabled" id="compositionPricingEnabled" <?= !empty($trip['composition_pricing_enabled']) ? 'checked' : '' ?>> Ativar pacotes por composição</label>
                    <small style="display:block;color:#6b7280;margin-top:4px;">Quando ativo, o cliente escolhe um pacote/composição ao reservar. Cada pacote define: pessoas, unidades, preço fixo.</small>
                </div>
                <div id="compositionPricingSection" style="<?= empty($trip['composition_pricing_enabled'] ?? null) ? 'display:none;' : '' ?>">
                    <div id="compositionPackagesList">
                        <?php
                        $compPkgs = $compositionPackages ?? [];
                        if (empty($compPkgs)) $compPkgs = [['label' => '', 'pax' => '', 'units' => '1', 'unit_label' => '', 'pax_per_unit' => '', 'price' => '']];
                        foreach ($compPkgs as $cpi => $cp):
                        ?>
                        <div class="composition-pkg-item card-inner" style="padding:14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:10px;">
                            <div class="form-row" style="grid-template-columns: 1fr; gap:10px;">
                                <div class="form-group" style="margin-bottom:8px;">
                                    <label>Nome/Descrição do Pacote</label>
                                    <input type="text" name="composition_packages[<?= $cpi ?>][label]" value="<?= e($cp['label'] ?? '') ?>" class="form-control" placeholder="Ex: 2 pessoas em 1 Buggy">
                                </div>
                            </div>
                            <div class="form-row" style="grid-template-columns: 1fr 1fr 1fr 1fr 1fr; gap:10px;">
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Pessoas</label>
                                    <input type="number" name="composition_packages[<?= $cpi ?>][pax]" value="<?= e($cp['pax'] ?? '') ?>" class="form-control" min="1" placeholder="Qtd" required>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Unidades</label>
                                    <input type="number" name="composition_packages[<?= $cpi ?>][units]" value="<?= e($cp['units'] ?? '1') ?>" class="form-control" min="1" placeholder="1">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Nome Unidade</label>
                                    <input type="text" name="composition_packages[<?= $cpi ?>][unit_label]" value="<?= e($cp['unit_label'] ?? '') ?>" class="form-control" placeholder="Ex: Buggy">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Pax/Unidade</label>
                                    <input type="number" name="composition_packages[<?= $cpi ?>][pax_per_unit]" value="<?= e($cp['pax_per_unit'] ?? '') ?>" class="form-control" min="1" placeholder="Opcional">
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Preço (USD)</label>
                                    <input type="number" name="composition_packages[<?= $cpi ?>][price]" value="<?= e($cp['price'] ?? '') ?>" class="form-control" min="0" step="0.01" placeholder="Ex: 120.00" required>
                                </div>
                            </div>
                            <?php if ($cpi > 0): ?>
                            <button type="button" class="btn btn-sm btn-danger" style="margin-top:10px;" onclick="this.closest('.composition-pkg-item').remove();">Remover</button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" class="btn btn-outline" id="addCompositionPkgBtn" style="margin-top:4px;">+ Adicionar Pacote</button>
                    <p style="font-size:11px;color:#94a3b8;margin-top:8px;">Configure cada combinação com seu preço. O cliente escolherá o pacote ao reservar.</p>
                </div>
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
                        <p style="font-size:11px;color:#94a3b8;margin-bottom:10px;">Adicione imagens ao carrossel do passeio (apenas upload)</p>
                        
                        <!-- Imagens atuais (miniaturas) -->
                        <?php
                        $galleryImages = ($isEdit && !empty($trip['gallery'])) ? json_decode($trip['gallery'], true) : [];
                        ?>
                        <?php if (!empty($galleryImages)): ?>
                        <label style="font-size:11px;font-weight:600;color:#64748b;margin-bottom:6px;display:block;text-transform:uppercase;">Imagens atuais</label>
                        <div id="gallery-current" style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px;">
                            <?php foreach ($galleryImages as $idx => $gImg): ?>
                            <div class="gallery-thumb" id="galleryThumb<?= $idx ?>" style="position:relative;width:72px;height:72px;border-radius:8px;overflow:hidden;border:2px solid #e2e8f0;flex-shrink:0;">
                                <img src="<?= e($gImg) ?>" style="width:100%;height:100%;object-fit:cover;" alt="">
                                <input type="hidden" name="gallery_existing[]" value="<?= e($gImg) ?>">
                                <button type="button" onclick="this.closest('.gallery-thumb').remove()" style="position:absolute;top:2px;right:2px;width:18px;height:18px;border-radius:50%;background:#ef4444;color:#fff;border:none;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">&times;</button>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Upload de novas imagens -->
                        <div style="margin-bottom:14px;">
                            <label for="galleryFiles" class="btn btn-outline" style="cursor:pointer;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                Escolher Imagens
                            </label>
                            <input type="file" name="gallery_files[]" id="galleryFiles" multiple accept="image/*" style="display:none;" onchange="previewGalleryFiles(this)">
                            <span id="galleryCount" style="font-size:12px;color:#94a3b8;margin-left:10px;"></span>
                            <p style="font-size:10px;color:#94a3b8;margin-top:6px;">JPG, PNG, WebP, GIF, SVG, AVIF — Máx. 10MB cada</p>
                            <div id="galleryPreviews" style="display:flex;flex-wrap:wrap;gap:8px;margin-top:10px;"></div>
                        </div>
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

function previewGalleryFiles(input) {
    const container = document.getElementById('galleryPreviews');
    const countEl = document.getElementById('galleryCount');
    // Acumular arquivos em um DataTransfer para permitir adicionar mais
    if (!window._galleryDT) window._galleryDT = new DataTransfer();
    for (let i = 0; i < input.files.length; i++) {
        window._galleryDT.items.add(input.files[i]);
    }
    input.files = window._galleryDT.files;
    countEl.textContent = window._galleryDT.files.length + ' arquivo(s) selecionado(s)';
    // Renderizar previews
    container.innerHTML = '';
    for (let i = 0; i < window._galleryDT.files.length; i++) {
        const file = window._galleryDT.files[i];
        const reader = new FileReader();
        const idx = i;
        reader.onload = function(e) {
            const thumb = document.createElement('div');
            thumb.style.cssText = 'position:relative;width:64px;height:64px;border-radius:8px;overflow:hidden;border:2px solid #e2e8f0;flex-shrink:0;';
            thumb.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;"><button type="button" onclick="removeGalleryFile(' + idx + ')" style="position:absolute;top:2px;right:2px;width:18px;height:18px;border-radius:50%;background:#ef4444;color:#fff;border:none;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">&times;</button>';
            container.appendChild(thumb);
        };
        reader.readAsDataURL(file);
    }
}

function removeGalleryFile(idx) {
    const input = document.getElementById('galleryFiles');
    const dt = new DataTransfer();
    for (let i = 0; i < window._galleryDT.files.length; i++) {
        if (i !== idx) dt.items.add(window._galleryDT.files[i]);
    }
    window._galleryDT = dt;
    input.files = dt.files;
    previewGalleryFiles(input);
}
document.addEventListener('click', function(e) { if (e.target.classList.contains('repeater-remove')) { e.target.closest('.repeater-item, .package-item').remove(); } });

function previewDocFiles(input) {
    const container = document.getElementById('docPreviews');
    // Acumular arquivos usando DataTransfer
    if (!window._docDT) window._docDT = new DataTransfer();
    for (let i = 0; i < input.files.length; i++) {
        window._docDT.items.add(input.files[i]);
    }
    input.files = window._docDT.files;
    // Renderizar lista
    container.innerHTML = '';
    for (let i = 0; i < window._docDT.files.length; i++) {
        const file = window._docDT.files[i];
        const div = document.createElement('div');
        div.style.cssText = 'display:flex;align-items:center;gap:10px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;font-size:13px;color:#166534;';
        div.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg><span style="flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">' + file.name + '</span><span style="font-size:11px;color:#64748b;">' + (file.size / 1024 / 1024).toFixed(2) + ' MB</span><button type="button" onclick="removeDocFile(' + i + ')" style="background:#ef4444;color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;">&times;</button>';
        container.appendChild(div);
    }
}

function removeDocFile(idx) {
    const input = document.getElementById('docFiles');
    const dt = new DataTransfer();
    for (let i = 0; i < window._docDT.files.length; i++) {
        if (i !== idx) dt.items.add(window._docDT.files[i]);
    }
    window._docDT = dt;
    input.files = dt.files;
    previewDocFiles(input);
}
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

// FAQ - Adicionar pergunta
document.getElementById('addFaqBtn')?.addEventListener('click', function() {
    var list = document.getElementById('faqsList');
    var i = list.children.length;
    var div = document.createElement('div');
    div.className = 'faq-item-admin';
    div.style.cssText = 'padding:14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:10px;';
    div.innerHTML = '<div class="form-group" style="margin-bottom:8px;"><label>Pergunta</label><input type="text" name="faqs[' + i + '][question]" class="form-control" placeholder="Ex: Preciso saber nadar?"></div><div class="form-group" style="margin-bottom:0;"><label>Resposta</label><textarea name="faqs[' + i + '][answer]" class="form-control" rows="2" placeholder="Resposta..."></textarea></div><button type="button" class="btn btn-sm btn-danger" style="margin-top:8px;" onclick="this.closest(\'.faq-item-admin\').remove()">Remover</button>';
    list.appendChild(div);
});

// Group Pricing - Toggle e adicionar faixas
document.getElementById('groupPricingEnabled')?.addEventListener('change', function() {
    document.getElementById('groupPricingSection').style.display = this.checked ? '' : 'none';
});

document.getElementById('addGroupPricingBtn')?.addEventListener('click', function() {
    var list = document.getElementById('groupPricingList');
    var i = list.children.length;
    var div = document.createElement('div');
    div.className = 'group-pricing-item';
    div.style.cssText = 'display:flex;align-items:center;gap:12px;padding:12px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;margin-bottom:8px;';
    div.innerHTML = '<div style="flex:0 0 120px;"><label style="font-size:12px;font-weight:600;color:#475569;">Adultos</label><input type="number" name="group_pricing[' + i + '][pax]" class="form-control" min="1" max="50" placeholder="Qtd" required></div><div style="flex:1;"><label style="font-size:12px;font-weight:600;color:#475569;">Preço Total (USD)</label><input type="number" name="group_pricing[' + i + '][price]" class="form-control" min="0" step="0.01" placeholder="Ex: 120.00" required></div><button type="button" class="btn btn-sm btn-danger" style="margin-top:18px;" onclick="this.closest(\'.group-pricing-item\').remove();">&times;</button>';
    list.appendChild(div);
});

// Composition Pricing - Toggle e adicionar pacotes
document.getElementById('compositionPricingEnabled')?.addEventListener('change', function() {
    document.getElementById('compositionPricingSection').style.display = this.checked ? '' : 'none';
});

document.getElementById('addCompositionPkgBtn')?.addEventListener('click', function() {
    var list = document.getElementById('compositionPackagesList');
    var i = list.children.length;
    var div = document.createElement('div');
    div.className = 'composition-pkg-item card-inner';
    div.style.cssText = 'padding:14px;background:#f9fafb;border:1px solid #e5e7eb;border-radius:8px;margin-bottom:10px;';
    div.innerHTML = '<div class="form-row" style="grid-template-columns:1fr;gap:10px;"><div class="form-group" style="margin-bottom:8px;"><label>Nome/Descrição do Pacote</label><input type="text" name="composition_packages[' + i + '][label]" class="form-control" placeholder="Ex: 2 pessoas em 1 Buggy"></div></div><div class="form-row" style="grid-template-columns:1fr 1fr 1fr 1fr 1fr;gap:10px;"><div class="form-group" style="margin-bottom:0;"><label>Pessoas</label><input type="number" name="composition_packages[' + i + '][pax]" class="form-control" min="1" placeholder="Qtd" required></div><div class="form-group" style="margin-bottom:0;"><label>Unidades</label><input type="number" name="composition_packages[' + i + '][units]" class="form-control" min="1" placeholder="1" value="1"></div><div class="form-group" style="margin-bottom:0;"><label>Nome Unidade</label><input type="text" name="composition_packages[' + i + '][unit_label]" class="form-control" placeholder="Ex: Buggy"></div><div class="form-group" style="margin-bottom:0;"><label>Pax/Unidade</label><input type="number" name="composition_packages[' + i + '][pax_per_unit]" class="form-control" min="1" placeholder="Opcional"></div><div class="form-group" style="margin-bottom:0;"><label>Preço (USD)</label><input type="number" name="composition_packages[' + i + '][price]" class="form-control" min="0" step="0.01" placeholder="Ex: 120.00" required></div></div><button type="button" class="btn btn-sm btn-danger" style="margin-top:10px;" onclick="this.closest(\'.composition-pkg-item\').remove();">Remover</button>';
    list.appendChild(div);
});
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
