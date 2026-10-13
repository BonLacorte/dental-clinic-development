<?php
/*
Use DataTables:
    1. Set $useDataTables = true;
    2. Head: include DataTables CSS
    3. Footer: include DataTables JS (after jQuery)

Use htmlspecialchars() on php echo - ex. <?= e($variable); ?> !important, 
Use json_encode() - ex. je(['errors' => $result['errors']], 'json'); /// custom JSON echo in helpers.php

Use Toast (Get request) - redirect to /url?alert=message&type=error
Use Toast (AJAX/JS) - showToast('message','error');
Use Loader
    showLoader("Message...");
    hideLoader();

HTML Template (copy paste to new page/file)
-------------------------------------------

<?php
$title = "New Web Page";
$useDataTables = false;
include 'partials/admin-header.php';
?>
<h2 class="mb-4">New Web Page</h2>
<div class="card mb-4">
    <div class="card-body">
    <!-- Main Contents Here -->
    </div>
</div>
<?php include 'partials/admin-footer.php'; ?>
<!-- JS Jquery Scripts Here -->
</body>
</html>

------------------------------------------

---- Session structure ----
$_SESSION['user'] = [           
    'userid'    => 'johndoe',        // username
    'roleid'        => 'ENCODER',         // role string, or null if not logged in
    'userscope'       => 'GLOBAL',          // location scope, GLOBAL or LOCAL,
    'regioncode'      => '01',              // will be required if scope is local
    'permissions' => ['edit_post', 'delete_post'], // none yet, optional, can be empty array
    'email'        => 'example@gmail.com',       // optional display name
];

*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= 'Montero Dental | ' . e($data['title'] ?? 'Admin Panel') ?></title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css?v=<?= filemtime(BASE_PATH . '/public/assets/css/style.css') ?>">
    <link rel="icon" href="/assets/images/logo512.png">

    <!-- DataTables -->
    <?php if ($useDataTables ?? false): ?>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
    <?php endif; ?>

    <script>
        function showToast(message, type="", duration = 3000) {
            const toast = document.getElementById('toast');
            toast.textContent = message;
            toast.style.display = 'block';
            toast.className = type; // e.g., 'success' or 'error' add more to css
            toast.style.opacity = '1';

            setTimeout(() => {
                toast.style.transition = 'opacity 0.5s';
                toast.style.opacity = '0';
            }, duration);
        }
    </script>
</head>
<body>
<!-- TOP NAVBAR -->
<?php include 'admin-navbar-top.php' ?>

<!-- Alert Toast Container -->
<div id="toast"></div>
<?php if (!empty($_GET['alert'])): ?>
<script>
    showToast(
        "<?= htmlspecialchars($_GET['alert'], ENT_QUOTES) ?>",
        "<?= htmlspecialchars($_GET['type'] ?? '', ENT_QUOTES) ?>"
    );
</script>
<?php endif; ?>

<!-- Full Page Loader -->
<div id="page-loader" class="d-none">
    <div class="loader-content">
        <div class="spinner-border text-primary" role="status"></div>
        <div class="loader-text">Loading, please wait...</div>
    </div>
</div>

<div class="d-flex" id="wrapper">

<!-- Sidebar -->
<?php include 'admin-sidebar.php';?>

    <!-- CONTENT + FOOTER WRAPPER -->
        <div id="content-wrapper">
            <div id="content" class="content">
            <!-- Page content goes here -->
