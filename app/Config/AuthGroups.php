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
            'title'       => 'Super Administrator',
            'description' => 'ผู้ดูแลระบบสูงสุดของ Platform (tenant_id = NULL) มีสิทธิ์ทุกอย่างในระบบ',
        ],
        'admin' => [
            'title'       => 'Tenant Administrator / Owner',
            'description' => 'ผู้ดูแลระบบประจำองค์กร / เจ้าของร้าน มีสิทธิ์ระดับองค์กร (.all) ครอบคลุมทุกสาขา',
        ],
        'manager' => [
            'title'       => 'Branch Manager',
            'description' => 'ผู้จัดการประจำสาขา มีสิทธิ์ควบคุมและจัดการระดับสาขา (.branch)',
        ],
        'cashier' => [
            'title'       => 'Cashier',
            'description' => 'พนักงานแคชเชียร์หน้าร้าน มีสิทธิ์เปิดบิลขาย เช็กสต็อกสาขา และดูยอดของตนเอง (.own)',
        ],
        'staff' => [
            'title'       => 'Staff',
            'description' => 'พนักงานทั่วไปประจำสาขา มีสิทธิ์ดูสินค้าและสต็อกสาขา',
        ],
        'user' => [
            'title'       => 'User',
            'description' => 'ผู้ใช้งานเริ่มต้นทั่วไปในระบบ',
        ],
    ];

    /**
     * รายการสิทธิ์ทั้งหมดในระบบ (รูปแบบ {module}.{action}.{scope})
     */
    public array $permissions = [
        // Module: Admin & Dashboard
        'admin.access'              => 'เข้าใช้งานหน้าแดชบอร์ดและระบบจัดการ',
        'admin.settings'            => 'จัดการการตั้งค่าระบบและข้อมูลร้านค้า',

        // Module: Tenant (Platform Level)
        'tenant.manage.all'         => 'จัดการและระงับการใช้งานองค์กร (Tenants)',

        // Module: Branch (การจัดการสาขา)
        'branch.view.all'           => 'ดูข้อมูลสาขาทั้งหมดในองค์กร',
        'branch.manage.all'         => 'เพิ่ม แก้ไข และจัดการข้อมูลสาขา',

        // Module: User (การจัดการผู้ใช้และพนักงาน)
        'user.view.all'             => 'ดูรายชื่อพนักงานทั้งหมดในองค์กร',
        'user.manage.all'           => 'เพิ่ม ลบ แก้ไข และมอบหมายสาขาให้พนักงาน',

        // Module: Product (สินค้าและบริการ)
        'product.view.all'          => 'ดูรายการสินค้า ราคา และหมวดหมู่',
        'product.create.all'        => 'เพิ่มรายการสินค้าใหม่',
        'product.edit.all'          => 'แก้ไขข้อมูลสินค้าและราคา',
        'product.delete.all'        => 'ลบรายการสินค้า',

        // Module: Inventory (คลังสินค้าและสต็อก)
        'inventory.view.branch'     => 'ดูจำนวนสต็อกสินค้าคงเหลือในสาขาของตนเอง',
        'inventory.view.all'        => 'ดูจำนวนสต็อกสินค้าคงเหลือทุกสาขาในองค์กร',
        'inventory.adjust.branch'   => 'ปรับยอดสต็อกและตรวจนับสินค้าในสาขา',
        'inventory.transfer.branch' => 'โอนย้ายสต็อกสินค้าระหว่างสาขา',

        // Module: Sale (การขายหน้าร้าน POS)
        'sale.view.own'             => 'ดูประวัติและยอดขายเฉพาะรายการของตนเอง',
        'sale.view.branch'          => 'ดูประวัติและยอดขายทั้งหมดภายในสาขา',
        'sale.view.all'             => 'ดูยอดขายรวมทุกสาขาในองค์กร',
        'sale.create.branch'        => 'เปิดบิลขายหน้าร้านภายในสาขา',
        'sale.void.branch'          => 'ยกเลิกบิลขายและคืนเงินในสาขา',
        'sale.delete.branch'        => 'ลบประวัติการขายในสาขา',

        // Module: Customer (ลูกค้าและระบบสมาชิก)
        'customer.view.all'         => 'ดูรายชื่อลูกค้าและข้อมูลสมาชิก',
        'customer.create.all'       => 'สมัครสมาชิกลูกค้าใหม่',
        'customer.edit.all'         => 'แก้ไขข้อมูลสมาชิกและแต้มสะสม',

        // Module: Report (รายงานและสถิติ)
        'report.view.branch'        => 'ดูรายงานยอดขายและสรุปกะประจำสาขา',
        'report.view.all'           => 'ดูรายงานภาพรวมยอดขาย กำไร-ขาดทุน ทุกสาขา',
        'report.export.all'         => 'ส่งออกรายงานเป็นไฟล์ Excel หรือ PDF',
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
            'admin.settings',
            '*.all',
            'branch.*',
            'user.*',
            'product.*',
            'inventory.*',
            'sale.*',
            'customer.*',
            'report.*',
        ],
        'manager' => [
            'admin.access',
            '*.branch',
            'product.view.all',
            'inventory.view.branch',
            'inventory.adjust.branch',
            'inventory.transfer.branch',
            'sale.*',
            'customer.*',
            'report.view.branch',
            'user.view.all',
        ],
        'cashier' => [
            'admin.access',
            'product.view.all',
            'inventory.view.branch',
            'sale.create.branch',
            'sale.view.own',
            'customer.view.all',
            'customer.create.all',
        ],
        'staff' => [
            'admin.access',
            'product.view.all',
            'inventory.view.branch',
            'sale.view.own',
        ],
        'user' => [],
    ];
}
