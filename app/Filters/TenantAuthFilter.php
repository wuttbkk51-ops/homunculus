<?php

declare(strict_types=1);

namespace App\Filters;

use App\Models\TenantModel;
use App\Services\TenantAuthService;
use App\Services\TenantService;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Filter ตรวจสอบการยืนยันตัวตน, กำหนดบริบท Tenant ผ่าน UUID, ตรวจสอบ Cache Flag และแจ้งเตือน Client เมื่อสิทธิ์เปลี่ยน
 */
class TenantAuthFilter implements FilterInterface
{
    /**
     * Flag ส่งต่อให้ after() แนบ Header X-Auth-Refreshed
     */
    protected static bool $authRefreshed = false;

    public function before(RequestInterface $request, $arguments = null)
    {
        static::$authRefreshed = false;

        if (! $request instanceof IncomingRequest) {
            return $request;
        }

        // ตรวจสอบสถานะการยืนยันตัวตนผ่าน Shield
        if (! auth()->loggedIn()) {
            return $this->fail(401, 'กรุณาเข้าสู่ระบบก่อนดำเนินการ');
        }

        $user = auth()->user();
        $uuid = $request->getHeaderLine('X-Tenant-ID');
        if ($uuid === '') {
            $uuid = (string) session('active_tenant_uuid', '');
        }

        // Admin ทำงานระดับ Platform เมื่อไม่ได้ระบุ Tenant มาใน request
        if ($user->inGroup('admin') && $uuid === '') {
            TenantModel::setCurrentTenantId(0);

            return $request;
        }

        if ($uuid === '') {
            return $this->fail(400, 'กรุณาระบุ Tenant UUID ผ่าน Header X-Tenant-ID');
        }

        // ค้นหาเฉพาะ UUID เพื่อป้องกันการสุ่มเดา Integer ID ตามนโยบายความปลอดภัย
        $tenantService = new TenantService();
        $tenant        = $tenantService->findByUuid($uuid);

        if (! $tenant || $tenant['status'] !== 'active') {
            return $this->fail(404, 'ไม่พบ Tenant ที่ระบุ หรือ Tenant ไม่พร้อมใช้งาน');
        }

        $tenantId = (int) $tenant['id'];
        TenantModel::setCurrentTenantId($tenantId);

        // Admin ได้รับสิทธิ์ข้ามการตรวจสอบสมาชิกภาพภายใน Tenant
        if ($user->inGroup('admin')) {
            return $request;
        }

        $userId      = (int) $user->id;
        $authService = new TenantAuthService();
        $cachedAuth  = $authService->getUserAuthCache($tenantId, $userId);

        if ($cachedAuth !== null) {
            return $request;
        }

        // Cache Miss: ตรวจสอบสถานะสมาชิกภาพจาก database
        $member = $tenantService->getMember($tenantId, $userId);

        // ตอบ 403 และไม่ logout เพื่อรักษา session เดิมสำหรับ Tenant อื่นที่ผู้ใช้มีสิทธิ์
        if (! $member || $member['status'] !== 'active') {
            return $this->fail(403, 'คุณไม่มีสิทธิ์เข้าถึง Tenant นี้ หรือบัญชีถูกระงับการใช้งาน');
        }

        $permissions = $authService->resolveUserPermissions($tenantId, $userId, $member['role']);
        $authService->setUserAuthCache($tenantId, $userId, [
            'role'         => $member['role'],
            'permissions'  => $permissions,
            'refreshed_at' => date('Y-m-d H:i:s'),
        ]);

        static::$authRefreshed = true;

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (static::$authRefreshed) {
            $response->setHeader('X-Auth-Refreshed', 'true');
        }

        return $response;
    }

    /**
     * Helper ส่ง JSON error response ในรูปแบบมาตรฐาน
     */
    protected function fail(int $status, string $message): ResponseInterface
    {
        return service('response')
            ->setStatusCode($status)
            ->setJSON([
                'status'  => 'error',
                'message' => $message,
            ]);
    }
}
