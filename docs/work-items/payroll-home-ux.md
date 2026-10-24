# Payroll Home — implementation and transfer handoff

## Identity and review status

- Task: `MD-PAY-HOME-UX-001-BUILD`, approved brief v2.0, 24 October 2026 (Asia/Manila).
- Status: `READY_FOR_REVIEW` for the approved unavailable-data dashboard; authenticated acceptance remains blocked and live data requires a backend decision.
- Repository: `BonLacorte/dental-clinic-development`, an independent development copy.
- Baseline: `b5526bb4a7999bbd3c253fcdda1c8419a171314d` (latest accepted `main`, verified through the GitHub connector and a successful `git fetch origin` / `git ls-remote`).
- Feature branch: `codex/md-pay-home-ux-001-build`.
- Cloud checkout: `/workspace/dental-clinic-development`. No WSL worktree was created or accessed.
- Application commit: `31815fec3556bd3f4fb817feef9825c76aa5fbd4` — `feat(payroll): establish accessible Payroll Overview dashboard`.
- This handoff is a separate documentation commit. Resolve its final SHA with `git rev-parse HEAD`; publication details and the draft PR URL are in the completion report and containing PR.
- The explicit user brief authorizes branch creation, narrowly scoped presentation PHP, verification, commits, push and one draft PR. It supersedes the generic workflow's routine approval pauses. Merge, deployment and production access remain outside scope.

## Goal and acceptance criteria

Replace the Payroll Home placeholder with a useful Pearl & Teal operational overview while preserving payroll confidentiality and existing server contracts.

- [x] Replace the placeholder with the exact approved title and description.
- [x] Present four compact summary cards without invented counts or hardcoded zero.
- [x] Provide Recent Payroll Runs and Needs Attention with honest unavailable-data messages.
- [x] Add all three existing Quick Access destinations using the established navigation predicate.
- [x] Preserve shared shell/navigation, backend, financial, import and print sources.
- [x] Verify scoped CSS, responsive reflow, keyboard operation and permission presentation with fictional fixtures.
- [ ] Enable live metrics/records only after backend authorization and data-scope approval.
- [ ] Complete real authenticated application and assistive-technology acceptance before any separately approved transfer.

## Implementation results and exact files

| Deliverable | Result | Actual behavior |
| --- | --- | --- |
| Payroll Overview header | IMPLEMENTED | One `h1`, approved description, original shared includes and Payroll navigation. Controller browser title remains a separate finding. |
| Payroll Summary | PARTIALLY IMPLEMENTED | Four compact definition-list cards: Total Payroll Runs, Draft Runs, Processed Runs, Finalized Runs. Each says Unavailable; no data retrieval or fabricated KPI value. |
| Recent Payroll Runs | PARTIALLY IMPLEMENTED | Five-column semantic table foundation, caption, column scopes, neutral unavailable message, local keyboard scrolling and authorized View All Payroll Runs link. No fake rows or record actions. |
| Needs Attention | PARTIALLY IMPLEMENTED | Neutral follow-up message and authorized Review Payroll Runs link. No issue count, approval/overdue claim or inferred Draft work queue. |
| Quick Access | IMPLEMENTED | Payroll Items, Payroll Runs and Attendance Import, exact approved URLs/descriptions, icons and visible navigation affordances. |
| Responsive presentation | IMPLEMENTED | Four/two/one-column summary, stacked tablet/mobile panels and shortcuts, wrapping text, local table overflow. |
| Accessibility presentation | IMPLEMENTED | Heading hierarchy, labeled sections/navigation/table region, static summary status message, decorative icons hidden from AT, keyboard targets/focus and text contrast. Real AT acceptance remains NOT RUN. |
| Live data and refresh | BLOCKED | `DEFERRED_BACKEND_APPROVAL`; no dashboard requests, loading simulation, automatic polling, Refresh button or browser storage. |

Only these files change:

| File | Purpose and functional scope |
| --- | --- |
| [app/views/module_payroll/payroll_index.php](../../app/views/module_payroll/payroll_index.php) | Replace placeholder HTML with the approved five-section dashboard. Keep the three original shared includes. Add exactly three presentation conditions using the existing `$navPayroll` value: recent navigation, attention navigation and Quick Access. No independent permission helper, query, calculation or JavaScript. All new link destinations/text are static; existing dynamic shared account/navigation output remains escaped by unchanged shared partials. |
| [public/assets/css/style.css](../../public/assets/css/style.css) | Append 300 lines of screen-only rules, all selectors rooted at `.payroll-home-page`. Reuse existing `--montero-*` tokens, card radii and spacing; use tabular numerals, restrained borders, wrapping, 44px link height, 3px separated focus outlines and table-local overflow. No new font, chart, dependency, animation or global rule. The full baseline stylesheet is a byte-identical prefix. |
| [docs/work-items/payroll-home-ux.md](payroll-home-ux.md) | This task-specific handoff, evidence, senior findings and transfer instructions. No application behavior. |

## Data authorization gate

**Decision: `DEFERRED_BACKEND_APPROVAL`.** Do not enable API requests from Home until the intended audience, complete response and tenant/branch visibility contract are approved and enforced server-side.

Verified current source:

1. `routes/web.php:27,51–53`: Home requires Auth, active Tenant, PAYROLL module and `payroll.view`. `PermissionMiddleware` uses the session permission map with the existing Super Admin bypass.
2. `routes/payroll-routes.php:4,104–107`: Runs page, list API and details use Auth, Tenant and PAYROLL module only. They do not add `PermissionMiddleware` or `BranchMiddleware`. Thus the API is explicitly less restricted than Home; a successful request would not settle the intended dashboard policy.
3. `app/views/partials/admin-header.php:55–68` and `module_payroll/partials/payroll_nav.php:19–34`: `$navPayroll` means authenticated user with active tenant and PAYROLL subscription, or the existing module bypass for Super Admin within that tenant context. It controls all three existing destinations. Home additionally uses `$navPayrollHome`. New shortcuts exactly reuse `$navPayroll`; they do not grant server access or treat link visibility as proof of data authority.
4. `PayrollRunController.php:264–312`: session tenant ID, `page` clamped to at least 1, fixed page size 10, optional `status` and `payroll_month`. Response: `success`, `data`, `currentPage`, `totalPages`, `totalRecords`.
5. `PayrollRunModel.php:1590–1703,1706–1762`: list and count queries constrain tenant ID. Join requires matching employee/run tenant. Neither query constrains assigned/active branch; no supported `branch_id` input is established. Status is uppercased/trimmed by controller and compared to stored status by the model; the backend does not whitelist status text. Month uses the existing `YYYY-MM` shape check and pay-date range.
6. List ordering is `pr.pay_date DESC, pr.id DESC`, not creation time. Fields include run identity, pay date, employee count and stored status **plus** tenant/run metadata and `total_gross_pay`, `total_deductions`, `total_net_pay`. Ignoring financial fields in the DOM would still download them to Home's browser audience.

Tenant scoping is established in these list/count queries. A supported branch-scoped Home audience authorized to receive the complete financial response is not established. Frontend predicates cannot supply that missing server contract. This implementation sends no list requests at all.

Future separately approved integration must preserve page size/order, use four page-1 GETs (unfiltered, `DRAFT`, `PROCESSED`, `FINALIZED`), take each count from `totalRecords`, reuse the unfiltered page for at most five recent rows, and render only approved escaped fields. Do not fetch all pages, infer counts from row length, pass tenant/branch IDs, equate PROCESSING with PROCESSED, or infer transitions from processing feedback. Add genuine loading/empty/unauthorized/error/partial-failure and manual refresh behavior only with that approved integration.

`DEFERRED_FRONTEND_AUTHORIZATION`: none for the implemented navigation/presentation. New executable view conditions were explicitly authorized and reuse established predicates. Live retrieval is deferred for backend policy/scope, not lack of frontend permission. Per-run data rendering, draft links and network states remain unimplemented because no permitted data source was enabled.

