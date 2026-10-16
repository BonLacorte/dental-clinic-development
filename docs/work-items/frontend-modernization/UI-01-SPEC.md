# UI-01 specification — approved revision 2

**Milestone:** Responsive Admin Foundation + Employee Screen Pilot

**Stage:** Approved SPEC; documentation-only handoff prepared for review. BUILD and Git write operations are not authorized.

**Date:** 16 October 2026, Asia/Manila

**Source baseline:** `ecec12a`, branch `work`; repository source rechecked on 16 October 2026. Working tree/index were clean before these two documentation additions.

**Supersedes:** revision 1 for scope, defaults, ownership and sequencing. This is the complete revision-2 specification carried into the repository with the user’s subsequent approval decisions and sanitized written evidence. Original baseline evidence remains unchanged.

## 1. Objective, constraints and approved defaults

Deliver three independently reviewable presentation increments: **UI-01A Shared responsive foundation**, **UI-01B Login and navigation**, and **UI-01C Employee screen pilot**. Existing working flows, routes and data contracts remain the source of truth.

Preserve PHP/custom MVCLite, MySQL/MariaDB, Bootstrap 5, jQuery, AJAX, PHP views, controllers/models and routing conventions. Keep existing dependency versions, asset loading sequence, request methods/URLs, database schema, field meanings, tenant boundaries and financial semantics. No framework migration, new build system, dependency installation/upgrade or global response-contract rewrite. No production access or deployment. A specification/branch/PR plan does not authorize creating branches, editing application files, committing, pushing or merging.

The user approved revision 2 and **D1–D7**, with the refinements below. This approves the design specification; the current authorization covers preparing these documentation files only. It does not authorize BUILD or Git write operations.

| Default | Approved revision 2 interpretation |
|---|---|
| **D1** | Bootstrap `lg` breakpoint **992px**; retain **250px** desktop sidebar; overlay navigation below `lg`. |
| **D2** | Existing Bootstrap blue for primary administrative actions; darker accessible teal for branding/login, proposed `#0F766E`; system font and consistent spacing. Contrast must be measured in actual states. |
| **D3** | Retain employee columns and the four action definitions. Render actions according to existing authorization policies; four actions need not appear for every user. Do not invent compensation rights. |
| **D4** | Confirm before discarding dirty Add/Edit forms through controlled dismissal paths using an accessible **in-dialog Stay/Discard** confirmation, initially focusing Stay editing. Use current Bootstrap/jQuery/native browser capabilities only; no new dependency. |
| **D5** | Hide genuinely unsupported destinations; expose supported existing routes and preserve working URLs and current authorization policies. Do not create unsupported pages or routes. |
| **D6** | Keep C-EMP-01/02/03 separate. Compensation authorization stays unchanged until explicitly approved. No security/validation changes hidden in styling diffs. |
| **D7** | Preserve sanitized **written** findings and a limited **15-item screenshot manifest** in this specification. Screenshot images remain outside the repository; do not commit them yet. Preparing these two documentation files is approved; staging, committing, pushing and publishing evidence are not. |

The visual baseline established the real small-screen failures: Employees was 798px wide at a 375px viewport and 548px after sidebar collapse; login used an approximately 980px layout viewport; employee Add/View/Edit pointer activation failed on mobile; tablet dialogs were cramped. Search/sorting worked. Error and loading captures included controlled fault injection. Programmatically opened mobile dialogs are inspection evidence only, not passing user interactions.

## 2. Split, dependencies and common exclusions

| Increment | Owns | Does not own | Proposed effort |
|---|---|---|---|
| **UI-01A** | Shared shell structure, shrinking/overflow, responsive navbar/sidebar, basic shell keyboard controls, shared typography/spacing/focus conventions | Login redesign, navigation destination/current-page policy, employee-specific forms/state, any backend change | 2–3 developer days |
| **UI-01B** | Login presentation/branding and navigation labels/destinations/current state/established visibility | Authentication logic, new routes, sidebar mechanics already owned by A, employee actions/CRUD, payroll business pages | 1–2 days |
| **UI-01C** | Employee table/actions, dialogs, page-local feedback/read coordination, accessibility and correctness-contract integration | Controller/model fixes, compensation authorization, new financial rules, global AJAX framework | 3–5 days including targeted regression work |

Efforts include task-specific verification; allow another 1–2 days for combined acceptance and evidence as needed. Correctness tasks are estimated separately. These are planning estimates, not approval or commitments.

**Default dependency order: A → B → C.** B and C start from accepted development baselines containing their dependencies. A/B can be accepted independently of employee correctness repairs. C's presentation can be reviewed separately, but its mutation acceptance remains gated by independently approved correctness work. Do not represent a partial visual pass as all 18 criteria passing.

All three exclude payroll calculations, rounding/cutoffs/status transitions, compensation values/rate rules, schema changes, import/payslip/dashboard redesign, broad CRUD consolidation, new permission codes, tenant/branch switchers, global auth/session/middleware redesign, framework/build/dependency changes and production actions. Shared CSS regression work may repair only foundation-induced problems; unrelated existing business/print defects remain separate.

## 3. UI-01A — Shared responsive foundation

### Objective and affected files

Provide a viewport-fitting shell whose navigation, account controls, content and existing tables remain reachable. One sidebar DOM tree serves desktop and offcanvas modes.

| Affected file | A-owned change |
|---|---|
| `app/views/partials/admin-header.php` | Add scoped `admin-shell` body class, skip link/focusable content target; preserve existing wrapper IDs and include structure. Keep content wrapper a div, avoiding nested main elements in existing views. |
| `app/views/partials/admin-navbar-top.php` | Responsive brand/toggle/account structure with meaningful control names and escaped user name; keep existing `/home` and `/logout` behavior. B owns final brand text. |
| `app/views/partials/admin-sidebar.php` | Bootstrap offcanvas-compatible structure, close control, native Modules button/disclosure, internal scrolling and focus behavior. Preserve existing link contents/current-state logic until B. |
| `app/views/partials/admin-footer.php` | Paired wrapper closures and fitting/wrapping footer; preserve asset order and optional DataTables loading. B owns placeholder brand-text replacement. |
| `public/assets/css/style.css` | Scoped shell font/spacing/focus and sizing; content/flex/card/table shrink constraints; responsive sidebar geometry, viewport-safe toast width. Do not rewrite all element selectors. |
| `public/assets/js/main.js` | Existing jQuery plus Bootstrap Offcanvas lifecycle, desktop toggle, ARIA synchronization and breakpoint cleanup. Keep global loader/toast function signatures unchanged. |

### Technical behavior and boundaries

