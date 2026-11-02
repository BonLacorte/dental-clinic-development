# HRIS / Payroll Home integration and audit

Status: INITIAL AUDIT RETAINED FOR HISTORY — the owner subsequently requested the fixes. Current disposition and verification are in [Dashboard live summaries](md-dashboard-live-summaries.md).

## Scope / PR identity
- Owner named HRIS PR 21 and Payroll PR 20. Live GitHub metadata resolves PR 20 as HRIS Home, PR 19 as Payroll Home; PR 21 returned 404. The corrected mapping was reported before implementation; use the two matching titled PRs.
- HRIS source: PR 20 `73b2ad1a2f1c8096549a568a80f37a204c30cc86`.
- Payroll source: PR 19 `a7be94008d85324821a003ebc80fb04df5836343`.
- Both source PRs base on `b5526bb4a7999bbd3c253fcdda1c8419a171314d`; no PR merge or Git branch change.
- Current branch `review/md-hris-manual-refinements`, HEAD `52929377eabc8101460141b108062d2b0154b905`, existing uncommitted changes protected.
- Application allowlist: `app/views/module_hris/hris_index.php`, `app/views/module_payroll/payroll_index.php`, additive `public/assets/css/style.css`; this consolidated handoff.
- Snapshot: `/tmp/home-integration-20261027-u0_ssyhk` (188 pre-existing files plus staged index). Source/PR evidence: local `audit-evidence/home-integration-20261027`.
- First `git apply --check`: PR 19 conflicts only at shared CSS; PR 20 conflicts at previously accepted HRIS Home and CSS. Reconcile views and append exact PR CSS additions; preserve original CSS prefix.
- Retain the existing HRIS consistency class and HRIS · Overview context. Imported links retain the PR's existing navigation predicates; no permission or access policy changes.
- Backend/data changes, live metrics, permissions, financial operations, dependency installs, Git staging/commit/push, merges and deployment remain outside scope. No Process, Import, Reset, Delete or other business-action controls will be tested. Browser checks are limited to read-only page/link navigation and layout/keyboard observations.

## Acceptance plan
- Two page H1s/regions and current shared nav; explicit unavailable-state messaging without fictitious metrics or all-clear claims.
- Home shortcut destinations lead to the current HRIS/Payroll pages; preserve all completed HRIS/Items/Runs/Import work and print sources.
- Native PHP lint and isolated template fixtures for the existing boolean navigation combinations (not real role/access-policy acceptance).
- Real app checks at desktop/tablet/mobile, 320px/short height and breakpoint boundaries; no root overflow, target/focus/contrast samples and local Payroll table scroll.
- Audit current data availability, definitions and supported actions against actual source; classify backend dependencies and presentation findings separately. No inferred formulas, aggregates or policy changes.

## Implementation completed
- Imported HRIS Home from verified PR 20 and Payroll Home from verified PR 19. The corresponding PRs remain drafts; no merge or publication.
- HRIS Home gains the four Quick Access destinations plus Leave Management follow-up navigation. Retained the current `hris-consistency-page` class and `HRIS · Overview` context; all other incoming page content/predicates match the pinned PR source.
- Payroll Home replaces the generic chart/table placeholder with Payroll Summary, Recent Payroll Runs, Needs Attention and Quick Access.
- Both use explicit unavailable states. No live metric, record, queue, fabricated zero or all-clear claim was added. Neither Home view has page JavaScript, data-fetch code or a form.
- Kept the original shared stylesheet as an exact byte prefix and appended the exact two PR CSS blocks, with trailing blank-line cleanup only. No stylesheet replacement or unrelated override.
- The reconciled patch passed `git apply --check` before application. Final increment passes `git apply --reverse --check`; `git diff --check` passes.

## Original findings (superseded by the follow-up disposition)

| ID / priority | Finding and evidence | Proposed action / boundary |
| --- | --- | --- |
| HOME-01 / functional completion dependency | **The dashboards are navigation/overview shells, not live operational dashboards.** Both Home controller methods supply only name/title. HRIS shows Summary unavailable and no counts; Payroll shows four Unavailable metrics, Recent runs unavailable and unavailable follow-up information. Existing records are reachable through the links. Actual browser and source agree. This is deliberate in the PRs and not a new load failure. | Approve a separate dashboard data contract and implementation for exact totals, recent records and actionable follow-up. Define the Active Employee and Pending Leave meanings and count/date scope. HRIS list responses currently expose paginated rows/page metadata; Payroll list has `totalRecords`, but reuse on Home was explicitly deferred. Do not derive totals from a loaded page or silently add calls under the backend freeze. |
| HOME-02 / P2 defect, pre-existing | **Payroll Home has the wrong browser/tab title.** Live `/payroll` title is `Montero Dental | HRIS Home`, despite visible H1 Payroll Overview. `app/controllers/ModuleController.php:48` supplies HRIS Home. | Small controller-title correction in a separately authorized backend/static-controller batch; no controller change made here. |
| HOME-03 / future data-definition dependency | **Payroll run-status metrics need an agreed meaning.** The earlier local run has COMPUTED employee results while its stored run status stays DRAFT; `PayrollRunModel.php:1573` still comments out the run transition. New Home labels correctly say Stored Draft/Processed/Finalized status, but eventual Processed counts would not mean employees with computed results. | Resolve the previously deferred lifecycle/metric definition before presenting those counts as workflow completion. Do not fake a status transition or substitute employee computation counts. No processing or lifecycle changes here. |
| HOME-04 / P3 presentation consistency | **Home heading treatments differ.** Measured HRIS H1 is 24px/600 with the module context label; Payroll H1 is 28px/600 without a matching context label. Both headers are on the canvas. Layout is usable, but the pair is less uniform than the accepted page introductions elsewhere. | Optional frontend-only follow-up: agree one title/context/header format for both dashboards. Record as a design decision rather than an invented business feature. No extra redesign included in this import/audit task. |

