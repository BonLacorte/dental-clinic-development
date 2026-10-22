# HRIS Employees UX modernization — A, B and C

- Branch: `feat/hris-employees-ux-modernization`
- Status: `READY_FOR_REVIEW` — draft/source review only; PHP, application, browser and export runtime acceptance remain BLOCKED.
- Last reviewed: 2026-10-22, Asia/Manila
- Development baseline: `b5526bb4a7999bbd3c253fcdda1c8419a171314d`, merge of PR #11 at its reviewed head `6ed6f3d242d03d93a16d81e108d392b2c8fbfd39`.
- Authorization: owner explicitly accepted prior runtime risk for this personal development repository and authorized PR #11 merge, A/B/C implementation, narrow export backend changes, commits, branch push and a draft PR. No production access/deployment or PR #8 integration.

## Table UX contract and scope

`/hris/employees`: find, scan and act on employees; compare records in the existing six-column table (Employee No, Name, Branch, Date Hired, Status, Actions). Status is employment status. Existing row View/Edit/Delete permission predicates and legacy Compensation visibility are retained. Existing explicit Search/Clear, five sortable fields plus newest-added default, list endpoint and 10-row pagination remain unchanged. A refines presentation; B is the separately authorized mobile-card/sorting/focus change; C adds the separately authorized authenticated Excel endpoint. Shared dialogs and mutation code are protected.

Application allowlist: `app/views/module_hris/employees.php`, `public/assets/css/style.css`; C additionally `routes/hris-routes.php`, `app/controllers/hris/EmployeeController.php`, `app/models/hris/EmployeeModel.php` and focused export service files under `app/services/hris/`. Focused verification scripts and this task handoff are in scope. No dependency/schema/permission-database changes or shared Payroll changes.

MTD-01–07/10–13 apply to desktop presentation and mobile information/action parity; MTD-14–18/20 apply to shared query state and feedback; MTD-21–27 apply to table/card breakpoints, keyboard/focus/contrast; MTD-28–30 require table-only print, protected-code checks and honest evidence. MTD-08/09 preserve existing nonfinancial identifier/date values. MTD-19 is N/A: no selection/bulk actions. MTD-24 mobile cards are explicitly authorized here. No new filters or unsupported sorts.

## Progress

- PR #11 merged with GitHub expected-head guard and merge-commit strategy. Merge parents match prior main and reviewed PR head. Original `work` checkout preserved; separate worktree created.
- A implemented: Employees heading/actions, labeled explicit-search grouping/help, visible existing applied-result/order feedback, scoped Premium table typography/spacing and pagination refinements. Add predicate/trigger and all existing JavaScript preserved.
- A committed as `d32d51f`; whitespace and exact JavaScript preservation checks PASS.
- B implemented: below-768px labeled cards and mobile field/direction sort controls; both render from a shared escaped representation of the same 10-row response. One delegated row-action handler and existing dialogs; responsive focus restoration chooses the visible matching action. Tablet retains local-scroll table; print hides cards/mobile controls and retains the table. No extra request on breakpoint changes.
- B committed as `1ead774`.
- C implemented: permission-conditioned Export Excel, successful-applied-context POST/CSRF, strict session/export policy and a dedicated bounded query, minimal-field XLSX service, private temporary files, binary response and cleanup. Follow-up B focus tracking also handles native focus moving to body during a responsive transition.
- C committed as `6637846`.
- B verification: JavaScript syntax and `node scripts/verify-employees-frontend.cjs` PASS with an externally captured baseline argument on this restricted executor. Tests use SIMULATED DOM/AJAX: safe escaping/Unicode/IDs, table/card/action parity, legacy permissions, applied state, Search/Clear, five fields plus date-added in both directions, pagination, stale-response protection, one request per interaction and exact protected mutation/dialog function comparisons. This is not visual browser evidence.

## Verification and limits

Runtime prerequisites were previously blocked on this Debian cloud machine. Do not report historical source/browser claims as fresh passes. No application/DB writes or seed/startup scripts have run. PHP/real browser verification will be attempted only with available supported tools; independently executable checks continue.

## Export policy and resource decisions

`POST /hris/employees/export` inherits Auth/Tenant/HRIS and **both** `hris.employee.view` and `hris.view` Permission middleware. The endpoint checks authentication, active tenant, explicit permission-map intersection, HRIS subscription, CSRF and assigned branches again. There is no suitable export permission in the inspected baseline. Export intentionally requires explicit permission/module entries **even for Super Admin**, and nonempty validated branch assignments for everyone. This new endpoint grants no legacy empty-branch or unrestricted/global fallback; existing employee/Compensation authorization is not changed.

