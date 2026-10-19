# Pattern selection, screen mapping and UX contract examples

## Begin with the user's job, not the UI effect

| Main user task | Prefer | Avoid |
|---|---|---|
| Compare multiple staff by date/status/branch | Conventional aligned table, prominent Employee No/name, sort/search presentation | Cards replacing comparable columns; unreadably tiny text |
| Find a particular employee | Wide discoverable search, stable identity, readable name/identifier and existing pagination | Turning every field into a colorful badge |
| Check DTR or attendance for a date range | Existing matrix/list presentation, stable date/employee context, locally scrollable width | Silent date format/timezone changes |
| Review payroll amounts | Right-aligned numbers, tabular figures, existing exact monetary formats and totals, clear identifiers | Added calculated totals, changed rounding, status/color invention |
| Review catalog names and status | Clear name/record hierarchy, compact consistent status/action area | New sort/filter logic without approval |
| Inspect one record in isolation on narrow screens | Propose optional record detail/card mode when separately authorized | Assuming cards always outperform row comparison |

## Decision tree

1. **Does cross-record comparison matter?** If YES, default to semantic table and preserved columns (MTD-01, 08, 21). If NO, a supplemental card/detail pattern can be *proposed*, not built under styling-only scope.
2. **Are multiple fields wider than the viewport?** If YES, use a locally scrollable table container; keep toolbar and pagination outside. Do not hide a business column or clip the page (MTD-21, 22).
3. **Do controls exist already?** If YES, make their search/filter/sort and pagination affordances noticeable and accessible while preserving mechanics. If NO, add the idea to the functional backlog rather than fabricate controls (MTD-14–20).
4. **Does a row have destructive or permission-conditioned actions?** If YES, retain exact permission and confirmation behavior, keep actions distinguishable and ensure reachable targets (MTD-11–13).
5. **Are there numeric, payroll, attendance, or date values?** If YES, protect precision, semantics, locale, units and print output (MTD-08–10, 28).
6. **Does the requested change require JS/PHP executable logic or DataTables initialization?** If YES, stop on that part, specify it separately and request explicit authorization. Continue independently approved CSS/static-view refinements.

## Montero Dental actual screens (inventory hypotheses; verify against current code)

These entries are **planning anchors from approved repo documents**, not claims that all current source was runtime-tested. Inspect before writing an actual Contract.

| Screen | Existing documented fields / user task | UX attention | Hard preservation |
|---|---|---|---|
| `app/views/module_hris/employees.php` | Six columns: Employee No, Name, Branch, Date Hired, Status, Actions; search/Clear; five sorts; pagination; four existing action definitions | Identifier emphasis, searchable toolbar, sort signifiers, status label, action reachability, local scroll | Generated rows/actions, handler/event delegation, permission predicates, six columns, dialogs, form/requests, same sort and page semantics |
| `app/views/module_hris/dtr.php` | Daily time records / attendance inspection | Row/date identity, numeric/time alignment, overflow | Time/date semantics and totals, existing filter and data query |
| `app/views/module_hris/leaves.php` | Leave requests | Readable dates and statuses, safe actions | Meaning of leave/status, permissions and action flow |
| `app/views/module_hris/branches.php`, `app/views/module_hris/Position.php` | Branch and position catalog tables | Salient existing name, consistent existing actions | Exact catalog fields and endpoints |
| `app/views/module_hris/OrgUnits.php` | Offices / organizational units are primarily a hierarchical tree; a separate employee-detail table appears in a dialog | Preserve hierarchy, expand/collapse controls and tree interactions; apply table rules to the employee-detail table only where applicable | Existing tree structure, generated markup, employee-detail columns, actions and endpoints; no conversion to a management table |
| `app/views/module_hris/holidays.php` | Holiday catalog table | Existing date/name hierarchy, sort affordances, readable pay multiplier and allow-work values | Exact dates, holiday types, multipliers, allow-work semantics, sort/request/action behavior |
| `app/views/module_hris/shifts.php` | Shift catalog table | Existing shift identity, readable times, hours and related settings | Exact fixed/flexible time semantics, hours, breaks, overtime fields, actions and request behavior |
| `app/views/module_hris/appointments.php` | Employment-type catalog — NOT patient appointments | Consistent list readability | No patient-facing scheduling invention |
| `app/views/module_payroll/payroll_run_index.php` / `app/views/module_payroll/payroll_run_view.php` | Payroll run listing/review | Numeric comparisons, headings, status clarity | Payroll/run status, calculations, print content, monetary values and sensitive actions |
| `app/views/module_payroll/payroll_employee_view.php` | Employee payroll detail | Tabular amounts, preserved print alignment | Earnings/deductions, rounding, date and identity |
| `app/views/module_payroll/importView.php` | Import preview/results | Dense inspection, failure-vs-empty feedback | No import submission/schema/validation changes |
| `app/views/users.php` / `app/views/tenants.php` | Admin records | Names, roles, accessible row actions | Authorization, tenant boundary, permission visibility |

