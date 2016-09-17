<?php

namespace k1lib\html;

function js_back() {
    die("<body><script type='text/javascript''>history.back();</script>");
}

/**
 * Send HTML HEAD command to redirect the browser
 * @param string $url
 * @param bolean $app_format 
 */
function html_header_go($url) {
    ob_clean();
    header("X-K1.LIB-Message : Redirecting...");
    header("Location: {$url}");
    exit;
}

/**
 * Send JS code to redirect the browser
 * @param string $url
 * @param string $root The DOM object to redirect
 * @param bolean $app_format 
 */
function js_go($url, $root = "window") {
    ob_clean();
    die("<script type='text/javascript'>{$root}.location.href = '{$url}';</script>");
}

function js_alert($msg) {
    if (is_string($msg)) {
        echo "\n<script type=\"text/javascript\">\nalert(\"$msg\");</script>";
    }
}

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
