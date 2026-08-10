<div class="card-header">
    <div class="header-actions">
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-outline">&larr; Voltar</a>
    </div>
</div>

<div class="form-container">
    <form method="POST" action="<?= $hotel ? "/admin/horarios/{$trip['id']}/hotel/{$hotel['id']}/editar" : "/admin/horarios/{$trip['id']}/hotel/criar" ?>">
        <?= csrf_field() ?>

        <div class="form-section">
            <h3 class="form-section-title">Informações do Hotel</h3>

            <div class="form-group">
                <label for="hotel_name" class="form-label">Nome do Hotel *</label>
                <input type="text" 
                       id="hotel_name" 
                       name="hotel_name" 
                       value="<?= e($hotel['hotel_name'] ?? '') ?>" 
                       class="form-control" 
                       placeholder="Ex: Hotel Barceló Bávaro Palace"
                       required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="sort_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           value="<?= (int) ($hotel['sort_order'] ?? 0) ?>" 
                           class="form-control" 
                           min="0">
                    <small class="form-help">Menor número aparece primeiro.</small>
                </div>

                <?php if ($hotel): ?>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" <?= ($hotel['is_active'] ?? 1) ? 'checked' : '' ?>>
                        Ativo
                    </label>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-section">
            <h3 class="form-section-title">Horários de Pickup</h3>
            <p class="form-section-description">Adicione os horários disponíveis para este hotel neste passeio.</p>

            <div id="times-container">
                <?php if (!empty($schedules)): ?>
                    <?php foreach ($schedules as $i => $schedule): ?>
                    <div class="time-row">
                        <input type="time" 
                               name="times[]" 
                               value="<?= substr($schedule['pickup_time'], 0, 5) ?>" 
                               class="form-control time-input">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-time" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="time-row">
                        <input type="time" name="times[]" class="form-control time-input" placeholder="HH:MM">
                        <button type="button" class="btn btn-sm btn-danger btn-remove-time" onclick="this.parentElement.remove()">&times;</button>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-sm btn-outline" id="btn-add-time">+ Adicionar Horário</button>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn btn-primary">
                <?= $hotel ? 'Salvar Alterações' : 'Cadastrar Hotel' ?>
            </button>
            <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

<style>
.form-container {
    max-width: 700px;
}
.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
}
.form-section-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: #1e293b;
}
.form-section-description {
    font-size: 0.875rem;
    color: #64748b;
    margin-bottom: 1rem;
}
.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.time-row {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}
.time-input {
    max-width: 160px;
}
.btn-remove-time {
    padding: 0.25rem 0.5rem;
    font-size: 1.1rem;
    line-height: 1;
}
.form-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 2rem;
}
#btn-add-time {
    margin-top: 0.5rem;
}
</style>

<script>
document.getElementById('btn-add-time').addEventListener('click', function() {
    const container = document.getElementById('times-container');
    const row = document.createElement('div');
    row.className = 'time-row';
    row.innerHTML = `
        <input type="time" name="times[]" class="form-control time-input" placeholder="HH:MM">
        <button type="button" class="btn btn-sm btn-danger btn-remove-time" onclick="this.parentElement.remove()">&times;</button>
    `;
    container.appendChild(row);
    row.querySelector('input').focus();
});
</script>
