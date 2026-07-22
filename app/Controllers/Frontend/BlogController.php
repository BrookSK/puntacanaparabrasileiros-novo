<?php
declare(strict_types=1);

namespace App\Controllers\Frontend;

use Core\Controller;
use Core\Request;
use Core\Response;
use App\Models\BlogPost;

class BlogController extends Controller
{
    private BlogPost $blogModel;

    public function __construct()
    {
        parent::__construct();
        $this->blogModel = new BlogPost();
    }

    public function index(Request $request, Response $response): void
    {
        $page = max(1, (int) $request->query('page', '1'));
        $category = $request->query('categoria');

        if ($category) {
            $posts = $this->blogModel->getByCategory($category, $page, 9);
        } else {
            $posts = $this->blogModel->getPublished($page, 9);
        }

        // Post destaque (primeiro mais recente)
        $featuredPost = $this->blogModel->getLatest(1);
        $featuredPost = $featuredPost[0] ?? null;

        // Categorias para filtro
        $categories = $this->db->fetchAll("SELECT * FROM blog_categories ORDER BY sort_order ASC");

        $this->view('frontend/blog/index', [
            'posts' => $posts,
            'featuredPost' => $featuredPost,
            'categories' => $categories,
            'currentCategory' => $category,
            'pageTitle' => 'Blog - Punta Cana para Brasileiros',
            'metaDescription' => 'Dicas, roteiros e informações úteis para planejar sua viagem perfeita para Punta Cana.',
        ], 'app');
    }

    public function show(Request $request, Response $response): void
    {
        $slug = $request->param('slug', '');
        $post = $this->blogModel->findBySlug($slug);

        if (!$post || $post['status'] !== 'published') {
            $this->abort(404, 'Post não encontrado.');
        }

        // Posts relacionados
        $relatedPosts = $this->blogModel->getLatest(3);

        $this->view('frontend/blog/show', [
            'post' => $post,
            'relatedPosts' => $relatedPosts,
            'pageTitle' => $post['meta_title'] ?: $post['title'],
            'metaDescription' => $post['meta_description'] ?: ($post['excerpt'] ?? ''),
        ], 'app');
    }

    public function category(Request $request, Response $response): void
    {
        $slug = $request->param('slug', '');
        $page = max(1, (int) $request->query('page', '1'));

        // Buscar categoria
        $category = $this->db->fetchOne("SELECT * FROM blog_categories WHERE slug = ?", [$slug]);
        if (!$category) {
            $this->abort(404, 'Categoria não encontrada.');
        }

        $posts = $this->blogModel->getByCategory($slug, $page, 9);

        $this->view('frontend/blog/category', [
            'category' => $category,
            'posts' => $posts,
            'pageTitle' => $category['name'] . ' - Blog',
            'metaDescription' => 'Artigos sobre ' . $category['name'] . ' em Punta Cana.',
        ], 'app');
    }
}
