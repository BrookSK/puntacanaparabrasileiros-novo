<?php
$statusColors = ['pending' => 'warning', 'confirmed' => 'success', 'completed' => 'info', 'cancelled' => 'danger'];
$statusLabels = ['pending' => 'Pendente', 'confirmed' => 'Confirmada', 'completed' => 'Concluída', 'cancelled' => 'Cancelada'];
?>

<?php if (empty($moduleEnabled)): ?>
<div class="alert alert-warning" style="margin-bottom:18px;padding:12px 16px;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;color:#92400e;font-size:13.5px;">
    O módulo de agendamento está <strong>desativado</strong>. O botão "Agendar Chamada de Vídeo" não aparece na página dos passeios.
    Ative em <a href="/admin/configuracoes" style="color:#92400e;text-decoration:underline;">Configurações → Agendamento</a>.
</div>
<?php endif; ?>

<div class="card-header">
    <div class="header-actions" style="display:flex;gap:8px;flex-wrap:wrap;">
        <a href="/admin/agendamentos" class="btn btn-sm <?= empty($currentStatus) ? 'btn-primary' : 'btn-outline' ?>">Todas (<?= (int)($counts['all'] ?? 0) ?>)</a>
        <a href="/admin/agendamentos?status=pending" class="btn btn-sm <?= ($currentStatus ?? '') === 'pending' ? 'btn-primary' : 'btn-outline' ?>">Pendentes (<?= (int)($counts['pending'] ?? 0) ?>)</a>
        <a href="/admin/agendamentos?status=confirmed" class="btn btn-sm <?= ($currentStatus ?? '') === 'confirmed' ? 'btn-primary' : 'btn-outline' ?>">Confirmadas (<?= (int)($counts['confirmed'] ?? 0) ?>)</a>
        <a href="/admin/agendamentos?status=completed" class="btn btn-sm <?= ($currentStatus ?? '') === 'completed' ? 'btn-primary' : 'btn-outline' ?>">Concluídas (<?= (int)($counts['completed'] ?? 0) ?>)</a>
        <a href="/admin/agendamentos?status=cancelled" class="btn btn-sm <?= ($currentStatus ?? '') === 'cancelled' ? 'btn-primary' : 'btn-outline' ?>">Canceladas (<?= (int)($counts['cancelled'] ?? 0) ?>)</a>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>#</th>
            <th>Cliente</th>
            <th>Contato</th>
            <th>Passeio</th>
            <th>Data / Hora</th>
            <th>Reunião</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($bookings['items'])): ?>
        <tr><td colspan="8" class="text-center">Nenhum agendamento encontrado.</td></tr>
        <?php else: ?>
        <?php foreach ($bookings['items'] as $b): ?>
        <?php $st = $b['status'] ?? 'pending'; ?>
        <tr>
            <td><strong>#<?= (int)$b['id'] ?></strong></td>
            <td>
                <span style="font-size:13px;"><?= e($b['customer_name'] ?? '') ?></span>
                <?php if (!empty($b['notes'])): ?>
                <br><small style="color:#636e72;" title="<?= e($b['notes']) ?>"><?= e(mb_strimwidth($b['notes'], 0, 40, '...')) ?></small>
                <?php endif; ?>
            </td>
            <td style="font-size:12px;">
                <?= e($b['phone'] ?? '') ?><br>
                <small style="color:#636e72;"><?= e($b['email'] ?? '') ?></small>
            </td>
            <td style="font-size:12px;"><?= e($b['trip_title'] ?? '—') ?></td>
            <td style="font-size:12px;"><strong><?= !empty($b['scheduled_at']) ? date('d/m/Y', strtotime($b['scheduled_at'])) : '-' ?></strong><br><?= !empty($b['scheduled_at']) ? date('H:i', strtotime($b['scheduled_at'])) : '' ?></td>
            <td>
                <?php if (!empty($b['meeting_link'])): ?>
                <a href="<?= e($b['meeting_link']) ?>" target="_blank" rel="noopener" class="btn btn-sm btn-outline">Abrir</a>
                <?php else: ?>
                <span style="color:#aaa;">—</span>
                <?php endif; ?>
            </td>
            <td>
                <span class="badge badge-<?= $statusColors[$st] ?? 'secondary' ?>"><?= $statusLabels[$st] ?? $st ?></span>
                <?php if (!empty($b['admin_notes']) && $st === 'cancelled'): ?>
                <br><small style="color:#636e72;" title="<?= e($b['admin_notes']) ?>"><?= e(mb_strimwidth($b['admin_notes'], 0, 30, '...')) ?></small>
                <?php endif; ?>
            </td>
            <td class="actions-cell" style="white-space:nowrap;">
                <?php if ($st === 'pending'): ?>
                <button type="button" class="btn btn-sm btn-primary" onclick="vcStatus(<?= (int)$b['id'] ?>, 'confirmed')">Confirmar</button>
                <?php endif; ?>
                <?php if (in_array($st, ['pending', 'confirmed'], true)): ?>
                <button type="button" class="btn btn-sm btn-success" onclick="vcStatus(<?= (int)$b['id'] ?>, 'completed')">Concluir</button>
                <button type="button" class="btn btn-sm btn-outline" onclick="vcCancel(<?= (int)$b['id'] ?>)">Cancelar</button>
                <?php endif; ?>
                <button type="button" class="btn btn-sm btn-danger" onclick="vcDelete(<?= (int)$b['id'] ?>)">Excluir</button>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($bookings['total_pages']) && $bookings['total_pages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $bookings['total_pages']; $p++): ?>
    <a href="?page=<?= $p ?>&status=<?= e($currentStatus ?? '') ?>" class="pagination-btn <?= $p === ($bookings['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>

<script>
var VC_CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '<?= e(csrf_token()) ?>';

function vcSubmit(action, fields){
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = action;
    var t = document.createElement('input'); t.name = '_token'; t.value = VC_CSRF; form.appendChild(t);
    for (var k in fields){
        var i = document.createElement('input'); i.name = k; i.value = fields[k]; form.appendChild(i);
    }
    document.body.appendChild(form);
    form.submit();
}

function vcStatus(id, status){
    var labels = {confirmed:'confirmar', completed:'concluir'};
    if (!confirm('Deseja ' + (labels[status] || status) + ' este agendamento?')) return;
    vcSubmit('/admin/agendamentos/' + id + '/status', {status: status});
}

function vcCancel(id){
    var reason = prompt('Motivo do cancelamento (será enviado ao cliente):', '');
    if (reason === null) return;
    vcSubmit('/admin/agendamentos/' + id + '/status', {status: 'cancelled', admin_notes: reason});
}

function vcDelete(id){
    if (!confirm('Excluir permanentemente este agendamento?')) return;
    vcSubmit('/admin/agendamentos/' + id + '/excluir', {});
}
</script>
