# UI-01C — Employee screen pilot handoff

## Identity and status

- Task: UI-01C BUILD, presentation increment only.
- Branch: `feat/ui-01c-employee-pilot`.
- Status: **READY_FOR_REVIEW**, with explicit acceptance gates below.
- Last reviewed: 17 October 2026, Asia/Manila.
- Verified source baseline / current HEAD: `1cb8a13bbac4ab50196d341d8dfd6813e00c6ed5` (UI-01B merge, freshly fetched `origin/main`).
- Implementation commit: none. Work remains unstaged and uncommitted. No push, PR, merge, production access or deployment.

## Goal, scope and architecture

Implement specification section 5 in [UI-01-SPEC.md](UI-01-SPEC.md), preserving the approved PHP/MVCLite, MySQL/MariaDB, Bootstrap 5, jQuery/AJAX stack and all request/data/business contracts. The user's BUILD request authorizes isolated implementation/testing and branch creation; it does not authorize publication or employee/database mutations. The older specification's documentation-only stage is historical and was not edited.

Only application files changed:

- `app/views/module_hris/employees.php`
- `public/assets/css/style.css` (appended employee-scoped rules)

This handoff is the only added repository documentation. No controller/model/middleware/route, `main.js`, schema, financial rule, lockfile or UI-01 specification change. Existing source routes and PermissionMiddleware remain authoritative. Endpoint validation/response/access fixes stay separate.

## Completed implementation

- Preserved Employee No, Name, Branch, Date Hired, Status and Actions; all five visible sorts, original page size/parameters and default newest-added order. Added table/caption/header semantics, loaded-page counts, truthful sorting and current-page pagination.
- Local keyboard/touch horizontal scrolling; wrapping toolbar/pagination and long names/branches. No hidden columns or page-wide overflow clipping.
- Add/View/Edit/Delete visibility mirrors existing permission-map/Super Admin semantics. Compensation retains legacy HRIS visibility independently of Edit/Payroll policy. Named per-record actions, unique IDs and 44px targets.
- Add/Edit grouped fields with 1/2/3 columns at <768 / 768–1199 / ≥1200px. Fullscreen small dialogs below 576px, scrollable bodies with reachable headers/footer. All existing names, values, readonly number, CSRF and payload keys retained. Required hints/native attributes mirror existing server rules; server validation remains unresolved.
- Named dialogs/close controls; focus enters after Bootstrap shown, Tab/Shift+Tab stays inside (including browser-chrome edge prevention), focus returns to trigger or stable record/list fallback. Delete names the record and focuses Cancel.
- Dirty Add/Edit uses in-dialog Stay/Discard with initial Stay focus. Static editing backdrop, preserved drafts and pending-submission dismissal guard.
- Local list/detail/lookup loading, persistent failures and Retry. Prior list rows/context survive refresh failure. Sequenced/aborted superseded reads and per-dialog state prevent stale replacement. Lookup retries retain typed, selected and intentionally cleared optional values; unavailable stored selections stay visible and block submission.
- No automatic mutation retries. Minimal presentation guard requires existing explicit success before clearing; ambiguous/lost outcomes preserve entries and show unconfirmed-result feedback. This does not repair/verify server response correctness or complete C-EMP-02.

Application diff: **2 files, 680 insertions / 1,204 deletions**. View: +490/−1,204; CSS: +190. Screenshots/PDFs/test runners/full patches and detailed `REVIEW.md` are review artifacts outside Git at `/workspace/ui-01c-review`; no image or credential was added to the repository. Critical results/gates are retained here independently of those artifacts.

## Verification actually completed

Used existing fictional Docker demo and Cloud helper; PHP **8.3.35**, installed Chromium/Playwright. No dependency installation, reset, fixture insertion, employee/compensation mutation, payroll processing or attendance import.

Commands executed from repository root:

```sh
git status --short --branch
git fetch origin main
git rev-parse origin/main
git log -8 --oneline origin/main
git switch -c feat/ui-01c-employee-pilot origin/main
bash .tools/cloud-env.sh start
bash .tools/cloud-env.sh compose exec -T app php -v
bash .tools/cloud-env.sh compose exec -T app php -l ../app/views/module_hris/employees.php
git diff --check
git diff --stat
```

PHP lint, inline JavaScript parser and whitespace checks: PASS. Existing shared/controller/model/route files and specification checked unchanged. Temporary ignored/external runners exercised 30 employee/browser groups, 7 extra edge/measurement checks, 59 regression groups, and 4 in-memory permission-render cases. These initial read-only results pass; no uncaught JavaScript errors or attempted mutation requests in that sweep. Its runners block non-login mutations. The targeted simulated POST suite is documented separately below. A sidebar-only test synchronization rerun waited for full Bootstrap focus/scroll-unlock lifecycle and checked Modules Enter/Space.

