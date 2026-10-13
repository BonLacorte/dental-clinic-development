<?php
/// sample NCFRS about page
$title = "About Page";
include 'partials/admin-header.php';
?>

<!-- Header Banner -->
<div class="bg-primary text-white text-center py-4 rounded-top mb-4">
    <h1 class="mb-1">NCFRS</h1>
    <p class="lead mb-0">National Coconut Farmers Registry System</p>
</div>

<!-- Stats Cards (example placeholders) -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-people-fill fs-1 mb-2"></i>
                <h5>Total Active Users</h5>
                <h3 id="totalUsers">...</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-info h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-graph-up fs-1 mb-2"></i>
                <h5>Active Modules</h5>
                <h3>3</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-warning h-100 shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-clock-history fs-1 mb-2"></i>
                <h5>Future Modules</h5>
                <h3>1</h3>
            </div>
        </div>
    </div>
</div>

<!-- Main Card -->
<div class="card shadow-sm mb-4">
    <div class="card-body">

        <h2 class="mb-3">About NCFRS</h2>
        <p>
            The <strong>National Coconut Farmers Registry System (NCFRS)</strong> is a centralized digital platform 
            designed to efficiently manage, organize, and maintain records of coconut farmers. It ensures that 
            accurate and up-to-date information is available for monitoring, validation, and decision-making.
        </p>

        <hr>

        <div class="bg-light p-3 rounded mb-4">
            <h4>System Overview</h4>
            <p>
                NCFRS is built using a <strong>modular architecture</strong>, allowing each major function to operate 
                independently while remaining fully integrated. Each module is accessible through its own dedicated 
                link, making the system scalable, flexible, and easy to maintain.
            </p>
        </div>

        <h4 class="mb-3">Core Modules</h4>
        <div class="row g-3">
            <!-- Dashboard Module -->
            <div class="col-md-3 mb-3">
                <div class="card h-100 shadow-sm text-center border-success hover-scale">
                    <div class="card-body">
                        <i class="bi bi-speedometer2 fs-2 mb-2 text-success"></i>
                        <h5>Dashboard Module</h5>
                        <p>Overview, statistics, and key performance indicators. Quickly monitor activities.</p>
                        <a href="https://ncfrsdb.pca-webapps.com" class="btn btn-sm btn-outline-success mt-2">Go to Dashboard</a>
                    </div>
                </div>
            </div>

            <!-- New Registration Module -->
            <div class="col-md-3 mb-3">
                <div class="card h-100 shadow-sm text-center border-primary hover-scale">
                    <div class="card-body">
                        <i class="bi bi-person-plus fs-2 mb-2 text-primary"></i>
                        <h5>Registration Module</h5>
                        <p>Manage farmer and farm profiles. Input, update, and verify information accurately.</p>
                        <a href="/home" class="btn btn-sm btn-outline-primary mt-2">Go to Registration</a>
                    </div>
                </div>
            </div>

            <!-- Legacy Registration Module -->
            <div class="col-md-3 mb-3">
                <div class="card h-100 shadow-sm text-center border-secondary hover-scale">
                    <div class="card-body">
                        <i class="bi bi-person-lines-fill fs-2 mb-2 text-secondary"></i>
                        <h5>Legacy Registration Module</h5>
                        <p>This is the older registration system still in use. Users are encouraged to migrate to the new module.</p>
                        <span class="badge bg-secondary mt-2">Legacy</span>
                        <a href="https://ncfrs.pca-webapps.com:8136" class="btn btn-sm btn-outline-secondary mt-2">Go to Legacy</a>
                    </div>
                </div>
            </div>

            <!-- IIMS Module -->
            <div class="col-md-3 mb-3">
                <div class="card h-100 shadow-sm text-center border-warning hover-scale">
                    <div class="card-body">
                        <i class="bi bi-journal-text fs-2 mb-2 text-warning"></i>
                        <h5>IIMS Module</h5>
                        <p>Supports Implementing Agencies (IAs) information management and monitoring processes.</p>
                        <span class="badge bg-warning text-dark mt-2">Coming Soon</span>
                    </div>
                </div>
            </div>
        </div>

        <hr>

        <h4>Key Features</h4>
        <ul>
            <li>Modular and scalable architecture</li>
            <li>Separate links for each module</li>
            <li>Centralized farmer data management</li>
            <li>Efficient data validation and processing</li>
            <li>User-friendly interface</li>
        </ul>

        <hr>

        <h4>Purpose</h4>
        <p>
            The primary goal of NCFRS is to provide a reliable and efficient registry system that supports 
            agricultural programs, policy-making, and fair distribution of resources. By maintaining accurate 
            records, the system ensures that benefits reach the rightful beneficiaries.
        </p>

    </div>
</div>

<!-- Hover effect style -->
<style>
.hover-scale {
    transition: transform 0.2s;
}
.hover-scale:hover {
    transform: scale(1.05);
}
</style>

<?php include 'partials/admin-footer.php'; ?>

<script>
$(document).ready(function() {
    function getTotalUsers() {
        $.ajax({
            url: '/getTotalUsers',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                $('#totalUsers').text(response.total.toLocaleString());
            },
            error: function() {
                $('#totalUsers').text('Error');
            }
        });
    }

    getTotalUsers();

    // Optional: auto-refresh every 30 seconds
    setInterval(loadTotalUsers, 30000);
});
</script>

</body>
</html>