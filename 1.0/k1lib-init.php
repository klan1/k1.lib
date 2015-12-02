<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * K1 Lib loader
 *
 * PHP version 5.6
 *
 * LICENSE:  
 *
 * @author            Alejandro Trujillo J. <alejo@klan1.com>
 * @copyright       2015 Klan1 Network
 * @license           Apache 2.0
 * @version          1.0 beta 0
 * @since              File available since Release 0.1
 */
/*
 * App run time vars
 */

namespace k1lib;

const IN_K1LIB = TRUE;

header('Content-Type: text/html; charset=utf-8');

/*
 * GENERAL VARIABLE DECLARATIONS
 */

/*
 * PATH AUTO CONFIG
 */
$k1lib_local_dir = dirname(__FILE__);
//$k1lib_directory_name = basename($k1lib_local_dir);

define("K1LIB_BASE_PATH", $k1lib_local_dir);
unset($k1lib_local_dir);

const VERSION = "1.0";

/**
 * This MUST to be reflected on the .htaccess, IF NOT rewriting WONT work!
 */
const URL_REWRITE_VAR_NAME = "K1LIB_URL";
const BASE_PATH = \K1LIB_BASE_PATH;
define("HTML_TEMPLATES_PATH" , BASE_PATH . "/html-templates");
const HTML_TEMPLATES_PATH = \HTML_TEMPLATES_PATH;

const MAGIC_VALUE = "9d5042fd5925dfc995b7958a84a24ead";

$files_lv1 = scandir(BASE_PATH);
/**
 * Includes at last 2 sub directories levels on actual K1.lib directory.
 */
foreach ($files_lv1 as $file_lv1) {
    if ((substr($file_lv1, 0, 1) == '.') || (substr($file_lv1, 0, 2) == '__') || ($file_lv1 == 'index.php')) {
        continue;
    }
    $file_lv1 = BASE_PATH . "/" . $file_lv1;
    if (is_file($file_lv1) && (substr($file_lv1, -4) == ".php")) {
        require_once $file_lv1;
    } elseif (is_dir($file_lv1)) {
        $files_lv2 = scandir($file_lv1);
        foreach ($files_lv2 as $file_lv2) {
            if (($file_lv2 == '.') || ($file_lv2 == '..')) {
                continue;
            }
            $file_lv2 = $file_lv1 . "/" . $file_lv2;

            if (is_file($file_lv2) && (substr($file_lv2, -4) == ".php")) {

                require_once $file_lv2;
            } elseif (is_dir($file_lv2)) {
                $files_lv3 = scandir($file_lv2);
                foreach ($files_lv3 as $file_lv3) {
                    if (($file_lv3 == '.') || ($file_lv3 == '..')) {
                        continue;
                    }
                    $file_lv3 = $file_lv2 . "/" . $file_lv3;
                    if (is_file($file_lv3) && (substr($file_lv3, -4) == ".php")) {
                        require_once $file_lv3;
                    }
                }
            }
        }
    }
}
unset($files_lv1);
unset($files_lv2);
unset($files_lv3);
unset($file_lv1);
unset($file_lv2);
unset($file_lv3);

//set_include_path((\k1lib\BASE_PATH . '/_loaders') . PATH_SEPARATOR . get_include_path());

    /*
     * FACEBOOK SDK
     */

//    if (!defined("FBSDK_VER")) {
//        define("FBSDK_VER", "3.0.1");
//    }
//    set_include_path("/fb-sdk/" . PATH_SEPARATOR . get_include_path());
//    set_include_path("/fb-sdk/" . FBSDK_VER . PATH_SEPARATOR . get_include_path());
//define("K1LIB_INCLUDES_URL",  "http://" . $_SERVER['SERVER_NAME'] . "/" . $k1lib_directory_name);
//    if (USE_FB) {
//        require_once $k1lib_local_dir . "/fb-sdk/" . FBSDK_VER . "/facebook.php";
//        //require_once $k1lib_local_dir . "/fb-sdk/k1-facebook-cached.php";
//    }