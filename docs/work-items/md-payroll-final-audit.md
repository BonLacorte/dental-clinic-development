# Payroll final non-dashboard audit — 2026-10-27

## Result and scope

The six Payroll Items pages pass the scoped UI regression checks. Payroll Runs, employee payroll and Attendance Import received the narrow view fixes below and passed their targeted checks. **Full functional Payroll acceptance remains open** because existing route, processing lifecycle and pay-rule gaps need a separate backend/business decision and isolated write tests.

- Branch: `review/md-hris-manual-refinements`; HEAD: `52929377eabc8101460141b108062d2b0154b905`.
- All changes remain uncommitted. No branch switch, staging, commit, push, PR merge, dependency install, configuration change or container restart in this audit.
- Existing uncommitted HRIS, Items styling/forms and navigation changes preserved. Permissions/access and both dashboards remain deferred.
- No controllers, models, routes, database/schema, calculations or persisted lifecycle behavior changed. No business records created, updated, imported, processed, finalized, cancelled, reset or deleted.
- Actual browser uses Windows `http://127.0.0.1:18080`; project/Git checks run in Ubuntu at the exact project directory. Windows app/database containers were running (database healthy), HTTP returned 200. Ubuntu Docker/local port connectivity is a separate previously recorded environment limitation; it was not used as the browser test endpoint.

## Correction: February is already correct

**PAY-01 from the earlier audit is RETRACTED.** The actual controller assigns February month-end back to `$requestedPayDate` before computing the cutoff. The earlier isolated reproduction omitted that reassignment; it was a test error, not an application defect. The actual controller and actual JavaScript preview agree for 48 valid cases: all months of 2026 and leap-year 2028, both periods. February 2026 period 2 is February 15–27, pay February 28. No cutoff/formula repair is needed or applied.

## Fixes completed in this audit

Application edits are limited to `app/views/module_payroll/{payroll_employee_view,payroll_run_index,payroll_run_view,importView}.php`, on top of their prior navigation changes.

- Employee details and payslip now read the model's existing `firstname/middlename/lastname` keys, retaining compatibility with underscored keys. Verified all four fictional employees; names and rates agree with run data. No identity/model/database changes.
- Import explains the first-300-row display cap and full-preview validation. Invalid previews disable Import and explain how to correct them.
- Import retains confirmed actual counts, prevents duplicate current-page submissions, treats missing counts/network/server uncertainty truthfully, and offers deliberate GET navigation for another upload. Removed the automatic document reload that could replay a preview POST. Existing endpoint, FormData and CSRF payload preserved.
- Run processing feedback now distinguishes confirmed processed/failed counts, partial failures, definitive rejection and uncertain outcomes. It guards duplicate pending submissions and refreshes stored data with truthful feedback. This does **not** repair the existing backend lifecycle or computation gaps.
- Reset appears only for DRAFT, matching the current server eligibility; existing CANCELLED Delete remains. No server policy changed.
- Added page H1s, filter names, modal names, table captions/column scope and labelled keyboard-scroll regions. Visual heading sizes retained.

## Verification actually run

| Status | Check | Evidence / limits |
| --- | --- | --- |
| PASS | PHP syntax | All 34 Payroll controller/model/view files lint successfully in Ubuntu. |
| PASS | JavaScript syntax | Extracted scripts from the four edited views pass Node syntax checks. No dependency installation/build step. |
| PASS | Actual-source dates | Actual controller called with an in-memory model spy, plus actual preview JS: 48 valid outputs agree. No PDO, app bootstrap or database writes. |
| PASS | Callback safeguards | 15 simulated scenarios execute extracted actual view code: cancellation, invalid/duplicate guards, explicit rejection, partial results, missing counts, 422, network/500 uncertainty, retained counts and no replay. These are simulations, not real imports/processing. |
| PASS | Render contracts | Before/after fixtures preserve named form controls (0 employee, 3 run-detail, 6 run-index, 3 import). Payslip subtree is unchanged except the intended corrected identity. DRAFT/CANCELLED visibility checked. 301-row fixture with a hidden invalid last row is explained and blocked. |
| PASS | Fresh responsive browser checks | 28 cases at 1440×1000 and 375×812: six Items records, four Types tabs, Runs list/detail, employee detail and Import. Page headings/navigation present; no root horizontal overflow or visible error alerts in these cases. |
| PASS | Fresh search/filter checks | Six Items no-match/Clear cycles restore records; Runs status/month empty and restore; employee name/status no-match states and restore. |
| PASS | Fresh employee/preview checks | Four employee and payslip identities/rates; Attendance and Payslip tab navigation; live February create preview cancelled; two-row fictional CSV preview with one invalid date disables Import. Preview only; no Create/Import confirmed. |
| PASS | Protected prior work | Snapshot of 185 pre-existing files: 180 outside the five-file current edit/correction allowlist unchanged; staged index identical. The five allowed changes are the four views and historical audit correction. New report is additional documentation. |
| PASS | Diff checks | `git diff --check` passes; branch and HEAD unchanged. |
| FAIL — existing gaps | Route target audit | 81 registered targets: absent `PayrollController` for GET `/payroll/create`, absent `PayrollRunController::finalize`. No registered Run Edit/Print GET routes; earlier authenticated browser checks returned 404. No mutation action invoked. |
| NOT RUN | Persistence/payroll acceptance | Real create/edit/delete/import/process/finalize/cancel/reset, rounding/policy acceptance, transaction recovery, multi-page data beyond available seed records, actual printer output, full assistive-technology/other-browser testing. |

