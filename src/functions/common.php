<?php

/**
 * k1.lib Common Functions
 *
 * General-purpose utility functions for common development tasks including
 * array manipulation, data serialization, validation, and web utilities.
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib;

/**
 * Recursively include PHP files from a directory.
 *
 * Scans a directory and requires all PHP files found, excluding hidden files,
 * index.php, and files prefixed with underscores. Directories are recursed into.
 *
 * @param string $path_to_explore Directory path to scan for PHP files
 * @param array $prefix_to_exclude Array of prefixes to exclude (default: ['.', '..', '__'])
 * @return void
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
 * Common utility functions for k1.lib applications.
 *
 * Base functions for typical development projects and other k1lib packages.
 * Includes array manipulation, data serialization, validation, and web utilities.
 *
 * @author Alejandro Trujillo J. <https://github.com/j0hnd03>
 * @license Apache-2.0
 * @package k1lib\common
 */

namespace k1lib\common;

use k1lib\K1MAGIC;
use k1lib\session\app_session;
use Ramsey\Uuid\DegradedUuid;

/**
 * Checks if the application is running within the k1lib framework.
 *
 * @deprecated This function is deprecated and should not be used.
 * @return bool Always returns false
 */
function check_on_k1lib() {
    d(__FUNCTION__ . " do not use me more!");
}

/**
 * Recursively convert an array to a guide array with keys as values.
 *
 * Takes an array and creates a new array where each key becomes a value.
 * For nested arrays, the structure is preserved recursively.
 *
 * @param array $data_array Array to convert to guide array
 * @return array Guide array with keys as values
 */
