<?php

namespace App\Controllers;

use App\Services\BranchService;

/**
 * Controller สำหรับขั้นตอนจัดการ Authentication Context (เลือกสาขา / สลับสาขา / แจ้งเตือน Tenant ระงับ)
 * ออกแบบตามหลัก Thin Controller (Zero Business Logic)
 */
class AuthController extends BaseController
{
    protected BranchService $branchService;

    public function __construct()
    {
        $this->branchService = service('branch_service');
    }

    /**
     * สลับสาขาที่กำลังทำงานอยู่ (Switch Branch ผ่าน Navbar Dropdown หรือ API)
     */
    public function switchBranch(int $branchId)
    {
        $user    = auth()->user();
        $success = $this->branchService->setActiveBranch((int) $user->id, $branchId);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => $success,
                'message' => $success ? 'สลับสาขาเรียบร้อยแล้ว' : 'คุณไม่มีสิทธิ์เข้าถึงสาขาดังกล่าว',
            ]);
        }

        if (! $success) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์สลับไปยังสาขาดังกล่าว');
        }

        return redirect()->back()->with('message', 'สลับสาขาเรียบร้อยแล้ว');
    }

    /**
     * แสดงหน้าแจ้งเตือนเมื่อ Tenant ถูกระงับหรือหมดอายุ
     */
    public function tenantSuspendedView()
    {
        return view('auth/tenant_suspended');
    }
}
