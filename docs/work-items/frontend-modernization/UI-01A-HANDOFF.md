# UI-01A handoff — Shared responsive foundation

## Task identity, authority and verified starting state

**Task:** UI-01A, first increment of Responsive Admin Foundation + Employee Screen Pilot.

**Status:** DOCUMENTATION_READY_FOR_REVIEW; implementation NOT STARTED.

**Recorded:** 16 October 2026.

**Verified source:** `ecec12a`, branch `work`; tree/index were clean before the two documentation additions. Recheck the actual branch, HEAD and outstanding edits in every new task.

**Proposed future branch:** `feat/ui-01a-responsive-foundation`; development PR target `main`. Neither branch nor PR was created by this task.

**Authoritative specification:** [UI-01-SPEC.md](UI-01-SPEC.md), complete approved revision 2, including all 18 acceptance criteria, ownership, independent correctness tasks and sanitized baseline evidence.

The user approved the specification and D1–D7, then authorized **only documentation preparation**. This handoff does not authorize implementation, branch/index changes, commits, pushes, PR creation, merge or deployment. Wait for the user's review and explicit authorization of the next action. Do not modify application files merely because this document describes future work.

This document and the specification contain all task-specific requirements. No original conversation, scratch directory, screenshot assets, browser state or previous environment snapshot is required. Historical measurements are evidence of the old interface, not passing tests of an implementation.

Read [AGENTS.md](../../../AGENTS.md), [architecture](../../architecture.md), [PROJECT_OVERVIEW.md](../../../PROJECT_OVERVIEW.md), [session workflow](../../codex-session-workflow.md), [work-item template](../TEMPLATE.md), this handoff and the specification before work. Inspect current source; correct stale handoff claims rather than overwriting newer code. Documentation paths are repository-relative, so they remain useful if the checkout root changes.

## Goal and approved decisions

Deliver one responsive shared admin shell: usable navbar, one sidebar for desktop/mobile, fitting content/footer, consistent system typography/spacing, and accessible shell controls. Preserve current request/data behavior and downstream views. Estimated effort is 2–3 developer days including verification; combined milestone acceptance is additional work.

| Decision | Approved behavior and ownership |
|---|---|
| D1 | Bootstrap `lg` at **992px**; **250px** desktop sidebar, overlay below `lg`. A owns mechanics. |
| D2 | Preserve Bootstrap blue primary actions and dark sidebar; system font and consistent spacing. Accessible darker teal branding, proposed `#0F766E`, belongs to B; measure actual state contrast. |
| D3 | Preserve employee columns and four action definitions with established authorization policies. Employee rendering belongs to C, not A. |
| D4 | Accessible in-dialog Stay/Discard for dirty employee Add/Edit, initially focusing Stay editing; no new dependency. This is C work. |
| D5 | Hide genuinely unsupported destinations, expose supported existing routes, preserve working URLs and current authorization policies. B owns destination/visibility/current-page changes; A preserves existing link content/state. |
| D6 | Keep C-EMP-01/02/03 independently reviewable; leave compensation authorization unchanged pending separate approval. A requires none of these repairs. |
| D7 | Preserve sanitized written evidence in the repository. The specification contains the findings and limited 15-item written screenshot manifest. Do not commit screenshot images; no Git writes are authorized now. |

Preserve PHP, custom MVCLite, MySQL/MariaDB, Bootstrap 5, jQuery, AJAX, CSS, PHP views, existing controllers/models/routes and database contracts. Keep current dependency versions and asset order. No React/Vue/Angular/Laravel/Symfony/Tailwind, new build system, library, dependency installation/upgrade or architecture migration. No production access.

## Scope, file ownership and current implementation

After BUILD is authorized, the application allowlist for A is these **six existing files**. Update this handoff with actual results when authorized. Do not broaden the file list silently.

