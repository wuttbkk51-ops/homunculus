<?php

declare(strict_types=1);

/**
 * Menu & Sidebar Helper สำหรับ AdminLTE 4
 * จัดการโครงสร้างเมนูและการ Render Sidebar Navigation ควบคุมสิทธิ์ด้วย Contextual RBAC (Permission-First)
 */

if (! function_exists('get_admin_menu_items')) {
    /**
     * คืนค่าโครงสร้างเมนูทั้งหมดของระบบ Admin
     * เมนูที่มีการระบุ 'permission' จะแสดงเฉพาะเมื่อผู้ใช้มีสิทธิ์ตาม Contextual RBAC เท่านั้น
     *
     * @return array<int, array<string, mixed>>
     */
    function get_admin_menu_items(): array
    {
        return [
            [
                'type'  => 'header',
                'title' => 'ระบบหลัก',
            ],
            [
                'title'      => 'แดชบอร์ด',
                'url'        => 'dashboard',
                'icon'       => 'bi bi-speedometer2',
                'permission' => null, // ผู้ใช้ที่เข้าสู่ระบบ Admin ทุกคนดูแดชบอร์ดได้
            ],
            [
                'type'  => 'header',
                'title' => 'งานขายและคลังสินค้า',
            ],
            [
                'title'      => 'การขาย & POS',
                'icon'       => 'bi bi-cart3',
                'permission' => 'sale.*',
                'children'   => [
                    [
                        'title'      => 'จุดขายหน้าร้าน (POS)',
                        'url'        => 'admin/pos',
                        'icon'       => 'bi bi-shop',
                        'permission' => 'sale.create.branch',
                    ],
                    [
                        'title'      => 'รายการขายทั้งหมด',
                        'url'        => 'admin/sales',
                        'icon'       => 'bi bi-receipt',
                        'permission' => 'sale.view.*',
                    ],
                    [
                        'title'      => 'ลูกค้า & สมาชิก',
                        'url'        => 'admin/customers',
                        'icon'       => 'bi bi-people',
                        'permission' => 'customer.view.*',
                    ],
                ],
            ],
            [
                'title'      => 'สินค้า & สต็อก',
                'icon'       => 'bi bi-box-seam',
                'permission' => 'product.*',
                'children'   => [
                    [
                        'title'      => 'รายการสินค้า',
                        'url'        => 'admin/products',
                        'icon'       => 'bi bi-tags',
                        'permission' => 'product.view.all',
                    ],
                    [
                        'title'      => 'หมวดหมู่สินค้า',
                        'url'        => 'admin/categories',
                        'icon'       => 'bi bi-grid',
                        'permission' => 'product.view.all',
                    ],
                    [
                        'title'      => 'สต็อกคงเหลือ',
                        'url'        => 'admin/inventory',
                        'icon'       => 'bi bi-boxes',
                        'permission' => 'inventory.view.*',
                    ],
                ],
            ],
            [
                'title'      => 'รายงาน & สถิติ',
                'icon'       => 'bi bi-graph-up-arrow',
                'permission' => 'report.*',
                'children'   => [
                    [
                        'title'      => 'รายงานยอดขาย',
                        'url'        => 'admin/reports/sales',
                        'icon'       => 'bi bi-cash-stack',
                        'permission' => 'report.view.*',
                    ],
                    [
                        'title'      => 'รายงานสต็อกสินค้า',
                        'url'        => 'admin/reports/inventory',
                        'icon'       => 'bi bi-journal-text',
                        'permission' => 'report.view.*',
                    ],
                ],
            ],
            [
                'type'  => 'header',
                'title' => 'ตั้งค่า & จัดการระบบ',
            ],
            [
                'title'      => 'จัดการสาขา',
                'url'        => 'admin/branches',
                'icon'       => 'bi bi-building',
                'permission' => 'branch.view.all',
            ],
            [
                'title'      => 'ผู้ใช้งาน & พนักงาน',
                'icon'       => 'bi bi-shield-lock',
                'permission' => 'user.view.all',
                'children'   => [
                    [
                        'title'      => 'รายชื่อพนักงาน',
                        'url'        => 'admin/users',
                        'icon'       => 'bi bi-person-lines-fill',
                        'permission' => 'user.view.all',
                    ],
                    [
                        'title'      => 'มอบหมายและจัดการสิทธิ์',
                        'url'        => 'admin/roles',
                        'icon'       => 'bi bi-key',
                        'permission' => 'user.manage.all',
                    ],
                ],
            ],
            [
                'title'      => 'ตั้งค่าระบบ',
                'url'        => 'admin/settings',
                'icon'       => 'bi bi-gear',
                'permission' => 'admin.settings',
            ],
        ];
    }
}

