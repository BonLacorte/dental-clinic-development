# Normative Data Table UX Rules — Montero Dental

Use **MUST** for acceptance-critical presentation/preservation requirements, **SHOULD** for strong defaults that may be overridden by evidence, and **MAY** for conditional ideas. Every applicable MUST requires verification evidence in a future approved BUILD. All rules apply **within authorized scope only**: if satisfying a rule requires changes to protected functional logic, record a proposal and do not modify it.

The source tags below mean: **V2** = publisher-page-supported and third-party, potentially machine-generated transcript-derived ideas for video 2 (transcript accuracy unverified against footage), **V3** = published description for video 3, **V4** = published description for video 4; **W3C**, **USWDS**, **NNG** = independent evidence; **P** = project-specific requirement / adopted judgment. These tags are not claims that video 1 or video 5 were inspected.

## Structure, recognition, scanning

| ID | Level | Operational rule | Acceptance evidence | Basis |
|---|---|---|---|---|
| MTD-01 | MUST | Use a semantic `<table>` for genuinely tabular data; preserve `<thead>`, `<tbody>`, `<th scope="col">` and applicable row headers. Do not convert tables to generic clickable `<div>` grids for cosmetics. | DOM inspection; header-cell association; screen-reader/keyboard spot check where available. | W3C, USWDS |
| MTD-02 | MUST | Give the dataset an identifiable visible title and unambiguous column labels; existing labels/columns may not be changed by styling-only work. A programmatic caption or labelled relationship SHOULD describe table context where allowed. | Table context understandable without guessing; diff confirms business columns/labels unchanged. | W3C, P |
| MTD-03 | MUST | Visually distinguish the header from records with existing Pearl & Teal tokens and readable type. Distinction must survive non-color/contrast review. | Header identifiable in grayscale and at 200% zoom. | V2, P |
| MTD-04 | MUST | Highlight the primary **existing** record identifier using weight and consistent placement, not extra data or arbitrary invented links. | Employee No/Name, run ID or catalog name recognized quickly; no value/route change. | V2, NNG |
| MTD-05 | MUST | Use restrained, legible row/cell separators. Dividers are subtle decoration, not input/control boundaries; avoid full dark grids dominating data. | Readable row tracking at desktop, tablet, zoom. | V2, P |
| MTD-06 | MUST | Use consistent padding, alignment and minimum legible type. Never shrink values below readable font sizes just to squeeze columns. | Text wraps without overlapping; 200% zoom; full cell text accessible. | V2, P |
| MTD-07 | SHOULD | Use row-hover/focus cues where existing row interactions support them; do not imply a row is clickable if it is not. Sticky headers are OPTIONAL only if existing scroll containment and stacking remain intact. | Pointer/focus tests; no false affordance; z-index/print checks where sticky. | V2, NNG, P |

## Value precision, formatting, statuses and actions

| ID | Level | Operational rule | Acceptance evidence | Basis |
|---|---|---|---|---|
| MTD-08 | MUST | Align comparable numbers and monetary figures to the right, with tabular numerals where supported; keep their **existing** precision, sign, locale and units exactly unchanged. | Digit placement scan; visual and raw value/format preservation comparison. | V2, P |
| MTD-09 | MUST | Preserve dates exactly as the current business presentation/contract renders them. **Do not silently reformat** ambiguous dates or timestamps. If `DD MMM YYYY` is proposed for human readability, require separate approval and verify ambiguity/locale/timezone impacts. | Representative same-date snapshots unchanged, or separate change authority recorded. | V2 idea with P safety override |
| MTD-10 | MUST | Statuses must communicate meaning using existing text plus visual treatment; color alone cannot distinguish status. Never infer, rename, merge, or fabricate business states. | Text readable in grayscale; same raw/status labels and authorized meanings. | V2, W3C, P |
| MTD-11 | MUST | Icon-only row actions must have accessible names and visible keyboard focus; action identity and destructive nature must remain clear. Do not remove visible text labels merely because a video prefers less textual noise. | Screen-reader/keyboard names, 44px target objective, no hidden/unreachable action. | V2 qualified by W3C/P |
| MTD-12 | MUST | Preserve the row-to-action association and existing permission-conditioned visibility; icon swapping or moving action DOM must not change handlers, modal triggers, CSRF/form ownership or event delegation. | Same actions per permission case, same targets/handlers, click/touch/keyboard smoke tests. | P |
| MTD-13 | SHOULD | Group existing low-frequency actions consistently without adding new menus; destructive options MUST retain original meaning, visibility and confirmation. No tooltip-only essential instruction. | Find and invoke correct row action; close/return focus remains sound. | NNG, P |

