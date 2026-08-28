/**
 * PUNTA CANA PARA BRASILEIROS - Frontend JavaScript
 */
(function() {
    'use strict';

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ==================== TOAST NOTIFICATION ====================
    function toast(message, type = 'success') {
        // Remove toast anterior
        const existing = document.getElementById('appToast');
        if (existing) existing.remove();

        const icons = {
            success: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><path d="M8 12l3 3 5-5"/></svg>',
            error: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>',
            warning: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>',
            info: '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>'
        };

        const toast = document.createElement('div');
        toast.id = 'appToast';
        toast.className = 'app-toast app-toast-' + type;
        toast.innerHTML = `<div class="app-toast-icon">${icons[type] || icons.info}</div><div class="app-toast-msg">${message}</div><button class="app-toast-close" onclick="this.parentElement.remove()">&times;</button>`;
        document.body.appendChild(toast);

        // Animar entrada
        requestAnimationFrame(() => toast.classList.add('show'));

        // Auto-remover após 5s
        setTimeout(() => { if (toast.parentElement) { toast.classList.remove('show'); setTimeout(() => toast.remove(), 300); } }, 5000);
    }

    // Expor globalmente
    window.toast = toast;

    // ==================== MODAL CENTRALIZADO (LOTADO) ====================
    /**
     * Exibe um modal centralizado na tela com mensagem e botão OK.
     * @param {string} message - Mensagem a exibir
     * @param {object} options - { hasAlternatives: bool }
     *   hasAlternatives = true → ao clicar OK, fecha o modal e mantém na tela atual
     *   hasAlternatives = false → ao clicar OK, redireciona para /transfers
     */
    function showFullModal(message, options = {}) {
        // Remove modal anterior se existir
        const existing = document.getElementById('fullScreenModal');
        if (existing) existing.remove();

        const hasAlternatives = options.hasAlternatives || false;

        const overlay = document.createElement('div');
        overlay.id = 'fullScreenModal';
        overlay.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:99999;display:flex;align-items:center;justify-content:center;padding:20px;';

        const modal = document.createElement('div');
        modal.style.cssText = 'background:#fff;border-radius:12px;padding:32px 28px 24px;max-width:460px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.3);text-align:center;animation:modalFadeIn 0.3s ease;';

        const icon = '<div style="margin-bottom:16px;"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#e53e3e" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg></div>';
        const title = '<h3 style="margin:0 0 12px;font-size:1.25rem;color:#1a1a1a;font-weight:600;">Capacidade Máxima Atingida</h3>';
        const msg = '<p style="margin:0 0 24px;font-size:1rem;color:#555;line-height:1.5;">' + message + '</p>';
        const btn = '<button id="fullModalOkBtn" style="background:#1B6F00;color:#fff;border:none;padding:12px 40px;border-radius:8px;font-size:1rem;font-weight:600;cursor:pointer;transition:background 0.2s;">OK</button>';

        modal.innerHTML = icon + title + msg + btn;
        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        // Impedir scroll do body enquanto modal está aberto
        document.body.style.overflow = 'hidden';

        // Adicionar animação CSS inline
        if (!document.getElementById('fullModalStyles')) {
            const style = document.createElement('style');
            style.id = 'fullModalStyles';
            style.textContent = '@keyframes modalFadeIn{from{opacity:0;transform:scale(0.9);}to{opacity:1;transform:scale(1);}}';
            document.head.appendChild(style);
        }

        // Handler do botão OK
        document.getElementById('fullModalOkBtn').addEventListener('click', function() {
            overlay.remove();
            document.body.style.overflow = '';
            if (!hasAlternatives) {
                window.location = '/transfers';
            }
        });
    }

    // Expor globalmente
    window.showFullModal = showFullModal;

    // ==================== UTILITIES ====================
    function ajax(url, options = {}) {
        const defaults = { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': CSRF_TOKEN, 'Content-Type': 'application/json' } };
        const config = { ...defaults, ...options, headers: { ...defaults.headers, ...(options.headers || {}) } };
        return fetch(url, config).then(r => r.json());
    }

    // ==================== HEADER ====================
    // User dropdown
    const userBtn = document.getElementById('userMenuBtn');
    const userDropdown = document.getElementById('userDropdown');
    if (userBtn && userDropdown) {
        userBtn.addEventListener('click', (e) => { e.stopPropagation(); userDropdown.classList.toggle('active'); });
        document.addEventListener('click', () => userDropdown.classList.remove('active'));
    }

    // Mobile nav toggle
    const mobileToggle = document.getElementById('mobileToggle');
    const mainNav = document.getElementById('mainNav');
    if (mobileToggle && mainNav) {
        mobileToggle.addEventListener('click', () => mainNav.classList.toggle('open'));
    }

    // Search toggle
    const searchToggle = document.getElementById('searchToggle');
    const searchOverlay = document.getElementById('searchOverlay');
    const searchClose = document.getElementById('searchClose');
    const searchInput = document.getElementById('searchInput');
    if (searchToggle && searchOverlay) {
        searchToggle.addEventListener('click', () => { searchOverlay.style.display = 'block'; searchInput?.focus(); });
        searchClose?.addEventListener('click', () => { searchOverlay.style.display = 'none'; });
    }

    // Alert dismiss
    document.querySelectorAll('.alert-close').forEach(btn => {
        btn.addEventListener('click', () => btn.closest('.alert').remove());
    });

    // ==================== NEWSLETTER SUBSCRIBE ====================
    window.submitNewsletter = function(e) {
        e.preventDefault();
        const email = document.getElementById('newsletterEmail')?.value;
        const msg = document.getElementById('newsletterMsg');
        if (!email) return false;

        ajax('/api/newsletter/subscribe', { body: JSON.stringify({ email: email }) })
            .then(data => {
                if (msg) {
                    msg.style.display = 'block';
                    msg.style.color = '#1B6F00';
                    msg.textContent = data.message || 'Inscrito com sucesso!';
                }
                document.getElementById('newsletterEmail').value = '';
            })
            .catch(() => {
                if (msg) {
                    msg.style.display = 'block';
                    msg.style.color = '#dc2626';
                    msg.textContent = 'Erro ao inscrever. Tente novamente.';
                }
            });
        return false;
    };

    // ==================== FAQ ACCORDION ====================
    document.querySelectorAll('.faq-question').forEach(btn => {
        btn.addEventListener('click', () => {
            const item = btn.closest('.faq-item');
            const isActive = item.classList.contains('active');

            // Fechar todos (exceto se expandir tudo está ativo)
            const expandAll = document.getElementById('expandAllFaqs');
            if (!expandAll || !expandAll.checked) {
                document.querySelectorAll('.faq-item.active').forEach(el => el.classList.remove('active'));
            }

            // Abrir o clicado (se não estava ativo)
            if (!isActive) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    });

    // Toggle All FAQs
    window.toggleAllFaqs = function(expand) {
        document.querySelectorAll('.faq-item').forEach(item => {
            if (expand) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });
    };

    // ==================== STATS COUNTER ANIMATION ====================
    const statNumbers = document.querySelectorAll('.stat-number');
    if (statNumbers.length) {
        let statsAnimated = false;

        function animateCounters() {
            if (statsAnimated) return;
            statsAnimated = true;

            statNumbers.forEach(el => {
                const target = parseInt(el.dataset.target) || 0;
                const prefix = el.dataset.prefix || '';
                const suffix = el.dataset.suffix || '';
                const duration = 2000;
                const step = Math.ceil(target / (duration / 30));
                let current = 0;

                const timer = setInterval(() => {
                    current += step;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    // Formatar número com ponto para milhares
                    let formatted = current >= 1000 ? current.toLocaleString('pt-BR') : current.toString();
                    el.textContent = prefix + formatted + suffix;
                }, 30);
            });
        }

        // Intersection Observer para disparar quando visível
        const statsSection = document.querySelector('.section-stats');
        if (statsSection) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        animateCounters();
                        observer.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.3 });
            observer.observe(statsSection);
        }
    }

    // ==================== DEPOIMENTOS SLIDER ====================
    document.querySelectorAll('.depoimentos-slider').forEach(slider => {
        const track = slider.querySelector('.depoimentos-track');
        const dots = slider.querySelectorAll('.depoimentos-dots .dot');
        if (!track || !dots.length) return;

        let currentSlide = 0;
        const totalDots = dots.length;
        const gap = 24;

        function goToSlide(index) {
            currentSlide = index;
            if (!track.children[0]) return;
            const cardWidth = track.children[0].offsetWidth;
            track.style.transform = `translateX(-${index * (cardWidth + gap)}px)`;
            dots.forEach((d, i) => d.classList.toggle('active', i === index));
        }

        dots.forEach(dot => {
            dot.addEventListener('click', () => goToSlide(parseInt(dot.dataset.slide)));
        });

        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalDots;
            goToSlide(currentSlide);
        }, 5000);
    });

    // ==================== CART BADGE ====================
    function updateCartBadge() {
        ajax('/api/cart/count', { method: 'GET', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(data => {
                const badge = document.getElementById('cartBadge');
                if (badge) badge.textContent = data.count > 0 ? data.count : '';
            }).catch(() => {});
    }
    updateCartBadge();

    // ==================== PAX COUNTER ====================
    document.querySelectorAll('.pax-plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('.pax-input');
            const max = parseInt(input.max) || 20;
            if (parseInt(input.value) < max) { input.value = parseInt(input.value) + 1; input.dispatchEvent(new Event('change')); }
        });
    });
    document.querySelectorAll('.pax-minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = btn.parentElement.querySelector('.pax-input');
            const min = parseInt(input.min) || 0;
            if (parseInt(input.value) > min) { input.value = parseInt(input.value) - 1; input.dispatchEvent(new Event('change')); }
        });
    });

    // ==================== TRIP BOOKING WIDGET ====================
    const bookingForm = document.getElementById('bookingForm');
    if (bookingForm) {
        // Update price when pax or date changes
        const updatePrice = () => {
            const paxInputs = bookingForm.querySelectorAll('.pax-input');

            // Verificar group pricing
            const gpEnabled = typeof GROUP_PRICING_ENABLED !== 'undefined' && GROUP_PRICING_ENABLED;
            const gpTable = typeof GROUP_PRICING_TABLE !== 'undefined' ? GROUP_PRICING_TABLE : [];

            if (gpEnabled && gpTable.length > 0) {
                // Modo GROUP PRICING: tabela aplica APENAS para adultos
                let adultPax = 0;
                let childTotal = 0;
                paxInputs.forEach(input => {
                    const slug = (input.dataset.slug || '').toLowerCase();
                    const qty = parseInt(input.value) || 0;
                    if (slug === 'adulto') {
                        adultPax += qty;
                    } else {
                        const price = parseFloat(input.dataset.price) || 0;
                        childTotal += price * qty;
                    }
                });

                let groupPrice = null;
                if (adultPax > 0) {
                    for (let i = 0; i < gpTable.length; i++) {
                        if (parseInt(gpTable[i].pax) === adultPax) { groupPrice = parseFloat(gpTable[i].price); break; }
                    }
                    if (groupPrice === null) {
                        const sorted = [...gpTable].sort((a, b) => parseInt(a.pax) - parseInt(b.pax));
                        for (let i = sorted.length - 1; i >= 0; i--) {
                            if (parseInt(sorted[i].pax) <= adultPax) { groupPrice = parseFloat(sorted[i].price); break; }
                        }
                        if (groupPrice === null && sorted.length > 0) groupPrice = parseFloat(sorted[0].price);
                    }
                }

                const totalEl = document.getElementById('widgetTotal');
                if (totalEl) totalEl.textContent = '$' + ((groupPrice || 0) + childTotal).toFixed(2);
            } else {
                // Modo PER-PERSON
                let total = 0;
                paxInputs.forEach(input => {
                    const price = parseFloat(input.dataset.price) || 0;
                    const qty = parseInt(input.value) || 0;
                    total += price * qty;
                });
                const totalEl = document.getElementById('widgetTotal');
                if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
            }
        };
        bookingForm.querySelectorAll('.pax-input').forEach(input => input.addEventListener('change', updatePrice));

        // Fetch dynamic price by date
        const dateInput = document.getElementById('bookingDate');
        if (dateInput) {
            dateInput.addEventListener('change', () => {
                const packageId = bookingForm.querySelector('[name="package_id"]').value;
                const date = dateInput.value;
                if (!packageId || !date) return;

                ajax('/api/pricing/day-prices', { body: JSON.stringify({ package_id: packageId, date: date }) })
                    .then(data => {
                        if (data.success && data.prices) {
                            data.prices.forEach(p => {
                                const input = bookingForm.querySelector(`[name="pax[${p.traveler_category_id}]"]`);
                                if (input) input.dataset.price = p.effective_price;
                            });
                            updatePrice();
                        }
                    }).catch(() => {});
            });
        }
    }

    // ==================== TRIP GALLERY SLIDER ====================
    const tripSlider = document.getElementById('tripSliderTrack');
    const sliderPrev = document.getElementById('sliderPrev');
    const sliderNext = document.getElementById('sliderNext');
    if (tripSlider && sliderPrev && sliderNext) {
        let currentTripSlide = 0;
        const totalSlides = tripSlider.children.length;

        function goToTripSlide(index) {
            if (index < 0) index = totalSlides - 1;
            if (index >= totalSlides) index = 0;
            currentTripSlide = index;
            tripSlider.style.transform = `translateX(-${index * 100}%)`;
        }

        sliderPrev.addEventListener('click', () => goToTripSlide(currentTripSlide - 1));
        sliderNext.addEventListener('click', () => goToTripSlide(currentTripSlide + 1));

        // Swipe support (mobile)
        let startX = 0;
        tripSlider.addEventListener('touchstart', e => { startX = e.touches[0].clientX; });
        tripSlider.addEventListener('touchend', e => {
            const diff = startX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 50) { diff > 0 ? goToTripSlide(currentTripSlide + 1) : goToTripSlide(currentTripSlide - 1); }
        });
    }

    // ==================== GALLERY ====================
    window.changeGallery = function(thumb) {
        const main = document.getElementById('galleryMain');
        if (main) main.src = thumb.src;
        document.querySelectorAll('.gallery-thumbs .thumb').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    };

    // ==================== WISHLIST ====================
    const wishlistBtn = document.getElementById('wishlistBtn');
    if (wishlistBtn) {
        wishlistBtn.addEventListener('click', () => {
            const tripId = wishlistBtn.dataset.trip;
            ajax('/minha-conta/wishlist/toggle', { body: JSON.stringify({ trip_id: tripId, _token: CSRF_TOKEN }) })
                .then(data => {
                    if (data.success) {
                        wishlistBtn.innerHTML = data.in_wishlist ? '&#10084; Na Lista de Desejos' : '&#9825; Adicionar à Lista de Desejos';
                    }
                }).catch(() => {});
        });
    }

    // ==================== TRANSFER SEARCH ====================
    const searchBtn = document.getElementById('searchTransfersBtn');
    if (searchBtn) {
        searchBtn.addEventListener('click', searchTransfers);

        // Pax dropdown toggle
        const paxBtn = document.getElementById('paxDropdownBtn');
        const paxDrop = document.getElementById('paxDropdown');
        if (paxBtn && paxDrop) {
            paxBtn.addEventListener('click', (e) => { e.stopPropagation(); paxDrop.classList.toggle('active'); });
            document.addEventListener('click', (e) => { if (!paxDrop.contains(e.target) && e.target !== paxBtn) paxDrop.classList.remove('active'); });
        }

        // Transfer tabs
        document.querySelectorAll('.transfer-tab').forEach(btn => {
            btn.addEventListener('click', () => {
                document.querySelectorAll('.transfer-tab').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                const tab = btn.dataset.tab;
                const tabRoundtrip = document.getElementById('tabRoundtrip');
                const tabMultiple = document.getElementById('tabMultiple');
                const depFields = document.querySelectorAll('.departure-field');

                if (tab === 'multiple') {
                    if (tabRoundtrip) tabRoundtrip.style.display = 'none';
                    if (tabMultiple) tabMultiple.style.display = 'block';
                } else {
                    if (tabRoundtrip) tabRoundtrip.style.display = 'block';
                    if (tabMultiple) tabMultiple.style.display = 'none';
                    if (tab === 'oneway') {
                        depFields.forEach(f => f.classList.add('hidden'));
                    } else {
                        depFields.forEach(f => f.classList.remove('hidden'));
                    }
                }
            });
        });

        // Pax dropdown for multiple transfers
        const paxBtnMulti = document.getElementById('paxDropdownBtnMulti');
        const paxDropMulti = document.getElementById('paxDropdownMulti');
        if (paxBtnMulti && paxDropMulti) {
            paxBtnMulti.addEventListener('click', (e) => { e.stopPropagation(); paxDropMulti.classList.toggle('active'); });
            document.addEventListener('click', (e) => { if (!paxDropMulti.contains(e.target) && e.target !== paxBtnMulti) paxDropMulti.classList.remove('active'); });
        }
    }

    window.changePaxTransfer = function() {};
    window.changePaxMulti = function() {};

    // Multiple transfers - add route
    window.addRoute = function() {
        const container = document.getElementById('multipleRoutesContainer');
        if (!container) return;
        const routes = container.querySelectorAll('.multiple-route-item');
        const newIndex = routes.length + 1;
        if (newIndex > 10) { toast('Máximo de 10 rotas.', 'warning'); return; }

        // Get locations options from first route select
        const firstSelect = container.querySelector('.multi-origin');
        const optionsHtml = firstSelect ? firstSelect.innerHTML : '<option value="">Digite para buscar...</option>';

        const routeHtml = `
        <div class="multiple-route-item" data-route="${newIndex}">
            <div class="multiple-route-header">
                <span class="route-number">Rota ${newIndex}</span>
                <button type="button" class="btn-remove-route" onclick="removeRoute(this)" title="Remover rota">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div class="transfer-form-row">
                <div class="tf-field">
                    <label>ORIGEM</label>
                    <select name="multi_origin_${newIndex}" class="tf-input multi-origin">${optionsHtml}</select>
                </div>
                <div class="tf-field">
                    <label>DESTINO</label>
                    <select name="multi_destination_${newIndex}" class="tf-input multi-destination">${optionsHtml}</select>
                </div>
                <div class="tf-field">
                    <label>DATA</label>
                    <input type="date" name="multi_date_${newIndex}" class="tf-input multi-date" min="${new Date().toISOString().split('T')[0]}">
                </div>
                <div class="tf-field tf-field-sm">
                    <label>HORA</label>
                    <input type="time" name="multi_time_${newIndex}" class="tf-input multi-time">
                </div>
            </div>
        </div>`;
        container.insertAdjacentHTML('beforeend', routeHtml);
    };

    // Multiple transfers - remove route
    window.removeRoute = function(btn) {
        const container = document.getElementById('multipleRoutesContainer');
        const routes = container.querySelectorAll('.multiple-route-item');
        if (routes.length <= 2) { toast('Mínimo de 2 rotas.', 'warning'); return; }
        const routeItem = btn.closest('.multiple-route-item');
        routeItem.remove();
        // Renumber routes
        container.querySelectorAll('.multiple-route-item').forEach((item, idx) => {
            item.dataset.route = idx + 1;
            item.querySelector('.route-number').textContent = 'Rota ' + (idx + 1);
        });
    };

    // Multiple transfers - search
    document.getElementById('searchMultiTransfersBtn')?.addEventListener('click', searchMultipleTransfers);

    function searchMultipleTransfers() {
        const container = document.getElementById('multipleRoutesContainer');
        const routes = container.querySelectorAll('.multiple-route-item');
        const serviceType = document.getElementById('multiServiceType')?.value || 'private';

        // Coletar todos os valores de passageiros do dropdown multi
        var paxPayload = {};
        var paxInputs = document.querySelectorAll('#paxDropdownMulti .pax-input-sm');
        for (var i = 0; i < paxInputs.length; i++) {
            var name = paxInputs[i].getAttribute('name');
            if (name) {
                // Remover prefixo 'multi_' para enviar ao backend
                var cleanName = name.replace(/^multi_/, '');
                paxPayload[cleanName] = paxInputs[i].value || '0';
            }
        }

        const routesData = [];
        let hasError = false;

        routes.forEach((route, idx) => {
            const origin = route.querySelector('.multi-origin')?.value;
            const destination = route.querySelector('.multi-destination')?.value;
            const date = route.querySelector('.multi-date')?.value;
            const time = route.querySelector('.multi-time')?.value;

            if (!origin || !destination) {
                hasError = true;
            toast('Selecione origem e destino para a Rota ' + (idx + 1) + '.', 'warning');
            }
            routesData.push({ origin_id: origin, destination_id: destination, date, time });
        });

        if (hasError) return;

        document.getElementById('transferLoading').style.display = 'block';
        document.getElementById('transferResults').style.display = 'none';
        document.getElementById('transferMultiResults').style.display = 'none';
        document.getElementById('transferEmptyState').style.display = 'none';

        // Search for all routes in parallel
        const promises = routesData.map(route =>
            ajax('/api/transfers/buscar', {
                body: JSON.stringify(Object.assign({}, paxPayload, {
                    origin_id: route.origin_id,
                    destination_id: route.destination_id,
                    service_type: serviceType
                }))
            })
        );

        Promise.all(promises).then(results => {
            document.getElementById('transferLoading').style.display = 'none';

            // Check if all routes have results
            const allHaveResults = results.every(r => r.success && r.results && r.results.length > 0);
            if (!allHaveResults) {
                document.getElementById('transferEmptyState').style.display = 'block';
                return;
            }

            renderMultipleResults(results, routesData);
        }).catch(() => {
            document.getElementById('transferLoading').style.display = 'none';
            toast('Erro de conex�o. Tente novamente.', 'error');
        });
    }

    function renderMultipleResults(results, routesData) {
        const container = document.getElementById('resultsList');
        const resultsDiv = document.getElementById('transferMultiResults');
        const totalBar = document.getElementById('transferTotalBar');
        container.innerHTML = '';

        results.forEach((data, idx) => {
            const route = routesData[idx];
            const routeLabel = data.origin + ' → ' + data.destination + (route.date ? ' (' + formatDateBR(route.date) + ')' : '');

            let vehiclesHtml = '';
            data.results.forEach((v, vIdx) => {
                vehiclesHtml += `
                <div class="transfer-vehicle-option" data-direction="multi_${idx}" data-idx="${vIdx}" data-price="${v.price}" data-id="${v.id}">
                    <div class="tv-img"><img src="${v.image || '/assets/images/placeholder.jpg'}" alt="${v.title}"></div>
                    <div class="tv-body">
                        <div class="tv-info">
                            <h4>${v.title}</h4>
                            <p>${v.description || ''}</p>
                            <div class="tv-specs">
                                <span class="tv-spec"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg> Até ${v.max_passengers} passageiros</span>
                                <span class="tv-spec"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a4 4 0 00-8 0v2"/></svg> Até ${v.max_luggage} malas</span>
                                <span class="tv-spec"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg> ${v.duration} min</span>
                            </div>
                        </div>
                        <div class="tv-price-col">
                            <span class="tv-price">$${v.price.toFixed(2)}</span>
                            <span class="tv-price-label">USD</span>
                        </div>
                    </div>
                    <div class="tv-select-indicator"></div>
                </div>`;
            });

            container.innerHTML += `
            <div class="transfer-section">
                <div class="transfer-section-header">
                    <div class="transfer-section-icon transfer-section-icon--arrival">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="M12 5l7 7-7 7"/></svg>
                    </div>
                    <div>
                        <h3 class="transfer-section-title">ROTA ${idx + 1}</h3>
                        <p class="transfer-section-route">${routeLabel}</p>
                    </div>
                </div>
                <div class="transfer-vehicles-grid">${vehiclesHtml}</div>
            </div>`;
        });

        resultsDiv.style.display = 'block';

        // Store data for multi selection
        window._multiTransferData = results;
        window._multiTransferRoutes = routesData;
        window._multiTransferSelection = {};

        // Bind click events
        document.querySelectorAll('#resultsList .transfer-vehicle-option').forEach(card => {
            card.addEventListener('click', function() {
                const direction = this.dataset.direction;
                const vehicleIdx = parseInt(this.dataset.idx);
                // Deselect all in this route
                document.querySelectorAll(`#resultsList .transfer-vehicle-option[data-direction="${direction}"]`).forEach(c => c.classList.remove('selected'));
                this.classList.add('selected');
                window._multiTransferSelection[direction] = window._multiTransferData[parseInt(direction.split('_')[1])].results[vehicleIdx];
                updateMultiTotal();
            });
        });

        // Auto-select first vehicle of each route
        results.forEach((data, idx) => {
            const firstCard = document.querySelector(`#resultsList .transfer-vehicle-option[data-direction="multi_${idx}"][data-idx="0"]`);
            if (firstCard) firstCard.click();
        });
    }

    function updateMultiTotal() {
        const sel = window._multiTransferSelection || {};
        let total = 0;
        Object.values(sel).forEach(v => { if (v) total += v.price; });
        document.getElementById('multiTotalValue').textContent = '$' + total.toFixed(2) + ' USD';
        document.getElementById('transferTotalBar').style.display = 'block';
        window._multiTransferTotalPrice = total;
    }

    function searchTransfers() {
        const origin = document.getElementById('originSelect')?.value;
        const destination = document.getElementById('destinationSelect')?.value;
        const serviceType = document.getElementById('serviceType')?.value || 'private';

        if (!origin || !destination) { toast('Selecione origem e destino.', 'warning'); return; }

        // Coletar todos os valores de passageiros do dropdown
        var paxPayload = {};
        var paxInputs = document.querySelectorAll('#paxDropdown .pax-input-sm');
        for (var i = 0; i < paxInputs.length; i++) {
            var name = paxInputs[i].getAttribute('name');
            if (name) paxPayload[name] = paxInputs[i].value || '0';
        }

        document.getElementById('transferLoading').style.display = 'block';
        document.getElementById('transferResults').style.display = 'none';
        document.getElementById('transferEmptyState').style.display = 'none';

        var payload = Object.assign({}, paxPayload, { origin_id: origin, destination_id: destination, service_type: serviceType });
        ajax('/api/transfers/buscar', { body: JSON.stringify(payload) })
            .then(data => {
                document.getElementById('transferLoading').style.display = 'none';
                if (data.success && data.results && data.results.length > 0) {
                    renderTransferResults(data);
                } else if (data.success && data.results && data.results.length === 0) {
                    document.getElementById('transferEmptyState').style.display = 'block';
                } else {
                    alert(data.error || 'Erro na busca.');
                }
            }).catch(() => { document.getElementById('transferLoading').style.display = 'none'; toast('Erro de conex�o.', 'error'); });
    }

    function renderTransferResults(data) {
        const resultsDiv = document.getElementById('transferResults');
        const arrivalContainer = document.getElementById('arrivalVehicles');
        const departureContainer = document.getElementById('departureVehicles');
        const arrivalSection = document.getElementById('transferArrivalSection');
        const departureSection = document.getElementById('transferDepartureSection');
        const summaryDiv = document.getElementById('transferSummary');

        arrivalContainer.innerHTML = '';
        departureContainer.innerHTML = '';
        summaryDiv.style.display = 'none';

        const arrivalDate = document.querySelector('[name="arrival_date"]')?.value || '';
        const departureDate = document.querySelector('[name="departure_date"]')?.value || '';
        const activeTab = document.querySelector('.transfer-tab.active')?.dataset.tab;
        const isRoundtrip = activeTab === 'roundtrip' && departureDate;

        // Set route labels
        document.getElementById('arrivalRouteLabel').textContent = data.origin + ' → ' + data.destination + (arrivalDate ? ' (' + formatDateBR(arrivalDate) + ')' : '');
        if (isRoundtrip) {
            document.getElementById('departureRouteLabel').textContent = data.destination + ' → ' + data.origin + (departureDate ? ' (' + formatDateBR(departureDate) + ')' : '');
            departureSection.style.display = 'block';
        } else {
            departureSection.style.display = 'none';
        }

        // Render vehicle cards for ARRIVAL
        data.results.forEach((v, idx) => {
            arrivalContainer.innerHTML += buildVehicleCard(v, 'arrival', idx);
        });

        // Render vehicle cards for DEPARTURE (same vehicles, reverse route)
        if (isRoundtrip) {
            data.results.forEach((v, idx) => {
                departureContainer.innerHTML += buildVehicleCard(v, 'departure', idx);
            });
        }

        resultsDiv.style.display = 'block';

        // Store data
        window._transferData = data;
        window._transferSelection = { arrival: null, departure: null };

        // Bind click events
        document.querySelectorAll('.transfer-vehicle-option').forEach(card => {
            card.addEventListener('click', function() {
                const direction = this.dataset.direction;
                const vehicleIdx = parseInt(this.dataset.idx);
                selectVehicle(direction, vehicleIdx);
            });
        });
    }

    function buildVehicleCard(v, direction, idx) {
        return `
        <div class="transfer-vehicle-option" data-direction="${direction}" data-idx="${idx}" data-price="${v.price}" data-id="${v.id}">
            <div class="tv-img"><img src="${v.image || '/assets/images/placeholder.jpg'}" alt="${v.title}"></div>
            <div class="tv-body">
                <div class="tv-info">
                    <h4>${v.title}</h4>
                    <p>${v.description || ''}</p>
                    <div class="tv-specs">
                        <span class="tv-spec">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            Até ${v.max_passengers} passageiros
                        </span>
                        <span class="tv-spec">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a4 4 0 00-8 0v2"/></svg>
                            Até ${v.max_luggage} malas
                        </span>
                        <span class="tv-spec">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                            ${v.duration} min
                        </span>
                    </div>
                </div>
                <div class="tv-price-col">
                    <span class="tv-price">$${v.price.toFixed(2)}</span>
                    <span class="tv-price-label">USD</span>
                </div>
            </div>
            <div class="tv-select-indicator"></div>
        </div>`;
    }

    function selectVehicle(direction, idx) {
        // Deselect all in this direction
        document.querySelectorAll(`.transfer-vehicle-option[data-direction="${direction}"]`).forEach(c => c.classList.remove('selected'));
        // Select clicked
        const card = document.querySelector(`.transfer-vehicle-option[data-direction="${direction}"][data-idx="${idx}"]`);
        if (card) card.classList.add('selected');

        // Store selection
        const data = window._transferData;
        window._transferSelection[direction] = data.results[idx];

        // Update summary
        updateTransferSummary();
    }

    function updateTransferSummary() {
        const sel = window._transferSelection;
        const data = window._transferData;
        const summaryDiv = document.getElementById('transferSummary');
        const itemsDiv = document.getElementById('transferSummaryItems');
        const activeTab = document.querySelector('.transfer-tab.active')?.dataset.tab;
        const isRoundtrip = activeTab === 'roundtrip';

        // Need at least arrival selected
        if (!sel.arrival) { summaryDiv.style.display = 'none'; return; }
        // For roundtrip, need both
        if (isRoundtrip && !sel.departure) { summaryDiv.style.display = 'none'; return; }

        let total = sel.arrival.price;
        let html = `<div class="transfer-summary-item"><span class="transfer-summary-item-label">Entrada: ${sel.arrival.title}</span><span class="transfer-summary-item-value">$${sel.arrival.price.toFixed(2)}</span></div>`;

        if (isRoundtrip && sel.departure) {
            total += sel.departure.price;
            html += `<div class="transfer-summary-item"><span class="transfer-summary-item-label">Saída: ${sel.departure.title}</span><span class="transfer-summary-item-value">$${sel.departure.price.toFixed(2)}</span></div>`;
        }

        itemsDiv.innerHTML = html;
        document.getElementById('transferTotalValue').textContent = '$' + total.toFixed(2) + ' USD';
        summaryDiv.style.display = 'block';
        window._transferTotalPrice = total;
    }

    function formatDateBR(dateStr) {
        if (!dateStr) return '';
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    // Add to cart button
    document.getElementById('btnAddCart')?.addEventListener('click', () => {
        const sel = window._transferSelection;
        if (!sel || !sel.arrival) { toast('Selecione um transfer de entrada.', 'warning'); return; }

        const activeTab = document.querySelector('.transfer-tab.active')?.dataset.tab;
        const isRoundtrip = activeTab === 'roundtrip';
        if (isRoundtrip && !sel.departure) { toast('Selecione um transfer de saída.', 'warning'); return; }

        const groupId = 'tg_' + Date.now();
        const originId = document.getElementById('originSelect').value;
        const destinationId = document.getElementById('destinationSelect').value;
        const serviceType = document.getElementById('serviceType').value;

        // Coletar passageiros dinamicamente
        var paxData = {};
        var paxInputs = document.querySelectorAll('#paxDropdown .pax-input-sm');
        for (var i = 0; i < paxInputs.length; i++) {
            var name = paxInputs[i].getAttribute('name');
            if (name) paxData[name] = paxInputs[i].value || '0';
        }

        // Add arrival
        const flightNumber = document.getElementById('flightNumber')?.value || '';
        const flightTime = document.getElementById('flightTime')?.value || '';

        const arrivalPayload = Object.assign({}, paxData, {
            vehicle_id: sel.arrival.id,
            origin_id: originId,
            destination_id: destinationId,
            date: document.querySelector('[name="arrival_date"]').value,
            time: document.querySelector('[name="arrival_time"]').value,
            type: 'arrival',
            service_type: serviceType,
            group_id: groupId,
            flight_number: flightNumber,
            flight_time: flightTime,
        });

        const addArrival = ajax('/api/cart/add-transfer', { body: JSON.stringify(arrivalPayload) });

        if (isRoundtrip && sel.departure) {
            const departurePayload = Object.assign({}, paxData, {
                vehicle_id: sel.departure.id,
                origin_id: destinationId,
                destination_id: originId,
                date: document.querySelector('[name="departure_date"]').value,
                time: document.querySelector('[name="departure_time"]').value,
                type: 'departure',
                service_type: serviceType,
                group_id: groupId,
                flight_number: flightNumber,
                flight_time: flightTime,
            });
            addArrival.then(arrResult => {
                if (!arrResult.success) {
                    const data = window._transferData;
                    const availableVehicles = data && data.results ? data.results.filter(v => !v.is_full) : [];
                    const hasAlternatives = availableVehicles.length > 1;
                    showFullModal(arrResult.error || 'Transfer indisponível.', { hasAlternatives: hasAlternatives });
                    return;
                }
                return ajax('/api/cart/add-transfer', { body: JSON.stringify(departurePayload) });
            }).then(d => {
                if (!d) return; // arrival falhou, já mostrou modal
                if (d.success) { toast('Transfers adicionados ao carrinho!', 'success'); updateCartBadge(); }
                else {
                    const data = window._transferData;
                    const availableVehicles = data && data.results ? data.results.filter(v => !v.is_full) : [];
                    const hasAlternatives = availableVehicles.length > 1;
                    showFullModal(d.error || 'Transfer indisponível.', { hasAlternatives: hasAlternatives });
                }
            }).catch(() => toast('Erro de conexão.', 'error'));
        } else {
            addArrival.then(d => {
                if (d.success) { toast('Transfer adicionado ao carrinho!', 'success'); updateCartBadge(); }
                else {
                    const data = window._transferData;
                    const availableVehicles = data && data.results ? data.results.filter(v => !v.is_full) : [];
                    const hasAlternatives = availableVehicles.length > 1;
                    showFullModal(d.error || 'Transfer indisponível.', { hasAlternatives: hasAlternatives });
                }
            }).catch(() => toast('Erro de conexão.', 'error'));
        }
    });

    document.getElementById('btnDirectCheckout')?.addEventListener('click', () => {
        const sel = window._transferSelection;
        if (!sel || !sel.arrival) { toast('Selecione um transfer de entrada.', 'warning'); return; }

        const activeTab = document.querySelector('.transfer-tab.active')?.dataset.tab;
        const isRoundtrip = activeTab === 'roundtrip';
        if (isRoundtrip && !sel.departure) { toast('Selecione um transfer de saída.', 'warning'); return; }

        const groupId = 'tg_' + Date.now();
        const originId = document.getElementById('originSelect').value;
        const destinationId = document.getElementById('destinationSelect').value;
        const serviceType = document.getElementById('serviceType').value;

        var paxData = {};
        var paxInputs = document.querySelectorAll('#paxDropdown .pax-input-sm');
        for (var i = 0; i < paxInputs.length; i++) {
            var name = paxInputs[i].getAttribute('name');
            if (name) paxData[name] = paxInputs[i].value || '0';
        }

        const flightNumberDC = document.getElementById('flightNumber')?.value || '';
        const flightTimeDC = document.getElementById('flightTime')?.value || '';

        const arrivalPayload = Object.assign({}, paxData, {
            vehicle_id: sel.arrival.id,
            origin_id: originId,
            destination_id: destinationId,
            date: document.querySelector('[name="arrival_date"]').value,
            time: document.querySelector('[name="arrival_time"]').value,
            type: 'arrival',
            service_type: serviceType,
            group_id: groupId,
            flight_number: flightNumberDC,
            flight_time: flightTimeDC,
        });

        ajax('/api/cart/add-transfer', { body: JSON.stringify(arrivalPayload) }).then(arrivalResult => {
            if (!arrivalResult.success) {
                // Verificar se há mais de uma opção de veículo disponível
                const data = window._transferData;
                const availableVehicles = data && data.results ? data.results.filter(v => !v.is_full) : [];
                const hasAlternatives = availableVehicles.length > 1;
                showFullModal(arrivalResult.error || 'Transfer indisponível.', { hasAlternatives: hasAlternatives });
                return;
            }

            if (isRoundtrip && sel.departure) {
                const departurePayload = Object.assign({}, paxData, {
                    vehicle_id: sel.departure.id,
                    origin_id: destinationId,
                    destination_id: originId,
                    date: document.querySelector('[name="departure_date"]').value,
                    time: document.querySelector('[name="departure_time"]').value,
                    type: 'departure',
                    service_type: serviceType,
                    group_id: groupId,
                    flight_number: flightNumberDC,
                    flight_time: flightTimeDC,
                });
                ajax('/api/cart/add-transfer', { body: JSON.stringify(departurePayload) }).then(depResult => {
                    if (!depResult.success) {
                        const data = window._transferData;
                        const availableVehicles = data && data.results ? data.results.filter(v => !v.is_full) : [];
                        const hasAlternatives = availableVehicles.length > 1;
                        showFullModal(depResult.error || 'Transfer indisponível.', { hasAlternatives: hasAlternatives });
                        return;
                    }
                    updateCartBadge();
                    window.location = '/checkout';
                }).catch(() => toast('Erro de conexão.', 'error'));
            } else {
                updateCartBadge();
                window.location = '/checkout';
            }
        }).catch(() => toast('Erro de conexão.', 'error'));
    });

    // Multi transfers - Add to cart
    document.getElementById('btnAddCartMulti')?.addEventListener('click', function() {
        const sel = window._multiTransferSelection || {};
        const routes = window._multiTransferRoutes || [];
        const results = window._multiTransferData || [];
        if (Object.keys(sel).length === 0) { toast('Selecione um veículo para cada rota.', 'warning'); return; }

        // Coletar passageiros
        var paxData = {};
        var paxInputs = document.querySelectorAll('#paxDropdownMulti .pax-input-sm');
        for (var i = 0; i < paxInputs.length; i++) {
            var name = paxInputs[i].getAttribute('name');
            if (name) paxData[name.replace(/^multi_/, '')] = paxInputs[i].value || '0';
        }
        var serviceType = document.getElementById('multiServiceType')?.value || 'private';
        var groupId = 'tg_' + Date.now();

        // Adicionar cada rota selecionada ao carrinho
        var promises = [];
        Object.keys(sel).forEach(function(key) {
            var idx = parseInt(key.split('_')[1]);
            var vehicle = sel[key];
            var route = routes[idx];
            var data = results[idx];
            if (!vehicle || !route) return;

            var payload = Object.assign({}, paxData, {
                vehicle_id: vehicle.id,
                origin_id: route.origin_id,
                destination_id: route.destination_id,
                date: route.date || '',
                time: route.time || '',
                type: 'multi_' + (idx + 1),
                service_type: serviceType,
                group_id: groupId,
            });
            promises.push(ajax('/api/cart/add-transfer', { body: JSON.stringify(payload) }));
        });

        Promise.all(promises).then(function(responses) {
            var allOk = responses.every(function(r) { return r.success; });
            if (allOk) {
                toast('Transfers adicionados ao carrinho!', 'success');
                updateCartBadge();
            } else {
                // Pegar a primeira mensagem de erro
                var errorMsg = '';
                for (var i = 0; i < responses.length; i++) {
                    if (!responses[i].success) { errorMsg = responses[i].error || 'Transfer indisponível.'; break; }
                }
                var multiData = window._multiTransferData || [];
                var hasAlternatives = false;
                for (var j = 0; j < multiData.length; j++) {
                    if (multiData[j] && multiData[j].results && multiData[j].results.filter(function(v) { return !v.is_full; }).length > 1) {
                        hasAlternatives = true;
                        break;
                    }
                }
                showFullModal(errorMsg, { hasAlternatives: hasAlternatives });
            }
        }).catch(function() { toast('Erro de conexão.', 'error'); });
    });

    // Multi transfers - Direct checkout
    document.getElementById('btnDirectCheckoutMulti')?.addEventListener('click', function() {
        const sel = window._multiTransferSelection || {};
        const routes = window._multiTransferRoutes || [];
        const results = window._multiTransferData || [];
        if (Object.keys(sel).length === 0) { toast('Selecione um veículo para cada rota.', 'warning'); return; }

        // Coletar passageiros
        var paxData = {};
        var paxInputs = document.querySelectorAll('#paxDropdownMulti .pax-input-sm');
        for (var i = 0; i < paxInputs.length; i++) {
            var name = paxInputs[i].getAttribute('name');
            if (name) paxData[name.replace(/^multi_/, '')] = paxInputs[i].value || '0';
        }
        var serviceType = document.getElementById('multiServiceType')?.value || 'private';
        var groupId = 'tg_' + Date.now();

        // Adicionar cada rota selecionada ao carrinho
        var promises = [];
        Object.keys(sel).forEach(function(key) {
            var idx = parseInt(key.split('_')[1]);
            var vehicle = sel[key];
            var route = routes[idx];
            if (!vehicle || !route) return;

            var payload = Object.assign({}, paxData, {
                vehicle_id: vehicle.id,
                origin_id: route.origin_id,
                destination_id: route.destination_id,
                date: route.date || '',
                time: route.time || '',
                type: 'multi_' + (idx + 1),
                service_type: serviceType,
                group_id: groupId,
            });
            promises.push(ajax('/api/cart/add-transfer', { body: JSON.stringify(payload) }));
        });

        Promise.all(promises).then(function(responses) {
            var hasError = responses.some(function(r) { return !r.success; });
            if (hasError) {
                // Pegar a primeira mensagem de erro
                var errorMsg = '';
                for (var i = 0; i < responses.length; i++) {
                    if (!responses[i].success) { errorMsg = responses[i].error || 'Transfer indisponível.'; break; }
                }
                // Verificar se há alternativas (mais de um veículo por rota)
                var multiData = window._multiTransferData || [];
                var hasAlternatives = false;
                for (var j = 0; j < multiData.length; j++) {
                    if (multiData[j] && multiData[j].results && multiData[j].results.filter(function(v) { return !v.is_full; }).length > 1) {
                        hasAlternatives = true;
                        break;
                    }
                }
                showFullModal(errorMsg, { hasAlternatives: hasAlternatives });
                return;
            }
            updateCartBadge();
            window.location = '/checkout';
        }).catch(function() { toast('Erro de conexão.', 'error'); });
    });

    // ==================== CHECKOUT ====================
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!document.getElementById('termsCheck').checked) { toast('Aceite os termos e condi��es.', 'warning'); return; }

            const formData = new FormData(checkoutForm);
            const data = Object.fromEntries(formData.entries());
            data.payment_mode = 'partial';

            document.getElementById('checkoutLoading').style.display = 'flex';

            ajax('/checkout/processar', { body: JSON.stringify(data) })
                .then(response => {
                    if (response.success) {
                        if (response.gateway === 'simulate' && response.redirect) {
                            // Simula��o aprovada � redirecionar direto
                            window.location = response.redirect;
                        } else if (response.gateway === 'paypal' && response.paypal_order_id) {
                            capturePayPal(response);
                        } else if (response.gateway === 'stripe' && response.stripe_client_secret) {
                            handleStripePayment(response);
                        } else if (response.gateway === 'pix' && response.pix) {
                            handlePixPayment(response);
                        } else {
                            window.location = '/checkout/sucesso/' + response.booking_number;
                        }
                    } else {
                        document.getElementById('checkoutLoading').style.display = 'none';
                        toast(response.error || 'Erro ao processar.', 'error');
                    }
                }).catch(err => { document.getElementById('checkoutLoading').style.display = 'none'; toast('Erro de conex�o. Tente novamente.', 'error'); });
        });
    }

    function handlePixPayment(response) {
        document.getElementById('checkoutLoading').style.display = 'none';

        // Esconder formulário e mostrar QR Code
        const formSections = document.querySelectorAll('.checkout-section, .checkout-submit, #checkoutStep4, #checkoutTerms, #paymentContainer, #checkoutStep3Actions');
        formSections.forEach(el => { if (el) el.style.display = 'none'; });

        const pixContainer = document.getElementById('pixContainer');
        if (pixContainer) {
            pixContainer.style.display = 'block';

            const pix = response.pix;

            // QR Code image
            const qrImage = document.getElementById('pixQrImage');
            if (qrImage && pix.qr_code_url) {
                qrImage.innerHTML = '<img src="' + pix.qr_code_url + '" alt="QR Code PIX" style="max-width:220px;border-radius:12px;border:2px solid #e5e7eb;">';
            } else if (qrImage) {
                qrImage.innerHTML = '<div style="padding:20px;background:#f1f5f9;border-radius:12px;color:#64748b;">QR Code será exibido aqui</div>';
            }

            // PIX copia e cola
            const pixCodeText = document.getElementById('pixCodeText');
            if (pixCodeText && pix.qr_code_text) {
                pixCodeText.value = pix.qr_code_text;
            }

            // Valor em BRL
            const pixAmountEl = document.getElementById('pixAmountBRL');
            if (pixAmountEl && pix.amount_brl) {
                pixAmountEl.textContent = 'R$ ' + pix.amount_brl.toFixed(2).replace('.', ',');
            }

            // Timer countdown (30 min)
            let timeLeft = 30 * 60;
            const timerEl = document.getElementById('pixTimer');
            const countdown = setInterval(() => {
                timeLeft--;
                const mins = Math.floor(timeLeft / 60);
                const secs = timeLeft % 60;
                if (timerEl) timerEl.textContent = mins.toString().padStart(2, '0') + ':' + secs.toString().padStart(2, '0');
                if (timeLeft <= 0) { clearInterval(countdown); if (timerEl) timerEl.textContent = 'Expirado'; }
            }, 1000);

            // Polling para verificar pagamento (a cada 5 seg)
            const paymentId = response.payment_id;
            const bookingNumber = response.booking_number;
            const checkInterval = setInterval(() => {
                ajax('/api/webhook/pix-status', { body: JSON.stringify({ payment_id: paymentId }) })
                    .then(statusData => {
                        if (statusData.paid) {
                            clearInterval(checkInterval);
                            clearInterval(countdown);
                            const statusEl = document.getElementById('pixStatus');
                            if (statusEl) { statusEl.textContent = 'Pagamento confirmado! Redirecionando...'; statusEl.style.color = '#16a34a'; }
                            setTimeout(() => { window.location = '/checkout/sucesso/' + bookingNumber; }, 2000);
                        }
                    }).catch(() => {});
            }, 5000);
        }
    }

    function capturePayPal(response) {
        document.getElementById('checkoutLoading').style.display = 'none';

        // Usar PayPal JS SDK para que o cliente aprove a order
        if (typeof paypal === 'undefined') {
            toast('PayPal não carregou. Recarregue a página.', 'error');
            return;
        }

        // Esconder botão de submit e mostrar PayPal buttons
        document.getElementById('paymentContainer').style.display = 'none';
        var paypalContainer = document.getElementById('paypalButtonContainer');
        paypalContainer.style.display = 'block';
        paypalContainer.innerHTML = '';

        paypal.Buttons({
            createOrder: function() {
                // Order já foi criada server-side, retornar o ID
                return response.paypal_order_id;
            },
            onApprove: function(data) {
                // Cliente aprovou - capturar server-side
                document.getElementById('checkoutLoading').style.display = 'flex';
                paypalContainer.style.display = 'none';
                ajax('/api/webhook/payment', { body: JSON.stringify({ gateway: 'paypal', payment_id: response.payment_id, transaction_id: data.orderID }) })
                    .then(function() { window.location = '/checkout/sucesso/' + response.booking_number; })
                    .catch(function() { window.location = '/checkout/sucesso/' + response.booking_number; });
            },
            onCancel: function() {
                toast('Pagamento cancelado. Tente novamente.', 'warning');
                paypalContainer.style.display = 'none';
                document.getElementById('paymentContainer').style.display = 'block';
            },
            onError: function(err) {
                toast('Erro no PayPal. Tente novamente.', 'error');
                paypalContainer.style.display = 'none';
                document.getElementById('paymentContainer').style.display = 'block';
            }
        }).render('#paypalButtonContainer');
    }

    function handleStripePayment(response) {
        if (typeof Stripe === 'undefined') { alert('Stripe não carregado.'); return; }
        const stripe = Stripe(typeof CHECKOUT_CONFIG !== 'undefined' ? CHECKOUT_CONFIG.stripePublishableKey : '');
        stripe.confirmCardPayment(response.stripe_client_secret).then(result => {
            if (result.error) { document.getElementById('checkoutLoading').style.display = 'none'; toast(result.error.message, 'error'); }
            else { window.location = '/checkout/sucesso/' + response.booking_number; }
        });
    }
})();

