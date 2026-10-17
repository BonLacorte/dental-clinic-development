<?php
$current = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar offcanvas offcanvas-start" id="sidebar" tabindex="-1" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title h5" id="sidebarLabel">Navigation</h2>
        <button class="btn-close btn-close-white" type="button" id="closeSidebar" aria-label="Close navigation"></button>
    </div>
    <nav class="offcanvas-body" aria-label="Main navigation">
    <a class="<?= ($current == '/home') ? 'active' : '' ?>" href="/home">
        <i aria-hidden="true" class="fas fa-tachometer-alt me-2"></i>Dashboard
    </a>
    <a class="<?= ($current == '/about') ? 'active' : '' ?>" href="/about">
        <i aria-hidden="true" class="fas fa-info-circle me-2"></i>About
    </a>
    <a class="<?= ($current == '/users') ? 'active' : '' ?>" href="/users">
        <i aria-hidden="true" class="fas fa-user me-2"></i>Users
    </a>
    <a class="<?= ($current == '/roles') ? 'active' : '' ?>" href="/roles">
        <i aria-hidden="true" class="fas fa-user me-2"></i>Roles
    </a>
    <a class="<?= ($current == '/tenants') ? 'active' : '' ?>" href="/tenants">
        <i aria-hidden="true" class="fas fa-building me-2"></i>Tenants
    </a>
    <a class="<?= ($current == '/branches') ? 'active' : '' ?>" href="/branches">
        <i aria-hidden="true" class="fas fa-building me-2"></i>Branches
    </a>

    <!-- Dropdown -->
    <?php
        $openModules = ['/hris', '/payroll', '/clinic']; // also add modules url here
        $isOpen = false;
        foreach ($openModules as $module) {
            if (str_contains($current, $module)) {
                $isOpen = true;
                break;
            }
        }
    ?>
    <!-- <div class="nav-group <?= (str_contains($current, '/hris')) ? 'open' : '' ?>"> -->
    <div class="nav-group <?= $isOpen ? 'open' : '' ?>">
        <button class="nav-parent" type="button" aria-controls="sidebarModules" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>">
            <i aria-hidden="true" class="fa fa-bars"></i>
            <span>Modules</span>
            <i aria-hidden="true" class="fas fa-chevron-down arrow"></i>
        </button>
        <div class="nav-child" id="sidebarModules">
            <a class="<?= ($current == '/hris') ? 'active' : '' ?>" href="/hris">HRIS</a>
            <a class="<?= ($current == '/payroll') ? 'active' : '' ?>" href="/payroll">Payroll</a>
            
            <a class="<?= ($current == '/clinic') ? 'active' : '' ?>" href="/clients">Clinic</a>
        </div>
    </div>

    <a class="<?= ($current == '/settings') ? 'active' : '' ?>" href="/settings">
        <i aria-hidden="true" class="fas fa-cog me-2"></i>Settings
    </a>
    </nav>
</div>
