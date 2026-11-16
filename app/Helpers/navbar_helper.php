<?php

declare(strict_types=1);

/**
 * Navbar & Header Helper สำหรับ AdminLTE 4 และ Bootstrap 5
 * ฟังก์ชันได้รับการตั้งชื่อให้สอดคล้องกับ Native Class ของ AdminLTE 4 เช่น navItem(), navLink(), dropdownItem(), breadcrumb(), pageHeader()
 * รองรับการตรวจสอบสิทธิ์แบบ Contextual RBAC (Permission-First), Auto-Language (i18n),
 * Module Aliases, Default Icons และการสร้าง Page Header / Breadcrumb อัตโนมัติ
 */

if (! function_exists('navbar_resolve_module')) {
    /**
     * แมปชื่อโมดูล (Module Key) ไปเป็น Permission Wildcard
     *
     * @internal
     */
    function navbar_resolve_module(string $permission): string
    {
        $permission = trim($permission);

        // หากมีจุดอยู่แล้ว แสดงว่าเป็น full permission key (เช่น sale.create.branch หรือ sale.*)
        if (str_contains($permission, '.')) {
            return $permission;
        }

        $moduleMap = [
            'sales'      => 'sale.*',
            'sale'       => 'sale.*',
            'items'      => 'product.*',
            'item'       => 'product.*',
            'products'   => 'product.*',
            'product'    => 'product.*',
            'categories' => 'product.*',
            'category'   => 'product.*',
            'inventory'  => 'inventory.*',
            'customers'  => 'customer.*',
            'customer'   => 'customer.*',
            'users'      => 'user.*',
            'user'       => 'user.*',
            'branches'   => 'branch.*',
            'branch'     => 'branch.*',
            'reports'    => 'report.*',
            'report'     => 'report.*',
            'settings'   => 'admin.settings',
            'setting'    => 'admin.settings',
            'tenants'    => 'tenant.*',
            'tenant'     => 'tenant.*',
            'pos'        => 'sale.create.branch',
            'dashboard'  => 'admin.access',
        ];

        return $moduleMap[strtolower($permission)] ?? (strtolower($permission) . '.*');
    }
}

if (! function_exists('navbar_default_icon')) {
    /**
     * ค้นหาไอคอนเริ่มต้นประจำโมดูล (Default Module Icon)
     *
     * @internal
     */
    function navbar_default_icon(string $permission): string
    {
        $icons = [
            'sales'      => 'bi bi-cart3',
            'sale'       => 'bi bi-cart3',
            'items'      => 'bi bi-box-seam',
            'item'       => 'bi bi-box-seam',
            'products'   => 'bi bi-box-seam',
            'product'    => 'bi bi-box-seam',
            'categories' => 'bi bi-tags',
            'category'   => 'bi bi-tags',
            'inventory'  => 'bi bi-boxes',
            'customers'  => 'bi bi-people',
            'customer'   => 'bi bi-people',
            'users'      => 'bi bi-person-gear',
            'user'       => 'bi bi-person-gear',
            'branches'   => 'bi bi-building',
            'branch'     => 'bi bi-building',
            'reports'    => 'bi bi-graph-up-arrow',
            'report'     => 'bi bi-graph-up-arrow',
            'settings'   => 'bi bi-gear',
            'setting'    => 'bi bi-gear',
            'tenants'    => 'bi bi-buildings',
            'tenant'     => 'bi bi-buildings',
            'pos'        => 'bi bi-shop',
            'dashboard'  => 'bi bi-speedometer2',
        ];

        $lowerKey = strtolower(trim($permission));

        if (isset($icons[$lowerKey])) {
            return $icons[$lowerKey];
        }

        // หากเป็น permission ย่อย เช่น sale.create.branch ให้ตัดคำหน้าโมดูลมาหาไอคอน
        $parts = explode('.', $lowerKey);
        $module = $parts[0] ?? '';

        return $icons[$module] ?? 'bi bi-circle';
    }
}

