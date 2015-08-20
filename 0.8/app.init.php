<?php

if (!defined("IN_K1APP")) {
    die("haking attemp '^_^");
}
/*
 * App run time vars
 */
$app_init_time = microtime(true);

$db_querys = 0;
$db_query_cached_true = 0;
$db_query_cached_false = 0;
$db_query_cached_total = 0;
$sql_profiles = array();

$fbq_calls = 0;
$fb_api_calls = 0;
$fbq_profiles = array();
$fbapi_profiles = array();

/*
 * GENERAL VARIABLE DECLARATIONS
 */
date_default_timezone_set("America/Bogota");
$date = date("Y-m-d");
$time = date("H:i:s");
$now = date("Y-m-d H:i:s");

/*
 * PATH AUTO CONFIG
 */

$k1lib_local_dir = dirname(__FILE__);
$k1lib_directory_name = basename($k1lib_local_dir);

if (!defined("K1LIB_VER")) {
    define("K1LIB_VER", "0.8");
}
define("K1LIB_PATH", $k1lib_local_dir . "/" . K1LIB_VER);
$k1lib_files = scandir(K1LIB_PATH);

set_include_path(K1LIB_PATH . PATH_SEPARATOR . get_include_path());

foreach ($k1lib_files as $k1lib_file) {
    if (is_file(K1LIB_PATH . "/" . $k1lib_file)) {
        require_once $k1lib_file;
    }
}

/*
 * FACEBOOK SDK
 */

if (!defined("FBSDK_VER")) {
    define("FBSDK_VER", "3.0.1");
}

set_include_path("/fb-sdk/" . PATH_SEPARATOR . get_include_path());
set_include_path("/fb-sdk/" . FBSDK_VER . PATH_SEPARATOR . get_include_path());


//define("K1LIB_INCLUDES_URL",  "http://" . $_SERVER['SERVER_NAME'] . "/" . $k1lib_directory_name);

if (USE_FB) {
    require_once $k1lib_local_dir . "/fb-sdk/" . FBSDK_VER . "/facebook.php";
    //require_once $k1lib_local_dir . "/fb-sdk/k1-facebook-cached.php";
}
?>
