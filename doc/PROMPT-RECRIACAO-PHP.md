# PROMPT PARA RECRIAÇÃO DO SISTEMA EM PHP PURO (MVC)

## CONTEXTO

Você receberá dois documentos de referência:
1. **DOCUMENTACAO-SISTEMA-COMPLETA.md** — Análise completa do sistema WordPress atual
2. **Este documento** — Especificação técnica para a recriação em PHP

O objetivo é **recriar 100% do sistema** que hoje roda em WordPress (puntacanaparabrasileiros.com) em um sistema PHP puro com arquitetura MVC, mantendo **todas as funcionalidades, fluxos e layouts idênticos** ao sistema atual.

---

## ARQUITETURA DO NOVO SISTEMA

### Stack Tecnológico
- **Backend**: PHP 8.1+ puro com arquitetura MVC (sem framework)
- **Frontend**: HTML5, CSS3 (com SASS/SCSS), JavaScript (vanilla + jQuery onde necessário)
- **Banco de Dados**: MySQL 8.0+
- **Servidor**: Apache ou Nginx
- **Sessões**: PHP nativas
- **Templates**: PHP nativo (views .php)
- **Rotas**: Sistema de rotas próprio (mod_rewrite / .htaccess)

### Estrutura MVC de Diretórios
```
/
├── index.php               ← Fallback (require public/index.php)
├── .htaccess               ← Rewrite rules (raiz)
├── app/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── TripsController.php
│   │   │   ├── TransfersController.php
│   │   │   ├── BookingsController.php
│   │   │   ├── VouchersController.php
│   │   │   ├── AffiliatesController.php
│   │   │   ├── UsersController.php
│   │   │   └── SettingsController.php
│   │   ├── Frontend/
│   │   │   ├── HomeController.php
│   │   │   ├── TripsController.php
│   │   │   ├── TransferController.php
│   │   │   ├── CartController.php
│   │   │   ├── CheckoutController.php
│   │   │   ├── AccountController.php
│   │   │   └── PageController.php
│   │   ├── Api/
│   │   │   ├── TransferSearchController.php
│   │   │   ├── PricingController.php
│   │   │   ├── CartController.php
│   │   │   └── WebhookController.php
│   │   └── Auth/
│   │       ├── LoginController.php
│   │       └── RegisterController.php
│   ├── Models/
│   │   ├── Trip.php
│   │   ├── TripPackage.php
│   │   ├── TripCategory.php
│   │   ├── Booking.php
│   │   ├── Payment.php
│   │   ├── TransferVehicle.php
│   │   ├── TransferLocation.php
│   │   ├── TransferBooking.php
│   │   ├── Voucher.php
│   │   ├── User.php
│   │   ├── Affiliate.php
│   │   ├── Commission.php
│   │   ├── Wishlist.php
│   │   └── Setting.php
│   ├── Services/
│   │   ├── PricingService.php
│   │   ├── PaymentService.php
│   │   ├── PayPalService.php
│   │   ├── StripeService.php
│   │   ├── VoucherService.php
│   │   ├── EmailService.php
│   │   ├── WhatsAppService.php
│   │   ├── AffiliateService.php
│   │   └── CartService.php
│   ├── Middleware/
│   │   ├── AuthMiddleware.php
│   │   ├── AdminMiddleware.php
│   │   └── CsrfMiddleware.php
│   └── Helpers/
│       ├── functions.php
│       ├── Currency.php
│       └── Validator.php
├── config/
│   ├── routes.php
│   ├── database.php
│   └── app.php
├── database/
│   ├── schema.sql          ← SQL completo para criar todas as tabelas
│   ├── seeds.sql           ← Dados iniciais (locais, configurações padrão)
│   └── migrations/         ← Migrações futuras
├── public/
│   ├── index.php           ← Entry point
│   ├── .htaccess
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   ├── images/
│   │   └── fonts/
│   └── uploads/
│       └── vouchers/       ← Protegido por .htaccess
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.php         ← Layout principal frontend
│       │   ├── admin.php       ← Layout admin
│       │   └── auth.php        ← Layout login/registro
│       ├── frontend/
│       │   ├── home.php
│       │   ├── trips/
│       │   │   ├── index.php       ← Listagem
│       │   │   └── show.php        ← Detalhe do passeio
│       │   ├── transfers/
│       │   │   ├── search.php      ← Formulário de busca
│       │   │   └── results.php     ← Resultados
│       │   ├── cart/
│       │   │   └── index.php
│       │   ├── checkout/
│       │   │   ├── index.php
│       │   │   └── success.php
│       │   ├── account/
│       │   │   ├── dashboard.php
│       │   │   ├── bookings.php
│       │   │   ├── transfers.php
│       │   │   ├── wishlist.php
│       │   │   └── profile.php
│       │   └── pages/
│       │       └── *.php
│       ├── admin/
│       │   ├── dashboard.php
│       │   ├── trips/
│       │   ├── transfers/
│       │   ├── bookings/
│       │   ├── vouchers/
│       │   ├── affiliates/
│       │   ├── users/
│       │   └── settings/
│       ├── emails/
│       │   ├── booking-confirmation.php
│       │   ├── voucher-email.php
│       │   ├── transfer-voucher.php
│       │   ├── admin-notification.php
│       │   └── affiliate-*.php
│       ├── vouchers/
│       │   ├── trip-voucher.php
│       │   └── transfer-voucher.php
│       └── components/
│           ├── header.php
│           ├── footer.php
│           ├── navbar.php
│           └── modals/
│               └── booking-modal.php
├── storage/
│   ├── logs/
│   ├── cache/
│   └── sessions/
└── core/
    ├── App.php
    ├── Router.php
    ├── Database.php
    ├── Controller.php
    ├── Model.php
    ├── View.php
    ├── Session.php
    ├── Request.php
    ├── Response.php
    └── Middleware.php
```

