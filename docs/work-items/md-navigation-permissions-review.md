# Navigation permissions: initial commit versus current implementation

## Identity and scope

- Task: `md-navigation-permissions-review`; reviewed 2026-11-07 (Asia/Manila).
- Status: `DONE` for source/history analysis. This is not a completed security audit or live-server test.
- Initial GitHub baseline: `c99ded60c92b33ba18f21a62a1461763fc779caa`, 2026-10-13.
- Current comparison: `b39bfed` plus the Employee Profile changes on `feat/employee-profile`.
- Additional context: selected source files from the user-authorized local production snapshot. No production connection, private records, credentials or database import was used.
- User requested both Employee Profile publication to the personal repository's `main` and this historical explanation. No message to the senior developer or production deployment is authorized.

## What the senior developer appears to mean

The supplied message says the navigation already has permissions and is connected to authentication, which overlaps work the senior developer had planned. The closest verified change is commit [`14dbc7f`](https://github.com/BonLacorte/dental-clinic-development/commit/14dbc7f4f31b57c7b144abde1013ae9d5dce5caa), **feat(ui): improve login and navigation (UI-01B)**, 2026-10-17 at 23:02 Manila time.

This is an inference about his intent from the message and source history. The code change itself is verified: that commit introduced the navigation flags in `admin-header.php` and consumed them in the sidebar and module navigation.

## Before and after

| Area | Initial commit | Current playground |
| --- | --- | --- |
| Login/backend authentication | Already validates credentials/CSRF, regenerates the session ID and stores user context. | The authentication controller is byte-identical to the initial commit. The login form's presentation, labels and autocomplete changed. |
| Role/permission loading | Already reads roles, permission codes, tenant modules and branch assignments at login. | Same models and loading logic; navigation now uses their session values. |
| Endpoint permission enforcement | Already has authentication, tenant, module, permission, role and branch middleware. | Those middleware files are byte-identical to the initial commit. Route-specific checks remain responsible for requests. |
| Sidebar | Links such as Users, Tenants, HRIS and Payroll were rendered without the current permission/module visibility conditions. | Links are conditionally rendered using tenant context, permission codes, enabled modules and the super-admin flag. |
| HRIS navigation | Home, Employees, Organization and Attendance navigation was mostly unconditional. | Home/Organization require HRIS plus `hris.view`; Employees requires HRIS plus `hris.employee.view`; DTR is gated by HRIS module availability. |
| Payroll navigation | Module links were rendered without the current shared visibility conditions. | Home requires Payroll plus `payroll.view`; payroll items/runs/import links use the Payroll module gate. Individual routes must still enforce their registered checks. |
| Employee Profile | Did not exist. | Adds a view-permission route, tenant-scoped lookup, branch check and permission-conditioned editing UI using the existing session contract. |

## How the connection works

1. `UserModel::getUserPermissions()` joins `user_roles` to `role_permissions` and `permissions`, returning permission codes. `TenantModel::getModules()` returns the tenant's module codes.
2. `AuthController::loginPost()` loads these after credential validation and creates `permission_map` and `modules` lookup maps with `array_flip()`. It also stores `is_super_admin`, tenant and branch context in the session.
3. `app/views/partials/admin-header.php` lines 49-68 derives shared navigation flags. `$navHasTenant` requires a user ID, tenant ID and an ACTIVE tenant session state. `$navCanPermission()` checks `is_super_admin` or the required code in `permission_map`; `$navCanModule()` checks the same bypass or the module map.
4. `admin-sidebar.php`, `module_hris/partials/hris_nav.php` and `module_payroll/partials/payroll_nav.php` consume those flags when rendering links.
5. Direct URL/AJAX requests go through the router's separately registered middleware. Hiding a link does not grant or revoke backend access. Every endpoint and row query still needs its own appropriate checks.

Examples: Users is shown with `user.view`; Tenants with `tenant.view`; HRIS Employees with module `HRIS` plus `hris.employee.view`; Payroll Home with module `PAYROLL` plus `payroll.view`. Dashboard, About and Branches use the tenant-context gate rather than new dedicated permission codes.

The permissions are loaded at login, not reloaded from the database by the menu on every request. A fresh login reloads permission/module assignments. No immediate permission-revocation mechanism was established by this navigation change.

## History and unchanged backend evidence

- `9ade18a`, 2026-10-17: responsive admin shell foundation; the later `14dbc7f` introduced the shared permission-aware navigation flags.
- `14dbc7f`, 2026-10-17: permission/module-aware sidebar and module navigation, plus login presentation changes.
- `abc4e70`, 2026-10-19: later HRIS navigation/presentation refinements.
- `c2f88ba`, 2026-10-28: later Payroll navigation refinements; the current sidebar differs from the earlier UI-01B version.
- Byte comparison from `c99ded6` to the reviewed playground confirmed no changes to `AuthController.php`, `UserModel.php`, `TenantModel.php`, all six middleware files, `Csrf.php`, `Crypto.php`, `core/App.php` or `routes/web.php`.

Reproduce the central comparisons with:

```bash
git diff c99ded6 14dbc7f -- app/views/partials/admin-header.php app/views/partials/admin-sidebar.php app/views/module_hris/partials/hris_nav.php app/views/module_payroll/partials/payroll_nav.php
git diff c99ded6 b39bfed -- app/controllers/AuthController.php app/models/UserModel.php app/models/TenantModel.php app/middleware app/services/Csrf.php app/services/Crypto.php core/App.php routes/web.php
git log --oneline -- app/views/partials/admin-header.php app/views/partials/admin-sidebar.php
```

The second command has no changes for those paths. This does not claim every controller, route or model elsewhere in the repository is unchanged.

## Production snapshot is a separate version

The snapshot's shared admin header/sidebar and Permission/Module/Tenant middleware match the playground. Its authentication controller and user model have additional differences absent from this GitHub baseline:

- `AuthController` consumes role rows, extracts role names and IDs, stores `role_ids`, and checks both role ID `1` and name `Super Admin` for its bypass flag. The playground's existing controller checks the returned role-name list.
- The snapshot's user lookup also filters `users.deleted_at IS NULL`.
- Module navigation files differ between snapshot and playground.

These snapshot changes cannot be attributed to the personal repository's commits or to the navigation change from Git evidence alone. Keep each authentication controller paired with its compatible user-model role return shape; copying either wholesale between versions needs a separate review. None was imported for Employee Profile publication.

## Limits and follow-up items

- **PASS:** initial/current byte comparisons, navigation commit attribution, session/query/flag source tracing and selected snapshot comparisons.
- **NOT RUN:** a full restricted-role browser matrix, immediate permission-revocation tests or a complete endpoint/row-level authorization audit.
- Existing mismatch to review separately: Employees navigation/list page uses `hris.employee.view`, while `getallemployees` also requires `hris.view`. Having a visible menu does not prove every subsequent AJAX call is allowed for a restricted user. No permission behavior was changed by this review.
- The new profile reuses existing update/lookup endpoints; permission-conditioned editing controls do not by themselves establish row-level authorization of those existing endpoints.

Next concrete step: review this explanation with the senior developer, then agree separately on any endpoint-permission alignment or snapshot-authentication synchronization work.

## Suggested explanation to the senior developer

> Oo sir, yung navigation naka-base na sa existing permissions at modules na niloload during login. Dati may backend middleware checks na, pero hindi pa consistent yung visibility ng menu links. Sa UI-01B changes, ginamit ko yung `permission_map`, module map at `is_super_admin` sa session para conditional yung sidebar at HRIS/Payroll navigation. Same existing authentication flow pa rin sa GitHub copy; yung dinagdag ko ay permission-aware menu rendering. May separate middleware checks pa rin sa direct URL at AJAX requests, kaya kailangan pa rin i-review per endpoint yung permissions.
