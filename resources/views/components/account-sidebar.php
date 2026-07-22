<aside class="account-sidebar">
    <div class="account-user-card">
        <div class="account-user-avatar">
            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
        </div>
        <div class="account-user-info">
            <strong><?= e(current_user()['first_name'] ?? '') ?> <?= e(current_user()['last_name'] ?? '') ?></strong>
            <span><?= e(current_user()['email'] ?? '') ?></span>
        </div>
    </div>
    <nav class="account-nav">
        <a href="/minha-conta" class="nav-item <?= ($_SERVER['REQUEST_URI'] ?? '') === '/minha-conta' ? 'active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Dashboard
        </a>
        <a href="/minha-conta/reservas" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/minha-conta/reservas') ? 'active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
            Reservas
        </a>
        <a href="/minha-conta/transfers" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/minha-conta/transfers') ? 'active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
            Meus Transfers
        </a>
        <a href="/minha-conta/wishlist" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/minha-conta/wishlist') ? 'active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
            Lista de Desejos
        </a>
        <a href="/minha-conta/cancelamentos" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/minha-conta/cancelamentos') ? 'active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            Cancelamentos
        </a>
        <a href="/minha-conta/cobranca" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/minha-conta/cobranca') ? 'active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            Informações de Cobrança
        </a>
        <a href="/minha-conta/perfil" class="nav-item <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/minha-conta/perfil') ? 'active' : '' ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            Detalhes da Conta
        </a>
        <hr class="nav-divider">
        <a href="/logout" class="nav-item nav-item-logout">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
            Sair
        </a>
    </nav>
</aside>