Earlier Items/form verification remains recorded in `md-payroll-items-stage2-local-verification.md` and `md-payroll-hris-form-format.md`, including 144 live sort transitions and modal/form cancellation checks. Those source files are unchanged by this audit; those earlier counts are not represented as newly rerun here.

## Unresolved findings and next decision

| Priority | Finding | Evidence / disposition |
| --- | --- | --- |
| P1 | Run Edit/Print/Finalize are incomplete | Missing GET routes and controller method above. Decide supported behavior before implementing or deliberately disabling those actions. No silent feature removal or route implementation in this audit. |
| P1 | Processing completion/lifecycle and partial failure policy | `PayrollRunModel::processPayrollRun()` has `markPayrollRunProcessed()` commented out; individual failures are counted and outer transaction still commits. Source-only finding; no processing executed. Feedback fixed, but status transitions, transaction boundaries and retry policy require a separately approved implementation/test scope. |
| P1 business review | Pay-item integration and overtime policy | `processPayrollRunEmployee()` invokes basic/holiday/overtime/allowance/lateness handling; configured commission, incentive, cash-advance/other deductions are not all connected. Overtime uses `hours * 100`. Confirm intended pay policy with senior; do not infer formulas. |
| P2 | Server month/period validation | Actual controller with an in-memory persistence spy accepts period 3, `2026-00`, and a month with trailing garbage. This shows acceptance at the controller-to-model boundary, not proof of a successful real DB insert. A strict YYYY-MM / period-1-or-2 patch is prepared but **NOT APPLIED**, deferred for senior review by the owner’s explicit decision on 2026-10-27; backend behavior remains unchanged. Proposal preserves all 48 valid outputs and rejects five malformed inputs with 422 before spy persistence. |
| P2 | Cancellation audit details | Model assignments for cancellation reason/actor/time are commented despite UI reason input. Persistence fix requires backend approval and isolated write tests. |
| P2 | Payslip company identity | Live payslip displays fallback `Company Name`; source expects missing run company data. Confirm the correct company source/name rather than inventing a legal entity. |

Historical audit PAY-02 is fixed; PAY-04 feedback is fixed but lifecycle remains open; PAY-10/11/12/13 are fixed at view level. PAY-06/07/08/09 Items presentation/interaction work is covered by the prior patch handoffs, with the remaining Runs/Import semantics added here. PAY-01 is retracted. The earlier navigation audit describes its original baseline; this report is the current disposition.

## Reviewable proposal and evidence

- Local Windows folder: `C:/Users/loueg/Documents/ChatGPT/montero-dental-local/audit-evidence/payroll-final-20261027`.
- Proposal: `payroll-input-validation-proposal.patch` (not applied to project). Source tests/results and callback harness are in that folder. Controller fixtures require the project working directory; the proposed-controller fixture is an isolated copy.
- Browser screenshots: `C:/Users/loueg/.codex/visualizations/2026/10/27/01a17bde-13e2-7bc3-99b4-c6b8d2508889/payroll-final-import-validation.png` and `payroll-final-employee-payslip.png`.
- Temporary source snapshot: `/tmp/payroll-final-audit-20261027-2moh8d08`.
- Owner decision (2026-10-27): keep backend unchanged; record malformed month/period validation for senior review. The tested proposal remains unapplied.
- Next concrete step: senior review of validation, Edit/Print/Finalize, lifecycle and pay rules before separately authorizing backend repairs and isolated acceptance tests. Dashboard work and access policy remain outside this task.
