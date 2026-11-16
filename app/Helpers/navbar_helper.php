<?php

declare(strict_types=1);

/**
 * Navbar & Header Helper สำหรับ AdminLTE 4 และ Bootstrap 5
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

if (! function_exists('navbar_resolve_label')) {
    /**
     * ดึงข้อความ Label อัตโนมัติจากระบบภาษา lang() ตาม permission หรือ module key
     *
     * @internal
     */
    function navbar_resolve_label(string $permission, ?string $customLabel = null): string
    {
        if ($customLabel !== null && $customLabel !== '') {
            return $customLabel;
        }

        $key = trim($permission);

        // ดึงจากระบบภาษาปัจจุบัน
        $text = lang("Permissions.{$key}");

        // ถ้าหาไม่เจอใน locale ปัจจุบัน ให้ลอง fallback เป็นภาษาไทย หรืออังกฤษ
        if ($text === "Permissions.{$key}") {
            $text = lang("Permissions.{$key}", [], 'th');
        }
        if ($text === "Permissions.{$key}") {
            $text = lang("Permissions.{$key}", [], 'en');
        }

        // ถ้าพบคำแปล
        if ($text !== "Permissions.{$key}") {
            return $text;
        }

        // กรณีเป็นโมดูลเดี่ยว เช่น sale ลองหา sales หรือกลับกัน
        if (! str_contains($key, '.')) {
            $altKey = str_ends_with($key, 's') ? rtrim($key, 's') : ($key . 's');
            $altText = lang("Permissions.{$altKey}");
            if ($altText === "Permissions.{$altKey}") {
                $altText = lang("Permissions.{$altKey}", [], 'th');
            }
            if ($altText === "Permissions.{$altKey}") {
                $altText = lang("Permissions.{$altKey}", [], 'en');
            }

            if ($altText !== "Permissions.{$altKey}") {
                return $altText;
            }
        }

        // Fallback: แปลง slug/key เป็น Title Case ที่อ่านง่าย
        return ucwords(str_replace(['.', '_', '-'], ' ', $key));
    }
}

if (! function_exists('navbar')) {
    /**
     * แสดงผล HTML DOM สำหรับ Navigation Link, Button หรือ Dropdown Item
     * พร้อมตรวจสอบสิทธิ์ Contextual RBAC (Permission-First) และดึง Label จากระบบภาษาอัตโนมัติ
     *
     * ตัวอย่างการใช้งาน:
     * - navbar('sales', 'admin/sales')
     * - navbar('sale.create.branch', 'admin/pos', ['as' => 'button', 'icon' => 'bi bi-plus-lg'])
     * - navbar('users', 'admin/users', ['as' => 'dropdown-item'])
     *
     * @param string $permission ชื่อสิทธิ์ เช่น 'sales', 'items', 'sale.create.branch'
     * @param string|array<string, mixed> $urlOrOptions URL ปลายทาง หรือ array ของ options
     * @param array<string, mixed> $options ตัวเลือกเพิ่มเติม:
     *   - 'label': ข้อความระบุเจาะจง (หากไม่ใส่จะดึงจาก lang() อัตโนมัติ)
     *   - 'icon': คลาสไอคอน เช่น 'bi bi-cart' (หากไม่ใส่จะใช้ default icon ประจำโมดูล, ส่ง false เพื่อไม่แสดง)
     *   - 'as': ประเภทการแสดงผล 'nav-link' (ค่าเริ่มต้น), 'button', 'dropdown-item', 'link', 'nav-item'
     *   - 'class': คลาส CSS เพิ่มเติม
     *   - 'badge': array ['text' => 'ใหม่', 'class' => 'bg-danger']
     *   - 'attrs': array HTML attributes เพิ่มเติม เช่น ['data-bs-toggle' => 'modal']
     *   - 'wrapper': กำหนด true หากต้องการให้หุ้มด้วย <li class="nav-item">...</li> (เฉพาะ nav-link)
     *
     * @return string คืนค่า HTML DOM เมื่อมีสิทธิ์ หรือคืนค่า '' เมื่อไม่มีสิทธิ์
     */
    function navbar(string $permission, $urlOrOptions = '#', array $options = []): string
    {
        // แยกตัวแปร URL และ Options
        if (is_array($urlOrOptions)) {
            $options = array_merge($urlOrOptions, $options);
            $url     = $options['url'] ?? '#';
        } else {
            $url = $urlOrOptions;
        }

        // 1. ตรวจสอบสิทธิ์แบบ Permission-First
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
            if (preg_match('/^(https?:\/\/|\/\/|#|javascript:)/i', $url)) {
                $targetUrl = $url;
            } else {
                $targetUrl = site_url(ltrim($url, '/'));
            }
        }

        // 4. Resolve Presentation Style ('as')
        $as = strtolower((string) ($options['as'] ?? $options['type'] ?? 'nav-link'));
        $extraClass = trim((string) ($options['class'] ?? ''));

        // สร้าง HTML Attributes เพิ่มเติม
        $customAttrs = '';
        if (! empty($options['attrs']) && is_array($options['attrs'])) {
            foreach ($options['attrs'] as $attrKey => $attrVal) {
                if ($attrVal === true) {
                    $customAttrs .= ' ' . esc($attrKey);
                } elseif ($attrVal !== false && $attrVal !== null) {
                    $customAttrs .= ' ' . esc($attrKey) . '="' . esc((string) $attrVal, 'attr') . '"';
                }
            }
        }

        // สร้าง Icon HTML
        $iconSpacing = ($as === 'button' || $as === 'btn') ? 'me-1' : 'me-2';
        $iconHtml = $icon ? '<i class="' . esc($icon) . ' ' . $iconSpacing . '"></i>' : '';

        // สร้าง Badge HTML (ถ้ามี)
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

        $safeUrl   = esc($targetUrl);
        $safeLabel = esc($label);

        // 5. พ่น DOM ตามประเภทที่เลือก
        switch ($as) {
            case 'button':
            case 'btn':
                $btnClass = $extraClass !== '' ? $extraClass : 'btn btn-primary btn-sm';
                return '<a href="' . $safeUrl . '" class="' . esc($btnClass) . '"' . $customAttrs . '>'
                    . $iconHtml . '<span>' . $safeLabel . '</span>' . $badgeHtml
                    . '</a>';

            case 'dropdown-item':
                $itemClass = trim('dropdown-item ' . $extraClass);
                return '<a href="' . $safeUrl . '" class="' . esc($itemClass) . '"' . $customAttrs . '>'
                    . $iconHtml . '<span>' . $safeLabel . '</span>' . $badgeHtml
                    . '</a>';

            case 'link':
            case 'a':
                $linkClass = $extraClass !== '' ? ' class="' . esc($extraClass) . '"' : '';
                return '<a href="' . $safeUrl . '"' . $linkClass . $customAttrs . '>'
                    . $iconHtml . '<span>' . $safeLabel . '</span>' . $badgeHtml
                    . '</a>';

            case 'nav-item':
                $linkClass = trim('nav-link ' . $extraClass);
                return '<li class="nav-item">'
                    . '<a href="' . $safeUrl . '" class="' . esc($linkClass) . '"' . $customAttrs . '>'
                    . $iconHtml . '<span>' . $safeLabel . '</span>' . $badgeHtml
                    . '</a></li>';

            case 'nav-link':
            default:
                $linkClass = trim('nav-link ' . $extraClass);
                $linkDom   = '<a href="' . $safeUrl . '" class="' . esc($linkClass) . '"' . $customAttrs . '>'
                    . $iconHtml . '<span>' . $safeLabel . '</span>' . $badgeHtml
                    . '</a>';

                if (! empty($options['wrapper'])) {
                    return '<li class="nav-item">' . $linkDom . '</li>';
                }

                return $linkDom;
        }
    }
}

