# Codex session continuity — Montero Dental

**Purpose:** Continue the same development task from a work laptop, home PC, or an approved cloud workspace without depending on a previous Codex conversation. This is a development-copy workflow, **not** authorization to edit the live Remote SSH server.

## Frontend-design clarification

For explicitly frontend-design tasks, follow the stricter frontend-only section in [AGENTS.md](../AGENTS.md) and the [Montero Dental frontend design Skill](../.agents/skills/montero-dental-frontend-design/SKILL.md): **PLAN → SPEC → BUILD → VERIFY → REVIEW**. An approved documentation stage does not authorize application edits. An approved frontend BUILD permits autonomous presentation work only within its explicit file allowlist; executable PHP/JavaScript, backend, security, database, API and workflow changes remain outside that authority. Historical examples below are not permission to publish: obtain explicit human authorization for commits, pushes, pull requests, merges and deployment. Never automatically publish; verify current authorization before any Git write. Stage 1 ends at documentation review, with no application implementation or Git publication.

## The four pieces of state

| State | Portable through Git? | How it is preserved |
| --- | --- | --- |
| Application code and its history | Yes | A committed, pushed feature branch |
| Project conventions | Yes | `AGENTS.md`, `docs/architecture.md`, other tracked docs |
| What the agent was working on | Yes, if recorded | `docs/work-items/<task-slug>.md` on the same feature branch |
| Local runtime, DB, secrets, tools, uncommitted edits | **No** | Configure independently on each device; never commit sensitive data |

A handoff is a **summary**, not a guarantee of continuity. A new session must verify it against the current checked-out source, branch, and Git history. Models may still differ in how they reason. The goal is repeatable inputs and verifiable outcomes, not identical chat memory.

## A. Start a new multi-session task (first device)

1. Before switching branches, check `git status --short`. Resolve existing work deliberately; do not discard or blindly stash other work.
2. Update the baseline and create a **descriptive feature branch**; example below. If `main` may contain unpublished production differences, see the production synchronization caution at the end.
3. Copy `docs/work-items/TEMPLATE.md` to a distinct task file (example: `docs/work-items/employee-filter.md`); fill in goal, acceptance criteria, baseline, and known risks.
4. Open Codex in **This computer** mode, scoped to the local checkout. Give it the START prompt below. Review its plan before allowing substantive edits.

```powershell
# Only when your working tree is clean and your main branch has no local-only changes:
git switch main
git pull --ff-only origin main
git switch -c feat/employee-filter
Copy-Item docs/work-items/TEMPLATE.md docs/work-items/employee-filter.md
```

Do not run the branch-creation command if the branch already exists; switch to the existing branch instead. Feature work should be independently testable. Do not refactor unrelated modules just to make a task look cleaner.

### START prompt — copy into Codex

> We are starting/continuing the Montero Dental development task on the CURRENT CHECKED-OUT BRANCH. Read `AGENTS.md`, `docs/architecture.md`, `PROJECT_OVERVIEW.md`, and `docs/work-items/<task-slug>.md`. Inspect `git status`, the active branch, recent commits, and the affected existing implementation. Confirm the task objective, the current verified state, and the next safe step. Identify missing information or stale handoff claims. Follow existing MVCLite/PHP/Bootstrap/jQuery conventions. Do not edit, run destructive commands, commit, push, connect to production, or deploy yet. Propose a concise plan for my approval.

Replace `<task-slug>` with the real task file. If no prior handoff exists, explain the requirement and create one from the template **after approval**.

## B. Work on the task

- Make narrow changes following existing routes/controllers/models/views and middleware patterns.
- Use only an isolated demo DB with fictional data; see `LOCAL_SETUP.md`.
- Run syntax checks on changed PHP files with a compatible PHP executable, plus any supported local smoke checks. Record **exact commands and actual outcomes**.
- Keep the handoff updated when a significant decision, blocker, or test result changes; do not fill it with raw chat logs.
- Never present an unimplemented feature, unrun test, or production behavior as verified.

