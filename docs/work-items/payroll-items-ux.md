# Payroll Items Premium UI/UX — MD-PAY-ITEMS-UX-001-BUILD

## Identity and status

- **Authorization:** owner request v2.0, APPROVED BUILD, 23 October 2026 (Manila). Authorizes the seven application files below, this handoff, page-local presentation/interaction changes, a dedicated branch, commits, push and one development draft PR. No merge or deployment.
- **Branch:** `codex/md-pay-items-ux-001-build`.
- **Checkout:** cloud `/workspace/dental-clinic-development`; no laptop/WSL worktree was created.
- **Verified development main / source baseline:** `b5526bb4a7999bbd3c253fcdda1c8419a171314d`. Live `git fetch origin '+refs/heads/*:refs/remotes/origin/*'` confirmed this baseline. The original checkout started on `work` at the same SHA with a clean tracked tree/index. Downloaded request attachments were moved outside the checkout.
- **Other work:** Employees PR #13, Organization PR #14 and Attendance PR #15 were open and separate at preflight. No merge/cherry-pick/import from those branches or Draft PR #8.
- **Status:** `PARTIALLY_IMPLEMENTED` — frontend build is implemented on all six pages; native PHP lint and authenticated application acceptance remain BLOCKED. This is a development draft, not merge or production acceptance.
- **Last reviewed:** 27 October 2026, Manila.
- **Implementation commits:** `c3a3398` (scoped CSS) and `e9a3728` (six views). The handoff commit follows them; final HEAD, remote verification and draft PR are recorded in the final chat/PR. No merge or deployment.

## Header and search refinement — 27 October 2026

Owner follow-up requests the supplied Payroll Items tables with the Leave Management header and Search section format. Refinement baseline: `8c671adca2b48574cd3423bb1011e632fecf6dfe`; same approved topic branch and existing draft PR #16, initially clean. The seven-file application allowlist and this handoff remain the scope. No separate module branch was imported.

**Presentation contract / implementation:** all six pages now show `Payroll · Items`, the existing page title and unchanged description. Title size/weight and eyebrow spacing follow the supplied Leave Management reference. Records and Types use visible “Search” labels, retaining page-specific accessible text. Search field, Search, Clear and the compact current-sort button share a desktop row and wrap on smaller screens. The button initially reads “Date added: newest first” and opens the existing field/direction selectors in a Bootstrap dropdown. All original supported keys/defaults/ASC-DESC values remain available. Selectors retain their IDs, events and placement outside the search form; opening the menu sends no request. The only executable change is five display/accessibility lines in `syncSort` per view to keep the button truthful after selector/header sorting. This is the owner-requested sort-button presentation under the existing narrow frontend authorization; no request, response or business behavior change.

**Exact files:** the same six Payroll Items views, existing Payroll Items screen-scoped rules in `public/assets/css/style.css`, and this handoff. PHP tag sequences/configuration, dialog markup, row generation, financial formatting, search/pagination/request/CRUD functions and all other JavaScript compare identically with this refinement baseline. CSS preceding the existing Payroll Items block is byte-identical. HRIS Leave Management was inspected as a reference only and is unchanged.

| Fresh check | Result | Evidence / limits |
| --- | --- | --- |
| Six extracted JavaScript scripts | PASS | `node --check` for every view |
| PHP/executable/request/form scope | PASS | Ordered PHP tag sequence equality, unchanged config and complete dialog markup; removing only the five authorized sort-label lines makes each script byte-identical to baseline |
| Full diff, whitespace and protected trees | PASS | Exact seven application files plus this handoff; protected-tree diff empty and `git diff --check` clean |
| Browser fixture refinement checks | PASS — SIMULATED | **774/774** checks across six record and four Type workspaces: original sort keys/both directions, summary/header synchronization, menu open without requests, Escape/focus return, keyboard activation, Enter search/Clear, exact business-form metadata, desktop row alignment, targets, menu access, root overflow at 1440/1200/1199/992/991/768/767/576/575/375/320px and 200% CSS text enlargement. An initial harness expectation used the Type display title instead of the existing “Name” sort option; expectation corrected and final suite rerun. |
| Visual review | PASS — SIMULATED | Desktop/mobile source-derived screenshots inspected; reference header and compact toolbar hierarchy reproduced |
| Native PHP lint | BLOCKED | No PHP executable available; no runtime/dependency installed. Unchanged PHP source tokens are preservation evidence, not native lint. |
| Authenticated WSL/application/DB/permissions, screen reader, physical devices/other browsers, native zoom, payroll/print integration | NOT RUN | This cloud checkout cannot access the user's laptop. No database, real CRUD, payroll processing or production operation executed. |

