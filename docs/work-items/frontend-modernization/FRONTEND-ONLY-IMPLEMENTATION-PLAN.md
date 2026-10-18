# Montero Dental — frontend-only implementation and preservation plan

**Stage 1 planning only**, 18 October 2026 (Asia/Manila). Direction 01 — [Pearl & Teal](PEARL-TEAL-DESIGN-SYSTEM.md) is approved. This plan authorizes no application implementation, commits, pushes, pull requests, merges or deployment. Follow [AGENTS.md](../../../AGENTS.md), the [frontend Skill](../../../.agents/skills/montero-dental-frontend-design/SKILL.md) and its [verification reference](../../../.agents/skills/montero-dental-frontend-design/references/verification.md).

## Accepted functional baseline and BUILD entry gate

Stage 1 verified live development `main` and checkout HEAD at `01c9d5e0f9c1e391ee5bb74a40879d83c864c4dc`, including merged PR #7. Draft PR #8 (`feat/c-emp-correctness`) is open/unmerged and separate. This is a development-copy baseline, not production or complete functional/security acceptance.

Every future BUILD must start from the **then-accepted functional baseline**, recheck branch/status/history and accepted dependencies, and record the exact baseline SHA. Do not blindly replay this SHA over newer accepted work. If the task pins a baseline and `main` differs, report the difference and stop before editing. If correctness work is later accepted independently, use its accepted baseline without copying or cherry-picking application changes from Draft PR #8.

Before each BUILD:

1. Obtain explicit authorization for that frontend increment. Record its acceptance criteria, exact **file allowlist**, protected executable regions, baseline evidence and required runtime checks in a task-specific handoff. An allowlisted PHP view is not blanket permission to edit its PHP/JavaScript.
2. Read the current instructions, setup and actual affected source. Check pre-existing changes and preserve unrelated work; stop on conflicts rather than resetting/stashing/overwriting. Branch/staging operations require their own authorization; styling approval does not authorize Git publication.
3. Capture the current visual/functional reference using authorized isolated fictional resources only. Existing bootstrap/jQuery mechanisms, endpoints, fields, permissions, navigation and financial/print output are the comparison baseline. Do not invent fixture data in the product or use real clinic records.
4. Confirm available tooling and verification coverage. No framework, build system or application dependency may be added. If a required change needs executable PHP/JavaScript, backend/security/API/database/workflow work, record it for separate owner authorization and complete independent approved styling.

## Seven sequential increments

The following are **candidate allowlists**, not current edit authorization. At each BUILD, select and explicitly record the exact necessary files from current source; additions need explicit scope authorization. Each increment also updates only its approved documentation/handoff. Every increment preserves all functional regions and completes the preservation checks below before review.

### 1. Visual foundation and shared design tokens

**Candidate application allowlist:** `public/assets/css/style.css` only.

Establish the approved tokens, system typography, canvas/surface/text, spacing, borders, radius and focus through scoped CSS. Map Bootstrap component variables explicitly and isolate screen decoration from print. Retain the 250px sidebar, 992px transition, measured header offsets, table overflow rules and reduced motion. Existing shell/login/employee scopes must remain independent; do not rewrite resets or add a token compiler.

**Acceptance:** computed primary/hover/active, control/focus and surface treatments match the design system; shared screens retain fitting geometry and all controls/columns; no functional or print-output drift. Run the shared regression sample because even a one-file CSS change can affect payroll/login. Review only this increment before continuing.

### 2. Sidebar, navbar, navigation styling and login

**Candidate application allowlist:** `public/assets/css/style.css`; `app/views/partials/admin-header.php`, `admin-sidebar.php`, `admin-navbar-top.php`, `admin-footer.php`; `app/views/module_hris/partials/hris_nav.php`; `app/views/module_payroll/partials/payroll_nav.php`; `app/views/login.php`.

Apply the bright sidebar and restrained teal selection, consistent navbar/footer and login treatment. Only static presentation within these PHP files is eligible. Preserve navigation arrays, predicates, URLs/current-state logic and all executable PHP; use scoped CSS for existing generated links and Bootstrap utilities. Preserve login CSP/security headers, session/lockout logic, POST `/`, email/password fields, CSRF, native validation and script-free rendering.

**Acceptance:** existing allowed links and current/ancestor states remain identical; desktop/sidebar/mobile overlay, keyboard disclosure, Escape/backdrop/focus and resize cleanup retain behavior. Login remains readable at 320px and short heights and retains its current authentication behavior. No `public/assets/js/main.js` edits.

### 3. Employee management interface

**Candidate application allowlist:** `public/assets/css/style.css`; `app/views/module_hris/employees.php` (static presentation only).

