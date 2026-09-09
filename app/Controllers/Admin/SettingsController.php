<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Setting;
use App\Services\EmailService;
use App\Services\GoogleMeetService;

class SettingsController extends Controller
{
    private Setting $settingModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
        $this->settingModel = new Setting();
    }

    public function index(Request $request, Response $response): void
    {
        // Apenas superadmin pode acessar settings
        $user = $this->currentUser();
        if ($user['role'] !== 'superadmin') {
            $this->flash('error', 'Apenas o superadmin pode acessar configurações.');
            $this->redirect('/admin');
            return;
        }

        $settings = $this->settingModel->getGrouped();

        // Boards do CRM (para o seletor da Aurora).
        $crmBoards = (new \App\Models\CrmBoard())->listWithColumns();

        // Relatório de diagnóstico da Aurora (exibido uma vez, após clicar em "Diagnosticar").
        $auroraDiagnose = $this->session->get('aurora_diagnose_report', '');
        if ($auroraDiagnose !== '') {
            $this->session->remove('aurora_diagnose_report');
        }

        $this->view('admin/settings/index', [
            'settings' => $settings,
            'crmBoards' => $crmBoards,
            'auroraDiagnose' => $auroraDiagnose,
            'pageTitle' => 'Configurações do Sistema',
        ], 'admin');
    }

    public function update(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        if ($user['role'] !== 'superadmin') {
            $this->flash('error', 'Acesso negado.');
            $this->redirect('/admin');
            return;
        }

        $data = $request->post();
        unset($data['_token']); // Remover CSRF token

        // Processar uploads de arquivo (logo, favicon, voucher_logo)
        $fileFields = ['site_logo', 'site_favicon', 'voucher_logo'];
        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $uploaded = $this->uploadSettingsFile($request->file($field), $field);
                if ($uploaded) {
                    $data[$field] = $uploaded;
                }
            } else {
                // Manter valor anterior se não veio novo upload
                unset($data[$field]);
            }
        }

        // Processar checkboxes (booleans) — se não vem no POST, é "0"
        $booleanFields = [
            'paypal_enabled', 'stripe_enabled', 'pagbank_enabled', 'partial_payment_enabled',
            'whatsapp_enabled', 'affiliate_enabled', 'affiliate_auto_approve',
            'checkout_online_enabled', 'checkout_whatsapp_enabled',
            'videocall_enabled', 'google_meet_enabled',
            'aurora_enabled',
        ];
        foreach ($booleanFields as $field) {
            $data[$field] = isset($data[$field]) ? '1' : '0';
        }

        // Campos que chegam como array (checkbox múltiplo) → salvar como CSV
        if (array_key_exists('videocall_days', $data) && is_array($data['videocall_days'])) {
            $days = array_values(array_filter($data['videocall_days'], static fn($d) => $d !== '' && $d !== null));
            $data['videocall_days'] = implode(',', $days);
        }

        // Mapeamento de campo → grupo para garantir que novos campos sejam criados no grupo correto
        $fieldGroupMap = [
            'paypal_enabled' => 'payments', 'paypal_client_id' => 'payments', 'paypal_secret' => 'payments', 'paypal_mode' => 'payments',
            'stripe_enabled' => 'payments', 'stripe_publishable_key' => 'payments', 'stripe_secret_key' => 'payments',
            'pagbank_enabled' => 'payments', 'pagbank_token' => 'payments', 'pagbank_mode' => 'payments', 'pagbank_usd_brl_rate' => 'payments',
            'partial_payment_enabled' => 'payments', 'partial_payment_percent' => 'payments',
            'checkout_online_enabled' => 'payments', 'checkout_whatsapp_enabled' => 'payments',
            'whatsapp_enabled' => 'whatsapp', 'admin_whatsapp_numbers' => 'whatsapp',
            'affiliate_enabled' => 'affiliates', 'affiliate_auto_approve' => 'affiliates',
            'videocall_enabled' => 'videocall', 'videocall_days' => 'videocall',
            'videocall_hour_start' => 'videocall', 'videocall_hour_end' => 'videocall',
            'videocall_duration' => 'videocall', 'videocall_reminder_token' => 'videocall',
            'google_meet_enabled' => 'videocall', 'google_meet_client_id' => 'videocall',
            'google_meet_client_secret' => 'videocall', 'google_meet_refresh_token' => 'videocall',
            'google_meet_calendar_id' => 'videocall', 'google_meet_timezone' => 'videocall',
            'aurora_enabled' => 'aurora', 'aurora_openai_api_key' => 'aurora',
            'aurora_model' => 'aurora', 'aurora_system_prompt' => 'aurora',
            'aurora_crm_board_id' => 'aurora', 'aurora_max_replies' => 'aurora',
            'aurora_history_limit' => 'aurora',
        ];

        // Salvar no banco
        foreach ($data as $key => $value) {
            $group = $fieldGroupMap[$key] ?? null;
            $this->settingModel->setWithGroup($key, $value, $group);
        }

        // Recarregar settings na App
        $this->app->reloadSettings();

        $this->flash('success', 'Configurações salvas com sucesso!');
        $this->redirect('/admin/configuracoes');
    }

    public function testEmail(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        $testTo = $request->input('test_email', $user['email'] ?? '');

        if (!filter_var($testTo, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Email inválido para teste.');
            $this->redirect('/admin/configuracoes');
            return;
        }

        // Recarregar settings para pegar os valores atualizados
        $this->app->reloadSettings();

        $emailService = new EmailService();

        try {
            $result = $emailService->sendTestEmail($testTo);
            if ($result) {
                $this->flash('success', 'Email de teste enviado com sucesso para ' . $testTo . '!');
            } else {
                // Buscar último erro no log
                $lastLog = $this->db->fetchOne("SELECT * FROM email_log WHERE status = 'failed' ORDER BY id DESC LIMIT 1");
                $errorDetail = $lastLog['error_message'] ?? 'Erro desconhecido';
                $this->flash('error', 'Falha ao enviar email. Erro: ' . $errorDetail);
            }
        } catch (\Throwable $e) {
            $this->flash('error', 'Erro SMTP: ' . $e->getMessage());
        }

        $this->redirect('/admin/configuracoes');
    }

    /**
     * Testa a conexão da Aurora com a OpenAI usando as configurações salvas.
     * POST /admin/aurora/test
     */
    public function testAurora(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        if (($user['role'] ?? '') !== 'superadmin') {
            $this->flash('error', 'Acesso negado.');
            $this->redirect('/admin/configuracoes');
            return;
        }

        // Garante que a leitura via setting() use os valores mais recentes.
        $this->app->reloadSettings();

        $aurora = new \App\Services\AuroraService();
        $result = $aurora->testConnection();

        if ($result['ok']) {
            $this->flash('success', 'Aurora — ' . $result['message']);
        } else {
            $this->flash('error', 'Aurora — ' . $result['message']);
        }

        $this->redirect('/admin/configuracoes');
    }

    /**
     * Diagnóstico completo do fluxo da Aurora (mostra cada etapa na tela).
     * POST /admin/aurora/diagnose
     */
    public function diagnoseAurora(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        if (($user['role'] ?? '') !== 'superadmin') {
            $this->flash('error', 'Acesso negado.');
            $this->redirect('/admin/configuracoes');
            return;
        }

        $this->app->reloadSettings();

        $aurora = new \App\Services\AuroraService();
        $steps = $aurora->diagnose();

        $lines = [];
        foreach ($steps as $s) {
            $mark = $s['ok'] ? '[OK]' : '[FALHOU]';
            $lines[] = "{$mark} {$s['step']}: {$s['detail']}";
        }

        // Teste de transcrição do último áudio recebido.
        $audio = $aurora->diagnoseLastAudio();
        $lines[] = ($audio['ok'] ? '[OK]' : '[FALHOU]') . ' Transcrição de áudio: ' . $audio['detail'];

        $report = implode("\n", $lines);

        // Passa o relatório para a view exibir num bloco legível.
        $this->session->set('aurora_diagnose_report', $report);
        $this->redirect('/admin/configuracoes');
    }

    /**
     * Diagnóstico de ÁUDIO acessível pelo navegador (texto puro).
     * GET /admin/aurora/diagnostico-audio  — só superadmin.
     */
    public function diagnoseAudioWeb(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        if (($user['role'] ?? '') !== 'superadmin') {
            http_response_code(403);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'Acesso negado.';
            exit;
        }

        $this->app->reloadSettings();

        header('Content-Type: text/plain; charset=utf-8');
        $out = [];
        $out[] = '=== DIAGNÓSTICO DE ÁUDIO DA AURORA ===';

        // 1) Config básica
        $enabled = setting('aurora_enabled', '0') === '1';
        $key = trim((string) setting('aurora_openai_api_key', ''));
        $out[] = 'Aurora ativada: ' . ($enabled ? 'sim' : 'NÃO');
        $out[] = 'Chave OpenAI: ' . ($key !== '' ? ('preenchida (' . substr($key, 0, 7) . '...)') : 'VAZIA');

        // 2) Último áudio + transcrição
        try {
            $aurora = new \App\Services\AuroraService();
            $audio = $aurora->diagnoseLastAudio();
            $out[] = '';
            $out[] = 'TESTE DE TRANSCRIÇÃO (último áudio recebido):';
            $out[] = ($audio['ok'] ? '[OK] ' : '[FALHOU] ') . $audio['detail'];
        } catch (\Throwable $e) {
            $out[] = 'Erro no teste de transcrição: ' . $e->getMessage();
        }

        // 3) Estado do webhook da instância na Evolution (base64?)
        try {
            $instance = $this->db->fetchOne(
                "SELECT * FROM whatsapp_instances WHERE connection_status = 'open' ORDER BY is_default DESC, id ASC LIMIT 1"
            );
            if ($instance) {
                $out[] = '';
                $out[] = 'INSTÂNCIA: ' . ($instance['instance_name'] ?? '?') . ' (status: ' . ($instance['connection_status'] ?? '?') . ')';
                $api = \App\Services\EvolutionApi::fromInstance($instance);
                if (method_exists($api, 'findWebhook')) {
                    $wh = $api->findWebhook();
                    $out[] = 'Webhook atual: ' . json_encode($wh, JSON_UNESCAPED_SLASHES);

                    // Se o base64 estiver desativado, re-registra o webhook com base64=true.
                    $base64On = false;
                    if (is_array($wh)) {
                        $flat = json_encode($wh);
                        $base64On = str_contains((string) $flat, '"base64":true');
                    }
                    if (!$base64On) {
                        $webhookUrl = rtrim((string) setting('site_url', ''), '/') . '/whatsapp/webhook';
                        $set = $api->setWebhook($webhookUrl);
                        $out[] = 'Base64 estava DESATIVADO. Reaplicado webhook com base64=true (url: ' . $webhookUrl . '). Resultado: ' . json_encode($set, JSON_UNESCAPED_SLASHES);
                        $out[] = '>>> Agora peça um NOVO áudio e teste de novo.';
                    } else {
                        $out[] = 'Base64 do webhook: ATIVO (ok).';
                    }
                }
            } else {
                $out[] = 'Nenhuma instância conectada encontrada.';
            }
        } catch (\Throwable $e) {
            $out[] = 'Erro ao checar webhook: ' . $e->getMessage();
        }

        echo implode("\n", $out);
        exit;
    }

    private function uploadSettingsFile(array $file, string $fieldName): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/svg+xml', 'image/x-icon'];
        if (!in_array($file['type'], $allowedTypes)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = $fieldName . '-' . time() . '.' . $ext;
        $destination = BASE_PATH . '/public/uploads/' . $filename;
        move_uploaded_file($file['tmp_name'], $destination);
        return '/uploads/' . $filename;
    }

    // ─────────────────────────────────────────────
    // Google Meet — fluxo de autorização OAuth 2.0
    // ─────────────────────────────────────────────

    /**
     * Inicia o fluxo: redireciona o admin para a tela de consentimento do Google.
     * GET /admin/google-meet/oauth/start
     */
    public function googleMeetOAuthStart(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        if (($user['role'] ?? '') !== 'superadmin') {
            $this->flash('error', 'Apenas o superadmin pode autorizar o Google Meet.');
            $this->redirect('/admin/configuracoes');
            return;
        }

        $google = new GoogleMeetService();
        $clientId = trim((string) $this->setting('google_meet_client_id', ''));
        $clientSecret = trim((string) $this->setting('google_meet_client_secret', ''));
        if ($clientId === '' || $clientSecret === '') {
            $this->flash('error', 'Preencha e salve o Client ID e o Client Secret antes de autorizar.');
            $this->redirect('/admin/configuracoes');
            return;
        }

        // Guarda um state anti-CSRF na sessão.
        $state = bin2hex(random_bytes(16));
        $this->session->set('google_meet_oauth_state', $state);

        $authUrl = $google->buildAuthUrl($this->googleMeetRedirectUri(), $state);
        $this->redirect($authUrl);
    }

    /**
     * Callback do Google: troca o code por tokens e persiste o refresh_token.
     * GET /admin/google-meet/oauth/callback
     */
    public function googleMeetOAuthCallback(Request $request, Response $response): void
    {
        $user = $this->currentUser();
        if (($user['role'] ?? '') !== 'superadmin') {
            $this->flash('error', 'Acesso negado.');
            $this->redirect('/admin/configuracoes');
            return;
        }

        $error = (string) $request->query('error', '');
        if ($error !== '') {
            $this->flash('error', 'Autorização negada pelo Google: ' . $error);
            $this->redirect('/admin/configuracoes');
            return;
        }

        // Valida o state anti-CSRF.
        $state = (string) $request->query('state', '');
        $expected = (string) $this->session->get('google_meet_oauth_state', '');
        $this->session->remove('google_meet_oauth_state');
        if ($state === '' || !hash_equals($expected, $state)) {
            $this->flash('error', 'Falha de validação (state). Tente autorizar novamente.');
            $this->redirect('/admin/configuracoes');
            return;
        }

        $code = (string) $request->query('code', '');
        if ($code === '') {
            $this->flash('error', 'Código de autorização ausente.');
            $this->redirect('/admin/configuracoes');
            return;
        }

        $google = new GoogleMeetService();
        $tokens = $google->exchangeCodeForTokens($code, $this->googleMeetRedirectUri());

        if ($tokens === null || empty($tokens['refresh_token'])) {
            $this->flash('error', 'Não foi possível obter o refresh token. Revogue o acesso do app na conta Google e tente novamente (o Google só devolve o refresh_token no primeiro consentimento).');
            $this->redirect('/admin/configuracoes');
            return;
        }

        $this->settingModel->setWithGroup('google_meet_refresh_token', (string) $tokens['refresh_token'], 'videocall');
        $this->app->reloadSettings();

        $this->flash('success', 'Conta Google autorizada com sucesso! As reuniões agora serão criadas no Google Meet.');
        $this->redirect('/admin/configuracoes');
    }

    /**
     * URL de redirecionamento registrada no Google Cloud Console.
     */
    private function googleMeetRedirectUri(): string
    {
        $base = rtrim((string) $this->setting('site_url', ''), '/');
        return $base . '/admin/google-meet/oauth/callback';
    }
}
