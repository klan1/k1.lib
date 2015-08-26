<?php

/**
 * General use functions, K1.lib.
 * 
 * Those are the base functions for a typical develpoment proyect and for the other packages functions/classes.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package common
 */

namespace k1lib\common;

/**
 * Checks if the application is runnin with the Framework
 * 
 * TODO: Make this better
 *
 *  @return boolean 
 */
function check_on_k1lib() {
    if (!defined("\k1app\IN_K1APP")) {
        \trigger_error("Hacking attemp '^_^", E_USER_ERROR);
    } else {
        return TRUE;
    }
}

/**
 * Based on a HTML template if the APP_MODE is WEB will return a HTML formated error.
 * @param mixed $e This coud be an String, Object or Array
 * @param String $title Subject
 * @param String $app_mode this should be "web" to return HTML, is not will return plain text format.
 * @return string
 */
function get_error($e, $title = "ERROR", $app_mode = "web") {
    if (is_object($e) || is_array($e)) {
        $msg = print_r($e, TRUE);
    } else {
        $msg = $e;
    }
    return get_message($msg, $title, "alert", $app_mode);
}

/**
 * Based on a HTML template if the APP_MODE is WEB will return a HTML formated message
 * @param String $msg Message to show
 * @param String $title Subject
 * @param String $type This should be "success", "warning", "info", "alert" or NULL. This is a Foundation CLASS.
 * @param String $app_mode this should be "web" to return HTML, is not will return plain text format.
 * @return string
 */
function get_message($msg, $title = "", $type = "info", $app_mode = "web") {
//    trigger_error("Using this!", E_USER_NOTICE);
    if ($app_mode == 'web') {
        $tpl = \k1lib\html\load_html_template("message_template");
        $output = sprintf($tpl, $title, $msg, $type);
    } else {
        $output = "- $type - " . ((!empty($title)) ? "$title :" : "") . $msg;
    }
    return $output;
}

/**
 * Based on a HTML template if the APP_MODE is WEB will echo a HTML formated message
 * @param String $msg Message to show
 * @param String $title Subject
 * @param String $type This should be "success", "warning", "info", "alert" or NULL. This is a Foundation CLASS.
 * @param String $app_mode this should be "web" to return HTML, is not will return plain text format.
 */
function show_message($msg, $title = "", $type = "info", $app_mode = "web") {
    echo get_message($msg, $title, $type, $app_mode);
}

/**
 * This will make an Array with $data_array[keys] as values as $return_array[0] = value0 .. $return_array[N] = $valueN. Is recursive.
 * @param Array $data_array
 * @return Array
 */
function make_guide_array($data_array) {
    $guide_array = array();
    foreach ($data_array as $key => $value) {
        if (!is_array($value)) {
            $guide_array[] = $key;
        } else {
            $guide_array[$key] = make_guide_array($value);
        }
    }
    return $guide_array;
}

/**
 * Only the existing KEYS of $array_to_clean in $guide_array will be returned
 * @param Array $array_to_clean
 * @param Array $guide_array
 * @return Array
 */
function clean_array_with_guide($array_to_clean, $guide_array) {
    foreach ($array_to_clean as $clean_key => $clean_value) {
        if (!isset($guide_array[$clean_key])) {
            unset($array_to_clean[$clean_key]);
        }
    }
    return $array_to_clean;
}

/**
 * Takes an Array and transform in key1=value1&keyN=valueN. Is recursive.
 * @param Array $data_array The data to convert to GET URL
 * @param Array $guide_array Only the existing KEYS in this Array will be converted
 * @return string 
 */
function array_to_url_parameters($data_array, $guide_array = FALSE, $use_json = FALSE, $upper_name = "") {
    $url_parameters = "";
    if (!is_array($guide_array)) {
        $guide_array = make_guide_array($data_array);
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
                        $url_parameters .= array_to_url_parameters($data_array[$key], $value, FALSE, $key);
                    } else {
                        $url_parameters .= array_to_url_parameters($data_array[$key], $value, FALSE, "{$upper_name}[{$key}]");
                    }
                }
            }
        }
    }
    return $url_parameters;
}

/**
 * This SHOULD be used always to receive any kind of value from _GET _POST _REQUEST if it will be used on SQL staments.
 * @param String $var Value to check.
 * @param Boolean FALSE to check $var as is. Use TRUE if $var become an index as: $_REQUEST[$var]
 * @param Boolean $url_decode TRUE if the data should be URL decoded.
 * @return String Rerturn NULL on error This could be that $var IS NOT String, Number or IS Array.
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
        /**
         * TODO: CHECK THIS !!
         */
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

/**
 * Converts Booleans vars to text
 * @param Boolean $bolean
 * @param String $true_value Text to convert on TRUE
 * @param String $false_value Text to convert on FALSE
 * @return String
 */
function bolean_to_string($bolean, $true_value = "Si", $false_value = "No") {
    if ($bolean) {
        return $true_value;
    } else {
        return $false_value;
    }
}

/**
 * Returns a qualified MAGIC NAME
 * @param String $name
 * @return String
 */
function get_magic_name($name) {
    if (\k1lib\session\session_plain::on_session()) {
        return "magic_{$name}_secret";
    } else {
        trigger_error("Magic system REQUIRES the session system to be enabled and a session started", E_USER_ERROR);
    }
}

/**
 * Uses the PHP Session system to generate and stores the MAGIC VALUE by $name
 * @param String $name This name HAVE TO BE used to check the form on the receiver script.
 * @return String Magic value to be used on FORM
 */
function set_magic_value($name) {
    if (\k1lib\session\session_plain::on_session()) {
        $secret = md5($name . microtime(TRUE));
        $_SESSION[\k1lib\common\get_magic_name($name)] = $secret;
        $client_magic = md5(\k1lib\MAGIC_VALUE . $secret);
        return $client_magic;
    } else {
        trigger_error("Magic system REQUIRES the session system to be enabled and a session started", E_USER_ERROR);
    }
}

/**
 * Check a incomming MAGIC VALUE 
 * @param String $name The name with it was stored
 * @param String $value_to_check Received var
 * @return boolean
 */
function check_magic_value($name, $value_to_check) {
    if (\k1lib\session\session_plain::on_session()) {
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
    } else {
        trigger_error("Magic system REQUIRES the session system to be enabled and a session started", E_USER_ERROR);
    }
}

/**
 * Checks if the $email var is a valid email by regular expressions.
 * @param String $email
 * @return boolean
 */
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

/**
 * This supposed to conver an XML string stored on $xml and return the JSON data as string. NOT TESTED!!
 * @param String $xml
 * @param String $append Any string to append to the converted string... but has no logic -.-
 * @return JSON String
 */
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
        \trigger_error("The file name to check only can be a string ", E_USER_ERROR);
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
