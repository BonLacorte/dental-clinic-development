# HRIS Employees UX modernization — A, B and C

- Branch: `feat/hris-employees-ux-modernization`
- Status: `IN_PROGRESS`
- Last reviewed: 2026-10-22, Asia/Manila
- Development baseline: `b5526bb4a7999bbd3c253fcdda1c8419a171314d`, merge of PR #11 at its reviewed head `6ed6f3d242d03d93a16d81e108d392b2c8fbfd39`.
- Authorization: owner explicitly accepted prior runtime risk for this personal development repository and authorized PR #11 merge, A/B/C implementation, narrow export backend changes, commits, branch push and a draft PR. No production access/deployment or PR #8 integration.

## Table UX contract and scope

`/hris/employees`: find, scan and act on employees; compare records in the existing six-column table (Employee No, Name, Branch, Date Hired, Status, Actions). Status is employment status. Existing row View/Edit/Delete permission predicates and legacy Compensation visibility are retained. Existing explicit Search/Clear, five sortable fields plus newest-added default, list endpoint and 10-row pagination remain unchanged. A refines presentation; B is the separately authorized mobile-card/sorting/focus change; C adds the separately authorized authenticated Excel endpoint. Shared dialogs and mutation code are protected.

Application allowlist: `app/views/module_hris/employees.php`, `public/assets/css/style.css`; C additionally `routes/hris-routes.php`, `app/controllers/hris/EmployeeController.php`, `app/models/hris/EmployeeModel.php` and focused export service files under `app/services/hris/`. Focused verification scripts and this task handoff are in scope. No dependency/schema/permission-database changes or shared Payroll changes.

MTD-01–07/10–13 apply to desktop presentation and mobile information/action parity; MTD-14–18/20 apply to shared query state and feedback; MTD-21–27 apply to table/card breakpoints, keyboard/focus/contrast; MTD-28–30 require table-only print, protected-code checks and honest evidence. MTD-08/09 preserve existing nonfinancial identifier/date values. MTD-19 is N/A: no selection/bulk actions. MTD-24 mobile cards are explicitly authorized here. No new filters or unsupported sorts.

## Progress

- PR #11 merged with GitHub expected-head guard and merge-commit strategy. Merge parents match prior main and reviewed PR head. Original `work` checkout preserved; separate worktree created.
- A implemented: Employees heading/actions, labeled explicit-search grouping/help, visible existing applied-result/order feedback, scoped Premium table typography/spacing and pagination refinements. Add predicate/trigger and all existing JavaScript preserved.
- B and C pending; continue sequentially without another approval gate.

## Verification and limits

Runtime prerequisites were previously blocked on this Debian cloud machine. Do not report historical source/browser claims as fresh passes. No application/DB writes or seed/startup scripts have run. PHP/real browser verification will be attempted only with available supported tools; independently executable checks continue.

Next: verify A diff/protected script, commit A, then implement B using one list response and shared dialogs; inspect authoritative session branch data before C.