Source findings: `AuthController` stores IDs from `UserModel::getUserBranches()` in the server session. The list query is tenant-only. `BranchMiddleware` has an empty-assignment fallback; obsolete `ScopeService` refers to a different schema and cannot establish production policy. The export uses the conservative intersection of session-assigned branch IDs, current DB assignments, an active (not soft-deleted) tenant branch and matching employee/user tenant. Soft-deleted employees, branches, users and branch assignments are excluded. Client tenant/branch IDs are rejected, not used for scope. No list/mutation query was altered. Missing/invalid branch context blocks export rather than allowing tenant-wide access. Thus export can contain fewer employees than the legacy list; UI feedback identifies assigned-branch scope. Senior review of the actual production branch and Super Admin policy is mandatory.

Five worksheet columns only: Employee Number, Employee Name, Branch, Date Hired, Employment Status. The last comes from `hris_employment_statuses.name`, not `e.status`. Four text columns use explicit Excel string types, including formula-looking values; dates are validated real Excel numeric dates with `yyyy-mm-dd` formatting. Leading zeros/Unicode remain intact. One worksheet named Employees, bold teal header, widths, wrap, autofilter and frozen A2. Zero matches produce headers only. Filename date is explicitly Asia/Manila.

The query reuses existing search SQL/LIKE wildcard behavior and five UI sorts plus date-added default, adding an ID tie-break for deterministic export ordering. It retrieves at most cap+1 rows and rejects overflow; no pagination-limit trick or silent truncation. Default cap is **1,000** rows; `MONTERO_EMPLOYEE_EXPORT_MAX_ROWS` may configure 1–5,000. Hard text budget is 2,000,000 bytes, with Excel's 32,767-character cell limit. These are conservative **unbenchmarked development limits**, not capacity evidence. No global memory/time limit was increased.

Temporary output uses a random directory under PHP's temp directory, mode 0700 with a 0600 file. Exceptions clean it; response-finally/shutdown close the stream and clean it, including disconnects. Generic failures return JSON without SQL/record/path details; cleanup failures log a fixed operational message. Production must supply a secure local temp directory/ACLs, suitable PHP error handling and operational cleanup monitoring. No migration, seed, permission grant or configuration file change is included.

## Verification results

Environment: existing Debian 13.6 x86_64 cloud workspace, Node 24.19.0. Chromium 154.0.8037.57 and Playwright 1.62.0 are present but sandbox-enabled launch fails (`TargetClosedError`); extra network permission did not fix it. PHP/Composer executables remain unavailable. No initialized isolated fictional application DB is established. No dependency was installed and no application server, container, database initialization or employee/payroll write was executed.

| Check | Result | Actual evidence/limits |
|---|---|---|
| PR #11 head/main/merge safety | PASS | Exact reviewed head and old main matched; clean merge state; GitHub expected-head guarded merge; resulting commit parents verified |
| JavaScript syntax | PASS | Extracted employee script with PHP JSON placeholder; `node --check` exits 0 |
| Frontend logic | PASS | `node scripts/verify-employees-frontend.cjs /tmp/montero-employees-baseline.php`; **SIMULATED** DOM/AJAX/binary transport, not a browser/app test |
| Protected source | PASS | `python3 scripts/verify-employees-preservation.py`: 14 controller and 18 model existing non-index methods, complete dialog/form markup, existing routes, full original CSS prefix, exact allowlist and export static safety gates |
| Existing mutations/dialog functions | PASS | VM comparisons preserve named functions and mutation handlers byte-for-byte; only approved responsive focus restoration changed |
| Dependencies | PASS (source only) | `composer.json` and `composer.lock` unchanged; existing original-checkout metadata identifies PhpSpreadsheet 5.10.0. Autoload/platform execution unavailable; no vendor installed/copied into this worktree |
| Whitespace | PASS | `git diff --check` |
| PHP lint: view/controller/model/service/routes/test | BLOCKED | No compatible PHP executable; no syntax pass claimed |
| Real XLSX/auth/CSRF/query/isolation/limit/cleanup tests | BLOCKED | Authored `scripts/verify-employee-export.php` but could not execute it. No real workbook/download is claimed |
| Application routes/dialogs/role fixtures | BLOCKED | No safe running PHP/DB environment |
| 1440/1280/992/991/768/767/375/320px, overflow, hidden controls, focus transitions, 200% text | BLOCKED | Real browser launch and application prerequisites unavailable; no screenshots or measured visual/a11y claims |
| Table-only print and Payroll/payslip output | PASS source / BLOCKED runtime | New mobile/export UI hidden for print; table retained; shared Payroll files unchanged. No new print preview/PDF |

Local sanitized evidence is outside Git under `/tmp/montero-employees-abc-evidence/` (frontend-tests.log, preservation.log, javascript-syntax.log, runtime-limitations.json). This directory is ephemeral and not a GitHub attachment. The durable scripts/documentation are in this branch. Historical PR #11 screenshots/tests are not new implementation evidence.

Run on an approved existing compatible runtime, from the feature checkout:

