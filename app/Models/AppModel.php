<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use RuntimeException;

/**
 * AppModel
 *
 * Base Multi-Tenant Model for Homunculus Application.
 * Automatically scopes all database actions (Find, Insert, Update, Delete, Count)
 * by `tenant_id` to prevent data leakage across tenants.
 */
class AppModel extends Model
{
    /**
     * The name of the database column that stores the tenant ID.
     */
    protected string $tenantColumn = 'tenant_id';

    /**
     * Whether this model should be scoped by tenant.
     * Set to false in models that represent global/shared tables.
     */
    protected bool $scopedByTenant = true;

    /**
     * Temporary scoping flag for one-off operations (e.g. withoutTenant()).
     */
    protected bool $tempScopedByTenant = true;

    /**
     * If true, throws an exception when an action is performed without a resolved tenant ID.
     * Prevents accidental un-scoped queries in multi-tenant environments.
     */
    protected bool $strictTenantScope = true;

    /**
     * Explicit tenant ID override for this instance or next query.
     */
    protected ?int $customTenantId = null;

    /**
     * Initializes model settings, ensures tenantColumn is protected/allowed,
     * and registers tenant scoping callbacks.
     */
    protected function initialize(): void
    {
        parent::initialize();

        $this->tempScopedByTenant = $this->scopedByTenant;

        // Auto-include tenantColumn in allowedFields if field protection is active
        if ($this->protectFields && ! in_array($this->tenantColumn, $this->allowedFields, true)) {
            $this->allowedFields[] = $this->tenantColumn;
        }

        $this->registerTenantCallbacks();
    }

    /**
     * Registers tenant lifecycle callbacks with priority (prepended).
     */
    protected function registerTenantCallbacks(): void
    {
        // Before Find
        if (! in_array('scopeTenantFind', $this->beforeFind, true)) {
            array_unshift($this->beforeFind, 'scopeTenantFind');
        }

        // Before Insert
        if (! in_array('scopeTenantInsert', $this->beforeInsert, true)) {
            array_unshift($this->beforeInsert, 'scopeTenantInsert');
        }

        // Before Insert Batch
        if (! in_array('scopeTenantInsertBatch', $this->beforeInsertBatch, true)) {
            array_unshift($this->beforeInsertBatch, 'scopeTenantInsertBatch');
        }

        // Before Update
        if (! in_array('scopeTenantUpdate', $this->beforeUpdate, true)) {
            array_unshift($this->beforeUpdate, 'scopeTenantUpdate');
        }

        // Before Update Batch
        if (! in_array('scopeTenantUpdateBatch', $this->beforeUpdateBatch, true)) {
            array_unshift($this->beforeUpdateBatch, 'scopeTenantUpdateBatch');
        }

        // Before Delete
        if (! in_array('scopeTenantDelete', $this->beforeDelete, true)) {
            array_unshift($this->beforeDelete, 'scopeTenantDelete');
        }

        // Reset temporary state after execution
        foreach (['afterFind', 'afterInsert', 'afterUpdate', 'afterDelete'] as $hook) {
            if (! in_array('resetTenantScope', $this->{$hook}, true)) {
                $this->{$hook}[] = 'resetTenantScope';
            }
        }
    }

    // =========================================================================
    // Callbacks
    // =========================================================================

    /**
     * Auto WHERE tenant_id on SELECT queries (find, findAll, first, etc.)
     */
    protected function scopeTenantFind(array $data): array
    {
        if ($this->shouldApplyTenantScope()) {
            $tenantId = $this->getTenantId();
            if ($tenantId !== null) {
                $this->builder()->where($this->table . '.' . $this->tenantColumn, $tenantId);
            }
        }

        return $data;
    }

    /**
     * Auto fill tenant_id on single INSERT
     */
    protected function scopeTenantInsert(array $data): array
    {
        if ($this->shouldApplyTenantScope()) {
            $tenantId = $this->getTenantId();
            if ($tenantId !== null && isset($data['data']) && is_array($data['data'])) {
                if (! isset($data['data'][$this->tenantColumn])) {
                    $data['data'][$this->tenantColumn] = $tenantId;
                }
            }
        }

        return $data;
    }

    /**
     * Auto fill tenant_id on INSERT BATCH
     */
    protected function scopeTenantInsertBatch(array $data): array
    {
        if ($this->shouldApplyTenantScope()) {
            $tenantId = $this->getTenantId();
            if ($tenantId !== null && isset($data['data']) && is_array($data['data'])) {
                foreach ($data['data'] as &$row) {
                    if (is_array($row) && ! isset($row[$this->tenantColumn])) {
                        $row[$this->tenantColumn] = $tenantId;
                    }
                }
            }
        }

        return $data;
    }

    /**
     * Auto WHERE tenant_id on UPDATE queries
     */
    protected function scopeTenantUpdate(array $data): array
    {
        if ($this->shouldApplyTenantScope()) {
            $tenantId = $this->getTenantId();
            if ($tenantId !== null) {
                $this->builder()->where($this->table . '.' . $this->tenantColumn, $tenantId);
            }
        }

        return $data;
    }

    /**
     * Auto WHERE tenant_id on UPDATE BATCH queries
     */
    protected function scopeTenantUpdateBatch(array $data): array
    {
        if ($this->shouldApplyTenantScope()) {
            $tenantId = $this->getTenantId();
            if ($tenantId !== null) {
                $this->builder()->where($this->table . '.' . $this->tenantColumn, $tenantId);
            }
        }

        return $data;
    }

