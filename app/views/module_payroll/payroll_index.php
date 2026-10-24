<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_payroll/partials/payroll_nav.php'; ?>

<div class="payroll-home-page">
    <header class="payroll-home-header">
        <h1>Payroll Overview</h1>
        <p>Monitor payroll activity, review recent runs, and access payroll tools.</p>
    </header>

    <?php // Live data is deferred pending backend authorization and branch-scope approval.
    // Navigation uses the same server-rendered predicate as payroll_nav.php.
    ?>
    <section class="payroll-home-summary" aria-labelledby="payroll-home-summary-title">
        <div class="payroll-home-section-heading">
            <h2 id="payroll-home-summary-title">Payroll Summary</h2>
            <span class="payroll-home-info-label"><i class="bi bi-info-circle" aria-hidden="true"></i> Summary unavailable</span>
        </div>
        <p class="payroll-home-description" id="payroll-home-summary-note" role="status">Run counts are not available on this overview. Review Payroll Runs for current records.</p>
        <dl class="payroll-home-summary-grid" aria-describedby="payroll-home-summary-note">
            <div class="payroll-home-summary-card">
                <dt><span>Total Payroll Runs</span><i class="bi bi-collection" aria-hidden="true"></i></dt>
                <dd>Unavailable<span class="payroll-home-metric-context">All payroll run statuses</span></dd>
            </div>
            <div class="payroll-home-summary-card">
                <dt><span>Draft Runs</span><i class="bi bi-pencil-square" aria-hidden="true"></i></dt>
                <dd>Unavailable<span class="payroll-home-metric-context">Stored Draft status</span></dd>
            </div>
            <div class="payroll-home-summary-card">
                <dt><span>Processed Runs</span><i class="bi bi-check2-circle" aria-hidden="true"></i></dt>
                <dd>Unavailable<span class="payroll-home-metric-context">Stored Processed status</span></dd>
            </div>
            <div class="payroll-home-summary-card">
                <dt><span>Finalized Runs</span><i class="bi bi-lock" aria-hidden="true"></i></dt>
                <dd>Unavailable<span class="payroll-home-metric-context">Stored Finalized status</span></dd>
            </div>
        </dl>
    </section>

    <div class="payroll-home-activity-grid">
        <section class="payroll-home-panel" aria-labelledby="payroll-home-recent-title">
            <div class="payroll-home-panel-heading">
                <div>
                    <h2 id="payroll-home-recent-title">Recent Payroll Runs</h2>
                    <p>Review the latest payroll runs and their current status.</p>
                </div>
                <?php if ($navPayroll): ?>
                <a class="payroll-home-link" href="/payroll/runs">View All Payroll Runs<i class="bi bi-arrow-right" aria-hidden="true"></i></a>
                <?php endif; ?>
            </div>
            <p class="payroll-home-scroll-hint" id="payroll-home-scroll-hint">On narrow screens, scroll the table horizontally to see all columns.</p>
            <div class="payroll-home-table-scroll" role="region" aria-labelledby="payroll-home-recent-title" aria-describedby="payroll-home-scroll-hint" tabindex="0">
                <table class="table payroll-home-table">
                    <caption class="visually-hidden">Recent payroll runs. Records are currently unavailable on this overview.</caption>
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
                        <tr>
                            <td colspan="5">
                                <div class="payroll-home-unavailable">
                                    <i class="bi bi-journal-text payroll-home-state-icon" aria-hidden="true"></i>
                                    <p class="payroll-home-state-title">Recent runs unavailable</p>
                                    <p>Recent payroll activity is not available on this overview. Open Payroll Runs to review existing records.</p>
                                </div>
                            </td>
                        </tr>
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
                <i class="bi bi-info-circle payroll-home-state-icon" aria-hidden="true"></i>
                <p class="payroll-home-state-title">Follow-up information unavailable</p>
                <p>Payroll follow-up information is not currently available here. Review existing runs for work that may need your attention.</p>
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
