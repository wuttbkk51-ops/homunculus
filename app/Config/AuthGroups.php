<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Shield\Config\AuthGroups as ShieldAuthGroups;

class AuthGroups extends ShieldAuthGroups
{
    public string $defaultGroup = 'user';

    /**
     * กำหนด Groups / Roles เป็น Preset สำหรับจัดกลุ่ม Permission
     */
    public array $groups = [
        'superadmin' => [
            'title'       => 'Super Admin',
            'description' => 'ผู้ดูแลระบบสูงสุดของแพลตฟอร์ม เข้าถึงได้ทุก Tenant',
        ],
        'admin' => [
            'title'       => 'Tenant Admin',
            'description' => 'ผู้ดูแลระบบประจำ Tenant มีสิทธิ์จัดการทุกสาขาใน Tenant',
        ],
        'manager' => [
            'title'       => 'Branch Manager',
            'description' => 'ผู้จัดการประจำสาขา มีสิทธิ์ระดับสาขา (.branch)',
        ],
        'cashier' => [
            'title'       => 'Cashier',
            'description' => 'พนักงานแคชเชียร์ประจำสาขา',
        ],
        'staff' => [
            'title'       => 'Staff',
            'description' => 'พนักงานทั่วไปประจำสาขา',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'ผู้ใช้งานทั่วไป',
        ],
    ];

    /**
     * รายการสิทธิ์ตัวอย่างในระบบ (รองรับรูปแบบ {module}.{action}.{scope})
     */
    public array $permissions = [
        'admin.access'       => 'สิทธิ์เข้าใช้งานหน้า Admin',
        'admin.settings'     => 'สิทธิ์เข้าจัดการตั้งค่าระบบ',
        'sale.view.own'      => 'ดูยอดขายเฉพาะรายการของตนเอง',
        'sale.view.branch'   => 'ดูยอดขายภายในสาขาของตนเอง',
        'sale.view.all'      => 'ดูยอดขายทั้งหมดใน Tenant',
        'sale.create.branch' => 'เปิดบิลขายภายในสาขา',
        'sale.delete.branch' => 'ยกเลิกบิลขายภายในสาขา',
        'report.view.branch' => 'ดูรายงานประจำสาขา',
        'report.view.all'    => 'ดูรายงานรวมทุกสาขาใน Tenant',
        'user.manage.all'    => 'จัดการผู้ใช้ทั้งหมดใน Tenant',
    ];

    /**
     * Matrix จับคู่ Role Presets กับ Permissions
     * รองรับ Wildcard (*) และ Scope Hierarchy (.all > .branch > .own)
     */
    public array $matrix = [
        'superadmin' => [
            '*',
        ],
        'admin' => [
            'admin.access',
            '*.all',
            'user.manage.all',
            'sale.*',
            'report.*',
        ],
        'manager' => [
            'admin.access',
            '*.branch',
            'sale.*',
            'report.view.branch',
        ],
        'cashier' => [
            'admin.access',
            'sale.create.branch',
            'sale.view.own',
        ],
        'staff' => [
            'admin.access',
            'sale.view.own',
        ],
        'user' => [],
    ];
}
