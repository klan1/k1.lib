<?php

/**
 * Checks if the application is runnin with the Framework
 * 
 * TODO: Make this better
 *
 *  @return boolean 
 */
function k1_check_on_app() {
    if (!defined("IN_K1APP")) {
        die("haking attemp '^_^");
    } else {
        return true;
    }
}

/**
 * Function for debug on screen any kind of data
 * @param mixed $d
 * @param boolean $dump
 * @param boolean $inline 
 */
function d($d, $dump = false, $inline = true) {
    $msg = ( ($dump) ? var_export($d, true) : print_r($d, true) );
    if (APP_MODE == "shell") {
        echo "\n{$msg}\n";
    } else {
        if ($inline) {
            echo "<pre>\n";
            echo $msg;
            echo "\n</pre>\n";
        } else {
            k1_show_message("$msg", "DUMP");
        }
    }
}

/**
 * Only receive arrays to show them 
 * @param type $array 
 */
function k1_dump_array($array) {
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            echo "<div id='array-dump'>\n";
            k1_show_message((is_array($value) ? "<pre>" . print_r($value, true) . "</pre>" : $value), $key);
            echo "\n</div>\n";
        }
    } else {
        k1_show_error("The variable is not an Array", __FUNCTION__);
    }
}

/**
 * Show an error with the K1FW way
 * @global unknown $app_fatal_error unknown use xD
 * @param mixed $e      Could be an error msg or an object from some php class
 * @param string $title    Title for the messase window or baloon
 * @param bolean $exit  Tell the function if have to terminate the execution
 */
function k1_show_error($e, $title = "ERROR", $exit = false) {
    echo k1_get_error($e, $title);
    if ($exit) {
// $app_fatal_error = true;
// desactivated by jd
//    k1_set_place_value("bottom_script", "<script type='text/javascript'>k1_clear_controller_content()</script>");
        exit();
    }
}

/**
 * Get an error with the K1FW way 
 * @global unknown $app_fatal_error unknown use xD
 * @param mixed $e      Could be an error msg or an object from some php class
 * @param string $title    Title for the messase window or baloon
 * @param bolean $exit  Tell the function if have to terminate the execution
 * @return string HTML with the error
 */
function k1_get_error($e, $title = "ERROR") {
    $msg_error = "";
    if (is_object($e) || is_array($e)) {
        $msg = print_r($e, true);
    } else {
        $msg = $e;
    }
    if ((APP_MODE == 'web') || (APP_MODE == 'ajax')) {
        include k1_load_template("app.messages");
        $msg_error = str_replace("%title%", $title, $msg_error);
        $msg_error = str_replace("%message%", $msg, $msg_error);
    } else {
        $msg_error = $msg;
    }
    return $msg_error;
}

/**
 *
 * @param type $guide_array
 * @param type $data_array
 * @return string 
 */
function k1_array_to_url_parameters($data_array, $guide_array = false, $use_json = false, $upper_name = "") {
    $url_parameters = "";
    if (!is_array($guide_array)) {
        $guide_array = k1_make_guide_array($data_array);
    }

    foreach ($guide_array as $key => $value) {
        if (!is_array($value)) {
            if (isset($data_array[$value])) {
                if ($upper_name == "") {
                    $url_parameters .= "{$value}=" . urlencode($data_array[$value]) . "&";
                } else {
                    $url_parameters .= "{$upper_name}[{$value}]=" . urlencode($data_array[$value]) . "&";
                }
            }
        } else {
            if (isset($data_array[$key])) {
                if ($use_json) {
                    $url_parameters .= "$key=" . urlencode(json_encode($data_array[$key])) . "&";
                } else {
                    if ($upper_name == "") {
                        $url_parameters .= k1_array_to_url_parameters($data_array[$key], $value, false, $key);
                    } else {
                        $url_parameters .= k1_array_to_url_parameters($data_array[$key], $value, false, "{$upper_name}[{$key}]");
                    }
                }
            }
        }
    }
    return $url_parameters;
}

function k1_show_message($msg, $title = "") {
    echo k1_get_message($msg, $title);
}

