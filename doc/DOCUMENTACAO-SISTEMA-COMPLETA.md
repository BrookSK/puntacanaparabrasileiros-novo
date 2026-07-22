# DOCUMENTAÇÃO COMPLETA DO SISTEMA - puntacanaparabrasileiros.com

## Objetivo deste documento
Documentar **todos os fluxos, funcionalidades, plugins, configurações e regras de negócio** do site atual em WordPress para permitir a recriação completa em PHP puro.

---

## 1. VISÃO GERAL DO SISTEMA

| Item | Valor |
|------|-------|
| URL | https://puntacanaparabrasileiros.com |
| CMS | WordPress |
| Tema Principal | Astra (+ Travel Monster como alternativo) |
| Page Builder | Elementor + Elementor Pro |
| Idioma Principal | Português (BR) |
| Moeda | USD (Dólar americano) |
| Banco de Dados | sql_puntacanapar |
| Table Prefix | wp_cad131_ |
| Servidor | Nginx + LiteSpeed Cache |
| SSL | Forçado (HTTPS) |
| Cache | WP Rocket + LiteSpeed + Nginx Helper |

---

## 2. SISTEMA DE PASSEIOS (TOURS/TRIPS)

### 2.1 Plugin Principal: WP Travel Engine + WP Travel Engine Pro

O core do sistema de passeios é o **WP Travel Engine** com seus addons premium.

#### CPT (Custom Post Type): `trip`
Cada passeio é um post do tipo `trip` com:
- Título, descrição, imagem destacada
- Galeria de fotos
- Itinerário avançado (via Advanced Itinerary Builder)
- Datas fixas de saída (via Trip Fixed Starting Dates)
- Previsão do tempo (via Trip Weather Forecast)
- Reviews/avaliações (via Trip Reviews)
- Documentos legais (via Legal Documents)
- Downloads de arquivos (via File Downloads)

#### Pacotes de Preço (`trip-packages`)
Cada trip pode ter múltiplos pacotes com:
- **Categorias de viajante** (Adulto, Criança, Bebê, etc.) — cada uma com seu preço
- **Preço regular** e **preço promocional** (sale_price)
- **Preço por dia da semana** (customização via LRV Day Pricing)
- **Preço por feriado** (DD/MM ou DD/MM/AAAA)
- **Preço por data específica** (maior prioridade)
- **Preço por mês** (regras mensais)
- **Preço por período anual** (meses do ano)

#### Prioridade de Preço (maior para menor):
1. Data específica (DD/MM/AAAA)
2. Feriado (DD/MM)
3. Dia da semana (segunda a domingo)
4. Preço mensal (dias do mês)
5. Preço anual (meses)
6. Preço padrão do pacote

### 2.2 Fluxo de Reserva de Passeio (Frontend)

```
1. Cliente acessa a página do passeio (single trip)
2. Pop-up/Modal de reserva abre com:
   - Calendário para selecionar DATA
   - Seleção de HORÁRIO (se aplicável)
   - Seleção de PACOTE (se múltiplos)
   - Seleção de PASSAGEIROS por categoria (Adulto, Criança, Bebê)
   - Preço atualiza dinamicamente conforme data selecionada (via AJAX)
3. Cliente clica "Reservar" → item adicionado ao CARRINHO (multi-cart habilitado)
4. Cliente pode:
   a) Continuar comprando (adicionar mais passeios)
   b) Ir para o CHECKOUT
5. No CHECKOUT:
   - Resumo dos itens no carrinho (trips + transfers se houver)
   - Formulário de dados pessoais (nome, email, telefone, país)
   - Campo de país com busca/filtro dinâmico
   - Dados dos viajantes por trip
   - Seleção de método de pagamento
   - Pagamento parcial (depósito %) ou total
6. Após pagamento confirmado:
   - Booking criado (CPT: booking)
   - Vouchers HTML gerados automaticamente
   - Email enviado ao cliente com vouchers em anexo
   - Mensagem WhatsApp enviada via webhook
   - Página de confirmação/obrigado exibida
```

### 2.3 Configurações do Trip

