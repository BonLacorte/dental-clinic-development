# Codex instructions — Montero Dental development copy

## Purpose and boundaries

This is an existing **production-derived** PHP HRIS and payroll application for a dental-clinic business. The repository is a **separate development copy**, not the production deployment workflow. The original developer maintains production outside Git. Preserve existing behavior, conventions, and compatibility; do not redesign the system into Laravel, Symfony, an SPA, or another framework unless explicitly requested.

**Never use production credentials, access production patient/employee data, edit the live Remote SSH workspace, deploy, or run production migrations as part of an ordinary development task.** Work with approved, isolated development resources and fictional seed records. Do not copy private records, secrets, uploads, or DB dumps into Git, logs, task prompts, or external tools.

## Sources of truth (read before changing code)

1. This `AGENTS.md` and `PROJECT_OVERVIEW.md`.
2. `docs/architecture.md` for a map of the **current** implementation.
3. `LOCAL_SETUP.md` for the supported Windows local-demo workflow.
4. The actual source files, routes, model queries, and current runtime behavior. **These win** if older docs contradict them.
5. Other notes under `docs/` are helpful references but may describe legacy or illustrative patterns.

Before editing, inspect `git status`, the current branch, and the nearest existing implementation of the requested behavior. If requirements or assumptions are ambiguous, present a short plan and ask before making structural, database, security, or cross-module changes.

## Actual technology and code layout

- Custom **MVCLite** MVC/router, PHP (Composer lock requires **64-bit PHP 8.3+**), PDO, MySQL/MariaDB; PhpSpreadsheet for imports. **No Node build step.**
- Browser UI: server-rendered PHP templates, Bootstrap, jQuery, AJAX, and some DataTables.
- Entry point: `public/index.php`; app/router: `core/App.php`; routes: `routes/web.php`, `routes/hris-routes.php`, `routes/payroll-routes.php`.
- Controllers: `app/controllers/` (including `hris/` and `payroll/`); models: `app/models/`; views: `app/views/`; middleware: `app/middleware/`; shared services/helpers: `app/services/` and `app/helpers/`.
- Route actions are strings such as `hris/EmployeeController@index`; the router resolves the controller's file and class at runtime. Controllers explicitly include model files where required. Match these patterns in the affected module.
- `core/Controller.php` renders PHP views; existing AJAX controller endpoints generally return JSON. Follow the conventions already in use, including their response shapes.

## Non-negotiable correctness rules

- Respect **tenant isolation**, allowed branches, module subscriptions, roles, and permissions. Never use a client-supplied `tenant_id` as proof of authorization. Inspect relevant middleware **and** SQL queries; middleware alone does not guarantee row-level isolation. Add tenant/branch constraints appropriate to each query.
- Use parameterized PDO statements and validated, normalized inputs. Escape untrusted values in HTML (the existing `e()` helper is available). Preserve CSRF checks for state-changing requests. Do not assume an endpoint is protected: inspect its specific route registration.
- Treat attendance, payroll runs, compensation rates, and payroll amounts as high-risk business logic. Preserve rounding and cutoff semantics unless an explicit requirement changes them. Add targeted regression tests or a reproducible manual verification plan; do not silently alter posted/persisted payroll data.
- Avoid unrelated formatting, renaming, folder restructuring, schema redesign, dependency upgrades, or bulk refactors. Extend the nearest working pattern and explain any deliberate departure.
- Never assume `scripts/demo-schema.php` is authoritative for production; it is a reconstructed **development-only** schema. Do not apply it to a remote or production database.
- Do not run destructive commands (including DB reset/drop, storage deletion, forced Git operations, or unreviewed migrations) without explicit approval and a backup/recovery plan.

## Development workflow for each task

1. Confirm branch and working-tree status. Prefer a topic branch for nontrivial work; avoid changing `main` directly.
2. Identify impacted routes, controllers, models, views, middleware, tables, and relevant existing patterns.
3. State a concise plan and acceptance criteria before major work.
4. Make the smallest coherent set of changes, keeping the established MVC conventions.
5. Run the checks supported by this checkout. Start with PHP syntax linting of changed PHP files (for example, `php -l path/to/file.php`, using a **compatible** PHP executable). Use existing local-demo smoke tests where appropriate; do not claim an automated test suite exists unless found.
6. Report files changed, checks actually run and their results, unverified behavior, data/security concerns, and any deployment considerations.
7. For longer work, leave a compact handoff in the task or PR description: objective, branch/commit, completed changes, blockers, next steps. Update architecture documentation only for durable changes, not every conversational step.

