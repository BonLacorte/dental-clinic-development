# C-EMP — Employee correctness and autonomous milestone workflow

## Identity, scope and acceptance

- Branch: `feat/c-emp-correctness`; status: IN_PROGRESS.
- Reviewed: 2026-10-18, Asia/Manila. Verified origin/main: `01c9d5e0f9c1e391ee5bb74a40879d83c864c4dc` (merged UI-01A/B/C).
- User explicitly authorized grouped development, routine local checkpoints and one final push/draft PR. No merge, production synchronization/access or deployment.
- PLAN → SPEC → BUILD → VERIFY → REVIEW is continuous. One consolidated handoff contains C-EMP-01/02/03; historical UI-01 documentation remains unchanged.

Acceptance: reusable skill/workflow reads and validates; invalid employee payloads never persist;
readonly number remains server-owned; valid optional fields work; failed/missing/ambiguous
requests cannot claim success; scoped writes preserve caller shapes; assess every employee
and compensation route/middleware/controller/model/SQL boundary; implement only established
policies. Run PHP lint, focused tests and browser/regressions with isolated fictional fixtures.
Report blocked policy and unavailable coverage honestly.

## C-EMP-01 — Validation specification and checkpoint

Confirmed source defects: controller ignores validator errors; readonly employee_no is an
unexpected field; invalid required fields can reach SQL; shared date helper dereferences
false getLastErrors on modern PHP; array payloads can throw or cast; compensation uses
unsupported integer/numeric types. Use a narrow employee-input adapter rather than changing
global validator behavior or payroll handlers. Accept/ignore the legacy readonly employee_no;
optional blank fields normalize to null. Validate canonical calendar dates, positive IDs,
unknown fields and resignation order before persistence. Keep existing requiredness.

## C-EMP-02 — Response specification and checkpoint

Preserve success/data/message/id and list pagination keys. Require boolean success, never
infer success from missing flags or SQL execute returning true for zero matched rows.
Scoped unchanged edits may succeed; missing/deleted/foreign rows must fail. Keep the legacy
`success:false,message:"Data not found."` only for an accessible employee with no compensation;
other read failures cannot open compensation creation. Add JSON failure envelopes with
success:false/message/type and optional field errors, using existing HTTP-200 application
failure convention for compatible callers. Exceptions use generic HTTP 500 messages; callers
preserve drafts for ambiguous failures and never automatically retry writes.

## C-EMP-03 — Security specification and checkpoint

Review confirms auth/tenant/HRIS module group and per-action employee permissions, but list
uses broader hris.view; compensation HRIS routes have no explicit permission or CSRF check.
Employee base SQL has tenant predicates, but references/joins can cross tenants and deleted
rows remain addressable. Payroll compensation uses a separate PAYROLL group and CSRF/reference
checks; its per-operation permissions are unspecified. Assess it without inventing rights.

Use existing session branch assignments/BranchMiddleware semantics (Super Admin and empty
assignments retain tenant-wide fallback; nonempty assignments restrict employee rows).
Normalize server session IDs; never trust client tenant_id. Enforce scope on employee list,
detail and mutation queries; validate tenant-owned references, retaining global employment
catalogs. Match compensation update to both existing compensation and employee (no silent
reparenting), and require existing CSRF tokens on HRIS compensation writes. Do not create a
new compensation permission, change financial calculations, select a current rate policy,
or alter payroll endpoint business contracts. Report unresolved policy separately.

## Verification and final review

C-EMP-01 checkpoint: employee-local EmployeeInput adapter and controller rejection/CSRF gate implemented. PASS: 54 pure input assertions and PHP 8.3.35 lint. Initial NUL-date test exposed trimming of NUL; adapter now rejects NUL before normalization and all cases pass. Dedicated test DB verified empty, no mounts/DB host port, tmpfs storage and own internal network; fictional seeder completed. Persistence/HTTP/browser tests pending. Establish a fresh, dedicated MariaDB
container with tmpfs storage, no DB host port, its own internal network and no shared demo
volume before any write-capable tests. Verify empty owned fixtures and exact connection;
dispose only that owned container/network. Never run write tests against shared demo state.
No production/private records or raw tokens belong in this handoff or Git.

Next concrete action: implement and test C-EMP-01 input validation on this branch; then
response and scoped security checkpoints, final combined review and authorized publication.
