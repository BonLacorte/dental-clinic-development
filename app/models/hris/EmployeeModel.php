<?php

class EmployeeModel extends Model
{
    private ?array $allowedBranches = null;

    public function setAllowedBranches(?array $branches): void
    {
        $this->allowedBranches = $branches === null ? null : array_values(array_unique(array_filter(
            array_map('intval', $branches), static fn ($id) => $id > 0
        )));
    }

    private function branchSql(string $column = 'e.branch_id'): string
    {
        if ($this->allowedBranches === null) return '';
        $keys = array_keys($this->branchValues());
        return $keys ? ' AND ' . $column . ' IN (:' . implode(', :', $keys) . ')' : ' AND 1 = 0';
    }

    private function branchValues(): array
    {
        $values = [];
        foreach ($this->allowedBranches ?? [] as $index => $id) $values['employee_branch_' . $index] = $id;
        return $values;
    }

    public function employeeReferencesAllowed(int $tenant_id, array $data): bool
    {
        if ($this->allowedBranches !== null && !in_array($data['branch_id'], $this->allowedBranches, true)) return false;
        // Table names/conditions are fixed here; every external value is bound.
        $references = [
            'branch_id' => ['tenant_branches', 'AND deleted_at IS NULL'],
            'position_id' => ['hris_positions', ''],
            'org_unit_id' => ['hris_org_units', ''],
            'employment_type_id' => ['hris_employment_types', 'AND is_active = 1 AND deleted_at IS NULL'],
            'employment_status_id' => ['hris_employment_statuses', 'AND is_active = 1 AND deleted_at IS NULL']
        ];
        foreach ($references as $field => [$table, $condition]) {
            if (($data[$field] ?? null) === null) continue;
            $tenant = in_array($field, ['employment_type_id', 'employment_status_id'], true)
                ? '(tenant_id = :tenant_id OR tenant_id IS NULL)' : 'tenant_id = :tenant_id';
            $stmt = $this->db->prepare("SELECT id FROM $table WHERE id = :id AND $tenant $condition LIMIT 1");
            $stmt->execute(['id' => $data[$field], 'tenant_id' => $tenant_id]);
            if (!$stmt->fetchColumn()) return false;
        }
        return true;
    }

    public function __construct()
    {
        parent::__construct('default');
    }