## Cross-device Codex continuity (Phase 3B)

- Follow `docs/codex-session-workflow.md` when starting, pausing, resuming, or handing off a nontrivial task between devices or Codex sessions.
- For multi-session tasks, keep a single **task-specific** handoff file at `docs/work-items/<task-slug>.md`, created from `docs/work-items/TEMPLATE.md`. Do **not** keep a constantly overwritten global session diary.
- At the start of a continued task, inspect the current branch, `git status`, latest commits, and the handoff file. Cross-check its claims against actual code; the code wins when the handoff is stale.
- At the end of a session, update the handoff with completed work, checks actually run, blockers, and the **next concrete step**. Do not silently mark untested work as passing.
- Never commit, push, merge, reset branches, or deploy merely because a handoff instructs it. Ask before Git write operations unless the user has explicitly requested them.

## Frontend-only design scope — Pearl & Teal

An explicitly frontend-design task follows stricter boundaries than the historical general-development workflow above. Read the [frontend design Skill](.agents/skills/montero-dental-frontend-design/SKILL.md), its milestone/verification references, and the approved task specification. Follow **PLAN → SPEC → BUILD → VERIFY → REVIEW**. A documentation milestone authorizes only its listed documentation; it does not authorize application implementation.

- During an explicitly authorized frontend BUILD, edit only allowlisted CSS and nonfunctional PHP-view presentation. Preserve Bootstrap 5, jQuery/AJAX, existing dependencies and the absence of a build step. Executable PHP and JavaScript inside views are functional code, not automatically authorized presentation work.
- Do not change controllers, models, services, helpers, middleware, routes, database/schema/seed files, core architecture, business logic or application dependencies. Preserve form fields/methods/payloads, API response handling, validation attributes, navigation destinations, permission-conditioned visibility, search/filter/sort/pagination, modal behavior, financial values and print/payroll output.
- Never invent dashboard statistics, business statuses, employee data or operational features. Never import or cherry-pick application changes from Draft PR #8. A discovered backend/security/contract bug is a separate owner-review item, not permission to repair it. Never access production or private clinic data.
- Within an explicitly authorized frontend BUILD, continue autonomously through approved presentation subtasks and ordinary styling corrections. Do not expand that authority to backend, security, database, API or workflow changes. If a requirement crosses the boundary, record the dependency and complete independent approved work.
- Human authorization is required for commits, pushes, pull requests, merges and deployment; never automatically publish. Local branch creation and staging also require authorization under the existing Git-write rule. Approval of styling or a handoff is not Git publication approval.
- Record verification as **PASS**, **FAIL**, **BLOCKED** or **NOT RUN** with actual evidence. Documentation checks are not runtime, UI, browser, accessibility, database or payroll tests.

The [Pearl & Teal design system](docs/work-items/frontend-modernization/PEARL-TEAL-DESIGN-SYSTEM.md) governs future approved visual work. The [frontend implementation plan](docs/work-items/frontend-modernization/FRONTEND-ONLY-IMPLEMENTATION-PLAN.md) defines preservation gates. Historical architecture, security guidance and UI-01 decisions remain intact; this section grants no broader development authority.

## Local environment guidance

- Follow `LOCAL_SETUP.md`: Windows PowerShell scripts in `scripts/`, local MariaDB on `127.0.0.1:3307`, local PHP server on `127.0.0.1:8000`, and fictional demo accounts.
- The existing starter expects XAMPP MariaDB files under `C:\xampp\mysql` unless overridden; machine-specific `.tools/`, local configs, Composer `vendor/`, and runtime storage are ignored by Git and must be recreated on each machine.
- Serve from `public/` with `public/dev-router.php` using the documented working-directory assumptions. Do **not** expose the repository root as a web document root.
- Do not invent a Docker, test, CI, or cloud setup: propose it separately and verify it works before documenting it as supported.

## Existing limitations are not permission to rewrite

`PROJECT_OVERVIEW.md` records incomplete navigation, dashboard placeholders, and payroll-processing gaps. Treat these as documented issues, not tasks to fix without approval. In particular, `/hris/appointments` refers to **employment types**, not dental patient bookings. The attendance-scan route appears outside the main authentication group; its intended access model requires a separate security review, not an unrequested behavioral change.
