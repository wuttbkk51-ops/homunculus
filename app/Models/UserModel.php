<?php

namespace App\Models;

use App\Entities\User;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;

/**
 * Custom UserModel สำหรับ Homunculus
 * ขยายความสามารถจาก Shield UserModel เพื่อรองรับ tenant_id และ User Entity ประจำระบบ
 */
class UserModel extends ShieldUserModel
{
    protected $returnType = User::class;

    protected function initialize(): void
    {
        parent::initialize();

        $this->allowedFields = array_merge($this->allowedFields, [
            'tenant_id',
        ]);

        $this->returnType = User::class;
    }
}