---

## CONFIGURAÇÕES DO SISTEMA (SEM .env)

**NÃO USAR arquivo .env.** Todas as configurações devem ser gerenciadas via **painel administrativo** (tela de Settings) e armazenadas no banco de dados na tabela `settings`.

### Tela de Configurações (Admin → Settings)

O superadmin deve ter uma tela completa de configurações com as seguintes seções/abas:

#### Aba: Geral
- Nome do site
- URL do site
- Email do administrador
- Logo (upload)
- Favicon (upload)
- Moeda padrão (USD)
- Símbolo da moeda
- Formato de data
- Fuso horário
- Idioma padrão

#### Aba: SMTP / Email
- Host SMTP
- Porta SMTP
- Usuário SMTP
- Senha SMTP
- Encryption (TLS/SSL/None)
- Email remetente (From)
- Nome remetente (From Name)
- Botão "Enviar email de teste"

#### Aba: Pagamentos
- **PayPal Express:**
  - Ativar/Desativar
  - Client ID
  - Secret Key
  - Modo (Sandbox / Produção)
- **Stripe:**
  - Ativar/Desativar
  - Publishable Key
  - Secret Key
  - Modo (Test / Live)
- **Pagamento Parcial:**
  - Ativar/Desativar
  - Percentual padrão de depósito (%)

#### Aba: WhatsApp
- Ativar/Desativar notificações WhatsApp
- URL do Webhook
- Template da mensagem de passeio
- Template da mensagem de transfer

#### Aba: Vouchers
- Logo para voucher (upload)
- Texto de rodapé do voucher
- Instruções padrão
- Dias para limpeza automática de vouchers antigos

#### Aba: Afiliados
- Ativar/Desativar programa de afiliados
- Comissão padrão (%)
- Duração do cookie (dias)
- Auto-aprovação (sim/não)
- Método de pagamento (manual/Stripe)

#### Aba: SEO
- Meta title padrão
- Meta description padrão
- Google Analytics ID
- Scripts do <head> (customizáveis)
- Scripts do </body> (customizáveis)

#### Aba: Aparência
- Cores principais (primária, secundária, accent)
- Fonte principal
- CSS customizado
- WhatsApp número para botão flutuante
- Texto do botão WhatsApp

---

## SISTEMA DE PERMISSÕES E USUÁRIOS

### Roles (Papéis)
| Role | Permissões |
|------|-----------|
| **superadmin** | Acesso total a tudo, incluindo Settings |
| **admin** | Gerencia passeios, transfers, bookings, vouchers, afiliados, usuários |
| **editor** | Cria/edita passeios e transfers, visualiza bookings |
| **affiliate** | Acesso ao painel de afiliado (links, comissões, relatórios) |
| **customer** | Acesso à área "Minha Conta" (bookings, transfers, vouchers, wishlist, perfil) |

