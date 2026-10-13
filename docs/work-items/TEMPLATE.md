# Task: <short feature or bug title>

> Copy this file to `docs/work-items/<task-slug>.md` on your task branch. Replace placeholders. **Never put credentials or real patient/employee records here.**

## Identity and status

- **Task ID / issue:** <GitHub issue or local short slug>
- **Branch:** `<feat/name-or-fix/name>`
- **Status:** `PLANNING` | `IN_PROGRESS` | `BLOCKED` | `READY_FOR_REVIEW` | `DONE`
- **Last reviewed (date, Manila time):** <YYYY-MM-DD>
- **Last relevant commit:** <short SHA or `none yet`>
- **Source baseline:** <Git commit and, if applicable, a separate authorized production snapshot reference; never store sensitive details>

## Goal and acceptance criteria

**Goal:** <describe the requested change in user-visible terms>

- [ ] <Acceptance criterion 1>
- [ ] <Acceptance criterion 2>
- [ ] <Acceptance criterion 3>

**In scope:** <modules and behavior>

**Out of scope:** <explicit exclusions; no production deployment>

## Relevant architecture and impacted files

- **Existing patterns to follow:** <route/controller/model/view examples>
- **Likely files:** <list relative paths>
- **Security/data boundaries:** <tenant, branch, permissions, CSRF, payroll etc.>
- **Dependencies:** <related tasks, schema assumptions; cite what is actually verified>

## Progress so far

- **Completed:** <verified implementation or investigation>
- **Decisions and reasons:** <what was chosen and why>
- **Incomplete or reverted:** <what still needs attention>

## Verification evidence

| Check or command | Result (`PASS`, `FAIL`, `BLOCKED`, `NOT RUN`) | Notes |
| --- | --- | --- |
| <e.g., PHP syntax check> | NOT RUN | <actual output or reason> |
| <e.g., isolated demo smoke test> | NOT RUN | <actual output or reason> |

Do not claim tests passed if they were not run. Record which environment was used (without secrets).

## Blockers, risks and open questions

- **Blockers:** <or `none`>
- **Known risks:** <or `none identified`>
- **Needs user approval:** <or `none`>

## Next session — first concrete action

1. <single specific action with file/command/context>
2. <the follow-up action>

**Important:** Read `AGENTS.md`, `docs/architecture.md`, and `docs/codex-session-workflow.md`; verify branch, status, and code before taking action. No production access or deployment is authorized by this handoff.
