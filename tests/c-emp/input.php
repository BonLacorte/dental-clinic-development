<?php
// Pure input tests: no config, database or session is loaded.
require_once __DIR__ . '/../../app/helpers/validator.php';
require_once __DIR__ . '/../../app/services/hris/EmployeeInput.php';
set_error_handler(static function ($severity, $message, $file, $line) { throw new ErrorException($message, 0, $severity, $file, $line); });
$count = 0;
function check(bool $ok, string $name): void {
    global $count;
    if (!$ok) { throw new RuntimeException('FAIL ' . $name); }
    $count++;
}
$valid = ['employee_no' => '', 'firstname' => ' Fictional ', 'lastname' => 'Fixture', 'branch_id' => '1', 'employment_type_id' => '1', 'date_hired' => '2024-02-29', 'csrf_token' => 'test-only'];
$result = EmployeeInput::employee($valid);
check(!$result['errors'] && $result['data']['firstname'] === 'Fictional', 'valid required fields and trimming');
check(!isset($result['data']['employee_no']), 'number is server owned');
check($result['data']['position_id'] === null && $result['data']['email'] === null, 'omitted optional fields');
check(!EmployeeInput::employee($valid + ['email' => '', 'position_id' => ''])['errors'], 'blank optional fields');
foreach (['firstname', 'lastname', 'branch_id', 'employment_type_id', 'date_hired', 'csrf_token'] as $field) {
    $input = $valid; unset($input[$field]);
    check(isset(EmployeeInput::employee($input)['errors'][$field]), 'missing required ' . $field);
    $input[$field] = '  ';
    check(isset(EmployeeInput::employee($input)['errors'][$field]), 'blank required ' . $field);
}
foreach (['2026-02-29', '2026-04-31', '2026-2-01', '0000-01-01', '2026-01-01 extra', "2026-01-01\0", '2026-13-01'] as $date) {
    $input = $valid; $input['date_hired'] = $date;
    check(isset(EmployeeInput::employee($input)['errors']['date_hired']), 'invalid calendar date');
}
foreach (['0', '-1', '1.5', 'abc', '999999999999999999999999'] as $id) {
    $input = $valid; $input['branch_id'] = $id;
    check(isset(EmployeeInput::employee($input)['errors']['branch_id']), 'invalid integer selection');
}
foreach ([['x'], new stdClass(), true] as $value) {
    $input = $valid; $input['firstname'] = $value;
    check(!empty(EmployeeInput::employee($input)['errors']), 'reject nonscalar field');
}
check(!empty(EmployeeInput::employee($valid + ['tenant_id' => '2'])['errors']), 'unexpected tenant field');
check(!empty(EmployeeInput::employee($valid + ['email' => 'bad'])['errors']), 'invalid optional email');
check(!empty(EmployeeInput::employee($valid + ['contact_no' => 'abc'])['errors']), 'invalid optional phone');
check(!empty(EmployeeInput::employee($valid + ['date_resigned' => '2024-01-01'], true)['errors']), 'resignation order');
check(!EmployeeInput::employee($valid + ['date_resigned' => '2024-02-29'], true)['errors'], 'same-day resignation');
$comp = ['id' => '', 'employee_id' => '1', 'salary_type' => 'DAILY', 'payroll_frequency' => 'SEMI_MONTHLY', 'salary_rate' => '123.45', 'effective_from' => '2026-01-01', 'effective_to' => '', 'csrf_token' => 'test-only'];
check(!EmployeeInput::compensation($comp)['errors'], 'valid serialized compensation');
check(EmployeeInput::compensation($comp)['data']['salary_rate'] === '123.45', 'money stays a string');
foreach (['DAILY', 'WEEKLY', 'BI_WEEKLY', 'SEMI_MONTHLY', 'MONTHLY'] as $frequency) {
    $input = $comp; $input['payroll_frequency'] = $frequency;
    check(!EmployeeInput::compensation($input)['errors'], 'existing HRIS frequency ' . $frequency);
}
foreach (['0', '-1', 'nan', '1e999', 'abc'] as $rate) {
    $input = $comp; $input['salary_rate'] = $rate;
    check(!empty(EmployeeInput::compensation($input)['errors']['salary_rate']), 'invalid rate');
}
foreach (['salary_type' => 'OTHER', 'payroll_frequency' => 'OTHER', 'employee_id' => '1.1', 'effective_to' => '2025-12-31'] as $field => $value) {
    $input = $comp; $input[$field] = $value;
    check(!empty(EmployeeInput::compensation($input)['errors'][$field]), 'invalid compensation ' . $field);
}
check(!empty(EmployeeInput::csrf(['csrf_token' => ['x']])['errors']), 'CSRF array');
check(!empty(EmployeeInput::csrf(['csrf_token' => 'x', 'extra' => 'x'])['errors']), 'strict delete fields');
echo "PASS $count employee input assertions (no database)\n";
