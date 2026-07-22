<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TransferLocation extends Model
{
    protected string $table = 'transfer_locations';
    protected array $fillable = [
        'title', 'slug', 'address', 'latitude', 'longitude',
        'location_type', 'sort_order', 'status',
    ];

    public function getActive(): array
    {
        return $this->where('status = 1', [], 'sort_order ASC, title ASC');
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->findWhere('slug', $slug);
    }

    public function getByType(string $type): array
    {
        return $this->where('location_type = ? AND status = 1', [$type], 'sort_order ASC');
    }

    public function getGroupedByType(): array
    {
        $locations = $this->getActive();
        $grouped = [];
        foreach ($locations as $loc) {
            $grouped[$loc['location_type']][] = $loc;
        }
        return $grouped;
    }
}
