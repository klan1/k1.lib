<?php

namespace k1lib\html;

function js_back() {
    die("<body><script type='text/javascript''>history.back();</script>");
}

function get_back_link($text_link = "Regresar", $target = "_self", $id = "", $class = "", $app_format = FALSE, $keep_vars = TRUE, $vars_to_keep = "") {
    if (isset($_SERVER['HTTP_REFERER']) && (!empty($_SERVER['HTTP_REFERER']))) {
        $back_link = $_SERVER['HTTP_REFERER'];
    } elseif (isset($_SESSION['K1_LAST_URL']) && (!empty($_SESSION['K1_LAST_URL']))) {
        $back_link = $_SESSION['K1_LAST_URL'];
    } else {
        $back_link = "javascript:history.back()";
    }
    $back_link = \k1lib\html\generate_link($back_link, $text_link, $target, $id, $class, $app_format, $keep_vars, $vars_to_keep);

    return $back_link;
}

function back_link($text_link = "< Volver", $return = TRUE, $url = FALSE, $use_div = TRUE, $class = "k1-back-link") {
    if ($url) {
        $back_link = "<a href=\"{$url}\" \">{$text_link}</a>";
    } else {
        $back_link = "<a href=\"#\" onclick=\"history.back()\">{$text_link}</a>";
    }
    if ($use_div) {
        $back_link = "<div class=\"{$class}\">" . $back_link . "</div>";
    }
    if ($return) {
        return $back_link;
    } else {
        echo $back_link;
    }
}

function generate_link($url, $text_link, $target = "_self", $id = "", $class = "", $app_format = TRUE, $keep_vars = TRUE, $vars_to_keep = "") {
    if ($app_format) {
        $url = \k1lib\urlrewrite\classes\url_manager::get_app_link($url, $keep_vars, $vars_to_keep);
    }
    return $back_link = "<a href=\"{$url}\" id=\"{$id}\" class=\"{$class}\" target=\"{$target}\" \">{$text_link}</a>";
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
        $url = \k1lib\urlrewrite\classes\url_manager::get_app_link($url, $keep_vars, $get_vars_to_keep);
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
        $url = \k1lib\urlrewrite\classes\url_manager::get_app_link($url, $keep_vars, $get_vars_to_keep);
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