### Autenticação
- **Página de Login**: email + senha, "Lembrar-me", "Esqueci a senha"
- **Página de Registro**: nome, email, senha, confirmar senha, telefone, país
- **Recuperação de Senha**: envio de link por email com token temporário
- **Sessão**: PHP session com timeout configurável
- **Proteção**: CSRF token em todos os formulários, rate limiting no login

### Área "Minha Conta" (Customer)
- **Dashboard**: resumo das reservas recentes
- **Minhas Reservas**: lista de bookings com status, link para voucher
- **Meus Transfers**: lista de reservas de transfer com status e download de voucher
- **Lista de Desejos (Wishlist)**: passeios salvos como favoritos
- **Meu Perfil**: editar nome, email, telefone, senha, país
- **Logout**

---

## LAYOUT E DESIGN — REPLICAÇÃO EXATA

### REGRA FUNDAMENTAL
O layout visual de TODAS as páginas deve ser **idêntico ao site WordPress atual**. A estrutura HTML/CSS deve replicar fielmente o design Elementor existente, mas usando código estático (sem page builder).

### Páginas Frontend a Replicar

#### 1. Home Page
- Header com logo, menu de navegação, botão de conta/login
- Hero section com banner/slider de destaque
- Seção de passeios populares (cards com imagem, nome, preço, botão)
- Seção de transfers (CTA para busca de transfer)
- Depoimentos/reviews
- Seção Instagram feed
- Footer com links, contato, redes sociais
- Botão flutuante de WhatsApp (canto inferior direito)

#### 2. Listagem de Passeios
- Filtros (por categoria, preço, duração, destino)
- Grid de cards de passeios
- Cada card: imagem, título, preço (de/por), duração, avaliação, botão "Ver Mais"
- Paginação
- Ordenação (preço, popularidade, data)

#### 3. Página do Passeio (Detalhe)
- Galeria de fotos (slider/lightbox)
- Título, preço, avaliação
- Descrição completa
- Itinerário (accordion/tabs com dias)
- Informações adicionais (o que inclui, o que não inclui)
- Mapa
- Previsão do tempo
- Reviews dos clientes
- Sidebar com:
  - **Módulo de booking**: calendário, seleção de pax, preço dinâmico, botão "Reservar"
  - Serviços extras disponíveis
  - Informações de contato rápido
- Botão "Adicionar à Lista de Desejos" (coração)
- Trips relacionados (sugestões)

#### 4. Modal/Pop-up de Reserva
- Calendário para seleção de data (com datas disponíveis destacadas)
- Seleção de horário (se aplicável)
- Seleção de pacote (se múltiplos)
- Contador de passageiros por categoria (+/- com labels e faixas etárias)
- Preço atualiza em tempo real conforme data e passageiros
- Serviços extras (checkboxes com preço)
- Botão "Adicionar ao Carrinho" e "Reservar Agora" (direto checkout)

#### 5. Página de Transfer (Busca)
- Abas: "Ida e Volta" | "Somente Ida" | "Múltiplos Transfers"
- Formulário de busca:
  - Selects com busca (origem, destino) — usando Select2 ou similar
  - Campos de data e hora
  - Seletor de passageiros (adultos, crianças, bebês) com +/-
  - Tipo de serviço (privado/coletivo)
- Resultados aparecem abaixo (AJAX):
  - Cards de veículos com: foto, nome, capacidade, preço, duração, botão
- Botões: "Adicionar ao Carrinho" e "Ir Direto ao Checkout"

#### 6. Página de Carrinho
- Lista de itens (trips + transfers)
- Cada item: imagem thumb, nome, data, passageiros, preço, botão remover
- Transfers agrupados (ida+volta = pacote)
- Resumo de totais
- Botão "Ir para Checkout"

#### 7. Página de Checkout
- Resumo do pedido (mini-cart lateral ou topo)
- Formulário:
  - Dados de cobrança (nome, email, telefone, endereço, país com busca)
  - Dados dos viajantes (nome completo de cada)
  - Dados de passageiros de transfer (se houver)
- Seleção de pagamento (radio: PayPal / Stripe)
- Opção de pagamento parcial (se habilitado)
- Termos e condições (checkbox)
- Botão de confirmação
- Loading/overlay durante processamento

#### 8. Página de Confirmação/Obrigado
- Mensagem de sucesso
- Resumo da reserva (nº do booking, trips, transfers, total pago)
- Link para área "Minha Conta"
- Botão "Voltar para Home"

