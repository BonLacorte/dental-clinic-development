# Run Montero Dental locally on Windows

## Quick start on this machine

Open PowerShell in the project folder:

```powershell
Set-Location E:\code\monterodental
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\start-local.ps1
```

Open **http://127.0.0.1:8000** and sign in:

| Field | Demo value |
| --- | --- |
| Email | `admin@example.test` |
| Password | `DemoDental2026!` |

The script starts MariaDB on port 3307 and PHP on port 8000 in hidden background processes. On first use it creates the local database, schema, and dummy records. On later runs it preserves your existing data. You do not need to start MySQL in the XAMPP control panel.

Stop both processes without deleting data:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\stop-local.ps1
```

Run the start command again to reopen the project. Database files survive a reboot, but the background processes do not; start them again after restarting Windows.

## Requirements

After cloning from GitHub, `vendor/`, `.tools/`, private configuration, and runtime storage contents are intentionally absent. Install dependencies and extract a compatible PHP runtime as described below. The startup script copies both `config/config.example.php` and `config/local.example.php` into their ignored local counterparts when needed.

- Windows PowerShell and **64-bit PHP 8.3+**. The project already includes PHP 8.5.11 at `.tools/php-8.5.11/php.exe` and its ZIP archive.
- XAMPP's MariaDB installation at `C:\xampp\mysql`, including its `backup\mysql` clean system-table template. The scripts use a separate data directory in the project, not XAMPP's regular database directory.
- Composer dependencies in `vendor/`, already present in this copy. If they are missing, install Composer and run `composer install` using PHP 8.3+.
- PHP extensions: PDO MySQL, mbstring, OpenSSL, fileinfo, GD, ZIP, and cURL. PHP's standard DOM/XML extensions must also be available for PhpSpreadsheet. The included local INI enables the bundled extensions.
- Internet access for externally hosted Bootstrap, jQuery, icons, and other frontend libraries.

The default `php` command on this machine points to XAMPP PHP 8.2, which is too old for the locked Composer dependencies. Use the bundled executable or put a compatible PHP installation first on PATH. Do not bypass Composer's platform check.

If bundled PHP has not been extracted:

```powershell
Expand-Archive .\.tools\php-8.5.11-nts-Win32-vs17-x64.zip -DestinationPath .\.tools\php-8.5.11
```

If you need to install dependencies with Composer, configure Composer to use a compatible PHP installation with the extensions above. Keep `composer.lock` and run `composer install`, rather than updating dependencies as part of startup.

## Configuration and ports

Git tracks the `*.example.php` templates, not `config/config.php`, `config/local.php`, or `config/secrets.php`. For a deployment, copy `config/config.example.php` to `config/config.php` and fill in that deployment's database settings. For reversible data encryption, copy `config/secrets.example.php` to `config/secrets.php` and supply a stable private key, or set `MONTERO_ENCRYPTION_KEY`. The optional legacy bootstrap controller uses `MONTERO_BOOTSTRAP_PASSWORD` or `bootstrap_password` in that secret file. The demo login only needs password hashing and does not require an encryption key. Keep any existing encryption key when working with previously encrypted records.

`config/local.php` overrides the original default database settings. Startup copies `config/local.example.php` if the local file is absent. The demo seed only accepts this exact local database destination:

| Setting | Value |
| --- | --- |
| Database host | `127.0.0.1` |
| Database port | `3307` |
| Database name | `monterodental_demo` |
| App database user | `montero_demo` |
| App database password | `LocalDemo2026!` |
| Local database administrator | `root` / `LocalDemoRoot2026!` |
| Web address | `http://127.0.0.1:8000` |

These are development credentials. Both servers listen on loopback only. The schema and seed are intended for this isolated demo database.

