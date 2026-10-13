<?php

class ModuleMiddleware
{
    public function handle(array $params = [])
    {
        $module = $params['module'] ?? null;
        $module = $module ? strtoupper(trim($module)) : null;

        if (!$module) {
            throw new Exception("ModuleMiddleware requires module parameter.");
        }

        /// sadmin bypass
        if (!empty($_SESSION['user']['is_super_admin'])) {
            return true;
        }

        /// Check tenant module access
        if (!isset($_SESSION['user']['modules'][$module])) {
            return $this->deny($params, "Module not enabled for tenant.");
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