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
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($locations)): ?>
        <tr>
            <td colspan="7" class="text-center">Nenhum local cadastrado.</td>
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
            <td class="actions-cell">
                <button type="button" class="btn btn-sm btn-outline" onclick="editLocation(<?= (int)$loc['id'] ?>, '<?= e(addslashes($loc['title'])) ?>', '<?= e($loc['address'] ?? '') ?>', '<?= e($loc['location_type'] ?? 'hotel') ?>', <?= (int)($loc['sort_order'] ?? 0) ?>, <?= (int)($loc['status'] ?? 1) ?>)">Editar</button>
                <form method="POST" action="/admin/transfers/locais/<?= (int)$loc['id'] ?>/excluir" style="display:inline" onsubmit="return confirm('Excluir este local?')">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- Modal de Edição -->
<div id="edit-modal" class="admin-modal" style="display:none">
    <div class="admin-modal-content">
        <h3>Editar Local</h3>
        <form method="POST" id="edit-form" class="admin-form">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="PUT">
            <div class="form-row">
                <div class="form-group col-4">
                    <label>Título *</label>
                    <input type="text" name="title" id="edit-title" class="form-control" required>
                </div>
                <div class="form-group col-3">
                    <label>Tipo</label>
                    <select name="location_type" id="edit-type" class="form-control">
                        <option value="airport">Aeroporto</option>
                        <option value="hotel">Hotel</option>
                        <option value="city">Cidade</option>
                        <option value="resort">Resort</option>
                        <option value="zone">Zona</option>
                        <option value="other">Outro</option>
                    </select>
                </div>
                <div class="form-group col-3">
                    <label>Endereço</label>
                    <input type="text" name="address" id="edit-address" class="form-control">
                </div>
                <div class="form-group col-1">
                    <label>Ordem</label>
                    <input type="number" name="sort_order" id="edit-order" class="form-control">
                </div>
                <div class="form-group col-1">
                    <label>Status</label>
                    <select name="status" id="edit-status" class="form-control">
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-outline" onclick="document.getElementById('edit-modal').style.display='none'">Cancelar</button>
            </div>
        </form>
    </div>
</div>

<script>
function editLocation(id, title, address, type, order, status) {
    document.getElementById('edit-form').action = '/admin/transfers/locais/' + id + '/editar';
    document.getElementById('edit-title').value = title;
    document.getElementById('edit-address').value = address;
    document.getElementById('edit-type').value = type;
    document.getElementById('edit-order').value = order;
    document.getElementById('edit-status').value = status;
    document.getElementById('edit-modal').style.display = 'flex';
}
</script>
