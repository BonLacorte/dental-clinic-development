# Dashboard live summaries and consistency fixes

Status: READY_FOR_REVIEW — scoped dashboard fixes implemented and verified on 2026-10-27. The underlying Payroll lifecycle/calculation work remains separately deferred. Owner requested solving the four Home audit findings. This authorizes the necessary read-only dashboard aggregation/controller/view work and static title correction; earlier access-policy, calculation, processing/lifecycle and database-write deferrals remain in force.

## Baseline / scope
- Ubuntu project: `/home/costa/projects/monterodental-stage2-review`.
- Branch `review/md-hris-manual-refinements`; HEAD `52929377eabc8101460141b108062d2b0154b905`; keep all uncommitted work and index.
- Application allowlist: `app/controllers/ModuleController.php`; new `app/models/ModuleDashboardModel.php`; `app/views/module_hris/hris_index.php`; `app/views/module_payroll/payroll_index.php`; additive `public/assets/css/style.css`.
- Documentation: this handoff and current disposition in `md-home-dashboard-integration-audit.md`.
- Preserve other ModuleController actions, route registrations, navigation predicates, completed HRIS/Payroll views and print output. No new API route, AJAX request, dependency, schema, permission or session-policy change.
- No financial/attendance action controls tested. Runtime checks are GET navigation only. New SQL is SELECT-only with server-session tenant binding.

## Data contract
- Match current list scope: current organization/tenant, all saved dates; no new branch policy or account selector. Use only the positive server-session tenant ID, never request parameters.
- HRIS: non-deleted employee total; non-deleted employees whose record status is ACTIVE (label Employees marked Active, not inferred employment status); exact PENDING leave requests; non-deleted branches.
- Payroll: total runs and exact saved DRAFT/PROCESSED/FINALIZED counts. Total also includes other saved statuses. Five recent runs by pay date DESC/id DESC, with same-tenant employee count and no financial amounts or employee details.
- Needs Attention: exact PENDING leave count; same-tenant employee results marked ERROR; Draft runs with at least one COMPUTED employee result. Saved batch status remains distinct from employee computation state. No status normalization or record repair.
- Available empty data displays real zeros and truthful empty messages. Query failure/missing tenant displays unavailable/retry context, not fabricated zeros. No live metrics added to the unrelated main dashboard.
- Align both Home introductions at 24px with the accepted context/title/subtitle white-card format; correct Payroll browser title.

## Completed changes / finding disposition

| Finding | Current disposition |
| --- | --- |
| HOME-01 live data | RESOLVED for these overviews. HRIS uses exact counts and pending-leave attention; Payroll uses exact saved-status counts, latest five runs and explicit computation/error attention. No paginated-page extrapolation, fabricated zeros or employee/financial-detail response. All data is rendered by the existing GET routes; no new endpoint, polling or AJAX. |
| HOME-02 tab title | RESOLVED. `ModuleController::payroll()` now supplies Payroll Home. |
| HOME-03 status meaning | RESOLVED at dashboard level. Cards explicitly use stored batch status; computed Draft runs and ERROR employee results are separately counted and explained. Existing processing transitions remain unchanged, and the earlier demo run remains Draft with computed results. No synthetic Processed count or record repair. |
| HOME-04 consistency | RESOLVED. Both page introductions use the same white card, module context label and 24px/500 title format. |

The new `ModuleDashboardModel` has seven fixed SELECT-only query constants. Tenant IDs come only from the authenticated server session; joins match both tenant and run. Query results contain counts and bounded run metadata, without salary amounts or employee names. Existing Home middleware, list scope and navigation flags remain authoritative and unchanged. Existing main Dashboard/About controller methods remain byte-identical and start no dashboard queries.

## Runtime observations
- HRIS: total employees **4**, employees marked Active **4**, pending leave requests **1**, branches **2**. Employee total, Pending count and Branch total independently match current visible lists in this small demo dataset. Active is explicitly the record status, not an inferred employment-status definition.
- Payroll: saved run total **1**, Draft **1**, Processed **0**, Finalized **0**; recent run `DEMO-202610`, pay date October 15, 2026, four loaded employees, saved Draft status.
- Needs Attention correctly shows **1 Draft run with computed employee results** and **0 employee results marked Error**. The prior processing exception is not hidden or reset.
- Before/after comparisons confirm the previously displayed employee/run financial values are unchanged by this task. No Create/Load/Process/Import/Reset/Delete/Cancel/Finalize controls were clicked; navigation used GET links only.

## Verification actually run

