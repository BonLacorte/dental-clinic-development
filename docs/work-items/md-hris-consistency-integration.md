# Task: Controlled HRIS frontend consistency integration

## Identity and status

- **Task ID:** MD-HRIS-CONSISTENCY-STAGE2
- **Branch:** `codex/md-hris-consistency-integration`
- **Worktree:** `/workspace/monterodental-hris-consistency-integration`
- **Status:** `STAGE_2_PARTIAL — OWNER_REVIEW_REQUIRED` — independent static presentation complete; PHP and authenticated/rendered acceptance blocked. No publication authority.
- **Last reviewed (Manila):** 2026-10-26.
- **Source baseline / current HEAD:** `b5526bb4a7999bbd3c253fcdda1c8419a171314d`.
- **Last relevant commit:** baseline only; no new commits authorized.

## Goal and acceptance criteria

Compose compatible static presentation from the four frozen HRIS drafts using the existing PHP/MVCLite, Bootstrap, jQuery/AJAX and Pearl & Teal system.

- [x] Original and integration checkouts independently verified clean at the approved baseline before editing.
- [x] Exact source commits retrieved and verified without moving existing branch/tracking refs.
- [x] Every candidate view and each base-relative CSS addition inspected before application edits.
- [x] Compose safe static markup and scoped screen CSS, preserving executable PHP/JavaScript and existing table/form contracts.
- [x] Verify exact allowlist, protected sources, original checkout and index; attempt syntax checks and record unavailable tooling.
- [x] Record all deferrals and runtime limitations; stop for owner review.
- [ ] Compatible PHP syntax lint and authenticated/rendered acceptance. These remain required; source preservation is not runtime proof.

**Authority:** The owner's pasted Stage 2 request authorizes the eleven candidate application paths and this handoff only. Stage 3–6, backend work, staging, commits, pushes, merges, PR changes and deployment remain unauthorized.

## Relevant architecture and source provenance

Read repository AGENTS, overview, architecture, setup, continuity instructions, both frontend skills/references, Pearl & Teal design system, implementation plan and work-item template. The four previously supplied HRIS planning documents remain accessible under `/workspace/attachments`; they were reviewed during Stage 0. The current Stage 2 request narrows their future P/F/I/S proposals.

| Source PR | Verified SHA | Complete base-relative manifest |
| --- | --- | --- |
| #20 | `73b2ad1a2f1c8096549a568a80f37a204c30cc86` | Home view, stylesheet; added `docs/work-items/hris-home-ux.md`. |
| #14 | `cb83bf07534421f1fad091c145990b2bad0f0b83` | Four Organization views, stylesheet; added `docs/work-items/md-hris-org-ux-001-build.md`. |
| #15 | `a027c9ba5d57503816bfedc9f21bdf9eeb109c58` | Four Attendance views, stylesheet; added `docs/work-items/md-hris-att-ux-001-build.md`. |
| #13 | `57c28aa3e039c34b6abd2135b10301ecb97702c4` | Employee view, stylesheet, EmployeeController, EmployeeModel, HRIS routes; added EmployeeExportService, two Employee handoffs and four verification scripts. |

All live heads/base SHAs matched the approved snapshot and all PRs remained open drafts at preflight. The initial fetch could not reach the proxy in the default sandbox. Retrying with tool-level network permission succeeded through the inherited proxy; no proxy or Git configuration was changed. Each missing PR was fetched with `git fetch --no-tags --no-recurse-submodules --refmap= origin refs/pull/<number>/head`, in order 20, 14, 15, 13; each `FETCH_HEAD` matched its approved SHA. No persistent PR branch was created. The existing stale `origin/main` remains `ecec12ad5c4c72786a9b3b1bd09ce60971573b0d`.

### Stage 2A compatibility matrix and intended actions

All view paths below are under `app/views/module_hris/`. Every baseline PHP block and script will remain byte-identical. The columns and generated rows remain the accepted baseline's columns and rows.

