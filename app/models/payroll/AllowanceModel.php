<?php

class AllowanceModel extends Model
{
    protected string $table;
    protected array $recordFields = [];
    protected array $recordSortColumns = [];
    protected ?string $typeTable = null;
    protected ?string $typeForeignKey = null;
    protected array $typeFields = [];
    protected array $typeSortColumns = [];


    public function __construct()
    {
        parent::__construct();

        $this->table = 'payroll_employee_allowances';
        $this->recordFields = ['employee_id', 'allowance_type_id', 'amount', 'is_recurring', 'effective_from', 'effective_to'];
        $this->recordSortColumns = [
            'employee_name' => 'employee_name',
            'employee_no' => 'e.employee_no',
            'type_name' => 'type_name',
            'type_code' => 'type_code',
            'amount' => 'r.amount',
            'is_recurring' => 'r.is_recurring',
            'effective_from' => 'r.effective_from',
            'effective_to' => 'r.effective_to',
            'created_at' => 'r.created_at'
        ];
        $this->typeTable = 'payroll_allowance_types';
        $this->typeForeignKey = 'allowance_type_id';
        $this->typeFields = ['code', 'name', 'computation_type', 'default_value', 'taxable'];
        $this->typeSortColumns = [
            'code' => 'code',
            'name' => 'name',
            'computation_type' => 'computation_type',
            'default_value' => 'default_value',
            'taxable' => 'taxable',
            'created_at' => 'created_at'
        ];
    }

    public function getAllAllowances(int $tenant_id, int $limit, int $offset, string $search = '', string $sortBy = 'created_at', string $sortDir = 'DESC'): array
    {
        return $this->getRecords($tenant_id, $limit, $offset, $search, $sortBy, $sortDir);
    }

    public function getTotalAllowances(int $tenant_id, string $search = ''): int
    {
        return $this->getTotalRecords($tenant_id, $search);
    }

    public function getAllowanceById(int $id, int $tenant_id): array|false
    {
        return $this->getRecordById($id, $tenant_id);
    }

    public function createAllowance(int $tenant_id, array $data): int
    {
        return $this->createRecord($tenant_id, $data);
    }

    public function updateAllowance(int $id, int $tenant_id, array $data): bool
    {
        return $this->updateRecord($id, $tenant_id, $data);
    }

    public function deleteAllowance(int $id, int $tenant_id): bool
    {
        return $this->deleteRecord($id, $tenant_id);
    }

    public function getAllowanceTypes(int $tenant_id, int $limit, int $offset, string $search = '', string $sortBy = 'created_at', string $sortDir = 'DESC'): array
    {
        return $this->getTypes($tenant_id, $limit, $offset, $search, $sortBy, $sortDir);
    }

    public function getTotalAllowanceTypes(int $tenant_id, string $search = ''): int
    {
        return $this->getTotalTypes($tenant_id, $search);
    }

    public function getAllowanceTypeById(int $id, int $tenant_id): array|false
    {
        return $this->getTypeById($id, $tenant_id);
    }

    public function createAllowanceType(int $tenant_id, array $data): int
    {
        return $this->createType($tenant_id, $data);
    }

    public function updateAllowanceType(int $id, int $tenant_id, array $data): bool
    {
        return $this->updateType($id, $tenant_id, $data);
    }

    public function deleteAllowanceType(int $id, int $tenant_id): bool
    {
        return $this->deleteType($id, $tenant_id);
    }

    public function getAllowanceTypeOptions(int $tenant_id): array
    {
        return $this->getTypeOptions($tenant_id);
    }

    public function allowanceTypeAllowedForTenant(int $typeId, int $tenant_id): bool
    {
        return $this->typeAllowedForTenant($typeId, $tenant_id);
    }

    public function allowanceTypeCodeExists(string $code, int $tenant_id, ?int $ignoreId = null): bool
    {
        return $this->typeCodeExists($code, $tenant_id, $ignoreId);
    }

    protected function sortColumn(string $sortBy, array $columns, string $default): string
    {
        return $columns[$sortBy] ?? $default;
    }

    protected function sortDirection(string $sortDir): string
    {
        return strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';
    }

