# Task: Attendance UI/UX modernization

## Identity and status

- **Task ID:** MD-HRIS-ATT-UX-001-BUILD, user-approved version 2.0.
- **Branch:** `codex/md-hris-att-ux-001-build`.
- **Status:** `READY_FOR_REVIEW` — implementation complete; PHP/application integration and human review remain required.
- **Last reviewed:** 2026-10-23, Asia/Manila.
- **Source baseline:** accepted development `main`, `b5526bb4a7999bbd3c253fcdda1c8419a171314d`.
- **Checkout:** `/workspace/dental-clinic-development`, cloud checkout. No WSL worktree was created or modified.
- **Git publication:** explicitly authorized by the user's build request. Resolve final implementation/documentation HEAD with `git rev-parse HEAD`; the draft PR and final chat handoff identify its immutable SHA.

Implementation commits (oldest first):

| Commit | Change |
| --- | --- |
| `dfcca3a` | Shared scoped Attendance presentation. |
| `fb1b472` | Daily Time Record interface. |
| `62ba9ce` | Leave Management presentation and interactions. |
| `f2bcbf8` | Holiday catalog and dialogs. |
| `9a9b9d4` | Shift schedule presentation and dialogs. |

The subsequent documentation commit records this consolidated handoff. Review the complete baseline-to-HEAD diff rather than transferring a single dependent view commit in isolation.

## Goal and acceptance criteria

Improve finding, scanning, comparing and acting on Attendance records using the approved Pearl & Teal design system, while preserving the current custom PHP MVC, Bootstrap and jQuery contracts.

- [x] Improve DTR selection, selected reporting context, four existing summary metrics and readable daily records.
- [x] Improve Leave Requests/Types, contextual Add actions, periods, statuses and paid/unpaid classification.
- [x] Combine holiday identity/date; display existing type, exact multiplier factor and work setting.
- [x] Group shift identity/type and schedules; retain required hours, breaks, overtime and every original supported sort.
- [x] Improve existing dialogs, record context, pending states, known/uncertain failures, cancellation protection and focus.
- [x] Keep application modifications within the five-file allowlist.
- [ ] Complete PHP lint and read-only integration checks in a compatible isolated fictional PHP/database environment.
- [ ] Complete assistive-technology, browser/device and print review before any production transfer.

**In scope:** the four Attendance views, scoped shared CSS and expressly authorized page-local JavaScript. The user explicitly authorized the functional presentation interactions that older styling-only skill text would otherwise defer.

**Out of scope:** backend changes, new permission policy, SQL/schema/data changes, business calculations, payroll, import/scanning, global navigation, dependencies, production, merge and deployment. Employees/Organization branches and PRs were not imported or changed.

## Relevant architecture and exact changed files

| File | Change |
| --- | --- |
| `app/views/module_hris/dtr.php` | Selection hierarchy, View Records label, initial state, submitted selection context, summary cards, units and local scroll. |
| `app/views/module_hris/leaves.php` | Contextual Add action, period grouping, employee number/name, status labels, paid/unpaid presentation, existing sorts and refined dialogs. |
| `app/views/module_hris/holidays.php` | Name/date grouping, readable classifications, factor display without rounding, all existing sorts, work-setting help and dialogs. |
| `app/views/module_hris/shifts.php` | Name/type and schedule grouping, separate hours/break/OT, all time sorts, form sections, conditional visibility and dialogs. |
| `public/assets/css/style.css` | Appended screen-only rules under Attendance workspace/dialog selectors using existing tokens. |
| `docs/work-items/md-hris-att-ux-001-build.md` | This required task-specific handoff and verification record. |

No application allowlist deviation. Page-local helper routines are intentionally repeated in the three CRUD views because shared JavaScript and additional application files are outside the authorized allowlist. No new runtime dependency or build step was introduced.

Read-only contracts inspected: AttendanceLogController/Model, LeavesController/LeaveModel, HolidaysController/HolidayModel, ShiftsController/ShiftModel, HRIS routes, authentication/tenant/module/permission/branch middleware, existing employee dialog patterns and HRIS presentation helpers.

