<!-- HEADER -->
<header class="header">
    <div class="header-inner">
        <a href="/" class="header-logo">
            <img src="<?= asset('images/layout/PUNTA-CANA-1.png') ?>" alt="Punta Cana Para Brasileiros">
        </a>
        <nav class="header-nav">
            <button class="tab-btn active" data-tab="passeios">Passeios</button>
            <button class="tab-btn" data-tab="transfers">Transfer</button>
        </nav>
        <div class="header-actions">
            <a href="/passeios" class="btn-cta">Ver Passeios</a>
        </div>
    </div>
</header>

<!-- HERO -->
<section class="hero">
    <div class="hero-content">
        <h1 id="heroTitle">Nosso Catálogo de Experiências</h1>
        <p id="heroSubtitle">Descubra passeios exclusivos e transfers privativos em Punta Cana</p>
    </div>
    <div class="hero-wave">
        <svg viewBox="0 0 1440 40" preserveAspectRatio="none">
            <path d="M0,20 C360,40 720,0 1080,20 C1260,30 1380,35 1440,30 L1440,40 L0,40 Z" fill="#ffffff"/>
            <path d="M0,25 C300,10 600,35 900,20 C1100,10 1300,30 1440,22 L1440,40 L0,40 Z" fill="#ffffff" opacity="0.5"/>
        </svg>
    </div>
</section>

<!-- PASSEIOS TAB -->
<div class="tab-content active" id="tab-passeios">
    <div class="section-intro">
        <span class="caveat-label">Experiências Exclusivas</span>
        <h2>Passeios em Punta Cana</h2>
        <div class="wave-divider">
            <svg viewBox="0 0 120 20">
                <path d="M0,10 C20,0 40,20 60,10 C80,0 100,20 120,10" stroke-linecap="round"/>
            </svg>
        </div>
    </div>

    <div class="catalog-container">
        <div class="trips-grid">
            <?php foreach ($trips as $idx => $trip): ?>
            <?php
                $images = array_merge(
                    [$trip['featured_image'] ?? '/assets/images/placeholder.jpg'],
                    !empty($trip['gallery_images']) && is_array($trip['gallery_images']) ? $trip['gallery_images'] : []
                );
                $duration = $trip['duration'] . ($trip['duration_unit'] === 'hours' ? 'h' : ' dias');
                $price = $trip['min_price'] > 0 ? 'US$' . number_format($trip['min_price'], 0) : 'Consultar';
                $rating = $trip['rating'] > 0 ? $trip['rating'] : 4.5;
                $badge = $trip['featured'] ? 'Premium' : '';
            ?>
            <div class="trip-card">
                <div class="card-carousel" data-card="<?= $idx ?>">
                    <?php foreach ($images as $i => $img): ?>
                    <img src="<?= e($img) ?>" alt="<?= e($trip['title']) ?>" class="<?= $i === 0 ? 'active' : '' ?>" loading="lazy">
                    <?php endforeach; ?>
                    <?php if ($badge): ?>
                    <span class="card-badge"><?= e($badge) ?></span>
                    <?php endif; ?>
                    <button class="card-fav" aria-label="Favoritar">
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                    </button>
                    <?php if (count($images) > 1): ?>
                    <button class="carousel-btn prev" onclick="carouselNav(<?= $idx ?>, -1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <button class="carousel-btn next" onclick="carouselNav(<?= $idx ?>, 1)">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                    <div class="carousel-dots">
                        <?php foreach ($images as $i => $img): ?>
                        <span class="<?= $i === 0 ? 'active' : '' ?>" onclick="carouselGo(<?= $idx ?>, <?= $i ?>)"></span>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <h3><?= e($trip['title']) ?></h3>
                    <p class="description"><?= e($trip['short_description'] ?? '') ?></p>
                    <div class="card-meta">
                        <span>
                            <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            Punta Cana
                        </span>
                        <span>
                            <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?= e($duration) ?>
                        </span>
                        <span class="stars">
                            <svg viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                            <?= $rating ?>
                        </span>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="card-price">
                        <span class="label">Desde</span>
                        <span class="amount"><?= $price ?></span>
                    </div>
                    <a href="/passeios/<?= e($trip['slug']) ?>" class="btn-add">Ver Passeio</a>
                </div>
                <a href="#" class="card-details" onclick="openModal(<?= $idx ?>); return false;">Ver Detalhes <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<!-- TRANSFERS TAB -->
