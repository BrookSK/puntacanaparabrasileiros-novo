<?php
// Monta a lista de imagens da galeria: imagem destacada + galeria.
$galleryImages = [];
if (!empty($trip['featured_image'])) {
    $galleryImages[] = $trip['featured_image'];
}
if (!empty($gallery)) {
    foreach ($gallery as $g) {
        if (!empty($g)) $galleryImages[] = $g;
    }
}
if (empty($galleryImages)) {
    $galleryImages[] = '/assets/images/placeholder.jpg';
}
// Primeira imagem = destaque (grande); próximas = miniaturas laterais.
$heroImage = $galleryImages[0];
$sideImages = array_slice($galleryImages, 1, 2);
$extraCount = max(0, count($galleryImages) - 3); // fotos além das 3 visíveis
$galleryCount = count($galleryImages);

// ── Pré-cálculos de preços (usados no card lateral, nos cards de preço e na barra mobile) ──
$basePrice = 0;
$priceLabel = '/ Adulto';
$isGroupPricing = (!empty($trip['group_pricing_enabled']) && !empty($trip['group_pricing']) && empty($trip['composition_pricing_enabled']));
$priceCategories = [];

if ($isGroupPricing) {
    $gpRules = json_decode($trip['group_pricing'], true);
    if (is_array($gpRules) && !empty($gpRules)) {
        usort($gpRules, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0));
        $basePrice = (float) $gpRules[0]['price'];
        $paxLabel = (int) $gpRules[0]['pax'];
        $priceLabel = $paxLabel === 1 ? '/ Pessoa' : '/ ' . $paxLabel . ' pessoas';
    }
} elseif (!empty($packages)) {
    $basePrice = $packages[0]['base_price'] ?? 0;
    $priceCategories = $packages[0]['categories'] ?? [];
}

// Avaliações
$reviewCountDisplay = is_array($reviews) ? count($reviews) : 0;
$ratingDisplay = (float) ($rating ?? 0);
?>

<!-- ============================================================ -->
<!-- GALERIA EM GRADE                                             -->
<!-- ============================================================ -->
<section class="trip-gallery-section">
    <div class="container">
        <div class="trip-gallery-grid" id="tripSlider">
            <!-- Foto principal -->
            <div class="trip-gallery-main">
                <img src="<?= e($heroImage) ?>" alt="<?= e($trip['title']) ?>" loading="eager">
            </div>

            <!-- Fotos laterais -->
            <div class="trip-gallery-side">
                <?php for ($i = 0; $i < 2; $i++): ?>
                <?php if (!empty($sideImages[$i])): ?>
                <div class="trip-gallery-thumb">
                    <img src="<?= e($sideImages[$i]) ?>" alt="<?= e($trip['title']) ?>" loading="lazy">
                    <?php if ($i === 1 && $extraCount > 0): ?>
                    <button type="button" class="trip-gallery-more" id="galleryBtn">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        Galeria &middot; +<?= $extraCount ?> fotos
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                <?php endfor; ?>
            </div>

            <!-- Fonte de imagens para o lightbox (lida pelo JS; nunca exibida). -->
            <div class="trip-lightbox-source" id="tripSliderTrack" aria-hidden="true" style="display:none !important;">
                <?php foreach ($galleryImages as $img): ?>
                <div class="trip-slide"><img src="<?= e($img) ?>" alt="<?= e($trip['title']) ?>"></div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (!empty($googleReviews)): ?>
        <!-- Barra de avaliações reais do Google (item 5) — abre modal ao clicar -->
        <?php $grRating = (float) $googleReviews['rating']; $grCount = (int) $googleReviews['count']; ?>
        <button type="button" class="trip-rating-bar trip-rating-bar--btn" id="btnGoogleReviews" title="Ver avaliações do Google">
            <span class="trip-rating-stars">
                <?php for ($s = 1; $s <= 5; $s++): ?><?= $s <= round($grRating) ? '&#9733;' : '&#9734;' ?><?php endfor; ?>
            </span>
            <span class="trip-rating-text">Avaliações reais de clientes no Google <?php if ($grCount > 0): ?><strong>(<?= $grCount ?>)</strong><?php endif; ?></span>
            <?php if ($grRating > 0): ?><span class="trip-rating-value"><?= number_format($grRating, 1, ',', '') ?></span><?php endif; ?>
            <svg class="trip-rating-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
        </button>
        <?php endif; ?>
    </div>