if (! function_exists('navbar_resolve_module_file')) {
    /**
     * ค้นหาชื่อไฟล์ภาษาตาม Module Key หรือ Permission
     * เช่น sales -> Sales, items -> Items, sale.create.branch -> Sales
     *
     * @internal
     */
    function navbar_resolve_module_file(string $permission): string
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
            'user'       => 'Users',
            'users'      => 'Users',
            'report'     => 'Reports',
            'reports'    => 'Reports',
            'setting'    => 'Settings',
            'settings'   => 'Settings',
            'admin'      => 'Settings',
            'branch'     => 'Settings',
            'branches'   => 'Settings',
        ];

        return $map[$prefix] ?? ucfirst($prefix);
    }
}

if (! function_exists('navbar_resolve_label')) {
    /**
     * ดึงข้อความ Label อัตโนมัติจากไฟล์ระบบภาษาแยกตามโมดูล (Dashboard, Users, Items, Sales, Reports, Settings)
     *
     * @internal
     */
    function navbar_resolve_label(string $permission, ?string $customLabel = null): string
    {
        if ($customLabel !== null && $customLabel !== '') {
            return $customLabel;
        }

        $key  = trim($permission);
        $file = navbar_resolve_module_file($key);

        // รายการคีย์ที่ต้องการลองค้นหาในไฟล์ภาษาประจำโมดูล
        $candidateKeys = [
            $key,                    // เช่น 'sale.create.branch' หรือ 'sales'
            'module_name',           // ชื่อโมดูลหลัก
            'title',                 // ชื่อหัวข้อโมดูล
            strtolower($key),        // lowercase
        ];

        // ถ้าเป็น granular key เช่น sale.create.branch ให้ลองตัดคำ action เช่น 'create'
        if (str_contains($key, '.')) {
            $parts = explode('.', $key);
            if (isset($parts[1])) {
                $candidateKeys[] = $parts[1]; // เช่น 'create', 'view'
            }
        }

        // ค้นหาในไฟล์ประจำโมดูล
        foreach ($candidateKeys as $cKey) {
            $text = lang("{$file}.{$cKey}");
            if ($text !== "{$file}.{$cKey}") {
                return $text;
            }

            // ลอง fallback ภาษาไทย
            $textTh = lang("{$file}.{$cKey}", [], 'th');
            if ($textTh !== "{$file}.{$cKey}") {
                return $textTh;
            }

            // ลอง fallback ภาษาอังกฤษ
            $textEn = lang("{$file}.{$cKey}", [], 'en');
            if ($textEn !== "{$file}.{$cKey}") {
                return $textEn;
            }
        }

        // Fallback: แปลง slug/key เป็น Title Case ที่อ่านง่าย
        return ucwords(str_replace(['.', '_', '-'], ' ', $key));
    }
}

