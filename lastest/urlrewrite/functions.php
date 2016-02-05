<?php

namespace k1lib\urlrewrite;

function get_back_url() {
    if (isset($_GET['back-url'])) {
        $back_url = $_GET['back-url'];
//        $back_url = \k1lib\forms\check_single_incomming_var($_GET['back-url']);
    } elseif (isset($_SERVER['HTTP_REFERER']) && (!empty($_SERVER['HTTP_REFERER']))) {
        $back_url = $_SERVER['HTTP_REFERER'];
    } elseif (isset($_SESSION['K1APP_LAST_URL']) && (!empty($_SESSION['K1APP_LAST_URL']))) {
        $back_url = $_SESSION['K1APP_LAST_URL'];
    } else {
        $back_url = "javascript:history.back();";
    }
    return $back_url;
}

/** NO
 * Makes asimple link without app format and just with class attribute builtin 
 * @param string $link Link to build
 * @param string $text Text to print on the document
 * @param string $class CSS Class to use
 * @param string $extra OThers tag attributes that you want to add Ej. onclick='NULL'
 */
function print_link($link, $text, $class = "", $extra = "") {
    echo "<a href='$link' class='$class' $extra>$text</a>";
}

/**
 *  NO
 * @param type $link
 * @param type $text
 * @param type $class
 * @param type $extra
 * @return type
 */
function get_link($link, $text, $class = "", $extra = "") {
    return "<a href='$link' class='$class' $extra>$text</a>";
}

/** NO
 * This is an alias of "\k1lib\urlrewrite\url_manager::get_app_link" with the $get_vars_to_keep parameter ready to use from the CONFIG file
 * @param string $url_to_link
 * @param boolean $keep_get_vars
 * @param string $get_vars_to_keep Coma separated value
 * @return string
 */
function get_fb_app_link($url_to_link, $keep_get_vars = TRUE, $get_vars_to_keep = GET_VARS_TO_KEEP) {
    $link = \k1lib\urlrewrite\url_manager::get_app_link($url_to_link, $keep_get_vars, $get_vars_to_keep);
    return str_replace(APP_URL, FB_APP_URL, $link);
}