| File | Source PR | Static changes eligible | Executable changes excluded | Dependencies | Decision |
| --- | --- | --- | --- | --- | --- |
| `hris_index.php` | #20 | Overview header, unavailable summary, neutral follow-up and guidance panels | New permission conditionals and shortcut links | Quick Access requires separate F; aggregates require S | PARTIAL_PRESENTATION_ONLY |
| `branches.php` | #14 | Header/subtitle, opt-in scope, table/toolbar presentation | Grouped name/code renderer, feedback, request/mutation guards | Keep four-column baseline and paginated page-only GET | PARTIAL_PRESENTATION_ONLY |
| `OrgUnits.php` | #14 | Offices header/description, scoped hierarchy presentation | New tree renderer, search, More menu, direct-assignment action label, dialog guards | Keep existing tree, handlers and actions; no reparenting | PARTIAL_PRESENTATION_ONLY |
| `Position.php` | #14 | Header/description and catalog presentation | New filtering, two-column renderer and dialog behavior | Retain ID/name/created/actions columns | PARTIAL_PRESENTATION_ONLY |
| `appointments.php` | #14 | Header/description and identity typography | New filtering, grouped name/code, System Default/protected-action renderer and guards | Baseline has no System Default UI marker; do not invent it with CSS | PARTIAL_PRESENTATION_ONLY |
| `dtr.php` | #15 | Header, static selection guidance, metric presentation | Selected-context script and new PHP else branch | Preserve employee/month query, full table, summaries and all PHP | PARTIAL_PRESENTATION_ONLY |
| `leaves.php` | #15 | Header/description, table/toolbar presentation | Grouped period renderer, contextual Add, sort controls, uncertainty/dirty guards | Preserve both tabs, original columns/statuses/forms | PARTIAL_PRESENTATION_ONLY |
| `holidays.php` | #15 | Header/description and targeted screen alignment | Grouped identity, factor/date formatting, sorting/modal logic | Retain seven-column baseline, exact multipliers and values | PARTIAL_PRESENTATION_ONLY |
| `shifts.php` | #15 | Header/description and targeted screen alignment | Grouped schedule, new sorting/modal logic | Retain eleven-column baseline, times/seconds/hours/break/OT values | PARTIAL_PRESENTATION_ONLY |
| `employees.php` | #13 | Header/search/feedback presentation and table density | Cards, menu, Position column, export, PHP loops, response/modal changes | Preserve six-column baseline including Date Hired; export and Position backend deferred | PARTIAL_PRESENTATION_ONLY |
| `public/assets/css/style.css` | all four | Select compatible rules and adapt them to retained baseline markup | Whole snapshots, card/menu/renderer-dependent rules, new print rules | Four unchanged baseline prefixes; 262/86/223/528 appended source lines | INTEGRATE_PRESENTATION |

## Verification evidence

| Check | Result | Scope / limitation |
| --- | --- | --- |
| Worktree, branch, clean baseline and original checkout preflight | PASS | Exact two Stage 1 worktrees and approved HEADs. |
| Four exact source objects, manifests and common CSS prefix | PASS | Local Git inspection plus live read-only PR metadata. |
| PHP blocks and complete script blocks in all ten modified views | PASS | Byte-identical ordered blocks against baseline; no new PHP or JavaScript. |
| Complete table/form source and parsed control attributes/ownership | PASS | Byte-identical table/form regions and unchanged form/input/select/textarea/button/option metadata; existing IDs and links retained. |
| Exact allowlist and original/protected tracked files | PASS | Only eleven permitted application files plus this handoff differ; all other tracked bytes compared against the base. Original checkout remains clean. |
| Common CSS prefix and source isolation | PASS | Entire 2,024-line baseline unchanged; 365 added lines, 55 qualified source rule groups, all inside screen media and requiring the opt-in page scope. No new print rules or page-wide overflow hiding. |
| CSS source structure and custom properties | PASS | Balanced rule blocks; every referenced property defined by the accepted stylesheet. This is not a browser CSSOM or computed-style pass. |
| Whitespace and Git integrity | PASS | `git diff --check`; branch/HEAD preserved, index empty, no new branch/ref/worktree after retrieval. |
| Native PHP 8.3+ availability | BLOCKED | `php`, `php8.3`, `php8.4`, `php8.5` absent from PATH. No runtime installed. |
| `php -l` for each of the ten modified views | BLOCKED | Attempted each exact absolute path with `php -l`; executable unavailable. Byte-identical PHP blocks do not replace lint. |
| Offline CSSOM parser attempt | BLOCKED | Existing Playwright/Chromium discovered; Node harness stopped at `spawnSync git EPERM` before browser launch. No application loaded and no CSSOM or browser pass claimed. No dependency installed. |
| Authenticated visual/runtime acceptance | BLOCKED | No approved running fictional application/database established; no services started. |
| Browser, print, accessibility and business mutations | NOT RUN | Source checks cannot establish these results. |

