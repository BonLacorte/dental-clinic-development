<?php

class OrgUnit extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    public function getAllOrgUnits(int $tenant_id): array
    {
        $stmt = $this->db->prepare("
            SELECT id, parent_id, name, code, type, created_at
            FROM hris_org_units
            WHERE tenant_id = :tenant_id
            ORDER BY created_at ASC
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrgUnitById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM hris_org_units
            WHERE id = :id
                AND tenant_id = :tenant_id
            LIMIT 1
        ");

        $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createOrgUnit(array $data, int $tenant_id): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO hris_org_units (tenant_id, parent_id, name, code, type, created_at)
            VALUES (:tenant_id, :parent_id, :name, :code, :type, NOW())
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id,
            'parent_id' => $data['parent_id'] ?? null,
            'name'      => $data['name'],
            'code'      => $data['code'],
            'type'      => $data['type']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getParentId(int $id, int $tenant_id): int|null
    {
        $stmt = $this->db->prepare("
            SELECT parent_id
            FROM hris_org_units
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id
        ]);

        $result = $stmt->fetchColumn();
        return $result !== false ? (int) $result : null;
    }

    public function getChildUnits(int $parent_id, int $tenant_id): array
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM hris_org_units
            WHERE parent_id = :parent_id
                AND tenant_id = :tenant_id
            ORDER BY name
        ");

        $stmt->execute([
            'parent_id' => $parent_id,
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmployeesInUnit(int $org_unit_id, int $tenant_id): array
    {
        $stmt = $this->db->prepare("
            SELECT
                e.id, e.employee_no, e.firstname, e.middlename, e.lastname,
                e.email, e.contact_no,
                b.name AS branch_name,
                p.name AS position,
                es.name AS employment_status,
                e.date_hired, e.status
            FROM hris_employees e
            LEFT JOIN tenant_branches b ON b.id = e.branch_id
            LEFT JOIN hris_positions p ON p.id = e.position_id
            LEFT JOIN hris_employment_statuses es ON es.id = e.employment_status_id
            WHERE e.org_unit_id = :org_unit_id
                AND e.tenant_id = :tenant_id
                AND e.deleted_at IS NULL
            ORDER BY e.lastname ASC, e.firstname ASC
        ");

        $stmt->execute([
            'org_unit_id' => $org_unit_id,
            'tenant_id'   => $tenant_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateOrgUnit(int $id, array $data, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_org_units
            SET
                name = :name,
                code = :code,
                type = :type
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id,
            'name'      => $data['name'],
            'code'      => $data['code'],
            'type'      => $data['type']
        ]);
    }

    public function deleteOrgUnit(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM hris_org_units
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id'        => $id,
            'tenant_id' => $tenant_id
        ]);
    }
}