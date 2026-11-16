<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration สำหรับสร้างตาราง user_branches (ความสัมพันธ์ผู้ใช้กับสาขาแบบ Explicit)
 */
class CreateUserBranchesTable extends Migration
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
            'branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'branch_role' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'default'    => 'staff',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['tenant_id', 'user_id']);
        $this->forge->addUniqueKey(['user_id', 'branch_id']);
        $this->forge->addKey('branch_id');
        $this->forge->addForeignKey('tenant_id', 'tenants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('branch_id', 'branches', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_branches', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('user_branches', true);
    }
}
