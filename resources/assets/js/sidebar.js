var $ = require('jquery');
require("malihu-custom-scrollbar-plugin");
require("jquery-mousewheel");
require('octicons');


$(document).ready(function () {
    $("#sidebar").mCustomScrollbar({
        theme: "minimal"
    });

    $("#sidebarPlaceholder").html('<button type="button" id="sidebarCollapse" class="btn btn-info navbar-btn"><span><i class="fas fa-user"></i></span></button>');



    //TODO: When the browser is resized, if below 390px, the brand name should wrap instead of the burger button (below)
    //TODO: The width of the navbar should be tweaked
    // $(window).resize(function() {
    //     if (window.width() < 390) {
    //         document.getElementById("theBrandName").innerHTML = "New text!";
    //     }
    // });



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