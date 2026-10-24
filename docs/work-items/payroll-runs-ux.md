# Payroll Runs premium UI implementation

## Identity and status

- Task: `MD-PAY-RUNS-UX-001-BUILD`, approved v2 build, 24 October 2026 (Manila).
- Branch: `codex/md-pay-runs-ux-001-build`.
- Checkout: `/workspace/dental-clinic-development` (cloud; no WSL worktree created).
- Accepted baseline: `b5526bb4a7999bbd3c253fcdda1c8419a171314d`; verified live `main` through GitHub and `git fetch origin`/`git ls-remote`.
- Status: `READY_FOR_REVIEW` as a development draft. Frontend build complete; authenticated runtime/security acceptance remains blocked. Overall acceptance: `PARTIALLY_IMPLEMENTED`.
- Authorization: user explicitly authorized the four application files, this handoff, page-local presentation interactions, commits, push and one development draft PR. This supersedes older styling-only/Git-write limitations for this task. No merge, production transfer or financial mutation is authorized.
- Existing independent drafts #8/#13/#14/#15/#16 were inspected as metadata only; no application changes imported. Initial checkout/index clean; no other worktrees owned here; remote Payroll Runs branch absent.

## Goal, scope and acceptance

Find the correct run and pay period; compare original financial values and employee results; distinguish stored status, failed computation and request failure; reach existing actions and Payroll Details/Attendance/Payslip across screen sizes. Preserve PHP/Bootstrap/jQuery architecture, requests, CSRF, permission/tenant predicates, financial calculations and print output.

Exact application allowlist:

- `app/views/module_payroll/payroll_run_index.php`
- `app/views/module_payroll/payroll_run_view.php`
- `app/views/module_payroll/payroll_employee_view.php`
- `public/assets/css/style.css`

The only documentation change is this file. Backend/models/routes/middleware/services/helpers/core/database, other modules, shared partials/JS, configuration, dependencies and existing print-specific source remain protected.

## Table UX contract

| Surface | Existing contract | Approved presentation/interactions |
| --- | --- | --- |
| `/payroll/runs` | Run code/name, period, cutoff from/to, pay date, employee count, gross/deductions/net, stored status, existing actions; GET `page/status/payroll_month`; ten-row server pagination, pay-date/id descending | Six grouped columns; all values retained; Pay Month means pay-date month; named direct actions; no new sort/search; truthful loading/error/empty/matching states; current page context; stale-read guard |
| Create dialog | POST `/payroll/runs/create`; `payroll_month/period_no/csrf_token`; original first/second options, defaults and `getSemiMonthlyDates` | Period and preview sections, native required validation, local feedback, duplicate/pending guard, confirmed-success check and uncertain-outcome guidance; no actual submission during verification |
| `/payroll/runs/view/{id}` | Original run overview; original four aggregation expressions; nine employee columns; loaded-run-only name/number/status filtering; original status/action predicates | Headings, overview/summary cards, prominent Net Pay, explicit details action, filter counts and full-run total context, local scroll, pending context and accurate partial-error communication |
| `/payroll/runs/{runId}/employee/{runEmployeeId}` | Original identity, salary/cutoff/pay date, earnings/deductions loops, attendance arrays/summary arithmetic, tabs, payslip/print script | Screen hierarchy, accurate separate Earnings/Gross Pay labels, original supplied gross shown separately, named tabs/regions, financial alignment, attendance units. Payslip subtree, inline style and print function preserved byte-for-byte |

MTD-01–15/18/20–23/25–30 apply as presentation/preservation checks. MTD-16/17 sorting and MTD-19 selection are N/A: these screens expose neither. MTD-24 mobile cards are N/A and explicitly excluded. The approved six-column grouping, labels and narrow JS improvements override the skills' generic styling-only restrictions; no backend authority is inferred.

## Implementation results

