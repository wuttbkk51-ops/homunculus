<?php

declare(strict_types=1);

/**
 * Navbar & Header Helper สำหรับ AdminLTE 4 และ Bootstrap 5
 * ฟังก์ชันสอดคล้องกับ Native Class เช่น navItem(), navLink(), dropdownItem(), navButton(), breadcrumb(), pageHeader()
 * รองรับการตรวจสอบสิทธิ์ Contextual RBAC (Permission-First), Modular i18n, Default Icons
 */

if (! function_exists('navbar_resolve_module')) {
    /**
     * แมปชื่อโมดูล (Module Key) ไปเป็น Permission Wildcard
     *
     * @internal
     */
    function navbar_resolve_module(string $permission): string
    {
        $clean = trim($permission);
        if (str_contains($clean, '.')) {
            return $clean;
        }

        $moduleMap = [
            'sales'      => 'sale.*',
            'sale'       => 'sale.*',
            'pos'        => 'sale.create.branch',
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
            'dashboard'  => 'admin.access',
        ];

        return $moduleMap[strtolower($clean)] ?? (strtolower($clean) . '.*');
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
            'pos'        => 'bi bi-shop',
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
            'dashboard'  => 'bi bi-speedometer2',
        ];

        $lowerKey = strtolower(trim($permission));
        if (isset($icons[$lowerKey])) {
            return $icons[$lowerKey];
        }

        $module = explode('.', $lowerKey)[0] ?? '';

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
     * ดึงข้อความ Label อัตโนมัติจากไฟล์ระบบภาษาแยกตามโมดูล
     * กติกา: ดึงตรงตามชื่อ key เช่น navItem('dashboard') -> lang('Dashboard.dashboard')
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

if (! function_exists('_navbar_render')) {
    /**
     * Core Renderer ร่วมสำหรับ navLink, navItem, dropdownItem, navButton
     * รองรับทั้งกรณีตรวจสอบสิทธิ์แบบ Permission-First และกรณีที่ไม่ต้องตรวจสอบสิทธิ์ (auth => false หรือ permission => null)
     * ป้องกันโค้ดซ้ำซ้อน (DRY) และรวมการตรวจสอบความปลอดภัย / XSS ไว้ที่เดียว
     *
     * @internal
     * @param string $type ประเภทการแสดงผล 'nav-link', 'nav-item', 'dropdown-item', 'button'
     * @param string|null $permission ชื่อสิทธิ์หรือโมดูล (ส่ง null เพื่อข้ามการเช็คสิทธิ์)
     * @param string|array<string, mixed> $urlOrOptions URL หรือ array options
     * @param array<string, mixed> $options ตัวเลือกเพิ่มเติม
     */
    function _navbar_render(string $type, ?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
            $url     = $options['url'] ?? '#';
        } else {
            $url = $urlOrOptions;
        }

        // 1. ตรวจสอบสิทธิ์แบบ Contextual RBAC (ถ้าไม่ได้ส่ง permission หรือตั้ง 'auth' => false ให้ข้ามการตรวจสิทธิ์)
        $requireAuth = $options['auth'] ?? true;
        if ($requireAuth && $permission !== null && $permission !== '' && $permission !== '*' && $permission !== 'public') {
            $targetPermission = navbar_resolve_module($permission);
            if (! auth_can($targetPermission)) {
                return '';
            }
        }

        // 2. Resolve Label & Icon
        $label = ($permission !== null && $permission !== '')
            ? navbar_resolve_label($permission, $options['label'] ?? null)
            : ($options['label'] ?? '');

        $icon = null;
        if (! array_key_exists('icon', $options)) {
            $icon = ($permission !== null && $permission !== '') ? navbar_default_icon($permission) : null;
        } elseif ($options['icon'] !== false && $options['icon'] !== null && $options['icon'] !== '') {
            $icon = (string) $options['icon'];
        }

        // 3. Resolve URL
        $targetUrl = '#';
        if ($url !== '#' && $url !== '') {
            $targetUrl = preg_match('/^(https?:\/\/|\/\/|#|javascript:)/i', $url) ? $url : site_url(ltrim($url, '/'));
        }

        // 4. Custom Attributes & Classes
        $customAttrs = navbar_build_attrs($options['attrs'] ?? []);
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

if (! function_exists('navLink')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.nav-link`
     * ส่ง $permission เป็น null หรือใส่ 'auth' => false สำหรับลิงก์ทั่วไปที่ไม่ต้องเช็คสิทธิ์
     */
    function navLink(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return _navbar_render('nav-link', $permission, $urlOrOptions, $options);
    }
}

if (! function_exists('navItem')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.nav-item`
     * พ่น <li class="nav-item"><a class="nav-link" ...>...</a></li>
     * ส่ง $permission เป็น null หรือใส่ 'auth' => false สำหรับเมนูทั่วไปที่ไม่ต้องเช็คสิทธิ์
     */
    function navItem(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return _navbar_render('nav-item', $permission, $urlOrOptions, $options);
    }
}

if (! function_exists('dropdownItem')) {
    /**
     * สร้าง HTML สำหรับ AdminLTE / Bootstrap Native `.dropdown-item`
     * ส่ง $permission เป็น null หรือใส่ 'auth' => false สำหรับเมนูย่อยทั่วไปที่ไม่ต้องเช็คสิทธิ์
     */
    function dropdownItem(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return _navbar_render('dropdown-item', $permission, $urlOrOptions, $options);
    }
}

if (! function_exists('navButton')) {
    /**
     * สร้าง HTML สำหรับปุ่ม Action Button (AdminLTE / Bootstrap Native `.btn`)
     * ส่ง $permission เป็น null หรือใส่ 'auth' => false สำหรับปุ่มทั่วไปที่ไม่ต้องเช็คสิทธิ์
     */
    function navButton(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return _navbar_render('button', $permission, $urlOrOptions, $options);
    }
}

if (! function_exists('navbar')) {
    /**
     * ฟังก์ชันแม่แบบอเนกประสงค์ (Smart Wrapper / Alias)
     */
    function navbar(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
        }

        $as = strtolower((string) ($options['as'] ?? $options['type'] ?? 'nav-link'));

        return _navbar_render($as, $permission, $urlOrOptions, $options);
    }
}

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
// Backward Compatibility & Style Aliases (CamelCase & Snake_case)
// --------------------------------------------------------------------------
if (! function_exists('nav_item')) {
    function nav_item(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return navItem($permission, $urlOrOptions, $options);
    }
}

if (! function_exists('nav_link')) {
    function nav_link(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return navLink($permission, $urlOrOptions, $options);
    }
}

if (! function_exists('dropdown_item')) {
    function dropdown_item(?string $permission = null, $urlOrOptions = '#', array $options = []): string
    {
        return dropdownItem($permission, $urlOrOptions, $options);
    }
}

if (! function_exists('nav_button')) {
    function nav_button(?string $permission = null, $urlOrOptions = '#', array $options = []): string
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
