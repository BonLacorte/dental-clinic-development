<?php include '../app/views/partials/admin-header.php'; ?>
<?php include '../app/views/module_hris/partials/hris_nav.php'; // include on all hris pages 
?>
<div class="hris-home-page hris-consistency-page">
    <header class="hris-home-header">
        <p class="hris-home-eyebrow d-print-none">HRIS · Overview</p>
        <h1>HRIS Overview</h1>
        <p>Workforce information, HR activities, and access to employee management.</p>
    </header>

    <section class="hris-home-panel" aria-labelledby="hrisHomeSummaryHeading" aria-describedby="hrisHomeSummaryHelp">
        <div class="hris-home-section-header">
            <h2 id="hrisHomeSummaryHeading">Workforce Summary</h2>
            <span class="hris-home-availability"><i class="bi bi-info-circle" aria-hidden="true"></i> Summary unavailable</span>
        </div>
        <p id="hrisHomeSummaryHelp" class="hris-home-muted">Workforce totals are not available on this overview yet. Review current records using the HRIS navigation above.</p>
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
                <p>HR activity summaries are not available here yet.</p>
            </div>
        </div>
    </section>

    <section class="hris-home-panel" aria-labelledby="hrisHomeAccessHeading">
        <div class="hris-home-section-header">
            <h2 id="hrisHomeAccessHeading">Quick Access</h2>
        </div>
        <p class="hris-home-muted">Use the HRIS navigation above to open the workforce tools available to your account.</p>
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

<script>

</script>

</body>

</html>