// ==================== BOOKING MODAL ====================
(function() {
    const modal = document.getElementById('bookingModal');
    if (!modal) return;

    const CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let tripData = typeof PACKAGES !== 'undefined' ? PACKAGES : [];
    let tripTitle = document.querySelector('.trip-title')?.textContent || '';
    let selectedHotel = null;
    let selectedPickupTime = null;
    let selectedDate = null;
    let selectedTime = null;
    let selectedPackage = null;
    let travelerCounts = {};
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    let hotelsData = [];

    // Abrir modal
    document.querySelectorAll('.btn-verificar').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'flex';
            document.getElementById('bmSidebarTitle').textContent = tripTitle;
            const codeEl = document.getElementById('bmSidebarCode');
            if (codeEl && typeof TRIP_ID !== 'undefined') {
                codeEl.innerHTML = '<span class="bm-code-badge">C\u00F3digo Da Viagem: WTE-' + (8000 + TRIP_ID) + '</span>';
            }
            loadHotels();
        });
    });

    // Fechar modal
    document.getElementById('bookingModalClose')?.addEventListener('click', () => { modal.style.display = 'none'; });
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });

    // ===== STEP 1: Hotel & Horário de Busca =====

    function loadHotels() {
        if (typeof TRIP_ID === 'undefined') return;
        if (hotelsData.length > 0) return; // já carregou
        const loading = document.getElementById('bmHotelLoading');
        loading.style.display = 'block';
        document.getElementById('bmHotelHint').style.display = 'none';
        fetch('/api/schedules/' + TRIP_ID, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
            .then(data => {
                loading.style.display = 'none';
                document.getElementById('bmHotelHint').style.display = 'block';
                if (data.success && data.hotels && data.hotels.length > 0) { hotelsData = data.hotels; }
                else { hotelsData = []; }
            }).catch(err => {
                loading.style.display = 'none';
                document.getElementById('bmHotelHint').innerHTML = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin:0 auto 8px;display:block;color:#e74c3c;"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>Erro ao carregar hot\u00E9is. ' + err.message;
            });
    }

    function renderHotelList(hotels) {
        const list = document.getElementById('bmHotelList');
        if (!hotels.length) {
            list.innerHTML = '<div class="bm-hotel-empty"><svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:block;margin:0 auto 8px;color:#bbb;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>Nenhum hotel encontrado com esse nome.</div>';
            return;
        }
        list.innerHTML = hotels.map(h => `<div class="bm-hotel-item ${selectedHotel && selectedHotel.id === h.id ? 'selected' : ''}" data-hotel-id="${h.id}" onclick="selectHotel(${h.id})"><div class="bm-hotel-item-icon"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l8-4v18"/><path d="M19 21V11l-6-4"/></svg></div><div class="bm-hotel-item-content"><span class="bm-hotel-name notranslate" translate="no">${h.hotel_name}</span><span class="bm-hotel-times-count">${h.schedules.length} hor\u00E1rio(s) de busca</span></div><div class="bm-hotel-check"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"/></svg></div></div>`).join('');
    }


    document.getElementById('bmHotelSearch')?.addEventListener('input', function() {
        const q = this.value.toLowerCase().trim();
        const hint = document.getElementById('bmHotelHint');
        if (q.length < 2) {
            // Esconde resultados e mostra hint
            hint.style.display = 'block';
            hint.innerHTML = '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="display:block;margin:0 auto 8px;color:#bbb;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>Digite pelo menos 2 letras para buscar';
            // Limpar lista renderizada
            const items = document.querySelectorAll('.bm-hotel-item');
            items.forEach(el => el.remove());
            return;
        }
        hint.style.display = 'none';
        const filtered = hotelsData.filter(h => h.hotel_name.toLowerCase().includes(q));
        renderHotelList(filtered);
    });

    window.selectHotel = function(hotelId) {
        selectedHotel = hotelsData.find(h => h.id === hotelId); selectedPickupTime = null;
        if (!selectedHotel) return;
        document.querySelectorAll('.bm-hotel-item').forEach(el => el.classList.remove('selected'));
        document.querySelector(`.bm-hotel-item[data-hotel-id="${hotelId}"]`)?.classList.add('selected');
        const section = document.getElementById('bmPickupSection');
        document.getElementById('bmPickupTimes').innerHTML = selectedHotel.schedules.map(s => `<button type="button" class="bm-pickup-time-btn" data-time="${s.time}" onclick="selectPickupTime('${s.time}')">${s.time}</button>`).join('');
        section.style.display = 'block';
        document.getElementById('bmContinueStep1').disabled = true;
        updateSidebar();
    };

    window.selectPickupTime = function(time) {
        selectedPickupTime = time;
        document.querySelectorAll('.bm-pickup-time-btn').forEach(el => el.classList.remove('selected'));
        document.querySelector(`.bm-pickup-time-btn[data-time="${time}"]`)?.classList.add('selected');
        document.getElementById('bmContinueStep1').disabled = false;
        updateSidebar();
    };

    document.getElementById('bmContinueStep1')?.addEventListener('click', () => { goToStep(2); renderCalendar(); });

    // ===== STEP 2: Data =====
    document.getElementById('bmPrevMonth')?.addEventListener('click', () => { currentMonth--; if (currentMonth < 0) { currentMonth = 11; currentYear--; } renderCalendar(); });
    document.getElementById('bmNextMonth')?.addEventListener('click', () => { currentMonth++; if (currentMonth > 11) { currentMonth = 0; currentYear++; } renderCalendar(); });

    function renderCalendar() {
        const months = ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'];
        const monthSelect = document.getElementById('bmMonth');
        monthSelect.innerHTML = months.map((m, i) => `<option value="${i}" ${i === currentMonth ? 'selected' : ''}>${m}</option>`).join('');
        monthSelect.onchange = function() { currentMonth = parseInt(this.value); renderCalendar(); };
        document.getElementById('bmYear').textContent = currentYear;

        const daysContainer = document.getElementById('bmDays');
        const firstDay = new Date(currentYear, currentMonth, 1).getDay();
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        const today = new Date();

        let html = '';
        for (let i = 0; i < firstDay; i++) html += '<div class="bm-day"></div>';
        for (let d = 1; d <= daysInMonth; d++) {
            const date = new Date(currentYear, currentMonth, d);
            const isPast = date < new Date(today.getFullYear(), today.getMonth(), today.getDate());
            const isToday = d === today.getDate() && currentMonth === today.getMonth() && currentYear === today.getFullYear();
            const dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            const isSelected = selectedDate === dateStr;
            let classes = 'bm-day';
            if (isPast) classes += ' disabled';
            else classes += ' available';
            if (isToday && !isSelected) classes += ' today';
            if (isSelected) classes += ' selected';
            html += `<div class="${classes}" data-date="${dateStr}" onclick="selectBookingDate('${dateStr}')">${d}</div>`;
        }
        daysContainer.innerHTML = html;
    }

    window.selectBookingDate = function(dateStr) {
        selectedDate = dateStr;
        renderCalendar();
        showTimeSlots(dateStr);
        updateSidebar();
        document.getElementById('bmContinue').disabled = false;
    };

    function showTimeSlots(dateStr) {
        const container = document.getElementById('bmTimes');
        const list = document.getElementById('bmTimesList');
        // Horários do passeio (os mesmos de antes, independente do horário de busca no hotel)
        const slots = ['9:00 AM – 12:00 PM', '12:00 PM – 3:00 PM', '3:00 PM – 6:00 PM'];
        list.innerHTML = slots.map(s => `<button type="button" class="bm-time-slot ${selectedTime === s ? 'selected' : ''}" onclick="selectBookingTime('${s}')">${s}</button>`).join('');
        container.style.display = 'block';
    }

    window.selectBookingTime = function(time) {
        selectedTime = time;
        document.querySelectorAll('.bm-time-slot').forEach(el => el.classList.remove('selected'));
        if (event && event.target) event.target.classList.add('selected');
        updateSidebar();
    };

    // Voltar Step 2 → Step 1
    document.getElementById('bmBackStep2')?.addEventListener('click', () => { goToStep(1); });

    // Continuar para Step 3
    document.getElementById('bmContinue')?.addEventListener('click', () => {
        document.getElementById('bmStep2').classList.remove('active');
        document.getElementById('bmStep3').classList.add('active');
        document.getElementById('bmTab2').classList.remove('active');
        document.getElementById('bmTab3').classList.add('active');
        renderPackages();
    });

    // Voltar para Step 2
    document.getElementById('bmBack')?.addEventListener('click', () => { goToStep(2); });

    function renderPackages() {
        const container = document.getElementById('bmPackages');
        if (!tripData.length) return;
        container.innerHTML = tripData.map((pkg, i) =>
            `<button type="button" class="bm-package-btn ${i === 0 ? 'selected' : ''}" data-pkg="${i}" onclick="selectBookingPackage(${i})">${pkg.title}</button>`
        ).join('');
        selectBookingPackage(0);
    }

    window.selectBookingPackage = function(index) {
        selectedPackage = tripData[index];
        document.querySelectorAll('.bm-package-btn').forEach(el => el.classList.remove('selected'));
        document.querySelector(`.bm-package-btn[data-pkg="${index}"]`)?.classList.add('selected');
        renderTravelers();
        updateSidebar();
    };

    function renderTravelers() {
        const container = document.getElementById('bmTravelersList');
        if (!selectedPackage) return;

        // Verificar se composition pricing está ativo
        const compEnabled = typeof COMPOSITION_PRICING_ENABLED !== 'undefined' && COMPOSITION_PRICING_ENABLED;
        const compPackages = typeof COMPOSITION_PACKAGES !== 'undefined' ? COMPOSITION_PACKAGES : [];

        if (compEnabled && compPackages.length > 0) {
            // ─── MODO COMPOSIÇÃO: primeiro viajantes, depois pacotes filtrados ───
            window._selectedCompositionPkg = null;

            // Montar seletores de viajantes primeiro
            let cats = selectedPackage.categories || [];
            const filtered = cats.filter(c => {
                const slug = (c.category_slug || '').toLowerCase();
                return slug === 'adulto' || slug === 'crianca' || slug === 'infantil';
            });
            cats = filtered.length > 0 ? filtered : [
                { traveler_category_id: 0, category_name: 'Adulto', category_slug: 'adulto', age_group: '18-85', price: '0', sale_price: null },
                { traveler_category_id: 1, category_name: 'Criança', category_slug: 'crianca', age_group: '4-11', price: '0', sale_price: null },
                { traveler_category_id: 2, category_name: 'Infantil', category_slug: 'infantil', age_group: '0-3', price: '0', sale_price: null }
            ];
            const seen = {};
            cats = cats.filter(c => { const k = (c.category_slug || c.category_name || '').toLowerCase(); if (seen[k]) return false; seen[k] = true; return true; });

            travelerCounts = {};

            // Accordion: Viajantes (aberto por padrão)
            let travelersHtml = `<div class="bm-accordion">
                <div class="bm-accordion-header active" onclick="toggleAccordion(this)">
                    <span>Viajantes</span>
                    <svg class="bm-accordion-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="bm-accordion-body" style="display:block;">`;

            travelersHtml += cats.map(cat => {
                const catId = cat.traveler_category_id || cat.id || 0;
                const defaultQty = (cat.category_slug || '').toLowerCase() === 'adulto' ? 1 : 0;
                travelerCounts[catId] = defaultQty;
                return `<div class="bm-traveler-row">
                    <div class="bm-traveler-info">
                        <span class="bm-traveler-name">${cat.category_name}${cat.age_group ? ' (' + cat.age_group + ')' : ''}</span>
                    </div>
                    <div class="bm-traveler-counter">
                        <button type="button" onclick="changeTraveler(${catId}, -1)">&#8722;</button>
                        <input type="text" value="${defaultQty}" id="traveler_${catId}" readonly>
                        <button type="button" onclick="changeTraveler(${catId}, 1)">&#43;</button>
                    </div>
                </div>`;
            }).join('');

            travelersHtml += `</div></div>`;

            // Accordion: Composição (fechado por padrão, abre quando seleciona viajantes)
            travelersHtml += `<div class="bm-accordion">
                <div class="bm-accordion-header" onclick="toggleAccordion(this)">
                    <span>Pacotes / Composição</span>
                    <svg class="bm-accordion-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                </div>
                <div class="bm-accordion-body" style="display:none;">
                    <div id="compositionOptionsContainer"></div>
                </div>
            </div>`;

            // Accordion: Acompanhante
            const companionAccHtml = getCompanionHtml();
            if (companionAccHtml) {
                travelersHtml += `<div class="bm-accordion">
                    <div class="bm-accordion-header" onclick="toggleAccordion(this)">
                        <span>${(typeof COMPANION_CONFIG !== 'undefined' && COMPANION_CONFIG.label) || 'Acompanhante'}</span>
                        <svg class="bm-accordion-arrow" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                    <div class="bm-accordion-body" style="display:none;">
                        ${companionAccHtml}
                    </div>
                </div>`;
            }

            container.innerHTML = travelersHtml;
            updateCompositionOptions();
            updateSidebar();
            return;
        }

        let cats = selectedPackage.categories || [];

        // Filtrar para mostrar apenas Adulto, Criança e Infantil
        const filtered = cats.filter(c => {
            const slug = (c.category_slug || '').toLowerCase();
            return slug === 'adulto' || slug === 'crianca' || slug === 'infantil';
        });

        // Usar filtrado se tem resultados, senão fallback
        cats = filtered.length > 0 ? filtered : [
            { traveler_category_id: 0, category_name: 'Adulto', category_slug: 'adulto', age_group: '18-85', price: selectedPackage.base_price || '0', sale_price: null },
            { traveler_category_id: 1, category_name: 'Criança', category_slug: 'crianca', age_group: '4-11', price: selectedPackage.base_price ? (parseFloat(selectedPackage.base_price) * 0.5).toFixed(2) : '0', sale_price: null },
            { traveler_category_id: 2, category_name: 'Infantil', category_slug: 'infantil', age_group: '0-3', price: '0', sale_price: null }
        ];

        // Remover duplicatas por category_slug
        const seen = {};
        cats = cats.filter(c => {
            const key = (c.category_slug || c.category_name || '').toLowerCase();
            if (seen[key]) return false;
            seen[key] = true;
            return true;
        });

        // Verificar se group pricing está ativo
        const gpEnabled = typeof GROUP_PRICING_ENABLED !== 'undefined' && GROUP_PRICING_ENABLED;
        const gpTable = typeof GROUP_PRICING_TABLE !== 'undefined' ? GROUP_PRICING_TABLE : [];

        travelerCounts = {};

        // Se group pricing ativo, mostrar tabela de preços por grupo acima dos seletores
        let gpHtml = '';
        if (gpEnabled && gpTable.length > 0) {
            gpHtml = '<div class="bm-group-pricing-info" style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:8px;padding:12px 14px;margin-bottom:12px;"><div style="font-size:13px;font-weight:600;color:#1e40af;margin-bottom:6px;">Preço por Grupo (Adultos):</div>';
            gpTable.forEach(gp => {
                gpHtml += `<div style="display:flex;justify-content:space-between;font-size:13px;color:#334155;padding:2px 0;"><span>${gp.pax} adulto${parseInt(gp.pax) > 1 ? 's' : ''}</span><span style="font-weight:600;">$${parseFloat(gp.price).toFixed(2)}</span></div>`;
            });
            gpHtml += '</div>';
        }

        container.innerHTML = gpHtml + cats.map(cat => {
            const catId = cat.traveler_category_id || cat.id || 0;
            const defaultQty = (cat.category_slug || '').toLowerCase() === 'adulto' ? 1 : 0;
            travelerCounts[catId] = defaultQty;
            const price = parseFloat(cat.sale_price || cat.price || 0);
            const slug = (cat.category_slug || '').toLowerCase();
            let priceLabel = '';
            if (gpEnabled && slug === 'adulto') {
                priceLabel = 'Preço por grupo (ver tabela)';
            } else {
                priceLabel = price > 0 ? '$' + price.toFixed(2) + ' / Pessoa' : 'Gratuito';
            }
            return `<div class="bm-traveler-row">
                <div class="bm-traveler-info">
                    <span class="bm-traveler-name">${cat.category_name}${cat.age_group ? ' (' + cat.age_group + ')' : ''}</span>
                    <span class="bm-traveler-price">${priceLabel}</span>
                </div>
                <div class="bm-traveler-counter">
                    <button type="button" onclick="changeTraveler(${catId}, -1)">&#8722;</button>
                    <input type="text" value="${defaultQty}" id="traveler_${catId}" readonly>
                    <button type="button" onclick="changeTraveler(${catId}, 1)">&#43;</button>
                </div>
            </div>`;
        }).join('') + getCompanionHtml();
        updateSidebar();
    }

    // Toggle accordion
    window.toggleAccordion = function(header) {
        const body = header.nextElementSibling;
        const isOpen = body.style.display !== 'none';
        body.style.display = isOpen ? 'none' : 'block';
        header.classList.toggle('active', !isOpen);
    };

    // Seleção de pacote de composição
    window.selectCompositionPkg = function(pkgId, el) {
        window._selectedCompositionPkg = pkgId;
        document.querySelectorAll('.bm-composition-option').forEach(opt => {
            opt.style.background = '#f9fafb';
            opt.style.borderColor = '#e5e7eb';
        });
        el.style.background = '#eff6ff';
        el.style.borderColor = '#3b82f6';
        updateSidebar();
    };

    // Acompanhantes
    window._companionCount = 0;

    window.changeCompanion = function(delta) {
        const cfg = typeof COMPANION_CONFIG !== 'undefined' ? COMPANION_CONFIG : null;
        if (!cfg || !cfg.enabled) return;

        let val = window._companionCount + delta;
        if (val < 0) val = 0;

        // Limite por participante
        if (cfg.max_per_participant) {
            let totalParticipants = 0;
            Object.keys(travelerCounts).forEach(k => { totalParticipants += (travelerCounts[k] || 0); });
            const maxByParticipant = totalParticipants * cfg.max_per_participant;
            if (val > maxByParticipant) val = maxByParticipant;
        }

        // Limite total
        if (cfg.max_total && val > cfg.max_total) val = cfg.max_total;

        window._companionCount = val;
        const el = document.getElementById('companion_count');
        if (el) el.value = val;
        updateSidebar();
    };

    // Renderiza o seletor de acompanhantes (chamado dentro do renderTravelers)
    function getCompanionHtml() {
        const cfg = typeof COMPANION_CONFIG !== 'undefined' ? COMPANION_CONFIG : null;
        if (!cfg || !cfg.enabled) return '';

        window._companionCount = 0;
        let html = '<div style="margin-top:14px;padding-top:12px;border-top:1px solid #e5e7eb;">';
        html += `<div class="bm-traveler-row">
            <div class="bm-traveler-info">
                <span class="bm-traveler-name">${cfg.label}</span>
                <span class="bm-traveler-price">${cfg.price > 0 ? '$' + parseFloat(cfg.price).toFixed(2) + ' / pessoa' : 'Gratuito'}</span>
            </div>
            <div class="bm-traveler-counter">
                <button type="button" onclick="changeCompanion(-1)">&#8722;</button>
                <input type="text" value="0" id="companion_count" readonly>
                <button type="button" onclick="changeCompanion(1)">&#43;</button>
            </div>
        </div>`;
        if (cfg.description) {
            html += `<p style="font-size:11px;color:#6b7280;margin:6px 0 0;padding-left:2px;">${cfg.description}</p>`;
        }
        if (cfg.max_per_participant) {
            html += `<p style="font-size:11px;color:#94a3b8;margin:2px 0 0;padding-left:2px;">Máx. ${cfg.max_per_participant} por participante</p>`;
        }
        html += '</div>';
        return html;
    }

    window.changeTraveler = function(catId, delta) {
        let val = (travelerCounts[catId] || 0) + delta;
        if (val < 0) val = 0;
        travelerCounts[catId] = val;
        document.getElementById('traveler_' + catId).value = val;
        updateCompositionOptions();
        updateSidebar();
    };

    // Atualiza as opções de composição baseado no total de viajantes selecionados
    function updateCompositionOptions() {
        const compContainer = document.getElementById('compositionOptionsContainer');
        if (!compContainer) return;

        const compEnabled = typeof COMPOSITION_PRICING_ENABLED !== 'undefined' && COMPOSITION_PRICING_ENABLED;
        const compPackages = typeof COMPOSITION_PACKAGES !== 'undefined' ? COMPOSITION_PACKAGES : [];
        if (!compEnabled || compPackages.length === 0) return;

        // Calcular total de viajantes
        let totalPax = 0;
        Object.keys(travelerCounts).forEach(k => { totalPax += (travelerCounts[k] || 0); });

        // Filtrar pacotes que atendem a quantidade de viajantes
        const available = compPackages.filter(cp => parseInt(cp.pax) === totalPax);

        // Abrir accordion de composição automaticamente se tem opções
        const compAccordion = compContainer.closest('.bm-accordion-body');
        if (compAccordion && available.length > 0) {
            compAccordion.style.display = 'block';
            const header = compAccordion.previousElementSibling;
            if (header) header.classList.add('active');
        }

        if (totalPax === 0) {
            compContainer.innerHTML = '<div style="font-size:13px;color:#94a3b8;padding:8px 0;">Selecione a quantidade de viajantes acima.</div>';
            window._selectedCompositionPkg = null;
            return;
        }

        if (available.length === 0) {
            compContainer.innerHTML = '<div style="font-size:13px;color:#ef4444;padding:8px 0;">Nenhum pacote disponível para ' + totalPax + ' viajante' + (totalPax > 1 ? 's' : '') + '.</div>';
            window._selectedCompositionPkg = null;
            return;
        }

        // Selecionar o primeiro por padrão
        window._selectedCompositionPkg = available[0].id;

        let html = '<div style="font-size:13px;font-weight:600;color:#1e40af;margin-bottom:10px;">Escolha a composição para ' + totalPax + ' viajante' + (totalPax > 1 ? 's' : '') + ':</div>';
        html += available.map((cp, idx) => {
            const unitInfo = cp.unit_label ? (cp.units + ' ' + cp.unit_label + (parseInt(cp.units) > 1 ? 's' : '')) : '';
            const desc = cp.label || (cp.pax + ' pessoa' + (parseInt(cp.pax) > 1 ? 's' : '') + (unitInfo ? ' em ' + unitInfo : ''));
            return `<label class="bm-composition-option" style="display:flex;align-items:center;gap:12px;padding:12px 14px;background:${idx === 0 ? '#eff6ff' : '#f9fafb'};border:2px solid ${idx === 0 ? '#3b82f6' : '#e5e7eb'};border-radius:10px;margin-bottom:8px;cursor:pointer;transition:all .15s;" onclick="selectCompositionPkg(${cp.id}, this)">
                <input type="radio" name="composition_pkg_radio" value="${cp.id}" ${idx === 0 ? 'checked' : ''} style="accent-color:#3b82f6;width:18px;height:18px;">
                <div style="flex:1;">
                    <div style="font-size:14px;font-weight:600;color:#1e293b;">${desc}</div>
                    ${unitInfo && cp.label ? '<div style="font-size:12px;color:#64748b;">' + unitInfo + (cp.pax_per_unit ? ' (' + cp.pax_per_unit + ' por unid)' : '') + '</div>' : ''}
                </div>
                <div style="font-size:16px;font-weight:700;color:#059669;">$${parseFloat(cp.price).toFixed(2)}</div>
            </label>`;
        }).join('');

        compContainer.innerHTML = html;
    }

    function updateSidebar() {
        // Hotel
        const hotelEl = document.getElementById('bmSidebarHotel');
        const hotelNameEl = document.getElementById('bmSidebarHotelName');
        const pickupEl = document.getElementById('bmSidebarPickupTime');
        if (selectedHotel) {
            hotelEl.style.display = 'block';
            hotelNameEl.textContent = 'Hotel: ' + selectedHotel.hotel_name;
            pickupEl.textContent = selectedPickupTime ? 'Busca: ' + selectedPickupTime : '';
        }
        // Date
        const dateEl = document.getElementById('bmSidebarDate');
        if (selectedDate) {
            const [y,m,d] = selectedDate.split('-');
            dateEl.textContent = `Data De In\u00EDcio: ${d}/${m}/${y}${selectedTime ? ' \u00E0s ' + selectedTime : ''}`;
        }
        // Package
        const pkgEl = document.getElementById('bmSidebarPackage');
        const pkgNameEl = document.getElementById('bmSidebarPackageName');
        if (selectedPackage) {
            pkgEl.style.display = 'block';
            pkgNameEl.textContent = 'Pacote: ' + selectedPackage.title;
        }
        // Travelers & Total
        const travDiv = document.getElementById('bmSidebarTravelers');
        const travList = document.getElementById('bmSidebarTravelersList');
        const totalEl = document.getElementById('bmSidebarTotal');
        let total = 0;
        let travHtml = '';

        // Verificar composition pricing
        const compEnabled = typeof COMPOSITION_PRICING_ENABLED !== 'undefined' && COMPOSITION_PRICING_ENABLED;
        const compPackages = typeof COMPOSITION_PACKAGES !== 'undefined' ? COMPOSITION_PACKAGES : [];

        // Verificar group pricing
        const gpEnabled = typeof GROUP_PRICING_ENABLED !== 'undefined' && GROUP_PRICING_ENABLED;
        const gpTable = typeof GROUP_PRICING_TABLE !== 'undefined' ? GROUP_PRICING_TABLE : [];

        if (compEnabled && compPackages.length > 0 && window._selectedCompositionPkg) {
            // ─── MODO COMPOSIÇÃO ───
            const selPkg = compPackages.find(p => p.id == window._selectedCompositionPkg);
            if (selPkg) {
                total = parseFloat(selPkg.price);
                const desc = selPkg.label || (selPkg.pax + ' pessoa(s)');
                travHtml = `<div class="bm-sidebar-traveler-line"><span>${desc}</span><span>$${total.toFixed(2)}</span></div>`;
            }
        } else if (selectedPackage) {
            // Preparar categorias
            let cats = selectedPackage.categories || [];
            const filtered = cats.filter(c => {
                const slug = (c.category_slug || '').toLowerCase();
                return slug === 'adulto' || slug === 'crianca' || slug === 'infantil';
            });
            cats = filtered.length > 0 ? filtered : [
                { traveler_category_id: 0, category_name: 'Adulto', category_slug: 'adulto', price: selectedPackage.base_price || '0', sale_price: null },
                { traveler_category_id: 1, category_name: 'Criança', category_slug: 'crianca', price: selectedPackage.base_price ? (parseFloat(selectedPackage.base_price) * 0.5).toFixed(2) : '0', sale_price: null },
                { traveler_category_id: 2, category_name: 'Infantil', category_slug: 'infantil', price: '0', sale_price: null }
            ];
            // Deduplicar
            const seen = {};
            cats = cats.filter(c => { const k = (c.category_slug || c.category_name || '').toLowerCase(); if (seen[k]) return false; seen[k] = true; return true; });

            if (gpEnabled && gpTable.length > 0) {
                // Modo GROUP PRICING: tabela aplica APENAS para adultos
                // Criança/infantil somam preço normal
                let adultPax = 0;
                let childTotal = 0;

                cats.forEach(cat => {
                    const catId = cat.traveler_category_id || cat.id || 0;
                    const qty = travelerCounts[catId] || 0;
                    if (qty <= 0) return;
                    const slug = (cat.category_slug || '').toLowerCase();

                    if (slug === 'adulto') {
                        adultPax += qty;
                    } else {
                        // Criança, infantil — preço por pessoa normal
                        const price = parseFloat(cat.sale_price || cat.price || 0);
                        const line = price * qty;
                        childTotal += line;
                        if (price > 0) {
                            travHtml += `<div class="bm-sidebar-traveler-line"><span>${cat.category_name}: ${qty}</span><span>$${line.toFixed(2)}</span></div>`;
                        } else {
                            travHtml += `<div class="bm-sidebar-traveler-line"><span>${cat.category_name}: ${qty}</span><span>Gratuito</span></div>`;
                        }
                    }
                });

                // Resolver preço de grupo para adultos
                if (adultPax > 0) {
                    let groupPrice = null;
                    // Match exato
                    for (let i = 0; i < gpTable.length; i++) {
                        if (parseInt(gpTable[i].pax) === adultPax) { groupPrice = parseFloat(gpTable[i].price); break; }
                    }
                    // Nearest lower
                    if (groupPrice === null) {
                        const sorted = [...gpTable].sort((a, b) => parseInt(a.pax) - parseInt(b.pax));
                        for (let i = sorted.length - 1; i >= 0; i--) {
                            if (parseInt(sorted[i].pax) <= adultPax) { groupPrice = parseFloat(sorted[i].price); break; }
                        }
                        if (groupPrice === null && sorted.length > 0) groupPrice = parseFloat(sorted[0].price);
                    }
                    if (groupPrice !== null) {
                        travHtml = `<div class="bm-sidebar-traveler-line"><span>Adulto${adultPax > 1 ? 's' : ''} (${adultPax})</span><span>$${groupPrice.toFixed(2)}</span></div>` + travHtml;
                        total = groupPrice + childTotal;
                    } else {
                        total = childTotal;
                    }
                } else {
                    total = childTotal;
                }
            } else {
                // Modo PER-PERSON: preço por categoria × quantidade
                cats.forEach(cat => {
                    const catId = cat.traveler_category_id || cat.id || 0;
                    const qty = travelerCounts[catId] || 0;
                    if (qty > 0) {
                        const price = parseFloat(cat.sale_price || cat.price || 0);
                        const line = price * qty;
                        total += line;
                        if (price > 0) {
                            travHtml += `<div class="bm-sidebar-traveler-line"><span>${cat.category_name}: ${qty}</span><span>$${line.toFixed(2)}</span></div>`;
                        } else {
                            travHtml += `<div class="bm-sidebar-traveler-line"><span>${cat.category_name}: ${qty}</span><span>Gratuito</span></div>`;
                        }
                    }
                });
            }
        }
        if (travHtml) {
            travDiv.style.display = 'block';
            travList.innerHTML = travHtml;
        }

        // Somar acompanhantes ao total
        const companionCfg = typeof COMPANION_CONFIG !== 'undefined' ? COMPANION_CONFIG : null;
        const companionQty = window._companionCount || 0;
        let companionTotal = 0;
        if (companionCfg && companionCfg.enabled && companionQty > 0) {
            companionTotal = companionQty * (companionCfg.price || 0);
            total += companionTotal;
            // Adicionar linha no sidebar
            const companionLine = `<div class="bm-sidebar-traveler-line"><span>${companionCfg.label}: ${companionQty}</span><span>${companionCfg.price > 0 ? '$' + companionTotal.toFixed(2) : 'Gratuito'}</span></div>`;
            if (travList) travList.innerHTML += companionLine;
        }

        totalEl.textContent = '$' + total.toFixed(0);

        // Mostrar informação de pagamento parcial
        let partialEl = document.getElementById('bmSidebarPartial');
        const ppEnabled = typeof PARTIAL_PAYMENT_ENABLED !== 'undefined' && PARTIAL_PAYMENT_ENABLED;
        const ppPercent = typeof PARTIAL_PAYMENT_PERCENT !== 'undefined' ? PARTIAL_PAYMENT_PERCENT : 50;
        if (partialEl && ppEnabled && total > 0) {
            const partialAmount = Math.round(total * ppPercent / 100);
            const restante = total - partialAmount;
            partialEl.innerHTML = `<div style="padding:10px 14px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;margin-bottom:12px;">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px;">
                    <span style="font-size:13px;color:#166534;font-weight:600;">Pague agora (${ppPercent}%)</span>
                    <span style="font-size:15px;color:#166534;font-weight:700;">$${partialAmount}</span>
                </div>
                <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:12px;color:#6b7280;">Restante no local</span>
                    <span style="font-size:13px;color:#6b7280;">$${restante}</span>
                </div>
            </div>`;
            partialEl.style.display = '';
        } else if (partialEl) {
            partialEl.style.display = 'none';
        }
    }

    // Navigation helper
    function goToStep(step) {
        document.querySelectorAll('.bm-step').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.bm-tab').forEach(el => el.classList.remove('active'));
        document.getElementById('bmStep' + step).classList.add('active');
        document.getElementById('bmTab' + step).classList.add('active');
    }

    // Add to Cart
    document.getElementById('bmAddCart')?.addEventListener('click', () => {
        submitBooking('cart');
    });
    document.getElementById('bmCheckout')?.addEventListener('click', () => {
        submitBooking('checkout');
    });
    document.getElementById('bmDirectCheckout')?.addEventListener('click', () => {
        submitBooking('checkout');
    });

    function submitBooking(redirect) {
        if (!selectedDate || !selectedPackage) { alert('Selecione data e pacote.'); return; }

        // Verificar se é modo composição
        const compEnabled = typeof COMPOSITION_PRICING_ENABLED !== 'undefined' && COMPOSITION_PRICING_ENABLED;
        const compPackages = typeof COMPOSITION_PACKAGES !== 'undefined' ? COMPOSITION_PACKAGES : [];

        const pax = {};
        let compositionPkgId = '';

        if (compEnabled && compPackages.length > 0 && window._selectedCompositionPkg) {
            // Modo composição: enviar pacote selecionado + viajantes
            compositionPkgId = window._selectedCompositionPkg;
            Object.keys(travelerCounts).forEach(k => { if (travelerCounts[k] > 0) pax[k] = travelerCounts[k]; });
        } else {
            Object.keys(travelerCounts).forEach(k => { if (travelerCounts[k] > 0) pax[k] = travelerCounts[k]; });
        }

        if (Object.keys(pax).length === 0) { alert('Selecione pelo menos 1 viajante.'); return; }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/carrinho/adicionar';
        form.innerHTML = `<input name="_token" value="${CSRF}">
            <input name="trip_id" value="${typeof TRIP_ID !== 'undefined' ? TRIP_ID : ''}">
            <input name="package_id" value="${selectedPackage.id}">
            <input name="composition_package_id" value="${compositionPkgId}">
            <input name="date" value="${selectedDate}">
            <input name="time" value="${selectedTime || ''}">
            <input name="hotel_id" value="${selectedHotel ? selectedHotel.id : ''}">
            <input name="hotel_name" value="${selectedHotel ? selectedHotel.hotel_name : ''}">
            <input name="pickup_time" value="${selectedPickupTime || ''}">
            <input name="redirect" value="${redirect}">
            <input name="companion_count" value="${window._companionCount || 0}">`;
        Object.entries(pax).forEach(([k, v]) => { form.innerHTML += `<input name="pax[${k}]" value="${v}">`; });
        document.body.appendChild(form);
        form.submit();
    }
})();


