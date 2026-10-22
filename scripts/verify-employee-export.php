<?php
// Standalone CLI verification using fictional in-memory data. Never loads app config/bootstrap.
// Usage: php scripts/verify-employee-export.php [existing-vendor/autoload.php]
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once ($argv[1] ?? __DIR__ . '/../vendor/autoload.php');
require_once __DIR__ . '/../app/services/hris/EmployeeExportService.php';
require_once __DIR__ . '/../app/services/Csrf.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;

function check(bool $condition, string $label): void
{
    if (!$condition) {
        throw new RuntimeException('FAIL: ' . $label);
    }
}
function rejects(callable $test, int $status, string $label): void
{
    try {
        $test();
    } catch (EmployeeExportException $error) {
        check($error->httpStatus === $status, $label . ' status');
        return;
    }
    throw new RuntimeException('FAIL: ' . $label . ' did not reject');
}
$session = ['user' => ['id' => 7, 'tenant_id' => 1, 'permission_map' => ['hris.employee.view' => 0, 'hris.view' => 1], 'modules' => ['HRIS' => 0], 'branches' => ['10', 10]], 'tenant' => ['id' => 1, 'status' => 'ACTIVE']];
$scope = EmployeeExportService::authorizedContext($session);
check($scope === ['user_id' => 7, 'tenant_id' => 1, 'branch_ids' => [10]], 'server-session branch scope and deduplication');
rejects(fn () => EmployeeExportService::authorizedContext([]), 401, 'unauthenticated');
foreach (['hris.employee.view', 'hris.view'] as $permission) {
    $denied = $session; unset($denied['user']['permission_map'][$permission]);
    rejects(fn () => EmployeeExportService::authorizedContext($denied), 403, 'missing permission ' . $permission);
}
foreach (['branches' => [], 'modules' => [], 'tenant_id' => 2] as $field => $value) {
    $denied = $session; $denied['user'][$field] = $value;
    rejects(fn () => EmployeeExportService::authorizedContext($denied), 403, 'unverified context ' . $field);
}
$denied = $session; $denied['user']['is_super_admin'] = true; $denied['user']['branches'] = [];
rejects(fn () => EmployeeExportService::authorizedContext($denied), 403, 'no Super Admin unrestricted fallback');
$denied = $session; $denied['tenant']['status'] = 'INACTIVE';
rejects(fn () => EmployeeExportService::authorizedContext($denied), 403, 'inactive tenant');
foreach ([['branch_id' => 20], ['tenant_id' => 2], ['search' => []], ['sort_by' => 'status'], ['sort_by' => 'id DESC; DROP TABLE x'], ['sort_dir' => 'UP'], ['search' => str_repeat('x', 513)]] as $input) {
    rejects(fn () => EmployeeExportService::parameters($input), 422, 'invalid/untrusted input');
}
check(EmployeeExportService::parameters(['search' => '  Ana%  '])['search'] === 'Ana%', 'trim and existing wildcard semantics');
session_start();
$token = Csrf::generate();
check(Csrf::validate($token, 'keep'), 'valid export CSRF');
check(!Csrf::validate('fictional-invalid-token', 'keep'), 'invalid CSRF rejected');
check(Csrf::validate($token, 'keep'), 'export does not consume other form token');
session_write_close();
putenv('MONTERO_EMPLOYEE_EXPORT_MAX_ROWS');
check(EmployeeExportService::rowLimit() === 1000, 'conservative default cap');
putenv('MONTERO_EMPLOYEE_EXPORT_MAX_ROWS=12');
check(EmployeeExportService::rowLimit() === 12, 'configured cap');
putenv('MONTERO_EMPLOYEE_EXPORT_MAX_ROWS=5001');
try { EmployeeExportService::rowLimit(); throw new LogicException('FAIL: invalid cap allowed'); } catch (RuntimeException $error) { check(!($error instanceof LogicException), 'invalid cap rejected'); }
putenv('MONTERO_EMPLOYEE_EXPORT_MAX_ROWS');
echo "PASS: session authorization/branch policy, invalid input, CSRF keep/reject, configurable limits.\n";

