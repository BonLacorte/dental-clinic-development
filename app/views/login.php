<?php
// 1. Force HTTPS
//header("Strict-Transport-Security: max-age=63072000; includeSubDomains; preload");

// 2. Prevent clickjacking
header("X-Frame-Options: SAMEORIGIN");

// 3. Stop MIME type sniffing
header("X-Content-Type-Options: nosniff");

// 4. Control referrer info
header("Referrer-Policy: no-referrer-when-downgrade");

// 5. Browser feature permissions
header("Permissions-Policy: camera=(), microphone=(), geolocation=()");

// 6. Basic XSS protection
header("X-XSS-Protection: 1; mode=block");

// 7. Content Security Policy
header("Content-Security-Policy: frame-ancestors 'self'; base-uri 'self'; default-src 'none'; script-src 'none'; style-src 'self'; object-src 'none'; img-src 'self' data:; form-action 'self';");


if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }

$maxAttempts = 5;     // max failed attempts
$lockoutTime = 300;   // seconds (5 minutes)

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = time();
}

// Check if user is currently locked out
if ($_SESSION['login_attempts'] >= $maxAttempts && (time() - $_SESSION['last_attempt_time']) < $lockoutTime) {
    //$_SESSION['login_attempts'] = 0; enable to allow valid user to login
    ?>
        <div class="alert alert-warning"><?= htmlspecialchars('Too many failed login attempts. Try again later.') ?></div>
    <?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Montero | Login</title>
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css">
    <link rel="icon" href="/assets/images/montero_logo2.jpg">
</head>
<body>
    <section class="login-bg">
        <grid class="login-grid">
            <div class="login-logo">
                <img class="login-image" src="/assets/images/montero_logo.jpg" >
            </div>
            <div class="login-form-box">
                <div class="longin-form-value">
                    <form action="/" id="loginForm" method="POST" class="form">
                        <?= Csrf::input(); ?>
                        <h2 class="login-h2">Login</h2>
                        <div class="login-inputbox">
                            <ion-icon name="person-outline"></ion-icon>
                            <input type="email" name="email" class="field" id="email" required><label
                                for="username">Email</label>
                        </div>
                        <div class="login-inputbox">
                            <ion-icon name="lock-closed-outline"></ion-icon>
                            <input type="password" name="password" class="field" id="password" autocomplete="off" required><label
                                for="password">Password</label>
                        </div>
                        <button type="submit" name="submit" id="login-button">Login</button>
                    </form>
                </div>
            </div>
        </grid>
    </section>
</body>
</html>

