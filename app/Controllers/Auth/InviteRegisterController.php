<?php

declare(strict_types=1);

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Services\InvitationService;
use CodeIgniter\HTTP\RedirectResponse;
use InvalidArgumentException;
use RuntimeException;

/**
 * Controller สำหรับฝั่งผู้รับคำเชิญ
 *
 * GET  /register/invite   → แสดงฟอร์มสมัคร (ตรวจ token ก่อน)
 * POST /register/invite   → บันทึก User + ผูกสิทธิ์ + Auto-Login
 */
class InviteRegisterController extends BaseController
{
    public function __construct(
        protected InvitationService $inviteService = new InvitationService(),
    ) {}

    public function show(): string|RedirectResponse
    {
        $rawToken = $this->request->getGet('token') ?? '';

        try {
            $invitation = $this->inviteService->validateToken($rawToken);
        } catch (InvalidArgumentException) {
            return view('auth/invite_expired');
        }

        // Hybrid: needTenantInfo = true เมื่อเป็น Invite สร้าง Tenant ใหม่
        //         และ Admin ไม่ได้กำหนด tenant_name ไว้ล่วงหน้า
        $needTenantInfo = $invitation['tenant_id'] === null
            && empty($invitation['tenant_name']);

        return view('auth/invite_register', [
            'invitation'     => $invitation,
            'token'          => $rawToken,
            'needTenantInfo' => $needTenantInfo,
        ]);
    }

    public function store(): RedirectResponse
    {
        $post = $this->request->getPost();

        $rules = [
            'token'            => 'required',
            'email'            => 'required|valid_email|max_length[254]',
            'password'         => 'required|min_length[8]',
            'password_confirm' => 'required|matches[password]',
        ];

        // ถ้าต้องกรอกข้อมูลร้านด้วย (Hybrid / New Tenant)
        if (! empty($post['need_tenant_info'])) {
            $rules['tenant_name'] = 'required|min_length[2]|max_length[100]';
            $rules['tenant_slug'] = 'required|min_length[2]|max_length[50]|alpha_dash';
        }

        if (! $this->validateData($post, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->validator->getValidated();

        try {
            $user = $this->inviteService->acceptInvite($data['token'], $data);
            auth()->login($user);

            return redirect()->to('/dashboard')->with('message', lang('Invite.registrationSuccess'));

        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        } catch (RuntimeException $e) {
            log_message('error', '[InviteRegister] ' . $e->getMessage());

            return redirect()->back()->withInput()->with('error', lang('Invite.registrationFailed'));
        }
    }
}
