<?php

class BranchMiddleware
{
    public function handle(array $params = []): bool
    {
        /*
        |--------------------------------------------------------------------------
        | AUTH CHECK
        |--------------------------------------------------------------------------
        */

        if (empty($_SESSION['user']['id'])) {
            return $this->deny($params, 'User not authenticated.');
        }

        /*
        |--------------------------------------------------------------------------
        | GET USER CONTEXT
        |--------------------------------------------------------------------------
        */

        $userId = $_SESSION['user']['id'];

        $isSuperAdmin = $_SESSION['user']['is_super_admin'] ?? false;

        /*
        |--------------------------------------------------------------------------
        | BRANCH INPUT (OPTIONAL OVERRIDE)
        |--------------------------------------------------------------------------
        | You may pass branch_id via request (switching context)
        */

        $requestedBranchId =
            $_POST['branch_id'] ??
            $_GET['branch_id'] ??
            $_SESSION['branch']['id'] ??
            null;

        /*
        |--------------------------------------------------------------------------
        | SUPER ADMIN BYPASS
        |--------------------------------------------------------------------------
        */

        if ($isSuperAdmin) {
            $_SESSION['branch'] = [
                'id' => $requestedBranchId ?: null,
                'scope' => 'ALL'
            ];

            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDATE USER BRANCH ACCESS
        |--------------------------------------------------------------------------
        */

        $allowedBranches = $_SESSION['user']['branches'] ?? [];

        /*
        | If no branches assigned, assume ALL ACCESS (fallback rule)
        */

        if (empty($allowedBranches)) {
            $_SESSION['branch'] = [
                'id' => $requestedBranchId ?: null,
                'scope' => 'ALL'
            ];

            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | STRICT CHECK (if branch is requested)
        |--------------------------------------------------------------------------
        */

        if ($requestedBranchId !== null) {

            if (!in_array($requestedBranchId, $allowedBranches, true)) {
                return $this->deny(
                    $params,
                    'Access denied: branch not permitted.'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SET ACTIVE BRANCH CONTEXT
        |--------------------------------------------------------------------------
        */

        $_SESSION['branch'] = [
            'id' => $requestedBranchId ?: $allowedBranches[0],
            'scope' => (count($allowedBranches) > 1) ? 'MULTIPLE' : 'SINGLE',
            'allowed' => $allowedBranches
        ];

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | DENY HANDLER (same pattern as your other middleware)
    |--------------------------------------------------------------------------
    */

    private function deny(array $params, string $defaultMessage)
    {
        $message = $params['message'] ?? $defaultMessage;
        $type = $params['type'] ?? 'error';

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

        $redirect = $params['redirect'] ?? '/home';

        header(
            'Location: ' . $redirect .
            '?alert=' . urlencode($message) .
            '&type=' . urlencode($type)
        );

        exit;
    }
}