if (! function_exists('page_header_title')) {
    /**
     * แสดงผล Title, Subtitle และ Icon ของหน้าจอในรูปแบบมาตรฐาน
     *
     * @param string $title ชื่อหลักของหน้าจอ เช่น 'ภาพรวมระบบและการขาย'
     * @param string $subtitle คำอธิบายสั้นๆ ใต้ชื่อหน้า
     * @param string $icon คลาสไอคอน เช่น 'bi bi-speedometer2'
     */
    function page_header_title(string $title, string $subtitle = '', string $icon = ''): string
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

if (! function_exists('render_breadcrumbs')) {
    /**
     * แสดงผล Breadcrumb ตามมาตรฐาน Bootstrap 5
     *
     * รูปแบบตัวอย่าง:
     * render_breadcrumbs([
     *     'หน้าหลัก' => 'dashboard',
     *     'การขาย' => 'admin/sales',
     *     'เปิดบิลขาย' => null, // null คือหน้าปัจจุบัน (active)
     * ])
     *
     * @param array<string, string|null> $items
     */
    function render_breadcrumbs(array $items): string
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

if (! function_exists('render_page_header')) {
    /**
     * ควบคุมและแสดงผลส่วน Page Header ทั้งหมด (Title, Actions และ Breadcrumb)
     * อำนวยความสะดวกให้ Layout หลักและ View แสดงผลร่วมกันอย่างเป็นระบบ
     *
     * @param string|null $title ชื่อหน้าจอ
     * @param string|null $subtitle คำอธิบายหน้าจอ
     * @param string|null $icon ไอคอนหน้าจอ
     * @param array<string, string|null>|null $breadcrumbs รายการ Breadcrumbs
     * @param list<string>|string|null $actions รายการปุ่ม Action ฝั่งขวา (สร้างจาก navbar() หรือ string HTML)
     */
    function render_page_header(
        ?string $title = null,
        ?string $subtitle = null,
        ?string $icon = null,
        ?array $breadcrumbs = null,
        $actions = null
    ): string {
        $titleHtml = '';
        if ($title !== null && $title !== '') {
            $titleHtml = page_header_title($title, $subtitle ?? '', $icon ?? '');
        }

        $breadcrumbHtml = '';
        if (! empty($breadcrumbs)) {
            $breadcrumbHtml = render_breadcrumbs($breadcrumbs);
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
