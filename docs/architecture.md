# Montero Dental — current architecture map

**Status:** Initial read-only source review, 13 October 2026. This describes the checked-in development copy on `main` at initial commit `c99ded60c92b33ba18f21a62a1461763fc779caa`; it is **not** a verified map of the live Remote SSH server. Confirm against current source before relying on specific implementation details.

## Scope

The checked-in application focuses on **multi-tenant HRIS and payroll** for a dental-clinic organization. It is not currently a complete patient records or dental treatment system. Core capabilities are user/tenant/branch administration, HRIS employee and attendance records, time-off/holidays, payroll compensation and adjustments, and payroll runs.

## Technology

- **Backend:** PHP; custom, non-namespaced MVCLite MVC/router under `core/`; PDO with MySQL/MariaDB.
- **Dependencies:** Composer (`composer.json`, `composer.lock`), including `phpoffice/phpspreadsheet` for biometric/spreadsheet imports. Lockfile dependencies require 64-bit PHP 8.3+.
- **Frontend:** server-rendered PHP views, Bootstrap, jQuery, AJAX, with DataTables in some modules. There is no Node.js build pipeline.
- **Time zone:** `Asia/Manila` is set in the public entry point.

## Request and response flow

```text
HTTP request
  -> public/index.php (session, Composer autoload, core classes, helpers/services)
  -> routes/web.php (includes HRIS + payroll route definitions)
  -> core/App.php (method and route-pattern matching)
  -> ordered route/group middleware (authentication / tenant / module / permission / branch where registered)
  -> app/controllers/... Controller@method
  -> app/models/... -> core/Model.php -> PDO -> MySQL/MariaDB
  -> PHP view or JSON response
```

The router supports `GET`, `POST`, `PATCH`, `DELETE`, `/{parameter}` routes, middleware groups, and POST `_method` overrides. Controller actions use strings such as `hris/EmployeeController@index`. The controller base class exposes `view($view, $data)`, which loads PHP view files and extracts supplied data. Model classes extend `Model`, which reads `config/config.php` and overlays `config/local.php` if present.

The built-in dev router, `public/dev-router.php`, serves actual public assets and forwards app URLs through `public/index.php`. The app's relative includes assume the documented `public/` working directory; serving the repository root is not equivalent.

## Repository map

| Path | Active responsibility |
| --- | --- |
| `public/index.php`, `public/dev-router.php`, `public/.htaccess` | Web bootstrap, local routing, web-server URL rewriting |
| `core/App.php` | Routing and middleware dispatch |
| `core/Controller.php`, `core/Model.php`, `core/Storage.php` | View rendering, PDO connection, upload/storage utility |
| `routes/web.php` | Main login, admin, and module entry routes; includes specialized routes |
| `routes/hris-routes.php` | HRIS page and AJAX endpoints |
| `routes/payroll-routes.php` | Payroll configuration, import, and run endpoints |
| `app/controllers/` | HTTP/JSON actions; subfolders for HRIS and payroll |
| `app/models/` | SQL persistence and payroll/attendance calculations |
| `app/views/` | HTML/PHP templates and page-local JavaScript |
| `app/middleware/` | Auth, tenant, module, role, permission, and branch checks |
| `app/services/`, `app/helpers/` | CSRF, crypto, scope, validation, common helpers |
| `config/` | DB configuration templates, optional local override, private secret template |
| `scripts/` | Isolated demo database schema/seed, local PHP INI, Windows startup/shutdown |
| `storage/` | Runtime attachments, sessions, logs, local database files (ignored) |
| `docs/`, `PROJECT_OVERVIEW.md`, `LOCAL_SETUP.md` | Project documentation; not all older examples match active implementation |

## Authentication, authorization, and data boundaries

