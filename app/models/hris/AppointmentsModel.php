<?php

class AppointmentsModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }


    public function getAllAppointments(int $tenant_id): array
    {
        $stmt = $this->db->prepare("SELECT *, CASE WHEN tenant_id IS NULL THEN 1 ELSE 0 END AS is_global
            FROM hris_employment_types
            WHERE (tenant_id = :tenant_id OR tenant_id IS NULL)
                AND deleted_at IS NULL
            ORDER BY name ASC
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAppointmentById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare(" SELECT *, CASE WHEN tenant_id IS NULL THEN 1 ELSE 0 END AS is_global
            FROM hris_employment_types
            WHERE id = :id
                AND (tenant_id = :tenant_id OR tenant_id IS NULL)
                AND deleted_at IS NULL
            LIMIT 1
        ");

        $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    //chekcs if the appointment exists for the given tenant_id (even if its deleted at)
    //set the deleted_at to null and update the record to be is_active = 1
    public function createAppointment(int $tenant_id, array $data): int
    {
        $stmt = $this->db->prepare("INSERT INTO hris_employment_types (tenant_id, code, name, description, is_active, created_at, deleted_at)
            VALUES (:tenant_id, :code, :name, :description, :is_active, NOW(), NULL)
            ON DUPLICATE KEY UPDATE
                name = VALUES(name),
                description = VALUES(description),
                is_active = VALUES(is_active),
                deleted_at = NULL,
                updated_at = NOW()
        ");

        $stmt->execute([
            'tenant_id'   => $tenant_id,
            'code'        => $data['code'],
            'name'        => $data['name'],
            'description' => $data['description'] ?: null,
            'is_active'   => $data['is_active'] ?? 1
        ]);

        if ($this->db->lastInsertId() == 0) {
            $stmt = $this->db->prepare(" SELECT id FROM hris_employment_types WHERE tenant_id = :tenant_id AND code = :code LIMIT 1 ");
            $stmt->execute(['tenant_id' => $tenant_id, 'code' => $data['code']]);
            return (int) $stmt->fetchColumn();
        }
        return (int) $this->db->lastInsertId();
    }

    public function updateAppointment(int $id, int $tenant_id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_employment_types
            SET
                code = :code,
                name = :name,
                description = :description,
                is_active = :is_active,
                updated_at = NOW()
            WHERE id = :id
                AND tenant_id = :tenant_id
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            'id'          => $id,
            'tenant_id'   => $tenant_id,
            'code'        => $data['code'],
            'name'        => $data['name'],
            'description' => $data['description'] ?: null,
            'is_active'   => $data['is_active'] ?? 1
        ]);

        return $stmt->rowCount() > 0;
    }

    public function deleteAppointment(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_employment_types
            SET deleted_at = NOW()
            WHERE id = :id
                AND tenant_id = :tenant_id
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function existsById(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare(" SELECT id FROM hris_employment_types WHERE id = :id AND tenant_id = :tenant_id AND deleted_at IS NULL LIMIT 1");

        $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id
        ]);

        return (bool) $stmt->fetchColumn();
    }
}