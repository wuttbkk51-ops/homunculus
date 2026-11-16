<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Seeder สำหรับสร้างข้อมูลทดสอบระบบ Auth, Multi-Tenant และ Multi-Branch
 */
class AuthTestSeeder extends Seeder
{
    public function run(): void
    {
        $db = $this->db;
        $now = date('Y-m-d H:i:s');
        $nextYear = date('Y-m-d H:i:s', strtotime('+1 year'));

        // 1. ตรวจสอบหรือสร้าง Tenant ทดสอบ
        $tenant = $db->table('tenants')->where('code', 'ACME')->get()->getRow();
        if (! $tenant) {
            $db->table('tenants')->insert([
                'name'        => 'Acme Corporation',
                'code'        => 'ACME',
                'status'      => 'active',
                'expire_date' => $nextYear,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
            $tenantId = (int) $db->insertID();
        } else {
            $tenantId = (int) $tenant->id;
        }

        // 2. ตรวจสอบหรือสร้างสาขา
        $branch1 = $db->table('branches')->where(['tenant_id' => $tenantId, 'code' => 'BKK01'])->get()->getRow();
        if (! $branch1) {
            $db->table('branches')->insert([
                'tenant_id'  => $tenantId,
                'name'       => 'สำนักงานใหญ่ (กรุงเทพฯ)',
                'code'       => 'BKK01',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $branch1Id = (int) $db->insertID();
        } else {
            $branch1Id = (int) $branch1->id;
        }

        $branch2 = $db->table('branches')->where(['tenant_id' => $tenantId, 'code' => 'CNX01'])->get()->getRow();
        if (! $branch2) {
            $db->table('branches')->insert([
                'tenant_id'  => $tenantId,
                'name'       => 'สาขาเชียงใหม่',
                'code'       => 'CNX01',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $branch2Id = (int) $db->insertID();
        } else {
            $branch2Id = (int) $branch2->id;
        }

        // 3. ผูก tenant_id ให้กับผู้ใช้ somchai
        $userSomchai = $db->table('users')->where('username', 'somchai')->get()->getRow();
        if ($userSomchai) {
            $db->table('users')->where('id', $userSomchai->id)->update([
                'tenant_id'  => $tenantId,
                'updated_at' => $now,
            ]);

            // ผูกสาขาใน user_branches
            // สาขา 1: บทบาท manager
            $exists1 = $db->table('user_branches')->where([
                'user_id'   => $userSomchai->id,
                'branch_id' => $branch1Id,
            ])->countAllResults();

            if ($exists1 === 0) {
                $db->table('user_branches')->insert([
                    'user_id'     => $userSomchai->id,
                    'branch_id'   => $branch1Id,
                    'branch_role' => 'manager',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }

            // สาขา 2: บทบาท cashier
            $exists2 = $db->table('user_branches')->where([
                'user_id'   => $userSomchai->id,
                'branch_id' => $branch2Id,
            ])->countAllResults();

            if ($exists2 === 0) {
                $db->table('user_branches')->insert([
                    'user_id'     => $userSomchai->id,
                    'branch_id'   => $branch2Id,
                    'branch_role' => 'cashier',
                    'created_at'  => $now,
                    'updated_at'  => $now,
                ]);
            }
        }
    }
}
