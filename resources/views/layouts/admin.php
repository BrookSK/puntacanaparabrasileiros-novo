<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> - Painel Administrativo</title>
    <!-- Favicon -->
    <?php if (setting('site_favicon')): ?>
    <link rel="icon" href="<?= e(setting('site_favicon')) ?>">
    <?php else: ?>
    <link rel="icon" type="image/svg+xml" href="<?= asset('images/favicon.svg') ?>">
    <link rel="icon" type="image/png" href="<?= asset('images/favicon.png') ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="/admin" class="sidebar-logo">
                    <strong>PCB</strong> Admin
                </a>
                <button class="sidebar-toggle" id="sidebarToggle">&times;</button>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin" class="nav-item <?= ($_SERVER['REQUEST_URI'] ?? '') === '/admin' ? 'active' : '' ?>">
                    <span class="nav-icon">&#9632;</span> Dashboard
                </a>
                <a href="/admin/passeios" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/passeios') ? 'active' : '' ?>">
                    <span class="nav-icon">&#9992;</span> Passeios
                </a>
                <a href="/admin/transfers/veiculos" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/transfers') ? 'active' : '' ?>">
                    <span class="nav-icon">&#128663;</span> Transfers
                </a>
                <a href="/admin/reservas" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/reservas') ? 'active' : '' ?>">
                    <span class="nav-icon">&#128203;</span> Reservas
                </a>
                <a href="/admin/vouchers" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/vouchers') ? 'active' : '' ?>">
                    <span class="nav-icon">&#127915;</span> Vouchers
                </a>
                <a href="/admin/afiliados" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/afiliados') ? 'active' : '' ?>">
                    <span class="nav-icon">&#128101;</span> Afiliados
                </a>
                <a href="/admin/newsletter" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/newsletter') ? 'active' : '' ?>">
                    <span class="nav-icon">&#9993;</span> Newsletter
                </a>
                <a href="/admin/usuarios" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/usuarios') ? 'active' : '' ?>">
                    <span class="nav-icon">&#128100;</span> Usuários
                </a>
                <?php if (is_superadmin()): ?>
                <a href="/admin/configuracoes" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/configuracoes') ? 'active' : '' ?>">
                    <span class="nav-icon">&#9881;</span> Configurações
                </a>
                <?php endif; ?>
                <hr class="nav-divider">
                <a href="/" class="nav-item" target="_blank">
                    <span class="nav-icon">&#127760;</span> Ver Site
                </a>
                <a href="/logout" class="nav-item">
                    <span class="nav-icon">&#10140;</span> Sair
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <button class="mobile-menu-btn" id="mobileMenuBtn">&#9776;</button>
                <h1 class="topbar-title"><?= e($pageTitle ?? 'Dashboard') ?></h1>
                <div class="topbar-right">
                    <span class="topbar-user">
                        <?= e(current_user()['first_name'] ?? 'Admin') ?>
                        (<?= e(current_user()['role'] ?? '') ?>)
                    </span>
                </div>
            </header>

            <!-- Flash Messages -->
            <?php if (has_flash('success')): ?>
            <div class="alert alert-success"><?= e(flash('success')) ?></div>
            <?php endif; ?>
            <?php if (has_flash('error')): ?>
            <div class="alert alert-danger"><?= e(flash('error')) ?></div>
            <?php endif; ?>
            <?php if (has_flash('info')): ?>
            <div class="alert alert-info"><?= e(flash('info')) ?></div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="admin-content">
                <?= $content ?>
            </div>
        </div>
    </div>

    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
