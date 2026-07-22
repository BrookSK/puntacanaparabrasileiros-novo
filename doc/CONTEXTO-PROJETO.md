# CONTEXTO COMPLETO DO PROJETO - Punta Cana para Brasileiros

## RESUMO DO PROJETO

Sistema de reservas de passeios e transfers em Punta Cana, recriado do zero em PHP puro MVC (sem framework). Originalmente era um WordPress com WP Travel Engine + plugins customizados. Foi recriado 100% em PHP com arquitetura própria.

**URL de referência do site original:** https://puntacanaparabrasileiros.com  
**Instagram:** https://www.instagram.com/puntacanaparabrasileiros  
**WhatsApp (com mensagem pré-preenchida):** https://api.whatsapp.com/send?phone=18294582170&text=Oi%2C%20tudo%20bem%3FPara%20ajudar%20voc%C3%AA%20da%20melhor%20forma%2C%20me%20diga%3A%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Seu%20nome%3A%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Quando%20voc%C3%AA%20vai%20chegar%3F%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Quantas%20pessoas%20s%C3%A3o%3F%C2%A0%C2%A0%C2%A0%E2%80%A2%C2%A0%C2%A0%C2%A0Em%20qual%20hotel%20voc%C3%AA%20vai%20ficar%3FEstou%20aqui%20para%20te%20ajudar%20com%20o%20que%20precisar!

---

## DADOS DA EMPRESA

- **Razão Social:** PUNTA CANA PARA BRASILEIROS OLIVEIRA & RAMOS SRL
- **RNC:** 1-33-28776-5
- **Endereço:** Avenida Barceló, nº 01, Local 7 – Plaza Arrecife, Verón - Punta Cana, República Dominicana – Código Postal 23000
- **Telefone:** +1 (829) 458-2170
- **Fundadores:** Anna & Danilo (casal brasileiro/dominicano)
- **Desenvolvido por:** LRV Web

---

## STACK TECNOLÓGICO

- **Backend:** PHP 8.1+ puro com MVC customizado (sem Laravel/Symfony)
- **Frontend:** HTML5, CSS3 (vanilla), JavaScript (vanilla + AJAX)
- **Banco de Dados:** MySQL 8.0+
- **Servidor:** Apache (.htaccess com mod_rewrite)
- **Sessões:** PHP nativas
- **Templates:** Views PHP com layouts
- **Sem .env:** Todas as configurações ficam no banco (tabela `settings`) e são editáveis via painel admin

---

## CORES GLOBAIS DO SITE

```css
--primary: #1C2011;         /* Texto escuro/dark base */
--secondary: #E4B505;       /* Dourado (botões, badges, estrelas) */
--text-green: #1B6F00;      /* Verde (preços, botões primários, links ativos) */
--accent: #3772C0;          /* Azul (links, botões accent, ícones) */
--dark: #1C2011;            /* Igual ao primary */
--gold: #E4B505;            /* Igual ao secondary */
--gold-dark: #c9a004;       /* Hover do dourado */
--accent-dark: #2a5a9a;     /* Hover do azul */
--gray: #636e72;            /* Textos secundários */
--light: #f8f9fa;           /* Fundos claros */
--white: #ffffff;
--font: 'Poppins', sans-serif;
```

---

## ESTRUTURA DE DIRETÓRIOS

```
/
├── index.php               ← Fallback (require public/index.php)
├── .htaccess               ← Rewrite rules (raiz)
├── app/
│   ├── Controllers/Admin/  (Dashboard, Trips, Transfers, Bookings, Vouchers, Affiliates, Users, Settings, Newsletter)
│   ├── Controllers/Frontend/ (Home, Trips, Transfer, Cart, Checkout, Account, Page, Blog)
│   ├── Controllers/Api/    (TransferSearch, Pricing, Cart, Webhook)
│   ├── Controllers/Auth/   (Login, Register)
│   ├── Models/             (User, Trip, TripPackage, TripCategory, Booking, Payment, TransferVehicle, TransferLocation, TransferBooking, Voucher, Affiliate, Commission, Setting, Wishlist, BlogPost, NewsletterSubscriber)
│   ├── Services/           (Pricing, Payment, PayPal, Stripe, Voucher, Email, WhatsApp, Affiliate, Cart, Instagram)
│   ├── Middleware/         (Auth, Admin, CSRF)
│   └── Helpers/            (functions.php, Currency, Validator)
├── config/
│   ├── routes.php          ← Todas as rotas
│   ├── database.php        ← Credenciais MySQL
│   └── app.php             ← Config geral
├── core/                   (App, Router, Database, Controller, Model, View, Session, Request, Response, Middleware)
├── database/
│   ├── schema.sql          ← IMUTÁVEL - criação de tabelas
│   ├── seeds.sql           ← IMUTÁVEL - dados iniciais
│   └── migrations/         ← Alterações incrementais (001_, 002_, ...)
├── public/
│   ├── index.php           ← Entry point
│   ├── .htaccess
│   ├── assets/css/         (app.css, admin.css, auth.css)
│   ├── assets/js/          (app.js, admin.js)
│   ├── assets/images/layout/  ← Imagens do layout (logo, fotos, etc)
│   ├── assets/videos/      (hero-bg.mp4)
│   └── uploads/vouchers/   (protegido por .htaccess)
├── resources/views/
│   ├── layouts/            (app.php, admin.php, auth.php)
│   ├── components/         (header.php, footer.php, account-sidebar.php, affiliate-nav.php, modals/booking-modal.php)
│   ├── frontend/           (home, trips/, transfers/, cart/, checkout/, account/, blog/, affiliate/, pages/)
│   ├── admin/              (dashboard, trips/, transfers/, bookings/, vouchers/, affiliates/, users/, settings/, newsletter/)
│   ├── auth/               (login, register, forgot-password, reset-password)
│   ├── emails/             (booking-confirmation, voucher-email)
│   ├── vouchers/           (trip-voucher, transfer-voucher)
│   └── errors/             (404, 500)
└── storage/                (logs/, cache/, sessions/)
```