$fixture = ['employee_no' => '000071', 'employee_name' => '=Fictional María 例', 'branch_name' => '+Fictional clinic', 'date_hired' => '2026-10-01', 'employment_status' => '@Probationary', 'status' => 'CONFIDENTIAL-INTERNAL-STATUS'];
$rows = array_fill(0, 12, $fixture);
$path = null;
try {
    $path = EmployeeExportService::createFile($rows, 1000);
    check(file_get_contents($path, false, null, 0, 4) === "PK\x03\x04", 'real ZIP container');
    if (DIRECTORY_SEPARATOR === '/') {
        check((fileperms($path) & 0777) === 0600 && (fileperms(dirname($path)) & 0777) === 0700, 'private temporary permissions');
    }
    $workbook = IOFactory::load($path);
    $sheet = $workbook->getActiveSheet();
    check($sheet->getTitle() === 'Employees' && $workbook->getSheetCount() === 1, 'one Employees worksheet');
    check($sheet->getHighestRow() === 13 && $sheet->getHighestColumn() === 'E', '12 records and exactly five columns');
    check($sheet->getFreezePane() === 'A2', 'frozen header');
    check($sheet->getStyle('A1')->getFont()->getBold(), 'bold header');
    check($sheet->getCell('A2')->getValue() === '000071' && $sheet->getCell('A2')->getDataType() === DataType::TYPE_STRING, 'leading zeros');
    foreach (['B2' => '=Fictional María 例', 'C2' => '+Fictional clinic', 'E2' => '@Probationary'] as $cell => $value) {
        check($sheet->getCell($cell)->getValue() === $value && $sheet->getCell($cell)->getDataType() === DataType::TYPE_STRING, 'Unicode/formula-like string ' . $cell);
    }
    check($sheet->getCell('D2')->getDataType() === DataType::TYPE_NUMERIC && $sheet->getCell('D2')->getFormattedValue() === '2026-10-01', 'real Excel hire date');
    check(preg_match('/^Montero_Dental_Employees_' . (new DateTimeImmutable('now', new DateTimeZone('Asia/Manila')))->format('Y-m-d') . '\.xlsx$/', EmployeeExportService::filename()) === 1, 'Manila filename');
    $workbook->disconnectWorksheets();
} finally {
    $directory = $path ? dirname($path) : null;
    EmployeeExportService::cleanup($path);
    check($path === null || (!file_exists($path) && !is_dir($directory)), 'successful file cleanup');
}
$path = EmployeeExportService::createFile([], 1000);
try {
    $empty = IOFactory::load($path);
    check($empty->getActiveSheet()->getHighestRow() === 1, 'empty export has headers only');
    $empty->disconnectWorksheets();
} finally { EmployeeExportService::cleanup($path); }
rejects(fn () => EmployeeExportService::createFile($rows, 10), 422, 'overflow never truncates');
$before = glob(sys_get_temp_dir() . '/montero-employee-export-*');
$invalidDate = $fixture; $invalidDate['date_hired'] = '2026-02-31';
rejects(fn () => EmployeeExportService::createFile([$invalidDate], 1000), 422, 'invalid date generation failure');
$largeText = $fixture; $largeText['branch_name'] = str_repeat('x', 32768);
rejects(fn () => EmployeeExportService::createFile([$largeText], 1000), 422, 'cell capacity');
$byteLimit = $fixture; $byteLimit['employee_name'] = str_repeat('例', 20000);
rejects(fn () => EmployeeExportService::createFile(array_fill(0, 40, $byteLimit), 1000), 422, 'text memory budget');
check(glob(sys_get_temp_dir() . '/montero-employee-export-*') === $before, 'failed generation private directories cleaned');
echo "PASS: real XLSX read-back, >10 rows, Unicode/leading zeros/formula text, employment status, dates, empty result, row/text bounds and cleanup.\n";

