<?php
$homeAvailable = ($dashboard['available'] ?? false) === true
    && isset($dashboard['summary']['total_runs'], $dashboard['summary']['draft_runs'], $dashboard['summary']['processed_runs'], $dashboard['summary']['finalized_runs'], $dashboard['recent'], $dashboard['attention']['computed_draft_runs'], $dashboard['attention']['error_results']);
$homeSummary = $dashboard['summary'] ?? [];
$homeRecent = $dashboard['recent'] ?? [];
$homeAttention = $dashboard['attention'] ?? [];
$homeStatusLabels = ['DRAFT' => 'Draft', 'PROCESSING' => 'Processing', 'PROCESSED' => 'Processed', 'FINALIZED' => 'Finalized', 'CANCELLED' => 'Cancelled'];
$homeStatusClasses = ['DRAFT' => 'text-bg-secondary', 'PROCESSING' => 'text-bg-warning', 'PROCESSED' => 'text-bg-info', 'FINALIZED' => 'text-bg-success', 'CANCELLED' => 'text-bg-danger'];
$homePayDate = static function ($value): string {
    $raw = (string) ($value ?? '');
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $raw);
    return $date && $date->format('Y-m-d') === $raw ? $date->format('M d, Y') : ($raw !== '' ? $raw : '-');
};
?>
<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_payroll/partials/payroll_nav.php'; ?>

