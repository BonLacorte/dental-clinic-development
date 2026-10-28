# HRIS and Payroll publication and follow-up plan

Date: 2026-10-28. Branch: `review/md-hris-manual-refinements`.
Status: **PUBLISHED FOR REVIEW; functional Payroll acceptance remains open**.

## Authorized publication

The owner explicitly requested three separate commits and pushes, in this order:

| Commit | Scope |
|---|---|
| `c2f88ba300f8e2b609382d1963937bffb64a6946` | Payroll Items, Runs, employee payroll and Attendance Import integrations/refinements; associated Leave/Holiday/Shift form alignment; sidebar cleanup and their CSS. |
| `8d9be9d94f73729ecd1b97e3ec9390e0340430f7` | Dashboard summaries and visuals, unified Payroll Summary, scoped Home CSS and CLI-only expanded-demo loader. |
| Documentation commit containing this handoff | Existing guides/audits, original-versus-current comparison and this follow-up plan. |

All three belong to the same existing review branch. No PR creation, PR status changes, closing, merging, deployment or backend fixes were authorized by this publication request. No branch switch, history rewrite or force push is part of this task.

The shared stylesheet was split in the index at the existing Payroll Home section boundary. The first commit contains the business-page rules; the second contains the Home rules. The working stylesheet and application files were not edited to manufacture that split. The only staged boundary normalization was removing a trailing blank line from the first commit's CSS; the second restores the complete audited file byte-for-byte.

The demo loader source is published; actual database contents, backup snapshots, credentials, sessions, private audit harness data and runtime configuration stay outside Git. Publishing the loader does not run it or populate another machine.

## Verification and historical context

- All 21 pending PHP files passed lint in the installed PHP 8.3 runtime. No dependency installation.
- Pending application files matched the source fingerprint used for the completed comparison audit; no application behavior was changed for publication.
- Secret-pattern review of the publication scope found no credential/private-key/token patterns. No config, storage, vendor, dump or session files are in these commits.
- Each staged group passed whitespace/scope checks. The source files stayed byte-identical while committing; only documentation received publication context and this follow-up plan.
- Each push targets the existing review branch and is checked against the live remote SHA. Nothing is merged into main.
- Prior integration/browser results are evidence from the comparison audit, not newly rerun business-operation tests during publication. The loader was not applied and Payroll/Import mutations were not invoked in this publication task.

Earlier work-item documents record the state when those tasks ended, including phrases such as “uncommitted” or “backend unchanged.” They are historical snapshots, not current Git status. The comparison's HEAD/fingerprint identifies the exact tested pre-publication working source. Its preservation statements describe the audit, before these explicitly requested Git writes.

## Unresolved current issues and proposed solutions

Source: [original-versus-current comparison](md-hris-payroll-base-vs-current.md), findings F-01 through F-11. These are proposals for separate work, not fixes included in the publication commits. Access/permission policy remains deferred to the senior as requested.

### Highest-priority data and lifecycle issues

| Finding | Current problem | Proposed resolution | Acceptance test |
|---|---|---|---|
| F-01: Import timestamps | Under the reconstructed local DATETIME schema, accepted 08:00/17:00 punches are stored as malformed dates and DTR shows midnight/zero hours. | First confirm the authoritative schema and timestamp contract. If DATETIME is intended, persist the complete validated Manila date/time rather than only H:i:s. Define recalculation ownership for hours/late/undertime/overtime. Review affected records from original files; never silently repair historical punches. | Real paired CSV import round-trips exact timestamps, yields the approved hours/minutes, renders correctly in DTR and feeds the intended payroll day. Test overnight/missing punches and repeated import in disposable data. |
| F-02/F-07: Processing completion and partial errors | Computed employee records coexist with a Draft run; one failure commits other successes. | Senior chooses atomic versus partial-result policy. Implement explicit transaction/state transitions consistent with that choice; only mark Processed when approved completion criteria hold. Make failures distinguishable and make retries safe for saved/generated items. | All-success run reaches the approved state; one controlled error produces exactly the approved persisted outcome and feedback; retry does not duplicate financial items. |
| F-03: Finalized integrity | Server accepts loading employees into a Finalized run and deleting it; list/detail availability differs. | Enforce an approved action-by-status matrix in the server, not just views. Preserve finalized snapshots; define permitted Draft edits, load, reset, cancel and delete actions. Align list/detail controls with the same rules. This is a workflow-state decision, not a new user permission matrix. | Direct POSTs against prohibited statuses are rejected without changing run, employee, item or day hashes; allowed Draft operations still work. |
| F-08: Calculation coverage | Item CRUD succeeds but the pipeline does not establish complete commission/incentive/deduction/advance application. Overtime uses a fixed rate and lateness has a multiplier. | Confirm pay policies, effective dates, recurrence, rounding and advance repayment with the senior. Add each approved item processor and ledger/balance effect separately, with explicit treatment of retries, reset and cancellation. Do not invent rates or statutory formulas. | Known fictional examples reconcile source items to saved basic, earnings, gross, deductions and net in integer centavos; advances are deducted once under the approved rule. |

### Broken features and traceability