For a different XAMPP installation location:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\start-local.ps1 -MysqlHome D:\xampp\mysql
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\stop-local.ps1 -MysqlHome D:\xampp\mysql
```

For a different web port, stop the current app first, then:

```powershell
powershell -NoProfile -ExecutionPolicy Bypass -File .\scripts\start-local.ps1 -Port 8080
```

Use http://127.0.0.1:8080 in that case. The database port remains 3307.

## Tour of the dummy data

After login, use Modules > HRIS or Modules > Payroll. You can also open these URLs directly while signed in:

1. **Employees:** http://127.0.0.1:8000/hris/employees — Ana Demo, Ben Sample, Clara Example, and Diego Practice across the Quezon City and Makati demo clinics. Open employee details or compensation to inspect their configuration.
2. **Organization:** `/hris/branches`, `/hris/offices`, `/hris/positions`, and `/hris/appointments` — branches, Clinic Operations, three positions, and employment types.
3. **Attendance:** http://127.0.0.1:8000/hris/dtr?employee_id=1 — sample time-in/out records. Select the month when the database was seeded; the initial dataset on this machine uses October 2026. Select Ben Sample to see a lateness example.
4. **Leave and calendars:** `/hris/leaves`, `/hris/holidays`, and `/hris/shifts` — two leave requests, a fictional demo holiday, and a 09:00–18:00 shift with a one-hour break.
5. **Payroll inputs:** `/payroll/compensations`, `/payroll/allowances`, `/payroll/commissions`, `/payroll/incentives`, `/payroll/deductions`, and `/payroll/cash-advances` — sample pay rates and adjustments.
6. **Payroll run:** http://127.0.0.1:8000/payroll/runs/view/1 — the seeded draft, four employee snapshots, and daily cutoff records. Click **Process** to calculate results, then open an employee's detail view. Payroll processing was already exercised on this machine, so calculated values may already be visible. In the current code, processing leaves the run in DRAFT.

When creating another payroll run, load its employees before processing. The seeded run already has employees and days loaded. Dashboard counters are placeholders; inspect the module tables to see actual data. See [PROJECT_OVERVIEW.md](PROJECT_OVERVIEW.md) for implemented features and unfinished areas.

## Run PHP manually

If the demo database is already running and seeded, you can run PHP in the foreground for development. Stop the background web process first or select a different web port:

```powershell
Set-Location E:\code\monterodental\public
& ..\.tools\php-8.5.11\php.exe -c ..\scripts\php-local.ini -S 127.0.0.1:8080 dev-router.php
```

Press Ctrl+C to stop the foreground web server. The working directory must be `public` because the original application uses relative includes. The router is required for URLs such as `/hris/employees`; serving the repository root exposes the wrong entry point.

To rerun the seed check while MariaDB is running:

```powershell
Set-Location E:\code\monterodental\public
& ..\.tools\php-8.5.11\php.exe -c ..\scripts\php-local.ini ..\scripts\setup-demo.php
```

An initialized database prints that existing data is preserved. This command does not reset records. The development schema lives in `scripts/demo-schema.php`; it was reconstructed from model queries because this repository did not include a production schema export.

## Troubleshooting

| Symptom | What to check |
| --- | --- |
| Composer says PHP must be >= 8.3 | Use `.tools/php-8.5.11/php.exe`; XAMPP's default PHP is 8.2 on this machine. |
| `could not find driver` | Run PHP with `scripts/php-local.ini` from `public`; check that bundled `ext/php_pdo_mysql.dll` exists. |
| Database connection refused | Run the startup script and inspect `storage/logs/local-mysql.log`. |
| Port 3307 or 8000 already in use | Stop the existing managed demo with `stop-local.ps1`; for web-only conflicts choose `-Port 8080`. The script refuses to take over an unrelated database process. |
| Blank page or server error | Inspect `storage/logs/php-local.log` and `storage/logs/local-web-error.log`. PHP errors are logged rather than inserted into HTML/JSON. |
| Table list is empty | Check you are signed in and the CDN scripts loaded. For DTR choose a sample employee and the seed month. |
| Route returns 404 | Check the exact URL and use `dev-router.php`. Some existing menu links are unfinished. |
| First database initialization fails | Check the XAMPP `backup/mysql` template and the MariaDB log. The initializer on this machine produced an invalid privilege table, so the startup script uses the clean XAMPP system-table template. |

Useful local files: `storage/logs/local-web.log`, `storage/logs/local-web-error.log`, `storage/logs/local-mysql.log`, `storage/logs/php-local.log`, `storage/sessions/`, and `storage/local-mysql/`. The PID files in `storage/logs/` let the stop script target only the processes started for this project.