- At **≥992px**, the sidebar is initially visible, 250px wide, below the normal 56px navbar, bottom-aligned to the viewport and independently scrollable. The desktop toggle changes its visibility and the matching content offset.
- At **<992px**, content has zero sidebar offset and navigation initially starts closed. The existing Bootstrap 5.3.3 Offcanvas component controls overlay/backdrop/body-lock/focus. Panel width is `min(280px, viewport width - 32px)` and its own header has an accessible close button. The overlay may cover the navbar; desktop mode never does.
- On crossing `lg`, use the Bootstrap lifecycle to close an overlay and release its backdrop/body lock before restoring desktop state. Returning below `lg` starts closed. Maintain any desktop collapse choice only within the current document; no localStorage/cookie/session preference.
- A click must activate one mode only; do not retain competing legacy transform and Offcanvas handlers. Closing returns focus to the trigger. Modules supports Tab/Enter/Space and expanded-state semantics. No duplicate navigation tree or new navigation dependency.
- Constrain shared flex descendants with `min-width:0` and available-width rules. Existing responsive table containers own necessary scrolling. Page-wide `overflow-x:hidden` is not an accepted fix for clipped content.
- Use the existing system font, normal 16px body text, 4/8/12/16/24/32px spacing conventions and visible focus. Preserve existing blue primary styles and dark sidebar; accessible branding color details belong to B. Avoid unqualified `.row`, `.modal`, `.card`, `input` or `button` overrides affecting legacy/scanner views.
- Target 44px shell touch controls; account menu supports long names without widening the document. No new tenant/branch/account route or switcher.
- At text zoom, header/content/sidebar offsets must accommodate actual header height rather than overlap. Keep 56px as the normal-size baseline; use scoped CSS/current jQuery resize handling if actual height varies. Honor reduced motion.
- A does not change routes, navigation rights, current-page selection or unsupported destinations. Those known issues must be described as B work, not silently fixed or counted as A failures. Likewise, employee-specific dialog/list issues are C work.

### Acceptance and verification

Own A02–A06/A08–A10 for the **shell**, and contribute A01/A16–A18. At 1440×1000, 768×1024, 375×812, 320px stress width and 991/992px boundary, verify correct mode, visible account/menu controls, no shell-caused document overflow (>1 CSS px tolerance), local table scrolling and no orphan overlay state after repeated resizing.

Use real clicks/taps/keyboard for toggle/disclosure/account; verify Escape, focus return, skip link, long user names, short heights, reduced motion and text zoom. Smoke-test login/Logout compatibility without altering login logic. Check dashboard, Users/Tenants, both Branch paths, DTR/Leaves, six payroll configuration pages, runs/details/employee and import; compare known print baseline for new CSS regressions. A may improve their container fit, but does not redesign their content or claim old defects fixed.

Use compatible PHP 8.3+ lint on changed PHP, diff/scope checks, installed browser tooling, console/network checks and read-only demo fingerprints. All implementation checks are **NOT RUN** now.

### Risks, rollback and branch/PR plan

Risks: global CSS leakage, conflicting transforms, backdrop/body-lock leftovers, changed account access and print offsets. Mitigate with scoped rules, one lifecycle/state model and cross-module sweeps.

Proposed branch: **`feat/ui-01a-responsive-foundation`**, from the authorized current development `main` baseline verified against `ecec12a`; PR target `main`, title **“UI-01A: Establish the responsive admin foundation”**. No branch/PR exists yet. Describe shell changes, out-of-scope known defects and exact checks in the PR. Do not combine B/C/backend changes.

Rollback A's matched partial/CSS/JS changes as a coherent reviewed unit. If B/C have subsequently been accepted, reverse dependents first or prepare a compatibility patch; do not blindly restore entire shared files. Recheck login/account access and employee/payroll/import shell behavior afterward. No database recovery is involved.

## 4. UI-01B — Login and navigation

### Objective and affected files

Make login readable at device width and navigation accurate, while preserving authentication, access rules and every working URL. Depends on A's shell structure.

| Affected file | B-owned change |
|---|---|
| `app/views/login.php` | Charset/viewport, main/h1, existing logo with alt, visible associated labels, current identity/password field names and autocomplete, responsive card structure. |
| `public/assets/css/style.css` | Login-scoped system font, card sizing, accessible darker teal branding and focus; navigation presentation only as needed. Reuse A sizing/spacing conventions. |
| `app/views/partials/admin-header.php` | Compute normalized path for display matching; do not redesign wrappers or global alerts. |
| `app/views/partials/admin-navbar-top.php`, `admin-footer.php` | Replace APP Name with Montero Dental; preserve A structure/controls. |
| `app/views/partials/admin-sidebar.php` | Correct current module/leaf state and established permission-based link visibility; hide unsupported destinations. Do not rewrite A offcanvas mechanics. |
| `app/views/module_hris/partials/hris_nav.php` | Correct Home/Employees/Organization/Attendance leaf/parent state, accessible dropdown controls and wrapping; visible Employment Types label on existing URL. |
| `app/views/module_payroll/partials/payroll_nav.php` | Correct Home/current state, hide Schedules, expose existing Runs and Attendance Import paths. No payroll page changes. |

**No `main.js`, controller, model or route edits are expected in B.** If a mechanical dependency defect needs a shared JS fix, document/review it against A before expanding B's file list.

### Technical behavior and boundaries

Login card is at most 400px, available width minus 32px, with content-driven height. Remove the fixed 525px grid/card-height constraint. Center only where height permits; allow ordinary vertical scrolling at short heights. Use 16px inputs/system typography, visible labels/focus, existing logo, and darker teal such as `#0F766E` with measured contrast. Existing Bootstrap blue administrative action styling stays unchanged.

Preserve POST `/`, email/password names, CSRF input, password masking, native required/email checks, login headers/CSP, lockout/session behavior and Logout URL. Use `autocomplete="username"` and `current-password`; add no login JS, show-password feature or CDN/bootstrap requirement. Existing invalid-login alert/lockout response remains a documented limitation; replacing it is a separate auth-feedback task.

Normalize only the pathname for **display matching**, ignoring queries and consistently handling a trailing slash. Exact leaf matches receive `aria-current="page"`; module/section ancestors may be visually active without falsely marking Home current. Prefix matches require a `/` boundary. Do not rewrite/redirect/rename routes. Expanded state describes an open menu, not merely an active child.

| Navigation | Preserved/exposed destination |
|---|---|
| Dashboard / About / Users / Tenants / Branches | `/home`, `/about`, `/users`, `/tenants`, `/branches` |
| HRIS Home / Employees | `/hris`, `/hris/employees` |
| Organization | `/hris/branches`, `/hris/offices`, `/hris/positions`, `/hris/appointments` — last label becomes Employment Types |
| Attendance | `/hris/dtr`, `/hris/leaves`, `/hris/holidays`, `/hris/shifts` |
| Payroll Home / Items | `/payroll` and existing six payroll configuration links |
| Payroll Runs / Attendance Import | `/payroll/runs` (including its existing run/employee descendants), `/payroll/attendance/import` |
| Unsupported | Hide Roles `/roles`, Settings `/settings`, Clinic `/clients`, Schedules `/payroll/schedules` and unsupported Reports destinations. Preserve all working routes and current route-specific authorization policies. |

