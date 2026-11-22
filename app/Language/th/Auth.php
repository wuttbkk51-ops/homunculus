<?php

declare(strict_types=1);

/**
 * This file is part of CodeIgniter Shield.
 *
 * (c) CodeIgniter Foundation <admin@codeigniter.com>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

return [
    // Exceptions
    'unknownAuthenticator'  => '{0} ไม่ใช่ authenticator ที่ถูกต้อง',
    'unknownUserProvider'   => 'ไม่สามารถระบุ User Provider ที่จะใช้งานได้',
    'invalidUser'           => 'ไม่พบข้อมูลผู้ใช้งานที่ระบุ',
    'bannedUser'            => 'ไม่สามารถเข้าสู่ระบบได้เนื่องจากบัญชีของคุณถูกระงับการใช้งาน',
    'logOutBannedUser'      => 'คุณถูกออกจากระบบเนื่องจากบัญชีของคุณถูกระงับการใช้งาน',
    'badAttempt'            => 'ไม่สามารถเข้าสู่ระบบได้ กรุณาตรวจสอบข้อมูลการเข้าสู่ระบบของคุณ',
    'noPassword'            => 'ไม่สามารถตรวจสอบสิทธิ์ผู้ใช้โดยไม่มีรหัสผ่านได้',
    'invalidPassword'       => 'ไม่สามารถเข้าสู่ระบบได้ กรุณาตรวจสอบรหัสผ่านของคุณ',
    'noToken'               => 'ทุก Request ต้องมี Bearer Token ในส่วนหัว {0}',
    'badToken'              => 'Access Token ไม่ถูกต้อง',
    'oldToken'              => 'Access Token หมดอายุแล้ว',
    'noUserEntity'          => 'ต้องระบุ User Entity สำหรับการตรวจสอบรหัสผ่าน',
    'invalidEmail'          => 'ไม่สามารถยืนยันได้ว่าอีเมล "{0}" ตรงกับอีเมลในระบบ',
    'unableSendEmailToUser' => 'ขออภัย เกิดปัญหาในการส่งอีเมล ไม่สามารถส่งอีเมลไปยัง "{0}" ได้',
    'throttled'             => 'มีการส่งคำขอจาก IP นี้มากเกินไป กรุณาลองใหม่อีกครั้งในอีก {0} วินาที',
    'notEnoughPrivilege'    => 'คุณไม่มีสิทธิ์ในการดำเนินการตามที่ร้องขอ',
    // JWT Exceptions
    'invalidJWT'     => 'Token ไม่ถูกต้อง',
    'expiredJWT'     => 'Token หมดอายุแล้ว',
    'beforeValidJWT' => 'Token ยังไม่พร้อมใช้งาน',

    'email'           => 'อีเมล',
    'username'        => 'ชื่อผู้ใช้',
    'password'        => 'รหัสผ่าน',
    'passwordConfirm' => 'ยืนยันรหัสผ่านอีกครั้ง',
    'haveAccount'     => 'มีบัญชีผู้ใช้อยู่แล้ว?',
    'token'           => 'Token',

    // Buttons
    'confirm' => 'ยืนยัน',
    'send'    => 'ส่งข้อมูล',

    // Registration
    'register'         => 'ลงทะเบียน',
    'registerDisabled' => 'ระบบยังไม่เปิดให้ลงทะเบียนในขณะนี้',
    'registerSuccess'  => 'ยินดีต้อนรับสู่ระบบ!',

    // Login
    'login'              => 'เข้าสู่ระบบ',
    'needAccount'        => 'ยังไม่มีบัญชีผู้ใช้?',
    'rememberMe'         => 'จดจำการเข้าสู่ระบบ',
    'forgotPassword'     => 'ลืมรหัสผ่าน?',
    'useMagicLink'       => 'เข้าสู่ระบบด้วย Magic Link',
    'magicLinkSubject'   => 'ลิงก์สำหรับเข้าสู่ระบบของคุณ',
    'magicTokenNotFound' => 'ไม่สามารถตรวจสอบความถูกต้องของลิงก์ได้',
    'magicLinkExpired'   => 'ขออภัย ลิงก์เข้าสู่ระบบหมดอายุแล้ว',
    'checkYourEmail'     => 'กรุณาตรวจสอบกล่องจดหมายในอีเมลของคุณ!',
    'magicLinkDetails'   => 'เราได้ส่งอีเมลพร้อมลิงก์เข้าสู่ระบบให้คุณแล้ว ลิงก์นี้จะใช้งานได้ภายใน {0} นาที',
    'magicLinkDisabled'  => 'ระบบยังไม่เปิดให้ใช้งาน Magic Link ในขณะนี้',
    'successLogout'      => 'คุณได้ออกจากระบบเรียบร้อยแล้ว',
    'backToLogin'        => 'กลับไปยังหน้าเข้าสู่ระบบ',

    // Passwords
    'errorPasswordLength'       => 'รหัสผ่านต้องมีความยาวอย่างน้อย {0, number} ตัวอักษร',
    'suggestPasswordLength'     => 'การใช้วลีรหัสผ่าน (Passphrase) ความยาวไม่เกิน 255 ตัวอักษร จะช่วยเพิ่มความปลอดภัยและจำได้ง่ายขึ้น',
    'errorPasswordCommon'       => 'รหัสผ่านนี้เป็นรหัสผ่านที่พบบ่อยเกินไป',
    'suggestPasswordCommon'     => 'รหัสผ่านถูกตรวจสอบกับฐานข้อมูลรหัสผ่านที่พบบ่อยกว่า 65,000 รายการหรือรหัสผ่านที่เคยรั่วไหลจากการถูกแฮก',
    'errorPasswordPersonal'     => 'รหัสผ่านต้องไม่มีข้อมูลส่วนบุคคล',
    'suggestPasswordPersonal'   => 'ไม่ควรนำอีเมลหรือชื่อผู้ใช้มาดัดแปลงเป็นรหัสผ่าน',
    'errorPasswordTooSimilar'   => 'รหัสผ่านคล้ายกับชื่อผู้ใช้มากเกินไป',
    'suggestPasswordTooSimilar' => 'ห้ามนำส่วนใดส่วนหนึ่งของชื่อผู้ใช้ไปใส่ในรหัสผ่าน',
    'errorPasswordPwned'        => 'รหัสผ่าน {0} เคยรั่วไหลจากการละเมิดข้อมูล และถูกพบถึง {1, number} ครั้งในฐานข้อมูล {2} ของรหัสผ่านที่ไม่ปลอดภัย',
    'suggestPasswordPwned'      => 'ไม่ควรใช้ {0} เป็นรหัสผ่านโดยเด็ดขาด หากคุณกำลังใช้อยู่ที่ใดก็ตาม กรุณาเปลี่ยนทันที',
    'errorPasswordEmpty'        => 'จำเป็นต้องระบุรหัสผ่าน',
    'errorPasswordTooLongBytes' => 'รหัสผ่านต้องมีความยาวไม่เกิน {param} ไบต์',
    'passwordChangeSuccess'     => 'เปลี่ยนรหัสผ่านสำเร็จเรียบร้อยแล้ว',
    'userDoesNotExist'          => 'ไม่สามารถเปลี่ยนรหัสผ่านได้ เนื่องจากไม่พบข้อมูลผู้ใช้นี้ในระบบ',
    'resetTokenExpired'         => 'ขออภัย Token สำหรับรีเซ็ตรหัสผ่านหมดอายุแล้ว',

    // Email Globals
    'emailInfo'      => 'ข้อมูลเบื้องต้นเกี่ยวกับผู้ใช้:',
    'emailIpAddress' => 'ที่อยู่ IP:',
    'emailDevice'    => 'อุปกรณ์:',
    'emailDate'      => 'วันที่:',

    // 2FA
    'email2FATitle'       => 'การยืนยันตัวตนแบบสองขั้นตอน (2FA)',
    'confirmEmailAddress' => 'ยืนยันที่อยู่อีเมลของคุณ',
    'emailEnterCode'      => 'ยืนยันอีเมลของคุณ',
    'emailConfirmCode'    => 'กรอกรหัส 6 หลักที่เราส่งไปยังอีเมลของคุณ',
    'email2FASubject'     => 'รหัสยืนยันตัวตนของคุณ',
    'email2FAMailBody'    => 'รหัสยืนยันตัวตนของคุณคือ:',
    'invalid2FAToken'     => 'รหัสยืนยันไม่ถูกต้อง',
    'need2FA'             => 'คุณต้องผ่านการยืนยันตัวตนแบบสองขั้นตอนก่อน',
    'needVerification'    => 'กรุณาตรวจสอบอีเมลของคุณเพื่อเปิดใช้งานบัญชี',

    // Activate
    'emailActivateTitle'    => 'การเปิดใช้งานบัญชีผ่านอีเมล',
    'emailActivateBody'     => 'เราได้ส่งรหัสยืนยันไปยังอีเมลของคุณแล้ว กรุณาคัดลอกรหัสมากรอกในช่องด้านล่าง',
    'emailActivateSubject'  => 'รหัสสำหรับเปิดใช้งานบัญชีของคุณ',
    'emailActivateMailBody' => 'กรุณาใช้รหัสด้านล่างนี้เพื่อเปิดใช้งานบัญชีและเริ่มเข้าใช้งานระบบ',
    'invalidActivateToken'  => 'รหัสยืนยันไม่ถูกต้อง',
    'needActivate'          => 'คุณต้องยืนยันรหัสที่ส่งไปยังอีเมลของคุณเพื่อเสร็จสิ้นขั้นตอนการลงทะเบียน',
    'activationBlocked'     => 'คุณต้องเปิดใช้งานบัญชีก่อนเข้าสู่ระบบ',

    // Groups
    'unknownGroup' => '{0} ไม่ใช่กลุ่มผู้ใช้ที่ถูกต้อง',
    'missingTitle' => 'กลุ่มผู้ใช้ต้องมีชื่อ (Title)',

    // Permissions
    'unknownPermission' => '{0} ไม่ใช่สิทธิ์การใช้งาน (Permission) ที่ถูกต้อง',
];