## Implementation results

| Area | Result | Behavior |
| --- | --- | --- |
| DTR | IMPLEMENTED | Original GET form and visibility predicates; original backend values and display expressions; clearer selection, four metrics and units. Selection context describes the loaded records even after controls are changed without submitting. |
| Leave Management | IMPLEMENTED | Separate Requests/Types workflows, active-tab Add action, grouped period with both original date sorts, employee identity, four existing statuses, paid/unpaid classification and preserved search/paging. |
| Holidays | IMPLEMENTED | Combined name/date, readable type, Yes/No work setting and multiplier factor. Decimal text is padded to at least two places, never rounded; `2.0001` displays as `2.0001×`. Raw form values stay unchanged. |
| Shifts | IMPLEMENTED | Fixed/Flexible Window/Flexible Hours labels with original enum values; all four schedule times remain accessible; hours/break/OT remain separate. Conditional hiding follows the original disabled/required rules. |
| Shared Attendance styling | IMPLEMENTED | Scoped hierarchy, spacing, numeric alignment, wrapping, local table scroll, focus indicators and responsive existing Bootstrap dialogs. |

Dialogs keep the selected record context and original form ownership. Add/Edit/Delete requests retain their original URL, method, CSRF and payload construction. Controls lock only after serialization. Confirmed success uses the original refresh callbacks; explicit server failures retain the dialog and values. Field messages are attached only for a server error map keyed by actual visible fields. Transport, malformed-response and unstructured failures are uncertain outcomes: the UI does not claim success or repeat the mutation. Repeating that dialog's mutation requires a page reload and result verification. Delete confirmations retain context on failure and initially focus Cancel. Stay/Discard protection restores the dialog snapshot on explicit discard.

Leave choice loading disables dependent fields/save actions and provides inline retry. Record edit loads are guarded against concurrent detail requests. List reads have local progress, empty and retryable failure presentation; obsolete list responses cannot overwrite a newer request. No new server filter, global pending count or current-page-only sort was added.

## Table UX contract

Baseline for every table: accepted development `main` at `b5526bb4a7999bbd3c253fcdda1c8419a171314d`. User tasks remain find / scan / compare / act, using the existing employee or catalog name as record identity. All original Edit/Delete actions remain associated with their original record IDs and route permissions.

| Screen / route | Original columns | Approved presentation |
| --- | --- | --- |
| DTR `/hris/dtr` | Date, Day, Time In, Time Out, Late, Undertime, Overtime, Hours, Status | Same ordered fields; explicit minute units and original formatting/calculations. |
| Requests `/hris/leaves` | Employee, Leave Type, Date From, Date To, Status, Approved By, Approved At, Created At, Actions | Dates grouped into Leave Period; other information retained; backend-supplied employee number supports identity. |
| Types `/hris/leaves` | Name, Is Paid, Created At, Actions | Same fields, Paid/Unpaid classification. |
| Holidays `/hris/holidays` | Holiday Date, Name, Type, Pay Multiplier, Allow Work, Created At, Actions | Name/date identity grouped; precise factor and Yes/No work display. |
| Shifts `/hris/shifts` | Name, Type, Start, End, Flex Start, Flex End, Hours, Break, OT, Created At, Actions | Name/type grouped; all four schedule fields grouped with individual labels; hours/break/OT separate. |

Creation timestamps, previously hidden on screen by shared HRIS CSS, are accessible on the changed directories. Grouping is explicitly authorized by the build request. Supplementary Sort By controls retain every original sort even when a field no longer has its own column header.

| Table | Supported sort keys, unchanged | Default |
| --- | --- | --- |
| Requests | `employee_name`, `leave_type`, `date_from`, `date_to`, `status`, `approved_by_name`, `approved_at`, `created_at` | `created_at DESC` |
| Types | `name`, `is_paid`, `created_at` | `created_at DESC` |
| Holidays | `holiday_date`, `name`, `holiday_type`, `pay_multiplier`, `allow_work`, `created_at` | `holiday_date DESC` |
| Shifts | `name`, `shift_type`, `start_time`, `end_time`, `flex_start_time`, `flex_end_time`, `required_hours`, `break_minutes`, `allow_overtime`, `created_at` | `created_at DESC` |

