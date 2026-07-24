<!-- Hero Section com Vídeo de Fundo -->
<section class="hero-section">
    <div class="hero-video-wrapper">
        <video class="hero-video" autoplay muted loop playsinline>
            <source src="<?= asset('videos/hero-bg.mp4') ?>" type="video/mp4">
        </video>
        <div class="hero-overlay"></div>
    </div>
    <div class="hero-content container">
        <h1>Punta Cana espera por você!</h1>
        <p>Descubra o paraíso caribenho com os melhores pacotes exclusivos para brasileiros. Praias paradisíacas, resorts all-inclusive e uma experiência inesquecível.</p>
        <div class="hero-badges">
            <span class="hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5a2.5 2.5 0 010-5 2.5 2.5 0 010 5z"/></svg>
                Caribe Dominicano
            </span>
            <span class="hero-badge">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11z"/></svg>
                Pacotes Flexíveis
            </span>
        </div>
    </div>
</section>

<!-- Seção Nossa História -->
<section class="section section-nossa-historia">
    <div class="container">
        <div class="historia-grid">
            <div class="historia-content">
                <span class="historia-label">Nossa História</span>
                <h2 class="historia-title">Nós Oferecemos Experiências De Viagem Únicas E Personalizadas Para Você.</h2>
                <p class="historia-text">Fundada por um casal apaixonado pela beleza de Punta Cana, nossa agência se dedica a criar experiências de viagem sob medida, diferenciando-se de pacotes comuns. Nossa equipe, fluente em português, garante suporte completo para que cada visitante vivencie o melhor do Caribe de forma única e inesquecível.</p>
            </div>
            <div class="historia-images">
                <div class="historia-img-top">
                    <img src="<?= asset('images/layout/praia.jpeg') ?>" alt="Praia paradisíaca em Punta Cana" loading="lazy">
                </div>
                <div class="historia-img-bottom">
                    <img src="<?= asset('images/layout/praia-com-arvore.jpeg') ?>" alt="Palmeira com balanço na praia" loading="lazy">
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Passeios em Destaque -->
<section class="section section-featured-trips">
    <div class="container">
        <div class="section-intro">
            <span class="section-label">Passeios mais realizados</span>
            <h2 class="section-title">Explore os favoritos de Punta Cana</h2>
            <p class="section-subtitle">Descubra os passeios mais amados por quem já viveu essa experiência paradisíaca.</p>
            <div class="wave-divider">
                <svg width="60" height="20" viewBox="0 0 60 20" fill="none">
                    <path d="M2 10C7 2 12 18 17 10C22 2 27 18 32 10C37 2 42 18 47 10C52 2 57 18 58 10" stroke="#3772C0" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
        </div>

        <div class="featured-trips-grid">
            <?php foreach ($featuredTrips as $trip): ?>
            <div class="ft-card">
                <a href="/passeios/<?= e($trip['slug']) ?>" class="ft-card-image">
                    <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>" loading="lazy">
                    <?php if (!empty($trip['featured'])): ?>
                    <span class="ft-card-featured-badge" title="Destaque">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="white" stroke="none"><path d="M5 16L3 5l5.5 4L12 2l3.5 7L21 5l-2 11H5zm0 2h14v2H5v-2z"/></svg>
                    </span>
                    <?php endif; ?>
                    <?php if (isset($trip['regular_price']) && $trip['regular_price'] > $trip['min_price'] && $trip['min_price'] > 0): ?>
                    <?php $discount = round(100 - ($trip['min_price'] / $trip['regular_price'] * 100)); ?>
                    <span class="ft-card-discount"><?= $discount ?>% Off</span>
                    <?php endif; ?>
                </a>
                <div class="ft-card-body">
                    <h3 class="ft-card-title"><a href="/passeios/<?= e($trip['slug']) ?>"><?= e($trip['title']) ?></a></h3>
                    <p class="ft-card-desc"><?= e(truncate($trip['short_description'] ?? '', 70)) ?></p>
                    <div class="ft-card-footer">
                        <span class="ft-card-duration">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                            <?= e($trip['duration'] ?? '') ?> <?= ($trip['duration_unit'] ?? '') === 'hours' ? 'Horas' : 'Dias' ?>
                        </span>
                        <div class="ft-card-price">
                            <?php if (isset($trip['regular_price']) && $trip['regular_price'] > $trip['min_price'] && $trip['min_price'] > 0): ?>
                            <span class="price-old"><?= money($trip['regular_price']) ?></span>
                            <?php endif; ?>
                            <span class="price-current"><?= money($trip['min_price'] ?? 0) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div class="section-cta">
            <a href="/passeios" class="btn-ver-todos">Ver Todos os Passeios &rarr;</a>
        </div>
    </div>
