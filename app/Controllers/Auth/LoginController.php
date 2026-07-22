<?php
declare(strict_types=1);

namespace App\Controllers\Auth;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\User;
use App\Helpers\Validator;

class LoginController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function showLogin(Request $request, Response $response): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect($this->getRedirectUrl());
            return;
        }
        $this->view('auth/login', [], 'auth');
    }

    public function login(Request $request, Response $response): void
    {
        $email = trim($request->input('email', ''));
        $password = $request->input('password', '');
        $remember = $request->input('remember') === '1';

        // Validação
        $errors = [];
        if (empty($email)) $errors['email'] = 'Email é obrigatório.';
        if (empty($password)) $errors['password'] = 'Senha é obrigatória.';

        if (!empty($errors)) {
            $this->flash('errors', $errors);
            $this->flash('old', ['email' => $email]);
            $this->redirect('/login');
            return;
        }

        // Rate limiting
        if ($this->isRateLimited($request->ip())) {
            $this->flash('error', 'Muitas tentativas de login. Tente novamente em alguns minutos.');
            $this->redirect('/login');
            return;
        }

        // Autenticar
        $user = $this->userModel->authenticate($email, $password);
        if (!$user) {
            $this->incrementRateLimit($request->ip());
            $this->flash('error', 'Email ou senha incorretos.');
            $this->flash('old', ['email' => $email]);
            $this->redirect('/login');
            return;
        }

        // Limpar rate limit
        $this->clearRateLimit($request->ip());

        // Criar sessão
        $this->session->regenerate();
        $this->session->set('user', $user);

        // Log de atividade
        $this->db->insert('activity_log', [
            'user_id' => $user['id'],
            'action' => 'login',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Redirect
        $this->redirect($this->getRedirectUrl());
    }

    public function logout(Request $request, Response $response): void
    {
        $this->session->destroy();
        $this->redirect('/');
    }

    public function showForgotPassword(Request $request, Response $response): void
    {
        $this->view('auth/forgot-password', [], 'auth');
    }

    public function forgotPassword(Request $request, Response $response): void
    {
        $email = trim($request->input('email', ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Informe um email válido.');
            $this->redirect('/esqueci-senha');
            return;
        }

        $user = $this->userModel->findByEmail($email);
        if ($user) {
            // Gerar token
            $token = bin2hex(random_bytes(32));
            $this->db->insert('password_resets', [
                'email' => $email,
                'token' => hash('sha256', $token),
                'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour')),
            ]);

            // Enviar email
            $emailService = new \App\Services\EmailService();
            $resetUrl = $this->setting('site_url') . '/resetar-senha/' . $token;
            $emailService->sendTemplate(
                $email,
                $user['first_name'],
                'Recuperação de Senha - Punta Cana para Brasileiros',
                'password-reset',
                ['user' => $user, 'reset_url' => $resetUrl]
            );
        }

        // Sempre mostra sucesso (evita enumeration de emails)
        $this->flash('success', 'Se o email estiver cadastrado, você receberá um link de recuperação.');
        $this->redirect('/esqueci-senha');
    }

    public function showResetPassword(Request $request, Response $response): void
    {
        $token = $request->param('token', '');
        $this->view('auth/reset-password', ['token' => $token], 'auth');
    }

    public function resetPassword(Request $request, Response $response): void
    {
        $token = $request->input('token', '');
        $password = $request->input('password', '');
        $passwordConfirm = $request->input('password_confirmation', '');

        if (strlen($password) < 6) {
            $this->flash('error', 'A senha deve ter pelo menos 6 caracteres.');
            $this->redirect('/resetar-senha/' . $token);
            return;
        }

        if ($password !== $passwordConfirm) {
            $this->flash('error', 'As senhas não coincidem.');
            $this->redirect('/resetar-senha/' . $token);
            return;
        }

        // Verificar token
        $hashedToken = hash('sha256', $token);
        $reset = $this->db->fetchOne(
            "SELECT * FROM password_resets WHERE token = ? AND used = 0 AND expires_at > NOW() ORDER BY created_at DESC LIMIT 1",
            [$hashedToken]
        );

        if (!$reset) {
            $this->flash('error', 'Link de recuperação inválido ou expirado.');
            $this->redirect('/esqueci-senha');
            return;
        }

        // Atualizar senha
        $user = $this->userModel->findByEmail($reset['email']);
        if ($user) {
            $this->userModel->updatePassword((int) $user['id'], $password);
            $this->db->update('password_resets', ['used' => 1], 'id = ?', [(int) $reset['id']]);
        }

        $this->flash('success', 'Senha alterada com sucesso! Faça login com sua nova senha.');
        $this->redirect('/login');
    }

    private function getRedirectUrl(): string
    {
        $intended = $this->session->get('intended_url');
        if ($intended) {
            $this->session->remove('intended_url');
            return $intended;
        }

        $user = $this->session->get('user');
        if (in_array($user['role'] ?? '', ['superadmin', 'admin', 'editor'])) {
            return '/admin';
        }
        return '/minha-conta';
    }

    private function isRateLimited(string $ip): bool
    {
        $record = $this->db->fetchOne(
            "SELECT * FROM rate_limits WHERE identifier = ? AND action = 'login'",
            [$ip]
        );
        if (!$record) return false;
        if ($record['blocked_until'] && strtotime($record['blocked_until']) > time()) return true;
        if ((int) $record['attempts'] >= 5) {
            $this->db->update('rate_limits', ['blocked_until' => date('Y-m-d H:i:s', strtotime('+15 minutes'))], 'id = ?', [(int) $record['id']]);
            return true;
        }
        return false;
    }

    private function incrementRateLimit(string $ip): void
    {
        $record = $this->db->fetchOne(
            "SELECT * FROM rate_limits WHERE identifier = ? AND action = 'login'",
            [$ip]
        );
        if ($record) {
            $this->db->query("UPDATE rate_limits SET attempts = attempts + 1, last_attempt_at = NOW() WHERE id = ?", [(int) $record['id']]);
        } else {
            $this->db->insert('rate_limits', ['identifier' => $ip, 'action' => 'login', 'attempts' => 1]);
        }
    }

    private function clearRateLimit(string $ip): void
    {
        $this->db->delete('rate_limits', "identifier = ? AND action = 'login'", [$ip]);
    }
}
