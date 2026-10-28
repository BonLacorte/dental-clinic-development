<?php

/** Read-only Home summaries using the existing organization-wide list scope. */
class ModuleDashboardModel extends Model
{
    private const EMPLOYEES = <<<'SQL'
        SELECT COUNT(*) AS total_employees,
            COALESCE(SUM(CASE WHEN status = 'ACTIVE' THEN 1 ELSE 0 END), 0) AS active_employees
        FROM hris_employees
        WHERE tenant_id = :tenant_id AND deleted_at IS NULL
        SQL;
    private const LEAVES = <<<'SQL'
        SELECT COUNT(*) FROM hris_leave_requests
        WHERE tenant_id = :tenant_id AND status = 'PENDING'
        SQL;
    private const BRANCHES = <<<'SQL'
        SELECT COUNT(*) FROM tenant_branches
        WHERE tenant_id = :tenant_id AND deleted_at IS NULL
        SQL;
    private const RUNS = <<<'SQL'
        SELECT COUNT(*) AS total_runs,
            COALESCE(SUM(CASE WHEN status = 'DRAFT' THEN 1 ELSE 0 END), 0) AS draft_runs,
            COALESCE(SUM(CASE WHEN status = 'PROCESSED' THEN 1 ELSE 0 END), 0) AS processed_runs,
            COALESCE(SUM(CASE WHEN status = 'FINALIZED' THEN 1 ELSE 0 END), 0) AS finalized_runs
        FROM payroll_runs WHERE tenant_id = :tenant_id
        SQL;
    private const RECENT = <<<'SQL'
        SELECT pr.id, pr.run_code, pr.pay_date, pr.status,
            (SELECT COUNT(*) FROM payroll_run_employees pre
             WHERE pre.run_id = pr.id AND pre.tenant_id = pr.tenant_id) AS employee_count
        FROM payroll_runs pr WHERE pr.tenant_id = :tenant_id
        ORDER BY pr.pay_date DESC, pr.id DESC LIMIT 5
        SQL;
    private const COMPUTED_DRAFTS = <<<'SQL'
        SELECT COUNT(*) FROM payroll_runs pr
        WHERE pr.tenant_id = :tenant_id AND pr.status = 'DRAFT'
          AND EXISTS (SELECT 1 FROM payroll_run_employees pre
                      WHERE pre.run_id = pr.id AND pre.tenant_id = pr.tenant_id
                        AND pre.computation_status = 'COMPUTED')
        SQL;
    private const ERROR_RESULTS = <<<'SQL'
        SELECT COUNT(*) FROM payroll_run_employees pre
        INNER JOIN payroll_runs pr ON pr.id = pre.run_id AND pr.tenant_id = pre.tenant_id
        WHERE pr.tenant_id = :tenant_id AND pre.computation_status = 'ERROR'
        SQL;

    public function getHrisSummary(int $tenantId): array
    {
        $this->requireTenant($tenantId);
        $employees = $this->query(self::EMPLOYEES, $tenantId)->fetch(PDO::FETCH_ASSOC);
        if (!is_array($employees)) throw new UnexpectedValueException('Employee summary unavailable.');
        return [
            'total_employees' => (int) $employees['total_employees'],
            'active_employees' => (int) $employees['active_employees'],
            'pending_leaves' => $this->count(self::LEAVES, $tenantId),
            'branches' => $this->count(self::BRANCHES, $tenantId),
        ];
    }

    public function getPayrollOverview(int $tenantId): array
    {
        $this->requireTenant($tenantId);
        $summary = $this->query(self::RUNS, $tenantId)->fetch(PDO::FETCH_ASSOC);
        if (!is_array($summary)) throw new UnexpectedValueException('Payroll summary unavailable.');
        return [
            'summary' => array_map('intval', $summary),
            'recent' => $this->query(self::RECENT, $tenantId)->fetchAll(PDO::FETCH_ASSOC),
            'attention' => [
                'computed_draft_runs' => $this->count(self::COMPUTED_DRAFTS, $tenantId),
                'error_results' => $this->count(self::ERROR_RESULTS, $tenantId),
            ],
        ];
    }


