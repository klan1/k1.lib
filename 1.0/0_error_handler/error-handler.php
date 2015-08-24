<?php

/**
 * TODO: Make this... 
 */
function k1_deprecated($func_name) {
    trigger_error("Function '" . $func_name . "' do not exist more, please use the new class instead", E_USER_ERROR);
}
