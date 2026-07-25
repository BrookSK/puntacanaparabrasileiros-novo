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

                <div id="routesContainer">
                    <?php foreach ($routes as $i => $route): ?>
                    <div class="route-block" data-index="<?= $i ?>">
                        <div class="route-block-label">Rota <?= $i + 1 ?></div>
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
                    <?php endforeach; ?>
                </div>

                <button type="button" class="btn btn-outline" onclick="addRoute()" style="margin-top:10px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Adicionar Rota
                </button>
            </div>
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
function addRoute() {
    const container = document.getElementById('routesContainer');
    const locOptions = `<?php foreach ($locations as $loc): ?><option value="<?= (int)$loc['id'] ?>"><?= e($loc['title']) ?></option><?php endforeach; ?>`;
    container.insertAdjacentHTML('beforeend', `
    <div class="route-block" data-index="${routeIndex}">
        <div class="route-block-label">Rota ${routeIndex + 1}</div>
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
                    <input type="number" name="routes[${routeIndex}][tariffs][0][max_pax]" class="form-control" value="10">
                </div>
                <div class="form-group">
                    <label>Preço (USD)</label>
                    <div class="input-prefix-wrapper"><span class="input-prefix">$</span><input type="number" step="0.01" name="routes[${routeIndex}][tariffs][0][price]" class="form-control input-with-prefix" value="0"></div>
                </div>
            </div>
        </div>
    </div>`);
    routeIndex++;
}
</script>