    private const EMPLOYEE_BRANCHES = <<<'SQL'
        SELECT b.id AS group_id, b.name AS label, COUNT(*) AS value
        FROM hris_employees e
        LEFT JOIN tenant_branches b ON b.id = e.branch_id
          AND b.tenant_id = e.tenant_id AND b.deleted_at IS NULL
        WHERE e.tenant_id = :tenant_id AND e.deleted_at IS NULL
        GROUP BY b.id, b.name ORDER BY value DESC, b.name ASC, b.id ASC
        SQL;
    private const EMPLOYEE_DEPARTMENTS = <<<'SQL'
        SELECT g.id AS group_id, g.name AS label, COUNT(*) AS value
        FROM hris_employees e
        LEFT JOIN hris_org_units g ON g.id = e.org_unit_id
          AND g.tenant_id = e.tenant_id AND g.deleted_at IS NULL
        WHERE e.tenant_id = :tenant_id AND e.deleted_at IS NULL
        GROUP BY g.id, g.name ORDER BY value DESC, g.name ASC, g.id ASC
        SQL;
    private const EMPLOYEE_POSITIONS = <<<'SQL'
        SELECT g.id AS group_id, g.name AS label, COUNT(*) AS value
        FROM hris_employees e
        LEFT JOIN hris_positions g ON g.id = e.position_id
          AND g.tenant_id = e.tenant_id AND g.deleted_at IS NULL
        WHERE e.tenant_id = :tenant_id AND e.deleted_at IS NULL
        GROUP BY g.id, g.name ORDER BY value DESC, g.name ASC, g.id ASC
        SQL;
    private const APPROVED_LEAVE = <<<'SQL'
        SELECT lr.employee_id, lr.date_from, lr.date_to
        FROM hris_leave_requests lr
        INNER JOIN hris_employees e ON e.id = lr.employee_id
          AND e.tenant_id = lr.tenant_id AND e.deleted_at IS NULL
        WHERE lr.tenant_id = :tenant_id AND lr.status = 'APPROVED'
          AND lr.date_from <= :date_to AND lr.date_to >= :date_from
        SQL;
    private const PENDING_AGE = <<<'SQL'
        SELECT created_at, COUNT(*) AS value FROM hris_leave_requests
        WHERE tenant_id = :tenant_id AND status = 'PENDING'
        GROUP BY created_at
        SQL;
    private const RUN_STATUSES = <<<'SQL'
        SELECT status, COUNT(*) AS value FROM payroll_runs
        WHERE tenant_id = :tenant_id GROUP BY status
        SQL;
    private const SELECTED_RUN = <<<'SQL'
        SELECT id, run_code, pay_date, status FROM payroll_runs
        WHERE tenant_id = :tenant_id AND id = :run_id
        SQL;
    private const DEFAULT_RUN = <<<'SQL'
        SELECT id, run_code, pay_date, status FROM payroll_runs
        WHERE tenant_id = :tenant_id AND (status IS NULL OR status <> 'CANCELLED')
        ORDER BY pay_date DESC, id DESC LIMIT 1
        SQL;
    private const RUN_OPTIONS = <<<'SQL'
        SELECT id, run_code, pay_date, status FROM payroll_runs
        WHERE tenant_id = :tenant_id AND (run_code LIKE :run_search ESCAPE '!' OR :search_empty = 1)
        ORDER BY pay_date DESC, id DESC LIMIT 51
        SQL;
    private const RUN_PROGRESS = <<<'SQL'
        SELECT pre.computation_status AS status, COUNT(*) AS value
        FROM payroll_run_employees pre
        INNER JOIN payroll_runs pr ON pr.id = pre.run_id AND pr.tenant_id = pre.tenant_id
        WHERE pr.tenant_id = :tenant_id AND pr.id = :run_id
        GROUP BY pre.computation_status
        SQL;
    private const RUN_DATES = <<<'SQL'
        SELECT id, run_code, status, cutoff_date_from, cutoff_date_to, pay_date
        FROM payroll_runs WHERE tenant_id = :tenant_id AND (
            cutoff_date_from BETWEEN :start_from AND :end_from OR
            cutoff_date_to BETWEEN :start_to AND :end_to OR
            pay_date BETWEEN :start_pay AND :end_pay)
        ORDER BY pay_date ASC, id ASC
        SQL;

    /** Each chart fails independently; existing summary queries stay untouched. */
    public function getHrisVisuals(int $tenantId, array $filters, DateTimeImmutable $today): array
    {
        $this->requireTenant($tenantId);
        return [
            'branches' => $this->visualPanel(fn() => $this->employeeGroups(self::EMPLOYEE_BRANCHES, $tenantId, 'Unassigned / unavailable branch')),
            'leave_calendar' => $this->visualPanel(fn() => $this->approvedLeaveCalendar($tenantId, $filters['start'])),
            'leave_age' => $this->visualPanel(fn() => $this->pendingLeaveAge($tenantId, $today)),
            'composition' => $this->visualPanel(fn() => $this->employeeGroups(
                $filters['group'] === 'position' ? self::EMPLOYEE_POSITIONS : self::EMPLOYEE_DEPARTMENTS,
                $tenantId, 'Unassigned / unavailable ' . $filters['group'])),
        ];
    }