    protected function typeJoinSql(): string
    {
        if (!$this->typeTable || !$this->typeForeignKey) {
            return '';
        }

        return "
            LEFT JOIN {$this->typeTable} t
                ON t.id = r.{$this->typeForeignKey}
                AND (t.tenant_id = r.tenant_id OR t.tenant_id IS NULL)
        ";
    }

    protected function typeSelectSql(): string
    {
        if (!$this->typeTable) {
            return '';
        }

        return ",
                t.code AS type_code,
                t.name AS type_name";
    }

    public function getRecords(
        int $tenant_id,
        int $limit,
        int $offset,
        string $search = '',
        string $sortBy = 'created_at',
        string $sortDir = 'DESC'
    ): array {
        $orderBy = $this->sortColumn($sortBy, $this->recordSortColumns, 'r.created_at');
        $direction = $this->sortDirection($sortDir);
        $whereSearch = $this->recordSearchSql($search);
        $typeJoin = $this->typeJoinSql();
        $typeSelect = $this->typeSelectSql();

        $stmt = $this->db->prepare("
            SELECT
                r.*,
                CONCAT(e.lastname, ', ', e.firstname, IFNULL(CONCAT(' ', e.middlename), '')) AS employee_name,
                e.employee_no
                $typeSelect
            FROM {$this->table} r
            LEFT JOIN hris_employees e
                ON e.id = r.employee_id
                AND e.tenant_id = r.tenant_id
            $typeJoin
            WHERE r.tenant_id = :tenant_id
                $whereSearch
            ORDER BY $orderBy $direction
            LIMIT :limit OFFSET :offset
        ");

        $this->bindListValues($stmt, $tenant_id, $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalRecords(int $tenant_id, string $search = ''): int
    {
        $whereSearch = $this->recordSearchSql($search);
        $typeJoin = $this->typeJoinSql();

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM {$this->table} r
            LEFT JOIN hris_employees e
                ON e.id = r.employee_id
                AND e.tenant_id = r.tenant_id
            $typeJoin
            WHERE r.tenant_id = :tenant_id
                $whereSearch
        ");

        $this->bindListValues($stmt, $tenant_id, $search);
        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    protected function recordSearchSql(string $search): string
    {
        if ($search === '') {
            return '';
        }

        $columns = [
            'e.employee_no',
            'e.firstname',
            'e.middlename',
            'e.lastname',
            'r.created_at'
        ];

        foreach ($this->recordFields as $field) {
            $columns[] = "r.$field";
        }

        if ($this->typeTable) {
            $columns[] = 't.code';
            $columns[] = 't.name';
        }

        return "AND CONCAT_WS('|', " . implode(', ', $columns) . ") LIKE :search";
    }

    protected function bindListValues(PDOStatement $stmt, int $tenant_id, string $search): void
    {
        $stmt->bindValue(':tenant_id', $tenant_id, PDO::PARAM_INT);

        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
    }

    public function getRecordById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->table}
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

    public function createRecord(int $tenant_id, array $data): int
    {
        $columns = array_merge(['tenant_id'], $this->recordFields, ['created_at']);
        $placeholders = array_merge([':tenant_id'], array_map(fn($field) => ':' . $field, $this->recordFields), ['NOW()']);

        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (" . implode(', ', $columns) . ")
            VALUES (" . implode(', ', $placeholders) . ")
        ");

        $stmt->execute($this->params($tenant_id, $this->recordFields, $data));

        return (int) $this->db->lastInsertId();
    }

    public function updateRecord(int $id, int $tenant_id, array $data): bool
    {
        $sets = array_map(fn($field) => "$field = :$field", $this->recordFields);

        $stmt = $this->db->prepare("
            UPDATE {$this->table}
            SET " . implode(', ', $sets) . "
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        $params = $this->params($tenant_id, $this->recordFields, $data);
        $params['id'] = $id;

        return $stmt->execute($params);
    }

    protected function params(int $tenant_id, array $fields, array $data): array
    {
        $params = ['tenant_id' => $tenant_id];

        foreach ($fields as $field) {
            $params[$field] = $data[$field] ?? null;
        }

        return $params;
    }

    public function deleteRecord(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table}
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);
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

        $stmt->execute(['tenant_id' => $tenant_id]);

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
                AND status = 'ACTIVE'
        ");

        $stmt->execute([
            'id' => $employeeId,
            'tenant_id' => $tenant_id
        ]);

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    public function getTypes(
        int $tenant_id,
        int $limit,
        int $offset,
        string $search = '',
        string $sortBy = 'created_at',
        string $sortDir = 'DESC'
    ): array {
        $this->requireTypeTable();
        $orderBy = $this->sortColumn($sortBy, $this->typeSortColumns, 'created_at');
        $direction = $this->sortDirection($sortDir);
        $whereSearch = $this->typeSearchSql($search);

        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->typeTable}
            WHERE tenant_id = :tenant_id
                $whereSearch
            ORDER BY $orderBy $direction
            LIMIT :limit OFFSET :offset
        ");

        $this->bindListValues($stmt, $tenant_id, $search);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalTypes(int $tenant_id, string $search = ''): int
    {
        $this->requireTypeTable();
        $whereSearch = $this->typeSearchSql($search);

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM {$this->typeTable}
            WHERE tenant_id = :tenant_id
                $whereSearch
        ");

        $this->bindListValues($stmt, $tenant_id, $search);
        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    protected function typeSearchSql(string $search): string
    {
        if ($search === '') {
            return '';
        }

        $columns = array_merge($this->typeFields, ['created_at']);
        return "AND CONCAT_WS('|', " . implode(', ', $columns) . ") LIKE :search";
    }

    public function getTypeById(int $id, int $tenant_id): array|false
    {
        $this->requireTypeTable();
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->typeTable}
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

    public function createType(int $tenant_id, array $data): int
    {
        $this->requireTypeTable();
        $columns = array_merge(['tenant_id'], $this->typeFields, ['created_at']);
        $placeholders = array_merge([':tenant_id'], array_map(fn($field) => ':' . $field, $this->typeFields), ['NOW()']);

        $stmt = $this->db->prepare("
            INSERT INTO {$this->typeTable} (" . implode(', ', $columns) . ")
            VALUES (" . implode(', ', $placeholders) . ")
        ");

        $stmt->execute($this->params($tenant_id, $this->typeFields, $data));

        return (int) $this->db->lastInsertId();
    }

    public function updateType(int $id, int $tenant_id, array $data): bool
    {
        $this->requireTypeTable();
        $sets = array_map(fn($field) => "$field = :$field", $this->typeFields);

        $stmt = $this->db->prepare("
            UPDATE {$this->typeTable}
            SET " . implode(', ', $sets) . "
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        $params = $this->params($tenant_id, $this->typeFields, $data);
        $params['id'] = $id;

        return $stmt->execute($params);
    }

    public function deleteType(int $id, int $tenant_id): bool
    {
        $this->requireTypeTable();
        $stmt = $this->db->prepare("
            DELETE FROM {$this->typeTable}
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);
    }

    public function getTypeOptions(int $tenant_id): array
    {
        $this->requireTypeTable();
        $stmt = $this->db->prepare("
            SELECT *
            FROM {$this->typeTable}
            WHERE tenant_id = :tenant_id
                OR tenant_id IS NULL
            ORDER BY name ASC, code ASC
        ");

        $stmt->execute(['tenant_id' => $tenant_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function typeAllowedForTenant(int $typeId, int $tenant_id): bool
    {
        $this->requireTypeTable();
        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM {$this->typeTable}
            WHERE id = :id
                AND (tenant_id = :tenant_id OR tenant_id IS NULL)
        ");

        $stmt->execute([
            'id' => $typeId,
            'tenant_id' => $tenant_id
        ]);

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    public function typeCodeExists(string $code, int $tenant_id, ?int $ignoreId = null): bool
    {
        $this->requireTypeTable();
        $ignoreSql = $ignoreId ? 'AND id <> :ignore_id' : '';

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM {$this->typeTable}
            WHERE tenant_id = :tenant_id
                AND code = :code
                $ignoreSql
        ");

        $stmt->bindValue(':tenant_id', $tenant_id, PDO::PARAM_INT);
        $stmt->bindValue(':code', $code);

        if ($ignoreId) {
            $stmt->bindValue(':ignore_id', $ignoreId, PDO::PARAM_INT);
        }

        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    protected function requireTypeTable(): void
    {
        if (!$this->typeTable) {
            throw new Exception('This payroll model does not support type records.');
        }
    }
}
