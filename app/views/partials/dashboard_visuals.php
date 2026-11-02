<?php
// Read-only dashboard presentation. No page script or state-changing controls.
$visualFilters = $visuals['filters'] ?? ['start' => date('Y-m-d'), 'group' => 'department', 'run_id' => null, 'run_q' => '', 'notices' => []];
$visualPanels = $visuals['panels'] ?? [];
$visualAsOf = $visuals['as_of'] ?? '';
$visualAction = $visualModule === 'hris' ? '/hris' : '/payroll';
$visualDate = static function (string $date): string {
    $parsed = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    return $parsed && $parsed->format('Y-m-d') === $date ? $parsed->format('j M Y') : $date;
};
$visualEnd = (new DateTimeImmutable($visualFilters['start']))->modify('+29 days')->format('Y-m-d');
$renderVisual = static function (array $card) use ($visualFilters, $visualAsOf, $visualAction, $visualDate, $visualEnd): void {
    include __DIR__ . '/dashboard_chart.php';
};
$hrisEmployeeLink = !empty($navHrisEmployees) ? '/hris/employees' : null;
$hrisLeaveLink = !empty($navHrisHome) ? '/hris/leaves' : null;
$payrollRunLink = !empty($navPayroll) ? '/payroll/runs' : null;
?>
<section class="dashboard-visuals" aria-labelledby="<?= e($visualModule); ?>-visuals-title">
    <div class="dashboard-visuals-heading">
        <div><h2 id="<?= e($visualModule); ?>-visuals-title"><?= $visualModule === 'hris' ? 'Workforce Insights' : 'Payroll Insights'; ?></h2>
        <p>Current organization · Read-only visuals<?= $visualAsOf !== '' ? ' · Updated ' . e($visualAsOf) . ' (Manila)' : ''; ?>.</p></div>
    </div>
    <?php if ($visualFilters['notices']): ?><div class="dashboard-filter-notice" role="status"><ul><?php foreach ($visualFilters['notices'] as $notice): ?><li><?= e($notice); ?></li><?php endforeach; ?></ul></div><?php endif; ?>
    <div class="dashboard-visual-grid">
        <?php if ($visualModule === 'hris'): ?>
        <?php $renderVisual(['key' => 'branches', 'title' => 'Employees by Branch', 'kind' => 'bars',
            'help' => 'Current branch assignments of all non-deleted employee records, including inactive records. Counts reconcile with Total Employees.',
            'panel' => $visualPanels['branches'] ?? [], 'link' => $hrisEmployeeLink, 'link_label' => 'Open Employees']); ?>
        <?php $renderVisual(['key' => 'leave-calendar', 'title' => 'Upcoming Approved Leave', 'kind' => 'calendar',
            'help' => 'Distinct employees per day with approved leave. Overlapping requests count once per employee; deleted employee records are excluded. Pending requests are not included.',
            'panel' => $visualPanels['leave_calendar'] ?? [], 'link' => $hrisLeaveLink, 'link_label' => 'Open Leave Management']); ?>
        <?php $renderVisual(['key' => 'leave-age', 'title' => 'Pending Leave Request Age', 'kind' => 'bars',
            'help' => 'All dates · Calendar days since each Pending request was created, as of today in Manila. These groups describe waiting time; they are not approval deadlines.',
            'panel' => $visualPanels['leave_age'] ?? [], 'link' => $hrisLeaveLink, 'link_label' => 'Open Leave Management']); ?>
        <?php $renderVisual(['key' => 'composition', 'title' => 'Employees by Department / Position', 'kind' => 'bars',
            'help' => 'Current ' . $visualFilters['group'] . ' assignments of all non-deleted employees, including inactive records. Departments use the existing organizational-unit records.',
            'panel' => $visualPanels['composition'] ?? [], 'link' => $hrisEmployeeLink, 'link_label' => 'Open Employees']); ?>
        <?php $renderVisual(['key' => 'attendance-exceptions', 'title' => 'Attendance Exceptions', 'kind' => 'prerequisite',
            'help' => 'Missing punches, lateness and undertime need validated attendance calculations before they can be charted reliably. Missing logs alone do not establish absence.',
            'link' => !empty($navHris) ? '/hris/dtr' : null, 'link_label' => 'Open Daily Time Record']); ?>
        <?php $renderVisual(['key' => 'workforce-movement', 'title' => 'New Hires and Departures', 'kind' => 'prerequisite',
            'help' => 'Monthly movement charts need verified employment-date history, including departures and archived records. Current employee records are not a complete historical workforce.',
            'link' => $hrisEmployeeLink, 'link_label' => 'Open Employees']); ?>
        <?php else: ?>
        <?php $renderVisual(['key' => 'progress', 'title' => 'Employee Calculation Progress', 'kind' => 'progress',
            'help' => 'Saved employee calculation states within one run. Computed does not mean approved, finalized or ready for payment. The run’s saved status remains separate.',
            'panel' => $visualPanels['progress'] ?? [], 'link' => $payrollRunLink, 'link_label' => 'Open Payroll Runs']); ?>
        <?php $renderVisual(['key' => 'statuses', 'title' => 'Runs by Saved Status', 'kind' => 'bars',
            'help' => 'All saved dates · Exact stored run statuses. Employee calculation results do not change these counts. Unexpected or missing statuses appear under Other / unknown.',
            'panel' => $visualPanels['statuses'] ?? [], 'link' => $payrollRunLink, 'link_label' => 'Open Payroll Runs']); ?>
        <?php $renderVisual(['key' => 'timeline', 'title' => 'Upcoming Cutoffs and Pay Dates', 'kind' => 'timeline',
            'help' => 'Saved cutoff starts, cutoff ends and pay dates, with their run’s saved status. These dates do not establish whether employees have been paid.',
            'panel' => $visualPanels['timeline'] ?? [], 'link' => $payrollRunLink, 'link_label' => 'Open Payroll Runs', 'can_view_run' => !empty($navPayroll)]); ?>
        <?php $renderVisual(['key' => 'gross-trend', 'title' => 'Gross Payroll Trend', 'kind' => 'prerequisite',
            'help' => 'A reliable trend needs validated calculations and an agreed authoritative run for each comparable period, with cancelled or replaced runs handled correctly. No historical points are fabricated.',
            'link' => $payrollRunLink, 'link_label' => 'Open Payroll Runs']); ?>
        <?php $renderVisual(['key' => 'net-breakdown', 'title' => 'Basic-to-Net Breakdown', 'kind' => 'prerequisite',
            'help' => 'The waterfall needs validated basic pay, additional earnings and deductions that reconcile to net pay. Existing computed amounts are not presented as verified financial results.',
            'link' => $payrollRunLink, 'link_label' => 'Open Payroll Runs']); ?>
        <?php $renderVisual(['key' => 'branch-payroll', 'title' => 'Gross Payroll by Branch', 'kind' => 'prerequisite',
            'help' => 'This chart needs validated calculations and historical branch allocation for each payroll run. Current employee branch assignments cannot reliably allocate historical payroll.',
            'link' => $payrollRunLink, 'link_label' => 'Open Payroll Runs']); ?>
        <?php endif; ?>
    </div>
</section>
<?php unset($renderVisual); ?>
