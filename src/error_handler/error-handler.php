<?php

/**
 * Error handling functions for k1lib
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib;

/**
 * Trigger a deprecation error for removed functions.
 *
 * @param string $func_name Function name that is deprecated
 * @return void
 */
function k1_deprecated($func_name) {
    trigger_error("Function '" . $func_name . "' do not exist more, please use the new class instead", E_USER_ERROR);
}