<div class="card-header">
    <div class="header-actions">
        <a href="/admin/equipe/criar" class="btn btn-primary">+ Novo Membro</a>
    </div>
    <p style="color:#64748b;font-size:13px;margin:0;">Membros exibidos no carrossel "Nossa equipe" da página Sobre Nós. Apenas os publicados aparecem no site.</p>
</div>

<div class="trips-list">
    <?php if (empty($members)): ?>
    <div class="trip-list-item" style="justify-content:center;padding:40px;">
        <p style="color:#94a3b8;font-size:15px;">Nenhum membro cadastrado ainda.</p>
    </div>
    <?php endif; ?>

    <?php foreach ($members as $m): ?>
    <div class="trip-list-item">
        <div class="trip-list-img">
            <?php if (!empty($m['photo'])): ?>
            <img src="<?= e($m['photo']) ?>" alt="<?= e($m['name']) ?>">
            <?php else: ?>
            <div style="width:100%;height:100%;background:#f1f5f9;display:flex;align-items:center;justify-content:center;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <?php endif; ?>
        </div>
        <div class="trip-list-info">
            <h4 class="trip-list-title"><?= e($m['name']) ?></h4>
            <span class="trip-list-slug"><?= e($m['role'] ?? '—') ?></span>
        </div>
        <div class="trip-list-status">
            <?php if (($m['status'] ?? 'draft') === 'published'): ?>
            <span class="badge badge-success">Publicado</span>
            <?php else: ?>
            <span class="badge badge-warning">Rascunho</span>
            <?php endif; ?>
            <span class="badge badge-info">Ordem: <?= (int)$m['sort_order'] ?></span>
        </div>
        <div class="trip-list-actions">
            <a href="/admin/equipe/<?= (int)$m['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
            <form method="POST" action="/admin/equipe/<?= (int)$m['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Tem certeza que deseja excluir <?= e($m['name']) ?>?')">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-danger">Excluir</button>
            </form>
        </div>
    </div>
    <?php endforeach; ?>
</div>