Refine existing cards, toolbar, six-column table, action spacing, pagination and dialogs using employee-scoped styles. Preserve Employee No, Name, Branch, Date Hired, Status and Actions, the existing four action definitions and all visibility predicates. Generated rows/actions are JavaScript; style their existing classes without editing the generator. Preserve 1/2/3-column forms, small fullscreen dialogs, existing feedback, focus/dirty/pending lifecycle and values.

**Acceptance:** real clicks/taps/keyboard reach all existing permitted controls; search/Clear/five sorts/pagination match baseline, all columns remain locally scrollable, modal/form/request snapshots remain unchanged. Compensation policy remains unresolved and unchanged. Styling must not claim C-EMP-01/02/03 or A15 mutation correctness completed.

### 4. HRIS catalogs, attendance and leave interfaces

**Candidate application allowlist:** `public/assets/css/style.css`; `app/views/module_hris/branches.php`, `OrgUnits.php`, `Position.php`, `appointments.php`, `shifts.php`, `holidays.php`, `dtr.php`, `leaves.php`.

Apply the established card/table/form/feedback treatments to existing catalog, DTR and leave content. `/hris/appointments` remains the employment-type catalog. Preserve exact field/status meanings, options, dates/time representation, filters, attendance totals and leave actions. Attendance scanner behavior and import processing are not authorized here; scanner presentation needs its own explicit file scope if requested.

**Acceptance:** relevant catalogs/DTR/leaves work through their current requests and permissions without new controls, data transformations or workflows; filters/selection and responsive tables match baseline. Do not repair attendance authorization or scope helpers.

### 5. Payroll screens and financial data presentation

**Candidate application allowlist:** `public/assets/css/style.css`; `app/views/module_payroll/allowances.php`, `cash_advances.php`, `commissions.php`, `compensations.php`, `deductions.php`, `incentives.php`, `partials/payroll_crud_view.php`, `payroll_run_index.php`, `payroll_run_view.php`, `payroll_employee_view.php`, `importView.php`.

Improve screen readability and alignment of existing monetary data, summaries, forms, tables and import feedback. Preserve currency/precision/rounding, values, cutoffs, calculation/status logic, import preview/validation/submit behavior and every financial action. Generated content/executable regions remain protected. Keep existing print markup/styles/output; no payslip redesign, employee-name repair, new calculation, total, chart or status transition.

**Acceptance:** read-only screen/financial-value comparisons and print preview/PDF comparisons show unchanged values, output and page breaks; all wide columns remain accessible. No process/load/finalize/reset/cancel/delete/import submission is authorized as a styling test. Missing write evidence is not a functional pass.

### 6. Administration pages and existing dashboards

**Candidate application allowlist:** `public/assets/css/style.css`; `app/views/users.php`, `tenants.php`, `branches.php`, `home.php`, `about.php`; `app/views/module_hris/hris_index.php`; `app/views/module_payroll/payroll_index.php`.

Apply shared presentation patterns without changing administrative fields/actions/visibility or inventing routes. Preserve existing dashboard placeholders and displayed counters/chart content exactly; no fabricated statistics, employee data, operational features or new dashboard calculations.

**Acceptance:** current content/actions/navigation and visibility match baseline across admin and module home pages. Keep the two Branch paths distinct. Existing unfinished tenant creation and unsupported destinations remain separate owner-review findings.

### 7. Final responsive, accessibility and functional regression review

**Default application allowlist:** none; documentation/evidence review first. Any presentation repair needs an explicit narrow CSS/static-view allowlist and reuses the accepted prior increment baseline. No functional repair authority follows from final regression review.

Complete combined viewport/keyboard/accessibility/state, navigation, read-only data/financial and print comparisons. Check representative main/HRIS/payroll/admin/login screens and the employee dialogs; verify no new console errors, hidden columns, unreachable actions or stale overlay states. Record coverage gaps and historical defects separately. A visual pass is not full workflow/security/persistence acceptance.

**Acceptance:** required changed-presentation checks pass with inspectable evidence; any unavailable assistive-technology/device/runtime coverage is explicitly BLOCKED or NOT RUN. Do not claim full WCAG conformance or all historical UI-01 criteria complete while required coverage/A15 gates remain unresolved. Present a consolidated review; require human authorization for any Git publication, merge or deployment.

## Functional preservation matrix

