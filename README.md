# Homunculus

A Modular (HMVC) SaaS Framework built on CodeIgniter 4 (CI4) — Guided by Unix Philosophy

## Setup

### 1. Install dependencies (auto-copies AdminLTE assets)
```bash
composer install
```

### 2. Environment config
```bash
cp env-example .env
```
เปิดไฟล์ `.env` แล้วตั้งค่า Database เป็น **SQLite3** สำหรับ Local Dev (ไม่ต้องเปิด MySQL):
```env
CI_ENVIRONMENT = development

database.default.DBDriver = SQLite3
database.default.database = database.db
```
*(ระบบจะเก็บไฟล์ฐานข้อมูลไว้ที่ `writable/database.db` โดยอัตโนมัติ)*

### 3. Database migrations
```bash
php spark migrate --all
```

### 4. (Optional) Run seeder
```bash
php spark db:seed <SeederName>
```

### 5. Start dev server
```bash
php spark serve
```
Visit: `http://localhost:8080`

## Useful Commands

```bash
composer test                       # Run tests
composer run-script copy-assets     # Re-copy AdminLTE assets
php spark routes                    # List routes
```