- **Desconto de Grupo** (via Group Discount addon): desconto baseado em qtd de passageiros
- **Serviços Extra** (via Extra Services addon): itens adicionais como seguro, almoço, etc.
- **Partial Payment** (depósito): pode configurar por trip o % mínimo a pagar
- **Social Proof**: mostra quantas reservas recentes existem
- **Countdown**: contagem regressiva para datas fixas de saída
- **Currency Converter**: exibe preços em múltiplas moedas
- **Conditional Pricing**: preços condicionais baseados em regras

---

## 3. SISTEMA DE TRANSFERS

### 3.1 Plugin: WTE Transfer (Custom)

Plugin customizado que gerencia todo o sistema de transfers aeroporto/hotel/destinos.

#### Custom Post Types:
| CPT | Descrição |
|-----|-----------|
| `transfer_vehicle` | Veículos disponíveis (van, SUV, ônibus, etc.) |
| `transfer_location` | Locais/pontos de embarque e desembarque |
| `transfer_booking` | Reservas de transfer realizadas |

#### Taxonomia:
- `vehicle_type` — Tipos de veículo (hierárquica)

### 3.2 Configuração de Veículos

Cada veículo (`transfer_vehicle`) possui:
- **Título** (nome do veículo/serviço)
- **Descrição e imagem**
- **Capacidade**:
  - Máx. passageiros total
  - Máx. adultos (+12 anos)
  - Máx. crianças (2-11 anos)
  - Máx. bebês (0-1 ano)
  - Capacidade de bagagem
- **Rotas** (array de rotas):
  - Origem (transfer_location ID)
  - Destino (transfer_location ID)
  - Preço base (USD)
  - Duração (minutos)
  - **Tarifas por faixa** (opcional):
    - Tipo de serviço: Privado ou Coletivo (shared)
    - Mín. passageiros
    - Máx. passageiros
    - Preço para essa faixa

### 3.3 Configuração de Locais

Cada local (`transfer_location`) possui:
- Título (nome do local)
- Endereço
- Latitude/Longitude (para mapa)

### 3.4 Lógica de Preço de Transfer

```
1. Busca o veículo e a rota (origem → destino)
2. Se a rota tem TARIFAS por faixa:
   - Filtra por tipo de serviço (privado/coletivo)
   - Verifica total de passageiros (adultos + crianças + bebês)
   - Encontra a faixa onde min <= total <= max
   - Retorna o preço da faixa
   - Se nenhuma faixa corresponde → veículo não aparece
3. Se a rota NÃO tem tarifas:
   - Tenta "espelhar" da rota inversa (destino → origem)
   - Se a inversa tem tarifas, usa as mesmas
   - Se não, usa o preço base da rota
4. Se nada encontrado → veículo não listado
```

### 3.5 Fluxo de Reserva de Transfer (Frontend)

```
1. Página de transfer com shortcode [wte_transfer_search]
2. Formulário de busca:
   - ABAS: "Ida e Volta" | "Somente Ida" | "Múltiplos Transfers"
   - Campos:
     - Origem (select com transfer_locations)
     - Destino (select com transfer_locations)
     - Data de Chegada + Hora
     - Data de Partida + Hora (se ida e volta)
     - Passageiros: Adultos (+12), Crianças (2-11), Bebês (0-1)
     - Tipo de Serviço: Privado ou Coletivo
   - Botão "Buscar"
3. AJAX → search_transfers → retorna veículos disponíveis com preços
4. Resultados exibidos com:
   - Nome do veículo, imagem, descrição
   - Preço calculado
   - Capacidade máxima de passageiros e bagagem
   - Duração estimada
5. Cliente seleciona veículo → Adiciona ao carrinho
   - OPÇÃO A: "Adicionar ao Carrinho" (vai para página de carrinho)
   - OPÇÃO B: "Ir Direto ao Checkout" (pula carrinho)
6. No CHECKOUT:
   - Se tem SOMENTE transfers (sem trips):
     → Checkout especial "transfer-only" é ativado
     → Formulário de dados pessoais
     → Campos de passageiros (nome de cada passageiro por tipo)
   - Se tem trips + transfers:
     → Checkout normal do WTE com transfers integrados
7. Após confirmação:
   - Post `transfer_booking` criado para cada transfer
   - Ordem WooCommerce criada (se WC ativo)
   - Voucher HTML gerado e enviado por email
   - Mensagem de sucesso com detalhes dos transfers
   - Redirect para página de obrigado (/transfer-obrigado/)
```

### 3.6 Dados Salvos por Reserva de Transfer

