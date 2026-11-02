# Payroll audit and navigation alignment

## Identity and authorization

- Branch: `review/md-hris-manual-refinements`.
- Accepted HRIS/source baseline: `52929377eabc8101460141b108062d2b0154b905`.
- Reviewed: 2026-10-27, Asia/Manila.
- Status: READY_FOR_REVIEW - requested navigation implemented; initial source/runtime audit compiled. Wider improvements are proposals, not implemented.
- Owner requested independent source/runtime findings for non-dashboard Payroll pages, compilation before wider improvements, removal of the Payroll Runs and Attendance Import sidebar shortcuts, and the shared Payroll navbar on Payroll Runs.
- Permission/access behavior stays unchanged and deferred to the owner's senior. Both module dashboards remain excluded. No business-record writes, import processing, payroll calculations, dependency installation, Git writes or publication are authorized by this increment.

## Plan and specification

Audit the existing running app first, compare with accepted HRIS conventions and Payroll draft references, implement only the two navigation requests, verify preserved contracts, and compile evidence and proposed follow-up batches.

Application allowlist:

1. `app/views/partials/admin-sidebar.php`: remove exactly the two requested shortcuts; retain existing Payroll entry/fallback and all visibility predicates.
2. `app/views/module_payroll/payroll_run_index.php`: enable the existing shared Payroll navbar.
3. `app/views/module_payroll/payroll_run_view.php`: enable the same navbar for run review.
4. `app/views/module_payroll/payroll_employee_view.php`: include the same navbar within the Runs detail flow.
5. This task-specific audit/handoff.

This is explicit owner-authorized navigation behavior, an exception to otherwise protected navigation destinations/includes. Use the existing shared nav and its current-state/module checks. Wrap each newly included nav in `d-print-none`; no CSS, script, form, calculation, print subtree, route or backend edits.

Acceptance: the sidebar retains Payroll but no longer duplicates Runs/Import; the existing module navbar exposes Home, Payroll Items, Payroll Runs and Attendance Import on the Runs flow; active/current behavior remains existing behavior; read-only destinations work; existing controls/financial values/print source remain unchanged; HRIS navigation still works; source/working-tree scope matches this allowlist.

## Audit scope and evidence

The audit covers the current running review branch, not an assumed integration of the draft PRs. Browser observations used the documented isolated fictional demo account. Sources were inspected independently before comparing selected draft patterns. No production resources, private employee data, database writes or financial actions were used.

### Completed navigation change

- Removed exactly the two requested sidebar anchors. The existing Payroll entry, fallback Payroll Items entry, module conditions and other sidebar links remain intact.
- Reused `partials/payroll_nav.php` on Runs list, run review and employee payroll detail. The existing navbar provides Home, Payroll Items, Payroll Runs and Attendance Import.
- Runs is active throughout its detail flow, with the existing `aria-current="page"` behavior on the exact list route. No current-state policy was changed.
- All three added includes are inside `d-print-none`. No stylesheet or payslip/print subtree was changed.

## Correction from the final source audit

PAY-01 is retracted. The actual controller already assigns the February month-end back to `$requestedPayDate` before deriving the cutoff. The earlier isolated reproduction omitted that assignment. The actual controller (with an in-memory persistence spy) and actual preview function agree in 48 valid 2026/2028 cases. No date-formula fix was needed. See `md-payroll-final-audit.md` for current findings and remaining limits.

## Independent findings and proposed work

**Historical baseline note:** the table below records the original navigation-audit findings. Current fixes and remaining issues are tracked in `md-payroll-final-audit.md`; in particular employee names and import safeguards have since been repaired, while lifecycle/calculation gaps remain open.

P1 means resolve before functional Payroll acceptance. P2 means important usability/consistency work for a separately approved batch. Findings are pre-existing unless marked FIXED in this increment. Source-only findings are not claims that a real mutation was executed.