#### 9. Área Minha Conta
- Menu lateral com: Dashboard, Reservas, Transfers, Wishlist, Perfil
- Design responsivo e limpo
- Tabelas com status coloridos
- Botões de ação (download voucher, ver detalhes)

#### 10. Login/Registro
- Design centrado, minimalista
- Formulários com validação inline
- Links entre login ↔ registro ↔ recuperar senha

---

## PAINEL ADMINISTRATIVO

### Dashboard Admin
- Cards com métricas: total de vendas, reservas hoje, reservas pendentes, receita do mês
- Gráfico simples de reservas últimos 30 dias
- Últimas reservas (tabela rápida)
- Alertas (bookings pendentes, etc.)

### Gerenciamento de Passeios (Trips)
- CRUD completo de trips
- Editor rico para descrição
- Upload de galeria de imagens
- Configuração de pacotes com categorias e preços
- Configuração de preço por dia (dia da semana, feriados, datas específicas)
- Datas fixas de saída (calendário de disponibilidade)
- Itinerário com múltiplos dias/etapas
- Serviços extras
- Configuração de pagamento parcial (% por trip)
- Desconto de grupo
- Informações de inclusão/exclusão
- SEO (meta title, description)
- Status (publicado, rascunho, desativado)

### Gerenciamento de Transfers
- CRUD de Veículos (com imagem, capacidade, descrição)
- CRUD de Locais (com endereço, latitude, longitude)
- Configuração de Rotas por veículo:
  - Origem → Destino
  - Preço base
  - Duração
  - Tarifas por faixa de passageiros (min, max, preço, tipo: privado/coletivo)
- Reservas de Transfer:
  - Listagem com filtros (data, status, veículo)
  - Detalhes da reserva
  - Ações: visualizar voucher, enviar por email, marcar como concluído

### Gerenciamento de Bookings
- Listagem com filtros (data, status, trip, cliente)
- Detalhes completos do booking (dados do cliente, itens, pagamentos, transfers)
- Alterar status (pendente, confirmado, cancelado, concluído)
- Enviar voucher manualmente
- Ver histórico de pagamentos
- Criar booking manual (para vendas offline)

### Gerenciamento de Vouchers
- Listagem de vouchers gerados (com filtros)
- Visualizar, baixar, reenviar por email
- Log de envios

### Gerenciamento de Afiliados
- Listagem de afiliados (com status, comissões, vendas)
- Aprovar/rejeitar afiliados
- Visualizar comissões pendentes/pagas
- Processar pagamentos
- Configurar % por afiliado (override do padrão)

### Gerenciamento de Usuários
- CRUD de usuários
- Atribuição de roles
- Resetar senha
- Login como usuário (impersonate)
- Filtros por role

### Settings (conforme seção anterior)

---

## BANCO DE DADOS — ESQUEMA SQL

Criar arquivo `database/schema.sql` com todas as tabelas. As principais:

```sql
-- Usar InnoDB, charset utf8mb4

-- Usuários e Autenticação
CREATE TABLE users (...)
CREATE TABLE password_resets (...)
CREATE TABLE sessions (...)

-- Configurações
CREATE TABLE settings (key, value, group, type)

-- Passeios
CREATE TABLE trips (id, title, slug, description, short_description, featured_image, gallery, duration, difficulty, min_pax, max_pax, includes, excludes, map_data, status, created_at, updated_at)
CREATE TABLE trip_categories (id, name, slug, parent_id, image)
CREATE TABLE trip_category_relations (trip_id, category_id)
CREATE TABLE trip_packages (id, trip_id, title, sort_order)
CREATE TABLE trip_package_categories (id, package_id, traveler_category_id, price, sale_price, min_pax, max_pax)
CREATE TABLE traveler_categories (id, name, slug, age_group, sort_order)
CREATE TABLE trip_fixed_dates (id, trip_id, date, time, max_pax, status)
CREATE TABLE trip_itinerary (id, trip_id, day_number, title, description, sort_order)
CREATE TABLE trip_extra_services (id, trip_id, name, price, type, required)
CREATE TABLE trip_reviews (id, trip_id, user_id, rating, comment, status, created_at)

-- Preço Dinâmico por Dia
CREATE TABLE trip_day_pricing (id, package_id, category_id, rule_type ENUM('weekday','holiday','specific','monthly','annual'), day_key, price, sale_price, active, label)

-- Transfers
CREATE TABLE transfer_vehicles (id, title, description, image, max_passengers, max_adults, max_children, max_infants, max_luggage, status)
CREATE TABLE transfer_locations (id, title, address, latitude, longitude)
CREATE TABLE transfer_routes (id, vehicle_id, origin_id, destination_id, base_price, duration)
CREATE TABLE transfer_tariffs (id, route_id, service_type ENUM('private','shared'), min_pax, max_pax, price)
CREATE TABLE transfer_bookings (id, vehicle_id, origin_id, destination_id, booking_id, date, time, type ENUM('arrival','departure'), service_type, price, adults, children, infants, customer_name, customer_email, customer_phone, passengers JSON, flight_number, flight_time, status, created_at)

-- Carrinho (persistência opcional)
CREATE TABLE cart_sessions (id, session_id, user_id, data JSON, expires_at)

-- Reservas/Bookings
CREATE TABLE bookings (id, user_id, booking_number, status, subtotal, total, paid_amount, due_amount, payment_mode, billing_info JSON, notes, created_at, updated_at)
CREATE TABLE booking_items (id, booking_id, trip_id, package_id, trip_date, trip_time, pax JSON, extra_services JSON, price, partial_price)
CREATE TABLE booking_travelers (id, booking_item_id, name, email, phone, age_group, extra_data JSON)

-- Pagamentos
CREATE TABLE payments (id, booking_id, gateway, transaction_id, amount, currency, status, gateway_response JSON, type ENUM('full','partial','remaining'), created_at)

-- Vouchers
CREATE TABLE vouchers (id, booking_id, booking_item_id, transfer_booking_id, reference_code, file_path, email_sent, whatsapp_sent, created_at)

-- Afiliados
CREATE TABLE affiliates (id, user_id, status, commission_rate, cookie_days, total_sales, total_earnings, created_at)
CREATE TABLE affiliate_visits (id, affiliate_id, ip, referrer, page_url, created_at)
CREATE TABLE commissions (id, affiliate_id, booking_id, amount, rate, status ENUM('pending','approved','paid','rejected'), paid_at, created_at)

-- Wishlist (Lista de Desejos)
CREATE TABLE wishlists (id, user_id, trip_id, created_at)

-- Logs
CREATE TABLE activity_log (id, user_id, action, entity_type, entity_id, details JSON, ip, created_at)
CREATE TABLE email_log (id, to_email, subject, status, error, created_at)
CREATE TABLE voucher_log (id, booking_id, reference, email, trip_name, file_path, email_sent, created_at)
```

**IMPORTANTE**: Gerar o arquivo SQL completo com todas as tabelas, índices, foreign keys e constraints. O banco será criado manualmente executando esse arquivo.

### REGRA DE IMUTABILIDADE DOS ARQUIVOS SQL

**Uma vez que um arquivo SQL é criado, ele NUNCA pode ser editado.** Se precisar alterar a estrutura do banco (adicionar coluna, mudar tipo, criar tabela nova), deve-se criar um **novo arquivo de migration** na pasta `database/migrations/` com naming sequencial:

```
database/
├── schema.sql                    ← Criação inicial (IMUTÁVEL após criado)
├── seeds.sql                     ← Dados iniciais (IMUTÁVEL após criado)
└── migrations/
    ├── 001_add_column_x_to_trips.sql
    ├── 002_create_table_coupons.sql
    ├── 003_alter_bookings_add_coupon_id.sql
    └── ...
```

**Regras:**
- `schema.sql` é gerado UMA VEZ e nunca mais tocado
- `seeds.sql` é gerado UMA VEZ e nunca mais tocado
- Qualquer alteração posterior ao banco vai em `database/migrations/XXX_descricao.sql`
- Migrations são numeradas sequencialmente (001, 002, 003...)
- Cada migration deve ser idempotente (usar IF NOT EXISTS, IF EXISTS, etc.)
- O sistema deve ter um mecanismo simples para rodar migrations pendentes (verificar quais já foram executadas)

---

## FUNCIONALIDADES QUE DEVEM SER RECRIADAS (CHECKLIST)

