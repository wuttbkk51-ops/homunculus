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
 * Filter ตรวจสอบการยืนยันตัวตน, บริบท Tenant (UUID), ตรวจจับ Cache Flag และแจ้ง Client เมื่อสิทธิ์เปลี่ยน
 */
class TenantAuthFilter implements FilterInterface
{
    /**
     * Flag ภายในสำหรับส่งต่อให้ after() รู้ว่าต้องแนบ Header X-Auth-Refreshed
     */
    protected static bool $authRefreshed = false;

    public function before(RequestInterface $request, $arguments = null)
    {
        static::$authRefreshed = false;

        if (! $request instanceof IncomingRequest) {
            return $request;
        }

        // 1. ตรวจสอบว่า Login ผ่าน Shield แล้วหรือไม่
        if (! auth()->loggedIn()) {
            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'กรุณาเข้าสู่ระบบก่อนดำเนินการ',
                ]);
        }

        $user          = auth()->user();
        $tenantService = new TenantService();
        $authService   = new TenantAuthService();

        // 2. ดึง Tenant Identifier (UUID) จาก Header X-Tenant-ID หรือ Session
        $tenantIdentifier = $request->getHeaderLine('X-Tenant-ID');
        if ($tenantIdentifier === '') {
            $tenantIdentifier = (string) session('active_tenant_uuid', '');
        }

        // 3. กรณีเป็น Admin แพลตฟอร์ม (Shield Group 'admin')
        if ($user->inGroup('admin')) {
            if ($tenantIdentifier !== '') {
                $tenant = is_numeric($tenantIdentifier)
                    ? $tenantService->findById((int) $tenantIdentifier)
                    : $tenantService->findByUuid($tenantIdentifier);

                if (! $tenant || $tenant['status'] !== 'active') {
                    return service('response')
                        ->setStatusCode(404)
                        ->setJSON([
                            'status'  => 'error',
                            'message' => 'ไม่พบ Tenant ที่ระบุ หรือ Tenant ไม่พร้อมใช้งาน',
                        ]);
                }

                TenantModel::setCurrentTenantId($tenant['id']);
            } else {
                // ระดับ Platform กลาง
                TenantModel::setCurrentTenantId(0);
            }

            // Admin ได้รับสิทธิ์ข้ามทุกการตรวจสอบ
            return $request;
        }

        // 4. กรณีเป็นผู้ใช้ทั่วไป ต้องระบุ Tenant
        if ($tenantIdentifier === '') {
            return service('response')
                ->setStatusCode(400)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'กรุณาระบุ Tenant UUID ผ่าน Header X-Tenant-ID',
                ]);
        }

        $tenant = is_numeric($tenantIdentifier)
            ? $tenantService->findById((int) $tenantIdentifier)
            : $tenantService->findByUuid($tenantIdentifier);

        if (! $tenant || $tenant['status'] !== 'active') {
            return service('response')
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'ไม่พบ Tenant ที่ระบุ หรือ Tenant ไม่พร้อมใช้งาน',
                ]);
        }

        $tenantId = (int) $tenant['id'];
        $userId   = (int) $user->id;
        TenantModel::setCurrentTenantId($tenantId);

        // 5. ตรวจสอบ Cache Flag
        $cachedAuth = $authService->getUserAuthCache($tenantId, $userId);

        if ($cachedAuth !== null) {
            // มี Cache Flag สมบูรณ์ ทำงานต่อได้ทันที
            return $request;
        }

        // 6. กรณีไม่มี Cache Flag (Cache Miss หรือโดนลบเพราะสิทธิ์เปลี่ยน/เตะออก)
        $member = $tenantService->getMember($tenantId, $userId);

        // หากถูกเตะออก หรือไม่ได้เป็นสมาชิก หรือโดนระงับ
        if (! $member || $member['status'] !== 'active') {
            auth()->logout();

            return service('response')
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => 'error',
                    'message' => 'คุณไม่มีสิทธิ์เข้าถึง Tenant นี้ หรือบัญชีถูกระงับการใช้งาน',
                ]);
        }

        // หากสถานะยัง Active ปกติ -> โหลดสิทธิ์ใหม่ลง Cache
        $permissions = $authService->resolveUserPermissions($tenantId, $userId, $member['role']);
        $authService->setUserAuthCache($tenantId, $userId, [
            'role'        => $member['role'],
            'permissions' => $permissions,
            'refreshed_at'=> date('Y-m-d H:i:s'),
        ]);

        // ตั้งสถานะให้ after() ส่ง Header X-Auth-Refreshed: true
        static::$authRefreshed = true;

        return $request;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        if (static::$authRefreshed) {
            $response->setHeader('X-Auth-Refreshed', 'true');
            // รีเซ็ตสถานะ
            static::$authRefreshed = false;
        }

        return $response;
    }
}