</section>

<!-- ============================================================ -->
<!-- CONTEÚDO                                                     -->
<!-- ============================================================ -->
<section class="trip-detail">
    <div class="container">
        <div class="trip-content-grid">
            <!-- ========================= MAIN ========================= -->
            <div class="trip-main">
                <!-- Título + Badge duração -->
                <div class="trip-title-row">
                    <h1 class="trip-title"><?= e($trip['title']) ?></h1>
                    <?php if ($trip['duration']): ?>
                    <div class="trip-duration-badge">
                        <span class="duration-number"><?= e($trip['duration']) ?></span>
                        <span class="duration-unit"><?= $trip['duration_unit'] === 'hours' ? 'horas' : 'dias' ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if (!empty($trip['short_description'])): ?>
                <p class="trip-lead"><?= nl2br(e($trip['short_description'])) ?></p>
                <?php endif; ?>

                <?php
                // ── Cards de destaque com ícones (DETALHES DO PASSEIO) ──
                // Fonte: campo próprio "detail_highlights", cadastrado no painel.
                // NÃO reaproveita o Roteiro (item 1 da validação do cliente): são
                // conteúdos independentes. Sem detalhes cadastrados, a área some.
                $highlightItems = [];
                $detailHighlights = !empty($trip['detail_highlights']) ? json_decode($trip['detail_highlights'], true) : [];
                if (is_array($detailHighlights)) {
                    foreach ($detailHighlights as $dh) {
                        $dh = trim((string) $dh);
                        if ($dh !== '') $highlightItems[] = $dh;
                    }
                }
                $highlightItems = array_slice($highlightItems, 0, 8);

                // Mapeia uma palavra-chave do texto para um ícone SVG (acento verde).
                $highlightIcon = function (string $text): string {
                    $t = mb_strtolower($text, 'UTF-8');
                    $has = fn(array $words) => (bool) array_filter($words, fn($w) => mb_strpos($t, $w) !== false);
                    if ($has(['brasil', 'brasileiro', 'português', 'portugues'])) {
                        return '<path d="M2 12h20"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/><circle cx="12" cy="12" r="10"/>';
                    }
                    if ($has(['guia', 'guide'])) {
                        return '<path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>';
                    }
                    if ($has(['transfer', 'transporte', 'traslado', 'ônibus', 'onibus', 'van'])) {
                        return '<rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>';
                    }
                    if ($has(['almoço', 'almoco', 'buffet', 'comida', 'refeição', 'refeicao', 'jantar'])) {
                        return '<path d="M3 2v7c0 1.1.9 2 2 2h0a2 2 0 002-2V2"/><path d="M7 2v20"/><path d="M21 15V2a5 5 0 00-5 5v6c0 1.1.9 2 2 2h3z"/>';
                    }
                    if ($has(['open bar', 'bar', 'bebida', 'drink', 'espumante', 'brinde'])) {
                        return '<path d="M8 22h8"/><path d="M12 11v11"/><path d="M5 3h14l-1 7a6 6 0 01-12 0z"/>';
                    }
                    if ($has(['praia', 'piscina', 'natural', 'mar', 'ilha', 'saona', 'catamarã', 'catamara', 'lancha', 'barco'])) {
                        return '<path d="M2 20a6 6 0 006-6 6 6 0 006 6 6 6 0 006-6"/><path d="M12 2v10"/><path d="M12 2l4 4-4 2-4-2z"/>';
                    }
                    if ($has(['estrela', 'star', 'premium', 'vip', 'exclusiv'])) {
                        return '<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>';
                    }
                    return '<path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>';
                };
                ?>
                <?php if (!empty($highlightItems)): ?>
                <div class="trip-highlights-grid">
                    <?php foreach ($highlightItems as $hl): ?>
                    <div class="trip-highlight-card">
                        <span class="trip-highlight-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><?= $highlightIcon($hl) ?></svg>
                        </span>
                        <span class="trip-highlight-text"><?= e($hl) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <!-- ===================== NAV DE ÂNCORAS (abas sticky) ===================== -->
                <div class="trip-tabs" id="tripTabs">
                    <nav aria-label="Seções do passeio">
                        <a href="#sec-visao" class="trip-tab active">Visão Geral</a>
                        <?php if (!empty($itinerary)): ?><a href="#sec-roteiro" class="trip-tab">Roteiro</a><?php endif; ?>
                        <a href="#sec-precos" class="trip-tab">Preços e Datas</a>
                        <?php if (!empty($reviews) || !empty($trip['youtube_url']) || !empty($trip['documents'])): ?><a href="#sec-confianca" class="trip-tab">Quem já foi</a><?php endif; ?>
                        <?php if (!empty($tripFaqs)): ?><a href="#sec-faqs" class="trip-tab">FAQ</a><?php endif; ?>
                    </nav>
                </div>

                <!-- ===================== SEÇÃO: VISÃO GERAL ===================== -->
                <section class="trip-sec" id="sec-visao">
                    <h2>Visão Geral</h2>
                    <div class="trip-block">
                        <div class="trip-body-content">
                            <?php if (!empty($trip['description'])): ?>
                            <?= nl2br(e($trip['description'])) ?>
                            <?php elseif (!empty($trip['short_description'])): ?>
                            <?= nl2br(e($trip['short_description'])) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php
                        $idealForList = !empty($trip['ideal_for']) ? json_decode($trip['ideal_for'], true) : [];
                        $idealForList = is_array($idealForList) ? array_values(array_filter($idealForList)) : [];
                    ?>
                    <?php if (!empty($idealForList)): ?>
                    <div class="trip-block">
                        <h3>Ideal para</h3>
                        <div class="trip-ideal">
                            <?php foreach ($idealForList as $if): ?>
                            <span class="trip-chip"><?= e($if) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php
                        $displayIncludes = !empty($includes) ? $includes : [];
                        $displayExcludes = !empty($excludes) ? $excludes : [];
                    ?>
                    <?php if (!empty($displayIncludes) || !empty($displayExcludes)): ?>
                    <div class="trip-two-col">
                        <?php if (!empty($displayIncludes)): ?>
                        <div class="trip-block">
                            <h3>O que inclui</h3>
                            <ul class="trip-check-list">
                                <?php foreach ($displayIncludes as $item): ?>
                                <li><?= e($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($displayExcludes)): ?>
                        <div class="trip-block">
                            <h3>Não inclui</h3>
                            <ul class="trip-check-list trip-no-list">
                                <?php foreach ($displayExcludes as $item): ?>
                                <li><?= e($item) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($whatToBring)): ?>
                    <!-- O que levar — lista individual deste passeio (item 4) -->
                    <div class="trip-block trip-what-to-bring">
                        <h3>O que levar</h3>
                        <ul class="trip-check-list trip-bring-list">
                            <?php foreach ($whatToBring as $item): ?>
                            <li><?= e($item) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($trip['important_notes'])): ?>
                    <div class="trip-block trip-notice">
                        <h3>Informações importantes</h3>
                        <div class="trip-body-content"><?= nl2br(e($trip['important_notes'])) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($trip['meeting_point'])): ?>
                    <div class="trip-block">
                        <h3>Ponto de encontro e transporte</h3>
                        <div class="trip-body-content"><?= nl2br(e($trip['meeting_point'])) ?></div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($trip['companion_enabled']) && !empty($trip['companion_description'])): ?>
                    <div class="trip-block">
                        <h3>Regras de <?= e($trip['companion_label'] ?? 'Acompanhante') ?></h3>
                        <div class="trip-body-content"><?= nl2br(e($trip['companion_description'])) ?></div>
                        <?php if (!empty($trip['companion_price'])): ?>
                        <p class="trip-companion-price">Valor por acompanhante: <strong><?= money((float)$trip['companion_price']) ?></strong></p>
                        <?php endif; ?>
                        <?php if (!empty($trip['companion_max_per_participant'])): ?>
                        <p class="trip-small">Máximo de <?= (int)$trip['companion_max_per_participant'] ?> acompanhante<?= (int)$trip['companion_max_per_participant'] > 1 ? 's' : '' ?> por participante.</p>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </section>

                <!-- ===================== SEÇÃO: ROTEIRO ===================== -->
                <?php if (!empty($itinerary)): ?>
                <section class="trip-sec" id="sec-roteiro">
                    <h2>Roteiro</h2>
                    <ol class="trip-timeline">
                        <?php foreach ($itinerary as $index => $step): ?>
                        <?php
                            // Horário da etapa: prioriza o campo dedicado step_time (painel).
                            // Fallback: extrai do início da descrição ("7h00 – 8h00 | ..." ou "~35 min — ...").
                            $stepDesc = (string) ($step['description'] ?? '');
                            $stepTime = '';
                            if (!empty($step['step_time'])) {
                                $stepTime = substr((string) $step['step_time'], 0, 5);
                            } elseif (preg_match('/^\s*([~\d][^|–—\-\n]{0,18}?(?:min|h\d*0?|hora[s]?|h))\s*[|–—-]\s*(.*)$/isu', $stepDesc, $mt)) {
                                $stepTime = trim($mt[1]);
                                $stepDesc = trim($mt[2]);
                            }
                        ?>
                        <li>
                            <span class="trip-tl-n"><?= $index + 1 ?></span>
                            <div class="trip-tl-card">
                                <div class="trip-tl-head">
                                    <b><?= e($step['title']) ?></b>
                                    <?php if ($stepTime !== ''): ?><span class="trip-tl-t"><?= e($stepTime) ?></span><?php endif; ?>
                                </div>
                                <?php if ($stepDesc !== ''): ?>
                                <p><?= nl2br(e($stepDesc)) ?></p>
                                <?php endif; ?>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ol>
                    <p class="trip-note">A ordem das paradas pode ser alterada por condições climáticas ou operacionais.</p>
                </section>
                <?php endif; ?>

                <!-- ===================== SEÇÃO: PREÇOS E DATAS ===================== -->
                <section class="trip-sec" id="sec-precos">
                    <h2>Preços e Datas</h2>

                        <?php if ($isGroupPricing): ?>
                        <!-- Preço por grupo -->
                        <?php $gpRulesDisplay = json_decode($trip['group_pricing'], true); ?>
                        <?php if (is_array($gpRulesDisplay) && !empty($gpRulesDisplay)): ?>
                        <?php usort($gpRulesDisplay, fn($a, $b) => (int)($a['pax'] ?? 0) - (int)($b['pax'] ?? 0)); ?>
                        <div class="trip-price-cards">
                            <?php foreach ($gpRulesDisplay as $i => $gpRule): ?>
                            <div class="trip-pc<?= $i === 0 ? ' trip-pc-main' : '' ?>">
                                <span class="trip-pc-lbl"><?= (int)$gpRule['pax'] ?> adulto<?= (int)$gpRule['pax'] > 1 ? 's' : '' ?></span>
                                <small>Preço fechado por grupo</small>
                                <div class="trip-pc-v"><?= money((float)$gpRule['price']) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <p class="trip-small">* Preço fixo por grupo de adultos. Crianças e infantis somam separadamente.</p>
                        <?php endif; ?>
                        <?php elseif (!empty($priceCategories)): ?>
                        <!-- Cards de preço por categoria -->
                        <div class="trip-price-cards">
                            <?php foreach ($priceCategories as $i => $cat): ?>
                            <?php
                                $catRegular = (float) ($cat['price'] ?? 0);
                                $catSale = isset($cat['sale_price']) && $cat['sale_price'] !== null && $cat['sale_price'] !== '' ? (float) $cat['sale_price'] : null;
                                $catFinal = ($catSale !== null && $catSale > 0) ? $catSale : $catRegular;
                                $isFree = $catFinal <= 0;
                            ?>
                            <div class="trip-pc<?= $i === 0 ? ' trip-pc-main' : '' ?>">
                                <span class="trip-pc-lbl"><?= e($cat['category_name'] ?? 'Categoria') ?></span>
                                <?php if (!empty($cat['age_group'])): ?><small><?= e($cat['age_group']) ?></small><?php endif; ?>
                                <?php if ($isFree): ?>
                                <div class="trip-pc-v trip-pc-free">Cortesia</div>
                                <?php elseif ($catSale !== null && $catSale > 0 && $catSale < $catRegular): ?>
                                <div class="trip-pc-v"><span class="trip-pc-old"><?= money($catRegular) ?></span><?= money($catFinal) ?></div>
                                <?php else: ?>
                                <div class="trip-pc-v"><?= money($catFinal) ?></div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <?php endif; ?>

                        <!-- Grade de informações (facts) -->
                        <?php
                            // Dias de funcionamento: derivados das datas fixas futuras (0=Dom ... 6=Sáb).
                            $weekLabels = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                            $activeDows = [];
                            if (!empty($fixedDates)) {
                                foreach ($fixedDates as $fd) {
                                    if (($fd['status'] ?? '') === 'available' && !empty($fd['date'])) {
                                        $activeDows[(int) date('w', strtotime($fd['date']))] = true;
                                    }
                                }
                            }
                        ?>
                        <?php
                            // Valores variáveis (com fallback para o padrão quando não houver dado cadastrado).
                            $capacityInfo = '';
                            if (!empty($trip['max_pax'])) {
                                $capacityInfo = 'Até ' . (int) $trip['max_pax'] . ' pessoas';
                            } elseif (!empty($trip['min_pax'])) {
                                $capacityInfo = 'A partir de ' . (int) $trip['min_pax'] . ' pessoa(s)';
                            } else {
                                $capacityInfo = 'Grupos maiores: consulte nossa equipe';
                            }
                            // Faixa de horário (item 2): prioriza os campos estruturados De/Até.
                            // Fallback para o texto legado (departure_time_info) quando não houver faixa.
                            $formatWindow = function (?string $start, ?string $end): string {
                                $s = $start ? substr($start, 0, 5) : '';
                                $e = $end ? substr($end, 0, 5) : '';
                                if ($s !== '' && $e !== '') return $s . ' às ' . $e;
                                if ($s !== '') return $s;
                                if ($e !== '') return $e;
                                return '';
                            };
                            $departureWindow = $formatWindow($trip['departure_time_start'] ?? '', $trip['departure_time_end'] ?? '');
                            $returnWindow = $formatWindow($trip['return_time_start'] ?? '', $trip['return_time_end'] ?? '');
                            $departureInfo = $departureWindow !== '' ? $departureWindow : (!empty($trip['departure_time_info']) ? $trip['departure_time_info'] : 'Consulte nossa equipe');
                            $returnInfo = $returnWindow !== '' ? $returnWindow : (!empty($trip['return_time_info']) ? $trip['return_time_info'] : 'Consulte nossa equipe');
                            $availabilityInfo = !empty($trip['availability_info']) ? $trip['availability_info'] : 'Sujeita a quórum, capacidade e clima';
                        ?>
                        <div class="trip-facts">
                            <div><small>Dias de funcionamento</small>
                                <?php if (!empty($activeDows)): ?>
                                <div class="trip-days">
                                    <?php for ($d = 1; $d <= 6; $d++): ?><em class="<?= isset($activeDows[$d]) ? 'on' : '' ?>"><?= $weekLabels[$d] ?></em><?php endfor; ?>
                                    <em class="<?= isset($activeDows[0]) ? 'on' : '' ?>"><?= $weekLabels[0] ?></em>
                                </div>
                                <?php else: ?>
                                <span>Consulte as datas disponíveis</span>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($trip['duration'])): ?>
                            <div><small>Duração</small><span>Aprox. <?= e($trip['duration']) ?> <?= $trip['duration_unit'] === 'hours' ? 'horas' : 'dias' ?></span></div>
                            <?php endif; ?>
                            <div><small>Saída do hotel</small><span><?= e($departureInfo) ?></span></div>
                            <div><small>Retorno ao hotel</small><span><?= e($returnInfo) ?></span></div>
                            <div><small>Capacidade</small><span><?= e($capacityInfo) ?></span></div>
                            <div><small>Disponibilidade</small><span><?= e($availabilityInfo) ?></span></div>
                            <?php if (!empty($fixedDates)): ?>
                            <div><small>Próximas saídas</small><span><?= count($fixedDates) ?> data<?= count($fixedDates) > 1 ? 's' : '' ?> disponível<?= count($fixedDates) > 1 ? 'eis' : '' ?></span></div>
                            <?php endif; ?>
                        </div>

                        <!-- Datas fixas -->
                        <?php if (!empty($fixedDates)): ?>
                        <h3 class="trip-subsec">Datas disponíveis</h3>
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
                        <p class="trip-small">Disponível durante todo o ano. Selecione a data desejada no momento da reserva.</p>
                        <?php endif; ?>

                        <!-- Pacotes detalhados (quando houver tabela de categorias) -->
                        <?php foreach ($packages as $pkg): ?>
                        <?php if (!empty($pkg['categories']) && !$isGroupPricing): ?>
                        <div class="trip-package-card">
                            <h4><?= e($pkg['title']) ?></h4>
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
                                        <strong><?= (float)$cat['price'] > 0 ? money((float)$cat['price']) : 'Cortesia' ?></strong>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (!empty($extraServices)): ?>
                        <h3 class="trip-subsec">Serviços extras</h3>
                        <ul class="trip-check-list">
                            <?php foreach ($extraServices as $svc): ?>
                            <li><?= e($svc['name']) ?> — <?= money((float)$svc['price']) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>

                        <!-- Accordions: pagamento e cancelamento -->
                        <div class="trip-accordions">
                            <details class="trip-detail-acc">
                                <summary>Como reservar e pagar</summary>
                                <div class="trip-acc-body">
                                    <?php if (!empty($trip['partial_payment_enabled'])): ?>
                                    <?php $signalPct = (int)($trip['partial_payment_percent'] ?? 50); ?>
                                    <p>Reserve com <b><?= $signalPct ?>% de sinal via Pix, em reais</b>, e envie o comprovante. Pague os <?= 100 - $signalPct ?>% restantes no primeiro contato presencial com nossa equipe no destino.</p>
                                    <?php else: ?>
                                    <p>Reserve com nossa equipe e confirme o pagamento conforme as instruções enviadas. A reserva é confirmada após o envio do comprovante.</p>
                                    <?php endif; ?>
                                    <p class="trip-small">A reserva é confirmada após o sinal e o envio do comprovante. Em caso de dúvida, fale com nossa equipe pelo WhatsApp.</p>
                                </div>
                            </details>
                            <details class="trip-detail-acc">
                                <summary>Política de cancelamento</summary>
                                <div class="trip-acc-body">
                                    <div class="trip-cancel">
                                        <div><span class="trip-cx ok">100%</span><p>Reembolso integral cancelando com <b>mais de 48h</b> de antecedência.</p></div>
                                        <div><span class="trip-cx mid">50%</span><p>Reembolso cancelando <b>entre 48h e 24h</b> antes do passeio.</p></div>
                                        <div><span class="trip-cx bad">0%</span><p><b>Menos de 24h</b> ou não comparecimento (no show).</p></div>
                                    </div>
                                    <ul class="trip-check-list" style="margin-top:14px">
                                        <li>Troca de data gratuita com pelo menos 24h de antecedência, sujeita à disponibilidade.</li>
                                        <li>Se cancelarmos por clima, operação ou segurança, você escolhe: reagendar ou reembolso total.</li>
                                        <li>Reembolso pelo mesmo método de pagamento, em 3 a 10 dias úteis.</li>
                                    </ul>
                                </div>
                            </details>
                        </div>
                </section>

                <!-- ===================== SEÇÃO: QUEM JÁ FOI E QUEM LEVA VOCÊ ===================== -->
                <?php
                $tripDocuments = !empty($trip['documents']) ? json_decode($trip['documents'], true) : [];
                $ytId = '';
                if (!empty($trip['youtube_url'])) {
                    $ytUrl = $trip['youtube_url'];
                    if (preg_match('/[?&]v=([^&]+)/', $ytUrl, $m)) $ytId = $m[1];
                    elseif (preg_match('/youtu\.be\/([^?]+)/', $ytUrl, $m)) $ytId = $m[1];
                    elseif (preg_match('/embed\/([^?]+)/', $ytUrl, $m)) $ytId = $m[1];
                }
                ?>
                <?php if ($ytId || !empty($tripDocuments) || !empty($reviews)): ?>
                <section class="trip-sec" id="sec-confianca">
                    <h2>Quem já foi e quem leva você</h2>

                    <?php if ($ytId): ?>
                    <div class="trip-video-embed">
                        <iframe src="https://www.youtube.com/embed/<?= e($ytId) ?>" allow="accelerometer;autoplay;clipboard-write;encrypted-media;gyroscope;picture-in-picture" allowfullscreen loading="lazy"></iframe>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($tripDocuments)): ?>
                    <div class="trip-block">
                        <h3>Documentos importantes</h3>
                        <div class="trip-docs">
                            <?php foreach ($tripDocuments as $doc): ?>
                            <a href="<?= e($doc['path']) ?>" target="_blank" rel="noopener" download>
                                <span class="trip-doc-ext"><?= e(strtoupper($doc['type'] ?? 'FILE')) ?></span>
                                <span class="trip-doc-info">
                                    <strong><?= e($doc['name'] ?? 'Documento') ?></strong>
                                    <small><?= e(strtoupper($doc['type'] ?? 'FILE')) ?><?= !empty($doc['size']) ? ' · ' . number_format($doc['size'] / 1024, 0) . ' KB' : '' ?></small>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (!empty($reviews)): ?>
                    <div class="trip-reviews">
                        <?php foreach ($reviews as $review): ?>
                        <div class="trip-rv">
                            <div class="trip-rv-stars"><?= str_repeat('&#9733;', max(1, (int)$review['rating'])) ?></div>
                            <p><?= e($review['comment']) ?></p>
                            <small><?= e($review['first_name'] ?? $review['author_name'] ?? 'Anônimo') ?><span>Avaliação no Google</span></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </section>
                <?php endif; ?>

                <!-- ===================== SEÇÃO: FAQ ===================== -->
                <!-- Só aparece quando há FAQs cadastradas para este passeio (item 3). -->
                <?php if (!empty($tripFaqs)): ?>
                <section class="trip-sec" id="sec-faqs">
                    <div class="trip-faq-header">
                        <h2>FAQ</h2>
                        <label class="expand-all-toggle">
                            <span>Expandir tudo</span>
                            <input type="checkbox" id="expandAllFaqs" onchange="toggleAllFaqs(this.checked)">
                            <span class="toggle-switch"></span>
                        </label>
                    </div>
                    <div class="faq-list">
                        <?php foreach ($tripFaqs as $faq): ?>
                        <div class="faq-item">
                            <button class="faq-question" type="button"><span><?= e($faq['question']) ?></span><svg class="faq-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg></button>
                            <div class="faq-answer"><p><?= e($faq['answer']) ?></p></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </section>
                <?php endif; ?>

                <!-- ===================== FORMULÁRIO DE CONSULTA ===================== -->
                <div class="trip-contact-form" id="booking-section">
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

            <!-- ========================= SIDEBAR ========================= -->
            <aside class="trip-sidebar">
                <div class="trip-price-card">
                    <?php if (!$isGroupPricing && !empty($priceCategories)): ?>
                    <!-- Preços por categoria -->
                    <div class="trip-price-list">
                        <?php foreach ($priceCategories as $cat): ?>
                        <?php
                            $catRegular = (float) ($cat['price'] ?? 0);
                            $catSale = isset($cat['sale_price']) && $cat['sale_price'] !== null && $cat['sale_price'] !== '' ? (float) $cat['sale_price'] : null;
                            $catFinal = ($catSale !== null && $catSale > 0) ? $catSale : $catRegular;
                            $catName = $cat['category_name'] ?? 'Categoria';
                            $catAge = !empty($cat['age_group']) ? ': ' . $cat['age_group'] : '';
                            $isFree = $catFinal <= 0;
                        ?>
                        <div class="trip-price-row">
                            <span class="price-from">A partir de</span>
                            <?php if ($isFree): ?>
                            <span class="trip-price-value trip-price-free">Cortesia</span>
                            <?php elseif ($catSale !== null && $catSale > 0 && $catSale < $catRegular): ?>
                            <span class="trip-price-value">
                                <span class="trip-price-old"><?= money($catRegular) ?></span>
                                <?= money($catFinal) ?>
                            </span>
                            <?php else: ?>
                            <span class="trip-price-value"><?= money($catFinal) ?></span>
                            <?php endif; ?>
                            <span class="price-per">/ <?= e($catName . $catAge) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <!-- Preço único -->
                    <div class="trip-price-row">
                        <span class="price-from">A partir de</span>
                        <span class="trip-price-value"><?= (float)$basePrice > 0 ? money($basePrice) : 'Cortesia' ?></span>
                        <span class="price-per"><?= $priceLabel ?></span>
                    </div>
                    <?php endif; ?>

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

            </aside>
        </div>

        <?php if (!empty($relatedTrips)): ?>
        <!-- ===================== RELACIONADOS / DESTAQUES (largura total, fora da sidebar) ===================== -->
        <!-- Movidos para fora da coluna lateral para que o card de preço sticky
             não cubra estes conteúdos ao rolar a página (item 6 da validação). -->
        <div class="trip-related-fullwidth">
            <div class="trip-related-section">
                <h3 class="trip-related-section-title">Passeios relacionados que podem te interessar</h3>
                <div class="trip-related-grid">
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
            </div>

            <div class="trip-related-section">
                <h3 class="trip-related-section-title">Passeios em Destaque</h3>
                <div class="trip-related-grid">
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
            </div>
        </div>
        <?php endif; ?>
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

