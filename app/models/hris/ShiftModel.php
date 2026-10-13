<?php

class ShiftModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    private function sortColumn(string $sortBy): string
    {
        $columns = [
            'name' => 'name',
            'shift_type' => 'shift_type',
            'start_time' => 'start_time',
            'end_time' => 'end_time',
            'flex_start_time' => 'flex_start_time',
            'flex_end_time' => 'flex_end_time',
            'required_hours' => 'required_hours',
            'break_minutes' => 'break_minutes',
            'allow_overtime' => 'allow_overtime',
            'created_at' => 'created_at'
        ];

        return $columns[$sortBy] ?? 'created_at';
    }

    private function sortDirection(string $sortDir): string
    {
        return strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';
    }

    public function getAllShifts(
        int $tenant_id,
        int $limit,
        int $offset,
        string $search = '',
        string $sortBy = 'created_at',
        string $sortDir = 'DESC'
    ): array {
        $orderBy = $this->sortColumn($sortBy);
        $direction = $this->sortDirection($sortDir);
        $whereSearch = $this->searchSql($search);

        $stmt = $this->db->prepare("
            SELECT
                id, name, shift_type, start_time, end_time,
                flex_start_time, flex_end_time, required_hours,
                break_minutes, allow_overtime, created_at
            FROM hris_shifts
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

    public function getTotalShifts(int $tenant_id, string $search = ''): int
    {
        $whereSearch = $this->searchSql($search);

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM hris_shifts
            WHERE tenant_id = :tenant_id
                $whereSearch
        ");

        $this->bindListValues($stmt, $tenant_id, $search);
        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    private function searchSql(string $search): string
    {
        if ($search === '') {
            return '';
        }

        return "
            AND CONCAT_WS('|',
                name,
                shift_type,
                start_time,
                end_time,
                flex_start_time,
                flex_end_time,
                required_hours,
                break_minutes,
                allow_overtime,
                created_at
            ) LIKE :search
        ";
    }

    private function bindListValues(PDOStatement $stmt, int $tenant_id, string $search): void
    {
        $stmt->bindValue(':tenant_id', $tenant_id, PDO::PARAM_INT);

        if ($search !== '') {
            $stmt->bindValue(':search', '%' . $search . '%', PDO::PARAM_STR);
        }
    }

    public function getShiftById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM hris_shifts
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

    public function createShift(int $tenant_id, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO hris_shifts (
                tenant_id, name, shift_type, start_time, end_time,
                flex_start_time, flex_end_time, required_hours,
                break_minutes, allow_overtime, created_at
            )
            VALUES (
                :tenant_id, :name, :shift_type, :start_time, :end_time,
                :flex_start_time, :flex_end_time, :required_hours,
                :break_minutes, :allow_overtime, NOW()
            )
        ");

        $stmt->execute($this->shiftParams($tenant_id, $data));

        return (int) $this->db->lastInsertId();
    }

    public function updateShift(int $id, int $tenant_id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_shifts
            SET
                name = :name,
                shift_type = :shift_type,
                start_time = :start_time,
                end_time = :end_time,
                flex_start_time = :flex_start_time,
                flex_end_time = :flex_end_time,
                required_hours = :required_hours,
                break_minutes = :break_minutes,
                allow_overtime = :allow_overtime
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        $params = $this->shiftParams($tenant_id, $data);
        $params['id'] = $id;

        return $stmt->execute($params);
    }

    private function shiftParams(int $tenant_id, array $data): array
    {
        return [
            'tenant_id' => $tenant_id,
            'name' => $data['name'],
            'shift_type' => $data['shift_type'],
            'start_time' => $data['start_time'] ?: null,
            'end_time' => $data['end_time'] ?: null,
            'flex_start_time' => $data['flex_start_time'] ?: null,
            'flex_end_time' => $data['flex_end_time'] ?: null,
            'required_hours' => $data['required_hours'],
            'break_minutes' => $data['break_minutes'],
            'allow_overtime' => $data['allow_overtime']
        ];
    }

    public function deleteShift(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM hris_shifts
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);
    }
}
