<div class="card-header">
    <div class="header-actions">
        <a href="/admin/horarios" class="btn btn-outline">&larr; Voltar</a>
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/criar" class="btn btn-primary">+ Adicionar Hotel</a>
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/importar" class="btn btn-success">Importar Planilha</a>
        <?php if (!empty($hotels)): ?>
        <form method="POST" action="/admin/horarios/<?= (int) $trip['id'] ?>/limpar" class="inline-form" onsubmit="return confirm('Tem certeza? Isso removerá TODOS os hotéis e horários deste passeio.')">
            <?= csrf_field() ?>
            <button class="btn btn-sm btn-danger">Limpar Tudo</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<div class="schedules-info">
    <p class="text-muted">
        Passeio: <strong><?= e($trip['title']) ?></strong> &mdash;
        <?= count($hotels) ?> hotel(éis) cadastrado(s)
    </p>
</div>

<?php if (empty($hotels)): ?>
<div class="empty-state">
    <div class="empty-state-icon">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
            <line x1="12" y1="8" x2="12" y2="16"/>
            <line x1="8" y1="12" x2="16" y2="12"/>
        </svg>
    </div>
    <h3>Nenhum hotel cadastrado</h3>
    <p>Adicione hotéis manualmente ou importe uma planilha Excel/CSV.</p>
    <div class="empty-state-actions">
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/criar" class="btn btn-primary">Adicionar Hotel</a>
        <a href="/admin/horarios/<?= (int) $trip['id'] ?>/importar" class="btn btn-outline">Importar Planilha</a>
    </div>
</div>
<?php else: ?>
<div class="hotels-schedules-grid">
    <?php foreach ($hotels as $hotel): ?>
    <div class="hotel-schedule-card <?= $hotel['is_active'] ? '' : 'card-inactive' ?>">
        <div class="hotel-card-header">
            <h4 class="hotel-card-title">
                <?= e($hotel['hotel_name']) ?>
                <?php if (!$hotel['is_active']): ?>
                <span class="badge badge-secondary">Inativo</span>
                <?php endif; ?>
            </h4>
            <div class="hotel-card-actions">
                <a href="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/<?= (int) $hotel['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
                <form method="POST" action="/admin/horarios/<?= (int) $trip['id'] ?>/hotel/<?= (int) $hotel['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir hotel e todos os seus horários?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </div>
        </div>
        <div class="hotel-card-body">
            <?php if (empty($hotel['schedules'])): ?>
            <p class="text-muted">Nenhum horário cadastrado.</p>
            <?php else: ?>
            <div class="schedule-times">
                <?php foreach ($hotel['schedules'] as $schedule): ?>
                <span class="schedule-time-badge <?= $schedule['is_active'] ? '' : 'time-inactive' ?>">
                    <?= substr($schedule['pickup_time'], 0, 5) ?>
                    <?php if (!empty($schedule['notes'])): ?>
                    <small title="<?= e($schedule['notes']) ?>">*</small>
                    <?php endif; ?>
                </span>
                <?php endforeach; ?>
            </div>
            <p class="schedules-count"><?= count($hotel['schedules']) ?> horário(s)</p>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<style>
.schedules-info {
    margin-bottom: 1.5rem;
}
.hotels-schedules-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 1.25rem;
}
.hotel-schedule-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    overflow: hidden;
    transition: box-shadow 0.2s;
}
.hotel-schedule-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.08);
}
.hotel-schedule-card.card-inactive {
    opacity: 0.6;
}
.hotel-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #f1f5f9;
    background: #f8fafc;
}
.hotel-card-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 600;
}
.hotel-card-actions {
    display: flex;
    gap: 0.5rem;
}
.hotel-card-body {
    padding: 1rem 1.25rem;
}
.schedule-times {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}
.schedule-time-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.375rem 0.75rem;
    background: #e0f2fe;
    color: #0369a1;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    font-family: 'JetBrains Mono', monospace;
}
.schedule-time-badge.time-inactive {
    background: #f1f5f9;
    color: #94a3b8;
    text-decoration: line-through;
}
.schedules-count {
    margin: 0;
    font-size: 0.8rem;
    color: #64748b;
}
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}
.empty-state-icon {
    color: #94a3b8;
    margin-bottom: 1rem;
}
.empty-state h3 {
    margin-bottom: 0.5rem;
    color: #334155;
}
.empty-state p {
    color: #64748b;
    margin-bottom: 1.5rem;
}
.empty-state-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: center;
}
</style>
