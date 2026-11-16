<?php

declare(strict_types=1);

/**
 * Menu & UI Helper สำหรับจัดการ Sidebar Navigation และองค์ประกอบ UI ใน AdminLTE 4
 * รองรับการตรวจสอบสิทธิ์แบบ Contextual RBAC (Permission-First)
 */

if (! function_exists('get_admin_menu_items')) {
    /**
     * คืนค่าโครงสร้างเมนูทั้งหมดของระบบ Admin
     * แต่ละเมนูสามารถกำหนดสิทธิ์ (permission key) เพื่อควบคุมการมองเห็นได้
     *
     * @return array<int, array<string, mixed>>
     */
    function get_admin_menu_items(): array
    {
        return [
            [
                'type' => 'header',
                'title' => 'ระบบหลัก',
            ],
            [
                'title'      => 'แดชบอร์ด',
                'url'        => 'dashboard',
                'icon'       => 'bi bi-speedometer2',
                'permission' => null, // ทุกคนที่มีสิทธิ์เข้า admin ดูแดชบอร์ดได้
            ],
            [
                'type' => 'header',
                'title' => 'งานขายและคลังสินค้า',
            ],
            [
                'title'      => 'การขาย & POS',
                'icon'       => 'bi bi-cart3',
                'permission' => 'sale.view.*',
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
                'permission' => 'product.view.*',
                'children'   => [
                    [
                        'title'      => 'รายการสินค้า',
                        'url'        => 'admin/products',
                        'icon'       => 'bi bi-tags',
                        'permission' => 'product.view.*',
                    ],
                    [
                        'title'      => 'หมวดหมู่สินค้า',
                        'url'        => 'admin/categories',
                        'icon'       => 'bi bi-grid',
                        'permission' => 'category.view.*',
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
                'permission' => 'report.view.*',
                'children'   => [
                    [
                        'title'      => 'รายงานยอดขาย',
                        'url'        => 'admin/reports/sales',
                        'icon'       => 'bi bi-cash-stack',
                        'permission' => 'report.view.*',
                    ],
                    [
                        'title'      => 'รายงานความเคลื่อนไหวสต็อก',
                        'url'        => 'admin/reports/inventory',
                        'icon'       => 'bi bi-journal-text',
                        'permission' => 'report.view.*',
                    ],
                ],
            ],
            [
                'type' => 'header',
                'title' => 'ตั้งค่า & จัดการระบบ',
            ],
            [
                'title'      => 'จัดการสาขา',
                'url'        => 'admin/branches',
                'icon'       => 'bi bi-building',
                'permission' => 'branch.manage.tenant',
            ],
            [
                'title'      => 'จัดการผู้ใช้งาน & สิทธิ์',
                'icon'       => 'bi bi-shield-lock',
                'permission' => 'user.manage.all',
                'children'   => [
                    [
                        'title'      => 'บัญชีผู้ใช้งาน',
                        'url'        => 'admin/users',
                        'icon'       => 'bi bi-person-lines-fill',
                        'permission' => 'user.manage.all',
                    ],
                    [
                        'title'      => 'บทบาท & สิทธิ์การใช้งาน',
                        'url'        => 'admin/roles',
                        'icon'       => 'bi bi-key',
                        'permission' => 'role.manage.all',
                    ],
                ],
            ],
            [
                'title'      => 'ตั้งค่าระบบ',
                'url'        => 'admin/settings',
                'icon'       => 'bi bi-gear',
                'permission' => 'setting.manage.all',
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

        // อนุญาตให้ตรงกับ path ย่อย เช่น dashboard กับ admin/dashboard
        if ($targetUri !== '' && str_starts_with($currentUri, $targetUri . '/')) {
            return true;
        }

        return false;
    }
}

if (! function_exists('render_sidebar_menu')) {
    /**
     * Render โครงสร้างเมนู Sidebar ของ AdminLTE 4 โดยกรองสิทธิ์ Contextual RBAC อัตโนมัติ
     *
     * @param array<int, array<string, mixed>>|null $menuItems โครงสร้างเมนู ถ้าไม่ระบุจะดึงค่าเริ่มต้น
     */
    function render_sidebar_menu(?array $menuItems = null): string
    {
        $items = $menuItems ?? get_admin_menu_items();
        $html  = '<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">' . PHP_EOL;

        foreach ($items as $item) {
            // ประเภท Header ขั้นส่วน
            if (($item['type'] ?? '') === 'header') {
                $html .= '<li class="nav-header">' . esc($item['title'] ?? '') . '</li>' . PHP_EOL;
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

                // หากไม่มีเมนูย่อยตัวใดที่ผู้ใช้มีสิทธิ์เข้าถึง ให้ซ่อนเมนูแม่
                if (empty($visibleChildren)) {
                    continue;
                }

                $parentActive = $hasActiveChild ? ' menu-open' : '';
                $linkActive   = $hasActiveChild ? ' active' : '';

                $html .= '<li class="nav-item' . $parentActive . '">' . PHP_EOL;
                $html .= '    <a href="#" class="nav-link' . $linkActive . '">' . PHP_EOL;
                $html .= '        <i class="nav-icon ' . esc($item['icon'] ?? 'bi bi-circle') . '"></i>' . PHP_EOL;
                $html .= '        <p>' . esc($item['title']) . '<i class="nav-arrow bi bi-chevron-right"></i></p>' . PHP_EOL;
                $html .= '    </a>' . PHP_EOL;
                $html .= '    <ul class="nav nav-treeview">' . PHP_EOL;

                foreach ($visibleChildren as $child) {
                    $childActive = ! empty($child['_active']) ? ' active' : '';
                    $html .= '        <li class="nav-item">' . PHP_EOL;
                    $html .= '            <a href="' . site_url($child['url']) . '" class="nav-link' . $childActive . '">' . PHP_EOL;
                    $html .= '                <i class="nav-icon ' . esc($child['icon'] ?? 'bi bi-circle') . '"></i>' . PHP_EOL;
                    $html .= '                <p>' . esc($child['title']) . '</p>' . PHP_EOL;
                    $html .= '            </a>' . PHP_EOL;
                    $html .= '        </li>' . PHP_EOL;
                }

                $html .= '    </ul>' . PHP_EOL;
                $html .= '</li>' . PHP_EOL;
            } else {
                // เมนูเดี่ยว ไม่มี Submenu
                $url       = $item['url'] ?? '#';
                $isActive  = admin_menu_is_active($url);
                $linkClass = 'nav-link' . ($isActive ? ' active' : '');

                $html .= '<li class="nav-item">' . PHP_EOL;
                $html .= '    <a href="' . site_url($url) . '" class="' . $linkClass . '">' . PHP_EOL;
                $html .= '        <i class="nav-icon ' . esc($item['icon'] ?? 'bi bi-circle') . '"></i>' . PHP_EOL;
                $html .= '        <p>' . esc($item['title']) . '</p>' . PHP_EOL;
                $html .= '    </a>' . PHP_EOL;
                $html .= '</li>' . PHP_EOL;
            }
        }

        $html .= '</ul>' . PHP_EOL;

        return $html;
    }
}
