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

namespace k1lib;

function d($var, $var_dump = FALSE, $trigger_notice = TRUE) {
//    trigger_error(__FILE__, E_USER_ERROR);
    $msg = ( ($var_dump) ? var_export($var, TRUE) : print_r($var, TRUE) );
    if ($trigger_notice) {
        trigger_error($msg, E_USER_NOTICE);
    }
    if (class_exists('\k1lib\html\DOM')) {
        if (\k1lib\html\DOM::is_started()) {
            $pre = new \k1lib\html\pre($msg);
            if (!empty(\k1lib\html\DOM::html()->body()->get_element_by_id("k1lib-output"))) {
                \k1lib\html\notifications\on_DOM::queue_title('Message from K1.lib', 'warning');
                \k1lib\html\notifications\on_DOM::queue_mesasage($pre->generate(), 'warning');
            } else {
                echo $pre->generate();
            }
        } else {
            echo $msg . "\n";
        }
    } else {
        echo $msg . "\n";
        ;
    }
}