Cada `transfer_booking` post salva:
- `_vehicle_id` — ID do veículo
- `_origin` — ID da localização de origem
- `_destination` — ID da localização de destino
- `_date` — Data do transfer
- `_time` — Horário
- `_type` — "arrival" (chegada) ou "departure" (partida)
- `_price` — Preço cobrado
- `_adults` — Qtd adultos
- `_children` — Qtd crianças
- `_infants` — Qtd bebês
- `_customer_name` — Nome do cliente
- `_customer_email` — Email
- `_customer_phone` — Telefone/WhatsApp
- `_passengers` — Array com nome e tipo de cada passageiro
- `_flight_number` — Número do voo (opcional)
- `_flight_time` — Horário do voo (opcional)
- `_booking_id` — ID do booking principal (se integrado ao WTE)

### 3.7 Área do Cliente (My Account)

Tab "Meus Transfers" no dashboard do WTE:
- Lista todas as reservas de transfer do usuário
- Mostra: veículo, rota, data, status (Confirmado/Pendente)
- Botão para baixar voucher (HTML imprimível, abre diálogo de impressão)

---

## 4. SISTEMA DE PAGAMENTOS

### 4.1 Gateways Disponíveis

| Gateway | Plugin | Fluxo |
|---------|--------|-------|
| **PayPal Express** | wp-travel-engine-paypal-express-gateway v2.2.0 | PayPal SDK JS → aprovação → capture → booking confirmado |
| **Stripe** | wp-travel-engine-stripe-payment-gateway v2.2.2 | Stripe Elements → PaymentIntent → confirmação → booking confirmado |
| **WooCommerce PayPal** | woocommerce-paypal-payments v4.0.4 | Via bridge WTE↔WC → PayPal, Pay Later, cartões |
| **WooCommerce (genérico)** | wptravelengine-woocommerce-payments | Bridge que roteia pagamentos WTE pelos gateways do WooCommerce |

### 4.2 Configurações PayPal Express (WTE nativo)

- `paypalexpress_enable` — Ativa/desativa
- `paypalexpress_client_id` — Client ID do PayPal
- `paypalexpress_secret` — Secret Key
- `paypalexpress_payment_method` — Métodos desabilitados (ex: "card")
- Ambiente: sandbox (se WP_TRAVEL_ENGINE_PAYMENT_DEBUG = true) ou produção
- Moeda: usa a moeda configurada no WTE (USD)

### 4.3 Configurações Stripe (WTE nativo)

- `stripe_publishable` — Publishable Key
- `stripe_secret` — Secret Key
- `stripe_btn_label` — Label do botão
- `stripe_hide_postal_code` — Ocultar CEP no formulário

### 4.4 Pagamento Parcial (Deposit)

- Plugin: wp-travel-engine-partial-payment
- Configurável por trip individualmente
- Define % mínimo a pagar no checkout
- Cria status "partially_paid" no booking
- Permite pagamento do restante depois (`remaining_payment`)
- Calcula: `paid_amount`, `due_amount`, `total`, `partial_total`

### 4.5 Fluxo de Pagamento (Geral)

```
1. Checkout form submetido
2. WTE processa reserva → cria booking + payment posts
3. Gateway selecionado processa o pagamento:
   - PayPal: SDK JS renderiza botão → approve → capture
   - Stripe: PaymentIntent criado → elemento de cartão → confirmação
4. Após sucesso:
   - payment_status = "captured" ou "completed"
   - booking_status = "booked"
   - paid_amount atualizado
   - due_amount atualizado (se parcial)
   - WTE_Booking::send_emails() → envia confirmação
   - Vouchers gerados e enviados
   - WhatsApp webhook disparado
5. Redirect para página de confirmação
```

---

## 5. SISTEMA DE NOTIFICAÇÕES

### 5.1 Email (via SureMail)

- **SureMail** substitui o `wp_mail()` nativo do WordPress
- Roteia todos os emails por SMTP confiável
- Garante entrega em inbox (não spam)
- Mantém logs de emails enviados

### 5.2 Templates de Email

- **WTE Email Customizer**: personaliza templates visuais de email do WTE
- **Per-Trip Emails**: templates diferentes por passeio específico
- Tipos de email enviados:
  - Confirmação de reserva (cliente)
  - Notificação de nova reserva (admin)
  - Vouchers de passeio (cliente, com HTML em anexo)
  - Vouchers de transfer (cliente, com HTML em anexo)

