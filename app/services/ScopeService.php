<?php

class ScopeService
{
    /**
     * DB connection (multi-tenant ready)
     */
    private static function db($dbName = 'default')
    {
        return Database::getInstance($dbName);
    }

    /**
     * Get all scopes assigned to a user
     */
    public static function getUserScopes($userid, $dbName = 'default')
    {
        $db = self::db($dbName);

        $stmt = $db->prepare("
            SELECT s.scope_name
            FROM user_scopes us
            JOIN scopes s ON s.scope_id = us.scope_id
            WHERE us.userid = ?
        ");

        $stmt->execute([$userid]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Check if user has a specific scope
     * Example: GLOBAL, REPORT_VIEW
     * Global scope in the user table should only access users own tenant only.
     * Only Devs have true global access, (sadmin)
     */
    public static function hasScope($userid, $requiredScope, $dbName = 'default')
    {
        $scopes = self::getUserScopes($userid, $dbName);

        return in_array($requiredScope, $scopes);
    }

    /**
     * Get all branches assigned to a user (WHITELIST MODEL)
     * This replaces fragile "special group" logic.
     */
    public static function getUserBranches($userid, $dbName = 'default')
    {
        $db = self::db($dbName);

        $stmt = $db->prepare("
            SELECT branch_id
            FROM user_branches
            WHERE userid = ?
        ");

        $stmt->execute([$userid]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Branch-based access control
     * Rule: user must be explicitly assigned to branch
     */
    public static function canAccessBranch($userid, $branchId, $dbName = 'default')
    {
        $branches = self::getUserBranches($userid, $dbName);

        if (!$branches) {
            return [
                'allowed' => false,
                'message' => 'No branch access assigned'
            ];
        }

        if (!in_array($branchId, $branches)) {
            return [
                'allowed' => false,
                'message' => 'Forbidden: You do not have access to this branch'
            ];
        }

        return ['allowed' => true];
    }

    /**
     * GLOBAL override + branch access check
     * GLOBAL scope bypasses all branch restrictions
     */
    public static function authorizeBranchAccess($userid, $branchId, $dbName = 'default')
    {
        if (self::hasScope($userid, 'GLOBAL', $dbName)) {
            return ['allowed' => true];
        }

        return self::canAccessBranch($userid, $branchId, $dbName);
    }

    /**
     * Tenant validation helper (security layer)
     */
    public static function validateTenant($userid, $dbName = 'default')
    {
        $db = self::db($dbName);

        $stmt = $db->prepare("
            SELECT tenant_id
            FROM users
            WHERE userid = ?
        ");

        $stmt->execute([$userid]);

        return $stmt->fetchColumn();
    }
}

/* reference db structure

1. USERS (multi-tenant identity) 
CREATE TABLE users (
    userid INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    username VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(150),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

2. BRANCHES (tenant-based organizational units)
CREATE TABLE branches (
    branch_id INT PRIMARY KEY AUTO_INCREMENT,
    tenant_id INT NOT NULL,
    branch_name VARCHAR(100),
    branch_code VARCHAR(20),
    is_active TINYINT(1) DEFAULT 1
);

3. USER ↔ BRANCHES (WHITELIST ACCESS CONTROL)
CREATE TABLE user_branches (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userid INT NOT NULL,
    branch_id INT NOT NULL,

    FOREIGN KEY (userid) REFERENCES users(userid) ON DELETE CASCADE,
    FOREIGN KEY (branch_id) REFERENCES branches(branch_id) ON DELETE CASCADE
);

4. SCOPES (WHAT USER CAN DO)
CREATE TABLE scopes (
    scope_id INT PRIMARY KEY AUTO_INCREMENT,
    scope_name VARCHAR(50) UNIQUE NOT NULL,
    description VARCHAR(255) NULL
);

5. USER SCOPES (RBAC mapping)
CREATE TABLE user_scopes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    userid INT NOT NULL,
    scope_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    FOREIGN KEY (userid) REFERENCES users(userid) ON DELETE CASCADE,
    FOREIGN KEY (scope_id) REFERENCES scopes(scope_id) ON DELETE CASCADE
);

6. SAMPLE DATA

users:
userid | tenant_id
1      | 1

branches:
branch_id | tenant_id
1          | 1
2          | 1
3          | 1

user_branches:
userid | branch_id
1      | 1
1      | 2

scopes:
scope_id | scope_name
1        | GLOBAL
2        | VIEW_REPORT
3        | VIEW_EMPLOYEES
4        | EDIT_USER

user_scopes:
userid | scope_id
1      | 2
*/