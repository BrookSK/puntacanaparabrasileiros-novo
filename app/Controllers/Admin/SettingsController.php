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
            'paypal_enabled', 'stripe_enabled', 'partial_payment_enabled',
            'whatsapp_enabled', 'affiliate_enabled', 'affiliate_auto_approve',
        ];
        foreach ($booleanFields as $field) {
            $data[$field] = isset($data[$field]) ? '1' : '0';
        }

        // Salvar no banco
        $this->settingModel->updateBulk($data);

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

        $emailService = new EmailService();
        $result = $emailService->sendTestEmail($testTo);

        if ($result) {
            $this->flash('success', 'Email de teste enviado para ' . $testTo . '!');
        } else {
            $this->flash('error', 'Falha ao enviar email de teste. Verifique as configurações SMTP.');
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
