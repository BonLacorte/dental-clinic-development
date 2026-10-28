# Payroll Items Stage 2 - local integration verification

## Identity and status

- Task: `md-payroll-items-stage2-local-verification`.
- Branch: `review/md-hris-manual-refinements`.
- Committed baseline/unchanged HEAD: `52929377eabc8101460141b108062d2b0154b905`.
- Reviewed: 2026-10-27, Asia/Manila.
- Status: `READY_FOR_REVIEW` for the applied UI patch and actual read-only verification; not a persistence, calculation, security or production sign-off.
- Owner authority: apply the supplied ZIP patch, follow its LOCAL-HANDOFF, check first, preserve finished HRIS and all local navigation changes, verify six pages in the existing app, and leave uncommitted. No branch switch, PR #16 merge or backend/database changes.

## Input and application

- Input: `payroll-items-stage2-review.zip` from the owner's Downloads folder.
- ZIP SHA-256: `6a78d11cd0033cdaa566909bbcf6866313d58a0bb96a6a16e509eafe483ef74f`.
- Patch: `payroll-items-for-stage2-review.patch`.
- Patch SHA-256: `67ef69b3b387586ef26c99791aa8b5e7ee430a13f21a3ec4e3e064477f8a7e19`.
- Read LOCAL-HANDOFF; verified its branch/checkpoint/scope against actual state. Its 774 cloud simulations are preparation evidence, not fresh local test passes.
- `git apply --check` passed before application. Applied with ordinary `git apply`; no force, reject, three-way, restoration, branch change, merge or cherry-pick.
- Existing container/app on Windows localhost port 18080 reused without rebuilding, restarting, initializing, seeding or resetting.

## Exact incremental application scope

1. `app/views/module_payroll/allowances.php`
2. `app/views/module_payroll/cash_advances.php`
3. `app/views/module_payroll/commissions.php`
4. `app/views/module_payroll/compensations.php`
5. `app/views/module_payroll/deductions.php`
6. `app/views/module_payroll/incentives.php`
7. `public/assets/css/style.css`

This task-specific verification handoff is the only additional project file created. The earlier audit handoff remains byte-identical and describes the pre-patch Items UI; use this document for the current integration evidence.

The existing uncommitted navigation edits in the three Payroll Runs views and shared sidebar remain byte-identical, as does `docs/work-items/md-payroll-audit-navigation.md`. All 176 captured protected file hashes match. The entire pre-patch integrated stylesheet remains an exact byte prefix. No HRIS, backend, route, permission, database, dependency or dashboard file changed.

## User-visible result / acceptance contract

- Six pages use `Payroll · Items`, one page H1 and a page-specific subtitle, with contextual primary Add actions.
- Header/search structure compared in the actual browser with Leave Management: context/title/description, tabs where applicable, labeled Search, Search/Clear and compact current-order control. This matches the requested format rather than replacing Leave Management.
- Grouped record/type tables retain original data, supported sorting, search/Clear and paging contracts. Existing employee/type identity, exact monetary source values and raw form values remain available; no calculation or units policy changed.
- Named Edit/Delete actions, contextual dialogs, labels, cancellation, focus return/containment and unsaved-change guards supplied by the patch work in the tested real app.

## Verification actually executed

All project/Git/static checks ran through Ubuntu from the exact project directory. Browser checks used the existing Windows-reachable app and documented fictional demo account. A stale login session was renewed normally; no credentials or session tokens are stored in evidence.

