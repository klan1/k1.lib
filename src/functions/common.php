<?php

namespace k1lib;

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

/**
 * General use functions, K1.lib.
 * 
 * Those are the base functions for a typical develpoment proyect and for the other packages functions/classes.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package common
 */

namespace k1lib\common;

use k1lib\K1MAGIC;
use k1lib\session\app_session;
use Ramsey\Uuid\DegradedUuid;

/**
 * Checks if the application is runnin with the Framework
 * 
 * TODO: Make this better
 *
 *  @return boolean 
 */
function check_on_k1lib() {
    d(__FUNCTION__ . " do not use me more!");
}

/**
 * This will make an Array with $data_array[keys] as values as $return_array[0] = value0 .. $return_array[N] = $valueN. Is recursive.
 * @param array $data_array
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
 * @param array $array_to_clean
 * @param array $guide_array
 * @return Array
 */
function clean_array_with_guide($array_to_clean, $guide_array) {
    $new_array = [];
    if (!empty($guide_array)) {
        foreach ($guide_array as $guide_key => $guide_value) {
            if (array_key_exists($guide_key, $array_to_clean)) {
                $new_array[$guide_key] = $array_to_clean[$guide_key];
            }
        }
//
//    foreach ($array_to_clean as $clean_key => $clean_value) {
//        if (!isset($guide_array[$clean_key])) {
//            unset($array_to_clean[$clean_key]);
//        }
//    }
        return $new_array;
    } else {
        return $array_to_clean;
    }
}

function organize_array_with_guide($array_to_organize, $guide_array) {
    $new_array = [];
    foreach ($guide_array as $guide_key => $no_use) {
        if (isset($array_to_organize[$guide_key])) {
            $new_array[$guide_key] = $array_to_organize[$guide_key];
        }
    }
    return $new_array;
}

/**
 * Takes an Array and transform in key1=value1&keyN=valueN. Is recursive.
 * @param array $data_array The data to convert to GET URL
 * @param array $guide_array Only the existing KEYS in this Array will be converted
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
 * This function will avoid the assignation and post unset of the index to rename
 * @param array $array
 * @param string $key_to_rename
 * @param string $new_key_name
 * @return boolean TRUE on success or FALSE on non exist key on the array
 */