Real interaction checks used ordinary actionable mouse clicks, keyboard and emulated-touch taps; no forced clicks/programmatic modal opening counted as passing user interaction. All four real fictional employees had View/Edit fields and all compensation fields compared with real endpoint reads. Search/no-match/Clear and five sorts in both directions used actual server responses. Dirty Add/Edit, Delete Cancel/Enter, focus entry/trapping/return, footer access and responsive layouts passed.

Measured viewport/document widths matched exactly at **1440, 768, 375, 320, 575, 576, 767, 991, 992, 1199 and 1200px** (768 also exercised as a boundary). At 375px the table is 895px wide inside a 341px scroll region. Tested 1440×1000, 768×1024, 375×812 and programmatic 200% root text size at 375×667; no document overflow and reachable dialog fields/footer. Repeated resizing with a dialog/sidebar open left no backdrop/body lock after lifecycle completion.

Failures/delays, empty/multiple-page datasets, long names/branches, missing optional lookup values and restricted permission maps were **SIMULATED**. Retry recovery used real reads. They do not establish real server authorization, persistence or large-data SQL behavior. Four permission cases were in-memory PHP rendering only, without creating accounts/sessions/DB fixtures.

All 25 visible employee buttons measured ≥44×44px. Computed focus outline: 3px `#0a58ca`. Palette ratios: primary 4.50:1, danger 4.53:1, secondary/input boundary 4.69:1, sort/focus 6.44:1, text status badge 10.72:1. No full WCAG claim.

Cross-module regression: **19 pages × 3 baseline widths = 57 comparisons**, all HTTP 200 and identical baseline/current CSS root/header/content geometry and control counts. Covered Main/HRIS/Payroll Home, Users/Tenants, both Branch paths, DTR/Leaves, six payroll configuration screens, runs/detail/employee and import. Login/Logout/native empty/email validity/password masking/current Employees nav smoke passed. No import upload/preview/process.

Payslip: clicked existing Print control; compared baseline/current print-media geometry/text and two A4 PDF pages. Both rasterized pages were byte-identical. Existing two-page spacing/business/identity limitations were preserved.

Read-only PDO row/schema/count fingerprints: **37/37 tables unchanged**, identical table set, no changed data or schema. Includes compensation and payroll snapshots/values/statuses/cutoffs. No raw records/dumps in Git or review results.

## Targeted code-review correction: success dismissal

The user identified a confirmed lifecycle defect: `employeeMutation()` called Bootstrap `hide()` with `state.pending` still true. Its synchronous `hide.bs.modal` event was prevented by the pending guard before `always()` could clear pending. Reproduced with **four fully intercepted SIMULATED success POSTs**, covering Add/Edit/Delete/Compensation; each success hide was prevented and no hidden event occurred. Initial read-only tests did not cover this success callback.

Exact application correction is **one added line** in the explicit-success path, immediately before existing `state.allowHide = true` and the existing toast/hide calls:

```js
state.pending = false;
```

The pending-dismissal guard and common `always()` cleanup remain unchanged. Explicitly confirmed completion may close; unresolved requests still block user dismissal. No other application code or CSS was changed in this correction; no C-EMP contract/validation/security fix was introduced.

Fresh SIMULATED verification: **30 passing scenarios**: Add, Edit, Delete, Compensation update/create × explicit success/failure, ambiguous response, network failure, malformed JSON at 1440×1000, plus five explicit-success cases at 375×812. All requests were held pending while Escape/header Close/footer Close or Cancel were attempted; the dialogs stayed open, pending stayed true, no discard prompt appeared and only one POST was issued. After release, success closed once, restored focus and displayed one SIMULATED success toast. Other outcomes preserved values/open state, showed persistent feedback and no success toast, with no automatic retry. Add/Edit Stay/Discard and failed-dialog Tab containment/focus return were rechecked. Compensation create uses an explicitly simulated absent-rate read.

The context interceptor covers all pages and disables service workers. It fulfills/aborts every employee/compensation POST, aborts unexpected mutations, and permits only ordinary login during login. **34 intercepted simulated POSTs total (4 reproductions + 30 corrected); zero server-forwarded employee/compensation mutations**, zero unexpected mutations and zero uncaught JavaScript errors. No payload/token/form contents logged. Evidence and runner: `/workspace/ui-01c-review/lifecycle-reproduce-results.json`, `lifecycle-corrected-results.json`, `mutation-lifecycle.cjs`.

