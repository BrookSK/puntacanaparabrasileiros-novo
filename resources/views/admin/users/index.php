<div class="card-header">
    <div class="header-actions">
        <a href="/admin/usuarios/criar" class="btn btn-primary">+ Novo Usuário</a>
    </div>
    <form method="GET" class="filter-form">
        <select name="role" class="form-control" onchange="this.form.submit()">
            <option value="">Todos os Perfis</option>
            <option value="superadmin" <?= ($currentRole ?? '') === 'superadmin' ? 'selected' : '' ?>>Superadmin</option>
            <option value="admin" <?= ($currentRole ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            <option value="affiliate" <?= ($currentRole ?? '') === 'affiliate' ? 'selected' : '' ?>>Afiliado</option>
            <option value="customer" <?= ($currentRole ?? '') === 'customer' ? 'selected' : '' ?>>Cliente</option>
        </select>
        <input type="text" name="busca" class="form-control" placeholder="Buscar por nome ou e-mail..." value="<?= e($currentSearch ?? '') ?>">
        <button type="submit" class="btn btn-outline">Filtrar</button>
    </form>
</div>

<table class="table">
    <thead>
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Perfil</th>
            <th>Status</th>
            <th>Cadastro</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($users['items'])): ?>
        <tr>
            <td colspan="6" class="text-center">Nenhum usuário encontrado.</td>
        </tr>
        <?php else: ?>
        <?php foreach ($users['items'] as $user): ?>
        <tr>
            <td><strong><?= e(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?></strong></td>
            <td><?= e($user['email'] ?? '-') ?></td>
            <td>
                <?php
                    $roleColors = ['superadmin' => 'danger', 'admin' => 'info', 'affiliate' => 'warning', 'customer' => 'secondary'];
                    $roleLabels = ['superadmin' => 'Superadmin', 'admin' => 'Admin', 'affiliate' => 'Afiliado', 'customer' => 'Cliente'];
                    $r = $user['role'] ?? 'customer';
                ?>
                <span class="badge badge-<?= $roleColors[$r] ?? 'secondary' ?>"><?= $roleLabels[$r] ?? $r ?></span>
            </td>
            <td>
                <span class="badge badge-<?= ($user['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                    <?= ($user['status'] ?? 'active') === 'active' ? 'Ativo' : 'Inativo' ?>
                </span>
            </td>
            <td><?= !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '-' ?></td>
            <td class="actions-cell">
                <a href="/admin/usuarios/<?= (int)$user['id'] ?>/editar" class="btn btn-sm btn-outline">Editar</a>
                <form method="POST" action="/admin/usuarios/<?= (int)$user['id'] ?>/excluir" class="inline-form" onsubmit="return confirm('Excluir este usuário?')">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-danger">Excluir</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php if (!empty($users['total_pages']) && $users['total_pages'] > 1): ?>
<div class="pagination">
    <?php for ($p = 1; $p <= $users['total_pages']; $p++): ?>
    <a href="?page=<?= $p ?>&role=<?= e($currentRole ?? '') ?>&busca=<?= e($currentSearch ?? '') ?>" class="pagination-btn <?= $p === ($users['current_page'] ?? 1) ? 'active' : '' ?>"><?= $p ?></a>
    <?php endfor; ?>
</div>
<?php endif; ?>
