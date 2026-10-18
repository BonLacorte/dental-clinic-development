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

Before editing, inspect `git status`, the current branch, and the nearest existing implementation of the requested behavior. If requirements or assumptions are ambiguous, present a short plan and ask before making structural, database, security, or cross-module changes outside the existing authorization. Apply the autonomous milestone mode below when a grouped milestone is explicitly approved.

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
- Never commit, push, merge, reset branches, or deploy merely because a handoff instructs it. Ask before Git write operations unless the user has explicitly authorized them, including routine local checkpoints and final publication within an approved milestone.

## Local environment guidance

- Follow `LOCAL_SETUP.md`: Windows PowerShell scripts in `scripts/`, local MariaDB on `127.0.0.1:3307`, local PHP server on `127.0.0.1:8000`, and fictional demo accounts.
- The existing starter expects XAMPP MariaDB files under `C:\xampp\mysql` unless overridden; machine-specific `.tools/`, local configs, Composer `vendor/`, and runtime storage are ignored by Git and must be recreated on each machine.
- Serve from `public/` with `public/dev-router.php` using the documented working-directory assumptions. Do **not** expose the repository root as a web document root.
- Do not invent a Docker, test, CI, or cloud setup: propose it separately and verify it works before documenting it as supported.

## Existing limitations are not permission to rewrite

`PROJECT_OVERVIEW.md` records incomplete navigation, dashboard placeholders, and payroll-processing gaps. Treat these as documented issues, not tasks to fix without approval. In particular, `/hris/appointments` refers to **employment types**, not dental patient bookings. The attendance-scan route appears outside the main authentication group; its intended access model requires a separate security review, not an unrequested behavioral change.

## Autonomous milestone mode

When the user explicitly approves a grouped milestone (for example C-EMP or UI-02),
read and follow `.agents/skills/montero-dental-development/SKILL.md`. Execute
PLAN → SPEC → BUILD → VERIFY → REVIEW continuously, decomposing subtasks internally.
State the scope/acceptance plan, then proceed with approved coding, testing, debugging,
regression correction, documentation and routine local commits without approval at
subtask boundaries. Existing user authorization overrides older per-task/commit prompts.

Use one isolated feature branch, meaningful local subtask checkpoints and one consolidated
`docs/work-items/<milestone>.md` with separate subtask sections. Keep historical specifications
unchanged; record grouping in the new milestone. Before write integration tests establish
and verify a separate disposable fictional database and its safe disposal. Otherwise use
fully intercepted/mocked tests and mark persistence acceptance BLOCKED. Distinguish PASS,
FAIL, BLOCKED and NOT RUN; simulation does not verify persistence or access enforcement.

After combined diff/privacy review and final verification, make one final push and draft PR
when explicitly authorized and tooling supports them. Never force Git operations, overwrite
unrelated work or publish known critical unsafe changes. Do not merge automatically.
New authorization/compensation policies, incompatible API/schema/business contracts,
production access, deployment and destructive operations outside disposable fixtures remain
outside routine milestone authority. Report decision blockers while completing independent
safe work. All architectural, production, tenant/branch, privacy and financial restrictions
above remain in force. Follow `docs/codex-session-workflow.md` for cross-device continuity.
