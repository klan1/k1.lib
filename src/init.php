<?php

/**
 * k1.lib Bootstrap and Initialization
 *
 * Core loader that initializes constants, configuration, error handling,
 * internationalization, and sets up the k1lib environment.
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib;

use k1lib\lang\translator;

const IN_K1LIB = TRUE;
const VERSION = "2.6";
const K1LIB_ROOT = __DIR__;

/*
 * PATH AUTO CONFIG
 */
define("k1lib\K1LIB_BASE_PATH", dirname(__FILE__));
/**
 * This MUST to be reflected on the .htaccess, IF NOT rewriting WONT work!
 */
const URL_REWRITE_VAR_NAME = "K1LIB_URL";
if (!defined("k1lib\K1LIB_LOCALE")) {
    define("k1lib\K1LIB_LOCALE", "es_CO");
}

// Peace for user, info for the developer with ZendZerver and Z-Ray Live!
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);


header("PHP-Framework: K1.lib V" . VERSION);
header("Developed-by: j0hnd03 | http://www.github.com/j0hnd03");

/**
 * internationalization 
 */

$translator = translator::getInstance();

