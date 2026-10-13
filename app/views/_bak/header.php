<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'My App' ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<header class="bg-dark text-white p-3 text-center">
    <h1>APP Name</h1>
</header>
<!-- Full Page Loader -->
<div id="page-loader" class="page-loader d-none">
    <div class="loader-content">
        <div class="spinner"></div>
        <div class="loader-text">Loading, please wait...</div>
    </div>
</div>
<?php include 'navbar-top.php'; ?>

<div class="container mt-4">