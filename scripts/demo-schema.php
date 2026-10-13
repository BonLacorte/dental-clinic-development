<?php
// Development schema reconstructed from the active models. Not a production migration.
$common = 'id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, tenant_id INT UNSIGNED NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NULL, deleted_at DATETIME NULL';
$catalog = "$common, code VARCHAR(100) NULL, name VARCHAR(150) NOT NULL, description TEXT NULL, is_active TINYINT NOT NULL DEFAULT 1, UNIQUE KEY tenant_code (tenant_id, code)";
$effective = 'employee_id INT UNSIGNED NOT NULL, effective_from DATE NOT NULL, effective_to DATE NULL';
$money = 'DECIMAL(12,2) NOT NULL DEFAULT 0';
$schemas = [
    'local_demo_metadata' => 'id INT PRIMARY KEY, version INT NOT NULL',
    'tenants' => "$common, name VARCHAR(150) NOT NULL, code VARCHAR(100) NOT NULL UNIQUE, contact_person VARCHAR(150), email VARCHAR(150), contact_no VARCHAR(50), address TEXT, status VARCHAR(20) DEFAULT 'ACTIVE', is_default TINYINT DEFAULT 0",
    'users' => "$common, name VARCHAR(150) NOT NULL, email VARCHAR(150) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, contact_no VARCHAR(50), status VARCHAR(20) DEFAULT 'ACTIVE', is_default TINYINT DEFAULT 0",
    'roles' => "$catalog",
    'permissions' => "$catalog",
    'user_roles' => "$common, user_id INT UNSIGNED NOT NULL, role_id INT UNSIGNED NOT NULL, branch_id INT UNSIGNED NULL, UNIQUE KEY assignment (user_id, role_id)",
    'role_permissions' => "$common, role_id INT UNSIGNED NOT NULL, permission_id INT UNSIGNED NOT NULL, UNIQUE KEY assignment (role_id, permission_id)",
    'modules' => "$catalog",
    'tenant_modules' => "$common, module_id INT UNSIGNED NOT NULL, starts_at DATETIME NULL, ends_at DATETIME NULL, UNIQUE KEY subscription (tenant_id, module_id)",
    'tenant_branches' => "$catalog, address TEXT, status VARCHAR(20) DEFAULT 'ACTIVE'",
    'user_branches' => "$common, user_id INT UNSIGNED NOT NULL, branch_id INT UNSIGNED NOT NULL, UNIQUE KEY assignment (user_id, branch_id)",
    'hris_org_units' => "$catalog, parent_id INT UNSIGNED NULL, type VARCHAR(50)",
    'hris_positions' => "$catalog",
    'hris_employment_types' => "$catalog",
    'hris_employment_statuses' => "$catalog",
    'hris_employees' => "$common, employee_no VARCHAR(100) NOT NULL, firstname VARCHAR(100) NOT NULL, middlename VARCHAR(100), lastname VARCHAR(100) NOT NULL, email VARCHAR(150), contact_no VARCHAR(50), branch_id INT UNSIGNED NOT NULL, position_id INT UNSIGNED NULL, org_unit_id INT UNSIGNED NULL, employment_type_id INT UNSIGNED NULL, employment_status_id INT UNSIGNED NULL, date_hired DATE NOT NULL, date_resigned DATE NULL, status VARCHAR(20) DEFAULT 'ACTIVE', UNIQUE KEY employee_number (tenant_id, employee_no)",
    'hris_shifts' => "$common, name VARCHAR(150) NOT NULL, shift_type VARCHAR(30), start_time TIME NULL, end_time TIME NULL, flex_start_time TIME NULL, flex_end_time TIME NULL, required_hours DECIMAL(5,2) DEFAULT 8, break_minutes INT DEFAULT 60, allow_overtime TINYINT DEFAULT 1",
    'hris_employee_shift_schedules' => "$common, $effective, shift_id INT UNSIGNED NOT NULL",
    'hris_branch_shifts' => "$common, branch_id INT UNSIGNED NOT NULL, shift_id INT UNSIGNED NOT NULL, is_active TINYINT DEFAULT 1",
    'hris_holidays' => "$common, holiday_date DATE NOT NULL, name VARCHAR(150) NOT NULL, holiday_type VARCHAR(30), pay_multiplier DECIMAL(5,2) DEFAULT 1, allow_work TINYINT DEFAULT 1, UNIQUE KEY holiday (tenant_id, holiday_date)",
    'hris_leave_types' => "$common, name VARCHAR(150) NOT NULL, is_paid TINYINT DEFAULT 1",
    'hris_leave_requests' => "$common, employee_id INT UNSIGNED NOT NULL, leave_type_id INT UNSIGNED NOT NULL, date_from DATE NOT NULL, date_to DATE NOT NULL, reason TEXT, status VARCHAR(30) DEFAULT 'PENDING', approved_by INT UNSIGNED NULL, approved_at DATETIME NULL",
    'hris_attendance_logs' => "$common, branch_id INT UNSIGNED NOT NULL, employee_id INT UNSIGNED NOT NULL, attendance_date DATE NOT NULL, time_in DATETIME NULL, time_out DATETIME NULL, status VARCHAR(30) DEFAULT 'PRESENT', break_minutes INT DEFAULT 60, late_minutes INT DEFAULT 0, undertime_minutes INT DEFAULT 0, overtime_minutes INT DEFAULT 0, computed_expected_hours DECIMAL(8,2) DEFAULT 8, computed_actual_hours DECIMAL(8,2) DEFAULT 8, UNIQUE KEY attendance (tenant_id, branch_id, employee_id, attendance_date)",
    'payroll_employee_compensations' => "$common, $effective, salary_type VARCHAR(30), payroll_frequency VARCHAR(30), salary_rate $money",
    'payroll_cash_advances' => "$common, employee_id INT UNSIGNED NOT NULL, amount $money, deduction_type VARCHAR(30), installment_amount $money, remaining_balance $money, status VARCHAR(30) DEFAULT 'ACTIVE'",
    'payroll_runs' => "$common, period_no INT, period_id INT, run_code VARCHAR(100), payroll_name VARCHAR(150), cutoff_date_from DATE, cutoff_date_to DATE, pay_date DATE, status VARCHAR(30) DEFAULT 'DRAFT', created_by INT UNSIGNED NULL, processed_by INT UNSIGNED NULL, processed_at DATETIME NULL, cancelled_by INT UNSIGNED NULL, cancelled_at DATETIME NULL, cancellation_reason TEXT NULL, reset_by INT UNSIGNED NULL, reset_at DATETIME NULL, reset_reason TEXT NULL",
    'payroll_run_employees' => "$common, run_id INT UNSIGNED NOT NULL, employee_id INT UNSIGNED NOT NULL, shift_id INT UNSIGNED NULL, shift_name VARCHAR(150), scheduled_time_in TIME NULL, scheduled_time_out TIME NULL, scheduled_break_minutes INT DEFAULT 60, allow_overtime TINYINT DEFAULT 1, scheduled_work_minutes DECIMAL(8,2) DEFAULT 8, salary_type VARCHAR(30), payroll_frequency VARCHAR(30), salary_rate $money, days_present DECIMAL(8,2) DEFAULT 0, basic_pay $money, total_earnings $money, gross_pay $money, total_deductions $money, net_pay $money, computation_status VARCHAR(30) DEFAULT 'PENDING', error_message TEXT NULL, computed_at DATETIME NULL, UNIQUE KEY employee_run (run_id, employee_id)",
    'payroll_run_employee_days' => "$common, run_employee_id INT UNSIGNED NOT NULL, attendance_date DATE NOT NULL, day_type VARCHAR(30), is_holiday TINYINT DEFAULT 0, holiday_id INT UNSIGNED NULL, holiday_name VARCHAR(150), holiday_type VARCHAR(30), holiday_multiplier DECIMAL(5,2) DEFAULT 1, allow_work TINYINT DEFAULT 1, attendance_log_id INT UNSIGNED NULL, time_in DATETIME NULL, time_out DATETIME NULL, shift_id INT UNSIGNED NULL, scheduled_time_in TIME NULL, scheduled_time_out TIME NULL, scheduled_break_minutes INT DEFAULT 60, is_present TINYINT DEFAULT 0, late_minutes INT DEFAULT 0, undertime_minutes INT DEFAULT 0, overtime_minutes INT DEFAULT 0, UNIQUE KEY employee_day (run_employee_id, attendance_date)",
    'payroll_run_items' => "$common, run_employee_id INT UNSIGNED NOT NULL, item_type VARCHAR(30), item_code VARCHAR(100), description TEXT, amount $money, source_type VARCHAR(30)",
];
foreach (['allowance', 'commission', 'incentive', 'deduction'] as $kind) {
    $schemas["payroll_{$kind}_types"] = "$catalog, computation_type VARCHAR(30) DEFAULT 'FIXED', default_value $money" . ($kind === 'allowance' ? ', taxable TINYINT DEFAULT 0' : '');
    $schemas["payroll_employee_{$kind}s"] = "$common, $effective, {$kind}_type_id INT UNSIGNED NOT NULL, amount $money" . ($kind !== 'commission' ? ', is_recurring TINYINT DEFAULT 1' : '');
}
return $schemas;