### Passeios
- [ ] CRUD de passeios com editor rico
- [ ] Pacotes com múltiplas categorias de viajante
- [ ] Preço dinâmico por dia da semana
- [ ] Preço por feriado
- [ ] Preço por data específica
- [ ] Preço por mês e período anual
- [ ] Datas fixas de saída com disponibilidade
- [ ] Itinerário multi-dia
- [ ] Galeria de fotos com lightbox
- [ ] Serviços extras (add-ons)
- [ ] Desconto de grupo
- [ ] Reviews/avaliações com estrelas
- [ ] Lista de desejos (wishlist/favoritos)
- [ ] Trips relacionados (sugestão)
- [ ] Embedder de trips (widget para outras páginas)

### Transfers
- [ ] Busca: Ida e volta, somente ida, múltiplos
- [ ] Seleção de origem/destino com busca
- [ ] Seleção de data/hora
- [ ] Seleção de passageiros (adulto/criança/bebê)
- [ ] Tipo de serviço (privado/coletivo)
- [ ] Resultados com veículos, preço, capacidade
- [ ] Preço calculado no servidor por faixa
- [ ] Espelhamento de rota inversa
- [ ] Adição ao carrinho ou checkout direto
- [ ] Campos de nome de passageiro no checkout
- [ ] Voucher de transfer com dados completos

### Carrinho
- [ ] Multi-item (trips + transfers)
- [ ] Agrupamento de transfers (ida+volta)
- [ ] Remoção individual
- [ ] Totais calculados (subtotal, total, parcial)
- [ ] Expiração (7 dias)
- [ ] Persistência em sessão

### Checkout
- [ ] Formulário de dados do cliente
- [ ] Campo de país com busca/filtro
- [ ] Dados dos viajantes (por trip)
- [ ] Dados dos passageiros de transfer
- [ ] Seleção de gateway de pagamento
- [ ] Pagamento parcial (depósito %)
- [ ] Checkout transfer-only (sem trip)
- [ ] Validação frontend + backend
- [ ] CSRF protection

### Pagamentos
- [ ] PayPal Express (SDK JS, sandbox/produção)
- [ ] Stripe (PaymentIntent, Elements)
- [ ] Pagamento parcial com status "partially_paid"
- [ ] Pagamento do restante (remaining payment)
- [ ] Status: pending, paid, partially_paid, failed, refunded

### Vouchers
- [ ] Geração automática de HTML
- [ ] Template profissional com dados completos
- [ ] Envio por email como anexo
- [ ] Download seguro na área do cliente
- [ ] QR Code no voucher
- [ ] Limpeza automática de antigos
- [ ] Re-envio manual pelo admin

### Notificações
- [ ] Email de confirmação de reserva (cliente)
- [ ] Email com vouchers (cliente)
- [ ] Email de notificação (admin)
- [ ] WhatsApp automático via webhook
- [ ] Templates customizáveis (admin pode editar)
- [ ] Log de todos os envios

### Afiliados
- [ ] Cadastro de afiliado
- [ ] Painel do afiliado (links, comissões, relatórios)
- [ ] Cookie de rastreamento
- [ ] Cálculo automático de comissão
- [ ] Aprovação manual pelo admin
- [ ] Processamento de pagamento

### Área do Cliente
- [ ] Dashboard com resumo
- [ ] Lista de reservas com status e ações
- [ ] Lista de transfers com voucher download
- [ ] Lista de desejos (favoritar/desfavoritar passeios)
- [ ] Edição de perfil
- [ ] Alteração de senha

### Admin
- [ ] Dashboard com métricas
- [ ] CRUD completo de todas as entidades
- [ ] Configuração de preço por dia (interface completa)
- [ ] Gerenciamento de vouchers
- [ ] Gerenciamento de afiliados e comissões
- [ ] Tela de Settings (todas as abas)
- [ ] Gerenciamento de usuários e roles
- [ ] Login como outro usuário (impersonate)
- [ ] Logs de atividade

---

## ARQUIVOS OBRIGATÓRIOS NA RAIZ DO PROJETO

Os seguintes arquivos devem existir na **raiz** do projeto (fora de `/public`):

### `index.php` (raiz)
```php
<?php
// Front controller de fallback — redireciona para public/index.php
require __DIR__ . '/public/index.php';
```

### `.htaccess` (raiz)
```apache
RewriteEngine On

# Servir assets diretamente do /public
RewriteRule ^assets/(.*)$ public/assets/$1 [L]
RewriteRule ^uploads/(.*)$ public/uploads/$1 [L]
RewriteRule ^import\.png$ public/import.png [L]
RewriteRule ^favicon\.ico$ public/favicon.ico [L]

# Se o arquivo ou diretório existir na raiz, entrega direto
RewriteCond %{REQUEST_FILENAME} -f [OR]
RewriteCond %{REQUEST_FILENAME} -d
RewriteRule ^ - [L]

# Caso contrário, envia tudo para o front controller em /public
RewriteRule ^ public/index.php [L]
```

