<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Trip extends Model
{
    protected string $table = 'trips';
    protected array $fillable = [
        'title', 'slug', 'description', 'short_description', 'featured_image',
        'gallery', 'duration', 'duration_unit', 'difficulty', 'min_pax', 'max_pax',
        'includes', 'excludes', 'map_latitude', 'map_longitude', 'map_embed',
        'weather_info', 'meeting_point', 'important_notes',
        'partial_payment_enabled', 'partial_payment_percent',
        'group_discount_enabled', 'group_discount_rules',
        'meta_title', 'meta_description', 'sort_order', 'featured', 'status',
    ];

    public function findBySlug(string $slug): ?array
    {
        return $this->findWhere('slug', $slug);
    }

    public function getPublished(int $page = 1, int $perPage = 12, ?string $orderBy = null): array
    {
        $order = $orderBy ?? 'sort_order ASC, created_at DESC';
        return $this->paginate($page, $perPage, "status = 'published'", [], $order);
    }

    public function getFeatured(int $limit = 6): array
    {
        return $this->where(
            "status = 'published' AND featured = 1",
            [],
            'sort_order ASC',
            $limit
        );
    }

    public function getByCategory(int $categoryId, int $page = 1, int $perPage = 12): array
    {
        $sql = "SELECT t.* FROM `{$this->table}` t
                INNER JOIN trip_category_relations tcr ON t.id = tcr.trip_id
                WHERE tcr.category_id = ? AND t.status = 'published'
                ORDER BY t.sort_order ASC
                LIMIT ? OFFSET ?";
        $offset = ($page - 1) * $perPage;
        $items = $this->db->fetchAll($sql, [$categoryId, $perPage, $offset]);

        $countSql = "SELECT COUNT(*) FROM `{$this->table}` t
                     INNER JOIN trip_category_relations tcr ON t.id = tcr.trip_id
                     WHERE tcr.category_id = ? AND t.status = 'published'";
        $total = (int) $this->db->fetchColumn($countSql, [$categoryId]);

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
    }

    public function getCategories(int $tripId): array
    {
        return $this->db->fetchAll(
            "SELECT tc.* FROM trip_categories tc
             INNER JOIN trip_category_relations tcr ON tc.id = tcr.category_id
             WHERE tcr.trip_id = ?",
            [$tripId]
        );
    }

    public function syncCategories(int $tripId, array $categoryIds): void
    {
        $this->db->delete('trip_category_relations', 'trip_id = ?', [$tripId]);
        foreach ($categoryIds as $catId) {
            $this->db->insert('trip_category_relations', [
                'trip_id' => $tripId,
                'category_id' => (int) $catId,
            ]);
        }
    }

    public function getPackages(int $tripId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM trip_packages WHERE trip_id = ? ORDER BY sort_order ASC",
            [$tripId]
        );
    }

    public function getItinerary(int $tripId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM trip_itinerary WHERE trip_id = ? ORDER BY sort_order ASC",
            [$tripId]
        );
    }

    public function getExtraServices(int $tripId): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM trip_extra_services WHERE trip_id = ? ORDER BY sort_order ASC",
            [$tripId]
        );
    }

    public function getFixedDates(int $tripId, bool $futureOnly = true): array
    {
        $where = 'trip_id = ?';
        $params = [$tripId];
        if ($futureOnly) {
            $where .= ' AND date >= CURDATE()';
        }
        return $this->db->fetchAll(
            "SELECT * FROM trip_fixed_dates WHERE {$where} ORDER BY date ASC",
            $params
        );
    }

    public function getReviews(int $tripId, string $status = 'approved'): array
    {
        return $this->db->fetchAll(
            "SELECT r.*, u.first_name, u.last_name FROM trip_reviews r
             LEFT JOIN users u ON r.user_id = u.id
             WHERE r.trip_id = ? AND r.status = ?
             ORDER BY r.created_at DESC",
            [$tripId, $status]
        );
    }

    public function getAverageRating(int $tripId): float
    {
        $avg = $this->db->fetchColumn(
            "SELECT AVG(rating) FROM trip_reviews WHERE trip_id = ? AND status = 'approved'",
            [$tripId]
        );
        return round((float) ($avg ?: 0), 1);
    }

    public function getRelated(int $tripId, int $limit = 4): array
    {
        $categories = $this->getCategories($tripId);
        if (empty($categories)) {
            return $this->where("status = 'published' AND id != ?", [$tripId], 'RAND()', $limit);
        }
        $catIds = array_column($categories, 'id');
        $placeholders = implode(',', array_fill(0, count($catIds), '?'));
        $params = array_merge($catIds, [$tripId, $limit]);

        return $this->db->fetchAll(
            "SELECT DISTINCT t.* FROM trips t
             INNER JOIN trip_category_relations tcr ON t.id = tcr.trip_id
             WHERE tcr.category_id IN ({$placeholders})
             AND t.id != ? AND t.status = 'published'
             ORDER BY RAND() LIMIT ?",
            $params
        );
    }

    public function incrementViews(int $tripId): void
    {
        $this->db->query("UPDATE trips SET views_count = views_count + 1 WHERE id = ?", [$tripId]);
    }

    public function search(string $query, int $page = 1, int $perPage = 12): array
    {
        $search = '%' . $query . '%';
        return $this->paginate(
            $page,
            $perPage,
            "status = 'published' AND (title LIKE ? OR short_description LIKE ?)",
            [$search, $search],
            'title ASC'
        );
    }

    public function generateSlug(string $title, ?int $excludeId = null): string
    {
        $slug = mb_strtolower($title);
        $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
        $slug = preg_replace('/[\s-]+/', '-', $slug);
        $slug = trim($slug, '-');

        $baseSlug = $slug;
        $counter = 1;
        while ($this->exists('slug', $slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
