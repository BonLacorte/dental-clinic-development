# Milestone execution

1. PLAN: inspect clean/dirty status, branch, current origin/main and relevant previous
   handoffs. Preserve unrelated work; use one isolated topic branch. State scope,
   acceptance criteria and identified uncertainties before substantive implementation.
2. SPEC: record the grouped scope and compatible request/response assumptions in one
   `docs/work-items/<milestone>.md`. Preserve historical specifications. Distinguish
   established policies from unresolved decisions; do not invent rules to clear a gate.
3. BUILD: make small coherent changes using existing MVC patterns. Keep separate
   internal subtask checkpoints and meaningful local commits when authorized. Do not
   push each checkpoint. Correct discovered regressions within the approved scope.
4. VERIFY: follow verification.md, inspect failures, repair and retest. Mark blocked
   acceptance separately; complete other achievable work. Do not turn simulated success
   into a real persistence, isolation or authorization claim.
5. REVIEW: inspect combined diff, intended files, secrets/private data and final tests;
   update the consolidated handoff. Commit remaining authorized changes, push the
   milestone branch once and open one draft PR against main when authorized/supported.
   Attach the PR to the task where supported. Do not publish known critical unsafe changes.

Final review includes findings/corrections per subtask, actual tests, unmet acceptance,
policy questions, modified files/checkpoints, branch/PR or supported transfer commands,
local verification, rollback and an example prompt for the next milestone. Keep one
compact handoff, not global diaries. A draft PR can contain explicitly blocked acceptance
only when its implemented changes are safe and the limitations are clear.