Visibility follows each existing route's auth/tenant/module/permission requirements and exact Super Admin behavior in its middleware. Do not assume all routes in a module use the same permission. Users/Tenants already have `user.view`/`tenant.view`; HRIS Home and Employees differ. Do not hide an allowed supported descendant merely because its Home page requires a different permission; if needed retain the module as a labelled disclosure with links to allowed children and hide the inaccessible Home link. This does not create new authorization.

Do not invent compensation policy or use navigation hiding as a replacement for server checks. Both Branch paths remain distinct. B does not edit employee row-action policy; C owns those controls.

### Acceptance and verification

Own A07 and the login portion of A04–A06/A08–A10/A16; contribute A01–A03/A17–A18. At the three baseline widths and 320px/short-height/zoom stress cases, verify readable labels/inputs/logo and no page-level horizontal overflow. Measure teal text/button/focus contrast. Test valid login, native invalid fields, existing invalid-credentials/CSRF/lockout handling and Logout without weakening CSP.

Exercise every enabled visible link for authorized demo roles: intended existing page or legitimate access response, not a dead 404. Test query-string/trailing-slash path matching, direct/deep links, active ancestors, keyboard dropdowns, restricted roles/module subscriptions and Super Admin behavior as actually defined. Preserve known unimplemented routes instead of building them. Fixtures for additional roles are fictional and separately approved; do not infer policy from only the demo administrator.

Run PHP lint, link/state matrix checks and shared-module regressions. All implementation checks are **NOT RUN** now.

### Risks, rollback and branch/PR plan

Risks: hiding legitimate descendant access, falsely marking parent Home current, login font/layout leakage, accidental CSP/auth changes. Mitigate with route-by-route policy comparison, scoped login CSS and actual login regression tests.

Proposed branch: **`feat/ui-01b-login-navigation`**, from development `main` after A is accepted/merged through the user's authorized workflow; PR target `main`, title **“UI-01B: Improve login layout and navigation accuracy”**. No branch/PR exists. Shared-file changes must be incremental on A, not a copy of the old files or duplicate A diff.

Rollback B's login/labels/path/visibility changes while retaining A's structure/resize logic. If C depends on B markup/current-path variables, remove/adapt C's dependent changes first. Recheck valid login/Logout and every working navigation URL. Do not roll back independently accepted server correctness fixes.

## 5. UI-01C — Employee screen pilot

### Objective and affected files

Provide a responsive, accessible employee list and dialogs using existing endpoints and contracts, with authorization-aware actions and honest page-local feedback. Depends on A/B; correctness fixes remain separate.

| Affected file | C-owned change |
|---|---|
| `app/views/module_hris/employees.php` | Employee main/page header, toolbar/table/pagination semantics, authorization-aware action rendering, unique labels/IDs, responsive dialog markup, focus/dirty-state handling, local loading/read errors and lookup readiness. |
| `public/assets/css/style.css` | Employee-scoped table, toolbar, field/dialog, touch-target and state styles. Reuse A/B conventions. |

**No shared `main.js` rewrite, new page script/dependency/build step, controller/model/route change, salary rule change or new permission belongs in C's presentation PR.** Keep existing inline-script conventions for this pilot. C-EMP endpoint/client repairs are separate review units even where they touch this same view.

### Columns and action visibility

Retain Employee No, Name, Branch, Date Hired, Status and Actions, plus four action definitions: View, Edit, Compensation, Delete. Keep server page size, default `created_at DESC`, allowed sort keys/request parameters, date values, employee fields and business values. Do not enable DataTables, convert rows to cards or add action-menu portals.

Use a constrained `.table-responsive` region; proposed minimum table width 48rem (adjust only with fixture evidence), no-wrap ID/date/status/action groups, readable name/branch wrapping, accessible caption/column headers and horizontal-scroll description. Table scrolling is keyboard/touch usable; outer page stays within viewport. The toolbar/pagination wrap without clipping. No column disappears on mobile.

| Control | Rendering rule |
|---|---|
| Add Employee | Existing `hris.employee.create` policy |
| View | Existing `hris.employee.view` policy |
| Edit | Existing `hris.employee.edit` policy |
| Delete | Existing `hris.employee.delete` policy |
| Compensation | **Preserve the existing legacy visibility/access behavior.** Its route group has HRIS auth/tenant/module requirements but no defined per-action compensation permission. Do not tie it to Edit or Payroll rights, remove/add a right, or change endpoint authorization without explicit C-EMP-03 approval. Record the unresolved policy/security limitation. |

Use the session permission map and exact existing middleware bypass semantics to reflect those policies; backend enforcement remains authoritative. The four actions are not guaranteed to appear together for all users. If a row has no permitted action, do not show misleading disabled controls; retain a clear empty Actions cell. All rendered actions have meaningful per-employee names, readable contrast and 44px targets. Remove repeated compensation-button IDs while preserving delegated classes/data IDs and existing route handlers.

Sorting announces actual state; Created At default gets a “Sorted by newest added” summary rather than a false visible-column state. Pagination preserves current search/sort and current-page semantics. Existing list responses lack total record count: use a loaded-page count and page count, never an invented total; no response expansion.

### Dialogs, forms and state

- Retain Add/Edit/View, Delete confirmation and the existing Compensation entry point. Financial fields/values and compensation write behavior are unchanged; basic sizing/naming/label fixes are allowed without asserting authorization is safe.
- Add/Edit fields use 1 column below 768px, 2 at 768–1199px, 3 above; group Identity/Contact/Employment. Fullscreen below 576px; otherwise scrollable body, up to 800px width subject to viewport clearance. Structure form/modal flex containers so header/footer remain reachable.
- Unique add/edit IDs, associated labels, required/optional hints and existing CSRF hidden fields. Create required hints mirror current rules: first/last name, branch, employment type, date hired. Preserve optional fields and server defaults; do not silently add required salary/contact/position/status policy.
- Deliberate focus entry after Bootstrap's shown event, complete Tab/Shift+Tab containment, named Close/heading, and return to initiating control (or stable employee-ID/list-heading fallback after refresh).
- Static editing backdrop. Pristine Add/Edit may close; dirty forms require inline discard confirmation **inside the same modal**, with Stay editing initially focused and explicit Discard. No nested modal/new dependency. Record dirty state after async record/options initialization, excluding token/hidden/read-only noise; preserve it through request failures. Pending saves block controlled dismissal. View/Delete Escape cancels/closes without mutation. Browser refresh/close confirmation is browser-dependent; no autosave or guarantee for forced tab closure is introduced.
- Delete names the selected employee and initially focuses Cancel; never submit through opening/closing/Enter on an unintended control. Existing soft-delete behavior is unchanged.
- Native attributes are usability aids only; server-side rejection is C-EMP-01. Show persistent summaries/field errors only from the separately approved contract, keep values on failure, and never close/reset on an ambiguous response.
- Use local table loading/status, not a new global blocking pattern. Preserve prior rows during refresh; mark prior-result context if refresh fails. Persistent Retry for list/detail/lookups; no false “empty” on network failure. A required/unresolved lookup blocks a save and never silently clears optional selected values.
- Abort/ignore superseded **read** requests using current jqXHR/request sequence; intentional aborts are not errors. Save requests are never replayed automatically; a lost response states that the save could not be confirmed. Disable pending submission once, release on completion, and retain inputs.
- Use existing escaping/`.text()` for dynamic names/errors, accessible error/status regions without duplicate announcements, no raw response-body logging. No generic global AJAX framework.