| ID / priority | Finding and evidence | Proposed action / boundary |
| --- | --- | --- |
| PAY-01 / RETRACTED | The reported February cutoff mismatch was a false positive in the earlier reproduction, not an application defect. Actual controller and preview agree, including February and leap years. | No date-formula change needed; verified against actual code in the final audit. |
| PAY-02 / P1 | **Employee name missing from employee payroll/payslip.** Run overview identifies the fictional employee by name; employee detail and Payslip show `Employee #1`. `PayrollRunModel.php:1970` selects `firstname/middlename/lastname`, while `payroll_employee_view.php:64` reads underscored field names. Live detail and Payslip tabs confirm fallback identity. | Agree on one existing data contract and verify both detail and print identity. Requires separately authorized functional view/model work. No name repair was made. |
| PAY-03 / P1 | **Run actions point to incomplete workflows.** GET `/payroll/runs/edit/1` and `/payroll/runs/print/1` both return 404 in the authenticated browser. Route inspection finds no corresponding GET routes. POST finalize targets a missing `PayrollRunController::finalize`; its view handler also references SweetAlert without a corresponding loaded dependency. Finalize was not invoked. | Decide whether Edit/Print/Finalize should be implemented or temporarily made unavailable, then approve an explicit functional scope. Do not make dead actions appear operational through styling. |
| PAY-04 / P1 | **Processing lifecycle/partial-success feedback does not match completion expectations.** Source: `processPayrollRun()` leaves `markPayrollRunProcessed()` commented at model line 1573, counts individual employee failures, then commits and returns counts. Controller returns success; the current view shows a generic success toast without interpreting `error_count`. | Senior/business review of status transitions and partial processing, followed by isolated mutation acceptance. No processing, lifecycle or calculation change was made. |
| PAY-05 / P1 business review | **Configured items are not all integrated into the run pipeline.** Source `processPayrollRunEmployee()` calls basic/holiday/overtime/allowance/lateness handling, but not the configured commission, incentive, cash advance and other deduction flows. Overtime currently multiplies hours by a hardcoded 100. These are implementation observations, not a decision about the correct pay policy. | Confirm intended calculation scope and overtime policy with the senior before claiming Payroll correctness. Separate business-logic task and disposable-data tests; do not infer replacement formulas. |
| PAY-06 / P2 | **Page structure does not yet match HRIS.** Six item pages, Import and Runs use lower-level headings without a page H1; item pages lack the accepted HRIS context/subtitle/result feedback pattern. Runtime DOM confirms zero H1s on the seven catalog/import entry pages. | Adopt accepted HRIS page introduction, typography, spacing and truthful existing result context using scoped styles. Propose any new response-derived summaries separately. |
| PAY-07 / P2 | **Controls, actions and tables lack programmatic context.** Each item table has unnamed icon Edit/Delete buttons; visible search labels lack input association. Compensation Add has six controls with no associated label or ARIA name, and the dialog lacks `aria-labelledby`; the close icon is unnamed. Existing tables lack `scope` and captions/scroll-region labels/tab stops. Source patterns repeat across the six views. | Reuse HRIS action naming, label associations, dialog headings and labeled keyboard-scroll regions. Verify keyboard behavior and targets rather than relying on glyphs/tooltips. |
| PAY-08 / P2 | **Deletion and asynchronous safeguards trail HRIS.** Compensation Delete displays only “this record,” without selected employee/rate context. Source item requests lack the pending/duplicate and stale-read guards used by the completed HRIS flow; option-read failures lack local recovery feedback. No duplicate-submit or failure injection was performed. | Add selected-record identity, Cancel focus/return, pending/dirty/race guards and explicit/uncertain result handling under separately approved functional UI scope. Preserve request and server contracts. |
| PAY-09 / P2 | **Sort and pagination context is weak.** Salary Rate sorting works (observed order 800, 900, 1700, 1800), but current headings show no active direction/`aria-sort`; item pagination has only a numbered link and no result context/current-page semantics. Four or fewer records were available, so multi-page acceptance was not run. | Reuse HRIS sort feedback and page semantics without changing supported sorts, server page size or query keys. Additional page fixtures require separate approval if they write data. |
| PAY-10 / P2 | **Invalid import previews still offer Import.** Actual two-row CSV preview correctly reports one invalid date, but Import Attendance remains enabled. Server source rejects invalid rows before persistence; the defect is misleading client affordance, not proof that invalid records can be imported. | Disable and explain unavailable import until the full preview is structurally valid; use an explicit contextual confirmation and pending protection. No import was submitted. |
| PAY-11 / P2 | **Errors beyond the 300-row preview cap cannot be located.** Actual 301-row fictional CSV: header reports 301 rows / 1 invalid; table contains 300 rows, zero visible error rows, and no “first 300” notice; Import remains enabled. Only the final row was malformed. | Explain displayed versus total rows, include full-preview validation counts and clear correction instructions, and make invalid full previews unavailable. Preserve the full session-backed preview and server validation. |
| PAY-12 / P2 | **Import completion can replay the prior POST.** Source `importView.php:173` reloads the document after AJAX success; that document is the multipart preview POST. Missing result counts default to zero and no pending guard prevents repeated current-page submissions. Source finding only; no real import/reload executed. | Retain confirmed results and offer deliberate GET navigation to a new upload; handle uncertain outcomes explicitly and do not auto-retry financial writes. |
| PAY-13 / P2 business review | **Reset visibility disagrees with server eligibility.** Run view shows Reset for DRAFT or CANCELLED; model `resetPayrollRun()` accepts DRAFT only. Source comparison only; no cancelled run or Reset mutation created. | Decide intended lifecycle, then reconcile UI and server under separate functional authorization. |
| PAY-14 / FIXED | **Duplicate sidebar shortcuts and missing Runs navbar.** Confirmed on the baseline and corrected in this increment as requested. | Completed and verified; broader page redesign remains pending. |

