# Organization and DTR owner UI corrections

- Original task: `MD-HRIS-ORG-DTR-OWNER-CORRECTIONS-001`.
- Authorized completion: `MD-HRIS-ORG-DELETE-DIALOG-COMPLETION-001`.
- Branch: `codex/md-hris-consistency-integration`.
- Baseline/unchanged HEAD: `625346d32f7f2d1ef6198ecdcae733715f0ca25e`.
- Status: `ORG_DTR_OWNER_CORRECTIONS_COMPLETE — AWAITING_OWNER_REVIEW`.
- Authority: the owner's Stage 2T-B request and nine attached screenshots. This batch permits static presentation and only three functional view changes: Positions column/lookup mapping, Employment Types column mapping, and the DTR no-selection conditional. The subsequent completion request explicitly permits selected Branch identity binding and the Office Bootstrap confirmation, including necessary view-local state, handlers and text-safe updates. No publication or broader backend/functionality authority.

## Completed changes, in the requested sequence

### Branches

The existing Add form already retained the original vertical Name/Code/Address arrangement. Kept that structure, control types, IDs, validation, CSRF and submit logic. Restored the reference placeholder text and added opt-in Organization dialog styling for Add and the existing compact Delete modal. The Edit form, list values and paging remain unchanged; listing cells gain only stable identity classes and a row ID attribute for the approved confirmation binding.

The existing Delete modal now displays the selected branch name and code, emphasized, followed by the irreversible warning. Identity comes from the clicked row's escaped `.branch-name`/`.branch-code` text and a stable `data-branch-id`, checked against the button ID. No additional request is made. `.text()` inserts the identity; optional absent code is omitted rather than invented. Resolution requires a live row, valid positive ID and nonempty name; confirmation rechecks ID/name/code to reject stale selections. Pending selection is consumed before dispatch, and a busy guard prevents repeated requests. Cancel, close and modal hide clear pending state and disable confirmation. Existing static-backdrop/Escape-disabled policy is preserved. Cancel receives initial focus; Tab/Shift+Tab wrap within this dialog and dismissal restores the trigger, or the Add button if the trigger is unavailable. Confirmed deletion returns to the stable Add button before the list refresh. The original AJAX request, CSRF, loaders, callbacks and current-page refresh are preserved; returning its jqXHR only supports clearing the busy guard.

### Offices / organizational units

The published and original Add/Edit forms already have the requested 50/50 Parent/Type row and 2:1 Name/Code row in centered `modal-lg` dialogs. Preserved this structure, parent options, types, IDs, hidden fields and loaded values; restored Add placeholders and applied opt-in dialog spacing/footer styling. Desktop grouping and mobile stacking verified.

Office tree rendering, traversal, expansion/collapse, parent-choice loading, Edit behavior and View Unit Employees are unchanged. The separately authorized completion replaces native `confirm()` with a centered, rounded Bootstrap Delete Unit modal: emphasized actual name, irreversible warning, Cancel and red Delete Unit. The existing `deleteOffice` entry now opens confirmation only, resolving exactly one matching positive ID and nonempty name from `orgUnitsFlat`. `.text()` inserts the name; missing, ambiguous or changed selection fails closed. The sole existing action caller supplies its trigger for focus return. A consuming confirm handler revalidates, clears pending state and blocks repeated requests. The original AJAX body is retained in a helper private to the existing ready callback, with only the confirmation handler able to call it; direct calls to `deleteOffice(id)` still require confirmation. Cancel, close, Escape, backdrop dismissal and programmatic hide clear pending state without dispatch. Cancel receives initial focus, Tab/Shift+Tab wrap within the dialog, and dismissal restores the trigger or Add button. The original endpoint/CSRF/loader/toasts/response handling/tree refresh remain unchanged.

### Positions

- Removed the numbering header and generated numbering cell. Exactly Position Name, Created At and Actions remain.
- Updated both loading/empty colspans from four to three.
- Added stable `.position-name`, `.position-created-at` and `.position-actions` cell classes.
- Replaced Delete's fragile `td:nth-child(2)` lookup with `td.position-name`; IDs, actions, endpoints and date values unchanged.
- Reconciled inherited positional CSS with the three-column schema: Name is emphasized, Created At is muted, and Actions no longer inherit creation-metadata styling.
- Centered Add/Edit dialogs and restored the dental-role placeholder/helper copy. Existing fields and validation are unchanged. Delete retains its selected-name emphasis and irreversible warning.

### Employment Types