| File | Current source, verified at `ecec12a` | Intended A-owned change |
|---|---|---|
| `app/views/partials/admin-header.php` | Device viewport already present; unscoped body. Includes navbar/sidebar, opens `#wrapper`, `#content-wrapper`, `#content`. Defines `showToast(message, type="", duration=3000)` in the head; owns toast/loader markup. | Scoped `admin-shell` body class, skip link and focusable content target. Preserve IDs, includes, alert behavior and toast API. Keep `#content` a div, avoiding nested main landmarks in existing views. |
| `app/views/partials/admin-navbar-top.php` | Fixed dark navbar; APP Name links to `/home`; `#toggleSidebar`; escaped current name and `/logout`. | Responsive brand/toggle/account structure, meaningful names and synchronized ARIA; keep both URLs/name escaping. B owns final brand text. |
| `app/views/partials/admin-sidebar.php` | Fixed 250px panel; raw request-URI highlights; Modules is a div with `.nav-parent`; `.nav-group.open` reveals `.nav-child`. | One offcanvas-compatible DOM tree, labelled navigation, mobile close/header, native Modules disclosure and internal scrolling/focus. Preserve existing links, visibility and current-state calculations until B. |
| `app/views/partials/admin-footer.php` | Closes content/content-wrapper/wrapper; APP Name footer. Loads jQuery 3.7.1 → Bootstrap 5.3.3 bundle → `main.js`; optional DataTables 2.3.7 follows. | Keep paired wrappers and asset order/cache behavior; footer fits/wraps. B owns placeholder brand replacement. |
| `public/assets/css/style.css` | Shared reset/font rule, fixed 56px navbar, 250px sidebar/content offset, `height:100vh` sidebar starting at 56px; body `overflow-x:hidden`; unscoped footer/nav selectors; login styles share this file. | Scoped shell font/spacing/focus, flex/content/card shrink rules, desktop/mobile geometry, header offsets, viewport-safe toast width and reduced motion. Do not rewrite all legacy element selectors or login layout. |
| `public/assets/js/main.js` | Global `showLoader(text=...)` and `hideLoader()`; ready handler toggles `.collapsed`/`.expanded`; separate vanilla click listener toggles Modules. No responsive lifecycle or ARIA synchronization. | One jQuery/Bootstrap Offcanvas state model, desktop toggle, breakpoint cleanup, focus/ARIA synchronization and native disclosure behavior. Preserve global loader signatures and caller behavior; toast stays in the existing header. |

Keep `#toggleSidebar`, `#sidebar`, `#wrapper`, `#content-wrapper`, `#content`, `#toast` and `#page-loader` integration points. Preserve escaped PHP outputs and existing `filemtime` asset URLs. The shared content div is deliberate: `app/views/module_payroll/importView.php` already contains a `<main>`; introducing another encompassing main/role=main would create nested landmarks.

**Excluded from A:** login markup/geometry; final Montero Dental branding; supported/unsupported link policy, route matching and permissions; HRIS/Payroll subnav redesign; employee rows/dialogs/validation/security/async states; page-specific payroll/import/print redesign. No controller/model/middleware/route/helper/schema/seed/Composer/configuration/Docker edits. Do not change payroll calculations, financial values, compensation access, database/API contracts or scanner behavior. No duplicate sidebar, new assets, switcher, persistent sidebar preference or global AJAX framework.

If an A requirement genuinely cannot be met within this boundary, present the specific dependency/scope amendment for review. Existing B/C defects are not an invitation to implement them in A.

## Required behavior and state model

Use the **already loaded Bootstrap 5.3.3 Offcanvas** and jQuery 3.7.1. No additional installation or build step is needed. One sidebar DOM serves both modes; keep route visibility/state code distinct from its responsive mechanics.