Fresh fixtures/scripts/results/screenshots: `/tmp/payroll-items-refinement/` (ephemeral, fictional, outside Git). PHP is not executed by these fixtures; shared includes are omitted and shell geometry emulated. Actual jQuery/Bootstrap run with in-memory GET responses; no application or SQL endpoint executes. Historical build evidence below remains the 23 October record, not fresh integration evidence.

**Local review:** in the clean `~/projects/monterodental-payroll-items-review` checkout, fast-forward from `origin/codex/md-pay-items-ux-001-build` and refresh `http://payroll.localhost:8104/payroll/allowances` (then the other five pages and four Type tabs). Docker bind-mounts source, so no image rebuild or database reset is required. Keep the draft until authenticated acceptance and the existing senior-owned financial/security gates are addressed.

## Goal and acceptance criteria

Make existing Payroll Items easier to find, scan, compare and act on using Pearl & Teal, while retaining financial values and existing backend contracts.

- [x] All six page headers, descriptions, tables, search/sort controls and Add/Edit/Delete dialogs modernized.
- [x] Original form definitions, defaults, field values/constraints, CSRF metadata, URLs, HTTP methods, list query keys and server pagination preserved in source comparisons.
- [x] Supported sorts retained through visible headers and supplementary selectors; no client-only sorting/filtering.
- [x] Scoped screen CSS; protected backend, database, Payroll Runs, print, HRIS, shared navigation and dependencies unchanged.
- [x] Source-derived fictional browser checks and in-memory callback checks run without an application/database connection.
- [ ] Compatible native PHP lint and authenticated read-only integration.
- [ ] Real assistive-technology, other-browser/device and Payroll/print acceptance in an approved environment.

## Architecture, file inventory and Table UX Contract

Existing MVCLite/PHP views include the normal admin and Payroll navigation partials, render forms from pageConfig and use jQuery AJAX. Six dedicated views duplicate the CRUD renderer. The shared `app/views/module_payroll/partials/payroll_crud_view.php` was inspected and remains unchanged; no new shared application file is introduced.

Exactly seven application files and this documentation file are changed:

| File | Result | Purpose |
| --- | --- | --- |
| `app/views/module_payroll/allowances.php` | IMPLEMENTED | Employee/Type workspace, six-column records and five-column type table, recurrence, effective period, financial/type presentation and grouped dialogs |
| `app/views/module_payroll/cash_advances.php` | IMPLEMENTED | Advance, repayment/installment, prominent actual remaining balance, status and specialized three-section form; balance stays editable |
| `app/views/module_payroll/commissions.php` | IMPLEMENTED | Compact five-column records/four-column types, effective period, accurate default-value display and contextual dialogs |
| `app/views/module_payroll/compensations.php` | IMPLEMENTED | Salary Rate, separate Salary Type/Payroll Frequency, actual rate unit and grouped effective period; no earnings estimates |
| `app/views/module_payroll/deductions.php` | IMPLEMENTED | Six-column employee records, four-column types and grouped dialogs; monetary values use normal financial styling |
| `app/views/module_payroll/incentives.php` | IMPLEMENTED | Same coherent records/type treatment while retaining the original one-time default |
| `public/assets/css/style.css` | IMPLEMENTED | Additive `@media screen` rules scoped to `.payroll-items-page` and `.payroll-items-dialog`, including responsive layout, numeric alignment, local scrolling, reachable dialog footers and reduced motion |
| `docs/work-items/payroll-items-ux.md` | IMPLEMENTED | This single task handoff, contracts, evidence, limitations and transfer instructions |