if (! function_exists('navbar_build_attrs')) {
    /**
     * สร้างสตริง HTML Attributes จาก Array
     *
     * @internal
     * @param array<string, mixed> $attrs
     */
    function navbar_build_attrs(array $attrs): string
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

if (! function_exists('navLink')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.nav-link`
     * ตรวจสอบสิทธิ์แบบ Permission-First อัตโนมัติ (ไม่แสดงหากไม่มีสิทธิ์)
     *
     * @param string $permission ชื่อสิทธิ์หรือโมดูล เช่น 'sales', 'sale.create.branch'
     * @param string|array<string, mixed> $urlOrOptions URL หรือ array ตัวเลือก
     * @param array<string, mixed> $options ตัวเลือกเพิ่มเติม
     */
    function navLink(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
            $url     = $options['url'] ?? '#';
        } else {
            $url = $urlOrOptions;
        }

        // 1. ตรวจสอบสิทธิ์
        $targetPermission = navbar_resolve_module($permission);
        if (! auth_can($targetPermission)) {
            return '';
        }

        // 2. Resolve Label & Icon
        $label = navbar_resolve_label($permission, $options['label'] ?? null);

        $icon = null;
        if (! array_key_exists('icon', $options)) {
            $icon = navbar_default_icon($permission);
        } elseif ($options['icon'] !== false && $options['icon'] !== null && $options['icon'] !== '') {
            $icon = (string) $options['icon'];
        }

        // 3. Resolve URL
        $targetUrl = '#';
        if ($url !== '#' && $url !== '') {
            $targetUrl = preg_match('/^(https?:\/\/|\/\/|#|javascript:)/i', $url) ? $url : site_url(ltrim($url, '/'));
        }

        // 4. Classes & Attributes
        $extraClass = trim((string) ($options['class'] ?? ''));
        $linkClass  = trim('nav-link ' . $extraClass);
        $customAttrs = navbar_build_attrs($options['attrs'] ?? []);

        // 5. Icon & Badge
        $iconHtml = $icon ? '<i class="' . esc($icon) . ' me-2"></i>' : '';
        $badgeHtml = '';
        if (! empty($options['badge'])) {
            if (is_string($options['badge'])) {
                $badgeHtml = ' <span class="badge bg-danger ms-1">' . esc($options['badge']) . '</span>';
            } elseif (is_array($options['badge'])) {
                $bText  = $options['badge']['text'] ?? '';
                $bClass = $options['badge']['class'] ?? 'bg-danger';
                if ($bText !== '') {
                    $badgeHtml = ' <span class="badge ' . esc($bClass) . ' ms-1">' . esc($bText) . '</span>';
                }
            }
        }

        return '<a href="' . esc($targetUrl) . '" class="' . esc($linkClass) . '"' . $customAttrs . '>'
            . $iconHtml . '<span>' . esc($label) . '</span>' . $badgeHtml
            . '</a>';
    }
}

if (! function_exists('navItem')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.nav-item`
     * พ่น <li class="nav-item"><a class="nav-link" ...>...</a></li>
     *
     * @param string $permission ชื่อสิทธิ์หรือโมดูล เช่น 'sales', 'users'
     * @param string|array<string, mixed> $urlOrOptions URL หรือ array ตัวเลือก
     * @param array<string, mixed> $options ตัวเลือกเพิ่มเติม
     *   - 'itemClass': คลาสสำหรับ <li>
     *   - 'wrapper': ถ้าส่ง false จะพ่นเฉพาะ <a> (เช่นเดียวกับ navLink)
     */
    function navItem(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
        }

        $linkDom = navLink($permission, $urlOrOptions, $options);
        if ($linkDom === '') {
            return '';
        }

        if (isset($options['wrapper']) && $options['wrapper'] === false) {
            return $linkDom;
        }

        $itemClass = trim('nav-item ' . ($options['itemClass'] ?? ''));

        return '<li class="' . esc($itemClass) . '">' . $linkDom . '</li>';
    }
}

