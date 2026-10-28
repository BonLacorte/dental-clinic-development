# Unified Payroll Summary and expanded fictional demo

Status: READY_FOR_REVIEW — implemented and populated locally on 28 October 2026 (Asia/Manila). Source changes remain uncommitted. The dataset demonstrates screens and saved states; it is not payroll-calculation or lifecycle acceptance.

## Baseline and authorization

- Owner explicitly approved a shared Payroll Summary card and an additive fictional dataset of 20 employees, supporting HRIS/Payroll records and six saved-status scenarios. This authorizes the new local CLI loader and its database inserts; prior calculation/lifecycle and access-policy deferrals remain unchanged.
- Project `/home/costa/projects/monterodental-stage2-review`; branch `review/md-hris-manual-refinements`; HEAD `52929377eabc8101460141b108062d2b0154b905`.
- Existing-file allowlist: `app/views/module_payroll/payroll_index.php` and additive `public/assets/css/style.css` only. New source: `scripts/extend-demo.php`. Documentation: this handoff. All 192 other pre-existing source/document files match their baseline hashes.
- Source baseline `/tmp/summary-demo-20261028-baseline`. Previous CSS remains an exact byte prefix. All executable PHP tokens in Payroll Home are identical to the baseline; only four redundant static context spans were removed. IDs, counts, definitions, unavailable handling, navigation and Recent Payroll Runs remain intact.
- No staging, commit, push, merge, branch switch, dependency install, schema/configuration/permission changes, deployment, existing-record updates, payroll processing or attendance-import submission occurred.

## Presentation

Payroll Summary now contains its title, organization badge, explanatory note, divider and all four counters inside one white card matching Workforce Summary. Counter tiles have no individual borders/backgrounds/shadows; icons precede labels and values are prominent. Desktop/tablet/mobile use four/two/one columns. Recent Payroll Runs remains a separate existing section.

## Applied dataset

Bundle `DEMOEXP-v1`, anchored at **2026-10-28 11:50:05 +08:00**. All new employee identifiers use `DEMOEXP-001` through `DEMOEXP-020`, last names are Fictional, and emails end in `.test`. Existing branches and compatible lookup/item types were reused. No user or access-assignment records were added.

| New records | Count |
| --- | ---: |
| Employees — 18 Active / 2 Inactive, branch split 12 / 8 | 20 |
| Departments — Administration and Patient Services | 2 |
| Fixed shifts — 08:00–17:00 and 10:00–19:00, one-hour break | 2 |
| Employee shift schedules | 20 |
| Compensation records | 20 |
| Attendance logs | 400 |
| Leave requests — 8 Pending, 6 Approved, 4 Rejected, 2 Cancelled | 20 |
| Explicitly fictional SPECIAL holidays | 2 |
| Allowance / commission / incentive / deduction assignments | 10 / 6 / 6 / 8 |
| Cash advances | 4 |
| Payroll scenario runs | 6 |
| Run-employee snapshots — 8 per run | 48 |
| Run-day snapshots — 8 per run-employee | 384 |
| Illustrative payroll items | 72 |
| **Total newly inserted rows** | **1,030** |

Attendance covers 20 recent Monday–Saturday clinic days for each employee. The two inactive employees' attendance ends before their resignation date. It includes 300 ordinary records, 20 lateness examples, 20 undertime examples, 20 overtime examples and 40 incomplete-punch examples. Missing-punch actual duration is NULL, not asserted to be zero or absence. No attendance-import workflow was invoked.

Pending requests add two examples in each 0–2 / 3–7 / 8–14 / 15+ day bracket at the anchor date. Approved requests include overlapping requests for one employee and simultaneous leave for three distinct employees. Historical creation times and payroll snapshots are explicitly fictional fixtures.

### Payroll scenarios

