<div class="card-header">
    <div class="header-actions">
        <a href="/admin/horarios" class="btn btn-outline">&larr; Voltar</a>
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/criar" class="btn btn-primary">+ Adicionar Hotel</a>
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/importar" class="btn btn-success">Importar Planilha</a>
    </div>
</div>

<div style="margin-bottom:1.5rem;padding:1rem 1.25rem;background:var(--bg-card, #1e293b);border-radius:8px;border:1px solid var(--border, rgba(255,255,255,0.06));">
    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:0.75rem;">
        <div>
            <h3 style="margin:0 0 0.25rem;font-size:1.1rem;color:var(--text-primary, #f1f5f9);"><?= e($trip['title']) ?></h3>
            <span style="font-size:0.85rem;color:var(--text-muted, #94a3b8);"><?= count($hotels) ?> hotel(éis) cadastrado(s)</span>
        </div>
        <?php if (!empty($hotels)): ?>
        <form method="POST" action="/admin/horarios/<?= (int) $trip['id'] ?>/limpar" class="inline-form" onsubmit="return confirm('Tem certeza? Isso removerá TODOS os hotéis e horários deste passeio.')">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-danger">Limpar Tudo</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<?php if (empty($hotels)): ?>
<div style="text-align:center;padding:4rem 1rem;">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:var(--text-muted, #94a3b8);margin-bottom:1rem;">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
    </svg>
    <h3 style="margin-bottom:0.5rem;color:var(--text-primary, #f1f5f9);">Nenhum hotel cadastrado</h3>
    <p style="color:var(--text-muted, #94a3b8);margin-bottom:1.5rem;">Adicione hotéis manualmente ou importe uma planilha Excel/CSV.</p>
    <div style="display:flex;gap:0.75rem;justify-content:center;">
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/criar" class="btn btn-primary">Adicionar Hotel</a>
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/importar" class="btn btn-outline">Importar Planilha</a>
    </div>
</div>
<?php else: ?>

<div class="schedules-grid">
    <?php foreach ($hotels as $hotel): ?>
    <div class="schedule-card <?= $hotel['is_active'] ? '' : 'schedule-card--inactive' ?>">
        <div class="schedule-card__header">
            <div class="schedule-card__title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;opacity:0.6;">
                    <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                </svg>
                <span><?= e($hotel['hotel_name']) ?></span>
                <?php if (!$hotel['is_active']): ?>
                <span class="badge badge-secondary" style="font-size:0.7rem;">Inativo</span>
                <?php endif; ?>
            </div>
            <div class="schedule-card__actions">
                <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/<?= (int) $hotel['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
                <form method="POST" action="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/<?= (int) $hotel['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir hotel e todos os seus horários?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </div>
        </div>
        <div class="schedule-card__body">
            <?php if (empty($hotel['schedules'])): ?>
            <p style="color:var(--text-muted, #94a3b8);font-size:0.85rem;margin:0;">Nenhum horário cadastrado.</p>
            <?php else: ?>
            <div class="schedule-times">
                <?php foreach ($hotel['schedules'] as $schedule): ?>
                <span class="schedule-time <?= $schedule['is_active'] ? '' : 'schedule-time--inactive' ?>" <?= !empty($schedule['notes']) ? 'title="' . e($schedule['notes']) . '"' : '' ?>>
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" style="opacity:0.7;">
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    </svg>
                    <?= substr($schedule['pickup_time'], 0, 5) ?>
                </span>
                <?php endforeach; ?>
            </div>
            <div class="schedule-card__footer">
                <span style="font-size:0.8rem;color:var(--text-muted, #94a3b8);"><?= count($hotel['schedules']) ?> horário(s) disponíveis</span>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
.schedules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 1rem;
}
.schedule-card {
    background: var(--bg-card, #1e293b);
    border: 1px solid var(--border, rgba(255,255,255,0.06));
    border-radius: 10px;
    overflow: hidden;
    transition: border-color 0.2s, transform 0.15s;
}
.schedule-card:hover {
    border-color: var(--primary, #0ea5e9);
    transform: translateY(-1px);
}
.schedule-card--inactive {
    opacity: 0.55;
}
.schedule-card__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.875rem 1.125rem;
    border-bottom: 1px solid var(--border, rgba(255,255,255,0.06));
    gap: 0.75rem;
}
.schedule-card__title {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--text-primary, #f1f5f9);
    min-width: 0;
}
.schedule-card__title > span:first-of-type {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.schedule-card__actions {
    display: flex;
    gap: 0.375rem;
    flex-shrink: 0;
}
.schedule-card__body {
    padding: 1rem 1.125rem;
}
.schedule-card__footer {
    margin-top: 0.75rem;
    padding-top: 0.625rem;
    border-top: 1px solid var(--border, rgba(255,255,255,0.04));
}
.schedule-times {
    display: flex;
    flex-wrap: wrap;
    gap: 0.4rem;
}
.schedule-time {
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.35rem 0.7rem;
    background: rgba(14, 165, 233, 0.12);
    color: #38bdf8;
    border: 1px solid rgba(14, 165, 233, 0.2);
    border-radius: 6px;
    font-size: 0.82rem;
    font-weight: 600;
    font-family: 'JetBrains Mono', 'Fira Code', monospace;
    letter-spacing: 0.02em;
}
.schedule-time--inactive {
    background: rgba(148, 163, 184, 0.08);
    color: #64748b;
    border-color: rgba(148, 163, 184, 0.15);
    text-decoration: line-through;
}
</style>
