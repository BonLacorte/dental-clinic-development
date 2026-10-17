<?php
$navPrimaryLinks = [
    ['/home', 'Dashboard', 'fa-tachometer-alt', $navHasTenant],
    ['/about', 'About', 'fa-info-circle', $navHasTenant],
    ['/users', 'Users', 'fa-user', $navCanPermission('user.view')],
    ['/tenants', 'Tenants', 'fa-building', $navCanPermission('tenant.view')],
    ['/branches', 'Branches', 'fa-building', $navHasTenant],
];
$isOpen = $navIsSection('/hris') || $navIsSection('/payroll');
?>

<div class="sidebar offcanvas offcanvas-start" id="sidebar" tabindex="-1" aria-labelledby="sidebarLabel">
    <div class="offcanvas-header">
        <h2 class="offcanvas-title h5" id="sidebarLabel">Navigation</h2>
        <button class="btn-close btn-close-white" type="button" id="closeSidebar" aria-label="Close navigation"></button>
    </div>
    <nav class="offcanvas-body" aria-label="Main navigation">
        <?php foreach ($navPrimaryLinks as [$href, $label, $icon, $visible]): ?>
            <?php if ($visible): ?>
            <a class="<?= $navIsSection($href) ? 'active' : '' ?>" href="<?= e($href) ?>"<?= $navCurrentAttribute($href) ?>>
                <i aria-hidden="true" class="fas <?= e($icon) ?> me-2"></i><?= e($label) ?>
            </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($navHris || $navPayroll): ?>
        <div class="nav-group <?= $isOpen ? 'open' : '' ?>">
            <button class="nav-parent" type="button" aria-controls="sidebarModules" aria-expanded="<?= $isOpen ? 'true' : 'false' ?>">
                <i aria-hidden="true" class="fa fa-bars"></i>
                <span>Modules</span>
                <i aria-hidden="true" class="fas fa-chevron-down arrow"></i>
            </button>
            <div class="nav-child" id="sidebarModules">
                <?php if ($navHrisHome): ?>
                <a class="<?= $navIsSection('/hris') ? 'active' : '' ?>" href="/hris"<?= $navCurrentAttribute('/hris') ?>>HRIS</a>
                <?php elseif ($navHris): ?>
                <div role="group" aria-label="HRIS">
                    <span class="sidebar-module-label">HRIS</span>
                    <?php if ($navHrisEmployees): ?>
                    <a class="sidebar-module-link <?= $navIsSection('/hris/employees') ? 'active' : '' ?>" href="/hris/employees"<?= $navCurrentAttribute('/hris/employees') ?>>Employees</a>
                    <?php endif; ?>
                    <a class="sidebar-module-link <?= $navIsCurrent('/hris/dtr') ? 'active' : '' ?>" href="/hris/dtr"<?= $navCurrentAttribute('/hris/dtr') ?>>DTR</a>
                </div>
                <?php endif; ?>

                <?php if ($navPayroll): ?>
                <div role="group" aria-label="Payroll">
                    <?php if ($navPayrollHome): ?>
                    <a class="<?= $navIsSection('/payroll') ? 'active' : '' ?>" href="/payroll"<?= $navCurrentAttribute('/payroll') ?>>Payroll</a>
                    <?php else: ?>
                    <span class="sidebar-module-label">Payroll</span>
                    <a class="sidebar-module-link <?= $navIsSection('/payroll/allowances') ? 'active' : '' ?>" href="/payroll/allowances"<?= $navCurrentAttribute('/payroll/allowances') ?>>Payroll Items</a>
                    <?php endif; ?>
                    <a class="sidebar-module-link <?= $navIsSection('/payroll/runs') ? 'active' : '' ?>" href="/payroll/runs"<?= $navCurrentAttribute('/payroll/runs') ?>>Payroll Runs</a>
                    <a class="sidebar-module-link <?= $navIsSection('/payroll/attendance/import') ? 'active' : '' ?>" href="/payroll/attendance/import"<?= $navCurrentAttribute('/payroll/attendance/import') ?>>Attendance Import</a>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </nav>
</div>