- `AuthController` validates a login CSRF token, checks password hashes, regenerates the session identifier, and loads a session containing user ID, tenant ID, roles, permissions, module subscriptions, and assigned branches.
- Middleware includes `AuthMiddleware`, `TenantMiddleware`, `ModuleMiddleware`, `PermissionMiddleware`, `RoleMiddleware`, and `BranchMiddleware`. Route groups apply middleware where configured. Authorization requirements must be checked **per route**; not every route has the same permission checks.
- Permission lookups use a session permission map; the session also tracks active tenant and branch context.
- Tenant and branch scoping must be enforced in the actual data queries and operations as well as in route/middleware checks. A session tenant value or role bypass is not an adequate substitute for verifying the affected record belongs to the intended scope.
- CSRF helpers are implemented in `app/services/Csrf.php`; encryption/password helpers are in `app/services/Crypto.php`. Their existence does not prove every handler applies them correctly.

## Main functional areas

| Module | Primary code and behavior |
| --- | --- |
| Administration | `UserController`, `TenantController`, related models/views; users, branches, roles/permissions and subscriptions |
| HRIS | `app/controllers/hris/`, `app/models/hris/`, `app/views/module_hris/`; employees, organizational units, positions, employment types, shifts, holidays, leave requests |
| Attendance | `AttendanceLogController` and `AttendanceLogModel`; logs, DTR, summaries, scanner-related endpoints |
| Payroll configuration | Allowance, commission, incentive, deduction, compensation, and cash-advance controller/model pairs |
| Payroll runs | `PayrollRunController`/`PayrollRunModel`; run creation, loading employee/day snapshots, processing, detail views, cancellation/reset operations |
| Biometric imports | `BiometricController`; PhpSpreadsheet-backed import workflow |

**Terminology:** The HRIS `/hris/appointments` module is an **employment-type** catalog; it does not manage dental patient appointments.

## Database and local setup

The repository does not include an authoritative production migration history. The `scripts/demo-schema.php` file reconstructs a **development-only** schema inferred from active model queries; it must not be treated as a production schema upgrade. `scripts/setup-demo.php` generates fictional data for an isolated `monterodental_demo` database.

The documented Windows setup uses PowerShell startup/shutdown scripts, XAMPP MariaDB files, port `3307` for local MariaDB, and port `8000` for local PHP. `LOCAL_SETUP.md` records the current machine-specific prerequisites. Composer dependencies, local secrets, the runtime PHP distribution, and local DB files are not tracked. Another computer must install/recreate them; a Git clone alone is not a complete runtime.

## Known behavior and review hotspots

These are observations, **not** a completed security, payroll, or production-readiness audit.

- The project overview identifies static home dashboard counters, some unfinished navigation/routes, and incomplete tenant creation.
- The overview reports that payroll processing uses a fixed overtime amount, does not yet wire every configured adjustment into calculations, leaves processed runs in `DRAFT`, and has a registered finalize route without a controller implementation. Verify current code before modifying any of these flows.
- `routes/hris-routes.php` registers `/hris/attendance/scan` outside the main authenticated HRIS group. Its intended authentication, rate limits, and abuse prevention require a separate review before changes.
- Older documents in `docs/` contain examples that may not match current controller signatures or routing; the running code takes precedence.
- No committed automated test suite or CI workflow was identified in the initial repository tree. Local-demo smoke-check notes are in `PROJECT_OVERVIEW.md` and `LOCAL_SETUP.md`; they are historical results, not independent verification on every clone.

## Safe development and eventual production integration

1. Use a topic branch in the personal development repository; develop only against isolated, fictional data.
2. Keep changes narrow, document assumptions and verification, and avoid unapproved schema or architectural migrations.
3. Before deploying with VS Code Remote SSH, obtain a **fresh production source snapshot** and compare it with the baseline used by your branch. Your collaborator may have changed production outside Git.
4. Reconcile changes, retest them, obtain authorization, make backups, and transfer only reviewed files using the existing agreed method. Do not use this repository to force-reset or overwrite production.

## Reference files

- `PROJECT_OVERVIEW.md` — functional inventory and known limitations
- `LOCAL_SETUP.md` — current local Windows demo setup
- `core/App.php`, `core/Model.php`, `public/index.php` — actual request path
- `routes/web.php`, `routes/hris-routes.php`, `routes/payroll-routes.php` — routing and guards
- `app/controllers/AuthController.php`, `app/services/Csrf.php` — login/session/CSRF paths
- `app/controllers/payroll/PayrollRunController.php`, `app/models/payroll/PayrollRunModel.php` — payroll run logic