### 5.3 WhatsApp (via Webhook)

- **Não usa Joinchat para notificações** (Joinchat é só botão de chat no site)
- Usa webhook customizado para enviar mensagens
- URL do webhook: `https://api.lrvweb.com.br/api/webhooks/028f76ff-75bf-46eb-a2c9-afeff5e718b8`
- Payload:
```json
{
  "numero": "5511999999999",
  "message": "🎫 SEUS VOUCHERS DE PASSEIO 🎫\n..."
}
```
- Mensagem inclui: emojis formatados, dados do passeio, data, horário, ingressos, código do voucher, instruções
- Enviado automaticamente após booking confirmado

### 5.4 Vouchers (HTML)

#### Voucher de Passeio (gerado pelo WTE Multi Cart Enhancement + WTE Voucher System):
- Arquivo HTML salvo em: `/wp-content/uploads/wte-vouchers/`
- Naming: `voucher-{booking_id}-{trip_id}.html`
- Protegido por .htaccess (deny from all)
- Dados incluídos:
  - Nome do passeio, data, horário
  - Nome do cliente, email, WhatsApp
  - Categorias de passageiros e quantidades
  - Valor total e valor pago
  - Código de referência único
  - Instruções de embarque
- Enviado como ANEXO no email
- Link de download na área "Minha Conta"
- Limpeza automática de vouchers com mais de 90 dias

#### Voucher de Transfer (gerado pelo WTE Transfer):
- Formato HTML com layout profissional
- Dados: veículo, rota, data/hora, passageiros, tipo (chegada/partida)
- Geração de PDF básico (sem biblioteca externa — usa PDF raw com Helvetica)
- Também gera versão HTML imprimível (com window.print())
- Inclui QR Code via API externa (qrserver.com)
- Ações admin: Visualizar, Baixar, Enviar por Email

---

## 6. CARRINHO E CHECKOUT

### 6.1 Multi-Cart (WTE Multi Cart Enhancement)

- Permite múltiplos itens no carrinho do WTE simultaneamente
- Trips + Transfers podem coexistir no mesmo carrinho
- Transfers ficam na sessão PHP: `$_SESSION['wte_transfer_cart']`
- Trips ficam no cart nativo do WTE
- Expiração do carrinho: 7 dias (604800 segundos)
- Limpeza de client storage (localStorage, sessionStorage, cookies) após checkout

### 6.2 Página de Carrinho

- Exibe trips com: imagem, nome, data, passageiros por categoria, preço
- Exibe transfers com: veículo, rota, data/hora, passageiros, preço
- Transfers agrupados por `group_id` (ida+volta = 1 pacote)
- Botão remover por item/pacote
- Total geral (trips + transfers somados)
- Botão "Ir para Checkout"

### 6.3 Página de Checkout

- Formulário com campos:
  - Nome, Sobrenome, Email, Telefone
  - Endereço, Cidade, País (com filtro/busca)
  - Dados dos viajantes (nome completo por viajante, por trip)
  - Dados de passageiros de transfer (nome completo por passageiro)
- Resumo do pedido (mini-cart) com totais
- Seleção de gateway de pagamento
- Opção de pagamento parcial (se habilitado)
- Submit → processamento → pagamento → confirmação

### 6.4 Checkout "Transfer-Only"

Quando há SOMENTE transfers no carrinho (sem trips):
- Intercepta o POST antes do WTE processar
- Cria posts `transfer_booking` diretamente
- Não cria booking do WTE (sem trip)
- Envia vouchers por email
- Redirect para `/transfer-obrigado/?transfer_success=1`
- Exibe mensagem de sucesso com detalhes dos transfers

---

## 7. SISTEMA DE AFILIADOS (SliceWP)

### 7.1 Configuração

- Plugin: SliceWP + SliceWP Pro
- Comissão padrão: Configurável (tipicamente 20%)
- Tipo: Percentual sobre a venda
- Cookie de rastreamento: 30 dias
- Integrações customizadas:
  - **lrvweb-slicewp-hubspot** — Sincroniza afiliados com HubSpot CRM
  - **lrvweb-slicewp-stripe-payouts** — Pagamento de comissões via Stripe
  - **lrvweb-slicewp-webhooks** — Webhooks para notificações externas
  - **lrvweb-slicewp-landing-tab** — Tab de landing pages para afiliados
  - **wpte-slicewp-integration-custom** — Integração customizada WTE ↔ SliceWP

