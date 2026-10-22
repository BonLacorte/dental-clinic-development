<?php

class EmployeeModel extends Model
{
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
            LEFT JOIN tenant_branches b ON b.id = e.branch_id
            LEFT JOIN hris_employment_types et ON et.id = e.employment_type_id
            LEFT JOIN hris_employment_statuses es ON es.id = e.employment_status_id
            WHERE e.tenant_id = :tenant_id
                AND e.deleted_at IS NULL
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
            LEFT JOIN tenant_branches b ON b.id = e.branch_id
            LEFT JOIN hris_employment_types et ON et.id = e.employment_type_id
            LEFT JOIN hris_employment_statuses es ON es.id = e.employment_status_id
            WHERE e.tenant_id = :tenant_id
                AND e.deleted_at IS NULL
                {$whereSearch}
        ");

        $this->bindEmployeeListValues($stmt, $tenant_id, $search);
        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    /** Dedicated export query; the paginated list and mutation scopes are unchanged. */
    public function getEmployeesForExport(int $tenantId, int $userId, array $branchIds, string $search, string $sortBy, string $sortDir, int $maxRows): array
    {
        if ($tenantId < 1 || $userId < 1 || !$branchIds || count($branchIds) > 200 || $maxRows < 1 || $maxRows > 5000) {
            throw new InvalidArgumentException('Invalid bounded export scope.');
        }
        foreach ($branchIds as $branchId) {
            if (!is_int($branchId) || $branchId < 1) {
                throw new InvalidArgumentException('Invalid export branch scope.');
            }
        }
        $allowedSorts = ['employee_no', 'name', 'branch_name', 'date_hired', 'employment_status', 'created_at'];
        if (!in_array($sortBy, $allowedSorts, true) || !in_array($sortDir, ['ASC', 'DESC'], true)) {
            throw new InvalidArgumentException('Invalid export order.');
        }
        $orderBy = $this->sortColumn($sortBy);
        $whereSearch = $this->employeeSearchSql($search);
        $branchParameters = implode(', ', array_map(static fn ($key) => ':export_branch_' . $key, array_keys(array_values($branchIds))));
        $stmt = $this->db->prepare("
            SELECT e.employee_no,
                CONCAT_WS(' ', e.firstname, e.middlename, e.lastname) AS employee_name,
                b.name AS branch_name, e.date_hired, es.name AS employment_status
            FROM hris_employees e
            INNER JOIN tenant_branches b ON b.id = e.branch_id AND b.tenant_id = e.tenant_id AND b.deleted_at IS NULL
            LEFT JOIN hris_employment_types et ON et.id = e.employment_type_id AND (et.tenant_id = e.tenant_id OR et.tenant_id IS NULL)
            LEFT JOIN hris_employment_statuses es ON es.id = e.employment_status_id AND (es.tenant_id = e.tenant_id OR es.tenant_id IS NULL)
            WHERE e.tenant_id = :tenant_id AND e.deleted_at IS NULL
                AND e.branch_id IN ({$branchParameters})
                AND EXISTS (
                    SELECT 1 FROM user_branches ub
                    INNER JOIN users u ON u.id = ub.user_id AND u.tenant_id = e.tenant_id AND u.deleted_at IS NULL
                    WHERE ub.user_id = :export_user_id AND ub.branch_id = e.branch_id AND ub.deleted_at IS NULL
                )
                {$whereSearch}
            ORDER BY {$orderBy} {$sortDir}, e.created_at DESC, e.id ASC
            LIMIT :export_limit
        ");
        $this->bindEmployeeListValues($stmt, $tenantId, $search);
        $stmt->bindValue(':export_user_id', $userId, PDO::PARAM_INT);
        foreach (array_values($branchIds) as $index => $branchId) {
            $stmt->bindValue(':export_branch_' . $index, $branchId, PDO::PARAM_INT);
        }
        // One sentinel row detects overflow. Never silently return a truncated workbook.
        $stmt->bindValue(':export_limit', $maxRows + 1, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) > $maxRows) {
            throw new EmployeeExportException('Too many employees to export. Narrow your search and try again.');
        }
        return $rows;
    }

    public function getEmployeeById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT e.*, b.name AS branch_name, et.name AS employment_type, es.name AS employment_status,
                p.name AS position, ou.name AS org_unit
            FROM hris_employees e
            LEFT JOIN tenant_branches b ON b.id = e.branch_id
            LEFT JOIN hris_employment_types et ON et.id = e.employment_type_id
            LEFT JOIN hris_employment_statuses es ON es.id = e.employment_status_id
            LEFT JOIN hris_positions p ON p.id = e.position_id
            LEFT JOIN hris_org_units ou ON ou.id = e.org_unit_id
            WHERE e.id = :id
                AND e.tenant_id = :tenant_id
            LIMIT 1
        ");

        $stmt->execute(['id' => $id, 'tenant_id' => $tenant_id]);

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

        $stmt->execute([
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

        return (int) $this->db->lastInsertId();
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
        ");

        return $stmt->execute([
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
        ]);
    }

    public function deleteEmployee(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("UPDATE hris_employees SET status = 'INACTIVE', deleted_at = NOW() WHERE id = :id AND tenant_id = :tenant_id");
        return $stmt->execute(['id' => $id, 'tenant_id' => $tenant_id]);
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
        $stmt = $this->db->prepare("SELECT id FROM hris_employment_statuses WHERE code = 'ACTIVE' AND (tenant_id = :tenant_id OR tenant_id IS NULL) LIMIT 1 ");
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

        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
    }

    public function getEmployeeCompensation(int $id, int $tenant_id)
    {
        $stmt = $this->db->prepare("
            SELECT * from payroll_employee_compensations WHERE
            tenant_id = :tenant_id and employee_id = :employee_id
        ");

        $stmt->execute(['tenant_id' => $tenant_id, 'employee_id' => $id,]);

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
            VALUES
            (
                :tenant_id,
                :employee_id,
                :salary_type,
                :payroll_frequency,
                :salary_rate,
                :effective_from,
                :effective_to,
                NOW()
            
            )
            ";

        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            'tenant_id' => $tenant_id,
            'employee_id' => $data['employee_id'],
            'salary_type' => $data['salary_type'],
            'payroll_frequency' => $data['payroll_frequency'],
            'salary_rate' => $data['salary_rate'],
            'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateEmployeeCompensation(int $id, int $tenant_id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE payroll_employee_compensations
            SET 
  
                employee_id = :employee_id,
                salary_type = :salary_type,
                payroll_frequency = :payroll_frequency,
                salary_rate = :salary_rate,
                effective_from = :effective_from,
                effective_to = :effective_to,
                updated_at = NOW()
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id,
            'employee_id' => $data['employee_id'],
            'salary_type' => $data['salary_type'],
            'payroll_frequency' => $data['payroll_frequency'],
            'salary_rate' => $data['salary_rate'],
            'effective_from' => $data['effective_from'],
            'effective_to' => $data['effective_to'],
            'id' => $id

        ]);

        if ($stmt->rowCount() > 0) {
            return true;
        }

        $check = $this->db->prepare("
                SELECT id
                FROM payroll_employee_compensations
                WHERE id = :id
                AND tenant_id = :tenant_id
                LIMIT 1
            ");

        $check->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);

        return (bool) $check->fetchColumn();
    }
}