| Check | Result | Method / scope |
| --- | --- | --- |
| Preflight / `git apply --check` | PASS | Correct branch/HEAD; seven target files had no newer local edits; checked before applying. |
| Patch/path scope | PASS | Exactly the six views and stylesheet listed above. |
| Protected source / local work | PASS | 176 fingerprints unchanged, including finished HRIS, backend/routes, prior navigation and prior audit. |
| Integrated CSS | PASS | Complete old stylesheet is an exact prefix; no whole-file replacement from an older branch. |
| PHP syntax | PASS | All six applied views, existing Ubuntu PHP 8.3.6. |
| JavaScript syntax | PASS | Six scripts with existing Node `--check`; PHP JSON interpolation neutralized for syntax parsing only. |
| Complete page configuration / APIs | PASS | Native PHP extraction confirms all six original `pageConfig` arrays are identical: URLs, field definitions, original sort contracts, validation/default metadata. |
| Named form controls | PASS | Native before/after renders without app/database bootstrap: 134 controls total, metadata and values identical (28/15/24/15/26/26 by page). |
| Six actual pages | PASS | All six authenticated pages load, populate, expose named row actions and have one H1 and correct context/subtitle. |
| Leave Management format | PASS | Live desktop and narrow layouts compared; header hierarchy and compact labeled Search/Clear/current-order structure checked. HRIS source preserved. |
| Record search / Clear | PASS | No-match search and exact restored default table contents on all six pages. |
| Type tabs / search / Clear | PASS | All four Type tabs populate; contextual Add Type replaces Add Record; no-match and Clear restore original rows. |
| All supported record sorts | PASS | 102 key/direction cases via actual UI. Sort inventory exactly matches existing model allowlists, including Employee Number and applicable Type Code. |
| All supported type sorts | PASS | 42 key/direction cases via actual UI on the four Type tabs; model allowlists match exactly. |
| Amounts / raw record form values | PASS | Nine displayed record rows match existing GET source amounts; six Edit forms match source raw values for all 35 business fields. No Save submitted. |
| Raw Type form values | PASS | Four Edit Type forms match source raw values for 17 business fields. No Save submitted. |
| Add/Edit/Delete dialogs | PASS | Record dialogs on all six pages and Type dialogs on all four tabs open and cancel. Delete identity includes the employee or actual type name/code. |
| Cancellation focus | PASS | All ten delete dialogs focus Cancel initially and restore the clicked trigger after cancellation. |
| Keyboard / unsaved changes | PASS | Representative Delete Tab/Shift+Tab wrap; Compensation form-only edit triggers Stay/Discard, Stay retains the typed value, Discard closes/returns focus. No update request. |
| Keyboard table scroll | PASS | At 375px, ArrowRight on the labeled table region moved its own horizontal scroll from 0 to 40px. |
| Desktop/tablet/mobile | PASS | Six record views and four Type tabs at 1440x1000, 768x1024, 375x812 and 320x667: 40 cases, no root overflow, named search controls and keyboard-focusable local scroll regions. |
| Short mobile dialog footers | PASS | All ten Add dialogs at 320x667 retain their footer within the viewport; no root overflow. |
| Completed HRIS regression | PASS | Nine non-dashboard pages at 1440px and 375px: 18 cases; headings/navigation/data present and no root overflow. Employee Position, four expanded Office borders and no-wrap Employment Type codes retained. |
| Existing Payroll navigation | PASS | Runs list, run detail and employee detail keep their Payroll navbar; sidebar has no reintroduced Runs/Import duplicates. |
| Browser diagnostics | PASS | No observed warnings/errors in the final browser session. |
| Pagination context | PASS limited | All ten record/type views correctly show the real count and Page 1 of 1. Available seeded data fits one page. |
| Multi-page paging | NOT RUN | No real second page exists; no database fixtures were added just to create one. |
| Financial CRUD / import / processing | NOT RUN | No create/update/delete/import/process or other financial mutation request submitted; no database behavior changed. |
| Pending/uncertain mutation outcomes | NOT RUN live | Present in the supplied UI code, but real mutation paths/fault injection are outside this read-only verification. Do not substitute cloud simulations for local persistence acceptance. |
| Real AT/devices/other browsers, native text zoom, full print/PDF | NOT RUN | No WCAG, physical-device, financial persistence or print pagination claim. |
| Git publication | NOT RUN / prohibited | Branch/HEAD/index preserved; all new changes remain unstaged and uncommitted; no PR merged or changed. |

The test-side DOM evaluator initially rejected a global `parseFloat` helper during an Office border check. The expression was corrected, then the interrupted checks completed. This was a verification-script compatibility issue, not an application error or source change.

## Remaining boundaries

- Existing Payroll date/identity/lifecycle/calculation findings in the earlier audit remain separate; this Items-only patch does not repair them.
- Permissions/access remain unchanged and deferred to the owner's senior.
- Both home/dashboard pages remain deferred.
- Application scope is exactly the supplied patch. No in-scope repair beyond its contents was necessary.

## Inspectable evidence

- Ubuntu temporary directory: `/tmp/payroll-items-stage2-20261027-getgrnt_` (input handoff/patch, original seven files, original status/index, protected hashes, extracted contracts).
- Sanitized local browser results: `C:/Users/loueg/.codex/visualizations/2026/10/27/01a17bde-13e2-7bc3-99b4-c6b8d2508889/payroll-items-stage2-local-results.json`.
- In that same local evidence directory: `payroll-items-stage2-desktop.png`, `payroll-items-stage2-mobile.png`, `payroll-items-stage2-leaves-reference.png`.
- Raw API rows and session data were kept only in browser-test memory, not copied into the report or saved results.
- Temporary viewport override reset and browser returned to Allowances.

## Next concrete action

Owner review of the applied local Payroll Items UI and this evidence. Preserve all current work. Do not stage, commit, push, switch branches, merge drafts, modify backend/records or resume deferred dashboards without the owner's next instruction.