## Composition delivered

All ten views are **PARTIAL_PRESENTATION_ONLY**. No view is represented as the complete draft implementation. Only the changes below were composed; table layouts, conditional visibility and existing actions/requests remain the baseline's.

| File | Presentation delivered | Explicitly omitted |
| --- | --- | --- |
| `hris_index.php` | One Overview H1/context, static unavailable Workforce Summary, neutral Needs Attention and guidance panels, Quick Access guidance to existing navigation | Conditional shortcut/follow-up links and all new permission blocks; live metrics/data requests. No nonfunctional buttons or simulated links. |
| `branches.php` | Context/header/subtitle, shared table typography and secondary code styling | Three-column grouped renderer, new feedback/guards/cards, new search. |
| `OrgUnits.php` | Offices title/context/subtitle, scoped tree padding/connector spacing and wrapping | New tree/search/More renderer, labeled assigned-employee action and lifecycle changes. Existing expansion/View Employees/CRUD handlers retained. |
| `Position.php` | Context/subtitle, retained catalog typography, subdued creation metadata | Removed-column renderer, client search and revised modal behavior. Existing direct icon actions retained. |
| `appointments.php` | Context/subtitle, identity/code typography | Name/code regrouping, filters, System Default/protected-action UI, new guards. Existing backend protection unchanged; no claim that baseline UI already identifies global rows. |
| `dtr.php` | Context/subtitle and static filter instructions; screen-only metric grid | Selected-context script, added PHP initial-state branch, changed data formatting/table labels. Filter form, summary output and full table preserved. |
| `leaves.php` | Context/subtitle, shared typography, retained tabs and baseline table schemas | Contextual Add, grouped period/status/date renderers, new sorts, cards and modal states. |
| `holidays.php` | Context/subtitle and screen-only left alignment of multiplier/work fields | Grouped date/name, factor/date reformatting, new sorts, cards and dialogs. Exact values unchanged. |
| `shifts.php` | Context/subtitle and screen-only left alignment of hours/breaks | Grouped schedules, date/time reformatting, new sorts, cards and dialogs. Exact times/precision/units unchanged. |
| `employees.php` | Context/title, static search help, visible existing on-page/sort feedback, table/sort typography and form-group separators | Position column/backend, export, new PHP field loops, name action/menu/cards, response classifier and modal changes. Six-column baseline and Date Hired remain. |

### CSS provenance and conflict resolution

