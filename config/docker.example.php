<?php
// Fictional demo credentials only; bound over config/local.php INSIDE Docker.
// Never point this file at a production or staging database.
return [
    'default' => [
        'host' => 'db',
        'port' => 3306,
        'dbname' => 'monterodental_demo',
        'user' => 'montero_demo',
        'pass' => 'LocalDemo2026!',
    ],
];
