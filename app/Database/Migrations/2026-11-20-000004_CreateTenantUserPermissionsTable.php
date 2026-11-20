<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration สร้างตาราง tenant_user_permissions (สิทธิ์ระดับ Granular module.action.scope ประจำ Tenant)
 */
class CreateTenantUserPermissionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'permission' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        // Composite Index เอา tenant_id นำหน้าเสมอตาม Leftmost Prefix Rule
        $this->forge->addUniqueKey(['tenant_id', 'user_id', 'permission']);
        $this->forge->addKey('user_id');

        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('tenant_user_permissions', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('tenant_user_permissions', true);
    }
}