### 7.2 Fluxo de Afiliados

```
1. Afiliado se cadastra no programa
2. Admin aprova (ou auto-aprovação)
3. Afiliado recebe link único com parâmetro de rastreamento
4. Cliente acessa via link do afiliado → cookie setado (30 dias)
5. Cliente realiza compra
6. Sistema detecta cookie → cria comissão para o afiliado
7. Comissão fica pendente até aprovação
8. Admin aprova → pagamento via Stripe Payouts
9. Notificações por email em cada etapa
```

---

## 8. AUTOMAÇÕES (Uncanny Automator)

- Plugin: Uncanny Automator Pro v7.1.0.1
- Receitas/automações armazenadas no banco de dados
- Conecta plugins do WordPress entre si
- Possíveis automações configuradas:
  - Booking criado → ação X
  - Pagamento confirmado → ação Y
  - Novo usuário registrado → ação Z
  - Integração com WP Travel Engine, WooCommerce, etc.
- Nota: receitas específicas precisam de acesso ao banco para inspecionar

---

## 9. MULTILÍNGUE E SEO

### 9.1 Tradução
- **Polylang** — Sistema de multilíngue principal
- **Automatic Translations for Polylang** — Tradução automática
- **Loco Translate** + addon automático — Tradução de strings
- **TranslatePress** — Tradução visual (alternativo)
- **GTranslate** — Widget de tradução rápida

### 9.2 SEO
- **Rank Math SEO + Pro** — SEO principal (meta tags, schema, sitemap)
- **Google Site Kit** — Integração com Google Analytics/Search Console

---

## 10. PLUGINS AUXILIARES INSTALADOS

### 10.1 Conteúdo e Design
| Plugin | Função |
|--------|--------|
| Elementor + Pro | Page builder visual |
| Jet Engine | Custom post types, fields e queries dinâmicas |
| Jet Elements | Widgets extras para Elementor |
| Mega Elements for Elementor | Addons de elementos |
| WTE Elementor Widgets | Widgets WTE para Elementor |

### 10.2 Formulários
| Plugin | Função |
|--------|--------|
| Contact Form 7 | Formulários de contato simples |
| Fluent Forms + Pro | Formulários avançados |
| Fluent Forms PDF | Geração de PDF a partir de forms |
| SureForms | Formulários adicionais |

### 10.3 Segurança e Performance
| Plugin | Função |
|--------|--------|
| WP Cerber | Segurança, firewall, anti-spam |
| WP Rocket | Cache de página e otimização |
| LiteSpeed Cache | Cache no nível do servidor |
| WP Smush It | Compressão de imagens |
| WP Optimize | Otimização de banco de dados |
| Nginx Helper | Limpeza de cache nginx |

### 10.4 Backup e Migração
| Plugin | Função |
|--------|--------|
| UpdraftPlus | Backup automático |
| WPVivid | Backup e restauração |
| All-in-One WP Migration | Migração do site |
| Migrate Guru | Migração assistida |

### 10.5 Utilidades
| Plugin | Função |
|--------|--------|
| Code Snippets | Trechos de código PHP customizados (armazenados no BD) |
| Better Search Replace | Substituição em massa no banco |
| Duplicate Page | Duplicar posts/páginas |
| Login As User | Login como outro usuário (admin tool) |
| WP CLI Login Server | Login via CLI |
| Download Plugin | Download de plugins como ZIP |
| Easy Theme/Plugin Upgrades | Upload facilitado |
| Maintenance | Modo manutenção |
| Cookie Law Info | Aviso de cookies (LGPD) |
| Instagram Feed Pro | Feed do Instagram |