    /**
     * Auto WHERE tenant_id on DELETE queries (Soft or Hard Delete)
     */
    protected function scopeTenantDelete(array $data): array
    {
        if ($this->shouldApplyTenantScope()) {
            $tenantId = $this->getTenantId();
            if ($tenantId !== null) {
                $this->builder()->where($this->table . '.' . $this->tenantColumn, $tenantId);
            }
        }

        return $data;
    }

    /**
     * Resets temporary scope settings back to defaults after a query executes.
     */
    protected function resetTenantScope(array $data): array
    {
        $this->tempScopedByTenant = $this->scopedByTenant;
        $this->customTenantId     = null;

        return $data;
    }

    // =========================================================================
    // Overridden Methods
    // =========================================================================

    /**
     * Ensures countAllResults applies the tenant scope correctly.
     */
    public function countAllResults(bool $reset = true, bool $test = false)
    {
        if ($this->shouldApplyTenantScope()) {
            $tenantId = $this->getTenantId();
            if ($tenantId !== null) {
                $this->builder()->where($this->table . '.' . $this->tenantColumn, $tenantId);
            }
        }

        if ($reset) {
            $this->tempScopedByTenant = $this->scopedByTenant;
            $this->customTenantId     = null;
        }

        return parent::countAllResults($reset, $test);
    }

    // =========================================================================
    // Scope Control & Helper Methods
    // =========================================================================

    /**
     * Temporarily disable tenant scoping for the next query.
     * Useful for superadmin queries, cronjobs, or cross-tenant reporting.
     *
     * Example: $model->withoutTenant()->findAll();
     */
    public function withoutTenant(): static
    {
        $this->tempScopedByTenant = false;

        return $this;
    }

    /**
     * Explicitly set the tenant ID for this query/operation.
     *
     * Example: $model->forTenant(2)->findAll();
     */
    public function forTenant(int|string $tenantId): static
    {
        $this->customTenantId     = (int) $tenantId;
        $this->tempScopedByTenant = true;

        return $this;
    }

    /**
     * Returns whether tenant scoping should be applied currently.
     */
    public function shouldApplyTenantScope(): bool
    {
        return $this->scopedByTenant && $this->tempScopedByTenant;
    }

    /**
     * Gets the current tenant ID. Throws an exception if strict mode is on and no tenant is resolved.
     */
    public function getTenantId(): ?int
    {
        $tenantId = $this->resolveTenantId();

        if ($tenantId === null && $this->shouldApplyTenantScope() && $this->strictTenantScope) {
            // In CLI, allow without exception only if explicitly permitted, otherwise fail-closed
            if (! is_cli()) {
                throw new RuntimeException(
                    sprintf(
                        'Tenant scoping is active on model [%s], but no tenant ID could be resolved. ' .
                        'Ensure user/context has a tenant, or call ->withoutTenant() / ->forTenant($id).',
                        static::class
                    )
                );
            }
        }

        return $tenantId;
    }

    /**
     * Resolves the current tenant ID from multiple available sources:
     * 1. Explicit model instance override ($this->customTenantId)
     * 2. AppContext service (if exists)
     * 3. Session (Web): 'current_tenant.id', 'current_tenant_id', 'tenant_id'
     * 4. Request Header (API): 'X-Tenant-ID'
     * 5. Authenticated Shield User: auth()->user()->tenant_id
     */
    protected function resolveTenantId(): ?int
    {
        // 1. Explicit instance override
        if ($this->customTenantId !== null) {
            return $this->customTenantId;
        }

        // 2. AppContext service (if defined)
        if (class_exists('App\Libraries\AppContext') && method_exists('App\Libraries\AppContext', 'getTenantId')) {
            $contextTenantId = \App\Libraries\AppContext::getTenantId();
            if ($contextTenantId !== null) {
                return (int) $contextTenantId;
            }
        }

        // 3. Web Session
        if (function_exists('session') && session()->isStarted()) {
            $currentTenant = session('current_tenant');
            if (is_array($currentTenant) && isset($currentTenant['id'])) {
                return (int) $currentTenant['id'];
            }
            if (session()->has('current_tenant_id')) {
                return (int) session('current_tenant_id');
            }
            if (session()->has('tenant_id')) {
                return (int) session('tenant_id');
            }
        }

        // 4. API Request Header (X-Tenant-ID) - รองรับทั้ง INT ID และ UUID
        if (function_exists('service')) {
            $request = service('request');
            if ($request instanceof \CodeIgniter\HTTP\RequestInterface && method_exists($request, 'header')) {
                $header = $request->header('X-Tenant-ID');
                if ($header !== null && ($val = trim($header->getValue())) !== '') {
                    if (is_numeric($val)) {
                        return (int) $val;
                    }

                    // ค้นหา ID จาก UUID
                    return $this->resolveTenantIdByUuid($val);
                }
            }
        }

        // 5. Shield Auth user fallback
        if (function_exists('auth') && auth()->loggedIn()) {
            $user = auth()->user();
            if (isset($user->tenant_id) && $user->tenant_id) {
                return (int) $user->tenant_id;
            }
        }

        return null;
    }

    /**
     * ค้นหา tenant_id (INT) จาก UUID โดยมี In-Memory Cache ในรอบ Request
     */
    protected function resolveTenantIdByUuid(string $uuid): ?int
    {
        static $uuidCache = [];

        if (isset($uuidCache[$uuid])) {
            return $uuidCache[$uuid];
        }

        $db = \Config\Database::connect();
        $row = $db->table('tenants')
            ->select('id')
            ->where('uuid', $uuid)
            ->where('status', 'active')
            ->get()
            ->getRow();

        if ($row) {
            $uuidCache[$uuid] = (int) $row->id;
            return $uuidCache[$uuid];
        }

        return null;
    }
}
