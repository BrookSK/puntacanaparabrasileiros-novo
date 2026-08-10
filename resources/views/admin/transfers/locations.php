<div class="card-header">
    <div class="header-actions">
        <a href="/admin/transfers/veiculos" class="btn btn-outline">&larr; Veículos</a>
    </div>
</div>

<div class="admin-card">
    <h3 id="form-title">Adicionar Novo Local</h3>
    <form method="POST" id="location-form" action="/admin/transfers/locais/criar" class="admin-form">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-3">
                <label>Título *</label>
                <input type="text" name="title" id="loc-title" class="form-control" required>
            </div>
            <div class="form-group col-2">
                <label>Tipo</label>
                <select name="location_type" id="loc-type" class="form-control">
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
                <input type="text" name="address" id="loc-address" class="form-control">
            </div>
            <div class="form-group col-1">
                <label>Ordem</label>
                <input type="number" name="sort_order" id="loc-order" class="form-control" value="0">
            </div>
            <div class="form-group col-1" id="status-group" style="display:none">
                <label>Status</label>
                <select name="status" id="loc-status" class="form-control">
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
            </div>
            <div class="form-group col-2" style="display:flex;align-items:flex-end;gap:8px;">
                <button type="submit" class="btn btn-primary" id="loc-submit-btn">Salvar Local</button>
                <button type="button" class="btn btn-outline" id="loc-cancel-btn" style="display:none" onclick="cancelEdit()">Cancelar</button>
            </div>
        </div>
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
        <tr id="row-<?= (int)$loc['id'] ?>">
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
                <button type="button" class="btn btn-sm btn-outline" onclick="editLocation(<?= (int)$loc['id'] ?>, '<?= e(addslashes($loc['title'])) ?>', '<?= e(addslashes($loc['address'] ?? '')) ?>', '<?= e($loc['location_type'] ?? 'hotel') ?>', <?= (int)($loc['sort_order'] ?? 0) ?>, <?= (int)($loc['status'] ?? 1) ?>)">Editar</button>
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

<script>
function editLocation(id, title, address, type, order, status) {
    // Mudar o formulário para modo edição
    document.getElementById('form-title').textContent = 'Editar Local #' + id;
    document.getElementById('location-form').action = '/admin/transfers/locais/' + id + '/editar';
    document.getElementById('loc-title').value = title;
    document.getElementById('loc-address').value = address;
    document.getElementById('loc-type').value = type;
    document.getElementById('loc-order').value = order;
    document.getElementById('loc-status').value = status;
    document.getElementById('status-group').style.display = 'block';
    document.getElementById('loc-submit-btn').textContent = 'Atualizar';
    document.getElementById('loc-cancel-btn').style.display = 'inline-block';

    // Destacar a linha sendo editada
    document.querySelectorAll('tr.editing').forEach(function(r) { r.classList.remove('editing'); });
    var row = document.getElementById('row-' + id);
    if (row) row.classList.add('editing');

    // Scroll para o form
    document.getElementById('form-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function cancelEdit() {
    document.getElementById('form-title').textContent = 'Adicionar Novo Local';
    document.getElementById('location-form').action = '/admin/transfers/locais/criar';
    document.getElementById('loc-title').value = '';
    document.getElementById('loc-address').value = '';
    document.getElementById('loc-type').value = 'airport';
    document.getElementById('loc-order').value = '0';
    document.getElementById('status-group').style.display = 'none';
    document.getElementById('loc-submit-btn').textContent = 'Salvar Local';
    document.getElementById('loc-cancel-btn').style.display = 'none';
    document.querySelectorAll('tr.editing').forEach(function(r) { r.classList.remove('editing'); });
}
</script>

<style>
tr.editing { background: rgba(27, 111, 0, 0.08) !important; }
</style>
