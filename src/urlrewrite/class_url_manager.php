<?php

namespace k1lib\urlrewrite;

use k1lib_common as k1lib_common;

class url {

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
                trigger_error("The level name must have a value : " . __FUNCTION__, E_USER_ERROR);
            }
            //convert the URL string into an array separated by "/" character
            $exploded_url = explode("/", $_GET[\k1lib\URL_REWRITE_VAR_NAME]);
//            unset($_GET[\k1lib\URL_REWRITE_VAR_NAME]);
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
    static public function get_this_url() {
        return self::make_url_from_rewrite("this");
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
        if ($url_num_levels < 0) {
            return "/";
        } else {
            /**
             * LEVEL CHECK
             */
            if ($level_to_built === 'this') {
                $level_to_built = $url_num_levels;
            } else {
                if (is_int($level_to_built)) {
                    if (($level_to_built < 0) && (($level_to_built + $url_num_levels) <= $url_num_levels)) {
                        $level_to_built += $url_num_levels;
                        if ($level_to_built > $url_num_levels) {
                            trigger_error(__METHOD__ . " : The calculated level do not exist ", E_USER_ERROR);
                        }
                    }
                    if ($level_to_built > $url_num_levels) {
                        trigger_error(__METHOD__ . "The calculated level do not exist ", E_USER_ERROR);
                    }
                } else {
                    trigger_error(__METHOD__ . "The level to built have to be a number ", E_USER_ERROR);
                }
            }
            $page_url = "";
            /**
             * LETS DO IT
             */
            if (($level_to_built <= $url_num_levels) && ($level_to_built >= 0)) {
                foreach (self::$url_data as $level => $level_data) {
                    $page_url .= (($level === 0) ? "" : "/") . $level_data['value'];
                    if ($level_to_built == $level) {
                        break;
                    }
                }
            }
            return $page_url . '/';
        }
    }

    /**
     * Return an URL with NEW and EXISTENT GET values with no efford
     * @param type $url
     * @param array $new_get_vars
     * @param type $keep_actual_get_vars
     * @param array $wich_get_vars
     * @param type $keep_including
     * @return string
     */
    static public function do_url($url, array $new_get_vars = [], $keep_actual_get_vars = TRUE, array $wich_get_vars = [], $keep_including = TRUE) {
        if (!is_string($url)) {
            trigger_error("The value to make the link have to be a string", E_USER_ERROR);
        }

        /**
         * Separate URL, GET VARS and HASH
         */
        //Get the HASH part
        $hash = strstr($url, "#");
        // Clean the hash part from URL
        $url = str_replace($hash, "", $url);

        //Get the GET vars part
        $url_vars = strstr($url, "?", FALSE);
        // Clean the GET vars from URL
        $url = str_replace($url_vars, "", $url);
        // Now remove the ? from GET vars part
//        $url_vars = str_replace("?", "", $url_vars);
        $url_var_array = \k1lib\common\explode_with_2_delimiters("&", "=", $url_vars, 1);
        /**
         * Catch all _GET vars
         */
        foreach ($_GET as $key => $value) {
            $_GET[$key] = urldecode($value);
        }
        $actual_get_vars = \k1lib\forms\check_all_incomming_vars($_GET);
        unset($actual_get_vars[\k1lib\URL_REWRITE_VAR_NAME]);

        /**
         * Join actual GET vars with the URL GET vars
         */
        $actual_get_vars = array_merge($actual_get_vars, $url_var_array);
        /**
         * We have to uset() the new vars from the ACTUAL _GET to avoid problems
         */
        foreach ($actual_get_vars as $var_name => $value) {
            if (key_exists($var_name, $new_get_vars)) {
                unset($actual_get_vars[$var_name]);
            }
        }

        $get_vars_to_add = [];
        if (!empty($new_get_vars)) {
            foreach ($new_get_vars as $var_name => $value) {
                $get_vars_to_add[] = "{$var_name}=" . urlencode($value);
            }
        }
        $get_var_to_keep = [];
        if ($keep_actual_get_vars) {
            if (!empty($wich_get_vars)) {
                foreach ($actual_get_vars as $var_name => $value) {
                    if (key_exists($var_name, array_flip($wich_get_vars))) {
                        if ($keep_including) {
                            $get_var_to_keep[] = "{$var_name}=" . urlencode($value);
                        } else {
                            unset($actual_get_vars[$var_name]);
                        }
                    }
                }
                if (!$keep_including) {
                    foreach ($actual_get_vars as $var_name => $value) {
                        $get_var_to_keep[] = "{$var_name}=" . urlencode($value);
                    }
                }
            } else {
                foreach ($actual_get_vars as $var_name => $value) {
                    $get_var_to_keep[] = "{$var_name}=" . urlencode($value);
                }
            }
        }
        $get_vars = array_merge($get_var_to_keep, $get_vars_to_add);
        /**
         * join the new get vars
         */
        if (!empty($new_get_vars) || !empty($get_vars)) {
            $get_vars_on_text = "?" . implode("&", $get_vars);
        } else {
            $get_vars_on_text = "";
        }
        $url_to_return = $url . $get_vars_on_text . $hash;
        return $url_to_return;
    }

    static function do_clean_url($url) {
        return self::do_url($url, [], FALSE);
    }

    static function set_next_url_level($controller_path, $required_level = FALSE, $level_name = 'default') {
        $next_url_level = self::get_url_level_count();
        // get the base URL to load the next one
        $actual_url = self::get_this_url();
        // get from the URL the next level value :   /$actual_url/next_level_value
        $next_directory_name = self::set_url_rewrite_var($next_url_level, $level_name, $required_level);
        if (!empty($next_directory_name)) {
            $file_to_include = \k1lib\controllers\load_controller($next_directory_name, $controller_path . $actual_url);
            return $file_to_include;
        } else {
            return FALSE;
        }
    }

}
