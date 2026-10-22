# MD-HRIS-EMP-UX-001 v2 — Employees complete UI/UX

- Date: 2026-10-22, Asia/Manila. Status: `IN_PROGRESS`.
- Accepted development main: `b5526bb4a7999bbd3c253fcdda1c8419a171314d` (PR #11 already merged; no second merge).
- In-progress starting baseline: `681f84e0bb3bbcd02cfb92b20a042e032a766d53`, draft PR #13, branch `feat/hris-employees-ux-modernization`. Extend this existing branch/PR to preserve earlier A/B/C without duplicate PRs. Original checkout stays on `work`.
- Owner v2 approval authorizes scoped frontend/dialog/menu changes and backward-compatible Position reads, commits/push/draft PR. No database contents/schema, production, permission grants, shared authorization, Payroll or PR #8 changes. PR #8 verified open/draft/unmerged at `73a951ed6b015c2b71d4a1336efcfac475150e2f`; its source is not imported.

## Contract and boundaries

Directory columns: Employee (exact existing name composition and employee number), Position, Branch, Employment Type, Employment Status, Actions. Keep explicit server search, ten-row pagination, stale-response protection, one shared table/card state and existing export. Name/View/Edit/Delete use original permission predicates; Compensation legacy visibility remains subject to separate unresolved backend policy. No Position sorting. Four header sorts plus supplementary existing Employee Number/Date Hired/Date Added ordering share state. Existing export's five-column workbook and scope stay intact.

Application allowlist for this v2 increment: `app/models/hris/EmployeeModel.php` (Position read only), `app/views/module_hris/employees.php`, `public/assets/css/style.css`; a minimal export sort compatibility change only if required by the newly visible existing Employment Type sort. Supporting verification scripts and this handoff. Existing controller/routes/CSRF/payloads, other model methods, export projection/scope, dependencies, schema and unrelated modules remain protected.

## Findings and progress

- Position uses existing `position_id → hris_positions.id`. The new list `position` field uses a LEFT JOIN constrained by employee/position tenant; primary key prevents duplicate rows. No total/search/sort query change, N+1 request or migration. Missing response property will be shown as unavailable, distinct from legitimate null/unassigned.
- Existing delete model updates only employee `status='INACTIVE'` and `deleted_at=NOW()` under ID/tenant. The list excludes deleted rows; no restore implementation or guaranteed related-record retention was established. Existing mutation semantics stay unchanged.
- Compensation routes have no dedicated permission predicate and absence uses ambiguous `success:false, message:'Data not found.'`. This cannot establish permission to create a rate. Refine existing-record workflow; block ambiguous creation and document policy/response dependency rather than inventing grants or changing backend logic.
- Real PHP/application/browser validation remains subject to previously observed environment limits. Continue independent source/VM checks. Never label simulated tests as real application or DB tests.

Next: implement directory, menus and dialogs sequentially; verify and publish updates to draft PR #13 without merging it.