</section>

<!-- CTA Transfer -->
<section class="section cta-section">

<!-- Seção Depoimentos -->
<section class="section section-depoimentos">
    <div class="container">
        <div class="section-intro">
            <span class="section-label">Depoimentos</span>
            <h2 class="section-title">O que nossos viajantes dizem</h2>
            <p class="section-subtitle">Histórias reais, experiências inesquecíveis e opiniões sinceras para ajudar você a escolher sua próxima aventura.</p>
            <div class="wave-divider">
                <svg width="60" height="20" viewBox="0 0 60 20" fill="none">
                    <path d="M2 10C7 2 12 18 17 10C22 2 27 18 32 10C37 2 42 18 47 10C52 2 57 18 58 10" stroke="#3772C0" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
        </div>

        <div class="depoimentos-slider" id="depoimentosSlider">
            <div class="depoimentos-track" id="depoimentosTrack">
                <!-- Slide 1 -->
                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div class="depoimento-info">
                            <strong>Carlos Eduardo</strong>
                            <span>Rio de Janeiro, RJ</span>
                        </div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="depoimento-text">"Segunda vez que viajo com a PuntaCanaBR e mais uma vez superou as expectativas. Recomendo para quem busca tranquilidade e bom preço."</p>
                </div>

                <!-- Slide 2 -->
                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div class="depoimento-info">
                            <strong>Mariana Silva</strong>
                            <span>Belo Horizonte, MG</span>
                        </div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
                    <p class="depoimento-text">"Amei a experiência, principalmente os passeios exclusivos. O único ponto a melhorar seria o tempo de transfer do aeroporto ao hotel."</p>
                </div>

                <!-- Slide 3 -->
                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div class="depoimento-info">
                            <strong>Ana Beatriz</strong>
                            <span>São Paulo, SP</span>
                        </div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="depoimento-text">"Viagem perfeita! Atendimento em português do início ao fim, hotel excelente e passeios bem organizados. Já estou planejando voltar no próximo ano."</p>
                </div>

                <!-- Slide 4 -->
                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div class="depoimento-info">
                            <strong>Fernanda Oliveira</strong>
                            <span>Curitiba, PR</span>
                        </div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="depoimento-text">"Tudo perfeito do início ao fim. O passeio à Isla Saona foi inesquecível. Equipe super atenciosa e dedicada!"</p>
                </div>

                <!-- Slide 5 -->
                <div class="depoimento-card">
                    <div class="depoimento-header">
                        <div class="depoimento-avatar">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="#ddd"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
                        </div>
                        <div class="depoimento-info">
                            <strong>Roberto Santos</strong>
                            <span>Brasília, DF</span>
                        </div>
                    </div>
                    <div class="depoimento-stars">&#9733;&#9733;&#9733;&#9733;&#9734;</div>
                    <p class="depoimento-text">"Ótimo custo-benefício. O transfer foi pontual e o motorista muito simpático. Voltarei com certeza."</p>
                </div>
            </div>

            <!-- Dots -->
            <div class="depoimentos-dots" id="depoimentosDots">
                <span class="dot active" data-slide="0"></span>
                <span class="dot" data-slide="1"></span>
                <span class="dot" data-slide="2"></span>
            </div>
        </div>
    </div>
</section>

