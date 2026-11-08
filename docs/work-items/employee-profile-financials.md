# Task: Employee Profile Financials

## Identity and status

- **Task ID:** `employee-profile-financials`
- **Branch / source baseline:** `main`, implementation based on `b3ced3b`. This handoff accompanies the Employee Financials-only commit; use Git history for its resulting commit ID.
- **Status:** `READY_FOR_REVIEW`; implementation validated and owner-approved for selective publication, with the existing demo-only SINGLE cash-advance schema limitation below.
- **Last reviewed (Manila):** 2026-11-08.
- **Authorization:** Owner requested the functional Employee Profile Financials implementation and subsequently explicitly authorized committing/pushing it on `main`, excluding the modified Payroll Items pages. Production access, deployment and persistent schema changes remain outside scope.
- **Related work:** [Original employee profile](md-employee-profile.md), [schema comparison](md-employee-profile-schema-comparison.md). The Payroll Items record-controls changes and their local handoff are separate, unpublished work.

## Goal and acceptance criteria

**Goal:** View, create, edit and delete the selected employee's payroll inputs from Employee Profile > Financials.

- [x] Enable Financials with Compensations, Allowances, Commissions, Incentives, Deductions and Cash Advances.
- [x] Keep the profile employee fixed; posted employee/tenant identifiers do not determine ownership.
- [x] Apply tenant, employee, deleted-employee and allowed-branch constraints on new record queries and mutations.
- [x] Require existing module/permission guards and valid CSRF for mutations.
- [x] Preserve existing field definitions, choices/defaults, numeric conversion, effective-date ordering and cash-advance normalization.
- [x] Provide bounded paging, loading/error/retry states, cancellation, mutation guards and contextual deletion confirmation.
- [x] Preserve the separate local Payroll Items record-controls work without including those pages in this publication. Published Payroll Items pages retain their prior controls.
- [x] Validate syntax, live demo CRUD and relevant isolation/UI behavior using fictional records.

**Out of scope:** Payroll processing, posted run values, schema migrations, legacy payroll endpoint authorization changes, unrelated profile tabs, production deployment and publication of the separate Payroll Items page changes.

## Relevant architecture and impacted files

- `app/controllers/hris/EmployeeController.php`: supplies Financials visibility using existing PAYROLL module and payroll view permission.
- `app/controllers/hris/EmployeeFinancialsController.php`: new profile-specific JSON reads and CRUD actions; validates route IDs, CSRF and type references; returns generic persistence errors.
- `app/models/hris/EmployeeFinancialsModel.php`: parameterized, employee/tenant/branch-scoped SQL, ten-row paging and tenant/global type lookups. Mutations constrain the employee in SQL, including INSERT ... SELECT; an earlier controller check is not the only scope check.
- `app/services/EmployeeFinancials.php`: fixed server catalog of the six categories and matching legacy field metadata/normalization. Client metadata excludes SQL identifiers.
- `app/views/module_hris/employee_profile.php`: enables Financials and includes its page content/inline script.
- `app/views/module_hris/partials/employee_financials.php` and `employee_financials_script.php`: employee-context forms, records table and Bootstrap dialogs; the script renders inline in the delivered profile page, consistent with the existing profile convention.
- `routes/hris-routes.php`: new GET collection/detail and POST create/update/delete endpoints under `/hris/employees/{employeeId}/financials/{category}/...`.
- `docs/architecture.md`: durable route/data boundary documentation.
- Six Payroll Items template changes and their record-controls handoff are preserved locally and explicitly excluded from this commit/push.

### Decisions