---

## REGRAS IMPORTANTES

1. **SQL Imutável:** `schema.sql` e `seeds.sql` NUNCA são editados. Alterações vão em `database/migrations/XXX_descricao.sql`
2. **Sem .env:** Tudo no banco, editável via admin → Configurações
3. **Segurança:** PDO prepared statements, CSRF em forms, bcrypt para senhas, preços calculados no servidor
4. **Preço dinâmico:** Prioridade: Data específica > Feriado > Dia da semana > Mensal > Anual > Padrão
5. **Carrinho:** Multi-item (trips + transfers) em sessão PHP, expiração 7 dias
6. **Pagamentos:** PayPal Express (SDK JS) + Stripe (PaymentIntent)
7. **Vouchers:** HTML gerados automaticamente com QR code, enviados por email
8. **WhatsApp:** Webhook automático após confirmação de reserva
9. **Afiliados:** Cookie 30 dias, comissão 20%, painel próprio em /painel-afiliado

---

## PÁGINAS DO SITE (ROTAS FRONTEND)

| Rota | Controller | Descrição |
|------|-----------|-----------|
| `/` | HomeController@index | Home com todas as dobras |
| `/passeios` | TripsController@index | Listagem de passeios |
| `/passeios/{slug}` | TripsController@show | Detalhe do passeio (com booking modal) |
| `/transfers` | TransferController@index | Busca de transfer |
| `/blog` | BlogController@index | Blog com sidebar |
| `/blog/{slug}` | BlogController@show | Post singular |
| `/blog/categoria/{slug}` | BlogController@category | Posts por categoria |
| `/sobre-nos` | PageController@about | Sobre nós |
| `/contato` | PageController@contact | Contato com formulário |
| `/pesquisa` | PageController@search | Página de pesquisa |
| `/termos-e-condicoes` | PageController@terms | Termos |
| `/politicas-de-cancelamento` | PageController@cancellationPolicy | Cancelamento |
| `/politicas-de-privacidade` | PageController@privacyPolicy | Privacidade |
| `/termos-afiliados` | PageController@affiliateTerms | Termos afiliados |
| `/programa-de-afiliados` | PageController@affiliateProgram | Página do programa |
| `/cadastro-afiliado` | PageController@affiliateRegister | Cadastro de afiliado |
| `/login-afiliado` | PageController@affiliateLogin | Login afiliado |
| `/carrinho` | CartController@index | Carrinho |
| `/checkout` | CheckoutController@index | Checkout |
| `/login` | LoginController@showLogin | Login |
| `/registrar` | RegisterController@showRegister | Registro |
| `/minha-conta` | AccountController@dashboard | Área do cliente |
| `/minha-conta/reservas` | Bookings | Lista de reservas |
| `/minha-conta/transfers` | Transfers do cliente |
| `/minha-conta/wishlist` | Lista de desejos |
| `/minha-conta/cancelamentos` | Cancelamentos |
| `/minha-conta/cobranca` | Informações de cobrança |
| `/minha-conta/perfil` | Detalhes da conta |
| `/painel-afiliado` | Painel do afiliado (8 abas) |
| `/admin` | Admin Dashboard |

---

## LAYOUT / DESIGN — PADRÕES VISUAIS

