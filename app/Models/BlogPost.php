<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class BlogPost extends Model
{
    protected string $table = 'blog_posts';
    protected array $fillable = [
        'title', 'slug', 'excerpt', 'content', 'featured_image',
        'category_id', 'author_id', 'meta_title', 'meta_description',
        'status', 'published_at',
    ];

    public function getLatest(int $limit = 3): array
    {
        return $this->db->fetchAll(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug, bc.color as category_color,
                    u.first_name as author_first_name, u.last_name as author_last_name
             FROM blog_posts bp
             LEFT JOIN blog_categories bc ON bp.category_id = bc.id
             LEFT JOIN users u ON bp.author_id = u.id
             WHERE bp.status = 'published'
             ORDER BY bp.published_at DESC
             LIMIT ?",
            [$limit]
        );
    }

    public function getPublished(int $page = 1, int $perPage = 9): array
    {
        return $this->paginate($page, $perPage, "status = 'published'", [], 'published_at DESC');
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetchOne(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug, bc.color as category_color,
                    u.first_name as author_first_name, u.last_name as author_last_name
             FROM blog_posts bp
             LEFT JOIN blog_categories bc ON bp.category_id = bc.id
             LEFT JOIN users u ON bp.author_id = u.id
             WHERE bp.slug = ?",
            [$slug]
        );
    }

    public function getByCategory(string $categorySlug, int $page = 1, int $perPage = 9): array
    {
        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM blog_posts bp
             INNER JOIN blog_categories bc ON bp.category_id = bc.id
             WHERE bc.slug = ? AND bp.status = 'published'",
            [$categorySlug]
        );
        $offset = ($page - 1) * $perPage;
        $items = $this->db->fetchAll(
            "SELECT bp.*, bc.name as category_name, bc.slug as category_slug, bc.color as category_color,
                    u.first_name as author_first_name, u.last_name as author_last_name
             FROM blog_posts bp
             INNER JOIN blog_categories bc ON bp.category_id = bc.id
             LEFT JOIN users u ON bp.author_id = u.id
             WHERE bc.slug = ? AND bp.status = 'published'
             ORDER BY bp.published_at DESC LIMIT ? OFFSET ?",
            [$categorySlug, $perPage, $offset]
        );
        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }
}