```bash
node scripts/verify-employees-frontend.cjs
python3 scripts/verify-employees-preservation.py
php scripts/verify-employee-export.php
```

The PHP suite never loads project config/bootstrap or connects to the app DB. It verifies authorization/CSRF/parameters and real XLSX save/read-back on fictional in-memory arrays, >10 rows, Unicode/formula strings, empty results, limits and private-file cleanup. Optional PDO SQLite enables isolated `sqlite::memory:` SQL fixtures for tenant/branch/deletion/revocation/search/order/sentinel behavior; absence is reported BLOCKED. It remains supplementary to actual MySQL and HTTP tests. An optional first argument selects an existing approved Composer autoloader without installing/copying dependencies. On restricted Node executors, capture the accepted baseline with `git show b5526bb4a7999bbd3c253fcdda1c8419a171314d:app/views/module_hris/employees.php > /tmp/montero-employees-baseline.php` and pass that path to the JS suite.

## Production transfer requirements — no deployment authorization

Application files: `app/views/module_hris/employees.php`, `public/assets/css/style.css`, `routes/hris-routes.php`, `app/controllers/hris/EmployeeController.php`, `app/models/hris/EmployeeModel.php`, `app/services/hris/EmployeeExportService.php`. Review aids: this handoff and the three `scripts/verify-employee*`/`verify-employees-*` scripts. A/B/C depend sequentially on the accepted PR #11/Premium development baseline; do not replace whole production files with these versions blindly.

Senior developer must obtain an authorized fresh production source comparison and reconcile its MVCLite routes, employee response/sort/search fields, existing mutation/modal changes, Premium CSS/tokens and separate correctness/security work. Production may differ from development `main`; PR #8 was not imported or recreated. Preserve independently accepted PR #8 work if it later overlaps. No production snapshot/data/credentials were accessed here.

Before transfer, verify PHP >=8.3/64-bit, compatible existing PhpSpreadsheet 5.10.0 and lock/platform extensions (PDO MySQL, mbstring, ZIP, GD and DOM/XML-related requirements plus documented application extensions), Bootstrap/jQuery/Icons and autoloading. Review actual permission/module/Super Admin policy, session branch IDs, assignment revocation, active branch vs all assigned branches, and tenant restrictions. New SQL assumes the **development** names/columns, including `users.id/tenant_id/deleted_at`, `user_branches.user_id/branch_id/deleted_at`, `tenant_branches.tenant_id/deleted_at` and nullable tenant-owned/global employment lookups. Never assume the reconstructed demo schema matches production.

Keep default limits until representative export memory/time/DB-index/concurrency benchmarks justify a separately reviewed configuration. Validate private temp directory ACLs and cleanup/disconnect behavior on the actual host. Run PHP lint and the suite, real authenticated/denied/invalid-CSRF HTTP tests, tenant/branch/deleted/stale-assignment isolation, >10 authorized matches, search/all sorts, real Excel read-back and limits. Then run the eight viewports/200% text, keyboard/breakpoint/dialog focus, table/card parity and request counts, long names/branches, empty/loading/error/Retry, permission cases, employee mutations only on separately approved disposable fictional fixtures, and same-data Payroll/payslip print regression checks. No new feature PR merge or production use is authorized by this implementation.

## Commit/file transfer inventory

| Commit | Purpose / exact application files |
|---|---|
| `b5526bb4a7999bbd3c253fcdda1c8419a171314d` | Accepted development prerequisite: merge of reviewed PR #11 (`6ed6f3d242d03d93a16d81e108d392b2c8fbfd39`). Senior must reconcile its Premium presentation with production separately; do not transfer its whole baseline automatically. |
| `d32d51f99cf12989a2891fa64c6d32a2d5f7f507` | A: `app/views/module_hris/employees.php`, `public/assets/css/style.css`; adds this task handoff |
| `1ead774cfa889258dd7c1a3c4614872d7f100204` | B: same view/CSS, `scripts/verify-employees-frontend.cjs`, task handoff |
| `66378468184d022bc35e5d68b08411678157c779` | C: view/CSS, `routes/hris-routes.php`, `app/controllers/hris/EmployeeController.php`, `app/models/hris/EmployeeModel.php`, new `app/services/hris/EmployeeExportService.php`; JS verification update, new `scripts/verify-employee-export.php` and `scripts/verify-employees-preservation.py`, task handoff |

The A→B→C application commits are sequential, not independent patches against arbitrary production source. This final handoff-only update contains no additional application behavior. No dependency/config/schema/permission-database/Payroll files changed. Production transfer remains a separately approved senior-developer operation after runtime/security validation and baseline reconciliation.

Next: push `feat/hris-employees-ux-modernization` and open a **draft** PR into development main, then run the blocked checks in an existing authorized isolated environment. Do not merge the Employees PR automatically. Preserve the original `work` checkout and keep PR #8 separate.
