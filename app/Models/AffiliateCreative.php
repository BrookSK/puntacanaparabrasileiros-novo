<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class AffiliateCreative extends Model
{
    protected string $table = 'affiliate_creatives';
    protected array $fillable = [
        'title', 'description', 'image_url', 'type',
        'dimensions', 'sort_order', 'status',
    ];

    public function getActive(): array
    {
        return $this->where("status = 'active'", [], 'sort_order ASC, created_at DESC');
    }

    public function getAll(): array
    {
        return $this->db->fetchAll("SELECT * FROM {$this->table} ORDER BY sort_order ASC, created_at DESC");
    }
}
