<?php

class Csrf
{
    private static int $limit = 20; // max stored tokens

    public static function generate(): string {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_tokens'])) {
            $_SESSION['csrf_tokens'] = [];
        }

        $token = bin2hex(random_bytes(32));

        // Add new token
        $_SESSION['csrf_tokens'][] = $token;

        // Limit number of tokens
        if (count($_SESSION['csrf_tokens']) > self::$limit) {
            array_shift($_SESSION['csrf_tokens']); // remove oldest
        }

        return $token;
    }

    public static function destroy() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        unset($_SESSION['csrf_tokens']); // remove all tokens
    }

    public static function validate(string $token, $action = 'reset'): bool {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (!isset($_SESSION['csrf_tokens'])) {
            return false;
        }

        $index = array_search($token, $_SESSION['csrf_tokens'], true);

        if ($index !== false) {
            if ($action == 'reset') {
                // remove only the used token
                unset($_SESSION['csrf_tokens'][$index]);
            } 
            

            return true;
        }

        return false;
    }

    public static function input(): string {
        return '<input type="hidden" name="csrf_token" value="' . e(self::generate()) . '">';
    }
}