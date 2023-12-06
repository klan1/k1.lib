
$(function () {
    $('#search-button').click(function () {
        $('#search-iframe').toggle();
    });
//    $('.fi-page-search').click(function () {
//        $('#fk-iframe').toggle();
//    });
});

function use_select_row_keys(form_obj, url_to_submit) {
//    $('#fk-iframe').toggle();
    form_obj.action = url_to_submit;
//    form_obj.target = "_self";
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
