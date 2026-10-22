# Verification and evidence

- Discover existing tests and supported runtimes; do not assume a suite exists. Run
  compatible PHP lint (64-bit PHP 8.3+), focused tests and relevant browser/regression checks.
- Before write integration tests, establish a separate disposable fictional DB. Verify
  the actual connection/database, fresh fixture ownership, isolation from shared demo
  and production, and a bounded disposal procedure. Use no private records or dumps.
  Never reset shared demo state. Disposable fixtures may be cleaned up within explicitly
  approved test scope; never generalize that authority to other storage or databases.
- If isolation cannot be established, use fully intercepted/mocked tests and mark real
  persistence acceptance BLOCKED. Do not weaken safety to get a passing test.
- Exercise required/optional fields, malformed payloads, date boundaries, explicit
  failures, missing/foreign identifiers, CSRF and existing permission/tenant/branch rules.
  Compensation policy gaps stay blocked pending a decision; do not create new permissions.
- Verify success is backed by scoped persistence, including no-op edits and missing rows;
  lost responses remain ambiguous and must not trigger automatic mutation retries.
- Preserve unrelated UI, search/sort/dialog/focus, payroll/financial behavior and data.
  Label ordinary browser interaction, simulated network responses and direct model tests
  separately. Inspect logs without committing raw records, tokens or session contents.
- Report PASS (executed, passed), FAIL (executed, failed), BLOCKED (required prerequisite
  unavailable) and NOT RUN (not executed). Record commands, scope, environment and limits;
  never infer production readiness, full accessibility or cross-browser coverage.