Esses dois arquivos são **obrigatórios** e devem ser criados exatamente como mostrado acima. O `.htaccess` da raiz garante que todas as requisições sejam encaminhadas para o front controller em `public/index.php`, enquanto assets e uploads são servidos diretamente.

---

## REGRAS TÉCNICAS OBRIGATÓRIAS

1. **PHP 8.1+** com tipagem rigorosa (declare strict_types)
2. **MVC puro** — sem frameworks (Laravel, Symfony, etc.)
3. **Sem .env** — configurações no banco (tabela `settings`) editáveis via admin
4. **SQL puro** — gerar arquivos `.sql` para criação do banco, não ORMs
5. **Segurança**:
   - Prepared statements (PDO) em todas as queries
   - CSRF tokens em todos os formulários
   - XSS prevention (htmlspecialchars em outputs)
   - Rate limiting no login e endpoints sensíveis
   - Senha com password_hash/password_verify (bcrypt)
   - Preços SEMPRE calculados no servidor
   - Upload de arquivos com validação de tipo e tamanho
6. **Sessão PHP nativa** para carrinho e autenticação
7. **Responsivo** — Mobile-first (turistas usam celular)
8. **Performance** — Queries otimizadas, paginação server-side, cache onde possível
9. **Layout idêntico** ao site WordPress atual (replicar HTML/CSS do Elementor)
10. **Todos os fluxos documentados** devem funcionar exatamente como descrito

---

## INTEGRAÇÕES EXTERNAS A IMPLEMENTAR

| Serviço | Tipo | Para quê |
|---------|------|----------|
| PayPal REST API | Payment | Processar pagamentos com PayPal Express |
| Stripe API | Payment | Processar pagamentos com cartão |
| WhatsApp Webhook | Notification | Enviar vouchers/confirmações por WhatsApp |
| SMTP | Email | Enviar todos os emails transacionais |
| QR Code API | Utility | Gerar QR codes nos vouchers (pode ser lib PHP) |

---

## ENTREGÁVEIS ESPERADOS

1. **`database/schema.sql`** — Script SQL completo para criar todas as tabelas do sistema
2. **`database/seeds.sql`** — Dados iniciais (settings padrão, roles, categorias de viajante)
3. **Código PHP completo** seguindo a estrutura MVC descrita
4. **Frontend completo** com HTML/CSS/JS replicando fielmente o layout atual
5. **Painel Admin funcional** com todas as telas de gerenciamento
6. **Sistema de autenticação** com login, registro, recuperação de senha
7. **Sistema de pagamentos** integrado com PayPal e Stripe
8. **Sistema de notificações** (email + WhatsApp)
9. **Sistema de vouchers** com geração, envio e download
10. **Sistema de afiliados** funcional
11. **`.htaccess`** configurado para rotas amigáveis e proteção de diretórios

---

## OBSERVAÇÕES FINAIS

- O sistema deve ser **auto-contido** — não depende de WordPress, plugins ou qualquer CMS
- Tudo que antes era feito por plugins agora será código próprio
- A migração de dados do WordPress (trips, bookings, etc.) não é escopo deste prompt — o foco é recriar o sistema do zero com a mesma funcionalidade
- O design deve ser pixel-perfect em relação ao site atual — copie o CSS/layout das páginas Elementor existentes
- Qualquer funcionalidade descrita no documento DOCUMENTACAO-SISTEMA-COMPLETA.md que não esteja explicitamente mencionada aqui TAMBÉM deve ser incluída — a regra é: se existe no WordPress, existe no novo sistema
- O idioma da interface do frontend é Português (BR), o admin pode ser em Português também
- Toda comunicação com o cliente (emails, WhatsApp, vouchers) é em Português (BR)
- O WordPress está em D:\Projects\PuntaCana\puntacanaparabrasileiros.com\ aqui no meu computador, se precisar ver algo dele, ou pode pedir para mim que peço para o meu desenvolvedor auxiliar confirmar nos arquivos do WordPress, como fuxos, códigos e etc.

---

*Este documento deve ser usado em conjunto com DOCUMENTACAO-SISTEMA-COMPLETA.md para a recriação completa do sistema.*
