# Frontend verification and preservation

Read [SKILL.md](../SKILL.md), [milestones](milestones.md) and the [implementation plan](../../../../docs/work-items/frontend-modernization/FRONTEND-ONLY-IMPLEMENTATION-PLAN.md). Verification authority has the same frontend-only boundary as BUILD; finding a bug does not authorize a backend repair.

## Every stage

- Verify branch, HEAD, accepted baseline and pre-existing changes. Inspect full tracked/untracked diffs, not just a diff stat. Compare with the explicit allowlist; use content hashes or equivalent to confirm protected source unchanged. Inspect the index separately. Run `git diff --check` and inspect `git status --short`.
- Check Skill YAML frontmatter (`name`, `description`), local relative references and Markdown paths. Confirm design token values match the approved design system. Check new instructions for conflicting broad development authority; never import Draft PR #8's backend/commit autonomy.
- Confirm no unintended source, CSS, JavaScript, database, route, backend, configuration, dependency or generated artifact changes. Documentation-only stages must change no application files. Confirm Draft PR #8 stays separate and untouched when required; read-only remote metadata checks do not modify the PR.
- Keep sanitized written evidence: command/method, baseline, environment, scope, result and limits. Do not store secrets, private clinic records, raw payloads, sessions, database dumps or production details.

## Future authorized frontend BUILD

- Compare executable PHP and JavaScript with the accepted baseline, including functional code embedded in views. Preserve form controls/attributes, payloads, endpoints/methods, response handling, permission predicates, navigation destinations, search/filter/sort/pagination, modal/confirmation/dirty/pending behavior, financial values, print output and persistence. A changed static wrapper must not alter form ownership or Bootstrap targets.
- Lint changed PHP with compatible 64-bit PHP 8.3+ using the existing approved runtime. Inspect current setup/tool availability first. No application dependencies or build/test system may be introduced; mark unavailable checks BLOCKED rather than inventing setup.
- Use real pointer/touch/keyboard interactions at 1440×1000, 768×1024 and 375×812; check 320px width, 375×667 short height and 575/576/767/768/991/992/1199/1200px boundaries. Preserve the 250px desktop sidebar and 992px offcanvas transition. Measure root overflow (at most 1 CSS px tolerance), local wide-table scrolling and all business-column visibility. Never use page-wide horizontal clipping as a fix.
- Inspect 200% text zoom/reflow, long fictional labels/names, 44px target objective, visible focus, contrast, validation states, reduced motion and keyboard order. Check shell skip link, navigation, sorting/pagination, modal entry/trap/return and existing Stay/Discard/pending guards. WCAG 2.2 AA is the changed-presentation target; DOM or automated checks alone are not compliance proof. Record assistive-technology and physical-device/browser coverage separately.
- Verify existing loading/error/empty states and recovery without changing handlers. Label fault injection and intercepted/mocked responses SIMULATED; they cannot establish server validation, persistence, authorization, SQL scope or compensation safety. Do not submit employee, compensation, payroll or import mutations under styling-only authority. Real write verification requires separate authorization and isolated disposable fictional fixtures.
- Compare representative main/HRIS/payroll/admin/login screens, shared navigation, employee dialogs, attendance/import, payroll details and existing print layouts. Use approved read-only business-data comparisons when runtime verification is authorized; keep raw data outside Git and record only sanitized invariant results. Never access production/private data or reset shared demo storage.

## Results vocabulary

| Result | Required meaning |
|---|---|
| PASS | The stated check was executed and passed within the stated scope. |
| FAIL | The stated check ran and found a discrepancy; report it without expanding repair authority. |
| BLOCKED | A required check cannot run because a stated prerequisite or authorization is unavailable. |
| NOT RUN | The check was not executed; give the reason and any future requirement. |

Documentation/link/hash/whitespace checks are not application runtime tests. Never claim fresh UI, browser, accessibility, database, payroll or regression passes from documentation checks or historical UI-01 evidence. A documentation stage can be ready for review while runtime checks remain NOT RUN because no application implementation was authorized.
