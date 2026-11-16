<?php

declare(strict_types=1);

/**
 * Nav Helper สำหรับ AdminLTE 4 และ Bootstrap 5
 * รวมศูนย์ระบบ Navigation ทั้งหมด (Sidebar Treeview, Top Navbar, Page Header, Breadcrumb, Action Buttons, Dropdowns)
 * ควบคุมสิทธิ์ด้วย Contextual RBAC (Permission-First) และดึงภาษา i18n / Default Icons อัตโนมัติ
 */

if (! function_exists('nav_resolve_module')) {
    /**
     * แมปชื่อโมดูล (Module Key) ไปเป็น Permission Wildcard
     *
     * @internal
     */
    function nav_resolve_module(string $permission): string
    {
        $clean = trim($permission);
        if (str_contains($clean, '.')) {
            return $clean;
        }

        $moduleMap = [
            'dashboard'  => 'admin.access',
            'pos'        => 'sale.create.branch',
            'sale'       => 'sale.*',
            'sales'      => 'sale.*',
            'item'       => 'product.*',
            'items'      => 'product.*',
            'product'    => 'product.*',
            'products'   => 'product.*',
            'category'   => 'product.*',
            'categories' => 'product.*',
            'inventory'  => 'inventory.*',
            'customer'   => 'customer.*',
            'customers'  => 'customer.*',
            'user'       => 'user.*',
            'users'      => 'user.*',
            'branch'     => 'branch.*',
            'branches'   => 'branch.*',
            'report'     => 'report.*',
            'reports'    => 'report.*',
            'setting'    => 'admin.settings',
            'settings'   => 'admin.settings',
            'tenant'     => 'tenant.*',
            'tenants'    => 'tenant.*',
        ];

        return $moduleMap[strtolower($clean)] ?? (strtolower($clean) . '.*');
    }
}

if (! function_exists('nav_default_icon')) {
    /**
     * ค้นหาไอคอนเริ่มต้นประจำโมดูล (Default Module Icon)
     *
     * @internal
     */
    function nav_default_icon(string $permission): string
    {
        $icons = [
            'dashboard'  => 'bi bi-speedometer2',
            'pos'        => 'bi bi-shop',
            'sale'       => 'bi bi-cart3',
            'sales'      => 'bi bi-cart3',
            'item'       => 'bi bi-box-seam',
            'items'      => 'bi bi-box-seam',
            'product'    => 'bi bi-box-seam',
            'products'   => 'bi bi-box-seam',
            'category'   => 'bi bi-tags',
            'categories' => 'bi bi-tags',
            'inventory'  => 'bi bi-boxes',
            'customer'   => 'bi bi-people',
            'customers'  => 'bi bi-people',
            'user'       => 'bi bi-person-gear',
            'users'      => 'bi bi-person-gear',
            'branch'     => 'bi bi-building',
            'branches'   => 'bi bi-building',
            'report'     => 'bi bi-graph-up-arrow',
            'reports'    => 'bi bi-graph-up-arrow',
            'setting'    => 'bi bi-gear',
            'settings'   => 'bi bi-gear',
            'tenant'     => 'bi bi-buildings',
            'tenants'    => 'bi bi-buildings',
        ];

        $lowerKey = strtolower(trim($permission));
        if (isset($icons[$lowerKey])) {
            return $icons[$lowerKey];
        }

        $module = explode('.', $lowerKey)[0] ?? '';

        return $icons[$module] ?? 'bi bi-circle';
    }
}