if (! function_exists('admin_menu_is_active')) {
    /**
     * ตรวจสอบว่าเมนูดังกล่าวตรงกับ URL หน้าปัจจุบันหรือไม่
     */
    function admin_menu_is_active(string $path): bool
    {
        $currentUri = trim(uri_string(), '/');
        $targetUri  = trim($path, '/');

        if ($currentUri === $targetUri) {
            return true;
        }

        return $targetUri !== '' && str_starts_with($currentUri, $targetUri . '/');
    }
}

if (! function_exists('render_sidebar_menu')) {
    /**
     * Render โครงสร้างเมนู Sidebar ของ AdminLTE 4 โดยกรองสิทธิ์ Contextual RBAC อัตโนมัติ
     *
     * @param array<int, array<string, mixed>>|null $menuItems โครงสร้างเมนู (หากไม่ระบุจะดึง get_admin_menu_items())
     */
    function render_sidebar_menu(?array $menuItems = null): string
    {
        $items = $menuItems ?? get_admin_menu_items();
        $html  = '<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">' . "\n";

        foreach ($items as $item) {
            // ประเภท Header คั่นส่วน
            if (($item['type'] ?? '') === 'header') {
                $html .= '<li class="nav-header">' . esc($item['title'] ?? '') . '</li>' . "\n";
                continue;
            }

            // ตรวจสอบสิทธิ์เมนูหลัก
            if (! empty($item['permission']) && ! auth_can($item['permission'])) {
                continue;
            }

            // กรณีมีเมนูย่อย (Submenu / Treeview)
            if (! empty($item['children'])) {
                $visibleChildren = [];
                $hasActiveChild  = false;

                foreach ($item['children'] as $child) {
                    if (empty($child['permission']) || auth_can($child['permission'])) {
                        $isActive = ! empty($child['url']) && admin_menu_is_active($child['url']);
                        if ($isActive) {
                            $hasActiveChild = true;
                        }
                        $child['_active']  = $isActive;
                        $visibleChildren[] = $child;
                    }
                }

                // หากไม่มีเมนูย่อยใดที่ผู้ใช้มีสิทธิ์เข้าถึง ให้ข้ามเมนูแม่
                if (empty($visibleChildren)) {
                    continue;
                }

                $parentActive = $hasActiveChild ? ' menu-open' : '';
                $linkActive   = $hasActiveChild ? ' active' : '';
                $icon         = esc($item['icon'] ?? 'bi bi-circle');
                $title        = esc($item['title']);

                $html .= "<li class=\"nav-item{$parentActive}\">\n";
                $html .= "    <a href=\"#\" class=\"nav-link{$linkActive}\">\n";
                $html .= "        <i class=\"nav-icon {$icon}\"></i>\n";
                $html .= "        <p>{$title}<i class=\"nav-arrow bi bi-chevron-right\"></i></p>\n";
                $html .= "    </a>\n";
                $html .= "    <ul class=\"nav nav-treeview\">\n";

                foreach ($visibleChildren as $child) {
                    $childActive = ! empty($child['_active']) ? ' active' : '';
                    $childUrl    = site_url($child['url']);
                    $childIcon   = esc($child['icon'] ?? 'bi bi-circle');
                    $childTitle  = esc($child['title']);

                    $html .= "        <li class=\"nav-item\">\n";
                    $html .= "            <a href=\"{$childUrl}\" class=\"nav-link{$childActive}\">\n";
                    $html .= "                <i class=\"nav-icon {$childIcon}\"></i>\n";
                    $html .= "                <p>{$childTitle}</p>\n";
                    $html .= "            </a>\n";
                    $html .= "        </li>\n";
                }

                $html .= "    </ul>\n</li>\n";
            } else {
                // เมนูเดี่ยว ไม่มี Submenu
                $url       = $item['url'] ?? '#';
                $isActive  = admin_menu_is_active($url);
                $linkClass = 'nav-link' . ($isActive ? ' active' : '');
                $targetUrl = site_url($url);
                $icon      = esc($item['icon'] ?? 'bi bi-circle');
                $title     = esc($item['title']);

                $html .= "<li class=\"nav-item\">\n";
                $html .= "    <a href=\"{$targetUrl}\" class=\"{$linkClass}\">\n";
                $html .= "        <i class=\"nav-icon {$icon}\"></i>\n";
                $html .= "        <p>{$title}</p>\n";
                $html .= "    </a>\n";
                $html .= "</li>\n";
            }
        }

        $html .= "</ul>\n";

        return $html;
    }
}
