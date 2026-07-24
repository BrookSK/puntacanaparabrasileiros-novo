<div class="card-header">
    <div class="header-actions">
        <a href="/admin/transfers/veiculos" class="btn btn-outline">&larr; Veículos</a>
    </div>
</div>

<div class="admin-card">
    <h3>Adicionar Novo Local</h3>
    <form method="POST" action="/admin/transfers/locais/criar" class="admin-form">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-4">
                <label>Título *</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group col-3">
                <label>Tipo</label>
                <select name="location_type" class="form-control">
                    <option value="airport">Aeroporto</option>
                    <option value="hotel">Hotel</option>
                    <option value="zone">Zona</option>
                    <option value="other">Outro</option>
                </select>
            </div>
            <div class="form-group col-3">
                <label>Endereço</label>
                <input type="text" name="address" class="form-control">
            </div>
            <div class="form-group col-2">
                <label>Ordem</label>
                <input type="number" name="sort_order" class="form-control" value="0">
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Local</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Título</th>
            <th>Tipo</th>
            <th>Endereço</th>
            <th>Ordem</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($locations)): ?>
        <tr>
            <td colspan="6" class="text-center">Nenhum local cadastrado.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($locations as $loc): ?>
        <tr>
            <td><?= (int)$loc['id'] ?></td>
            <td><strong><?= e($loc['title']) ?></strong></td>
            <td><?= e($loc['location_type'] ?? '-') ?></td>
            <td><?= e($loc['address'] ?? '-') ?></td>
            <td><?= (int)($loc['sort_order'] ?? 0) ?></td>
            <td>
                <span class="badge badge-<?= (int)($loc['status'] ?? 1) === 1 ? 'success' : 'secondary' ?>">
                    <?= (int)($loc['status'] ?? 1) === 1 ? 'Ativo' : 'Inativo' ?>
                </span>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