| Finding | Proposed resolution | Acceptance test |
|---|---|---|
| F-04: Finalize 500, Edit 404, Register 404 | Define supported Draft edit fields and snapshot invalidation rules. Implement their missing routes/handlers or explicitly designate unsupported actions. Implement Finalize with validated snapshot totals and transactional status guards. Render Register from saved snapshots, without recalculating payroll while printing. | Browser and authenticated HTTP tests reach Edit, valid Finalize and read-only Register; invalid states reject cleanly; print totals match snapshots and no print operation writes records. |
| F-05: Month/period validation | Reject anything except an exact, real YYYY-MM month and supported period 1 or 2 before persistence. Reuse one date policy for preview and backend. | Existing valid dates remain unchanged, including February 2026/2028; period 3, month 00/13 and trailing garbage produce validation errors and no inserted run. February arithmetic itself is already correct. |
| F-06: Cancellation audit fields | Persist server-owned actor/time and submitted reason in the same transaction/status change. Preserve the approved cancellation eligibility. | A successful cancellation has complete metadata; rejected or repeated cancellation does not fabricate a second event or change financial snapshots. |
| F-09: Payslip company identity | Obtain the authoritative organization/legal display name and branch policy; provide it through the tenant-matched run/view contract and escape it. Do not substitute a guessed legal entity. | Correct organization appears in screen and print; names/amounts remain accurate and no cross-tenant name leaks. |
| F-10: Errors outside first 300 preview rows | Add an all-error review view or paginated structural-error details with original row numbers. Keep full-file validation and never equate the displayed subset with the whole file. | Invalid row 301 is specifically discoverable/correctable; import remains blocked until the entire preview is valid. |
| F-11: “Pay on the 30th” label | Use wording such as “Second payroll — scheduled pay date shown below,” or derive the label from the same valid month/period policy. | February wording agrees with the correct 28/29 preview without changing cutoff calculations. |

Basic HRIS/organization and all six Payroll Items CRUD samples passed. That does not certify every schedule assignment, leave transition, effective-date overlap, dependency deletion or role. Those are acceptance gaps rather than confirmed defects; the comparison distinguishes them from observed failures.

## Home/dashboard follow-up

The seven live sections and saved-count summaries passed the sampled reconciliation/filter checks. Five sections are intentionally deferred; they should remain explanatory until their prerequisites are met:

| Dashboard section | What must be established before plotting |
|---|---|
| HRIS Attendance Exceptions | Validate timestamp import and attendance calculations, shifts/grace windows and exception definitions. Do not label missing logs as absence. |
| HRIS New Hires/Departures | Verify reliable employment-event dates/history and reconciliation rules. Current assignments are not a complete historical headcount ledger. |
| Payroll Gross Trend | Validate calculations and define authoritative comparable runs/periods, revisions and cancelled-run treatment. |
| Payroll Basic-to-Net | Validate each included earning/deduction and basic + earnings = gross; gross - deductions = net. |
| Payroll Gross by Branch | Validate financial snapshots and historical branch allocation. Do not allocate old payroll using employees' current branches. |

Keep saved run status and employee calculation status separate while fixing the lifecycle. Preserve the dashboard warning for computed Drafts until backend state transitions are resolved. Test full charts on fictional normal/error/empty cases after each prerequisite is approved. Screen-reader, native zoom, physical printer, XLSX-specific and broader role/tenant acceptance remain explicit unverified areas.

## Recommended treatment of open PRs

GitHub check on 2026-10-28: #13–#20 and #8 are open Drafts; no PR currently uses this review branch as its head. Correct mapping is **HRIS Home #20, Payroll Home #19**. These recommendations do not change PRs.

1. Prefer **one consolidated Draft review PR from the current integration branch to main**. The branch contains the reconciled/refined result rather than the independent older snapshots. Summarize the three publication commits, include the comparison report and disclose shared backend findings.
2. Keep #13–#20 available as source/design references while reviewing. Do not merge all eight independently over the combined implementation: overlapping CSS/views could create conflicts or replace accepted refinements.
3. Before marking any as superseded, review each draft's diff and handoff against the integration branch. Confirm that wanted changes and review notes are represented; record any intentional exclusions. Importing files manually is not the same as merging the source commits.
4. After integration approval, use a separately authorized PR-management step to label/comment and close superseded #13–#20 without merging them. Preserve the review history and link to the consolidated PR. Do not delete source branches until their work is accounted for.
5. Keep **#8 separate**. It includes employee correctness/access-scope changes and a deferred permission-policy decision. Discuss it with the senior, reconcile with the current code, and test its explicit acceptance matrix before any merge.
6. Track import integrity, lifecycle/status guards, missing actions, input validation, item rules and dashboard prerequisites as separate focused issue/fix PRs. Review backend policy decisions before implementing them; do not hide those changes in a styling PR.
7. A code/UI integration merge is not a production payroll-readiness certificate or deployment. Separate the combined presentation review from the outstanding operational acceptance work.

## Next concrete step

Review the consolidated changes and agree with the senior on the timestamp/schema contract, lifecycle/action matrix and pay-item policies. Then authorize one focused backend repair scope, beginning with import/DTR integrity and run immutability/state transitions. Permissions/access changes remain deferred. No such repair or PR creation/closing/merge was performed by this publication task.
