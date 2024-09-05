<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * K1 Lib loader
 *
 * PHP version 7.4
 *
 * LICENSE:  
 *
 * @author          Alejandro Trujillo J. <alejo@klan1.com>
 * @copyright       2015-2023 Klan1 Network SAS
 * @license         Apache 2.0
 * @version         2.0
 * @since           File available since Release 0.1
 */
/*
 * App run time vars
 */

namespace k1lib;

const IN_K1LIB = TRUE;
const VERSION = "2.0";

// Peace for user, info for the developer with ZendZerver and Z-Ray Live!
error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

/*
 * PATH AUTO CONFIG
 */
define("K1LIB_BASE_PATH", dirname(__FILE__));

header("PHP-Framework: K1.lib V" . VERSION);
header("Developed-by: Klan1 Network | http://www.klan1.com");

/**
 * This MUST to be reflected on the .htaccess, IF NOT rewriting WONT work!
 */
const URL_REWRITE_VAR_NAME = "K1LIB_URL";

define("HTML_TEMPLATES_PATH", K1LIB_BASE_PATH . "/html-templates");

const HTML_TEMPLATES_PATH = \HTML_TEMPLATES_PATH;
const MAGIC_VALUE = "9d5042fd5925dfc995b7958a84a24ead";


//k1lib_include_files(K1LIB_BASE_PATH);



namespace k1lib\html;

const IS_SELF_CLOSED = TRUE;
const IS_NOT_SELF_CLOSED = FALSE;
const NO_CLASS = NULL;
const NO_ID = NULL;
const NO_VALUE = NULL;
const APPEND_ON_HEAD = 1;
const APPEND_ON_MAIN = 2;
const APPEND_ON_TAIL = 3;
const INSERT_ON_PRE_TAG = -1;
const INSERT_ON_AFTER_TAG_OPEN = 2;
const INSERT_ON_VALUE = 0;
const INSERT_ON_BEFORE_TAG_CLOSE = 3;
const INSERT_ON_POST_TAG = 1;