| Run | ID | Saved state | Pay date | Employee results |
| --- | ---: | --- | --- | --- |
| DEMOEXP-01 | 2 | FINALIZED | 15 Aug 2026 | 8 Computed |
| DEMOEXP-02 | 3 | PROCESSED | 30 Aug 2026 | 8 Computed |
| DEMOEXP-03 | 4 | CANCELLED | 15 Sep 2026 | 8 Pending |
| DEMOEXP-04 | 5 | PROCESSING | 30 Sep 2026 | 4 Computed / 4 Pending |
| DEMOEXP-05 | 6 | DRAFT | 30 Oct 2026 | 4 Computed / 3 Pending / 1 Error |
| DEMOEXP-06 | 7 | DRAFT | 15 Nov 2026 | 8 Pending |

Names identify each as a fictional illustration; the Error message explicitly says it is simulated and no processing occurred. Computed fixtures have internally reconciled basic/additional earnings/gross/deductions/net and matching items, calculated in integer centavos solely to build illustrative snapshots. No existing calculation or status-transition code was invoked or changed.

The five prerequisite dashboard sections remain unplotted. Scenario records do not validate financial calculations, historical staffing completeness, historical branch allocation, Finalize or other deferred lifecycle actions.

## Live totals and walkthrough

1. Open [HRIS Home](http://127.0.0.1:18080/hris): **24 employees**, **22 marked Active**, **9 Pending requests**, **2 branches**. Branch counts are QC 14 / Makati 10. Departments are Clinic Operations 16 / Administration 4 / Patient Services 4; Position grouping shows Assistant 9 / Dentist 8 / Receptionist 7.
2. At the anchor date, the approved-leave calendar peaks at **3 distinct employees** on 31 October. Pending-age bars show **3 / 2 / 2 / 2** because the original Pending request is preserved alongside the eight new ones. Changing date or allowing time to pass naturally changes these temporal views.
3. Open Employees: three pages contain 10 / 10 / 4 records. Search `DEMOEXP` or open Amara Fictional to inspect a new record. In DTR select `DEMOEXP-001 - Fictional, Amara` and October 2026 to inspect the seeded attendance. Shifts, Offices and Holidays contain the new labelled configurations.
4. Open each of the six Payroll Items pages; new assignments and compensations are populated without replacing original records.
5. Open [Payroll Home](http://127.0.0.1:18080/payroll): shared-card counts are **7 total / 3 Draft / 1 Processed / 1 Finalized**. The status chart also shows **1 Processing / 1 Cancelled**. Needs Attention shows **2 computed Draft runs** and **1 Error employee result**. The default latest run is DEMOEXP-06 with eight Pending employees.
6. Select DEMOEXP-05 in Employee Calculation Progress and choose View calculation to see the mixed stacked bar, or open [the mixed dashboard scenario](http://127.0.0.1:18080/payroll?run_id=6). Its progress is **3 Pending / 4 Computed / 1 Error**. View Run opens the saved detail and read-only employee links show labelled illustrative items.
7. Open Payroll Runs and **clear Pay Month**, then Refresh, to see all seven runs across all periods. The default October filter intentionally shows only the original October run and DEMOEXP-05. Read the scenario names before interpreting status or financial values; this walkthrough does not require processing, resetting, finalizing or deleting anything.

Original run DEMO-202610 remains saved Draft with its four Computed employees. Fresh before/after browser comparison and row hashes confirm unchanged gross **53,350.00**, additional earnings **11,750.00**, deductions **56.25** and net **53,293.75**.

## Loader and recovery contract

- CLI-only `scripts/extend-demo.php`: `--dry-run` previews without database/file writes; `--apply` inserts only the approved fixture bundle or verifies/skips a completed bundle. Uses the existing configured runtime, not a new server or dependency.
- Rejects any transport/database/user other than the recognized local demo pair, missing initialization marker, missing/ambiguous demo lookups, additional organizations, non-`.test` employee email identities, nontransactional target tables, incompatible schema columns or unmanaged/conflicting DEMOEXP records. No initializer, root credentials, grants, database/schema creation, UPDATE or DELETE statements are used.
- Prepared INSERTs with generated IDs, explicit tenant bindings and staged references. Existing lookup rows are read only. All inserts and inserted-row validations occur within one transaction; exceptions before commit roll back the transaction.
- Before insertion, a private data-only JSON backup and original ID/hash manifest are saved under Git-ignored `storage/demo-fixtures/DEMOEXP-v1/`. No authentication/credential tables are copied.
- Backup: `storage/demo-fixtures/DEMOEXP-v1/before-20261028-115005-1c1864.json`; SHA256 `b22685b15662120a18b9679c16b4b9b4d7d53805f37de2b08e9f1b349fdc2024`. Backup and manifest modes are 0600, owned by the workspace user (1000:1000).
- A recoverable intent journal is written before commit, followed by the completion manifest. If commit succeeded but manifest writing was interrupted, a later run can verify the intent against rows and recover the manifest without reinserting. Partial/changed records are refused rather than repaired or overwritten.
- A second real `--apply` returned `already_complete`, verified **140 original / 1,030 fixture rows**, and inserted **zero**. A final read-only verification after browser testing confirmed those same row hashes. Subsequent legitimate edits to recorded rows can cause repeat verification to refuse; do not remove journals or reset records to bypass that guard.
- No cleanup, restore or reset was run. Any future removal/restoration should be separately reviewed against manifest IDs and newer user changes.

## Verification actually run

| Result | Check |
| --- | --- |
| PASS | Actual local schema/transport preflight and SELECT-only dry run: 1,030 planned inserts; 140 original rows across the affected business tables. |
| PASS | Read-only plan-integrity checks: 3,874 assertions across planned row counts, tenant/reference ordering, identity markers, employment dates, attendance durations/modes, leave statuses/ages, cutoff dates, snapshot dates and integer-centavo arithmetic. These are fixture validation assertions, not 3,874 independent product workflow tests. Wrong-target configuration and unsupported CLI mode were rejected before mutation. |
| PASS | Real apply committed 1,030 inserts in one transaction with backup and validation; all 140 pre-existing rows preserved. Second apply inserted zero duplicates. |
| PASS | 15 post-apply checks: totals/statuses, attendance modes, backup hash/private modes, excluded authentication tables, old/new row hashes, and recovery/conflict guards. Recovery tests used copied temporary journals against read-only queries; they changed no real database rows or original journals. |
| PASS | Three full native Payroll view renders: populated, empty and unavailable. Executable view PHP token sequence identical to baseline. |
| PASS | Browser walkthrough: employee details and three-page pagination; new DTR records; Shifts, Offices, Holidays and Leave screens; all six Items pages; all-period run list; all six scenario details with eight employee links each; computed employee detail; mixed dashboard selection; original run financial comparison. Run-table observation counts include a summary row; employee links and database checks establish eight employees per fixture run. |
| PASS | Both live dashboards reconcile with source data and show exactly five prerequisite panels. No console errors observed on checked pages. |
| PASS | 18 responsive checks across both dashboards: 1440×1000, 768×1024, 375×812, 320×812, 375×667 and 575/576/991/992px widths. White parent summary, zero inner-counter borders, correct four/two/one columns, no page/chart overflow greater than 1px. Desktop screenshots inspected. |
| PASS | CLI loader and Payroll view PHP syntax lint; whitespace check; all 192 protected source files, prior CSS prefix, index, branch and HEAD preserved. Incremental patch reverse-check recorded in local evidence. |
| NOT RUN | Real payroll processing, payment, Finalize/Reset/Cancel/Delete, actual import submission, production data, additional browsers/physical devices, complete assistive-technology/200% native text-zoom acceptance, forced live transaction failure or production-scale performance. Existing deferred backend/access issues were not repaired or certified. |

## Evidence and next step

Local evidence: `C:/Users/loueg/Documents/ChatGPT/montero-dental-local/audit-evidence/summary-demo-20261028` — approved source increment, dry-run/apply/repeat/final results, plan and post-apply guards, native view tests, browser before/walkthrough/run/totals/console/responsive observations and preservation report.

Screenshots in the established visualization folder: `payroll-summary-expanded-demo.png`, `payroll-mixed-scenario-demo.png`, `hris-expanded-demo.png` and `hris-expanded-demo-calendar.png`.

Next: review the shared summary and explore the labelled demo scenarios using the walkthrough. Source changes remain uncommitted; no Git publication is implied. Continue treating the five prerequisite charts and deferred backend/access work as separate decisions.
