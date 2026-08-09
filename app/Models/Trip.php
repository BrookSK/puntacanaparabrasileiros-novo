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

    public function getPublished(int $page = 1, int $perPage = 12, string $orderBy = 'relevancia'): array
    {
        $offset = ($page - 1) * $perPage;
        $needsPriceJoin = in_array($orderBy, ['preco_asc', 'preco_desc']);

        if ($needsPriceJoin) {
            $direction = $orderBy === 'preco_asc' ? 'ASC' : 'DESC';
            $sql = "SELECT t.*, MIN(COALESCE(tpc.sale_price, tpc.price)) as min_price
                    FROM `{$this->table}` t
                    LEFT JOIN trip_packages tp ON tp.trip_id = t.id
                    LEFT JOIN trip_package_categories tpc ON tpc.package_id = tp.id AND COALESCE(tpc.sale_price, tpc.price) > 0
                    WHERE t.status = 'published'
                    GROUP BY t.id
                    ORDER BY min_price {$direction}
                    LIMIT ? OFFSET ?";
            $items = $this->db->fetchAll($sql, [$perPage, $offset]);
        } else {
            $orderMap = [
                'recente' => 'created_at DESC',
                'antigo' => 'created_at ASC',
                'relevancia' => 'sort_order ASC, created_at DESC',
            ];
            $order = $orderMap[$orderBy] ?? 'sort_order ASC, created_at DESC';
            $items = $this->db->fetchAll(
                "SELECT * FROM `{$this->table}` WHERE status = 'published' ORDER BY {$order} LIMIT ? OFFSET ?",
                [$perPage, $offset]
            );
        }

        $total = (int) $this->db->fetchColumn(
            "SELECT COUNT(*) FROM `{$this->table}` WHERE status = 'published'"
        );

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => (int) ceil($total / $perPage),
        ];
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

    public function getByCategory(int $categoryId, int $page = 1, int $perPage = 12, string $orderBy = 'relevancia'): array
    {
        $offset = ($page - 1) * $perPage;

        // Para ordenar por preço, precisa de JOIN com trip_packages e trip_package_categories
        $needsPriceJoin = in_array($orderBy, ['preco_asc', 'preco_desc']);

        if ($needsPriceJoin) {
            $direction = $orderBy === 'preco_asc' ? 'ASC' : 'DESC';
            $sql = "SELECT t.*, MIN(COALESCE(tpc.sale_price, tpc.price)) as min_price
                    FROM `{$this->table}` t
                    INNER JOIN trip_category_relations tcr ON t.id = tcr.trip_id
                    LEFT JOIN trip_packages tp ON tp.trip_id = t.id
                    LEFT JOIN trip_package_categories tpc ON tpc.package_id = tp.id AND COALESCE(tpc.sale_price, tpc.price) > 0
                    WHERE tcr.category_id = ? AND t.status = 'published'
                    GROUP BY t.id
                    ORDER BY min_price {$direction}
                    LIMIT ? OFFSET ?";
        } else {
            $orderMap = [
                'recente' => 't.created_at DESC',
                'antigo' => 't.created_at ASC',
                'relevancia' => 't.sort_order ASC, t.created_at DESC',
            ];
            $order = $orderMap[$orderBy] ?? 't.sort_order ASC, t.created_at DESC';
            $sql = "SELECT t.* FROM `{$this->table}` t
                    INNER JOIN trip_category_relations tcr ON t.id = tcr.trip_id
                    WHERE tcr.category_id = ? AND t.status = 'published'
                    ORDER BY {$order}
                    LIMIT ? OFFSET ?";
        }

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
