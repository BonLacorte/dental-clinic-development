// Show the loader (global)
function showLoader(text = "Loading, please wait...") {
    $('#page-loader .loader-text').text(text);
    $('#page-loader').removeClass('d-none').fadeIn(200);
}

// Hide the loader (global)
function hideLoader() {
    $('#page-loader').fadeOut(200, function() {
        $(this).addClass('d-none');
    });
}

$(document).ready(function(){
    console.log("jQuery loaded!");

    $('#toggleSidebar').on('click', function () {
        $('#sidebar').toggleClass('collapsed');
        $('#content-wrapper').toggleClass('expanded');
    });

    document.querySelectorAll('.nav-parent').forEach(el => {
        el.addEventListener('click', () => {
            el.parentElement.classList.toggle('open');
        });
    });

});