| Milestone | Result | Delivered behavior and limitations |
| --- | --- | --- |
| Payroll Runs Listing | IMPLEMENTED | Six grouped columns retain run code/name/period, exact cutoff/pay dates, API employee count, gross/deductions/net and stored status. Pay Month follows the verified `pay_date BETWEEN` query. View Run is explicit; existing Edit/Delete/Print conditions and destinations remain. Counts/page context come from the API; local loading, distinct failure/empty/matching states, Refresh and stale-read suppression. |
| Create Payroll Run dialog | IMPLEMENTED | Payroll Period and Period Preview sections; original controls/options/CSRF/defaults and date function; required validation, local server errors, pending field/close protection, duplicate guard and explicit-success check. Uncertain responses keep the dialog open and creation disabled until a subsequent successful list refresh; the administrator must inspect that list before retrying. No automatic retry or submission on field changes. |
| Payroll Run Details | IMPLEMENTED | Run Overview and Financial Summary headings; original four PHP aggregations and all nine granular result columns; prominent Net Pay, labeled loaded-run-only filters, accurate visible counts, full-run total labeling and explicit employee detail actions. Reset/Delete form a separate warning group. Existing operational buttons/visibility and native confirmations remain. Pending guards and response feedback are local; partial employee errors never become an all-success claim or simulated PROCESSED status. |
| Employee Payroll Details | IMPLEMENTED | Clear identity/run context, salary/cutoff cards, separate supplied Earnings and Gross Pay, readable item tables, attendance units, named tabs, keyboard scroll regions and Net Pay emphasis. Original values/loops/summary arithmetic remain; missing attendance is not relabeled a violation. Known identity mapping limitations remain below. |
| Shared/responsive styling | IMPLEMENTED | Additive `.payruns-ui` / `.payruns-dialog` CSS in screen media only; Pearl & Teal surfaces, semantic badges, tabular/right-aligned money, visible focus, 44px action objectives, wrapped filters/pagination and locally scrolling tables. No cards renderer, charts, new framework, dependency or build pipeline. |

Implementation status describes the frontend work. It does not certify existing business capabilities, endpoint authorization or production readiness. Finalize/Edit/Print Register remain inherited unavailable destinations. Native confirmation dismissal and the unavailable Finalize path were preserved; no replacement confirmation framework was introduced.

The listing adds explicit peso symbols and preserves source decimal places beyond two when present; missing values display “Not available” and valid zeros stay zero. Existing PHP monetary formatters remain at their original two-decimal behavior because their financial/print semantics are protected. No new equation, aggregate, precision policy, persisted amount or submitted monetary value was introduced. The employee screen’s Earnings label now describes its actual `$totalEarnings` source; Gross Pay uses the already supplied `$grossPay` separately.

## Preserved contracts

| Contract | Evidence and limits |
| --- | --- |
| Requests | Identical existing URL and GET/POST inventories. Listing remains GET `/payroll/runs/getallruns` with `page/status/payroll_month`; controller page size remains ten and ordering remains pay date/id descending. Create remains POST `/payroll/runs/create` with `payroll_month/period_no/csrf_token`. Load/process/finalize/reset/cancel/delete URLs and payloads remain; no new request fields or unsupported search/sort/branch filters. |
| CSRF/permissions/tenant | Original CSRF inputs and request source occurrences preserved; no route, middleware, session, authorization or tenant/branch query changed. This is source preservation, not proof of adequate baseline security. |
| Lifecycle/actions | Original PHP visibility predicates and loops compare identically. Listing still shows Draft Edit, Delete in every rendered status, Print for Processed/Finalized; the unused cancel-button template remains unused. Detail Reset/Delete still show for Draft/Cancelled. No new transition or optimistic stored-status update. Finalize function is byte-identical to baseline. |
| Financial/employee/attendance | Run aggregation block is byte-identical. Employee PHP blocks/loops are identical except one additional escaped/formatted display of the already supplied gross value. No financial/attendance arithmetic, arrays, model or persisted data changed. |
| Payslip/print | Entire payslip subtree, inline stylesheet (including `@media print`) and print function are byte-identical. Existing `no-print` mechanism excludes added screen content; original header layout classes and print font geometry are retained. Source-fixture print text/DOM/computed styles/geometry, PDF text/page count and screenshot bytes match baseline. Authenticated application print acceptance remains required. |

