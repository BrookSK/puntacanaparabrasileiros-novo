<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Admin') ?> - Painel Administrativo</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/admin.css') ?>?v=4.5">
    <link rel="stylesheet" href="<?= asset('css/phone-country.css') ?>?v=1.1">
    <?php if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/whatsapp/chat')): ?>
    <link rel="stylesheet" href="<?= asset('css/whatsapp.css') ?>?v=1.4">
    <?php endif; ?>
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar" id="adminSidebar">
            <div class="sidebar-header">
                <a href="/admin" class="sidebar-logo">
                    <strong>Punta Cana</strong> <span style="opacity:0.7;font-weight:400;">Admin</span>
                </a>
                <button class="sidebar-toggle" id="sidebarToggle">&times;</button>
            </div>
            <nav class="sidebar-nav">
                <a href="/admin" class="nav-item <?= ($_SERVER['REQUEST_URI'] ?? '') === '/admin' ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span> Dashboard
                </a>
                <a href="/admin/relatorios" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/relatorios') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg></span> Relatórios
                </a>
                <a href="/admin/passeios" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/passeios') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 010 20 14.5 14.5 0 010-20"/><path d="M2 12h20"/></svg></span> Passeios
                </a>
                <a href="/admin/categorias" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/categorias') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg></span> Categorias
                </a>
                <a href="/admin/transfers/veiculos" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/transfers') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span> Transfers
                </a>
                <a href="/admin/reservas" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/reservas') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg></span> Reservas
                </a>
                <a href="/admin/cancelamentos" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/cancelamentos') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></span> Cancelamentos
                </a>

                <a href="/admin/afiliados" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/afiliados') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg></span> Afiliados
                </a>
                <a href="/admin/newsletter" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/newsletter') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></span> Newsletter
                </a>
                <a href="/admin/usuarios" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/usuarios') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span> Usuários
                </a>
                <?php if (is_superadmin()): ?>
                <a href="/admin/configuracoes" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/configuracoes') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2h0a2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2h0a2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06A1.65 1.65 0 009 4.6h0a1.65 1.65 0 001-1.51V3a2 2 0 012-2h0a2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06A1.65 1.65 0 0019.4 9h0a1.65 1.65 0 001.51 1H21a2 2 0 012 2h0a2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg></span> Configurações
                </a>
                <?php endif; ?>
                <hr class="nav-divider">
                <a href="/whatsapp/chat" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/whatsapp') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/></svg></span> WhatsApp
                </a>
                <a href="/crm" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/crm') ? 'active' : '' ?>">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg></span> CRM
                </a>
                <hr class="nav-divider">
                <a href="/" class="nav-item" target="_blank">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg></span> Ver Site
                </a>
                <a href="/logout" class="nav-item">
                    <span class="nav-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg></span> Sair
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
            <div class="alert alert-success">
                <span><?= e(flash('success')) ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <?php endif; ?>
            <?php if (has_flash('error')): ?>
            <div class="alert alert-danger">
                <span><?= e(flash('error')) ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <?php endif; ?>
            <?php if (has_flash('info')): ?>
            <div class="alert alert-info">
                <span><?= e(flash('info')) ?></span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">&times;</button>
            </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="admin-content">
                <?= $content ?>
            </div>
        </div>
    </div>

    <?php if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/admin/relatorios')): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-array@3"></script>
    <script src="https://cdn.jsdelivr.net/npm/d3-geo@3"></script>
    <script src="https://cdn.jsdelivr.net/npm/topojson-client@3"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-chart-geo@4.3.0/build/index.umd.min.js"></script>
    <?php endif; ?>
    <script src="<?= asset('js/admin.js') ?>?v=2.3"></script>
    <script src="<?= asset('js/phone-country.js') ?>?v=1.1"></script>
    <?php if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/whatsapp/chat')): ?>
    <script src="<?= asset('js/whatsapp.js') ?>?v=1.5"></script>
    <?php endif; ?>
</body>
</html>