### Acceptance, verification and correctness boundary

Own A11–A15 and employee portions of A02–A10; contribute A01/A17/A18. Verify all widths/boundaries/zoom with real pointer/touch/keyboard operations. No forced/programmatic dialog opening can count as a pass. Test the four seeded employees, long names, no records/no matches, multiple pages, five sorts, rapid requests, lookup/detail failure/recovery, dirty dismissal, row focus restoration, and restricted-user action rendering.

Synthetic browser responses may test presentation, but must be labelled and cannot prove SQL scope or valid persistence. Real server mutation tests require independently approved correctness work and isolated fictional fixtures. Verify intended record deltas and unchanged payroll tables; do not reset the shared demo. Compensation opening/value preservation is a read/UI regression, not a security certification or permission change.

C presentation may be reviewed/accepted as a **presentation-only increment** with explicit unresolved correctness dependencies. **A15 stays GATED/NOT RUN until relevant correctness work is approved and verified.** The whole UI-01 milestone cannot be reported as passing all 18 criteria while A15 or required coverage is outstanding. C-EMP-03 does not block A/B; unresolved compensation policy is recorded separately and is not silently altered to obtain a green UI result.

### Risks, rollback and branch/PR plan

Risks: form ownership/serialization regressions, stale async options, focus loss after refresh, false success from legacy endpoints, accidental authorization changes and collisions with correctness edits to the same PHP view. Mitigate with narrow selectors/field preservation, scoped styles, explicit dependencies and separate review units.

Proposed branch: **`feat/ui-01c-employee-pilot`**, from development `main` containing accepted A/B. PR target `main`, title **“UI-01C: Pilot the responsive employee screen”**. If C-EMP repairs are accepted first, branch from a baseline containing them and reference their reviews; do not copy backend fixes into C or force simultaneous conflicting edits to `employees.php`. Keep blocked acceptance visible in any future draft PR.

Rollback C's employee view and employee-scoped CSS together, retaining A/B and independently accepted correctness changes. Selectively revert the actual C diff rather than restoring the entire old employee file over later security work. Recheck search, permitted actions, dialog opening/values, focus and valid write handling if already approved. No schema rollback; any mistaken data writes need separately authorized recovery, not a UI revert.

## 6. Independent correctness tasks — unchanged separation, narrower coupling

None is implemented or approved by adopting D6. Each needs a separate task, branch/review and verification; approving a presentation increment does not approve compensation authorization or database-contract changes.

| Task | Finding and proposed review boundary | Verification | Future branch/PR plan |
|---|---|---|---|
| **C-EMP-01** | Validator errors are returned but not consistently rejected. Read-only `employee_no` is sent although strict rules do not accept it. Specify narrow input/payload alignment and rejection before persistence. Preserve field meanings/defaults; no shared-validator overhaul. | Required/optional invalid input, unexpected fields, date order and valid direct requests; invalid requests cause no writes. | `fix/c-emp-01-validation` → development `main`, independently approved input-validation PR. |
| **C-EMP-02** | Legacy `{message,type}` failures can be mistaken for success. Specify endpoint/caller truth handling separately, preserving successful fields and established contracts unless an explicit compatible response amendment is approved. No UI-01 PR changes response semantics by assumption. | CSRF/permission/not-found/validation/server/malformed/network cases never falsely succeed or clear inputs; valid/no-op updates handled truthfully. | `fix/c-emp-02-response-handling` → `main`, after coordination with C-EMP-01; independent endpoint/caller PR. |
| **C-EMP-03** | Review actual route → handler → SQL guards, branch/tenant references and compensation CSRF/policy gaps. Existing tenant predicates must not be misrepresented as absent. **Do not change compensation authorization until its matrix is explicitly approved.** | Real two-tenant/branch/role fixtures, tampered IDs, direct requests and CSRF rejection; no unauthorized writes or data disclosure. | First a review-only task; `fix/c-emp-03-access-csrf` is a reserved proposed branch only if a specific repair is later approved. No pre-approved authorization patch. |

C-EMP-01/02 changes to requests/responses must document compatibility and inspect every affected caller. If a proposed fix changes a routing/database/API contract, present that amendment for approval instead of treating this spec as blanket authorization. C-EMP-03 policy choices are not invented from absent permission codes. Payroll calculations/schema/contracts are excluded throughout.

## 7. All 18 acceptance criteria: retained and allocated

The original A01–A18 requirements below are retained. Ownership identifies the tested surface; completion of one increment is not completion of the whole row. All implementation checks are **NOT RUN**, with A15 additionally **GATED**. No requirement has been dropped.