if (! function_exists('dropdownItem')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.dropdown-item`
     *
     * @param string $permission ชื่อสิทธิ์หรือโมดูล
     * @param string|array<string, mixed> $urlOrOptions URL หรือ array ตัวเลือก
     * @param array<string, mixed> $options ตัวเลือกเพิ่มเติม
     */
    function dropdownItem(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
            $url     = $options['url'] ?? '#';
        } else {
            $url = $urlOrOptions;
        }

        $targetPermission = navbar_resolve_module($permission);
        if (! auth_can($targetPermission)) {
            return '';
        }

        $label = navbar_resolve_label($permission, $options['label'] ?? null);

        $icon = null;
        if (! array_key_exists('icon', $options)) {
            $icon = navbar_default_icon($permission);
        } elseif ($options['icon'] !== false && $options['icon'] !== null && $options['icon'] !== '') {
            $icon = (string) $options['icon'];
        }

        $targetUrl = '#';
        if ($url !== '#' && $url !== '') {
            $targetUrl = preg_match('/^(https?:\/\/|\/\/|#|javascript:)/i', $url) ? $url : site_url(ltrim($url, '/'));
        }

        $extraClass  = trim((string) ($options['class'] ?? ''));
        $itemClass   = trim('dropdown-item ' . $extraClass);
        $customAttrs = navbar_build_attrs($options['attrs'] ?? []);
        $iconHtml    = $icon ? '<i class="' . esc($icon) . ' me-2"></i>' : '';

        $badgeHtml = '';
        if (! empty($options['badge'])) {
            $bText  = is_array($options['badge']) ? ($options['badge']['text'] ?? '') : (string) $options['badge'];
            $bClass = is_array($options['badge']) ? ($options['badge']['class'] ?? 'bg-danger') : 'bg-danger';
            if ($bText !== '') {
                $badgeHtml = ' <span class="badge ' . esc($bClass) . ' ms-1">' . esc($bText) . '</span>';
            }
        }

        $dom = '<a href="' . esc($targetUrl) . '" class="' . esc($itemClass) . '"' . $customAttrs . '>'
            . $iconHtml . '<span>' . esc($label) . '</span>' . $badgeHtml
            . '</a>';

        if (! empty($options['wrapper'])) {
            return '<li>' . $dom . '</li>';
        }

        return $dom;
    }
}

if (! function_exists('navButton')) {
    /**
     * สร้าง HTML สำหรับปุ่ม Action Button ใน Page Header หรือเนื้อหา (AdminLTE / Bootstrap Native `.btn`)
     *
     * @param string $permission ชื่อสิทธิ์หรือโมดูล
     * @param string|array<string, mixed> $urlOrOptions URL หรือ array ตัวเลือก
     * @param array<string, mixed> $options ตัวเลือกเพิ่มเติม
     */
    function navButton(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
            $url     = $options['url'] ?? '#';
        } else {
            $url = $urlOrOptions;
        }

        $targetPermission = navbar_resolve_module($permission);
        if (! auth_can($targetPermission)) {
            return '';
        }

        $label = navbar_resolve_label($permission, $options['label'] ?? null);

        $icon = null;
        if (! array_key_exists('icon', $options)) {
            $icon = navbar_default_icon($permission);
        } elseif ($options['icon'] !== false && $options['icon'] !== null && $options['icon'] !== '') {
            $icon = (string) $options['icon'];
        }

        $targetUrl = '#';
        if ($url !== '#' && $url !== '') {
            $targetUrl = preg_match('/^(https?:\/\/|\/\/|#|javascript:)/i', $url) ? $url : site_url(ltrim($url, '/'));
        }

        $extraClass  = trim((string) ($options['class'] ?? ''));
        $btnClass    = $extraClass !== '' ? $extraClass : 'btn btn-primary btn-sm';
        $customAttrs = navbar_build_attrs($options['attrs'] ?? []);
        $iconHtml    = $icon ? '<i class="' . esc($icon) . ' me-1"></i>' : '';

        $badgeHtml = '';
        if (! empty($options['badge'])) {
            $bText  = is_array($options['badge']) ? ($options['badge']['text'] ?? '') : (string) $options['badge'];
            $bClass = is_array($options['badge']) ? ($options['badge']['class'] ?? 'bg-danger') : 'bg-danger';
            if ($bText !== '') {
                $badgeHtml = ' <span class="badge ' . esc($bClass) . ' ms-1">' . esc($bText) . '</span>';
            }
        }

        return '<a href="' . esc($targetUrl) . '" class="' . esc($btnClass) . '"' . $customAttrs . '>'
            . $iconHtml . '<span>' . esc($label) . '</span>' . $badgeHtml
            . '</a>';
    }
}

if (! function_exists('navbar')) {
    /**
     * ฟังก์ชันแม่แบบอเนกประสงค์ (Smart Wrapper / Alias)
     * สลับการทำงานไปยัง navItem(), navLink(), dropdownItem() หรือ navButton() ตาม option 'as'
     *
     * @param string $permission ชื่อสิทธิ์หรือโมดูล
     * @param string|array<string, mixed> $urlOrOptions URL หรือ array ตัวเลือก
     * @param array<string, mixed> $options ตัวเลือกเพิ่มเติม ('as' => 'nav-item' | 'nav-link' | 'dropdown-item' | 'button')
     */
    function navbar(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
        }

        $as = strtolower((string) ($options['as'] ?? $options['type'] ?? 'nav-link'));

        switch ($as) {
            case 'nav-item':
                return navItem($permission, $urlOrOptions, $options);

            case 'button':
            case 'btn':
                return navButton($permission, $urlOrOptions, $options);

            case 'dropdown-item':
                return dropdownItem($permission, $urlOrOptions, $options);

            case 'link':
            case 'a':
            case 'nav-link':
            default:
                if (! empty($options['wrapper'])) {
                    return navItem($permission, $urlOrOptions, $options);
                }
                return navLink($permission, $urlOrOptions, $options);
        }
    }
}

if (! function_exists('pageTitle')) {
    /**
     * แสดงผล Title, Subtitle และ Icon ของหน้าจอในรูปแบบ AdminLTE 4 Content Header
     *
     * @param string $title ชื่อหลักของหน้าจอ เช่น 'ภาพรวมระบบและการขาย'
     * @param string $subtitle คำอธิบายสั้นๆ ใต้ชื่อหน้า
     * @param string $icon คลาสไอคอน เช่น 'bi bi-speedometer2'
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
     *
     * ตัวอย่าง:
     * breadcrumb([
     *     'หน้าหลัก' => 'dashboard',
     *     'การขาย'   => 'admin/sales',
     *     'เปิดบิล'  => null, // null คือหน้าปัจจุบัน
     * ])
     *
     * @param array<string, string|null> $items
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
     *
     * @param string|null $title ชื่อหน้าจอ
     * @param string|null $subtitle คำอธิบายหน้าจอ
     * @param string|null $icon ไอคอนหน้าจอ
     * @param array<string, string|null>|null $breadcrumbs รายการ Breadcrumbs
     * @param list<string>|string|null $actions รายการปุ่ม Action ฝั่งขวา (สร้างจาก navButton() หรือ string HTML)
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
// Backward Compatibility & Style Aliases (CamelCase & Snake_case)
// --------------------------------------------------------------------------
if (! function_exists('nav_item')) {
    function nav_item(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        return navItem($permission, $urlOrOptions, $options);
    }
}

if (! function_exists('nav_link')) {
    function nav_link(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        return navLink($permission, $urlOrOptions, $options);
    }
}

if (! function_exists('dropdown_item')) {
    function dropdown_item(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        return dropdownItem($permission, $urlOrOptions, $options);
    }
}

if (! function_exists('nav_button')) {
    function nav_button(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        return navButton($permission, $urlOrOptions, $options);
    }
}

if (! function_exists('page_title')) {
    function page_title(string $title, string $subtitle = '', string $icon = ''): string
    {
        return pageTitle($title, $subtitle, $icon);
    }
}

if (! function_exists('page_header_title')) {
    function page_header_title(string $title, string $subtitle = '', string $icon = ''): string
    {
        return pageTitle($title, $subtitle, $icon);
    }
}

if (! function_exists('breadcrumbs')) {
    function breadcrumbs(array $items): string
    {
        return breadcrumb($items);
    }
}

if (! function_exists('render_breadcrumbs')) {
    function render_breadcrumbs(array $items): string
    {
        return breadcrumb($items);
    }
}

if (! function_exists('page_header')) {
    function page_header(
        ?string $title = null,
        ?string $subtitle = null,
        ?string $icon = null,
        ?array $breadcrumbs = null,
        $actions = null
    ): string {
        return pageHeader($title, $subtitle, $icon, $breadcrumbs, $actions);
    }
}

if (! function_exists('render_page_header')) {
    function render_page_header(
        ?string $title = null,
        ?string $subtitle = null,
        ?string $icon = null,
        ?array $breadcrumbs = null,
        $actions = null
    ): string {
        return pageHeader($title, $subtitle, $icon, $breadcrumbs, $actions);
    }
}
