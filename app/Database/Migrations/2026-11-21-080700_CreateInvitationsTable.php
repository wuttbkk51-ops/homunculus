<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration สร้างตาราง invitations
 *
 * เป็น Central Table (ไม่มี tenant_id บน PK) ใช้เก็บคำเชิญทั้งสองแบบ:
 * - tenant_id = NULL: เชิญสร้าง Tenant ใหม่
 * - tenant_id มีค่า: เชิญเข้าทีม Tenant ที่มีอยู่
 */
class CreateInvitationsTable extends Migration
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
            // NULL = เชิญสร้าง Tenant ใหม่ / มีค่า = เชิญเข้า Tenant ที่มีอยู่
            'tenant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            // Hybrid mode: Admin กำหนดล่วงหน้าได้ ถ้า NULL ผู้รับกรอกเองในฟอร์ม
            // ใช้เฉพาะกรณี tenant_id = NULL
            'tenant_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tenant_slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 254,
            ],
            // role ที่จะได้รับใน tenant_users เช่น 'admin','manager','staff','member'
            'role_group' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            // เก็บเฉพาะ SHA-256 hash ของ raw token (64 hex chars) ป้องกัน DB รั่ว
            'token_hash' => [
                'type'       => 'CHAR',
                'constraint' => 64,
            ],
            'invited_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            // มีค่า = ถูกใช้งานแล้ว ปิด Token ถาวร
            'accepted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('token_hash');
        $this->forge->addKey('email');
        // Composite Index เอา tenant_id นำหน้าตาม Leftmost Prefix Rule
        $this->forge->addKey(['tenant_id', 'email']);

        // SET NULL ทั้งคู่เพื่อเก็บ audit trail เมื่อ user/tenant ถูกลบ
        $this->forge->addForeignKey('tenant_id',  'tenants', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('invited_by', 'users',   'id', 'CASCADE', 'SET NULL');

        $this->forge->createTable('invitations', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('invitations', true);
    }
}