**Routes / tables:** `/payroll/{allowances,cash-advances,commissions,compensations,deductions,incentives}`. Four corresponding Type workspaces remain; Cash Advances and Compensations have no Type tab.

**User tasks:** find by existing server search/ordering; scan employee name/number and type name/code; compare actual financial amounts/periods; invoke the intended existing Edit/Delete or contextual Add action.

**Identifiers and ordered presentation:**

- Allowances/Deductions/Incentives: Employee, respective Type, Amount, Recurrence, Effective Period, Actions.
- Commissions: Employee, Commission Type, Amount, Effective Period, Actions.
- Compensations: Employee, Salary Rate, Salary Type, Payroll Frequency, Effective Period, Actions.
- Cash Advances: Employee, Advance Amount, Repayment (deduction type + installment amount), Remaining Balance, Status, Actions.
- Types: respective Type (name + code), Computation, Default Value, Taxable (Allowances only), Actions.
- Created At remains visible as secondary “Added” metadata and available in ordering. Both effective dates and repayment fields remain visible together, with independent original sorts available.

**Existing actions / visibility:** direct Edit and Delete remain on every row as in the original six views; Type markup remains conditional on existing `hasTypes`. No new permission predicates, access grants or frontend masking. Backend authorization remains authoritative and unmodified.

**Find/order/page contract:** existing GETs use exactly `page`, `search`, `sort_by`, `sort_dir`; default `created_at DESC`. Controller page size remains ten. Each original view sort remains; model-supported `employee_no` and `type_code` are also exposed where applicable. Original keys and ASC/DESC values are used. Sorting is always server-backed. Page context reports current-array length and server page counts, never an inferred dataset total. Pagination uses a bounded first/current/neighbors/last window plus Previous/Next; every page remains reachable.

**Responsive presentation:** semantic tables with row/column headers, visible header sort buttons, truthful aria-sort for the visible active key, full sort selectors and locally scrollable named keyboard regions. Toolbars/pagination wrap outside the table region. Forms use one/two columns and Bootstrap small-screen fullscreen dialogs; headers/footers remain reachable while the body scrolls. Existing shell/sidebar code is unchanged.

**Approved executable regions:** only page-local display helpers, row/header composition, option-label presentation, sort-control synchronization, list lifecycle feedback, detail-load/option readiness, dialog grouping/context/focus/dirty/pending guards and existing AJAX callbacks supporting these features. Original PHP configuration arrays and field-generation decisions/constraints remain intact. The sole extra PHP decision displays the required indicator from existing requiredness. No executable PHP business/permission/query logic changes.

**Functional proposals:** none implemented beyond owner v2 authorization. Backend-dependent semantics below are explicitly deferred. No bulk actions, new filters, mobile-card alternative, column hiding, computed totals or new endpoints.

## Implemented interaction and accuracy decisions

- Active Records/Types tabs select the contextual header Add action without changing Bootstrap IDs/targets or list state.
- Existing native search submission supports Enter; Clear resets the existing server search. Applied-search text, local loading, empty, no-match and error/retry states are distinct. Request generation numbers prevent stale list replies overwriting a newer search/order/page.
- Edit shows an empty loading dialog before filling the selected record. Fields remain hidden until detail and required option lists are ready, preventing stale records/late option replacement. Failed loads cannot submit. Reopening retries read-only choices/details.
- Labels wrap their existing controls. Fieldsets, required indicators, descriptions and dialog labels add hierarchy without changing field names, types, defaults, option values or validation. Human-readable select labels keep their raw values.
- Save/Update/Delete use a dialog-local pending guard, explicit progress, inert presentation fields and disabled submit/confirmation buttons. Inputs are never disabled for serialization, so payload ownership/values remain intact. Pending dialogs cannot close. Only explicit server success closes/refreshes and announces success; explicit rejection preserves entries and shows the genuine server message.
- Network/parsing/ambiguous mutation outcomes do not trigger retry. The dialog explains the uncertainty and blocks resubmission until closed and records checked. This cannot prove database persistence; it trusts the unchanged server contract.
- Dirty close offers Stay Editing / Discard Changes using the existing modal. Bootstrap retains ownership of focus trapping; a page-local Tab boundary guard prevents focus leaving at the final/first control. Delete initially focuses Cancel. Closing returns to the origin or a contextual Add fallback if refreshed rows replaced it.
- Monetary display formats decimal strings with the peso symbol and grouping, preserving all source fractional digits and avoiding Number conversion/rounding. Raw numeric form values are unchanged. Zero, absent values and unknown values remain distinct.
- Fixed type defaults use their existing monetary meaning. Percentage/unknown defaults retain the original numeric representation plus “Stored value”; no percent scaling/base is inferred.
- Calendar date display uses direct `YYYY-MM-DD` parts, with no Date/timezone conversion. Raw date inputs/API values and Created At timestamps are unchanged. Missing end dates read “End date not set.”
- Salary units label the actual DAILY/HOURLY/MONTHLY rate; no multiplication or pay-period estimate. Cash balances/statuses are displayed from response fields, never recalculated.