<div class="payroll-home-page">
    <header class="payroll-home-header home-dashboard-context">
        <p class="home-dashboard-eyebrow d-print-none">Payroll · Overview</p>
        <h1>Payroll Overview</h1>
        <p>Monitor payroll activity, review recent runs, and access payroll tools.</p>
    </header>

    <section class="payroll-home-summary" aria-labelledby="payroll-home-summary-title">
        <div class="payroll-home-section-heading">
            <h2 id="payroll-home-summary-title">Payroll Summary</h2>
            <span class="payroll-home-info-label"><i class="bi bi-info-circle" aria-hidden="true"></i> <?= $homeAvailable ? 'Current organization · All dates' : 'Summary unavailable'; ?></span>
        </div>
        <p class="payroll-home-description" id="payroll-home-summary-note" role="<?= $homeAvailable ? 'status' : 'alert'; ?>"><?php if ($homeAvailable): ?>Counts use saved run statuses, not employee calculation status. Total includes all statuses. Updated <?= e($dashboard['as_of']); ?> (Manila).<?php else: ?><?= e($dashboard['notice'] ?? 'Summary could not be loaded. Refresh this page to retry.'); ?><?php endif; ?></p>
        <dl class="payroll-home-summary-grid" aria-describedby="payroll-home-summary-note">
            <div class="payroll-home-summary-card">
                <dt><span>Total Payroll Runs</span><i class="bi bi-collection" aria-hidden="true"></i></dt>
                <dd><?= $homeAvailable ? number_format($homeSummary['total_runs']) : 'Unavailable'; ?></dd>
            </div>
            <div class="payroll-home-summary-card">
                <dt><span>Draft Runs</span><i class="bi bi-pencil-square" aria-hidden="true"></i></dt>
                <dd><?= $homeAvailable ? number_format($homeSummary['draft_runs']) : 'Unavailable'; ?></dd>
            </div>
            <div class="payroll-home-summary-card">
                <dt><span>Processed Runs</span><i class="bi bi-check2-circle" aria-hidden="true"></i></dt>
                <dd><?= $homeAvailable ? number_format($homeSummary['processed_runs']) : 'Unavailable'; ?></dd>
            </div>
            <div class="payroll-home-summary-card">
                <dt><span>Finalized Runs</span><i class="bi bi-lock" aria-hidden="true"></i></dt>
                <dd><?= $homeAvailable ? number_format($homeSummary['finalized_runs']) : 'Unavailable'; ?></dd>
            </div>
        </dl>
    </section>

    <?php $visualModule = 'payroll'; include __DIR__ . '/../partials/dashboard_visuals.php'; ?>

    <div class="payroll-home-activity-grid">
        <section class="payroll-home-panel" aria-labelledby="payroll-home-recent-title">
            <div class="payroll-home-panel-heading">
                <div>
                    <h2 id="payroll-home-recent-title">Recent Payroll Runs</h2>
                    <p>Latest five runs by pay date. Status is the saved batch status.</p>
                </div>
                <?php if ($navPayroll): ?>
                <a class="payroll-home-link" href="/payroll/runs">View All Payroll Runs<i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                <?php endif; ?>
            </div>
            <p class="payroll-home-scroll-hint" id="payroll-home-scroll-hint">On narrow screens, scroll the table horizontally to see all columns.</p>
            <div class="payroll-home-table-scroll" role="region" aria-labelledby="payroll-home-recent-title" aria-describedby="payroll-home-scroll-hint" tabindex="0">
                <table class="table payroll-home-table">
                    <caption class="visually-hidden">Recent payroll runs by pay date, with saved run status and loaded employee counts.</caption>
                    <thead>
                        <tr>
                            <th scope="col">Payroll Run</th>
                            <th scope="col">Pay Date</th>
                            <th scope="col" class="payroll-home-number">Employees</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($homeAvailable && $homeRecent): ?>
                            <?php foreach ($homeRecent as $recentRun): ?>
                            <tr>
                                <th scope="row"><?= e($recentRun['run_code']); ?></th>
                                <td><?= e($homePayDate($recentRun['pay_date'])); ?></td>
                                <td class="payroll-home-number"><?= number_format((int) $recentRun['employee_count']); ?></td>
                                <td><span class="badge <?= e($homeStatusClasses[$recentRun['status']] ?? 'text-bg-secondary'); ?>"><?= e($homeStatusLabels[$recentRun['status']] ?? $recentRun['status']); ?></span></td>
                                <td><?php if ($navPayroll): ?><a class="payroll-home-link" href="/payroll/runs/view/<?= (int) $recentRun['id']; ?>" aria-label="View payroll run <?= e($recentRun['run_code']); ?>">View Run</a><?php endif; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <tr><td colspan="5"><div class="payroll-home-unavailable">
                            <i class="bi bi-journal-text payroll-home-state-icon" aria-hidden="true"></i>
                            <p class="payroll-home-state-title"><?= $homeAvailable ? 'No payroll runs yet' : 'Recent runs unavailable'; ?></p>
                            <p><?= $homeAvailable ? 'No payroll runs are saved for the current organization.' : 'Refresh this page to retry, or open Payroll Runs to review records.'; ?></p>
                        </div></td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="payroll-home-panel payroll-home-attention" aria-labelledby="payroll-home-attention-title">
            <div class="payroll-home-panel-heading">
                <div>
                    <h2 id="payroll-home-attention-title">Needs Attention</h2>
                    <p>Review payroll work that may require follow-up.</p>
                </div>
            </div>
            <div class="payroll-home-attention-body">
                <?php if ($homeAvailable): ?>
                <dl class="home-attention-counts">
                    <div><dt>Draft runs with computed employee results</dt><dd><?= number_format($homeAttention['computed_draft_runs']); ?></dd></div>
                    <div><dt>Employee results marked Error</dt><dd><?= number_format($homeAttention['error_results']); ?></dd></div>
                </dl>
                <?php if ($homeAttention['computed_draft_runs'] > 0): ?>
                <p class="home-status-notice">Some Draft runs already contain computed results. Review them before processing again; this overview does not change their saved status.</p>
                <?php endif; ?>
                <p>Run status and employee calculation status are separate. Error counts cover saved employee results across all run statuses.</p>
                <?php else: ?>
                <p class="payroll-home-state-title">Follow-up information unavailable</p>
                <p>Refresh this page to retry, or review existing runs.</p>
                <?php endif; ?>
                <?php if ($navPayroll): ?>
                <a class="payroll-home-link" href="/payroll/runs">Review Payroll Runs<i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <section class="payroll-home-quick-access" aria-labelledby="payroll-home-quick-title">
        <div class="payroll-home-section-heading">
            <h2 id="payroll-home-quick-title">Quick Access</h2>
        </div>
        <?php if ($navPayroll): ?>
        <nav class="payroll-home-shortcuts" aria-label="Payroll quick access">
            <a class="payroll-home-shortcut" href="/payroll/allowances">
                <i class="bi bi-list-check payroll-home-shortcut-icon" aria-hidden="true"></i>
                <span><span class="payroll-home-shortcut-title">Payroll Items</span><span class="payroll-home-shortcut-description">Manage employee allowances, deductions, commissions and other payroll items.</span></span>
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
            <a class="payroll-home-shortcut" href="/payroll/runs">
                <i class="bi bi-calendar2-week payroll-home-shortcut-icon" aria-hidden="true"></i>
                <span><span class="payroll-home-shortcut-title">Payroll Runs</span><span class="payroll-home-shortcut-description">Review payroll periods and manage existing payroll runs.</span></span>
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
            <a class="payroll-home-shortcut" href="/payroll/attendance/import">
                <i class="bi bi-upload payroll-home-shortcut-icon" aria-hidden="true"></i>
                <span><span class="payroll-home-shortcut-title">Attendance Import</span><span class="payroll-home-shortcut-description">Preview and import biometric attendance logs.</span></span>
                <i class="bi bi-arrow-right" aria-hidden="true"></i>
            </a>
        </nav>
        <?php else: ?>
        <p class="payroll-home-access-note">Payroll tools are unavailable for your current access.</p>
        <?php endif; ?>
    </section>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>
</body>
</html>