## Table UX contract and preservation

- Screen/route: Payroll Home, `/payroll`; new Recent Payroll Runs foundation, no existing dataset/table replaced.
- Baseline: accepted development `main` at `b5526bb4a7999bbd3c253fcdda1c8419a171314d`.
- Tasks: find Payroll Runs through navigation; scan data availability; compare counts/records only after approved integration; act through existing destinations only.
- Approved columns in order: Payroll Run, Pay Date, Employees, Status, Action. No data rows; one explicitly unavailable full-width cell. No fabricated Run Code, date, count, business status or View Run target.
- Existing search/filter/sort/page contract: none on the original Home placeholder. No new controls, selection, client sorting or pagination. Runs listing's filters, ten-row pagination, statuses and actions remain unchanged in its protected source.
- Presentation: section title/caption, teal-tinted table header, left-anchored wrapping unavailable message, keyboard-focusable named scroll region, right-aligned Employees heading and tabular numeral styling.
- Protected regions: shared header/nav/footer source and scripts, backend/data routes, all other Payroll/HRIS views, print assets and existing stylesheet prefix. New PHP consists only of the approved three `$navPayroll` conditions and explanatory comments.

| MTD rules / classification | Result | Evidence and limit |
| --- | --- | --- |
| 01–03,05–06 / PRESENTATION | PASS | Semantic table, caption/title, five scoped headers, visual header hierarchy, restrained separators, readable wrapping. Chromium fixture verification. |
| 04,08–13 / NOT APPLICABLE to records | N/A | No real records, dates, counts, statuses or row actions are rendered. Future live implementation must validate all of these. Navigation links are tested separately. |
| 07,14–19,23–25 / NOT APPLICABLE | N/A | No record interaction, filters, sorting, selection, paging or record-card mode on Home. Existing Runs behavior is untouched. |
| 20 / PRESENTATION | PASS | Clear integration-unavailable state; no failed retrieval disguised as empty, no fake zeros or all-clear claim. Live lifecycle states N/A while requests are absent. |
| 21–22 / PRESENTATION | PASS | Five columns remain reachable with local keyboard scrolling; no root overflow at tested widths/enlargement. |
| 26–27 / PRESENTATION | PASS within simulated scope | Keyboard focus/order/Enter, target geometry, sampled text contrast and reduced motion verified. Real AT/native zoom/physical touch not verified. |
| 28–29 / PRESERVATION | PASS at source / CSS-fixture scope | Allowlist, complete diff, baseline CSS prefix and screen-only rules; unchanged adjacent-screen computed styles. No actual financial/print workflow executed. |
| 30 / EVIDENCE | PASS | Distinguish actual source/native PHP render, simulated browser and missing authenticated evidence. No claim of video/AT/WCAG conformance. |

No advanced table behavior is proposed for this change. The skill's two unverified videos remain unverified; no new video research is claimed.

## Verification evidence

Checks ran against fictional template data only. Native PHP rendered the real view/shared partials without bootstrapping the application, controllers, PDO or a session-backed login. Browser requests were all intercepted and fulfilled from local fixture/assets; the unchanged Runs fixture's list request received a synthetic failure, not business data.

