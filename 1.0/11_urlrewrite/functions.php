<?php

namespace k1lib\urlrewrite;

/**
 * This functions depends of .htaccess with this content:

  Options +FollowSymLinks
  RewriteEngine On
  RewriteCond %{SCRIPT_FILENAME} !-d
  RewriteCond %{SCRIPT_FILENAME} !-f
  RewriteRule ^(.*) index.php?url=$1 [QSA,L]

 */
/**
 * Test and make pre-requisites 
 */
/**
 * @param int This storage the actual level for the URL rewrite system 
 */
//$rewrite_level = 0;
//$url_data = array();
//$this_url = "";

/**
 * Set the URL level name for the app 
 * The $url_data array will hold the data in this way:
 * $url_data[$level]['name']
 * $url_data[$level]['value']
 * @param int $level Level deep to define
 * @param string $name Level name
 * @param boolean $required Required? is TRUE the app will stop is the leves is not pressent on the APP_URL
 * @return boolean 
 * TODO: check level prerequisites 
 */
function set_url_rewrite_var($level, $name, $required = TRUE) {
    k1_deprecated(__FUNCTION__);
}

function get_url_level_count() {
    k1_deprecated(__FUNCTION__);
}

/**
 * Returns the index of URL by url_name
 * @global array $url_data this holds all the URL levels data
 * @param int $level Level number for query
 * @return string 
 */
function get_url_level_index_by_name($level_name) {
    k1_deprecated(__FUNCTION__);
}

/**
 * Returns the value of URL by url_name
 * @global array $url_data this holds all the URL levels data
 * @param int $level Level number for query
 * @return string 
 */
function get_url_level_value_by_name($level_name) {
    k1_deprecated(__FUNCTION__);
}

/**
 * Returns the url portion of the level requested
 * @global array $url_data this holds all the URL levels data
 * @param int $level Level number for query
 * @return string 
 */
function get_url_level_value($level = "this") {
    k1_deprecated(__FUNCTION__);
}

/**
 * Returns the url name of the level requested
 * @global array $url_data this holds all the URL levels data
 * @param int $level Level number for query
 * @return string 
 */
function get_url_level_name($level = "this") {
    k1_deprecated(__FUNCTION__);
}

/**
 * Returns the URL string for the max level defined AKA this actual level
 * @global array $url_data
 * @return string URL 
 */
function get_this_url($complete_url = TRUE) {
    k1_deprecated(__FUNCTION__);
}

function get_this_controller_id() {
    k1_deprecated(__FUNCTION__);
}

function set_last_url($url_to_set = "", $exclude = "fb-connect") {
    k1_deprecated(__FUNCTION__);
}

/**
 * Returns the URL until the level received in $level_to_built
 * @global array $url_data
 * @param type $level_to_built
 * @return string 
 */
function make_url_from_rewrite($level_to_built = 'this') {
    k1_deprecated(__FUNCTION__);
}

/** NO
 * Makes asimple link without app format and just with class attribute builtin 
 * @param string $link Link to build
 * @param string $text Text to print on the document
 * @param string $class CSS Class to use
 * @param string $extra OThers tag attributes that you want to add Ej. onclick='NULL'
 */
function print_link($link, $text, $class = "", $extra = "") {
    echo "<a href='$link' class='$class' $extra>$text</a>";
}

/**
 *  NO
 * @param type $link
 * @param type $text
 * @param type $class
 * @param type $extra
 * @return type
 */
function get_link($link, $text, $class = "", $extra = "") {
    return "<a href='$link' class='$class' $extra>$text</a>";
}

/** NO
 * This is an alias of "\k1lib\urlrewrite\classes\url_manager::get_app_link" with the $get_vars_to_keep parameter ready to use from the CONFIG file
 * @param string $url_to_link
 * @param boolean $keep_get_vars
 * @param string $get_vars_to_keep Coma separated value
 * @return string
 */
function get_fb_app_link($url_to_link, $keep_get_vars = TRUE, $get_vars_to_keep = GET_VARS_TO_KEEP) {
    $link = \k1lib\urlrewrite\classes\url_manager::get_app_link($url_to_link, $keep_get_vars, $get_vars_to_keep);
    return str_replace(APP_URL, FB_APP_URL, $link);
}

/**
 * Returns a URL ready for the APP, has the ability of keep the GET var and you can order wich one or all by default
 * @param string $url_to_link
 * @param boolean $keep_get_vars
 * @param type $get_vars_to_keep
 * @return string
 */
function get_app_link($url_to_link, $keep_get_vars = TRUE, $get_vars_to_keep = "") {
    k1_deprecated(__FUNCTION__);
}
