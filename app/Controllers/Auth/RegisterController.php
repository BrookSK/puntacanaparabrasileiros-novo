<?php
declare(strict_types=1);

namespace App\Controllers\Auth;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\User;

class RegisterController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function showRegister(Request $request, Response $response): void
    {
        if ($this->isAuthenticated()) {
            $this->redirect('/minha-conta');
            return;
        }
        $this->view('auth/register', [], 'auth');
    }

    public function register(Request $request, Response $response): void
    {
        $data = $request->only([
            'first_name', 'last_name', 'email', 'password',
            'password_confirmation', 'phone', 'country',
        ]);

        // Validação
        $errors = [];
        if (empty($data['first_name'])) $errors['first_name'] = 'Nome é obrigatório.';
        if (empty($data['last_name'])) $errors['last_name'] = 'Sobrenome é obrigatório.';
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email inválido.';
        if (strlen($data['password'] ?? '') < 6) $errors['password'] = 'Senha deve ter pelo menos 6 caracteres.';
        if (($data['password'] ?? '') !== ($data['password_confirmation'] ?? '')) $errors['password_confirmation'] = 'Senhas não coincidem.';

        // Verificar email duplicado
        if (empty($errors['email']) && $this->userModel->findByEmail($data['email'])) {
            $errors['email'] = 'Este email já está cadastrado.';
        }

        if (!empty($errors)) {
            $this->flash('errors', $errors);
            $this->flash('old', $data);
            $this->redirect('/registrar');
            return;
        }

        // Criar usuário
        $userId = $this->userModel->createUser([
            'first_name' => trim($data['first_name']),
            'last_name' => trim($data['last_name']),
            'email' => strtolower(trim($data['email'])),
            'password' => $data['password'],
            'phone' => trim($data['phone'] ?? ''),
            'country' => $data['country'] ?? null,
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => date('Y-m-d H:i:s'),
        ]);

        // Login automático
        $user = $this->userModel->find($userId);
        unset($user['password']);
        $this->session->regenerate();
        $this->session->set('user', $user);

        // Log
        $this->db->insert('activity_log', [
            'user_id' => $userId,
            'action' => 'register',
            'ip_address' => $request->ip(),
        ]);

        $this->flash('success', 'Conta criada com sucesso! Bem-vindo(a)!');
        $this->redirect('/minha-conta');
    }
}