Search and pagination still use the server's `page`, `search`, `sort_by`, `sort_dir` contract and ten-record server limit. Choosing a different key starts ascending; activating the current key reverses direction, preserving existing behavior. DTR keeps `month`, `employee_id`, `canViewAll`, `getEmployeeDtr` and `getEmployeeDtrSummary` unchanged.

All business data remains in semantic tables with local horizontal scrolling on narrow screens. Toolbars/pagination wrap outside that scrolling surface. No mobile record cards, row selection, bulk actions, new filters or new endpoints were introduced.

## Preserved contracts

- Original routes, HTTP methods, AJAX URL expressions and payload construction match baseline.
- Original form IDs, field IDs/names/types/defaults, enum option values, required/readonly/disabled attributes, numeric precision/minimum/step and form ownership match baseline. Temporary saving locks restore prior disabled states. Shift visibility follows the unchanged original state rules.
- Every original executable PHP block is unchanged. DTR adds only a presentation `else` branch for the initial empty state.
- CSRF fields and serialization, permission predicates, tenant/branch boundaries, backend validation, approval semantics and CRUD endpoints remain unchanged.
- No attendance, leave, holiday-pay, shift, overtime, payroll or rounding calculation changes.
- No controllers, models, middleware, helpers, services, routes, core, database, configuration, navigation, shared JS, Composer or payroll files changed.
- Existing print declarations remain unchanged; new CSS is screen-only. Actual print equivalence is unverified, particularly the authorized grouping in Attendance tables.

## Verification evidence

Environment: managed cloud checkout; Node and Playwright with `/usr/bin/chromium`. No native compatible PHP executable was available. Docker inspection failed on the socket permission boundary; no containers, schema, initializers, migrations or seed commands were run.

Browser evidence is **SIMULATED source-fixture testing**, not an authenticated application run. The harness reads the changed view source, substitutes a fictional CSRF placeholder, removes PHP includes, supplies a small fixture shell and intercepts GET responses with fictional `.test`-style identities. DTR PHP output is substituted with display placeholders rather than executed. No form was submitted. Mutation-state checks capture AJAX callbacks in memory and invoke them without making any mutation HTTP request. Screenshots visibly identify the fixture and absence of a database.

