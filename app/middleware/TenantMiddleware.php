<?php
// SIMPLIFIED ROLE as "guard" or "normalizer", Enforce “tenant is always present”, hard safety in case devs forget to filter tenant in queries (like me.)
class TenantMiddleware
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
        | TENANT CHECK (FROM LOGIN SESSION)
        |--------------------------------------------------------------------------
        */

        $tenantId = $_SESSION['user']['tenant_id'] ?? null;

        if (!$tenantId) {
            return $this->deny($params, 'Tenant context missing.');
        }

        /*
        |--------------------------------------------------------------------------
        | NORMALIZE TENANT SESSION STRUCTURE
        |--------------------------------------------------------------------------
        */

        $_SESSION['tenant'] = [
            'id' => $tenantId,
            'status' => $_SESSION['tenant']['status'] ?? 'ACTIVE'
        ];

        /*
        |--------------------------------------------------------------------------
        | OPTIONAL SAFETY CHECK
        |--------------------------------------------------------------------------
        */

        if ($_SESSION['tenant']['status'] !== 'ACTIVE') {
            return $this->deny($params, 'Tenant is not active.');
        }

        return true;
    }

    /*
    |--------------------------------------------------------------------------
    | DENY HANDLER (same pattern as yours)
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