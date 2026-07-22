<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\TripCategory;

class CategoriesController extends Controller
{
    private TripCategory $categoryModel;

    public function __construct()
    {
        parent::__construct();
        $this->categoryModel = new TripCategory();
    }

    public function index(Request $request, Response $response): void
    {
        $categories = $this->categoryModel->getAll();

        $this->view('admin/categories/index', [
            'categories' => $categories,
            'pageTitle' => 'Categorias de Passeios',
        ], 'admin');
    }

    public function create(Request $request, Response $response): void
    {
        $this->view('admin/categories/form', [
            'category' => null,
            'pageTitle' => 'Nova Categoria',
        ], 'admin');
    }

    public function store(Request $request, Response $response): void
    {
        $name = trim($request->input('name', ''));
        $description = trim($request->input('description', ''));
        $sortOrder = (int) $request->input('sort_order', '0');

        if (empty($name)) {
            $this->flash('error', 'O nome da categoria é obrigatório.');
            $this->redirect('/admin/categorias/criar');
            return;
        }

        $slug = $this->generateSlug($name);

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description ?: null,
            'sort_order' => $sortOrder,
        ];

        // Upload de imagem
        if ($request->hasFile('image')) {
            $uploaded = $this->uploadImage($request->file('image'));
            if ($uploaded) {
                $data['image'] = $uploaded;
            }
        }

        $this->categoryModel->create($data);

        $this->flash('success', 'Categoria criada com sucesso!');
        $this->redirect('/admin/categorias');
    }

    public function edit(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $category = $this->categoryModel->find($id);

        if (!$category) {
            $this->flash('error', 'Categoria não encontrada.');
            $this->redirect('/admin/categorias');
            return;
        }

        $this->view('admin/categories/form', [
            'category' => $category,
            'pageTitle' => 'Editar Categoria',
        ], 'admin');
    }

    public function update(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $category = $this->categoryModel->find($id);

        if (!$category) {
            $this->flash('error', 'Categoria não encontrada.');
            $this->redirect('/admin/categorias');
            return;
        }

        $name = trim($request->input('name', ''));
        $description = trim($request->input('description', ''));
        $sortOrder = (int) $request->input('sort_order', '0');

        if (empty($name)) {
            $this->flash('error', 'O nome da categoria é obrigatório.');
            $this->redirect('/admin/categorias/' . $id . '/editar');
            return;
        }

        $slug = ($name !== $category['name'])
            ? $this->generateSlug($name, $id)
            : $category['slug'];

        $data = [
            'name' => $name,
            'slug' => $slug,
            'description' => $description ?: null,
            'sort_order' => $sortOrder,
        ];

        // Upload de imagem
        if ($request->hasFile('image')) {
            $uploaded = $this->uploadImage($request->file('image'));
            if ($uploaded) {
                $data['image'] = $uploaded;
            }
        }

        $this->categoryModel->update($id, $data);

        $this->flash('success', 'Categoria atualizada com sucesso!');
        $this->redirect('/admin/categorias');
    }

    public function destroy(Request $request, Response $response): void
    {
        $id = (int) $request->param('id');
        $this->categoryModel->delete($id);

        $this->flash('success', 'Categoria excluída.');
        $this->redirect('/admin/categorias');
    }

    private function uploadImage(array $file): ?string
    {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) return null;
        if ($file['size'] > 5 * 1024 * 1024) return null;

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'category-' . uniqid() . '.' . $ext;
        $destination = BASE_PATH . '/public/uploads/' . $filename;
        move_uploaded_file($file['tmp_name'], $destination);
        return '/uploads/' . $filename;
    }

    private function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug = mb_strtolower($name);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        $baseSlug = $slug;
        $counter = 1;
        while (true) {
            $existing = $this->categoryModel->findBySlug($slug);
            if (!$existing || ($excludeId && (int) $existing['id'] === $excludeId)) {
                break;
            }
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
