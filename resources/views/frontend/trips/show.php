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
                        <?php if ($trip['short_description']): ?>
                        <?= nl2br(e($trip['short_description'])) ?>
                        <?php endif; ?>

                        <?php if ($trip['description']): ?>
                        <?= nl2br(e($trip['description'])) ?>
                        <?php endif; ?>

                        <?php
                        // Texto padrão de visão geral — exibir apenas se não estiver já na descrição
                        $defaultOverviewText = 'Prepare-se para uma aventura emocionante sobre quatro rodas pelas estradas e trilhas da região de Macao';
                        $hasDefaultText = (
                            str_contains($trip['short_description'] ?? '', $defaultOverviewText) ||
                            str_contains($trip['description'] ?? '', $defaultOverviewText)
                        );
                        if (!$hasDefaultText):
                        ?>
                        <p style="margin-top:20px;">Prepare-se para uma aventura emocionante sobre quatro rodas pelas estradas e trilhas da região de Macao, em Punta Cana! Com paisagens de tirar o fôlego, lama, cavernas escondidas e elementos culturais. O passeio oferece uma experiência vibrante para quem busca adrenalina e diversão. Você poderá nadar em um cenote de águas cristalinas e visitar a Vila Taína para uma imersão na cultura local.</p>
                        <p>A ordem do passeio poderá ser alterada sem aviso prévio.</p>
                        <p>Gestantes não são permitidas.</p>
                        <p style="margin-top:16px;font-weight:600;">O que levar:</p>
                        <ul style="padding-left:20px;margin-top:8px;line-height:2;">
                            <li>Roupas e calçados confortáveis</li>
                            <li>Roupa de praia</li>
                            <li>Óculos de sol e bandana</li>
                            <li>Protetor solar</li>
                            <li>Repelente</li>
                            <li>Roupa de banho</li>
                            <li>Toalha de banho</li>
                            <li>Dinheiro para fotos Explore as estradas de Macao.</li>
                        </ul>
                        <?php endif; ?>
                    </div>

                    <!-- Notas Importantes / Avisos -->
                    <?php if (!empty($trip['important_notes'])): ?>
                    <div class="trip-section">
                        <div class="trip-body-content">
                            <?= nl2br(e($trip['important_notes'])) ?>
                        </div>
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

                    <!-- Documentos Extras -->
                    <?php
                    $tripDocuments = !empty($trip['documents']) ? json_decode($trip['documents'], true) : [];
                    ?>
                    <?php if (!empty($tripDocuments)): ?>
                    <div class="trip-section">
                        <h3>Documentos Importantes</h3>
                        <p style="font-size:14px;color:#636e72;margin-bottom:14px;">Documentos necessários para este passeio. Baixe e leia antes da data reservada.</p>
                        <div style="display:flex;flex-direction:column;gap:10px;">
                            <?php foreach ($tripDocuments as $doc): ?>
                            <a href="<?= e($doc['path']) ?>" target="_blank" download style="display:flex;align-items:center;gap:12px;background:#f8faf8;border:1px solid #e8f0e8;border-radius:10px;padding:14px 18px;text-decoration:none;transition:border-color .2s;">
                                <span style="width:38px;height:38px;border-radius:8px;background:#e8f5e9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                                    <?php if (($doc['type'] ?? '') === 'pdf'): ?>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1B6F00" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                                    <?php else: ?>
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#1B6F00" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                    <?php endif; ?>
                                </span>
                                <span style="flex:1;overflow:hidden;">
                                    <strong style="display:block;font-size:14px;color:#1C2011;margin-bottom:2px;"><?= e($doc['name'] ?? 'Documento') ?></strong>
                                    <span style="font-size:12px;color:#636e72;text-transform:uppercase;"><?= e(strtoupper($doc['type'] ?? 'FILE')) ?> • <?= $doc['size'] ? number_format($doc['size'] / 1024, 0) . ' KB' : '' ?></span>
                                </span>
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#1B6F00" stroke-width="2" style="flex-shrink:0;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($trip['companion_enabled']) && !empty($trip['companion_description'])): ?>
                    <!-- Regras de Acompanhantes -->
                    <div class="trip-section" style="margin-top:24px;">
                        <h3 style="font-size:1.1rem;margin-bottom:12px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:6px;"><path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>
                            Regras de <?= e($trip['companion_label'] ?? 'Acompanhante') ?>
                        </h3>
                        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:16px 20px;">
                            <p style="font-size:14px;color:#334155;margin:0;line-height:1.6;"><?= nl2br(e($trip['companion_description'])) ?></p>
                            <?php if (!empty($trip['companion_price'])): ?>
                            <div style="margin-top:10px;padding-top:10px;border-top:1px solid #e2e8f0;display:flex;align-items:center;gap:8px;">
                                <span style="font-size:13px;color:#475569;">Valor por acompanhante:</span>
                                <span style="font-size:15px;font-weight:700;color:#059669;"><?= money((float)$trip['companion_price']) ?></span>
                            </div>
                            <?php endif; ?>
                            <?php if (!empty($trip['companion_max_per_participant'])): ?>
                            <p style="font-size:12px;color:#6b7280;margin:6px 0 0;">Máximo de <?= (int)$trip['companion_max_per_participant'] ?> acompanhante<?= (int)$trip['companion_max_per_participant'] > 1 ? 's' : '' ?> por participante.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($trip['youtube_url'])): ?>
                    <!-- Vídeo do Passeio -->
                    <div style="margin-top:24px;">
                        <h3 style="font-size:1.1rem;margin-bottom:12px;">Vídeo do Passeio</h3>
                        <?php
                        $ytUrl = $trip['youtube_url'];
                        $ytId = '';
                        if (preg_match('/[?&]v=([^&]+)/', $ytUrl, $m)) $ytId = $m[1];
                        elseif (preg_match('/youtu\.be\/([^?]+)/', $ytUrl, $m)) $ytId = $m[1];
                        elseif (preg_match('/embed\/([^?]+)/', $ytUrl, $m)) $ytId = $m[1];
                        ?>
                        <?php if ($ytId): ?>
                        <div style="position:relative;padding-bottom:56.25%;height:0;overflow:hidden;border-radius:10px;">
                            <iframe src="https://www.youtube.com/embed/<?= e($ytId) ?>" style="position:absolute;top:0;left:0;width:100%;height:100%;border:none;" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen loading="lazy"></iframe>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tab: Custo -->
                <div class="trip-tab-content" id="tab-custo">
                    <h2>Inclui / Exclui</h2>

                    <?php
                    $displayIncludes = !empty($includes) ? $includes : ['Transporte ida e volta do hotel', 'Guia em portugues', 'Equipamentos inclusos'];
                    $displayExcludes = !empty($excludes) ? $excludes : ['Fotos profissionais', 'Gorjetas (opcional)', 'Itens pessoais'];
                    ?>

                    <div class="trip-section">
                        <h3>Inclui</h3>
                        <ul class="trip-check-list">
                            <?php foreach ($displayIncludes as $item): ?>
                            <li><span class="check-icon">&#10003;</span> <?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="trip-section">
                        <h3>Não Inclui</h3>
                        <ul class="trip-x-list">
                            <?php foreach ($displayExcludes as $item): ?>
                            <li><span class="x-icon">&#10007;</span> <?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <?php foreach ($packages as $pkg): ?>
                    <div class="trip-package-card">
                        <h4><?= e($pkg['title']) ?></h4>
                        <?php if (!empty($trip['group_pricing_enabled']) && !empty($trip['group_pricing'])): ?>
                        <?php $gpRulesDisplay = json_decode($trip['group_pricing'], true); ?>
                        <?php if (is_array($gpRulesDisplay) && !empty($gpRulesDisplay)): ?>
                        <?php usort($gpRulesDisplay, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0)); ?>
                        <h5 style="margin-bottom:8px;font-size:14px;color:#1e40af;">Preço por Grupo (Adultos)</h5>
                        <table class="table">
                            <thead><tr><th>Adultos</th><th>Preço Total</th></tr></thead>
                            <tbody>
                            <?php foreach ($gpRulesDisplay as $gpRule): ?>
                            <tr>
                                <td><?= (int)$gpRule['pax'] ?> adulto<?= (int)$gpRule['pax'] > 1 ? 's' : '' ?></td>
                                <td><strong><?= money((float)$gpRule['price']) ?></strong></td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <p style="font-size:12px;color:#6b7280;margin-top:8px;">* Preço fixo por grupo de adultos. Crianças e infantis somam separadamente.</p>

                        <?php if (!empty($pkg['categories'])): ?>
                        <?php
                        $nonAdultCats = array_filter($pkg['categories'], fn($c) => strtolower($c['category_slug'] ?? '') !== 'adulto');
                        ?>
                        <?php if (!empty($nonAdultCats)): ?>
                        <h5 style="margin:16px 0 8px;font-size:14px;color:#475569;">Crianças / Infantis</h5>
                        <table class="table">
                            <thead><tr><th>Categoria</th><th>Idade</th><th>Preço / Pessoa</th></tr></thead>
                            <tbody>
                            <?php foreach ($nonAdultCats as $cat): ?>
                            <tr>
                                <td><?= e($cat['category_name']) ?></td>
                                <td><?= e($cat['age_group'] ?? '') ?></td>
                                <td>
                                    <?php if ($cat['sale_price']): ?>
                                    <span style="text-decoration:line-through;color:#999"><?= money((float)$cat['price']) ?></span>
                                    <strong><?= money((float)$cat['sale_price']) ?></strong>
                                    <?php else: ?>
                                    <strong><?= (float)$cat['price'] > 0 ? money((float)$cat['price']) : 'Gratuito' ?></strong>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php endif; ?>
                        <?php elseif (!empty($pkg['categories'])): ?>
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
                                <input type="tel" name="phone" class="form-control" placeholder="DDD + Número" required data-phone-country>
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
                            $priceLabel = '/ Adulto';
                            if (!empty($trip['group_pricing_enabled']) && !empty($trip['group_pricing']) && empty($trip['composition_pricing_enabled'])) {
                                // Group pricing ativo (sem composition): usar preço da primeira faixa
                                $gpRules = json_decode($trip['group_pricing'], true);
                                if (is_array($gpRules) && !empty($gpRules)) {
                                    usort($gpRules, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0));
                                    $basePrice = (float) $gpRules[0]['price'];
                                    $paxLabel = (int) $gpRules[0]['pax'];
                                    $priceLabel = $paxLabel === 1 ? '/ Pessoa' : '/ ' . $paxLabel . ' pessoas';
                                }
                            } elseif (!empty($packages)) {
                                $basePrice = $packages[0]['base_price'] ?? 0;
                            }
                            echo money($basePrice);
                            ?>
                        </span>
                        <span class="price-per"><?= $priceLabel ?></span>
                    </div>
                    <a href="#booking-section" class="btn-verificar">Verificar Disponibilidade</a>
                    <?php if (setting('videocall_enabled', '0') === '1'): ?>
                    <button type="button" class="btn-videocall" id="btnOpenVideoCall">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
                        Agendar Chamada de Vídeo
                    </button>
                    <?php endif; ?>
                    <?php if (current_user()): ?>
                    <button type="button" class="btn-wishlist-trip" id="btnWishlist" onclick="toggleWishlist(<?= (int)$trip['id'] ?>)">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="<?= !empty($inWishlist) ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        <span id="wishlistText"><?= !empty($inWishlist) ? 'Na Lista de Desejos' : 'Adicionar à Lista de Desejos' ?></span>
                    </button>
                    <script>
                    function toggleWishlist(tripId) {
                        var btn = document.getElementById('btnWishlist');
                        var svg = btn.querySelector('svg');
                        var text = document.getElementById('wishlistText');

                        fetch('/minha-conta/wishlist/toggle', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: '_token=' + document.querySelector('meta[name="csrf-token"]').content + '&trip_id=' + tripId
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.success) {
                                if (data.in_wishlist) {
                                    svg.setAttribute('fill', 'currentColor');
                                    text.textContent = 'Na Lista de Desejos';
                                    btn.classList.add('active');
                                } else {
                                    svg.setAttribute('fill', 'none');
                                    text.textContent = 'Adicionar à Lista de Desejos';
                                    btn.classList.remove('active');
                                }
                                // Update wishlist badge in header
                                var badge = document.getElementById('wishlistBadge');
                                if (badge) {
                                    fetch('/api/wishlist/count', { headers: {'X-Requested-With': 'XMLHttpRequest'} })
                                        .then(r => r.json())
                                        .then(d => {
                                            badge.textContent = d.count || '';
                                            badge.style.display = d.count > 0 ? 'flex' : 'none';
                                        });
                                }
                            }
                        });
                    }
                    </script>
                    <?php else: ?>
                    <a href="/login" class="btn-wishlist-trip">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>
                        Adicionar à Lista de Desejos
                    </a>
                    <?php endif; ?>
                    <p class="trip-price-help">Precisa de ajuda com a reserva? <a href="/contato">Envie-Nos Uma Mensagem</a></p>
                </div>

                <!-- Related Trips -->
                <div class="trip-related-card">
                    <h4>Passeios relacionados que podem te interessar</h4>
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
                                $rprice = 0;
                                if (!empty($related['group_pricing_enabled']) && !empty($related['group_pricing'])) {
                                    $rgp = json_decode($related['group_pricing'], true);
                                    if (is_array($rgp) && !empty($rgp)) {
                                        usort($rgp, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0));
                                        $rprice = (float) $rgp[0]['price'];
                                    }
                                } else {
                                    $rpkg = (new \App\Models\TripPackage())->getByTrip((int)$related['id']);
                                    $rprice = !empty($rpkg) ? (new \App\Models\TripPackage())->getBasePrice((int)$rpkg[0]['id']) : 0;
                                }
                                echo money($rprice);
                            ?></span>
                        </div>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Featured Trips -->
                <div class="trip-related-card">
                    <h4>Passeios em Destaque</h4>
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
const GROUP_PRICING_ENABLED = <?= !empty($trip['group_pricing_enabled']) ? 'true' : 'false' ?>;
const GROUP_PRICING_TABLE = <?= !empty($trip['group_pricing']) ? $trip['group_pricing'] : '[]' ?>;
const COMPOSITION_PRICING_ENABLED = <?= !empty($trip['composition_pricing_enabled']) ? 'true' : 'false' ?>;
const COMPOSITION_PACKAGES = <?= json_encode($compositionPackages ?? []) ?>;
const PARTIAL_PAYMENT_ENABLED = <?= !empty($trip['partial_payment_enabled']) ? 'true' : 'false' ?>;
const PARTIAL_PAYMENT_PERCENT = <?= (int)($trip['partial_payment_percent'] ?? 50) ?>;
const COMPANION_CONFIG = <?= json_encode([
    'enabled' => !empty($trip['companion_enabled']),
    'label' => $trip['companion_label'] ?? 'Acompanhante',
    'price' => (float)($trip['companion_price'] ?? 0),
    'max_per_participant' => $trip['companion_max_per_participant'] ? (int)$trip['companion_max_per_participant'] : null,
    'max_total' => $trip['companion_max_total'] ? (int)$trip['companion_max_total'] : null,
    'description' => $trip['companion_description'] ?? '',
]) ?>;

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

