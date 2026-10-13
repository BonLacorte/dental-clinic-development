============================================================
ROLE SERVICE (RBAC GUIDE - COPY PASTE NOTES)
============================================================

This RoleService is the SINGLE SOURCE OF TRUTH for role-based access control.

------------------------------------------------------------
1. ROLE HIERARCHY
------------------------------------------------------------
Defines role power levels (higher number = more access)

GUEST     = 1
ENCODER   = 2
VALIDATOR = 3
REGION    = 4
ADMIN     = 5
SADMIN    = 6

------------------------------------------------------------
2. getLevel(role)
------------------------------------------------------------
Purpose:
Converts role name → numeric level

Usage:
getLevel("ADMIN") → 5
getLevel("ENCODER") → 2
Invalid role → 0

Use case:
- comparisons
- middleware checks
- permission validation

------------------------------------------------------------
3. getAllowedRoles(role)
------------------------------------------------------------
Purpose:
Returns roles LOWER than current role

Example:
Input: ADMIN
Output: [GUEST, ENCODER, VALIDATOR, REGION]

Logic:
Only roles with LOWER level are returned

Use case:
- DataTables filtering
- dropdown role selection
- restricted API responses

------------------------------------------------------------
4. canManage(currentRole, targetRole)
------------------------------------------------------------
Purpose:
Checks if a user can manage another role

Example:
ADMIN → REGION  = TRUE
REGION → ADMIN  = FALSE

Rule:
currentLevel > targetLevel

Use case:
- edit user
- delete user
- assign role
- authorization checks

------------------------------------------------------------
HOW TO USE
------------------------------------------------------------

1. Middleware (access control)
--------------------------------
if (!$roleService->canManage($userRole, 'ADMIN')) {
    deny access;
}

2. Data filtering (SQL / API)
------------------------------
$allowedRoles = $roleService->getAllowedRoles($userRole);

Use in SQL:
WHERE roleid IN (...)

3. Create user validation
--------------------------
if (!$roleService->canManage($currentRole, $newRole)) {
    throw Exception("Cannot assign higher role");
}

4. Update user validation
--------------------------
Same logic as create user

5. UI dropdown filtering
------------------------
$allowedRoles = $roleService->getAllowedRoles($currentRole);

Only show these roles in select options

6. Button permissions (UI)
--------------------------
if ($roleService->canManage($currentRole, $rowRole)) {
    show edit/delete button;
}

------------------------------------------------------------
IMPORTANT RULES
------------------------------------------------------------

- NEVER duplicate hierarchy elsewhere
- NEVER trust frontend (UI is not security)
- ALWAYS enforce in backend (middleware + service)
- RoleService is the ONLY authority for roles

------------------------------------------------------------
FUTURE UPGRADE PATH
------------------------------------------------------------

Current:
- Hardcoded PHP array hierarchy

Future:
- roles table (id, name, level)

Then RoleService becomes DB-driven WITHOUT changing usage.

============================================================
END OF GUIDE
============================================================

------------------------------------------------------------
EXAMPLE CONTROLLER
------------------------------------------------------------

<?php

class UserController
{
    public function createUser()
    {
        $roleService = new RoleService(); // services/RoleService.php

        $currentRole = $_SESSION['user']['roleid'] ?? 'GUEST';
        $newRole = $_POST['roleid'];

        // check permission
        if (!$roleService->canManage($currentRole, $newRole)) {
            http_response_code(403);
            echo "Access denied: insufficient role";
            exit;
        }

        // proceed save
        $model = new UserModel();
        $model->createUser($_POST);

        echo "User created successfully";
    }
}
