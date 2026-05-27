<?php

/**
 * Language initialization for k1lib
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib;

if (!defined("k1lib\K1LIB_LANG")) {
    define("k1lib\K1LIB_LANG", "en");
}

/**
 * Include all language files from the specified language directory.
 *
 * @return void
 */
k1lib_include_files(K1LIB_BASE_PATH . "/lang/" . K1LIB_LANG);