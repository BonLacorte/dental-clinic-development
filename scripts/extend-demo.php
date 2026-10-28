<?php
/** Additive fictional fixtures for the recognized local demo only. Never initializes a database. */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }

final class DemoExpansion
{
    private const BUNDLE = 'DEMOEXP-v1';
    private const TABLES = [
        'hris_org_units', 'hris_shifts', 'hris_employees', 'hris_employee_shift_schedules',
        'hris_attendance_logs', 'hris_leave_requests', 'hris_holidays',
        'payroll_employee_compensations', 'payroll_allowance_types', 'payroll_commission_types',
        'payroll_incentive_types', 'payroll_deduction_types', 'payroll_employee_allowances',
        'payroll_employee_commissions', 'payroll_employee_incentives', 'payroll_employee_deductions',
        'payroll_cash_advances', 'payroll_runs', 'payroll_run_employees',
        'payroll_run_employee_days', 'payroll_run_items',
    ];
    private PDO $db;
    private int $tenant;
    private int $admin;
    private string $directory;
    private array $columns = [];

    public function __construct(private string $root)
    {
        date_default_timezone_set('Asia/Manila');
        $config = (require $root . '/config/local.php')['default'] ?? [];
        $docker = getenv('MONTERO_DEMO_DOCKER') === '1';
        $expected = ['host' => $docker ? 'db' : '127.0.0.1', 'port' => $docker ? 3306 : 3307,
            'dbname' => 'monterodental_demo', 'user' => 'montero_demo'];
        foreach ($expected as $key => $value) {
            if ((string) ($config[$key] ?? '') !== (string) $value) throw new RuntimeException('Refusing an unrecognized demo connection.');
        }
        $this->db = new PDO('mysql:host=' . $expected['host'] . ';port=' . $expected['port'] . ';dbname=monterodental_demo;charset=utf8mb4',
            $config['user'], $config['pass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]);
        if ($this->db->query('SELECT DATABASE()')->fetchColumn() !== 'monterodental_demo') throw new RuntimeException('Unexpected database.');
        if ((int) $this->db->query('SELECT version FROM local_demo_metadata WHERE id = 1')->fetchColumn() !== 1) throw new RuntimeException('Recognized initialization marker required.');
        $tenant = $this->rows("SELECT id FROM tenants WHERE code = 'DEMO' AND name = 'Montero Dental Demo' AND deleted_at IS NULL");
        if (count($tenant) !== 1 || (int) $this->db->query('SELECT COUNT(*) FROM tenants')->fetchColumn() !== 1) throw new RuntimeException('Expected the isolated, single-organization demo.');
        $this->tenant = (int) $tenant[0]['id'];
        $admins = $this->rows('SELECT id FROM users WHERE tenant_id = ? AND email = ?', [$this->tenant, 'admin@example.test']);
        if (count($admins) !== 1) throw new RuntimeException('Existing fictional demo administrator required.');
        $this->admin = (int) $admins[0]['id'];
        // A copied or repurposed database must not be backed up or seeded as a fictional demo.
        $emails = $this->rows('SELECT email FROM hris_employees');
        foreach ($emails as $employee) {
            $email = (string) ($employee['email'] ?? '');
            if ($email !== '' && !str_ends_with(strtolower($email), '.test')) throw new RuntimeException('Non-demo employee identity detected; refusing changes.');
        }
        $engines = $this->rows('SELECT TABLE_NAME, ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE()');
        $engines = array_column($engines, 'ENGINE', 'TABLE_NAME');
        foreach (self::TABLES as $table) {
            if (($engines[$table] ?? '') !== 'InnoDB') throw new RuntimeException('Expected transactional demo table: ' . $table);
            $this->columns[$table] = array_column($this->db->query('SHOW COLUMNS FROM `' . $table . '`')->fetchAll(PDO::FETCH_ASSOC), null, 'Field');
        }
        $this->directory = $root . '/storage/demo-fixtures/' . self::BUNDLE;
    }

    private function rows(string $sql, array $parameters = []): array
    {
        $statement = $this->db->prepare($sql); $statement->execute($parameters);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function one(string $sql, array $parameters = []): array
    {
        $rows = $this->rows($sql, $parameters);
        if (count($rows) !== 1) throw new RuntimeException('Required demo lookup is missing or ambiguous.');
        return $rows[0];
    }

    private static function money(int $centavos): string
    {
        if ($centavos < 0) throw new InvalidArgumentException('Negative fixture amount.');
        return intdiv($centavos, 100) . '.' . str_pad((string) ($centavos % 100), 2, '0', STR_PAD_LEFT);
    }

    private function snapshot(): array
    {
        $snapshot = [];
        foreach (self::TABLES as $table) $snapshot[$table] = $this->db->query('SELECT * FROM `' . $table . '` ORDER BY id')->fetchAll(PDO::FETCH_ASSOC);
        return $snapshot;
    }

    private static function rowHash(array $row): string
    {
        ksort($row);
        return hash('sha256', json_encode($row, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE));
    }

    private static function hashes(array $snapshot): array
    {
        $hashes = [];
        foreach ($snapshot as $table => $rows) {
            $hashes[$table] = [];
            foreach ($rows as $row) $hashes[$table][(int) $row['id']] = self::rowHash($row);
        }
        return $hashes;
    }

    private function verifyHashes(array $expected): int
    {
        $verified = 0;
        foreach ($expected as $table => $rows) {
            if (!in_array($table, self::TABLES, true) || !is_array($rows)) throw new RuntimeException('Invalid fixture journal.');
            foreach ($rows as $id => $hash) {
                if (!ctype_digit((string) $id) || (int) $id <= 0 || !is_string($hash)) throw new RuntimeException('Invalid journal row identity.');
                $actual = $this->rows('SELECT * FROM `' . $table . '` WHERE id = ?', [(int) $id]);
                if (count($actual) !== 1 || self::rowHash($actual[0]) !== $hash) throw new RuntimeException('Recorded row changed or missing; refusing to overwrite: ' . $table . ' #' . $id);
                $verified++;
            }
        }
        return $verified;
    }

    private function writeJson(string $path, array $data): void
    {
        $temporary = $path . '.pending-' . bin2hex(random_bytes(4));
        $content = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
        if (file_put_contents($temporary, $content, LOCK_EX) === false || !chmod($temporary, 0600) || !rename($temporary, $path)) throw new RuntimeException('Cannot safely save demo backup/journal.');
    }

    private function journal(string $name): ?array
    {
        $path = $this->directory . '/' . $name . '.json';
        if (!is_file($path)) return null;
        $data = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        if (($data['bundle'] ?? '') !== self::BUNDLE || ($data['tenant_id'] ?? null) !== $this->tenant) throw new RuntimeException('Journal identity mismatch.');
        return $data;
    }

    private function namespaceCount(): int
    {
        $count = 0;
        foreach (['hris_employees' => 'employee_no', 'hris_org_units' => 'code', 'hris_shifts' => 'name',
            'hris_holidays' => 'name', 'hris_leave_requests' => 'reason', 'payroll_runs' => 'run_code',
            'payroll_allowance_types' => 'code', 'payroll_commission_types' => 'code',
            'payroll_incentive_types' => 'code', 'payroll_deduction_types' => 'code'] as $table => $field) {
            $count += (int) $this->rows('SELECT COUNT(*) AS n FROM `' . $table . '` WHERE tenant_id = ? AND `' . $field . '` LIKE ?', [$this->tenant, 'DEMOEXP%'])[0]['n'];
        }
        return $count;
    }

    private function add(array &$plan, string $key, string $table, array $data): void
    {
        if (!in_array($table, self::TABLES, true)) throw new LogicException('Table outside fixture allowlist.');
        foreach ($data as $column => $value) {
            if (!isset($this->columns[$table][$column])) throw new RuntimeException('Expected schema column unavailable: ' . $table . '.' . $column);
            $type = $this->columns[$table][$column]['Type'];
            if (str_starts_with($type, 'enum(') && is_string($value)) {
                preg_match_all("/'([^']*)'/", $type, $matches);
                if (!in_array($value, $matches[1], true)) throw new RuntimeException('Fixture value incompatible with live schema.');
            }
        }
        $plan[] = ['key' => $key, 'table' => $table, 'data' => ['tenant_id' => $this->tenant] + $data];
    }

    private static function reference(string $key): array { return ['ref' => $key]; }

    private function periods(DateTimeImmutable $anchor): array
    {
        $occupied = $this->rows("SELECT cutoff_date_from, cutoff_date_to FROM payroll_runs WHERE tenant_id = ? AND (status IS NULL OR status <> 'CANCELLED')", [$this->tenant]);
        $occupied = array_fill_keys(array_map(static fn($row) => $row['cutoff_date_from'] . '/' . $row['cutoff_date_to'], $occupied), true);
        $periods = [];
        for ($offset = -3; $offset <= 1; $offset++) {
            $month = $anchor->modify('first day of this month')->modify(($offset >= 0 ? '+' : '') . $offset . ' months');
            foreach ([1, 2] as $period) {
                $pay = $period === 1 ? $month->modify('+14 days') : $month->setDate((int) $month->format('Y'), (int) $month->format('m'), min(30, (int) $month->format('t')));
                $from = $period === 1 ? $month->modify('-1 day') : $month->modify('+14 days');
                $to = $pay->modify('-1 day');
                if ($pay < $anchor->modify('-90 days') || $pay > $anchor->modify('+29 days') || isset($occupied[$from->format('Y-m-d') . '/' . $to->format('Y-m-d')])) continue;
                $periods[] = ['period_no' => $period, 'period_id' => $period, 'cutoff_date_from' => $from->format('Y-m-d'), 'cutoff_date_to' => $to->format('Y-m-d'), 'pay_date' => $pay->format('Y-m-d')];
            }
        }
        if (count($periods) < 6) throw new RuntimeException('Not enough distinct free demo cutoff periods; existing runs were preserved.');
        return array_slice($periods, -6);
    }

    public function plan(DateTimeImmutable $anchor): array
    {
        $anchor = $anchor->setTimezone(new DateTimeZone('Asia/Manila'));
        $today = $anchor->setTime(0, 0); $stamp = $anchor->format('Y-m-d H:i:s');
        $branches = [];
        foreach (['QC', 'MKT'] as $code) $branches[] = (int) $this->one('SELECT id FROM tenant_branches WHERE tenant_id = ? AND code = ? AND deleted_at IS NULL', [$this->tenant, $code])['id'];
        $office = (int) $this->one("SELECT id FROM hris_org_units WHERE tenant_id = ? AND code = 'CLINIC' AND deleted_at IS NULL", [$this->tenant])['id'];
        $positions = [];
        foreach (['Dentist', 'Dental Assistant', 'Receptionist'] as $position) $positions[] = (int) $this->one('SELECT id FROM hris_positions WHERE tenant_id = ? AND name = ? AND deleted_at IS NULL', [$this->tenant, $position])['id'];
        $employment = (int) $this->one("SELECT id FROM hris_employment_types WHERE tenant_id = ? AND code = 'REGULAR' AND deleted_at IS NULL", [$this->tenant])['id'];
        $active = (int) $this->one("SELECT id FROM hris_employment_statuses WHERE tenant_id = ? AND code = 'ACTIVE' AND deleted_at IS NULL", [$this->tenant])['id'];
        $resigned = (int) $this->one("SELECT id FROM hris_employment_statuses WHERE tenant_id = ? AND code = 'RESIGNED' AND deleted_at IS NULL", [$this->tenant])['id'];
        $leaveType = (int) $this->one("SELECT id FROM hris_leave_types WHERE tenant_id = ? AND name = 'Demo Vacation Leave'", [$this->tenant])['id'];
        $plan = [];
        foreach (['admin' => 'Administration', 'service' => 'Patient Services'] as $key => $name) $this->add($plan, 'office-' . $key, 'hris_org_units', ['code' => 'DEMOEXP-' . strtoupper($key), 'name' => 'DEMOEXP ' . $name, 'type' => 'DEPARTMENT', 'description' => 'Fictional demonstration department', 'created_at' => $stamp]);
        foreach ([['08:00:00', '17:00:00', 'Early'], ['10:00:00', '19:00:00', 'Late']] as $i => [$start, $end, $name]) $this->add($plan, 'shift-' . $i, 'hris_shifts', ['name' => 'DEMOEXP ' . $name . ' Shift (fictional)', 'shift_type' => 'FIXED', 'start_time' => $start, 'end_time' => $end, 'required_hours' => '8.00', 'break_minutes' => 60, 'allow_overtime' => 1, 'created_at' => $stamp]);
        $firstNames = ['Amara','Benedict','Celia','Darius','Elena','Felix','Gia','Hugo','Isla','Jonas','Kiara','Loren','Mira','Nico','Opal','Paolo','Quinn','Rhea','Soren','Talia'];
        $rates = []; $employeeShifts = []; $attendance = [];
        for ($i = 1; $i <= 20; $i++) {
            $key = 'employee-' . $i; $inactive = $i >= 19; $branch = $branches[$i <= 12 ? 0 : 1]; $shift = $i % 2;
            $hire = $today->modify('-' . (180 + $i * 7) . ' days')->format('Y-m-d');
            $resign = $inactive ? $today->modify('-10 days')->format('Y-m-d') : null;
            $group = $i <= 12 ? $office : self::reference($i <= 16 ? 'office-admin' : 'office-service');
            $position = $i <= 6 ? 0 : ($i <= 14 ? 1 : 2);
            $rate = $position === 0 ? 180000 + 5000 * ($i % 3) : ($position === 1 ? 90000 + 2500 * ($i % 4) : 80000 + 2500 * ($i % 2));
            $rates[$i] = $rate; $employeeShifts[$i] = $shift;
            $this->add($plan, $key, 'hris_employees', ['employee_no' => sprintf('DEMOEXP-%03d', $i), 'firstname' => $firstNames[$i - 1], 'lastname' => 'Fictional', 'email' => sprintf('demoexp%03d@example.test', $i), 'branch_id' => $branch, 'position_id' => $positions[$position], 'org_unit_id' => $group, 'employment_type_id' => $employment, 'employment_status_id' => $inactive ? $resigned : $active, 'date_hired' => $hire, 'date_resigned' => $resign, 'status' => $inactive ? 'INACTIVE' : 'ACTIVE', 'created_at' => $stamp]);
            $this->add($plan, 'comp-' . $i, 'payroll_employee_compensations', ['employee_id' => self::reference($key), 'salary_type' => 'DAILY', 'payroll_frequency' => 'SEMI_MONTHLY', 'salary_rate' => self::money($rate), 'effective_from' => $hire, 'effective_to' => $resign, 'created_at' => $stamp]);
            $this->add($plan, 'schedule-' . $i, 'hris_employee_shift_schedules', ['employee_id' => self::reference($key), 'shift_id' => self::reference('shift-' . $shift), 'effective_from' => $hire, 'effective_to' => $resign, 'created_at' => $stamp]);
            $day = ($inactive ? new DateTimeImmutable($resign) : $today)->modify('-1 day'); $days = [];
            while (count($days) < 20) { if ($day->format('N') !== '7') $days[] = $day; $day = $day->modify('-1 day'); }
            $days = array_reverse($days);
            foreach ($days as $j => $day) {
                $mode = ($i + $j) % 20; $date = $day->format('Y-m-d'); $start = $shift ? '10:00:00' : '08:00:00'; $end = $shift ? '19:00:00' : '17:00:00';
                $in = new DateTimeImmutable($date . ' ' . $start); $out = new DateTimeImmutable($date . ' ' . $end);
                if ($mode === 0) $in = $in->modify('+15 minutes');
                if ($mode === 1) $out = $out->modify('-30 minutes');
                if ($mode === 2) $out = $out->modify('+60 minutes');
                $hours = $mode === 3 || $mode === 4 ? null : number_format(($out->getTimestamp() - $in->getTimestamp() - 3600) / 3600, 2, '.', '');
                $this->add($plan, 'attendance-' . $i . '-' . $j, 'hris_attendance_logs', ['branch_id' => $branch, 'employee_id' => self::reference($key), 'attendance_date' => $date, 'time_in' => $mode === 4 ? null : $in->format('Y-m-d H:i:s'), 'time_out' => $mode === 3 ? null : $out->format('Y-m-d H:i:s'), 'status' => 'PRESENT', 'break_minutes' => 60, 'late_minutes' => $mode === 0 ? 15 : 0, 'undertime_minutes' => $mode === 1 ? 30 : 0, 'overtime_minutes' => $mode === 2 ? 60 : 0, 'computed_expected_hours' => '8.00', 'computed_actual_hours' => $hours, 'created_at' => $stamp]);
            }
        }
        $ages = [0, 2, 3, 7, 8, 14, 15, 22];
        for ($i = 0; $i < 20; $i++) {
            $status = $i < 8 ? 'PENDING' : ($i < 14 ? 'APPROVED' : ($i < 18 ? 'REJECTED' : 'CANCELLED'));
            $employee = ($i % 18) + 1; $from = $today->modify('+' . (1 + $i % 12) . ' days'); $to = $from->modify('+1 day');
            if ($status === 'APPROVED') {
                $examples = [[1,1,3],[1,2,4],[2,2,3],[3,3,5],[4,10,11],[5,15,16]];
                [$employee, $fromOffset, $toOffset] = $examples[$i - 8];
                $from = $today->modify('+' . $fromOffset . ' days'); $to = $today->modify('+' . $toOffset . ' days');
            }
            $created = $i < 8 ? $anchor->modify('-' . $ages[$i] . ' days') : $anchor->modify('-2 days');
            $this->add($plan, 'leave-' . $i, 'hris_leave_requests', ['employee_id' => self::reference('employee-' . $employee), 'leave_type_id' => $leaveType, 'date_from' => $from->format('Y-m-d'), 'date_to' => $to->format('Y-m-d'), 'reason' => 'DEMOEXP fictional ' . strtolower($status) . ' request #' . ($i + 1) . '; demonstration only', 'status' => $status, 'approved_by' => $status === 'APPROVED' ? $this->admin : null, 'approved_at' => $status === 'APPROVED' ? $anchor->modify('-1 day')->format('Y-m-d H:i:s') : null, 'created_at' => $created->format('Y-m-d H:i:s')]);
        }
        $occupiedHolidays = array_fill_keys(array_column($this->rows('SELECT holiday_date FROM hris_holidays WHERE tenant_id = ?', [$this->tenant]), 'holiday_date'), true);
        foreach ([10, 20] as $i => $offset) {
            $date = $today->modify('+' . $offset . ' days');
            while (isset($occupiedHolidays[$date->format('Y-m-d')])) $date = $date->modify('+1 day');
            $occupiedHolidays[$date->format('Y-m-d')] = true;
            $this->add($plan, 'holiday-' . $i, 'hris_holidays', ['holiday_date' => $date->format('Y-m-d'), 'name' => 'DEMOEXP Fictional Team Holiday ' . ($i + 1), 'holiday_type' => 'SPECIAL', 'pay_multiplier' => '1.00', 'allow_work' => 0, 'created_at' => $stamp]);
        }
        foreach (['allowance' => 10, 'commission' => 6, 'incentive' => 6, 'deduction' => 8] as $kind => $count) {
            $typeTable = 'payroll_' . $kind . '_types';
            $types = $this->rows('SELECT id FROM `' . $typeTable . '` WHERE tenant_id = ? AND computation_type = ? AND deleted_at IS NULL ORDER BY id LIMIT 1', [$this->tenant, 'FIXED']);
            $type = $types ? (int) $types[0]['id'] : self::reference('type-' . $kind);
            if (!$types) $this->add($plan, 'type-' . $kind, $typeTable, ['code' => 'DEMOEXP-' . strtoupper($kind), 'name' => 'DEMOEXP Fictional ' . ucfirst($kind), 'computation_type' => 'FIXED', 'default_value' => '100.00', 'created_at' => $stamp]);
            for ($i = 1; $i <= $count; $i++) {
                $record = ['employee_id' => self::reference('employee-' . $i), $kind . '_type_id' => $type, 'amount' => self::money(($kind === 'deduction' ? 10000 : 15000) + $i * 2500), 'effective_from' => $today->modify('-30 days')->format('Y-m-d'), 'created_at' => $stamp];
                if ($kind !== 'commission') $record['is_recurring'] = 1;
                $this->add($plan, $kind . '-' . $i, 'payroll_employee_' . $kind . 's', $record);
            }
        }
        for ($i = 1; $i <= 4; $i++) $this->add($plan, 'advance-' . $i, 'payroll_cash_advances', ['employee_id' => self::reference('employee-' . $i), 'amount' => self::money(200000 + 50000 * $i), 'remaining_balance' => self::money(200000 + 50000 * $i), 'deduction_type' => 'INSTALLMENT', 'installment_amount' => '500.00', 'status' => 'ACTIVE', 'created_at' => $stamp]);
        $statuses = ['FINALIZED', 'PROCESSED', 'CANCELLED', 'PROCESSING', 'DRAFT', 'DRAFT'];
        foreach ($this->periods($today) as $r => $period) {
            $status = $statuses[$r]; $runKey = 'run-' . $r; $scenario = $r === 4 ? 'Mixed calculation states' : ($r === 5 ? 'All pending' : ucfirst(strtolower($status)));
            $record = $period + ['run_code' => sprintf('DEMOEXP-%02d', $r + 1), 'payroll_name' => 'DEMOEXP Fictional scenario: ' . $scenario . ' (illustration)', 'status' => $status, 'created_by' => $this->admin, 'created_at' => min($stamp, $period['cutoff_date_from'] . ' 09:00:00')];
            if (in_array($status, ['PROCESSED', 'FINALIZED'], true)) { $record['processed_by'] = $this->admin; $record['processed_at'] = $period['cutoff_date_to'] . ' 18:00:00'; }
            if ($status === 'CANCELLED') { $record['cancelled_by'] = $this->admin; $record['cancelled_at'] = $period['cutoff_date_to'] . ' 18:00:00'; $record['cancellation_reason'] = 'DEMOEXP fictional cancellation scenario; no actual processing or payment.'; }
            $this->add($plan, $runKey, 'payroll_runs', $record);
            $days = []; $date = new DateTimeImmutable($period['cutoff_date_from']);
            while (count($days) < 8 && $date->format('Y-m-d') <= $period['cutoff_date_to']) { if ($date->format('N') !== '7') $days[] = $date; $date = $date->modify('+1 day'); }
            if (count($days) !== 8) throw new RuntimeException('Fixture cutoff has fewer than eight clinic days.');
            foreach ([1,2,3,7,8,13,15,17] as $j => $i) {
                $computed = $r <= 1 || ($r === 3 && $j < 4) || ($r === 4 && $j < 4);
                $calculation = $computed ? 'COMPUTED' : ($r === 4 && $j === 7 ? 'ERROR' : 'PENDING');
                $preKey = $runKey . '-employee-' . $i; $shift = $employeeShifts[$i];
                $basic = $computed ? $rates[$i] * 8 : 0; $earnings = $computed ? 75000 : 0; $deductions = $computed ? 12500 : 0;
                $this->add($plan, $preKey, 'payroll_run_employees', ['run_id' => self::reference($runKey), 'employee_id' => self::reference('employee-' . $i), 'shift_id' => self::reference('shift-' . $shift), 'shift_name' => 'DEMOEXP ' . ($shift ? 'Late' : 'Early') . ' Shift (fictional)', 'scheduled_time_in' => $shift ? '10:00:00' : '08:00:00', 'scheduled_time_out' => $shift ? '19:00:00' : '17:00:00', 'scheduled_break_minutes' => 60, 'allow_overtime' => 1,
                    // Existing loadEmployees stores required_hours in this legacy-named field.
                    'scheduled_work_minutes' => '8.00', 'salary_type' => 'DAILY', 'payroll_frequency' => 'SEMI_MONTHLY', 'salary_rate' => self::money($rates[$i]), 'days_present' => $computed ? '8.00' : '0.00', 'basic_pay' => self::money($basic), 'total_earnings' => self::money($earnings), 'gross_pay' => self::money($basic + $earnings), 'total_deductions' => self::money($deductions), 'net_pay' => self::money($basic + $earnings - $deductions), 'computation_status' => $calculation, 'error_message' => $calculation === 'ERROR' ? 'DEMOEXP simulated calculation error for demonstration; no processing performed.' : null, 'computed_at' => $computed ? min($stamp, $period['cutoff_date_to'] . ' 18:00:00') : null, 'created_at' => $record['created_at']]);
                foreach ($days as $d => $day) $this->add($plan, $preKey . '-day-' . $d, 'payroll_run_employee_days', ['run_employee_id' => self::reference($preKey), 'attendance_date' => $day->format('Y-m-d'), 'day_type' => 'REGULAR', 'is_holiday' => 0, 'allow_work' => 1, 'time_in' => $computed ? $day->format('Y-m-d') . ($shift ? ' 10:00:00' : ' 08:00:00') : null, 'time_out' => $computed ? $day->format('Y-m-d') . ($shift ? ' 19:00:00' : ' 17:00:00') : null, 'shift_id' => self::reference('shift-' . $shift), 'scheduled_time_in' => $shift ? '10:00:00' : '08:00:00', 'scheduled_time_out' => $shift ? '19:00:00' : '17:00:00', 'scheduled_break_minutes' => 60, 'is_present' => $computed ? 1 : 0, 'late_minutes' => 0, 'undertime_minutes' => 0, 'overtime_minutes' => 0, 'created_at' => $record['created_at']]);
                if ($computed) foreach ([['EARNING','BASIC_PAY',$basic],['EARNING','DEMOEXP_ADDITIONAL',$earnings],['DEDUCTION','DEMOEXP_DEDUCTION',$deductions]] as $item => [$type, $code, $amount]) $this->add($plan, $preKey . '-item-' . $item, 'payroll_run_items', ['run_employee_id' => self::reference($preKey), 'item_type' => $type, 'item_code' => $code, 'description' => 'DEMOEXP fictional ' . $code . '; illustrative snapshot, not validated payroll', 'amount' => self::money($amount), 'source_type' => 'DEMOEXP_FIXTURE', 'created_at' => $record['created_at']]);
            }
        }
        return $plan;
    }

    private function validateInserted(array $inserted): void
    {
        foreach ($inserted as $table => $ids) foreach ($ids as $id) {
            $row = $this->one('SELECT * FROM `' . $table . '` WHERE id = ?', [$id]);
            if ((int) $row['tenant_id'] !== $this->tenant) throw new RuntimeException('Inserted row escaped the demo organization.');
        }
        $employees = $inserted['hris_employees'] ?? [];
        if (count($employees) !== 20 || count($inserted['hris_attendance_logs'] ?? []) !== 400 || count($inserted['hris_leave_requests'] ?? []) !== 20 || count($inserted['payroll_runs'] ?? []) !== 6 || count($inserted['payroll_run_employees'] ?? []) !== 48) throw new RuntimeException('Fixture row count mismatch.');
        foreach ($inserted['payroll_run_employees'] as $id) {
            $row = $this->one('SELECT * FROM payroll_run_employees WHERE id = ?', [$id]);
            $cents = static fn($v) => (int) str_replace('.', '', (string) $v);
            if ($cents($row['basic_pay']) + $cents($row['total_earnings']) !== $cents($row['gross_pay']) || $cents($row['gross_pay']) - $cents($row['total_deductions']) !== $cents($row['net_pay'])) throw new RuntimeException('Illustrative payroll arithmetic does not reconcile.');
            if (!in_array((int) $row['employee_id'], $employees, true) || !in_array((int) $row['run_id'], $inserted['payroll_runs'], true)) throw new RuntimeException('Snapshot references a pre-existing employee/run.');
            $items = $this->rows('SELECT item_type, item_code, amount FROM payroll_run_items WHERE run_employee_id = ?', [$id]);
            $gross = 0; $deductions = 0;
            foreach ($items as $item) { if ($item['item_type'] === 'EARNING') $gross += $cents($item['amount']); else $deductions += $cents($item['amount']); }
            if ($gross !== $cents($row['gross_pay']) || $deductions !== $cents($row['total_deductions'])) throw new RuntimeException('Snapshot item totals do not reconcile.');
        }
    }

    public function run(bool $apply): array
    {
        $lock = null;
        if ($apply) {
            if (!is_dir($this->directory) && !mkdir($this->directory, 0700, true) && !is_dir($this->directory)) throw new RuntimeException('Cannot create ignored demo backup directory.');
            $lock = fopen($this->directory . '/loader.lock', 'c');
            if (!$lock || !flock($lock, LOCK_EX | LOCK_NB)) throw new RuntimeException('Another fixture operation is running.');
        }
        try {
            $journal = $this->journal('manifest'); $intent = $this->journal('intent');
            if ($journal) {
                $old = $this->verifyHashes($journal['before_hashes']); $new = $this->verifyHashes($journal['inserted_hashes']);
                return ['status' => 'already_complete', 'bundle' => self::BUNDLE, 'anchor' => $journal['anchor'], 'existing_rows_preserved' => $old, 'fixture_rows_verified' => $new, 'inserted' => 0, 'counts' => $journal['counts'], 'runs' => $journal['runs']];
            }
            if ($intent) {
                $present = 0; $expected = 0;
                foreach ($intent['inserted_hashes'] as $table => $rows) {
                    if (!in_array($table, self::TABLES, true)) throw new RuntimeException('Invalid interrupted-operation journal.');
                    foreach ($rows as $id => $hash) { $expected++; $present += count($this->rows('SELECT id FROM `' . $table . '` WHERE id = ?', [(int) $id])); }
                }
                if ($present === $expected && $expected > 0) {
                    $old = $this->verifyHashes($intent['before_hashes']); $this->verifyHashes($intent['inserted_hashes']);
                    if ($apply) $this->writeJson($this->directory . '/manifest.json', $intent);
                    return ['status' => 'committed_bundle_verified', 'existing_rows_preserved' => $old, 'fixture_rows_verified' => $expected, 'inserted' => 0, 'counts' => $intent['counts'], 'runs' => $intent['runs']];
                }
                if ($present !== 0) throw new RuntimeException('Partial or conflicting interrupted fixture records; refusing changes.');
            }
            if ($this->namespaceCount() !== 0) throw new RuntimeException('Unmanaged or conflicting DEMOEXP records exist; refusing changes.');
            $anchor = new DateTimeImmutable('now', new DateTimeZone('Asia/Manila'));
            $plan = $this->plan($anchor); $counts = array_count_values(array_column($plan, 'table'));
            $before = $this->snapshot(); $beforeHashes = self::hashes($before);
            $report = ['status' => $apply ? 'applying' : 'dry_run', 'bundle' => self::BUNDLE, 'anchor' => $anchor->format(DATE_ATOM), 'existing_rows' => array_sum(array_map('count', $before)), 'planned_insertions' => count($plan), 'counts' => $counts,
                'runs' => array_map(static fn($r) => $r['data'], array_values(array_filter($plan, static fn($r) => $r['table'] === 'payroll_runs')))];
            if (!$apply) return $report;
            $backup = $this->directory . '/before-' . $anchor->format('Ymd-His') . '-' . bin2hex(random_bytes(3)) . '.json';
            $this->writeJson($backup, ['bundle' => self::BUNDLE, 'tenant_id' => $this->tenant, 'anchor' => $report['anchor'], 'tables' => $before, 'hashes' => $beforeHashes]);
            $backupHash = hash_file('sha256', $backup);
            $this->db->beginTransaction();
            try {
                $references = []; $inserted = [];
                foreach ($plan as $entry) {
                    $data = $entry['data'];
                    foreach ($data as &$value) if (is_array($value)) {
                        if (!isset($references[$value['ref'] ?? ''])) throw new LogicException('Unresolved fixture reference.');
                        $value = $references[$value['ref']];
                    }
                    unset($value);
                    $statement = $this->db->prepare('INSERT INTO `' . $entry['table'] . '` (`' . implode('`,`', array_keys($data)) . '`) VALUES (' . implode(',', array_fill(0, count($data), '?')) . ')');
                    $statement->execute(array_values($data)); $id = (int) $this->db->lastInsertId();
                    $references[$entry['key']] = $id; $inserted[$entry['table']][] = $id;
                }
                $this->validateInserted($inserted);
                $old = $this->verifyHashes($beforeHashes); $newHashes = [];
                foreach ($inserted as $table => $ids) foreach ($ids as $id) $newHashes[$table][$id] = self::rowHash($this->one('SELECT * FROM `' . $table . '` WHERE id = ?', [$id]));
                $runs = [];
                foreach ($inserted['payroll_runs'] as $id) $runs[] = $this->one('SELECT id, run_code, payroll_name, status, cutoff_date_from, cutoff_date_to, pay_date FROM payroll_runs WHERE id = ?', [$id]);
                $manifest = ['bundle' => self::BUNDLE, 'tenant_id' => $this->tenant, 'anchor' => $report['anchor'], 'backup' => $backup, 'backup_sha256' => $backupHash, 'before_hashes' => $beforeHashes, 'inserted_hashes' => $newHashes, 'counts' => $counts, 'runs' => $runs];
                // Save recoverable intent before committing; a crash after commit cannot cause duplicates.
                $this->writeJson($this->directory . '/intent.json', $manifest);
                $this->db->commit();
            } catch (Throwable $error) {
                if ($this->db->inTransaction()) $this->db->rollBack();
                throw $error;
            }
            $this->writeJson($this->directory . '/manifest.json', $manifest);
            return ['status' => 'applied', 'bundle' => self::BUNDLE, 'anchor' => $report['anchor'], 'existing_rows_preserved' => $old, 'inserted' => count($plan), 'counts' => $counts, 'runs' => $runs, 'backup' => $backup, 'backup_sha256' => $backupHash];
        } finally {
            if ($lock) { flock($lock, LOCK_UN); fclose($lock); }
        }
    }
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    try {
        $arguments = array_slice($argv, 1);
        if (count($arguments) !== 1 || !in_array($arguments[0], ['--dry-run', '--apply'], true)) throw new InvalidArgumentException('Usage: php scripts/extend-demo.php --dry-run|--apply');
        $loader = new DemoExpansion(dirname(__DIR__));
        echo json_encode($loader->run($arguments[0] === '--apply'), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR) . "\n";
    } catch (Throwable $error) {
        // Never expose config credentials, SQL parameters or raw database errors.
        fwrite(STDERR, 'Demo expansion stopped [' . get_class($error) . ']: ' . ($error instanceof PDOException ? 'Database operation failed; no automatic overwrite or reset.' : $error->getMessage()) . "\n");
        exit(1);
    }
}