// SQL semantics use an isolated SQLite :memory: fixture only; no project DB/config.
if (!extension_loaded('pdo_sqlite')) {
    echo "BLOCKED: SQL fixture checks need optional PDO SQLite. No database connection attempted.\n";
    exit;
}
class Model
{
    public static PDO $fixtureDb;
    protected PDO $db;
    public function __construct(string $name = 'default') { $this->db = self::$fixtureDb; }
}
Model::$fixtureDb = new PDO('sqlite::memory:');
$db = Model::$fixtureDb;
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->sqliteCreateFunction('CONCAT_WS', static fn ($separator, ...$parts) => implode($separator, array_filter($parts, static fn ($value) => $value !== null)));
foreach ([
    'CREATE TABLE users (id INTEGER, tenant_id INTEGER, deleted_at TEXT)',
    'CREATE TABLE user_branches (user_id INTEGER, branch_id INTEGER, deleted_at TEXT)',
    'CREATE TABLE tenant_branches (id INTEGER, tenant_id INTEGER, name TEXT, deleted_at TEXT)',
    'CREATE TABLE hris_employment_types (id INTEGER, tenant_id INTEGER, name TEXT)',
    'CREATE TABLE hris_employment_statuses (id INTEGER, tenant_id INTEGER, name TEXT)',
    'CREATE TABLE hris_employees (id INTEGER, tenant_id INTEGER, employee_no TEXT, firstname TEXT, middlename TEXT, lastname TEXT, email TEXT, contact_no TEXT, branch_id INTEGER, employment_type_id INTEGER, employment_status_id INTEGER, date_hired TEXT, deleted_at TEXT, created_at TEXT)',
    'INSERT INTO users VALUES (7,1,NULL)',
    "INSERT INTO user_branches VALUES (7,10,NULL),(8,11,NULL),(7,20,NULL),(7,11,'2026-10-02')",
    "INSERT INTO tenant_branches VALUES (10,1,'Fictional assigned',NULL),(11,1,'Fictional unassigned',NULL),(20,2,'Other fictional tenant',NULL)",
    "INSERT INTO hris_employment_statuses VALUES (1,1,'Probationary')",
] as $sql) { $db->exec($sql); }
$insert = $db->prepare('INSERT INTO hris_employees VALUES (?, ?, ?, ?, NULL, ?, ?, NULL, ?, NULL, 1, ?, ?, ?)');
for ($id = 1; $id <= 12; $id++) { $insert->execute([$id,1,str_pad((string)$id,6,'0',STR_PAD_LEFT),'Fictional','Sample','fictional@example.test',10,'2026-10-01',null,'2026-10-01']); }
$insert->execute([13,1,'UNASSIGNED','Do not','Export',null,11,'2026-10-01',null,'2026-10-01']);
$insert->execute([14,2,'OTHER-TENANT','Do not','Export',null,20,'2026-10-01',null,'2026-10-01']);
$insert->execute([15,1,'DELETED','Do not','Export',null,10,'2026-10-01','2026-10-02','2026-10-01']);
$insert->execute([16,1,'FOREIGN-BRANCH','Do not','Export',null,20,'2026-10-01',null,'2026-10-01']);
require_once __DIR__ . '/../app/models/hris/EmployeeModel.php';
$model = new EmployeeModel();
$data = $model->getEmployeesForExport(1,7,[10,11,20],'','employee_no','ASC',1000);
check(count($data) === 12, 'tenant/branch/assignment/deletion isolation');
check(array_keys($data[0]) === ['employee_no','employee_name','branch_name','date_hired','employment_status'], 'no confidential export columns');
check($data[0]['employee_no'] === '000001' && $data[11]['employee_no'] === '000012', 'stable ascending ordering');
$descending = $model->getEmployeesForExport(1,7,[10],'','employee_no','DESC',1000);
check($descending[0]['employee_no'] === '000012', 'descending order');
check(count($model->getEmployeesForExport(1,7,[10],'000003','name','ASC',1000)) === 1, 'applied search');
check(count($model->getEmployeesForExport(1,7,[10],'no-match','created_at','DESC',1000)) === 0, 'zero matches');
rejects(fn () => $model->getEmployeesForExport(1,7,[10],'','created_at','DESC',10), 422, 'SQL sentinel row rejects oversized result');
$db->exec('DELETE FROM user_branches WHERE user_id=7 AND branch_id=10');
check($model->getEmployeesForExport(1,7,[10],'','created_at','DESC',1000) === [], 'revoked assignment overrides stale session');
echo "PASS: isolated in-memory SQL search/order, >10 results, cross-tenant and branch isolation, soft deletion, revoked assignment and limit sentinel. MySQL integration still requires a real safe runtime.\n";
