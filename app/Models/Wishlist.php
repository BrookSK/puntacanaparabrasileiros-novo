<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class Wishlist extends Model
{
    protected string $table = 'wishlists';
    protected array $fillable = ['user_id', 'trip_id'];

    public function getByUser(int $userId): array
    {
        return $this->db->fetchAll(
            "SELECT w.*, t.title, t.slug, t.featured_image, t.short_description, t.duration
             FROM wishlists w
             INNER JOIN trips t ON w.trip_id = t.id
             WHERE w.user_id = ? AND t.status = 'published'
             ORDER BY w.created_at DESC",
            [$userId]
        );
    }

    public function toggle(int $userId, int $tripId): bool
    {
        $existing = $this->db->fetchOne(
            "SELECT id FROM wishlists WHERE user_id = ? AND trip_id = ?",
            [$userId, $tripId]
        );

        if ($existing) {
            $this->db->delete($this->table, 'id = ?', [$existing['id']]);
            return false; // removed
        }

        $this->create(['user_id' => $userId, 'trip_id' => $tripId]);
        return true; // added
    }

    public function isInWishlist(int $userId, int $tripId): bool
    {
        return $this->db->count($this->table, 'user_id = ? AND trip_id = ?', [$userId, $tripId]) > 0;
    }

    public function countByUser(int $userId): int
    {
        return $this->count('user_id = ?', [$userId]);
    }
}