    public function getPayrollVisuals(int $tenantId, array $filters, DateTimeImmutable $today): array
    {
        $this->requireTenant($tenantId);
        return [
            'progress' => $this->visualPanel(fn() => $this->runProgress($tenantId, $filters)),
            'statuses' => $this->visualPanel(fn() => ['rows' => $this->statusGroups(
                $this->query(self::RUN_STATUSES, $tenantId)->fetchAll(PDO::FETCH_ASSOC),
                ['DRAFT' => 'Draft', 'PROCESSING' => 'Processing', 'PROCESSED' => 'Processed', 'FINALIZED' => 'Finalized', 'CANCELLED' => 'Cancelled'])]),
            'timeline' => $this->visualPanel(fn() => $this->runTimeline($tenantId, $filters['start'], $today)),
        ];
    }

    private function visualPanel(callable $read): array
    {
        try {
            return ['available' => true] + $read();
        } catch (Throwable $error) {
            error_log('Dashboard visual read failed: ' . get_class($error));
            return ['available' => false, 'notice' => 'This visual could not be loaded. Refresh the page to retry.'];
        }
    }

    private function visualQuery(string $sql, int $tenantId, array $parameters = []): PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute(['tenant_id' => $tenantId] + $parameters);
        return $statement;
    }

    private function employeeGroups(string $sql, int $tenantId, string $fallback): array
    {
        $rows = $this->query($sql, $tenantId)->fetchAll(PDO::FETCH_ASSOC);
        return ['rows' => array_map(static fn($row) => [
            'label' => $row['group_id'] === null ? $fallback : (string) $row['label'],
            'value' => (int) $row['value'], 'tone' => 'teal',
        ], $rows)];
    }

    private function exactDate($raw): ?DateTimeImmutable
    {
        if (!is_string($raw) || !preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/D', $raw)) return null;
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $raw, new DateTimeZone('Asia/Manila'));
        return $date && $date->format('Y-m-d') === $raw && $date->format('Y') >= '1000' ? $date : null;
    }

    private function approvedLeaveCalendar(int $tenantId, string $start): array
    {
        $first = $this->exactDate($start);
        if (!$first) throw new InvalidArgumentException('Invalid calendar date.');
        $last = $first->modify('+29 days');
        $days = [];
        for ($i = 0; $i < 30; $i++) $days[$first->modify("+$i days")->format('Y-m-d')] = [];
        $statement = $this->visualQuery(self::APPROVED_LEAVE, $tenantId, [
            'date_from' => $start, 'date_to' => $last->format('Y-m-d'),
        ]);
        $invalid = 0;
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $from = $this->exactDate($row['date_from']); $to = $this->exactDate($row['date_to']);
            if (!$from || !$to || $from > $to) { $invalid++; continue; }
            foreach ($days as $date => &$employees) {
                if ($date >= $from->format('Y-m-d') && $date <= $to->format('Y-m-d')) $employees[(int) $row['employee_id']] = true;
            }
            unset($employees);
        }
        return ['days' => array_map('count', $days), 'invalid' => $invalid];
    }

    private function pendingLeaveAge(int $tenantId, DateTimeImmutable $today): array
    {
        $counts = [0, 0, 0, 0, 0];
        $now = $today->setTimezone(new DateTimeZone('Asia/Manila'));
        $statement = $this->query(self::PENDING_AGE, $tenantId);
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $raw = (string) ($row['created_at'] ?? '');
            $date = preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2} [0-9]{2}:[0-9]{2}:[0-9]{2}$/D', $raw)
                ? DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $raw, new DateTimeZone('Asia/Manila')) : false;
            if (!$date || $date->format('Y-m-d H:i:s') !== $raw || $date > $now || $date->format('Y') < '1000') $index = 4;
            else {
                $age = (int) $date->setTime(0, 0)->diff($now->setTime(0, 0))->days;
                $index = $age <= 2 ? 0 : ($age <= 7 ? 1 : ($age <= 14 ? 2 : 3));
            }
            $counts[$index] += (int) $row['value'];
        }
        $labels = ['0–2 days', '3–7 days', '8–14 days', '15+ days', 'Missing / invalid / future timestamp'];
        $rows = [];
        foreach ($counts as $i => $value) $rows[] = ['label' => $labels[$i], 'value' => $value, 'tone' => $i === 4 ? 'neutral' : 'teal'];
        return ['rows' => $rows];
    }

    private function statusGroups(array $source, array $labels): array
    {
        $counts = array_fill_keys(array_keys($labels), 0); $counts['OTHER'] = 0;
        $tones = ['PENDING' => 'pending', 'COMPUTED' => 'computed', 'ERROR' => 'error', 'DRAFT' => 'neutral',
            'PROCESSING' => 'pending', 'PROCESSED' => 'info', 'FINALIZED' => 'computed', 'CANCELLED' => 'error', 'OTHER' => 'neutral'];
        foreach ($source as $row) {
            $key = array_key_exists((string) $row['status'], $labels) ? (string) $row['status'] : 'OTHER';
            $counts[$key] += (int) $row['value'];
        }
        $rows = [];
        foreach ($counts as $key => $value) $rows[] = ['label' => $labels[$key] ?? 'Other / unknown', 'value' => $value, 'tone' => $tones[$key]];
        return $rows;
    }

    private function runProgress(int $tenantId, array $filters): array
    {
        $search = $filters['run_q'];
        $options = $this->visualQuery(self::RUN_OPTIONS, $tenantId, [
            'run_search' => '%' . strtr($search, ['!' => '!!', '%' => '!%', '_' => '!_']) . '%',
            'search_empty' => $search === '' ? 1 : 0,
        ])->fetchAll(PDO::FETCH_ASSOC);
        $more = count($options) > 50; $options = array_slice($options, 0, 50);
        $run = $filters['run_invalid'] ? false : ($filters['run_id'] === null
            ? $this->query(self::DEFAULT_RUN, $tenantId)->fetch(PDO::FETCH_ASSOC)
            : $this->visualQuery(self::SELECTED_RUN, $tenantId, ['run_id' => $filters['run_id']])->fetch(PDO::FETCH_ASSOC));
        if (!$run) return ['rows' => [], 'run' => null, 'options' => $options, 'more' => $more,
            'notice' => $filters['run_id'] !== null || $filters['run_invalid'] ? 'The selected run is unavailable. Choose a run from the list.' : 'No non-cancelled payroll run is available. You can select an existing cancelled run to review its saved results.'];
        $rows = $this->visualQuery(self::RUN_PROGRESS, $tenantId, ['run_id' => (int) $run['id']])->fetchAll(PDO::FETCH_ASSOC);
        return ['rows' => $this->statusGroups($rows, ['PENDING' => 'Pending', 'COMPUTED' => 'Computed', 'ERROR' => 'Error']),
            'run' => $run, 'options' => $options, 'more' => $more];
    }

    private function runTimeline(int $tenantId, string $start, DateTimeImmutable $today): array
    {
        $first = $this->exactDate($start);
        if (!$first) throw new InvalidArgumentException('Invalid timeline date.');
        $last = $first->modify('+29 days');
        $now = $today->setTimezone(new DateTimeZone('Asia/Manila'))->setTime(0, 0);
        $past = $now->modify('-30 days');
        $runs = [];
        foreach ([[$first, $last], [$past, $now->modify('-1 day')]] as [$windowStart, $windowEnd]) {
            $parameters = [];
            foreach (['from', 'to', 'pay'] as $field) {
                $parameters['start_' . $field] = $windowStart->format('Y-m-d');
                $parameters['end_' . $field] = $windowEnd->format('Y-m-d');
            }
            $statement = $this->visualQuery(self::RUN_DATES, $tenantId, $parameters);
            while ($run = $statement->fetch(PDO::FETCH_ASSOC)) $runs[(int) $run['id']] = $run;
        }
        $events = []; $review = [];
        foreach ($runs as $run) {
            foreach (['cutoff_date_from' => 'Cutoff starts', 'cutoff_date_to' => 'Cutoff ends', 'pay_date' => 'Pay date'] as $field => $label) {
                $date = $this->exactDate($run[$field]);
                if (!$date) continue;
                $event = ['id' => (int) $run['id'], 'run_code' => $run['run_code'], 'status' => $run['status'] ?? 'Unknown', 'date' => $date->format('Y-m-d'), 'label' => $label];
                if ($date >= $first && $date <= $last) $events[] = $event;
                if ($date >= $past && $date < $now && !in_array($run['status'], ['FINALIZED', 'CANCELLED'], true)) $review[] = $event;
            }
        }
        $sort = static fn($a, $b) => [$a['date'], $a['id'], $a['label']] <=> [$b['date'], $b['id'], $b['label']];
        usort($events, $sort); usort($review, $sort);
        return ['events' => $events, 'review' => array_reverse($review)];
    }

    private function requireTenant(int $tenantId): void
    {
        if ($tenantId <= 0) throw new InvalidArgumentException('A current organization is required.');
    }

    private function query(string $sql, int $tenantId): PDOStatement
    {
        $statement = $this->db->prepare($sql);
        $statement->execute(['tenant_id' => $tenantId]);
        return $statement;
    }

    private function count(string $sql, int $tenantId): int
    {
        $value = $this->query($sql, $tenantId)->fetchColumn();
        if ($value === false) throw new UnexpectedValueException('Dashboard count unavailable.');
        return (int) $value;
    }
}