if (! function_exists('nav_resolve_module_file')) {
    /**
     * ค้นหาชื่อไฟล์ภาษาตาม Module Key หรือ Permission
     *
     * @internal
     */
    function nav_resolve_module_file(string $permission): string
    {
        $clean  = strtolower(trim($permission));
        $prefix = str_contains($clean, '.') ? explode('.', $clean)[0] : $clean;

        $map = [
            'dashboard'  => 'Dashboard',
            'pos'        => 'Sales',
            'sale'       => 'Sales',
            'sales'      => 'Sales',
            'item'       => 'Items',
            'items'      => 'Items',
            'product'    => 'Items',
            'products'   => 'Items',
            'category'   => 'Items',
            'categories' => 'Items',
            'inventory'  => 'Items',
            'customer'   => 'Sales',
            'customers'  => 'Sales',
            'user'       => 'Users',
            'users'      => 'Users',
            'report'     => 'Reports',
            'reports'    => 'Reports',
            'setting'    => 'Settings',
            'settings'   => 'Settings',
            'admin'      => 'Settings',
            'branch'     => 'Settings',
            'branches'   => 'Settings',
            'tenant'     => 'Tenants',
            'tenants'    => 'Tenants',
        ];

        return $map[$prefix] ?? ucfirst($prefix);
    }
}

if (! function_exists('nav_resolve_label')) {
    /**
     * ดึงข้อความ Label อัตโนมัติจากไฟล์ระบบภาษาแยกตามโมดูล
     * กติกา: ดึงตรงตามชื่อ key เช่น navItem('dashboard') -> lang('Dashboard.dashboard')
     *
     * @internal
     */
    function nav_resolve_label(string $permission, ?string $customLabel = null): string
    {
        if ($customLabel !== null && $customLabel !== '') {
            return $customLabel;
        }

        $key  = trim($permission);
        $file = nav_resolve_module_file($key);

        $candidateKeys = [$key, strtolower($key)];

        if (! str_contains($key, '.')) {
            $candidateKeys[] = str_ends_with($key, 's') ? rtrim($key, 's') : ($key . 's');
        } else {
            $parts = explode('.', $key);
            if (isset($parts[1])) {
                $candidateKeys[] = $parts[1];
            }
        }

        $candidateKeys = array_values(array_unique(array_filter($candidateKeys)));

        foreach ($candidateKeys as $cKey) {
            $text = lang("{$file}.{$cKey}");
            if ($text !== "{$file}.{$cKey}") {
                return $text;
            }

            $textTh = lang("{$file}.{$cKey}", [], 'th');
            if ($textTh !== "{$file}.{$cKey}") {
                return $textTh;
            }

            $textEn = lang("{$file}.{$cKey}", [], 'en');
            if ($textEn !== "{$file}.{$cKey}") {
                return $textEn;
            }
        }

        return ucwords(str_replace(['.', '_', '-'], ' ', $key));
    }
}

if (! function_exists('nav_build_attrs')) {
    /**
     * สร้างสตริง HTML Attributes จาก Array
     *
     * @internal
     * @param array<string, mixed> $attrs
     */
    function nav_build_attrs(array $attrs): string
    {
        $html = '';
        foreach ($attrs as $key => $val) {
            if ($val === true) {
                $html .= ' ' . esc($key);
            } elseif ($val !== false && $val !== null) {
                $html .= ' ' . esc($key) . '="' . esc((string) $val, 'attr') . '"';
            }
        }

        return $html;
    }
}

