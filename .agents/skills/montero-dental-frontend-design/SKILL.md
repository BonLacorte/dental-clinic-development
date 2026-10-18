---
name: montero-dental-frontend-design
description: A skill for approved frontend presentation milestones in Montero Dental, preserving backend behavior, request contracts, authorization, database operations and payroll functionality.
---

# Montero Dental frontend design

Use for explicitly approved frontend presentation or frontend-design documentation milestones. Read repository `AGENTS.md`, `PROJECT_OVERVIEW.md`, `docs/architecture.md`, `docs/codex-session-workflow.md`, the task specification and handoff. Inspect current source, branch, HEAD, working tree and accepted functional baseline; current code wins over historical handoff claims. Preserve unrelated work; stop on conflicting edits rather than stashing, resetting or overwriting them.

Follow [milestones](references/milestones.md) for **PLAN → SPEC → BUILD → VERIFY → REVIEW** and [verification](references/verification.md) for preservation checks and evidence. Read the [approved design system](../../../docs/work-items/frontend-modernization/PEARL-TEAL-DESIGN-SYSTEM.md) and [implementation plan](../../../docs/work-items/frontend-modernization/FRONTEND-ONLY-IMPLEMENTATION-PLAN.md). These relative links resolve from this Skill directory.

## Authorization and hard boundaries

- Documentation-only stages may edit only explicitly approved instruction, specification and handoff Markdown. They do not authorize CSS or application implementation.
- During a separately authorized frontend BUILD, edit approved CSS and **nonfunctional PHP-view presentation only**, within an explicit file allowlist. Preserve Bootstrap 5 and jQuery/AJAX. Never introduce a JavaScript framework, new build system or application dependency, or upgrade existing dependencies.
- Never modify controllers, models, services, helpers, middleware, routes, database files, schema/seeds, core architecture or business logic under frontend authority.
- Executable PHP and JavaScript inside views are functional code. File placement in `app/views/` is not authorization to edit conditionals, loops, data preparation, session/auth checks, escaping, event handlers, request logic or generated-row behavior. Static classes/layout may change only without changing those decisions or behavior. If implementation needs functional edits, record the dependency for separate authorization.
- Preserve form fields, names, IDs, values, methods, action URLs, serialization and request payloads; API response handling; navigation destinations; permission-conditioned action visibility; required/readonly/disabled and other validation attributes; search, filtering, sorting and pagination; Bootstrap modal lifecycle, confirmation, dirty/pending state and focus behavior; financial values, persistence and print/payroll output.
- Never invent dashboard statistics, business statuses, employee data or operational features. Existing placeholders and status meanings/text remain unchanged.
- Never import or cherry-pick application changes from Draft PR #8. Its broader Development Skill is not authority for frontend work. Never interpret a discovered backend, security, API or workflow bug as permission to repair it.
- Never access production, live SSH workspaces or private clinic data. Use only authorized isolated fictional resources for verification; do not copy credentials, tokens, records, uploads or dumps into evidence.

## Autonomy and review

Within an explicitly authorized future frontend BUILD, continue through the approved subtasks, ordinary styling adjustments, verification and corrections to presentation regressions without seeking approval for each adjustment. This never expands to backend, security, database, API or workflow changes. Record out-of-scope findings for owner review and continue independent approved work.

Require human authorization for **commits, pushes, pull requests, merges and deployment**. Never automatically publish or treat a handoff as publication authority. Local branch creation and staging also require authorization under repository instructions; an explicitly authorized local branch does not authorize commits or publication. Leave the index untouched when staging is not authorized.

Report **PASS**, **FAIL**, **BLOCKED** and **NOT RUN** accurately. At REVIEW provide one consolidated handoff with baseline, complete file list/diff, actual checks, unresolved findings and next action. Stop at the authorized review boundary; a later BUILD requires its own authorization.
