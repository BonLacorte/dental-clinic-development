# Montero Dental — portable Docker development (Phase 4A)

**Status:** Proposed dev-only setup; not yet proven end-to-end on your Windows/Docker Desktop machine. Keep `LOCAL_SETUP.md` as a working Windows/XAMPP fallback. These containers do **not** access the live Remote SSH deployment or its database.

## What changes and what stays the same

- The existing MVCLite/PHP app, routing, database business logic, and frontend remain unchanged.
- The Docker-based development option uses PHP **8.3** (Composer lock requires PHP 8.3+) and MariaDB **10.11**. This differs from the existing Windows demo's PHP 8.5.11 and XAMPP MariaDB 10.4.32: regression-check important behavior.
- The Docker demo database is isolated in a Docker named volume (`demo_mysql`) and **is not shared** with the existing Windows/XAMPP demo DB. Both environments seed fictional records independently; changes to one DB are not automatically visible in the other.
- Browser access binds to `127.0.0.1:8000` by default; the database has **no host port**. Docker's internal `db` service is used only when `MONTERO_DEMO_DOCKER=1` is set in the container.
- The existing `scripts/setup-demo.php` is changed minimally to allow **only** two explicitly whitelisted fictional demo destinations: Windows `127.0.0.1:3307` and Docker `db:3306`. No production host or database is permitted by the setup script.
- The `config/docker.example.php` demo template is overlaid as `config/local.php` **inside** Docker and will not replace the Windows machine's ignored `config/local.php`.

## Requirements

- Docker Desktop with WSL 2 / Linux containers, with Compose v2 available via `docker compose`.
- Internet connection for pulling images, Linux system packages, Composer packages, and the application's existing CDN-backed UI assets.
- A clone or local checkout of this repository with the Phase 4A changes installed. **Do not** run against a VS Code Remote SSH production folder, or point Docker mounts at production source.
- Avoid starting the Windows/XAMPP PHP demo on the same host port 8000 at the same time (or change the Docker web port below).

## First-time install in a feature branch

1. Check `git status`; create a new topic branch before changing tracked files.
2. Copy this Phase 4A package into the repository root. The included patch is separate because we do not want to overwrite the existing seed script blindly.
3. Apply the patch to `scripts/setup-demo.php`, first checking it against your exact version:

   ```powershell
   git apply --check .\phase4a-setup-demo.patch
   git apply .\phase4a-setup-demo.patch
   ```

4. Inspect the changes and remove the helper patch file **before staging**, because it is only an installation artifact:

   ```powershell
   git diff -- scripts/setup-demo.php
   Remove-Item .\phase4a-setup-demo.patch
   git status --short
   ```

## Start the isolated demo

From the repository root in **PowerShell**:

```powershell
docker compose -f compose.dev.yaml config --quiet
docker compose -f compose.dev.yaml up --build -d
docker compose -f compose.dev.yaml ps
docker compose -f compose.dev.yaml logs --tail 100 app
```

The `app` service waits for MariaDB to report healthy, uses the checked-in Composer lockfile to install dependencies into a named volume, seeds the database if not previously seeded, and starts the built-in PHP web server from `/workspace/public`, using `dev-router.php`.

Browse to <http://127.0.0.1:8000> and use **fictional** demo credentials from `LOCAL_SETUP.md` (`admin@example.test` and `DemoDental2026!`).

For a host port conflict with the existing local PHP server:

```powershell
$env:MONTERO_WEB_PORT = '8080'
docker compose -f compose.dev.yaml up --build -d
# Open http://127.0.0.1:8080
```

If the web service has already been created using another port, run `docker compose -f compose.dev.yaml down` then `up` to recreate it; **do not add `-v`** or you will lose the Docker demo database and other named-volume state.

## Inspect and verify (before treating this as validated)

```powershell
docker compose -f compose.dev.yaml exec app php -v
docker compose -f compose.dev.yaml exec app php -m
docker compose -f compose.dev.yaml exec app php -l ../scripts/setup-demo.php
docker compose -f compose.dev.yaml exec app php -l index.php
docker compose -f compose.dev.yaml logs --tail 100 app
```

**Manual smoke test:** log in, open HRIS employee listing, switch modules, visit attendance/DTR and a payroll-run detail page, and confirm that the demo data appears. Test representative payroll processing only against these fictional records. Report the actual results; the package itself does not prove the application runs correctly on Docker.

## Stop / restart / persistence

```powershell
docker compose -f compose.dev.yaml stop
docker compose -f compose.dev.yaml start
# Or: docker compose -f compose.dev.yaml down   # removes containers/networks, keeps named volumes
```

The source stays in your Git checkout; vendor, demo DB, sessions, and attachments are stored in named volumes on **that Docker host**, not in Git. `docker compose down --volumes` or `docker volume rm` **destroys demo state**; only do this with explicit confirmation and after an appropriate backup.

To update PHP dependencies after changing `composer.lock`, `docker compose -f compose.dev.yaml up --build -d` re-runs `composer install` and reconciles the volume. Do not run `composer update` as a startup step.

## Cross-device workflow

On each device: clone/pull the Git branch, install Docker Desktop once, run the same Compose commands. The **code, docs, and handoff files** move through Git, but the Docker database and local secrets do not. Initial seed records are fictional and recreated independently. A feature depending on changed database records must use committed migrations and safe seed fixtures, not rely on one computer's DB state.

## Diagnostics / common problems

| Problem | Checks / action |
| --- | --- |
| `docker compose` not found | Install/start Docker Desktop, enable WSL 2 backend, use Linux containers. |
| Image build fails | Confirm network access and available storage; Docker requires downloading PHP/MariaDB/Composer images and Debian packages. |
| `vendor/autoload.php` missing | Inspect `app` logs; Composer install must succeed before serving the app. |
| Database inaccessible | `docker compose -f compose.dev.yaml ps` and `logs db`; confirm `db` is healthy and the seeder reports completion. |
| Port 8000 occupied | Stop the old local PHP server or set `$env:MONTERO_WEB_PORT='8080'`. |
| MySQL compatibility failure | MariaDB 10.11 differs from XAMPP 10.4; inspect the actual SQL error and document necessary *dev-only* fixes. |
| PHP returns 404 | Check the `public/` working directory and `dev-router.php` command in Compose. |
| Unexpected write error | Check mounted `storage/sessions` and `storage/attachments` named volumes and Docker Desktop filesystem permissions. |
| Changes not reflected in browser | Ensure you're editing the checkout mounted at `.` and that the correct Compose project is running. |

## Operational and data protection restrictions

This Compose file deliberately uses known fictional demo credentials and is **not for staging or production**. Never replace the demo database URL with a clinic database. Do not import real employee/patient data, production configuration, uploads, or SSH secrets. Docker's internal DB network and loopback web port are intentional restrictions. Obtain permission before hosting code or running cloud agents. Review the live production version independently before any future Remote SSH deployment.