| Criterion | Preserved requirement | Allocation / gate |
|---|---|---|
| **A01 Stack/scope** | Existing Bootstrap/jQuery versions, route registrations, field/business semantics and dependency lockfiles remain unchanged except explicitly approved correctness tasks. No build system/schema change. | A/B/C: scope check in each PR; correctness changes only under separate approval |
| **A02 Desktop** | At 1440×1000, sidebar initially visible; toggle changes content offset without overlap. Navbar/account/footer fit. Employee toolbar and actions are usable without forced clicks. | A: desktop shell; B: nav/account labels; C: employee toolbar/actions |
| **A03 Tablet** | At 768×1024, sidebar initially closed; full content width available. Overlay opens/closes, scrolls and restores focus. Employee modal uses two columns with readable choices. | A: tablet shell/offcanvas; C: two-column employee dialog |
| **A04 Mobile** | At 375×812, login and shell render at device width with readable 16px inputs; primary actions/account access visible. Employee dialogs open through real taps/clicks; forms/footer actions are reachable with vertical body scrolling. No programmatic opening/force-click accepted as a pass. | A: mobile shell; B: login; C: actual employee taps/dialog footer access |
| **A05 Width/resize** | At 375/768/1440 and boundary widths 575/576/767/768/991/992/1199/1200, login/employee root document width does not exceed viewport by more than 1 CSS px. A 320px stress check also passes. Necessary two-dimensional table scrolling stays local. Repeated breakpoint crossing leaves no orphan backdrop/body lock/hidden focus. | A: shell/breakpoint cleanup; B: login width; C: employee page/table width |
| **A06 Reflow** | At 200% text zoom and 320 CSS px equivalent reflow, controls/text remain reachable, navbar does not cover content, and page content does not require horizontal scrolling except the data table region. At 375×667, long dialogs retain accessible header/body/footer. | A: header/offset reflow; B: login reflow; C: table and long-dialog reflow |
| **A07 Navigation** | Every rendered enabled nav destination resolves to an existing intended page or its legitimate permission response, never a dead 404. Query strings/trailing slash display matching do not break highlights. Home is not current on Employees. Approved role/module visibility verified. | B: destination/current-state and established navigation policy matrix |
| **A08 Keyboard** | Skip link, menu/disclosure/account controls, sorting, scroll region, pagination and row actions work with keyboard. Escape and dirty-form decisions follow the approved policy. Dialog focus enters, cycles, and returns without falling into background/browser chrome. | A: shell controls/skip link; B: login/nav; C: table, pagination, dirty-dialog focus |
| **A09 Names/semantics** | Every pilot field has a unique associated label; named dialogs/close/action buttons; decorative icons hidden; correct main/nav/table/column/current-page semantics; no repeated row IDs. | A: shell semantics; B: login/nav; C: pilot labels, actions, table, dialogs and IDs |
| **A10 Readability** | Computed contrast meets 4.5:1 normal text, 3:1 large text/control/focus graphics. Measured normal-size touch targets meet the proposed 44px target. No clipped long names/branches, and status meaning is textual. | A: shell focus/control sizing; B: login/branding contrast; C: pilot controls/text |
| **A11 List** | Search, Clear, all five visible sorts, default newest-first announcement, pagination and no-match states retain existing server behavior. Numeric IDs/dates/sort parameters are unchanged. Result count describes the loaded page, not an unavailable total. | C: list/search/sorts/pagination preservation and truthful loaded-page counts |
| **A12 Loading/races** | Slow initial/refresh loads display correct local state. Rapid searches/sorts cannot let an old response overwrite the latest result. Cancelled reads cause no error. No new uncaught JavaScript errors or duplicate event handlers. | C: local loading/read sequence; A/B additionally prohibit new console errors/duplicate handlers |
| **A13 Lookup/read failure** | A failed list/detail/lookup request provides named persistent feedback and retry; retry recovers without clearing entered values or silently losing selections. Old result context is explicit. | C: list/detail/lookup failures and preservation/retry |
| **A14 Form preservation** | Add/Edit/View/Delete/Compensation open the intended fictional record; values, read-only number, options, date fields and financial values remain unchanged by layout. Opening/closing causes no mutations. Dirty forms do not lose values silently. | C: all employee dialog opening/value preservation; compensation policy/values unchanged |
| **A15 Mutation correctness** | After C-EMP-01/02 approval, invalid input/token/denial/server/malformed response cannot show success or close/reset. Valid fictional CRUD yields one request and the intended scoped record change. No blind automatic mutation retry. | C integration; GATED by separately approved C-EMP-01/02; compensation policy not changed/certified |
| **A16 Login regression** | Valid login, native empty/email validation, existing invalid-credentials/lockout/CSRF feedback and Logout still work. Password remains masked; no CSP weakening or login script dependency. | B: complete login regression; A: account/Logout compatibility; C: regression smoke |
| **A17 Shared regressions** | Main/HRIS/Payroll home, Users, Tenants, both Branch paths, DTR, Leaves, six payroll configuration screens, runs/detail/employee and import remain reachable and structurally usable at all three baseline widths. No new shell-induced document overflow or hidden actions. Known business/print defects remain separately recorded. | A/B/C: cross-module sweep after each affected shared change; cumulative at final acceptance |
| **A18 Financial/data invariants** | UI-only browsing leaves all demo business tables unchanged; approved employee mutations change only intended records. Existing payroll snapshots, amounts, status/cutoffs and schema remain unchanged. Print preview shows no new regression from shared CSS. | A/B/C: data/schema/financial invariants; mutation deltas only under separate approved testing |

The source wording of A01 permits separately approved correctness work; it does not authorize that work. A14 includes compensation **opening/value preservation** under unchanged policy. A15 is employee mutation correctness, not proof that compensation authorization has been approved or fixed. A17 is no new foundation-induced regression, not a demand to redesign every existing payroll/import screen. Known baseline print/identity/calculation issues remain separately documented.

Additional test discipline applies to every increment:

- Use only approved isolated fictional development resources; review helper/configuration at BUILD start. No production credentials/data/access.
- Compatible PHP lint for changed PHP, diff scope/whitespace checks and no dependency/schema/route-contract drift.
- Installed Chromium/Playwright can provide browser evidence without adding an application dependency or test build system. Record unavailable real-device/other-browser/assistive-technology checks as blocked/not run. At least one real assistive-technology pass is required for the specified accessibility acceptance; do not claim WCAG compliance from DOM inspection alone.
- Baseline viewport matrix: 1440×1000, 768×1024, 375×812; boundaries 575/576/767/768/991/992/1199/1200; 320px reflow, 375×667 short height, 200% text zoom and reduced motion. Preserve 1 CSS px document-width tolerance and the data-table local-scroll exception.
- Read-only sweeps compare unchanged business tables. Approved mutation tests compare intended changes and unaffected tables, including payroll snapshots/amounts/statuses/cutoffs. No blind fixture reset or mutation retry.
- Record actual commands/status, screenshots, accessible names/states, computed contrast, viewport/document widths, console/network errors and fault-injection conditions. Never relabel historical evidence as a new test pass.

## 8. Separate branch/PR and rollback sequencing

The branches/PRs above are **plans only**; none was created. The target is the development repository's `main`, never production. Before any future Git work, verify the then-current base and outstanding user changes. A source approval does not imply permission to push/merge/deploy.

Default plan: one feature branch and one PR each for A, B and C, sequentially based on accepted `main` revisions. This avoids duplicating shared changes and keeps each PR reviewable. If the user later requests stacked draft PRs, set explicit dependency/base links, retarget/reconcile after dependencies land and re-run affected tests. Do not auto-merge, force-push or silently rebase other work.

