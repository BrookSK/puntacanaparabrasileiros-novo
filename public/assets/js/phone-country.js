/**
 * PHONE COUNTRY SELECTOR - Seletor de DDI com bandeira (imagem)
 * Transforma automaticamente campos input[type=tel] em campos com seletor de país
 * Usa imagens de bandeira do flagcdn.com
 */
(function() {
    'use strict';

    // URL base para bandeiras em imagem (flagcdn.com)
    var FLAG_CDN = 'https://flagcdn.com/24x18/';

    // Lista de países com código ISO (minúsculo para URL da bandeira), DDI e nome
    var countries = [
        { code: 'br', name: 'Brasil', ddi: '+55' },
        { code: 'us', name: 'Estados Unidos', ddi: '+1' },
        { code: 'do', name: 'República Dominicana', ddi: '+1' },
        { code: 'pt', name: 'Portugal', ddi: '+351' },
        { code: 'ar', name: 'Argentina', ddi: '+54' },
        { code: 'co', name: 'Colômbia', ddi: '+57' },
        { code: 'cl', name: 'Chile', ddi: '+56' },
        { code: 'mx', name: 'México', ddi: '+52' },
        { code: 'pe', name: 'Peru', ddi: '+51' },
        { code: 'uy', name: 'Uruguai', ddi: '+598' },
        { code: 'py', name: 'Paraguai', ddi: '+595' },
        { code: 'ec', name: 'Equador', ddi: '+593' },
        { code: 'bo', name: 'Bolívia', ddi: '+591' },
        { code: 've', name: 'Venezuela', ddi: '+58' },
        { code: 'pa', name: 'Panamá', ddi: '+507' },
        { code: 'cr', name: 'Costa Rica', ddi: '+506' },
        { code: 'gt', name: 'Guatemala', ddi: '+502' },
        { code: 'cu', name: 'Cuba', ddi: '+53' },
        { code: 'es', name: 'Espanha', ddi: '+34' },
        { code: 'fr', name: 'França', ddi: '+33' },
        { code: 'de', name: 'Alemanha', ddi: '+49' },
        { code: 'it', name: 'Itália', ddi: '+39' },
        { code: 'gb', name: 'Reino Unido', ddi: '+44' },
        { code: 'ca', name: 'Canadá', ddi: '+1' },
        { code: 'jp', name: 'Japão', ddi: '+81' },
        { code: 'au', name: 'Austrália', ddi: '+61' },
        { code: 'il', name: 'Israel', ddi: '+972' },
        { code: 'ao', name: 'Angola', ddi: '+244' },
        { code: 'mz', name: 'Moçambique', ddi: '+258' },
    ];

    // Gerar HTML da bandeira como <img>
    function flagImg(code, size) {
        size = size || '24x18';
        return '<img src="' + FLAG_CDN.replace('24x18', size) + code + '.png" alt="' + code + '" class="phone-flag-img">';
    }

    // País padrão
    var defaultCountry = countries[0]; // Brasil

    function initPhoneCountrySelector() {
        var phoneInputs = document.querySelectorAll('input[type="tel"][data-phone-country]');
        for (var i = 0; i < phoneInputs.length; i++) {
            var input = phoneInputs[i];
            if (input.dataset.phoneInitialized) continue;
            input.dataset.phoneInitialized = 'true';
            buildSelector(input);
        }
    }

    function buildSelector(input) {
        // Detectar valor existente e extrair DDI se houver
        var currentValue = input.value || '';
        var selectedCountry = defaultCountry;
        var phoneNumber = currentValue;

        // Se o valor começa com +, tentar detectar o país
        if (currentValue.startsWith('+')) {
            for (var i = 0; i < countries.length; i++) {
                if (currentValue.startsWith(countries[i].ddi + ' ') || currentValue.startsWith(countries[i].ddi)) {
                    selectedCountry = countries[i];
                    phoneNumber = currentValue.substring(countries[i].ddi.length).trim();
                    break;
                }
            }
        }

        // Criar wrapper
        var wrapper = document.createElement('div');
        wrapper.className = 'phone-country-wrapper';

        // Criar botão do seletor de país
        var selectorBtn = document.createElement('button');
        selectorBtn.type = 'button';
        selectorBtn.className = 'phone-country-btn';
        selectorBtn.innerHTML = '<span class="phone-country-flag">' + flagImg(selectedCountry.code) + '</span><span class="phone-country-ddi">' + selectedCountry.ddi + '</span><span class="phone-country-arrow">▾</span>';

        // Criar dropdown
        var dropdown = document.createElement('div');
        dropdown.className = 'phone-country-dropdown';
        dropdown.style.display = 'none';

        // Criar campo de busca no dropdown
        var searchInput = document.createElement('input');
        searchInput.type = 'text';
        searchInput.className = 'phone-country-search';
        searchInput.placeholder = 'Buscar país...';
        dropdown.appendChild(searchInput);

        // Criar lista de países
        var list = document.createElement('div');
        list.className = 'phone-country-list';

        countries.forEach(function(country) {
            var item = document.createElement('div');
            item.className = 'phone-country-item';
            if (country.code === selectedCountry.code) item.classList.add('selected');
            item.dataset.code = country.code;
            item.innerHTML = '<span class="phone-country-item-flag">' + flagImg(country.code) + '</span><span class="phone-country-item-name">' + country.name + '</span><span class="phone-country-item-ddi">' + country.ddi + '</span>';
            item.addEventListener('click', function() {
                selectCountry(country, selectorBtn, input, wrapper, dropdown);
            });
            list.appendChild(item);
        });

        dropdown.appendChild(list);

        // Busca no dropdown
        searchInput.addEventListener('input', function() {
            var query = this.value.toLowerCase();
            var items = list.querySelectorAll('.phone-country-item');
            for (var j = 0; j < items.length; j++) {
                var name = items[j].querySelector('.phone-country-item-name').textContent.toLowerCase();
                var ddi = items[j].querySelector('.phone-country-item-ddi').textContent;
                items[j].style.display = (name.indexOf(query) !== -1 || ddi.indexOf(query) !== -1) ? '' : 'none';
            }
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
        var hiddenInput = document.createElement('input');
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
            var isOpen = dropdown.style.display !== 'none';
            closeAllDropdowns();
            if (!isOpen) {
                dropdown.style.display = 'block';
                searchInput.value = '';
                searchInput.focus();
                var items = list.querySelectorAll('.phone-country-item');
                for (var k = 0; k < items.length; k++) { items[k].style.display = ''; }
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
        selectorBtn.innerHTML = '<span class="phone-country-flag">' + flagImg(country.code) + '</span><span class="phone-country-ddi">' + country.ddi + '</span><span class="phone-country-arrow">▾</span>';
        wrapper._selectedCountry = country;
        wrapper._hiddenInput.value = country.ddi + ' ' + input.value.trim();
        dropdown.style.display = 'none';

        // Atualizar classe selected
        var items = dropdown.querySelectorAll('.phone-country-item');
        for (var i = 0; i < items.length; i++) {
            if (items[i].dataset.code === country.code) {
                items[i].classList.add('selected');
            } else {
                items[i].classList.remove('selected');
            }
        }
    }

    function closeAllDropdowns() {
        var dropdowns = document.querySelectorAll('.phone-country-dropdown');
        for (var i = 0; i < dropdowns.length; i++) {
            dropdowns[i].style.display = 'none';
        }
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
