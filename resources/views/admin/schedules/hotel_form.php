<div class="card-header">
    <div class="header-actions">
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-outline">&larr; Voltar</a>
    </div>
</div>

<div style="max-width:680px;">
    <form method="POST" action="<?= $hotel ? "/admin/horarios/{$trip['id']}/hotel/{$hotel['id']}/editar" : "/admin/horarios/{$trip['id']}/hotel/criar" ?>">
        <?= csrf_field() ?>

        <!-- Dados do Hotel -->
        <div style="margin-bottom:2rem;padding:1.25rem;background:var(--bg-card, #1e293b);border-radius:10px;border:1px solid var(--border, rgba(255,255,255,0.06));">
            <h3 style="margin:0 0 1.25rem;font-size:1rem;color:var(--text-primary, #f1f5f9);display:flex;align-items:center;gap:0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.7;">
                    <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                </svg>
                Informações do Hotel
            </h3>

            <div style="margin-bottom:1rem;">
                <label for="hotel_name" class="form-label">Nome do Hotel *</label>
                <input type="text" 
                       id="hotel_name" 
                       name="hotel_name" 
                       value="<?= e($hotel['hotel_name'] ?? '') ?>" 
                       class="form-control" 
                       placeholder="Ex: Hotel Barceló Bávaro Palace"
                       required
                       autocomplete="off">
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;">
                <div>
                    <label for="sort_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" 
                           id="sort_order" 
                           name="sort_order" 
                           value="<?= (int) ($hotel['sort_order'] ?? 0) ?>" 
                           class="form-control" 
                           min="0">
                    <small style="font-size:0.75rem;color:var(--text-muted, #94a3b8);">Menor número = aparece primeiro</small>
                </div>

                <?php if ($hotel): ?>
                <div>
                    <label class="form-label">Status</label>
                    <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;padding-top:0.5rem;">
                        <input type="checkbox" name="is_active" value="1" <?= ($hotel['is_active'] ?? 1) ? 'checked' : '' ?>>
                        <span style="color:var(--text-primary, #f1f5f9);font-size:0.9rem;">Ativo</span>
                    </label>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Horários -->
        <div style="padding:1.25rem;background:var(--bg-card, #1e293b);border-radius:10px;border:1px solid var(--border, rgba(255,255,255,0.06));">
            <h3 style="margin:0 0 0.5rem;font-size:1rem;color:var(--text-primary, #f1f5f9);display:flex;align-items:center;gap:0.5rem;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.7;">
                    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                </svg>
                Horários de Pickup
            </h3>
            <p style="margin:0 0 1.25rem;font-size:0.82rem;color:var(--text-muted, #94a3b8);">Adicione os horários disponíveis para este hotel.</p>

            <div id="times-container">
                <?php if (!empty($schedules)): ?>
                    <?php foreach ($schedules as $schedule): ?>
                    <div class="time-row">
                        <div class="time-row__input">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0ea5e9;flex-shrink:0;">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <input type="time" name="times[]" value="<?= substr($schedule['pickup_time'], 0, 5) ?>" class="form-control" style="max-width:140px;">
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.time-row').remove()">&times;</button>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="time-row">
                        <div class="time-row__input">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0ea5e9;flex-shrink:0;">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            <input type="time" name="times[]" class="form-control" style="max-width:140px;">
                        </div>
                        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.time-row').remove()">&times;</button>
                    </div>
                <?php endif; ?>
            </div>

            <button type="button" class="btn btn-sm btn-outline" id="btn-add-time" style="margin-top:0.75rem;">
                + Adicionar Horário
            </button>
        </div>

        <!-- Ações -->
        <div style="display:flex;gap:0.75rem;margin-top:1.5rem;">
            <button type="submit" class="btn btn-primary">
                <?= $hotel ? 'Salvar Alterações' : 'Cadastrar Hotel' ?>
            </button>
            <a href="/admin/horarios/<?= (int) $trip['id'] ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>

<style>
.time-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    margin-bottom: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: rgba(255,255,255,0.02);
    border: 1px solid var(--border, rgba(255,255,255,0.06));
    border-radius: 8px;
    transition: border-color 0.15s;
}
.time-row:hover {
    border-color: rgba(14, 165, 233, 0.3);
}
.time-row__input {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>

<script>
document.getElementById('btn-add-time').addEventListener('click', function() {
    const container = document.getElementById('times-container');
    const row = document.createElement('div');
    row.className = 'time-row';
    row.innerHTML = `
        <div class="time-row__input">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:#0ea5e9;flex-shrink:0;">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            <input type="time" name="times[]" class="form-control" style="max-width:140px;">
        </div>
        <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('.time-row').remove()">&times;</button>
    `;
    container.appendChild(row);
    row.querySelector('input').focus();
});
</script>