### 10.6 WP Travel Engine — Addons Completos
| Addon | Função |
|-------|--------|
| wptravelengine-pro | Core PRO features |
| wp-travel-engine-advanced-itinerary-builder | Itinerário detalhado |
| wp-travel-engine-trip-fixed-starting-dates | Datas fixas de saída |
| wp-travel-engine-trip-fixed-starting-dates-countdown | Countdown para datas |
| wp-travel-engine-trip-reviews | Sistema de reviews |
| wp-travel-engine-trip-weather-forecast | Previsão do tempo |
| wp-travel-engine-trips-embedder | Embed de trips |
| wp-travel-engine-extra-services | Serviços extras |
| wp-travel-engine-group-discount | Desconto de grupo |
| wp-travel-engine-file-downloads | Downloads de arquivos |
| wp-travel-engine-form-editor | Editor de formulário de checkout |
| wp-travel-engine-itinerary-downloader | Download de itinerário |
| wp-travel-engine-legal-documents | Documentos legais |
| wp-travel-engine-social-proof | Prova social |
| wp-travel-engine-currency-converter | Conversor de moeda |
| wp-travel-engine-user-history | Histórico do usuário |
| wp-travel-engine-zapier | Integração Zapier |
| wp-travel-engine-affiliate-booking | Reservas de afiliados |
| wptravelengine-conditional-price | Preço condicional |
| wptravelengine-conditional-price-unlocked | Preço condicional desbloqueado |
| wte-date-pricing | Precificação por data |
| wp-travel-engine-authorize-net-payment-gateway | Gateway Authorize.net |
| wp-travel-engine-payfast-payment-gateway | Gateway PayFast |
| wp-travel-engine-hbl-payment-gateway | Gateway HBL |

---

## 11. BANCO DE DADOS — TABELAS RELEVANTES

### Tabelas WordPress padrão (prefixo: wp_cad131_)
- `wp_cad131_posts` — Todos os posts (trips, bookings, transfer_vehicle, etc.)
- `wp_cad131_postmeta` — Metadados dos posts
- `wp_cad131_options` — Configurações do sistema
- `wp_cad131_users` / `wp_cad131_usermeta` — Usuários

### Tabelas Customizadas
- `wp_cad131_wte_voucher_logs` — Log de vouchers gerados (order_id, reference, email, trip, file, email_sent, created_at)
- Tabelas do SliceWP (afiliados, comissões, visits, etc.)
- Tabelas do Uncanny Automator (receitas, triggers, ações, logs)
- Tabelas do WooCommerce (orders, order_items, sessions, etc.)

---

## 12. ESTRUTURA DE DADOS — BOOKING (RESERVA)

### Meta do Booking WTE:
```php
// Dados principais
'wp_travel_engine_booking_setting' => [
    'place_order' => [
        'booking' => [
            'fname' => 'Nome',
            'lname' => 'Sobrenome',
            'email' => 'email@exemplo.com',
            'phone' => '+5511999999999',
            'address' => 'Endereço',
            'city' => 'Cidade',
            'country' => 'BR'
        ],
        'trip_id' => 123,
        'trip_price' => 150.00,
        'trip_price_partial' => 75.00,
        'pax' => ['adulto' => 2, 'crianca' => 1]
    ]
]

// Dados de billing
'billing_info' => [
    'fname' => 'Nome',
    'lname' => 'Sobrenome',
    'email' => 'email@exemplo.com',
    'phone' => '+5511999999999'
]

// Trips no pedido
'order_trips' => [
    'trip_id' => [
        'ID' => 123,
        'title' => 'Passeio de Catamaran',
        'datetime' => '2024-03-15 14:00:00',
        'cost' => 150.00,
        'pax' => ['cat_id' => 2],
        '_cart_item_object' => [...dados completos do item...]
    ]
]

// Cart info
'cart_info' => [
    'total' => 225.00,
    'subtotal' => 225.00,
    'cart_partial' => 112.50,
    'transfer_total' => 75.00  // se tem transfers
]

// Status
'wp_travel_engine_booking_status' => 'booked',
'paid_amount' => 225.00,
'due_amount' => 0.00,

// Transfers associados
'_transfer_bookings' => [...array de dados dos transfers...],
'_has_transfers' => 'yes',
'_transfer_posts_created' => 'yes',
'_transfer_post_ids' => [456, 457],
'_transfer_wc_order_id' => 789,

// Vouchers
'_wte_multi_cart_vouchers' => [...dados dos vouchers gerados...],
'_wte_vouchers_sent' => timestamp
```

---

## 13. APIs E ENDPOINTS AJAX

