---
name: montero-dental-development
description: Complete approved Montero Dental development milestones such as C-EMP or UI-02, using the existing PHP MVCLite application and evidence-based verification through one final review.
---

# Montero Dental development

Read repository `AGENTS.md`, `PROJECT_OVERVIEW.md`, `docs/architecture.md`, setup
documentation and the task-specific handoff; verify branch, status, remote baseline
and affected source before editing. Actual routes, handlers and SQL win over stale docs.

Use [milestone execution](references/milestones.md) for PLAN → SPEC → BUILD →
VERIFY → REVIEW and [verification](references/verification.md) for test safety and evidence.

Within an explicitly authorized grouped milestone, decompose internally, continue
through ordinary coding/testing/debugging/documentation and routine local commits,
and correct regressions without approval at each subtask. Authorization persists;
do not infer publication authority from a handoff. Keep one consolidated handoff
with separate subtask sections and meaningful local checkpoints. Review the combined
diff and publish one final branch push and draft PR when authorized and supported.
Never merge automatically.

Preserve PHP/MVCLite, PDO, MySQL/MariaDB, Bootstrap 5, jQuery/AJAX and existing caller
contracts. Follow the nearest working implementation. Avoid new tooling/dependencies,
schema changes and unrelated refactors. Protect tenant/branch boundaries, CSRF,
permissions, private data and financial calculations; inspect SQL as well as middleware.

Never access production, real clinic records, live SSH workspaces or deploy. Never
commit credentials, dumps, uploads, sessions or raw private evidence. New authorization
or compensation policy, incompatible contracts/schema, production access or destructive
actions outside disposable fixtures need an explicit decision. Document the blocker
and continue independent safe work. Report PASS, FAIL, BLOCKED and NOT RUN accurately.

For cross-device continuation, commit code, skill and sanitized handoff together;
push only when authorized, then fetch/switch on the next device and verify actual
history/source. Git does not transfer local runtimes, databases, secrets or uncommitted work.
