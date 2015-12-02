<?php

namespace k1lib\urlrewrite;

use k1lib_common as k1lib_common;

class url_manager {

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;

    /**
     * Actual URL level 
     * @var Int
     */
    static private $levels_count;

    /**
     * URL data array
     * @var Array
     */
    static private $url_data;

    /**
     * Enable the engenie
     */
    static public function enable() {
        self::$enabled = TRUE;
        self::$levels_count = null;
        self::$url_data = array();
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("URL Rewrite system is not enabled yet", E_USER_ERROR);
        }
        return self::$enabled;
    }

    static public function get_data() {
        return self::$url_data;
    }

    /**
     * Set the URL level name for the app 
     * The self::$url_data array will hold the data in this way:
     * self::$url_data[$level]['name']
     * self::$url_data[$level]['value']
     * @param int $level Level deep to define
     * @param string $name Level name
     * @param boolean $required Required? is TRUE the app will stop is the leves is not pressent on the APP_URL
     * @return boolean 
     * TODO: check level prerequisites 
     */
    static public function set_url_rewrite_var($level, $name, $required = TRUE) {
        self::is_enabled(true);
        if (!empty($_GET[\k1lib\URL_REWRITE_VAR_NAME])) {

            // checks if the level variable is INT
            if (!is_int($level) || ($level < 0)) {
                k1lib_common\show_error("The level for URL REWRITE have to be numeric", __FUNCTION__, TRUE);
            } elseif ($level == 0) { // if is the frist leves it must to be required
                $required = TRUE;
            }
            // the name var has to have a value
            if ($name == "") {
                k1lib_common\show_error("The level name must have a value", __FUNCTION__, TRUE);
            }
            //convert the URL string into an array separated by "/" character
            $exploded_url = explode("/", $_GET[\k1lib\URL_REWRITE_VAR_NAME]);

            //the level requested can't be lower than the count of the items returned from the explode
            if ($level < count($exploded_url)) {
                $url_data_level_value = $exploded_url[$level];
                if (!empty($url_data_level_value)) {
                    self::$url_data[$level]['name'] = $name;
                    self::$url_data[$level]['value'] = $url_data_level_value;
                    // very bad practice so, I did comment it
                    // $GLOBALS[$name] = $url_data_level_value;

                    $this_url = self::get_this_url();
                    self::set_last_url($this_url);


                    return $url_data_level_value;
                } else {
                    if ($required) {
                        die("The URL value in the level {$level} is empty, the actual URL is bad formed " . __FUNCTION__);
                    } else {
                        return FALSE;
                    }
                }
            } else {
                if (!$required) {
                    $GLOBALS[$name] = NULL;
                    return FALSE;
                } else {
                    trigger_error("The URL level {$level} requested do not exist and is required", E_USER_ERROR);
                }
            }
        } else {
            return FALSE;
        }
    }

    static public function get_url_level_count() {

        return count(self::$url_data);
    }

    /**
     * Returns the index of URL by url_name
     * @global array self::$url_data this holds all the URL levels data
     * @param int $level Level number for query
     * @return string 
     */
    static public function get_url_level_index_by_name($level_name) {

        if (is_string($level_name)) {
            foreach (self::$url_data as $index => $array) {
                if ($array['name'] == $level_name) {
                    return $index;
                }
            }
            return FALSE;
        } else {
            trigger_error("The level value only can be STRING on " . __FUNCTION__);
        }
    }

    /**
     * Returns the value of URL by url_name
     * @global array self::$url_data this holds all the URL levels data
     * @param int $level Level number for query
     * @return string 
     */
    static public function get_url_level_value_by_name($level_name) {

        if (is_string($level_name)) {
            foreach (self::$url_data as $index => $array) {
                if ($array['name'] == $level_name) {
                    return $array['value'];
                }
            }
            return FALSE;
        } else {
            trigger_error("The level value only can be STRING on " . __FUNCTION__);
        }
    }

    /**
     * Returns the url portion of the level requested
     * @global array self::$url_data this holds all the URL levels data
     * @param int $level Level number for query
     * @return string 
     */
    static public function get_url_level_value($level = "this") {

        if (is_int($level)) {
            if (isset(self::$url_data[$level])) {
                return self::$url_data[$level]['value'];
            }
        } elseif ($level == "this") {
            return self::$url_data[count(self::$url_data) - 1]['value'];
        } else {
            trigger_error("The level value only can be INT on " . __FUNCTION__);
        }
    }

    /**
     * Returns the url name of the level requested
     * @global array self::$url_data this holds all the URL levels data
     * @param int $level Level number for query
     * @return string 
     */
    static public function get_url_level_name($level = "this") {

        if (is_int($level)) {
            if (isset(self::$url_data[$level])) {
                return self::$url_data[$level]['name'];
            }
        } elseif ($level == "this") {
            return self::$url_data[count(self::$url_data) - 1]['name'];
        } else {
            trigger_error("The level value only can be INT");
        }
    }

    /**
     * Returns the URL string for the max level defined AKA this actual level
     * @global array self::$url_data
     * @return string URL 
     */
    static public function get_this_url($complete_url = TRUE) {
        if ($complete_url) {
            return self::get_app_link(self::make_url_from_rewrite("this"));
        } else {
            return self::make_url_from_rewrite();
        }
    }

    static public function get_this_controller_id() {
        $controller_url = self::make_url_from_rewrite();
        $controller_id = str_replace("/", "-", $controller_url);
        $controller_id = substr($controller_id, 1);
        return $controller_id;
    }

    static public function set_last_url($url_to_set = "", $exclude = "fb-connect") {
        if (!strpos($url_to_set, $exclude)) {
            $_SESSION['last_url'] = $url_to_set;
        }
    }

    /**
     * Returns the URL until the level received in $level_to_built
     * @global array self::$url_data
     * @param type $level_to_built
     * @return string 
     */
    static public function make_url_from_rewrite($level_to_built = 'this') {

        $url_num_levels = count(self::$url_data) - 1;
        if ($url_num_levels < 1) {
            return "/";
        }
        if ($level_to_built === 'this') {
            $level_to_built = $url_num_levels;
        } else {
            if (is_int($level_to_built)) {
                if (($level_to_built < 0) && (($level_to_built + $url_num_levels) <= $url_num_levels)) {
                    $level_to_built += $url_num_levels;
                    if ($level_to_built > $url_num_levels) {
                        die("The calculated level do not exist " . __FUNCTION__);
                    }
                }
                if ($level_to_built > $url_num_levels) {
                    die("The calculated level do not exist " . __FUNCTION__);
                }
            } else {
                die("The level to built have to be a number " . __FUNCTION__);
            }
        }
        $page_url = "";
        if (($level_to_built <= $url_num_levels) && ($level_to_built >= 0)) {
            foreach (self::$url_data as $level => $level_data) {
                $page_url .= "/" . $level_data['value'];
                if ($level_to_built == $level) {
                    break;
                }
            }
        }
        return $page_url;
    }

    /**
     * Returns a URL ready for the APP, has the ability of keep the GET var and you can order wich one or all by default
     * @param string $url_to_link
     * @param boolean $keep_get_vars
     * @param type $get_vars_to_keep
     * @return string
     */
    static public function get_app_link($url_to_link, $keep_get_vars = TRUE, $get_vars_to_keep = "") {
        if ($url_to_link === NULL) {
            return NULL;
        }
        if (!is_string($url_to_link)) {
            trigger_error("The value to make the link have to be a string", E_USER_ERROR);
        }
        if (!is_string($get_vars_to_keep)) {
            trigger_error("The value of get_vars_to_keep have to be a string", E_USER_ERROR);
        }

        if (($get_vars_to_keep == "") && defined("\k1app\GLOBAL_GET_KEEP_VARS")) {
            // we always have to keep the signed_request from FB
            $get_vars_to_keep .= ( (!empty($get_vars_to_keep)) ? ',' . \k1app\GLOBAL_GET_KEEP_VARS : \k1app\GLOBAL_GET_KEEP_VARS);
        }
        //make the initial link
        if (strstr($url_to_link, "http://") === FALSE) {
            // if the url do not have / at start we must to put it
            if (substr($url_to_link, 0, 2) == './') {
                $page_url = $url_to_link;
            } else {
                if (substr($url_to_link, 0, 1) != '/') {
                    $url_to_link = '/' . $url_to_link;
                }
                $page_url = APP_URL . $url_to_link;
            }
        } else {
            $page_url = $url_to_link;
        }
        $i = 0;
        // build the GET list whit only with the vars that the user needs to keep
        if ($keep_get_vars) {
            foreach ($_GET as $name => $value) {
                $value = urlencode($value);
                if (strpos($get_vars_to_keep, $name) !== FALSE) {
                    $i++;
                    if (strpos($page_url, "?")) {
                        $page_url .= "&{$name}={$value}";
                    } else {
                        $page_url .= ( ($i == 1) ? '?' : '&') . "{$name}={$value}";
                    }
                } else {
                    continue;
                }
            }
        }

        return $page_url;
    }

}
