<?php
$homeAvailable = ($dashboard['available'] ?? false) === true
    && isset($dashboard['summary']['total_employees'], $dashboard['summary']['active_employees'], $dashboard['summary']['pending_leaves'], $dashboard['summary']['branches']);
$homeSummary = $dashboard['summary'] ?? [];
?>
<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="hris-home-page hris-consistency-page">
    <header class="hris-home-header home-dashboard-context">
        <p class="hris-home-eyebrow d-print-none">HRIS · Overview</p>
        <h1>HRIS Overview</h1>
        <p>Workforce information, HR activities, and quick access to employee management.</p>
    </header>

    <section class="hris-home-panel" aria-labelledby="hrisHomeSummaryHeading" aria-describedby="hrisHomeSummaryHelp">
        <div class="hris-home-section-header">
            <h2 id="hrisHomeSummaryHeading">Workforce Summary</h2>
            <span class="hris-home-availability"><?= $homeAvailable ? 'Current organization · All dates' : 'Summary unavailable'; ?></span>
        </div>
        <p id="hrisHomeSummaryHelp" class="hris-home-muted" role="<?= $homeAvailable ? 'status' : 'alert'; ?>"><?php if ($homeAvailable): ?>Organization-wide totals. Active counts employees marked Active in Employee records; employee and branch totals exclude deleted records. Updated <?= e($dashboard['as_of']); ?> (Manila).<?php else: ?><?= e($dashboard['notice'] ?? 'Summary could not be loaded. Refresh this page to retry.'); ?><?php endif; ?></p>
        <ul class="hris-home-summary">
            <?php foreach ([['total_employees', 'Total Employees', 'bi-people'], ['active_employees', 'Employees marked Active', 'bi-person-check'], ['pending_leaves', 'Pending Leave Requests', 'bi-calendar2-week'], ['branches', 'Branches', 'bi-buildings']] as [$key, $label, $icon]): ?>
            <li><i class="bi <?= e($icon); ?>" aria-hidden="true"></i><span class="home-metric-copy"><span><?= e($label); ?></span><strong><?= $homeAvailable ? number_format($homeSummary[$key]) : 'Unavailable'; ?></strong></span></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <?php $visualModule = 'hris'; include __DIR__ . '/../partials/dashboard_visuals.php'; ?>

    <section class="hris-home-panel" aria-labelledby="hrisHomeAttentionHeading">
        <div class="hris-home-section-header"><h2 id="hrisHomeAttentionHeading">Needs Attention</h2></div>
        <p class="hris-home-muted">Pending leave requests in the current organization.</p>
        <div class="hris-home-follow-up">
            <span class="hris-home-icon" aria-hidden="true"><i class="bi bi-journal-text"></i></span>
            <div class="hris-home-follow-up-copy">
                <h3><?= !$homeAvailable ? 'Leave summary unavailable' : ($homeSummary['pending_leaves'] > 0 ? number_format($homeSummary['pending_leaves']) . ' pending leave ' . ($homeSummary['pending_leaves'] === 1 ? 'request' : 'requests') : 'No pending leave requests'); ?></h3>
                <p><?= !$homeAvailable ? 'Refresh this page to retry, or review requests in Leave Management.' : ($homeSummary['pending_leaves'] > 0 ? 'Review the requests and their current status in Leave Management.' : 'No requests are currently marked Pending. Other HR activities are managed in their respective sections.'); ?></p>
            </div>
            <?php if ($navHrisHome): ?>
            <a class="btn btn-outline-primary hris-home-review-link" href="/hris/leaves">Open Leave Management <i class="bi bi-arrow-right" aria-hidden="true"></i></a>
            <?php endif; ?>
        </div>
    </section>

    <section aria-labelledby="hrisHomeAccessHeading">
        <div class="hris-home-section-header">
            <h2 id="hrisHomeAccessHeading">Quick Access</h2>
        </div>
        <p class="hris-home-muted">Go directly to your workforce tools.</p>
        <div class="hris-home-shortcuts">
            <?php if ($navHrisEmployees): ?>
            <a class="hris-home-shortcut" href="/hris/employees">
                <span class="hris-home-icon" aria-hidden="true"><i class="bi bi-people"></i></span>
                <div><h3>Employees</h3><p>View and manage employee records.</p></div>
                <i class="bi bi-arrow-right hris-home-arrow" aria-hidden="true"></i>
            </a>
            <?php endif; ?>
            <?php if ($navHrisHome): ?>
            <a class="hris-home-shortcut" href="/hris/leaves">
                <span class="hris-home-icon" aria-hidden="true"><i class="bi bi-calendar2-week"></i></span>
                <div><h3>Leave Management</h3><p>Review leave requests and leave settings.</p></div>
                <i class="bi bi-arrow-right hris-home-arrow" aria-hidden="true"></i>
            </a>
            <?php endif; ?>
            <?php if ($navHris): ?>
            <a class="hris-home-shortcut" href="/hris/dtr">
                <span class="hris-home-icon" aria-hidden="true"><i class="bi bi-clock-history"></i></span>
                <div><h3>Daily Time Record</h3><p>Review employee attendance and time records.</p></div>
                <i class="bi bi-arrow-right hris-home-arrow" aria-hidden="true"></i>
            </a>
            <?php endif; ?>
            <?php if ($navHrisHome): ?>
            <a class="hris-home-shortcut" href="/hris/offices">
                <span class="hris-home-icon" aria-hidden="true"><i class="bi bi-diagram-3"></i></span>
                <div><h3>Organization</h3><p>Browse departments, teams, and organizational structure.</p></div>
                <i class="bi bi-arrow-right hris-home-arrow" aria-hidden="true"></i>
            </a>
            <?php endif; ?>
        </div>
        <?php if (!$navHris): ?>
        <p class="hris-home-muted">HRIS shortcuts are available when your account has access to the module.</p>
        <?php endif; ?>
    </section>

    <aside class="hris-home-guidance" aria-labelledby="hrisHomeGuidanceHeading">
        <i class="bi bi-compass" aria-hidden="true"></i>
        <div>
            <h2 id="hrisHomeGuidanceHeading">Manage Your Workforce</h2>
            <p>Employee information, organizational structure, and attendance records are managed in their respective HRIS sections.</p>
        </div>
    </aside>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>
</body>
</html>
