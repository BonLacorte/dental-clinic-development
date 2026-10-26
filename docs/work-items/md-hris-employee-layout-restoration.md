# Employee UI restoration — Option 1 review snapshot

## Identity, authorization and publication scope

- Task: `MD-HRIS-EMPLOYEE-UI-RESTORATION-SNAPSHOT`.
- Branch: `codex/md-hris-consistency-integration`.
- Parent snapshot: `91c8c630ccdb7d52f3a1d0678df3e242c144bcc2`.
- Owner approved **Option 1: UI-only review publication** independently of the unresolved Employee branch-access policy.
- Exactly three files are authorized for this snapshot: the Employee view, Employee-scoped stylesheet additions, and this handoff.
- One new review commit and one ordinary non-force push are authorized, using `fix(hris): restore employee layouts for visual review`.
- General Stage 3, authorization-policy implementation, other HRIS/backend changes, PR modification, merging and deployment remain unauthorized.
- The earlier frozen Stage 2 integration handoff remains unchanged.

## Published correction scope

1. `app/views/module_hris/employees.php`
   - Employee number beneath the name; desktop Employee, Position, Branch, Employment Type, Employment Status and Actions columns.
   - Organized mobile cards below 768px retain identity, number, status, Position, Branch, Employment Type, Date Hired and the same permitted actions.
   - Three-dot menu restores View details, Edit employee, Manage compensation and Remove employee, using existing permission predicates and existing dialog/request targets.
   - Redundant lower-left page-count text removed; pagination retained.
   - Add/Edit dialog field grouping restored to two desktop columns, with employee-number help and selected employee identity.
   - Compensation grouping restored to Pay Arrangement, full-width Salary Rate and Effective Period; label/help copy never converts monetary values.
   - Remove dialog restores employee identity, number and branch context.
2. `public/assets/css/style.css`
   - Append-only screen rules require `.employee-page.employee-layout-restored`; the body-portaled menu uses its unique Employee menu ID.
   - The entire published parent stylesheet remains an exact prefix. No shared tokens, shell breakpoints or existing print CSS are changed.
3. `docs/work-items/md-hris-employee-layout-restoration.md`
   - This UI-only scope, exclusions, verification evidence and remaining review requirements.

## Excluded Position backend change — preserved, not published

`app/models/hris/EmployeeModel.php` is expressly excluded from staging and publication. Its existing two-line working-tree addition remains intact and uncommitted for separate owner review:

```sql
p.name AS position,
LEFT JOIN hris_positions p ON p.id = e.position_id AND p.tenant_id = e.tenant_id
```

The excluded working file has SHA-256:
`d85e140acc3271e7555957351a1ea45f12359715277080df3da1c8b23b9e3d5b`.

The parent Employee list response does not supply `position`. The published UI therefore uses its existing truthful **Position unavailable** fallback. A supplied null/blank value means **Not assigned**; missing data is not presented as an unassigned position. No additional per-row detail requests or fabricated Position values are introduced.

## Preserved contracts

- Same list endpoint, `page/search/sort_by/sort_dir` request parameters, ten-row page size and server sort mapping. Mobile order controls use those existing keys.
- Same View/Edit/Delete permission predicates. Legacy compensation visibility remains unchanged; this does not certify its server authorization.
- Same 34 named form controls, names, IDs, options, validation/read-only attributes and form ownership.
- Same mutation endpoints, payload construction, response handling, dirty/pending guards and compensation calculations.
- Date Hired remains in forms, mobile cards, existing sorting and print data. Print-only number/hire-date cells retain the parent identifying/date fields; new cards/menus and screen-only Position/Employment Type cells are excluded using existing Bootstrap utilities.
- Nine unchanged functions compared against the parent: `dialogValues`, `dialogIsDirty`, `dialogReadStatus`, `populateChoices`, `loadDialogLookup`, `focusDialog`, `employeeMutation`, `submitEmployee`, `renderPagination`.
- No controller, model, route, middleware, database, configuration or dependency changes belong to this commit.

## Verification for this UI-only publication