<!-- Barra fixa mobile: Preço + Botão Verificar Disponibilidade -->
<div class="trip-mobile-cta">
    <div class="trip-mobile-cta-price">
        <span class="trip-mobile-cta-from">A partir de</span>
        <span class="trip-mobile-cta-value"><?= money($basePrice) ?> <small><?= $priceLabel ?></small></span>
    </div>
    <a href="#booking-section" class="trip-mobile-cta-btn btn-verificar">Verificar Disponibilidade</a>
</div>

<?php if (setting('videocall_enabled', '0') === '1'): ?>
<!-- ============================================================ -->
<!-- Modal: Agendar Chamada de Vídeo                              -->
<!-- ============================================================ -->
<div class="vc-modal-overlay" id="vcModalOverlay" aria-hidden="true">
    <div class="vc-modal" role="dialog" aria-modal="true" aria-labelledby="vcModalTitle">
        <button type="button" class="vc-modal-close" id="vcModalClose" aria-label="Fechar">&times;</button>

        <div class="vc-modal-head">
            <div class="vc-modal-icon">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg>
            </div>
            <div>
                <h3 id="vcModalTitle">Agendar Chamada de Vídeo</h3>
                <p>Tire suas dúvidas sobre o passeio numa conversa por vídeo com a nossa equipe.</p>
            </div>
        </div>

        <!-- Formulário -->
        <form id="vcForm" class="vc-form">
            <div class="vc-field">
                <label>Nome completo *</label>
                <input type="text" name="customer_name" required>
            </div>
            <div class="vc-row">
                <div class="vc-field">
                    <label>E-mail *</label>
                    <input type="email" name="email" required>
                </div>
                <div class="vc-field">
                    <label>WhatsApp *</label>
                    <input type="tel" name="phone" placeholder="DDD + Número" required data-phone-country>
                </div>
            </div>
            <div class="vc-field">
                <label>Escolha o dia *</label>
                <input type="date" name="date" id="vcDate" required min="<?= date('Y-m-d') ?>">
            </div>
            <div class="vc-field">
                <label>Horário disponível *</label>
                <div class="vc-slots" id="vcSlots">
                    <span class="vc-slots-hint">Selecione uma data para ver os horários.</span>
                </div>
                <input type="hidden" name="time" id="vcTime" required>
            </div>
            <div class="vc-field">
                <label>Mensagem (opcional)</label>
                <textarea name="notes" rows="2" placeholder="Conte o que gostaria de saber..."></textarea>
            </div>

            <div class="vc-alert" id="vcAlert" style="display:none;"></div>

            <button type="submit" class="vc-submit" id="vcSubmit">Confirmar Agendamento</button>
        </form>

        <!-- Sucesso -->
        <div class="vc-success" id="vcSuccess" style="display:none;">
            <div class="vc-success-icon">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h3>Chamada agendada!</h3>
            <p id="vcSuccessWhen"></p>
            <p class="vc-success-note">Enviamos os detalhes e o link da reunião no seu WhatsApp e e-mail.</p>
            <a href="#" class="vc-success-link" id="vcMeetingLink" target="_blank" rel="noopener">Abrir sala da reunião</a>
            <a href="#" class="vc-success-cal" id="vcCalLink" target="_blank" rel="noopener">Adicionar ao Google Agenda</a>
        </div>
    </div>
