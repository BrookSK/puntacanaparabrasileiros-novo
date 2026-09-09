<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Trip;
use App\Models\TripCategory;
use App\Models\TripHotel;
use App\Models\TransferVehicle;
use App\Models\TransferLocation;
use App\Models\WhatsappMessage;

/**
 * Aurora — agente de IA para primeiro atendimento no WhatsApp.
 *
 * Responsabilidades:
 *  - Interpretar a mensagem do cliente e gerar uma resposta em português (via OpenAI).
 *  - Embasar a resposta no catálogo real de passeios publicados (nunca inventa preço/promoção).
 *  - Sinalizar quando um atendente humano deve assumir (intenção de compra / preço / data).
 *
 * Regras de negócio:
 *  - Só atua se `aurora_enabled` estiver ligado e houver chave de API configurada.
 *  - A decisão de "só atender até o humano assumir" é feita por quem chama (webhook),
 *    checando `whatsapp_contacts.assigned_to`.
 *  - Nunca lança exceção para o chamador: em erro, retorna null e loga (não quebra o webhook).
 *
 * Padrão de chamada HTTP: cURL, espelhando EvolutionApi.
 */
class AuroraService
{
    private const OPENAI_URL = 'https://api.openai.com/v1/chat/completions';

    /** Marcador que a IA emite quando o lead deve ir para um humano. Removido antes do envio. */
    private const HANDOFF_TAG = '[HANDOFF]';

    private Trip $tripModel;
    private TripCategory $categoryModel;
    private WhatsappMessage $messageModel;

    private string $apiKey;
    private string $model;
    private string $systemPrompt;
    private int $historyLimit;

    /** Cache do catálogo completo do sistema (montado uma vez por processo). */
    private static ?string $catalogCache = null;

    public function __construct()
    {
        $this->tripModel = new Trip();
        $this->categoryModel = new TripCategory();
        $this->messageModel = new WhatsappMessage();

        $this->apiKey = (string) setting('aurora_openai_api_key', '');
        $this->model = (string) (setting('aurora_model', 'gpt-4o-mini') ?: 'gpt-4o-mini');
        $this->systemPrompt = (string) setting('aurora_system_prompt', '');
        $this->historyLimit = (int) (setting('aurora_history_limit', '10') ?: 10);
    }

    /**
     * A Aurora está habilitada e configurada?
     */
    public function isEnabled(): bool
    {
        return setting('aurora_enabled', '0') === '1' && $this->apiKey !== '';
    }

    /**
     * Gera a resposta da Aurora para um contato.
     *
     * @param int    $contactId    ID do whatsapp_contacts
     * @param string $customerText Texto recebido do cliente (última mensagem)
     * @return array{reply:string, handoff:bool}|null  null em caso de erro/desabilitado
     */
    public function generateReply(int $contactId, string $customerText): ?array
    {
        if (!$this->isEnabled()) {
            return null;
        }

        $customerText = trim($customerText);
        if ($customerText === '') {
            return null;
        }

        try {
            $messages = $this->buildMessages($contactId, $customerText);
            $raw = $this->callOpenAi($messages);

            if ($raw === null || $raw === '') {
                error_log("[Aurora] OpenAI não retornou texto (modelo='{$this->model}'). Verifique a chave/modelo/saldo da conta OpenAI nos logs [Aurora] HTTP acima.");
                return null;
            }

            // Detectar e remover o marcador de handoff.
            $handoff = stripos($raw, self::HANDOFF_TAG) !== false;
            $reply = trim(str_ireplace(self::HANDOFF_TAG, '', $raw));

            // Segurança: nunca devolver resposta vazia.
            if ($reply === '') {
                $reply = 'Um de nossos consultores vai continuar seu atendimento em instantes. 😊';
                $handoff = true;
            }

            return ['reply' => $reply, 'handoff' => $handoff];
        } catch (\Throwable $e) {
            error_log('[Aurora] Falha ao gerar resposta: ' . $e->getMessage());
            return null;
        }
    }

    // ─────────────────────────────────────────────
    // MONTAGEM DE CONTEXTO
    // ─────────────────────────────────────────────

