# C-EMP verification

These tests use fictional fixtures only. The application has no new runtime dependency.
`input.php` needs PHP 8.3+ and no database. Real HTTP tests need Docker and Python 3's
standard library. `browser.cjs` optionally uses an already-installed Playwright/Chromium;
it is test tooling, not a Node build step. No package/lockfile was added.

## Pure checks with the existing Docker development runtime

From the repository root (PowerShell or Bash):

```sh
docker compose -f compose.dev.yaml exec -T app php /workspace/tests/c-emp/input.php
docker compose -f compose.dev.yaml exec -T app php -l /workspace/app/controllers/hris/EmployeeController.php
docker compose -f compose.dev.yaml exec -T app php -l /workspace/app/models/hris/EmployeeModel.php
docker compose -f compose.dev.yaml exec -T app php -l /workspace/app/services/hris/EmployeeInput.php
docker compose -f compose.dev.yaml exec -T app php -l /workspace/app/views/module_hris/employees.php
docker compose -f compose.dev.yaml exec -T app php -l /workspace/routes/hris-routes.php
```

These commands do not write to a database. Never point write tests at the ordinary demo.

## Dedicated disposable integration stack (Linux/Bash or WSL)

Prerequisites: approved local Docker daemon, existing `mariadb:10.11` and
`montero-dental-dev-app` images from the documented development setup, and the locked
Composer dependencies in the ignored checkout `vendor/`. When dependencies exist only
in the ordinary Docker app volume, copy that **dependency directory only** out first:
`docker compose -f compose.dev.yaml cp app:/workspace/vendor ./vendor` (use only if
`vendor/` is absent; do not overwrite an existing install). Follow cloud runtime CA/proxy
instructions for any required image build or dependency download. Do not change shared DBs.

```sh
bash tests/c-emp/fixtures.sh start
python3 -B tests/c-emp/acceptance.py
docker exec cemp-test-app php /workspace/tests/c-emp/input.php
node tests/c-emp/browser.cjs
bash tests/c-emp/fixtures.sh dispose
```

`start` refuses existing named resources, verifies an empty DB and seeds known fictional
records. The DB uses tmpfs, its own internal network, no persistent mount and no host port.
Only the app joins a separate frontend network; its port is loopback `8018`. Source/config
mounts are read-only; sessions/attachments are tmpfs. Existing local configs are not
overwritten. All passwords in this fixture helper are public disposable demo values.

The acceptance runner guards actual container labels, mounts, ports, network, DB hostname,
application config and DB DNS destination before writing. It creates only fictional users,
tenants and records, and briefly installs a synthetic failure trigger with `finally` cleanup.
It compares the other 35 table fingerprints during security/mutation checks. `dispose`
rechecks ownership/transport and removes **only** the two named test containers and two test
networks; their tmpfs data is intentionally discarded. It never removes volumes or normal
Compose resources. Do not reuse these names for shared services. If startup stops partway,
inspect the labelled resources and dispose only the fixtures actually created; do not run
broad prune/down-volume commands. DB isolation on this cloud runtime was tested; Windows
Docker Desktop/WSL execution remains NOT RUN.

Browser tests use real clicks and saves at 1440/768/375px, then read back the records.
Forty response scenarios are fully intercepted and leave all 37 DB table fingerprints
unchanged; they establish browser behavior only. Real saves use newly created fixture
employees/rates, never seeded payroll records. A global request interceptor aborts unexpected
mutations; simulations block service workers and do not forward writes. The 57 cross-module
checks are page/viewport smoke checks, not a full payroll-processing or accessibility suite.

Expected policy limitation: acceptance explicitly reports BLOCKED compensation authorization
certification because legacy HRIS/PAYROLL compensation routes lack agreed operation rights,
and PAYROLL access remains tenant-wide. See `docs/work-items/c-emp.md`. Do not interpret a
passing technical assertion that reproduces this legacy gap as a secure policy result.

No real clinic records, production access, payroll processing, imports or deployments are
part of these checks. Never log form payloads, session cookies or CSRF tokens.
