<?php
$current = $_SERVER['REQUEST_URI'];
?>

<div class="sidebar" id="sidebar">
    <a class="<?= ($current == '/home') ? 'active' : '' ?>" href="/home">
        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
    </a>
    <a class="<?= ($current == '/about') ? 'active' : '' ?>" href="/about">
        <i class="fas fa-info-circle me-2"></i>About
    </a>
    <a class="<?= ($current == '/users') ? 'active' : '' ?>" href="/users">
        <i class="fas fa-user me-2"></i>Users
    </a>
    <a class="<?= ($current == '/roles') ? 'active' : '' ?>" href="/roles">
        <i class="fas fa-user me-2"></i>Roles
    </a>
    <a class="<?= ($current == '/tenants') ? 'active' : '' ?>" href="/tenants">
        <i class="fas fa-building me-2"></i>Tenants
    </a>
    <a class="<?= ($current == '/branches') ? 'active' : '' ?>" href="/branches">
        <i class="fas fa-building me-2"></i>Branches
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
        <div class="nav-parent">
            <i class="fa fa-bars"></i>
            <span>Modules</span>
            <i class="fas fa-chevron-down arrow"></i>
        </div>
        <div class="nav-child">
            <a class="<?= ($current == '/hris') ? 'active' : '' ?>" href="/hris">HRIS</a>
            <a class="<?= ($current == '/payroll') ? 'active' : '' ?>" href="/payroll">Payroll</a>
            
            <a class="<?= ($current == '/clinic') ? 'active' : '' ?>" href="/clients">Clinic</a>
        </div>
    </div>

    <a class="<?= ($current == '/settings') ? 'active' : '' ?>" href="/settings">
        <i class="fas fa-cog me-2"></i>Settings
    </a>
</div>