function array_rename_key(&$array, $key_to_rename, $new_key_name) {
    if (array_key_exists($key_to_rename, $array)) {
        $array[$new_key_name] = $array[$key_to_rename];
        unset($array[$key_to_rename]);
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Converts Booleans vars to text
 * @param bool $bolean
 * @param string $true_value Text to convert on TRUE
 * @param string $false_value Text to convert on FALSE
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
 * @param string $name
 * @return String
 */
function get_magic_name($name) {
    if (app_session::on_session()) {
        return "magic_{$name}_secret";
    } else {
        trigger_error("Magic system REQUIRES the session system to be enabled and a session started", E_USER_ERROR);
    }
}

/**
 * Uses the PHP Session system to generate and stores the MAGIC VALUE by $name
 * @param string $name This name HAVE TO BE used to check the form on the receiver script.
 * @return String Magic value to be used on FORM
 */
function set_magic_value($name) {
    if (app_session::on_session()) {
        $secret = md5($name . microtime(TRUE));
        $_SESSION[get_magic_name($name)] = $secret;
        $client_magic = md5(K1MAGIC::get_value() . $secret);
        return $client_magic;
    } else {
        trigger_error("Magic system REQUIRES the session system to be enabled and a session started", E_USER_ERROR);
    }
}

/**
 * Check a incomming MAGIC VALUE 
 * @param string $name The name with it was stored
 * @param string $value_to_check Received var
 * @return boolean
 */
function check_magic_value($name, $value_to_check) {
    if (app_session::on_session()) {
        if ($value_to_check == "") {
            die("The magic value never can be empty!");
        } else {
            if (isset($_SESSION[get_magic_name($name)])) {
                $secret = $_SESSION[get_magic_name($name)];
                $client_magic = md5(K1MAGIC::get_value() . $secret);
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
 * Save a var to the selected method
 * @param miexd $var_to_save
 * @param string $save_name
 * @param string $method
 * @return boolean
 */
function serialize_var($var_to_save, $save_name, $method = "session") {
    if (!is_string($save_name) || empty($save_name)) {
        die(__FUNCTION__ . " save_name should be an non empty string");
    }
    if ($method == "session") {
        $_SESSION['serialized_vars'][$save_name] = $var_to_save;
    }
    return TRUE;
}

/**
 * Load the saved_name var from selected method
 * @param string $saved_name
 * @param string $method
 * @return boolean
 */
function unserialize_var($saved_name, $method = "session") {
    if (!is_string($saved_name) || empty($saved_name)) {
        die(__FUNCTION__ . " saved_name should be an non empty string");
    }
    $saved_vars = array();
    if ($method == "session") {
        if (isset($_SESSION['serialized_vars'][$saved_name])) {
            $saved_vars = $_SESSION['serialized_vars'][$saved_name];
        } else {
            $saved_vars = FALSE;
        }
    }
    return $saved_vars;
}

function unset_serialize_var($saved_name, $method = "session") {
    if (!is_string($saved_name) || empty($saved_name)) {
        die(__FUNCTION__ . " saved_name should be an non empty string");
    }
    if (isset($_SESSION['serialized_vars'][$saved_name])) {
        unset($_SESSION['serialized_vars'][$saved_name]);
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Checks if the $email var is a valid email by regular expressions.
 * @param string $email
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
 * @param string $xml
 * @param string $append Any string to append to the converted string... but has no logic -.-
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

/**
 * Explode an Array with php function explode() two times with the 2 delimiters
 * @param string $delimiter1
 * @param string $delimiter2
 * @param string $string
 * @return array always return an Array, empty if the string is empty or invalid. Normal Array if atleast could find the first delimiter.
 */
function explode_with_2_delimiters($delimiter1, $delimiter2, $string, $offset = 0) {
    if (!is_string($string)) {
        return [];
    }
    if ($offset > 0) {
        $string = substr($string, $offset);
    }

    $first_explode_array = [];
    $second_explode_array = [];
    if ($string != '') {
        $first_explode_array = explode($delimiter1, $string);
        foreach ($first_explode_array as $index => $var) {
            if (strstr($var, $delimiter2) !== FALSE) {
                list($key, $value) = explode($delimiter2, $var);
                $second_explode_array[$key] = $value;
            } else {
                $second_explode_array[$var] = '';
            }
        }
    }
    return $second_explode_array;
}

function get_http_protocol() {
    $isSecure = false;
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
        $isSecure = true;
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
        $isSecure = true;
    }
    $REQUEST_PROTOCOL = $isSecure ? 'https' : 'http';
    return $REQUEST_PROTOCOL;
}

/**
 * @return array
 */
function get_last_week_date_range() {
    $previous_week = strtotime("-1 week +1 day");

    $start_week = strtotime("last sunday midnight", $previous_week);
    $end_week = strtotime("next saturday", $start_week);

    $date_range[] = date("Y-m-d", $start_week);
    $date_range[] = date("Y-m-d", $end_week);

    return $date_range;
}

/**
 * @return array
 */
function get_current_week_date_range() {
    $d = strtotime("today");
    $start_week = strtotime("last sunday midnight", $d);
    $end_week = strtotime("next saturday", $d);

    $date_range[] = date("Y-m-d", $start_week);
    $date_range[] = date("Y-m-d", $end_week);

    return $date_range;
}

/**
 * @return array
 */
function get_next_week_date_range() {
    $d = strtotime("+1 week -1 day");
    $start_week = strtotime("last sunday midnight", $d);
    $end_week = strtotime("next saturday", $d);

    $date_range[] = date("Y-m-d", $start_week);
    $date_range[] = date("Y-m-d", $end_week);

    return $date_range;
}
