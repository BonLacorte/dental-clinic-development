# Montero Dental — Pearl & Teal Premium milestone

## Identity, authority and acceptance

- Task: complete authorized frontend presentation milestone; PLAN → SPEC → BUILD → VERIFY → REVIEW.
- Branch: `feat/frontend-pearl-teal-premium`.
- Status: `IN_PROGRESS`.
- Reviewed: 18 October 2026, Asia/Manila.
- Accepted baseline: `94c5d9dd99b9d61a2421863865236cf549770861` (merged PR #10), independently confirmed with live `git ls-remote origin refs/heads/main`. `acf9045` is an ancestor. Initial tree/index clean; stale `origin/main` was not used as baseline proof.
- The user's Premium brief authorizes this BUILD, branch/staging, meaningful local checkpoint commits, one final branch push and one draft PR to `main`. No merge, deployment, production access or business writes. Draft PR #8 is not incorporated.

Acceptance: cohesive deep-teal shell, readable hierarchy, consistent controls/tables/forms/tabs/dialogs and management composition; local wide-table scrolling; preserved 250px desktop sidebar and 992px offcanvas transition; measured responsive/focus/contrast checks; identical functional source, requests, values and print output. Record actual coverage and exceptions without implying complete WCAG/workflow acceptance.

## SPEC — exact file allowlist recorded before application edits

Application files eligible for edits (static classes/layout only in PHP):

1. `public/assets/css/style.css`
2. `app/views/partials/admin-navbar-top.php`
3. `app/views/home.php`
4. `app/views/users.php`
5. `app/views/tenants.php`
6. `app/views/branches.php`
7. `app/views/module_hris/hris_index.php`
8. `app/views/module_hris/OrgUnits.php`
9. `app/views/module_hris/Position.php`
10. `app/views/module_hris/appointments.php`
11. `app/views/module_hris/branches.php`
12. `app/views/module_hris/dtr.php`
13. `app/views/module_hris/employees.php`
14. `app/views/module_hris/holidays.php`
15. `app/views/module_hris/leaves.php`
16. `app/views/module_hris/shifts.php`
17. `app/views/module_payroll/payroll_index.php`
18. `app/views/module_payroll/allowances.php`
19. `app/views/module_payroll/cash_advances.php`
20. `app/views/module_payroll/commissions.php`
21. `app/views/module_payroll/compensations.php`
22. `app/views/module_payroll/deductions.php`
23. `app/views/module_payroll/incentives.php`
24. `app/views/module_payroll/partials/payroll_crud_view.php`
25. `app/views/module_payroll/importView.php`
26. `app/views/module_payroll/payroll_run_index.php`

Only documentation change: this consolidated handoff. Unneeded candidates will remain unchanged; no expansion into executable code is authorized. Protected even inside eligible views: every executable PHP token, script block, inline event handler, generated row, field/form/request/validation attribute, URL, ID, permission and current-state predicate. Preserve form ownership, content and financial text. All files outside this list are byte-protected. In particular, payroll run detail/employee/payslip templates, scanner, routes, backend, seeds, config and dependencies remain unchanged.

All new decoration belongs in existing screen-media theme scopes. Existing CSS outside the Stage 2A screen block remains byte-identical, preserving print. Login overrides also use screen media. Reuse component rules rather than appending competing theme copies. No JS, framework, font, asset dependency or build step.

## PLAN — complete source inventory and initial findings

All 44 PHP templates inspected, including static HTML, inline CSS, embedded PHP/JavaScript and shared/generated structures. Actual routes distinguish available pages from legacy/placeholders.

| Area / exact templates under `app/views/` | Existing presentation / findings |
| --- | --- |
| Shared `partials/admin-header.php`, `admin-navbar-top.php`, `admin-sidebar.php`, `admin-footer.php` | Stage 2A white shell; generic text brand, broad navigation rows and weak module hierarchy. Preserve single sidebar DOM, widths, dynamic header measurement, account/Logout and visibility/current logic. |
| `login.php` | Standalone, script-free CSP, centered white card on blue-gray; palette/shape differ from app. CSS-only polish, unchanged form/security headers. |
| `home.php` | Three existing placeholder counters; plain heading/cards. Improve composition without interpreting zeros as actual metrics. |
| `about.php` | Legacy NCFRS copy/cards and existing `loadTotalUsers` issue reported historically. Preserve content, script and semantic colors; shared styling only. |
| `users.php`, `tenants.php`, `branches.php` | Sparse card/table management screens, inconsistent action appearance. Preserve both distinct admin/HRIS Branch paths; incomplete tenant creation remains separate. |
| `module_hris/hris_index.php`, `module_payroll/payroll_index.php` | Nested gray panel, `<br>` spacer and half-width placeholder card. Screen-scoped layout can improve density; placeholder content must remain. |
| `module_hris/partials/hris_nav.php`, `module_payroll/partials/payroll_nav.php` | Wrapping underline nav/dropdowns; refine spacing/selected states through CSS, keep arrays/predicates/URLs exact. |
| HRIS `OrgUnits.php` | Tree office rows/action timestamps; generated content already wraps at Stage 2A. Improve card/actions without editing generator. |
| HRIS `Position.php`, `appointments.php` | Narrow 120px action column stacks two 44px buttons, causing tall rows. Keep actions inline and table locally scrollable. Appointments remains employment types. |
| HRIS `branches.php`, `holidays.php`, `shifts.php` | Catalog tables, search/sort/pagination and modals use inconsistent header spacing/density. Shared management classes and local scrolling. |
| HRIS `employees.php` | Existing accessible pilot: six columns, four actions, explicit local scroll region, feedback/dirty/pending/focus guards. Polish rather than rebuild. |
| HRIS `dtr.php` | GET filters, attendance summaries and wide daily table; improve hierarchy and numeric readability. No processing/attendance writes. |
| HRIS `leaves.php` | Two tabs, search forms and wide request/type tables. Wrap action group and unify tabs/search density; preserve exact states/approvals. |
| Payroll `allowances.php`, `cash_advances.php`, `commissions.php`, `compensations.php`, `deductions.php`, `incentives.php` | Six duplicated CRUD templates with record/type tabs and executable page configs. Add static page/card classes only, style existing money/sort/action classes. No page-config or generated-row edits. |
| Payroll `partials/payroll_crud_view.php` | Existing reusable version of CRUD markup; keep presentation convention aligned; no functional consolidation. |
| Payroll `payroll_run_index.php` | Heading squeezed by full-width flex filters; ten columns compress/wrap financial values/actions. Separate title/filter flex bases and establish readable local table minimum width. |
| Payroll `payroll_run_view.php` | Read-only summary/total cards, employee results and lifecycle buttons. CSS only; preserve all values, statuses, actions and inline PHP/JS. |
| Payroll `payroll_employee_view.php` | Earnings/attendance/payslip tabs, summaries, financial tables and print rules. Entire template protected; screen-only component polish, print/PDF comparison. |
| Payroll `importView.php` | Existing file/preview/error/result interface. Style current content only; no preview/process submissions or fabricated result states. |
| HRIS `attendance_scan_test.php` | Standalone inline-styled scanner, outside shared CSS and candidate allowlist. Inspected; no styling/scan submission under this scope. |
| HRIS `Shift.php` | Empty legacy file; no interface to modernize. |
| `partials/js/js-pagename-modulename.php`, `partials/modals/modal-pagename-first.php`, `partials/tabs/tab-example-name.php` | Example fragments, not independent routed screens; untouched. |
| `_bak/footer.php`, `_bak/header.php`, `_bak/navbar-side.php`, `_bak/navbar-top.php`, `_bak/forms/registration.php` | Archived legacy templates, untouched. |
| Reports / other destinations | No implemented dedicated HRIS Reports route or report template. Existing DTR/payroll views are available reporting surfaces. Do not invent a report/dashboard/workflow. |

Across management pages, preserve all columns and financial formatting; align existing numeric classes with tabular figures. Keep semantic status colors/text; do not recolor business states teal. Address existing loading/empty/error surfaces via CSS without interpreting responses or changing handlers. No page-wide horizontal clipping.

## Runtime and evidence plan

Existing fictional MariaDB container restarted. Separate `montero-premium-preview` uses existing PHP 8.3.35 image and documented development config mounts; serves `public/` at loopback port 8000. No setup, Composer, schema, seed, grants or application dependencies run/changed. Baseline source hashes cover 170 tracked files. Read-only transaction fingerprints cover demo rows/schema; browser uses normal fictional login only.

Browser: installed Playwright/Chromium. Existing CDN assets are relayed byte-for-byte through TLS-verified `curl` and the inherited proxy; application requests are real, with no API mocks. This transport adaptation adds no project files. Unknown external image hosts are not accessed. Temporary scripts/session metadata in `/tmp/montero-premium`; screenshots/PDFs/sanitized measurements in `/workspace/montero-premium-evidence`. Evidence remains outside Git.

Before/after: all 26 available shell screens at 1440 and 375; ten representative screens plus login at 1440/1024/768/375/320. Follow-up keyboard, dialogs, tabs, filters/sorting/pagination, boundary widths, text enlargement, focus, contrast and print checks. No employee/payroll/attendance/administration mutations during verification.

## BUILD — checkpoints and conventions

A — shell implemented (`eac8d21`): deep-teal sidebar with mint selected state, consistent icon slots/indentation, module divider, tooth-mark brand lockup, quieter account/Logout/footer, and refined wrapping module navigation. Existing width/breakpoint/offcanvas/selection/permission mechanisms unchanged. Actual desktop/320px browser screenshots inspected: root overflow 0px; desktop sidebar 250px; measured header offsets match 69px desktop / 118px narrow layout.

B — shared component styling implemented: quiet secondary buttons, consistent 44px action targets, bordered icon actions, compact table cells, readable headers, segmented existing tabs, dropdown spacing, dialog surfaces, alerts/loaders/toasts and standalone pearl/teal login. Existing validation/status meanings retained; source rules outside screen media unchanged. Actual desktop/320px component screenshots inspected; Positions actions now sit together and rows are visibly denser. C HRIS/admin, D payroll/specialized, E regression/review pending.

## VERIFY — actual results

| Check | Result | Evidence / limits |
| --- | --- | --- |
| Live main, clean tree and Stage 2A ancestry | PASS | Exact SHA above; `git merge-base --is-ancestor acf9045 HEAD`. |
| Baseline screenshots | PASS | 87 samples across 26 available shell pages and login; all five requested widths on representatives. No root overflow; no business-write requests. Existing About JS error and unavailable external Bootdey avatar recorded. |
| Checkpoint A desktop/320px shell | PASS | Actual browser/screenshots; 250px desktop sidebar, matching measured header offset, 0px root overflow. Full keyboard/behavior comparison remains pending. |
| Final accessibility / functional / print comparisons | NOT RUN | Implementation in progress. |

## REVIEW — outstanding coverage and next action

In progress. Historical C-EMP/permission/compensation, payroll lifecycle, missing routes, About content/script and print fallback/layout defects remain separate owner items. No backend/security/workflow acceptance follows from styling. Next concrete action: inspect baseline screenshots, implement allowlisted shell/components, and continue checkpoints without intermediate approval requests.