Additional source note: the registered GET `/payroll/create` points to an absent `PayrollController` file. It was not a tested visible destination and is recorded for later route cleanup. Permission, role and branch-access matters remain deferred by the owner's prior decision and were not altered or retested as policy acceptance.

## HRIS practices to carry into approved Payroll batches

1. Consistent context line, one page H1, concise subtitle and wrapping toolbar; retain existing primary tasks and truthful data.
2. Stable employee/run identity, secondary identifiers, explicit labels and named actions. Keep salary type, frequency, rate, stored balance and status distinct.
3. Exact financial precision, date semantics and status meanings; retain wide comparison tables with all columns accessible by labeled local scrolling. Avoid hiding financial columns or replacing comparison tables with cards by default.
4. Clear existing search/Clear, supported sort direction, pagination and loading/empty/error feedback. Preserve query keys, defaults and page sizes.
5. Selected-record confirmations, keyboard entry/containment/return, pending/dirty guards, and clear rejected/uncertain outcomes. These require approved functional UI work, not just CSS.
6. Screen-only scoped styling and protected payslip/print output. No global stylesheet replacement over the accepted HRIS refinements.

## Table UX Contract - current accepted data surfaces

- **Baseline:** branch and SHA above. Main jobs are find, scan, compare financial/attendance values, and act on the intended record.
- **Implemented now:** navigation only. All table renderers, column order, filters, forms, requests, permission predicates, financial formatting, actions and print regions are protected.
- **Items records:** Allowances/Deductions/Incentives: Employee, Type, Amount, Recurring, Effective From, Effective To, Created At, Actions. Commissions omit Recurring. Cash Advances: Employee, Amount, Deduction Type, Installment, Remaining, Status, Created At, Actions. Compensations: Employee, Salary Type, Frequency, Salary Rate, Effective From, Effective To, Created At, Actions.
- **Item types:** existing Code/Name, applicable Computation/Default Value/Taxable, Created At and Actions columns remain unchanged; no units, percentage bases or ownership policy is inferred.
- **Runs list:** Run Code, Payroll Period, Cutoff, Pay Date, Employees, Gross Pay, Deductions, Net Pay, Status, Actions; existing status/month/Refresh and server paging.
- **Run employees:** Employee, Salary Type, Basic Pay, Gross Pay, Earnings, Deductions, Net Pay, Status, Action; existing loaded-run search/status filters and totals.
- **Employee detail:** existing Earnings/Deductions tables, Attendance tab and full Payslip subtree preserved.
- **Import preview:** Row, Employee No, Name, Log Date/Time, In/Out, Verify Mode, Device, Validation; first-300 display and session-backed full preview preserved in this increment.
- **Actions/visibility:** unchanged except the explicitly removed sidebar shortcuts. All wider table, form or handler proposals above remain unimplemented.

## Draft comparison - references, not integrated code

Live GitHub metadata confirmed these open, unmerged drafts; local source heads matched the same SHA. Selected source patterns were inspected read-only. This is not an exhaustive PR review or authenticated acceptance of the draft implementations.

