<?php
$isEdit = !empty($vehicle);
$action = $isEdit ? '/admin/transfers/veiculos/' . $vehicle['id'] . '/editar' : '/admin/transfers/veiculos/criar';
?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Veículo' : 'Novo Veículo' ?></h2>
    <a href="/admin/transfers/veiculos" class="btn btn-sm btn-outline">&larr; Voltar</a>
</div>

<form method="POST" action="<?= $action ?>" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>

    <fieldset class="form-section">
        <legend>Informações Básicas</legend>
        <div class="form-row">
            <div class="form-group col-6">
                <label>Título *</label>
                <input type="text" name="title" class="form-control" value="<?= e($vehicle['title'] ?? '') ?>" required>
            </div>
            <div class="form-group col-3">
                <label>Tipo de Veículo</label>
                <select name="vehicle_type" class="form-control">
                    <option value="van" <?= ($vehicle['vehicle_type'] ?? '') === 'van' ? 'selected' : '' ?>>Van</option>
                    <option value="bus" <?= ($vehicle['vehicle_type'] ?? '') === 'bus' ? 'selected' : '' ?>>Ônibus</option>
                    <option value="suv" <?= ($vehicle['vehicle_type'] ?? '') === 'suv' ? 'selected' : '' ?>>SUV</option>
                    <option value="sedan" <?= ($vehicle['vehicle_type'] ?? '') === 'sedan' ? 'selected' : '' ?>>Sedan</option>
                </select>
            </div>
            <div class="form-group col-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="active" <?= ($vehicle['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Ativo</option>
                    <option value="inactive" <?= ($vehicle['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inativo</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Descrição</label>
            <textarea name="description" class="form-control" rows="3"><?= e($vehicle['description'] ?? '') ?></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-3">
                <label>Max Passageiros</label>
                <input type="number" name="max_passengers" class="form-control" value="<?= (int)($vehicle['max_passengers'] ?? 0) ?>" min="1">
            </div>
            <div class="form-group col-3">
                <label>Max Adultos</label>
                <input type="number" name="max_adults" class="form-control" value="<?= (int)($vehicle['max_adults'] ?? 0) ?>" min="0">
            </div>
            <div class="form-group col-3">
                <label>Max Crianças</label>
                <input type="number" name="max_children" class="form-control" value="<?= (int)($vehicle['max_children'] ?? 0) ?>" min="0">
            </div>
            <div class="form-group col-3">
                <label>Max Bagagem</label>
                <input type="number" name="max_luggage" class="form-control" value="<?= (int)($vehicle['max_luggage'] ?? 0) ?>" min="0">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-3">
                <label>Ordem</label>
                <input type="number" name="sort_order" class="form-control" value="<?= (int)($vehicle['sort_order'] ?? 0) ?>">
            </div>
            <div class="form-group col-9">
                <label>Imagem</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <?php if (!empty($vehicle['image'])): ?>
                <img src="<?= e($vehicle['image']) ?>" alt="" style="max-width:200px;margin-top:8px;border-radius:6px;">
                <?php endif; ?>
            </div>
        </div>
    </fieldset>

    <?php if ($isEdit): ?>
    <fieldset class="form-section">
        <legend>Rotas e Tarifas</legend>
        <div id="routesContainer">
            <?php foreach ($routes as $i => $route): ?>
            <div class="route-block" data-index="<?= $i ?>">
                <div class="form-row">
                    <div class="form-group col-4">
                        <label>Origem</label>
                        <select name="routes[<?= $i ?>][origin_id]" class="form-control">
                            <option value="">Selecione</option>
                            <?php foreach ($locations as $loc): ?>
                            <option value="<?= (int)$loc['id'] ?>" <?= (int)($route['origin_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '' ?>><?= e($loc['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-4">
                        <label>Destino</label>
                        <select name="routes[<?= $i ?>][destination_id]" class="form-control">
                            <option value="">Selecione</option>
                            <?php foreach ($locations as $loc): ?>
                            <option value="<?= (int)$loc['id'] ?>" <?= (int)($route['destination_id'] ?? 0) === (int)$loc['id'] ? 'selected' : '' ?>><?= e($loc['title']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group col-2">
                        <label>Preço Base ($)</label>
                        <input type="number" step="0.01" name="routes[<?= $i ?>][base_price]" class="form-control" value="<?= number_format((float)($route['base_price'] ?? 0), 2, '.', '') ?>">
                    </div>
                    <div class="form-group col-2">
                        <label>Duração (min)</label>
                        <input type="number" name="routes[<?= $i ?>][duration]" class="form-control" value="<?= (int)($route['duration'] ?? 0) ?>">
                    </div>
                </div>

                <?php if (!empty($route['tariffs'])): ?>
                <div class="tariffs-block">
                    <strong>Tarifas por faixa:</strong>
                    <?php foreach ($route['tariffs'] as $j => $tariff): ?>
                    <div class="form-row tariff-row">
                        <div class="form-group col-3">
                            <label>Serviço</label>
                            <select name="routes[<?= $i ?>][tariffs][<?= $j ?>][service_type]" class="form-control">
                                <option value="private" <?= ($tariff['service_type'] ?? '') === 'private' ? 'selected' : '' ?>>Privado</option>
                                <option value="shared" <?= ($tariff['service_type'] ?? '') === 'shared' ? 'selected' : '' ?>>Compartilhado</option>
                            </select>
                        </div>
                        <div class="form-group col-2">
                            <label>Min Pax</label>
                            <input type="number" name="routes[<?= $i ?>][tariffs][<?= $j ?>][min_pax]" class="form-control" value="<?= (int)($tariff['min_pax'] ?? 1) ?>">
                        </div>
                        <div class="form-group col-2">
                            <label>Max Pax</label>
                            <input type="number" name="routes[<?= $i ?>][tariffs][<?= $j ?>][max_pax]" class="form-control" value="<?= (int)($tariff['max_pax'] ?? 10) ?>">
                        </div>
                        <div class="form-group col-3">
                            <label>Preço ($)</label>
                            <input type="number" step="0.01" name="routes[<?= $i ?>][tariffs][<?= $j ?>][price]" class="form-control" value="<?= number_format((float)($tariff['price'] ?? 0), 2, '.', '') ?>">
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                <hr>
            </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="btn btn-sm btn-outline" onclick="addRoute()">+ Adicionar Rota</button>
    </fieldset>
    <?php endif; ?>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Salvar Alterações' : 'Criar Veículo' ?></button>
        <a href="/admin/transfers/veiculos" class="btn btn-outline">Cancelar</a>
    </div>
</form>

<script>
let routeIndex = <?= count($routes ?? []) ?>;
function addRoute() {
    const container = document.getElementById('routesContainer');
    const locOptions = `<?php foreach ($locations as $loc): ?><option value="<?= (int)$loc['id'] ?>"><?= e($loc['title']) ?></option><?php endforeach; ?>`;
    container.insertAdjacentHTML('beforeend', `
    <div class="route-block" data-index="${routeIndex}">
        <div class="form-row">
            <div class="form-group col-4">
                <label>Origem</label>
                <select name="routes[${routeIndex}][origin_id]" class="form-control"><option value="">Selecione</option>${locOptions}</select>
            </div>
            <div class="form-group col-4">
                <label>Destino</label>
                <select name="routes[${routeIndex}][destination_id]" class="form-control"><option value="">Selecione</option>${locOptions}</select>
            </div>
            <div class="form-group col-2">
                <label>Preço Base ($)</label>
                <input type="number" step="0.01" name="routes[${routeIndex}][base_price]" class="form-control" value="0">
            </div>
            <div class="form-group col-2">
                <label>Duração (min)</label>
                <input type="number" name="routes[${routeIndex}][duration]" class="form-control" value="0">
            </div>
        </div>
        <div class="form-row tariff-row">
            <div class="form-group col-3">
                <label>Serviço</label>
                <select name="routes[${routeIndex}][tariffs][0][service_type]" class="form-control"><option value="private">Privado</option><option value="shared">Compartilhado</option></select>
            </div>
            <div class="form-group col-2">
                <label>Min Pax</label>
                <input type="number" name="routes[${routeIndex}][tariffs][0][min_pax]" class="form-control" value="1">
            </div>
            <div class="form-group col-2">
                <label>Max Pax</label>
                <input type="number" name="routes[${routeIndex}][tariffs][0][max_pax]" class="form-control" value="10">
            </div>
            <div class="form-group col-3">
                <label>Preço ($)</label>
                <input type="number" step="0.01" name="routes[${routeIndex}][tariffs][0][price]" class="form-control" value="0">
            </div>
        </div>
        <hr>
    </div>`);
    routeIndex++;
}
</script>
