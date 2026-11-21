<?php

declare(strict_types=1);

return [
    // ฝั่งผู้รับ Invite
    'invalidOrExpired'   => 'ลิงก์เชิญนี้ไม่ถูกต้องหรือหมดอายุแล้ว',
    'emailMismatch'      => 'อีเมลที่กรอกไม่ตรงกับที่ถูกเชิญ',
    'tenantInfoRequired' => 'กรุณากรอกชื่อร้านและ slug เพื่อดำเนินการต่อ',
    'registrationSuccess'=> 'สร้างบัญชีสำเร็จแล้ว ยินดีต้อนรับ!',
    'registrationFailed' => 'สมัครสมาชิกไม่สำเร็จ กรุณาลองใหม่หรือติดต่อผู้ดูแลระบบ',

    // ฝั่ง Admin จัดการ
    'createFailed'  => 'สร้างคำเชิญไม่สำเร็จ กรุณาลองใหม่',
    'sendSuccess'   => 'ส่งคำเชิญสำเร็จแล้ว',
    'revokeSuccess' => 'ยกเลิกคำเชิญเรียบร้อยแล้ว',
    'resendSuccess' => 'ส่งคำเชิญซ้ำสำเร็จแล้ว',

    // UI Labels
    'pageTitle'       => 'คำเชิญ',
    'sendInvite'      => 'ส่งคำเชิญ',
    'revokeInvite'    => 'ยกเลิก',
    'resendInvite'    => 'ส่งซ้ำ',
    'statusPending'   => 'รอการตอบรับ',
    'statusAccepted'  => 'ยืนยันแล้ว',
    'statusExpired'   => 'หมดอายุ',

    // Form Labels
    'labelEmail'      => 'อีเมล',
    'labelRole'       => 'Role',
    'labelInviteType' => 'ประเภทคำเชิญ',
    'labelTenantId'   => 'Tenant',
    'labelTenantName' => 'ชื่อร้าน',
    'labelTenantSlug' => 'Slug',
    'labelPassword'   => 'รหัสผ่าน',
    'labelPasswordConfirm' => 'ยืนยันรหัสผ่าน',
    'optionNewTenant'      => 'สร้าง Tenant ใหม่',
    'optionExistingTenant' => 'Tenant ที่มีอยู่',
    'btnRegister'          => 'สร้างบัญชี',
    'expiredTitle'         => 'ลิงก์เชิญหมดอายุ',
    'expiredMessage'       => 'ลิงก์เชิญนี้หมดอายุหรือถูกใช้งานแล้ว กรุณาติดต่อผู้ดูแลระบบเพื่อขอลิงก์ใหม่',
    'backToLogin'          => 'กลับไปหน้าเข้าสู่ระบบ',
];