| Check | Result | Evidence / limits |
| --- | --- | --- |
| Repository identity, clean starting tree, worktrees, branch availability and accepted main | PASS | Git remote/worktree/status inspection; GitHub branch metadata and successful `git fetch origin` / `git ls-remote`. |
| PHP syntax lint | BLOCKED | `command -v php` found no compatible native runtime. Existing original PHP blocks compared exactly; this does not replace lint. |
| JavaScript syntax | PASS | Extracted all four page scripts and ran `node --check` on each. |
| CSS source and parsing | PASS | Balanced scoped CSS blocks; Chromium CSSOM parsed the appended screen rules. |
| Whitespace / allowlist / dependencies / protected paths | PASS | `git diff --check`, exact changed-file audit and source diff. |
| Form and request contracts | PASS | Python/lxml compared baseline/current fields, options, form ownership, URL/method expressions, payload blocks and default sorts; original PHP blocks match. |
| Original date rules / shift state and normalization / ID exclusion | PASS | Source comparison retains date rules, original shift state code, normalization and `formDataWithoutId`; fixture checks all three shift field states. |
| Reflow and table scrolling | PASS | SIMULATED Chromium at 1440, 1200, 1199, 992, 991, 768, 767, 576, 575, 375 and 320px. Root overflow ≤1px; local scrolling on narrow screens. |
| 200% text enlargement | PASS | SIMULATED root font size 200% at 375px; no root horizontal overflow. This is not physical browser/device zoom coverage. |
| Search, all original sort keys and paging | PASS | SIMULATED GET query assertions on Requests, Holidays and Shifts; source audit covers Types. Server results/order/SQL behavior remain unverified. |
| Tabs and contextual Add | PASS | SIMULATED Requests/Types switching and correct Add visibility. |
| Edit dialog, record context, safe Delete cancellation | PASS | SIMULATED detail responses, actual pointer/keyboard actions, Cancel initial focus and return to trigger. |
| Dialog footers and keyboard containment | PASS | SIMULATED footers within viewport at 1440/1200/992/768/375/320px; Tab and Shift+Tab remain inside dialog. |
| Dialog button targets | PASS | SIMULATED bounding-box measurements meet 44px objective. Physical touch testing NOT RUN. |
| Dirty Stay/Discard and focus return | PASS | SIMULATED input changes retained by Stay, restored by Discard; selected trigger restored after Delete Cancel. |
| Loading, read failure/retry and empty response | PASS | SIMULATED GET failures and empty fixtures. |
| Duplicate guard / rejection / uncertain outcome | PASS | In-memory callback simulation only; no mutation request or persistence test. |
| Console errors | PASS | No page errors in completed source-fixture browser runs. |
| Contrast samples | PASS | Primary/surface 6.64:1; body/surface 13.48:1; muted/surface 5.20:1; muted/tint 4.56:1; focus/surface 4.75:1; control border/surface 3.61:1. Token calculation, not comprehensive WCAG certification. |
| PHP/server rendering, real GET integration, tenant/permission scenarios and database correctness | BLOCKED | Compatible application runtime and approved running fictional database were unavailable. |
| Actual Create/Edit/Delete / persistence | NOT RUN | Explicitly prohibited without separately approved disposable fictional test conditions. |
| Screen reader / physical devices / Safari / Firefox / high contrast | NOT RUN | No such test environment used. |
| Print/PDF comparison and cross-module visual regression | NOT RUN | No authenticated application runtime. Static scope audit confirms those application paths and existing CSS are unchanged. |

During verification, keyboard Tab containment and a Sort By select overflow at 200% text enlargement were found and corrected. Subsequent targeted/full fixture checks passed. No unresolved demonstrated frontend test failure is known; the blocked integration gates remain outstanding.

Local verification evidence is kept outside the repository in `/workspace/attendance-review-evidence`: browser/static results, the source-fixture harness and actual 1440px/375px screenshots. Those fixtures are not application test results and should not be copied into production.

### Data Table UX rule mapping

PASS below is limited to source inspection and/or the explicitly simulated browser scope above. It does not establish real server, permissions, persistence, assistive-technology or full WCAG compliance.

