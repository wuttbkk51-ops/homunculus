<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PersonModel;
use App\Models\TenantModel;
use InvalidArgumentException;
use RuntimeException;

/**
 * Service สำหรับจัดการข้อมูลบุคคลในตาราง people
 */
class PersonService
{
    protected PersonModel $personModel;

    public function __construct(?PersonModel $personModel = null)
    {
        $this->personModel = $personModel ?? new PersonModel();
    }

    /**
     * สร้างข้อมูลบุคคลประจำ Tenant ปัจจุบัน
     */
    public function createPerson(array $data): array
    {
        if (empty($data['tenant_id'])) {
            $tenantId = TenantModel::getCurrentTenantId();
            if ($tenantId !== null) {
                $data['tenant_id'] = $tenantId;
            }
        }

        $id = $this->personModel->insert($data, true);
        if (! $id) {
            $errors = implode(', ', $this->personModel->errors());
            throw new RuntimeException("สร้างข้อมูลบุคคลไม่สำเร็จ: {$errors}");
        }

        return $this->personModel->find($id);
    }

    /**
     * ดึงข้อมูลบุคคลตาม ID
     */
    public function getPerson(int $id): ?array
    {
        return $this->personModel->find($id);
    }

    /**
     * ปรับปรุงข้อมูลบุคคล
     */
    public function updatePerson(int $id, array $data): array
    {
        $person = $this->getPerson($id);
        if (! $person) {
            throw new InvalidArgumentException('ไม่พบข้อมูลบุคคล');
        }

        if (! $this->personModel->update($id, $data)) {
            $errors = implode(', ', $this->personModel->errors());
            throw new RuntimeException("แก้ไขข้อมูลบุคคลไม่สำเร็จ: {$errors}");
        }

        return $this->personModel->find($id);
    }

    /**
     * ลบข้อมูลบุคคล (Soft delete)
     */
    public function deletePerson(int $id): bool
    {
        $person = $this->getPerson($id);
        if (! $person) {
            throw new InvalidArgumentException('ไม่พบข้อมูลบุคคล');
        }

        return (bool) $this->personModel->delete($id);
    }

    /**
     * รายการข้อมูลบุคคล (ดึงเฉพาะของ Tenant ปัจจุบัน + Global อัตโนมัติผ่าน Model)
     */
    public function listPeople(int $limit = 20, int $offset = 0): array
    {
        return $this->personModel
            ->orderBy('id', 'DESC')
            ->findAll($limit, $offset);
    }
}
