<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\Setting;
use App\Services\EmailService;

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

        $this->view('admin/settings/index', [
            'settings' => $settings,
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
            'videocall_enabled',
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
}
