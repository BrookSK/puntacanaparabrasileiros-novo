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
                <input type="text" name="name" id="pcat-name" class="form-control" placeholder="Ex: Idoso" required>
                <small class="form-hint">Nome exibido no dropdown de passageiros.</small>
            </div>
            <div class="form-group col-2">
                <label>Label de Idade</label>
                <input type="text" name="age_label" id="pcat-age-label" class="form-control" placeholder="Ex: +65 ANOS">
                <small class="form-hint">Texto exibido entre parênteses.</small>
            </div>
            <div class="form-group col-1">
                <label>Idade Mín.</label>
                <input type="number" name="age_min" id="pcat-age-min" class="form-control" value="0" min="0">
            </div>
            <div class="form-group col-1">
                <label>Idade Máx.</label>
                <input type="number" name="age_max" id="pcat-age-max" class="form-control" value="99" min="0">
            </div>
            <div class="form-group col-2">
                <label>Qtd. Inicial</label>
                <input type="number" name="default_quantity" id="pcat-default-qty" class="form-control" value="0" min="0">
                <small class="form-hint">Valor pré-selecionado (0 para a maioria).</small>
            </div>
            <div class="form-group col-2" id="pcat-status-group" style="display:none">
                <label>Status</label>
                <select name="status" id="pcat-status" class="form-control">
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                </select>
            </div>
        </div>
        <div style="display:flex;gap:8px;margin-top:8px;">
            <button type="submit" class="btn btn-primary" id="pcat-submit-btn">Salvar</button>
            <button type="button" class="btn btn-outline" id="pcat-cancel-btn" style="display:none" onclick="cancelEditPcat()">Cancelar</button>
        </div>
    </form>
</div>

<div class="admin-card">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Faixa de Idade</th>
                <th>Qtd. Inicial</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($categories)): ?>
            <tr>
                <td colspan="6" style="text-align:center;padding:30px;color:#94a3b8;">Nenhuma categoria cadastrada. Adicione acima.</td>
            </tr>
            <?php else: ?>
            <?php foreach ($categories as $idx => $cat): ?>
            <tr id="pcat-row-<?= (int)$cat['id'] ?>">
                <td><?= $idx + 1 ?></td>
                <td>
                    <strong><?= e($cat['name']) ?></strong>
                    <?php if (!empty($cat['age_label'])): ?>
                    <span style="color:#64748b;font-size:12px;margin-left:4px">(<?= e($cat['age_label']) ?>)</span>
                    <?php endif; ?>
                </td>
                <td><?= (int)$cat['age_min'] ?> - <?= (int)$cat['age_max'] ?> anos</td>
                <td><?= (int)$cat['default_quantity'] ?></td>
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
    document.getElementById('pcat-age-label').value = data.age_label || '';
    document.getElementById('pcat-age-min').value = data.age_min || 0;
    document.getElementById('pcat-age-max').value = data.age_max || 99;
    document.getElementById('pcat-default-qty').value = data.default_quantity || 0;
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
    document.getElementById('pcat-age-label').value = '';
    document.getElementById('pcat-age-min').value = '0';
    document.getElementById('pcat-age-max').value = '99';
    document.getElementById('pcat-default-qty').value = '0';
    document.getElementById('pcat-status-group').style.display = 'none';
    document.getElementById('pcat-submit-btn').textContent = 'Salvar';
    document.getElementById('pcat-cancel-btn').style.display = 'none';
    document.querySelectorAll('tr.editing').forEach(function(r) { r.classList.remove('editing'); });
}
</script>

<style>
tr.editing { background: rgba(27, 111, 0, 0.08) !important; }
</style>
