---
name: montero-dental-data-table-ux
description: Design, audit and verify accessible, task-focused data-table presentation for Montero Dental's existing PHP/Bootstrap/jQuery HRIS, payroll and administration screens, under the approved Pearl & Teal design system and strict frontend-only preservation rules.
---

# Montero Dental — Data Table UX

## When to use

Use this Skill when planning, specifying, reviewing, styling, or (with separately explicit authority) implementing a Montero Dental table, listing, catalog, attendance view, payroll listing, employee table, filter/search toolbar or table pagination. Use it for both new proposals and assessment of existing tables; do not assume every proposed UX improvement is authorized to build.

**Start by reading:** repository `AGENTS.md`, `PROJECT_OVERVIEW.md`, `docs/architecture.md`, `docs/codex-session-workflow.md`, the current task specification and accepted functional baseline; then read [the existing frontend Skill](../montero-dental-frontend-design/SKILL.md), [Pearl & Teal design system](../../../docs/work-items/frontend-modernization/PEARL-TEAL-DESIGN-SYSTEM.md), and [frontend implementation plan](../../../docs/work-items/frontend-modernization/FRONTEND-ONLY-IMPLEMENTATION-PLAN.md). Inspect actual source, branch, HEAD, working tree, affected view/CSS and table behavior. Actual current code wins over older documents.

Read [normative rules](references/rules.md), [patterns and table mapping](references/patterns.md), [verification matrix](references/verification.md), and [video/source evidence](references/video-evidence.md). **Do not infer that this Skill authorizes application edits.**

## Mission

For each table, optimize these four user tasks (not merely its appearance):

1. **Find** a record using stable identifier, search and familiar ordering.
2. **Scan** records, headings, statuses and key attributes rapidly.
3. **Compare** records or numerical values without losing row/column context.
4. **Act** on the intended authorized record with clear, reversible or appropriately confirmed actions.

The outcome must be easy to discover, accessible to keyboard and assistive-technology users, responsive without losing information, and consistent with the approved Pearl & Teal design system, including the requested Premium Edition direction only as defined by the checked-in approved tokens and plan. Do not invent additional Premium Edition tokens or functionality. The find / scan / compare / act framing is an adopted Montero synthesis, not a verbatim reproduction of NN/g's four tasks.

## Non-negotiable scope precedence

1. **Repository safety / authorization / existing frontend Skill** outrank this Skill.
2. Approved frontend task scope and exact file allowlist outrank recommendations in this Skill.
3. Existing functionality, server contracts, business definitions and permissions outrank visual prototypes.
4. Pearl & Teal tokens govern presentation; no new arbitrary colors, font pipeline, dependencies or framework.
5. This Skill's UX rules govern within the remaining approved presentation surface.

An instruction to "improve table UX" is not permission to change functional search, filters, sorting, selection, pagination, row generation, endpoint parameters, authorization, actions, business data, data masking, statuses, exports, totals, finance, persistence or print output. **Functional enhancement proposals require their own SPEC and authorization** even when this Skill recommends them.

- **Documentation-only authorization:** edit only explicitly allowlisted Markdown. No CSS, PHP views, executable code, packages or runtime data changes.
- **Separately authorized frontend BUILD:** edit only approved CSS and static/nonfunctional PHP-view presentation in an exact allowlist. Executable PHP and JavaScript in views remain protected. Preserve existing PHP/MVCLite, Bootstrap 5, Bootstrap Icons, jQuery/AJAX, DataTables where present, and no Node build step. Reuse installed icons; add no icon library or asset dependency.
- Never touch controllers, models, middleware, routing, database/schema/seed, authorization, business rules, payroll calculations, print contracts, import logic or production resources under this Skill.
- Never introduce fictitious records, labels, permissions, clinical/patient functions, "appointments" scheduling or imaginary operational features. In this repository, the HRIS Appointments page means **employment types**.
- Never access live SSH/production or private clinic data. Use isolated fictional demo data only where authorized.
- Never stage, create/switch branches, commit, push, open PR, merge or deploy without the authorization required by repository instructions. Stop and report conflicts rather than stash/reset/overwrite unrelated work.

## Required execution workflow: PLAN → SPEC → BUILD → VERIFY → REVIEW

