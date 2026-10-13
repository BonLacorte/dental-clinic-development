<?php

class LeaveModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    private function sortColumn(string $sortBy, string $table): string
    {
        $columns = [
            'requests' => [
                'employee_name' => 'employee_name',
                'leave_type' => 'leave_type',
                'date_from' => 'lr.date_from',
                'date_to' => 'lr.date_to',
                'status' => 'lr.status',
                'approved_by_name' => 'approved_by_name',
                'approved_at' => 'lr.approved_at',
                'created_at' => 'lr.created_at'
            ],
            'types' => [
                'name' => 'name',
                'is_paid' => 'is_paid',
                'created_at' => 'created_at'
            ]
        ];

        return $columns[$table][$sortBy] ?? $columns[$table]['created_at'];
    }

    private function sortDirection(string $sortDir): string
    {
        return strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';
    }

    public function getLeaveRequests(
        int $tenant_id,
        int $limit,
        int $offset,
        string $search = '',
        string $sortBy = 'created_at',
        string $sortDir = 'DESC'
    ): array {
        $orderBy = $this->sortColumn($sortBy, 'requests');
        $direction = $this->sortDirection($sortDir);
        $whereSearch = $this->leaveRequestSearchSql($search);

        $stmt = $this->db->prepare("
            SELECT
                lr.*,
                CONCAT(e.lastname, ', ', e.firstname, IFNULL(CONCAT(' ', e.middlename), '')) AS employee_name,
                e.employee_no,
                lt.name AS leave_type,
                approver.name AS approved_by_name
            FROM hris_leave_requests lr
            LEFT JOIN hris_employees e
                ON e.id = lr.employee_id
                AND e.tenant_id = lr.tenant_id
            LEFT JOIN hris_leave_types lt
                ON lt.id = lr.leave_type_id
                AND (lt.tenant_id = lr.tenant_id OR lt.tenant_id IS NULL)
            LEFT JOIN users approver
                ON approver.id = lr.approved_by
                AND approver.tenant_id = lr.tenant_id
            WHERE lr.tenant_id = :tenant_id
                $whereSearch
            ORDER BY $orderBy $direction
            LIMIT :limit OFFSET :offset
        ");

        $this->bindLeaveRequestListValues($stmt, $tenant_id, $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalLeaveRequests(int $tenant_id, string $search = ''): int
    {
        $whereSearch = $this->leaveRequestSearchSql($search);

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM hris_leave_requests lr
            LEFT JOIN hris_employees e
                ON e.id = lr.employee_id
                AND e.tenant_id = lr.tenant_id
            LEFT JOIN hris_leave_types lt
                ON lt.id = lr.leave_type_id
                AND (lt.tenant_id = lr.tenant_id OR lt.tenant_id IS NULL)
            LEFT JOIN users approver
                ON approver.id = lr.approved_by
                AND approver.tenant_id = lr.tenant_id
            WHERE lr.tenant_id = :tenant_id
                $whereSearch
        ");

        $this->bindLeaveRequestListValues($stmt, $tenant_id, $search);
        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function leaveRequestSearchSql(string $search): string
    {
        if ($search === '') {
            return '';
        }

        return "
            AND CONCAT_WS('|',
                e.employee_no,
                e.firstname,
                e.middlename,
                e.lastname,
                lt.name,
                lr.date_from,
                lr.date_to,
                lr.status,
                lr.reason,
                approver.name,
                approver.email,
                lr.approved_at,
                lr.created_at
            ) LIKE :search
        ";
    }

    private function bindLeaveRequestListValues(PDOStatement $stmt, int $tenant_id, string $search): void
    {
        $stmt->bindValue(':tenant_id', $tenant_id, PDO::PARAM_INT);

        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
    }

    public function getLeaveRequestById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM hris_leave_requests
            WHERE id = :id
                AND tenant_id = :tenant_id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createLeaveRequest(int $tenant_id, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO hris_leave_requests (
                tenant_id, employee_id, leave_type_id, date_from,
                date_to, reason, status, approved_by, approved_at, created_at
            )
            VALUES (
                :tenant_id, :employee_id, :leave_type_id, :date_from,
                :date_to, :reason, :status, :approved_by, :approved_at, NOW()
            )
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id,
            'employee_id' => $data['employee_id'],
            'leave_type_id' => $data['leave_type_id'],
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
            'reason' => $data['reason'] ?? null,
            'status' => $data['status'],
            'approved_by' => $data['approved_by'] ?? null,
            'approved_at' => $data['approved_at'] ?? null
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateLeaveRequest(int $id, int $tenant_id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_leave_requests
            SET
                employee_id = :employee_id,
                leave_type_id = :leave_type_id,
                date_from = :date_from,
                date_to = :date_to,
                reason = :reason,
                status = :status,
                approved_by = :approved_by,
                approved_at = :approved_at
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id,
            'employee_id' => $data['employee_id'],
            'leave_type_id' => $data['leave_type_id'],
            'date_from' => $data['date_from'],
            'date_to' => $data['date_to'],
            'reason' => $data['reason'] ?? null,
            'status' => $data['status'],
            'approved_by' => $data['approved_by'] ?? null,
            'approved_at' => $data['approved_at'] ?? null
        ]);
    }

    public function deleteLeaveRequest(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM hris_leave_requests
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);
    }

    public function getLeaveTypes(
        int $tenant_id,
        int $limit,
        int $offset,
        string $search = '',
        string $sortBy = 'created_at',
        string $sortDir = 'DESC'
    ): array {
        $orderBy = $this->sortColumn($sortBy, 'types');
        $direction = $this->sortDirection($sortDir);
        $whereSearch = $search !== '' ? "AND CONCAT_WS('|', name, is_paid, created_at) LIKE :search" : '';

        $stmt = $this->db->prepare("
            SELECT id, tenant_id, name, is_paid, created_at
            FROM hris_leave_types
            WHERE tenant_id = :tenant_id
                $whereSearch
            ORDER BY $orderBy $direction
            LIMIT :limit OFFSET :offset
        ");

        $this->bindSimpleListValues($stmt, $tenant_id, $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalLeaveTypes(int $tenant_id, string $search = ''): int
    {
        $whereSearch = $search !== '' ? "AND CONCAT_WS('|', name, is_paid, created_at) LIKE :search" : '';

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM hris_leave_types
            WHERE tenant_id = :tenant_id
                $whereSearch
        ");

        $this->bindSimpleListValues($stmt, $tenant_id, $search);
        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function bindSimpleListValues(PDOStatement $stmt, int $tenant_id, string $search): void
    {
        $stmt->bindValue(':tenant_id', $tenant_id, PDO::PARAM_INT);

        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
    }

    public function getLeaveTypeById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM hris_leave_types
            WHERE id = :id
                AND tenant_id = :tenant_id
            LIMIT 1
        ");

        $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createLeaveType(int $tenant_id, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO hris_leave_types (tenant_id, name, is_paid, created_at)
            VALUES (:tenant_id, :name, :is_paid, NOW())
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id,
            'name' => $data['name'],
            'is_paid' => $data['is_paid']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateLeaveType(int $id, int $tenant_id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_leave_types
            SET
                name = :name,
                is_paid = :is_paid
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id,
            'name' => $data['name'],
            'is_paid' => $data['is_paid']
        ]);
    }

    public function deleteLeaveType(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM hris_leave_types
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);
    }

    public function getLeaveTypeOptions(int $tenant_id): array
    {
        $stmt = $this->db->prepare("
            SELECT id, name, is_paid
            FROM hris_leave_types
            WHERE tenant_id = :tenant_id
                OR tenant_id IS NULL
            ORDER BY name ASC
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function employeeBelongsToTenant(int $employeeId, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM hris_employees
            WHERE id = :id
                AND tenant_id = :tenant_id
                AND deleted_at IS NULL
        ");

        $stmt->execute([
            'id' => $employeeId,
            'tenant_id' => $tenant_id
        ]);

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    public function leaveTypeAllowedForTenant(int $leaveTypeId, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM hris_leave_types
            WHERE id = :id
                AND (tenant_id = :tenant_id OR tenant_id IS NULL)
        ");

        $stmt->execute([
            'id' => $leaveTypeId,
            'tenant_id' => $tenant_id
        ]);

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    public function getEmployeeOptions(int $tenant_id): array
    {
        $stmt = $this->db->prepare("
            SELECT id, employee_no, firstname, middlename, lastname
            FROM hris_employees
            WHERE tenant_id = :tenant_id
                AND deleted_at IS NULL
                AND status = 'ACTIVE'
            ORDER BY lastname ASC, firstname ASC
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