### 13.1 Transfer System
| Action | Método | Descrição |
|--------|--------|-----------|
| `search_transfers` | POST | Busca veículos disponíveis para rota/data/passageiros |
| `add_transfer_to_cart` | POST | Adiciona transfer ao carrinho (sessão) |
| `lrv_add_transfers_to_cart` | POST | Adiciona transfers e retorna URL de redirect |
| `create_transfer_posts_manually` | POST | Cria posts de transfer (admin) |
| `view_transfer_voucher` | GET | Visualiza voucher HTML (admin) |
| `download_transfer_voucher` | GET | Download do voucher (admin) |
| `send_transfer_voucher` | POST | Envia voucher por email (admin) |
| `download_customer_voucher` | GET | Download do voucher (cliente logado) |

### 13.2 Day Pricing (LRV Enhancements)
| Action | Método | Descrição |
|--------|--------|-----------|
| `lrv_get_day_prices` | POST | Retorna preços ajustados para trip+data |
| `lrv_load_all_day_pricing` | POST | Carrega regras de preço (admin) |
| `lrv_save_all_day_pricing` | POST | Salva regras de preço (admin) |

### 13.3 Voucher System
| Action | Método | Descrição |
|--------|--------|-----------|
| `wte_download_voucher` | GET | Download seguro de voucher (cliente logado) |
| `generate_transfer_voucher` | POST | Gera voucher PDF de transfer |

### 13.4 WTE Core
| Action | Método | Descrição |
|--------|--------|-----------|
| `wp_travel_engine_new_booking_process_action` | POST | Processa nova reserva |
| `paypal_button_container` | POST | Renderiza botão PayPal |

---

## 14. SHORTCODES

| Shortcode | Descrição |
|-----------|-----------|
| `[wte_transfer_search]` | Formulário completo de busca de transfer |
| `[wte_transfer_results]` | Container para resultados de busca |
| `[wte_transfer_success]` | Página de sucesso pós-reserva de transfer |
| `[wp_travel_engine_cart]` | Página de carrinho do WTE |
| Elementor Widgets | Listagem de trips, filtros, busca, etc. |

---

## 15. PÁGINAS IMPORTANTES

| Página | Função |
|--------|--------|
| Home | Landing page principal |
| /checkout/ ou /wp-travel-engine-checkout/ | Checkout do WTE |
| /carrinho/ ou /wp-travel-engine-cart/ | Carrinho |
| /transfer-obrigado/ | Obrigado após reserva de transfer-only |
| /minha-conta/ | Dashboard do cliente (bookings, transfers, vouchers) |
| Thank you page (WTE) | Confirmação após pagamento com trips |

---

## 16. WEBHOOKS E INTEGRAÇÕES EXTERNAS

| Serviço | Tipo | Descrição |
|---------|------|-----------|
| WhatsApp API (LRV Web) | Webhook POST | Envia mensagens WhatsApp automáticas |
| HubSpot (via SliceWP) | CRM | Sincroniza dados de afiliados |
| Stripe Payouts (via SliceWP) | Pagamento | Paga comissões de afiliados |
| Zapier (via WTE addon) | Automação | Conecta com apps externos |
| QR Server API | GET | Gera QR codes para vouchers |
| PayPal API | REST | Processamento de pagamentos |
| Stripe API | REST | Processamento de pagamentos |

---

## 17. REGRAS DE NEGÓCIO IMPORTANTES

### 17.1 Preços
- Moeda: USD (dólar americano)
- Preços de trips: definidos por pacote e categoria de viajante
- Preços dinâmicos por data (dia da semana, feriado, data específica)
- Preços de transfer: por rota + faixa de passageiros + tipo de serviço
- Desconto de grupo: baseado em quantidade total de passageiros
- Pagamento parcial: configurável por trip (% de depósito)

### 17.2 Passageiros
- **Trips**:
  - Categorias customizáveis por pacote (Adulto, Criança, Bebê, etc.)
  - Cada categoria com faixa etária e preço próprio
- **Transfers**:
  - Adultos: +12 anos
  - Crianças: 2-11 anos
  - Bebês: 0-1 ano
  - Capacidade máxima por veículo (total e por tipo)

### 17.3 Proteções
- Preço calculado no SERVIDOR (evita manipulação frontend)
- Nonces em todos os AJAX
- Transient lock para evitar processamento duplicado de vouchers
- Verificação de permissão para download de vouchers
- .htaccess protege diretório de vouchers