| Reference | Head | Useful overlap / remaining limit |
| --- | --- | --- |
| [Payroll Items #16](https://github.com/BonLacorte/dental-clinic-development/pull/16) | `8c671ad` | H1, named actions/scrollports, ordering feedback, grouped dialogs and request guards overlap with PAY-06 through PAY-09. Draft changes real renderers/column grouping, so adopting it requires explicit scope and fresh runtime acceptance. |
| [Payroll Runs #17](https://github.com/BonLacorte/dental-clinic-development/pull/17) | `b9facde` | Page/table semantics, action labels and partial/uncertain feedback are useful candidates. It intentionally leaves backend lifecycle and identity concerns unresolved. |
| [Attendance Import #18](https://github.com/BonLacorte/dental-clinic-development/pull/18) | `69c5306` | Source explicitly disables invalid previews, explains the 300-row cap and adds contextual confirmation/results. Candidate for the next import batch; not permission to copy its entire stylesheet or claim its simulated tests are live acceptance here. |

PRs #19/#20 and both dashboard implementations were excluded. No PR, remote branch or existing feature worktree was modified; no draft was merged/cherry-picked.

## Verification actually executed

| Check | Result | Scope / evidence |
| --- | --- | --- |
| Git preflight | PASS | Clean starting tree at `5292937`; no branch switch, staging, commit or push. |
| PHP syntax | PASS | 34 Payroll controller/model/view PHP files plus the changed shared sidebar passed existing Ubuntu PHP 8.3.6 `php -l`. Dashboard source was syntax-parsed incidentally, not reviewed or improved. |
| JS syntax | PASS | Eleven Payroll scripts passed existing Ubuntu Node `--check`; PHP interpolations replaced with neutral literals for parsing only. No script content was changed. |
| Exact source preservation | PASS | Each Runs view equals baseline plus only the documented navbar include/wrapper. All inline scripts identical. Sidebar equals baseline minus exactly two anchors. CSS, backends, routes, dependencies, dashboards and HRIS views unchanged. |
| Changed navigation | PASS | Sidebar links inspected on Runs list/detail/employee, item, import and HRIS routes; Runs/Import absent only from sidebar, Payroll retained. Payroll navbar and existing active/current behavior present on the Runs flow. |
| Keyboard navigation | PASS | Enter reached Import via its navbar link and Compensation via Payroll Items dropdown. Mobile sidebar Escape closed it and returned focus to Toggle navigation after transition. |
| Responsive page checks | PASS | Ten screens at 1440x1000, 768x1024, 375x812, 320x667: 40 actual browser cases, all navs present, root overflow 0. Wide tables remain locally scrollable. This does not certify all cell-level accessibility. |
| Breakpoint checks | PASS | Runs list, run review and Import at widths 575/576/767/768/991/992/1199/1200: 24 cases, nav present, no root overflow. |
| Read-only interactions | PASS | Compensation no-match search, Clear and salary-rate ascending results; Add opens/dismisses, Delete cancellation. Run status PROCESSED yields empty list; restoring All returns seeded run. No record action submitted. |
| Import preview parsing | PASS with findings | Real preview POSTs using fictional 2-row and 301-row CSV fixtures; malformed dates identified. Stored preview/session only; Import Attendance never clicked. PAY-10/PAY-11 record UI failures separately. |
| Run route/handler audit | FAIL existing | Two authenticated GETs return 404; PHP registry inspection finds missing finalize method and unused create-controller target. No mutating route invoked. |
| February cutoff agreement | RETRACTED / superseded | Earlier reproduction was inaccurate. Final audit executes actual controller/preview code: 48 valid cases agree; no real create POST. |
| Employee identity | FAIL existing | Live detail and Payslip fall back to Employee #1; model/view field mismatch inspected. PAY-02. |
| Financial/form/print source preservation | PASS | Run form manifest and every run employee table text/amount matched before/after; full Payslip DOM matched exactly. New nav wrappers print-hidden. |
| Browser diagnostics | PASS | No observed browser warning/error messages from the final session; no new application script/resource requests introduced by nav includes. Intentional dead destinations inspected separately. |
| Whitespace/scope | PASS | `git diff --check`; exact four application files plus this audit. Index remains untouched. |
| Payroll writes/import processing/persistence | NOT RUN | No create/load/process/finalize/reset/cancel/delete/import or item save/delete requests. Existing financial correctness is not certified. |
| Full print/PDF, native text zoom, AT, physical devices, other browsers | NOT RUN | Payslip source/DOM comparison is not print pagination, screen-reader or WCAG conformance evidence. Temporary browser viewport override reset. |
| Restricted access acceptance | NOT RUN | Deferred by owner; no permissions/access changes. |

### MTD rule disposition - audit, not blanket table acceptance

| Rules | Result | Evidence / remaining work |
| --- | --- | --- |
| MTD-01 | FAIL existing | Semantic tables exist, but header scope associations are missing. |
| MTD-02 | PASS observed | Existing datasets/columns are visibly identified; H1/caption consistency remains PAY-06/PAY-07. |
| MTD-03, MTD-06 | NOT RUN fully | Header separation/readability observed; complete grayscale/200% text acceptance not performed. |
| MTD-04, MTD-05 | PASS observed | Existing employee/run identifier hierarchy and row separators present; not redesigned. |
| MTD-07 | N/A change | No row-interaction/sticky-header change. |
| MTD-08, MTD-09, MTD-10 | PASS preservation | Exact unchanged financial/date/status source and sampled run data. The earlier February finding PAY-01 was retracted in the final audit. |
| MTD-11 | FAIL existing | Item icon actions lack usable names. |
| MTD-12 | PASS preservation | Row/action/permission data and handlers unchanged. Restricted-role acceptance deferred. |
| MTD-13 | FAIL existing | Generic selected-record deletion context. |
| MTD-14, MTD-15 | FAIL existing | Missing search/form/filter programmatic labels. |
| MTD-16, MTD-17 | FAIL existing | Active sort direction/announcement absent on sampled Items table. |
| MTD-18 | NOT RUN fully | Single-page controls inspected; multi-page combination/large dataset tests unavailable without new fixtures. Result/current-page context recorded in PAY-09. |
| MTD-19 | N/A | No existing row-selection feature in audit scope. |
| MTD-20 | NOT RUN fully | No-match/empty observed; fault/uncertain recovery not injected. Source-only guard/response risks recorded. |
| MTD-21, MTD-22 | PASS structure | All existing columns remain in local scroll surfaces across 64 viewport cases; no root clipping introduced. Full keyboard-scroll acceptance remains PAY-07. |
| MTD-23 | PASS preservation | Existing identity emphasis retained. |
| MTD-24 | N/A | No mobile card conversion proposed as the default for comparable payroll numbers. |
| MTD-25 | NOT RUN fully | Small seeded item/run datasets; 301-row import cap checked separately. |
| MTD-26 | NOT RUN fully | No full contrast/target/zoom conformance claim; no new styling. |
| MTD-27 | FAIL existing | Unnamed controls/unassociated labels observed; navigation keyboard paths themselves passed. |
| MTD-28 | PASS source/DOM only | Payslip subtree, scripts and print CSS unchanged; actual PDF/page breaks NOT RUN. |
| MTD-29, MTD-30 | PASS | Exact allowlist, explicit navigation exception, protected behavior and honest evidence/limits. |

The skill's historical video provenance limits remain unchanged; no claim that the five reference videos were watched in this audit.

## Evidence artifacts

Temporary local evidence is outside Git under the task visualization directory:

- `payroll-navigation-after.png`: desktop navbar/sidebar result.
- `payroll-navigation-mobile.png`: mobile navigation result.
- `payroll-february-preview.png`: live February client preview.
- `payroll-preview-audit.csv`, `payroll-preview-301-audit.csv`: fictional preview-only fixtures. Do not import them.

## Proposed next batches - require approval before implementation

1. **Functional correctness decisions:** confirm remaining PAY-02 through PAY-05 and PAY-13 with the owner/senior. Specify narrow date/identity/action/lifecycle fixes and isolated regression tests; keep permission/access review deferred.
2. **Payroll Items consistency:** use accepted HRIS headers, table semantics, accessible actions/dialog identity, labels, sort/page feedback and request guards across all six pages; assess selected #16 patterns against current HRIS styles and preserved business columns.
3. **Payroll Runs consistency:** listing/review/employee-detail hierarchy and read-only feedback, then separately approved functional outcomes and dead-action handling. Preserve all amount columns, original precision, calculations and print.
4. **Attendance Import consistency:** explain Upload -> Preview -> Confirm, full-preview validation/cap, unavailable invalid imports, contextual confirmation and retained trustworthy results. Consider selected #18 patterns with real local preview acceptance.
5. Finish cross-page, zoom/keyboard/AT/print and approved disposable-data functional acceptance. Revisit both dashboards only after the owner resumes that combined task.

**First concrete next action:** review this compiled backlog and choose the next narrow improvement batch. The requested two navigation changes are ready for review locally; no publication or wider repair is authorized by this handoff.
