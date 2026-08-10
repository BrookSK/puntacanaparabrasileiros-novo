<?php $isEdit = !empty($hotel); ?>

<div class="card-header">
    <h2><?= $isEdit ? 'Editar Hotel' : 'Adicionar Hotel' ?></h2>
    <a href="/admin/passeios/<?= (int) $trip['id'] ?>/editar" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar para <?= e($trip['title']) ?>
    </a>
</div>

<form method="POST" action="<?= $isEdit ? "/admin/passeios/{$trip['id']}/horarios/hotel/{$hotel['id']}/editar" : "/admin/passeios/{$trip['id']}/horarios/hotel/criar" ?>" class="admin-form" style="max-width:720px;">
    <?= csrf_field() ?>

    <!-- Informações do Hotel -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-blue">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg>
            </div>
            <div>
                <h3>Informações do Hotel</h3>
                <p class="admin-card-subtitle">Dados do hotel para este passeio</p>
            </div>
        </div>

        <div class="form-group">
            <label>Nome do Hotel <span class="required">*</span></label>
            <input type="text" name="hotel_name" value="<?= e($hotel['hotel_name'] ?? '') ?>" class="form-control" placeholder="Ex: Hotel Barceló Bávaro Palace" required autocomplete="off">
        </div>

        <div class="form-row">
            <div class="form-group col-6">
                <label>Ordem de Exibição</label>
                <input type="number" name="sort_order" value="<?= (int) ($hotel['sort_order'] ?? 0) ?>" class="form-control" min="0">
            </div>
            <?php if ($isEdit): ?>
            <div class="form-group col-6">
                <label>Status</label>
                <label class="checkbox-label" style="margin-top:6px;">
                    <input type="checkbox" name="is_active" value="1" <?= ($hotel['is_active'] ?? 1) ? 'checked' : '' ?>>
                    Hotel Ativo
                </label>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Horários de Pickup -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div class="admin-card-icon admin-card-icon-orange">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div>
                <h3>Horários de Pickup</h3>
                <p class="admin-card-subtitle">Horários disponíveis para buscar neste hotel</p>
            </div>
        </div>

        <div id="times-list" class="repeater-list">
            <?php if (!empty($schedules)): ?>
                <?php foreach ($schedules as $schedule): ?>
                <div class="repeater-item">
                    <input type="time" name="times[]" value="<?= substr($schedule['pickup_time'], 0, 5) ?>" class="form-control" style="max-width:160px;">
                    <button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="repeater-item">
                    <input type="time" name="times[]" class="form-control" style="max-width:160px;">
                    <button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>
                </div>
            <?php endif; ?>
        </div>

        <button type="button" class="btn btn-sm btn-outline" id="btn-add-time">+ Adicionar Horário</button>
    </div>

    <!-- Ações -->
    <div style="display:flex;gap:12px;margin-top:24px;">
        <button type="submit" class="btn btn-primary btn-lg">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <?= $isEdit ? 'Salvar Alterações' : 'Cadastrar Hotel' ?>
        </button>
        <a href="/admin/passeios/<?= (int) $trip['id'] ?>/editar" class="btn btn-outline">Cancelar</a>
    </div>
</form>

<script>
document.getElementById('btn-add-time').addEventListener('click', function() {
    const list = document.getElementById('times-list');
    const div = document.createElement('div');
    div.className = 'repeater-item';
    div.innerHTML = '<input type="time" name="times[]" class="form-control" style="max-width:160px;"><button type="button" class="btn btn-sm btn-danger repeater-remove">&times;</button>';
    list.appendChild(div);
    div.querySelector('input').focus();
});
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('repeater-remove')) { e.target.closest('.repeater-item').remove(); }
});
</script>
