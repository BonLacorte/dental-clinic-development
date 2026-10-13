<?php

class PermissionMiddleware
{
    public function handle(array $params = []): bool
    {
        /*
        |--------------------------------------------------------------------------
        | AUTH CHECK
        |--------------------------------------------------------------------------
        */

        if (empty($_SESSION['user']['id'])) {
            return $this->deny(
                $params,
                'Access Denied: user not authenticated.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | REQUIRED PERMISSION
        |--------------------------------------------------------------------------
        */

        if (empty($params['permission'])) {
            throw new Exception(
                "PermissionMiddleware requires 'permission' parameter."
            );
        }

        $requiredPermission = trim($params['permission']);

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN BYPASS
        |--------------------------------------------------------------------------
        */

        if (!empty($_SESSION['user']['is_super_admin'])) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | PERMISSION CHECK (SESSION ONLY)
        |--------------------------------------------------------------------------
        */

        /// old, slightly slower version
        // $permissions = $_SESSION['user']['permissions'] ?? [];
        // if (!in_array($requiredPermission, $permissions, true)) {
        //     return $this->deny(
        //         $params,
        //         "Access Denied: insufficient permission."
        //     );
        // }
        // return true;

        /// optimized version
        $permissionMap = $_SESSION['user']['permission_map'] ?? [];
        if (!isset($permissionMap[$requiredPermission])) {
            return $this->deny(
                $params,
                "Access Denied: insufficient permission."
            );
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | DENY HANDLER
    |--------------------------------------------------------------------------
    */

    private function deny(array $params, string $defaultMessage)
    {
        $message = $params['message'] ?? $defaultMessage;
        $type = $params['type'] ?? 'error';

        /*
        |--------------------------------------------------------------------------
        | AJAX RESPONSE
        |--------------------------------------------------------------------------
        */

        if (
            !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
        ) {
            http_response_code(403);

            header('Content-Type: application/json');

            echo json_encode([
                'error' => $message,
                'type'  => $type
            ]);

            exit;
        }

        /*
        |--------------------------------------------------------------------------
        | NORMAL REDIRECT
        |--------------------------------------------------------------------------
        */

        $redirect = $params['redirect'] ?? '/home';

        header(
            'Location: ' . $redirect .
            '?alert=' . urlencode($message) .
            '&type=' . urlencode($type)
        );

        exit;
    }
}