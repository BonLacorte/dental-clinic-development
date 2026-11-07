# Task: Employee profile page

## Identity and status

- **Task ID:** md-employee-profile
- **Branch:** `feat/employee-profile`
- **Status:** `READY_FOR_REVIEW` - user accepted the profile and requested publication to personal GitHub `main`; all three new profile SELECTs now match the supplied schema. Current syntax, SELECT-only regression and local HTTP checks pass. Shared date-validator warnings and earlier browser coverage gaps remain separate follow-up items.
- **Last reviewed (Manila):** 2026-11-07
- **Source baseline:** `b39bfed`; initial tree was clean on `main`.
- **Authorization:** User approved the functional profile scope, inline JavaScript and local testing, and on 2026-11-07 explicitly requested committing/pushing the completed profile including query fixes into the personal repository main branch. Publication is authorized for this task; production access/deployment remains outside scope.

## Goal and implementation

Open an employee profile from the employee list, using Pearl & Teal. Show `HRIS · Employees · Employee Name`, Overview, Assignments and Details & Edit. Attendance, Leave, Payslips and Financial remain disabled navigation text. Exclude Statutory and Site cards.

- Overview shows actual employment information and current effective compensation.
- Assignments shows department, shift and payroll schedule. Leave-package and permission-group cards explain that the current employee records have no corresponding assignments; no business policies or values are invented.
- Details & Edit reuses existing employee fields and update contract, with lookup retry, dirty/discard/pending guards and explicit success handling.
- All profile JavaScript is inline in the profile view per the user's 2026-11-07 request. The original standalone file was removed; the relocation preserved its content and permission condition exactly.

## File scope

### Created

- [employee_profile.php](../../app/views/module_hris/employee_profile.php): profile page, form and inline JavaScript.
- [Schema comparison report](md-employee-profile-schema-comparison.md): schema-only comparison of the supplied snapshot and actual running demo metadata, with reproducible profile query failures.
- This task handoff, based on [TEMPLATE.md](TEMPLATE.md).

### Modified

- [employees.php](../../app/views/module_hris/employees.php): employee-name links, row profile attributes/click navigation and View details navigation. Existing create/edit/delete/compensation dialogs, list requests and print columns remain in place.
- [EmployeeController.php](../../app/controllers/hris/EmployeeController.php): profile action, validated ID, session tenant, assigned-branch checks under the existing empty-list/all-access policy, not-found response, edit permission and CSRF.
- [EmployeeModel.php](../../app/models/hris/EmployeeModel.php): dedicated tenant-scoped employee, effective compensation and shift reads. Current employee shift takes precedence over an active branch default.
- [hris-routes.php](../../routes/hris-routes.php): profile route in existing auth/tenant/HRIS middleware with employee view permission.
- [style.css](../../public/assets/css/style.css): scoped profile styling, responsive grids, forms and focus treatment.

No shared validator, existing mutation handlers, database/schema/seed, dependency, production configuration or payroll calculation changes were made. Experimental Dockerfile mirror changes from the first startup attempt were reverted completely.

## Existing isolated local runtime (observed 2026-11-07)

- **App:** http://127.0.0.1:8085/hris/employees
- **Profile:** http://127.0.0.1:8085/hris/employees/profile/1
- **Environment:** existing isolated Compose demo, PHP 8.3.36 and MariaDB 10.11, fictional seeded records only.
- **Containers:** `montero-dental-dev-app-1` running; `montero-dental-dev-db-1` healthy.
- **Networking:** app bound to host loopback port 8085; database has no host-published port. Port 8000 was already occupied and was left alone.
- **Start task:** ignored local `.vscode/tasks.json` includes `Start local demo on port 8085`, which runs `env MONTERO_WEB_PORT=8085 docker compose -f compose.dev.yaml up -d --no-build --pull never`.
- **Recovery history:** the earlier attempt stalled on downloads and Docker disconnected while Windows C: was full. On this run, C: had 45 GB free, Docker responded, a complete Debian download took 0.10 seconds, and the existing image built from cached layers in about 3 seconds. Six locked Composer packages installed and the fictional demo seeded successfully. No Composer update, platform-check bypass, alternate PHP image, Docker cleanup or host data deletion was used.
- Demo credentials remain documented in [LOCAL_SETUP.md](../../LOCAL_SETUP.md); no production credentials were used.

