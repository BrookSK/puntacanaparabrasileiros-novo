/**
 * PUNTA CANA PARA BRASILEIROS - Frontend JavaScript
 */
(function() {
    'use strict';

    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '';

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
            let total = 0;
            paxInputs.forEach(input => {
                const price = parseFloat(input.dataset.price) || 0;
                const qty = parseInt(input.value) || 0;
                total += price * qty;
            });
            const totalEl = document.getElementById('widgetTotal');
            if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
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
                const depFields = document.querySelectorAll('.departure-field');
                if (tab === 'oneway') {
                    depFields.forEach(f => f.classList.add('hidden'));
                } else {
                    depFields.forEach(f => f.classList.remove('hidden'));
                }
            });
        });
    }

    window.changePaxTransfer = function(field, delta) {
        const input = document.getElementById('transfer' + field.charAt(0).toUpperCase() + field.slice(1));
        if (input) {
            let v = parseInt(input.value) + delta;
            if (v < parseInt(input.min)) v = parseInt(input.min);
            input.value = v;
            // Update total display
            const adults = parseInt(document.getElementById('transferAdults')?.value || 1);
            const children = parseInt(document.getElementById('transferChildren')?.value || 0);
            const infants = parseInt(document.getElementById('transferInfants')?.value || 0);
            const totalEl = document.getElementById('paxTotal');
            if (totalEl) totalEl.textContent = adults + children + infants;
        }
    };

    function searchTransfers() {
        const origin = document.getElementById('originSelect')?.value;
        const destination = document.getElementById('destinationSelect')?.value;
        const adults = document.getElementById('transferAdults')?.value || '1';
        const children = document.getElementById('transferChildren')?.value || '0';
        const infants = document.getElementById('transferInfants')?.value || '0';
        const serviceType = document.getElementById('serviceType')?.value || 'private';

        if (!origin || !destination) { alert('Selecione origem e destino.'); return; }

        document.getElementById('transferLoading').style.display = 'block';
        document.getElementById('transferResults').style.display = 'none';
        document.getElementById('transferEmptyState').style.display = 'none';

        ajax('/api/transfers/buscar', { body: JSON.stringify({ origin_id: origin, destination_id: destination, adults, children, infants, service_type: serviceType }) })
            .then(data => {
                document.getElementById('transferLoading').style.display = 'none';
                if (data.success && data.results && data.results.length > 0) {
                    renderTransferResults(data);
                } else if (data.success && data.results && data.results.length === 0) {
                    document.getElementById('transferEmptyState').style.display = 'block';
                } else {
                    alert(data.error || 'Erro na busca.');
                }
            }).catch(() => { document.getElementById('transferLoading').style.display = 'none'; alert('Erro de conexão.'); });
    }

    function renderTransferResults(data) {
        const container = document.getElementById('resultsList');
        const resultsDiv = document.getElementById('transferResults');
        const totalBar = document.getElementById('transferTotalBar');
        container.innerHTML = '';

        let totalPrice = 0;
        const arrivalDate = document.querySelector('[name="arrival_date"]')?.value || '';
        const arrivalTime = document.querySelector('[name="arrival_time"]')?.value || '';
        const departureDate = document.querySelector('[name="departure_date"]')?.value || '';
        const departureTime = document.querySelector('[name="departure_time"]')?.value || '';

        // Render arrival
        data.results.forEach(v => {
            totalPrice += v.price;
            container.innerHTML += `
            <div class="transfer-result-item">
                <div class="transfer-result-route">
                    <strong>Entrada:</strong> ${data.origin} → ${data.destination} (${formatDateBR(arrivalDate)})
                </div>
                <div class="transfer-vehicle-card">
                    <div class="transfer-vehicle-img"><img src="${v.image || '/assets/images/placeholder.jpg'}" alt="${v.title}"></div>
                    <div class="transfer-vehicle-info">
                        <h4>${v.title}</h4>
                        <p>${v.description || ''}</p>
                        <div class="transfer-vehicle-meta">
                            <span>🌐 ${v.max_passengers} passageiros</span>
                            <span>🧳 ${v.max_luggage || 0} malas</span>
                            <span>⏱ ${v.duration || 0} min</span>
                        </div>
                    </div>
                    <div>
                        <span class="transfer-vehicle-price">$${v.price.toFixed(2)}</span>
                        <span class="transfer-vehicle-currency">USD</span>
                    </div>
                </div>
            </div>`;
        });

        // If roundtrip, also show return
        const activeTab = document.querySelector('.transfer-tab.active')?.dataset.tab;
        if (activeTab === 'roundtrip' && departureDate) {
            data.results.forEach(v => {
                totalPrice += v.price;
                container.innerHTML += `
                <div class="transfer-result-item">
                    <div class="transfer-result-route">
                        <strong>Saída:</strong> ${data.destination} → ${data.origin} (${formatDateBR(departureDate)})
                    </div>
                    <div class="transfer-vehicle-card">
                        <div class="transfer-vehicle-img"><img src="${v.image || '/assets/images/placeholder.jpg'}" alt="${v.title}"></div>
                        <div class="transfer-vehicle-info">
                            <h4>${v.title}</h4>
                            <p>${v.description || ''}</p>
                            <div class="transfer-vehicle-meta">
                                <span>🌐 ${v.max_passengers} passageiros</span>
                                <span>🧳 ${v.max_luggage || 0} malas</span>
                                <span>⏱ ${v.duration || 0} min</span>
                            </div>
                        </div>
                        <div>
                            <span class="transfer-vehicle-price">$${v.price.toFixed(2)}</span>
                            <span class="transfer-vehicle-currency">USD</span>
                        </div>
                    </div>
                </div>`;
            });
        }

        // Show total
        document.getElementById('transferTotalValue').textContent = '$' + totalPrice.toFixed(2) + ' USD';
        totalBar.style.display = 'block';
        resultsDiv.style.display = 'block';

        // Store data for cart
        window._transferSearchResults = data;
        window._transferTotalPrice = totalPrice;
    }

    function formatDateBR(dateStr) {
        if (!dateStr) return '';
        const [y, m, d] = dateStr.split('-');
        return `${d}/${m}/${y}`;
    }

    // Add to cart button
    document.getElementById('btnAddCart')?.addEventListener('click', () => {
        if (!window._transferSearchResults) return;
        const data = window._transferSearchResults;
        const v = data.results[0];
        if (!v) return;

        const payload = {
            vehicle_id: v.id,
            origin_id: document.getElementById('originSelect').value,
            destination_id: document.getElementById('destinationSelect').value,
            date: document.querySelector('[name="arrival_date"]').value,
            time: document.querySelector('[name="arrival_time"]').value,
            type: 'arrival',
            service_type: document.getElementById('serviceType').value,
            adults: document.getElementById('transferAdults').value,
            children: document.getElementById('transferChildren').value,
            infants: document.getElementById('transferInfants').value,
        };

        ajax('/api/cart/add-transfer', { body: JSON.stringify(payload) })
            .then(d => { if (d.success) { alert('Transfer adicionado ao carrinho!'); updateCartBadge(); } else { alert(d.error || 'Erro.'); } })
            .catch(() => alert('Erro de conexão.'));
    });

    document.getElementById('btnDirectCheckout')?.addEventListener('click', () => {
        document.getElementById('btnAddCart')?.click();
        setTimeout(() => { window.location = '/checkout'; }, 500);
    });

    // ==================== CHECKOUT ====================
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!document.getElementById('termsCheck').checked) { alert('Aceite os termos e condições.'); return; }

            const formData = new FormData(checkoutForm);
            const data = Object.fromEntries(formData.entries());
            data.payment_mode = document.getElementById('partialCheck')?.checked ? 'partial' : 'full';

            document.getElementById('checkoutLoading').style.display = 'flex';

            ajax('/checkout/processar', { body: JSON.stringify(data) })
                .then(response => {
                    if (response.success) {
                        if (response.gateway === 'paypal' && response.paypal_order_id) {
                            // PayPal flow handled by SDK
                            capturePayPal(response);
                        } else if (response.gateway === 'stripe' && response.stripe_client_secret) {
                            handleStripePayment(response);
                        } else {
                            window.location = '/checkout/sucesso/' + response.booking_number;
                        }
                    } else {
                        document.getElementById('checkoutLoading').style.display = 'none';
                        alert(response.error || 'Erro ao processar.');
                    }
                }).catch(() => { document.getElementById('checkoutLoading').style.display = 'none'; alert('Erro de conexão.'); });
        });
    }

    function capturePayPal(response) {
        // Confirm with webhook
        ajax('/api/webhook/payment', { body: JSON.stringify({ gateway: 'paypal', payment_id: response.payment_id, transaction_id: response.paypal_order_id }) })
            .then(data => { window.location = '/checkout/sucesso/' + response.booking_number; })
            .catch(() => { window.location = '/checkout/sucesso/' + response.booking_number; });
    }

    function handleStripePayment(response) {
        if (typeof Stripe === 'undefined') { alert('Stripe não carregado.'); return; }
        const stripe = Stripe(typeof CHECKOUT_CONFIG !== 'undefined' ? CHECKOUT_CONFIG.stripePublishableKey : '');
        stripe.confirmCardPayment(response.stripe_client_secret).then(result => {
            if (result.error) { document.getElementById('checkoutLoading').style.display = 'none'; alert(result.error.message); }
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
    let selectedDate = null;
    let selectedTime = null;
    let selectedPackage = null;
    let travelerCounts = {};
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();

    // Abrir modal
    document.querySelector('.btn-verificar')?.addEventListener('click', function(e) {
        e.preventDefault();
        modal.style.display = 'flex';
        document.getElementById('bmSidebarTitle').textContent = tripTitle;
        renderCalendar();
    });

    // Fechar modal
    document.getElementById('bookingModalClose')?.addEventListener('click', () => { modal.style.display = 'none'; });
    modal.addEventListener('click', (e) => { if (e.target === modal) modal.style.display = 'none'; });

    // Navegação do calendário
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
            const isSelected = selectedDate && date.toDateString() === new Date(selectedDate).toDateString();
            let classes = 'bm-day';
            if (isPast) classes += ' disabled';
            else classes += ' available';
            if (isToday) classes += ' today';
            if (isSelected) classes += ' selected';
            const dateStr = `${currentYear}-${String(currentMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
            html += `<div class="${classes}" data-date="${dateStr}" onclick="selectBookingDate('${dateStr}')">${d}</div>`;
        }
        daysContainer.innerHTML = html;
    }

    window.selectBookingDate = function(dateStr) {
        selectedDate = dateStr;
        renderCalendar();
        // Show time slots (mock or from fixed dates)
        showTimeSlots(dateStr);
        updateSidebar();
        document.getElementById('bmContinue').disabled = false;
    };

    function showTimeSlots(dateStr) {
        const container = document.getElementById('bmTimes');
        const list = document.getElementById('bmTimesList');
        // Default time slots (can be dynamic from fixed_dates)
        const slots = ['9:00 AM – 12:00 PM', '12:00 PM – 3:00 PM', '3:00 PM – 6:00 PM'];
        list.innerHTML = slots.map(s => `<button type="button" class="bm-time-slot ${selectedTime === s ? 'selected' : ''}" onclick="selectBookingTime('${s}')">${s}</button>`).join('');
        container.style.display = 'block';
    }

    window.selectBookingTime = function(time) {
        selectedTime = time;
        document.querySelectorAll('.bm-time-slot').forEach(el => el.classList.remove('selected'));
        event.target.classList.add('selected');
        updateSidebar();
    };

    // Continuar para Step 2
    document.getElementById('bmContinue')?.addEventListener('click', () => {
        document.getElementById('bmStep1').classList.remove('active');
        document.getElementById('bmStep2').classList.add('active');
        document.getElementById('bmTab1').classList.remove('active');
        document.getElementById('bmTab2').classList.add('active');
        renderPackages();
    });

    // Voltar para Step 1
    document.getElementById('bmBack')?.addEventListener('click', () => {
        document.getElementById('bmStep2').classList.remove('active');
        document.getElementById('bmStep1').classList.add('active');
        document.getElementById('bmTab2').classList.remove('active');
        document.getElementById('bmTab1').classList.add('active');
    });

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
        if (!selectedPackage || !selectedPackage.categories) return;
        travelerCounts = {};
        container.innerHTML = selectedPackage.categories.map(cat => {
            const defaultQty = cat.category_slug === 'adulto' ? 1 : 0;
            travelerCounts[cat.traveler_category_id] = defaultQty;
            const price = cat.sale_price || cat.price;
            return `<div class="bm-traveler-row">
                <div class="bm-traveler-info">
                    <span class="bm-traveler-name">${cat.category_name}: (${cat.age_group || ''})</span>
                    <span class="bm-traveler-price">$${parseFloat(price).toFixed(2)} / Pessoa</span>
                </div>
                <div class="bm-traveler-counter">
                    <button type="button" onclick="changeTraveler(${cat.traveler_category_id}, -1)">−</button>
                    <input type="text" value="${defaultQty}" id="traveler_${cat.traveler_category_id}" readonly>
                    <button type="button" onclick="changeTraveler(${cat.traveler_category_id}, 1)">+</button>
                </div>
            </div>`;
        }).join('');
        updateSidebar();
    }

    window.changeTraveler = function(catId, delta) {
        let val = (travelerCounts[catId] || 0) + delta;
        if (val < 0) val = 0;
        travelerCounts[catId] = val;
        document.getElementById('traveler_' + catId).value = val;
        updateSidebar();
    };

    function updateSidebar() {
        // Date
        const dateEl = document.getElementById('bmSidebarDate');
        if (selectedDate) {
            const [y,m,d] = selectedDate.split('-');
            dateEl.textContent = `Data De Início: ${d}/${m}/${y}${selectedTime ? ' at ' + selectedTime : ''}`;
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
        if (selectedPackage && selectedPackage.categories) {
            selectedPackage.categories.forEach(cat => {
                const qty = travelerCounts[cat.traveler_category_id] || 0;
                if (qty > 0) {
                    const price = parseFloat(cat.sale_price || cat.price);
                    const line = price * qty;
                    total += line;
                    travHtml += `<div class="bm-sidebar-traveler-line"><span>${cat.category_name}: ${qty} x $${price.toFixed(2)}</span><span>$${line.toFixed(2)}</span></div>`;
                }
            });
        }
        if (travHtml) {
            travDiv.style.display = 'block';
            travList.innerHTML = travHtml;
        }
        totalEl.textContent = '$' + total.toFixed(0);
    }

    // Add to Cart
    document.getElementById('bmAddCart')?.addEventListener('click', () => {
        submitBooking('cart');
    });
    document.getElementById('bmCheckout')?.addEventListener('click', () => {
        submitBooking('checkout');
    });

    function submitBooking(redirect) {
        if (!selectedDate || !selectedPackage) { alert('Selecione data e pacote.'); return; }
        const pax = {};
        Object.keys(travelerCounts).forEach(k => { if (travelerCounts[k] > 0) pax[k] = travelerCounts[k]; });
        if (Object.keys(pax).length === 0) { alert('Selecione pelo menos 1 viajante.'); return; }

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/carrinho/adicionar';
        form.innerHTML = `<input name="_token" value="${CSRF}">
            <input name="trip_id" value="${typeof TRIP_ID !== 'undefined' ? TRIP_ID : ''}">
            <input name="package_id" value="${selectedPackage.id}">
            <input name="date" value="${selectedDate}">
            <input name="time" value="${selectedTime || ''}">
            <input name="redirect" value="${redirect}">`;
        Object.entries(pax).forEach(([k, v]) => { form.innerHTML += `<input name="pax[${k}]" value="${v}">`; });
        document.body.appendChild(form);
        form.submit();
    }
})();