if (! function_exists('_nav_render')) {
    /**
     * Core Renderer ร่วมสำหรับ navLink, navItem, dropdownItem, navButton
     *
     * @internal
     * @param string $type ประเภทการแสดงผล 'nav-link', 'nav-item', 'dropdown-item', 'button'
     * @param string|null $permission ชื่อสิทธิ์หรือโมดูล (ส่ง null เพื่อข้ามการเช็คสิทธิ์)
     * @param string|array<string, mixed> $urlOrOptions URL หรือ array options
     * @param array<string, mixed> $options ตัวเลือกเพิ่มเติม
     */
    function _nav_render(string $type, ?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
            $url     = $options['url'] ?? '#';
        } else {
            $url = $urlOrOptions;
        }

        // 1. ตรวจสอบสิทธิ์แบบ Contextual RBAC (หากไม่ได้ระบุ permission หรือตั้ง auth => false ให้ข้ามการตรวจสิทธิ์)
        $requireAuth = $options['auth'] ?? true;
        if ($requireAuth && $permission !== null && $permission !== '' && $permission !== '*' && $permission !== 'public') {
            $targetPermission = nav_resolve_module($permission);
            if (! auth_can($targetPermission)) {
                return '';
            }
        }

        // 2. Resolve Label & Icon
        $label = ($permission !== null && $permission !== '')
            ? nav_resolve_label($permission, $options['label'] ?? null)
            : ($options['label'] ?? '');

        $icon = null;
        if (! array_key_exists('icon', $options)) {
            $icon = ($permission !== null && $permission !== '') ? nav_default_icon($permission) : null;
        } elseif ($options['icon'] !== false && $options['icon'] !== null && $options['icon'] !== '') {
            $icon = (string) $options['icon'];
        }

        // 3. Resolve URL
        $targetUrl = '#';
        if ($url !== '#' && $url !== '') {
            $targetUrl = preg_match('/^(https?:\/\/|\/\/|#|javascript:)/i', $url) ? $url : site_url(ltrim($url, '/'));
        }

        // 4. Custom Attributes & Classes
        $customAttrs = nav_build_attrs($options['attrs'] ?? []);
        $extraClass  = trim((string) ($options['class'] ?? ''));

        // 5. Icon & Badge HTML
        $spacing  = ($type === 'button' || $type === 'btn') ? 'me-1' : 'me-2';
        $iconHtml = $icon ? '<i class="' . esc($icon) . ' ' . $spacing . '"></i>' : '';

        $badgeHtml = '';
        if (! empty($options['badge'])) {
            $bText  = is_array($options['badge']) ? ($options['badge']['text'] ?? '') : (string) $options['badge'];
            $bClass = is_array($options['badge']) ? ($options['badge']['class'] ?? 'bg-danger') : 'bg-danger';
            if ($bText !== '') {
                $badgeHtml = ' <span class="badge ' . esc($bClass) . ' ms-1">' . esc($bText) . '</span>';
            }
        }

        $safeUrl   = esc($targetUrl);
        $safeLabel = esc($label);

        // 6. พ่น DOM ตามประเภทที่เลือก
        switch ($type) {
            case 'button':
            case 'btn':
                $btnClass = $extraClass !== '' ? $extraClass : 'btn btn-primary btn-sm';
                return '<a href="' . $safeUrl . '" class="' . esc($btnClass) . '"' . $customAttrs . '>'
                    . $iconHtml . '<span>' . $safeLabel . '</span>' . $badgeHtml
                    . '</a>';

            case 'dropdown-item':
                $itemClass = trim('dropdown-item ' . $extraClass);
                $dom = '<a href="' . $safeUrl . '" class="' . esc($itemClass) . '"' . $customAttrs . '>'
                    . $iconHtml . '<span>' . $safeLabel . '</span>' . $badgeHtml
                    . '</a>';
                return ! empty($options['wrapper']) ? '<li>' . $dom . '</li>' : $dom;

            case 'nav-item':
            case 'nav-link':
            default:
                $linkClass = trim('nav-link ' . $extraClass);
                $linkDom   = '<a href="' . $safeUrl . '" class="' . esc($linkClass) . '"' . $customAttrs . '>'
                    . $iconHtml . '<span>' . $safeLabel . '</span>' . $badgeHtml
                    . '</a>';

                $shouldWrap = ($type === 'nav-item') || ! empty($options['wrapper']);
                if (isset($options['wrapper']) && $options['wrapper'] === false) {
                    $shouldWrap = false;
                }

                if ($shouldWrap) {
                    $itemClass = trim('nav-item ' . ($options['itemClass'] ?? ''));
                    return '<li class="' . esc($itemClass) . '">' . $linkDom . '</li>';
                }

                return $linkDom;
        }
    }
}

// --------------------------------------------------------------------------
// Atomic Navigation Components (AdminLTE 4 Native)
// --------------------------------------------------------------------------