| Rule | Result | Evidence / applicability |
| --- | --- | --- |
| MTD-01 | PASS | Native tables, captions and column header scopes retained. |
| MTD-02 | PASS | Visible page/table labels and descriptive grouped headers. |
| MTD-03 | PASS | Existing tint/text tokens and distinct headers. |
| MTD-04 | PASS | Existing name/employee identity emphasized. |
| MTD-05 | PASS | Existing restrained separators; fixture visual review. |
| MTD-06 | PASS | Wrapping, spacing and 200% fixture text checks. |
| MTD-07 | PASS | Existing hover retained; no new row-click behavior. |
| MTD-08 | PASS | Right-aligned numeric fields; exact original quantities and precision; authorized lossless factor display. |
| MTD-09 | PASS | DTR formatting unchanged; authorized date-only human formatting avoids timezone conversion. |
| MTD-10 | PASS | Four existing leave states and semantic palettes; readable textual labels. |
| MTD-11 | PASS | Record-specific accessible action names and existing 44px action CSS; measured dialog targets. |
| MTD-12 | PASS | Source ID/action associations and permissions preserved; actual permission scenarios BLOCKED. |
| MTD-13 | PASS | Existing Edit/Delete actions and record-specific confirmation; Cancel initial focus. |
| MTD-14 | PASS | Existing labeled server search and Clear contract preserved. |
| MTD-15 | PASS | Existing DTR GET selections retained; no new server filters. Actual DTR GET integration BLOCKED. |
| MTD-16 | PASS | Supported sorts only; current key/direction retained in supplementary controls. |
| MTD-17 | PASS | Existing native sort buttons and truthful aria-sort retained where a column represents that sort. |
| MTD-18 | PASS | Same page contract, current-page semantics and server limit; simulated page request verified. |
| MTD-19 | N/A | No row selection or bulk toolbar exists. |
| MTD-20 | PASS | Distinct loading, empty and retryable read errors; uncertain mutations never report success. |
| MTD-21 | PASS | Every original field remains reachable; narrow table scrolling verified on fixtures. |
| MTD-22 | PASS | Toolbars/pagination outside scroll regions; no page-wide clipping. |
| MTD-23 | PASS | Identity and comparison hierarchy, without sticky overlays or lost fields. |
| MTD-24 | N/A | No mobile card replacement. |
| MTD-25 | PASS | Existing ten-record paging; no fetch-all behavior. |
| MTD-26 | BLOCKED | Contrast samples, focus/target/reflow fixture checks passed; full WCAG/real-app verification remains incomplete. |
| MTD-27 | PASS | Fixture keyboard scrolling, Tab containment and focus return; assistive-technology check NOT RUN. |
| MTD-28 | BLOCKED | Payroll/print source unchanged; actual print/PDF comparison NOT RUN. |
| MTD-29 | PASS | Exact allowlist and explicit page-local interaction authority; protected contracts compared. |
| MTD-30 | PASS | Evidence classifications and limitations recorded; no video-review or production-readiness claims. |

Video/source limitations in the existing skill remain unchanged: no new video footage verification was performed.

## Deferred dependencies, risks and production transfer

- **DEFERRED_BACKEND_APPROVAL:** the pre-existing DTR access-control policy (`canViewAll = true` in its controller) needs separate senior backend/security review. No visibility, tenant, permission or route policy was expanded or repaired here. Existing route-level permission distinctions likewise remain an owner-review concern.
- No requested visual grouping or sort needed a new backend capability. Unsupported pending-only server filters, global pending counters, accrual/balance features and one-click approval were not added.
- Compatible PHP lint and authenticated read-only smoke tests are required before integration acceptance. Test every tab, lookup, sort direction, search/Clear/page transition, error, long label, conditional form and dialog on isolated fictional data.
- Real mutations require separately approved disposable fictional test conditions. Verify success, server rejection, connectivity loss and refresh behavior without touching shared business data.
- For potential production transfer, review the five application-file commits together. They rely on the accepted baseline's Bootstrap 5.3.3, Bootstrap Icons, jQuery 3.7.1, Pearl & Teal tokens and `MonteroHris` helpers. Obtain an authorized fresh production snapshot and compare dependencies, contracts and overlapping employee/organization CSS before selective transfer. Do not replace whole files over newer changes.
- Complete human accessibility, cross-browser/device and Attendance/Payroll print checks. This development draft is neither merged nor deployed and does not imply production readiness.

## Next session — first concrete action

1. Fetch the published Attendance branch, inspect its draft PR and verify `git status`, `git log` and HEAD against the PR before continuing.
2. Run compatible PHP syntax lint on the four views, then perform read-only integration checks in the authorized isolated fictional application. Do not initialize or reuse an existing business database.

To create the requested WSL review worktree afterward, run from the original development repository, first checking path/branch ownership:

```bash
git remote -v
git status --short --branch
git worktree list
git branch --list codex/md-hris-att-ux-001-build
test ! -e ~/projects/monterodental-attendance-review
git fetch origin
# Only when the local branch is absent and the target path is free:
git worktree add --track -b codex/md-hris-att-ux-001-build \
  ~/projects/monterodental-attendance-review \
  origin/codex/md-hris-att-ux-001-build
# If the branch already exists and is not checked out in another worktree, use instead:
# git worktree add ~/projects/monterodental-attendance-review codex/md-hris-att-ux-001-build
```

Do not switch, reset, stash or clean the original checkout or the Employees/Organization worktrees. No merge or production action is authorized by this handoff.