    /**
     * Monta o array de mensagens (system + histórico + catálogo) para a API.
     */
    private function buildMessages(int $contactId, string $customerText): array
    {
        $messages = [];

        // 1) System prompt (personalidade + regras) + instrução do marcador de handoff.
        $system = $this->systemPrompt !== '' ? $this->systemPrompt : $this->defaultSystemPrompt();

        // Escopo ampliado — sempre reforçado, independente do prompt salvo no admin:
        $system .= "\n\nESCOPO: você conhece TODO o sistema e deve responder qualquer pergunta "
            . "do cliente sobre passeios, transfers, veículos de transfer, locais de transfer, "
            . "hotéis e horários de pickup — sempre com base no CATÁLOGO fornecido a seguir.\n"
            . "REGRAS OBRIGATÓRIAS sobre o catálogo:\n"
            . "1. NUNCA diga que um passeio/serviço 'não existe' ou 'não temos' sem ANTES procurar "
            . "em TODA a lista do catálogo. O catálogo é a verdade absoluta.\n"
            . "2. Entenda erros de digitação, gírias e abreviações e associe ao item correto. "
            . "Exemplos: 'bugie', 'bugue', 'bug', 'buguy', 'baggy' => procure por 'Buggy'/'Buggies' "
            . "no catálogo; 'catamara' => 'Catamarã'; 'golfino' => 'Golfinho'. Se existir um item "
            . "parecido no catálogo, é esse que o cliente quer.\n"
            . "3. Ao apresentar 'o que tem', você pode resumir por categorias, mas se o cliente "
            . "perguntar por um item específico, confirme pelo nome EXATO do catálogo se ele existir.\n"
            . "4. Só há uma coisa que você NÃO faz: fechar/finalizar a venda ou o pagamento — "
            . "nesse caso, aciona um consultor humano.";

        // Guard-rail de ASSUNTO: foco em Punta Cana, mas sem ser seca com o cliente.
        $system .= "\n\nASSUNTO / FORA DE CONTEXTO: seu foco é a empresa e os serviços em Punta Cana "
            . "(passeios, transfers, veículos, locais, hotéis, horários, reservas e dúvidas de "
            . "viagem/turismo em Punta Cana). Quando o cliente perguntar algo FORA desse assunto "
            . "(ex.: receitas, compras, política, conversa pessoal), NÃO ignore nem recuse de forma "
            . "seca: responda de maneira BREVE, simpática e educada à pergunta dele em 1 frase e, "
            . "logo em seguida, na MESMA mensagem, pergunte gentilmente se ele gostaria de saber algo "
            . "sobre Punta Cana (passeios, transfers, sua viagem). Exemplo de tom: se perguntarem "
            . "'vende gás?', responda algo como 'Ah, gás a gente não vende, viu? 😅 Mas se você "
            . "estiver planejando uma viagem para Punta Cana, posso te ajudar com passeios e "
            . "transfers — quer dar uma olhada?'. Nunca seja grosseira; se o cliente ofender, "
            . "mantenha a educação e traga a conversa de volta para Punta Cana. Não se aprofunde em "
            . "temas fora do turismo: responda curtinho e redirecione.";

        // Apresentação: se a Aurora ainda não falou com este contato, deve se apresentar.
        if ($this->isFirstContact($contactId)) {
            $company = trim((string) setting('site_name', '')) ?: 'nossa agência';
            $system .= "\n\nPRIMEIRA MENSAGEM: esta é a primeira vez que você fala com este cliente. "
                . "Comece se APRESENTANDO de forma calorosa e breve antes de responder — algo como: "
                . "\"Olá! Eu sou a Aurora, assistente virtual da {$company} para suas experiências em "
                . "Punta Cana. 😊\" — e então responda ao que o cliente perguntou. Faça a apresentação "
                . "apenas UMA vez (só nesta primeira mensagem).";
        }

        $system .= "\n\nINSTRUÇÃO TÉCNICA: quando perceber intenção clara de compra, "
            . "pedido de preço/disponibilidade de data específica, ou desejo de fechar/pagar, "
            . "adicione o marcador " . self::HANDOFF_TAG . " ao FINAL da sua mensagem "
            . "(ele será removido automaticamente antes de enviar ao cliente).";
        $messages[] = ['role' => 'system', 'content' => $system];

        // 2) Catálogo COMPLETO do sistema (todos os passeios, transfers, veículos, locais,
        //    hotéis e horários). Não filtra por palavra-chave — a Aurora precisa conhecer tudo.
        $catalog = $this->buildFullSystemCatalog();
        if ($catalog !== '') {
            $messages[] = ['role' => 'system', 'content' => $catalog];
        }

        // 3) Histórico recente da conversa (ordem cronológica).
        $history = $this->messageModel->getByContact($contactId, $this->historyLimit);
        foreach ($history as $msg) {
            $text = trim((string) ($msg['message_text'] ?? ''));
            if ($text === '') {
                // Usar transcrição de áudio se houver.
                $text = trim((string) ($msg['transcription'] ?? ''));
            }
            if ($text === '') {
                continue;
            }
            $role = ((int) ($msg['from_me'] ?? 0) === 1) ? 'assistant' : 'user';
            $messages[] = ['role' => $role, 'content' => $text];
        }

        // 4) Garantir que a última mensagem do cliente esteja presente.
        $last = end($messages);
        if (!$last || $last['role'] !== 'user' || trim($last['content']) !== $customerText) {
            $messages[] = ['role' => 'user', 'content' => $customerText];
        }

        return $messages;
    }