UI-01A/B smoke rechecked at desktop/tablet/mobile: login/Logout/native validity/password masking, Employees/Home current state/navigation, no root overflow, sidebar toggle/Modules keyboard or overlay/Escape/focus return. Three viewport groups PASS. Earlier 57 cross-module and print comparisons remain prior evidence, not a new rerun claim.

Fresh read-only fingerprints after the targeted tests: **all 37 tables, schemas and row counts unchanged**. PHP lint, JavaScript parsing, whitespace and patch dry-runs PASS. Regenerated full/application patches, review/handoff and bundle; added a correction-only patch for convenience.

**A15 remains GATED / NOT RUN**. Intercepted UI success/failure does not establish actual persistence, valid CRUD, server validation/response correctness, CSRF or authorization. C-EMP-01/02/03 and unavailable device/AT/restricted-role coverage remain open. No staging, commit, push, PR, merge, production access or deployment.

## A01–A18 status and remaining gates

| Criteria | Actual status |
|---|---|
| A01 | PASS: approved application scope/stack/contracts. |
| A02–A05 | PASS: exercised Chromium desktop/tablet/mobile/width and lifecycle cases; physical-device coverage NOT RUN. |
| A06 | PASS: 200% root text-size/320px/short-height stress; native browser text-zoom NOT RUN. |
| A07 | PASS: navigation smoke; full UI-01B policy/route matrix NOT RUN. |
| A08–A09 | PASS: browser keyboard/DOM semantics; real screen-reader coverage BLOCKED. |
| A10 | PASS: measured normal-size pilot controls/focus/palette; full device/AT certification unavailable. |
| A11 | PASS: real list/search/sorts; multiple pages/no-records simulated only. |
| A12–A13 | PASS: controlled loading/races/failures, preservation and real Retry recovery; not server correctness tests. |
| A14 | PASS: administrator presentation/values/dirty behavior; live restricted-role integration BLOCKED. |
| A15 | **GATED / NOT RUN**: separately approved/verified C-EMP-01/02 needed; no server write tests; intercepted lifecycle checks only. |
| A16 | PASS: login/Logout/native validity smoke; lockout/invalid-CSRF matrix NOT RUN. |
| A17 | PASS: defined 57-comparison cross-module and print scope. |
| A18 | PASS: read-only table/schema/financial invariants; mutation deltas NOT RUN. |

## Dependencies, risks and limitations

- **C-EMP-01**: validation rejection and employee-number payload alignment remain unchanged. Readonly `employee_no` is still sent; no backend correctness fix.
- **C-EMP-02**: legacy response/failure truth and persistence need separate approval/verification. Explicit-success presentation feedback is not proof of scoped persistence or complete failure handling. No A15 acceptance claim.
- **C-EMP-03**: tenant/branch references, route/SQL authorization, CSRF and compensation-policy review remains open. Existing model tenant predicates remain present; they are not security certification. Compensation legacy access/values and mutation-CSRF behavior are unchanged.
- BLOCKED/NOT RUN: real screen reader, physical device, Firefox/Safari, native text zoom, restricted-role/two-tenant/branch server fixtures, employee/compensation write tests, payroll processing, attendance import.
- Browser refresh/forced tab closure has no new autosave/recovery guarantee. Controlled dismissal only is protected.
- Existing CDN dependencies remain. Browser commands trusted the provided Cloud proxy CA without disabling TLS. No production credentials/identity used.

## Review deliverables and rollback

Detailed REVIEW, full unified application/full patches, before/after desktop/tablet/mobile screenshots, annotated test results and print evidence are delivered with this task outside Git. Forward patch checks use files extracted from the verified base; no commit or staging is required to review/apply them.

A future authorized rollback must reverse only C's employee-view and employee-scoped CSS increment together. Preserve accepted A/B and independently accepted later correctness/security changes; check reverse application and prepare a selective compatibility diff if files overlap. Recheck employee list/search/sorts/actions/dialogs/focus plus login/sidebar/payroll/import/print. No schema rollback or data restore. Unexpected business-data writes require separate investigation; no hard reset/force push/full-file overwrite is authorized.

## First concrete next action

Review the implementation, complete patch and evidence with the user, preserving the listed acceptance gates. **Wait for explicit approval before staging, committing, pushing, opening a PR, merging or deploying.** On continuation, reread repository instructions and verify branch, HEAD/status and this handoff against actual source. Do not reimplement C-EMP fixes or infer new publication authority from this document.
