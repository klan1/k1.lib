<?php

/**
 * URL rewrite and link handling functions
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib\urlrewrite;

/**
 * Get the back URL from various sources.
 *
 * @param bool $get_only If TRUE, only check GET parameter
 * @return string|false
 */
function get_back_url($get_only = FALSE) {
    if (isset($_GET['back-url'])) {
        $back_url = urldecode($_GET['back-url']);
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

/**
 * Print an HTML link.
 *
 * @param string $link URL
 * @param string $text Link text
 * @param string $class CSS class
 * @param string $extra Extra attributes
 * @return void
 */
function print_link($link, $text, $class = "", $extra = "") {
    echo "<a href='$link' class='$class' $extra>$text</a>";
}

/**
 * Get an HTML link as string.
 *
 * @param string $link URL
 * @param string $text Link text
 * @param string $class CSS class
 * @param string $extra Extra attributes
 * @return string
 */
function get_link($link, $text, $class = "", $extra = "") {
    return "<a href='$link' class='$class' $extra>$text</a>";
}