- **#20:** selected Home typography, panel/availability/summary/follow-up/guidance declarations; omit conditional-link/shortcut styles. Adapt to approved spacing tokens and the new static-only surface.
- **#14:** header intro/eyebrow/description, metadata and tree spacing adapted to the baseline DOM. Omit `.org-node`/More/search/default-state rules that require its renderer.
- **#15:** header/secondary typography, cell density, local scroll/focus and responsive presentation. Keep baseline tables; do not import regrouped-column selectors, `.attendance-created` visibility overrides, uncertain-dialog styles or green left stripes.
- **#13:** selected header spacing, search width/help, feedback, sort typography and field separators. Omit Position, export, card switching, portaled menus and all new print rules.
- Every added qualified selector requires `.admin-shell .hris-consistency-page`; no protected Payroll/login/shell template adds this class. Existing variables supply all colors/radii/fonts; no token redefinition, new reset or dependency.
- Header padding uses a narrow important override to beat baseline `20px 24px !important` and mobile `16px !important`; selected values are 24px desktop/16px small screens. Page H1 specificity beats the inherited management-header size, unlike the source Organization selector.
- Holidays baseline fields 4/5 are multiplier/work allowed; Shifts fields 7/8 are required hours/break minutes. Explicit scoped selectors override inherited positional right alignment only for those fields. DTR and financial alignment remain untouched.
- Do not import the source Employee `nth-child(2)` Position rules: the retained second column is Name. Its existing emphasis remains intact.
- Media: all added rules under `screen`; nested min 1200px and max 1199.98/767.98/575.98px. Existing 992px sidebar behavior and every baseline print declaration stay unchanged. No table hiding or mobile-card switch was added.

### Approved consistency coverage

| Standard | Coverage | Limit |
| --- | --- | --- |
| Page context/H1/subtitle/spacing | Applied in source | Rendered sizing/reflow pending. |
| Typography/table density | Applied in source | Computed styles/contrast pending. |
| Identity hierarchy | Partially applied | Existing baseline columns retained; grouped identity needs F. |
| Toolbars | Partially applied | Existing behavior retained; Employee help/width refined; no new sort/filter controls. |
| Cards | Partially applied | Static Home panels and DTR metric presentation; record cards deferred. |
| Pagination | Partially applied | Wrapping/spacing only; page semantics/prose unchanged. |
| Actions/default markers | Deferred where generated | Baseline icon actions retained; menu/labels/protection markers need F. |
| Notifications | Deferred | Existing helper/callers/styles untouched. |
| Dialogs | Partially applied | Existing groups plus Employee separators; no lifecycle changes. |
| Responsive presentation | Partially applied | Scoped wrapping/grids/local-scroll preservation; actual viewport/zoom coverage outstanding. |

### Retained Table UX contracts

Baseline is the SHA above. User tasks remain find/scan/compare/act. Existing permissions, action attributes, server page sizes, queries, active sorting, field values and print data are protected. All added CSS is presentation; every renderer or interaction proposal remains functional/deferred.

| Screen | Complete retained columns / structure | Existing behavior retained |
| --- | --- | --- |
| Employees | Employee No, Name, Branch, Date Hired, Status, Actions | Explicit search/Clear, existing header sorts/page requests, View/Edit/Compensation/Delete predicates and dialogs. No draft-only Employment Type/Position column or mobile Apply form. |
| Branches | Name, Code, Address, Actions | Ten-row server paging, existing Edit/Delete. No global search. |
| Offices | Existing nested tree; employees dialog: Employee No, Name, Position, Branch, Status | Expansion/collapse and direct assignment endpoint; existing View/Edit/Delete; no reparenting. |
| Positions | #, Position Name, Created At, Actions | Existing complete list and direct icon Edit/Delete; no new client filter. |
| Employment Types | Code, Employment Type Name, Description, Status, Actions | Existing list/CRUD and server ownership rules. Source draft's System Default marker/action gate deferred. |
| DTR | Date, Day, Time In, Time Out, Late, Undertime, Overtime, Hours, Status | GET employee/month, existing summary, full daily sequence and numeric/date formats. |
| Leave Requests | Employee, Leave Type, Date From, Date To, Status, Approved By, Approved At, Created At, Actions | Existing search/sorts/paging, status values and dialogs; original created-column screen visibility. |
| Leave Types | Name, Is Paid, Created At, Actions | Existing tab, search/sorts/paging, YES/NO meaning and dialogs. |
| Holidays | Holiday Date, Name, Type, Pay Multiplier, Allow Work, Created At, Actions | Existing search/sorts/paging, exact factors and YES/NO values. |
| Shifts | Name, Type, Start, End, Flex Start, Flex End, Hours, Break, OT, Created At, Actions | Existing search/sorts/paging, fixed/flexible fields and original units/values. |

