<nav class="navbar navbar-dark bg-dark fixed-top admin-navbar" aria-label="Account and navigation controls">
    <div class="container-fluid admin-navbar-inner">

        <!-- TOGGLE BUTTON -->
        <button class="btn btn-dark" type="button" id="toggleSidebar" aria-label="Toggle navigation" aria-controls="sidebar" aria-expanded="false">
            <span aria-hidden="true">☰</span>
        </button>

        <a class="navbar-brand" href="/home">Montero Dental</a>

        <div class="admin-account text-white">
            <span class="admin-account-name"><?= e($data['name']) ?></span>
            <a href="/logout" class="admin-logout">Logout</a>
        </div>

    </div>
</nav>
