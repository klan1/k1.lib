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
function html_header_go($url, $app_format = TRUE, $keep_vars = TRUE, $get_vars_to_keep = "") {
//    die("Redirecting... [$url]");
    ob_clean();
    if ($app_format) {
        $url = \k1lib\urlrewrite\url_manager::get_app_link($url, $keep_vars, $get_vars_to_keep);
    }
//    trigger_error("No se que pasa!! " . __FUNCTION__, E_USER_ERROR);
//    echo "$file - $line";
    header("X-K1.LIB-Message : Redirecting...");
//    $html = <<<HTML
//<html>
//  <head>
//    <title>X-K1.LIB-Message : Redirecting...</title>
//    <META http-equiv="refresh" content="0;URL={$url}">
//  </head>
//  <body bgcolor="#ffffff"></body>
//</html>
//HTML;
//    die($html);
    header("Location: {$url}");
    exit;
}

/**
 * Send JS code to redirect the browser
 * @param string $url
 * @param string $root The DOM object to redirect
 * @param bolean $app_format 
 */
function js_go($url, $root = "window", $app_format = TRUE, $keep_vars = TRUE, $get_vars_to_keep = "") {
    ob_clean();
//    trigger_error("No se que pasa!! " . __FUNCTION__, E_USER_ERROR);

    if ($app_format) {
        $url = \k1lib\urlrewrite\url_manager::get_app_link($url, $keep_vars, $get_vars_to_keep);
    }
//    echo "$file - $line";
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
