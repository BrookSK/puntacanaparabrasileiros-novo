<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TripCategory extends Model
{
    protected string $table = 'trip_categories';
    protected array $fillable = ['name', 'slug', 'description', 'image', 'parent_id', 'sort_order'];

    public function getAll(): array
    {
        return $this->all('sort_order ASC');
    }

    public function getParents(): array
    {
        return $this->where('parent_id IS NULL', [], 'sort_order ASC');
    }

    public function getChildren(int $parentId): array
    {
        return $this->where('parent_id = ?', [$parentId], 'sort_order ASC');
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->findWhere('slug', $slug);
    }

    public function getWithTripCount(): array
    {
        return $this->db->fetchAll(
            "SELECT tc.*, COUNT(tcr.trip_id) as trip_count
             FROM trip_categories tc
             LEFT JOIN trip_category_relations tcr ON tc.id = tcr.category_id
             LEFT JOIN trips t ON tcr.trip_id = t.id AND t.status = 'published'
             GROUP BY tc.id
             ORDER BY tc.sort_order ASC"
        );
    }
}