- The existing tenant-wide payroll list APIs do not filter by the profile employee/assigned branches. Dedicated endpoints keep new profile operations scoped without changing legacy request contracts or broadening the task into a legacy authorization refactor.
- Viewing Financials requires the existing HRIS and PAYROLL modules plus `hris.employee.view` and `payroll.view`. Managing an employee's financial records additionally requires the existing `hris.employee.edit` permission, including item deletion. No new permission names or database permission changes were introduced. Existing super-admin bypass and empty-assigned-branches/all-branches policy are preserved; tenant scope remains enforced.
- Record employee identity comes from the route and session-bound SQL. The displayed employee has no selector. Update statements never reassign `employee_id`.
- All original form field metadata/defaults match, including one-time Incentives and INSTALLMENT Cash Advances. Validation preserves the existing float conversion and stored cash balance/SINGLE rules; no new rounding or calculation policy was introduced.
- The separate, unpublished Payroll Items work comments out create/edit controls. Those changes are excluded here, so this Employee Financials-only publication retains the previously published Payroll Items controls and endpoints. The new Financials implementation does not depend on hiding those controls.

## Verification evidence

**Environment:** Existing `compose.dev.yaml` fictional-data demo, PHP 8.3.36 and MariaDB 10.11, loopback port 8085. Started existing images with `MONTERO_WEB_PORT=8085 docker compose -f compose.dev.yaml up -d --no-build --pull never`; existing data was preserved. No image/dependency upgrade, persistent schema alteration or production access occurred.

| Check / command | Result | Actual evidence and limits |
| --- | --- | --- |
| PHP syntax: changed/new PHP piped to installed `php:8.3-cli-bookworm php -l` with network disabled | PASS | Eight Financials/profile/route PHP files. |
| `python3 /tmp/montero-financials-check/http_check.py` | PASS | Authenticated Overview, Assignments, Details and Financials HTTP/render checks; six employee-scoped JSON lists; `node --check` on rendered inline scripts. |
| `python3 /tmp/montero-financials-check/crud_check.py` | PASS | All six categories create/read/update/unchanged-update/delete using newly created fictional records only. Tested employee/tenant payload tampering, another employee's record detail/update/delete rejection, invalid CSRF, negative amounts, invalid date ordering and unavailable types. Cash Advance live test uses INSTALLMENT. Existing category rows matched their pre-test baseline after cleanup. |
| Same HTTP regression, test-only session fixtures | PASS | Read-only session can read but cannot mutate; missing PAYROLL module/payroll view returns 403; foreign tenant and forbidden branch return 404 for read/create/update/delete despite forged query identifiers. Test sessions were removed; no user/permission rows or authentication code changed. |
| `docker compose -f compose.dev.yaml exec -T app php < /tmp/montero-financials-check/model_check.php` | PASS | Normalization matches all six existing controllers for `0.01`, `123.4567`, `100000.995` and scientific notation. Connection-local snapshot-shaped Cash Advance table accepts SINGLE with null installment and default balance. Model reads/lists/update/delete independently reject another employee/tenant/branch; out-of-scope inserts fail. Temporary table disappears on disconnect. |
| Tenant/global type fixture | PASS | Connection-local type table: own-tenant/global types accepted, foreign-tenant type excluded from options and rejected by reference check. No persistent table changes. |
| Field metadata comparison | PASS | All six catalogs match original Payroll Item field labels, inputs, required/min/step/choice/default metadata; comparison used preserved before-render configurations. |
| `python3 /tmp/montero-financials-check/pagination_check.py` | PASS | Eleven temporary future-dated allowance records exercised distinct ten-row pages and out-of-range page normalization; temporary records deleted and original rows preserved. |
| Playwright CLI browser forms | PASS | All six Add forms and fixed employee context; Edit/Delete open/cancel on the five populated categories, plus Cash Advance Edit/cancel on the second fictional employee. |
| Playwright actual browser CRUD | PASS | Created a new future-dated fictional allowance, edited it, deleted it and confirmed the displayed amounts. Seeded rows were not saved/deleted. |
| Playwright loading/mutation guards | PASS | Synthetic list failure exposes Retry and recovers. A deliberately aborted POST never reaches the server: pending Save/category disabled, pending dialog cannot close, uncertain outcome disables resubmission and closing refreshes records. Expected mocked 500/aborted-request console entries are test-generated; no uncaught application JavaScript exception was observed. |
| Playwright responsive bounds | PASS | Financials page and Add dialog checked at 1440, 768, 375 and 320px: no root overflow, Save footer within tested 900px viewport. Desktop/mobile screenshots visually inspected after modal backdrops closed. This is not a complete accessibility certification. |
| Payroll Items browser regression | PASS | Six real Records pages have no Add/Edit record controls/forms; four type tabs retain Add and row Edit. Legacy delete persistence was not re-exercised. |
| Unsaved-change native confirmation | PASS limited | Prompt observed in browser after changing a field and canceling. Independent assertions of every Stay/Discard variant were not completed; ordinary cancellation and pending-close protection passed. |
| `git diff --check` | PASS | No whitespace errors. |
| Actual local SINGLE persistence | BLOCKED | Existing reconstructed demo schema prohibits null installment amounts; see below. Code behavior passed on a connection-local table with the documented nullable column. |
| Payroll processing/posted data, production, screen readers, other browser engines | NOT RUN | No processing or production action; no broader payroll/accessibility sign-off. |
| Selective publication scope | PASS | Before commit, verified the explicit eight-PHP/two-document allowlist; no Payroll Items page or record-controls handoff staged. Owner authorized commit/push on main. |
| Production deployment | NOT RUN | Not authorized. |