Shared-file ownership is explicit: **A owns mechanics/layout; B changes login/nav content/state incrementally; C adds employee-scoped behavior/styles.** Styles remain in the existing stylesheet with clearly scoped selectors/sections; no asset pipeline. Employee correctness PRs coordinate their overlapping file edits and remain separate from C styling.

Each PR should name its approved scope, base commit/dependencies, intended files, acceptance rows/subchecks, actual verification, unresolved defects and rollback files. Keep a task-specific handoff per A/B/C using the existing template, linked from the milestone specification. The UI-01A handoff is [UI-01A-HANDOFF.md](UI-01A-HANDOFF.md); B/C handoffs will be prepared separately when authorized. Preserve sanitized written evidence here. Do not stage/commit/publish evidence or generate PRs during this documentation stage.

**Rollback dependency order:** normally C → B → A. Reverting A alone while retaining dependent B/C markup is not a safe plan. For a narrow defect use a separately reviewed compatibility patch if preferable. Revert only the increment's own matched PHP/CSS/JS changes and retain independently accepted correctness/security fixes. Restore coherent asset versions using current filemtime cache behavior; no migration/DB restore for presentation. Re-run the rollback smoke matrix and verify no stale overlay/body lock. This is a future procedure requiring authorization, not a command to execute now.

## 9. Sanitized documentation and limited screenshot manifest

The user approved repository preservation of sanitized **written** evidence, with screenshot images excluded from commits for now. Appendix A preserves the assessment findings and limitations; Appendix B is the limited 15-item written screenshot manifest. These documents are self-contained and do not require chat history, scratch files, image copies or environment snapshots to define implementation scope.

Durable documentation paths are:

- `docs/work-items/frontend-modernization/UI-01-SPEC.md` — complete approved specification, sanitized findings and limited manifest.
- [UI-01A-HANDOFF.md](UI-01A-HANDOFF.md) — standalone task handoff for the first increment.
- Future B/C handoffs belong alongside them when separately authorized; they are not created by this task.

A reviewed subset of original-pixel screenshots was prepared outside Git during SPEC review. It remains optional historical visual evidence, not a portable dependency of this handoff. No image is copied into the repository by this task. Manifest filenames below are identifiers, not links to repository assets. Per-image capture times were unavailable and are not invented. Preserve the tablet login image’s actual 768×1023 dimensions despite its requested 768×1024 viewport.

Any later image inclusion needs explicit approval and another pixel/metadata review. Permit known fictional demo identities only. Exclude source capture scripts, login credentials, raw browser/session/HTML/HAR data, database exports/fingerprints, environment IDs, host-specific paths and browser profiles. Preserve injected/programmatic/failed-interaction flags, source commit, browser/date, requested/actual dimensions and hashes. A future approved image package may use relative image links only after those assets actually exist.

A future separately authorized reviewed commit/push is required for reliable retrieval in another Cloud task. Until then these new documents are local untracked work, not a published checkpoint. No publishing service or new dependency is proposed. Future after-images must use separate IDs and implementation commits; baseline screenshots are neither mockups nor proof that UI-01 is implemented.

## 10. Approval record and remaining decisions

The user approved revision 2 and D1–D7 before this documentation handoff, including:

1. Preserve the established PHP/MVCLite/MySQL or MariaDB/Bootstrap 5/jQuery/AJAX/CSS/PHP-view/routing stack and contracts; no migrations, build system or new dependencies.
2. Leave compensation authorization unchanged pending independent C-EMP-03 policy review.
3. Hide genuinely unsupported destinations; preserve working routes and current authorization policies.
4. Use an accessible in-dialog Stay/Discard decision for dirty Add/Edit employee forms; no nested modal or dependency.
5. Permit C presentation work independently of correctness repairs, but keep complete functional acceptance gated by relevant C-EMP tasks. A15 remains GATED until C-EMP-01/02 approval and verification; no complete milestone acceptance while required coverage is outstanding.
6. Preserve sanitized written evidence in the repository; do not commit screenshot images yet.
7. Prepare only this specification and the UI-01A handoff now. Do not modify application source, schema, dependencies or configuration, or perform Git writes, PR creation, merge or deployment.

**Still open:** the compensation authorization matrix and any specific C-EMP-03 repair; independent approval of C-EMP-01/02 scope or contract amendments; BUILD authorization and subsequent Git/PR permissions. None blocks preparing these documents. UI-01A does not depend on resolving employee correctness or compensation policy. The next review is of this documentation, before any UI-01A implementation or Git write operation.

## 11. Current status and repository cross-check

**Documentation status:** prepared for review; application implementation NOT STARTED. Only `UI-01-SPEC.md` and `UI-01A-HANDOFF.md` are new repository files. No application/configuration/dependency/schema files, database records, branches, index entries, commits, PRs or deployment state were changed by this task. No production access or runtime startup was needed. All implementation checks remain **NOT RUN**; historical baseline results are labelled separately.

Required session references: [AGENTS.md](../../../AGENTS.md), [architecture](../../architecture.md), [project overview](../../../PROJECT_OVERVIEW.md), [session workflow](../../codex-session-workflow.md), [work-item template](../TEMPLATE.md), [Docker setup](../../DOCKER_SETUP.md) and [local setup](../../../LOCAL_SETUP.md). Current source takes precedence over older descriptive setup prose.

The following were rechecked directly against `ecec12a` during documentation preparation:

| Current source | Verified fact / effect on this specification |
|---|---|
| `app/views/partials/admin-header.php` | Device viewport already exists for the admin shell; body has no scope class. Opens `#wrapper`, `#content-wrapper` and `#content`; includes shared navbar/sidebar. Toast API is defined here, not in `main.js`. Preserve those integration points. |
| `app/views/partials/admin-navbar-top.php`, `admin-footer.php` | APP Name placeholders, fixed navbar, existing escaped user name and `/home`/`/logout` links. Footer closes the shared wrappers and loads jQuery 3.7.1 → Bootstrap 5.3.3 bundle → `main.js`, then optional DataTables 2.3.7. A preserves these behaviors; B owns final branding. |
| `app/views/partials/admin-sidebar.php`, `public/assets/js/main.js` | Fixed sidebar, exact raw request-URI active matching, non-button Modules disclosure, class-toggle-only sidebar behavior. A changes mechanics; B changes destinations/current state/visibility. |
| `public/assets/css/style.css` | Shared unscoped reset/font rule, body horizontal clipping, 250px sidebar, 56px navbar/content offset, fixed login geometry and no responsive shell mode. New rules must be scoped rather than broad legacy-element rewrites. |
| `app/views/login.php` | No charset/viewport meta; existing POST `/`, masked password, CSRF, native required/email checks, CSP denying scripts and current lockout presentation. B is presentation-only and preserves security/auth behavior. |
| `routes/web.php`, `routes/hris-routes.php`, `routes/payroll-routes.php`, `app/middleware/PermissionMiddleware.php` | Preserved navigation destinations are registered; unsupported listed destinations are absent/commented. Permission checks differ per route, use `permission_map` and an `is_super_admin` bypass. Employee list AJAX requires `hris.view`, while employee page/detail require `hris.employee.view`: B/C must record this pre-existing mismatch rather than change policy implicitly. |
| `app/views/module_hris/partials/hris_nav.php`, `app/views/module_payroll/partials/payroll_nav.php` | HRIS Home is always current; Payroll Home always carries `aria-current`. Employment Types retains `/hris/appointments`; Runs/import are supported routes missing from this payroll navigation. B owns these corrections. |
| `app/views/module_hris/employees.php`, `app/controllers/hris/EmployeeController.php`, `app/models/hris/EmployeeModel.php` | Six columns/four action definitions, inline AJAX, repeated compensation-button ID, client employee-number payload, validator/response concerns, page size 10 and newest-first default are present. C preserves list/business contracts; independent correctness reviews remain necessary. No write/security behavior was newly tested. |
| `app/views/module_payroll/importView.php` | Already contains a `<main>` inside shared content: A must keep `#content` a div and avoid nesting a new main around this view. |
| `compose.dev.yaml`, `.tools/cloud-env.sh`, `scripts/setup-demo.php` | Tracked fictional-demo Docker path exists; current ignored Cloud helper supports `start` and `compose`, handles session CA/proxy and uses the isolated demo. The helper is not guaranteed in future checkouts. No helper/startup/configuration change is needed for UI-01. |

The 18 acceptance-table rows are preserved verbatim from the complete revision-2 source, including allocations/gates. Repository paths and Markdown links are checked, and documentation is screened for accidental credentials/runtime identifiers. PHP/browser/mutation/accessibility tests are implementation requirements, not results of this documentation review.

## Appendix A — Sanitized historical frontend baseline

**Status:** historical assessment preserved as sanitized written evidence; no UI-01 implementation pass is claimed.

**Assessment date:** 16 October 2026, Asia/Manila

**Source commit:** `ecec12a`

**Data:** isolated fictional development demo only

**Browser:** Chromium 154.0.8037.57

This document is a sanitized derivative of the completed frontend baseline. It contains no production data, login credentials, environment identifiers, cookies/tokens, raw HTTP/DOM captures or database exports. It does not describe an implemented modernization or a new test pass. The original baseline remains unchanged.

### Method and evidence interpretation

The actual server-rendered PHP/MVCLite development application was inspected with existing Bootstrap 5.3.3, jQuery 3.7.1 and AJAX behavior. Desktop 1440×1000, tablet 768×1024 and mobile 375×812 viewports were used, with Chromium touch emulation on tablet/mobile. All six payroll configuration lists and representative shared/HRIS/payroll/import screens were included in the original assessment.

Authentication and an in-memory fictional CSV preview were exercised. No employee/payroll mutations, processing, reset/delete/finalize actions or attendance imports were submitted. Historical before/after verification found all **37 demo tables unchanged**. That is a baseline result, not verification of any later implementation.

The limited written manifest in Appendix B references 15 original-pixel screenshots, not all original captures; image assets are not included in this repository. Each entry preserves requested viewport and actual image size, source commit, browser/date, fixture classification, state, file hash and programmatic/injected flags. Per-image capture times were unavailable and are not invented. The tablet login image is 768×1023 despite a requested 768×1024 viewport.

Mobile Add/View/Edit pointer activation failed in the original employee layout. The selected mobile Add dialog was opened programmatically for inspection after that failure. It is **not** a successful interaction test. The selected mobile import preview used a programmatic sidebar collapse after a normal toggle failure. The list-error screenshot used an intentionally failed request. These limitations must remain visible when reusing the evidence.

### Findings relevant to UI-01

| Area | Verified baseline finding | Planned increment |
|---|---|---|
| Shared shell | 250px sidebar remains visible on mobile; content and account controls clip. Some documents remain wider than the viewport after collapse. | UI-01A |
| Login | Approximately 980px layout viewport causes tiny scaled content at 375px. Serif login font differs from Bootstrap system typography. Login label/button contrast is 3.94:1; focus has no outline/shadow. | UI-01B |
| Navigation | HRIS Home remains selected on Employees. Roles, Settings, Clinic and Payroll Schedules returned 404. Modules is not a native keyboard-operable control. | A mechanics; B state/destinations |
| Employee list | Four fictional rows load; search/Clear/no-match/sorting work. Document width is 798px at a 375px viewport, 548px after collapse. Tablet rows wrap heavily. Sixteen row buttons lack meaningful accessible labels; cyan View contrast is 1.96:1. | UI-01C, on A |
| Dialogs | Tablet Add fields remain in cramped three columns. Add form has no associated `label[for]` or required controls. Mobile dialog is about 1171px tall in an 812px visual viewport. Escape leaves it open. | UI-01C |
| Feedback | Employee list failure shows a transient unannounced toast and leaves old rows without persistent failure context. Delayed reads use a global overlay. | UI-01C page-local behavior |

#### Measured document widths with the sidebar initially open

These are CSS document scroll widths, not screenshot widths. Page-level clipping is not an acceptable responsive solution.

| Screen | 1440px viewport | 768px viewport | 375px viewport |
|---|---:|---:|---:|
| Main dashboard | 1440 | 768 | 417 |
| Employees | 1440 | 798 | 798 |
| Allowances | 1440 | 1020 | 1020 |
| Payroll runs | 1440 | 1044 | 1044 |
| Payroll run detail | 1440 | 1100 | 1100 |
| Mixed import preview | 1440 | 929 | 929 |

At 375px after collapse, run detail remains 850px and import preview 679px wide. These provide shared-foundation regression context; they are not authorization to redesign payroll/import workflows.

### Source-based correctness issues remain separate

- **C-EMP-01:** employee handlers do not consistently reject returned validator errors; the read-only employee-number payload conflicts with strict rules.
- **C-EMP-02:** some error response shapes can enter an incorrect success path. Repairing endpoint/caller handling is a separate review, preserving established contracts unless a compatible amendment is explicitly approved.
- **C-EMP-03:** route/handler/query authorization, branch/tenant references and compensation CSRF/policy gaps need independent review. Some tenant predicates already exist. Compensation authorization must not change before its policy is explicitly approved.

These write/security paths were not exercised in the baseline. User-interface visibility follows established policies but is not authorization enforcement. No safe compensation write claim follows from an improved dialog.

### Other baseline findings retained as context

Dashboard counters and HRIS/Payroll chart areas are placeholders. Payroll detail summaries, explicit status text, DTR summaries, server-side pagination, cutoff preview and import row validation are useful existing patterns.