<div class="tab-content" id="tab-transfers">
    <div class="catalog-container" style="padding-top:48px;">
        <div class="transfer-info-section">
            <h3>O que é Transfer Privado?</h3>
            <p>Nosso serviço de Transfer Privado oferece transporte exclusivo, motorizado e seguro entre o aeroporto e seu hotel (ou vice-versa). Você será recebido por um motorista profissional, experiente e que fala português, garantindo uma experiência tranquila desde o primeiro momento.</p>
            <p>Sem filas, sem surpresas com preços, sem esperas longas. Apenas conforto e pontualidade.</p>
        </div>

        <!-- Benefícios -->
        <h3 class="transfer-section-title">Benefícios do Transfer Privado</h3>
        <div class="transfer-benefits-grid">
            <div class="transfer-benefit-card">
                <div class="benefit-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <strong>Conforto Garantido</strong>
                <p>Veículos climatizados, limpos e modernos, com espaço total para bagagem.</p>
            </div>
            <div class="transfer-benefit-card">
                <div class="benefit-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </div>
                <strong>Motoristas Profissionais</strong>
                <p>Equipe treinada, pontual e que fala português. Sua segurança é nossa prioridade.</p>
            </div>
            <div class="transfer-benefit-card">
                <div class="benefit-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <strong>Preço Fixo</strong>
                <p>Sem surpresas. Você sabe exatamente o que paga antes de confirmar.</p>
            </div>
            <div class="transfer-benefit-card">
                <div class="benefit-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <strong>Rastreamento em Tempo Real</strong>
                <p>Acompanhe seu transfer em tempo real através do WhatsApp.</p>
            </div>
            <div class="transfer-benefit-card">
                <div class="benefit-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <strong>Flexibilidade</strong>
                <p>Adaptamos aos seus horários. Chegadas noturnas? Sem problemas.</p>
            </div>
            <div class="transfer-benefit-card">
                <div class="benefit-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <strong>Atendimento em Português</strong>
                <p>Comunicação clara e direta, tudo em português brasileiro.</p>
            </div>
        </div>

        <!-- Como Funciona -->
        <h3 class="transfer-section-title">Como Funciona?</h3>
        <div class="transfer-steps">
            <div class="transfer-step">
                <div class="step-number">1</div>
                <div class="step-content">
                    <strong>Faça sua Reserva</strong>
                    <p>Clique no botão "Reservar Transfer" e preencha suas informações: data, horário, origem e destino.</p>
                </div>
            </div>
            <div class="transfer-step">
                <div class="step-number">2</div>
                <div class="step-content">
                    <strong>Confirmação Imediata</strong>
                    <p>Receba a confirmação via WhatsApp com todos os detalhes do motorista e do veículo.</p>
                </div>
            </div>
            <div class="transfer-step">
                <div class="step-number">3</div>
                <div class="step-content">
                    <strong>Aproveite seu Transfer</strong>
                    <p>No dia, seu motorista estará esperando com placa de identificação. Relaxe e aproveite!</p>
                </div>
            </div>
        </div>

        <!-- O que está Incluído -->
        <div class="transfer-info-section">
            <h3>O que está Incluído?</h3>
            <ul class="transfer-includes-list">
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Transporte de ida ou volta (conforme contratação)</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Motorista profissional falante de português</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Veículo climatizado e em perfeito estado</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Espaço total para bagagem</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Preço fixo (sem surpresas)</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Acompanhamento via WhatsApp</li>
                <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>Atendimento 24/7</li>
            </ul>
        </div>

        <!-- Tipos de Veículos -->
        <h3 class="transfer-section-title">Serviços de transporte em Punta Cana</h3>
        <p style="text-align:center;font-size:14px;color:var(--gray);max-width:700px;margin:0 auto 32px;line-height:1.7;">Reserve já o seu traslado para o seu hotel em Punta Cana e evite atrasos desnecessários no aeroporto na chegada. Viaje sem preocupações com o nosso serviço profissional de transporte privativo ou compartilhado, em veículos confortáveis com Wi-Fi gratuito e cadeiras para crianças disponíveis.</p>
        <div class="vehicles-grid">
            <div class="vehicle-card">
                <div class="vehicle-card-img">
                    <img src="/assets/images/onibus.png" alt="Transfer em Ônibus Compartilhado">
                </div>
                <div class="vehicle-card-body">
                    <h4>Transfer em Ônibus Compartilhado</h4>
                    <p class="vehicle-desc">Viaje com conforto e economia em um <strong>ônibus climatizado</strong>, com embarques regulares e motoristas experientes.</p>
                </div>
            </div>
            <div class="vehicle-card">
                <div class="vehicle-card-img">
                    <img src="/assets/images/van.png" alt="Transfer Privativo em Van">
                </div>
                <div class="vehicle-card-body">
                    <h4>Transfer Privativo em Van</h4>
                    <p class="vehicle-desc">Tenha <strong>mais conforto e privacidade</strong> com nosso transfer exclusivo em van. Perfeito para famílias ou pequenos grupos.</p>
                </div>
            </div>
            <div class="vehicle-card">
                <div class="vehicle-card-img">
                    <img src="/assets/images/van_adap.png" alt="Transfer Acessível com Van Adaptada">
                </div>
                <div class="vehicle-card-body">
                    <h4>Transfer Acessível com Van Adaptada</h4>
                    <p class="vehicle-desc">Viaje com <strong>segurança e acessibilidade</strong> em nossa van adaptada com <strong>rampa para cadeirantes</strong>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer class="catalog-footer">
    <p>Precisa de ajuda? Fale conosco</p>
    <a href="https://api.whatsapp.com/send?phone=18294582170&text=Ol%C3%A1%2C%20gostaria%20de%20mais%20informa%C3%A7%C3%B5es!" class="btn-whatsapp" target="_blank">
        <svg viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        WhatsApp
    </a>
    <p class="copyright">&copy; <?= date('Y') ?> Punta Cana Para Brasileiros. Todos os direitos reservados.</p>