All checks below used an isolated export under `/tmp/md-hris-employee-ui-only-snapshot/`. It contains the exact candidate Employee view and stylesheet, parent HRIS presentation helper, and the already-used Bootstrap/jQuery assets. PHP rendering strips only the three application includes and supplies fictional permission/CSRF helpers. It starts no application service and never loads EmployeeModel.

The excluded model addition remains present in the integration working directory. It is **not copied into or used by the verification package**. Exported UI-file hashes are checked against the index and commit, so the tests cover the published UI source with simulated responses, rather than claiming authenticated application acceptance.

| Check | Result | Evidence / limits |
| --- | --- | --- |
| PHP syntax | PASS | PHP 8.4.24 CLI, `-n -l`, on the isolated exported Employee view. |
| JavaScript syntax | PASS | `node --check` on the exported view script; only its PHP-generated permission expression is replaced for syntax parsing. |
| Full CSS parsing | PASS | css-tree 3.1.0 parses 69,319 bytes with no diagnostics or recovered raw nodes. |
| CSS preservation/scope | PASS | Exact parent prefix; 44 new rule groups, screen-only and Employee-scoped; referenced custom properties defined. |
| Form and protected-function comparison | PASS | All 34 named controls and nine protected functions match the parent contract. |
| Missing Position field | PASS, simulated browser | List responses omit Position for every record. Both table and cards show Position unavailable; missing and explicit-null fallback semantics differ correctly. |
| Desktop hierarchy and page-count removal | PASS, simulated browser | Six visible columns, number under name, retained pagination and no lower-left summary. |
| Menu/dialog behavior | PASS, simulated browser | Keyboard navigation/Escape/focus return, viewport containment, desktop/mobile action targets, four requested dialogs and correct identity context. |
| Responsive cards/table | PASS, simulated browser | Widths 320/375/575/576/767/768/991/992/1199/1200/1440px; correct switching and document overflow <=1px with long escaped fictional values. |
| Search/sort/pagination | PASS, simulated browser | Existing GET parameters, mobile order controls, page change and search checked. |
| Failure/empty states | PASS, simulated browser | Failed refresh retains prior records; retry and empty filtered results update both representations. |
| Existing permission predicates | PASS, source/simulated browser | Denied View/Edit/Delete options absent; legacy compensation visibility unchanged. Not proof of backend authorization. |
| Financial/date/print data | PASS, source/simulated browser | Salary label changes preserve numeric rate; Date Hired retained; print media retains original identifying/date/status fields. |
| Complete offline browser run | PASS | Chromium 154.0.8037.57; 13 checkpoints; no browser errors; all intercepted API requests GET. |
| Whitespace and staged scope | PASS | Working and staged checks pass; staged paths are exactly the three listed above, with the model excluded. |
| Authenticated branch authorization | UNVERIFIED / NOT APPROVED | Existing tenant-wide list/count behavior and list/detail permission mismatch remain unresolved. |
| Persistence, real print/PDF, physical devices, screen reader and actual 200% zoom | NOT RUN | Require separately approved local acceptance. No record-writing test was performed. |

Temporary evidence and reproducible command:

```text
node /tmp/md-hris-employee-ui-only-snapshot/browser-check.cjs
/tmp/md-hris-employee-ui-only-snapshot/browser-results.json
/tmp/md-hris-employee-ui-only-snapshot/css-results.json
/tmp/md-hris-employee-ui-only-snapshot/verification-inputs.json
```

The existing isolated PHP CLI requires command-local:
`LD_LIBRARY_PATH=/tmp/md-hris-stage2t-a/php/usr/lib/x86_64-linux-gnu`.

## Local visual acceptance and remaining gates

Retrieve this commit into the existing isolated fictional-data Docker review worktree, without changing its database configuration or volumes. Check out the exact UI-only commit and refresh Employees using an authorized fictional test account. Expect Position unavailable until a separately approved backend implementation supplies the field.

This snapshot is not approval of authenticated Employee branch authorization, compensation policy, server validation or persistence. The pre-existing list/count branch-scope gap, list/detail permission mismatch and C-EMP-01/02/03 dependencies remain separate owner-review items.

No Employee branch policy is implemented here. The excluded model addition must remain uncommitted in the cloud worktree, and the original checkout and existing PRs must remain untouched.