| State / transition | Required observable behavior |
|---|---|
| Initial desktop, width ≥992px | Sidebar visible, 250px wide, below the navbar; content has matching offset. Normal navbar baseline is 56px. Sidebar extends to viewport bottom and scrolls independently so its final link is reachable. |
| Desktop toggle | Hide/reveal sidebar and change content offset together. No overlay, backdrop, modal focus trap or body-scroll lock. Expose the actual expanded state. Remember desktop collapse choice only for this document. |
| Initial tablet/mobile, width <992px | Sidebar closed; zero content margin reserved for it. Brand/toggle/current-user/Logout controls remain reachable. No backdrop or hidden focus left over from initial setup. |
| Open mobile navigation | Bootstrap manages backdrop, body-scroll lock and focus containment. Panel width is `min(280px, viewport width - 32px)`; labelled header and accessible close button. Overlay may cover navbar; desktop sidebar may not. |
| Close mobile navigation | Close button, Escape and standard backdrop dismissal use Bootstrap lifecycle. Return focus to menu trigger. Release body lock/backdrop; no focus retained in hidden navigation. |
| Cross 991/992px in either direction | Finish closing an active overlay through Bootstrap lifecycle before restoring desktop choice. Returning below `lg` starts closed. Repeated quick resizing during transitions must leave no duplicate instances, backdrop, body padding/lock or inaccessible focus. |
| Modules disclosure | Native button with `type="button"`, accessible name, `aria-controls` and synchronized `aria-expanded`; Enter/Space/Tab work; child links reachable only when visible. Preserve server-calculated initial open state. Avoid double toggling from retained legacy listeners. |
| Text zoom / long account name | Actual header height drives sidebar/content offsets when it exceeds 56px. Controls/text remain reachable; long name wraps or fits an accessible existing-account menu without widening the document. Existing home/Logout destinations stay unchanged. |

Replace competing old transform-only handlers; a click must activate only one mode. Reuse Bootstrap lifecycle events/APIs rather than manipulating its backdrop nodes or trapping focus independently. Bootstrap dialog/modal semantics apply only in overlay mode; desktop navigation remains ordinary labelled navigation. If using data attributes, prevent a second data-API handler from also toggling the same control.

Set shared flex descendants to shrink (`min-width:0`, available-width constraints), preserving local `.table-responsive` scrolling. **Page-wide `overflow-x:hidden` is not a fix** for inaccessible content. Prefer narrow `.admin-shell` rules and specific wrapper selectors; avoid unqualified `.row`, `.modal`, `.card`, `input`, `button`, nav/footer rules that would alter login/scanner/legacy views.

Use system typography and 16px normal body text, 4/8/12/16/24/32px spacing conventions, visible keyboard focus, 44px shell touch targets, and reduced-motion support. Preserve Bootstrap blue primary styles/dark sidebar. Do not change every existing page’s component density or enforce a narrow global content max-width; use available width with fitting gutters, leaving page-owned tables/forms to their increments.

## Acceptance allocation — all milestone criteria accounted for

The full normative wording of **A01–A18** is in specification section 7 and is unchanged from revision 2. This table defines A's portion; partial ownership never means the entire milestone criterion passes.

