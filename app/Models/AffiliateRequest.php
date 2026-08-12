<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

class AffiliateRequest extends Model
{
    protected string $table = 'affiliate_requests';
    protected array $fillable = [
        'first_name', 'last_name', 'email', 'phone', 'username',
        'password_hash', 'pix', 'bank_name', 'bank_agency', 'bank_account',
        'bank_account_type', 'payment_email', 'website',
        'followers_count', 'niche', 'content_type', 'promotion_strategy',
        'how_found', 'social_links', 'status', 'admin_notes',
        'approved_at', 'rejected_at',
    ];

    /**
     * Conta solicitações por status.
     */
    public function countByStatus(string $status): int
    {
        return $this->count("status = ?", [$status]);
    }

    /**
     * Lista solicitações pendentes.
     */
    public function getPending(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "status = 'pending'", [], 'created_at DESC');
    }

    /**
     * Lista solicitações aprovadas.
     */
    public function getApproved(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "status = 'approved'", [], 'approved_at DESC');
    }

    /**
     * Lista solicitações rejeitadas.
     */
    public function getRejected(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "status = 'rejected'", [], 'rejected_at DESC');
    }

    /**
     * Busca por email.
     */
    public function findByEmail(string $email): ?array
    {
        return $this->findWhere('email', $email);
    }

    /**
     * Marca como aprovada.
     */
    public function approve(int $id, ?string $notes = null): void
    {
        $data = ['status' => 'approved', 'approved_at' => date('Y-m-d H:i:s')];
        if ($notes) $data['admin_notes'] = $notes;
        $this->update($id, $data);
    }

    /**
     * Marca como rejeitada.
     */
    public function reject(int $id, ?string $notes = null): void
    {
        $data = ['status' => 'rejected', 'rejected_at' => date('Y-m-d H:i:s')];
        if ($notes) $data['admin_notes'] = $notes;
        $this->update($id, $data);
    }
}
