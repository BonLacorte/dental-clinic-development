# Task-specific handoffs for Codex

These files preserve **task state**, not conversation history. Each multi-session feature or bug gets its own file, based on `TEMPLATE.md`, committed on the **same feature branch** as its code. Example: `docs/work-items/employee-filter.md` on `feat/employee-filter`.

## Rules

1. Use a short, unique slug and a clear objective; do not rename an existing work item casually.
2. Only describe the **current verified** state. Record tests as passed, failed, blocked, or not run; never fabricate outcomes.
3. Record branch, last relevant commit, acceptance criteria, affected files, decisions, blockers, and **next specific action**.
4. Keep patient/employee identities, credentials, `.env` values, data exports, and remote-server details out of these files and Codex prompts.
5. Avoid a global `current-state.md` that multiple tasks continually rewrite: separate files reduce context drift and merge conflicts.
6. Do not create one document per chat turn. Update the same task handoff at meaningful milestones and before switching devices.
7. The code and tests outrank the handoff. If they conflict, inspect and correct the document.
8. Once merged, the file can remain as a short development record. `DONE` in Git does **not** indicate deployment to the real clinic.

See `../codex-session-workflow.md` for start, pause, resume, and review commands and prompts.
