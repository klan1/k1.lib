<?php

/**
 * Global functions, K1.lib.
 * 
 * Common functions needed on the main \ namespace.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package global
 */

/**
 * Function for debug on screen any kind of data
 * @param mixed $var
 * @param boolean $var_dump
 */
function d($var, $var_dump = FALSE) {
//    trigger_error(__FILE__, E_USER_ERROR);
    $msg = ( ($var_dump) ? var_export($var, TRUE) : print_r($var, TRUE) );
    if (defined("\k1app\APP_MODE") && (\k1app\APP_MODE == "shell")) {
        echo "\n{$msg}\n";
    } else {
        echo "<pre>\n";
        echo $msg;
        echo "\n</pre>\n";
    }
}
