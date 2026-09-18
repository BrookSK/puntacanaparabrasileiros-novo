<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class TransferPassengerCategory extends Model
{
    protected string $table = 'transfer_passenger_categories';
    protected array $fillable = [
        'name', 'slug', 'age_min', 'age_max', 'age_label',
        'field_name', 'min_quantity', 'max_quantity', 'default_quantity',
        'sort_order', 'status',
    ];

    public function getActive(): array
    {
        return $this->where("status = 'active'", [], 'sort_order ASC');
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->findWhere('slug', $slug);
    }

    public function findByFieldName(string $fieldName): ?array
    {
        return $this->findWhere('field_name', $fieldName);
    }

    public function generateSlug(string $name, ?int $excludeId = null): string
    {
        $slug = slugify($name);

        $baseSlug = $slug;
        $counter = 1;
        while ($this->exists('slug', $slug, $excludeId)) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        return $slug;
    }
}
