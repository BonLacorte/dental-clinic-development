# Payroll Items surfaces and HRIS attendance dialog format

- Branch / HEAD: `review/md-hris-manual-refinements` / `5292937` (preserve).
- Owner request: white Payroll Items tab strip, no thick active subpage underline, green table headers matching Leave Management; Leaves, Holidays and Shifts Add/Edit/Delete dialogs adopt Payroll Items format.
- Application allowlist: `public/assets/css/style.css`, `app/views/module_hris/leaves.php`, `app/views/module_hris/holidays.php`, `app/views/module_hris/shifts.php`.
- Presentation scope: scoped screen-only colors; reuse Payroll dialog classes; grouped existing controls, label spacing, Cancel footers, selected-record Delete context, focus entry/trap/return.
- Protected: original fields/IDs/options/validation, forms/CSRF, all existing AJAX URLs/payloads/callbacks, business data/calculations, print, permissions, backend, other HRIS, all Payroll Items views and previous uncommitted navigation/patch work. No submission, Git publication, database writes or dashboard work.
- Baseline evidence: `/tmp/montero-form-format-20261027-_sw7clg4`.
- Status: READY_FOR_REVIEW; requested presentation changes verified locally, unstaged and uncommitted.

## Implemented

- Payroll Items tab-strip surface and active tab use white; the active subpage border is thin (1px CSS), with no thick bottom accent. Shared module-navbar active styling is untouched.
- All record/type table headers use the same existing green tint as Leave Management: `--montero-primary-tint`, computed `rgb(230, 243, 239)`.
- Leaves (Requests and Types), Holidays and Shifts reuse Payroll Items dialog presentation, with page-appropriate grouped fields, labels, helper text and Cancel footers.
- Delete dialogs show actual selected record identity using text-safe DOM updates. Dialog focus enters the first form control or Delete Cancel, stays contained, and returns to the trigger on dismissal.
- All original form fields, validators, IDs, defaults/options, CSRF and AJAX methods/URLs/payloads/callbacks remain unchanged. Existing mutation lifecycle handlers are retained; a new persistence/dirty/pending subsystem was not introduced.

## Verification

| Check | Result | Evidence / limits |
| --- | --- | --- |
| PHP syntax | PASS | All three edited views, compatible Ubuntu PHP. |
| JavaScript syntax | PASS | All three inline scripts, existing Node. |
| Form preservation | PASS | 56 existing control definitions including names, IDs, values, options, validation attributes and form ownership match pre-task source. |
| Existing behavior source | PASS | All 41 existing named functions remain unchanged, including AJAX, loaded-value mapping, validation, date rules and Shift-mode rules. Only scoped presentation initialization/selected-row context was added to ready handlers. |
| Prior work preservation | PASS | All 180 protected fingerprints and complete pre-task CSS prefix match. Finished HRIS outside the requested three pages, all six Payroll Items views, prior navigation, backend, routes, permissions, dependencies and existing handoffs untouched. |
| Requested colors/tabs | PASS | 20 actual desktop/mobile record/Type cases across six Payroll Items pages: white strip/active surface, border <=1px after browser pixel rounding, every header exact Leave Management green, no root horizontal overflow. |
| All HRIS dialogs | PASS | Twelve Add/Edit/Delete dialogs across Leave Requests, Leave Types, Holidays and Shifts. Grouped fields/labels, loaded values, selected Delete identity, initial Cancel focus and dismissal focus return checked. No save/delete submitted. |
| Short mobile | PASS | All twelve dialogs at 320x667: content fits horizontally, footer remains visible, no root overflow. |
| Shift modes | PASS | FIXED, FLEX_WINDOW, FLEX_HOURS retain original required/disabled rules in the actual Add form. Form cancelled. |
| Leave dates | PASS | Changing Date From updates Date To minimum as before. Form cancelled/reloaded. |
| Keyboard containment | PASS | Tab from final form action wraps to the close control; focus entry/return checked in all four dialog families. |
| Browser diagnostics | PASS | No observed console warnings/errors. |
| Whitespace / Git | PASS | `git diff --check`; same branch/HEAD, index unchanged, no commit/push/merge. |
| CRUD persistence / financial writes | NOT RUN | No create/update/delete/import/processing requests or database modifications. |
| Full AT / physical devices / print-PDF | NOT RUN | No complete accessibility or print-conformance claim. Existing business print views and screen-independent CSS are unchanged. |

## Evidence and next step

Sanitized results and screenshots are outside Git in `C:/Users/loueg/.codex/visualizations/2026/10/27/01a17bde-13e2-7bc3-99b4-c6b8d2508889/`:

- `payroll-items-surface-update.png`
- `leave-dialog-format-update.png`
- `payroll-hris-form-format-results.json`

The temporary viewport override was reset; the browser is back on Allowances. All pre-existing edits remain reviewable. Next action is owner visual review of these requested changes; no Git publication, backend/access changes or deferred dashboard work is authorized by this handoff.
