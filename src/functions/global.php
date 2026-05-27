<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage global
 * Global Functions - Common utility functions for debugging and output.
 */

use k1lib\html\DOM;
use k1lib\notifications\on_DOM;
use k1lib\html\pre;

/**
 * Debug function for displaying variables on screen.
 * 
 * Outputs the given variable in a formatted style for debugging purposes.
 * Uses pre tags when DOM is available, otherwise outputs plain text.
 * 
 * @param mixed $var Variable to debug - can be any type
 * @param bool $var_dump If TRUE, uses var_export() instead of print_r()
 * @param bool $trigger_notice If TRUE, triggers a user notice with the debug output
 * @return void
 */
function d($var, $var_dump = FALSE, $trigger_notice = TRUE) {
    //    trigger_error(__FILE__, E_USER_ERROR);
    $msg = $var_dump ? var_export($var, TRUE) : print_r($var, TRUE);
    if ($trigger_notice) {
        trigger_error($msg, E_USER_NOTICE);
    }
    if (class_exists('\k1lib\html\DOM')) {
        $pre = new pre($msg);
        if (DOM::is_started()) {
            if (!empty(DOM::html()->body()->get_element_by_id("k1lib-output"))) {
                on_DOM::queue_title('Message from K1.lib', 'warning');
                on_DOM::queue_mesasage($pre->generate(), 'warning');
            } else {
                echo $pre->generate();
            }
        } else {
            echo $msg . "\n";
        }
    } else {
        echo $msg . "\n";
    }
}