No broken Home shortcut or root-overflow regression was found in the tested cases. No new access-policy review or changes were undertaken; the owner's permissions/access deferral remains in force.

## Verification actually run

| Result | Check | Evidence / limits |
| --- | --- | --- |
| PASS | Exact source/scope | Only the two Home views and additive CSS changed in application code. HRIS differs from pinned PR only by the two listed preservation edits; Payroll matches its pinned PR after newline cleanup. |
| PASS | PHP | Both views lint with Ubuntu PHP 8.3.6, 64-bit. |
| PASS — native fixtures | 10 template flag cases | Actual view PHP rendered with 8 HRIS boolean-navigation combinations and 2 Payroll combinations, no application/database bootstrap. Expected links, one H1, unique IDs and complete ARIA references; no forms/page scripts. These are template tests, not real-role/authorization acceptance. |
| PASS — actual app | 26 responsive cases | Both Home routes at 1440×1000, 768×1024, 375×812, 320×812, 375×667 and 575/576/767/768/991/992/1199/1200px boundaries. One H1; Home active in the correct module navbar; no root overflow >1px or visible error alert. All measured Home link targets meet 44×44px. |
| PASS — actual app | 10 Home link navigations | All five HRIS links (including both Leave links) and all five Payroll links reach expected pages with their page H1. Read-only GET navigation only; no operational buttons activated. |
| PASS — actual app | 8 exact before/after comparisons | Employees, Leaves, Allowances, Compensations, Runs list/detail, employee payroll detail and Import. Page H1, overflow, table header text/colors/background and displayed financial summaries match the pre-integration baseline. Earlier computed demo results were preserved; not reset. |
| PASS — actual app | Keyboard samples | Tab reaches next HRIS/Payroll shortcut with visible focus; Enter follows the read-only link. ArrowRight scrolls Payroll Home's local table from 0 to 13.6px at narrow width. No modal or financial-action test. |
| PASS — sampled | Contrast / visuals | Muted text on canvas 4.83:1; table text on green 11.83:1; focus color on white 4.75:1. Desktop/mobile screenshots saved for each page. Samples are not full WCAG certification. |
| PASS — sampled | Browser diagnostics | No warning/error entries returned after Home/link checks. Source inspection confirms no new Home data requests; a full network trace was not captured. |
| PASS | Preservation | 185 pre-existing files outside the three-file application allowlist unchanged. Exact previous CSS prefix preserved; additions are the PR blocks, screen-only with balanced braces and no new imports/print rules. Staged index, branch and HEAD unchanged; backend diff empty. |
| FAIL / open | Payroll document title | HOME-02 confirmed live and in source. It predates this integration and remains unfixed under the backend freeze. |
| NOT RUN — deferred backend functionality | Dashboard live data | HOME-01 and HOME-03; unavailable states are truthful but do not establish working live summaries/activity. |
| NOT RUN | Broader acceptance | Real denied-role/tenant/branch acceptance, native 200% zoom, full assistive technology, physical devices/other browsers, financial/attendance writes and real print/PDF output. No full production/dashboard-functionality sign-off. |

### Data-table UX disposition
Payroll Recent Runs preserves its five-column semantic table, caption, scoped headers, named keyboard-scroll region and explicit unavailable state. MTD structure/value/status/local-scroll/preservation checks pass for the sampled scope above. Sorting, selection and pagination are N/A for this static unavailable overview; no such feature is fabricated. Native zoom, AT and real record-state acceptance remain NOT RUN. No new video/footage verification or conformance claim.

## Evidence and next step
- Local evidence: `C:/Users/loueg/Documents/ChatGPT/montero-dental-local/audit-evidence/home-integration-20261027` — pinned PR JSON, source files, native fixture results, browser before/responsive/link/regression/keyboard/visual results, preservation checks and `integration-final.patch`.
- Increment patch SHA256: `305a30c186a268ee89e1ebccb221229c7cdfd054683d510deffe0265129e44c2`.
- Screenshots in `C:/Users/loueg/.codex/visualizations/2026/10/27/01a17bde-13e2-7bc3-99b4-c6b8d2508889`: `hris-home-integrated-desktop.png`, `payroll-home-integrated-desktop.png`, and their `-mobile.png` counterparts.
- [HRIS source PR 20](https://github.com/BonLacorte/dental-clinic-development/pull/20); [Payroll source PR 19](https://github.com/BonLacorte/dental-clinic-development/pull/19). PR 21 returned 404 through the connected account.
- No application/backend/database configuration, dependency, record or runtime-service changes beyond the listed Home view/CSS integration. No Create, Process, Import, Reset, Delete or other business-action controls were clicked in this task. All current source work remains uncommitted, with the index untouched.
- Next decision: review these findings, select any frontend consistency follow-up, and obtain a separately authorized backend/data scope for live dashboards and the title correction. Keep the earlier Payroll lifecycle/pay-rule and access matters deferred until explicitly addressed. No automatic Git publication or deployment.


## Follow-up closure — 2026-10-27

HOME-01 (live overview data), HOME-02 (Payroll title), HOME-04 (heading consistency) are resolved by the dashboard follow-up. HOME-03 is resolved at the metric-definition/presentation level: saved run statuses and employee computation state are distinct, and computed Draft runs are explicitly flagged. The underlying processing/lifecycle change remains deferred. Read-only dashboard controller/model work was authorized by the owner's subsequent request to solve these findings; the original integration-only boundaries above are historical. See [the follow-up handoff](md-dashboard-live-summaries.md) for current code, checks, scope and remaining limitations.