### 17.4 Sessão
- Transfer cart armazenado em `$_SESSION['wte_transfer_cart']`
- Cart do WTE usa sistema próprio (classe WTE_Cart)
- Expiração: 7 dias
- Limpeza automática após checkout

---

## 18. FLUXO COMPLETO END-TO-END (RESUMO)

```
┌─────────────────────────────────────────────────────────────────┐
│                    FLUXO DE PASSEIO                              │
├─────────────────────────────────────────────────────────────────┤
│ 1. Browse passeios → 2. Selecionar data/hora/pax              │
│ 3. Preço dinâmico calculado → 4. Add to cart                   │
│ 5. Checkout → 6. Dados pessoais + pagamento                    │
│ 7. PayPal/Stripe processa → 8. Booking criado                  │
│ 9. Voucher HTML gerado → 10. Email com anexo                   │
│ 11. WhatsApp com detalhes → 12. Página de obrigado             │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    FLUXO DE TRANSFER                             │
├─────────────────────────────────────────────────────────────────┤
│ 1. Busca (origem/destino/data/pax/serviço)                     │
│ 2. Resultados com veículos e preços                            │
│ 3. Seleciona veículo → Add to cart ou Direct checkout          │
│ 4. Checkout → Dados pessoais + nomes dos passageiros           │
│ 5. Se transfer-only: processamento direto (sem gateway)         │
│    Se misto (trip+transfer): processamento WTE com gateway      │
│ 6. transfer_booking posts criados                               │
│ 7. WooCommerce order criada (se WC ativo)                      │
│ 8. Voucher HTML/PDF gerado → Email enviado                     │
│ 9. Página de sucesso com resumo                                │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│                    FLUXO DE AFILIADO                             │
├─────────────────────────────────────────────────────────────────┤
│ 1. Cadastro de afiliado → 2. Aprovação (manual/auto)           │
│ 3. Link de rastreamento gerado → 4. Cookie 30 dias             │
│ 5. Cliente compra via link → 6. Comissão calculada             │
│ 7. Admin aprova comissão → 8. Payout via Stripe                │
└─────────────────────────────────────────────────────────────────┘
```

---

## 19. CONSIDERAÇÕES PARA RECRIAÇÃO EM PHP

### 19.1 Módulos Necessários
1. **Sistema de Passeios**: CRUD de passeios, pacotes, categorias de viajante, datas fixas, itinerário
2. **Sistema de Preço Dinâmico**: regras por dia/feriado/data/mês com prioridade
3. **Sistema de Transfers**: veículos, locais, rotas, tarifas por faixa, busca
4. **Carrinho**: multi-item (trips + transfers), sessão, expiração
5. **Checkout**: formulário adaptativo, validação, suporte a transfer-only
6. **Pagamentos**: PayPal Express (SDK JS), Stripe (PaymentIntent), pagamento parcial
7. **Bookings**: criação, status, metadados, linkagem trip↔transfer
8. **Vouchers**: geração HTML, armazenamento, envio por email, download seguro
9. **Notificações**: email (SMTP), WhatsApp (webhook), templates customizáveis
10. **Área do Cliente**: dashboard, histórico, vouchers, transfers
11. **Admin**: gerenciamento de tudo, meta boxes, listagens, ações em massa
12. **Afiliados**: cadastro, links, cookies, comissões, payouts
13. **Multilíngue**: suporte a PT-BR + ES (ou outros)
14. **SEO**: meta tags, schema markup, sitemap

### 19.2 Integrações Externas Necessárias
- PayPal API (checkout + capture)
- Stripe API (PaymentIntent + webhook)
- WhatsApp Webhook API
- SMTP para emails
- QR Code API (opcional)
- HubSpot API (opcional, para afiliados)

### 19.3 Considerações Técnicas
- Calcular preços SEMPRE no servidor (nunca confiar no frontend)
- Usar transações no banco para evitar duplicatas
- Implementar lock/mutex para processamento de vouchers
- Proteger arquivos de voucher contra acesso direto
- Implementar rate limiting nos endpoints de busca
- Sessões PHP para carrinho (com fallback para banco/cookie)
- Responsive design (mobile-first para turistas)

---

*Documento gerado em: Julho 2026*
*Fonte: Análise completa do código-fonte do site WordPress puntacanaparabrasileiros.com*
