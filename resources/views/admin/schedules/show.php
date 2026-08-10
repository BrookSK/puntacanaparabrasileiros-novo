<div class="card-header">
    <h2>Horários: <?= e($trip['title']) ?></h2>
    <a href="/admin/horarios" class="btn btn-back">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
        Voltar
    </a>
</div>

<div class="header-actions" style="margin-bottom:24px;display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
    <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/criar" class="btn btn-primary">+ Adicionar Hotel</a>
    <a href="/admin/horarios/<?= (int) $trip['id'] ?>/importar" class="btn btn-outline">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align:-2px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
        Importar Planilha
    </a>
    <?php if (!empty($hotels)): ?>
    <form method="POST" action="/admin/horarios/<?= (int) $trip['id'] ?>/limpar" class="inline-form" style="margin-left:auto;" onsubmit="return confirm('Tem certeza? Isso removerá TODOS os hotéis e horários deste passeio.')">
        <?= csrf_field() ?>
        <button class="btn btn-sm btn-danger">Limpar Tudo</button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($hotels)): ?>
<div class="admin-card" style="text-align:center;padding:60px 20px;">
    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="color:#94a3b8;margin-bottom:16px;">
        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
    </svg>
    <h3 style="margin-bottom:8px;">Nenhum hotel cadastrado</h3>
    <p style="color:#64748b;margin-bottom:24px;">Adicione hotéis manualmente ou importe uma planilha Excel/CSV.</p>
    <div style="display:flex;gap:12px;justify-content:center;">
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/criar" class="btn btn-primary">+ Adicionar Hotel</a>
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/importar" class="btn btn-outline">Importar Planilha</a>
    </div>
</div>
<?php else: ?>
<div class="admin-card" style="padding:16px 20px;margin-bottom:20px;">
    <span style="font-size:13px;color:#64748b;">
        <strong style="color:#334155;"><?= count($hotels) ?></strong> hotel(éis) cadastrado(s) para este passeio
    </span>
</div>

<div class="schedules-grid">
    <?php foreach ($hotels as $hotel): ?>
    <div class="admin-card schedule-hotel-card <?= $hotel['is_active'] ? '' : 'schedule-hotel-card--inactive' ?>">
        <div class="schedule-hotel-card__header">
            <div class="schedule-hotel-card__name">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--primary);flex-shrink:0;">
                    <path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/>
                </svg>
                <strong><?= e($hotel['hotel_name']) ?></strong>
                <?php if (!$hotel['is_active']): ?>
                <span class="badge badge-secondary">Inativo</span>
                <?php endif; ?>
            </div>
            <div class="actions-cell">
                <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/<?= (int) $hotel['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
                <form method="POST" action="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/<?= (int) $hotel['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir hotel e todos os seus horários?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger">&times;</button>
                </form>
            </div>
        </div>
        <div class="schedule-hotel-card__body">
            <?php if (empty($hotel['schedules'])): ?>
            <p style="color:#94a3b8;font-size:13px;margin:0;">Nenhum horário cadastrado.</p>
            <?php else: ?>
            <div class="schedule-chips">
                <?php foreach ($hotel['schedules'] as $schedule): ?>
                <span class="schedule-chip <?= $schedule['is_active'] ? '' : 'schedule-chip--inactive' ?>" <?= !empty($schedule['notes']) ? 'title="' . e($schedule['notes']) . '"' : '' ?>>
                    <?= substr($schedule['pickup_time'], 0, 5) ?>
                </span>
                <?php endforeach; ?>
            </div>
            <span class="schedule-hotel-card__count"><?= count($hotel['schedules']) ?> horário(s)</span>
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
    gap: 16px;
}
.schedule-hotel-card {
    padding: 0 !important;
    overflow: hidden;
}
.schedule-hotel-card--inactive {
    opacity: 0.5;
}
.schedule-hotel-card__header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 14px 18px;
    border-bottom: 1px solid var(--border, #e2e8f0);
    gap: 10px;
}
.schedule-hotel-card__name {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    min-width: 0;
}
.schedule-hotel-card__name strong {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.schedule-hotel-card__body {
    padding: 16px 18px;
}
.schedule-hotel-card__count {
    display: block;
    margin-top: 10px;
    font-size: 12px;
    color: #94a3b8;
}
.schedule-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.schedule-chip {
    display: inline-flex;
    align-items: center;
    padding: 6px 14px;
    background: rgba(14, 165, 233, 0.08);
    color: var(--primary, #0ea5e9);
    border: 1px solid rgba(14, 165, 233, 0.18);
    border-radius: 20px;
    font-size: 13px;
    font-weight: 700;
    font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
    letter-spacing: 0.5px;
}
.schedule-chip--inactive {
    background: #f1f5f9;
    color: #94a3b8;
    border-color: #e2e8f0;
    text-decoration: line-through;
}
</style>
