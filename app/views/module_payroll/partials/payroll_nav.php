<?php
$payrollItems = [
    '/payroll/allowances' => 'Allowances',
    '/payroll/cash-advances' => 'Cash Advances',
    '/payroll/commissions' => 'Commissions',
    '/payroll/compensations' => 'Compensations',
    '/payroll/deductions' => 'Deductions',
    '/payroll/incentives' => 'Incentives',
];
$isPayrollItem = (bool) array_filter(array_keys($payrollItems), $navIsSection);
?>
<nav class="module-navigation" aria-label="Payroll navigation">
    <ul class="nav nav-underline">
        <?php if ($navPayrollHome): ?>
        <li class="nav-item">
            <a class="nav-link <?= $navIsCurrent('/payroll') ? 'active' : '' ?>" href="/payroll"<?= $navCurrentAttribute('/payroll') ?>>Home</a>
        </li>
        <?php endif; ?>
        <?php if ($navPayroll): ?>
        <li class="nav-item dropdown">
            <button class="nav-link dropdown-toggle <?= $isPayrollItem ? 'active' : '' ?>" type="button" data-bs-toggle="dropdown" aria-expanded="false">Payroll Items</button>
            <ul class="dropdown-menu">
                <?php foreach ($payrollItems as $href => $label): ?>
                <li><a class="dropdown-item <?= $navIsCurrent($href) ? 'active' : '' ?>" href="<?= e($href) ?>"<?= $navCurrentAttribute($href) ?>><?= e($label) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $navIsSection('/payroll/runs') ? 'active' : '' ?>" href="/payroll/runs"<?= $navCurrentAttribute('/payroll/runs') ?>>Payroll Runs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= $navIsSection('/payroll/attendance/import') ? 'active' : '' ?>" href="/payroll/attendance/import"<?= $navCurrentAttribute('/payroll/attendance/import') ?>>Attendance Import</a>
        </li>
        <?php endif; ?>
    </ul>
</nav>
