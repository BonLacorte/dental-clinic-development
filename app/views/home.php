<?php include '../app/views/partials/admin-header.php'; ?>

<h2 class="mb-4">Dashboard</h2>

<div class="row">
    <div class="col-md-4">
        <div class="card p-3">
            <h5>Total Users</h5>
            <h3><?= $total_users ?? 0 ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h5>Active Sessions</h5>
            <h3><?= $active_sessions ?? 0 ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3">
            <h5>Uploads</h5>
            <h3><?= $uploads ?? 0 ?></h3>
        </div>
    </div>
</div>

<?php include '../app/views/partials/admin-footer.php'; ?>
</body>
</html>