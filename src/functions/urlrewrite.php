<?php

namespace k1lib\urlrewrite;

function get_back_url($get_only = FALSE) {
    // TODO: This is kind of dangerous :( take care!
    if (isset($_GET['back-url'])) {
        $back_url = urldecode($_GET['back-url']);
//        $back_url = \k1lib\forms\check_single_incomming_var($_GET['back-url']);
    } elseif (!$get_only && isset($_SERVER['HTTP_REFERER']) && (!empty($_SERVER['HTTP_REFERER']))) {
        $back_url = $_SERVER['HTTP_REFERER'];
    } elseif (!$get_only && isset($_SESSION['K1APP_LAST_URL']) && (!empty($_SESSION['K1APP_LAST_URL']))) {
        $back_url = $_SESSION['K1APP_LAST_URL'];
    } elseif (!$get_only) {
        $back_url = "javascript:history.back();";
    } else {
        $back_url = FALSE;
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