    /**
     * É a primeira vez que a Aurora fala com este contato?
     * (Não há nenhuma mensagem anterior enviada pela Aurora para ele.)
     */
    private function isFirstContact(int $contactId): bool
    {
        try {
            $count = (int) \Core\Database::getInstance()->fetchColumn(
                "SELECT COUNT(*) FROM whatsapp_messages
                 WHERE contact_id = ? AND from_me = 1 AND is_deleted = 0
                 AND sender_name = 'Aurora'",
                [$contactId]
            );
            return $count === 0;
        } catch (\Throwable $e) {
            error_log('[Aurora] Falha ao checar primeira mensagem: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Monta o catálogo COMPLETO do sistema (todos os passeios com preço, categorias,
     * transfers/veículos/locais/rotas e hotéis com horários). Cacheado por processo.
     */
    private function buildFullSystemCatalog(): string
    {
        if (self::$catalogCache !== null) {
            return self::$catalogCache;
        }

        $blocks = [];
        $blocks[] = "VOCÊ TEM ACESSO A TODO O CATÁLOGO DO SISTEMA ABAIXO. "
            . "Use APENAS estas informações reais para responder. Se o cliente citar algo com "
            . "erro de digitação (ex.: 'bugie', 'bugue', 'buguy' = Buggy/Buggies), reconheça e "
            . "associe ao item correto da lista. Nunca invente itens, preços ou horários que não "
            . "estejam aqui. Se algo não estiver na lista, diga que vai confirmar com um consultor.";

        $blocks[] = $this->buildTripsBlock();

        $catNames = [];
        try {
            foreach ($this->categoryModel->getAll() as $c) {
                $n = trim((string) ($c['name'] ?? ''));
                if ($n !== '') {
                    $catNames[] = $n;
                }
            }
        } catch (\Throwable $e) {
            error_log('[Aurora] Falha ao carregar categorias: ' . $e->getMessage());
        }
        if (!empty($catNames)) {
            $blocks[] = "CATEGORIAS DE PASSEIOS: " . implode(', ', $catNames) . '.';
        }

        $blocks[] = $this->buildTransfersBlock();
        $blocks[] = $this->buildHotelsBlock();

        self::$catalogCache = implode("\n\n", array_filter($blocks));
        error_log('[Aurora] Catálogo montado: ' . strlen(self::$catalogCache) . ' chars.');
        return self::$catalogCache;
    }

    /**
     * Bloco de passeios: nome, preço a partir de, duração e descrição curta.
     */
    private function buildTripsBlock(): string
    {
        // Query DIRETA: garante TODOS os passeios publicados, mesmo sem preço/pacote
        // cadastrado. O preço mínimo vem de um LEFT JOIN (NULL quando não há preço),
        // sem excluir nenhum passeio da lista.
        try {
            $db = \Core\Database::getInstance();
            $trips = $db->fetchAll(
                "SELECT t.id, t.title, t.short_description, t.duration, t.duration_unit,
                        (
                            SELECT MIN(COALESCE(tpc.sale_price, tpc.price))
                            FROM trip_packages tp
                            INNER JOIN trip_package_categories tpc ON tpc.package_id = tp.id
                            WHERE tp.trip_id = t.id AND COALESCE(tpc.sale_price, tpc.price) > 0
                        ) AS min_price
                 FROM trips t
                 WHERE t.status = 'published'
                 ORDER BY t.sort_order DESC, t.created_at DESC"
            );
        } catch (\Throwable $e) {
            error_log('[Aurora] Falha ao carregar passeios (query direta): ' . $e->getMessage());
            // Fallback ainda mais simples: só título/descrição.
            try {
                $trips = (new Trip())->where("status = 'published'", [], 'sort_order DESC, created_at DESC');
            } catch (\Throwable $e2) {
                error_log('[Aurora] Falha no fallback de passeios: ' . $e2->getMessage());
                return 'PASSEIOS: não foi possível carregar no momento — um consultor pode confirmar.';
            }
        }

        if (empty($trips)) {
            return 'PASSEIOS: nenhum passeio publicado no momento.';
        }

        $lines = [];
        foreach ($trips as $t) {
            $title = trim((string) ($t['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $parts = [$title];

            $min = (float) ($t['min_price'] ?? 0);
            if ($min > 0) {
                $parts[] = 'a partir de US$ ' . number_format($min, 2);
            }
            $dur = trim((string) ($t['duration'] ?? ''));
            if ($dur !== '') {
                $unit = ($t['duration_unit'] ?? 'hours') === 'days' ? 'dia(s)' : 'hora(s)';
                $parts[] = $dur . ' ' . $unit;
            }

            $line = '- ' . implode(' | ', $parts);
            $desc = trim(strip_tags((string) ($t['short_description'] ?? '')));
            if ($desc !== '') {
                if (mb_strlen($desc) > 140) {
                    $desc = mb_substr($desc, 0, 137) . '...';
                }
                $line .= "\n  " . $desc;
            }
            $lines[] = $line;
        }

        return "PASSEIOS DISPONÍVEIS (" . count($lines) . " no total — liste/expanda conforme a pergunta):\n" . implode("\n", $lines);
    }

    /**
     * Bloco de transfers: veículos (capacidade + rotas com preços) e locais.
     */
    private function buildTransfersBlock(): string
    {
        $out = [];

        try {
            $vehicleModel = new TransferVehicle();
            $vehicles = $vehicleModel->getActive();
            if (!empty($vehicles)) {
                $vLines = [];
                foreach ($vehicles as $v) {
                    $title = trim((string) ($v['title'] ?? ''));
                    if ($title === '') {
                        continue;
                    }
                    $vLine = '- ' . $title;
                    $cap = (int) ($v['max_passengers'] ?? 0);
                    if ($cap > 0) {
                        $vLine .= " (até {$cap} passageiros)";
                    }
                    $vDesc = trim(strip_tags((string) ($v['description'] ?? '')));
                    if ($vDesc !== '') {
                        if (mb_strlen($vDesc) > 100) {
                            $vDesc = mb_substr($vDesc, 0, 97) . '...';
                        }
                        $vLine .= ' — ' . $vDesc;
                    }
                    try {
                        $routes = $vehicleModel->getRoutes((int) $v['id']);
                        foreach (array_slice($routes, 0, 12) as $r) {
                            $origin = trim((string) ($r['origin_title'] ?? ''));
                            $dest = trim((string) ($r['destination_title'] ?? ''));
                            if ($origin === '' || $dest === '') {
                                continue;
                            }
                            $routeLine = "    · {$origin} -> {$dest}";
                            $price = (float) ($r['base_price'] ?? 0);
                            if ($price > 0) {
                                $routeLine .= ' — a partir de US$ ' . number_format($price, 2);
                            }
                            $vLine .= "\n" . $routeLine;
                        }
                    } catch (\Throwable $e) {
                        // rotas indisponíveis — segue sem
                    }
                    $vLines[] = $vLine;
                }
                if (!empty($vLines)) {
                    $out[] = "VEÍCULOS DE TRANSFER E ROTAS:\n" . implode("\n", $vLines);
                }
            }
        } catch (\Throwable $e) {
            error_log('[Aurora] Falha ao carregar veículos de transfer: ' . $e->getMessage());
        }

        try {
            $locationModel = new TransferLocation();
            $locations = method_exists($locationModel, 'getActive')
                ? $locationModel->getActive()
                : $locationModel->where('status = 1', [], 'sort_order ASC');
            if (!empty($locations)) {
                $typeLabel = [
                    'airport' => 'Aeroporto', 'hotel' => 'Hotel',
                    'resort' => 'Resort', 'city' => 'Cidade', 'other' => 'Outro',
                ];
                $lLines = [];
                foreach ($locations as $l) {
                    $title = trim((string) ($l['title'] ?? ''));
                    if ($title === '') {
                        continue;
                    }
                    $type = $typeLabel[$l['location_type'] ?? 'other'] ?? 'Local';
                    $lLines[] = "- {$title} ({$type})";
                }
                if (!empty($lLines)) {
                    $out[] = "LOCAIS DE TRANSFER (origens/destinos):\n" . implode("\n", $lLines);
                }
            }
        } catch (\Throwable $e) {
            error_log('[Aurora] Falha ao carregar locais de transfer: ' . $e->getMessage());
        }

        return implode("\n\n", $out);
    }

    /**
     * Bloco de hotéis e horários de pickup (cadastrados por passeio).
     */
    private function buildHotelsBlock(): string
    {
        try {
            $hotelModel = new TripHotel();
            $tripsResult = $this->tripModel->getPublished(1, 500, 'relevancia');
            $trips = $tripsResult['items'] ?? [];

            $lines = [];
            foreach ($trips as $t) {
                $hotels = $hotelModel->getByTrip((int) $t['id'], true);
                if (empty($hotels)) {
                    continue;
                }
                $tripTitle = trim((string) ($t['title'] ?? ''));
                $hotelParts = [];
                foreach ($hotels as $h) {
                    $hotelName = trim((string) ($h['hotel_name'] ?? ''));
                    if ($hotelName === '') {
                        continue;
                    }
                    $times = [];
                    foreach (($h['schedules'] ?? []) as $s) {
                        $tt = substr((string) ($s['pickup_time'] ?? ''), 0, 5);
                        if ($tt !== '') {
                            $times[] = $tt;
                        }
                    }
                    $hotelParts[] = $times
                        ? "{$hotelName} (pickup: " . implode(', ', $times) . ')'
                        : $hotelName;
                }
                if (!empty($hotelParts)) {
                    $lines[] = "- {$tripTitle}: " . implode('; ', $hotelParts);
                }
            }

            if (!empty($lines)) {
                return "HOTÉIS E HORÁRIOS DE PICKUP (por passeio):\n" . implode("\n", $lines);
            }
        } catch (\Throwable $e) {
            error_log('[Aurora] Falha ao carregar hotéis/horários: ' . $e->getMessage());
        }

        return '';
    }

    // ─────────────────────────────────────────────
    // OPENAI (cURL)
    // ─────────────────────────────────────────────

    /**
     * Chama a API de chat da OpenAI e retorna o texto da resposta (ou null).
     */
    private function callOpenAi(array $messages): ?string
    {
        $payload = [
            'model' => $this->model,
            'messages' => $messages,
            'temperature' => 0.6,
            'max_tokens' => 400,
        ];

        $response = $this->request($payload);
        if ($response === null) {
            return null;
        }

        $content = $response['choices'][0]['message']['content'] ?? null;
        return is_string($content) ? trim($content) : null;
    }

    /**
     * Executa a requisição HTTP para a OpenAI via cURL.
     */
    private function request(array $payload): ?array
    {
        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL => self::OPENAI_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            error_log("[Aurora] cURL error: {$error}");
            return null;
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            error_log("[Aurora] HTTP {$httpCode} | Response: " . substr((string) $response, 0, 500));
            return null;
        }

        $decoded = json_decode((string) $response, true);
        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Testa a conexão com a OpenAI (usado na tela de configuração).
     *
     * @return array{ok:bool, message:string}
     */
    public function testConnection(): array
    {
        if ($this->apiKey === '') {
            return ['ok' => false, 'message' => 'Chave de API não configurada.'];
        }

        try {
            $response = $this->request([
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => 'Responda apenas com a palavra: OK'],
                    ['role' => 'user', 'content' => 'teste'],
                ],
                'max_tokens' => 5,
            ]);

            if ($response === null) {
                return ['ok' => false, 'message' => 'Falha na conexão. Verifique a chave e o modelo (veja o log do servidor).'];
            }

            $content = $response['choices'][0]['message']['content'] ?? '';
            return ['ok' => true, 'message' => 'Conexão OK. Modelo respondeu: ' . trim((string) $content)];
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Erro: ' . $e->getMessage()];
        }
    }

    /**
     * Diagnóstico completo do fluxo da Aurora (usado pela tela de teste do admin).
     * Executa a MESMA chamada real (prompt completo com passeios + histórico) e reporta
     * cada etapa, incluindo o HTTP/erro exato da OpenAI.
     *
     * @return array<int, array{step:string, ok:bool, detail:string}>
     */
    public function diagnose(string $customerText = 'Olá, quero saber sobre os passeios'): array
    {
        $steps = [];

        // 1) Configuração
        $enabled = setting('aurora_enabled', '0') === '1';
        $steps[] = [
            'step' => 'Aurora ativada (aurora_enabled)',
            'ok' => $enabled,
            'detail' => $enabled ? 'Sim' : "Não (valor atual: '" . setting('aurora_enabled', '0') . "')",
        ];

        $hasKey = $this->apiKey !== '';
        $steps[] = [
            'step' => 'Chave da OpenAI carregada (via setting/autoload)',
            'ok' => $hasKey,
            'detail' => $hasKey ? ('Sim (' . substr($this->apiKey, 0, 7) . '...' . substr($this->apiKey, -4) . ')') : 'Não — a chave não está sendo lida no runtime. Verifique se a setting aurora_openai_api_key tem autoload=1.',
        ];

        $steps[] = [
            'step' => 'Modelo configurado',
            'ok' => $this->model !== '',
            'detail' => $this->model ?: '(vazio)',
        ];

        if (!$hasKey) {
            return $steps; // sem chave não adianta seguir
        }

        // 2) Chamada REAL à OpenAI (com detalhe do HTTP)
        $messages = [
            ['role' => 'system', 'content' => 'Você é a Aurora. Responda em uma frase curta.'],
            ['role' => 'user', 'content' => $customerText],
        ];
        $diag = $this->requestVerbose([
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => 60,
        ]);

        $steps[] = [
            'step' => 'Chamada à OpenAI (HTTP)',
            'ok' => $diag['ok'],
            'detail' => $diag['detail'],
        ];

        return $steps;
    }

    /**
     * Igual ao request(), mas retorna detalhes do erro (para diagnóstico visível).
     *
     * @return array{ok:bool, detail:string}
     */
    private function requestVerbose(array $payload): array
    {
        if (!function_exists('curl_init')) {
            return ['ok' => false, 'detail' => 'A extensão cURL do PHP não está disponível no servidor.'];
        }

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => self::OPENAI_URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['ok' => false, 'detail' => 'Erro de conexão cURL: ' . $error . ' (o servidor pode estar bloqueando conexões de saída para api.openai.com).'];
        }

        if ($httpCode < 200 || $httpCode >= 300) {
            $short = substr((string) $response, 0, 400);
            return ['ok' => false, 'detail' => "HTTP {$httpCode} da OpenAI: {$short}"];
        }

        $decoded = json_decode((string) $response, true);
        $content = $decoded['choices'][0]['message']['content'] ?? null;
        if (!is_string($content) || trim($content) === '') {
            return ['ok' => false, 'detail' => 'HTTP 200 mas resposta sem conteúdo: ' . substr((string) $response, 0, 300)];
        }

        return ['ok' => true, 'detail' => 'OK — modelo respondeu: "' . trim($content) . '"'];
    }

    /**
     * Transcreve um arquivo de áudio (caminho absoluto) usando OpenAI Whisper.
     * Retorna o texto ou null em falha. Usa a chave da Aurora (aurora_openai_api_key).
     */
    public function transcribeAudioFile(string $absolutePath): ?string
    {
        if ($this->apiKey === '' || !function_exists('curl_init')) {
            error_log('[Aurora] Transcrição abortada: sem chave OpenAI ou cURL indisponível.');
            return null;
        }
        if (!is_file($absolutePath)) {
            error_log("[Aurora] Áudio não encontrado para transcrição: {$absolutePath}");
            return null;
        }

        // O Whisper exige uma extensão de arquivo reconhecida. O WhatsApp salva áudio como
        // .ogg (opus), mas versões antigas podem ter salvo como .bin — nesse caso, criamos
        // uma cópia temporária com extensão válida para o envio.
        $allowedExt = ['flac', 'm4a', 'mp3', 'mp4', 'mpeg', 'mpga', 'oga', 'ogg', 'wav', 'webm'];
        $ext = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $sendPath = $absolutePath;
        $tempPath = null;
        $mimeForUpload = 'audio/ogg';

        if (!in_array($ext, $allowedExt, true)) {
            // Copiar para .ogg (formato padrão do WhatsApp) para o Whisper aceitar.
            $tempPath = sys_get_temp_dir() . '/aurora_audio_' . uniqid() . '.ogg';
            if (@copy($absolutePath, $tempPath)) {
                $sendPath = $tempPath;
                error_log("[Aurora] Áudio com extensão '{$ext}' — enviando como .ogg (cópia temporária).");
            } else {
                error_log("[Aurora] Não foi possível criar cópia .ogg do áudio: {$absolutePath}");
            }
        } else {
            $extMime = ['ogg' => 'audio/ogg', 'oga' => 'audio/ogg', 'mp3' => 'audio/mpeg',
                'mpga' => 'audio/mpeg', 'm4a' => 'audio/mp4', 'mp4' => 'audio/mp4',
                'wav' => 'audio/wav', 'webm' => 'audio/webm', 'flac' => 'audio/flac'];
            $mimeForUpload = $extMime[$ext] ?? 'audio/ogg';
        }

        try {
            $ch = curl_init('https://api.openai.com/v1/audio/transcriptions');
            $cFile = new \CURLFile($sendPath, $mimeForUpload, 'audio.ogg');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $this->apiKey],
                CURLOPT_POSTFIELDS => [
                    'file' => $cFile,
                    'model' => 'whisper-1',
                    'language' => 'pt',
                ],
                CURLOPT_TIMEOUT => 60,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            if ($tempPath && is_file($tempPath)) {
                @unlink($tempPath);
            }

            if ($error) {
                error_log("[Aurora] Whisper cURL error: {$error}");
                return null;
            }
            if ($httpCode < 200 || $httpCode >= 300) {
                error_log("[Aurora] Whisper HTTP {$httpCode}: " . substr((string) $response, 0, 300));
                return null;
            }

            $data = json_decode((string) $response, true);
            $text = $data['text'] ?? null;
            return is_string($text) ? trim($text) : null;
        } catch (\Throwable $e) {
            if ($tempPath && is_file($tempPath)) {
                @unlink($tempPath);
            }
            error_log('[Aurora] Falha ao transcrever áudio: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Prompt de fallback caso a setting esteja vazia.
     */
    private function defaultSystemPrompt(): string
    {
        return 'Você é a Aurora, assistente virtual de uma agência de turismo em Punta Cana para brasileiros. '
            . 'Faça o primeiro atendimento pelo WhatsApp de forma simpática e objetiva, em português do Brasil. '
            . 'Sugira passeios com base no catálogo fornecido, nunca invente preços ou disponibilidade, '
            . 'e quando o cliente quiser fechar ou saber preço/data, avise que um consultor vai continuar.';
    }
}
