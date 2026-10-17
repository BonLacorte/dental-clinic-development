// Show the loader (global)
function showLoader(text = "Loading, please wait...") {
    $('#page-loader .loader-text').text(text);
    $('#page-loader').removeClass('d-none').fadeIn(window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 200);
}

// Hide the loader (global)
function hideLoader() {
    $('#page-loader').fadeOut(window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 0 : 200, function() {
        $(this).addClass('d-none');
    });
}

$(document).ready(function () {
    const sidebar = document.getElementById('sidebar');
    const toggle = document.getElementById('toggleSidebar');
    const navbar = document.querySelector('.admin-navbar');
    if (!document.body.classList.contains('admin-shell') || !sidebar || !toggle || !navbar) return;

    const desktop = window.matchMedia('(min-width: 992px)');
    const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(sidebar, {
        backdrop: true,
        keyboard: true,
        scroll: false
    });
    let desktopCollapsed = false;
    let phase = 'closed';
    let closeRequested = false;

    function measureHeader() {
        document.body.style.setProperty('--admin-header-height', Math.ceil(navbar.getBoundingClientRect().height) + 'px');
    }

    function syncNavigation() {
        const overlayActive = phase !== 'closed';
        const collapsed = desktop.matches && desktopCollapsed && !overlayActive;
        // An in-flight overlay keeps its geometry until Bootstrap has released
        // its backdrop, scroll lock and focus trap in hidden.bs.offcanvas.
        document.body.classList.toggle('sidebar-overlay-active', overlayActive);
        sidebar.classList.toggle('collapsed', collapsed);
        $('#content-wrapper').toggleClass('expanded', collapsed);
        if ((collapsed || (!desktop.matches && !overlayActive)) && sidebar.contains(document.activeElement)) toggle.focus();
        sidebar.inert = collapsed;
        toggle.setAttribute('aria-expanded', String(overlayActive ? phase !== 'closing' : desktop.matches && !desktopCollapsed));
        toggle.disabled = phase === 'closing' || (desktop.matches && overlayActive);
        measureHeader();
    }

    function closeNavigation() {
        if (phase === 'opening') {
            closeRequested = true;
        } else if (phase === 'open') {
            offcanvas.hide();
        }
    }

    // No data-bs-toggle on the trigger: exactly one handler owns each mode.
    $(toggle).on('click', function () {
        if (desktop.matches) {
            desktopCollapsed = !desktopCollapsed;
            syncNavigation();
        } else if (phase === 'closed') {
            offcanvas.show(toggle);
        } else {
            closeNavigation();
        }
    });
    $('#closeSidebar').on('click', closeNavigation);

    // Before shown.bs.offcanvas, focus is still on the external trigger, so
    // Bootstrap's panel key listener cannot receive an early Escape press.
    $(document).on('keydown', function (event) {
        if (phase === 'opening' && event.key === 'Escape' && !event.isDefaultPrevented()) {
            event.preventDefault();
            closeNavigation();
        }
    });

    sidebar.addEventListener('show.bs.offcanvas', function (event) {
        if (desktop.matches) {
            event.preventDefault();
            return;
        }
        phase = 'opening';
        closeRequested = false;
        syncNavigation();
    });
    sidebar.addEventListener('shown.bs.offcanvas', function () {
        phase = 'open';
        syncNavigation();
        if (closeRequested || desktop.matches) closeNavigation();
    });
    sidebar.addEventListener('hide.bs.offcanvas', function (event) {
        // Bootstrap's show/hide transitions must finish in order, including
        // Escape/backdrop dismissal during the opening animation.
        if (phase === 'opening') {
            event.preventDefault();
            closeRequested = true;
            return;
        }
        phase = 'closing';
        syncNavigation();
    });
    sidebar.addEventListener('hidden.bs.offcanvas', function () {
        phase = 'closed';
        closeRequested = false;
        syncNavigation();
        toggle.focus({preventScroll: true});
    });

    desktop.addEventListener('change', function () {
        // Any breakpoint crossing closes the overlay, even if the user crosses
        // back before the opening animation finishes. Desktop choice is local.
        closeNavigation();
        syncNavigation();
    });

    $('#sidebar .nav-parent').on('click', function () {
        const open = $(this).closest('.nav-group').toggleClass('open').hasClass('open');
        this.setAttribute('aria-expanded', String(open));
    });

    $('.admin-skip-link').on('click', function () {
        document.getElementById('content').focus({preventScroll: true});
    });

    $(window).on('resize', measureHeader);
    if (window.ResizeObserver) new ResizeObserver(measureHeader).observe(navbar);
    syncNavigation();
});