| Criterion | A's acceptance / deferred responsibility |
|---|---|
| A01 Stack/scope | Only six application files above; no dependency/version/schema/routing/data-contract drift. B/C/correctness scopes remain separate. |
| A02 Desktop | At 1440×1000, initial visible 250px sidebar, working toggle/offset, fitting navbar/account/footer; employee toolbar/actions are C acceptance. |
| A03 Tablet | At 768×1024, initially closed overlay, full content width, real open/scroll/close/focus return; two-column employee dialog is C. |
| A04 Mobile | At 375×812, shell renders at device width and account/menu controls fit and operate; login is B, employee taps/forms are C. |
| A05 Width/resize | Test all widths below; shell causes no root overflow beyond 1 CSS px. Necessary table scrolling stays local; no stale backdrop/lock/focus on repeated resizing. B owns login geometry, C employee-specific table/layout. |
| A06 Reflow | 200% text zoom, 320 CSS px equivalent width and 375×667 short height: actual header offsets avoid overlap and shell controls remain reachable. B/C own login/long employee dialogs. |
| A07 Navigation | Preserve existing URLs/state/policy; B fixes unsupported links/highlighting. Record pre-existing dead links as B work, never claim A completed A07. |
| A08 Keyboard | Skip link targets focusable content; menu/Modules/account/Logout work with Tab/Shift+Tab/Enter/Space; mobile Escape/focus containment/return work. Employee controls/dirty forms belong to C. |
| A09 Semantics | Named menu/close/account/disclosure, unique targets, decorative icons hidden and labelled navigation. Preserve page-owned main landmarks; B/C own pilot field/table/current-page semantics. |
| A10 Readability | Measure shell contrast: 4.5:1 normal text, 3:1 large text/control/focus graphics; measured 44px normal-size shell targets, visible focus, fitting long names. B owns teal/login, C employee controls. |
| A11 List | C owns search/sorts/pagination/counts. In A, read-only smoke preserves current list behavior; do not change its JavaScript or response. |
| A12 Loading/races | A introduces no uncaught JS errors or duplicate handlers. C owns employee request races/loading; global loader/toast callers remain compatible. |
| A13 Lookup/read failure | C owns persistent employee errors/retry. A must not obscure existing feedback; viewport-safe toast remains visible without changing API/contract. |
| A14 Form preservation | C owns complete dialog acceptance. A must not introduce overlay/z-index/focus regressions or mutations through navigation; employee/compensation behavior remains unchanged. |
| A15 Mutation correctness | **GATED** on separately approved/verified C-EMP-01/02; not tested or repaired in A. Compensation policy remains unchanged and uncertified. |
| A16 Login regression | A verifies existing login and account/Logout compatibility; B owns complete native invalid/CSRF/lockout/login presentation acceptance. Do not weaken login CSP or add login script dependencies. |
| A17 Shared regressions | Run the complete cross-module matrix below; no new shell-induced clipping/hidden actions. Record old business/print/employee defects separately. |
| A18 Data invariants | UI-only browsing leaves demo business tables, schema, snapshots/amounts/statuses/cutoffs unchanged. Compare print baseline for new shared-CSS regression. No employee/payroll write tests in A. |

A can be accepted for its owned subchecks independently of employee correctness repairs. Full UI-01 acceptance requires all 18 complete criteria and their gates; do not label a shell pass as complete functional/accessibility acceptance of the milestone.

## Historical baseline and limitations

Specification Appendices A/B preserve sanitized measurements and the limited screenshot inventory. No image is in the repository. On the old Chromium baseline at `ecec12a`, the 250px sidebar remained visible on mobile. At 375px viewport, document widths were dashboard 417px, Employees 798px (548px after collapse), allowances 1020px, payroll runs 1044px, detail 1100px and mixed import preview 929px. Some overflow is page-owned; A must remove shell causes and record remaining B/C/separate work honestly.

Search/Clear/sorting worked. Mobile employee Add/View/Edit pointer activation failed; programmatically opened mobile Add and programmatically collapsed import preview are inspection evidence, not passing interactions. Injected list failure/delay captures were artificial states. Historical read-only verification found 37 demo tables unchanged; it is not a current fingerprint or a new acceptance result.

Baseline lacked comprehensive physical-device, other-browser, assistive-technology, zoom/reduced-motion, restricted-role, large-data and write-flow verification. No WCAG compliance claim follows. Missing historical images do not block reproducing the required cases; re-capture against the actual implementation using fictional data when authorized.

## Environment setup for a future authorized BUILD

Do not start services for this documentation task. On resumption, confirm the selected published **dental-clinic-development** Cloud environment using its environment metadata and the mounted checkout; a path alone is not proof. Read the managed-cloud runtime skill/instructions and network policy before runtime/network actions. Never inspect/export credential values into evidence or connect to production.

Read [Docker setup](../../DOCKER_SETUP.md), tracked `compose.dev.yaml`, `docker/php/Dockerfile`, `config/docker.example.php` and `scripts/setup-demo.php`. Inspect the current `.tools/cloud-env.sh` helper if present before using it. It is ignored and may be absent on a future checkout. At this baseline it supports `start` and `compose`, retains Cloud demo services, and configures current proxy/CA trust without disabling TLS. Its `compose` subcommand also prepares ignored local overlay files; this is a runtime action, not a read-only inspection command.

