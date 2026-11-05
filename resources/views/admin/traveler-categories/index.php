<div class="card-header">
    <div>
        <h2>Faixas Etárias</h2>
        <p class="text-muted" style="margin:4px 0 0;font-size:13px;">Idades de cada tipo de viajante. Vale para todos os passeios.</p>
    </div>
</div>

<div class="admin-card" style="background:#f8fafc;">
    <p style="margin:0 0 6px;font-size:13px;color:#334155;"><strong>Como usar:</strong> mude o nome ou as idades e clique em <strong>Salvar</strong>.</p>
    <ul style="margin:0;padding-left:18px;font-size:13px;color:#334155;">
        <li>Preencha <strong>de</strong> e <strong>até</strong> para mostrar "0-3 anos".</li>
        <li>Preencha só <strong>de</strong> para mostrar "+12 anos".</li>
        <li>O preço de cada faixa é definido em <strong>Passeios → Preços</strong>. Preço <strong>0</strong> aparece como <strong>Cortesia</strong>, e o cliente consegue incluir a pessoa na reserva sem cobrança.</li>
        <li>Para uma faixa aparecer num passeio, marque-a no card <strong>Pacotes</strong> do passeio.</li>
    </ul>
</div>

<form method="POST" action="/admin/faixas-etarias" class="admin-form">
    <?= csrf_field() ?>
    <div class="admin-card">
        <table class="table">
            <thead>
                <tr>
                    <th>Nome que aparece no site</th>
                    <th style="width:120px;">Idade de</th>
                    <th style="width:120px;">Idade até</th>
                    <th>Como fica no site</th>
                    <th style="width:130px;">Usada em</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($categories)): ?>
                <tr><td colspan="5" class="text-center">Nenhuma faixa cadastrada.</td></tr>
                <?php endif; ?>
                <?php foreach ($categories as $c): $cid = (int) $c['id']; ?>
                <tr class="tc-row">
                    <td><input type="text" name="categories[<?= $cid ?>][name]" value="<?= e($c['name']) ?>" class="form-control tc-name" required></td>
                    <td><input type="number" min="0" max="120" name="categories[<?= $cid ?>][age_min]" value="<?= $c['age_min'] !== null ? (int)$c['age_min'] : '' ?>" class="form-control tc-min"></td>
                    <td><input type="number" min="0" max="120" name="categories[<?= $cid ?>][age_max]" value="<?= $c['age_max'] !== null ? (int)$c['age_max'] : '' ?>" class="form-control tc-max"></td>
                    <td><span class="tc-preview" style="font-weight:600;color:#1B6F00;"></span></td>
                    <td><span class="text-muted" style="font-size:12px;"><?= (int)$c['in_use'] ?> pacote(s)</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="form-actions" style="border-top:none;padding-top:0;">
        <button type="submit" class="btn btn-primary">Salvar</button>
    </div>
</form>

<script>
(function () {
    function build(min, max) {
        if (min !== '' && max !== '') return min + '-' + max + ' anos';
        if (min !== '') return '+' + min + ' anos';
        if (max !== '') return 'até ' + max + ' anos';
        return '';
    }
    function refresh(row) {
        var name = row.querySelector('.tc-name').value;
        var age = build(row.querySelector('.tc-min').value.trim(), row.querySelector('.tc-max').value.trim());
        row.querySelector('.tc-preview').textContent = name + (age ? ' (' + age + ')' : '');
    }
    document.querySelectorAll('.tc-row').forEach(function (row) {
        refresh(row);
        row.addEventListener('input', function () { refresh(row); });
    });
})();
</script>