</footer>

<!-- MODAL DE DETALHES -->
<div class="modal-overlay" id="modalOverlay" onclick="closeModal(event)">
    <div class="modal" id="modalContent">
        <button class="modal-close" onclick="closeModal()">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
        <img class="modal-image" id="modalImage" src="" alt="">
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer" id="modalFooter"></div>
    </div>
</div>

<script>
// Dados dos trips para o modal
var CATALOG_TRIPS = <?= json_encode(array_map(function($t) {
    $inc = [];
    $exc = [];
    if (!empty($t['includes'])) {
        $decoded = json_decode($t['includes'], true);
        if (is_array($decoded)) $inc = $decoded;
    }
    if (!empty($t['excludes'])) {
        $decoded = json_decode($t['excludes'], true);
        if (is_array($decoded)) $exc = $decoded;
    }
    return [
        'title' => $t['title'],
        'slug' => $t['slug'],
        'description' => $t['description'] ?? '',
        'short_description' => $t['short_description'] ?? '',
        'duration' => $t['duration'] . ($t['duration_unit'] === 'hours' ? 'h' : ' dias'),
        'featured_image' => $t['featured_image'] ?? '/assets/images/placeholder.jpg',
        'min_price' => $t['min_price'] ?? 0,
        'rating' => $t['rating'] > 0 ? $t['rating'] : 4.5,
        'includes' => $inc,
        'excludes' => $exc,
        'prices' => array_map(function($p) {
            return [
                'category' => $p['category_name'] . ' (' . ($p['age_group'] ?? '') . ')',
                'price' => $p['sale_price'] ?: $p['price'],
                'type' => $p['category_slug'] ?? 'adult',
            ];
        }, $t['price_categories'] ?? []),
    ];
}, $trips), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

// Carousel navigation
function carouselNav(cardIdx, direction) {
    const carousel = document.querySelector('[data-card="' + cardIdx + '"]');
    const images = carousel.querySelectorAll('img');
    const dots = carousel.querySelectorAll('.carousel-dots span');
    let current = [...images].findIndex(img => img.classList.contains('active'));
    images[current].classList.remove('active');
    if (dots[current]) dots[current].classList.remove('active');
    current = (current + direction + images.length) % images.length;
    images[current].classList.add('active');
    if (dots[current]) dots[current].classList.add('active');
}

function carouselGo(cardIdx, imgIdx) {
    const carousel = document.querySelector('[data-card="' + cardIdx + '"]');
    const images = carousel.querySelectorAll('img');
    const dots = carousel.querySelectorAll('.carousel-dots span');
    images.forEach(img => img.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active'));
    images[imgIdx].classList.add('active');
    if (dots[imgIdx]) dots[imgIdx].classList.add('active');
}

// Tab switching
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        document.getElementById('tab-' + btn.dataset.tab).classList.add('active');

        const heroTexts = {
            passeios: { title: 'Nosso Cat\u00e1logo de Experi\u00eancias', subtitle: 'Descubra passeios exclusivos e experi\u00eancias \u00fanicas em Punta Cana' },
            transfers: { title: 'Transfer Privado em Punta Cana', subtitle: 'Conforto, seguran\u00e7a e tranquilidade para sua chegada e sa\u00edda' }
        };
        const texts = heroTexts[btn.dataset.tab];
        if (texts) {
            document.getElementById('heroTitle').textContent = texts.title;
            document.getElementById('heroSubtitle').textContent = texts.subtitle;
        }
    });
});