## C. Pause or end a session (before leaving a device)

Give Codex this prompt **before** committing:

> Prepare a precise handoff for this task. Read current `git status` and the relevant code, then update `docs/work-items/<task-slug>.md` with the task status, branch, completed changes, decisions and reasons, test commands and pass/fail/not-run results, blockers, remaining risks, and the **first concrete next action**. Do not store secrets, employee/patient information, raw tool logs, or a chat transcript. Show me the changes for review. Do not commit, push, merge, or deploy.

Then inspect the diff and stage **only intended files**. If the feature is unfinished, a clearly labeled `WIP` commit on its dedicated branch is acceptable; **do not merge unfinished work into `main`**. Push only after reviewing for sensitive data.

```powershell
git status --short
git diff
# Replace these placeholders with the actual reviewed files:
git add docs/work-items/employee-filter.md app/controllers/hris/EmployeeController.php
git diff --cached --check
git diff --cached
git commit -m "wip: employee filter implementation and handoff"
git push -u origin feat/employee-filter
```

The paths and branch above are **examples**, not instructions to edit the employee module. If you changed different files, stage those instead. If Git reports nothing to commit, check that the handoff and relevant changes are saved before leaving. A pushed branch is the portable checkpoint; uncommitted files stay only on that computer.

## D. Resume on another device

If you have **never cloned the repository** on that device, use its authorized GitHub URL and clone first. Do not run an unreviewed project startup script; follow `LOCAL_SETUP.md` and establish an isolated DB.

```powershell
# Existing clone, with no conflicting uncommitted changes:
git fetch origin
# First time checking out the feature branch on this device:
git switch --track origin/feat/employee-filter
# If you ALREADY have a local copy of that branch instead:
# git switch feat/employee-filter
# git pull --ff-only

git status --short
git log -3 --oneline
```

Read `docs/work-items/employee-filter.md` and paste the START prompt with the matching task file. Codex should compare the recorded handoff to the **actual code** before continuing. If the task branch has not been pushed from the first computer, the second computer cannot retrieve those edits.

**Never work on the same feature simultaneously from two machines without first synchronizing.** If Git reports divergent history, conflicts, or non-fast-forward errors, stop and reconcile deliberately rather than force-pushing.

## E. Finish a task

1. Confirm acceptance criteria are met or clearly identify what is incomplete.
2. Run relevant checks against the isolated local development environment; record results.
3. Change the task handoff to `READY_FOR_REVIEW` and provide reviewed file list, risk notes, and reproducible checks.
4. Commit and push the feature branch; open a pull request into `main` only after reviewing the changes. Merge only when the task is approved and ready.
5. Mark the task `DONE` after merging (this may be a later documentation update). A merged development branch is **not** a deployment.

## F. Production is an independent source of changes

Your collaborator edits the live server via Remote SSH without Git. **GitHub `main` is not automatically the current production version.** Before proposing a production transfer, obtain an authorized fresh, read-only production snapshot, compare it to the baseline and your branch, reconcile the intervening changes, review backups/rollback, and retest against the appropriate test environment. Never `git reset --hard` or overwrite the live directory to make it match your personal repository. Deployment approval and a full sync procedure belong to a separate release workflow (Phase 5).

## Quick troubleshooting

| Symptom | Check |
| --- | --- |
| New Codex session forgets prior decisions | Is the task file committed on the current branch? Did the session read it? |
| My changes aren't on home PC | Did you commit AND push the correct feature branch? Did home PC fetch and switch to it? |
| Handoff says work is done, but source differs | Trust inspected code and tests, then correct the handoff |
| Local app fails after clone | Git omits Composer `vendor/`, PHP `.tools/`, local DB and private config. Follow `LOCAL_SETUP.md` |
| I see a different implementation style | Ask Codex to reread `AGENTS.md` and inspect the nearest existing module before changes |
| Production changed meanwhile | Stop deployment and perform three-way reconciliation; do not overwrite blindly |