<!-- Seção Transfers em Destaque -->
<section class="section section-transfers-home">
    <div class="container">
        <div class="section-intro">
            <h2 class="section-title">Busque seu Transfer e Reserve Agora!</h2>
            <p class="section-subtitle">Reserve seu transfer do aeroporto ou hotel e desfrute de uma viagem pontual, confortável e segura.</p>
            <div class="wave-divider">
                <svg width="60" height="20" viewBox="0 0 60 20" fill="none">
                    <path d="M2 10C7 2 12 18 17 10C22 2 27 18 32 10C37 2 42 18 47 10C52 2 57 18 58 10" stroke="#3772C0" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
        </div>

        <div class="transfers-home-grid">
            <?php if (!empty($featuredVehicles)): ?>
                <?php foreach ($featuredVehicles as $vehicle): ?>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="<?= e($vehicle['image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($vehicle['title']) ?>" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title"><?= e($vehicle['title']) ?></h3>
                    <p class="transfer-home-desc"><?= $vehicle['description'] ?? '' ?></p>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="<?= asset('images/layout/onibus.png') ?>" alt="Ônibus Compartilhado" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer em Ônibus Compartilhado</h3>
                    <p class="transfer-home-desc">Viaje com conforto e economia em um <strong>ônibus climatizado</strong>, com embarques regulares e motoristas experientes. Ideal para quem busca praticidade em Punta Cana.</p>
                </div>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="<?= asset('images/layout/van.png') ?>" alt="Van Privativa" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer Privativo em Van</h3>
                    <p class="transfer-home-desc">Tenha <strong>mais conforto e privacidade</strong> com nosso transfer exclusivo em van. Perfeito para famílias ou pequenos grupos, com ar-condicionado e horários flexíveis.</p>
                </div>
                <div class="transfer-home-card">
                    <div class="transfer-home-img">
                        <img src="<?= asset('images/layout/van_adap.png') ?>" alt="Van Adaptada" loading="lazy">
                    </div>
                    <h3 class="transfer-home-title">Transfer Acessível com Van Adaptada</h3>
                    <p class="transfer-home-desc">Viaje com <strong>segurança e acessibilidade</strong> em nossa van adaptada com rampa para cadeirantes. Espaço amplo e motorista preparado para um trajeto tranquilo.</p>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-cta">
            <a href="/transfers" class="btn-ver-todos">Reserve seu Transfer &rarr;</a>
        </div>
    </div>
</section>

<!-- Seção Stats / Números com animação -->
<section class="section-stats">
    <div class="stats-overlay"></div>
    <div class="container">
        <div class="stats-grid-home">
            <div class="stat-item">
                <span class="stat-number" data-target="98" data-suffix="%">0%</span>
                <span class="stat-desc">Avaliação de satisfação dos clientes</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="30" data-suffix="+">0+</span>
                <span class="stat-desc">Passeios e experiências disponíveis</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="5000" data-prefix="+" data-suffix="">0</span>
                <span class="stat-desc">Brasileiros atendidos com excelência</span>
            </div>
            <div class="stat-item">
                <span class="stat-number" data-target="600" data-prefix="+" data-suffix="">0</span>
                <span class="stat-desc">Lugares paradisíacos visitados</span>
            </div>
        </div>
    </div>
</section>

