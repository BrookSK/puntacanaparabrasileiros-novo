<div class="card-header">
    <div class="header-actions">
        <a href="/admin/transfers/veiculos/criar" class="btn btn-primary">+ Novo Veículo</a>
        <a href="/admin/transfers/locais" class="btn btn-outline">Gerenciar Locais</a>
        <a href="/admin/transfers/reservas" class="btn btn-outline">Reservas</a>
    </div>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Imagem</th>
            <th>Título</th>
            <th>Tipo</th>
            <th>Max Passageiros</th>
            <th>Ordem</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($vehicles)): ?>
        <tr>
            <td colspan="7" class="text-center">Nenhum veículo cadastrado.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($vehicles as $vehicle): ?>
        <tr>
            <td>
                <?php if (!empty($vehicle['image'])): ?>
                <img src="<?= e($vehicle['image']) ?>" alt="<?= e($vehicle['title']) ?>" style="width:60px;height:40px;object-fit:cover;border-radius:4px;">
                <?php else: ?>
                <span class="text-muted">-</span>
                <?php endif; ?>
            </td>
            <td><strong><?= e($vehicle['title']) ?></strong></td>
            <td><?= e($vehicle['vehicle_type'] ?? '-') ?></td>
            <td><?= (int)($vehicle['max_passengers'] ?? 0) ?></td>
            <td><?= (int)($vehicle['sort_order'] ?? 0) ?></td>
            <td>
                <span class="badge badge-<?= ($vehicle['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                    <?= e($vehicle['status'] ?? 'active') ?>
                </span>
            </td>
            <td class="actions-cell">
                <a href="/admin/transfers/veiculos/<?= (int)$vehicle['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
