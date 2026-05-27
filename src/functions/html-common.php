<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage html
 * 
 * Common HTML utility functions for navigation, redirects, and array formatting.
 */

namespace k1lib\html;

/**
 * Redirects browser using JavaScript history.back().
 */
function js_back() {
    die("<body><script type='text/javascript''>history.back();</script>");
}

/**
 * Redirects browser using HTML Location header.
 *
 * @param string $url Target URL for redirect
 */
function html_header_go($url) {
    ob_clean();
    header("X-K1.LIB-Message: Redirecting...");
    header("Location: {$url}");
    exit;
}

/**
 * Redirects browser using JavaScript.
 *
 * @param string $url Target URL for redirect
 * @param string $root DOM object to use for redirect (default: window)
 */
function js_go($url, $root = "window") {
    ob_clean();
    die("<script type='text/javascript'>{$root}.location.href = '{$url}';</script>");
}

/**
 * Displays a JavaScript alert dialog.
 *
 * @param string $msg Message to display in alert
 */
function js_alert($msg) {
    if (is_string($msg)) {
        echo "\n<script type=\"text/javascript\">\nalert(\"$msg\");</script>";
    }
}

/**
 * Converts an array to an unordered HTML list.
 *
 * @param array $array Array to convert
 * @return string HTML unordered list
 */
function array_to_ul($array) {
    if (!is_array($array)) {
        die(__FUNCTION__ . " need an array to work on \$array");
    }
    $htlm = "<ul>\n";
    foreach ($array as $key => $value) {
        if (!is_numeric($key) && ($key >= 0)) {
            $htlm .= "\t<li><strong>$key:</strong> $value</li>\n";
        } else {
            $htlm .= "\t<li>$value</li>\n";
        }
    }
    $htlm .= "<ul>\n";
    return $htlm;
}
