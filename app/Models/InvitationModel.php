<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model จัดการตาราง invitations
 *
 * เป็น Central Table → สืบทอดจาก CodeIgniter\Model โดยตรง (ไม่ใช่ TenantModel)
 * เพราะ invitations ไม่ผูกกับ tenant เดียว
 */
class InvitationModel extends Model
{
    protected $table            = 'invitations';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'tenant_id',
        'tenant_name',
        'tenant_slug',
        'email',
        'role_group',
        'token_hash',
        'invited_by',
        'expires_at',
        'accepted_at',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = '';    // ไม่มี updated_at ใน schema

    // Validation
    protected $validationRules = [
        'email'      => 'required|valid_email|max_length[254]',
        'role_group' => 'required|max_length[50]',
        'token_hash' => 'required|exact_length[64]',
        'expires_at' => 'required',
    ];

    /**
     * ค้นหา Invitation ที่ยังไม่หมดอายุและยังไม่ถูกใช้จาก token_hash
     */
    public function findValidByHash(string $hash): ?array
    {
        return $this
            ->where('token_hash', $hash)
            ->where('accepted_at', null)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();
    }

    /**
     * รายการ Invitation ทั้งหมดของ Tenant (ใช้ใน Admin UI)
     *
     * @return list<array>
     */
    public function findByTenant(int $tenantId): array
    {
        return $this
            ->where('tenant_id', $tenantId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * รายการ Invitation ทั้งหมด สำหรับ Super Admin ดูภาพรวม
     *
     * @return list<array>
     */
    public function findAllWithTenant(): array
    {
        return $this
            ->select('invitations.*, tenants.name AS tenant_display_name')
            ->join('tenants', 'tenants.id = invitations.tenant_id', 'left')
            ->orderBy('invitations.created_at', 'DESC')
            ->findAll();
    }
}