## Verification evidence

The tooling and fictional fixtures live only under `/tmp/payroll-runs-evidence/`; they are not application dependencies or tracked files. The native runtime was extracted into that directory from Debian packages without installing system packages, changing configuration, starting an application/database server, or running initializers. PHP CLI 8.4.27, mbstring and its required libargon2 were used for syntax and isolated view rendering. Bootstrap 5.3.3, jQuery 3.7.1 and Bootstrap Icons 1.11.3 are the existing versions; temporary asset copies support offline fixtures only.

| Check / actual command | Result | Evidence and limits |
| --- | --- | --- |
| `git fetch origin`; `git ls-remote --heads origin main codex/md-pay-runs-ux-001-build` | PASS | Live main `b5526bb4a7999bbd3c253fcdda1c8419a171314d`; no existing remote feature branch. GitHub metadata also verified main and independent open drafts. Initial shell network call failed inside the restricted command sandbox; the supported command network permission enabled the normal configured proxy. |
| `/tmp/payroll-runs-evidence/php -l app/views/module_payroll/payroll_run_index.php` and equivalent `payroll_run_view.php`, `payroll_employee_view.php` commands | PASS | All three changed views: no syntax errors under native PHP 8.4.27. |
| Extract each `<script>` block, substitute PHP interpolation only in a temporary copy, parse with Node `vm.Script` | PASS | Three JavaScript syntax checks. Original employee print script remains unchanged. |
| `python /tmp/payroll-runs-evidence/audit.py` | PASS | 23 source-preservation checks: date-preview function/default month, URL/method inventories, field/validation metadata, CSRF source, lifecycle conditions/loops, financial aggregations, employee PHP, payslip/print, additive screen CSS and exact allowlist/protected paths. Audit uses the complete accepted-baseline diff, including committed files. |
| `python /tmp/payroll-runs-evidence/prepare.py` | PASS — source fixture | 28 before/after native-PHP view renders, covering all five run states and populated/empty arrays, with fictional records and no warnings. Only shared includes are removed in temporary copies. No application bootstrap, session, controller, PDO, SQL or business endpoint executes. |
| `node /tmp/payroll-runs-evidence/browser.cjs` | PASS — SIMULATED | Final 209 Chromium 154 assertions. Bootstrap/jQuery/icons and rendered PHP views run in a minimal shell fixture. AJAX is replaced by in-memory callbacks; browser transport permits only local fixture/asset GETs. No business POST, form submission, financial mutation or application database operation was performed. |
| Browser viewport/keyboard checks | PASS — SIMULATED | Three screens at 1440/1200/1199/992/991/768/767/576/575/375/320px, 1000px desktop height and 667px narrow height; additional 375×812 checks and 200% CSS text enlargement. No document overflow beyond 1px; columns/actions reachable through local scroll, keyboard scrolling, named controls, modal focus entry/containment/return, reachable 44px footer/action targets and sampled status/muted-text contrast. Screen captures at 1440 and 375px; manual image inspection sampled listing desktop and employee mobile. |
| State/request checks | PASS — SIMULATED | Status/month GET keys, page 2/Refresh/current aria, stale response suppression, loading, API/network failure and retry, filtered/unfiltered empty states; original status/action availability; employee search/Error filter retains full-run totals. Callback-only assertions cover required-field rejection, duplicate/pending guards, explicit create success, server rejection, uncertain create/delete/reset outcomes, and partial process errors. These are JavaScript tests, not actual Create/Process/etc. requests. |
| Browser CSSOM inspection | PASS — SIMULATED | Five added media blocks parse successfully, all screen-scoped and payroll-scoped. Original stylesheet prefix unchanged. |
| `pdfinfo` on `payslip-before.pdf` / `payslip-after.pdf`; `pdftotext -layout` then `diff -u`; `sha256sum payslip-print-before.png payslip-print-after.png` | PASS — SIMULATED | Both PDFs retain two A4 pages (595.92×842.88 pt); extracted text identical. Complete payslip DOM/financial text/computed style/geometry identical. Print screenshots have identical SHA-256 `1d0a90abdfdccc3616b9a7d51e9ff54577fb3061370778b6c621a861a49745b8`. Existing blank-space/two-page behavior was preserved. |
| Full `git diff b5526bb..HEAD` review; `git diff --check`; index and changed-file checks | PASS | Four authorized application files plus this handoff only. No protected paths, application dependency manifests or print-specific declarations changed. |
| Real authenticated PHP/MySQL application, restricted-role/two-tenant/branch read-only tests | BLOCKED | No approved configured/running fictional application/database context was supplied. Native standalone view rendering does not establish routing, authorization, query correctness or persisted totals. |
| Actual Create/Load/Process/Finalize/Reset/Cancel/Delete or other business writes | NOT RUN | Explicitly excluded from verification authorization. No schema/seed/initializer/Compose resource operation or production access occurred. |
| Real screen reader, physical devices, Firefox/Safari, native browser zoom, authenticated cross-module and real print/PDF comparisons | NOT RUN | Requires the approved real demo environment and manual coverage. CSS text enlargement and Chromium fixtures are not full WCAG conformance or physical-device evidence. |