After relevant startup permission is available, use the approved helper when present:

```bash
bash .tools/cloud-env.sh start
bash .tools/cloud-env.sh compose ps
```

If missing, use the already tracked Docker development setup according to current Cloud instructions after checking its mounts/proxy requirements; do not invent or commit a replacement helper/configuration. Older first-time-install patch instructions in Docker setup are historical: the Compose/Docker setup is already tracked at this source baseline; do not reapply installation patches. Report a genuine environment blocker before adding dependencies or changing configuration.

Only `monterodental_demo` with fictional fixtures is permitted. Docker uses internal `db:3306`, loopback web access normally `127.0.0.1:8000`, PHP 8.3 and `public/dev-router.php` from the `public/` working directory. The Windows fallback in [LOCAL_SETUP.md](../../../LOCAL_SETUP.md) uses `127.0.0.1:3307`; do not confuse these targets. Obtain fictional login details from the existing setup document without copying passwords/tokens into this handoff. Startup may seed an uninitialized isolated demo; preserve existing seeded data, never reset volumes/tables or import real records. No production configuration, Remote SSH, database export or payroll processing is needed.

Use already available browser tooling. CDN-backed Bootstrap/icons/jQuery need working approved network/CA trust; distinguish blocked assets from application regressions, preserve login CSP, and never bypass TLS verification. No new npm/Composer package or frontend build/test system is approved.

## Verification plan and recording requirements

All implementation checks below are **NOT RUN** in this documentation task. Record actual commands, baseline/implementation commits, viewport/document widths, accessible names/states, contrast, interactions, console/network results and known failures in this handoff. Use PASS/FAIL/BLOCKED/NOT RUN; do not silently waive missing coverage.

1. Inspect branch/status/history and all six actual files; compare against the specification. Read-only examples: `git status --short`, `git branch --show-current`, `git log -3 --oneline`, `git diff`, `git diff --cached`. Resolve existing work deliberately; never stash/reset/discard others' changes.
2. After authorized changes, lint the four changed PHP partials with **64-bit PHP 8.3+**. When the inspected Cloud helper/container exists, commands are:

   ```bash
   bash .tools/cloud-env.sh compose exec -T app php -l ../app/views/partials/admin-header.php
   bash .tools/cloud-env.sh compose exec -T app php -l ../app/views/partials/admin-navbar-top.php
   bash .tools/cloud-env.sh compose exec -T app php -l ../app/views/partials/admin-sidebar.php
   bash .tools/cloud-env.sh compose exec -T app php -l ../app/views/partials/admin-footer.php
   git diff --check
   git diff --stat
   ```

   If using another approved runtime, run equivalent lint with its compatible PHP from the documented working directory. These commands are future examples, not evidence they ran. Inspect the full diff and unchanged lockfiles/routes/schema/configuration, not only the stat. No standalone JS/parser dependency is needed: exercise the actual installed browser and inspect console errors.
