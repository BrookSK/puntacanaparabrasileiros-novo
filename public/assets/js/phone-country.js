/**
 * PHONE COUNTRY SELECTOR - Seletor de DDI com bandeira
 * Transforma automaticamente campos input[type=tel] em campos com seletor de país
 */
(function() {
    'use strict';

    // Lista de países com bandeira (emoji), DDI e código ISO
    const countries = [
        { code: 'BR', name: 'Brasil', ddi: '+55', flag: '🇧🇷' },
        { code: 'US', name: 'Estados Unidos', ddi: '+1', flag: '🇺🇸' },
        { code: 'DO', name: 'República Dominicana', ddi: '+1', flag: '🇩🇴' },
        { code: 'PT', name: 'Portugal', ddi: '+351', flag: '🇵🇹' },
        { code: 'AR', name: 'Argentina', ddi: '+54', flag: '🇦🇷' },
        { code: 'CO', name: 'Colômbia', ddi: '+57', flag: '🇨🇴' },
        { code: 'CL', name: 'Chile', ddi: '+56', flag: '🇨🇱' },
        { code: 'MX', name: 'México', ddi: '+52', flag: '🇲🇽' },
        { code: 'PE', name: 'Peru', ddi: '+51', flag: '🇵🇪' },
        { code: 'UY', name: 'Uruguai', ddi: '+598', flag: '🇺🇾' },
        { code: 'PY', name: 'Paraguai', ddi: '+595', flag: '🇵🇾' },
        { code: 'EC', name: 'Equador', ddi: '+593', flag: '🇪🇨' },
        { code: 'BO', name: 'Bolívia', ddi: '+591', flag: '🇧🇴' },
        { code: 'VE', name: 'Venezuela', ddi: '+58', flag: '🇻🇪' },
        { code: 'PA', name: 'Panamá', ddi: '+507', flag: '🇵🇦' },
        { code: 'CR', name: 'Costa Rica', ddi: '+506', flag: '🇨🇷' },
        { code: 'GT', name: 'Guatemala', ddi: '+502', flag: '🇬🇹' },
        { code: 'CU', name: 'Cuba', ddi: '+53', flag: '🇨🇺' },
        { code: 'ES', name: 'Espanha', ddi: '+34', flag: '🇪🇸' },
        { code: 'FR', name: 'França', ddi: '+33', flag: '🇫🇷' },
        { code: 'DE', name: 'Alemanha', ddi: '+49', flag: '🇩🇪' },
        { code: 'IT', name: 'Itália', ddi: '+39', flag: '🇮🇹' },
        { code: 'GB', name: 'Reino Unido', ddi: '+44', flag: '🇬🇧' },
        { code: 'CA', name: 'Canadá', ddi: '+1', flag: '🇨🇦' },
        { code: 'JP', name: 'Japão', ddi: '+81', flag: '🇯🇵' },
        { code: 'AU', name: 'Austrália', ddi: '+61', flag: '🇦🇺' },
        { code: 'IL', name: 'Israel', ddi: '+972', flag: '🇮🇱' },
        { code: 'AO', name: 'Angola', ddi: '+244', flag: '🇦🇴' },
        { code: 'MZ', name: 'Moçambique', ddi: '+258', flag: '🇲🇿' },
    ];

    // País padrão
    const defaultCountry = countries[0]; // Brasil

    function initPhoneCountrySelector() {
        const phoneInputs = document.querySelectorAll('input[type="tel"][data-phone-country]');
        phoneInputs.forEach(function(input) {
            if (input.dataset.phoneInitialized) return;
            input.dataset.phoneInitialized = 'true';
            buildSelector(input);
        });
    }

    function buildSelector(input) {
        // Detectar valor existente e extrair DDI se houver
        let currentValue = input.value || '';
        let selectedCountry = defaultCountry;
        let phoneNumber = currentValue;

        // Se o valor começa com +, tentar detectar o país
        if (currentValue.startsWith('+')) {
            for (let i = 0; i < countries.length; i++) {
                if (currentValue.startsWith(countries[i].ddi + ' ') || currentValue.startsWith(countries[i].ddi)) {
                    selectedCountry = countries[i];
                    phoneNumber = currentValue.substring(countries[i].ddi.length).trim();
                    break;
                }
            }
        }

        // Criar wrapper
        const wrapper = document.createElement('div');
        wrapper.className = 'phone-country-wrapper';

        // Criar botão do seletor de país
        const selectorBtn = document.createElement('button');
        selectorBtn.type = 'button';
        selectorBtn.className = 'phone-country-btn';
        selectorBtn.innerHTML = '<span class="phone-country-flag">' + selectedCountry.flag + '</span><span class="phone-country-ddi">' + selectedCountry.ddi + '</span><span class="phone-country-arrow">▾</span>';

        // Criar dropdown
        const dropdown = document.createElement('div');
        dropdown.className = 'phone-country-dropdown';
        dropdown.style.display = 'none';

        // Criar campo de busca no dropdown
        const searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'phone-country-search';
        searchInput.placeholder = 'Buscar país...';
        dropdown.appendChild(searchInput);

        // Criar lista de países
        const list = document.createElement('div');
        list.className = 'phone-country-list';

        countries.forEach(function(country) {
            const item = document.createElement('div');
            item.className = 'phone-country-item';
            if (country.code === selectedCountry.code) item.classList.add('selected');
            item.dataset.code = country.code;
            item.innerHTML = '<span class="phone-country-item-flag">' + country.flag + '</span><span class="phone-country-item-name">' + country.name + '</span><span class="phone-country-item-ddi">' + country.ddi + '</span>';
            item.addEventListener('click', function() {
                selectCountry(country, selectorBtn, input, wrapper, dropdown);
            });
            list.appendChild(item);
        });

        dropdown.appendChild(list);

        // Busca no dropdown
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            list.querySelectorAll('.phone-country-item').forEach(function(item) {
                const name = item.querySelector('.phone-country-item-name').textContent.toLowerCase();
                const ddi = item.querySelector('.phone-country-item-ddi').textContent;
                item.style.display = (name.includes(query) || ddi.includes(query)) ? '' : 'none';
            });
        });

        // Impedir que o input de busca feche o dropdown
        searchInput.addEventListener('click', function(e) { e.stopPropagation(); });

        // Inserir wrapper no DOM
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(selectorBtn);
        wrapper.appendChild(dropdown);
        wrapper.appendChild(input);

        // Ajustar o input
        input.classList.add('phone-country-input');
        input.placeholder = 'DDD + Número';
        input.value = phoneNumber;

        // Criar campo hidden para enviar o valor completo com DDI
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = input.name;
        hiddenInput.value = selectedCountry.ddi + ' ' + phoneNumber;
        wrapper.appendChild(hiddenInput);

        // Remover o name do input visível para não duplicar no envio
        input.removeAttribute('name');
        input.dataset.phoneVisible = 'true';

        // Armazenar referências
        wrapper._hiddenInput = hiddenInput;
        wrapper._selectedCountry = selectedCountry;

        // Atualizar hidden quando o número muda
        input.addEventListener('input', function() {
            hiddenInput.value = wrapper._selectedCountry.ddi + ' ' + this.value.trim();
        });

        // Toggle dropdown
        selectorBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const isOpen = dropdown.style.display !== 'none';
            closeAllDropdowns();
            if (!isOpen) {
                dropdown.style.display = 'block';
                searchInput.value = '';
                searchInput.focus();
                list.querySelectorAll('.phone-country-item').forEach(function(item) { item.style.display = ''; });
            }
        });

        // Fechar dropdown ao clicar fora
        document.addEventListener('click', function(e) {
            if (!wrapper.contains(e.target)) {
                dropdown.style.display = 'none';
            }
        });
    }

    function selectCountry(country, selectorBtn, input, wrapper, dropdown) {
        selectorBtn.innerHTML = '<span class="phone-country-flag">' + country.flag + '</span><span class="phone-country-ddi">' + country.ddi + '</span><span class="phone-country-arrow">▾</span>';
        wrapper._selectedCountry = country;
        wrapper._hiddenInput.value = country.ddi + ' ' + input.value.trim();
        dropdown.style.display = 'none';

        // Atualizar classe selected
        dropdown.querySelectorAll('.phone-country-item').forEach(function(item) {
            item.classList.toggle('selected', item.dataset.code === country.code);
        });
    }

    function closeAllDropdowns() {
        document.querySelectorAll('.phone-country-dropdown').forEach(function(d) {
            d.style.display = 'none';
        });
    }

    // Inicializar quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPhoneCountrySelector);
    } else {
        initPhoneCountrySelector();
    }

    // Expor para uso dinâmico (modais, ajax)
    window.initPhoneCountrySelector = initPhoneCountrySelector;
})();
