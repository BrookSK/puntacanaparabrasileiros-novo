<div class="card-header">
    <div class="header-actions">
        <a href="/admin/cupons/criar" class="btn btn-primary">+ Novo Cupom</a>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Desconto</th>
            <th>Vínculo</th>
            <th>Usos</th>
            <th>Validade</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($coupons)): ?>
        <tr><td colspan="7" class="text-center">Nenhum cupom cadastrado.</td></tr>
        <?php else: ?>
        <?php foreach ($coupons as $c): ?>
        <tr>
            <td>
                <strong><?= e($c['code']) ?></strong>
                <?php if (!empty($c['description'])): ?>
                <br><small style="color:#636e72;"><?= e(mb_strimwidth($c['description'], 0, 50, '...')) ?></small>
                <?php endif; ?>
            </td>
            <td>
                <?php if ($c['type'] === 'percentage'): ?>
                    <?= rtrim(rtrim(number_format((float)$c['value'], 2), '0'), '.') ?>%
                <?php else: ?>
                    <?= money((float)$c['value']) ?>
                <?php endif; ?>
                <?php if (!empty($c['min_order'])): ?>
                <br><small style="color:#636e72;">mín. <?= money((float)$c['min_order']) ?></small>
                <?php endif; ?>
            </td>
            <td style="font-size:13px;">
                <?php if (!empty($c['affiliate_id'])): ?>
                    <span class="badge badge-info">Afiliado</span><br>
                    <small style="color:#636e72;"><?= e(trim(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? ''))) ?: '#' . (int)$c['affiliate_id'] ?></small>
                <?php else: ?>
                    <span class="badge badge-secondary">Geral</span>
                <?php endif; ?>
            </td>
            <td style="font-size:13px;">
                <?= (int)$c['used_count'] ?><?= !empty($c['max_uses']) ? ' / ' . (int)$c['max_uses'] : '' ?>
            </td>
            <td style="font-size:12px;">
                <?php if (!empty($c['expires_at'])): ?>
                    até <?= date('d/m/Y', strtotime($c['expires_at'])) ?>
                <?php else: ?>
                    <span style="color:#aaa;">sem prazo</span>
                <?php endif; ?>
            </td>
            <td>
                <?php
                $isExpired = !empty($c['expires_at']) && strtotime($c['expires_at']) < time();
                if ((int)$c['active'] !== 1): ?>
                    <span class="badge badge-secondary">Inativo</span>
                <?php elseif ($isExpired): ?>
                    <span class="badge badge-danger">Expirado</span>
                <?php else: ?>
                    <span class="badge badge-success">Ativo</span>
                <?php endif; ?>
            </td>
            <td class="actions-cell" style="white-space:nowrap;">
                <a href="/admin/cupons/<?= (int)$c['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
                <button type="button" class="btn btn-sm btn-danger" onclick="couponDelete(<?= (int)$c['id'] ?>)">Excluir</button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
function couponDelete(id){
    if (!confirm('Excluir este cupom permanentemente?')) return;
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/cupons/' + id + '/excluir';
    form.innerHTML = '<input name="_token" value="<?= e(csrf_token()) ?>">';
    document.body.appendChild(form);
    form.submit();
}
</script>
