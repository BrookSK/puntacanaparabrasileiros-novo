<!-- Galeria Full-Width Slider -->
<section class="trip-gallery-hero">
    <div class="trip-slider" id="tripSlider">
        <div class="trip-slider-track" id="tripSliderTrack">
            <div class="trip-slide active">
                <img src="<?= e($trip['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($trip['title']) ?>">
            </div>
            <?php if (!empty($gallery)): ?>
            <?php foreach ($gallery as $img): ?>
            <div class="trip-slide">
                <img src="<?= e($img) ?>" alt="<?= e($trip['title']) ?>">
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <!-- Setas de navegação -->
        <button class="trip-slider-arrow trip-slider-prev" id="sliderPrev">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
        <button class="trip-slider-arrow trip-slider-next" id="sliderNext">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <!-- Botão Galeria -->
        <button class="trip-gallery-btn" id="galleryBtn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
            Galeria
        </button>
    </div>
</section>

<section class="trip-detail">
    <div class="container">
        <div class="trip-content-grid">
            <!-- Main Content -->
            <div class="trip-main">
                <!-- Título + Badge duração -->
                <div class="trip-title-row">
                    <h1 class="trip-title"><?= e($trip['title']) ?></h1>
                    <?php if ($trip['duration']): ?>
                    <div class="trip-duration-badge">
                        <span class="duration-number"><?= e($trip['duration']) ?></span>
                        <span class="duration-unit"><?= $trip['duration_unit'] === 'hours' ? 'Horas' : 'Dias' ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Descrição curta -->
                <?php if ($trip['short_description']): ?>
                <p class="trip-short-desc"><?= e($trip['short_description']) ?></p>
                <?php endif; ?>

                <?php if ($trip['important_notes']): ?>
                <p class="trip-important-note"><strong><?= e($trip['important_notes']) ?></strong></p>
                <?php endif; ?>

                <!-- Tabs -->
                <div class="trip-tabs">
                    <button class="trip-tab active" data-tab="visao-geral">Visão geral</button>
                    <button class="trip-tab" data-tab="custo">Custo</button>
                    <button class="trip-tab" data-tab="datas">Datas</button>
                    <button class="trip-tab" data-tab="faqs">FAQs</button>
                </div>

                <!-- Tab: Visão Geral -->
                <div class="trip-tab-content active" id="tab-visao-geral">
                    <h2>Visão Geral</h2>
                    <div class="trip-body-content">
                        <?= $trip['description'] ?>
                    </div>

                    <!-- O que inclui -->
                    <?php if (!empty($includes)): ?>
                    <div class="trip-section">
                        <h3>O que inclui</h3>
                        <ul class="trip-check-list">
                            <?php foreach ($includes as $item): ?>
                            <li><span class="check-icon">&#10003;</span> <?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- O que não inclui -->
                    <?php if (!empty($excludes)): ?>
                    <div class="trip-section">
                        <h3>O que não inclui</h3>
                        <ul class="trip-x-list">
                            <?php foreach ($excludes as $item): ?>
                            <li><span class="x-icon">&#10007;</span> <?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <!-- Itinerário / Destaques -->
                    <?php if (!empty($itinerary)): ?>
                    <div class="trip-section">
                        <h3>Destaques</h3>
                        <ul class="trip-check-list">
                            <?php foreach ($itinerary as $day): ?>
                            <li><span class="check-icon">&#10003;</span> <?= e($day['title']) ?><?= $day['description'] ? ' - ' . e($day['description']) : '' ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tab: Custo -->
                <div class="trip-tab-content" id="tab-custo">
                    <h2>Inclui / Exclui</h2>

                    <?php if (!empty($includes)): ?>
                    <div class="trip-section">
                        <h3>Inclui</h3>
                        <ul class="trip-check-list">
                            <?php foreach ($includes as $item): ?>
                            <li><span class="check-icon">&#10003;</span> <?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($excludes)): ?>
                    <div class="trip-section">
                        <h3>Não Inclui</h3>
                        <ul class="trip-x-list">
                            <?php foreach ($excludes as $item): ?>
                            <li><span class="x-icon">&#10007;</span> <?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php foreach ($packages as $pkg): ?>
                    <div class="trip-package-card">
                        <h4><?= e($pkg['title']) ?></h4>
                        <?php if (!empty($pkg['categories'])): ?>
                        <table class="table">
                            <thead><tr><th>Categoria</th><th>Idade</th><th>Preço</th></tr></thead>
                            <tbody>
                            <?php foreach ($pkg['categories'] as $cat): ?>
                            <tr>
                                <td><?= e($cat['category_name']) ?></td>
                                <td><?= e($cat['age_group'] ?? '') ?></td>
                                <td>
                                    <?php if ($cat['sale_price']): ?>
                                    <span style="text-decoration:line-through;color:#999"><?= money((float)$cat['price']) ?></span>
                                    <strong><?= money((float)$cat['sale_price']) ?></strong>
                                    <?php else: ?>
                                    <strong><?= money((float)$cat['price']) ?></strong>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>

                    <?php if (!empty($extraServices)): ?>
                    <h3 style="margin-top:20px">Serviços Extras</h3>
                    <ul class="trip-check-list">
                        <?php foreach ($extraServices as $svc): ?>
                        <li><span class="check-icon">+</span> <?= e($svc['name']) ?> — <?= money((float)$svc['price']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                </div>

                <!-- Tab: Datas -->
                <div class="trip-tab-content" id="tab-datas">
                    <h2>Datas Disponíveis</h2>
                    <?php if (!empty($fixedDates)): ?>
                    <div class="trip-dates-list">
                        <?php foreach ($fixedDates as $fd): ?>
                        <div class="trip-date-item">
                            <span class="trip-date-value"><?= format_date($fd['date']) ?></span>
                            <?php if ($fd['time']): ?><span class="trip-date-time"><?= e($fd['time']) ?></span><?php endif; ?>
                            <span class="badge badge-<?= $fd['status'] === 'available' ? 'success' : 'danger' ?>"><?= $fd['status'] === 'available' ? 'Disponível' : 'Esgotado' ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <p style="color:var(--gray)">Disponível durante todo o ano. Selecione a data desejada no momento da reserva.</p>
                    <?php endif; ?>
                </div>

                <!-- Tab: FAQs -->
                <div class="trip-tab-content" id="tab-faqs">
                    <div class="trip-faq-header">
                        <h2>FAQ (Perguntas Frequentes)</h2>
                        <label class="expand-all-toggle">
                            <span>Expandir tudo</span>
                            <input type="checkbox" id="expandAllFaqs" onchange="toggleAllFaqs(this.checked)">
                            <span class="toggle-switch"></span>
                        </label>
                    </div>
                    <div class="faq-list">
                        <?php if (!empty($tripFaqs)): ?>
                        <?php foreach ($tripFaqs as $faq): ?>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span><?= e($faq['question']) ?></span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p><?= e($faq['answer']) ?></p></div>
                        </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span>O que está incluído no passeio?</span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p>Confira os itens incluídos na aba "Custo" acima.</p></div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span>O que não está incluído?</span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p>Confira os itens não incluídos na aba "Custo" acima.</p></div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span>Crianças podem participar?</span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p>Sim, crianças a partir de 2 anos podem participar acompanhadas de um adulto responsável.</p></div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span>Gestantes podem fazer o passeio?</span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p>Depende do passeio. Consulte as notas importantes no topo da página ou entre em contato.</p></div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span>Quanto tempo dura o passeio?</span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p>A duração está indicada no badge ao lado do título. Geralmente entre 4 e 10 horas.</p></div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span>O que devo levar?</span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p>Roupas confortáveis, protetor solar, repelente, roupa de banho, toalha e dinheiro para fotos.</p></div>
                        </div>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span>O passeio acontece mesmo com chuva?</span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p>Em caso de condições climáticas adversas, o passeio pode ser reagendado ou reembolsado integralmente.</p></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Reviews -->
                <?php if (!empty($reviews)): ?>
                <div class="trip-section" style="margin-top:30px">
                    <h3>Avaliações dos Clientes</h3>
                    <?php foreach ($reviews as $review): ?>
                    <div class="review-item">
                        <div class="review-header">
                            <span class="review-stars"><?= str_repeat('&#9733;', (int)$review['rating']) ?></span>
                            <span class="review-author"><?= e($review['first_name'] ?? $review['author_name'] ?? 'Anônimo') ?></span>
                            <span class="review-date"><?= format_date($review['created_at']) ?></span>
                        </div>
                        <p><?= e($review['comment']) ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- Formulário de Consulta -->
                <div class="trip-contact-form">
                    <h3>Você pode enviar sua consulta através do formulário abaixo.</h3>
                    <p class="trip-contact-trip-name">Nome da viagem: * <strong><?= e($trip['title']) ?></strong></p>

                    <form method="POST" action="/contato" class="trip-inquiry-form">
                        <?= csrf_field() ?>
                        <input type="hidden" name="subject" value="Consulta sobre: <?= e($trip['title']) ?>">

                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Digite Seu Nome *" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="Digite seu e-mail *" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <select name="country" class="form-control" required>
                                    <option value="">Escolha um país*</option>
                                    <option value="BR">Brasil</option>
                                    <option value="US">Estados Unidos</option>
                                    <option value="PT">Portugal</option>
                                    <option value="AR">Argentina</option>
                                    <option value="CO">Colômbia</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="tel" name="phone" class="form-control" placeholder="Insira seu número de telefone com DDD*" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <input type="number" name="adults" class="form-control" placeholder="Insira o Número de Adultos*" min="1" required>
                            </div>
                            <div class="form-group">
                                <input type="number" name="children" class="form-control" placeholder="Insira o Número de Crianças" min="0">
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="text" name="consultation_subject" class="form-control" placeholder="Assunto da Consulta">
                        </div>
                        <div class="form-group">
                            <textarea name="message" class="form-control" rows="5" placeholder="Digite sua mensagem *" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Enviar Email</button>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <aside class="trip-sidebar">
                <!-- Card de Preço + CTA -->
                <div class="trip-price-card">
                    <div class="trip-price-header">
                        <span class="price-from">De</span>
                        <span class="trip-price-value">
                            <?php
                            $basePrice = 0;
                            if (!empty($packages)) {
                                $basePrice = $packages[0]['base_price'] ?? 0;
                            }
                            echo money($basePrice);
                            ?>
                        </span>
                        <span class="price-per">/ Adulto: 18-85</span>
                    </div>
                    <a href="#booking-section" class="btn-verificar">Verificar Disponibilidade</a>
                    <p class="trip-price-help">Precisa de ajuda com a reserva? <a href="/contato">Envie-Nos Uma Mensagem</a></p>
                </div>

                <!-- Related Trips -->
                <div class="trip-related-card">
                    <h4>Related trips you might interested in</h4>
                    <?php foreach ($relatedTrips as $related): ?>
                    <a href="/passeios/<?= e($related['slug']) ?>" class="related-trip-item">
                        <div class="related-trip-img">
                            <img src="<?= e($related['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="<?= e($related['title']) ?>" loading="lazy">
                        </div>
                        <div class="related-trip-info">
                            <h5><?= e($related['title']) ?></h5>
                            <span class="related-trip-location">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                Punta Cana
                            </span>
                            <span class="related-trip-price"><?php
                                $rpkg = (new \App\Models\TripPackage())->getByTrip((int)$related['id']);
                                $rprice = !empty($rpkg) ? (new \App\Models\TripPackage())->getBasePrice((int)$rpkg[0]['id']) : 0;
                                echo money($rprice);
                            ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Featured Trips -->
                <div class="trip-related-card">
                    <h4>Featured Trips</h4>
                    <?php foreach (array_slice($relatedTrips, 0, 3) as $ft): ?>
                    <a href="/passeios/<?= e($ft['slug']) ?>" class="related-trip-item related-trip-featured">
                        <div class="related-trip-img">
                            <img src="<?= e($ft['featured_image'] ?? '/assets/images/placeholder.jpg') ?>" alt="" loading="lazy">
                        </div>
                        <div class="related-trip-info">
                            <h5><?= e($ft['title']) ?></h5>
                            <span class="related-trip-location">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                Punta Cana
                            </span>
                            <span class="related-trip-duration">
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                <?= e($ft['duration'] ?? '4') ?> Horas
                            </span>
                            <div class="related-trip-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
    </div>
</section>

<script>
const TRIP_ID = <?= (int)$trip['id'] ?>;
const PACKAGES = <?= json_encode($packages) ?>;

// Trip Tabs
document.querySelectorAll('.trip-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.trip-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.trip-tab-content').forEach(c => c.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('tab-' + tab.dataset.tab)?.classList.add('active');
    });
});
</script>

<?= partial('modals/booking-modal') ?>