// ==================== CUSTOM DROPDOWN (para filtros CTA) ====================
(function() {
    var selects = document.querySelectorAll('.filtro-field .filtro-select');
    if (!selects.length) return;

    selects.forEach(function(select) {
        var wrapper = document.createElement('div');
        wrapper.className = 'custom-dropdown';
        wrapper.style.cssText = 'position:relative; flex:1;';

        var btn = document.createElement('div');
        btn.className = 'custom-dropdown-btn';
        btn.textContent = select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '';
        btn.style.cssText = 'cursor:pointer; font-size:15px; font-family:inherit; color:#1C2011; padding-right:18px; background-image:url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' fill=\'%23666\'%3E%3Cpath d=\'M6 8L1 3h10z\'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right 0 center;';

        var list = document.createElement('div');
        list.className = 'custom-dropdown-list';
        list.style.cssText = 'display:none; position:absolute; top:calc(100% + 12px); left:-20px; right:-20px; background:#fff; border:1px solid #e0e0e0; border-radius:10px; box-shadow:0 4px 16px rgba(0,0,0,0.1); z-index:100; max-height:280px; overflow-y:auto;';

        for (var i = 0; i < select.options.length; i++) {
            var opt = select.options[i];
            if (i === 0 && opt.value === '') continue;
            var item = document.createElement('div');
            item.className = 'custom-dropdown-item';
            item.textContent = opt.text;
            item.setAttribute('data-value', opt.value);
            item.style.cssText = 'padding:16px 24px; font-size:15px; cursor:pointer; transition:background .15s;';
            item.onmouseenter = function() { this.style.background = '#f7f8fa'; };
            item.onmouseleave = function() { this.style.background = ''; };
            item.onclick = (function(s, b, l, v, t) {
                return function() {
                    s.value = v;
                    b.textContent = t;
                    l.style.display = 'none';
                };
            })(select, btn, list, opt.value, opt.text);
            list.appendChild(item);
        }

        btn.onclick = function(e) {
            e.stopPropagation();
            var isOpen = list.style.display === 'block';
            var allLists = document.querySelectorAll('.custom-dropdown-list');
            for (var j = 0; j < allLists.length; j++) allLists[j].style.display = 'none';
            if (!isOpen) list.style.display = 'block';
        };

        document.addEventListener('click', function() { list.style.display = 'none'; });

        select.style.display = 'none';
        wrapper.appendChild(btn);
        wrapper.appendChild(list);
        select.parentNode.insertBefore(wrapper, select.nextSibling);
    });
})();