## Earlier implementation verification (historical; limits retained)

| Check | Result | Actual evidence and limits |
| --- | --- | --- |
| Changed PHP syntax | PASS | `php -l` inside the running app for controller, model, employee list/profile views and HRIS routes; all five passed after the inline JavaScript move. |
| Inline JavaScript relocation/syntax | PASS | Earlier exact-content comparison and `node --check` via stdin passed. No separate JS asset remains. |
| Login and employee list | PASS | Actual demo login succeeded; list returned HTTP 200 with four fictional employees; page-error listener recorded no JavaScript exceptions during list load. |
| Employee row navigation | PASS | Real pointer click on Ana Demo's branch cell opened her profile. |
| Profile views | PASS | Overview, Assignments and Details & Edit loaded in the browser. All four seeded employee overview requests returned HTTP 200 and contained employment/compensation cards. |
| Profile JavaScript | PASS | Reloaded Details & Edit while recording page errors; no JavaScript exceptions; all five lookup controls loaded and dirty state settled correctly. |
| Save and persistence | PASS | Changed a fictional employee contact via the browser, saved, confirmed the new value through the actual model against MariaDB, then restored and saved the original value. Browser reload and model read confirmed restoration. Normal edit timestamps reflect these saves. |
| Date ordering/discard | PASS | A resignation date before hire date was blocked with the expected feedback via keyboard submission. Discard confirmation restored the empty resignation date and clean form state. |
| Invalid/missing IDs | PASS | Authenticated same-origin HTTP requests for `0`, `-1`, `abc` and `999999` returned HTTP 404 and the employee-not-found page. |
| Actual model/MariaDB checks | PASS | Tenant isolation for employee/compensation/shift, unknown employee, current compensation and employee shift returned expected results. |
| Database edge cases | PASS | In one isolated transaction, actual model calls excluded deleted employees, foreign-tenant branch labels, future/expired/deleted compensation; expired employee shift fell back to active branch shift; disabled branch fallback returned false. All temporary changes rolled back in `finally`; hashes of all five affected tables matched before and after that transaction. |
| Responsive form geometry | PASS | At viewport widths 1440, 768, 375 and 320, document horizontal overflow was 0; all 15 form controls and save control remained in the DOM. This is geometry evidence, not pixel-perfect or accessibility certification. |
| Shared date validator logs | FAIL | Browser saves logged `Trying to access array offset on false` twice per save at [validator.php](../../app/helpers/validator.php#L126). PHP 8.3 runtime confirmed valid date parsing with `DateTime::getLastErrors() === false`. Saving still persisted. No fatal PHP exception or SQL error was observed during profile checks. |
| Action-menu/View details pointer coverage | BLOCKED | Later automation clicks timed out at Playwright actionability/stability checks. Some injected keyboard/mouse actions did not reach page event listeners. Did not change application handlers or claim a menu pass. Manual verification remains necessary. |
| Browser request-context helper | BLOCKED | The integrated browser's `page.request` helper failed with unsupported `Storage.getCookies`. Used actual same-origin `fetch` for read-only HTTP status checks instead; no mocked server response. |
| Restricted-role/branch HTTP coverage | NOT RUN | Admin session and model-level tenant checks were tested; separate restricted user/tenant login fixtures were not created. |
| Other acceptance coverage | NOT RUN | Screen reader, physical devices, other browsers, full contrast/zoom/print regression, all failure/retry/pagination cases and other business modules were not fully tested. |
| Git whitespace/index | PASS | `git diff --check` passed; no staging or publication. |

## Production-snapshot comparison

The user supplied a read-only SQL snapshot for schema comparison. Extracted only CREATE/ALTER metadata and compared it with INFORMATION_SCHEMA from the running isolated demo. No production server was accessed, no dump was imported, and no application or database schema was changed.

The [comparison report](md-employee-profile-schema-comparison.md) records 51 snapshot tables versus 37 local tables, 36 shared tables and 31 shared tables with differing column presence. Four profile tables have a local `deleted_at` column absent from the snapshot: compensation, employee shift schedules, shifts and branch shifts. Employee `deleted_at` exists in both schemas and must remain filtered.

SELECT-only local replay of the actual profile query strings against NULL derived tables with exactly the snapshot's column names produced SQLSTATE `42S22` for compensation and both shift queries. The employee lookup's column references are compatible. Compensation runs before view rendering and is therefore a concrete candidate for the reported GET HTTP 500; correcting only compensation would leave the shift failures. Production error logs remain necessary to establish the exact first live exception.

The reconstructed demo schema's common metadata columns masked these incompatibilities. Previously passing synthetic deleted-compensation/shift checks establish only the old demo behavior and do not establish production soft-delete support. The final correction removes unsupported compensation/assignment/shift soft-delete predicates while retaining employee soft deletion, tenant/effective-date constraints and active branch selection. Wider demo schema alignment is separate; the supplied snapshot itself remains unchanged.

## Remaining finding and proposed fix

[validator.php](../../app/helpers/validator.php) is a shared existing helper outside the approved profile implementation file scope. At its date-validation condition, `DateTime::getLastErrors()` is assumed to be an array. For a valid date on this PHP runtime it returns `false`, so indexing warning/error counts emits warnings.

Proposed one-line fix for owner approval: check the warning/error counts only when `$errorsInDate !== false`, retaining the existing invalid-date rejection and formatting. Do not suppress warnings globally, change validation rules or alter date/business semantics.

The [frontend skill](../../.agents/skills/montero-dental-frontend-design/SKILL.md) requires separate authorization for functional edits beyond presentation authority; the user's separately approved functional file scope did not include this shared helper. No helper change has been applied.

## Final correction and verification, 2026-11-07

- Compensation no longer filters a missing `deleted_at`; both employee-shift and branch-default queries no longer filter missing assignment/shift `deleted_at`. Employee soft deletion remains enforced.
- The complete 217-line profile template is present in the playground. The inspected production snapshot had only a nine-line empty template; this snapshot was not modified. The user subsequently reported the live implementation working; that report is not an independent production test.
- The user requested a historical navigation/authentication explanation. See [the source-backed comparison](md-navigation-permissions-review.md). No authentication or permission backend changes were imported from the snapshot.

| Current check | Result | Evidence and limits |
| --- | --- | --- |
| PHP syntax | PASS | `docker exec montero-dental-dev-app-1` with `php -l` for controller, model, list/profile templates and HRIS routes; PHP 8.3.36, 64-bit. Existing isolated container only; no service start/install. |
| Profile JavaScript syntax | PASS | Extracted existing inline script and passed it through `node --check` on stdin. |
| Snapshot-shaped SQL | PASS | Extracted the four actual profile SELECT strings, replaced table sources with NULL derived rows exposing only supplied CREATE TABLE column names, and prepared/executed them using local demo PDO. All four passed; no dump import, production rows or schema writes. |
| Actual demo model reads | PASS | SELECT-only calls for four fictional employees; employee/compensation/shift foreign-tenant exclusion; branch fallback using an unmatched employee ID and a real demo branch; absent-shift false result. |
| Local HTTP smoke checks | PASS | Python urllib cookie session: fictional demo login, employee list, all four profile overviews, Assignments and Details & Edit on the first employee returned 200 with expected HTML. IDs `0`, `-1`, `abc`, `999999` returned 404 with not-found content. Target was loopback port 8085 only. |
| Whitespace | PASS | `git diff --check`. |
| New browser/edit/save coverage | NOT RUN | Current smoke checks inspect server responses, not browser interactions. Earlier browser/save evidence is retained above and was not repeated. |
| Live production verification | NOT RUN | No live server connection or deployment. |

## Next concrete steps

1. Finish the authorized personal-repository publication and verify remote `main` contains the reviewed feature commit. The publication result will be reported in the chat; this pre-commit handoff does not claim it already happened.
2. Review [the navigation-permissions comparison](md-navigation-permissions-review.md) with the senior developer. Its listed restricted-role AJAX mismatch and snapshot authentication differences are separate work, not changes included in this feature.
3. Address the shared date-validator warning only under a separately approved helper fix; repeat fictional save/restore and restricted-role/browser checks before expanding verification claims.

Read [AGENTS.md](../../AGENTS.md), [architecture](../architecture.md) and [continuity workflow](../codex-session-workflow.md) when continuing. Verify actual branch/status and preserve unrelated work. This handoff does not authorize production access/deployment, database reset or Docker pruning.