### MTD rule evidence

PASS below is explicitly limited to source comparison, not browser/AT/persistence acceptance.

| Rule | Result | Evidence / remaining work |
| --- | --- | --- |
| MTD-01 | PASS source | Entire semantic table markup retained byte-for-byte. |
| MTD-02 | PASS source | Columns/captions unchanged; one page H1 per view. |
| MTD-03 | NOT RUN rendered | Existing header tint retained; new type/density source inspected. |
| MTD-04 | PASS source | Existing identity field emphasis preserved; no invented row links. |
| MTD-05 | NOT RUN rendered | Existing separators plus scoped form/tree dividers; visual tracking pending. |
| MTD-06 | NOT RUN rendered | Source padding/wrapping reviewed; zoom/readability pending. |
| MTD-07 | PASS source | Existing hover/interaction cues unchanged; no row-click behavior. |
| MTD-08 | PASS source | Values/precision unchanged. Named Holiday/Shift alignment exceptions follow owner standard. |
| MTD-09 | PASS source | All PHP/scripts and tables unchanged; no date/time formatting imported. |
| MTD-10 | PASS source | Status labels/maps/meaning unchanged. |
| MTD-11 | NOT RUN rendered | Existing action names/handlers retained; actual target/focus tests pending. |
| MTD-12 | PASS source | Control attributes, PHP decisions, scripts and form ownership equal baseline. |
| MTD-13 | PASS source | Existing confirmations/actions retained; no new menu/destructive wording. |
| MTD-14 | PASS source | Search fields/forms/scripts identical; existing Employee help added outside form. |
| MTD-15 | PASS source | Existing filters unchanged; no new filters. |
| MTD-16 | PASS source | Existing sort keys/cycles/scripts preserved. |
| MTD-17 | PASS source | Existing native sort controls and state-updating scripts preserved. |
| MTD-18 | PASS source | Existing pagination source, IDs, page size and announcements preserved. |
| MTD-19 | N/A | No selection or bulk action introduced. |
| MTD-20 | PASS source | Existing loading/empty/error/response behavior unchanged, including inherited limitations. |
| MTD-21 | NOT RUN rendered | All columns remain in identical tables; viewport reachability pending. |
| MTD-22 | NOT RUN rendered | Existing local-scroll regions retained; no document clipping added. |
| MTD-23 | PASS source | Existing identity columns/order remain; no sticky overlays. |
| MTD-24 | BLOCKED F | New mobile-card renderers deferred; baseline table mode retained. |
| MTD-25 | PASS source | Existing page limits/data requests unchanged; no fetch-all feature. |
| MTD-26 | NOT RUN | Measured contrast/targets/zoom and changed-presentation WCAG acceptance pending. |
| MTD-27 | NOT RUN | Keyboard/AT/focus/scroll acceptance pending. |
| MTD-28 | PASS source / NOT RUN print | Exact table/PHP/JS values and baseline CSS preserved; actual print geometry pending. |
| MTD-29 | PASS source | Exact allowlist, protected bytes and executable regions verified. |
| MTD-30 | PASS | Evidence tiers/limitations recorded; no claim that five videos were watched. |

## Reproducible verification commands

Run from the integration worktree. These are read-only checks; none stages, starts the app or accesses a database.

```bash
git status --short
git branch --show-current
git rev-parse HEAD
git diff --name-status
git diff --check
git diff --stat
git diff
git diff --cached --name-status
git worktree list --porcelain
git for-each-ref --format='%(refname) %(objectname)'
```

The executed `python -B -` inline preservation audit uses `git show b5526bb4a7999bbd3c253fcdda1c8419a171314d:<path>` and verifies, for all ten views, ordered PHP blocks, full scripts, full tables/forms, parsed control attributes/form ownership, existing IDs/links and one H1. It compares every protected tracked file in both worktrees byte-for-byte with that base, checks the exact twelve-path candidate set and empty index, and checks the CSS prefix. All assertions passed. A second inline Python source audit validated the appended CSS's balanced blocks, 55 opt-in selector groups, enclosing screen media, defined variables and absence of new print rules/overflow clipping. These inline audits create no test files.