// Modal
function openModal(idx) {
    var trip = CATALOG_TRIPS[idx];
    if (!trip) return;
    var overlay = document.getElementById('modalOverlay');
    document.getElementById('modalImage').src = trip.featured_image;
    document.getElementById('modalImage').alt = trip.title;

    // Preços por categoria
    var pricesHtml = '';
    if (trip.prices && trip.prices.length > 0) {
        pricesHtml = '<div class="modal-section-title"><svg viewBox="0 0 24 24" fill="none" stroke="#1B6F00" stroke-width="2" width="18" height="18"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg> Pre\u00e7os (por pessoa)</div><div class="modal-prices">';
        trip.prices.forEach(function(p) {
            var priceVal = parseFloat(p.price);
            var dotClass = p.type === 'infantil' ? 'infant' : (p.type === 'crianca' ? 'child' : '');
            if (priceVal === 0) {
                pricesHtml += '<div class="modal-price-row"><span class="modal-price-category"><span class="modal-price-dot ' + dotClass + '"></span>' + p.category + '</span><span class="modal-price-free">GRATIS</span></div>';
            } else {
                pricesHtml += '<div class="modal-price-row"><span class="modal-price-category"><span class="modal-price-dot ' + dotClass + '"></span>' + p.category + '</span><span class="modal-price-value">US$' + priceVal.toFixed(0) + '</span></div>';
            }
        });
        pricesHtml += '</div>';
    }

    // Inclui
    var includesHtml = '';
    var incl = trip.includes && trip.includes.length > 0 ? trip.includes : ['Transporte ida e volta do hotel', 'Guia em portugues', 'Equipamentos inclusos'];
    includesHtml = '<div class="modal-section-title"><svg viewBox="0 0 24 24" fill="none" stroke="#1B6F00" stroke-width="2" width="18" height="18"><polyline points="20 6 9 17 4 12"/></svg> O que Inclui</div><ul class="modal-list includes">';
    incl.forEach(function(item) {
        includesHtml += '<li><svg viewBox="0 0 24 24" width="16" height="16"><polyline points="20 6 9 17 4 12"/></svg>' + item + '</li>';
    });
    includesHtml += '</ul>';

    // Não inclui
    var excludesHtml = '';
    var excl = trip.excludes && trip.excludes.length > 0 ? trip.excludes : ['Fotos profissionais', 'Gorjetas (opcional)', 'Itens pessoais'];
    excludesHtml = '<div class="modal-section-title"><svg viewBox="0 0 24 24" fill="none" stroke="#e74c3c" stroke-width="2" width="18" height="18"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> O que N\u00e3o Inclui</div><ul class="modal-list excludes">';
    excl.forEach(function(item) {
        excludesHtml += '<li><svg viewBox="0 0 24 24" width="16" height="16"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>' + item + '</li>';
    });
    excludesHtml += '</ul>';

    document.getElementById('modalBody').innerHTML =
        '<span class="modal-badge">Passeio</span>' +
        '<h2 class="modal-title">' + trip.title + '</h2>' +
        '<div class="modal-rating"><svg viewBox="0 0 24 24" width="16" height="16" fill="#E4B505"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg> <strong>' + trip.rating + '</strong></div>' +
        '<p class="modal-desc">' + (trip.description || trip.short_description) + '</p>' +
        '<div class="modal-info-grid">' +
            '<div class="modal-info-item"><div class="modal-info-icon"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div><div class="modal-info-label">Dura\u00e7\u00e3o</div><div class="modal-info-value">' + trip.duration + '</div></div></div>' +
            '<div class="modal-info-item"><div class="modal-info-icon"><svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div><div><div class="modal-info-label">Local</div><div class="modal-info-value">Punta Cana</div></div></div>' +
        '</div>' +
        pricesHtml + includesHtml + excludesHtml;

    var priceText = trip.min_price > 0 ? 'US$' + Math.round(trip.min_price) : 'Consultar';
    document.getElementById('modalFooter').innerHTML =
        '<div class="modal-footer-price"><span class="modal-footer-label">Desde</span><span class="modal-footer-amount">' + priceText + '</span></div>' +
        '<a href="/passeios/' + trip.slug + '" class="btn-add">Ver Passeio</a>';

    overlay.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal(event) {
    if (event && event.target !== event.currentTarget) return;
    document.getElementById('modalOverlay').classList.remove('active');
    document.body.style.overflow = '';
}
</script>

