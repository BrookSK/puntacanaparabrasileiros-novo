<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\User;

class UsersController extends Controller
{
    private User $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $role = $request->query('role');
        $search = $request->query('busca');

        if ($search) {
            $users = $this->userModel->search($search, $page, 20);
        } elseif ($role) {
            $users = $this->userModel->getByRole($role, $page, 20);
        } else {
            $users = $this->userModel->paginate($page, 20, '1=1', [], 'created_at DESC');
        }

        $this->view('admin/users/index', [
            'users' => $users,
            'currentRole' => $role,
            'currentSearch' => $search,
            'pageTitle' => 'Gerenciar Usuários',
        ], 'admin');
    }

    public function create(Request $request, Response $response): void
    {
        $this->view('admin/users/form', [
            'user' => null,
            'pageTitle' => 'Novo Usuário',
        ], 'admin');
    }

    public function store(Request $request, Response $response): void
    {
        $data = $request->only(['first_name', 'last_name', 'email', 'password', 'phone', 'country', 'role', 'status']);

        $errors = [];
        if (empty($data['first_name'])) $errors['first_name'] = 'Nome obrigatório.';
        if (!filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Email inválido.';
        if (strlen($data['password'] ?? '') < 6) $errors['password'] = 'Senha deve ter 6+ caracteres.';
        if ($this->userModel->findByEmail($data['email'] ?? '')) $errors['email'] = 'Email já cadastrado.';

        if (!empty($errors)) {
            $this->flash('errors', $errors);
            $this->flash('old', $data);
            $this->redirect('/admin/usuarios/criar');
            return;
        }

        $this->userModel->createUser($data);
        $this->flash('success', 'Usuário criado!');
        $this->redirect('/admin/usuarios');
    }

    public function edit(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $user = $this->userModel->find($id);
        if (!$user) $this->abort(404);

        $this->view('admin/users/form', [
            'user' => $user,
            'pageTitle' => 'Editar: ' . $user['first_name'] . ' ' . $user['last_name'],
        ], 'admin');
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $data = $request->only(['first_name', 'last_name', 'email', 'phone', 'country', 'role', 'status']);

        // Verificar email duplicado
        $existing = $this->userModel->findByEmail($data['email'] ?? '');
        if ($existing && (int) $existing['id'] !== $id) {
            $this->flash('error', 'Email já em uso por outro usuário.');
            $this->redirect('/admin/usuarios/' . $id . '/editar');
            return;
        }

        $this->userModel->update($id, $data);

        // Se nova senha informada
        $newPassword = $request->input('password');
        if ($newPassword && strlen($newPassword) >= 6) {
            $this->userModel->updatePassword($id, $newPassword);
        }

        $this->flash('success', 'Usuário atualizado!');
        $this->redirect('/admin/usuarios/' . $id . '/editar');
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $currentUser = $this->currentUser();

        // Não pode excluir a si mesmo
        if ((int) $currentUser['id'] === $id) {
            $this->flash('error', 'Você não pode excluir sua própria conta.');
            $this->redirect('/admin/usuarios');
            return;
        }

        $this->userModel->delete($id);
        $this->flash('success', 'Usuário excluído.');
        $this->redirect('/admin/usuarios');
    }

    public function impersonate(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $user = $this->userModel->find($id);
        if (!$user) $this->abort(404);

        // Salvar admin original na sessão
        $this->session->set('impersonating_from', $this->currentUser());

        unset($user['password']);
        $this->session->set('user', $user);

        $this->flash('info', 'Você está agora logado como ' . $user['first_name'] . ' ' . $user['last_name']);
        $this->redirect('/minha-conta');
    }
}