An initial print-position regression was found and corrected using the original layout classes and `no-print` mechanism. Final print geometry and screenshot checks pass. A fixture-only asynchronous navigation race was corrected in the harness; adding the actual icon font also exposed tab naming noise, addressed by hiding decorative icons from assistive technology. No test failure remains in the executed final checks.

Screenshots: `/tmp/payroll-runs-evidence/payroll_run_index-{1440,375}.png`, `payroll_run_view-{1440,375}.png`, `payroll_employee_view-{1440,375}.png`, plus before/after print captures and PDFs. Machine-readable evidence: `source-audit.json`, `browser-results.json`. These are ephemeral local artifacts containing fictional fixture data, not authenticated application screenshots.

## FINDINGS_FOR_SENIOR_REVIEW

All references below describe the unchanged accepted backend source. **No backend fix was made for any finding.** Each remains `DEFERRED_BACKEND_APPROVAL` unless specifically marked otherwise. Frontend preservation is not security endorsement.

| Finding and source | Observed/static behavior | User/business risk and presentation impact | Recommended senior decision / change made |
| --- | --- | --- | --- |
| G01 — Processing transition. [Model:1510](../../app/models/payroll/PayrollRunModel.php#L1510), [Controller:198](../../app/controllers/payroll/PayrollRunController.php#L198) | `markPayrollRunProcessed` call is commented out. Controller returns success after the processing method commits. | Stored run may stay Draft after a completed request; administrators could confuse a toast with a lifecycle transition. | Define authoritative transition and partial-failure policy; add backend tests separately. UI only says request finished/review stored status. **No backend fix.** |
| G02 — Finalize. [Routes:110](../../routes/payroll-routes.php#L110), [Controller](../../app/controllers/payroll/PayrollRunController.php), [View finalizer](../../app/views/module_payroll/payroll_run_view.php) | POST route targets `finalize`, but that method is absent from the active controller. The existing view also calls `Swal.fire`; shared header/footer/main.js do not load SweetAlert. | Existing Processed-only Finalize can fail before or at dispatch; there is no verified read-only finalization capability. | Decide the backend handler/authorization/lifecycle and permitted confirmation dependency separately. Frontend dependency repair is `DEFERRED_FRONTEND_AUTHORIZATION`; original handler byte-for-byte preserved. **No backend fix.** |
| G03 — Edit. [Routes](../../routes/payroll-routes.php), [Listing](../../app/views/module_payroll/payroll_run_index.php) | Draft Edit links to `/payroll/runs/edit/{id}`; no matching registration found in routes. | Existing link remains a failed destination; the new primary View Run remains valid. | Approve a real edit workflow or separately authorize action removal. Destination/condition preserved. **No backend fix.** |
| G04 — Print Register. [Routes](../../routes/payroll-routes.php), [Run view](../../app/views/module_payroll/payroll_run_view.php) | `/payroll/runs/print/{id}` has no registered route. Processed/Finalized links remain. | Cannot claim register printing works. Employee Print Payslip is a separate, unchanged action. | Supply and review register route/content/access policy separately. No substitute workflow. **No backend fix.** |
| G05 — Reset eligibility. [Model:1881](../../app/models/payroll/PayrollRunModel.php#L1881), [Run view](../../app/views/module_payroll/payroll_run_view.php) | View renders Reset for Draft or Cancelled; model only accepts Draft. | Cancelled Reset remains rejected, even with clearer warning treatment. | Align lifecycle/eligibility on the server and authorize corresponding UI changes. Exact visibility preserved. **No backend fix.** |
| G06 — Draft deletion guard. [Model:1796](../../app/models/payroll/PayrollRunModel.php#L1796), [Listing](../../app/views/module_payroll/payroll_run_index.php) | Draft-only model guard is commented out. Listing exposes Delete for all stored statuses; detail page only for Draft/Cancelled. | Computed/finalized data may be deletable under existing access; client visibility is not server enforcement. | Establish server deletion/retention/audit policy before production transfer. Existing predicates remain. **No backend fix.** |
| G07 — Cancellation reason. [Model:1764](../../app/models/payroll/PayrollRunModel.php#L1764), [Controller:327](../../app/controllers/payroll/PayrollRunController.php#L327) | Controller passes reason/user, but UPDATE only persists status/updated_at; reason/by/at lines are commented out. Existing Cancel trigger is commented/unused in these views. | Audit context is not reliably retained. No claim of reason persistence appears in new feedback. | Decide required audit fields and activation policy separately. Existing reason field/request retained. **No backend fix.** |
| G08 — Partial employee errors. [Model:1510](../../app/models/payroll/PayrollRunModel.php#L1510), [Model:715](../../app/models/payroll/PayrollRunModel.php#L715) | Per-employee exceptions mark Error and increment error_count; rollback-on-errors is commented out; controller still returns top-level success. | A partially computed run can appear successful if only the top-level response is read. | Define transactional/retry/error policy and test separately. UI warning checks existing error_count and retains Error rows without an overall-completion percentage. **No backend fix.** |
| G09 — Server permissions. [Routes:4](../../routes/payroll-routes.php#L4), [ModuleMiddleware](../../app/middleware/ModuleMiddleware.php) | Runs use Auth/Tenant/PAYROLL module group, with no per-action PermissionMiddleware on the run routes. Legacy `/payroll/create` permission does not secure `/payroll/runs/create`. Existing mutation handlers validate CSRF. | Subscription/module membership may grant wider financial operations than intended. No new UI condition can certify endpoint protection. | Approve and enforce view/create/load/process/finalize/reset/cancel/delete action matrix server-side; test roles. **No backend fix or permission change.** |
| G10 — Tenant/branch scope. [Model:158](../../app/models/payroll/PayrollRunModel.php#L158), [Model:175](../../app/models/payroll/PayrollRunModel.php#L175), [Model:1590](../../app/models/payroll/PayrollRunModel.php#L1590), [Model:1970](../../app/models/payroll/PayrollRunModel.php#L1970) | Run lookup/list use tenant predicates and list aggregate join includes tenant. Detail employee lookup includes tenant; run employee list joins only IDs/run_id; child reads/deletes use parent IDs/run_id. These routes/queries do not apply assigned-branch intersections. | Parent ownership helps, but tenant integrity of every relationship and financial branch policy remain unverified. Tenant-wide data may exceed intended branch access. | Review relationships/FKs and explicit branch/tenant constraints for every query and operation; run isolated multi-tenant/branch tests. **No scope/query/backend fix.** |
| G11 — Calculation and audit limitations. [Model:962](../../app/models/payroll/PayrollRunModel.php#L962), [Model:1381](../../app/models/payroll/PayrollRunModel.php#L1381), [Model:762](../../app/models/payroll/PayrollRunModel.php#L762) | Overtime amount uses hours×100 after calculating provisional rates; other adjustment calls remain comments. Recalculation distinguishes gross from additional earnings. | Modern presentation could imply financial completeness that current calculations have not earned. Gross and Earnings must remain distinct; no “paid/approved/completed” state is added. | Senior payroll owner must validate supported components, rates, audit trail and precision independently. Original calculations and PHP formatting unchanged. **No backend fix.** |
| G12 — Employee identity source. [Model:1970](../../app/models/payroll/PayrollRunModel.php#L1970), [Employee helper](../../app/views/module_payroll/payroll_employee_view.php) | Detail query supplies firstname/middlename/lastname; helper reads first_name/middle_name/last_name. Existing employee fallback can appear although names exist. Payslip/company fallback content is inherited. | Some actual identity text may still fall back to Employee #; fictional fixtures cannot certify real mapping. | Resolve aliases/identity and print requirements in a separately approved correctness change. This build preserves source identity helpers and complete payslip content. **No backend fix.** |

## Git publication and review

Code commits:

- `7b09b6f84b82f309426cc0c460190df051b32dec` — scoped screen styling.
- `768bb38db95dd11ef2626657b4213c17c1fc884a` — listing/dialog/run/employee presentation and approved local feedback.

The final documentation commit contains this consolidated handoff. Its final SHA, remote-head verification and draft PR URL are recorded in the publication response and PR metadata; use `git rev-parse HEAD` in the review checkout. Publication targets only `codex/md-pay-runs-ux-001-build` → development `main`. No merge, auto-merge or deployment is authorized.

## WSL review worktree

The cloud checkout is not the developer’s WSL filesystem. These commands create a separate local review checkout only after confirming the preferred path and local branch are absent and existing worktrees are preserved:

```bash
cd ~/projects/monterodental
git status --short
git worktree list
git fetch origin
git branch --list codex/md-pay-runs-ux-001-build
test ! -e ~/projects/monterodental-payroll-runs-review
git worktree add -b codex/md-pay-runs-ux-001-build \
  ~/projects/monterodental-payroll-runs-review \
  origin/codex/md-pay-runs-ux-001-build
```

If the branch/path already exists, inspect its owner/status and reuse safely instead of running `worktree add -b`. Do not switch/reset/stash/overwrite the original or Employees/Organization/Attendance/Payroll Items checkouts.

## Production-transfer handoff and next action

1. In an approved fictional local environment, run native PHP lint and authenticated read-only acceptance for listing/filters/pages/dialog without submitting, run totals/employee filters, detail tabs/attendance and payslip printing. Test long/empty/error reads, keyboard/focus, all required widths, 768×1024, native 200% zoom, real assistive technology and relevant browsers. Do not seed/initialize shared databases or execute business mutations for this review.
2. Senior/backend/security review must resolve or explicitly accept G01–G12 before any production consideration. No frontend-only restriction counts as server security. Mutating integration/persistence tests require separate disposable-fixture approval.
3. For a separately approved transfer, obtain a fresh authorized production source snapshot and perform three-way reconciliation against `b5526bb` and these commits. Development main is not production. Transfer only compatible reviewed hunks/files; never copy entire historical files over a collaborator’s live changes.
4. The stylesheet change is additive and uniquely scoped, but the shared file overlaps independent Employees/Organization/Attendance/Payroll Items drafts. Reconcile those appended blocks and verify selector/order behavior deliberately; no automatic merge/cherry-pick is included.
5. Repeat real before/after payslip PDF/text/page-break/geometry checks with the actual shared header/footer/fonts, clinic identity and authorized fictional records. Preserve current page size/output; do not carry the screen-only gross context or scroll hints into print. Retest financial values and status/action/CSRF/role/tenant/branch contracts.
6. Review the complete baseline-to-final-HEAD five-file diff and approve any later merge/transfer/release separately. A scoped presentation rollback should reverse only these reviewed hunks while accounting for later overlapping work; no DB reset/restore, force-push or whole-file overwrite is part of rollback.

**First concrete next action:** senior review the draft PR’s five-file diff and the findings above, then arrange authenticated read-only acceptance in the existing approved fictional development environment. No additional routine implementation approval is needed; production transfer and financial writes remain outside this task.
