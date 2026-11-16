<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Master Seeder สำหรับสร้างข้อมูลเริ่มต้นระบบ Homunculus:
 * 1. SuperAdmin (Platform Owner)
 * 2. Tenant แรก (Acme Corporation) พร้อม Admin ดูแล
 * 3. Tenant ร้านค้าจำลอง (สยาม มินิมาร์ท) พร้อม เจ้าของร้าน และ พนักงาน
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $db       = $this->db;
        $now      = date('Y-m-d H:i:s');
        $nextYear = date('Y-m-d H:i:s', strtotime('+1 year'));

        // =========================================================================
        // 1. สร้าง SuperAdmin (Platform Owner, tenant_id = NULL)
        // =========================================================================
        $superadminId = $this->createUser([
            'username'  => 'superadmin',
            'tenant_id' => null,
        ], 'superadmin@homunculus.test', 'Password123!', ['superadmin', 'admin']);

        // =========================================================================
        // 2. Tenant ที่ 1: Acme Corporation (องค์กรแรก)
        // =========================================================================
        $db->table('tenants')->insert([
            'name'        => 'Acme Corporation',
            'code'        => 'ACME',
            'status'      => 'active',
            'expire_date' => $nextYear,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
        $acmeTenantId = (int) $db->insertID();

        // สาขาของ Acme Corporation
        $db->table('branches')->insert([
            'tenant_id'  => $acmeTenantId,
            'name'       => 'สำนักงานใหญ่ (กรุงเทพฯ)',
            'code'       => 'BKK01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $acmeBranch1Id = (int) $db->insertID();

        $db->table('branches')->insert([
            'tenant_id'  => $acmeTenantId,
            'name'       => 'สาขาเชียงใหม่',
            'code'       => 'CNX01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $acmeBranch2Id = (int) $db->insertID();

        // Admin ที่ดูแล Tenant แรก
        $acmeAdminId = $this->createUser([
            'username'  => 'admin',
            'tenant_id' => $acmeTenantId,
        ], 'admin@acme.com', 'Password123!', ['admin']);

        // มอบหมายสาขาให้ Admin ของ Acme (ดูแลทั้ง 2 สาขาในฐานะ manager)
        $this->assignBranch($acmeTenantId, $acmeAdminId, $acmeBranch1Id, 'manager');
        $this->assignBranch($acmeTenantId, $acmeAdminId, $acmeBranch2Id, 'manager');

        // =========================================================================
        // 3. Tenant ที่ 2: ร้านค้าจำลอง (สยาม มินิมาร์ท - Siam Minimart)
        // =========================================================================
        $db->table('tenants')->insert([
            'name'        => 'สยาม มินิมาร์ท (Siam Minimart)',
            'code'        => 'SIAM_MART',
            'status'      => 'active',
            'expire_date' => $nextYear,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
        $siamTenantId = (int) $db->insertID();

        // สาขาของร้านสยาม มินิมาร์ท
        $db->table('branches')->insert([
            'tenant_id'  => $siamTenantId,
            'name'       => 'สาขาสยามสแควร์ (Flagship)',
            'code'       => 'SIAM01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $siamBranch1Id = (int) $db->insertID();

        $db->table('branches')->insert([
            'tenant_id'  => $siamTenantId,
            'name'       => 'สาขาอารีย์',
            'code'       => 'ARI01',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $siamBranch2Id = (int) $db->insertID();

        // 3.1 เจ้าของร้าน (Shop Owner / Tenant Admin)
        $ownerId = $this->createUser([
            'username'  => 'shop_owner',
            'tenant_id' => $siamTenantId,
        ], 'owner@siammart.com', 'Password123!', ['admin']);

        // มอบหมายสาขาให้เจ้าของร้าน (ดูแลทุกสาขาในฐานะ manager)
        $this->assignBranch($siamTenantId, $ownerId, $siamBranch1Id, 'manager');
        $this->assignBranch($siamTenantId, $ownerId, $siamBranch2Id, 'manager');

        // 3.2 พนักงาน (Shop Staff / Cashier)
        $staffId = $this->createUser([
            'username'  => 'shop_staff',
            'tenant_id' => $siamTenantId,
        ], 'staff@siammart.com', 'Password123!', ['user']);

        // มอบหมายสาขาให้พนักงาน (ประจำสาขาสยามสแควร์ บทบาท cashier)
        $this->assignBranch($siamTenantId, $staffId, $siamBranch1Id, 'cashier');
    }

    /**
     * Helper สำหรับสร้างผู้ใช้ พร้อม Credentials และ Groups ใน Shield
     *
     * @param array<string, mixed> $userData
     * @param list<string>         $groups
     */
    protected function createUser(array $userData, string $email, string $password, array $groups = []): int
    {
        $db  = $this->db;
        $now = date('Y-m-d H:i:s');

        // 1. บันทึกข้อมูลตาราง users
        $db->table('users')->insert([
            'username'   => $userData['username'],
            'tenant_id'  => $userData['tenant_id'] ?? null,
            'active'     => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $userId = (int) $db->insertID();

        // 2. บันทึกข้อมูลตาราง auth_identities (Password Identity)
        $passwordHash = service('passwords')->hash($password);
        $db->table('auth_identities')->insert([
            'user_id'    => $userId,
            'type'       => 'email_password',
            'name'       => null,
            'secret'     => $email,
            'secret2'    => $passwordHash,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. บันทึกกลุ่มผู้ใช้ (auth_groups_users)
        foreach ($groups as $group) {
            $db->table('auth_groups_users')->insert([
                'user_id'    => $userId,
                'group'      => $group,
                'created_at' => $now,
            ]);
        }

        return $userId;
    }

    /**
     * Helper สำหรับมอบหมายสาขาให้ผู้ใช้ (user_branches)
     */
    protected function assignBranch(int $tenantId, int $userId, int $branchId, string $role = 'staff'): void
    {
        $now = date('Y-m-d H:i:s');

        $this->db->table('user_branches')->insert([
            'tenant_id'   => $tenantId,
            'user_id'     => $userId,
            'branch_id'   => $branchId,
            'branch_role' => $role,
            'created_at'  => $now,
            'updated_at'  => $now,
        ]);
    }
}