Temporary helpers in `/tmp/montero-financials-check/` and the prior `/tmp/montero-record-controls-check/` may not survive another session. They contain synthetic data. Screenshots are local, outside Git, under the current Codex visualization directory's `output/playwright/financials-desktop.png` and `financials-mobile.png`.

### Reproducible manual verification

1. Start/reuse the existing isolated demo and sign in with the documented fictional account. Open `/hris/employees/profile/1?tab=financial`.
2. Select each payroll category. Verify only this employee's records appear and the Add/Edit dialogs identify this employee without an employee selector. Cancel a seeded record without saving it.
3. Create a new future-dated test record, verify its displayed values, edit only that new record, save an unchanged edit and delete that new record. For local Cash Advances, use INSTALLMENT with a nonblank installment amount until the separate demo-schema mismatch is resolved.
4. Test invalid amounts/date ordering and CSRF; assert no new row was stored. Use isolated test sessions to confirm read-only, missing-module, missing-payroll-view, forbidden-branch and foreign-tenant behavior. Do not change production users or permissions to test this.
5. Create enough temporary records for a second page, test paging, then delete only those fixtures. Check narrow layouts and the original Payroll Items pages. Do not process/finalize payroll as part of this check.

## Blockers, risks and open questions

- **Existing demo schema limitation:** The documented schema comparison says `payroll_cash_advances.installment_amount` permits NULL in the supplied snapshot but is NOT NULL in the reconstructed demo. Both legacy and new SINGLE validation intentionally normalize installment amount to NULL. A real demo SINGLE insert therefore fails with SQL error 1048; INSTALLMENT with an omitted installment also encounters this mismatch. The new code preserves those rules. No schema change or zero-substitution workaround was applied. Resolve development schema alignment as a separately scoped task; this handoff does not authorize an ALTER or migration.
- **Publication:** Owner authorized committing/pushing Employee Profile Financials on `main` only. The six modified Payroll Items views and their record-controls handoff remain local and unpublished. This authority does not include a PR, production deployment or publishing the excluded work.
- **Production boundary:** Implementation/testing used the isolated demo and temporary tables, not a live production server. A subsequent owner-authorized read-only comparison of the copied production snapshot identified a missing PHP conditional and mismatched Financials tab key in that copied profile template; the playground template being published already contains the correct conditional and tab key. The snapshot/live code was not modified, and no production credentials, private database records or live server were accessed.

## Next session — first concrete action

1. Verify the Employee Financials commit/push and review the tab, its explicit permissions and the SINGLE demo limitation; preserve the separate local Payroll Items changes.
2. If requested, scope development-only schema alignment or publication of the excluded Payroll Items work separately; production work still requires distinct authorization.

Read `AGENTS.md`, `docs/codex-session-workflow.md`, branch/status and current code before continuing. Do not treat this handoff as authority for Git writes or deployment.