- Swapped only the first two listing headers/cells to Employment Type Name, Code, Description, Status, Actions.
- Kept `.appointment-name` for the existing Delete lookup; added `.appointment-code` for scoped secondary styling.
- Add/Edit controls remain Code, Employment Type Name, Description, Active, with the same options, required states and handlers. Restored the reference's static Active-switch explanation; no switch behavior changed.
- Existing server/default-type restrictions and translated response messages were not changed or claimed freshly verified.

Screenshot #6 is visibly **Edit Employment Type**, although the written reference label calls it Delete. It was used as the Add/Edit appearance reference. The actual existing Delete dialog and handler were preserved.

### DTR

- Wrapped the existing page introduction and filter card in one visual card, with a divider between heading and controls. Removed the competing screen-only filter heading/instructions; retained a single H1, eyebrow, subtitle and heading ID.
- Kept the original filter form byte-for-byte, including GET/action, names, selected values, employee loop, `$canViewAll`, month format and View behavior.
- Added only the `else` surface for the existing `$employeeId > 0` condition: calendar icon, "Choose an employee to view records", and "Monthly attendance and time summaries will appear here."
- The populated metrics/table source remains byte-for-byte unchanged. Selected employees with no attendance still see the existing no-attendance table state rather than the choose-employee card.
- Screen CSS removes the old inner filter card's border/shadow so there is one visible first card. A narrowly scoped print `display: contents` rule removes only the new grouping box, retaining the original introduction/filter layout. The new empty-state card is print-hidden. No existing print rules or financial values changed.

## Exact task file scope

1. `app/views/module_hris/branches.php`
2. `app/views/module_hris/OrgUnits.php`
3. `app/views/module_hris/Position.php`
4. `app/views/module_hris/appointments.php`
5. `app/views/module_hris/dtr.php`
6. `public/assets/css/style.css`
7. This handoff, `docs/work-items/md-hris-org-dtr-owner-corrections.md`.

The stylesheet retains the entire 69,319-byte published Employee snapshot as an exact prefix. Sixteen appended qualified rule groups target only opt-in Organization dialogs, the changed Organization table schemas, or `.dtr-owner-page`. The sole new print rule targets the new DTR grouping wrapper.

## Completion authorization and resolved requirements

The two former blockers were independently authorized by `MD-HRIS-ORG-DELETE-DIALOG-COMPLETION-001` and are now complete. New changes in this increment are limited to `branches.php`, `OrgUnits.php` and this existing handoff. No stylesheet change was necessary: the already accepted opt-in Organization modal rules are reused. Positions, Employment Types, DTR and Employee corrections remain byte-for-byte intact relative to the completion preflight. No backend, authorization, hierarchy, parent-child, permission, payroll or business-rule change was made.

## Verification actually executed

Temporary evidence: `/tmp/md-hris-org-dtr-owner-corrections/`. Fixtures strip only the three application includes, supply fictional data/CSRF and presentation helpers, and intercept every API request in Chromium. They start no application or database and are not authenticated acceptance.

