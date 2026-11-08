<?php

class EmployeeFinancialsModel extends Model
{
    /** The caller supplies assigned branches; an empty list follows the existing all-branch policy. */
    private function scope(int $employeeId, int $tenantId, array $branches): array
    {
        $sql = 'e.id = :employee_id AND e.tenant_id = :tenant_id AND e.deleted_at IS NULL';
        $params = ['employee_id' => $employeeId, 'tenant_id' => $tenantId];
        if ($branches) {
            $keys = [];
            foreach (array_values($branches) as $index => $branch) {
                $keys[] = ':branch_' . $index;
                $params['branch_' . $index] = (int) $branch;
            }
            $sql .= ' AND e.branch_id IN (' . implode(', ', $keys) . ')';
        }
        return [$sql, $params];
    }

    public function employeeExists(int $employeeId, int $tenantId, array $branches): bool
    {
        [$where, $params] = $this->scope($employeeId, $tenantId, $branches);
        $stmt = $this->db->prepare("SELECT e.id FROM hris_employees e WHERE {$where} LIMIT 1");
        $stmt->execute($params);
        return (bool) $stmt->fetchColumn();
    }

    private function join(array $definition): string
    {
        return "FROM {$definition['table']} r INNER JOIN hris_employees e ON e.id = r.employee_id AND e.tenant_id = r.tenant_id";
    }

    public function records(array $definition, int $employeeId, int $tenantId, array $branches, int $page): array
    {
        [$where, $params] = $this->scope($employeeId, $tenantId, $branches);
        $from = $this->join($definition);
        $count = $this->db->prepare("SELECT COUNT(*) {$from} WHERE {$where}");
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / 10));
        $page = min($page, $pages);
        $typeSelect = '';
        if (isset($definition['typeTable'])) {
            $from .= " LEFT JOIN {$definition['typeTable']} t ON t.id = r.{$definition['typeField']} AND (t.tenant_id = r.tenant_id OR t.tenant_id IS NULL)";
            $typeSelect = ', t.name AS type_name, t.code AS type_code';
        }
        $offset = ($page - 1) * 10;
        $stmt = $this->db->prepare("SELECT r.* {$typeSelect} {$from} WHERE {$where} ORDER BY r.created_at DESC, r.id DESC LIMIT 10 OFFSET {$offset}");
        $stmt->execute($params);
        return ['data' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'currentPage' => $page, 'totalPages' => $pages, 'totalRecords' => $total];
    }

    public function record(array $definition, int $id, int $employeeId, int $tenantId, array $branches): array|false
    {
        [$where, $params] = $this->scope($employeeId, $tenantId, $branches);
        $params['record_id'] = $id;
        $from = $this->join($definition);
        $stmt = $this->db->prepare("SELECT r.* {$from} WHERE {$where} AND r.id = :record_id LIMIT 1");
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function types(array $definition, int $tenantId): array
    {
        if (!isset($definition['typeTable'])) {
            return [];
        }
        $stmt = $this->db->prepare("SELECT id, code, name FROM {$definition['typeTable']} WHERE tenant_id = :tenant_id OR tenant_id IS NULL ORDER BY name, code");
        $stmt->execute(['tenant_id' => $tenantId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function typeAllowed(array $definition, int $typeId, int $tenantId): bool
    {
        $stmt = $this->db->prepare("SELECT id FROM {$definition['typeTable']} WHERE id = :id AND (tenant_id = :tenant_id OR tenant_id IS NULL) LIMIT 1");
        $stmt->execute(['id' => $typeId, 'tenant_id' => $tenantId]);
        return (bool) $stmt->fetchColumn();
    }

    /** Only catalog fields reach SQL; employee/tenant identity always comes from the route/session. */
    public function save(array $definition, ?int $id, array $data, int $employeeId, int $tenantId, array $branches): int|false
    {
        [$where, $params] = $this->scope($employeeId, $tenantId, $branches);
        $fields = array_column($definition['fields'], 'name');
        foreach ($fields as $field) {
            $params['value_' . $field] = $data[$field];
        }
        if ($id === null) {
            $values = implode(', ', array_map(static fn($field) => ':value_' . $field, $fields));
            $stmt = $this->db->prepare("INSERT INTO {$definition['table']} (tenant_id, employee_id, " . implode(', ', $fields) . ", created_at) SELECT e.tenant_id, e.id, {$values}, NOW() FROM hris_employees e WHERE {$where}");
        } else {
            $params['record_id'] = $id;
            $sets = implode(', ', array_map(static fn($field) => "r.{$field} = :value_{$field}", $fields));
            $stmt = $this->db->prepare("UPDATE {$definition['table']} r INNER JOIN hris_employees e ON e.id = r.employee_id AND e.tenant_id = r.tenant_id SET {$sets} WHERE {$where} AND r.id = :record_id");
        }
        $stmt->execute($params);
        if ($stmt->rowCount() === 0) {
            // MySQL reports zero changed rows for an unchanged update; distinguish it from a missing record.
            return $id !== null && $this->record($definition, $id, $employeeId, $tenantId, $branches) ? $id : false;
        }
        return $id ?? (int) $this->db->lastInsertId();
    }

    public function deleteRecord(array $definition, int $id, int $employeeId, int $tenantId, array $branches): bool
    {
        [$where, $params] = $this->scope($employeeId, $tenantId, $branches);
        $params['record_id'] = $id;
        $from = $this->join($definition);
        $stmt = $this->db->prepare("DELETE r {$from} WHERE {$where} AND r.id = :record_id");
        $stmt->execute($params);
        return $stmt->rowCount() === 1;
    }
}
