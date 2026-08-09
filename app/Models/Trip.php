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

    /**
     * Busca passeios com filtros combinados (sidebar da categoria).
     */
    public function getFiltered(int $categoryId, array $filters, string $orderBy = 'relevancia', int $page = 1, int $perPage = 12): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $joins = [];
        $conditions = ["t.status = 'published'"];

        // Filtro por categoria principal
        $joins[] = "INNER JOIN trip_category_relations tcr ON t.id = tcr.trip_id AND tcr.category_id = ?";
        $params[] = $categoryId;

        // Filtro por destino (tag tipo 'destino')
        if (!empty($filters['destino'])) {
            $placeholders = implode(',', array_fill(0, count($filters['destino']), '?'));
            $joins[] = "INNER JOIN trip_tag_relations ttr_dest ON t.id = ttr_dest.trip_id
                        INNER JOIN trip_tags tt_dest ON ttr_dest.tag_id = tt_dest.id AND tt_dest.type = 'destino' AND tt_dest.slug IN ({$placeholders})";
            $params = array_merge($params, $filters['destino']);
        }

        // Filtro por atividade (tag tipo 'atividade')
        if (!empty($filters['atividade'])) {
            $placeholders = implode(',', array_fill(0, count($filters['atividade']), '?'));
            $joins[] = "INNER JOIN trip_tag_relations ttr_act ON t.id = ttr_act.trip_id
                        INNER JOIN trip_tags tt_act ON ttr_act.tag_id = tt_act.id AND tt_act.type = 'atividade' AND tt_act.slug IN ({$placeholders})";
            $params = array_merge($params, $filters['atividade']);
        }

        // Filtro por tags genéricas (Tipos de Viagem no site antigo)
        if (!empty($filters['tag'])) {
            $placeholders = implode(',', array_fill(0, count($filters['tag']), '?'));
            $joins[] = "INNER JOIN trip_tag_relations ttr_tag ON t.id = ttr_tag.trip_id
                        INNER JOIN trip_tags tt_tag ON ttr_tag.tag_id = tt_tag.id AND tt_tag.type = 'tag' AND tt_tag.slug IN ({$placeholders})";
            $params = array_merge($params, $filters['tag']);
        }

        // Filtro por datas de início (mês)
        if (!empty($filters['data'])) {
            $dateConditions = [];
            foreach ($filters['data'] as $monthKey) {
                $dateConditions[] = "DATE_FORMAT(tfd.date, '%Y-%m') = ?";
                $params[] = $monthKey;
            }
            $joins[] = "INNER JOIN trip_fixed_dates tfd ON t.id = tfd.trip_id AND tfd.status = 'available' AND tfd.date >= CURDATE() AND (" . implode(' OR ', $dateConditions) . ")";
        }

        // Filtro por duração (em dias)
        if (!empty($filters['duracao_min']) && (int)$filters['duracao_min'] > 0) {
            $conditions[] = "CASE WHEN t.duration_unit = 'days' THEN CAST(t.duration AS UNSIGNED) ELSE CEIL(CAST(t.duration AS UNSIGNED) / 24) END >= ?";
            $params[] = (int)$filters['duracao_min'];
        }
        if (!empty($filters['duracao_max']) && (int)$filters['duracao_max'] > 0) {
            $conditions[] = "CASE WHEN t.duration_unit = 'days' THEN CAST(t.duration AS UNSIGNED) ELSE CEIL(CAST(t.duration AS UNSIGNED) / 24) END <= ?";
            $params[] = (int)$filters['duracao_max'];
        }

        // Filtro por preço
        $needsPriceFilter = (!empty($filters['preco_min']) && (int)$filters['preco_min'] > 0) || (!empty($filters['preco_max']) && (int)$filters['preco_max'] > 0);
        $needsPriceOrder = in_array($orderBy, ['preco_asc', 'preco_desc']);
        $needsPriceJoin = $needsPriceFilter || $needsPriceOrder;

        if ($needsPriceJoin) {
            $joins[] = "LEFT JOIN trip_packages tp_price ON tp_price.trip_id = t.id
                        LEFT JOIN trip_package_categories tpc_price ON tpc_price.package_id = tp_price.id AND COALESCE(tpc_price.sale_price, tpc_price.price) > 0";
        }

        if ($needsPriceFilter) {
            if (!empty($filters['preco_min']) && (int)$filters['preco_min'] > 0) {
                $conditions[] = "COALESCE(tpc_price.sale_price, tpc_price.price) >= ?";
                $params[] = (int)$filters['preco_min'];
            }
            if (!empty($filters['preco_max']) && (int)$filters['preco_max'] > 0) {
                $conditions[] = "COALESCE(tpc_price.sale_price, tpc_price.price) <= ?";
                $params[] = (int)$filters['preco_max'];
            }
        }

        $joinsSql = implode("\n", $joins);
        $conditionsSql = implode(' AND ', $conditions);

        // Ordenação
        if ($needsPriceOrder) {
            $direction = $orderBy === 'preco_asc' ? 'ASC' : 'DESC';
            $orderSql = "ORDER BY MIN(COALESCE(tpc_price.sale_price, tpc_price.price)) {$direction}";
        } else {
            $orderMap = [
                'recente' => 'ORDER BY t.created_at DESC',
                'relevancia' => 'ORDER BY t.sort_order ASC, t.created_at DESC',
            ];
            $orderSql = $orderMap[$orderBy] ?? 'ORDER BY t.sort_order ASC, t.created_at DESC';
        }

        $sql = "SELECT t.*
                FROM `{$this->table}` t
                {$joinsSql}
                WHERE {$conditionsSql}
                GROUP BY t.id
                {$orderSql}
                LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;

        $items = $this->db->fetchAll($sql, $params);

        // Count
        $countParams = array_slice($params, 0, -2);
        $countSql = "SELECT COUNT(DISTINCT t.id)
                     FROM `{$this->table}` t
                     {$joinsSql}
                     WHERE {$conditionsSql}";
        $total = (int) $this->db->fetchColumn($countSql, $countParams);

        return [
            'items' => $items,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'total_pages' => $total > 0 ? (int) ceil($total / $perPage) : 0,
        ];
    }

    /**
     * Retorna tags de um tipo específico com contagem de passeios publicados.
     */
    public function getTagsWithCount(string $type): array
    {
        return $this->db->fetchAll(
            "SELECT tt.*, COUNT(DISTINCT ttr.trip_id) as trip_count
             FROM trip_tags tt
             LEFT JOIN trip_tag_relations ttr ON tt.id = ttr.tag_id
             LEFT JOIN trips t ON ttr.trip_id = t.id AND t.status = 'published'
             WHERE tt.type = ?
             GROUP BY tt.id
             HAVING trip_count > 0
             ORDER BY tt.sort_order ASC",
            [$type]
        );
    }

    /**
     * Retorna o range de preços (min/max) dos passeios publicados.
     */
    public function getPriceRange(): array
    {
        $row = $this->db->fetchOne(
            "SELECT
                FLOOR(MIN(COALESCE(tpc.sale_price, tpc.price))) as min,
                CEIL(MAX(COALESCE(tpc.sale_price, tpc.price))) as max
             FROM trip_package_categories tpc
             INNER JOIN trip_packages tp ON tpc.package_id = tp.id
             INNER JOIN trips t ON tp.trip_id = t.id AND t.status = 'published'
             WHERE COALESCE(tpc.sale_price, tpc.price) > 0"
        );
        return [
            'min' => (int) ($row['min'] ?? 0),
            'max' => (int) ($row['max'] ?? 500),
        ];
    }

    /**
     * Retorna o range de duração (em dias) dos passeios publicados.
     */
    public function getDurationRange(): array
    {
        $row = $this->db->fetchOne(
            "SELECT
                MIN(CASE WHEN duration_unit = 'days' THEN CAST(duration AS UNSIGNED) ELSE CEIL(CAST(duration AS UNSIGNED) / 24) END) as min,
                MAX(CASE WHEN duration_unit = 'days' THEN CAST(duration AS UNSIGNED) ELSE CEIL(CAST(duration AS UNSIGNED) / 24) END) as max
             FROM trips
             WHERE status = 'published' AND duration IS NOT NULL AND duration != ''"
        );
        return [
            'min' => max(0, (int) ($row['min'] ?? 0)),
            'max' => max(1, (int) ($row['max'] ?? 1)),
        ];
    }

    /**
     * Retorna meses futuros com datas fixas disponíveis, formatados para filtro.
     */
    public function getAvailableMonths(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT DATE_FORMAT(tfd.date, '%Y-%m') as month_key,
                    DATE_FORMAT(tfd.date, '%M, %Y') as label_en
             FROM trip_fixed_dates tfd
             INNER JOIN trips t ON tfd.trip_id = t.id AND t.status = 'published'
             WHERE tfd.date >= CURDATE() AND tfd.status = 'available'
             ORDER BY month_key ASC
             LIMIT 12"
        );

        // Traduzir nomes dos meses
        $monthNames = [
            'January' => 'janeiro', 'February' => 'fevereiro', 'March' => 'março',
            'April' => 'abril', 'May' => 'maio', 'June' => 'junho',
            'July' => 'julho', 'August' => 'agosto', 'September' => 'setembro',
            'October' => 'outubro', 'November' => 'novembro', 'December' => 'dezembro',
        ];

        $result = [];
        foreach ($rows as $row) {
            $label = $row['label_en'];
            foreach ($monthNames as $en => $pt) {
                $label = str_replace($en, $pt, $label);
            }
            $result[] = [
                'month_key' => $row['month_key'],
                'label' => $label,
            ];
        }
        return $result;
    }
}