### Header
- Logo (56px height) à esquerda
- Menu central: Home, Passeios, Transfer, Blog, Sobre Nós, Contato
- Link ativo: **verde** (#1B6F00) com sublinhado verde e font-weight 700
- Bandeiras: SVG inline (EUA, Brasil, Espanha) com borda, links ?lang=en/pt/es
- Ícones: Lupa (→ /pesquisa), Instagram, Carrinho (badge), WhatsApp, User, Coração (→ wishlist)
- Botão "Agendar Agora": fundo dourado (#E4B505), border-radius 6px (pouco arredondado)

### Footer
- Wave SVG ondulado no topo (cyan)
- Fundo gradiente verde/amarelo neon com imagem (zipwp-image-5876.png)
- Logo + descrição + ícones sociais (Instagram, Threads, WhatsApp)
- 3 colunas de links
- Rodapé cinza: copyright + "Desenvolvido por LRV Web" + RNC + badge "Pagamento seguro"

### Elementos Recorrentes
- **Wave divider animado:** ondinha azul SVG com animação de escrita (stroke-dashoffset)
- **Section label:** texto em itálico azul (Georgia serif) acima dos títulos
- **Hero de páginas:** gradiente cinza claro (#f0f4f0 → #e8eef0)
- **Botão primário:** verde (#1B6F00)
- **Botão secundário:** dourado (#E4B505) com texto escuro
- **Botão outline:** borda escura, texto escuro
- **Cards de passeio:** imagem + título + descrição + localização + duração + preço
- **FAQ accordion:** borda cinza, chevron à direita, max-height animado

### Home (Dobras na ordem)
1. Hero com vídeo de fundo (hero-bg.mp4) + overlay verde + título + badges
2. "Nossa História" — grid 2col: texto esquerda + 2 fotos direita (escalonadas)
3. "Explore os favoritos de Punta Cana" — passeios em destaque (grid 3col, fundo #f7f7f7)
4. Depoimentos — slider 3 cards com auto-slide e dots
5. Transfers em destaque — 3 veículos do banco
6. Stats com contadores animados — fundo imagem parallax + overlay
7. Blog — 3 últimos posts (fundo #faf9f7)
8. Instagram Feed — 5 posts via API (fundo #f7f8fa)
9. FAQ — accordion com perguntas sobre Punta Cana
10. CTA Filtros — grid 2col: foto mulher + formulário de pesquisa

### Container
- max-width: 1400px
- padding: 0 30px

---

## FUNCIONALIDADES PRINCIPAIS

### Sistema de Passeios
- CRUD completo no admin
- Pacotes com categorias de viajante (Adulto, Criança, Bebê)
- Preço dinâmico por dia (6 níveis de prioridade)
- Datas fixas de saída
- Itinerário multi-dia
- Serviços extras
- Desconto de grupo
- Reviews/avaliações
- FAQs específicas por passeio (tabela trip_faqs)
- Booking Modal popup (calendário → horário → pacote → viajantes → carrinho/checkout)

### Sistema de Transfers
- Busca: Ida e Volta / Somente Ida / Múltiplos
- Formulário verde escuro com border-radius 16px
- Cálculo de preço por faixa de passageiros
- Espelhamento de rota inversa
- Resultados: "PACOTE DE TRANSFERS" + cards de veículo + total + botões

### Pagamentos
- PayPal Express (REST API, sandbox/produção)
- Stripe (PaymentIntent, Elements)
- Pagamento parcial (depósito %)
- Status: pending, booked, partially_paid, completed, cancelled, refunded

### Afiliados
- Cadastro com formulário completo (dados pessoais + pagamento + perfil de conteúdo)
- Painel com 8 abas: Dashboard, Links, Comissões, Visitas, Criativos, Pagamentos, Configurações, Landing Page
- Cookie 30 dias, comissão 20%, last-click attribution
- Pagamento via PIX ou PayPal

### Newsletter
- Inscrição via AJAX no blog sidebar e post singular
- Admin: listagem de inscritos, exportar CSV, criar/enviar campanhas

### Blog
- Posts com categorias coloridas
- Post singular: header + imagem + conteúdo + share + related + newsletter
- Página de categoria com breadcrumb
- Sidebar: busca, categorias, tags populares, newsletter

---

## IMAGENS DO LAYOUT

Pasta: `public/assets/images/layout/`
- `PUNTA-CANA-1.png` — Logo principal
- `praia.jpeg` — Praia (seção Nossa História)
- `praia-com-arvore.jpeg` — Palmeira com balanço
- `praia-pessoas.jpeg` — Fundo da seção de stats/números
- `mulher.jpg` — Mulher na praia (CTA filtros)
- `casal.jpg` — Anna & Danilo (página Sobre Nós)
- `zipwp-image-5876.png` — Background do footer
- `print1.jpg`, `print2.jpg`, `print3.jpg` — Screenshots na página de cancelamento

Vídeo: `public/assets/videos/hero-bg.mp4` (baixado do site original)

---

## O QUE FALTA AJUSTAR / PENDÊNCIAS CONHECIDAS

1. **Multilíngue completo** — estrutura existe (flags + ?lang=), falta implementar tradução de strings
2. **Booking Modal** — JS funcional, mas precisa testar com dados reais do banco
3. **Instagram Feed** — precisa configurar access token no admin para funcionar com dados reais
4. **Admin visual** — funcional mas pode ser refinado visualmente
5. **Página individual do passeio** — pode precisar de ajustes finos conforme novos prints
6. **Testes** — não foram criados testes automatizados

---

## COMO CONTINUAR O TRABALHO

1. Leia este documento para ter todo o contexto
2. O CSS principal está em `public/assets/css/app.css` (arquivo grande, ~3500+ linhas)
3. O JS principal está em `public/assets/js/app.js`
4. As rotas estão em `config/routes.php`
5. Para ajustes de layout, trabalhe nas views em `resources/views/`
6. Para lógica de negócio, trabalhe nos controllers e services

Quando o usuário mandar prints de referência para comparar com o estado atual, ajuste o CSS/HTML para ficar pixel-perfect ao print de referência.
