# Data Table UI/UX Verification & Preservation Matrix

Apply alongside the [parent frontend Skill verification](../../montero-dental-frontend-design/references/verification.md). Differentiate a **documentation inspection** from actual **UI runtime testing**; mark unsupported checks accurately. Respect the exact current baseline and authorized isolated fictional data. This file is an acceptance protocol, not evidence that tests already passed.

## Before tests

Record baseline SHA/branch, active task allowlist, current `git status --short`, current source ownership, any local diffs, current permitted user/role scenarios, applicable MTD rules and blocked checks. Use real source behavior to fill gaps; older documentation is advisory.

## Read-only static / diff gates

| ID | Check | Evidence |
|---|---|---|
| V-01 | Confirm exact allowlist and no executable JS/PHP or backend/model/middleware/route/database/config/dependency changes; protect view code and print styles | `git diff --name-status`; full line diff; protected-file hashes where feasible |
| V-02 | Check existing full column set and order, labels, sorting, status vocabulary, field semantics, actions/data attributes | Side-by-side baseline source comparison |
| V-03 | Validate Pearl & Teal token consistency and CSS scoping; no global rules causing accidental payroll/login/print drift | Scoped CSS inspection and computed style snapshots when browser available |
| V-04 | Check Markdown relative links and skill YAML; run `git diff --check` when in repository | Command output, path check |

## Browser/test cases for future authorized BUILD

| ID | Scenario | Expected outcome |
|---|---|---|
| V-05 | 1440×1000 desktop list | Clear title/headings/identity; existing search, filters, sort and page actions visible; no clipping |
| V-06 | 768×1024 tablet | Search/toolbar wrap; wide table scrolls locally; business data, action buttons and pagination reachable |
| V-07 | 375×812 mobile | Entire column set reachable by local scroll; no inadvertent root horizontal scrollbar; controls are not clipped |
| V-08 | 320px and 375×667 short viewport | Search, toolbar and pagination fit/reflow; no occluded actions, inaccessible modal footer or labels |
| V-09 | 575/576/767/768/991/992/1199/1200px boundaries | No breakpoint-specific loss; sidebar/offcanvas behavior and 992px transition unchanged |
| V-10 | 200% zoom and large fictional employee name | Readable text, uncut labels, table-only horizontal scroll and no page overflow masking |
| V-11 | Tab / Shift+Tab / Enter / Space only | Sort, search, Clear, pagination and actions accessible where existing; meaningful, unobscured focus; no focus trap |
| V-12 | Screen reader spot-check where available | Correct table header relationships, existing sort state truth, programmatic icon names, existing status meaning; document AT/browser pair |
| V-13 | Numeric and payroll samples | Values/precision/sign/currency/rounding and column order match accepted baseline; CSS numeric alignment readable |
| V-14 | Date samples including `03/04` ambiguity | Existing business date/time interpretation identical; any new format receives separate approval |
| V-15 | Default and ascending/descending existing sorts, combined with pagination | Exact existing results/order/page-size/query keys; no fake sort indicators |
| V-16 | Existing search, Clear, filter and zero matches | Same requests and results; active filter discoverable; no lost query or display-state confusion |
| V-17 | No records, filtered zero records, loading, failure/retry as implemented | Clearly distinct existing meanings and same handlers; simulations labeled SIMULATED |
| V-18 | Per-row action under representative allowed/denied permissions | Existing same-visible-actions, targets, confirmations, modal lifecycle and focus return; no unauthorized new options |
| V-19 | Touch/pointer action targets | 44px design objective checked; sufficient separation and tooltips/labels are not the only explanation |
| V-20 | Contrast and status in grayscale | Text ≥4.5:1; large text ≥3:1; necessary UI/focus contrast ≥3:1; status text not color-only |
| V-21 | Keyboard scrolling on overflow table | Table horizontal content reachable; focus order logical; all columns/actions remain present |
| V-22 | Print preview / payroll output (read-only) | Identical print data, monetary fields, page breaks and business content |
| V-23 | Browser console/network / runtime requests | No new errors, unexpected requests, changed AJAX/DOM selectors, or broken event delegation |
| V-24 | Very long fictional values, many columns and representative dataset size | Readable wrapping, stable scrolling, sensible row density; no all-rows preload or new request behavior |

If employee/payment/attendance/import mutations are needed to verify a behavior, **do not perform them as part of frontend authority**. Mark corresponding functional assertion BLOCKED and seek separate isolated-test authorization.

## Minimal report template

```markdown
### Table UX verification — <screen>
- Baseline: <branch / SHA / working tree>
- Authorization: <documentation-only or approved BUILD + exact allowlist>
- Tested environment: <browser, size, fictional demo ID, no sensitive records>
- Applicable rules: MTD-<...>

| Test | Result | Evidence/method | Limitations |
|---|---|---|---|
| V-01 | PASS / FAIL / BLOCKED / NOT RUN | ... | ... |
| V-05 | ... | ... | ... |

- Protected behavior compared: <columns, permissions, search, filters, sort, pagination, requests, print>
- Proposals requiring separate approval: <...>
- Video verification pending: w0P8ygbuiZU, Ksx9C2-3yMo, unless now independently examined
- Next authorized step: <...>
```

Result meanings: **PASS** = executed and passed within scope. **FAIL** = executed and discrepancy found. **BLOCKED** = required but prerequisite/approval inaccessible. **NOT RUN** = intentionally/not yet executed. **N/A** = justified inapplicability. A documentation-only stage cannot mark runtime tests PASS merely because a checklist was authored.
