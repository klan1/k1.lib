<?php

namespace k1lib\common;

/**
 * Checks if the application is runnin with the Framework
 * 
 * TODO: Make this better
 *
 *  @return boolean 
 */
function check_on_app() {
    if (!defined("\k1app\IN_K1APP")) {
        \trigger_error("hacking attemp '^_^", E_USER_ERROR);
    } else {
        return TRUE;
    }
}

/**
 * Function for debug on screen any kind of data
 * @param mixed $d
 * @param boolean $dump
 * @param boolean $inline 
 */
//function d($d, $dump = FALSE, $inline = TRUE) {
////    trigger_error(__FILE__, E_USER_ERROR);
//    $msg = ( ($dump) ? var_export($d, TRUE) : print_r($d, TRUE) );
//    if (\k1app\APP_MODE == "shell") {
//        echo "\n{$msg}\n";
//    } else {
//        if ($inline) {
//            echo "<pre>\n";
//            echo $msg;
//            echo "\n</pre>\n";
//        } else {
//            \k1lib\common\show_message("$msg", "DUMP");
//        }
//    }
//}

/**
 * Only receive arrays to show them 
 * @param type $array 
 */
function dump_array($array) {
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            echo "<div id='array-dump'>\n";
            \k1lib\common\show_message((is_array($value) ? "<pre>" . print_r($value, TRUE) . "</pre>" : $value), $key);
            echo "\n</div>\n";
        }
    } else {
        \k1lib\common\show_error("The variable is not an Array", __FUNCTION__);
    }
}

/**
 * Show an error with the K1FW way
 * @global unknown $app_fatal_error unknown use xD
 * @param mixed $e      Could be an error msg or an object from some php class
 * @param string $title    Title for the messase window or baloon
 * @param bolean $exit  Tell the function if have to terminate the execution
 */
function show_error($e, $title = "ERROR", $exit = FALSE) {
    echo \k1lib\common\get_error($e, $title);
    if ($exit) {
// $app_fatal_error = TRUE;
// desactivated by jd
//    \k1lib\templates\temply::set_place_value("bottom_script", "<script type='text/javascript'>k1_clear_controller_content()</script>");
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
function get_error($e, $title = "ERROR") {
    $msg_error = "";
    if (is_object($e) || is_array($e)) {
        $msg = print_r($e, TRUE);
    } else {
        $msg = $e;
    }
    if ((\k1app\APP_MODE == 'web') || (\k1app\APP_MODE == 'ajax')) {
        include \k1lib\templates\temply::load_template("app.messages");
        $msg_error = str_replace("%title%", $title, $msg_error);
        $msg_error = str_replace("%message%", $msg, $msg_error);
    } else {
        $msg_error = $msg;
    }
    return $msg_error;
}

/**
 * Takes an Array and transform in key1
 * @param type $guide_array
 * @param type $data_array
 * @return string 
 */
function array_to_url_parameters($data_array, $guide_array = FALSE, $use_json = FALSE, $upper_name = "") {
    $url_parameters = "";
    if (!is_array($guide_array)) {
        $guide_array = \k1lib\forms\make_guide_array($data_array);
    }
    d($guide_array);
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
                        $url_parameters .= \k1lib\common\array_to_url_parameters($data_array[$key], $value, FALSE, $key);
                    } else {
                        $url_parameters .= \k1lib\common\array_to_url_parameters($data_array[$key], $value, FALSE, "{$upper_name}[{$key}]");
                    }
                }
            }
        }
    }
    return $url_parameters;
}

function show_message($msg, $title = "") {
    echo k1_get_message($msg, $title);
}

function k1_get_message($msg, $title = "") {
    if ((\k1app\APP_MODE == 'web') || (\k1app\APP_MODE == 'ajax')) {
        include \k1lib\templates\temply::load_template("app.messages");
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

function check_incomming_var($var, $request = FALSE, $url_decode = FALSE) {
    if ((is_string($var) || is_numeric($var)) && !is_array($var)) {
        if (($request == TRUE) && isset($_REQUEST[$var])) {
            $value = $_REQUEST[$var];
        } elseif (($request == FALSE) && ($var != "")) {
            $value = $var;
        } else {
            $value = NULL;
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
        return NULL;
    }
}

function bolean_to_string($bolean) {
    if ($bolean) {
        return "Si";
    } else {
        return "No";
    }
}

function check_freedom_user($fb_uid = '') {
    global $app_freedom_users;
    if ($fb_uid == '') {
        $fb_uid = $GLOBALS['fb_uid'];
    }
    if (array_search($fb_uid, $app_freedom_users) !== FALSE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function check_blocked_user($fb_uid = '') {
    global $app_blocked_user;
    if ($fb_uid == '') {
        $fb_uid = $GLOBALS['fb_uid'];
    }
    if (array_search($fb_uid, $app_blocked_user) !== FALSE) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function check_blocked_message($post = "") {
    global $app_blocked_message;
    foreach ($app_blocked_message as $msg) {
        $post = strtolower($post);
        $msg = strtolower($msg);
        if (strpos($post, $msg) !== FALSE) {
            \k1lib\common\show_message("Entontrada la siguiente expresion NO permitida: {$msg}");
            return TRUE;
        }
    }
    return FALSE;
}

function get_magic_name($name) {
    return "magic_{$name}_secret";
}

function set_magic_value($name) {
    $secret = md5($name . microtime(TRUE));
    $_SESSION[\k1lib\common\get_magic_name($name)] = $secret;
    $client_magic = md5(\k1lib\MAGIC_VALUE . $secret);
    return $client_magic;
}

function check_magic_value($name, $value_to_check) {
    if ($value_to_check == "") {
        die("The magic value never can be empty!");
    } else {
        if (isset($_SESSION[\k1lib\common\get_magic_name($name)])) {
            $secret = $_SESSION[\k1lib\common\get_magic_name($name)];
            $client_magic = md5(\k1lib\MAGIC_VALUE . $secret);
            if ($client_magic == $value_to_check) {
                return $client_magic;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }
}

function check_email_address($email) {
// First, we check that there's one @ symbol, 
// and that the lengths are right.
    $regex = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';
    if (!preg_match($regex, $email)) {
// Email invalid because wrong number of characters 
// in one board or wrong number of @ symbols.
        return FALSE;
    }
    return TRUE;
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

function get_file_extension($file_name, $to_lower = FALSE) {
    if (!is_string($file_name)) {
        \k1lib\common\show_error("The file name to check only can be a string", __FUNCTION__, TRUE);
    }
    $last_dot_pos = strrpos($file_name, ".");
    if ($last_dot_pos !== FALSE) {
        //trim the ?query url
        $last_question_pos = strrpos($file_name, "?");
        if ($last_question_pos !== FALSE) {
            $file_name = substr($file_name, 0, $last_question_pos);
        }
        //extension
        $file_extension = substr($file_name, $last_dot_pos + 1);
        if ($to_lower) {
            return strtolower($file_extension);
        } else {
            return $file_extension;
        }
    } else {
        return FALSE;
    }
}