| Result | Check | Evidence / limits |
| --- | --- | --- |
| PASS | Four PHP lints | Controller, new model and both views on Ubuntu PHP 8.3.6 64-bit. |
| PASS — isolated SQL | 10 query-check groups | Actual SQL constants extracted from the PHP model and executed in in-memory SQLite with fictional multi-tenant records. Tests exact/deleted/status counts, totals beyond list page size, seven runs with five recent rows, stable date/id order, distinct computed-Draft runs, all-status ERROR count, cross-tenant mismatch rejection and genuine empty organization zeros. No application DB writes. |
| PASS — native controller fixtures | 8 cases | Actual candidate controller with an in-memory model spy: session tenant only (GET tenant ignored), successful zero counts, correct title, query/model-call exception with generic unavailable message, missing tenant makes no query, unrelated actions make no query, nonpositive IDs rejected before DB access. This is not real role-policy acceptance. |
| PASS — native PHP views | 20 renders | Populated/empty/error/incomplete states, ten existing flag combinations, IDs/ARIA/H1, no forms/page JS, recent code/status/date escaping and unknown-status fallback, and computed-Draft guidance. Live singular Pending wording also checked after copy polish. |
| PASS — actual app | 26 responsive cases | Both dashboards at 1440×1000, 768×1024, 375×812, 320×812, 375×667 and 575/576/767/768/991/992/1199/1200px boundaries. Real summaries available; correct title; both H1s 24px/500 with white introduction background; no root overflow >1px. All sampled dashboard links meet 44×44px. |
| PASS — actual app | 11 link navigations | All Home shortcuts/follow-up links and new recent-run View Run link reach expected read-only pages. |
| PASS — actual app | 10 exact before/after page comparisons | Main Dashboard, About, Employees, Leaves, DTR, Allowances, Runs list/detail, employee payroll and Import: same headings, overflow, table header text/colors/background and financial summary text. Existing calculated records preserved. |
| PASS — actual app | Live counts / keyboard | Current record counts agree as described above; Tab focus and Enter navigation work on both pages. Populated Payroll table scrolls locally with ArrowRight (settled scrollLeft 40px at narrow width). An immediate pre-animation reading returned zero; settled observation confirms normal native scrolling. |
| PASS with pre-existing observation | Browser console | No dashboard-attributable error observed. About already calls undefined `loadTotalUsers` at `app/views/about.php:172`; the same error was recorded before and after these changes. About source/method unchanged; this unrelated issue was not repaired or hidden. |
| PASS | Preservation / source | 184 prior files outside the existing-file allowlist unchanged; original CSS byte prefix and staged index unchanged; same branch/HEAD; `git diff --check` and reverse-check of final increment patch pass. All route/middleware/payroll-calculation/processing/import/print files protected. |
| NOT RUN | Wider acceptance | Real denied-role/tenant/branch sessions, actual financial/attendance writes, production data, native zoom/full assistive technology, other browsers/devices, print/PDF output, large production performance and forced database outages. Query-failure fixtures cover catchable model/query exceptions; the existing base Model connection-failure behaviour was neither changed nor outage-tested. |

## Remaining boundaries
- Underlying run status transition, Finalize/Edit/Print Register, pay-item/calculation gaps, malformed run-input validation and attendance-import calculation work remain in their earlier senior-review scope. Clarifying dashboard metrics does not fix those workflows or establish payroll financial correctness.
- Organization-wide/all-dates counts intentionally match the current list scope. No new branch selector or permissions/access policy was implemented. Any future scope change needs its own decision.
- The unrelated About console error is recorded only. No unrelated cleanup, dependency or data change.
- All work remains uncommitted, including the prior PR integrations; no staging, commit, push, merge or deployment.

## Evidence / next step
- Source baseline: `/tmp/dashboard-live-fixes-20261027-1k1icriv`.
- Local evidence: `C:/Users/loueg/Documents/ChatGPT/montero-dental-local/audit-evidence/dashboard-live-fixes-20261027` — builder, actual-SQL/controller/native-view fixtures, query/view/browser/count/regression/keyboard results, before snapshot, preservation results and `fixes-final.patch`.
- Final increment patch SHA256: `cbd5ecf3040dbcfbe08738d6a52dec80a0b9d4f80c49f38bf06ae2cfd88f32d1`.
- Screenshots in the established visualization folder: `hris-dashboard-live-desktop.png`, `payroll-dashboard-live-desktop.png`, `hris-dashboard-live-mobile.png`, `payroll-dashboard-live-mobile.png`.
- Next step: review the two live overviews and their definitions. Keep unrelated Payroll/access changes separate; Git publication requires its own instruction.