Each of the following exact lint commands was attempted and BLOCKED because `php` is unavailable:

```bash
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/hris_index.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/branches.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/OrgUnits.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/Position.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/appointments.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/dtr.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/leaves.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/holidays.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/shifts.php
php -l /workspace/monterodental-hris-consistency-integration/app/views/module_hris/employees.php
```

`node -` was used to discover existing parser/browser packages and attempt offline CSSOM parsing. Playwright and Chromium were available; the attempt failed at `child_process.execFileSync('git', ['show', 'HEAD:public/assets/css/style.css'])` with `spawnSync git EPERM`, before browser launch. No computed CSS, screenshots or responsive/browser pass resulted. No retry with broader permissions or new test runtime was performed.

## Final Git diff and protected boundaries

- Eleven tracked application files modified: the ten views above plus `public/assets/css/style.css`.
- One new untracked document: `docs/work-items/md-hris-consistency-integration.md`.
- Application diff: 467 insertions, 41 deletions; stylesheet contributes 365 additions. Handoff is untracked and therefore absent from ordinary `git diff --stat`.
- No staged files, new commits, pushes, PR modifications, merges, cherry-picks, resets, additional branches/worktrees, backend/database/config edits, dependency changes, services, exports or business mutations.
- Original checkout remains clean on `work` at the baseline. Integration HEAD remains at the same baseline; only the authorized working files differ. Both registered worktrees remain intact.
- Payroll, routes, authentication/authorization and Employee/Leave business source remain unchanged. This establishes source preservation, not fresh runtime/persistence/security acceptance.

## Deferred findings

### DEFERRED_FRONTEND_AUTHORIZATION

Stage 3: generated identity/actions, Employee name/menu/cards, other mobile-card renderers, sorting-control behavior, dirty/modal lifecycle, notification callers, Office tree enhancements, accurate System Default markers/action visibility, Home conditional shortcuts and DTR context/initial-state behavior.

Stage 4: HRIS-DEF-01–07 and 10–11 remain in the review backlog; 08–09 remain test targets. Distinguish unimported PR defects from inherited baseline limitations. Do not claim a PR-only classifier/uncertainty/print regression was introduced into this baseline-preserving composition.

### DEFERRED_BACKEND_APPROVAL

HRIS-S-01–12 remain open: export/Position queries and contracts; compensation authorization/CSRF/effective-rate selection; DTR access; catalog validation; tenant-safe joins; dashboard aggregates; Branch search/totals; reparenting; global ownership; delete semantics; per-action CRUD scope. No backend fix is authorized.

### INTEGRATION_RISKS

- Preserve the baseline table schemas rather than applying source CSS to the redesigned PR schemas.
- Baseline management-header padding is `!important`; header sizing and numeric positional selectors need explicit opt-in specificity.
- The accepted baseline Employment Type renderer lacks PR #14's System Default marker/action gate, and the Office tree lacks its new More/menu pattern. Those remain F dependencies, not completed UI features.
- The baseline Employee directory has no mobile cards, name-as-View button or portaled menu. Keep its working table/actions and Date Hired print source.

### REGRESSION_VERIFICATION_REMAINING

Compatible PHP lint; authenticated 320/375/768/992/1200/1440 layouts and breakpoint edges; 200% zoom, keyboard/AT/focus/contrast/targets; unchanged requests/sorts/paging/dialogs; >10 branch choices; Employee/Attendance/Payroll print; representative Payroll/login/shell regression; separate approved disposable mutation tests.

## Next session — first concrete action

Owner review of this uncommitted Stage 2 diff is the next action. Required PHP/runtime acceptance remains blocked; establish its permitted verification environment separately before acceptance. Stage 3/4 executable work and S dependencies require distinct authorization. Do not stage, commit, push, change a PR, create another branch/worktree, start an unverified application, merge or deploy.
