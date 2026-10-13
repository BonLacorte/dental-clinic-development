<?php
class AuthMiddleware
{
    public function handle($params = [])
    {
        $currentPage = $params['page'] ?? ''; // pass 'login' if this is login page

        if (!isset($_SESSION['user'])) {
            // Not logged in → protected pages
            if ($currentPage !== 'login') {
                echo "<script>
                    alert('Access denied. Please login.');
                    window.location.href = '/';
                    </script>";
                exit;
            }
            // If login page, just show form (do nothing)
        } else {
            // Logged in → login page
            if ($currentPage === 'login') {
                header('Location: /home');
                exit;
            }
            // Else: logged in and accessing protected page → allow
        }

        return true;
    }
}