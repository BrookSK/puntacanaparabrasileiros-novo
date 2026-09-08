<?php
declare(strict_types=1);

namespace App\Services;

use App\Models\Trip;
use App\Models\TripCategory;
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
        $system .= "\n\nINSTRUÇÃO TÉCNICA: quando perceber intenção clara de compra, "
            . "pedido de preço/disponibilidade de data específica, ou desejo de fechar/pagar, "
            . "adicione o marcador " . self::HANDOFF_TAG . " ao FINAL da sua mensagem "
            . "(ele será removido automaticamente antes de enviar ao cliente).";
        $messages[] = ['role' => 'system', 'content' => $system];

        // 2) Catálogo de passeios como contexto (baseado no interesse + lista geral).
        $catalog = $this->buildCatalogContext($customerText);
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
     * Monta um bloco de contexto com passeios relevantes ao que o cliente escreveu.
     */
    private function buildCatalogContext(string $customerText): string
    {
        $trips = [];

        // Busca por palavras-chave do cliente (usa o texto inteiro).
        $found = $this->tripModel->search($customerText, 1, 8);
        if (!empty($found['items'])) {
            $trips = $found['items'];
        }

        // Fallback / complemento: passeios em destaque/recentes.
        if (count($trips) < 6) {
            $general = $this->tripModel->getPublished(1, 8, 'relevancia');
            foreach ($general['items'] ?? [] as $t) {
                $trips[$t['id']] = $t; // dedup por id
            }
            // Reindexar
            $trips = array_values($trips);
        } else {
            // Normalizar chave
            $indexed = [];
            foreach ($trips as $t) {
                $indexed[$t['id']] = $t;
            }
            $trips = array_values($indexed);
        }

        if (empty($trips)) {
            return 'CATÁLOGO: nenhum passeio publicado encontrado no momento. '
                . 'Peça mais detalhes ao cliente e informe que um consultor trará as opções.';
        }

        $lines = [];
        foreach (array_slice($trips, 0, 10) as $t) {
            $title = trim((string) ($t['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $desc = trim(strip_tags((string) ($t['short_description'] ?? '')));
            if (mb_strlen($desc) > 160) {
                $desc = mb_substr($desc, 0, 157) . '...';
            }
            $lines[] = $desc !== '' ? "- {$title}: {$desc}" : "- {$title}";
        }

        // Categorias disponíveis (ajuda a Aurora a orientar).
        $catNames = [];
        foreach ($this->categoryModel->getAll() as $c) {
            $n = trim((string) ($c['name'] ?? ''));
            if ($n !== '') {
                $catNames[] = $n;
            }
        }

        $context = "CATÁLOGO DE PASSEIOS DISPONÍVEIS (use APENAS estes nomes reais; "
            . "não invente preços nem disponibilidade):\n" . implode("\n", $lines);

        if (!empty($catNames)) {
            $context .= "\n\nCATEGORIAS: " . implode(', ', array_slice($catNames, 0, 20)) . '.';
        }

        return $context;
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
