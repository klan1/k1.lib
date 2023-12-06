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
 * @version         1.9.2
 * @since           File available since Release 0.1
 */
/*
 * App run time vars
 */

namespace k1lib;

const IN_K1LIB = TRUE;
const VERSION = "1.9.2";

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

/**
 * Includes ALL files on a directory.
 */
if (!defined("K1LIB_LANG")) {
    define("K1LIB_LANG", "en");
}
k1lib_include_files(K1LIB_BASE_PATH . "/__lang/" . K1LIB_LANG);
require_once dirname(K1LIB_BASE_PATH) . '/src-globals/globals.php';

if (!defined("K1LIB_INC_MODE")) {
    define("K1LIB_INC_MODE", 0);
}

//k1lib_include_files(K1LIB_BASE_PATH);

/**
 * Use this function to inlude ONLY CLASSES and functions, if there are normal 
 * variables they will be on the function scope and you NEVER will reach them.
 * @param string $path_to_explore
 * @param array $prefix_to_exclude
 */
function k1lib_include_files($path_to_explore, array $prefix_to_exclude = ['.', '..', '__']) {
    $files_list = scandir($path_to_explore);

    foreach ($files_list as $file) {
        if ((substr($file, 0, 1) == '.') || (substr($file, 0, 2) == '__') || ($file == 'index.php')) {
            continue;
        }
        $file_path = $path_to_explore . "/" . $file;

        if (is_file($file_path) && (substr($file_path, -4) == ".php")) {
            require_once $file_path;
        } elseif (is_dir($file_path)) {
            /**
             * GOD BLESS function recursion !!
             */
            k1lib_include_files($file_path, $prefix_to_exclude);
        }
    }
}

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

