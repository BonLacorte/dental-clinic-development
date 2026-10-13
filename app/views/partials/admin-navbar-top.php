<nav class="navbar navbar-dark bg-dark px-3 fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand ms-2" href="/home">APP Name</a>

        <!-- TOGGLE BUTTON -->
        <button class="btn btn-dark" id="toggleSidebar">☰</button>

        <div class="ms-auto text-white">
            <?= e($data['name']) ?> | <a href="/logout" class="text-danger text-decoration-none">Logout</a>
        </div>

    </div>
</nav>