| Check | Result | Executed evidence / limitation |
| --- | --- | --- |
| Git preflight and remote accepted baseline | PASS | `git remote -v`, status/index/history/worktrees/branches, connector `branches/main`, branch list and open PR metadata, then `git fetch origin` and `git ls-remote --heads origin ... main`. Initially sandboxed fetch could not reach proxy; network-enabled retry succeeded. Clean starting checkout, only one cloud worktree, no existing Payroll Home branch. |
| PHP syntax/version | PASS | Existing `montero-dental-dev-app:latest` image: PHP **8.3.35, 64-bit**. Offline read-only container, overridden `php` entrypoint; `php -l app/views/module_payroll/payroll_index.php`: no syntax errors. No startup/seed command or DB mounted/run. |
| Native template rendering | PASS | Eleven fixture scenarios rendered without PHP warnings/errors: allowed, no module, no permission, inactive tenant, missing user, missing tenant, Super Admin, hostile account name, baseline Home, HRIS Home, Runs. These are direct view renders, not server authorization tests. |
| JavaScript | PASS / N/A | No page-local JavaScript introduced. `node --check /tmp/payroll-home-checks/browser.cjs` passed for the temporary verification driver. |
| CSS parse/scope | PASS | `css-tree` strict parsing, custom properties parsed; all added selectors rooted at `.payroll-home-page`. Whole baseline CSS byte-identical prefix. Parser was installed only in `/tmp`; no application package/dependency changed. |
| Source preservation | PASS | Exact allowlist, original three shared includes, exactly three `$navPayroll` conditions, fixed href inventory, no view scripts/forms/SQL/storage/API calls, protected directory diff empty; full view/CSS diff reviewed. |
| Chromium fixtures | PASS — SIMULATED | `node /tmp/payroll-home-checks/browser.cjs`, Chromium **154.0.8037.57**, **37 named check groups**. Isolated permission presentation, headings/table, paths, viewport/reflow, keyboard, escaping, motion, contrast and regressions. Initial browser launch was sandbox-blocked; network-capable sandbox permitted local browser sockets. A verification-driver strict-locator error was fixed; final run passed. |
| Responsive widths | PASS — SIMULATED | 1440,1200,1199,992,991,768,767,576,575,375,320px. Document horizontal overflow **0px** at each width. 4/2/1 summary columns; local table scroll on narrow screens; unavailable message stays visible. Desktop sidebar remains 250px. |
| Enlarged/long text | PASS — SIMULATED | 200% CSS root text enlargement and long fictional explanatory messages at 1440/768/375/320px; no root overflow. No long Run Name/status test claimed because no run rows are implemented. |
| Keyboard and focus | PASS — SIMULATED | Table ArrowRight scrolling/all columns reachable, 3px focus outlines, shortcut Tab/Shift+Tab and Enter navigation, mobile sidebar Escape/focus return at 375×667, existing desktop collapse. Tested shortcuts exceed 44×44px; inline links set minimum 44px height. |
| Contrast/motion | PASS — SIMULATED | Ten computed default text/background samples: minimum **4.8277:1** (muted on canvas), all ≥4.5:1. A targeted follow-up checked 12 hover/focus/inline-target measurements: hover title/description/link text ≥4.5:1; focus outline **4.7460:1** against white and **4.4057:1** against canvas, with 3px outline and 3px offset; inline links exceed 44×44px at 1440/375/320px. New shortcuts have no transitions/animations under reduced motion. Not a full WCAG audit. |
| Shared CSS regression | PASS — SIMULATED | All descendant computed geometry and sampled style properties on unchanged HRIS Home and Runs templates equal baseline CSS results. New Home markup receives no appended CSS in print media. No production or real payslip comparison. |
| Dashboard requests/errors | PASS — SIMULATED | No Home XHR/fetch requests, no non-GET requests or page JavaScript errors. All resources/interactions stay intercepted; no real API/security assertion. |
| Git whitespace/staging/diff | PASS | `git diff --check`, staged equivalent, full baseline-to-feature file inventory/diff; only two allowed application files plus this optional handoff. Final publication/clean HEAD are verified in completion report. |
| Live metrics/network states/backend authorization | BLOCKED | Gate above. No requests implemented; no claims about real zero/loading/error/partial failure or refresh. |
| Authenticated PHP application/tenant/branch acceptance | BLOCKED | No approved running authenticated fictional application/database context was established for this task. No database setup or seed run. |
| Real AT/native 200% browser zoom/Firefox/Safari/physical devices | NOT RUN | Required follow-up acceptance; CSS enlargement and Chromium fixtures do not prove these. |
| Business mutations, actual payroll/import/payslip operations | NOT RUN | Explicitly outside authorization. No processing/create/finalize/reset/cancel/delete/import or production access. |

