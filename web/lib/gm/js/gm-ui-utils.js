$(document).ready(function () {
    //###################### Mandatory variables //######################
    var pathname_gm_ui_utils = window.location.pathname;

    //###################### Custom alert handle //######################
    var gm_closebtn = document.getElementsByClassName("gm-closebtn");
    var gm_closebtn_index;

    for (gm_closebtn_index = 0; gm_closebtn_index < gm_closebtn.length; gm_closebtn_index++) {
        gm_closebtn[gm_closebtn_index].onclick = function () {
            var div = this.parentElement;
            div.style.opacity = "0";
            setTimeout(function () { div.style.display = "none"; }, 600);
        }
    }

    //###################### CLickable row handle //######################
    $(".gm-clickable-row").click(function () {
        window.location = $(this).data("href");
    });

    $(".date-datepicker").datepicker({
        pickTime: false,
        dateFormat: 'yy/mm/dd'
    });

    //###################### Current nav item (nav bar) handle //########
    // For dynamic page
    $('.navbar-nav > li > a[href="' + pathname_gm_ui_utils + '"]').parent().addClass('active');
    /* For static page
    $('.navbar-nav a').on('click', function () {
        $('.navbar-nav').find('li.active').removeClass('active');
        $(this).parent('li').addClass('active');
        console.log(this);
    });
    */

    //###################### handle register button (nav bar) handle //##
    $("#registration_at_login_form").on('click', function () {
        window.location.href = $("#registration_at_login_form").data("href");
    });
});