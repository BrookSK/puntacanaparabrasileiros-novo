<!-- Stats -->
<div class="stats-grid" style="margin-bottom:24px">
    <div class="stat-card">
        <div class="stat-value"><?= (int)$activeCount ?></div>
        <div class="stat-label">Inscritos Ativos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int)$totalCount ?></div>
        <div class="stat-label">Total de Inscritos</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= (int)$totalCount - (int)$activeCount ?></div>
        <div class="stat-label">Cancelados</div>
    </div>
</div>

<!-- Ações -->
<div class="card-header" style="margin-bottom:20px">
    <div class="header-actions">
        <a href="/admin/newsletter/campanhas" class="btn btn-primary">Criar Campanha</a>
        <a href="/admin/newsletter/exportar" class="btn btn-outline">Exportar CSV</a>
    </div>
    <form method="GET" class="filter-form">
        <select name="status" class="form-control form-control-sm">
            <option value="">Todos</option>
            <option value="active" <?= ($currentStatus ?? '') === 'active' ? 'selected' : '' ?>>Ativos</option>
            <option value="unsubscribed" <?= ($currentStatus ?? '') === 'unsubscribed' ? 'selected' : '' ?>>Cancelados</option>
        </select>
        <button type="submit" class="btn btn-sm btn-outline">Filtrar</button>
    </form>
</div>

<!-- Tabela -->
<table class="table">
    <thead>
        <tr>
            <th>Email</th>
            <th>Nome</th>
            <th>Origem</th>
            <th>Status</th>
            <th>Data</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
    <?php if (empty($subscribers['items'])): ?>
    <tr><td colspan="6" style="text-align:center;color:#999;padding:30px">Nenhum inscrito ainda.</td></tr>
    <?php else: ?>
    <?php foreach ($subscribers['items'] as $sub): ?>
    <tr>
        <td><strong><?= e($sub['email']) ?></strong></td>
        <td><?= e($sub['name'] ?? '-') ?></td>
        <td><span class="badge badge-secondary"><?= e($sub['source'] ?? 'blog') ?></span></td>
        <td>
            <?php if ($sub['status'] === 'active'): ?>
            <span class="badge badge-success">Ativo</span>
            <?php elseif ($sub['status'] === 'unsubscribed'): ?>
            <span class="badge badge-warning">Cancelado</span>
            <?php else: ?>
            <span class="badge badge-danger"><?= e($sub['status']) ?></span>
            <?php endif; ?>
        </td>
        <td><?= format_date($sub['subscribed_at']) ?></td>
        <td>
            <form method="POST" action="/admin/newsletter/<?= (int)$sub['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Remover este inscrito?')">
                <?= csrf_field() ?>
                <button class="btn btn-sm btn-danger">Remover</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php if (($subscribers['total_pages'] ?? 1) > 1): ?>
<nav class="pagination">
    <?php for ($i = 1; $i <= $subscribers['total_pages']; $i++): ?>
    <a href="?page=<?= $i ?>&status=<?= e($currentStatus ?? '') ?>" class="page-link <?= $i === $subscribers['current_page'] ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
</nav>
<?php endif; ?>
