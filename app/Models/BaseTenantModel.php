<?php

namespace App\Models;

use App\Models\Traits\TenantScopedTrait;
use CodeIgniter\Model;

/**
 * Base Model สำหรับทุก Model ที่ขึ้นตรงกับ Tenant
 * ป้องกัน Data Leak ระหว่าง Tenant โดยอัตโนมัติ (Zero Effort for Developers)
 */
abstract class BaseTenantModel extends Model
{
    use TenantScopedTrait;

    protected function initialize(): void
    {
        parent::initialize();

        // เพิ่ม Callbacks อัตโนมัติ
        if (! in_array('injectTenantId', $this->beforeInsert, true)) {
            $this->beforeInsert[] = 'injectTenantId';
        }

        if (! in_array('scopeTenant', $this->beforeFind, true)) {
            $this->beforeFind[] = 'scopeTenant';
        }

        if (! in_array('scopeTenant', $this->beforeUpdate, true)) {
            $this->beforeUpdate[] = 'scopeTenant';
        }

        if (! in_array('scopeTenant', $this->beforeDelete, true)) {
            $this->beforeDelete[] = 'scopeTenant';
        }
    }
}
