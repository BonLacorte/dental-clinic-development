# Montero Dental project overview

Montero Dental is a PHP web application for a dental business's internal employee administration. The implemented features focus on human resources and payroll across multiple organizations (tenants) and clinic branches. The current code does not implement a complete patient-record or dental-treatment system.

## What you can do

| Area | Implemented behavior |
| --- | --- |
| Login and access | Email/password login, password hashing, CSRF tokens, tenant context, database-backed roles and permissions, module subscriptions, and assigned branches. |
| Administration | List tenants and users; create users with roles and branch assignments; manage branches. |
| HRIS | Manage employees, organizational units, positions, employment types (the Appointments screen), shifts, holidays, leave types and leave requests. |
| Attendance | Record employee time-in/time-out, view daily time records and summaries, and import biometric attendance spreadsheets. |
| Payroll configuration | Manage compensation rates, allowances, commissions, incentives, deductions, and cash advances. |
| Payroll runs | Create cutoff periods, load employees and daily snapshots, calculate payroll, inspect each employee's earnings and deductions, and reset/cancel/delete runs where supported. |

## Typical workflow

1. Sign in with a user assigned to a tenant, its enabled modules, and its branches.
2. Configure branches, organizational units, positions, employment types, and shifts.
3. Add employees and their compensation rates.
4. Record attendance and maintain leave/holiday records.
5. Create a payroll run, load its employees, and process it.
6. Open an employee's payroll details to inspect daily attendance and generated pay items.

The local demo supplies this configuration and sample records so you can start at the employee and payroll screens immediately. See [LOCAL_SETUP.md](LOCAL_SETUP.md) for credentials and a guided tour.

## Technology and code organization

The backend uses a small custom MVC framework called MVCLite, PDO, and MySQL/MariaDB. Composer installs PhpSpreadsheet for spreadsheet imports. The locked dependencies require **64-bit PHP 8.3 or newer**. The frontend consists of server-rendered PHP views, Bootstrap, jQuery, and AJAX; some screens include DataTables. There is no Node.js build step.

| Location | Purpose |
| --- | --- |
| `public/index.php` | Web entry point; starts the session, loads dependencies/services/helpers, registers routes, and runs the app. |
| `public/dev-router.php` | PHP built-in server router; serves real public files directly and forwards application URLs to the entry point. |
| `core/` | Router/application class, base controller and model, and file storage utilities. |
| `routes/` | Main routes plus HRIS and payroll route definitions. |
| `app/controllers/` | Handle web pages, validation, and AJAX actions. |
| `app/models/` | SQL queries and persistence, including payroll calculations. |
| `app/views/` | Login, admin layout, HRIS screens, and payroll screens. |
| `app/middleware/` | Authentication, tenant, module, permission, role, and branch checks. |
| `app/services/`, `app/helpers/` | Shared CSRF, crypto, validation, and business helpers. |
| `config/config.php` | Original database configuration. |
| `config/local.php` | Local database override added for the demo. |
| `storage/` | Attachments, local sessions, logs, and the isolated demo database files. |
| `scripts/` | Local startup/shutdown, PHP settings, and repeatable demo schema/seed. |
| `docs/` | Existing developer notes, some of which describe older implementations. |

Requests pass through the router and middleware to a controller, which uses models to query the database and returns either a rendered view or JSON. Login loads roles, permissions, enabled modules, and branch assignments into the session. The demo administrator uses the normal Admin role with explicit permissions and both modules enabled.

## Database and sample data

The repository originally contained no complete SQL schema or migrations. `MigrationController` only bootstraps a tenant/user against existing tables. The added `scripts/demo-schema.php` reconstructs a **development schema from the active model queries**; it is not an authoritative production database export or production migration.

The demo uses `monterodental_demo` on `127.0.0.1:3307`, with database files under `storage/local-mysql`. Its sample data includes one tenant, one administrator, two branches, four fictional employees, employment lookups, positions, a department, a day shift, attendance logs, leave requests, a fictional holiday, compensation rates, payroll adjustments, a cash advance, and one draft payroll run with employee/day snapshots. Dates are based on the Manila month when first seeded.

Startup preserves existing demo data. The seed records completion in `local_demo_metadata` and will not duplicate a completed dataset. All sample identities use `.test` email addresses and fictional names.

## Current limitations

- Home dashboard counters are static placeholders, so they display zero even when records exist.
- Some navigation links, including Roles, Settings, Clinic, and HRIS Reports, lack corresponding implemented routes. Tenant creation is also incomplete.
- Appointments means employment types; it is not a patient appointment scheduler.
- Payroll processing currently computes basic pay, holiday pay, overtime, allowances, and lateness deductions. Other configured adjustment categories are not all wired into the calculation pipeline. The code uses a fixed overtime amount of 100 per hour and leaves a processed run in DRAFT because its status-transition call is commented out. Finalize is registered as a route but has no controller method.
- The attendance view allows all tenant employees to be selected; some old scope helpers refer to classes/schema names absent from the active implementation.
- Several UI libraries load from public CDNs, so the full interface requires internet access.

## Local verification

Verified on 13 October 2026 using bundled PHP 8.5.11 and XAMPP MariaDB 10.4.32: login, the employee table in a browser, HRIS/admin/payroll pages, populated JSON list endpoints, daily time records, payroll run details, employee payroll details, and payroll processing of all four sample employees (zero processing errors). This is a local functional check, not a production-readiness assessment.
