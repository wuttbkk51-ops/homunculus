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
     * แสดงหน้าเลือกสาขา (กรณีผู้ใช้มีหลายสาขา)
     */
    public function selectBranchView()
    {
        if (! auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $user = auth()->user();
        if ($user->isSuperAdmin()) {
            return redirect()->to('admin');
        }

        $branches = $this->branchService->getUserBranches((int) $user->id);

        if (empty($branches)) {
            auth()->logout();

            return redirect()->to('login')->with('error', 'คุณยังไม่ได้รับมอบหมายสาขาในการทำงาน');
        }

        // หากมีเพียงสาขาเดียว กำหนดให้อัตโนมัติและส่งไปหน้า admin
        if (count($branches) === 1) {
            $this->branchService->setActiveBranch((int) $user->id, (int) $branches[0]['branch_id']);

            return redirect()->to('admin');
        }

        return view('auth/select_branch', [
            'branches'     => $branches,
            'activeBranch' => $this->branchService->getActiveBranch(),
        ]);
    }

    /**
     * บันทึกสาขาที่เลือกเข้า Session
     */
    public function selectBranchSubmit()
    {
        if (! auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $rules = [
            'branch_id' => 'required|is_natural_no_zero',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $user     = auth()->user();
        $branchId = (int) $this->request->getPost('branch_id');

        $success = $this->branchService->setActiveBranch((int) $user->id, $branchId);

        if (! $success) {
            return redirect()->back()->with('error', 'ไม่สามารถเลือกสาขานี้ได้ หรือคุณไม่มีสิทธิ์เข้าถึง');
        }

        return redirect()->to('admin');
    }

    /**
     * สลับสาขาที่กำลังทำงานอยู่ (Switch Branch)
     */
    public function switchBranch(int $branchId)
    {
        if (! auth()->loggedIn()) {
            return redirect()->to('login');
        }

        $user    = auth()->user();
        $success = $this->branchService->setActiveBranch((int) $user->id, $branchId);

        if (! $success) {
            return redirect()->back()->with('error', 'คุณไม่มีสิทธิ์สลับไปยังสาขาดังกล่าว');
        }

        return redirect()->to('admin')->with('message', 'สลับสาขาเรียบร้อยแล้ว');
    }

    /**
     * แสดงหน้าแจ้งเตือนเมื่อ Tenant ถูกระงับหรือหมดอายุ
     */
    public function tenantSuspendedView()
    {
        return view('auth/tenant_suspended');
    }
}
