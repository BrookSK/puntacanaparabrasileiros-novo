<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\TeamMember;

class TeamController extends Controller
{
    private TeamMember $teamModel;

    public function __construct()
    {
        parent::__construct();
        $this->requireManager();
        $this->teamModel = new TeamMember();
    }

    public function index(Request $request, Response $response): void
    {
        $members = $this->teamModel->getAll();

        $this->view('admin/team/index', [
            'members' => $members,
            'pageTitle' => 'Equipe',
        ], 'admin');
    }

    public function create(Request $request, Response $response): void
    {
        $this->view('admin/team/form', [
            'member' => null,
            'pageTitle' => 'Novo Membro da Equipe',
        ], 'admin');
    }

    public function store(Request $request, Response $response): void
    {
        $data = $this->validatedData($request, '/admin/equipe/criar');
        if ($data === null) {
            return;
        }

        // Upload de foto (opcional)
        if ($request->hasFile('photo')) {
            $uploaded = $this->uploadImage($request->file('photo'));
            if ($uploaded) {
                $data['photo'] = $uploaded;
            }
        }

        $id = $this->teamModel->create($data);

        \App\Services\AuditService::getInstance()->logCreate(
            'team_member',
            $id,
            $data['name'],
            $data
        );

        $this->flash('success', 'Membro da equipe criado com sucesso!');
        $this->redirect('/admin/equipe');
    }

    public function edit(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $member = $this->teamModel->find($id);

        if (!$member) {
            $this->flash('error', 'Membro não encontrado.');
            $this->redirect('/admin/equipe');
            return;
        }

        $this->view('admin/team/form', [
            'member' => $member,
            'pageTitle' => 'Editar Membro da Equipe',
        ], 'admin');
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $member = $this->teamModel->find($id);

        if (!$member) {
            $this->flash('error', 'Membro não encontrado.');
            $this->redirect('/admin/equipe');
            return;
        }

        $data = $this->validatedData($request, '/admin/equipe/' . $id . '/editar');
        if ($data === null) {
            return;
        }

        // Upload de foto (mantém a anterior se nenhuma nova for enviada)
        if ($request->hasFile('photo')) {
            $uploaded = $this->uploadImage($request->file('photo'));
            if ($uploaded) {
                $data['photo'] = $uploaded;
            }
        }

        $this->teamModel->update($id, $data);

        \App\Services\AuditService::getInstance()->logUpdate(
            'team_member',
            $id,
            $data['name'],
            $member,
            $data
        );

        $this->flash('success', 'Membro da equipe atualizado com sucesso!');
        $this->redirect('/admin/equipe');
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $member = $this->teamModel->find($id);

        $this->teamModel->delete($id);

        if ($member) {
            \App\Services\AuditService::getInstance()->logDelete(
                'team_member',
                $id,
                $member['name'],
                ['name' => $member['name'], 'role' => $member['role'] ?? '']
            );
        }

        $this->flash('success', 'Membro da equipe excluído.');
        $this->redirect('/admin/equipe');
    }

    /**
     * Lê e valida os campos do formulário. Em caso de erro, define a flash,
     * redireciona para $backUrl e retorna null.
     */
    private function validatedData(Request $request, string $backUrl): ?array
    {
        $name = trim($request->input('name', ''));
        $role = trim($request->input('role', ''));
        $bio = trim($request->input('bio', ''));
        $sortOrder = (int) $request->input('sort_order', '0');
        $status = $request->input('status', 'draft') === 'published' ? 'published' : 'draft';

        if ($name === '') {
            $this->flash('error', 'O nome do membro é obrigatório.');
            $this->redirect($backUrl);
            return null;
        }

        return [
            'name' => $name,
            'role' => $role ?: null,
            'bio' => $bio ?: null,
            'sort_order' => $sortOrder,
            'status' => $status,
        ];
    }

    private function uploadImage(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'team-' . uniqid() . '.' . $ext;
        $destination = BASE_PATH . '/public/uploads/' . $filename;
        move_uploaded_file($file['tmp_name'], $destination);
        return '/uploads/' . $filename;
    }
}
