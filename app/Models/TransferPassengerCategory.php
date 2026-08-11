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
        $slug = mb_strtolower($name);
        $slug = preg_replace('/[àáâãäå]/', 'a', $slug);
        $slug = preg_replace('/[èéêë]/', 'e', $slug);
        $slug = preg_replace('/[ìíîï]/', 'i', $slug);
        $slug = preg_replace('/[òóôõö]/', 'o', $slug);
        $slug = preg_replace('/[ùúûü]/', 'u', $slug);
        $slug = preg_replace('/[ç]/', 'c', $slug);
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
