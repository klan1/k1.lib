<?php

function d($var, $var_dump = FALSE, $trigger_notice = TRUE) {
//    trigger_error(__FILE__, E_USER_ERROR);
    $msg = ( ($var_dump) ? var_export($var, TRUE) : print_r($var, TRUE) );
    if ($trigger_notice) {
        trigger_error($msg, E_USER_NOTICE);
    }
    if (class_exists('k1lib\html\DOM')) {
        if (k1lib\html\DOM::is_started()) {
            $pre = new \k1lib\html\pre($msg);
            if (!empty(k1lib\html\DOM::html()->body()->get_element_by_id("k1lib-output"))) {
                k1lib\notifications\on_DOM::queue_title('Message from K1.lib', 'warning');
                k1lib\notifications\on_DOM::queue_mesasage($pre->generate(), 'warning');
            } else {
                echo $pre->generate();
            }
        } else {
            echo $msg;
        }
    } else {
        echo $msg;
    }
}
