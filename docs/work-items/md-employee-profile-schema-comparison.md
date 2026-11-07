# Employee profile: production-snapshot and local-demo schema comparison

Reviewed 2026-11-07. Compared only schema metadata from the user-provided read-only SQL attachment with INFORMATION_SCHEMA metadata from the running `monterodental_demo` MariaDB database. No production connection or row-data inspection/import was used. The supplied snapshot was not edited.

## Final correction, 2026-11-07

The original findings below describe the queries before correction. The final feature removes the unsupported compensation and both assignment/shift soft-delete filters, preserves employee soft deletion, tenant constraints, effective dates and active branch selection, and does not alter the database schema. Before publication, all four actual profile SELECTs passed a fresh local replay against NULL sources exposing only the supplied schema column names. Actual fictional-demo model reads and HTTP checks also passed; live-server behavior was not independently tested. See [the task handoff](md-employee-profile.md) for current evidence and remaining limitations.

## Original finding that explains the profile failure

The new profile read queries assume soft-delete columns absent from the supplied production structure. The local reconstructed demo schema adds `deleted_at` to many tables through its shared `$common` definition, so local success masked these incompatibilities.

| Profile table | Snapshot `deleted_at` | Local `deleted_at` | Query reference |
| --- | --- | --- | --- |
| `hris_employees` | Present | Present | [EmployeeModel.php](../../app/models/hris/EmployeeModel.php#L108) |
| `payroll_employee_compensations` | Absent | Present | [EmployeeModel.php](../../app/models/hris/EmployeeModel.php#L120) |
| `hris_employee_shift_schedules` | Absent | Present | [EmployeeModel.php](../../app/models/hris/EmployeeModel.php#L137) |
| `hris_shifts` | Absent | Present | [EmployeeModel.php](../../app/models/hris/EmployeeModel.php#L137) |
| `hris_branch_shifts` | Absent | Present | [EmployeeModel.php](../../app/models/hris/EmployeeModel.php#L153) |

The first employee query has compatible column references. Once an employee is found, the compensation lookup at [EmployeeController.php](../../app/controllers/hris/EmployeeController.php#L56) references the missing compensation `deleted_at` field before the view renders. This produces an unhandled database exception and can explain HTTP 500. If that filter alone is corrected, both shift queries still reference absent `a.deleted_at` and `s.deleted_at` fields.

## Read-only SQL replay evidence

Replayed the actual profile SELECT strings in local MariaDB, replacing table sources with derived SELECTs exposing exactly the snapshot column names. Every derived value was NULL. This checks column compatibility without production records, importing the dump, changing persistent tables, or connecting to production.

| Query | Result |
| --- | --- |
| `getProfileEmployee` | Compatible column references; no returned employee claimed from NULL test rows |
| `getProfileCompensation` | SQLSTATE `42S22`: Unknown column `deleted_at` in WHERE |
| `getProfileShift`, employee assignment | SQLSTATE `42S22`: Unknown column `a.deleted_at` in WHERE |
| `getProfileShift`, branch fallback | SQLSTATE `42S22`: Unknown column `a.deleted_at` in WHERE |

This is a deterministic schema incompatibility. The live server error log is still needed to confirm its exact first production exception. Missing `s.deleted_at` fields are independently confirmed by schema comparison, even though SQL reports the missing `a.deleted_at` first.

## Correction identified by the original comparison

- In `getProfileCompensation`, remove the unsupported compensation `deleted_at` predicate.
- In both shift SELECTs, remove the unsupported assignment/shift `deleted_at` predicates.
- Keep employee soft-delete filtering: `hris_employees.deleted_at` exists in both schemas.
- Preserve tenant constraints, effective-date selection, deterministic ordering and the branch `is_active = 1` predicate.
- Do not add production columns or apply the reconstructed demo schema just to satisfy these queries.

The original comparison was read-only. The subsequent feature correction changes only the profile SELECT predicates described above; no database schema was changed.

## Overall schema coverage

- Snapshot tables: **51**.
- Running local tables: **37**.
- Shared tables: **36**.
- Shared tables with differing column presence: **31**.
- Shared-column type differences after ignoring integer display widths: **176**.
- Shared-column nullability differences: **104**.
- Snapshot foreign-key declarations: **20**; running local foreign keys: **0**.

### Snapshot-only tables

`history_tenant_modules`, `history_tenant_subscriptions`, `hris_employee_users`, `payroll_adjustments`, `payroll_government_brackets`, `payroll_government_tables`, `payroll_loan_payments`, `payroll_loans`, `payroll_periods`, `payroll_schedule_periods`, `payroll_schedules`, `plan_modules`, `plans`, `subscriptions`, `user_sessions`.

### Local-only tables

`local_demo_metadata`.

### Column presence by shared table

| Table | Snapshot-only columns | Local-only columns |
| --- | --- | --- |
| `hris_attendance_logs` | None | `deleted_at`, `updated_at` |
| `hris_branch_shifts` | None | `deleted_at`, `updated_at` |
| `hris_employee_shift_schedules` | None | `deleted_at`, `updated_at` |
| `hris_holidays` | None | `deleted_at`, `updated_at` |
| `hris_leave_requests` | None | `deleted_at`, `updated_at` |
| `hris_leave_types` | None | `deleted_at`, `updated_at` |
| `hris_org_units` | None | `deleted_at`, `description`, `is_active`, `updated_at` |
| `hris_positions` | None | `code`, `deleted_at`, `description`, `is_active`, `updated_at` |
| `hris_shifts` | None | `deleted_at`, `updated_at` |
| `modules` | None | `deleted_at`, `is_active`, `tenant_id`, `updated_at` |
| `payroll_allowance_types` | None | `deleted_at`, `description`, `is_active`, `updated_at` |
| `payroll_cash_advances` | None | `deleted_at`, `updated_at` |
| `payroll_commission_types` | None | `deleted_at`, `description`, `is_active`, `updated_at` |
| `payroll_deduction_types` | None | `deleted_at`, `description`, `is_active`, `updated_at` |
| `payroll_employee_allowances` | None | `deleted_at`, `updated_at` |
| `payroll_employee_commissions` | None | `deleted_at`, `updated_at` |
| `payroll_employee_compensations` | `tmpShift_id` | `deleted_at` |
| `payroll_employee_deductions` | None | `deleted_at`, `updated_at` |
| `payroll_employee_incentives` | None | `deleted_at`, `updated_at` |
| `payroll_incentive_types` | None | `deleted_at`, `description`, `is_active`, `updated_at` |
| `payroll_run_employee_days` | `grace_period_minutes`, `is_rest_day`, `scheduled_work_minutes`, `shift_name`, `worked_minutes` | `deleted_at`, `tenant_id`, `updated_at` |
| `payroll_run_employees` | `holiday_days`, `holiday_hours`, `holiday_pay` | `allow_overtime`, `deleted_at`, `scheduled_break_minutes`, `scheduled_time_in`, `scheduled_time_out`, `scheduled_work_minutes`, `shift_id`, `shift_name` |
| `payroll_run_items` | `created_by`, `multiplier`, `quantity`, `rate`, `remarks`, `source_id`, `taxable`, `updated_by` | `deleted_at`, `tenant_id` |
| `payroll_runs` | `finalized_at` | `cancellation_reason`, `cancelled_at`, `cancelled_by`, `deleted_at`, `reset_at`, `reset_by`, `reset_reason` |
| `permissions` | `is_default`, `module` | `deleted_at`, `description`, `is_active`, `tenant_id`, `updated_at` |
| `role_permissions` | None | `created_at`, `deleted_at`, `tenant_id`, `updated_at` |
| `roles` | `is_default` | `code`, `deleted_at`, `is_active`, `updated_at` |
| `tenant_branches` | None | `description`, `is_active`, `status`, `updated_at` |
| `tenants` | None | `tenant_id` |
| `user_branches` | None | `tenant_id`, `updated_at` |
| `user_roles` | None | `deleted_at`, `tenant_id`, `updated_at` |

### Type differences

Integer display widths such as `int(11)` versus `int` are ignored; signedness, character lengths and decimal precision are retained. These differences are metadata findings, not assertions that every affected query fails.

| Table / column | Snapshot type | Local type |
| --- | --- | --- |
| `hris_attendance_logs.branch_id` | `int` | `int unsigned` |
| `hris_attendance_logs.computed_actual_hours` | `decimal(5,2)` | `decimal(8,2)` |
| `hris_attendance_logs.computed_expected_hours` | `decimal(5,2)` | `decimal(8,2)` |
| `hris_attendance_logs.employee_id` | `int` | `int unsigned` |
| `hris_attendance_logs.id` | `int` | `int unsigned` |
| `hris_attendance_logs.tenant_id` | `int` | `int unsigned` |
| `hris_branch_shifts.branch_id` | `int` | `int unsigned` |
| `hris_branch_shifts.id` | `int` | `int unsigned` |
| `hris_branch_shifts.is_active` | `smallint` | `tinyint` |
| `hris_branch_shifts.shift_id` | `int` | `int unsigned` |
| `hris_branch_shifts.tenant_id` | `int` | `int unsigned` |
| `hris_employee_shift_schedules.employee_id` | `int` | `int unsigned` |
| `hris_employee_shift_schedules.id` | `int` | `int unsigned` |
| `hris_employee_shift_schedules.shift_id` | `int` | `int unsigned` |
| `hris_employee_shift_schedules.tenant_id` | `int` | `int unsigned` |
| `hris_employees.branch_id` | `int` | `int unsigned` |
| `hris_employees.contact_no` | `varchar(20)` | `varchar(50)` |
| `hris_employees.email` | `varchar(255)` | `varchar(150)` |
| `hris_employees.employment_status_id` | `int` | `int unsigned` |
| `hris_employees.employment_type_id` | `int` | `int unsigned` |
| `hris_employees.id` | `int` | `int unsigned` |
| `hris_employees.org_unit_id` | `int` | `int unsigned` |
| `hris_employees.position_id` | `int` | `int unsigned` |
| `hris_employees.status` | `varchar(30)` | `varchar(20)` |
| `hris_employees.tenant_id` | `int` | `int unsigned` |
| `hris_employment_statuses.code` | `varchar(50)` | `varchar(100)` |
| `hris_employment_statuses.id` | `int` | `int unsigned` |
| `hris_employment_statuses.is_active` | `smallint` | `tinyint` |
| `hris_employment_statuses.name` | `varchar(100)` | `varchar(150)` |
| `hris_employment_statuses.tenant_id` | `int` | `int unsigned` |
| `hris_employment_types.code` | `varchar(50)` | `varchar(100)` |
| `hris_employment_types.id` | `int` | `int unsigned` |
| `hris_employment_types.is_active` | `smallint` | `tinyint` |
| `hris_employment_types.name` | `varchar(100)` | `varchar(150)` |
| `hris_employment_types.tenant_id` | `int` | `int unsigned` |
| `hris_holidays.allow_work` | `smallint` | `tinyint` |
| `hris_holidays.id` | `int` | `int unsigned` |
| `hris_holidays.tenant_id` | `int` | `int unsigned` |
| `hris_leave_requests.approved_by` | `int` | `int unsigned` |
| `hris_leave_requests.employee_id` | `int` | `int unsigned` |
| `hris_leave_requests.id` | `int` | `int unsigned` |
| `hris_leave_requests.leave_type_id` | `int` | `int unsigned` |
| `hris_leave_requests.tenant_id` | `int` | `int unsigned` |
| `hris_leave_types.id` | `int` | `int unsigned` |
| `hris_leave_types.is_paid` | `smallint` | `tinyint` |
| `hris_leave_types.name` | `varchar(100)` | `varchar(150)` |
| `hris_leave_types.tenant_id` | `int` | `int unsigned` |
| `hris_org_units.code` | `varchar(50)` | `varchar(100)` |
| `hris_org_units.id` | `int` | `int unsigned` |
| `hris_org_units.parent_id` | `int` | `int unsigned` |
| `hris_org_units.tenant_id` | `int` | `int unsigned` |
| `hris_org_units.type` | `varchar(30)` | `varchar(50)` |
| `hris_positions.id` | `int` | `int unsigned` |
| `hris_positions.name` | `varchar(100)` | `varchar(150)` |
| `hris_positions.tenant_id` | `int` | `int unsigned` |
| `hris_shifts.allow_overtime` | `smallint` | `tinyint` |
| `hris_shifts.id` | `int` | `int unsigned` |
| `hris_shifts.name` | `varchar(100)` | `varchar(150)` |
| `hris_shifts.tenant_id` | `int` | `int unsigned` |
| `modules.code` | `varchar(50)` | `varchar(100)` |
| `modules.id` | `int` | `int unsigned` |
| `modules.name` | `varchar(100)` | `varchar(150)` |
| `payroll_allowance_types.computation_type` | `varchar(20)` | `varchar(30)` |
| `payroll_allowance_types.default_value` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_allowance_types.id` | `int` | `int unsigned` |
| `payroll_allowance_types.taxable` | `smallint` | `tinyint` |
| `payroll_allowance_types.tenant_id` | `int` | `int unsigned` |
| `payroll_cash_advances.amount` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_cash_advances.deduction_type` | `varchar(20)` | `varchar(30)` |
| `payroll_cash_advances.employee_id` | `int` | `int unsigned` |
| `payroll_cash_advances.id` | `int` | `int unsigned` |
| `payroll_cash_advances.installment_amount` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_cash_advances.remaining_balance` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_cash_advances.tenant_id` | `int` | `int unsigned` |
| `payroll_commission_types.computation_type` | `varchar(20)` | `varchar(30)` |
| `payroll_commission_types.default_value` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_commission_types.id` | `int` | `int unsigned` |
| `payroll_commission_types.tenant_id` | `int` | `int unsigned` |
| `payroll_deduction_types.computation_type` | `varchar(20)` | `varchar(30)` |
| `payroll_deduction_types.default_value` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_deduction_types.id` | `int` | `int unsigned` |
| `payroll_deduction_types.tenant_id` | `int` | `int unsigned` |
| `payroll_employee_allowances.allowance_type_id` | `int` | `int unsigned` |
| `payroll_employee_allowances.amount` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_employee_allowances.employee_id` | `int` | `int unsigned` |
| `payroll_employee_allowances.id` | `int` | `int unsigned` |
| `payroll_employee_allowances.is_recurring` | `smallint` | `tinyint` |
| `payroll_employee_allowances.tenant_id` | `int` | `int unsigned` |
| `payroll_employee_commissions.amount` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_employee_commissions.commission_type_id` | `int` | `int unsigned` |
| `payroll_employee_commissions.employee_id` | `int` | `int unsigned` |
| `payroll_employee_commissions.id` | `int` | `int unsigned` |
| `payroll_employee_commissions.tenant_id` | `int` | `int unsigned` |
| `payroll_employee_compensations.employee_id` | `int` | `int unsigned` |
| `payroll_employee_compensations.id` | `int` | `int unsigned` |
| `payroll_employee_compensations.salary_rate` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_employee_compensations.salary_type` | `varchar(20)` | `varchar(30)` |
| `payroll_employee_compensations.tenant_id` | `int` | `int unsigned` |
| `payroll_employee_deductions.amount` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_employee_deductions.deduction_type_id` | `int` | `int unsigned` |
| `payroll_employee_deductions.employee_id` | `int` | `int unsigned` |
| `payroll_employee_deductions.id` | `int` | `int unsigned` |
| `payroll_employee_deductions.is_recurring` | `smallint` | `tinyint` |
| `payroll_employee_deductions.tenant_id` | `int` | `int unsigned` |
| `payroll_employee_incentives.amount` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_employee_incentives.employee_id` | `int` | `int unsigned` |
| `payroll_employee_incentives.id` | `int` | `int unsigned` |
| `payroll_employee_incentives.incentive_type_id` | `int` | `int unsigned` |
| `payroll_employee_incentives.is_recurring` | `smallint` | `tinyint` |
| `payroll_employee_incentives.tenant_id` | `int` | `int unsigned` |
| `payroll_incentive_types.computation_type` | `varchar(20)` | `varchar(30)` |
| `payroll_incentive_types.default_value` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_incentive_types.id` | `int` | `int unsigned` |
| `payroll_incentive_types.tenant_id` | `int` | `int unsigned` |
| `payroll_run_employee_days.attendance_log_id` | `int` | `int unsigned` |
| `payroll_run_employee_days.holiday_id` | `int` | `int unsigned` |
| `payroll_run_employee_days.id` | `int` | `int unsigned` |
| `payroll_run_employee_days.is_present` | `int` | `tinyint` |
| `payroll_run_employee_days.run_employee_id` | `int` | `int unsigned` |
| `payroll_run_employee_days.shift_id` | `int` | `int unsigned` |
| `payroll_run_employees.basic_pay` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_run_employees.computation_status` | `varchar(50)` | `varchar(30)` |
| `payroll_run_employees.days_present` | `int` | `decimal(8,2)` |
| `payroll_run_employees.employee_id` | `int` | `int unsigned` |
| `payroll_run_employees.gross_pay` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_run_employees.id` | `int` | `int unsigned` |
| `payroll_run_employees.net_pay` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_run_employees.payroll_frequency` | `varchar(50)` | `varchar(30)` |
| `payroll_run_employees.run_id` | `int` | `int unsigned` |
| `payroll_run_employees.salary_rate` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_run_employees.salary_type` | `varchar(50)` | `varchar(30)` |
| `payroll_run_employees.tenant_id` | `int` | `int unsigned` |
| `payroll_run_employees.total_deductions` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_run_employees.total_earnings` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_run_items.amount` | `decimal(15,2)` | `decimal(12,2)` |
| `payroll_run_items.description` | `varchar(255)` | `text` |
| `payroll_run_items.id` | `int` | `int unsigned` |
| `payroll_run_items.run_employee_id` | `int` | `int unsigned` |
| `payroll_run_items.source_type` | `varchar(50)` | `varchar(30)` |
| `payroll_runs.created_by` | `int` | `int unsigned` |
| `payroll_runs.id` | `int` | `int unsigned` |
| `payroll_runs.period_no` | `tinyint` | `int` |
| `payroll_runs.processed_by` | `int` | `int unsigned` |
| `payroll_runs.tenant_id` | `int` | `int unsigned` |
| `permissions.id` | `int` | `int unsigned` |
| `role_permissions.id` | `int` | `int unsigned` |
| `role_permissions.permission_id` | `int` | `int unsigned` |
| `role_permissions.role_id` | `int` | `int unsigned` |
| `roles.id` | `int` | `int unsigned` |
| `roles.name` | `varchar(100)` | `varchar(150)` |
| `roles.tenant_id` | `int` | `int unsigned` |
| `tenant_branches.code` | `varchar(50)` | `varchar(100)` |
| `tenant_branches.id` | `int` | `int unsigned` |
| `tenant_branches.name` | `varchar(255)` | `varchar(150)` |
| `tenant_branches.tenant_id` | `int` | `int unsigned` |
| `tenant_modules.id` | `int` | `int unsigned` |
| `tenant_modules.module_id` | `int` | `int unsigned` |
| `tenant_modules.tenant_id` | `int` | `int unsigned` |
| `tenants.code` | `varchar(50)` | `varchar(100)` |
| `tenants.contact_no` | `varchar(20)` | `varchar(50)` |
| `tenants.id` | `int` | `int unsigned` |
| `tenants.is_default` | `smallint` | `tinyint` |
| `tenants.name` | `varchar(255)` | `varchar(150)` |
| `user_branches.branch_id` | `int` | `int unsigned` |
| `user_branches.id` | `int` | `int unsigned` |
| `user_branches.user_id` | `int` | `int unsigned` |
| `user_roles.branch_id` | `int` | `int unsigned` |
| `user_roles.id` | `int` | `int unsigned` |
| `user_roles.role_id` | `int` | `int unsigned` |
| `user_roles.user_id` | `int` | `int unsigned` |
| `users.contact_no` | `varchar(20)` | `varchar(50)` |
| `users.email` | `varchar(255)` | `varchar(150)` |
| `users.id` | `int` | `int unsigned` |
| `users.is_default` | `smallint` | `tinyint` |
| `users.name` | `varchar(255)` | `varchar(150)` |
| `users.tenant_id` | `int` | `int unsigned` |

### Nullability differences

| Table / column | Snapshot | Local |
| --- | --- | --- |
| `hris_attendance_logs.created_at` | NULL allowed | NOT NULL |
| `hris_attendance_logs.tenant_id` | NOT NULL | NULL allowed |
| `hris_branch_shifts.created_at` | NULL allowed | NOT NULL |
| `hris_branch_shifts.tenant_id` | NOT NULL | NULL allowed |
| `hris_employee_shift_schedules.created_at` | NULL allowed | NOT NULL |
| `hris_employee_shift_schedules.tenant_id` | NOT NULL | NULL allowed |
| `hris_employees.branch_id` | NULL allowed | NOT NULL |
| `hris_employees.created_at` | NULL allowed | NOT NULL |
| `hris_employees.date_hired` | NULL allowed | NOT NULL |
| `hris_employees.employee_no` | NULL allowed | NOT NULL |
| `hris_employees.tenant_id` | NOT NULL | NULL allowed |
| `hris_employment_statuses.code` | NOT NULL | NULL allowed |
| `hris_employment_statuses.created_at` | NULL allowed | NOT NULL |
| `hris_employment_statuses.is_active` | NULL allowed | NOT NULL |
| `hris_employment_types.code` | NOT NULL | NULL allowed |
| `hris_employment_types.created_at` | NULL allowed | NOT NULL |
| `hris_employment_types.is_active` | NULL allowed | NOT NULL |
| `hris_holidays.created_at` | NULL allowed | NOT NULL |
| `hris_holidays.tenant_id` | NOT NULL | NULL allowed |
| `hris_leave_requests.created_at` | NULL allowed | NOT NULL |
| `hris_leave_requests.tenant_id` | NOT NULL | NULL allowed |
| `hris_leave_types.created_at` | NULL allowed | NOT NULL |
| `hris_org_units.created_at` | NULL allowed | NOT NULL |
| `hris_org_units.tenant_id` | NOT NULL | NULL allowed |
| `hris_positions.created_at` | NULL allowed | NOT NULL |
| `hris_positions.tenant_id` | NOT NULL | NULL allowed |
| `hris_shifts.created_at` | NULL allowed | NOT NULL |
| `hris_shifts.required_hours` | NOT NULL | NULL allowed |
| `hris_shifts.shift_type` | NOT NULL | NULL allowed |
| `hris_shifts.tenant_id` | NOT NULL | NULL allowed |
| `modules.code` | NOT NULL | NULL allowed |
| `modules.created_at` | NULL allowed | NOT NULL |
| `modules.name` | NULL allowed | NOT NULL |
| `payroll_allowance_types.code` | NOT NULL | NULL allowed |
| `payroll_allowance_types.computation_type` | NOT NULL | NULL allowed |
| `payroll_allowance_types.created_at` | NULL allowed | NOT NULL |
| `payroll_allowance_types.default_value` | NULL allowed | NOT NULL |
| `payroll_cash_advances.created_at` | NULL allowed | NOT NULL |
| `payroll_cash_advances.installment_amount` | NULL allowed | NOT NULL |
| `payroll_cash_advances.tenant_id` | NOT NULL | NULL allowed |
| `payroll_commission_types.code` | NOT NULL | NULL allowed |
| `payroll_commission_types.computation_type` | NOT NULL | NULL allowed |
| `payroll_commission_types.created_at` | NULL allowed | NOT NULL |
| `payroll_commission_types.default_value` | NULL allowed | NOT NULL |
| `payroll_deduction_types.code` | NOT NULL | NULL allowed |
| `payroll_deduction_types.computation_type` | NOT NULL | NULL allowed |
| `payroll_deduction_types.created_at` | NULL allowed | NOT NULL |
| `payroll_deduction_types.default_value` | NULL allowed | NOT NULL |
| `payroll_employee_allowances.created_at` | NULL allowed | NOT NULL |
| `payroll_employee_allowances.tenant_id` | NOT NULL | NULL allowed |
| `payroll_employee_commissions.created_at` | NULL allowed | NOT NULL |
| `payroll_employee_commissions.tenant_id` | NOT NULL | NULL allowed |
| `payroll_employee_compensations.created_at` | NULL allowed | NOT NULL |
| `payroll_employee_compensations.payroll_frequency` | NOT NULL | NULL allowed |
| `payroll_employee_compensations.salary_type` | NOT NULL | NULL allowed |
| `payroll_employee_compensations.tenant_id` | NOT NULL | NULL allowed |
| `payroll_employee_deductions.created_at` | NULL allowed | NOT NULL |
| `payroll_employee_deductions.tenant_id` | NOT NULL | NULL allowed |
| `payroll_employee_incentives.created_at` | NULL allowed | NOT NULL |
| `payroll_employee_incentives.tenant_id` | NOT NULL | NULL allowed |
| `payroll_incentive_types.code` | NOT NULL | NULL allowed |
| `payroll_incentive_types.computation_type` | NOT NULL | NULL allowed |
| `payroll_incentive_types.created_at` | NULL allowed | NOT NULL |
| `payroll_incentive_types.default_value` | NULL allowed | NOT NULL |
| `payroll_run_employee_days.allow_work` | NOT NULL | NULL allowed |
| `payroll_run_employee_days.day_type` | NOT NULL | NULL allowed |
| `payroll_run_employee_days.holiday_multiplier` | NOT NULL | NULL allowed |
| `payroll_run_employee_days.is_holiday` | NOT NULL | NULL allowed |
| `payroll_run_employee_days.late_minutes` | NOT NULL | NULL allowed |
| `payroll_run_employee_days.overtime_minutes` | NOT NULL | NULL allowed |
| `payroll_run_employee_days.scheduled_break_minutes` | NOT NULL | NULL allowed |
| `payroll_run_employee_days.undertime_minutes` | NOT NULL | NULL allowed |
| `payroll_run_employees.computation_status` | NOT NULL | NULL allowed |
| `payroll_run_employees.gross_pay` | NULL allowed | NOT NULL |
| `payroll_run_employees.net_pay` | NULL allowed | NOT NULL |
| `payroll_run_employees.tenant_id` | NOT NULL | NULL allowed |
| `payroll_run_employees.total_deductions` | NULL allowed | NOT NULL |
| `payroll_run_employees.total_earnings` | NULL allowed | NOT NULL |
| `payroll_run_employees.updated_at` | NOT NULL | NULL allowed |
| `payroll_run_items.created_at` | NULL allowed | NOT NULL |
| `payroll_run_items.item_code` | NOT NULL | NULL allowed |
| `payroll_run_items.item_type` | NOT NULL | NULL allowed |
| `payroll_runs.created_at` | NULL allowed | NOT NULL |
| `payroll_runs.cutoff_date_from` | NOT NULL | NULL allowed |
| `payroll_runs.cutoff_date_to` | NOT NULL | NULL allowed |
| `payroll_runs.pay_date` | NOT NULL | NULL allowed |
| `payroll_runs.period_id` | NOT NULL | NULL allowed |
| `payroll_runs.period_no` | NOT NULL | NULL allowed |
| `payroll_runs.run_code` | NOT NULL | NULL allowed |
| `payroll_runs.tenant_id` | NOT NULL | NULL allowed |
| `payroll_runs.updated_at` | NOT NULL | NULL allowed |
| `permissions.code` | NOT NULL | NULL allowed |
| `permissions.created_at` | NULL allowed | NOT NULL |
| `roles.created_at` | NULL allowed | NOT NULL |
| `roles.tenant_id` | NOT NULL | NULL allowed |
| `tenant_branches.created_at` | NULL allowed | NOT NULL |
| `tenant_branches.tenant_id` | NOT NULL | NULL allowed |
| `tenant_modules.created_at` | NULL allowed | NOT NULL |
| `tenant_modules.tenant_id` | NOT NULL | NULL allowed |
| `tenants.created_at` | NULL allowed | NOT NULL |
| `user_branches.created_at` | NULL allowed | NOT NULL |
| `user_roles.created_at` | NULL allowed | NOT NULL |
| `users.created_at` | NULL allowed | NOT NULL |
| `users.tenant_id` | NOT NULL | NULL allowed |

### Index-shape differences on shared tables

Compared uniqueness and ordered column lists, ignoring index names. This is not a full DDL equivalence proof; prefix lengths, collation, engine options, CHECK constraints, triggers and default-value equivalence are not exhaustively evaluated.

| Table | Snapshot-only index shapes | Local-only index shapes |
| --- | --- | --- |
| `hris_attendance_logs` | INDEX employee_id, attendance_date; INDEX tenant_id, attendance_date | UNIQUE tenant_id, branch_id, employee_id, attendance_date |
| `hris_branch_shifts` | INDEX branch_id; INDEX shift_id; UNIQUE branch_id, shift_id | None |
| `hris_employee_shift_schedules` | INDEX employee_id; INDEX shift_id | None |
| `hris_employees` | INDEX branch_id; INDEX org_unit_id; INDEX position_id; INDEX tenant_id | None |
| `hris_employment_statuses` | INDEX tenant_id | None |
| `hris_employment_types` | INDEX tenant_id | None |
| `hris_org_units` | INDEX parent_id; INDEX tenant_id | UNIQUE tenant_id, code |
| `hris_positions` | UNIQUE tenant_id, name | UNIQUE tenant_id, code |
| `modules` | UNIQUE code | UNIQUE tenant_id, code |
| `payroll_cash_advances` | INDEX employee_id | None |
| `payroll_commission_types` | None | UNIQUE tenant_id, code |
| `payroll_employee_compensations` | INDEX employee_id; INDEX tenant_id | None |
| `payroll_incentive_types` | None | UNIQUE tenant_id, code |
| `payroll_run_employee_days` | INDEX attendance_date; INDEX holiday_id | None |
| `payroll_run_employees` | INDEX run_id, computation_status; INDEX run_id, employee_id | None |
| `payroll_run_items` | INDEX run_employee_id | None |
| `payroll_runs` | INDEX period_id; UNIQUE tenant_id, cutoff_date_from, cutoff_date_to; UNIQUE tenant_id, run_code | None |
| `permissions` | UNIQUE code | UNIQUE tenant_id, code |
| `role_permissions` | INDEX permission_id; INDEX role_id | None |
| `roles` | INDEX tenant_id; UNIQUE tenant_id, name | UNIQUE tenant_id, code |
| `tenant_branches` | INDEX tenant_id | None |
| `tenant_modules` | INDEX module_id | None |
| `user_branches` | INDEX branch_id; INDEX user_id | None |
| `user_roles` | INDEX branch_id; INDEX role_id; INDEX user_id; UNIQUE user_id, branch_id, role_id; UNIQUE user_id, role_id, branch_id | UNIQUE user_id, role_id |
| `users` | INDEX tenant_id; UNIQUE tenant_id, email | UNIQUE email |

## Additional implications

- All ten tables referenced by the profile reads exist in both schemas. The reproducible load failures concern absent columns, rather than missing profile tables.
- The snapshot contains a required production-only `payroll_employee_compensations.tmpShift_id` without an explicit default. The current profile only reads compensation, so that field is not the cause of its GET failure. Compensation creation requires separate compatibility review.
- The snapshot permits nullable employee branch and hire date while the demo does not. Demo fixtures do not cover every production shape; existing edit validation and access-policy decisions were not changed.
- Compensation precision is `decimal(15,2)` in the snapshot and `decimal(12,2)` locally. Many ID columns are signed in the snapshot and unsigned locally.
- The snapshot has foreign keys and additional payroll/subscription/account tables absent locally. Local demo success cannot certify production-wide schema or workflow compatibility.
- The previously observed shared date-validator warning occurs during saving. It is a separate finding from these profile-load SELECT failures.

## Next step

The narrow correction passed the current local-demo and snapshot-shaped SELECT checks recorded above. Complete the authorized publication and retain the separate browser/restricted-role follow-up items in the task handoff. Aligning the entire demo schema is separate structural work; it must not reset current demo data or alter production without explicit authorization.