Employee payroll details/payslip show a numeric employee fallback rather than the fictional employee's name, and the company heading falls back to a placeholder. A short Chromium A4 payslip spans two pages with substantial blank space. Import preview correctly identifies an invalid fictional row but keeps Import Attendance enabled. Payroll lifecycle/finalization limitations remain source-based and were not exercised.

These are separate follow-ups. UI-01 does not change payroll calculations, schema, financial values, compensation rights, printing logic, dashboard metrics or import semantics. Shared layout changes must create no new regression in them.

### Preservation and verification limits

Keep the PHP/MVCLite/Bootstrap/jQuery stack and existing request/database contracts. Preserve working search, table structure, summary hierarchy and preview-before-import behavior. UI-01A/B/C and all 18 acceptance criteria are defined above; no criteria pass is claimed by this evidence package.

Physical devices, Safari/Firefox, assistive technology, full focus cycling, high contrast/reduced motion/zoom, large data, restricted roles and valid write flows were not comprehensively verified in the baseline. A focus-boundary observation remains unverified; this evidence does not certify WCAG compliance. Any future acceptance run must use real user interactions, record unavailable coverage, and label synthetic/fault-injected conditions.

The screenshot copies contain known fictional demo names, clinic labels and `.test` identities only. They preserve visible failures and inspection limitations. Before any future repository inclusion, inspect both pixels and metadata again and retain the manifest. Do not include original capture scripts, browser state, raw findings dumps or database fingerprints wholesale. These written findings become portable only after a separately authorized reviewed commit/push. Image inclusion and any publication need separate authorization.

## Appendix B — Limited historical screenshot manifest (written only)

All entries: source `ecec12a`; Chromium 154.0.8037.57; capture date 16 October 2026, Asia/Manila; fictional isolated demo with four seeded employees. Exact per-image times are unavailable. Original-pixel copies were reviewed during the prior assessment; pixels are not re-inspected in this documentation task. SHA-256 identifies the historical image bytes, not an included repository file. No image is a UI-01 acceptance pass.

| ID | Historical filename | Route | Requested → actual image (px) | Interaction state / limitation | SHA-256 |
|---|---|---|---|---|---|
| BASE-UI-01 | `login-1440.png` | `/` | 1440×1000 → 1440×1000 | Blank login form; ordinary interaction, no injection | `9911b1e92b183290b641ecf21e8faac5a56810190d0462723a58651149a1ec3b` |
| BASE-UI-02 | `login-768.png` | `/` | 768×1024 → 768×1023 | Blank login form; scaled layout; ordinary interaction, no injection | `b72bfdb827f0a4cf4f3b14fba3d78cf8a11dd758b4136754cbd423b154295218` |
| BASE-UI-03 | `login-375.png` | `/` | 375×812 → 375×812 | Blank login form; scaled layout; ordinary interaction, no injection | `fa3bad7d38905c8e383087c9bed9b9bdd4162739ec0e32cad0bb387c020db9d8` |
| BASE-UI-04 | `employees-1440.png` | `/hris/employees` | 1440×1000 → 1440×1000 | Four fictional rows; sidebar open; ordinary interaction, no injection | `de346d90d3cbfb987deeae9365486e20615bec1b4b63eb2f98e64bef40ca363e` |
| BASE-UI-05 | `employees-768.png` | `/hris/employees` | 768×1024 → 768×1024 | Four fictional rows; sidebar open; wrapping; ordinary interaction, no injection | `79a69515ab68c45305e41cb018b6f39a2a25037702dcccdf006c97aa27e7f4a5` |
| BASE-UI-06 | `employees-375.png` | `/hris/employees` | 375×812 → 375×812 | Four fictional rows; sidebar open; clipping; ordinary interaction, no injection | `d3e57ba685397b1125848deb054efcf2e48aea6e5966210202939e196c78cfc4` |
| BASE-UI-07 | `employee-add-modal-1440.png` | `/hris/employees` | 1440×1000 → 1440×1000 | Add dialog opened normally; no save; ordinary interaction, no injection | `2b60926241b07dc285499f0ba87137ece49a0befe7c36fe5e848ad7360a34cbf` |
| BASE-UI-08 | `employee-add-modal-768.png` | `/hris/employees` | 768×1024 → 768×1024 | Add dialog opened normally; no save; ordinary interaction, no injection | `28da75c0eaea474bf928f2934705c00536daf45f7e7f35f0ee311ff00844c591` |
| BASE-UI-09 | `employee-add-modal-375-inspection.png` | `/hris/employees` | 375×812 → 375×812 | Inspection-only dialog; ordinary trigger failed; no save; **programmatic setup, ordinary interaction failed** | `8bc612ccb942511a645d87afe5138fe866246a6a4269aaf28f40904513a511ba` |
| BASE-UI-10 | `employee-edit-modal-768.png` | `/hris/employees` | 768×1024 → 768×1024 | Edit fictional employee; opened normally; no save; ordinary interaction, no injection | `975c177f4ee52025ffcb002b4648c9eb6e69ca64e52e16f8d0f3f6e254db5b18` |
| BASE-UI-11 | `employees-no-results-1440.png` | `/hris/employees` | 1440×1000 → 1440×1000 | Search with no matching fictional employee; ordinary interaction, no injection | `69faa37c75eb53eaec988add79f8a7b193eb7c6e98dd6e92076f3ba308abd5be` |
| BASE-UI-12 | `employees-network-error-1440.png` | `/hris/employees` | 1440×1000 → 1440×1000 | Controlled list request failure; historical rows retained; **injected network failure** | `5b76e25bfac7f28e657249b9c2db54cd8501afc55ee4c6139d4263e02cb003ca` |
| BASE-UI-13 | `hris-organization-menu-375.png` | `/hris` | 375×812 → 375×812 | Organization dropdown open; mobile shell clipping remains; ordinary interaction, no injection | `d1d43fee93f34d2006f10fddd3a5afa46e7cb95c413d870eafae2b30319a04aa` |
| BASE-UI-14 | `payroll-runs-768.png` | `/payroll/runs` | 768×1024 → 768×1024 | Existing fictional draft; shared responsive regression context; ordinary interaction, no injection | `e1aa5311c60aba4e0003309b88660c4a6171dd0f33fddceaf03c703e23e166b5` |
| BASE-UI-15 | `import-preview-mixed-sidebar-collapsed-375.png` | `/payroll/attendance/import` | 375×812 → 375×812 | Three fictional preview rows; one invalid; sidebar programmatically collapsed; no import; **programmatic setup, ordinary interaction failed** | `ad1f824e4ca9e74802add9e6724181f24172f6421bcd1e620e10e96de5bb4ba8` |

Keep this historical manifest distinct from future implementation evidence. Re-capture missing states against the actual implementation rather than depending on a previous task’s scratch directory.
