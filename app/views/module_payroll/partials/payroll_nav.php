<?php
$current = parse_url($_SERVER['REQUEST_URI'] ?? '/payroll', PHP_URL_PATH);
$payrollItems = [
    '/payroll/allowances' => 'Allowances',
    '/payroll/cash-advances' => 'Cash Advances',
    '/payroll/commissions' => 'Commissions',
    '/payroll/compensations' => 'Compensations',
    '/payroll/deductions' => 'Deductions',
    '/payroll/incentives' => 'Incentives'
];
$isPayrollItem = array_key_exists($current, $payrollItems);
?>
<div>
    <ul class="nav nav-underline">
        <li class="nav-item">
            <a class="nav-link <?= ($current === '/payroll') ? 'active' : ''; ?>" aria-current="page" href="/payroll">Home</a>
        </li>
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle <?= $isPayrollItem ? 'active' : ''; ?>" data-bs-toggle="dropdown" href="#" role="button" aria-expanded="false">Payroll Items</a>
            <ul class="dropdown-menu">
                <?php foreach ($payrollItems as $href => $label): ?>
                    <li><a class="dropdown-item <?= ($current === $href) ? 'active' : ''; ?>" href="<?= e($href); ?>"><?= e($label); ?></a></li>
                <?php endforeach; ?>
            </ul>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= ($current === '/payroll/schedules') ? 'active' : ''; ?>" href="/payroll/schedules">Schedules</a>
        </li>
        <li class="nav-item">
            <a class="nav-link disabled" href="/payroll/reports" tabindex="-1" aria-disabled="true">Reports</a>
        </li>
    </ul>
</div>