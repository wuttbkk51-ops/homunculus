<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAddresses extends Migration
{
    public function up(): void
    {
        // addresses (Standalone Canonical Address Table: ไม่ยึดโยงกับตารางใด ตารางไหนก็เรียกใช้ผ่าน address_id ได้)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'address_line1' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'address_line2' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'subdistrict' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'district' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'province' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'postal_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
            ],
            'country' => [
                'type'       => 'VARCHAR',
                'constraint' => 2,
                'default'    => 'TH',
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
        $this->forge->addKey(['province', 'postal_code']);
        $this->forge->createTable('addresses', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('addresses', true);
    }
}