## Search, filters, sort, selection, pagination

| ID | Level | Operational rule | Acceptance evidence | Basis |
|---|---|---|---|---|
| MTD-14 | MUST | Existing search must be noticeable, adjacent to its table, labeled and keyboard accessible. Styling must preserve input name/ID, debounce/submit/reset behavior, query parameters and entered values. | Search and Clear snapshots match baseline with the same query. | V2, NNG, P |
| MTD-15 | MUST | Existing filters must make **active state discoverable**, including text/check-state indicators not exclusively color. Layout may place existing filter affordances closer to relevant headers ONLY if static DOM movement is safe; avoid adding a new filter behavior. | Active filter comprehensible; same filtered results and same request payload. | V3, NNG, P |
| MTD-16 | MUST | Current sort affordances must be visible and accurately reflect existing sortability/direction; never show a sort icon for an unsortable column. Cosmetic work cannot introduce sorting or reverse its meaning. | Same columns and direction cycles; icon + label + focus accurate. | V2, W3C, P |
| MTD-17 | SHOULD | For an **already implemented** sortable header, prefer a keyboard-native `<button>` and `aria-sort` only where approved markup/JS can keep state truthful. If it needs JS changes, defer rather than add stale ARIA. | Same active header, correct ascending/descending announcements when implemented. | W3C, USWDS |
| MTD-18 | MUST | Existing pagination must retain page size, result context, page count, current-page semantics and request flow. Active page and disabled navigation must be visually clear and keyboard operable. | Page N, first/last, next/previous, search+sort+page cases unchanged. | NNG, P |
| MTD-19 | MUST | Selected-row highlighting MUST correspond to a **real existing selection state**. Do not visually simulate checkbox selection or enable new bulk actions under styling approval. | Checkbox, selected row and bulk toolbar agree; or rule marked N/A. | V2, P |
| MTD-20 | MUST | Existing loading, no-records, no-matches, failure and retry outcomes must remain distinguishable where currently implemented; don't turn an error into a successful empty state. | Baseline behavior/state text preserved; no invented outcomes or fake counts. | NNG, P |

## Large datasets, responsive and mobile

| ID | Level | Operational rule | Acceptance evidence | Basis |
|---|---|---|---|---|
| MTD-21 | MUST | All existing business columns, original values, labels, order and row actions remain reachable at every viewport. Default to **local horizontal scrolling inside the table container** for wide tables. | 320/375/768/992/1440px; no missing columns or clipped actions; page root does not spuriously scroll horizontally. | W3C, P |
| MTD-22 | MUST | Search, filters, title, action toolbar and pagination reflow **outside** the local table-scrolling surface. Never fix overflow with body-level `overflow-x: hidden`. | At 320px, headings/toolbars/pagination fit the viewport and table alone scrolls locally. | W3C, P |
| MTD-23 | SHOULD | Use a predictable column priority for **visual emphasis**, not for deletion; reinforce the record identifier (e.g., employee number/name), context fields, status and existing actions. Avoid sticky first column unless verified not to obscure other cells. | Cross-row comparisons still possible; sticky overlay check if applied. | NNG, P |
| MTD-24 | MAY | Mobile stacked-record cards MAY be proposed **as a separately approved functional/markup change** when the primary task is inspecting one record. Do not activate cards as the default for payroll, numerical comparison or very wide auditing tables; all attributes, actions, search and sort need parity. | Explicit task research, screen mode acceptance, parity tests and rollback plan. | V4, USWDS, NNG, P |
| MTD-25 | SHOULD | Present large datasets with existing pagination and sensible table density rather than rendering every row. Never change server-side page-size limits or fetch all rows for cosmetics. | Scrolling smooth under representative allowed fixtures; no request/count drift. | NNG, P |