| Check | Result | Evidence / limit |
| --- | --- | --- |
| PHP syntax | PASS | All five changed views, PHP 8.4.24 CLI with `-n -l`. |
| JavaScript syntax | PASS | `node --check` on all four inline scripts; DTR has no inline script. |
| Complete CSS parsing | PASS | css-tree 3.1.0, 72,450 bytes, no recovered raw nodes; parent prefix intact and all sixteen new rule groups scoped. |
| Forms/CSRF/selectors | PASS | PHP-generated control manifests match before/after, including form ownership, IDs, names, values/options, checked/required/read-only/disabled/min/max/step attributes. DTR also compared across four states. |
| Executable change boundary | PASS | Branch/Office scripts now contain the explicitly authorized Delete interaction changes; original form bodies and deletion AJAX contracts/callbacks match completion-preflight source. All unrelated functions remain unchanged apart from stable Branch identity cell mapping. Position and Employment Type files remain byte-identical to the previously checked correction batch. |
| DTR source preservation | PASS | Entire GET form, populated summary block and attendance table match baseline exactly. |
| Branch Add and Delete presentation | PASS, simulated | Vertical fields, reference placeholders, reachable footer, correct selected name/code and retained warning. |
| Office Add/Edit | PASS, simulated | Desktop grouping, loaded field values, mobile stacking and footer access. |
| Office Bootstrap Delete Unit | PASS, simulated | Correct name, title/warning/buttons; Cancel/close/Escape/backdrop/programmatic hide do not dispatch. No native confirmation remains. |
| Position table/Delete | PASS, simulated | Three columns, unchanged Created At, correct name after renumbering removal, correct metadata emphasis, three-column empty state; Add/Edit/Delete controls exercised without writes. |
| Employment Type mapping | PASS, simulated | Name/Code order, correct Delete identity, unchanged form order/Active switch and loaded values. |
| DTR states | PASS, simulated | Unified first visual card; no-selection empty state; populated metrics/table; selected-empty attendance; restricted selector condition. |
| Responsive checks | PASS, simulated | All five pages at 320/375/576/768/992/1200/1440px; document overflow <=1px, modal bounds and footer reachability checked. |
| DTR print preservation | PASS, simulated print media | Baseline/current populated table text and heading/form/summary/table rectangles match within 1px. No-selection card hidden in print. Not real PDF/printer acceptance. |
| Protected style probes | PASS, simulated | Before/after shared-shell, Employee and Payroll style probes identical. Not authenticated Payroll acceptance. |
| Retained Organization/DTR browser regression | PASS | Chromium 154.0.8037.57, ten checkpoints rerun against the completed views, zero browser errors; API request methods GET only. |
| Delete interaction checks | PASS, simulated | Fourteen additional checkpoints: multiple fictional records, text-safe special characters, optional Branch code, stale/invalid/duplicate identities, selection switching, exact target IDs and CSRF, no double dispatch during/after a request, server rejection/HTTP error, pagination/tree refresh, Office entry cannot bypass confirmation, keyboard focus/trap/return, long labels and reachable footer at 320/375/1440px. Seven POSTs were intercepted and fulfilled entirely within Playwright; none reached an application or database. |
| Final preservation/index/whitespace | PASS | All protected preflight hashes match, including the entire stylesheet and excluded Employee model. All 173 other tracked integration files and all 178 original-checkout tracked files match their respective HEADs. Expected seven tracked modifications plus this untracked handoff; index empty; both diff checks pass. |
| Real authenticated CRUD, authorization, persistence, real print/PDF, physical devices, screen reader and actual 200% zoom | NOT RUN | No application/database service started and no records created/modified/deleted. |

Completion evidence: `/tmp/md-hris-org-delete-dialog-completion/`. `preflight.json` records protected-file hashes before this increment; `source-results.json`, `browser-results.json` and `preservation-results.json` record the final checks. The refreshed ten-check regression and its results are under its `regression/` subdirectory. PHP fixture rendering includes no application bootstrapping, model or database, and every network request is intercepted. The uncommitted Employee model remains present on disk, but these fixtures never load it.

Completion commands:

```text
python /tmp/md-hris-org-delete-dialog-completion/prepare-checks.py
node /tmp/md-hris-org-delete-dialog-completion/browser-check.cjs
node /tmp/md-hris-org-delete-dialog-completion/regression/browser-check.cjs
```

Earlier batch evidence (the PHP/JS/CSS/form/DTR preservation checks remain retained):

```text
node /tmp/md-hris-org-dtr-owner-corrections/browser-check.cjs
/tmp/md-hris-org-dtr-owner-corrections/browser-results.json
/tmp/md-hris-org-dtr-owner-corrections/source-results.json
/tmp/md-hris-org-dtr-owner-corrections/css-results.json
```

PHP CLI: `/tmp/md-hris-stage2t-a/php/usr/bin/php8.4`, with command-local `LD_LIBRARY_PATH=/tmp/md-hris-stage2t-a/php/usr/lib/x86_64-linux-gnu`.

## Preservation and owner review

- Employee view, published restoration handoff and all prior stylesheet bytes remain intact.
- Excluded `app/models/hris/EmployeeModel.php` and its existing two-line Position addition remain unchanged, uncommitted and unstaged. Working-file SHA-256: `d85e140acc3271e7555957351a1ea45f12359715277080df3da1c8b23b9e3d5b`.
- Original checkout remains on `work` at `b5526bb4a7999bbd3c253fcdda1c8419a171314d`; no unrelated source or worktree changes.
- No staging, commits, pushes, fetches, branches, PR changes, merges, resets, deployments, dependency installs, database changes or Stage 3 work.
- Owner review is next: visually accept both completed Delete dialogs in the isolated authenticated local environment. Real deletion/persistence, backend authorization, actual assistive-technology/physical-device/zoom and print/PDF acceptance remain NOT RUN. No additional Delete-dialog authority is needed for the completed implementation; any publication requires separate explicit authorization.
