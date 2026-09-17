<?php
declare(strict_types=1);

namespace App\Models;

use Core\Model;

/**
 * Solicitações públicas de cadastro de agência (pendentes de aprovação).
 * Espelha o fluxo de AffiliateRequest.
 */
class AgencyRequest extends Model
{
    protected string $table = 'agency_requests';
    protected array $fillable = [
        'company_name', 'trade_name', 'cnpj', 'contact_name', 'email', 'phone',
        'city', 'country', 'password_hash', 'message',
        'status', 'admin_notes', 'approved_at', 'rejected_at',
    ];

    public function countByStatus(string $status): int
    {
        return $this->count("status = ?", [$status]);
    }

    public function getPending(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "status = 'pending'", [], 'created_at DESC');
    }

    public function getApproved(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "status = 'approved'", [], 'approved_at DESC');
    }

    public function getRejected(int $page = 1, int $perPage = 20): array
    {
        return $this->paginate($page, $perPage, "status = 'rejected'", [], 'rejected_at DESC');
    }

    public function findByEmail(string $email): ?array
    {
        return $this->findWhere('email', $email);
    }

    public function approve(int $id, ?string $notes = null): void
    {
        $data = ['status' => 'approved', 'approved_at' => date('Y-m-d H:i:s')];
        if ($notes) $data['admin_notes'] = $notes;
        $this->update($id, $data);
    }

    public function reject(int $id, ?string $notes = null): void
    {
        $data = ['status' => 'rejected', 'rejected_at' => date('Y-m-d H:i:s')];
        if ($notes) $data['admin_notes'] = $notes;
        $this->update($id, $data);
    }
}