function k1_get_message($msg, $title = "") {
    if ((APP_MODE == 'web') || (APP_MODE == 'ajax')) {
        include k1_load_template("app.messages");
        if (empty($title)) {
            $msg_alert_no_title = str_replace("%message%", $msg, $msg_alert_no_title);
            return $msg_alert_no_title;
        } else {
            $msg_alert = str_replace("%title%", $title, $msg_alert);
            $msg_alert = str_replace("%message%", $msg, $msg_alert);
            return $msg_alert;
        }
    } else {
        return $msg;
    }
}

/*
 * TODO: Do completely this function k1_for security
 */

function k1_check_incomming_var($var, $request = false, $url_decode = false) {
    if ((is_string($var) || is_numeric($var)) && !is_array($var)) {
        if (($request == true) && isset($_REQUEST[$var])) {
            $value = $_REQUEST[$var];
        } elseif (($request == false) && ($var != "")) {
            $value = $var;
        } else {
            $value = null;
        }
        if ($url_decode) {
            $value = urldecode($value);
        }
        if (@json_decode($value) === NULL) {
            $replace = array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"');
            $search = array("\\", "\0", "\n", "\r", "\x1a", "'", '"');
            $value = str_replace($search, $replace, $value);
//            $value = mysql_escape_string($value);
        } else {
            
        }
        if (is_string($value) && is_numeric($value)) {
            if (substr($value, 0, 1) != "0") {
                $value += 0;
            } elseif ((substr($value, 0, 1) == "0") && (strlen($value) == 1)) {
                $value = 0;
            }
        }
        return $value;
    } else {
        return null;
    }
}

function k1_bolean_to_string($bolean) {
    if ($bolean) {
        return "Si";
    } else {
        return "No";
    }
}

function k1_check_freedom_user($fb_uid = '') {
    global $app_freedom_users;
    if ($fb_uid == '') {
        $fb_uid = $GLOBALS['fb_uid'];
    }
    if (array_search($fb_uid, $app_freedom_users) !== false) {
        return true;
    } else {
        return false;
    }
}

function check_blocked_user($fb_uid = '') {
    global $app_blocked_user;
    if ($fb_uid == '') {
        $fb_uid = $GLOBALS['fb_uid'];
    }
    if (array_search($fb_uid, $app_blocked_user) !== false) {
        return true;
    } else {
        return false;
    }
}

function check_blocked_message($post = "") {
    global $app_blocked_message;
    foreach ($app_blocked_message as $msg) {
        $post = strtolower($post);
        $msg = strtolower($msg);
        if (strpos($post, $msg) !== false) {
            k1_show_message("Entontrada la siguiente expresion NO permitida: {$msg}");
            return true;
        }
    }
    return false;
}

function k1_get_magic_name($name) {
    return "magic_{$name}_secret";
}

function k1_set_magic_value($name) {
    $secret = md5($name . microtime(true));
    $_SESSION[k1_get_magic_name($name)] = $secret;
    $client_magic = md5(MAGIC_VALUE . $secret);
    return $client_magic;
}

function k1_check_magic_value($name, $value_to_check) {
    if ($value_to_check == "") {
        die("The magic value never can be empty!");
    } else {
        if (isset($_SESSION[k1_get_magic_name($name)])) {
            $secret = $_SESSION[k1_get_magic_name($name)];
            $client_magic = md5(MAGIC_VALUE . $secret);
            if ($client_magic == $value_to_check) {
                return $client_magic;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}

function k1_check_email_address($email) {
// First, we check that there's one @ symbol, 
// and that the lengths are right.
    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    if (!preg_match($regex, $email)) {
// Email invalid because wrong number of characters 
// in one board or wrong number of @ symbols.
        return false;
    }
    return true;
}

function XmlToJson($xml, $append = "") {
    $fileContents = $xml;
    $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
    $fileContents = trim(str_replace('"', "'", $fileContents));
    $simpleXml = simplexml_load_string($fileContents);
    $simpleXml->append = htmlentities($append);
    $json = json_encode($simpleXml);
    return $json;
}