</div>

<style>
.btn-videocall{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;margin-top:14px;margin-bottom:14px;padding:13px 16px;background:#fff;color:#1B6F00;border:2px solid #1B6F00;border-radius:10px;font-weight:600;font-size:15px;cursor:pointer;transition:all .18s}
.btn-videocall:hover{background:#E4B505;color:#1C2011;border-color:#E4B505}
.vc-modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.6);display:none;align-items:center;justify-content:center;z-index:9999;padding:16px}
.vc-modal-overlay.open{display:flex}
.vc-modal{background:#fff;border-radius:16px;max-width:520px;width:100%;max-height:92vh;overflow-y:auto;padding:28px;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.25)}
.vc-modal-close{position:absolute;top:14px;right:16px;background:none;border:none;font-size:28px;line-height:1;color:#94a3b8;cursor:pointer}
.vc-modal-close:hover{color:#334155}
.vc-modal-head{display:flex;gap:14px;align-items:flex-start;margin-bottom:22px}
.vc-modal-icon{flex-shrink:0;width:46px;height:46px;border-radius:12px;background:#dcfce7;color:#1B6F00;display:flex;align-items:center;justify-content:center}
.vc-modal-head h3{margin:0 0 4px;font-size:19px;color:#0f172a}
.vc-modal-head p{margin:0;font-size:13.5px;color:#64748b;line-height:1.4}
.vc-form .vc-field{margin-bottom:14px}
.vc-form label{display:block;font-size:13px;font-weight:600;color:#334155;margin-bottom:6px}
.vc-form input,.vc-form textarea{width:100%;padding:11px 13px;border:1.5px solid #e2e8f0;border-radius:9px;font-size:14px;font-family:inherit;box-sizing:border-box}
.vc-form input:focus,.vc-form textarea:focus{outline:none;border-color:#1B6F00}
.vc-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.vc-slots{display:flex;flex-wrap:wrap;gap:8px;min-height:40px;align-items:center}
.vc-slots-hint{font-size:13px;color:#94a3b8}
.vc-slot{padding:8px 14px;border:1.5px solid #e2e8f0;border-radius:8px;background:#fff;font-size:13.5px;cursor:pointer;transition:all .15s}
.vc-slot:hover{border-color:#1B6F00}
.vc-slot.selected{background:#1B6F00;color:#fff;border-color:#1B6F00}
.vc-alert{padding:11px 14px;border-radius:9px;font-size:13.5px;margin-bottom:14px}
.vc-alert.error{background:#fef2f2;color:#b91c1c;border:1px solid #fecaca}
.vc-submit{width:100%;padding:14px;background:#1B6F00;color:#fff;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;transition:background .18s}
.vc-submit:hover{background:#155700}
.vc-submit:disabled{opacity:.6;cursor:not-allowed}
.vc-success{text-align:center;padding:14px 0}
.vc-success-icon{width:64px;height:64px;border-radius:50%;background:#dcfce7;color:#1B6F00;display:flex;align-items:center;justify-content:center;margin:0 auto 16px}
.vc-success h3{margin:0 0 8px;color:#0f172a}
.vc-success p{margin:0 0 6px;color:#475569;font-size:14px}
.vc-success-note{font-size:13px;color:#64748b;margin-bottom:18px !important}
.vc-success-link,.vc-success-cal{display:block;padding:12px;border-radius:9px;font-weight:600;font-size:14px;text-decoration:none;margin-top:10px}
.vc-success-link{background:#1B6F00;color:#fff}
.vc-success-cal{background:#fff;color:#1B6F00;border:1.5px solid #1B6F00}
@media(max-width:480px){.vc-row{grid-template-columns:1fr}}
</style>

<script>
(function(){
    var overlay = document.getElementById('vcModalOverlay');
    var openBtn = document.getElementById('btnOpenVideoCall');
    var closeBtn = document.getElementById('vcModalClose');
    var dateInput = document.getElementById('vcDate');
    var slotsBox = document.getElementById('vcSlots');
    var timeInput = document.getElementById('vcTime');
    var form = document.getElementById('vcForm');
    var alertBox = document.getElementById('vcAlert');
    var submitBtn = document.getElementById('vcSubmit');
    var successBox = document.getElementById('vcSuccess');
    var TRIP_SLUG = <?= json_encode($trip['slug']) ?>;
    var CSRF = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').content : '';

    if (!openBtn || !overlay) return;

    function open(){
        overlay.classList.add('open');
        overlay.setAttribute('aria-hidden','false');
        // Inicializa o seletor de país (bandeira + DDI) no campo de WhatsApp
        if (typeof window.initPhoneCountrySelector === 'function') {
            window.initPhoneCountrySelector();
        }
    }
    function close(){ overlay.classList.remove('open'); overlay.setAttribute('aria-hidden','true'); }

    openBtn.addEventListener('click', open);
    closeBtn.addEventListener('click', close);
    overlay.addEventListener('click', function(e){ if(e.target === overlay) close(); });

    function showError(msg){
        alertBox.textContent = msg;
        alertBox.className = 'vc-alert error';
        alertBox.style.display = 'block';
    }
    function clearError(){ alertBox.style.display = 'none'; }

    dateInput.addEventListener('change', function(){
        timeInput.value = '';
        slotsBox.innerHTML = '<span class="vc-slots-hint">Carregando horários...</span>';
        if (!dateInput.value) { slotsBox.innerHTML = '<span class="vc-slots-hint">Selecione uma data.</span>'; return; }
        fetch('/api/videocall/slots?date=' + encodeURIComponent(dateInput.value), { headers: {'X-Requested-With':'XMLHttpRequest'} })
            .then(function(r){ return r.json(); })
            .then(function(data){
                if (!data.success || !data.slots || data.slots.length === 0){
                    slotsBox.innerHTML = '<span class="vc-slots-hint">Nenhum horário disponível nesta data.</span>';
                    return;
                }
                slotsBox.innerHTML = '';
                data.slots.forEach(function(slot){
                    var b = document.createElement('button');
                    b.type = 'button';
                    b.className = 'vc-slot';
                    b.textContent = slot;
                    b.addEventListener('click', function(){
                        slotsBox.querySelectorAll('.vc-slot').forEach(function(s){ s.classList.remove('selected'); });
                        b.classList.add('selected');
                        timeInput.value = slot;
                    });
                    slotsBox.appendChild(b);
                });
            })
            .catch(function(){ slotsBox.innerHTML = '<span class="vc-slots-hint">Erro ao carregar horários.</span>'; });
    });

    form.addEventListener('submit', function(e){
        e.preventDefault();
        clearError();
        if (!timeInput.value){ showError('Selecione um horário disponível.'); return; }

        // O seletor de país move o name="phone" para um input hidden com o valor "+DDI numero".
        // Por isso capturamos via querySelector (pega o hidden), com fallback para o visível.
        var phoneEl = form.querySelector('[name="phone"]');
        var phoneVal = phoneEl ? phoneEl.value : (form.phone ? form.phone.value : '');

        var fd = new URLSearchParams();
        fd.append('_token', CSRF);
        fd.append('customer_name', form.customer_name.value);
        fd.append('email', form.email.value);
        fd.append('phone', phoneVal);
        fd.append('date', dateInput.value);
        fd.append('time', timeInput.value);
        fd.append('notes', form.notes.value);

        submitBtn.disabled = true;
        submitBtn.textContent = 'Agendando...';

        fetch('/passeios/' + TRIP_SLUG + '/agendar-chamada', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
            body: fd.toString()
        })
        .then(function(r){ return r.json(); })
        .then(function(data){
            submitBtn.disabled = false;
            submitBtn.textContent = 'Confirmar Agendamento';
            if (!data.success){ showError(data.message || 'Não foi possível agendar.'); return; }
            form.style.display = 'none';
            document.getElementById('vcSuccessWhen').textContent = 'Sua chamada está marcada para ' + data.scheduled_at + '.';
            document.getElementById('vcMeetingLink').href = data.meeting_link;
            var cal = document.getElementById('vcCalLink');
            if (data.add_to_calendar){ cal.href = data.add_to_calendar; } else { cal.style.display = 'none'; }
            successBox.style.display = 'block';
        })
        .catch(function(){
            submitBtn.disabled = false;
            submitBtn.textContent = 'Confirmar Agendamento';
            showError('Erro de conexão. Tente novamente.');
        });
    });
})();
</script>
<?php endif; ?>