// Navegação por âncoras + scroll spy (seções empilhadas numa única página)
(function(){
    var links = Array.prototype.slice.call(document.querySelectorAll('.trip-tabs .trip-tab'));
    if (!links.length) return;
    var sections = links.map(function(a){ return document.querySelector(a.getAttribute('href')); });
    var tabsEl = document.getElementById('tripTabs');

    function setActive(i){
        links.forEach(function(a, j){ a.classList.toggle('active', i === j); });
        var n = links[i];
        if (n && n.parentNode && n.parentNode.scrollLeft !== undefined) {
            n.parentNode.scrollLeft = n.offsetLeft - 16;
        }
    }

    // Clique: rola suavemente até a seção, compensando a altura das abas fixas.
    links.forEach(function(a, i){
        a.addEventListener('click', function(e){
            var target = sections[i];
            if (!target) return;
            e.preventDefault();
            var tabsH = tabsEl ? tabsEl.offsetHeight : 0;
            var y = target.getBoundingClientRect().top + window.pageYOffset - tabsH - 12;
            try { window.scrollTo({ top: y, behavior: 'smooth' }); }
            catch (_) { window.scrollTo(0, y); }
            setActive(i);
            if (history.replaceState) history.replaceState(null, '', a.getAttribute('href'));
        });
    });

    // Scroll spy: destaca a aba da seção atual conforme a rolagem.
    function spy(){
        var y = window.scrollY + (tabsEl ? tabsEl.offsetHeight : 0) + 24;
        var idx = 0;
        sections.forEach(function(s, i){ if (s && s.offsetTop <= y) idx = i; });
        if (window.innerHeight + window.scrollY >= document.body.scrollHeight - 4) idx = sections.length - 1;
        setActive(idx);
    }
    window.addEventListener('scroll', spy, { passive: true });
    spy();
})();
</script>

<?= partial('modals/booking-modal') ?>
<?= partial('modals/google-reviews-modal', ['googleReviews' => $googleReviews ?? null]) ?>

<!-- Barra fixa mobile: Preço + Botão Verificar Disponibilidade -->
<div class="trip-mobile-cta">
    <div class="trip-mobile-cta-price">
        <span class="trip-mobile-cta-from">A partir de</span>
        <span class="trip-mobile-cta-value"><?= (float)$basePrice > 0 ? money($basePrice) : 'Cortesia' ?> <small><?= $priceLabel ?></small></span>
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
.btn-videocall:hover{background:#E4B505;color:#3a2e00;border-color:#c9a004}
.btn-videocall:hover svg{stroke:#3a2e00}
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
.vc-success-link:hover{background:#155700;color:#fff}
.vc-success-cal{background:#fff;color:#1B6F00;border:1.5px solid #1B6F00}
.vc-success-cal:hover{background:#1B6F00;color:#fff;border-color:#1B6F00}
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