## Preserved contracts and boundaries

All original per-page pageConfig arrays compare equal to baseline, including URLs, record/type field definitions, requiredness, defaults, numeric step/minimum, select values and original default orders. AJAX URL expressions and HTTP-method inventories compare equal. Source-derived before/after DOM metadata comparisons retain form IDs, field names/types/constraints/defaults/values and CSRF fields.

- List and option/detail endpoints remain the original configured GET URLs.
- Create remains POST to `recordCreate`/`typeCreate` with the original form serialization.
- Update remains POST to `recordUpdate + id` / `typeUpdate + id`, excluding `id` from serialized body through the original `formDataWithoutId` helper.
- Delete remains POST to `recordDelete + id` / `typeDelete + id` with `{ csrf_token: csrfToken() }`.
- Existing response fields remain authoritative. Presentation callbacks distinguish explicit success/rejection and uncertain outcomes; no endpoint, payload or response contract is added.
- Tenant/branch/action permissions, financial formulas, balance persistence, compensation editing, payroll cutoffs, record page size, calculations and all backend validation remain unchanged.
- No database connection, migration, schema/seed/reset, CRUD HTTP submission, processing, import, production access or deployment was performed. No application dependency was installed.

## Verification evidence

Evidence is deliberately separated by method. Browser fixtures use source-derived view HTML, the original configuration/form metadata, existing Bootstrap 5.3.3/jQuery 3.7.1 assets and **fictional in-memory AJAX**. They omit PHP includes and emulate shell geometry. They do not execute PHP, sessions, middleware, SQL, tenant policies or database persistence. They cannot certify real application integration or WCAG conformance.

**Final fixture run: 850/850 PASS** (323 layout/workflow, 324 state/keyboard, 189 ordering/race/callback/CSS/token and 14 computed-visual checks). Earlier keyboard failures were repaired and the entire suite rerun on final application code. Test-harness issues were corrected without expanding application scope.

Local evidence and test scripts are under `/tmp/payroll-items-evidence/` and `/tmp/test-payroll-{ui,states,final}.py`; they are ephemeral, outside Git and contain only fictional fixtures. The application acquires no test/build dependency. Before/after images are explicitly SIMULATED, not authenticated application screenshots.

