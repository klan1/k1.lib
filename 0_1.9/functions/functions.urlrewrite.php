<?php

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
if (!defined("URL_REWRITE_VAR_NAME")) {
    define("URL_REWRITE_VAR_NAME", "K1_URL");
}

/**
 * @param int This storage the actual level for the URL rewrite system 
 */
$rewrite_level = 0;
$url_data = array();
$this_url = "";

function k1_get_url_level_count() {
    global $url_data;
    return count($url_data);
}

/**
 * Returns the index of URL by url_name
 * @global array $url_data this holds all the URL levels data
 * @param int $level Level number for query
 * @return string 
 */
function k1_get_url_level_index_by_name($level_name) {
    global $url_data;
    if (is_string($level_name)) {
        foreach ($url_data as $index => $array) {
            if ($array['name'] == $level_name) {
                return $index;
            }
        }
        return false;
    } else {
        trigger_error("The level value only can be STRING on " . __FUNCTION__);
    }
}

/**
 * Returns the value of URL by url_name
 * @global array $url_data this holds all the URL levels data
 * @param int $level Level number for query
 * @return string 
 */
function k1_get_url_level_value_by_name($level_name) {
    global $url_data;
    if (is_string($level_name)) {
        foreach ($url_data as $index => $array) {
            if ($array['name'] == $level_name) {
                return $array['value'];
            }
        }
        return false;
    } else {
        trigger_error("The level value only can be STRING on " . __FUNCTION__);
    }
}

/**
 * Returns the url portion of the level requested
 * @global array $url_data this holds all the URL levels data
 * @param int $level Level number for query
 * @return string 
 */
function k1_get_url_level_value($level = "this") {
    global $url_data;
    if (is_int($level)) {
        if (isset($url_data[$level])) {
            return $url_data[$level]['value'];
        }
    } elseif ($level == "this") {
        return $url_data[count($url_data) - 1]['value'];
    } else {
        trigger_error("The level value only can be INT on " . __FUNCTION__);
    }
}

/**
 * Returns the url name of the level requested
 * @global array $url_data this holds all the URL levels data
 * @param int $level Level number for query
 * @return string 
 */
function k1_get_url_level_name($level = "this") {
    global $url_data;
    if (is_int($level)) {
        if (isset($url_data[$level])) {
            return $url_data[$level]['name'];
        }
    } elseif ($level == "this") {
        return $url_data[count($url_data) - 1]['name'];
    } else {
        trigger_error("The level value only can be INT");
    }
}

/**
 * Set the URL level name for the app 
 * The $url_data array will hold the data in this way:
 * $url_data[$level]['name']
 * $url_data[$level]['value']
 * @param int $level Level deep to define
 * @param string $name Level name
 * @param boolean $required Required? is true the app will stop is the leves is not pressent on the APP_URL
 * @return boolean 
 * TODO: check level prerequisites 
 */
function k1_set_url_rewrite_var($level, $name, $required = true) {
    if (!empty($_GET[URL_REWRITE_VAR_NAME])) {
        global $url_data;
        // checks if the level variable is INT
        if (!is_int($level) || ($level < 0)) {
            k1_show_error("The level for URL REWRITE have to be numeric", __FUNCTION__, true);
        } elseif ($level == 0) { // if is the frist leves it must to be required
            $required = true;
        }
        // the name var has to have a value
        if ($name == "") {
            k1_show_error("The level name must have a value", __FUNCTION__, true);
        }
        //convert the URL string into an array separated by "/" character
        $exploded_url = explode("/", $_GET[URL_REWRITE_VAR_NAME]);
        //the level requested can't be lower than the count of the items returned from the explode
        if ($level < count($exploded_url)) {
            $url_data_level_value = $exploded_url[$level];
            if (!empty($url_data_level_value)) {
                $url_data[$level]['name'] = $name;
                $url_data[$level]['value'] = $url_data_level_value;
                // very bad practice so, I did comment it
                // $GLOBALS[$name] = $url_data_level_value;

                $this_url = k1_get_this_url();
                k1_set_last_url($this_url);

                return $url_data_level_value;
            } else {
                if ($required) {
                    die("The URL value in the level {$level} is empty, the actual URL is bad formed " . __FUNCTION__);
                } else {
                    return false;
                }
            }
        } else {
            if (!$required) {
                $GLOBALS[$name] = null;
                return false;
            } else {
                k1_show_error("The URL level {$level} requested do not exist and is required", __FUNCTION__, true);
            }
        }
    } else {
        return false;
    }
}

