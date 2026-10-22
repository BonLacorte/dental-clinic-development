# MD-HRIS-EMP-UX-001 v2 — implementation and production-transfer handoff

Date: 2026-10-22, Asia/Manila. Status: **development implementation delivered except policy-blocked compensation creation; runtime verification blocked**. Review in existing draft [PR #13](https://github.com/BonLacorte/dental-clinic-development/pull/13). No automatic merge or deployment.

## Baseline and authorization

- Accepted development main: `b5526bb4a7999bbd3c253fcdda1c8419a171314d`, rechecked live at final review. PR #11 was already merged; no second merge/reset.
- Starting Employees head: `681f84e0bb3bbcd02cfb92b20a042e032a766d53`. Extend existing `feat/hris-employees-ux-modernization` / draft PR #13 to preserve earlier A/B/C without competing work.
- Worktree: `/workspace/montero-employees-build`. Original `/workspace/dental-clinic-development` stays clean on `work` at `5afcec223e8dd09800596b3ebc663b524a660eef`.
- Latest v2 BUILD approval authorizes functional UI/dialog/menu changes, narrow Position reads and compatible export sorting, commits/push/draft PR. It supersedes older frontend-only boundaries for this named scope. No production access, DB connection/data/schema changes, permission grants, dependency installation or deployment.
- PR #8 verified open/draft/unmerged at `73a951ed6b015c2b71d4a1336efcfac475150e2f`; its source was not imported or modified. Preserve independently accepted correctness/security changes during later reconciliation.

## Milestone results

| Milestone | Implementation | Result and limits |
|---|---|---|
| A — page structure | IMPLEMENTED | Requested heading/description, permission-sensitive Add and existing Export, labeled explicit Search/Clear, applied context and supplementary ordering. |
| B — Position | IMPLEMENTED | Existing tenant-owned Position relationship; backward-compatible `position: string|null`; no migration, N+1 request or Position sort. |
| C — directory | IMPLEMENTED | Employee identity (name + exact number), Position, Branch, Employment Type, Employment Status, Actions; existing ten-row server paging/search/stale guard retained. |
| D — actions | IMPLEMENTED | Shared contextual menu, existing action predicates, Bootstrap/Popper portal and keyboard/focus protocol; real geometry unverified. |
| E — Details | IMPLEMENTED | Identity summary and Employment/Contact/Record sections, Date Resigned, selectable values, loading/Retry/error/reset and identity-match safeguards. |
| F — Edit | IMPLEMENTED | Same field contracts, grouped responsive form, inline supplied/native errors, Save changes, pending state and Stay/Discard. |
| G — Compensation | PARTIALLY IMPLEMENTED | Verified existing rates: grouped form, safe salary units, dirty/pending/uncertain states. Creation BLOCKED by ambiguous absence response and unresolved authorization policy. |
| H — Removal | IMPLEMENTED | Verified soft-removal wording, name/number/branch, Cancel focus, pending/duplicate/uncertain guards; unchanged route, CSRF payload and model behavior. |

“IMPLEMENTED” means source delivery, not passed application/browser/accessibility or production acceptance testing.

## Implementation decisions and preserved contracts

Position: `EmployeeModel::getAllEmployees()` adds `p.name AS position` and `LEFT JOIN hris_positions p ON p.id=e.position_id AND p.tenant_id=e.tenant_id`. Existing Position lookup/model establishes tenant ownership; unique primary key avoids row multiplication. Other fields, employee tenant/nondeleted filter, count query, search, limit/offset and ordering are unchanged. Controller already serializes the rows: no v2 controller/route edit. Legitimate null/empty Position displays “Not assigned”; missing/wrongly typed response property displays “Position unavailable” with a page notice.

One escaped presentation supplies table and existing below-768px cards. Cards retain Date Hired and gain Position/Employment Type; inactive representation uses `display:none`. No extra request on breakpoint changes. Four visible header sorts and supplementary controls share one state: `name`, `branch_name`, `employment_type`, `employment_status`, plus `employee_no`, `date_hired`, `created_at` (initial DESC). Existing name ordering remains by last name. Position is non-sortable. Export's model/service whitelists add only the already-supported `employment_type` order, preserving export of every applied directory order.

Menu: one body portal escapes local table scrolling. Construct existing Bootstrap Dropdown with a sibling menu, then portal it using fixed Popper viewport overflow/flip modifiers. While portaled, remove generic `.dropdown-menu`: Bootstrap's delegated keyboard API assumes sibling-toggle markup. Scoped menu implements arrows/Home/End, Escape, native button activation, Tab continuation from origin, outside close, one-open-menu lifecycle and disposal. Close on resize; table scroll updates positioning. One delegated action handler closes menu before opening one existing modal. Escaped record identity and origin/counterpart/post-refresh focus are preserved. Real clipping/touch/focus behavior remains unverified.

Details never exposes compensation; protected contact fields remain inside the dialog. Each open clears prior values, retains known identity while loading and rejects mismatched record IDs. Retry preserves input. Existing unresolved select values remain visible and block submission until resolved. Optional Details→Edit shortcut omitted to avoid stacked modals. Edit/Add retain IDs/names/types/requiredness/readonly/hidden token, lookup URLs and original payload construction. ISO dates are unchanged.

Shared mutation presentation requires explicit success before close/toast/refresh, disables fields/submission while pending and prevents duplicate requests. Known rejections preserve input; inline server errors appear only when actually supplied. Network/5xx or ambiguous responses latch uncertainty and prevent resubmission until close/reopen and record verification. No automatic mutation retries or server-idempotency claims. Compensation only updates a verified existing rate; former implicit creation from ambiguous absence is blocked. DAILY/HOURLY/MONTHLY labels use actual option values without changing amount, precision, conversion, dates or payroll frequency; unknown types stay generic/unresolved.

Verified delete model updates only `status='INACTIVE'` and `deleted_at=NOW()` for employee ID/tenant; list excludes deleted rows. No restore/related-record-retention promise. Backend zero-affected-row response truthfulness remains separately owned; UI cannot manufacture unavailable-record evidence.

Name/View/Edit/Remove/Add retain original permission predicates. Legacy Compensation visibility is unchanged and is **not endpoint security**. No middleware/grants/policy changes, PR #8 fixes or payroll-calculation changes.

Existing Excel retains its route, session/tenant/module/both-permission/CSRF checks, explicit branch-assignment intersection, bounded dedicated query, five-column workbook, string-cell safety, dates, private cleanup and successful-applied search/sort. Position/Employment Type were not added to the workbook. Default 1,000 rows (`MONTERO_EMPLOYEE_EXPORT_MAX_ROWS`, 1–5,000), sentinel rejection and text bounds are unchanged, conservative and unbenchmarked. Actual production branch/Super Admin policy needs senior review. See [earlier export handoff](hris-employees-ux-modernization.md) for full decisions; its old completion/test statements describe the earlier increment only.

## Verification and evidence

Actual environment: Debian GNU/Linux 13.6 x86_64 managed cloud workspace, Linux 6.18.55, Node 24.19.0. PHP/Composer unavailable. Chromium 154.0.8037.57 / Playwright 1.62.0 present, but fresh sandbox-enabled launch fails with `TargetClosedError`. No bypass/install, app bootstrap, DB access, seed/startup script or business mutation. This is not the owner's laptop runtime.

Fresh artifacts outside Git: `/tmp/montero-employees-v2-evidence/` (ephemeral, not GitHub attachments). No screenshots, print previews or real workbooks. Scripts/handoff are durable in the branch. CSV records target SHA, environment, viewport, expected/observed behavior, result and evidence/limitations per case.

| Check | Outcome | Evidence and meaning |
|---|---|---|
| JavaScript syntax | PASS | `javascript-syntax.log`: extracted employee JS with fixture replacing PHP JSON; `node --check`. |
| Directory/export transport | PASS — SIMULATED | `frontend-tests.log`: escaping/Unicode/exact IDs, table/card/action parity, null vs absent Position, legacy predicates, Search/Clear, seven supplementary sorts/both directions, four header sorts/control synchronization, paging/stale replies, request count, one modal invocation/action, export applied criteria/CSRF/duplicate/download/error protocol. Synthetic bytes are not workbook evidence. |
| Menu/dialog states | PASS — SIMULATED | Same VM log: singleton portal/configuration, keyboard/outside/focus/disposal protocol, compensation identity/absence and unit guards, pending duplicates, known rejection, uncertain latch, explicit success and mismatched-detail rejection. No actual DOM geometry/Bootstrap/browser lifecycle pass. |
| Position query | PASS — SELECT simulation | `position-select-tests.log`: 126 extracted old/new SELECT comparisons across three fictional pages, seven orders/both directions, matching/empty/no-match search, null and cross-tenant Position, employee tenant/deletion and no duplicates. SQLite `:memory:` CTE SELECTs only, no DDL/DML/app DB; not MySQL/HTTP verification. |
| Protected source | PASS | `preservation.log`: 14 original controller methods; 18 original model methods (authorized Position additions normalized); form contracts, original payload handlers, routes, Premium CSS prefix, allowlist, Payroll/dependencies and static export authorization/projection/limits/cleanup gates. |
| Whitespace/dependency files | PASS — source | `whitespace.log`, `source-integrity.json`: diff checks and unchanged Composer/lock. Installed platform/autoload/Composer execution BLOCKED. |
| PHP syntax, real XLSX/HTTP/auth/CSRF/scope/limits/cleanup | BLOCKED | No PHP; authored PHP export suite not run. No real workbook/download pass. |
| Real list/dialog/role interactions | BLOCKED | No safe initialized running PHP/fictional application DB. No business mutations attempted. |
| Requested widths/200% text | BLOCKED | `verification-matrix.csv`: 1440/1200/992/991/768/767/375/320px, long text, keyboard/touch/reduced motion, menu first/last/right/scrolled/bottom placement. No overflow/full-a11y compliance claim. |
| Navigation/Payroll/print | PASS source / BLOCKED runtime | Shared files unchanged; Employees print retains table and hides cards/menu/controls. No payslip preview or Payroll runtime test. |

## Unresolved findings

1. Existing Compensation routes lack a dedicated established permission policy; create/update handlers lack explicit CSRF validation in this baseline. Menu visibility and pending guards do not fix this. Separate security/policy review is a **production-transfer blocker**; preserve accepted fixes if later integrated.
2. Compensation's `success:false, message:'Data not found.'` cannot establish an authorized create workflow. Creation remains blocked pending authoritative authorized-empty response/policy. Existing-record edit does not prove endpoint authorization.
3. Legacy list is tenant-only, not an established production branch policy. Existing detail joins lack the new Position tenant constraint and detail lookup does not exclude deleted employees. New list Position join is tenant-safe; broader existing scope concerns remain separately owned. Do not infer newly verified isolation for every legacy field.
4. Existing mutation responses can report execution without authoritative affected-record confirmation. UI uncertainty safeguards do not repair backend correctness. No PR #8 changes imported/recreated.
5. No outstanding v2 source defect confirmed by available checks; PHP syntax and real CSS/menu/focus/dialog/XLSX behavior remain unverified. Appropriate for draft development review, not production acceptance.

## Exact transfer inventory

These sequential commits depend on accepted PR #11/Premium development main; reconcile against actual production rather than replacing whole files.

| Commit | Purpose / application files |
|---|---|
| `d32d51f99cf12989a2891fa64c6d32a2d5f7f507` | Earlier desktop A: employee view/CSS. |
| `1ead774cfa889258dd7c1a3c4614872d7f100204` | Earlier mobile B: view/CSS/cards/shared state. |
| `66378468184d022bc35e5d68b08411678157c779` | Earlier export C: view/CSS, controller/model, new export service and HRIS route. |
| `681f84e0bb3bbcd02cfb92b20a042e032a766d53` | Earlier handoff-only; v2 starting branch head. |
| `42789615fac3609d55f29902d24a474af2cbdb75` | Position list: `app/models/hris/EmployeeModel.php`. |
| `8b4aac45059db1e855437d43cb4433bc83af885f` | Directory/sorting: view/CSS, model/export-service sort whitelist. |
| `99688742ab7e56336ff77572fa8ec7fa1b7c715f` | Contextual menus: view/CSS. |
| `2e41e112c103873bb2bf239a2d3071d1b7da7c3c` | Details/Edit: view/CSS. |
| `db46347a0914e26125fed05613b6a4aae261e381` | Compensation/Removal/shared guards/focus: view. |
| `881c0f6589af0d320a7f850577a5b37702b9d9df` | Expanded SELECT paging/header-sort/action/mutation tests; no app change. |

V2 application files: `app/models/hris/EmployeeModel.php`, `app/services/hris/EmployeeExportService.php`, `app/views/module_hris/employees.php`, `public/assets/css/style.css`. Supporting: this handoff, `scripts/verify-employees-frontend.cjs`, `scripts/verify-employees-preservation.py`, `scripts/verify-employees-position.py`. V2 does not change controller/routes or older PHP export test. Full PR additionally includes prior A/B/C files/tests/earlier handoff. Final handoff-only commit is this file's latest commit, with no additional app behavior.

Production dependencies: Premium tokens/CSS baseline; Bootstrap **5.3.3 bundled Popper**, jQuery 3.7.1, existing Icons; PHP 8.3+/64-bit, compatible existing PhpSpreadsheet 5.10.0/extensions/autoload for export. Position assumes `hris_employees.position_id` and tenant-owned `hris_positions.id/tenant_id/name`. Confirm actual schema, session branch/permission semantics and response contracts. No migration/config/grants supplied. Keep private temp storage and conservative export limits until capacity/cleanup validation. Reconcile overlapping accepted security fixes; production may differ substantially.

## Next safe action / manual verification

Keep PR #13 draft and unmerged. Senior developer reviews source and resolves Compensation/branch policy, then executes blocked tests using an **already initialized isolated fictional** local environment. No data/schema/init/seed/business write is authorized by this handoff. Do not run `setup-demo.php`, `start-local.ps1` or Docker build/start as a shortcut.

From feature checkout, existing tools only:

```bash
node scripts/verify-employees-frontend.cjs
python3 scripts/verify-employees-preservation.py
python3 scripts/verify-employees-position.py
php -l app/views/module_hris/employees.php
php -l app/models/hris/EmployeeModel.php
php -l app/services/hris/EmployeeExportService.php
```

On restricted Node executors, capture `git show b5526bb4a7999bbd3c253fcdda1c8419a171314d:app/views/module_hris/employees.php` outside Git and pass that path as JS test argument. Full PR also needs PHP lint of controller, routes and PHP export test. Composer/platform/autoload need existing supported PHP. Older `scripts/verify-employee-export.php` includes optional SQLite fixture DDL/DML: **do not execute under current no-database-change instruction without separate fixture-write authorization**. Arrange separately approved array-only XLSX validation or a reviewed SELECT-only fixture path. Do not assume Windows paths work in Linux/WSL.

Only after verifying exact source, existing isolated initialized fictional DB, nonproduction credentials and no startup migrations, follow `LOCAL_SETUP.md` → “Run PHP manually” from `public` with actual existing executable/config and `dev-router.php`. For a compatible existing Linux setup the command is `php -S 127.0.0.1:8080 dev-router.php` from `public`. These prerequisites are absent here, so no server started.

Open `/hris/employees`; record SHA/browser/version and screenshots at all eight widths and 200% text. Check long names/branches/positions, Search/Clear, seven sorts/both directions and header synchronization, multiple pages, empty/error/Retry/stale replies, table/card parity and one list GET/interaction. Check first/last/scrolled/rightmost/near-bottom menus, arrows/Home/End/Escape/Tab/outside/touch, clipping and focus after modal/refresh/breakpoint changes. Open all dialogs; inspect fields/validation and Stay/Discard without saving/deleting. Test only existing approved role fixtures. Inspect compensation verified-record/ambiguous-absence and unit labels without submitting rates. Verify real XLSX values/Unicode/zeros/formula strings/dates, empty/denied/CSRF/tenant/branch/resource/failure cleanup through separately approved isolated harnesses. View HRIS/Payroll and an existing payslip print preview without payroll processing/imports. Owner acceptance/merge is a separate decision after evidence; production transfer remains separately reviewed and authorized.