| Check | Result | Method / evidence / limits |
| --- | --- | --- |
| Live baseline, worktrees, branch ownership, unrelated drafts | PASS | Fetch + Git branch/worktree/index/status and GitHub PR metadata; only this cloud checkout exists here |
| Exact allowlist / protected trees / full diff / whitespace | PASS | Baseline diff, seven application-file scope, protected-tree `git diff --exit-code`, `git diff --check`; same audit required after commits |
| Original config, URLs, methods, field rules and CSRF metadata | PASS | Baseline/current source comparison on all six pages; DOM metadata comparison in fixtures |
| JavaScript syntax | PASS | `node --check` on extracted page scripts for all six views |
| Scoped CSS parsing and tokens | PASS | Chromium CSSOM plus additive-scope/source review; no baseline stylesheet rules edited |
| Responsive browser fixtures | PASS | Six pages at 1440/1200/1199/992/991/768/767/576/575/375/320px; root overflow <=1px, row targets >=44px, local tables retain all columns |
| Short mobile dialog and footer | PASS | Fixtures at 1440/768/375/320px, 667px height; footer bounding boxes in viewport |
| Search/Clear/sort/page/tab/context actions | PASS | Fictional GET-request objects, all record/type supported keys and both directions, default order, tab actions and page navigation |
| Formatting / zero / precision / types / dates | PASS | Fictional actual renderer assertions, large decimal strings, four-plus decimal places, raw form values, neutral percentages and calendar dates |
| Loading/empty/error/read retry/stale replies | PASS | SIMULATED responses; request-generation and readiness cases |
| Form groups / requiredness / option values / preserved defaults | PASS | Source/fixture comparison, including Incentives default 0 and Allowances/Deductions default 1 |
| Modal keyboard / dirty close / focus restoration | PASS | Real Chromium keyboard events inside simulated DOM, Tab/Shift+Tab, Stay/Discard and Cancel-focused confirmation; boundary defect found and fixed, final retest passes |
| Duplicate pending / known rejection / uncertain result / confirmed success | PASS | In-memory AJAX-call/callback assertions only; no mutation HTTP or database changes |
| Contrast samples / focus / target objective | PASS | Approved token contrast calculation, computed-style samples and fixture target/focus measurements; limited samples, not a conformance claim |
| Native PHP 8.3+ syntax lint | BLOCKED | `php -v`: executable not found; no runtime/dependency installation authorized or performed |
| Authenticated read-only six-page integration / actual role and tenant cases | BLOCKED | No approved running PHP/database application available; no startup/seeding attempted |
| Real CRUD persistence and database mutation paths | BLOCKED | Owner expressly excludes financial-data mutation tests; needs separate disposable fictional-data authorization |
| Payroll calculation/output and payslip/print comparison | NOT RUN | Protected source unchanged; no authenticated runtime, processing or print/PDF test performed |
| Cross-module shared shell / HRIS / login visual comparison | NOT RUN | No authenticated runtime; source scope shows no relevant paths changed, fixture shell is an emulation |
| Screen reader, physical touch device, Firefox/Safari, native browser text zoom | NOT RUN | Chromium fixtures only; 200% CSS text enlargement tested, not native zoom or assistive technology |

**MTD mapping:** PRESENTATION/approved functional implementation applies to MTD-01–18 and MTD-20–30. MTD-19 selection is NOT RUN / not applicable: no row selection or bulk state exists. MTD-24 alternative mobile cards is NOT RUN / intentionally excluded. Source/fixture portions pass for MTD-01–18, 20–23 and 25–30; real screen-reader/integration/print/persistence portions remain BLOCKED or NOT RUN as stated above. No video-watching or full accessibility-compliance claim is made.

## FINDINGS_FOR_SENIOR_REVIEW

These are source observations and owner-review questions, not backend fixes or a security certification.