/**
 * Returns the URL string for the max level defined AKA this actual level
 * @global array $url_data
 * @return string URL 
 */
function k1_get_this_url($complete_url = true) {
    if ($complete_url) {
        return k1_get_app_link(k1_make_url_from_rewrite("this"));
    } else {
        return k1_make_url_from_rewrite();
    }
}

function k1_get_this_controller_id() {
    $controller_url = k1_make_url_from_rewrite();
    $controller_id = str_replace("/", "-", $controller_url);
    $controller_id = substr($controller_id, 1);
    return $controller_id;
}

function k1_set_last_url($url_to_set = "", $exclude = "fb-connect") {
    if (!strpos($url_to_set, $exclude)) {
        $_SESSION['last_url'] = $url_to_set;
    }
}

/**
 * Returns the URL until the level received in $level_to_built
 * @global array $url_data
 * @param type $level_to_built
 * @return string 
 */
function k1_make_url_from_rewrite($level_to_built = 'this') {
    global $url_data;
    $url_num_levels = count($url_data) - 1;
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
        foreach ($url_data as $level => $level_data) {
            $page_url .= "/" . $level_data['value'];
            if ($level_to_built == $level) {
                break;
            }
        }
    }
    return $page_url;
}

/**
 * Makes asimple link without app format and just with class attribute builtin 
 * @param string $link Link to build
 * @param string $text Text to print on the document
 * @param string $class CSS Class to use
 * @param string $extra OThers tag attributes that you want to add Ej. onclick='null'
 */
function k1_print_link($link, $text, $class = "", $extra = "") {
    echo "<a href='$link' class='$class' $extra>$text</a>";
}
function k1_get_link($link, $text, $class = "", $extra = "") {
    return "<a href='$link' class='$class' $extra>$text</a>";
}

/**
 * This is an alias of "k1_get_app_link" with the $get_vars_to_keep parameter ready to use from the CONFIG file
 * @param string $url_to_link
 * @param boolean $keep_get_vars
 * @param string $get_vars_to_keep Coma separated value
 * @return string
 */
function k1_get_fb_app_link($url_to_link, $keep_get_vars = true, $get_vars_to_keep = GET_VARS_TO_KEEP) {
    $link = k1_get_app_link($url_to_link, $keep_get_vars, $get_vars_to_keep);
    return str_replace(APP_URL, FB_APP_URL, $link);
}

/**
 * Returns a URL ready for the APP, has the ability of keep the GET var and you can order wich one or all by default
 * @param string $url_to_link
 * @param boolean $keep_get_vars
 * @param type $get_vars_to_keep
 * @return string
 */
function k1_get_app_link($url_to_link, $keep_get_vars = true, $get_vars_to_keep = "") {
    if ($url_to_link === null) {
        return null;
    }
    if (!is_string($url_to_link)) {
        k1_show_error("The value to make the link have to be a string", __FUNCTION__, true);
    }
    if (!is_string($get_vars_to_keep)) {
        k1_show_error("The value of get_vars_to_keep have to be a string", __FUNCTION__, true);
    }

    if (($get_vars_to_keep == "") && defined("GLOBAL_GET_KEEP_VARS")) {
        // we always have to keep the signed_request from FB
        $get_vars_to_keep .= ( (!empty($get_vars_to_keep)) ? ',' . GLOBAL_GET_KEEP_VARS : GLOBAL_GET_KEEP_VARS);
    }

    //make the initial link
    if (strstr($url_to_link, "https://") === false) {
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
    foreach ($_GET as $name => $value) {
        if (strpos($get_vars_to_keep, $name) !== false) {
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

    return $page_url;
}


