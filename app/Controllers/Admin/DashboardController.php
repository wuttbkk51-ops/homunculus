<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

/**
 * Controller สำหรับหน้า Dashboard ผู้ดูแลระบบ (อยู่ภายใต้ Filter admin/*)
 */
class DashboardController extends BaseController
{
    public function index()
    {
        $user         = auth()->user();
        $activeBranch = active_branch();

        return view('admin/dashboard', [
            'user'         => $user,
            'activeBranch' => $activeBranch,
        ]);
    }
}
