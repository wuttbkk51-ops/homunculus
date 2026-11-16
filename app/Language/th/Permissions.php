<?php

declare(strict_types=1);

/**
 * ข้อความระบบภาษาไทยสำหรับชื่อสิทธิ์ (Permissions) และชื่อโมดูล (Modules)
 * ใช้ร่วมกับ navbar_helper และระบบตรวจสอบสิทธิ์แบบ Contextual RBAC
 */
return [
    // --------------------------------------------------------------------------
    // ระดับโมดูล (Module-Level Names & Aliases)
    // --------------------------------------------------------------------------
    'dashboard'  => 'แดชบอร์ด',
    'pos'        => 'จุดขายหน้าร้าน (POS)',
    'sales'      => 'การขาย & POS',
    'sale'       => 'การขาย & POS',
    'items'      => 'สินค้า & สต็อก',
    'item'       => 'สินค้า & สต็อก',
    'products'   => 'สินค้า & บริการ',
    'product'    => 'สินค้า & บริการ',
    'categories' => 'หมวดหมู่สินค้า',
    'category'   => 'หมวดหมู่สินค้า',
    'inventory'  => 'คลังสินค้า & สต็อก',
    'customers'  => 'ลูกค้า & สมาชิก',
    'customer'   => 'ลูกค้า & สมาชิก',
    'users'      => 'ผู้ใช้งาน & พนักงาน',
    'user'       => 'ผู้ใช้งาน & พนักงาน',
    'branches'   => 'สาขา & จุดให้บริการ',
    'branch'     => 'สาขา & จุดให้บริการ',
    'reports'    => 'รายงาน & สถิติ',
    'report'     => 'รายงาน & สถิติ',
    'settings'   => 'ตั้งค่าระบบ',
    'setting'    => 'ตั้งค่าระบบ',
    'tenants'    => 'องค์กร & บริษัท',
    'tenant'     => 'องค์กร & บริษัท',

    // --------------------------------------------------------------------------
    // สิทธิ์ระดับละเอียด (Granular Action & Scope Keys)
    // --------------------------------------------------------------------------
    // Platform / Tenant Level
    'tenant.manage.all'         => 'จัดการและระงับการใช้งานองค์กร',

    // Branch Module
    'branch.view.all'           => 'ดูข้อมูลสาขาทั้งหมด',
    'branch.manage.all'         => 'จัดการข้อมูลสาขา',

    // User Module
    'user.view.all'             => 'ดูรายชื่อพนักงานทั้งหมด',
    'user.manage.all'           => 'จัดการข้อมูลพนักงานและมอบหมายสาขา',

    // Product Module
    'product.view.all'          => 'ดูรายการสินค้าและราคา',
    'product.create.all'        => 'เพิ่มรายการสินค้าใหม่',
    'product.edit.all'          => 'แก้ไขข้อมูลสินค้าและราคา',
    'product.delete.all'        => 'ลบรายการสินค้า',

    // Inventory Module
    'inventory.view.branch'     => 'ดูยอดสต็อกในสาขา',
    'inventory.view.all'        => 'ดูยอดสต็อกทุกสาขา',
    'inventory.adjust.branch'   => 'ปรับยอดและตรวจนับสต็อก',
    'inventory.transfer.branch' => 'โอนย้ายสต็อกสินค้าระหว่างสาขา',

    // Sale & POS Module
    'sale.view.own'             => 'ดูประวัติการขายของตนเอง',
    'sale.view.branch'          => 'ดูประวัติการขายในสาขา',
    'sale.view.all'             => 'ดูยอดขายรวมทุกสาขา',
    'sale.create.branch'        => 'เปิดบิลขายหน้าร้าน',
    'sale.void.branch'          => 'ยกเลิกบิลขายและคืนเงิน',
    'sale.delete.branch'        => 'ลบประวัติการขาย',

    // Customer Module
    'customer.view.all'         => 'ดูรายชื่อลูกค้าและสมาชิก',
    'customer.create.all'       => 'สมัครสมาชิกลูกค้าใหม่',
    'customer.edit.all'         => 'แก้ไขข้อมูลสมาชิกและแต้มสะสม',

    // Report Module
    'report.view.branch'        => 'ดูรายงานยอดขายประจำสาขา',
    'report.view.all'           => 'ดูรายงานภาพรวมยอดขาย',
    'report.export.all'         => 'ส่งออกรายงาน (Excel/PDF)',

    // System Settings & Admin Access
    'admin.access'              => 'เข้าสู่ระบบบริหารจัดการ',
    'admin.settings'            => 'จัดการการตั้งค่าระบบแอดมิน',
];
