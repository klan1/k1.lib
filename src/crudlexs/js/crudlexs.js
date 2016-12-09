
$(function () {
    $(".datepicker").datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: "yy-mm-dd",
        showButtonPanel: true,
    });

    $('#search-button').click(function () {
        $('#search-iframe').toggle();
    });

    $('li.active').parent().foundation('toggle', $('ul'));

});

function use_select_row_keys(form_obj, url_to_submit) {
    $('#fk-iframe').toggle();
    form_obj.action = url_to_submit;
    form_obj.target = "fk-iframe";
    form_obj.submit();
}

function use_select_option_to_url_go(select_obj) {
    document.location = select_obj.options[select_obj.selectedIndex].value;
}

function close_search() {
    $('#search-iframe').toggle();
}
function close_fk_iframe() {
    $('#fk-iframe').toggle();
}
