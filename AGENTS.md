# Project Rules & Architecture (Homunculus)

## 1. Tech Stack & Core Libraries
- **Framework**: CodeIgniter 4 (v4.7+) | **PHP**: 8.3+
- **Architecture**: CSMV (Controller, Service, Model, View)
- **Authentication**: CodeIgniter Shield (`codeigniter4/shield`)
  - Web Admin: Session-based authentication
  - Future Offline Client: JWT / Access Token authenticator
- **Databases**: Local = SQLite3 (`writable/database.db`), Production = MySQL (via `.env`)

## 2. Dual-DB Rules (SQLite & MySQL Compatibility)
- **Config**: Connection via `.env` only. Never hardcode in `app/Config/Database.php`.
- **Schema & Migrations**:
  - Always use CI4 Migrations (`$this->forge`). **Never use raw `.sql` files**.
  - Portable types: `INT` auto-increment PK, `VARCHAR`, `TEXT`, `DATETIME`, `DECIMAL`.
  - Naming: `lowercase_snake_case` for all tables and columns.
- **Queries**:
  - Use CI4 Models and Query Builder only. **Zero raw SQL**.
  - **No DB-specific functions**:
    - ❌ `NOW()`, `CURDATE()` → Use PHP `date('Y-m-d H:i:s')` or `$useTimestamps = true`.
    - ❌ `DATE_FORMAT()` → Format in PHP.
    - ❌ `IFNULL()` → Use standard `COALESCE()`.
    - ❌ `ON DUPLICATE KEY UPDATE` / `REPLACE INTO` → Use Model `save()`, `insert()`, `update()`.
- **MySQL Strict Mode Safety**: Every Model **must** declare `$allowedFields` and `$validationRules`.

## 3. CSMV Architecture & Coding Standards
- **Controller (C)**: Thin entrypoint (Request -> Validate -> Call Service/Model -> Return). **Zero business logic**.
- **Service (S)**: Business logic layer (`app/Services/`). Manages multi-model workflows, transactions, calculations, and 3rd-party APIs.
- **Model (M)**: Data access layer. Handles single-table CRUD, query scopes, `$allowedFields`, and strict validation rules.
  - **Mandatory BaseTenantModel**: ทุก Model ที่เป็นข้อมูลของ Tenant (เช่น Products, Sales, Customers, Branches, Orders) **ต้องสืบทอดจาก `App\Models\BaseTenantModel` เสมอ**
  - ❌ **ห้ามสืบทอดจาก `CodeIgniter\Model` ตรงๆ สำหรับตารางของ Tenant** เพื่อป้องกันปัญหา Data Leak ข้ามองค์กร
- **View (V)**: Presentation only. Renders HTML with `esc()` (XSS prevention) or formats JSON output. Zero DB queries.
- **Security**: Include `<?= csrf_field() ?>` on all POST forms. Read secrets from `env()`.

## 4. Multi-Tenant & Multi-Branch Hierarchy
- **Tenant Model**: 1 User belongs to 1 Tenant (`users.tenant_id`).
  - Exception: `superadmin` has `tenant_id = NULL` (platform owner; can access or inspect any tenant).
- **Branch Model**: 1 Tenant has Multiple Branches (`branches`).
- **Branch Assignment**: Explicit Many-to-Many via pivot table `user_branches` (`user_id`, `branch_id`, `tenant_id`, `branch_role`).
  - ❌ **NEVER use `NULL = all branches`**. All branch access must be explicit.
  - A user can oversee specific branches (e.g. 1, 3, 4) without having access to branch 2.
- **Active Branch**: Users select an "active branch" in session/token when performing branch-specific transactions.
- **High-Performance Tenant Caching (Zero DB Query per Request)**:
  - Resolve tenant context and active status via `TenantService` using CI4 Cache (`cache()->remember()`).
  - **Dual Cache Driver**: File cache on Local (`writable/cache/`); switch to Redis on Production via `.env` with zero code change.
  - **Instant Invalidation**: Call `TenantService::clearCache($tenantId)` immediately when tenant status changes (e.g. suspended, renewed).
- **Tenant Isolation Security (Enforced via `BaseTenantModel`)**:
  - **Auto Scoping**: `BaseTenantModel` จะผูก `WHERE tenant_id = :tenantId` ให้ทุกคำสั่ง `find()`, `findAll()`, `first()`, `update()`, `delete()` อัตโนมัติ ป้องกัน IDOR และ Data Leak
  - **Auto Injection**: `BaseTenantModel` จะดึง `tenant_id()` จาก Session ไปใส่ในข้อมูล `insert()` หรือ `save()` อัตโนมัติ **ห้ามเชื่อถือ `tenant_id` จาก user input / POST forms**
  - **Bypass for SuperAdmin/CLI**: ใช้ `$model->withoutTenant()` เมื่อต้องการ query ข้อมูลข้าม Tenant อย่างเจาะจง (เช่น SuperAdmin หรือ Background Worker)

## 5. Authorization: Contextual RBAC & Permission-First
- **Permission-First**: Code and UI must check **granular permissions only**. **Never check role names directly in logic**.
- **Roles as Presets**: Roles (e.g. Admin, Manager, Staff) are merely human-friendly presets/flags grouping permissions for easy assignment in UI.
- **Contextual Roles**: A user's role can vary by branch (e.g., `manager` at Branch 1, but `cashier` at Branch 3).
- **Permission Key Structure (`module.action.scope`)**:
  - Format: `{module}.{action}.{scope}` (e.g., `sale.view.own`, `sale.delete.branch`, `sale.view.all`).
  - **Standard Scopes**:
    - `.own`: Only records created by the user (`WHERE created_by = :userId`).
    - `.branch`: Any record within user's active/assigned branch (`WHERE branch_id = :branchId`).
    - `.all`: Entire tenant across all branches (`WHERE tenant_id = :tenantId`).
  - **Wildcard support**: `sale.*`, `sale.view.*`.

## 6. Routing & Filter Security (2-Layer Defense)
- **Zero Inline Logic in Routes**: `Config/Routes.php` must contain only URL-to-Controller mappings. Never write auth/permission checks inside route files.
- **Layer 1: Gatekeeper via URI Filters (`Config/Filters.php`)**:
  - Configure path patterns in `$filters` (e.g. `'admin/*'` requires `session` and `active_branch`).
  - **Secure by default**: All endpoints under `admin/*` are automatically guarded. Public routes live outside or are explicitly exempted.
  - Ensure auto-routing is disabled (`Routing::$autoRoute = false`).
- **Layer 2: Granular & Ownership Security (Service/Controller)**:
  - Ownership (`.own`) and record-level permissions (`module.action.scope`) must be verified in the Service/Controller layer after loading the entity, avoiding duplicate DB queries in filters.

## 7. Communication & Language
- **User Chat**: Respond in **Thai**.
- **Code Comments**: Write PHPDoc and comments in **Thai**.
