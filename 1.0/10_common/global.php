<?php

/**
 * Function for debug on screen any kind of data
 * @param mixed $d
 * @param boolean $dump
 * @param boolean $inline 
 */
function d($d, $dump = false, $inline = true) {
//    trigger_error(__FILE__, E_USER_ERROR);
    $msg = ( ($dump) ? var_export($d, true) : print_r($d, true) );
    if (\k1app\APP_MODE == "shell") {
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
