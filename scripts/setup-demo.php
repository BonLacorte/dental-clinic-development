<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
date_default_timezone_set('Asia/Manila');
$root = dirname(__DIR__);
try {
    $config = (require $root . '/config/local.php')['default'];
    // Explicit dev-only Docker mode; the existing Windows/XAMPP path stays unchanged.
    $dockerDemo = getenv('MONTERO_DEMO_DOCKER') === '1';
    $demoHost = $dockerDemo ? 'db' : '127.0.0.1';
    $demoPort = $dockerDemo ? 3306 : 3307;
    if ($config['host'] !== $demoHost || (int)$config['port'] !== $demoPort || $config['dbname'] !== 'monterodental_demo' || $config['user'] !== 'montero_demo') {
        throw new RuntimeException('Setup only supports the isolated monterodental_demo database on the configured local demo transport.');
    }
    $rootPassword = $dockerDemo ? getenv('MONTERO_DEMO_DB_ROOT_PASSWORD') : 'LocalDemoRoot2026!';
    if (!is_string($rootPassword) || $rootPassword === '') {
        throw new RuntimeException('Docker demo root password was not provided.');
    }
    $db = new PDO("mysql:host={$demoHost};port={$demoPort};charset=utf8mb4", 'root', $rootPassword, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $db->exec('CREATE DATABASE IF NOT EXISTS monterodental_demo CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    $db->exec('USE monterodental_demo');
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    if ($tables && !in_array('local_demo_metadata', $tables, true)) {
        throw new RuntimeException('Database contains unrecognized tables; refusing to modify it.');
    }
    // The MariaDB image initializes its demo user for the container network.
    $userHost = $dockerDemo ? '%' : '127.0.0.1';
    $db->exec("CREATE USER IF NOT EXISTS 'montero_demo'@'{$userHost}' IDENTIFIED BY " . $db->quote($config['pass']));
    $db->exec("GRANT SELECT, INSERT, UPDATE, DELETE ON monterodental_demo.* TO 'montero_demo'@'{$userHost}'");
    $schema = require __DIR__ . '/demo-schema.php';
    foreach ($schema as $table => $columns) {
        $db->exec("CREATE TABLE IF NOT EXISTS `$table` ($columns) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
    if ($db->query('SELECT COUNT(*) FROM local_demo_metadata')->fetchColumn()) {
        echo "Demo database already initialized; existing data preserved.\n";
        exit(0);
    }
    // A completed seed is marked only after every sample row and payroll snapshot succeeds.
    $db->beginTransaction();
    $insert = static function (string $table, array $data) use ($db): int {
        $columns = '`' . implode('`,`', array_keys($data)) . '`';
        $stmt = $db->prepare("INSERT INTO `$table` ($columns) VALUES (" . implode(',', array_fill(0, count($data), '?')) . ')');
        $stmt->execute(array_values($data));
        return (int)$db->lastInsertId();
    };
    $tenant = $insert('tenants', ['name' => 'Montero Dental Demo', 'code' => 'DEMO', 'contact_person' => 'Demo Administrator', 'email' => 'admin@example.test', 'address' => 'Fictional demo address, Quezon City']);
    $admin = $insert('users', ['tenant_id' => $tenant, 'name' => 'Demo Administrator', 'email' => 'admin@example.test', 'contact_no' => '09000000001', 'password' => password_hash('DemoDental2026!', PASSWORD_DEFAULT)]);
    $role = $insert('roles', ['tenant_id' => $tenant, 'code' => 'ADMIN', 'name' => 'Admin']);
    $insert('user_roles', ['user_id' => $admin, 'role_id' => $role]);
    $permissions = ['user.view', 'user.add', 'user.edit', 'tenant.view', 'hris.view', 'hris.employee.view', 'hris.employee.create', 'hris.employee.edit', 'hris.employee.delete', 'payroll.view', 'payroll.create'];
    foreach ($permissions as $code) {
        $permission = $insert('permissions', ['code' => $code, 'name' => $code]);
        $insert('role_permissions', ['role_id' => $role, 'permission_id' => $permission]);
    }
    $staffRole = $insert('roles', ['tenant_id' => $tenant, 'code' => 'HR_STAFF', 'name' => 'HR Staff']);
    foreach (['hris.view', 'hris.employee.view'] as $code) {
        $stmt = $db->prepare('SELECT id FROM permissions WHERE code = ?');
        $stmt->execute([$code]);
        $insert('role_permissions', ['role_id' => $staffRole, 'permission_id' => (int)$stmt->fetchColumn()]);
    }
    foreach (['HRIS', 'PAYROLL'] as $code) {
        $module = $insert('modules', ['code' => $code, 'name' => $code]);
        $insert('tenant_modules', ['tenant_id' => $tenant, 'module_id' => $module]);
    }
    $branches = [];
    foreach (['QC' => 'Quezon City Demo Clinic', 'MKT' => 'Makati Demo Clinic'] as $code => $name) {
        $branch = $insert('tenant_branches', ['tenant_id' => $tenant, 'code' => $code, 'name' => $name, 'address' => 'Fictional demo address']);
        $insert('user_branches', ['user_id' => $admin, 'branch_id' => $branch]);
        $branches[] = $branch;
    }
    $office = $insert('hris_org_units', ['tenant_id' => $tenant, 'code' => 'CLINIC', 'name' => 'Clinic Operations', 'type' => 'DEPARTMENT']);
    $positions = [];
    foreach (['Dentist', 'Dental Assistant', 'Receptionist'] as $name) {
        $positions[] = $insert('hris_positions', ['tenant_id' => $tenant, 'name' => $name]);
    }
    $employmentType = $insert('hris_employment_types', ['tenant_id' => $tenant, 'code' => 'REGULAR', 'name' => 'Regular', 'description' => 'Regular employee']);
    $insert('hris_employment_types', ['tenant_id' => $tenant, 'code' => 'CONTRACTUAL', 'name' => 'Contractual']);
    $employmentStatus = $insert('hris_employment_statuses', ['tenant_id' => $tenant, 'code' => 'ACTIVE', 'name' => 'Active']);
    $insert('hris_employment_statuses', ['tenant_id' => $tenant, 'code' => 'RESIGNED', 'name' => 'Resigned']);
    $shift = $insert('hris_shifts', ['tenant_id' => $tenant, 'name' => 'Demo Clinic Day Shift', 'shift_type' => 'FIXED', 'start_time' => '09:00:00', 'end_time' => '18:00:00', 'required_hours' => 8, 'break_minutes' => 60, 'allow_overtime' => 1]);
    foreach ($branches as $branch) { $insert('hris_branch_shifts', ['tenant_id' => $tenant, 'branch_id' => $branch, 'shift_id' => $shift]); }
    $month = new DateTimeImmutable('first day of this month');
    $effectiveFrom = $month->modify('-3 months')->format('Y-m-d');
    $employees = [];
    foreach ([['Ana', 'Demo', 0, 0, 1800], ['Ben', 'Sample', 0, 1, 900], ['Clara', 'Example', 1, 2, 800], ['Diego', 'Practice', 1, 0, 1700]] as $index => [$first, $last, $branchIndex, $positionIndex, $rate]) {
        $employee = $insert('hris_employees', ['tenant_id' => $tenant, 'employee_no' => sprintf('%s-%04d', $branchIndex ? 'MKT' : 'QC', $index + 1), 'firstname' => $first, 'lastname' => $last, 'email' => strtolower($first) . '@example.test', 'contact_no' => sprintf('090000000%02d', $index + 2), 'branch_id' => $branches[$branchIndex], 'position_id' => $positions[$positionIndex], 'org_unit_id' => $office, 'employment_type_id' => $employmentType, 'employment_status_id' => $employmentStatus, 'date_hired' => $effectiveFrom]);
        $employees[] = $employee;
        $insert('payroll_employee_compensations', ['tenant_id' => $tenant, 'employee_id' => $employee, 'salary_type' => 'DAILY', 'payroll_frequency' => 'SEMI_MONTHLY', 'salary_rate' => $rate, 'effective_from' => $effectiveFrom]);
        $insert('hris_employee_shift_schedules', ['tenant_id' => $tenant, 'employee_id' => $employee, 'shift_id' => $shift, 'effective_from' => $effectiveFrom]);
        for ($day = 1; $day <= 10; $day++) {
            $date = $month->modify('+' . ($day - 1) . ' days');
            if ((int)$date->format('N') === 7) { continue; }
            $late = $index === 1 && $day === 5 ? 15 : 0;
            $insert('hris_attendance_logs', ['tenant_id' => $tenant, 'employee_id' => $employee, 'branch_id' => $branches[$branchIndex], 'attendance_date' => $date->format('Y-m-d'), 'time_in' => $date->format('Y-m-d') . ($late ? ' 09:15:00' : ' 09:00:00'), 'time_out' => $date->format('Y-m-d') . ' 18:00:00', 'late_minutes' => $late]);
        }
    }
    $leaveType = $insert('hris_leave_types', ['tenant_id' => $tenant, 'name' => 'Demo Vacation Leave', 'is_paid' => 1]);
    $insert('hris_leave_types', ['tenant_id' => $tenant, 'name' => 'Demo Unpaid Leave', 'is_paid' => 0]);
    $insert('hris_leave_requests', ['tenant_id' => $tenant, 'employee_id' => $employees[2], 'leave_type_id' => $leaveType, 'date_from' => $month->modify('+19 days')->format('Y-m-d'), 'date_to' => $month->modify('+20 days')->format('Y-m-d'), 'reason' => 'Fictional vacation request', 'status' => 'PENDING']);
    $insert('hris_leave_requests', ['tenant_id' => $tenant, 'employee_id' => $employees[1], 'leave_type_id' => $leaveType, 'date_from' => $month->modify('+21 days')->format('Y-m-d'), 'date_to' => $month->modify('+21 days')->format('Y-m-d'), 'reason' => 'Fictional approved request', 'status' => 'APPROVED', 'approved_by' => $admin, 'approved_at' => date('Y-m-d H:i:s')]);
    $insert('hris_holidays', ['tenant_id' => $tenant, 'holiday_date' => $month->modify('+7 days')->format('Y-m-d'), 'name' => 'Demo Holiday (fictional)', 'holiday_type' => 'REGULAR', 'pay_multiplier' => 2, 'allow_work' => 1]);
    foreach (['allowance' => ['MEAL', 'Demo Meal Allowance', 150], 'commission' => ['SALES', 'Demo Service Commission', 500], 'incentive' => ['BONUS', 'Demo Performance Bonus', 1000], 'deduction' => ['OTHER', 'Demo Other Deduction', 200]] as $kind => [$code, $name, $amount]) {
        $type = $insert("payroll_{$kind}_types", ['tenant_id' => $tenant, 'code' => $code, 'name' => $name, 'computation_type' => 'FIXED', 'default_value' => $amount]);
        $insert("payroll_employee_{$kind}s", ['tenant_id' => $tenant, 'employee_id' => $employees[0], "{$kind}_type_id" => $type, 'amount' => $amount, 'effective_from' => $effectiveFrom]);
    }
    $insert('payroll_cash_advances', ['tenant_id' => $tenant, 'employee_id' => $employees[1], 'amount' => 3000, 'deduction_type' => 'INSTALLMENT', 'installment_amount' => 500, 'remaining_balance' => 3000]);
    // Build a genuine draft through the app model, with employees and daily snapshots.
    require_once $root . '/core/Model.php';
    require_once $root . '/app/models/payroll/PayrollRunModel.php';
    $payroll = new class($db) extends PayrollRunModel {
        public function __construct(PDO $connection) { $this->db = $connection; }
    };
    $from = $month->modify('-1 day')->format('Y-m-d');
    $to = $month->modify('+13 days')->format('Y-m-d');
    $runId = $payroll->createPayrollRun(['tenant_id' => $tenant, 'period_no' => 1, 'period_id' => 1, 'run_code' => 'DEMO-' . $month->format('Ym'), 'payroll_name' => 'Demo Payroll - ' . $month->format('F Y'), 'cutoff_date_from' => $from, 'cutoff_date_to' => $to, 'pay_date' => $month->modify('+14 days')->format('Y-m-d'), 'created_by' => $admin]);
    $payroll->loadEmployees($runId, $tenant, $from, $to);
    // loadEmployees supplies salary snapshots and days; add shift snapshots as well.
    $db->exec("UPDATE payroll_run_employees SET shift_id = $shift, shift_name = 'Demo Clinic Day Shift', scheduled_time_in = '09:00:00', scheduled_time_out = '18:00:00' WHERE run_id = $runId");
    $insert('local_demo_metadata', ['id' => 1, 'version' => 1]);
    $db->commit();
    echo "Seeded demo: 1 tenant, 1 administrator, 2 branches, 4 employees, HR/payroll records, draft run $runId.\n";
    echo "Login: admin@example.test / DemoDental2026!\n";
} catch (Throwable $e) {
    if (isset($db) && $db->inTransaction()) { $db->rollBack(); }
    fwrite(STDERR, 'Demo setup failed: ' . $e->getMessage() . "\n");
    exit(1);
}
