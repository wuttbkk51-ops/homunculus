<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\InvitationModel;
use CodeIgniter\Shield\Entities\User;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service สำหรับจัดการระบบ Invite-Only Registration
 *
 * รองรับ 2 กระแส:
 * - เชิญสร้าง Tenant ใหม่ (tenant_id = NULL)
 * - เชิญเข้าทีม Tenant ที่มีอยู่ (tenant_id มีค่า)
 */
class InvitationService
{
    public function __construct(
        protected InvitationModel $model         = new InvitationModel(),
        protected TenantService   $tenantService = new TenantService(),
    ) {}

    /**
     * สร้าง Invitation record และคืน Raw Token สำหรับส่งอีเมล
     *
     * @throws RuntimeException เมื่อบันทึก DB ไม่สำเร็จ
     */
    public function createInvite(
        string  $email,
        string  $roleGroup,
        ?int    $tenantId,
        ?int    $invitedBy,
        ?string $tenantName = null,
        ?string $tenantSlug = null,
        int     $ttlHours   = 48,
    ): string {
        $rawToken  = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $insertId = $this->model->insert([
            'tenant_id'   => $tenantId,
            'tenant_name' => $tenantName,
            'tenant_slug' => $tenantSlug,
            'email'       => strtolower(trim($email)),
            'role_group'  => $roleGroup,
            'token_hash'  => $tokenHash,
            'invited_by'  => $invitedBy,
            'expires_at'  => date('Y-m-d H:i:s', strtotime("+{$ttlHours} hours")),
        ], true);

        if (! $insertId) {
            throw new RuntimeException(lang('Invite.createFailed'));
        }

        return $rawToken;
    }

    /**
     * ตรวจสอบความถูกต้องของ token และคืน Invitation record
     *
     * @throws InvalidArgumentException เมื่อ token ไม่ถูกต้อง / หมดอายุ / ถูกใช้แล้ว
     */
    public function validateToken(string $rawToken): array
    {
        if ($rawToken === '') {
            throw new InvalidArgumentException(lang('Invite.invalidOrExpired'));
        }

        $invitation = $this->model->findValidByHash(hash('sha256', $rawToken));

        if ($invitation === null) {
            throw new InvalidArgumentException(lang('Invite.invalidOrExpired'));
        }

        return $invitation;
    }

    /**
     * ยืนยัน Invite และสร้าง User + ผูกสิทธิ์ ภายใต้ Database Transaction
     *
     * Shield group: 'admin' ถ้า role_group = 'admin', 'user' ทุกกรณีอื่น
     * Tenant role: ใช้ role_group จาก invitation ตรงๆ ใน tenant_users.role
     *
     * @throws InvalidArgumentException เมื่อ token ไม่ถูกต้อง / email ไม่ตรง
     * @throws RuntimeException         เมื่อสร้าง User หรือ Tenant ไม่สำเร็จ
     * @return User Shield User entity ที่สร้างแล้ว (สำหรับ Auto-Login)
     */
    public function acceptInvite(string $rawToken, array $userData): User
    {
        $db = db_connect();
        $db->transStart();

        try {
            // ตรวจ token ซ้ำภายใน transaction ป้องกัน Race Condition
            $invitation = $this->validateToken($rawToken);

            // บังคับใช้อีเมลตามที่ถูกเชิญเท่านั้น
            if (strtolower($userData['email']) !== strtolower($invitation['email'])) {
                throw new InvalidArgumentException(lang('Invite.emailMismatch'));
            }

            // สร้าง Shield User
            $users = auth()->getProvider();
            $user  = new User();
            $user->fill([
                'email'    => $userData['email'],
                'username' => $userData['username'] ?? null,
                'password' => $userData['password'],
            ]);
            $users->save($user);

            /** @var User $user */
            $user = $users->findById($users->getInsertID());

            // Shield group: admin เฉพาะ role 'admin', ทุกกรณีอื่นเป็น 'user'
            $shieldGroup = ($invitation['role_group'] === 'admin') ? 'admin' : 'user';
            $user->addGroup($shieldGroup);

            if ($invitation['tenant_id'] !== null) {
                // กรณีเชิญเข้า Tenant ที่มีอยู่
                $this->tenantService->addMember(
                    (int) $invitation['tenant_id'],
                    $user->id,
                    $invitation['role_group'],
                );
            } else {
                // กรณีเชิญสร้าง Tenant ใหม่ (Hybrid)
                // Admin กำหนด tenant_name ไว้ล่วงหน้าได้ ถ้า NULL ผู้รับต้องกรอกเองผ่าน $userData
                $tenantName = $invitation['tenant_name'] ?? $userData['tenant_name'] ?? null;
                $tenantSlug = $invitation['tenant_slug'] ?? $userData['tenant_slug'] ?? null;

                if (empty($tenantName) || empty($tenantSlug)) {
                    throw new InvalidArgumentException(lang('Invite.tenantInfoRequired'));
                }

                $tenant = $this->tenantService->createTenant([
                    'name' => $tenantName,
                    'slug' => $tenantSlug,
                ]);

                $this->tenantService->addMember(
                    $tenant['id'],
                    $user->id,
                    $invitation['role_group'],
                );
            }

            // ปิด Token ถาวร: accepted_at = NOW()
            $this->model->update($invitation['id'], ['accepted_at' => date('Y-m-d H:i:s')]);

            $db->transComplete();

            if (! $db->transStatus()) {
                throw new RuntimeException(lang('Invite.registrationFailed'));
            }

            return $user;

        } catch (\Throwable $e) {
            $db->transRollback();
            throw $e;
        }
    }

    /**
     * Revoke: ตั้ง expires_at เป็นอดีต เพื่อปิดลิงก์โดยไม่ลบ record (audit trail)
     */
    public function revokeInvite(int $invitationId): void
    {
        $this->model->update($invitationId, [
            'expires_at' => date('Y-m-d H:i:s', strtotime('-1 second')),
        ]);
    }

    /**
     * Resend: สร้าง Token ใหม่ รีเซ็ต accepted_at และขยายเวลา
     *
     * @return string Raw Token ใหม่สำหรับส่งทางอีเมล
     */
    public function resendInvite(int $invitationId, int $ttlHours = 48): string
    {
        $rawToken  = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $rawToken);

        $this->model->update($invitationId, [
            'token_hash'  => $tokenHash,
            'expires_at'  => date('Y-m-d H:i:s', strtotime("+{$ttlHours} hours")),
            'accepted_at' => null,
        ]);

        return $rawToken;
    }
}
