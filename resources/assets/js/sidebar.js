$(document).ready(function () {
    yaSimpleScrollbar.attach(document.getElementById('sidebar'));

    $("#sidebarPlaceholder").html('<button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn"><i class="fas fa-home"></i></button>');


    $('#dismiss, .overlay').on('click', function () {
        $('#sidebar').removeClass('active');
        $('.overlay').fadeOut();
    });

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').addClass('active');
        $('.overlay').fadeIn();
        $('.collapse.in').toggleClass('in');
        $('a[aria-expanded=true]').attr('aria-expanded', 'false');
    });


});