3. Test **1440×1000, 768×1024, 375×812**, 320px stress width, **575/576/767/768/991/992/1199/1200** boundaries, **375×667**, **200% text zoom**, equivalent 320 CSS px reflow, reduced motion and a long fictional account name. Measure root `scrollWidth` against viewport/client width with ≤1 CSS px tolerance; verify two-dimensional scrolling remains inside table regions. Distinguish actual text zoom from merely changing screenshot scale.
4. Use real clicks/taps and keyboard: initial modes, desktop collapse/restore, mobile menu/close/backdrop/Escape, internal bottom-link scrolling, Modules disclosure, account/Logout access, skip link and focus return. Repeatedly resize with overlay open/closing and toggle quickly; inspect leftover backdrop/body lock/padding/focus. No forced clicks or programmatic opening count as interaction passes.
5. At all three baseline widths, smoke **`/home`, `/hris`, `/payroll`, `/users`, `/tenants`, `/branches`, `/hris/branches`, `/hris/employees`, `/hris/dtr`, `/hris/leaves`**, all six configuration pages **`/payroll/allowances`, `/payroll/cash-advances`, `/payroll/commissions`, `/payroll/compensations`, `/payroll/deductions`, `/payroll/incentives`**, **`/payroll/runs`**, a seeded **`/payroll/runs/view/{id}`**, its existing **`/payroll/runs/{runId}/employee/{runEmployeeId}`**, and **`/payroll/attendance/import`**. Discover fictional IDs from current lists; never assume numeric IDs. Open supported pages directly where old navigation lacks a link; B owns route visibility. Do not invoke process/load/finalize/reset/delete/import mutations. Use an already available or approved fictional preview only; never submit its Import Attendance action.
6. Verify existing login/Logout and unchanged current user display; login layout defects remain B work. Open representative employee dialogs through available normal controls to identify any new shell/backdrop/z-index/focus regression, without saving or claiming C dialog acceptance. Confirm global loader/toast signatures still work for current callers; do not create new failures to patch backend code in A.
7. Inspect print preview of seeded employee payroll/payslip using the current app's print path; compare to the known old two-page/blank-space limitation. No new navbar/sidebar/content print overlap from shared CSS. Do not repair payroll identity/print/business defects in A.
8. Use established read-only demo verification to compare business-table fingerprints before/after the browsing sweep, including payroll snapshots/amounts/statuses/cutoffs. Keep raw records/fingerprints outside Git; retain only sanitized unchanged-table outcome/count and method. Ordinary login/session state is not a business mutation. No DB reset, fixture write, schema alteration or employee mutation test is authorized by A.
9. Measure contrast/control size and inspect accessibility names/focus order. At least one real assistive-technology pass is required by the specification's accessibility acceptance; if unavailable, record BLOCKED/NOT RUN and obtain the missing coverage before claiming complete acceptance. Chromium emulation is not physical-device or Safari/Firefox coverage. Record those limits explicitly.
10. Save sanitized written results in this handoff. Screenshots may be temporary fictional-demo evidence when authorized, but do not copy/stage/commit images under the current decision. Do not include passwords, cookies, CSRF, raw HAR/HTML, logs with identity data, runtime IDs, database dumps or capture scripts containing credentials.

Known unsupported links (Roles/Settings/Clinic/Schedules/Reports), inaccurate active state, login geometry, employee mobile/dialog/read/error defects and C-EMP concerns stay separately labelled. A's no-new-regression sweep does not require redesigning those screens or certify their security. A genuine shell-owned failure must still be fixed within A or reported as incomplete.

## Risks, mitigation and rollback

| Risk | Prevention / verification | Future rollback boundary |
|---|---|---|
| Global CSS changes alter login/scanner/legacy pages | Scope to `.admin-shell` and owned wrappers; preserve legacy login rules for B; inspect representative non-shell/print behavior. | Revert only A's scoped CSS and matched markup/JS, preserving later independently reviewed fixes. |
| Bootstrap/legacy handlers conflict; backdrop/body lock survives resize | One mode owner/lifecycle, no duplicate DOM/data-API binding; stress breakpoint transitions and focus. | Roll back all A sidebar/navbar/state changes together, not just JS or a class. |
| Header wraps but content/sidebar retain 56px offset | Measure actual height on relevant resize/zoom; long-name/short-height tests. | Restore coherent geometry/markup/JS; smoke account/navigation afterward. |
| Shared IDs/includes/asset order broken | Preserve listed contracts and paired wrappers; PHP lint, console/network and cross-module sweep. | Restore matched header/navbar/sidebar/footer/CSS/JS increment diff as one reviewed unit. |
| Dependent B/C or security changes overwritten | Base later PRs on accepted A; document ownership and actual commit dependencies. | If B/C already landed, normally reverse C → B → A or prepare a reviewed compatibility patch; never restore whole old files over accepted correctness/security work. |

