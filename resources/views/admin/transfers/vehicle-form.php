<?php
$isEdit = !empty($vehicle);
$action = $isEdit ? '/admin/transfers/veiculos/' . $vehicle['id'] . '/editar' : '/admin/transfers/veiculos/criar';
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Veículo' : 'Novo Veículo' ?></h2>
    <a href="/admin/transfers/veiculos" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para Veículos
    </a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <div class="admin-grid-2">
        <!-- Coluna Esquerda: Formulário -->
        <div>
            <!-- Informações Básicas -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-blue">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                    </div>
                    <div>
                        <h3>Informações do Veículo</h3>
                        <p class="admin-card-subtitle">Dados principais do veículo de transfer</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Título <span class="required">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?= e($vehicle['title'] ?? '') ?>" placeholder="Ex: Van Privativa Executiva" required>
                    </div>
                    <div class="form-group col-6">
                        <label>Tipo de Veículo</label>
                        <select name="vehicle_type" class="form-control">
                            <option value="van" <?= ($vehicle['vehicle_type'] ?? '') === 'van' ? 'selected' : '' ?>>Van</option>
                            <option value="bus" <?= ($vehicle['vehicle_type'] ?? '') === 'bus' ? 'selected' : '' ?>>Ônibus</option>
                            <option value="suv" <?= ($vehicle['vehicle_type'] ?? '') === 'suv' ? 'selected' : '' ?>>SUV</option>
                            <option value="sedan" <?= ($vehicle['vehicle_type'] ?? '') === 'sedan' ? 'selected' : '' ?>>Sedan</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descrição</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Descreva as características e diferenciais do veículo..."><?= e($vehicle['description'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Capacidade -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-green">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div>
                        <h3>Capacidade</h3>
                        <p class="admin-card-subtitle">Limite de passageiros e bagagem</p>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Max Passageiros (Total)</label>
                        <input type="number" name="max_passengers" class="form-control" value="<?= (int)($vehicle['max_passengers'] ?? 0) ?>" min="1" placeholder="0">
                    </div>
                    <div class="form-group col-6">
                        <label>Max Adultos</label>
                        <input type="number" name="max_adults" class="form-control" value="<?= (int)($vehicle['max_adults'] ?? 0) ?>" min="0" placeholder="0">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-6">
                        <label>Max Crianças</label>
                        <input type="number" name="max_children" class="form-control" value="<?= (int)($vehicle['max_children'] ?? 0) ?>" min="0" placeholder="0">
                    </div>
                    <div class="form-group col-6">
                        <label>Max Bagagem</label>
                        <input type="number" name="max_luggage" class="form-control" value="<?= (int)($vehicle['max_luggage'] ?? 0) ?>" min="0" placeholder="0">
                    </div>
                </div>
            </div>

            <?php if ($isEdit): ?>
            <!-- Rotas e Tarifas -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-icon admin-card-icon-orange">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/></svg>
                    </div>
                    <div>
                        <h3>Rotas e Tarifas</h3>
                        <p class="admin-card-subtitle">Configure as rotas disponíveis e preços por faixa</p>
                    </div>
                </div>

                <!-- Barra de busca e contador -->
                <div class="routes-toolbar">
                    <div class="routes-search-box">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                        <input type="text" id="routeSearchInput" class="form-control" placeholder="Buscar rota por origem ou destino..." oninput="filterRoutes(this.value)">
                    </div>
                    <div class="routes-actions-bar">
                        <span class="routes-counter"><strong><?= count($routes) ?></strong> rota<?= count($routes) !== 1 ? 's' : '' ?></span>
                        <button type="button" class="btn btn-sm btn-outline" onclick="toggleAllRoutes(true)">Expandir Todas</button>
                        <button type="button" class="btn btn-sm btn-outline" onclick="toggleAllRoutes(false)">Recolher Todas</button>
                    </div>
                </div>

                <div id="routesContainer">
                    <?php foreach ($routes as $i => $route): ?>
                    <div class="route-block" data-index="<?= $i ?>" data-search="<?= e(strtolower(($route['origin_title'] ?? '') . ' ' . ($route['destination_title'] ?? ''))) ?>">
                        <div class="route-block-header" onclick="toggleRoute(this)">
                            <div class="route-block-info">
                                <span class="route-block-number"><?= $i + 1 ?></span>
                                <div class="route-block-summary">
                                    <span class="route-block-title">
                                        <?= e($route['origin_title'] ?? 'Origem') ?>
                                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                                        <?= e($route['destination_title'] ?? 'Destino') ?>
                                    </span>
                                    <span class="route-block-meta">
                                        Base: $<?= number_format((float)($route['base_price'] ?? 0), 2) ?>
                                        <?php if (!empty($route['duration'])): ?> &bull; <?= (int)$route['duration'] ?> min<?php endif; ?>
                                        <?php if (!empty($route['tariffs'])): ?> &bull; <?= count($route['tariffs']) ?> tarifa<?= count($route['tariffs']) !== 1 ? 's' : '' ?><?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <svg class="route-block-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                        </div>
                        <div class="route-block-body">
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label>Origem</label>
                                    <select name="routes[<?= $i ?>][origin_id]" class="form-control">
                                        <option value="">Selecione</option>
                                        <?php foreach ($locations as $loc): ?>
                                        <option value="<?= (int)$loc['id'] ?>" <?= (int)($route['origin_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '' ?>><?= e($loc['title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group col-6">
                                    <label>Destino</label>
                                    <select name="routes[<?= $i ?>][destination_id]" class="form-control">
                                        <option value="">Selecione</option>
                                        <?php foreach ($locations as $loc): ?>
                                        <option value="<?= (int)$loc['id'] ?>" <?= (int)($route['destination_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '' ?>><?= e($loc['title']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group col-6">
                                    <label>Preço Base (USD)</label>
                                    <div class="input-prefix-wrapper">
                                        <span class="input-prefix">$</span>
                                        <input type="number" step="0.01" name="routes[<?= $i ?>][base_price]" class="form-control input-with-prefix" value="<?= number_format((float)($route['base_price'] ?? 0), 2, '.', '') ?>">
                                    </div>
                                </div>
                                <div class="form-group col-6">
                                    <label>Duração (minutos)</label>
                                    <input type="number" name="routes[<?= $i ?>][duration]" class="form-control" value="<?= (int)($route['duration'] ?? 0) ?>">
                                </div>
                            </div>

                            <?php if (!empty($route['tariffs'])): ?>
                            <div class="tariffs-block">
                                <p class="tariffs-block-label">Tarifas por faixa de passageiros:</p>
                                <?php foreach ($route['tariffs'] as $j => $tariff): ?>
                                <div class="form-row form-row-4">
                                    <div class="form-group">
                                        <label>Serviço</label>
                                        <select name="routes[<?= $i ?>][tariffs][<?= $j ?>][service_type]" class="form-control">
                                            <option value="private" <?= ($tariff['service_type'] ?? '') === 'private' ? 'selected' : '' ?>>Privado</option>
                                            <option value="shared" <?= ($tariff['service_type'] ?? '') === 'shared' ? 'selected' : '' ?>>Compartilhado</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Min Pax</label>
                                        <input type="number" name="routes[<?= $i ?>][tariffs][<?= $j ?>][min_pax]" class="form-control" value="<?= (int)($tariff['min_pax'] ?? 1) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Max Pax</label>
                                        <input type="number" name="routes[<?= $i ?>][tariffs][<?= $j ?>][max_pax]" class="form-control" value="<?= (int)($tariff['max_pax'] ?? 10) ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>Preço (USD)</label>
                                        <div class="input-prefix-wrapper">
                                            <span class="input-prefix">$</span>
                                            <input type="number" step="0.01" name="routes[<?= $i ?>][tariffs][<?= $j ?>][price]" class="form-control input-with-prefix" value="<?= number_format((float)($tariff['price'] ?? 0), 2, '.', '') ?>">
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" class="btn btn-outline" onclick="addRoute()" style="margin-top:16px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Adicionar Rota
                </button>
            </div>

            <style>
            .routes-toolbar{display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:20px;flex-wrap:wrap}
            .routes-search-box{display:flex;align-items:center;gap:8px;flex:1;max-width:360px;background:#f8f9fa;border:1px solid #e2e8f0;border-radius:8px;padding:0 12px}
            .routes-search-box svg{flex-shrink:0;color:#94a3b8}
            .routes-search-box .form-control{border:none;background:transparent;padding:9px 0;box-shadow:none}
            .routes-search-box .form-control:focus{box-shadow:none;border:none}
            .routes-actions-bar{display:flex;align-items:center;gap:8px}
            .routes-counter{font-size:12px;color:#64748b;padding:4px 10px;background:#f1f5f9;border-radius:10px}
            .route-block{border:1px solid #e2e8f0;border-radius:10px;margin-bottom:12px;overflow:hidden;transition:all .2s}
            .route-block.hidden{display:none}
            .route-block-header{display:flex;align-items:center;justify-content:space-between;padding:14px 18px;cursor:pointer;background:#fafbfc;border-bottom:1px solid transparent;transition:all .15s;user-select:none}
            .route-block-header:hover{background:#f1f5f9}
            .route-block.open .route-block-header{background:#f0f9ff;border-bottom-color:#e2e8f0}
            .route-block-info{display:flex;align-items:center;gap:12px}
            .route-block-number{width:28px;height:28px;border-radius:50%;background:#e2e8f0;color:#475569;font-size:11px;font-weight:700;display:flex;align-items:center;justify-content:center;flex-shrink:0}
            .route-block.open .route-block-number{background:#3b82f6;color:#fff}
            .route-block-summary{display:flex;flex-direction:column;gap:2px}
            .route-block-title{font-size:13px;font-weight:600;color:#1e293b;display:flex;align-items:center;gap:6px}
            .route-block-title svg{color:#94a3b8}
            .route-block-meta{font-size:11px;color:#64748b}
            .route-block-chevron{transition:transform .2s;color:#94a3b8;flex-shrink:0}
            .route-block.open .route-block-chevron{transform:rotate(180deg)}
            .route-block-body{display:none;padding:18px;background:#fff;border-top:1px solid #f1f5f9}
            .route-block.open .route-block-body{display:block}
            .tariffs-block{margin-top:16px;padding-top:16px;border-top:1px dashed #e2e8f0}
            .tariffs-block-label{font-size:12px;font-weight:600;color:#64748b;text-transform:uppercase;letter-spacing:.3px;margin-bottom:12px}
            </style>
            <?php endif; ?>
        </div>

        <!-- Coluna Direita: Configurações -->
        <div>
            <!-- Status e Imagem -->
            <div class="admin-card admin-card-sticky summary-card">
                <div class="summary-card-header">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2h0a2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.6h0a1.65 1.65 0 001-1.51V3a2 2 0 012-2h0a2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9h0a1.65 1.65 0 001.51 1H21a2 2 0 012 2h0a2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                    <h3>Configurações</h3>
                </div>

                <div class="summary-card-body">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="active" <?= ($vehicle['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inactive" <?= ($vehicle['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                        <small class="form-hint">Veículos inativos não aparecem na busca.</small>
                    </div>

                    <div class="form-group">
                        <label>Ordem de Exibição</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= (int)($vehicle['sort_order'] ?? 0) ?>">
                        <small class="form-hint">Menor número = aparece primeiro.</small>
                    </div>

                    <div class="form-group">
                        <label>Imagem do Veículo</label>
                        <div class="file-upload-area">
                            <input type="file" name="image" id="vehicleImage" class="file-input-hidden" accept="image/*">
                            <label for="vehicleImage" class="file-upload-label">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                <span>Escolher imagem</span>
                            </label>
                            <?php if (!empty($vehicle['image'])): ?>
                            <div class="file-upload-preview">
                                <img src="<?= e($vehicle['image']) ?>" alt="Preview">
                            </div>
                            <?php endif; ?>
                        </div>
                        <small class="form-hint">JPG, PNG ou WebP. Recomendado: 800x600px.</small>
                    </div>
                </div>

                <div class="summary-card-actions">
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                        <?= $isEdit ? 'Salvar Alterações' : 'Criar Veículo' ?>
                    </button>
                    <a href="/admin/transfers/veiculos" class="btn btn-outline btn-block">Cancelar</a>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
let routeIndex = <?= count($routes ?? []) ?>;

function toggleRoute(header) {
    const block = header.closest('.route-block');
    block.classList.toggle('open');
}

function toggleAllRoutes(open) {
    document.querySelectorAll('.route-block').forEach(function(block) {
        if (block.classList.contains('hidden')) return;
        if (open) block.classList.add('open');
        else block.classList.remove('open');
    });
}

function filterRoutes(query) {
    const q = query.toLowerCase().trim();
    document.querySelectorAll('.route-block').forEach(function(block) {
        if (!q) {
            block.classList.remove('hidden');
            return;
        }
        const searchText = block.getAttribute('data-search') || '';
        if (searchText.includes(q)) {
            block.classList.remove('hidden');
        } else {
            block.classList.add('hidden');
        }
    });
}

function addRoute() {
    const container = document.getElementById('routesContainer');
    const locOptions = `<?php foreach ($locations as $loc): ?><option value="<?= (int)$loc['id'] ?>"><?= e($loc['title']) ?></option><?php endforeach; ?>`;
    container.insertAdjacentHTML('beforeend', `
    <div class="route-block open" data-index="${routeIndex}" data-search="">
        <div class="route-block-header" onclick="toggleRoute(this)">
            <div class="route-block-info">
                <span class="route-block-number">${routeIndex + 1}</span>
                <div class="route-block-summary">
                    <span class="route-block-title">Nova Rota</span>
                    <span class="route-block-meta">Configure origem e destino</span>
                </div>
            </div>
            <svg class="route-block-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
        <div class="route-block-body">
            <div class="form-row">
                <div class="form-group col-6">
                    <label>Origem</label>
                    <select name="routes[${routeIndex}][origin_id]" class="form-control"><option value="">Selecione</option>${locOptions}</select>
                </div>
                <div class="form-group col-6">
                    <label>Destino</label>
                    <select name="routes[${routeIndex}][destination_id]" class="form-control"><option value="">Selecione</option>${locOptions}</select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-6">
                    <label>Preço Base (USD)</label>
                    <div class="input-prefix-wrapper"><span class="input-prefix">$</span><input type="number" step="0.01" name="routes[${routeIndex}][base_price]" class="form-control input-with-prefix" value="0"></div>
                </div>
                <div class="form-group col-6">
                    <label>Duração (minutos)</label>
                    <input type="number" name="routes[${routeIndex}][duration]" class="form-control" value="0">
                </div>
            </div>
            <div class="tariffs-block">
                <p class="tariffs-block-label">Tarifas por faixa de passageiros:</p>
                <div class="form-row form-row-4">
                    <div class="form-group">
                        <label>Serviço</label>
                        <select name="routes[${routeIndex}][tariffs][0][service_type]" class="form-control"><option value="private">Privado</option><option value="shared">Compartilhado</option></select>
                    </div>
                    <div class="form-group">
                        <label>Min Pax</label>
                        <input type="number" name="routes[${routeIndex}][tariffs][0][min_pax]" class="form-control" value="1">
                    </div>
                    <div class="form-group">
                        <label>Max Pax</label>
                        <input type="number" name="routes[${routeIndex}][tariffs][0][max_pax]" class="form-control" value="4">
                    </div>
                    <div class="form-group">
                        <label>Preço (USD)</label>
                        <div class="input-prefix-wrapper"><span class="input-prefix">$</span><input type="number" step="0.01" name="routes[${routeIndex}][tariffs][0][price]" class="form-control input-with-prefix" value="0"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>`);
    routeIndex++;
    // Scroll to new route
    container.lastElementChild.scrollIntoView({behavior: 'smooth', block: 'center'});
}
</script>