if (! function_exists('navLink')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.nav-link`
     */
    function navLink(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return _nav_render('nav-link', $permission, $urlOrOptions, $options);
    }
}

if (! function_exists('navItem')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.nav-item`
     * พ่น <li class="nav-item"><a class="nav-link" ...>...</a></li>
     */
    function navItem(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return _nav_render('nav-item', $permission, $urlOrOptions, $options);
    }
}

if (! function_exists('dropdownItem')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.dropdown-item`
     */
    function dropdownItem(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return _nav_render('dropdown-item', $permission, $urlOrOptions, $options);
    }
}

if (! function_exists('navButton')) {
    /**
     * สร้าง HTML สำหรับปุ่ม Action Button (AdminLTE / Bootstrap Native `.btn`)
     */
    function navButton(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return _nav_render('button', $permission, $urlOrOptions, $options);
    }
}

// --------------------------------------------------------------------------
// Page Header & Breadcrumb Components
// --------------------------------------------------------------------------

if (! function_exists('pageTitle')) {
    /**
     * แสดงผล Title, Subtitle และ Icon ของหน้าจอในรูปแบบ AdminLTE 4 Content Header
     */
    function pageTitle(string $title, string $subtitle = '', string $icon = ''): string
    {
        $html = '<div class="d-flex align-items-center gap-2">';

        if ($icon !== '') {
            $html .= '<i class="' . esc($icon) . ' fs-3 text-primary"></i>';
        }

        $html .= '<div>';
        $html .= '<h3 class="mb-0 fw-bold fs-4">' . esc($title) . '</h3>';

        if ($subtitle !== '') {
            $html .= '<small class="text-body-secondary">' . esc($subtitle) . '</small>';
        }

        $html .= '</div></div>';

        return $html;
    }
}

if (! function_exists('breadcrumb')) {
    /**
     * แสดงผล Breadcrumb ตามมาตรฐาน Bootstrap 5 / AdminLTE 4
     */
    function breadcrumb(array $items): string
    {
        if (empty($items)) {
            return '';
        }

        $html = '<nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 justify-content-sm-end">';

        $total = count($items);
        $i = 0;

        foreach ($items as $label => $url) {
            $i++;
            $isLast = ($i === $total) || ($url === null);

            if ($isLast) {
                $html .= '<li class="breadcrumb-item active" aria-current="page">' . esc($label) . '</li>';
            } else {
                $targetUrl = preg_match('/^(https?:\/\/|\/\/|#)/i', (string) $url) ? (string) $url : site_url(ltrim((string) $url, '/'));
                $html .= '<li class="breadcrumb-item"><a href="' . esc($targetUrl) . '" class="text-decoration-none text-body-secondary">' . esc($label) . '</a></li>';
            }
        }

        $html .= '</ol></nav>';

        return $html;
    }
}

if (! function_exists('pageHeader')) {
    /**
     * ควบคุมและแสดงผลส่วน AdminLTE `.app-content-header` ทั้งหมด (Title, Actions และ Breadcrumb)
     */
    function pageHeader(
        ?string $title = null,
        ?string $subtitle = null,
        ?string $icon = null,
        ?array $breadcrumbs = null,
        $actions = null
    ): string {
        $titleHtml = '';
        if ($title !== null && $title !== '') {
            $titleHtml = pageTitle($title, $subtitle ?? '', $icon ?? '');
        }

        $breadcrumbHtml = '';
        if (! empty($breadcrumbs)) {
            $breadcrumbHtml = breadcrumb($breadcrumbs);
        }

        $actionsHtml = '';
        if (! empty($actions)) {
            if (is_array($actions)) {
                $actionItems = array_filter(array_map('trim', $actions));
                if (! empty($actionItems)) {
                    $actionsHtml = '<div class="d-flex align-items-center gap-2 justify-content-sm-end flex-wrap">'
                        . implode('', $actionItems)
                        . '</div>';
                }
            } else {
                $actionsHtml = trim((string) $actions);
            }
        }

        return '<div class="app-content-header py-3 border-bottom border-body-secondary bg-body mb-3">'
            . '<div class="container-fluid">'
            . '<div class="row align-items-center gy-2">'
            . '<div class="col-sm-6">' . $titleHtml . '</div>'
            . '<div class="col-sm-6 text-sm-end d-flex flex-column align-items-sm-end gap-1">'
            . $breadcrumbHtml
            . $actionsHtml
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';
    }
}

// --------------------------------------------------------------------------
// Sidebar Menu & Treeview Components (AdminLTE 4)
// --------------------------------------------------------------------------

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

if (! function_exists('get_admin_menu_items')) {
    /**
     * คืนค่าโครงสร้างเมนูทั้งหมดของระบบ Admin Sidebar
     * สามารถระบุเฉพาะ 'permission' เพื่อให้ระบบดึง Title และ Icon อัตโนมัติได้
     *
     * @return array<int, array<string, mixed>>
     */
    function get_admin_menu_items(): array
    {
        return [
            // 1. Dashboard (แดชบอร์ด)
            [
                'permission' => 'dashboard',
                'url'        => 'dashboard',
                'auth'       => false, // แดชบอร์ดแสดงผลเสมอ
            ],

            // 2. Tenants (ผู้เช่า & องค์กร)
            [
                'permission' => 'tenants',
                'children'   => [
                    [
                        'permission' => 'tenant.view.*',
                        'title'      => 'รายชื่อองค์กร / ผู้เช่า',
                        'url'        => 'admin/tenants',
                        'icon'       => 'bi bi-buildings',
                    ],
                    [
                        'permission' => 'tenant.create.*',
                        'title'      => 'ลงทะเบียนผู้เช่าใหม่',
                        'url'        => 'admin/tenants/new',
                        'icon'       => 'bi bi-building-add',
                    ],
                ],
            ],

            // 3. Users (ผู้ใช้งาน & พนักงาน)
            [
                'permission' => 'users',
                'children'   => [
                    [
                        'permission' => 'user.view.all',
                        'title'      => 'รายชื่อพนักงาน',
                        'url'        => 'admin/users',
                        'icon'       => 'bi bi-people',
                    ],
                    [
                        'permission' => 'user.manage.all',
                        'title'      => 'จัดการสิทธิ์ & บทบาท',
                        'url'        => 'admin/roles',
                        'icon'       => 'bi bi-shield-lock',
                    ],
                ],
            ],

            // 4. Items (สินค้า & สต็อก)
            [
                'permission' => 'items',
                'children'   => [
                    [
                        'permission' => 'product.view.all',
                        'title'      => 'รายการสินค้า',
                        'url'        => 'admin/products',
                        'icon'       => 'bi bi-tags',
                    ],
                    [
                        'permission' => 'product.view.all',
                        'title'      => 'หมวดหมู่สินค้า',
                        'url'        => 'admin/categories',
                        'icon'       => 'bi bi-grid',
                    ],
                    [
                        'permission' => 'inventory.view.*',
                        'title'      => 'สต็อกคงเหลือ',
                        'url'        => 'admin/inventory',
                        'icon'       => 'bi bi-boxes',
                    ],
                ],
            ],

            // 5. Sales (การขาย & POS)
            [
                'permission' => 'sales',
                'children'   => [
                    [
                        'permission' => 'sale.create.branch',
                        'title'      => 'เปิดบิลขายหน้าร้าน (POS)',
                        'url'        => 'admin/pos',
                        'icon'       => 'bi bi-shop',
                    ],
                    [
                        'permission' => 'sale.view.*',
                        'title'      => 'รายการขายทั้งหมด',
                        'url'        => 'admin/sales',
                        'icon'       => 'bi bi-receipt',
                    ],
                    [
                        'permission' => 'customer.view.*',
                        'title'      => 'ข้อมูลลูกค้า & สมาชิก',
                        'url'        => 'admin/customers',
                        'icon'       => 'bi bi-person-lines-fill',
                    ],
                ],
            ],

            // 6. Settings (ตั้งค่าระบบ)
            [
                'permission' => 'settings',
                'children'   => [
                    [
                        'permission' => 'admin.settings',
                        'title'      => 'ตั้งค่าทั่วไป',
                        'url'        => 'admin/settings',
                        'icon'       => 'bi bi-sliders',
                    ],
                    [
                        'permission' => 'branch.view.all',
                        'title'      => 'จัดการสาขา',
                        'url'        => 'admin/branches',
                        'icon'       => 'bi bi-building',
                    ],
                ],
            ],
        ];
    }
}

if (! function_exists('sidebarMenu')) {
    /**
     * Render โครงสร้างเมนู Sidebar ของ AdminLTE 4 โดยกรองสิทธิ์ Contextual RBAC อัตโนมัติ
     *
     * @param array<int, array<string, mixed>>|null $menuItems โครงสร้างเมนู (หากไม่ระบุจะดึง get_admin_menu_items())
     */
    function sidebarMenu(?array $menuItems = null): string
    {
        $items = $menuItems ?? get_admin_menu_items();
        $html  = '<ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">' . "\n";

        foreach ($items as $item) {
            // ประเภท Header คั่นส่วน
            if (($item['type'] ?? '') === 'header') {
                $html .= '<li class="nav-header">' . esc($item['title'] ?? '') . '</li>' . "\n";
                continue;
            }

            $permission   = $item['permission'] ?? null;
            $requiresAuth = $item['auth'] ?? ($permission !== null && $permission !== '');

            // ตรวจสอบสิทธิ์เมนูหลัก
            if ($requiresAuth && ! auth_can(nav_resolve_module((string) $permission))) {
                continue;
            }

            $title = $item['title'] ?? ($permission ? nav_resolve_label($permission) : '');
            $icon  = $item['icon'] ?? ($permission ? nav_default_icon($permission) : 'bi bi-circle');

            // กรณีมีเมนูย่อย (Submenu / Treeview)
            if (! empty($item['children'])) {
                $visibleChildren = [];
                $hasActiveChild  = false;

                foreach ($item['children'] as $child) {
                    $childPerm = $child['permission'] ?? null;
                    if ($childPerm === null || $childPerm === '' || auth_can(nav_resolve_module($childPerm))) {
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
                $safeIcon     = esc($icon);
                $safeTitle    = esc($title);

                $html .= "<li class=\"nav-item{$parentActive}\">\n";
                $html .= "    <a href=\"#\" class=\"nav-link{$linkActive}\">\n";
                $html .= "        <i class=\"nav-icon {$safeIcon}\"></i>\n";
                $html .= "        <p>{$safeTitle}<i class=\"nav-arrow bi bi-chevron-right\"></i></p>\n";
                $html .= "    </a>\n";
                $html .= "    <ul class=\"nav nav-treeview\">\n";

                foreach ($visibleChildren as $child) {
                    $childActive = ! empty($child['_active']) ? ' active' : '';
                    $childUrl    = site_url($child['url'] ?? '#');
                    $childPerm   = $child['permission'] ?? null;
                    $childTitle  = esc($child['title'] ?? ($childPerm ? nav_resolve_label($childPerm) : ''));
                    $childIcon   = esc($child['icon'] ?? ($childPerm ? nav_default_icon($childPerm) : 'bi bi-circle'));

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
                $safeIcon  = esc($icon);
                $safeTitle = esc($title);

                $html .= "<li class=\"nav-item\">\n";
                $html .= "    <a href=\"{$targetUrl}\" class=\"{$linkClass}\">\n";
                $html .= "        <i class=\"nav-icon {$safeIcon}\"></i>\n";
                $html .= "        <p>{$safeTitle}</p>\n";
                $html .= "    </a>\n";
                $html .= "</li>\n";
            }
        }

        $html .= "</ul>\n";

        return $html;
    }
}