    public function getAllEmployees(
        int $tenant_id,
        int $limit,
        int $offset,
        string $search = '',
        string $sortBy = 'created_at',
        string $sortDir = 'DESC'
    ): array {
        $orderBy = $this->sortColumn($sortBy);
        $direction = $this->sortDirection($sortDir);
        $whereSearch = $this->employeeSearchSql($search);

        $stmt = $this->db->prepare("
            SELECT
                e.id, e.employee_no, e.firstname, e.middlename, e.lastname,
                e.email, e.contact_no,
                e.branch_id, b.name AS branch_name,
                e.employment_type_id, et.name AS employment_type,
                e.employment_status_id, es.name AS employment_status,
                e.date_hired, e.date_resigned,
                e.status, e.created_at
            FROM hris_employees e
            LEFT JOIN tenant_branches b ON b.id = e.branch_id AND b.tenant_id = e.tenant_id
            LEFT JOIN hris_employment_types et ON et.id = e.employment_type_id AND (et.tenant_id = e.tenant_id OR et.tenant_id IS NULL)
            LEFT JOIN hris_employment_statuses es ON es.id = e.employment_status_id AND (es.tenant_id = e.tenant_id OR es.tenant_id IS NULL)
            WHERE e.tenant_id = :tenant_id
                AND e.deleted_at IS NULL
                {$this->branchSql()}
                {$whereSearch}
            ORDER BY {$orderBy} {$direction}, e.created_at DESC
            LIMIT :limit OFFSET :offset
        ");

        $this->bindEmployeeListValues($stmt, $tenant_id, $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalEmployees(int $tenant_id, string $search = ''): int
    {
        $whereSearch = $this->employeeSearchSql($search);

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM hris_employees e
            LEFT JOIN tenant_branches b ON b.id = e.branch_id AND b.tenant_id = e.tenant_id
            LEFT JOIN hris_employment_types et ON et.id = e.employment_type_id AND (et.tenant_id = e.tenant_id OR et.tenant_id IS NULL)
            LEFT JOIN hris_employment_statuses es ON es.id = e.employment_status_id AND (es.tenant_id = e.tenant_id OR es.tenant_id IS NULL)
            WHERE e.tenant_id = :tenant_id
                AND e.deleted_at IS NULL
                {$this->branchSql()}
                {$whereSearch}
        ");

        $this->bindEmployeeListValues($stmt, $tenant_id, $search);
        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public function getEmployeeById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT e.*, b.name AS branch_name, et.name AS employment_type, es.name AS employment_status,
                p.name AS position, ou.name AS org_unit
            FROM hris_employees e
            LEFT JOIN tenant_branches b ON b.id = e.branch_id AND b.tenant_id = e.tenant_id
            LEFT JOIN hris_employment_types et ON et.id = e.employment_type_id AND (et.tenant_id = e.tenant_id OR et.tenant_id IS NULL)
            LEFT JOIN hris_employment_statuses es ON es.id = e.employment_status_id AND (es.tenant_id = e.tenant_id OR es.tenant_id IS NULL)
            LEFT JOIN hris_positions p ON p.id = e.position_id AND p.tenant_id = e.tenant_id
            LEFT JOIN hris_org_units ou ON ou.id = e.org_unit_id AND ou.tenant_id = e.tenant_id
            WHERE e.id = :id
                AND e.tenant_id = :tenant_id
                AND e.deleted_at IS NULL
                {$this->branchSql()}
            LIMIT 1
        ");

        $stmt->execute(['id' => $id, 'tenant_id' => $tenant_id] + $this->branchValues());

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getLastEmployeeNoByBranch(int $branch_id, int $tenant_id): string|false
    {
        $stmt = $this->db->prepare("
            SELECT employee_no
            FROM hris_employees
            WHERE branch_id = :branch_id
                AND tenant_id = :tenant_id
            ORDER BY CAST(SUBSTRING_INDEX(employee_no, '-', -1) AS UNSIGNED) DESC
            LIMIT 1
        ");

        $stmt->execute(['branch_id' => $branch_id, 'tenant_id' => $tenant_id]);

        return $stmt->fetchColumn();
    }

    public function createEmployee(int $tenant_id, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO hris_employees
                (tenant_id, employee_no, firstname, middlename, lastname,
                 email, contact_no, branch_id, position_id, org_unit_id,
                 employment_type_id, employment_status_id,
                 date_hired, date_resigned, status, created_at)
            VALUES
                (:tenant_id, :employee_no, :firstname, :middlename, :lastname,
                 :email, :contact_no, :branch_id, :position_id, :org_unit_id,
                 :employment_type_id, :employment_status_id,
                 :date_hired, :date_resigned, :status, NOW())
        ");

        $success = $stmt->execute([
            'tenant_id' => $tenant_id,
            'employee_no' => $data['employee_no'],

            'firstname' => $data['firstname'],
            'middlename' => $data['middlename'] ?? null,
            'lastname' => $data['lastname'],

            'email' => $data['email'] ?? null,
            'contact_no' => $data['contact_no'] ?? null,

            'branch_id' => $data['branch_id'],
            'position_id' => $data['position_id'] ?? null,
            'org_unit_id' => $data['org_unit_id'] ?? null,

            'employment_type_id' => $data['employment_type_id'] ?? null,
            'employment_status_id' => $data['employment_status_id'] ?? null,

            'date_hired' => $data['date_hired'],
            'date_resigned' => $data['date_resigned'] ?? null,

            'status' => $data['status'] ?? 'ACTIVE'
        ]);

        return $success ? (int) $this->db->lastInsertId() : 0;
    }

    public function updateEmployee(int $id, int $tenant_id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_employees
            SET firstname = :firstname, middlename = :middlename, lastname = :lastname,
                email = :email, contact_no = :contact_no, branch_id = :branch_id,
                position_id = :position_id, org_unit_id = :org_unit_id,
                employment_type_id = :employment_type_id, employment_status_id = :employment_status_id,
                date_hired = :date_hired, date_resigned = :date_resigned,
                status = :status,
                updated_at = NOW()
            WHERE id = :id
                AND tenant_id = :tenant_id
                AND deleted_at IS NULL
                {$this->branchSql('branch_id')}
        ");

        $success = $stmt->execute([
            'firstname' => $data['firstname'],
            'middlename' => $data['middlename'] ?? null,
            'lastname' => $data['lastname'],

            'email' => $data['email'] ?? null,
            'contact_no' => $data['contact_no'] ?? null,

            'branch_id' => $data['branch_id'],
            'position_id' => $data['position_id'] ?? null,
            'org_unit_id' => $data['org_unit_id'] ?? null,

            'employment_type_id' => $data['employment_type_id'] ?? null,
            'employment_status_id' => $data['employment_status_id'] ?? null,

            'date_hired' => $data['date_hired'],
            'date_resigned' => $data['date_resigned'] ?? null,

            'status' => !empty($data['date_resigned']) ? 'INACTIVE' : 'ACTIVE',

            'id' => $id,
            'tenant_id' => $tenant_id
        ] + $this->branchValues());
        // An unchanged accessible row is a valid edit; a missing row is not.
        return $success && $this->getEmployeeById($id, $tenant_id) !== false;
    }

    public function deleteEmployee(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("UPDATE hris_employees SET status = 'INACTIVE', deleted_at = NOW() WHERE id = :id AND tenant_id = :tenant_id AND deleted_at IS NULL {$this->branchSql('branch_id')}");
        $success = $stmt->execute(['id' => $id, 'tenant_id' => $tenant_id] + $this->branchValues());
        return $success && $stmt->rowCount() > 0;
    }

    public function getEmploymentTypes(int $tenant_id): array
    {
        $stmt = $this->db->prepare(" SELECT * FROM hris_employment_types  WHERE (tenant_id = :tenant_id OR tenant_id IS NULL)  AND is_active = 1 AND deleted_at IS NULL ORDER BY name ASC");
        $stmt->execute(['tenant_id' => $tenant_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getEmploymentStatuses(int $tenant_id): array
    {
        $stmt = $this->db->prepare("SELECT * FROM hris_employment_statuses WHERE (tenant_id = :tenant_id OR tenant_id IS NULL) AND is_active = 1 AND deleted_at IS NULL ORDER BY name ASC ");
        $stmt->execute(['tenant_id' => $tenant_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDefaultEmploymentStatusId(int $tenant_id): int
    {
        $stmt = $this->db->prepare("SELECT id FROM hris_employment_statuses WHERE code = 'ACTIVE' AND (tenant_id = :tenant_id OR tenant_id IS NULL) AND is_active = 1 AND deleted_at IS NULL LIMIT 1 ");
        $stmt->execute(['tenant_id' => $tenant_id]);
        return (int) $stmt->fetchColumn();
    }

    private function sortColumn(string $sortBy): string
    {
        $columns = [
            'employee_no' => 'e.employee_no',
            'name' => 'e.lastname',
            'branch_name' => 'b.name',
            'employment_type' => 'et.name',
            'employment_status' => 'es.name',
            'date_hired' => 'e.date_hired',
            'status' => 'e.status',
            'created_at' => 'e.created_at'
        ];

        return $columns[$sortBy] ?? $columns['created_at'];
    }

    private function sortDirection(string $sortDir): string
    {
        return strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';
    }

    private function employeeSearchSql(string $search): string
    {
        if ($search === '') {
            return '';
        }

        return "
            AND CONCAT_WS('|', e.employee_no, e.firstname, e.middlename, e.lastname,
                e.email, e.contact_no, b.name, et.name, es.name) LIKE :search
        ";
    }

    private function bindEmployeeListValues(PDOStatement $stmt, int $tenant_id, string $search): void
    {
        $stmt->bindValue(':tenant_id', $tenant_id, PDO::PARAM_INT);
        foreach ($this->branchValues() as $key => $id) $stmt->bindValue(':' . $key, $id, PDO::PARAM_INT);

        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
    }

    public function getEmployeeCompensation(int $id, int $tenant_id)
    {
        $stmt = $this->db->prepare("
            SELECT c.* FROM payroll_employee_compensations c
            INNER JOIN hris_employees e ON e.id = c.employee_id AND e.tenant_id = c.tenant_id
            WHERE c.tenant_id = :tenant_id AND c.employee_id = :employee_id
                AND e.deleted_at IS NULL {$this->branchSql()}
        ");

        $stmt->execute(['tenant_id' => $tenant_id, 'employee_id' => $id] + $this->branchValues());

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createEmployeeCompensation(int $tenant_id, array $data): int
    {
        $sql = "
            INSERT INTO payroll_employee_compensations 
            (
                tenant_id,
                employee_id,
                salary_type,
                payroll_frequency,
                salary_rate,
                effective_from,
                effective_to,
                created_at  
            )
            SELECT
                :tenant_id,
                :employee_id,
                :salary_type,
                :payroll_frequency,
                :salary_rate,
                :effective_from,
                :effective_to,
                NOW()
            FROM hris_employees e
            WHERE e.id = :target_employee_id AND e.tenant_id = :employee_tenant_id
                AND e.deleted_at IS NULL {$this->branchSql()}
            ";

        $stmt = $this->db->prepare($sql);

        $success = $stmt->execute([
            'tenant_id' => $tenant_id,
            'employee_id' => $data['employee_id'],
            'salary_type' => $data['salary_type'],
            'payroll_frequency' => $data['payroll_frequency'],
            'salary_rate' => $data['salary_rate'],
            'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to'],
            'target_employee_id' => $data['employee_id'],
            'employee_tenant_id' => $tenant_id
        ] + $this->branchValues());

        return $success && $stmt->rowCount() === 1 ? (int) $this->db->lastInsertId() : 0;
    }

    public function updateEmployeeCompensation(int $id, int $tenant_id, array $data): bool
    {
        // IDs must describe the same existing employee/rate. Never reparent a rate.
        $where = "id = :id AND tenant_id = :tenant_id AND employee_id = :employee_id
            AND EXISTS (SELECT 1 FROM hris_employees e
                WHERE e.id = payroll_employee_compensations.employee_id
                    AND e.tenant_id = payroll_employee_compensations.tenant_id
                    AND e.deleted_at IS NULL {$this->branchSql()})";
        $params = ['id' => $id, 'tenant_id' => $tenant_id, 'employee_id' => $data['employee_id']] + $this->branchValues();
        $stmt = $this->db->prepare("UPDATE payroll_employee_compensations
            SET salary_type = :salary_type, payroll_frequency = :payroll_frequency,
                salary_rate = :salary_rate, effective_from = :effective_from,
                effective_to = :effective_to, updated_at = NOW()
            WHERE $where");
        $success = $stmt->execute($params + [
            'salary_type' => $data['salary_type'], 'payroll_frequency' => $data['payroll_frequency'],
            'salary_rate' => $data['salary_rate'], 'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to']
        ]);
        if (!$success) return false;
        if ($stmt->rowCount() > 0) return true;
        $check = $this->db->prepare("SELECT id FROM payroll_employee_compensations WHERE $where LIMIT 1");
        $check->execute($params);
        return (bool) $check->fetchColumn();
    }
}
