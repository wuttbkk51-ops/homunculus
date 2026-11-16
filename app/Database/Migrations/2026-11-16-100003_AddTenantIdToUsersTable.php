<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration สำหรับเพิ่มคอลัมน์ tenant_id ในตาราง users
 */
class AddTenantIdToUsersTable extends Migration
{
    public function up(): void
    {
        $fields = [
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', 'tenant_id');
    }
}
