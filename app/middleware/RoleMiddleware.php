<?php
/////////// may not be needed anymore //////////

class RoleMiddleware
{
    public function handle(array $params = []): bool
    {
        $userRoles = $_SESSION['user']['roles'] ?? [];

        if (empty($userRoles)) {
            return $this->deny($params, "Access Denied: no roles assigned to user.");
        }

        /*
        |--------------------------------------------------------------------------
        | REQUIRED ROLE(S)
        |--------------------------------------------------------------------------
        | Supports:
        | - 'Admin'
        | - ['Admin', 'Manager']
        | - 'Admin,Manager'
        */

        if (empty($params['role']) && empty($params['roles'])) {
            throw new Exception("RoleMiddleware requires 'role' or 'roles' parameter.");
        }

        // Normalize required roles
        $requiredRoles = $params['roles'] ?? $params['role'];

        if (!is_array($requiredRoles)) {
            $requiredRoles = explode(',', $requiredRoles);
        }

        $requiredRoles = array_map('trim', $requiredRoles);

        /*
        |--------------------------------------------------------------------------
        | CHECK INTERSECTION
        |--------------------------------------------------------------------------
        */

        $hasRole = !empty(array_intersect($userRoles, $requiredRoles));

        if (!$hasRole) {
            return $this->deny(
                $params,
                "Access Denied: insufficient role."
            );
        }

        return true;
    }

    private function deny($params, $defaultMessage)
    {
        $message = $params['message'] ?? $defaultMessage;
        $type = $params['type'] ?? "error";

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {

            http_response_code(403);
            header('Content-Type: application/json');

            echo json_encode([
                'error' => $message,
                'type'  => $type
            ]);
            exit;
        }

        $redirect = $params['redirect'] ?? "/home";

        header('Location: ' . $redirect .
            '?alert=' . urlencode($message) .
            '&type=' . urlencode($type));

        exit;
    }
}
/*

// single role
$app->group(['RoleMiddleware:role=Admin'], function ($app) {

// multi role
$app->group(['RoleMiddleware:roles=Admin,Manager'], function ($app) {
*/