function make_guide_array($data_array): array {
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
 * Filter an array to only include keys that exist in the guide array.
 *
 * Returns a new array containing only the keys from $array_to_clean that
 * also exist in $guide_array, preserving original values.
 *
 * @param array $array_to_clean Array to filter
 * @param array $guide_array Array whose keys define which keys to keep
 * @return array Filtered array
 */
function clean_array_with_guide($array_to_clean, $guide_array): array {
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

/**
 * Reorganize an array according to the key order in a guide array.
 *
 * Creates a new array with keys ordered according to $guide_array,
 * including only keys that exist in both arrays.
 *
 * @param array $array_to_organize Array to reorganize
 * @param array $guide_array Array whose keys define the desired key order
 * @return array Reorganized array
 */
function organize_array_with_guide($array_to_organize, $guide_array): array {
    $new_array = [];
    foreach ($guide_array as $guide_key => $no_use) {
        if (isset($array_to_organize[$guide_key])) {
            $new_array[$guide_key] = $array_to_organize[$guide_key];
        }
    }
    return $new_array;
}

/**
 * Convert an array to URL GET parameters string.
 *
 * Recursively transforms an array into a URL-encoded query string.
 * Supports nested arrays and optional JSON encoding.
 *
 * @param array $data_array The data to convert to URL parameters
 * @param array|bool $guide_array Only keys existing in this array will be converted (default: all keys)
 * @param bool $use_json Whether to use JSON encoding for nested arrays (default: false)
 * @param string $upper_name Parent key name for nested arrays (internal use)
 * @return string URL-encoded query string
 */
function array_to_url_parameters($data_array, $guide_array = FALSE, $use_json = FALSE, $upper_name = ""): string {
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
 * Rename a key in an array without losing the value.
 *
 * Creates a new key with the same value and removes the old key.
 *
 * @param array &$array Array to modify (passed by reference)
 * @param string $key_to_rename Key to rename
 * @param string $new_key_name New name for the key
 * @return bool True on success, false if key doesn't exist
 */
function array_rename_key(&$array, $key_to_rename, $new_key_name): bool {
    if (array_key_exists($key_to_rename, $array)) {
        $array[$new_key_name] = $array[$key_to_rename];
        unset($array[$key_to_rename]);
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Convert a boolean value to a text string.
 *
 * @param bool $boolean Value to convert
 * @param string $true_value Text to return when true (default: "Si")
 * @param string $false_value Text to return when false (default: "No")
 * @return string The appropriate text value based on boolean input
 */
function bolean_to_string($bolean, $true_value = "Si", $false_value = "No"): string {
    if ($bolean) {
        return $true_value;
    } else {
        return $false_value;
    }
}

/**
 * Get the qualified magic name for session-based form validation.
 *
 * Generates a session key name combining the magic prefix and provided name.
 * Requires an active session to be meaningful.
 *
 * @param string $name Base name for the magic value
 * @return string Qualified magic name (format: magic_{name}_secret)
 * @throws \Error If session is not enabled
 */
function get_magic_name($name): string {
    if (app_session::on_session()) {
        return "magic_{$name}_secret";
    } else {
        trigger_error("Magic system REQUIRES the session system to be enabled and a session started", E_USER_ERROR);
    }
}

/**
 * Generate and store a magic value for form validation.
 *
 * Creates a secure magic value using the application secret and stores
 * it in the session for later validation. Used for form security.
 *
 * @param string $name Unique identifier for this magic value (must match on validation)
 * @return string Magic value to include in forms
 * @throws \Error If session is not enabled
 */
function set_magic_value($name): string {
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
 * Validate a magic value submitted from a form.
 *
 * Compares the submitted magic value against the stored session value
 * using the application secret for comparison.
 *
 * @param string $name Name identifier used when the magic value was set
 * @param string $value_to_check Magic value received from form submission
 * @return string|false The validated magic value on success, false on failure
 * @throws \Error If session is not enabled
 */
function check_magic_value($name, $value_to_check): string|false {
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
 * Save a variable to session storage for later retrieval.
 *
 * @param mixed $var_to_save Variable to store
 * @param string $save_name Unique identifier for the stored variable
 * @param string $method Storage method (default: "session")
 * @return bool True on success
 * @throws \Error If save_name is empty or not a string
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
 * Load a previously saved variable from storage.
 *
 * @param string $saved_name Identifier used when the variable was saved
 * @param string $method Storage method (default: "session")
 * @return mixed|false The stored variable value, or false if not found
 * @throws \Error If saved_name is empty or not a string
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

/**
 * Remove a serialized variable from storage.
 *
 * @param string $saved_name Identifier of the variable to remove
 * @param string $method Storage method (default: "session")
 * @return bool True on success, false if variable didn't exist
 * @throws \Error If saved_name is empty or not a string
 */
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
 * Validate an email address using regular expressions.
 *
 * @param string $email Email address to validate
 * @return bool True if valid email format, false otherwise
 */
function check_email_address($email): bool {
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
 * Convert an XML string to JSON format.
 *
 * @param string $xml XML string to convert
 * @param string $append Optional string to append to the XML before conversion
 * @return string JSON-encoded string representation of the XML
 */
function XmlToJson($xml, $append = ""): string {
    $fileContents = $xml;
    $fileContents = str_replace(array("\n", "\r", "\t"), '', $fileContents);
    $fileContents = trim(str_replace('"', "'", $fileContents));
    $simpleXml = simplexml_load_string($fileContents);
    $simpleXml->append = htmlentities($append);
    $json = json_encode($simpleXml);
    return $json;
}

/**
 * Extract the file extension from a filename or URL.
 *
 * @param string $file_name Filename or URL to extract extension from
 * @param bool $to_lower Whether to return the extension in lowercase (default: false)
 * @return string|false File extension without the dot, or false if no extension found
 * @throws \Error If file_name is not a string
 */
function get_file_extension($file_name, $to_lower = FALSE): string|false {
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
 * Explode a string using two different delimiters.
 *
 * Performs two sequential explode operations to parse delimited data.
 * Returns an associative array where the first delimiter separates keys
 * and the second delimiter separates key/value pairs.
 *
 * @param string $delimiter1 First delimiter (used to split into key/value pairs)
 * @param string $delimiter2 Second delimiter (separates key from value within pairs)
 * @param string $string String to parse
 * @param int $offset Starting offset position (default: 0)
 * @return array Associative array of parsed key/value pairs
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

/**
 * Determine the HTTP protocol (HTTP or HTTPS) used for the current request.
 *
 * Checks server variables including HTTPS headers and X-Forwarded-Proto
 * to determine if the request was made over a secure connection.
 *
 * @return string Either "http" or "https"
 */
function get_http_protocol(): string {
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
 * Get the date range (start and end dates) for the previous week.
 *
 * @return array Array with two elements: [0] => start date (Y-m-d), [1] => end date (Y-m-d)
 */
function get_last_week_date_range(): array {
    $previous_week = strtotime("-1 week +1 day");

    $start_week = strtotime("last sunday midnight", $previous_week);
    $end_week = strtotime("next saturday", $start_week);

    $date_range[] = date("Y-m-d", $start_week);
    $date_range[] = date("Y-m-d", $end_week);

    return $date_range;
}

/**
 * Get the date range (start and end dates) for the current week.
 *
 * @return array Array with two elements: [0] => start date (Y-m-d), [1] => end date (Y-m-d)
 */
function get_current_week_date_range(): array {
    $d = strtotime("today");
    $start_week = strtotime("last sunday midnight", $d);
    $end_week = strtotime("next saturday", $d);

    $date_range[] = date("Y-m-d", $start_week);
    $date_range[] = date("Y-m-d", $end_week);

    return $date_range;
}

/**
 * Get the date range (start and end dates) for the next week.
 *
 * @return array Array with two elements: [0] => start date (Y-m-d), [1] => end date (Y-m-d)
 */
function get_next_week_date_range(): array {
    $d = strtotime("+1 week -1 day");
    $start_week = strtotime("last sunday midnight", $d);
    $end_week = strtotime("next saturday", $d);

    $date_range[] = date("Y-m-d", $start_week);
    $date_range[] = date("Y-m-d", $end_week);

    return $date_range;
}
