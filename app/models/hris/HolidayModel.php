<?php

class HolidayModel extends Model
{
    public function __construct()
    {
        parent::__construct('default');
    }

    private function sortColumn(string $sortBy): string
    {
        $columns = [
            'holiday_date' => 'holiday_date',
            'name' => 'name',
            'holiday_type' => 'holiday_type',
            'pay_multiplier' => 'pay_multiplier',
            'allow_work' => 'allow_work',
            'created_at' => 'created_at'
        ];

        return $columns[$sortBy] ?? 'holiday_date';
    }

    private function sortDirection(string $sortDir): string
    {
        return strtoupper($sortDir) === 'ASC' ? 'ASC' : 'DESC';
    }

    public function getAllHolidays(
        int $tenant_id,
        int $limit,
        int $offset,
        string $search = '',
        string $sortBy = 'holiday_date',
        string $sortDir = 'DESC'
    ): array {
        $orderBy = $this->sortColumn($sortBy);
        $direction = $this->sortDirection($sortDir);
        $whereSearch = $this->searchSql($search);

        $stmt = $this->db->prepare("
            SELECT id, holiday_date, name, holiday_type, pay_multiplier, allow_work, created_at
            FROM hris_holidays
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

    public function getTotalHolidays(int $tenant_id, string $search = ''): int
    {
        $whereSearch = $this->searchSql($search);

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM hris_holidays
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
                holiday_date,
                name,
                holiday_type,
                pay_multiplier,
                allow_work,
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

    public function getHolidayById(int $id, int $tenant_id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM hris_holidays
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

    public function holidayDateExists(string $holidayDate, int $tenant_id, ?int $ignoreId = null): bool
    {
        $ignoreSql = $ignoreId ? 'AND id <> :ignore_id' : '';

        $stmt = $this->db->prepare("
            SELECT COUNT(*) AS total
            FROM hris_holidays
            WHERE tenant_id = :tenant_id
                AND holiday_date = :holiday_date
                $ignoreSql
        ");

        $stmt->bindValue(':tenant_id', $tenant_id, PDO::PARAM_INT);
        $stmt->bindValue(':holiday_date', $holidayDate);

        if ($ignoreId) {
            $stmt->bindValue(':ignore_id', $ignoreId, PDO::PARAM_INT);
        }

        $stmt->execute();

        return (int) $stmt->fetch(PDO::FETCH_ASSOC)['total'] > 0;
    }

    public function createHoliday(int $tenant_id, array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO hris_holidays (
                tenant_id, holiday_date, name, holiday_type,
                pay_multiplier, allow_work, created_at
            )
            VALUES (
                :tenant_id, :holiday_date, :name, :holiday_type,
                :pay_multiplier, :allow_work, NOW()
            )
        ");

        $stmt->execute([
            'tenant_id' => $tenant_id,
            'holiday_date' => $data['holiday_date'],
            'name' => $data['name'],
            'holiday_type' => $data['holiday_type'] ?? null,
            'pay_multiplier' => $data['pay_multiplier'],
            'allow_work' => $data['allow_work']
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateHoliday(int $id, int $tenant_id, array $data): bool
    {
        $stmt = $this->db->prepare("
            UPDATE hris_holidays
            SET
                holiday_date = :holiday_date,
                name = :name,
                holiday_type = :holiday_type,
                pay_multiplier = :pay_multiplier,
                allow_work = :allow_work
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id,
            'holiday_date' => $data['holiday_date'],
            'name' => $data['name'],
            'holiday_type' => $data['holiday_type'] ?? null,
            'pay_multiplier' => $data['pay_multiplier'],
            'allow_work' => $data['allow_work']
        ]);
    }

    public function deleteHoliday(int $id, int $tenant_id): bool
    {
        $stmt = $this->db->prepare("
            DELETE FROM hris_holidays
            WHERE id = :id
                AND tenant_id = :tenant_id
        ");

        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenant_id
        ]);
    }
}
