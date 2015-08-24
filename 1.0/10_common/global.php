<?php

/**
 * Function for debug on screen any kind of data
 * @param mixed $d
 * @param boolean $dump
 * @param boolean $inline 
 */
function d($d, $dump = FALSE, $inline = TRUE) {
//    trigger_error(__FILE__, E_USER_ERROR);
    $msg = ( ($dump) ? var_export($d, TRUE) : print_r($d, TRUE) );
    if (defined("\k1app\APP_MODE") && (\k1app\APP_MODE == "shell")) {
        echo "\n{$msg}\n";
    } else {
        if ($inline) {
            echo "<pre>\n";
            echo $msg;
            echo "\n</pre>\n";
        } else {
            \k1lib\common\show_message("$msg", "DUMP");
        }
    }
}