### PLAN — investigate before redesign

- Identify the table's real user, record identifier, primary task, existing visible columns, source and row-action permissions.
- Audit the actual table: title/headers, hierarchy, numeric/date formatting, search, existing filters, existing sort, pagination, empty/loading/error states, actions, responsive overflow, keyboard behavior, print, and existing JS/PHP ownership.
- Classify its usage with the decision tree in `references/patterns.md`; do not use cards/column removal merely to reduce table width.
- Capture a read-only baseline of rendered behavior and accepted source where runtime access is authorized. Distinguish source observations from runtime evidence.

### SPEC — turn recommendations into a controlled table contract

Record a task-specific **Table UX Contract** using this structure:

```text
Screen / route / affected table:
Approved baseline SHA and branch:
User tasks (find / scan / compare / act):
Current record identifier and complete ordered columns:
Existing permitted row actions and visibility conditions:
Existing search / filters / sort / pagination contract:
Current desktop / tablet / mobile presentation:
Presentation-only changes authorized (exact file allowlist):
Proposed functional enhancements (NOT authorized by this scope):
Rules IDs and acceptance cases:
View/CSS protected executable regions and print scope:
Risk / test fixtures / browser and keyboard coverage:
```

For each proposed rule, label it **PRESENTATION**, **FUNCTIONAL PROPOSAL**, or **NOT APPLICABLE**, with justification. Map acceptance checks to IDs in `references/rules.md`; do not treat illustrative pixel recommendations as business requirements.

### BUILD — only if separately authorized

- Use scoped Pearl & Teal CSS and minimally changed static view markup; prefer the existing pattern over a new widget library.
- Maintain the same column set/order, values/labels, selectors, event delegation, form ownership, buttons/handlers, query/search/sort/pagination semantics, focus and print unless a separate functional change has been approved.
- Prioritize in this order: header distinction, record identity, readable spacing/alignment, separators, existing status/action visual treatment, search/filter presentation, local overflow, existing pagination, and focus/contrast states.
- **Do not implement suggestions like new header-filter menus, new row selection, new mobile card views, sort additions, column toggles, bulk actions or new search behavior from a styling-only instruction.** Document proposals instead.

### VERIFY — evidence, not impressions

Follow `references/verification.md` and the existing frontend Skill verification. Check both appearance and preservation. Contrast, focus, scrollability, responsive behavior and target size must be measured or explicitly marked unverified. Run supported checks on fictional isolated data only with permission. Do not mutate payroll/import/employee records merely to validate styling. Automated DOM checks cannot independently prove WCAG conformance.

### REVIEW — produce an accountable handoff

Return one consolidated review in the final chat response; do not create an additional `REVIEW.md` unless that path is separately allowlisted. Include: baseline branch/SHA; exact file list/diff; Table UX Contract; rule-by-rule **PASS / FAIL / BLOCKED / NOT RUN / N/A** matrix; preserved functionality; unresolved video claims and functional proposals; visual/browser evidence limits; next authorized action. Stop at the explicit review gate and obtain fresh authorization for application BUILD, Git publication or deployment.

## Compact acceptance gate

A changed table cannot be called presentation-ready if any **applicable** mandatory check fails:

- [ ] MTD-01–07: semantics, visible labels and record hierarchy
- [ ] MTD-08–13: numeric/date alignment, statuses, density, safe actions
- [ ] MTD-14–20: existing search/filter/sort/pagination behavior preserved
- [ ] MTD-21–25: mobile local scroll, discoverability and complete columns
- [ ] MTD-26–30: keyboard/contrast/focus, accessibility and test integrity
- [ ] No protected executable logic, permissions, print, payroll, request, dependency, backend or database drift
- [ ] Any advanced UX proposals requiring JS/PHP logic explicitly deferred or separately approved

**Source integrity:** Three of the five provided videos have corroborated publisher pages/descriptions; a third-party, potentially machine-generated transcript is available for video 2, with accuracy unverified against footage. No direct footage verification was performed; videos 1 and 5 have NOT been reliably inspected. `references/video-evidence.md` records this limitation. Do not claim that all five videos were fully watched or every lesson directly sourced. Treat unverified inputs as pending until transcripts/timecodes are provided and reviewed.