// ==================== CARD CAROUSEL (galeria de imagens nos cards) ====================
(function() {
    function initCardCarousels() {
        var wrappers = document.querySelectorAll('.card-carousel-wrapper');
        if (!wrappers.length) return;

        wrappers.forEach(function(wrapper) {
            var images = wrapper.querySelectorAll('.card-carousel-img');
            var dots = wrapper.querySelectorAll('.card-carousel-dots span');
            var prevBtn = wrapper.querySelector('.card-carousel-prev');
            var nextBtn = wrapper.querySelector('.card-carousel-next');

            if (images.length <= 1) return;

            function getCurrentIndex() {
                for (var i = 0; i < images.length; i++) {
                    if (images[i].classList.contains('active')) return i;
                }
                return 0;
            }

            function goTo(index) {
                var current = getCurrentIndex();
                if (index === current) return;
                images[current].classList.remove('active');
                if (dots[current]) dots[current].classList.remove('active');
                images[index].classList.add('active');
                if (dots[index]) dots[index].classList.add('active');
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var current = getCurrentIndex();
                    var next = (current - 1 + images.length) % images.length;
                    goTo(next);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var current = getCurrentIndex();
                    var next = (current + 1) % images.length;
                    goTo(next);
                });
            }

            dots.forEach(function(dot, idx) {
                dot.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    goTo(idx);
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCardCarousels);
    } else {
        initCardCarousels();
    }
})();