All `app/views` paths are **reference paths**, NOT an implementation allowlist. Some tables are server-rendered; others rely on existing JS/AJAX or DataTables. Inspect each. Avoid a single global CSS selector that unexpectedly changes unrelated screens, public login, dialogs or print.

## CSS and semantics guidance for separately approved BUILD

- Put styling in the project's existing `public/assets/css/style.css`, scoped to the existing `.admin-shell` or actual page/component selectors identified in source. Follow the frontend plan's exact allowlist. Never install Tailwind, React, a table library, or new dependency to achieve cosmetic effects.
- Use established tokens from the [approved Pearl & Teal design system](../../../../docs/work-items/frontend-modernization/PEARL-TEAL-DESIGN-SYSTEM.md):
  - primary `#11685D`, hover `#0D544B`, tint `#E6F3EF`, canvas `#F4F7F7`, surface `#FFFFFF`, text `#193330`, muted `#5C716D`, divider `#D9E4E1`, control border `#738C85`, focus `#087F8C`.
- Reuse Bootstrap table variants/classes when they suit actual markup. Inspect computed `--bs-table-*` values rather than assuming `--bs-primary` alone updates components.
- Scope row hover and selection to existing interactive states. Cursor `pointer` only when something actually activates.
- Use `font-variant-numeric: tabular-nums` for numeric alignment where supported without altering values.
- Use text wrapping for long names and preserve real values. Do not use CSS truncation as a substitute for access to full information unless a separately accepted accessible disclosure exists.
- Native `<table>` should remain native. For new markup (only under proper authority), use `<caption>`, `<th scope="col">`, native buttons for current sorts, and `aria-sort` only where existing code truthfully updates it. Avoid duplicating unsupported ARIA on passive headers.
- A scrollable wrapper for wide tables should be keyboard reachable when keyboard scrolling is otherwise inaccessible. Validate focus behavior; a bare wrapper with `tabindex=0` may require a meaningful accessible description. Do not create a focus trap.
- Print requires a separate check: screen-only cosmetics should not cause altered print contents, numbers, columns or page breaks.

## Proposed enhancements backlog template (not BUILD authority)

| Proposal | Why useful | Additional code / risk | Separate acceptance needed |
|---|---|---|---|
| Column-scoped filters inside header | Filter location matches field being filtered | JS/AJAX query wiring, keyboard menu and server filtering may change | Filter request/clear, permissions, cross-page results, keyboard |
| Mobile record cards (alternate view) | Easier single-record inspection | Extra markup/rendering, data mapping, actions; may hurt comparisons | Full attribute/action/sort parity, semantic labels, default view research |
| Column chooser / personalization | Handle very wide lists | Stateful visibility/accessibility, possible omitted critical data | All columns accessible, reset/persistence, print and export parity |
| Sticky first identity column | Maintain record context during horizontal scroll | CSS stacking, overlaps, focus/zoom, print conflicts | 320px zoom, no occlusion, screen reader, print preservation |
| Persistent bulk selection | Act on many items | Authorization, dangerous mutations, selection across pages | Explicit permissions, confirmation, payload/data scope |
| New sort or search capabilities | Locate records faster | Backend/DataTables behavior, request contract and performance | Stable semantics, parameters, tenant scope, validation, pagination |

Do not include any of the above as implemented functionality in a documentation-only or CSS-only completion report.
