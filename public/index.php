<?php
date_default_timezone_set('Asia/Manila');
session_start();

require_once '../vendor/autoload.php';
// upload path
require_once '../config/path.php';

require_once '../core/App.php';
require_once '../core/Controller.php';
require_once '../core/Model.php';
require_once '../core/Storage.php';

// Include services
foreach (glob('../app/services/*.php') as $file) {
    require_once $file;
}

// Include helpers
foreach (glob('../app/helpers/*.php') as $file) {
    require_once $file;
}

// Initialize app
$app = new App();

// Load routes
require_once '../routes/web.php';

// Run app
$app->run();