| Protected surface | Required invariant and verification method in future authorized work |
|---|---|
| Controllers/models/services/helpers/middleware/routes/core/database/config/dependencies | Byte/content comparison against accepted baseline; no modified or new files in these areas. Preserve existing versions and loading order. |
| Executable PHP and JavaScript | Review allowlisted views line by line against baseline; unchanged conditionals, loops/data definitions, escaping, permission checks, event bindings, AJAX/response code and generated-row behavior. A token/AST comparison may assist but does not replace reviewing static form/DOM effects. |
| Forms and requests | Same controls/names/IDs/types/values/hidden CSRF, method/action, required/readonly/disabled/validation attributes, submit ownership, serialized keys, endpoints and response handling. Compare sanitized metadata without logging tokens or private payloads. |
| Permission-conditioned actions | Same visibility predicates and Super Admin semantics for the same session inputs; no additions/removals/ties to a different permission. Client visibility is not server authorization proof. |
| Navigation | Same existing destinations/URLs, direct/deep link behavior, current/ancestor matching and module/tenant/permission visibility. No new route or redirect. |
| Search/filter/sort/pagination | Same query keys/defaults/page size, sort directions, selected context, result counts and request behavior. Styling must not replace server pagination or hide columns. |
| Modals/confirmation | Same IDs/Bootstrap targets, lifecycle, real pointer/keyboard activation, Escape/backdrop, focus entry/trap/return, Stay/Discard, Delete Cancel, pending guards and retry behavior. Wrappers must not move controls out of their forms. |
| Financial/print output | Same values, formatting, snapshots, rates, cutoffs/statuses, calculations and print content/page breaks. Compare existing print output, including known defects; no new financial summaries or rounding. |
| Persistence | No frontend-triggered write behavior changes. Authorized read-only browsing should leave business data/schema unchanged; record sanitized read-only comparison results if available. Do not run mutations to prove styling preservation; separate approved write testing is needed for persistence claims. |

## Legacy limitations — separate owner-review items

The historical [UI-01 specification](UI-01-SPEC.md) and [UI-01C handoff](UI-01C-HANDOFF.md) identify these limitations. The two permission observations below were rechecked in current Stage 1 source; other runtime findings remain historical/source-reported and are not new runtime test results.

| Owner-review item | Evidence / scope boundary |
|---|---|
| Employee page/list permission mismatch | `routes/hris-routes.php`: page `/hris/employees` requires `hris.employee.view`; list `/hris/employees/getallemployees` requires `hris.view`. A user may reach the page without list access. Do not change predicates, routes or navigation visibility to mask it. Owner must review the intended policy separately. |
| Compensation action visibility/access | `app/views/module_hris/employees.php` renders Compensation with `action(true, ...)`, independently of Edit/Payroll permissions. HRIS compensation routes have no per-action compensation permission in the existing HRIS auth/tenant/module group. Preserve this legacy behavior; it is not a security endorsement. Owner must authorize a compensation matrix and any C-EMP-03 repair separately. |
| C-EMP-01/02/03 and A15 | Validation/payload alignment, response truth/persistence, route/query/CSRF and tenant/branch issues need independent correctness/security work. Draft PR #8 is unmerged and must remain untouched; inspect only as needed, never import its application patch or broader authority. |
| Existing placeholders/unfinished features | Dashboard counters/chart placeholders, unsupported destinations, incomplete tenant creation and payroll lifecycle/finalize/adjustment gaps stay unchanged. No invented metrics, operational statuses or routes. |
| Payroll/print/import observations | Historical employee/company fallback labels, two-page/blank-space payslip and import-validation/submit observations remain separate owner items. Preserve output; do not fix financial or import behavior under visual authority. |
| Verification gaps | Historical restricted-role/two-tenant fixtures, real screen reader, physical devices/other browsers, native zoom and real writes have incomplete coverage. Prior assertions are not fresh evidence or approval to create fixtures/mutate data. |

## Review, rollback and continuation

Use **PLAN → SPEC → BUILD → VERIFY → REVIEW** for each increment. Run only checks appropriate to its authorized scope and label PASS/FAIL/BLOCKED/NOT RUN precisely. Preserve historical A/B/C documentation; Pearl & Teal changes the future visual direction, not the functional baseline or correctness gates.

Keep a single handoff per approved increment, with baseline, explicit files/protected regions, actual checks, out-of-scope findings and next action. Propose rollback as the smallest scoped presentation diff compatible with later accepted work; do not restore whole old files over newer fixes. No database restore, reset, force push or production transfer belongs to a visual rollback.

**Next recommended BUILD:** increment 1, visual foundation/shared tokens, with `public/assets/css/style.css` as its sole candidate application file. It requires separate user authorization, a freshly verified accepted baseline, exact scoped selectors and runtime/print preservation evidence. Stage 1 stops at the [handoff](STAGE-1-HANDOFF.md) for review.