Rollback is a future authorized Git operation. Record actual implementation commits/paths before proposing it. Recheck login/account/Logout, employee list, payroll/import, print and breakpoint cleanup afterward. Presentation rollback requires no schema migration or database restore. Any unexpected business-data write is investigated separately, never hidden by a UI rollback. No hard reset, force-push, production restore or deployment is part of this task.

## Implementation sequence and separate branch/PR plan

This is a plan for a future authorized task, not permission to execute it now.

1. Obtain documentation review and explicit BUILD/Git scope; reverify repository instructions, branch, HEAD, outstanding edits and six-file boundary. Confirm the actual authorized development base; `ecec12a` is the assessment baseline, not permission to overwrite a newer base.
2. When branch creation is authorized, use `feat/ui-01a-responsive-foundation` from the authorized current development `main`. If a branch already exists, inspect/resume it deliberately. Do not automatically fetch/pull/switch/rebase or recreate it under this handoff. Resolve baseline drift before implementation.
3. Inspect/start only the approved isolated fictional runtime, record a fresh pre-change shell baseline and business-table verification. Review the existing six files and downstream nesting/asset contracts.
4. Implement matched shell markup/semantics and scoped layout geometry, preserving integration IDs. Implement desktop/mobile/resize/disclosure behavior through the existing Bootstrap/jQuery lifecycle; keep changes reviewable as one coherent increment.
5. Verify desktop/tablet/mobile and keyboard first, then boundaries/zoom/long names/assistive technology, login/account and cross-module/print/data invariants. Fix only A-owned regressions. Re-run tests affected by fixes; record unresolved B/C and unavailable coverage honestly.
6. Update this handoff with exact changed files/commit baseline, results/commands, residual risks and first next action. Show full diff and actual acceptance allocation. Mark READY_FOR_REVIEW only with explicit remaining coverage; do not claim all UI-01 criteria pass.
7. Only after separate Git/PR authorization, prepare one A-only PR into development `main`, title **“UI-01A: Establish the responsive admin foundation”**. Description covers problem/result, six-file scope, subcriteria, actual verification, known B/C limitations and rollback. No B/C/backend/correctness diff, image commit, auto-merge or deployment. Attach any actually created PR to the task through the available app tool.

Default downstream order is **A → B → C**, each with a separate feature branch/PR based on accepted development main. B owns incremental login/nav content/state changes in shared files; C owns employee view and employee-scoped CSS. Correctness tasks remain independent. Do not duplicate A's commits in those changes or edit their overlapping files concurrently without coordination.

## Current checks, blockers and first concrete next action

| Check | Documentation-stage result |
|---|---|
| Read complete revision-2 source and repository instructions | PASS; carried into the sibling specification with approval record. |
| Inspect six A files, relevant routes/views/assets and startup instructions | PASS against `ecec12a`; source observations recorded above. |
| Preserve all 18 acceptance rows and D1–D7 | PASS in sibling specification; A allocation above covers each criterion. |
| Verify doc links/scope/sanitization and application/index unchanged | PASS: Markdown links/source paths checked; no scratch-file dependency or copied credential literals; all 160 existing tracked-file contents unchanged, index diff empty and only the two documentation files are new. |
| Startup, PHP lint, browser/UI/accessibility/print/data invariants after implementation | NOT RUN; no application implementation or runtime startup in this task. |
| C-EMP-01/02 mutation correctness | GATED/NOT RUN; separate approval/verification, not A scope. |
| Compensation authorization | Unchanged; independent review remains open. |

No technical blocker to documentation preparation was found. BUILD and Git writes await user authorization. Real assistive-technology/other-browser/device availability must be checked in the future task; they are not assumed present.

**First concrete next action:** review the two new documentation files and their complete diffs with the user. Do not begin UI-01A or perform Git writes. After explicit authorization, re-read current source/status and execute only the approved part of the sequence above. Before pausing a future implementation, replace planning claims with actual results in this handoff; never depend on chat memory or scratch evidence for continuity.
