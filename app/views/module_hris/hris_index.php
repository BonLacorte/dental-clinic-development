<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; ?>

<div class="hris-home-page">
    <header class="hris-home-header">
        <p class="hris-home-eyebrow">Workforce operations</p>
        <h1>HRIS Overview</h1>
        <p>Workforce information, HR activities, and quick access to employee management.</p>
    </header>

    <section class="hris-home-panel" aria-labelledby="hrisHomeSummaryHeading" aria-describedby="hrisHomeSummaryHelp">
        <div class="hris-home-section-header">
            <h2 id="hrisHomeSummaryHeading">Workforce Summary</h2>
            <span class="hris-home-availability"><i class="bi bi-info-circle" aria-hidden="true"></i> Summary unavailable</span>
        </div>
        <p id="hrisHomeSummaryHelp" class="hris-home-muted">Workforce totals are not available on this overview yet. Review current records in the HRIS sections below.</p>
        <ul class="hris-home-summary">
            <li><i class="bi bi-people" aria-hidden="true"></i><span>Total Employees</span></li>
            <li><i class="bi bi-person-check" aria-hidden="true"></i><span>Active Employees</span></li>
            <li><i class="bi bi-calendar2-week" aria-hidden="true"></i><span>Pending Leave Requests</span></li>
            <li><i class="bi bi-buildings" aria-hidden="true"></i><span>Branches</span></li>
        </ul>
    </section>

    <section class="hris-home-panel" aria-labelledby="hrisHomeAttentionHeading">
        <div class="hris-home-section-header">
            <h2 id="hrisHomeAttentionHeading">Needs Attention</h2>
        </div>
        <p class="hris-home-muted">Review HR activities that may require follow-up.</p>
        <div class="hris-home-follow-up">
            <span class="hris-home-icon" aria-hidden="true"><i class="bi bi-journal-text"></i></span>
            <div class="hris-home-follow-up-copy">
                <h3>Review current HR activities</h3>
                <p>HR activity summaries are not available here yet.<?php if ($navHrisHome): ?> Open Leave Management to review current requests.<?php endif; ?></p>
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
