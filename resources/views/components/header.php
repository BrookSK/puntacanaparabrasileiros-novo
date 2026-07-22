<header class="site-header">
    <div class="container">
        <div class="header-inner">
            <!-- Logo -->
            <a href="/" class="site-logo">
                <img src="<?= asset('images/layout/PUNTA-CANA-1.png') ?>" alt="Punta Cana para Brasileiros">
            </a>

            <!-- Navigation Central -->
            <nav class="main-nav" id="mainNav">
                <a href="/" class="nav-link <?= ($_SERVER['REQUEST_URI'] ?? '') === '/' ? 'active' : '' ?>">Home</a>
                <a href="/passeios" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/passeios') ? 'active' : '' ?>">Passeios</a>
                <a href="/transfers" class="nav-link <?= str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/transfers') ? 'active' : '' ?>">Transfer</a>
                <a href="/pagina/blog" class="nav-link">Blog</a>
                <a href="/sobre-nos" class="nav-link">Sobre Nós</a>
                <a href="/contato" class="nav-link">Contato</a>
            </nav>

            <!-- Right Actions -->
            <div class="header-actions">
                <!-- Bandeiras idioma -->
                <div class="header-flags">
                    <a href="?lang=en" class="flag-btn" title="English">
                        <svg width="24" height="16" viewBox="0 0 24 16" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="16" fill="#002868"/>
                            <rect y="1.23" width="24" height="1.23" fill="white"/><rect y="3.69" width="24" height="1.23" fill="white"/><rect y="6.15" width="24" height="1.23" fill="white"/><rect y="8.62" width="24" height="1.23" fill="white"/><rect y="11.08" width="24" height="1.23" fill="white"/><rect y="13.54" width="24" height="1.23" fill="white"/>
                            <rect y="2.46" width="24" height="1.23" fill="#BF0A30"/><rect y="4.92" width="24" height="1.23" fill="#BF0A30"/><rect y="7.38" width="24" height="1.23" fill="#BF0A30"/><rect y="9.85" width="24" height="1.23" fill="#BF0A30"/><rect y="12.31" width="24" height="1.23" fill="#BF0A30"/><rect y="14.77" width="24" height="1.23" fill="#BF0A30"/>
                            <rect width="10" height="8.62" fill="#002868"/>
                        </svg>
                    </a>
                    <a href="?lang=pt" class="flag-btn" title="Português">
                        <svg width="24" height="16" viewBox="0 0 24 16" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="16" fill="#009739"/>
                            <path d="M12 2L22 8L12 14L2 8Z" fill="#FEDD00"/>
                            <circle cx="12" cy="8" r="3.5" fill="#002776"/>
                            <path d="M9 8.5C9 8.5 10.5 7 12 7.5C13.5 8 15 8.5 15 8.5" stroke="white" stroke-width="0.5" fill="none"/>
                        </svg>
                    </a>
                    <a href="?lang=es" class="flag-btn" title="Español">
                        <svg width="24" height="16" viewBox="0 0 24 16" xmlns="http://www.w3.org/2000/svg">
                            <rect width="24" height="4" fill="#AA151B"/><rect y="4" width="24" height="8" fill="#F1BF00"/><rect y="12" width="24" height="4" fill="#AA151B"/>
                        </svg>
                    </a>
                </div>

                <!-- Ícones -->
                <div class="header-icons">
                    <!-- Busca -->
                    <a href="/pesquisa" class="header-icon-btn" title="Buscar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    </a>

                    <!-- Instagram -->
                    <a href="https://www.instagram.com/puntacanaparabrasileiros" target="_blank" class="header-icon-btn" title="Instagram">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </a>

                    <!-- Carrinho -->
                    <a href="/carrinho" class="header-icon-btn header-cart" title="Carrinho">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>
                        <span class="cart-badge" id="cartBadge"></span>
                    </a>

                    <!-- WhatsApp -->
                    <a href="https://api.whatsapp.com/send?phone=18294582170&text=Oi%2C%20tudo%20bem%3FPara%20ajudar%20voc%C3%AA%20da%20melhor%20forma%2C%20me%20diga%3A%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Seu%20nome%3A%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Quando%20voc%C3%AA%20vai%20chegar%3F%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Quantas%20pessoas%20s%C3%A3o%3F%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Em%20qual%20hotel%20voc%C3%AA%20vai%20ficar%3FEstou%20aqui%20para%20te%20ajudar%20com%20o%20que%20precisar!" target="_blank" class="header-icon-btn" title="WhatsApp">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    </a>

                    <!-- Conta/User -->
                    <?php if (current_user()): ?>
                    <div class="header-user-menu">
                        <button class="header-icon-btn" id="userMenuBtn" title="Minha Conta">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </button>
                        <div class="user-dropdown" id="userDropdown">
                            <a href="/minha-conta">Minha Conta</a>
                            <a href="/minha-conta/reservas">Minhas Reservas</a>
                            <a href="/minha-conta/wishlist">Lista de Desejos</a>
                            <?php if (is_admin()): ?>
                            <a href="/admin">Painel Admin</a>
                            <?php endif; ?>
                            <hr>
                            <a href="/logout">Sair</a>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="/login" class="header-icon-btn" title="Entrar">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </a>
                    <?php endif; ?>

                    <!-- Favoritos / Wishlist -->
                    <a href="<?= current_user() ? '/minha-conta/wishlist' : '/login' ?>" class="header-icon-btn" title="Lista de Desejos">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 00-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 00-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 000-7.78z"/></svg>
                    </a>
                </div>

                <!-- CTA Button -->
                <a href="/passeios" class="btn-agendar">Agendar Agora</a>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-toggle" id="mobileToggle" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>

    <!-- Barra de busca expandível -->
    <div class="search-overlay" id="searchOverlay" style="display:none;">
        <div class="container">
            <form action="/passeios" method="GET" class="search-bar">
                <input type="text" name="busca" placeholder="Buscar passeios, transfers..." class="search-input" id="searchInput" autocomplete="off">
                <button type="submit" class="search-submit">Buscar</button>
                <button type="button" class="search-close" id="searchClose">&times;</button>
            </form>
        </div>
    </div>
</header>