Temporary local evidence (not committed, not portable through Git):

- `/tmp/payroll-home-evidence/browser-results.json` — final named results, sampled measurements and intercepted request metadata.
- `/tmp/payroll-home-evidence/focus-hover-results.json` — 12 targeted hover/focus/inline-target measurements from `/tmp/payroll-home-checks/focus-hover.cjs`.
- `/tmp/payroll-home-checks/browser.cjs`, `render.php`, baseline PHP/CSS and cached public library assets — verification-only harness.
- `/tmp/payroll-home-evidence/payroll-home-1440.png`, `payroll-home-768.png`, `payroll-home-375.png`, `payroll-home-320.png`, `payroll-home-320-text-200.png` — actual simulated Chromium screenshots. Desktop and 320px captures were visually inspected.
- Fictional native HTML renders are in the same temporary evidence directory. No private clinic/financial response, token, database dump or environment value is included.

## FINDINGS_FOR_SENIOR_REVIEW

These source observations were reverified at the accepted baseline. **No backend fix was made for any finding.**

| Finding / exact reference | Verified observation and risk | UI impact / senior decision |
| --- | --- | --- |
| PAY-HOME-G01 — `app/controllers/ModuleController.php:44–50` | `payroll()` still supplies browser title `HRIS Home`; browser/tab context is misleading. | Visible Home heading corrected within approved view only. Correct controller title in a separately approved backend change. No controller edit. |
| PAY-HOME-G02 — `routes/web.php:27,51–53`; `routes/payroll-routes.php:4,104–107`; `PermissionMiddleware.php:44–86`; `ModuleMiddleware.php:19–33` | Home requires `payroll.view`; Runs page/API/details require module/tenant/auth only, with existing Super Admin module semantics. A 200 response does not establish intended financial data authority. | Live integration deferred. Decide/enforce the intended Home and Runs permission matrix and full-response entitlement. Existing navigation predicate preserved, no grant/middleware edit. |
| PAY-HOME-G03 — `app/models/payroll/PayrollRunModel.php:1510–1587`, especially `1573`; `routes/payroll-routes.php:110` | `markPayrollRunProcessed` call and employee-error failure guard remain commented out; processing can commit without changing stored run status. Finalize route is registered without a `finalize()` method in the inspected controller. Status is not proof of computation completion. | No inferred processing results, status counts or alerts. Review stored-status lifecycle/finalization separately before treating Draft as unprocessed or Processed as error-free. No calculation/transition edit. |
| PAY-HOME-G04 — `PayrollRunController.php:276–312`; `PayrollRunModel.php:1653–1668,1691–1696` | API returns ten ordered runs and per-run financial aggregates. One page cannot establish global historical expense totals; display-only omission would not prevent financial download. | No expense totals/charts/downloads. Approve a suitable authorized analytics source and definitions separately. No aggregation/model edit. |
| PAY-HOME-G05 — `PayrollRunController.php:269–300`; `PayrollRunModel.php:1590–1634,1706–1759`; payroll routes group at line 4 | Session tenant filter exists; no assigned/active branch filtering in list/count and no BranchMiddleware there. Tenant-wide employee/financial aggregates may exceed a branch user's intended scope. | No branch analytics and no live retrieval. Decide tenant-wide entitlement versus enforceable branch restrictions consistently for count/list/details. No tenant/branch/query edit. |
| PAY-HOME-G06 — `routes/payroll-routes.php:103–114`; `PayrollRunController.php:264–325`; `PayrollRunModel.php:1550–1579` | Inspected run contract has stored status and per-employee processing/error behavior, not a dedicated cross-run approvals/overdue/errors queue. Draft is not evidence of an approval request, overdue obligation or defect. | Neutral Needs Attention message; no all-clear assertion or client-derived queue. Define an authorized queue/source and semantics separately if needed. No workflow edit. |
| PAY-HOME-G07 — `app/controllers/payroll/PayrollRunController.php:314–323` | List handler returns raw exception message in the 500 JSON response, which may disclose internals. | No Home requests or internal JSON/error rendering. Review generic public error output and private logging separately. No endpoint/response edit. |

