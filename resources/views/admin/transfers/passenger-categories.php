<div class="card-header">
    <div class="header-actions">
        <a href="/admin/transfers/veiculos" class="btn btn-outline">&larr; Veículos</a>
        <a href="/admin/transfers/locais" class="btn btn-outline">Locais</a>
        <a href="/admin/transfers/reservas" class="btn btn-outline">Reservas</a>
    </div>
</div>

<div class="admin-card">
    <h3 id="form-title">Adicionar Categoria de Passageiro</h3>
    <p class="admin-card-subtitle" style="margin-bottom:20px">Defina os tipos de passageiros exibidos no formulário de busca de transfers (ex: Adultos, Crianças, Bebês, Cadeirante).</p>

    <form method="POST" id="pcat-form" action="/admin/transfers/passageiros/criar" class="admin-form">
        <?= csrf_field() ?>
        <div class="form-row">
            <div class="form-group col-3">
                <label>Nome <span class="required">*</span></label>
                <input type="text" name="name" id="pcat-name" class="form-control" placeholder="Ex: Adultos" required>
                <small class="form-hint">Exibido no dropdown de passageiros.</small>
            </div>
            <div class="form-group col-2">
                <label>Campo (field_name) <span class="required">*</span></label>
                <input type="text" name="field_name" id="pcat-field" class="form-control" placeholder="Ex: adults" required>
                <small class="form-hint">Nome interno do campo (sem espaços/acentos).</small>
            </div>
            <div class="form-group col-2">
                <label>Label de Idade</label>
                <input type="text" name="age_label" id="pcat-age-label" class="form-control" placeholder="Ex: +12 ANOS">
                <small class="form-hint">Texto exibido ao lado do nome.</small>
            </div>
            <div class="form-group col-1">
                <label>Idade Mín.</label>
                <input type="number" name="age_min" id="pcat-age-min" class="form-control" value="0" min="0">
            </div>
            <div class="form-group col-1">
                <label>Idade Máx.</label>
                <input type="number" name="age_max" id="pcat-age-max" class="form-control" value="99" min="0">
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-1">
                <label>Qtd. Mín.</label>
                <input type="number" name="min_quantity" id="pcat-min-qty" class="form-control" value="0" min="0">
                <small class="form-hint">Mín. no seletor.</small>
            </div>
            <div class="form-group col-1">
                <label>Qtd. Máx.</label>
                <input type="number" name="max_quantity" id="pcat-max-qty" class="form-control" value="50" min="1">
                <small class="form-hint">Máx. no seletor.</small>
            </div>
            <div class="form-group col-1">
                <label>Qtd. Padrão</label>
                <input type="number" name="default_quantity" id="pcat-default-qty" class="form-control" value="0" min="0">
                <small class="form-hint">Valor inicial.</small>
            </div>
            <div class="form-group col-1">
                <label>Ordem</label>
                <input type="number" name="sort_order" id="pcat-order" class="form-control" value="0">
            </div>
            <div class="form-group col-2" id="pcat-status-group" style="display:none">
                <label>Status</label>
                <select name="status" id="pcat-status" class="form-control">
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                </select>
            </div>
            <div class="form-group col-2" style="display:flex;align-items:flex-end;gap:8px;">
                <button type="submit" class="btn btn-primary" id="pcat-submit-btn">Salvar</button>
                <button type="button" class="btn btn-outline" id="pcat-cancel-btn" style="display:none" onclick="cancelEditPcat()">Cancelar</button>
            </div>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table">
        <thead>
            <tr>
                <th>Ordem</th>
                <th>Nome</th>
                <th>Idade</th>
                <th>Campo</th>
                <th>Qtd. (Mín/Máx/Padrão)</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
            <tr>
                <td colspan="7" style="text-align:center;padding:30px;color:#94a3b8;">Nenhuma categoria cadastrada. Adicione acima.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($categories as $cat): ?>
            <tr id="pcat-row-<?= (int)$cat['id'] ?>">
                <td><?= (int)$cat['sort_order'] ?></td>
                <td>
                    <strong><?= e($cat['name']) ?></strong>
                    <?php if (!empty($cat['age_label'])): ?>
                    <span style="color:#64748b;font-size:12px;margin-left:4px">(<?= e($cat['age_label']) ?>)</span>
                    <?php endif; ?>
                </td>
                <td><?= (int)$cat['age_min'] ?> - <?= (int)$cat['age_max'] ?> anos</td>
                <td><code style="font-size:12px;background:#f1f5f9;padding:2px 6px;border-radius:4px"><?= e($cat['field_name']) ?></code></td>
                <td><?= (int)$cat['min_quantity'] ?> / <?= (int)$cat['max_quantity'] ?> / <?= (int)$cat['default_quantity'] ?></td>
                <td>
                    <span class="badge badge-<?= ($cat['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                        <?= ($cat['status'] ?? 'active') === 'active' ? 'Ativo' : 'Inativo' ?>
                    </span>
                </td>
                <td class="actions-cell">
                    <button type="button" class="btn btn-sm btn-outline" onclick="editPcat(<?= (int)$cat['id'] ?>, <?= e(json_encode($cat)) ?>)">Editar</button>
                    <form method="POST" action="/admin/transfers/passageiros/<?= (int)$cat['id'] ?>/excluir" style="display:inline" onsubmit="return confirm('Excluir esta categoria?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function editPcat(id, data) {
    document.getElementById('form-title').textContent = 'Editar Categoria #' + id;
    document.getElementById('pcat-form').action = '/admin/transfers/passageiros/' + id + '/editar';
    document.getElementById('pcat-name').value = data.name || '';
    document.getElementById('pcat-field').value = data.field_name || '';
    document.getElementById('pcat-age-label').value = data.age_label || '';
    document.getElementById('pcat-age-min').value = data.age_min || 0;
    document.getElementById('pcat-age-max').value = data.age_max || 99;
    document.getElementById('pcat-min-qty').value = data.min_quantity || 0;
    document.getElementById('pcat-max-qty').value = data.max_quantity || 50;
    document.getElementById('pcat-default-qty').value = data.default_quantity || 0;
    document.getElementById('pcat-order').value = data.sort_order || 0;
    document.getElementById('pcat-status').value = data.status || 'active';
    document.getElementById('pcat-status-group').style.display = 'block';
    document.getElementById('pcat-submit-btn').textContent = 'Atualizar';
    document.getElementById('pcat-cancel-btn').style.display = 'inline-block';

    document.querySelectorAll('tr.editing').forEach(function(r) { r.classList.remove('editing'); });
    var row = document.getElementById('pcat-row-' + id);
    if (row) row.classList.add('editing');

    document.getElementById('form-title').scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function cancelEditPcat() {
    document.getElementById('form-title').textContent = 'Adicionar Categoria de Passageiro';
    document.getElementById('pcat-form').action = '/admin/transfers/passageiros/criar';
    document.getElementById('pcat-name').value = '';
    document.getElementById('pcat-field').value = '';
    document.getElementById('pcat-age-label').value = '';
    document.getElementById('pcat-age-min').value = '0';
    document.getElementById('pcat-age-max').value = '99';
    document.getElementById('pcat-min-qty').value = '0';
    document.getElementById('pcat-max-qty').value = '50';
    document.getElementById('pcat-default-qty').value = '0';
    document.getElementById('pcat-order').value = '0';
    document.getElementById('pcat-status-group').style.display = 'none';
    document.getElementById('pcat-submit-btn').textContent = 'Salvar';
    document.getElementById('pcat-cancel-btn').style.display = 'none';
    document.querySelectorAll('tr.editing').forEach(function(r) { r.classList.remove('editing'); });
}
</script>

<style>
tr.editing { background: rgba(27, 111, 0, 0.08) !important; }
</style>