## Accessibility, safety and evidence

| ID | Level | Operational rule | Acceptance evidence | Basis |
|---|---|---|---|---|
| MTD-26 | MUST | Changed UI targets WCAG 2.2 AA: normal text contrast ≥4.5:1; large text ≥3:1; necessary non-text graphics and focus boundaries ≥3:1. Focus is visible and unobscured. **44px touch target remains the project's design objective**, not a claim about a WCAG pass. | Computed contrast samples, keyboard focus observations, target bounding boxes. | W3C, P |
| MTD-27 | MUST | Every applicable control is accessible by keyboard alone. Icon controls have programmatic names. Scrollable regions that require keyboard scrolling must be discoverable and operable without trapping focus. | Tab/Shift+Tab, Enter/Space, local scroll keyboard test, focus return. | USWDS, W3C |
| MTD-28 | MUST | Preserve print/payroll rendering: screen-only table decoration may not modify financial values, print break/width, payslips, business totals, imported data or exported content. | Print preview/PDF read-only comparison where relevant; no calculation or markup content drift. | P |
| MTD-29 | MUST | Only authorized files and static presentation changes; preserve executable PHP/JS, selectors, data attributes, requests, permissions, interactions, databases, assets and dependency versions. | Full git diff, exact allowlist, protected-region comparison, runtime checks where permitted. | P |
| MTD-30 | MUST | Verification claims use **PASS / FAIL / BLOCKED / NOT RUN / N/A** with method and scope. No stage may claim all five videos were analyzed until all five are actually reviewed; historical examples are not current test results. | Review handoff with source provenance and honest environment limits. | P |

## Defaults (not absolute rules)

- **Readability-first density:** begin around 48–56px line-item height and 12–16px horizontal cell padding, then test the real screen/zoom. Adjust density using the existing approved spacing scale; a dense attendance/payroll table may need a different reviewed value. Do not inflate rows solely to satisfy a video.
- **Search layout:** when search already exists, allocate useful width on desktop and allow it to fill its row on mobile; retain the current accessible label and behavior.
- **Header styling:** subtle Pearl & Teal-tinted header with a consistent bottom divider; avoid dark/heavy boxed grids.
- **Primary identifier:** give the current Employee No or name discernible emphasis. Never turn it into a navigation link without route/permission approval.
- **Monetary columns:** visual right alignment and tabular numeral features are allowed when they do not change actual formatting or calculation. Avoid `text-transform`, CSS-generated currency symbols or pseudo-elements representing data.
- **Status:** badge treatment for *existing* status text and semantic meaning; never map all statuses to teal or rely on red/yellow/green alone.
- **Actions:** avoid pure icon-only treatment on obscure or destructive operations without accessible names and discoverability testing; don't compress controls below the touch target objective.

## Red flags — stop or re-scope

An improvement needs separate functional approval if it requires: adding/removing/reordering real business columns, column visibility preferences, new per-header filters, new debounced search, pagination or sort logic, selection persistence, new row action menus, expandable details, mobile card data mapping, AJAX/DataTables configuration changes, changes to server outputs, new badges/status vocabulary, or new calculations. Record each separately as a **proposal** with user task, expected benefit, required code surfaces, behavioral risk and acceptance tests.