1. **Cash Advance Remaining Balance — DEFERRED_BACKEND_APPROVAL.** The controller accepts an optional editable balance, substitutes amount when missing, and nulls installment amount for SINGLE. Ownership of balance changes versus repayments/status transitions is a business decision. The UI retains the field, requiredness and submission without computing a balance or repayment progress.
2. **Fixed/Percentage defaults — DEFERRED_BACKEND_APPROVAL.** Type rules accept numeric `default_value` and FIXED/PERCENTAGE. A verified percentage storage scale/base is unavailable; no scale conversion was made. The current allowance calculation uses amount/default fallback and frequency without establishing a generic percentage base. Confirm all type semantics before adding `%`, estimates or more explanatory financial claims.
3. **Compensation permissions and ownership — DEFERRED_BACKEND_APPROVAL.** Payroll Items routes are in Auth/Tenant/PAYROLL-module middleware but have no per-action compensation permission or BranchMiddleware. The model is tenant constrained, without assigned-branch filtering. HRIS also edits `payroll_employee_compensations`. Agree a permission and HRIS/Payroll ownership matrix separately. No client-side hiding/access changes were made.
4. **Delete effects — DEFERRED_BACKEND_APPROVAL.** Models issue hard DELETEs. Effects on configurations already referenced by processed/reprocessed payroll are not proven by frontend inspection. Confirmation identifies the target and makes no restore, archive or harmless-history promise.
5. **Type references — DEFERRED_BACKEND_APPROVAL.** No model-level reference-count protection was observed in type deletion. Actual remote FK/dependency behavior is unverified; the development reconstructed schema is not production authority. Options allow tenant/global types, while tenant-only type list/edit queries differ; preserve this existing distinction.
6. **Tenant/branch visibility — DEFERRED_BACKEND_APPROVAL.** Current records/employee joins and mutations use tenant constraints, and reference validation checks tenant membership. Branch restrictions are absent in these listing/options queries. Review intended policy and representative allowed/denied cases; frontend work is not row-level authorization proof.
7. **Historic versus future payroll — DEFERRED_BACKEND_APPROVAL.** Payroll processing reads live compensation/allowance configuration at cutoffs and uses run snapshots/items. The impact of later editing/deleting or reprocessing against prior runs requires senior confirmation. No process/rebuild/finalize operation was run.
8. **Outcome truth and existing financial limitations — DEFERRED_BACKEND_APPROVAL.** Model UPDATE/DELETE methods return PDO execute success, not necessarily affected-record proof. Frontend success remains dependent on that unchanged contract. Existing PayrollRun assumptions, recurring/frequency handling and incomplete adjustment pipeline are outside this task. No calculation/security repair or import from Draft PR #8 is included.

**DEFERRED_FRONTEND_AUTHORIZATION:** none of the enumerated safe frontend interactions needed a broader file allowlist. A shared renderer extraction would require authorization for the protected partial; duplicated page-local implementations are deliberately retained. Field-specific server validation cannot be fabricated from the current summary-message response; the genuine summary is shown in the dialog instead.

## Production-transfer handoff and next action

1. Review this draft and its exact eight-file baseline diff; run compatible `php -l` on the six views first.
2. Use an already approved isolated fictional runtime for authenticated read-only page/role/tenant/branch, keyboard, responsive, contrast, HRIS/Payroll and print checks. Inspect startup hooks before running anything; this task does not authorize seeds, schema writes or financial mutations.
3. If persistence testing is needed, obtain separate approval for disposable fictional data. Resolve the senior-owned permission/financial questions through a separate backend task.
4. Compare an independently authorized fresh production source snapshot against both development baseline and these commits. Production is separate and may have drifted. Do not copy whole historical files over newer work.
5. Reconcile scoped CSS carefully: Organization and Attendance drafts also append to `style.css`. Preserve both independently reviewed blocks and the accepted Pearl & Teal tokens; do not wholesale replace the stylesheet. Current shared includes/Bootstrap/jQuery, endpoint response fields and original form definitions are runtime dependencies.
6. Transfer only reviewed changes after reconciliation, approved testing, recovery planning and separate release authorization. A presentation rollback is a reviewed reverse of this scoped diff, accounting for later changes; no database reset or production overwrite.

For an existing clean WSL clone at `~/projects/monterodental`, once this branch is published and the target path is absent:

```bash
git -C ~/projects/monterodental fetch origin codex/md-pay-items-ux-001-build
git -C ~/projects/monterodental worktree list
git -C ~/projects/monterodental worktree add --track -b codex/md-pay-items-ux-001-build \
  ~/projects/monterodental-payroll-items-review origin/codex/md-pay-items-ux-001-build
```

If the local branch already exists and is not checked out elsewhere, use `git worktree add ~/projects/monterodental-payroll-items-review codex/md-pay-items-ux-001-build` instead. Inspect/reuse an existing worktree; never overwrite it. Do not run either add command when its branch/path is already owned. Original Employees/Organization/Attendance worktrees remain independent.

**Next concrete action:** owner/senior reviews the development draft and runs native PHP lint plus authenticated read-only verification before deciding merge eligibility. No merge, production transfer or deployment is authorized by this handoff.
