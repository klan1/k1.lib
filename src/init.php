<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * K1 Lib loader
 *
 * PHP version 8.2
 *
 * LICENSE:  
 *
 * @author          Alejandro Trujillo J. <alejo@klan1.com>
 * @copyright       2015-2023 Klan1 Network SAS
 * @license         Apache 2.0
 * @version         2.6
 * @since           File available since Release 0.1
 */
/*
 * App run time vars
 */
// TODO: remove this file dependency 

namespace k1lib;

use k1lib\lang\t;

const IN_K1LIB = TRUE;
const VERSION = "2.6";
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

$translator = t::getInstance();