<!-- Seção Blog -->
<section class="section section-blog-home">
    <div class="container">
        <div class="section-intro">
            <span class="section-label">Nosso Blog</span>
            <h2 class="section-title">Blog de Viagem</h2>
            <p class="section-subtitle">Descubra roteiros imperdíveis, curiosidades locais, recomendações de restaurantes e dicas práticas para aproveitar cada momento em Punta Cana.</p>
            <div class="wave-divider">
                <svg width="60" height="20" viewBox="0 0 60 20" fill="none">
                    <path d="M2 10C7 2 12 18 17 10C22 2 27 18 32 10C37 2 42 18 47 10C52 2 57 18 58 10" stroke="#3772C0" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
        </div>

        <div class="blog-grid-home">
            <?php if (!empty($latestPosts)): ?>
                <?php foreach ($latestPosts as $post): ?>
                <div class="blog-card">
                    <a href="/blog/<?= e($post['slug']) ?>" class="blog-card-image">
                        <img src="<?= e($post['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($post['title']) ?>" loading="lazy">
                        <?php if ($post['category_name'] ?? null): ?>
                        <span class="blog-card-category" style="background: <?= e($post['category_color'] ?? '#3772C0') ?>">
                            <?= e(strtoupper($post['category_name'])) ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    <div class="blog-card-body">
                        <h3 class="blog-card-title">
                            <a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a>
                        </h3>
                        <a href="/blog/<?= e($post['slug']) ?>" class="blog-card-readmore">LER MAIS &raquo;</a>
                        <div class="blog-card-meta">
                            <span><?= e(($post['author_first_name'] ?? 'Admin') . ' ' . ($post['author_last_name'] ?? '')) ?></span>
                            <span>&middot;</span>
                            <span><?= format_date($post['published_at'] ?? $post['created_at']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="blog-card">
                    <a href="/blog" class="blog-card-image"><img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/70edfaca-8405-44a3-be02-2ae5c68249d6-990x490.jpeg" alt="" loading="lazy"><span class="blog-card-category">SEM CATEGORIA</span></a>
                    <div class="blog-card-body"><h3 class="blog-card-title">O que fazer em Punta Cana em 2026 – Guia para Brasileiros</h3><a href="/blog" class="blog-card-readmore">LER MAIS &raquo;</a><div class="blog-card-meta"><span>Punta Cana para Brasileiros</span><span>&middot;</span><span>15/03/2026</span></div></div>
                </div>
                <div class="blog-card">
                    <a href="/blog" class="blog-card-image"><img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0101-1024x678.jpg" alt="" loading="lazy"><span class="blog-card-category" style="background:#3772C0">GERAL</span></a>
                    <div class="blog-card-body"><h3 class="blog-card-title">"Buggies em Macao: Aventura, lama e cenote em Punta Cana"</h3><a href="/blog" class="blog-card-readmore">LER MAIS &raquo;</a><div class="blog-card-meta"><span>Lucas Vacari</span><span>&middot;</span><span>02/06/2025</span></div></div>
                </div>
                <div class="blog-card">
                    <a href="/blog" class="blog-card-image"><img src="https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG_5378-1024x785.jpeg" alt="" loading="lazy"><span class="blog-card-category" style="background:#E4B505">PASSEIO</span></a>
                    <div class="blog-card-body"><h3 class="blog-card-title">"Coco Bongo: a balada mais incrível de Punta Cana!"</h3><a href="/blog" class="blog-card-readmore">LER MAIS &raquo;</a><div class="blog-card-meta"><span>Lucas Vacari</span><span>&middot;</span><span>02/06/2025</span></div></div>
                </div>
            <?php endif; ?>
        </div>

        <div class="section-cta">
            <a href="/blog" class="btn-ver-todos">Leia nosso Blog &rarr;</a>
        </div>
    </div>
</section>

<!-- Seção Instagram Feed -->
<section class="section section-instagram">
    <div class="container">
        <div class="section-intro">
            <h2 class="section-title">Siga nosso Instagram</h2>
            <p class="section-subtitle">Compartilhe suas fotos usando <strong>#PuntaCanaBR</strong> e acompanhe nossas publicações no <strong>@<?= e($instagramUsername ?? 'puntacanaparabrasileiros') ?></strong></p>
            <div class="wave-divider">
                <svg width="60" height="20" viewBox="0 0 60 20" fill="none">
                    <path d="M2 10C7 2 12 18 17 10C22 2 27 18 32 10C37 2 42 18 47 10C52 2 57 18 58 10" stroke="#3772C0" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
        </div>

        <div class="instagram-feed" style="display:grid; grid-template-columns:repeat(5,1fr); gap:20px; width:100%;">
            <?php if (!empty($instagramPosts)): ?>
                <?php foreach ($instagramPosts as $post): ?>
                <a href="<?= e($post['permalink']) ?>" target="_blank" class="instagram-item">
                    <div class="instagram-item-header">
                        <div class="instagram-user">
                            <div class="instagram-avatar">
                                <img src="<?= asset('images/layout/PUNTA-CANA-1.png') ?>" alt="" width="24" height="24">
                            </div>
                            <div class="instagram-user-info">
                                <span class="instagram-username"><?= e(truncate($post['username'], 16)) ?></span>
                                <span class="instagram-date"><?= e($post['date']) ?></span>
                            </div>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                    </div>
                    <div class="instagram-item-media">
                        <img src="<?= e($post['media_type'] === 'VIDEO' ? $post['thumbnail_url'] : $post['media_url']) ?>" alt="" loading="lazy">
                        <?php if ($post['media_type'] === 'VIDEO'): ?>
                        <span class="instagram-play">&#9654;</span>
                        <?php endif; ?>
                    </div>
                </a>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Placeholder: fotos do Instagram com header de usuário (layout igual site antigo) -->
                    <?php
                    $instaPosts = [
                        ['img' => 'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/70edfaca-8405-44a3-be02-2ae5c68249d6-300x199.jpeg', 'date' => 'Jul 19'],
                        ['img' => 'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0138-300x200.jpg', 'date' => 'Jul 16'],
                        ['img' => 'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/09/IMG_6370-1-300x199.jpeg', 'date' => 'Jul 12'],
                        ['img' => 'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/05/IMG-20250527-WA0101-300x199.jpg', 'date' => 'Jul 10'],
                        ['img' => 'https://puntacanaparabrasileiros.com/wp-content/uploads/2025/10/21f72a17-03d9-43a8-99f7-39c03d664ff2-300x225.jpeg', 'date' => 'Jul 8'],
                    ];
                    foreach ($instaPosts as $instaPost): ?>
                    <a href="https://instagram.com/puntacanaparabrasileiros" target="_blank" class="instagram-item">
                        <div class="instagram-item-header">
                            <div class="instagram-user">
                                <div class="instagram-avatar">
                                    <img src="<?= asset('images/instagram-avatar.png') ?>" alt="" width="24" height="24">
                                </div>
                                <div class="instagram-user-info">
                                    <span class="instagram-username">puntacanaparabr...</span>
                                    <span class="instagram-date"><?= $instaPost['date'] ?></span>
                                </div>
                            </div>
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#999" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
                        </div>
                        <div class="instagram-item-media">
                            <img src="<?= $instaPost['img'] ?>" alt="" loading="lazy">
                            <span class="instagram-play">&#9654;</span>
                        </div>
                    </a>
                    <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Seção FAQ - Perguntas Frequentes -->
<section class="section section-faq">
    <div class="container">
        <div class="section-intro">
            <h2 class="section-title">Perguntas Frequentes</h2>
            <p class="section-subtitle">Tire suas principais dúvidas sobre viagens para Punta Cana. Se não encontrar o que procura, entre em contato com nossa equipe.</p>
            <div class="wave-divider">
                <svg width="60" height="20" viewBox="0 0 60 20" fill="none">
                    <path d="M2 10C7 2 12 18 17 10C22 2 27 18 32 10C37 2 42 18 47 10C52 2 57 18 58 10" stroke="#3772C0" stroke-width="2.5" stroke-linecap="round" fill="none"/>
                </svg>
            </div>
        </div>

        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-question" type="button">
                    <span>É necessário visto para brasileiros viajarem para Punta Cana?</span>
                    <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>Não. Brasileiros não precisam de visto para entrar na República Dominicana como turistas por até 30 dias. Basta apresentar o passaporte com validade mínima de 6 meses e adquirir o cartão de turista, que pode ser comprado online ou no próprio aeroporto.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" type="button">
                    <span>Qual a melhor época para visitar Punta Cana?</span>
                    <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>O clima em Punta Cana é agradável o ano todo, com temperatura média de 25°C a 30°C. A temporada de dezembro a abril é considerada a melhor por ter menos chuvas. De maio a novembro é a temporada de chuvas e furacões, mas os preços são mais atrativos e os dias de sol ainda são predominantes.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" type="button">
                    <span>Como funciona o transporte do aeroporto para o hotel?</span>
                    <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>Oferecemos serviços de transfer, nas modalidades <strong>privado</strong> e <strong>compartilhado</strong>, para garantir conforto e comodidade em seu deslocamento. Um representante de nossa equipe estará presente no aeroporto, devidamente identificado, aguardando com uma placa com o seu nome. O tempo estimado de trajeto até os resorts da região varia entre <strong>20 e 40 minutos</strong>, conforme a localização do hotel.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" type="button">
                    <span>Vocês oferecem atendimento em português durante toda a viagem?</span>
                    <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>Sim! Esse é um dos nossos diferenciais. Temos equipe brasileira em Punta Cana, pronta para atender nossos clientes em português 24h por dia.</p>
                </div>
            </div>

            <div class="faq-item">
                <button class="faq-question" type="button">
                    <span>O que acontece em caso de cancelamento da viagem?</span>
                    <svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                </button>
                <div class="faq-answer">
                    <p>Nossa política de cancelamento permite reembolso de até 100% do valor pago caso o cancelamento seja feito com mais de 24 horas de antecedência.</p>
                </div>
            </div>
        </div>

        <div class="faq-footer">
            <p>Não encontrou o que procurava?</p>
            <a href="/contato" class="btn btn-accent">Entre em contato conosco &rarr;</a>
        </div>
    </div>
</section>

<!-- Seção CTA Filtros - Última dobra -->
<section class="section section-cta-filtros">
    <div class="container">
        <div class="cta-filtros-grid">
            <!-- Imagem esquerda -->
            <div class="cta-filtros-image">
                <img src="<?= asset('images/layout/mulher.jpg') ?>" alt="Paraíso a sua espera" loading="lazy">
                <div class="cta-filtros-image-overlay">
                    <span class="cta-filtros-sun">&#9728;&#65039;</span>
                    <h3>Paraíso a sua espera</h3>
                    <p>Última chance para garantir seu verão perfeito</p>
                </div>
            </div>

            <!-- Formulário direita -->
            <div class="cta-filtros-form">
                <h2>Pronto para viver essa experiência?</h2>
                <p class="cta-filtros-desc">Agende sua viagem agora e garanta os melhores preços e condições exclusivas para brasileiros. Parcelamento em até 12x sem juros e atendimento personalizado em português.</p>

                <form action="/passeios" method="GET" class="filtros-form">
                    <!-- Tipo de passeio -->
                    <div class="filtro-field">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                        <select name="categoria" class="filtro-select">
                            <option value="">Tipos de passeios</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= e($cat['slug']) ?>"><?= e($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Atividade -->
                    <div class="filtro-field">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15 1.65 1.65 0 003 14.08V14a2 2 0 014 0v.09c0 .67.44 1.26 1.09 1.49"/></svg>
                        <select name="atividade" class="filtro-select">
                            <option value="">Atividade</option>
                            <option value="aventura">Aventura</option>
                            <option value="relaxamento">Relaxamento</option>
                            <option value="cultural">Cultural</option>
                            <option value="aquatico">Aquático</option>
                            <option value="noturno">Noturno</option>
                        </select>
                    </div>

                    <!-- Duração -->
                    <div class="filtro-field">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        <select name="duracao" class="filtro-select">
                            <option value="">0 Dias - 1 Dias</option>
                            <option value="0-4">0 - 4 Horas</option>
                            <option value="4-8">4 - 8 Horas</option>
                            <option value="8-12">8 - 12 Horas</option>
                            <option value="12+">12+ Horas</option>
                        </select>
                    </div>

                    <!-- Preço -->
                    <div class="filtro-field">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 3v4M8 3v4"/></svg>
                        <select name="preco" class="filtro-select">
                            <option value="">$45 - $199</option>
                            <option value="0-50">$0 - $50</option>
                            <option value="50-100">$50 - $100</option>
                            <option value="100-200">$100 - $200</option>
                            <option value="200+">$200+</option>
                        </select>
                    </div>

                    <!-- Data -->
                    <div class="filtro-field">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        <select name="data" class="filtro-select">
                            <option value="">Data</option>
                            <option value="hoje">Hoje</option>
                            <option value="amanha">Amanhã</option>
                            <option value="semana">Esta semana</option>
                            <option value="mes">Este mês</option>
                            <option value="proximo-mes">Próximo mês</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-pesquisar">Pesquisar os Passeios</button>
                </form>

                <p class="cta-filtros-link">Sem compromisso. <a href="/contato">Tire suas dúvidas &rarr;</a></p>
            </div>
        </div>
    </div>
</section>