## Preserved contracts and overlapping work

- Shared Payroll/header/sidebar/footer partials and `public/assets/js/main.js` are unchanged. Existing active states, menus, offcanvas breakpoint and shell ownership remain intact.
- No controller/model/helper/service/middleware/core/route/config/dependency/database/script changes. No permission, tenant/branch, CSRF, query, status-transition, calculation, financial-value or business-data mutation.
- Runs endpoint/method/parameters/response, supported displayed statuses (`DRAFT`, `PROCESSING`, `PROCESSED`, `FINALIZED`, `CANCELLED`), financial calculations and existing Runs/Items/Attendance Import sources are unchanged. Home makes no claims about unknown statuses because it renders no business status data.
- Print/payslip markup/assets unchanged. Appended styles are screen-only; Home content itself intentionally changes from the placeholder. Actual payslip output was not run.
- Open PRs #13 (Employees), #14 (Organization), #15 (Attendance), #16 (Payroll Items), #17 (Payroll Runs), #18 (Attendance Import) all include shared CSS changes. File inventories were inspected read-only. No overlapping uncommitted work existed here; no branch was imported, merged or rebased. PR #8 remains separate and untouched.
- Review shared CSS append conflicts when these branches are eventually combined. Keep each page-specific selector block and the latest accepted prefix; do not restore whole historical stylesheets or copy other drafts into this branch.

## Local WSL review and production transfer

After publication, in the developer's actual WSL repository (verify the path and existing worktrees first):

```bash
cd ~/projects/monterodental
git status --short
git worktree list
git fetch origin
# Only if this branch and destination do not already exist locally:
git worktree add -b codex/md-pay-home-ux-001-build \
  ~/projects/monterodental-payroll-home-review \
  origin/codex/md-pay-home-ux-001-build
# If the local feature branch already exists and is not checked out elsewhere:
# git worktree add ~/projects/monterodental-payroll-home-review codex/md-pay-home-ux-001-build
```

Inspect an existing destination instead of overwriting it. Do not switch the original checkout or modify other review worktrees.

For a separately approved production transfer:

1. Review the application diff in commit `31815fec3556bd3f4fb817feef9825c76aa5fbd4` and this handoff. Transfer only the reviewed Home view and page-scoped CSS addition after fresh production-source reconciliation. The documentation is a development handoff, not a production dependency.
2. Confirm PHP 8.3+, existing MVCLite/includes, Bootstrap 5.3.3, Bootstrap Icons 1.11.3, jQuery 3.7.1 and installed Pearl & Teal variables. No new runtime/library/build pipeline is required. Ensure production actually defines `$navPayroll` using equivalent server-backed navigation context; if not, defer integration for owner review rather than inventing a new permission rule.
3. Reconcile all newer shared CSS/navigation changes and verify that existing screen-only token scope loads. Do not replace production's complete stylesheet with this development copy.
4. Obtain senior decisions for G01–G07 and the full-response/branch authorization gate before any live-data follow-up. The current unavailable-data build does not require an API call or database migration.
5. In approved isolated fictional staging, run PHP lint; authenticated Home/link access with module/permission/Super Admin/tenant/branch scenarios; actual CDN/library loading; required viewports, keyboard and native zoom; screen-reader/physical-device checks; adjacent Payroll Items/Runs/Import and read-only print/payslip acceptance. Do not use payroll mutations as presentation tests.
6. Seek separate deployment approval under the team's production workflow. No automatic merge, production access, deployment or transfer occurred. A future rollback should reverse only this Home/CSS addition after checking for later overlapping changes; do not reset databases or overwrite whole files.

## Next concrete action

Senior review of the draft and authenticated acceptance of this unavailable-data foundation. Decide the dashboard audience/full API response/branch-scope policy separately before authorizing live integration. Re-read repository instructions and verify branch/status/code before continuing; this handoff grants no additional backend, merge or deployment authority.
