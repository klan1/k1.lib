<?php

/** DIST ALL IN ONE CODE **/
                
// creation date: 2023-09-26 17:24:16

// ./src/_common/_global.php


/**
 * Global functions, K1.lib.
 * 
 * Common functions needed on the main \ namespace.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package global
 */
/**
 * Function for debug on screen any kind of data
 * @param mixed $var
 * @param boolean $var_dump
 */

namespace k1lib;

function d($var, $var_dump = FALSE, $trigger_notice = TRUE) {
//    trigger_error(__FILE__, E_USER_ERROR);
    $msg = ( ($var_dump) ? var_export($var, TRUE) : print_r($var, TRUE) );
    if ($trigger_notice) {
        trigger_error($msg, E_USER_NOTICE);
    }
    if (class_exists('\k1lib\html\DOM')) {
        if (\k1lib\html\DOM::is_started()) {
            $pre = new \k1lib\html\pre($msg);
            if (!empty(\k1lib\html\DOM::html()->body()->get_element_by_id("k1lib-output"))) {
                \k1lib\notifications\on_DOM::queue_title('Message from K1.lib', 'warning');
                \k1lib\notifications\on_DOM::queue_mesasage($pre->generate(), 'warning');
            } else {
                echo $pre->generate();
            }
        } else {
            echo $msg . "\n";
        }
    } else {
        echo $msg . "\n";
        ;
    }
}

// ./src/_common/classes.php


namespace k1lib;

class K1MAGIC {

    /**
     *
     * @var string
     */
    static public $value = "98148ef8279164d12b65ec8c9ba76c7e";

    /**
     * 
     * @return string
     */
    public static function get_value() {
        return self::$value;
    }

    /**
     * Set the value, please use an MD5 value here to increase security.
     * @param string
     */
    public static function set_value($value) {
        self::$value = $value;
    }

}

class LANG {

    /**
     *
     * @var string
     */
    static $lang = "en";

    /**
     * 
     * @return string
     */
    public static function get_lang() {
        return self::$lang;
    }

    /**
     * 
     * @param string $lang
     */
    public static function set_lang($lang) {
        self::$lang = $lang;
    }

}

class PROFILER {

    static private $init = 0;
    static private $finish = 0;
    static private $run_time = 0;

    static function start() {
        self::$init = microtime(TRUE);
    }

    static function end() {
        self::$finish = microtime(TRUE);
        self::$run_time = round((microtime(TRUE) - self::$init), 5);
        return self::$run_time;
    }

}

// ./src/_common/functions.php


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
    d(__FUNCTION__ . " do not use me more!");
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
        $client_magic = md5(\k1lib\K1MAGIC::get_value() . $secret);
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
                $client_magic = md5(\k1lib\K1MAGIC::get_value() . $secret);
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

// ./src/api/api_class.php


/**
 * API Class, K1.lib.
 * 
 * Class for define API REST
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 0.1
 * @package api
 */

namespace k1lib\api;

use \k1lib\urlrewrite\url;
use \k1lib\crudlexs\class_db_table;

const K1LIB_API_USE_MAGIC_HEADER = TRUE;
const K1LIB_API_DISABLE_MAGIC_HEADER = FALSE;
const K1LIB_API_USE_TOKEN = TRUE;
const K1LIB_API_DISABLE_TOKEN = FALSE;

class api {

    protected $allow_methods = 'POST,GET,PUT,DELETE';
    //execution time measurement
    protected float $start_time;
    protected float $end_time;

    // DB conection object

    /**
     * @var \k1lib\db\PDO_k1
     */
    protected $db = NULL;
    protected $do_debug = FALSE;

    /**
     *
     * @var \k1lib\crudlexs\class_db_table
     */
    protected $debug_table = FALSE;
    // MAGIC HEADER
    protected $magic_header = NULL;
    protected $use_magic_header = FALSE;
    // TOKEN
    protected $token = NULL;
    protected $use_token = TRUE;
    // INPUT
    protected $request_method = NULL;
    protected $input_data = NULL;
    // OUTPUT

    /**
     * @var boolean
     */
    protected $do_send_response = TRUE;

    /**
     * @var boolean
     */
    protected $read_input_return_array = TRUE;
    /*
     * @var array
     */
    protected $reponse_data = [];

    public function __construct($use_token = FALSE, $use_magic_header = FALSE) {
        // Start clock time in seconds
        $this->start_time = microtime(true);

        /**
         * OUT PUT BUFFER START
         */
        if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            ob_start('ob_gzhandler');
        } else {
            ob_start();
        }

        header('Access-Control-Allow-Methods: ' . $this->allow_methods);
        header("Access-Control-Allow-Origin: *");
        header("Access-Control-Allow-Headers: X-K1app-Api-Token,X-K1app-Magic-header,X-K1app-Api-Request-mode");

        /**
         * API config
         */
        $this->use_magic_header = $use_magic_header;
        $this->use_token = $use_token;
        $this->request_method = $_SERVER['REQUEST_METHOD'];
    }

    public function exec($send_response = TRUE) {
        $this->do_send_response = $send_response;
        // Clear all possible previous output buffer
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                return $this->get();
            case 'POST':
                return $this->post();
            case 'PUT':
                return $this->put();
            case 'DELETE':
                return $this->delete();
        }
    }

    protected function read_input() {
        $http_headers = getallheaders();
        /**
         * TODO: implements token
         */
        if ($this->use_token) {
            $this->api_token = $http_headers['X-K1app-Api-Token'];
            if (empty($this->api_token)) {
                $this->send_response(401, [$http_headers], ['message' => 'Invalid token']);
                return FALSE;
            }
        }
        /**
         * TODO: implements the magic header
         */
        if ($this->use_magic_header) {
            $this->magic_header = $http_headers['X-K1app-Magic-Header'];
            if (empty($this->magic_header)) {
                $this->send_response(401, [$http_headers], ['message' => 'x-magic-value is not fount with the correct magic value']);
                return FALSE;
            }
        }
        // IF EVERYTHING ITS OK, THEN LOAD THE DATA
        $input_data = json_decode(file_get_contents("php://input"), $this->read_input_return_array);
        if ($input_data) {
            $this->input_data = $input_data;
        } else {
            $this->input_data = NULL;
        }
        if ($this->do_debug) {
            header('X-k1app-using-debug: yes');
            $error = null;
            $this->debug_table->insert_data(
                    [
                        'api_node' => $_SERVER['QUERY_STRING'],
                        'data' => $_SERVER['REQUEST_METHOD'] . ':' . file_get_contents("php://input"),
                        'direction' => 'in',
                        'user_ip' => $_SERVER['REMOTE_ADDR'],
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'http_headers' => print_r(getallheaders(), TRUE),
                    ]
                    , $error);
            if ($error) {
                header('X-k1app-debug-error: ' . $error);
            }
        }
    }

    public function get_input() {
        return $this->input_data;
    }

    public function send_response($code, $data, $extra = null) {
        http_response_code($code);
        /**
         * TODO: make selectable the allow methods
         */
        header("Content-Type: application/json; charset=utf-8");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: {now} GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");

        /**
         * HTTP STATUS OUT SELECTION
         */
        $local_response_data = [];
        if ($code >= 200 && $code <= 299) {
            $local_response_data['status'] = 'success';
        } elseif ($code >= 400) {
            $this->send_response = TRUE;
            $local_response_data['status'] = 'error';
        }
        $local_response_data['data'] = $data;
        if ($extra) {
            $local_response_data['extra'] = $extra;
        }

        /**
         * OUTPUT BUFFER
         */
        $php_out = ob_get_contents();
        ob_end_clean();
        if (!empty($php_out)) {
            $this->reponse_data['phpOut'] = $php_out;
        }

        /**
         * FINAL API OUTPUT
         */
        // Calculate script execution time
        $local_response_data['system'] = ['runtime' => round((microtime(true) - $this->start_time), 4)];

        $response_array = array_merge($local_response_data, $this->reponse_data);

        if ($this->do_send_response) {
            $response = json_encode($response_array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
            if ($this->do_debug) {
                header('X-k1app-using-debug: yes');
                $error = null;
                $this->debug_table->insert_data(
                        [
                            'api_node' => url::get_this_url(),
                            'data' => $response,
                            'direction' => 'out',
                            'user_ip' => $_SERVER['REMOTE_ADDR'],
                            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        ]
                        , $error);
                if ($error) {
                    header('X-k1app-debug-error: ' . $error);
                }
            }
            echo $response;
            die();
        } else {
            return $response_array;
        }
    }

    public function get() {
        $this->read_input();
    }

    public function post() {
        $this->read_input();
    }

    public function put() {
        $this->read_input();
    }

    public function delete() {
        $this->read_input();
    }

    protected function check_input($array_to_check) {
        return $array_to_check;
    }

    function set_db(\k1lib\db\PDO_k1 $db) {
        $this->db = $db;
    }

    protected function get_url_value() {
        return url::set_url_rewrite_var(url::get_url_level_count(), 'level_' . url::get_url_level_count());
    }

    public function do_debug($table_name = 'debug_log') {
        if ($this->db) {
            $this->do_debug = true;
            $this->debug_table = new class_db_table($this->db, $table_name);
        }
    }

    function set_do_send_response($send_response) {
        $this->send_response = $send_response;
    }

}

// ./src/api/api_crud_class.php


namespace k1app\api\auth;

use \k1lib\api\api;
use \k1lib\urlrewrite\url;
use \k1lib\crudlexs\class_db_table;
use \k1lib\api\api_model;

class api_crud extends api {

    /**
     * @var \k1lib\api\api_model
     */
    private $table_model;

    /**
     *
     * @var \k1lib\crudlexs\class_db_table
     */
    public $db_table;
    private $db_table_name;
    private $db_table_keys_fields;
    private $data_key = NULL;
    private $data_keys_array = [];
    private $keyfield_data_array = [];
    private $controler_action = NULL;
    private $get_list_page = 1;
    private $get_list_page_size = 20;
    private $get_query_filter = [];
    private $orderby = [];

    /**
     * @var array
     */
    private $register_response_data = [];

    function __construct($use_token = FALSE, $use_magic_header = FALSE) {
        parent::__construct($use_token, $use_magic_header);

        /**
         * POSSIBLE GETS
         */
        if (array_key_exists('page', $_GET)) {
            $this->get_list_page = \k1lib\forms\check_single_incomming_var($_GET['page']);
        }
        if (array_key_exists('page-size', $_GET)) {
            $this->get_list_page_size = \k1lib\forms\check_single_incomming_var($_GET['page-size']);
        }
        if (array_key_exists('get-query-filter', $_GET)) {
            $this->get_query_filter = json_decode(\k1lib\forms\check_single_incomming_var($_GET['get-query-filter'], false, true), TRUE);
        }
        if (array_key_exists('keys-fields', $_GET)) {
            $this->db_table_keys_fields = explode(',', \k1lib\forms\check_single_incomming_var($_GET['keys-fields']));
        }
        if (array_key_exists('order-by', $_GET)) {
            $this->orderby = \k1lib\forms\check_single_incomming_var($_GET['order-by']);
        }
        /**
         * CRUD URL MANAGMENT
         */
        $possible_key = url::set_url_rewrite_var(url::get_url_level_count(), 'possible-id', FALSE);
        $possible_action = url::set_url_rewrite_var(url::get_url_level_count(), 'possible-action', FALSE);

        if (empty($possible_action) && empty($possible_key)) {
            $this->controler_action = 'get-all';
        } else {
            // IF THERE IS ONLY $possible_key
            if (!empty($possible_key)) {
                if (empty($possible_action)) {
                    $this->data_key = $possible_key;
                    $this->controler_action = 'get-one';
                } else {
                    $this->data_key = $possible_key;
                    $this->controler_action = $possible_action;
                }
            }
        }
    }

    function assing_keyfields_data() {
        $this->data_keys_array = explode('-', $this->data_key);
        $this->keyfield_data_array = [];
        echo (count($this->data_keys_array) . '===' . count($this->db_table_keys_fields));
        if (count($this->data_keys_array) === count($this->db_table_keys_fields)) {
            if (!empty($this->data_keys_array)) {
                foreach ($this->data_keys_array as $key => $value) {
                    if (!empty($value)) {
                        $this->keyfield_data_array[$this->db_table_keys_fields[$key]] = $value;
                    }
                }
            }
        } else {
            $this->send_response(500, $this->input_data, ['message' => 'Keys-Values mismatch', 'mode' => 'get', 'token' => $this->token, 'magic_header' => $this->magic_header]);
        }
    }

    function get() {
        parent::get();
        $this->assing_keyfields_data();
        switch ($this->controler_action) {
            case 'get-one':
                $table_data = $this->table_model->get_data($this->keyfield_data_array);
                $extra_data = ['data-type' => 'single', $this->keyfield_data_array];
                if ($this->do_send_response) {
                    $this->send_response(200, $table_data, $extra_data);
                } else {
                    return ['data' => $table_data, 'extra' => $extra_data];
                }
                break;
            case 'get-all':
                if (($this->get_list_page - 1) > 1) {
                    $previuos_page_num = $this->get_list_page - 1;
                    $previuos_page = url::do_url(url::get_this_url(), ['page' => $previuos_page_num, 'page_size' => $this->get_list_page_size]);
                } else {
                    $previuos_page_num = NULL;
                    $previuos_page = NULL;
                }
                $next_page_num = $this->get_list_page + 1;
                $next_page = url::do_url(url::get_this_url(), ['page' => $next_page_num, 'page_size' => $this->get_list_page_size]);
                $query_filter = array_merge($this->keyfield_data_array, $this->get_query_filter);
                $table_data = $this->table_model->get_all_data($this->get_list_page, $this->get_list_page_size, $query_filter, $this->orderby);
                $extra_data = [
                    'data-type' => 'multiple',
                    'pagination_url' => ['previos' => $previuos_page, 'next' => $next_page],
                    'pagination_data' => ['previos_page' => $previuos_page_num, 'next_page' => $next_page_num, 'page_size' => $this->get_list_page_size],
                    'keyfield_data_array' => $this->keyfield_data_array,
                    'order-by' => $this->orderby,
                ];
                if ($this->do_send_response) {
                    $this->send_response(200, $table_data, $extra_data);
                } else {
                    return ['data' => $table_data, 'extra' => $extra_data];
                }
            default :
                $this->send_response(500, ['message' => 'Action not implemented'], $this->controler_action);
                break;
        }
    }

    function set_db_table_keys_fields($db_table_keys_fields) {
        if (!empty($db_table_keys_fields)) {
            if (!array_key_exists('keys-fields', $_GET)) {
                $this->db_table_keys_fields = $db_table_keys_fields;
            }
        }
    }

    /**
     * {
     *  'device': {},
     *  'phonenumber': $phone,
     *  'persona': {},
     *  'password': $password
     * }
     */
    function post() {
        parent::post();
        $this->assing_keyfields_data();
        $this->table_model->assing_data_to_properties($this->input_data, TRUE);
        var_dump($this->input_data);

        $update_result = $this->table_model->update_data($this->keyfield_data_array);
        if ($update_result) {
            $this->send_response(200, ['operation' => 'update'], $this->table_model->get_data());
        } else {
            $this->send_response(500, $this->input_data, ['message' => 'Sin implementar aun', 'mode' => 'post', 'token' => $this->token, 'magic_header' => $this->magic_header]);
        }
    }

    function put() {
        parent::put();
        $this->assing_keyfields_data();
        $this->table_model->assing_data_to_properties($this->input_data, TRUE);
        var_dump($this->input_data);

        $insert_result = $this->table_model->insert_data($this->keyfield_data_array);
//        $inserted_id = $this->db->lastInsertId();

        if ($insert_result) {
            $this->send_response(200, ['operation' => 'insert', 'id' => $insert_result]);
        } else {
            $this->send_response(500, $this->input_data, ['message' => 'Insert error', 'mode' => 'put', 'error' => $this->table_model->get_errors(), 'token' => $this->token, 'magic_header' => $this->magic_header]);
        }
    }

    function delete() {
        parent::delete();
        $this->assing_keyfields_data();
        $this->table_model->assing_data_to_properties($this->input_data, TRUE);
        var_dump($this->input_data);

        $delete_result = $this->table_model->delete_data($this->keyfield_data_array);
//        $inserted_id = $this->db->lastInsertId();

        if ($delete_result >= 0 && $delete_result !== false) {
            $this->send_response(200, ['operation' => 'delete', 'id' => $this->keyfield_data_array, 'records' => $delete_result]);
        } else {
            $this->send_response(500, $this->input_data, ['message' => 'Delete error', 'mode' => 'delete', '$this->keyfield_data_array' => $this->keyfield_data_array, '$delete_result' => $delete_result, 'error' => $this->table_model->get_errors(), 'token' => $this->token, 'magic_header' => $this->magic_header]);
        }
    }

    function set_db_table_name($db_table_name) {
        $this->db_table_name = $db_table_name;
        $this->db_table = new class_db_table($this->db, $this->db_table_name);
//        echo " | set_db_table_name: " . print_r($this->input_data, TRUE) . " | ";
//        $this->table_model = new api_model($this->db_table, $this->input_data);
        $this->table_model = new api_model($this->db_table);
    }

    function exec($send_response = TRUE) {
        return parent::exec($send_response);
    }

}

// ./src/api/api_model.php


/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * K1 API model - fast api for all
 *
 * PHP version 7.1 - 7.2
 *
 * LICENSE:  
 *
 * @author          Alejandro Trujillo J. <alejo@klan1.com>
 * @copyright       2015-2019 Klan1 Network SAS
 * @license         Apache 2.0
 * @version         1.0
 * @since           File available since Release 0.8
 */
/*
 * App run time vars
 */

namespace k1lib\api;

use \k1lib\crudlexs\class_db_table;

class api_model {

    /**
     * @var \k1lib\crudlexs\class_db_table
     */
    private $db_table;
    private $errors = FALSE;

    function __construct(class_db_table $db_table = NULL, $data = NULL) {
        if (!is_null($db_table)) {
            $this->db_table = $db_table;
        }
        $this->assing_data_to_properties($data);
    }

    function assing_data_to_properties($data, $create_model_properties = FALSE) {
        if (!empty($data)) {
            if ($create_model_properties) {
                foreach ($data as $key => $value) {
                    $this->{$key} = $value;
                }
            } else {
                foreach ($this as $propName => $propVale) {
                    if (is_object($this->{$propName})) {
                        continue;
                    }
                    if (is_string($data)) {
                        $this->send_response(500, $data, 'No se llamado invalido de "assing_data_to_properties" en: ' . __CLASS__);
                    }
                    if (is_array($data)) {
                        if (key_exists($propName, $data)) {
                            $this->{$propName} = $data[$propName];
                        }
                    }
                    if (is_object($data)) {
                        if (property_exists($data, $propName)) {
                            $this->{$propName} = $data->{$propName};
                        }
                    }
                }
            }
        }
    }

    function get_data(array $custom_key_array = []) {
        if (empty($custom_key_array)) {
            $data_array = $this->get_data_from_params();
            $data_keys = \k1lib\sql\get_keys_array_from_row_data($data_array, $this->db_table->get_db_table_config());
            $this->db_table->set_query_filter($data_keys, TRUE);
        } else {
            $this->db_table->set_query_filter($custom_key_array, TRUE);
        }
        $data = $this->db_table->get_data(FALSE);
        $this->assing_data_to_properties($data);
        return $data;
    }

    function get_all_data($page = 1, $page_size = 20, $query_filter = [], $orderby = []) {

        $offset = ($page - 1) * $page_size;
        $this->db_table->set_query_limit($offset, $page_size);

        if (!empty($query_filter)) {
            $this->db_table->set_query_filter($query_filter, TRUE, FALSE);
        }

        if (!empty($orderby)) {
            if (is_array($orderby)) {
                foreach ($orderby as $field => $sort) {
                    $this->db_table->set_order_by($field, $sort);
                }
            } else {
                $this->db_table->set_order_by($orderby);
            }
        }
        $data = $this->db_table->get_data(TRUE, FALSE);
        $this->assing_data_to_properties($data);
        return $data;
    }

    function insert_data() {
        $data_to_insert = $this->get_data_from_params();
        $sql_query = null;
        $result = $this->db_table->insert_data($data_to_insert, $this->errors, $sql_query);
        if ($this->errors) {
            print_r($data_to_insert);
            var_dump($this->errors);
            echo "SQL: $sql_query";
        }
        return $result;
    }

    function update_data($keyfields) {
        $data_to_update = $this->get_data_from_params();
        echo "data to update " . print_r($data_to_update, TRUE);
        $sql_query = null;
        $result = $this->db_table->update_data($data_to_update, $keyfields, $this->errors, $sql_query);
        echo "errors: {$this->errors}\n";
        echo "sql: $sql_query\n";
        if ($this->errors) {
            print_r($data_to_update);
            var_dump($this->errors);
            echo "SQL: $sql_query";
        }
        return $result;
    }

    function delete_data($keyfields) {
        echo "Data to delete :" . print_r($keyfields, TRUE);
        $sql_query = null;
        $result = $this->db_table->delete_data($keyfields);
        if ($result === false) {
            print_r($keyfields);
        }
        return $result;
    }

    function get_data_from_params() {
//        print_r($this->db_table->get_db_table_name());
        $table_config = $this->db_table->get_db_table_config();
        $real_data = [];
        foreach ($table_config as $field => $config) {
            if (property_exists($this, $field)) {
                echo " | $field - existe - ";
//                if (!empty($this->{$field})) {
                    $real_data[$field] = $this->{$field};
//                }
            } else {
                echo " | $field - NO existe - ";
            }
        }
        return $real_data;
    }

    function get_errors() {
        return $this->errors;
    }

}

// ./src/controllers/functions.php


/**
 * Controller related functions, K1.lib.
 * 
 * This are my controller use propose.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package controllers
 */

namespace k1lib\controllers;

use k1lib\html\DOM as DOM;

/**
 * Return the controller PATH for include it, we cant do it from the function by the var scope, so we do here some esential checks before the include
 * @global array $url_data url_rewrite data variable from K1FW
 * @param string $controller_name Just the file name 
 * @return string correct path to include the file name recived on $controller_name
 */
//function load_controller($controller_name, $query_auto_load = TRUE) {
function load_controller($controller_name, $controllers_path, $return_error = FALSE, $api_mode = FALSE) {
//    d($controllers_path);
//    $controller_query_file = FALSE;
    if (is_string($controller_name)) {
        // Try with subfolder scheme
        $controller_subfix = $controllers_path . "/{$controller_name}";

        $controller_to_load = $controller_subfix . ".php";
        if (file_exists($controller_to_load)) {
            return $controller_to_load;
        } else {
            // Try with single file scheme
            $controller_to_load = $controller_subfix . "/index.php";
            if (file_exists($controller_to_load)) {
                // QUERY Auto load
                return $controller_to_load;
            } else {
                if (!$return_error) {
                    if ($api_mode) {
                        $error = new \k1lib\api\api();
                        $error->send_response(400, ['message' => 'Not found: ' . $controller_name]);
                    } else {
                        error_404($controller_name);
                    }
                } else {
                    return NULL;
                }
//                \trigger_error("The controller '{$controller_name}' could not be found on '{$controllers_path}'", E_USER_ERROR);
//                return false;
            }
        }
    } else {
        \trigger_error("The controller name value only can be string", E_USER_ERROR);
        exit;
    }
}

function error_404($non_found_name) {
    http_response_code(404);
    header("Access-Control-Allow-Origin: *");
    DOM::start();
    DOM::html()->body()->append_h1('404 Not found');
    DOM::html()->body()->append_p('The controller file \'' . $non_found_name . '\' is not on path.');
    echo DOM::generate();
    trigger_error('App error fired', E_USER_NOTICE);
    exit;
}

function load_template($template_name, $path_to_use) {
    if (is_string($template_name)) {
        if ($template_to_load = template_exist($template_name, $path_to_use)) {
            return $template_to_load;
        } else {
            trigger_error("Template ($template_name) do not exist", E_USER_ERROR);
        }
    } else {
        trigger_error("The template names value only can be string", E_USER_ERROR);
    }
}

function template_exist($template_name, $path_to_use) {
    if (is_string($template_name)) {
        // Try with subfolder scheme
        $template_to_load = $path_to_use . "/{$template_name}.php";
        if (file_exists($template_to_load)) {
            return $template_to_load;
        } else {
            trigger_error("Template ($template_to_load) is not on disk", E_USER_ERROR);
        }
    }
    return FALSE;
}

// ./src/crudlexs/board_classes/_board_base.php


namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\html\DOM as DOM;
use k1lib\notifications\on_DOM as DOM_notification;

interface board_interface {

    public function start_board();

    public function exec_board();

    public function finish_board();
}

class board_base {

    /**
     * DB table main object
     * @var \k1lib\crudlexs\controller_base 
     */
    protected $controller_object;

    /**
     * @var \k1lib\html\div;
     */
    public $board_content_div;

    /**
     * @var boolean
     */
    protected $data_loaded = FALSE;

    /**
     * @var boolean
     */
    protected $is_enabled = FALSE;

    /**
     * @var boolean
     */
    protected $skip_form_action = FALSE;

    /**
     * @var string
     */
    protected $user_levels_allowed = NULL;

    /**
     * @var mixed 
     */
    protected $sql_action_result = NULL;

    /**
     * @var string
     */
    protected $show_rule_to_apply = NULL;

    /**
     * @var boolean
     */
    protected $apply_label_filter = TRUE;

    /**
     * @var boolean
     */
    protected $apply_field_label_filter = TRUE;

    /**
     * @var string
     */
    protected $button_div_id = "k1lib-crudlexs-buttons";

    /**
     * @var string
     */
    protected $notifications_div_id = "k1lib-output";

    /**
     * @var \k1lib\html\div
     */
    protected $button_div_tag;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        $this->controller_object = $controller_object;
        $this->board_content_div = new \k1lib\html\div("board-content");

        $this->user_levels_allowed = $user_levels_allowed;

        if (\k1lib\session\session_plain::is_enabled()) {
            if (!$this->check_user_level_access()) {
                $this->is_enabled = false;
            } else {
                $this->is_enabled = true;
            }
        }

        /**
         * Search util hack
         */
        $post_data_to_use = \k1lib\common\unserialize_var("post-data-to-use");
//        $post_data_table_config = \k1lib\common\unserialize_var("post-data-table-config");

        if (!empty($post_data_to_use)) {
//            $_POST = $post_data_to_use;
            $this->skip_form_action = TRUE;
//            \k1lib\common\unset_serialize_var("post-data-to-use");
//            \k1lib\common\unset_serialize_var("post-data-table-config");
        }
    }

    public function start_board() {
        if (!$this->is_enabled) {
            DOM_notification::queue_mesasage(board_base_strings::$error_board_disabled, "warning", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_strings::$alert_board);
            return FALSE;
        }
        $this->button_div_tag = $this->board_content_div->append_div($this->button_div_id);
        return TRUE;
    }

    public function exec_board() {
        
    }

    function get_is_enabled() {
        return $this->is_enabled;
    }

    function set_is_enabled($is_enabled) {
        if ($this->is_enabled) {
            $this->is_enabled = $is_enabled;
        }
    }

    public function set_board_name($board_name) {
        if (!empty($board_name)) {
            $head = DOM::html()->head();
            $current_html_title = $head->get_title();
            $head->set_title($current_html_title . " - " . $board_name);

            if (is_array($this->controller_object->html_title_tags)) {
                foreach ($this->controller_object->html_title_tags as $tag) {
                    $tag->set_value(" - {$board_name}", TRUE);
                }
            }
        }
    }

    function set_user_levels_allowed(array $user_levels_allowed_array) {
        $this->user_levels_allowed = $user_levels_allowed_array;
    }

    function add_user_level_allowed($user_level_allowed) {
        if (!empty($user_level_allowed) && is_string($user_level_allowed)) {
            $this->user_levels_allowed[] = $user_level_allowed;
        }
    }

    function check_user_level_access() {
        if (empty($this->user_levels_allowed)) {
            return TRUE;
        } else {
            if (empty(array_key_exists(\k1lib\session\session_plain::get_user_level(), array_flip($this->user_levels_allowed)))) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    public function get_show_rule_to_apply() {
        return $this->show_rule_to_apply;
    }

    public function set_show_rule_to_apply($show_rule_to_apply) {
        $this->show_rule_to_apply = $show_rule_to_apply;
    }

    public function get_apply_field_label_filter() {
        return $this->apply_field_label_filter;
    }

    public function set_apply_field_label_filter($apply_field_label_filter) {
        $this->apply_field_label_filter = $apply_field_label_filter;
    }

    public function get_apply_label_filter() {
        return $this->apply_label_filter;
    }

    public function set_apply_label_filter($apply_label_filter) {
        $this->apply_label_filter = $apply_label_filter;
    }

    public function get_sql_action_result() {
        return $this->sql_action_result;
    }

    public function get_button_div_id() {
        return $this->button_div_id;
    }

    public function set_button_div_id($button_div_id) {
        $this->button_div_id = $button_div_id;
    }

    /**
     * @return \k1lib\html\div
     */
    public function button_div_tag() {
        return $this->button_div_tag;
    }

    public function set_button_div_tag(\k1lib\html\div $button_div_tag) {
        $this->button_div_tag = $button_div_tag;
    }

    /**
     * @return \k1lib\html\div
     */
    public function board_content_div() {
        return $this->board_content_div;
    }

    public function set_board_content_div(\k1lib\html\div $board_content_div) {
        $this->board_content_div = $board_content_div;
    }

    public function get_notifications_div_id() {
        return $this->notifications_div_id;
    }

    public function set_notifications_div_id($notifications_div_id) {
        $this->notifications_div_id = $notifications_div_id;
    }

    function set_skip_form_action($skip_form_action) {
        $this->skip_form_action = $skip_form_action;
    }

}

// ./src/crudlexs/board_classes/create.php


namespace k1lib\crudlexs;

use k1lib\urlrewrite\url as url;
use k1lib\html\DOM as DOM;
use k1lib\notifications\on_DOM as DOM_notification;

class board_create extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\creating
     */
    public $create_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-create";
            $this->create_object = new \k1lib\crudlexs\creating($this->controller_object->db_table, FALSE);
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }

        /**
         * IFRAME for KF tool
         */
//        $fk_iframe = new \k1lib\html\iframe('', 'utility-iframe', "fk-iframe");
//        DOM::html()->body()->content()->append_child_tail($fk_iframe);

        $this->create_object->enable_foundation_form_check();

        if ($this->create_object->get_state()) {
            $this->create_object->set_back_url(\k1lib\urlrewrite\get_back_url());

            $this->create_object->set_do_table_field_name_encrypt(TRUE);
            $this->controller_object->db_table->set_db_table_show_rule($this->show_rule_to_apply);
            $this->data_loaded = $this->create_object->load_db_table_data(TRUE);
            $this->create_object->catch_post_data();

            return $this->board_content_div;
        } else {
            DOM_notification::queue_mesasage(board_base_labels::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_labels::$error_mysql);
            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->data_loaded) {

            if ($this->create_object->get_post_data_catched()) {
                $this->create_object->put_post_data_on_table_data();
                if (!$this->skip_form_action) {
                    if ($this->create_object->do_post_data_validation()) {
                        $this->sql_action_result = $this->create_object->do_insert();
                    } else {
                        DOM_notification::queue_mesasage(board_create_strings::$error_form, "warning", $this->notifications_div_id);
                        DOM_notification::queue_title(board_base_strings::$alert_board);
                    }
                }
            }
            if (empty($this->sql_action_result)) {
                if ($this->apply_label_filter) {
                    $this->create_object->apply_label_filter();
                }
                $this->create_object->insert_inputs_on_data_row();

                $create_content_div = $this->create_object->do_html_object();
                $create_content_div->append_to($this->board_content_div);

                return $this->board_content_div;
            }
        } else {
            DOM_notification::queue_mesasage(board_create_strings::$error_no_blank_data, "alert", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_strings::$alert_board);
            $this->create_object->make_invalid();
            $this->is_enabled = FALSE;
            return FALSE;
        }
    }

    public function finish_board($do_redirect = TRUE, $custom_redirect = FALSE) {
        if ($this->sql_action_result !== NULL) {
            if ($custom_redirect === FALSE) {

                if (isset($_GET['back-url'])) {
                    $get_params = [];
                    $url_to_go = \k1lib\urlrewrite\get_back_url();
                } else {
                    $get_params = [
//                        "back-url" => \k1lib\urlrewrite\get_back_url(),
                        "auth-code" => "--authcode--"
                    ];
                    $url_to_go = "{$this->controller_object->get_controller_root_dir()}{$this->controller_object->get_board_read_url_name()}/--rowkeys--/";
                }
                $url_to_go = url::do_url($url_to_go, $get_params);
            } else {
                $url_to_go = url::do_url($custom_redirect);
            }
            $this->create_object->post_insert_redirect($url_to_go, $do_redirect);
        }
    }

}

// ./src/crudlexs/board_classes/delete.php


namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\urlrewrite\url as url;
use k1lib\html\DOM as DOM;
use k1lib\notifications\on_DOM as DOM_notification;

class board_delete extends board_base implements board_interface {

    protected $redirect_url = null;
    private $row_keys_text;
    private $row_keys_text_array;
    private $read_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        $this->redirect_url = (isset($_GET['back-url'])) ? \k1lib\urlrewrite\get_back_url() : "{$controller_object->get_controller_root_dir()}{$this->controller_object->get_board_list_url_name()}/";
        if ($this->is_enabled) {
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), 'row-keys-text', FALSE);
            $this->read_object = new \k1lib\crudlexs\reading($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    function set_redirect_url($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }
        return $this->board_content_div;
    }

    /**
     * @return boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }
        if (!empty($this->row_keys_text)) {
            if ($this->read_object->load_db_table_data()) {
                $this->row_keys_text_array = \k1lib\sql\table_url_text_to_keys($this->row_keys_text, $this->controller_object->db_table->get_db_table_config());
                if ($_GET['auth-code'] === $this->read_object->get_auth_code_personal()) {
                    $this->sql_action_result = $this->controller_object->db_table->delete_data($this->row_keys_text_array);
                    if ($this->sql_action_result) {
                        DOM_notification::queue_mesasage(board_delete_strings::$data_deleted, "success", $this->notifications_div_id);
                        return TRUE;
                    } else {
                        DOM_notification::queue_mesasage(board_delete_strings::$error_no_data_deleted, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
                        return FALSE;
                    }
                } else if ($_GET['auth-code'] === $this->read_object->get_auth_code()) {
                    DOM_notification::queue_mesasage(board_delete_strings::$error_no_data_deleted_hacker, "alert", $this->notifications_div_id, \k1lib\common_strings::$error_hacker);
                    return FALSE;
                }
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_not_opened, "alert", $this->notifications_div_id, board_base_strings::$error_mysql);
                $this->is_enabled = FALSE;
                return FALSE;
            }
        }
    }

    public function get_row_keys_text() {
        return $this->row_keys_text;
    }

    public function get_row_keys_text_array() {
        return $this->row_keys_text_array;
    }

    public function finish_board() {
        if ($this->sql_action_result) {
            \k1lib\html\html_header_go($this->redirect_url);
        }
    }

}

// ./src/crudlexs/board_classes/list.php


namespace k1lib\crudlexs;

use \k1lib\urlrewrite\url as url;
use k1lib\html\DOM as DOM;
use k1lib\notifications\on_DOM as DOM_notification;

class board_list extends board_base implements board_interface {

    const SHOW_BEFORE_TABLE = 1;
    const SHOW_AFTER_TABLE = 2;
    const SHOW_BEFORE_AND_AFTER_TABLE = 3;

    protected $search_enable = TRUE;
    protected $search_catch_post_enable = TRUE;
    protected $create_enable = TRUE;
    protected $export_enable = TRUE;
    protected $pagination_enable = TRUE;
    protected $stats_enable = TRUE;
    protected $where_to_show_stats = self::SHOW_AFTER_TABLE;
    protected $back_enable = TRUE;

    /**
     *
     * @var \k1lib\crudlexs\listing
     */
    public $list_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-list";
            $this->list_object = new \k1lib\crudlexs\listing($this->controller_object->db_table, FALSE);
            $this->list_object->set_do_table_field_name_encrypt(TRUE);
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }

        if ($this->list_object->get_state()) {

            /**
             * BACK
             */
            if ($this->back_enable && (isset($_GET['back-url']))) {
                $back_url = \k1lib\urlrewrite\get_back_url();
                $back_link = \k1lib\html\get_link_button($back_url, board_read_strings::$button_back);
                $back_link->append_to($this->button_div_tag);
            }
            /**
             * NEW BUTTON
             */
            $related_url_keys_text = url::get_url_level_value_by_name("related_url_keys_text");
            if (empty($related_url_keys_text)) {
                $related_url_keys_text = "";
                $new_link = \k1lib\html\get_link_button(url::do_url("../{$this->controller_object->get_board_create_url_name()}/" . urlencode($related_url_keys_text)), board_list_strings::$button_new);
            } else {
                $related_url_keys_text .= "/";
                $new_link = \k1lib\html\get_link_button(url::do_url("../../{$this->controller_object->get_board_create_url_name()}/" . urlencode($related_url_keys_text)), board_list_strings::$button_new);
            }
            if ($this->create_enable) {
//                $new_link = \k1lib\html\get_link_button(url::do_url("../{$this->controller_object->get_board_create_url_name()}/" . $related_url_keys_text), board_list_strings::$button_new);
//                $new_link = \k1lib\html\get_link_button("../{$this->controller_object->get_board_create_url_name()}/?back-url={$this_url}", board_list_strings::$button_new);
                $new_link->append_to($this->button_div_tag);
            }

            /**
             * Search
             */
            if ($this->search_enable) {


                $search_iframe = new \k1lib\html\iframe(url::do_url(
                                $this->controller_object->get_controller_root_dir() . "search/?just-controller=1&caller-url=" . urlencode($_SERVER['REQUEST_URI']))
                        , 'utility-iframe', "search-iframe"
                );
//                $this->board_content_div->append_child_tail($search_iframe);
                DOM::html()->body()->append_child_tail($search_iframe);
//                $search_iframe->append_to($this->board_content_div);

                $search_buttom = new \k1lib\html\a(NULL, " " . board_list_strings::$button_search, "_self");
                $search_buttom->set_id("search-button");
                $search_buttom->set_attrib("class", "button fi-page-search");
                $search_buttom->append_to($this->button_div_tag);

                if (isset($_POST) && isset($_POST['from-search']) && (urldecode($_POST['from-search']) == $_SERVER['REQUEST_URI'])) {
//                    if ($this->)
                    /**
                     * decrypt post field names
                     */
                    $incomming_search_data = \k1lib\forms\check_all_incomming_vars($_POST);
                    if ($this->list_object->get_do_table_field_name_encrypt()) {
                        $search_data = $this->list_object->decrypt_field_names($incomming_search_data);
                    } else {
                        $search_data = $incomming_search_data;
                    }
                    $this->controller_object->db_table->set_query_filter($search_data);
                    $search_post = \k1lib\common\serialize_var($_POST, urlencode($_SERVER['REQUEST_URI']));
                    /**
                     * Clear search
                     */
                    $clear_search_buttom = new \k1lib\html\a(url::do_url($_SERVER['REQUEST_URI']), board_list_strings::$button_search_cancel, "_self");
                    $search_buttom->set_value(" " . board_list_strings::$button_search_modify);
                    $clear_search_buttom->set_attrib("class", "button warning");
                    $clear_search_buttom->append_to($this->button_div_tag);
                } else {
                    $search_post = \k1lib\common\unset_serialize_var(urlencode($_SERVER['REQUEST_URI']));
                }
            }

            $this->data_loaded = $this->list_object->load_db_table_data($this->show_rule_to_apply);
            return $this->board_content_div;
        } else {
            DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_strings::$error_mysql);
            $this->list_object->make_invalid();
            $this->is_enabled = FALSE;

            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }
        /**
         * HTML DB TABLE
         */
        if ($this->data_loaded) {
            if ($this->apply_label_filter) {
                $this->list_object->apply_label_filter();
            }
            if ($this->apply_field_label_filter) {
                $this->list_object->apply_field_label_filter();
            }
            if (\k1lib\forms\file_uploads::is_enabled()) {
                $this->list_object->apply_file_uploads_filter();
            }
            // IF NOT previous link applied this will try to apply ONLY on keys if are present on show-list filter
            if (!$this->list_object->get_link_on_field_filter_applied()) {
                $get_vars = [
                    "auth-code" => "--authcode--",
                    "back-url" => urlencode($_SERVER['REQUEST_URI'])
                ];
                $this->list_object->apply_link_on_field_filter(url::do_url("../{$this->controller_object->get_board_read_url_name()}/--rowkeys--/", $get_vars), crudlexs_base::USE_KEY_FIELDS);
            }
            // Show stats BEFORE
            if (($this->stats_enable) && (($this->where_to_show_stats == self::SHOW_BEFORE_TABLE) || ($this->where_to_show_stats == self::SHOW_BEFORE_AND_AFTER_TABLE))) {
                $this->list_object->do_pagination()->append_to($this->board_content_div);
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
            }
            /**
             * HTML OBJECT
             */
            $list_content_div = $this->list_object->do_html_object();
            $list_content_div->append_to($this->board_content_div);
            // Show stats AFTER
            if (($this->stats_enable) && (($this->where_to_show_stats == self::SHOW_AFTER_TABLE) || ($this->where_to_show_stats == self::SHOW_BEFORE_AND_AFTER_TABLE))) {
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
                $this->list_object->do_pagination()->append_to($this->board_content_div);
            }

            return $this->board_content_div;
        } else {
            $this->list_object->do_html_object()->append_to($this->board_content_div);
            return $this->board_content_div;
        }
    }

    public function finish_board() {
        
    }

    public function set_search_catch_post_enable($search_catch_post_enable) {
        $this->search_catch_post_enable = $search_catch_post_enable;
    }

    function set_where_to_show_stats($where_to_show_stats) {
        $this->where_to_show_stats = $where_to_show_stats;
    }

    function get_search_enable() {
        return $this->search_enable;
    }

    function get_create_enable() {
        return $this->create_enable;
    }

    function get_export_enable() {
        return $this->export_enable;
    }

    function get_pagination_enable() {
        return $this->pagination_enable;
    }

    function get_stats_enable() {
        return $this->stats_enable;
    }

    function set_search_enable($search_enable) {
        $this->search_enable = $search_enable;
    }

    function set_create_enable($create_enable) {
        $this->create_enable = $create_enable;
    }

    function set_export_enable($export_enable) {
        $this->export_enable = $export_enable;
    }

    function set_pagination_enable($pagination_enable) {
        $this->pagination_enable = $pagination_enable;
    }

    function set_stats_enable($stats_enable) {
        $this->stats_enable = $stats_enable;
    }

    public function set_back_enable($back_enable) {
        $this->back_enable = $back_enable;
    }

}

// ./src/crudlexs/board_classes/read.php


namespace k1lib\crudlexs;

use \k1lib\urlrewrite\url as url;
use k1lib\notifications\on_DOM as DOM_notification;

class board_read extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\reading
     */
    public $read_object;
    private $row_keys_text;
    // Buttons enable
    protected $back_enable = TRUE;
    protected $all_data_enable = TRUE;
    protected $update_enable = TRUE;
    protected $delete_enable = TRUE;
    protected $use_label_as_title_enabled = TRUE;
    protected $related_do_clean_array_on_query_filter = FALSE;
    protected $related_use_rows_key_text = TRUE;
    protected $related_use_show_rule = "show-related";
    //RELATED CONFIG

    /**
     * @var listing
     */
    protected $related_list = NULL;
    protected $related_show_new = TRUE;
    protected $related_show_all_data = TRUE;
    protected $related_rows_to_show = 5;
    protected $related_edit_url = NULL;
    protected $related_apply_filters = TRUE;
    protected $related_custom_field_labels = [];
    protected $related_do_pagination = TRUE;
    //RELATED HTML OBJECTS

    /**
     * @var  \k1lib\html\a
     */
    protected $related_html_object_show_new = NULL;

    /**
     * @var \k1lib\html\a
     */
    protected $related_html_object_show_all_data = NULL;

    /**
     * @var \k1lib\html\foundation\table_from_data
     */
    protected $related_html_table_object = NULL;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-read";
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), 'row-keys-text', FALSE);
            $this->read_object = new \k1lib\crudlexs\reading($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }

        if (!empty($this->row_keys_text)) {
            if ($this->read_object->get_state()) {
                /**
                 * BACK
                 */
                if ($this->back_enable && (isset($_GET['back-url']))) {
                    $back_url = \k1lib\urlrewrite\get_back_url();
                    $back_link = \k1lib\html\get_link_button($back_url, board_read_strings::$button_back, "small");
                    $back_link->append_to($this->button_div_tag);
                }
                /**
                 * ALL DATA
                 */
                if ($this->all_data_enable) {
                    $all_data_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_list_url_name()}/";
                    $all_data_link = \k1lib\html\get_link_button(
                            url::do_url($all_data_url, [], TRUE, ['no-rules'])
                            , board_read_strings::$button_all_data
                            , "small"
                    );
                    $all_data_link->append_to($this->button_div_tag);
                }
                /**
                 * EDIT BUTTON
                 */
                if ($this->update_enable) {
                    $edit_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_update_url_name()}/" . urlencode($this->row_keys_text) . '/';
                    $get_vars = [
                        "auth-code" => $this->read_object->get_auth_code(),
//                        "back-url" => $_SERVER['REQUEST_URI'],
                    ];
                    $edit_link = \k1lib\html\get_link_button(url::do_url($edit_url, $get_vars), board_read_strings::$button_edit, "small");
                    $edit_link->append_to($this->button_div_tag);
                }
                /**
                 * DELETE BUTTON
                 */
                if ($this->delete_enable) {
                    $delete_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_delete_url_name()}/" . urlencode($this->row_keys_text) . '/';
                    if (\k1lib\urlrewrite\get_back_url(TRUE)) {
                        $get_vars = [
                            "auth-code" => $this->read_object->get_auth_code_personal(),
                            "back-url" => \k1lib\urlrewrite\get_back_url(TRUE),
                        ];
                    } else {
                        $get_vars = [
                            "auth-code" => $this->read_object->get_auth_code_personal(),
                        ];
                    }
                    $delete_link = \k1lib\html\get_link_button(url::do_url($delete_url, $get_vars), board_read_strings::$button_delete, "small");
                    $delete_link->append_to($this->button_div_tag);
                }

                $this->data_loaded = $this->read_object->load_db_table_data($this->show_rule_to_apply);
                return $this->board_content_div;
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
                DOM_notification::queue_title(board_base_strings::$error_mysql);
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->read_object->get_state() && !empty($this->row_keys_text)) {
            if ($this->data_loaded) {
                if ($this->apply_label_filter) {
                    $this->read_object->apply_label_filter();
                }
                if ($this->apply_field_label_filter) {
                    $this->read_object->apply_field_label_filter();
                }
//                $this->read_object->set_use_read_custom_template();
                if (\k1lib\forms\file_uploads::is_enabled()) {
                    $this->read_object->apply_file_uploads_filter();
                }

//                $this->board_content_div->set_attrib("class", "grid-x", TRUE);

                $span_tag = new \k1lib\html\span("key-field");
                $this->read_object->apply_html_tag_on_field_filter($span_tag, \k1lib\crudlexs\crudlexs_base::USE_KEY_FIELDS);

                $read_content_div = $this->read_object->do_html_object();
                $read_content_div->append_to($this->board_content_div);

                return $this->board_content_div;
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_no_data, "alert", $this->notifications_div_id);
                DOM_notification::queue_title(board_base_strings::$error_mysql);
                $this->read_object->make_invalid();
                $this->is_enabled = FALSE;
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function finish_board() {
        // (:    
    }

    public function set_related_use_rows_key_text($related_use_rows_key_text) {
        $this->related_use_rows_key_text = $related_use_rows_key_text;
    }

    public function set_related_use_show_rule($related_use_show_rule) {
        $this->related_use_show_rule = $related_use_show_rule;
    }

    public function set_related_do_clean_array_on_query_filter($related_do_clean_array_on_query_filter) {
        $this->related_do_clean_array_on_query_filter = $related_do_clean_array_on_query_filter;
    }

    public function get_related_do_clean_array_on_query_filter() {
        return $this->related_do_clean_array_on_query_filter;
    }

    public function get_related_show_new() {
        return $this->related_show_new;
    }

    public function get_related_show_all_data() {
        return $this->related_show_all_data;
    }

    /**
     * 
     * @param \k1lib\crudlexs\class_db_table $db_table
     * @param array $field_links_array
     * @param string $title
     * @param string $board_root
     * @param string $board_create
     * @param string $board_read
     * @param boolean $show_create
     * @return \k1lib\html\div|boolean
     */
    public function create_related_list(class_db_table $db_table, $field_links_array, $title, $board_root, $board_create, $board_read, $board_list, $use_back_url = FALSE, $clear_url = FALSE, $custom_key_array = NULL) {

        $table_alias = \k1lib\db\security\db_table_aliases::encode($db_table->get_db_table_name());
        $detail_div = new \k1lib\html\div();

        $this->related_list = $this->do_related_list($db_table, $field_links_array, $board_root, $board_read, $use_back_url, $clear_url, $custom_key_array);


        if (!empty($this->related_list)) {
            $current_row_keys_text = $this->read_object->get_row_keys_text();
            $current_row_keys_text_auth_code = md5(\k1lib\K1MAGIC::get_value() . $current_row_keys_text);

            $detail_div->set_class("k1lib-related-data-list {$table_alias}");
            $related_title = $detail_div->append_h4($title, "{$table_alias}");
            $detail_div->append_div("related-messaje");

            $get_vars = [
                "auth-code" => $current_row_keys_text_auth_code,
                "back-url" => urlencode($_SERVER['REQUEST_URI'])
            ];

            if (isset($data_loaded) && $data_loaded) {
                $all_data_url = url::do_url(APP_URL . $board_root . "/" . $board_list . "/" . urlencode($current_row_keys_text) . "/", $get_vars, FALSE);
                $this->related_html_object_show_all_data = \k1lib\html\get_link_button($all_data_url, board_read_strings::$button_all_data, "tiny");
                if ($this->related_show_all_data) {
                    $related_title->set_value($this->related_html_object_show_all_data, TRUE);
                }
            }
            if ($use_back_url) {
                $create_url = url::do_url(APP_URL . $board_root . "/" . $board_create . "/" . urlencode($current_row_keys_text) . "/", $get_vars, TRUE);
            } else {
                $get_vars = [
                    "auth-code" => $current_row_keys_text_auth_code,
                ];
                $create_url = url::do_url(APP_URL . $board_root . "/" . $board_create . "/" . urlencode($current_row_keys_text) . "/", $get_vars, TRUE, ['back-url'], FALSE);
            }
            $this->related_html_object_show_new = \k1lib\html\get_link_button($create_url, board_list_strings::$button_new, "tiny");

            if ($this->related_show_new) {
                $related_title->set_value($this->related_html_object_show_new, TRUE);
            }

            $this->related_list->do_html_object()->append_to($detail_div);
            $this->related_html_table_object = $this->related_list->get_html_table();
            if ($db_table->get_total_rows() > $this->related_rows_to_show && $this->related_do_pagination) {
                $this->related_list->do_pagination()->append_to($detail_div);
                $this->related_list->do_row_stats()->append_to($detail_div);
            }

//            listing::$rows_per_page = $actual_rows_per_page;
        }
// TODO: NONSENSE line !
//        $this->set_related_show_new(TRUE);
        return $detail_div;
    }

    /**
     * @param \k1lib\crudlexs\class_db_table $db_table
     * @param array $field_links_array
     * @param string $board_root
     * @param string $board_read
     * @param boolean $clear_url
     * @return \k1lib\crudlexs\listing|boolean
     */
    public function do_related_list(class_db_table $db_table, $field_links_array, $board_root, $board_read, $use_back_url, $clear_url = FALSE, $custom_key_array = []) {

        $table_alias = \k1lib\db\security\db_table_aliases::encode($db_table->get_db_table_name());

        if ($this->is_enabled && $this->read_object->is_valid()) {

            /**
             * Clients list
             */
            if ($db_table->get_state()) {
                if ($this->related_use_rows_key_text) {
                    if (count($custom_key_array) < 1) {
                        $current_row_keys_array = $this->read_object->get_row_keys_array();
                        /**
                         * lets fix the non-same key name
                         */
                        $db_table_config = $db_table->get_db_table_config();
                        foreach ($db_table_config as $field => $field_config) {
                            if (!empty($field_config['refereced_column_config'])) {
                                $fk_field_name = $field_config['refereced_column_config']['field'];
                                foreach ($current_row_keys_array as $field_current => $value) {
                                    if ($field_current == $field) {
                                        unset($current_row_keys_array[$field_current]);
                                        $current_row_keys_array[$fk_field_name] = $value;
                                    }
                                }
                            }
                        }
                    } else {
                        $current_row_keys_array = $custom_key_array;
                    }
                    $db_table->set_field_constants($current_row_keys_array);
                    $db_table->set_query_filter($current_row_keys_array, TRUE, $this->related_do_clean_array_on_query_filter);
                }

                /**
                 * LIST OBJECT must be created here to know if ther is data or not to show
                 * all data button.
                 */
                $this->related_list = new \k1lib\crudlexs\listing($db_table, FALSE);

                if (!empty($this->related_custom_field_labels)) {
                    $this->related_list->set_custom_field_labels($this->related_custom_field_labels);
                }

                $this->related_list->set_rows_per_page($this->related_rows_to_show);
                $data_loaded = $this->related_list->load_db_table_data($this->related_use_show_rule);
                if ($data_loaded) {
                    if ($this->related_apply_filters) {
                        $this->related_apply_filters();
                        $this->related_apply_link_read_field($field_links_array, $board_root, $board_read, $use_back_url, $clear_url);
                    }
                }
                return $this->related_list;
            } else {
                trigger_error("DB Table couldn't be opened : " . $db_table->get_db_table_name(), E_USER_NOTICE);
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function related_apply_link_read_field($field_links_array, $board_root, $board_read, $use_back_url, $clear_url = FALSE) {
        if ($this->related_list->get_db_table_data()) {
            if ($clear_url) {
                $get_vars = [];
            } else {

                $get_vars = [
                    "auth-code" => "--authcode--",
                ];
                if ($use_back_url) {
                    $get_vars["back-url"] = urlencode($_SERVER['REQUEST_URI']);
                }
            }
            $link_row_url = url::do_url(APP_URL . $board_root . "/" . $board_read . "/--rowkeys--/", $get_vars);
            $this->related_list->apply_link_on_field_filter($link_row_url, $field_links_array);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function related_apply_filters() {
        if ($this->related_list->get_db_table_data()) {
            $this->related_list->apply_label_filter();
            $this->related_list->apply_field_label_filter();
            if (\k1lib\forms\file_uploads::is_enabled()) {
//                    $this->related_list->set
                $this->related_list->apply_file_uploads_filter();
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function set_related_edit_url($related_edit_url) {
        $this->related_edit_url = $related_edit_url;
    }

    public function get_related_rows_to_show() {
        return $this->related_rows_to_show;
    }

    public function set_related_rows_to_show($related_rows_to_show) {
        $this->related_rows_to_show = $related_rows_to_show;
    }

    /**
     * @return \k1lib\html\a
     */
    public function get_related_html_object_show_new() {
        return $this->related_html_object_show_new;
    }

    /**
     * @return \k1lib\html\a
     */
    public function get_related_html_object_show_all_data() {
        return $this->related_html_object_show_all_data;
    }

    function get_back_enable() {
        return $this->back_enable;
    }

    function get_update_enable() {
        return $this->update_enable;
    }

    function get_delete_enable() {
        return $this->delete_enable;
    }

    function set_all_data_enable($all_data_enable) {
        $this->all_data_enable = $all_data_enable;
    }

    function set_back_enable($back_enable) {
        $this->back_enable = $back_enable;
    }

    function set_update_enable($update_enable) {
        $this->update_enable = $update_enable;
    }

    public function set_related_custom_field_labels($related_custom_field_labels) {
        $this->related_custom_field_labels = $related_custom_field_labels;
    }

    function set_delete_enable($delete_enable) {
        $this->delete_enable = $delete_enable;
    }

    public function set_related_show_new($related_show_new) {
        $this->related_show_new = $related_show_new;
    }

    public function set_related_show_all_data($related_show_all_data) {
        $this->related_show_all_data = $related_show_all_data;
    }

    public function set_related_do_pagination($related_do_pagination) {
        $this->related_do_pagination = $related_do_pagination;
    }

    /**
     * @return \k1lib\html\foundation\table_from_data
     */
    public function get_related_html_table_object() {
        return $this->related_html_table_object;
    }

    public function set_related_apply_filters($related_apply_filters) {
        $this->related_apply_filters = $related_apply_filters;
    }

    /**
     * @return listing
     */
    public function get_related_list() {
        return $this->related_list;
    }

}

// ./src/crudlexs/board_classes/search.php


namespace k1lib\crudlexs;

use k1lib\html\DOM as DOM;
use k1lib\notifications\on_DOM as DOM_notification;

class board_search extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\search_helper
     */
    public $search_object;
    protected $search_catch_post_enable = TRUE;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->search_object = new \k1lib\crudlexs\search_helper($this->controller_object->db_table);
            $this->data_loaded = $this->search_object->load_db_table_data(TRUE);
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }
        /**
         * IFRAME for KF tool
         */
        $fk_iframe = new \k1lib\html\iframe('', 'utility-iframe', "fk-iframe");
        DOM::html()->body()->content()->append_child_tail($fk_iframe);
        
        if ($this->search_object->get_state()) {
            $close_search_buttom = new \k1lib\html\a(NULL, " " . \k1lib\common_strings::$button_cancel, "_parent");
            $close_search_buttom->set_id("close-search-button");
            $close_search_buttom->set_attrib("class", "button warning fi-page-close");
            $close_search_buttom->set_attrib("onClick", "parent.close_search();");
            $close_search_buttom->append_to($this->button_div_tag);

            $this->search_object->set_search_catch_post_enable($this->search_catch_post_enable);
            $this->search_object->set_html_column_classes("column large-11 medium-11 small-12");
            $this->search_object->set_html_form_column_classes("large-11");

            $this->search_object->do_html_object()->append_to($this->board_content_div);

            return $this->board_content_div;
        } else {
            DOM_notification::queue_mesasage(board_base_labels::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_labels::$error_mysql);
            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->data_loaded) {
            return $this->board_content_div;
        } else {
            DOM_notification::queue_mesasage(board_create_strings::$error_no_blank_data, "alert", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_strings::$alert_board);
            $this->search_object->make_invalid();
            $this->is_enabled = FALSE;
            return FALSE;
        }
    }

    public function finish_board($do_redirect = TRUE, $custom_redirect = FALSE) {
        
    }

    public function set_search_catch_post_enable($search_catch_post_enable) {
        $this->search_catch_post_enable = $search_catch_post_enable;
    }

    function set_object_id($class_name) {
        if (isset($this->db_table) && key_exists($this->db_table->get_db_table_name(), db_table_aliases::$aliases)) {
            $table_name = db_table_aliases::$aliases[$this->db_table->get_db_table_name()];
        } else if (isset($this->db_table)) {
            $table_name = $this->db_table->get_db_table_name();
        } else {
            $table_name = "no-table";
        }
        return $this->object_id = $table_name . "-" . basename(str_replace("\\", "/", $class_name));
    }

}

// ./src/crudlexs/board_classes/update.php


namespace k1lib\crudlexs;

use k1lib\urlrewrite\url as url;
use k1lib\session\session_plain as session_plain;
use k1lib\html\DOM as DOM;
use k1lib\notifications\on_DOM as DOM_notification;

class board_update extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\updating
     */
    public $update_object;
    private $row_keys_text;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-update";
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), 'row-keys-text', FALSE);
            $this->update_object = new \k1lib\crudlexs\updating($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }
        /**
         * IFRAME for KF tool
         */
//        $fk_iframe = new \k1lib\html\iframe('./', 'utility-iframe', "fk-iframe");
//        DOM::html()->body()->content()->append_child_tail($fk_iframe);
        
        if (!empty($this->row_keys_text)) {

            if ($this->update_object->get_state()) {
                $this->update_object->set_back_url(\k1lib\urlrewrite\get_back_url());

                $this->update_object->set_do_table_field_name_encrypt(TRUE);
                $this->controller_object->db_table->set_db_table_show_rule($this->show_rule_to_apply);

                $this->data_loaded = $this->update_object->load_db_table_data();
                $this->update_object->catch_post_data();
                return $this->board_content_div;
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
                DOM_notification::queue_title(board_base_strings::$error_mysql);
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->update_object->get_state() && !empty($this->row_keys_text)) {

            if ($this->data_loaded) {
                if ($this->update_object->get_post_data_catched()) {
                    $this->update_object->put_post_data_on_table_data();
                    if (!$this->skip_form_action) {
                        if ($this->update_object->do_post_data_validation()) {
                            $this->sql_action_result = $this->update_object->do_update();
                        } else {
                            DOM_notification::queue_mesasage(board_update_strings::$error_form, "alert", $this->notifications_div_id);
                            DOM_notification::queue_title(board_base_strings::$alert_board);
                        }
                    }
                }
                if ($this->apply_label_filter) {
                    $this->update_object->apply_label_filter();
                }
                $this->update_object->insert_inputs_on_data_row();

                /**
                 * DELETE BUTTON
                 */
                if ($this->controller_object->get_board_delete_enabled() && $this->controller_object->get_board_delete_allowed_for_current_user()) {
                    $delete_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_delete_url_name()}/" . urlencode($this->row_keys_text) . '/';
                    if (\k1lib\urlrewrite\get_back_url(TRUE)) {
                        $get_vars = [
                            "auth-code" => md5(session_plain::get_user_hash() . $this->row_keys_text),
                            "back-url" => \k1lib\urlrewrite\get_back_url(TRUE),
                        ];
                    } else {
                        $get_vars = [
                            "auth-code" => md5(session_plain::get_user_hash() . $this->row_keys_text),
                        ];
                    }
                    $back_link = \k1lib\html\get_link_button(url::do_url(\k1lib\urlrewrite\get_back_url()), board_read_strings::$button_back, "small");
                    $back_link->append_to($this->button_div_tag);
                    $delete_link = \k1lib\html\get_link_button(url::do_url($delete_url, $get_vars), board_read_strings::$button_delete, "small");
                    $delete_link->append_to($this->button_div_tag);
                }

                $update_content_div = $this->update_object->do_html_object();
                $update_content_div->append_to($this->board_content_div);

                return $this->board_content_div;
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_no_data, "alert", $this->notifications_div_id, board_base_strings::$error_mysql);
                $this->update_object->make_invalid();
                $this->is_enabled = FALSE;
                return FALSE;
            }
        }
    }

    public function finish_board($do_redirect = TRUE, $custom_redirect = FALSE) {
        if ($this->sql_action_result !== NULL) {
            if ($custom_redirect === FALSE) {
                if (isset($_GET['back-url'])) {
                    $url_to_go = urldecode($_GET['back-url']);
                } else {
                    $get_params = [
                        "auth-code" => "--authcode--"
                    ];
                    $url_to_go = url::do_url("{$this->controller_object->get_controller_root_dir()}{$this->controller_object->get_board_read_url_name()}/--rowkeys--/?", $get_params);
                }
            } else {
                $url_to_go = url::do_url($custom_redirect);
            }
            $this->update_object->post_update_redirect($url_to_go, $do_redirect);
        }
    }

}

// ./src/crudlexs/controller_clasess/_controller_base.php


namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\urlrewrite\url as url;
use k1lib\html\DOM as DOM;
use k1lib\notifications\on_DOM as DOM_notification;

class controller_base {

    protected $security_no_rules_enable = FALSE;

    /**
     * DB table main object
     * @var \k1lib\crudlexs\class_db_table 
     */
    public $db_table;

    /**
     * Controller name for add on <html><title> and controller name tag
     * @var string 
     */
    protected $controller_name;

    /**
     * URL value after the domain
     * @var string
     */
    protected $controller_root_dir;

    /**
     * THIS controller URL value
     * @var string
     */
    protected $controller_url_value;

    /**
     * URL value for the board asked to show
     * @var string
     */
    protected $controller_board_url_value;
    protected $controller_board_allowed_leves = [];

    /**
     *
     * @var boolean
     */
    protected $board_inited = FALSE;
    protected $board_started = FALSE;
    protected $board_executed = FALSE;

    /**
     *
     * @var \k1lib\html\div
     */
    public $board_div_content;

    /**
     *
     * @var \k1lib\html\tag
     */
    public $html_title_tags = NULL;
    /**
     * 
     * URL MANAGEMENT VALUES
     * 
     */

    /**
     *
     * @var \k1lib\crudlexs\board_list;
     */
    public $board_list_object;
    protected $board_list_url_name = "list";
    protected $board_list_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_create
     */
    public $board_create_object;
    protected $board_create_url_name = "create";
    protected $board_create_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_read
     */
    public $board_read_object;
    protected $board_read_url_name = "read";
    protected $board_read_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_update
     */
    public $board_update_object;
    protected $board_update_url_name = "update";
    protected $board_update_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_delete
     */
    public $board_delete_object;
    protected $board_delete_url_name = "delete";
    protected $board_delete_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_search
     */
    public $board_search_object;
    protected $board_search_url_name = "search";
    protected $board_search_allowed_levels = [];


    /**
     *
     * Board names for html title and controller name tag
     * 
     */

    /**
     * Template name set for HTML-TITLE on the header.php
     * @var type 
     */
    protected $template_place_name_html_title = "html-title";

    /**
     * Template name set for CONTROLER-NAME on the header.php
     * @var type 
     */
    protected $template_place_name_controller_name = "controller-name";
    protected $template_place_name_board_name = "board-name";
    protected $board_list_name;
    protected $board_create_name;
    protected $board_read_name;
    protected $board_update_name;
    protected $board_delete_name;
    protected $url_redirect_after_delete = "../../list/";

    /**
     * BOARDS avaliabilty
     */
    protected $board_list_enabled = TRUE;
    protected $board_create_enabled = TRUE;
    protected $board_read_enabled = TRUE;
    protected $board_update_enabled = TRUE;
    protected $board_delete_enabled = TRUE;

    /**
     * One line config for more time to party and less coding :)
     * @param string $app_base_dir Use here \k1app\APP_BASE_URL
     * @param \PDO $db DB app object
     * @param string $db_table_name Table to open from the DB
     * @param string $controller_name Name for html title and controller name tag
     * @param string $template_place_name_html_title 
     * @param string $template_place_name_controller_name 
     */
    public function __construct($app_base_dir, \PDO $db, $db_table_name, $controller_name, $title_tag_class = null) {
        /**
         * URL Management
         */
        // Posible URL ERROR FIX
        if ($app_base_dir == '//') {
            $app_base_dir = '/';
        }
        $this->controller_root_dir = $app_base_dir . url::make_url_from_rewrite('this');
        $this->controller_url_value = url::get_url_level_value('this');
        $this->controller_board_url_value = $this->set_and_get_next_url_value();
        /**
         * DB Table 
         */
        $this->db_table = new \k1lib\crudlexs\class_db_table($db, $db_table_name);

        /**
         * Controller name for add on <html><title> and controller name tag
         */
        $this->controller_name = $controller_name;
        $this->html_title_tags = DOM::html()->body()->get_elements_by_class($title_tag_class);
        if (!empty($this->html_title_tags)) {
            $span = (new \k1lib\html\span("subheader"))->set_value($controller_name);
            foreach ($this->html_title_tags as $tag) {
                $tag->set_value($span);
            }
            DOM::html()->head()->set_title(DOM::html()->head()->get_title() . " | $controller_name");
        }


//        temply::set_place_value($this->template_place_name_html_title, " | $controller_name");
//        temply::set_place_value($this->template_place_name_controller_name, $controller_name);

        /**
         * SET FROM LANG HACK
         */
        $this->board_list_name = controller_base_strings::$board_list_name;
        $this->board_create_name = controller_base_strings::$board_create_name;
        $this->board_read_name = controller_base_strings::$board_read_name;
        $this->board_update_name = controller_base_strings::$board_update_name;
        $this->board_delete_name = controller_base_strings::$board_delete_name;

        if (DOM::html()->body()) {
            $js_file = K1LIB_BASE_PATH . '/crudlexs/js/crudlexs.js';
            if (file_exists($js_file)) {
                $js_content = file_get_contents($js_file);

                $js_script = new \k1lib\html\script();
                $js_script->set_value($js_content);

                DOM::html()->body()->append_child_tail($js_script);
            } else {
                d($js_file);
            }
        }
    }

    public function set_config_from_class($class_name = NULL) {
//        $class_name::CONTROLLER_ALLOWED_LEVELS;
        if (!class_exists($class_name)) {
            d("Warning: $class_name do not exist");
        }

        /**
         * ENABLED
         */
        if (defined("{$class_name}::BOARD_CREATE_ENABLED")) {
            $this->set_board_create_enabled($class_name::BOARD_CREATE_ENABLED);
        }
        if (defined("{$class_name}::BOARD_READ_ENABLED")) {
            $this->set_board_read_enabled($class_name::BOARD_READ_ENABLED);
        }
        if (defined("{$class_name}::BOARD_UPDATE_ENABLED")) {
            $this->set_board_update_enabled($class_name::BOARD_UPDATE_ENABLED);
        }
        if (defined("{$class_name}::BOARD_DELETE_ENABLED")) {
            $this->set_board_delete_enabled($class_name::BOARD_DELETE_ENABLED);
        }
        if (defined("{$class_name}::BOARD_LIST_ENABLED")) {
            $this->set_board_list_enabled($class_name::BOARD_LIST_ENABLED);
        }
        /**
         * URLS
         */
        if (defined("{$class_name}::BOARD_CREATE_URL")) {
            $this->set_board_create_url_name($class_name::BOARD_CREATE_URL);
        }
        if (defined("{$class_name}::BOARD_READ_URL")) {
            $this->set_board_read_url_name($class_name::BOARD_READ_URL);
        }
        if (defined("{$class_name}::BOARD_UPDATE_URL")) {
            $this->set_board_update_url_name($class_name::BOARD_UPDATE_URL);
        }
        if (defined("{$class_name}::BOARD_DELETE_URL")) {
            $this->set_board_delete_url_name($class_name::BOARD_DELETE_URL);
        }
        if (defined("{$class_name}::BOARD_LIST_URL")) {
            $this->set_board_list_url_name($class_name::BOARD_LIST_URL);
        }
        /**
         * NAMES
         */
        if (defined("{$class_name}::BOARD_CREATE_NAME")) {
            $this->set_board_create_name($class_name::BOARD_CREATE_NAME);
        }
        if (defined("{$class_name}::BOARD_READ_NAME")) {
            $this->set_board_read_name($class_name::BOARD_READ_NAME);
        }
        if (defined("{$class_name}::BOARD_UPDATE_NAME")) {
            $this->set_board_update_name($class_name::BOARD_UPDATE_NAME);
        }
        if (defined("{$class_name}::BOARD_DELETE_NAME")) {
            $this->set_board_delete_name($class_name::BOARD_DELETE_NAME);
        }
        if (defined("{$class_name}::BOARD_LIST_NAME")) {
            $this->set_board_list_name($class_name::BOARD_LIST_NAME);
        }

        /**
         * ALLOWED LEVELS
         */
        if (defined("{$class_name}::BOARD_CREATE_ALLOWED_LEVELS")) {
            $this->set_board_create_allowed_levels($class_name::BOARD_CREATE_ALLOWED_LEVELS);
        }
        if (defined("{$class_name}::BOARD_READ_ALLOWED_LEVELS")) {
            $this->set_board_read_allowed_levels($class_name::BOARD_READ_ALLOWED_LEVELS);
        }
        if (defined("{$class_name}::BOARD_UPDATE_ALLOWED_LEVELS")) {
            $this->set_board_update_allowed_levels($class_name::BOARD_UPDATE_ALLOWED_LEVELS);
        }
        if (defined("{$class_name}::BOARD_DELETE_ALLOWED_LEVELS")) {
            $this->set_board_delete_allowed_levels($class_name::BOARD_DELETE_ALLOWED_LEVELS);
        }
        if (defined("{$class_name}::BOARD_LIST_ALLOWED_LEVELS")) {
            $this->set_board_list_allowed_levels($class_name::BOARD_LIST_ALLOWED_LEVELS);
        }
//        if (defined("{$class_name}::BOARD_EXPORT_ALLOWED_LEVELS")) {
//            $this->set_board_export_allowed_levels($class_name::BOARD_EXPORT_ALLOWED_LEVELS);
//        }
    }

    public function set_and_get_next_url_value() {
        $next_url_level = url::get_url_level_count();
        $controller_url_value = "controller_url_{$next_url_level}";
        return url::set_url_rewrite_var($next_url_level, $controller_url_value, FALSE);
    }

    /**
     * @param string $specific_board_to_init 
     * @return \k1lib\html\div|boolean
     */
    public function init_board($specific_board_to_init = NULL) {
        if ($this->security_no_rules_enable === FALSE) {
            if (isset($_GET['no-rules'])) {
                unset($_GET['no-rules']);
            }
        }
        if (empty($specific_board_to_init)) {
            $specific_board_to_init = ($this->controller_board_url_value) ? $this->controller_board_url_value : "no-url";
        }
        switch ($specific_board_to_init) {
            case $this->board_create_url_name:
                $this->board_create_object = new board_create($this, $this->board_create_allowed_levels);
                $this->board_create_object->set_is_enabled($this->board_create_enabled);
                $this->board_create_object->set_board_name($this->board_create_name);
                $this->board_div_content = $this->board_create_object->board_content_div;

                break;

            case $this->board_read_url_name:
                $this->board_read_object = new board_read($this, $this->board_read_allowed_levels);
                $this->board_read_object->set_is_enabled($this->board_read_enabled);
                $this->board_read_object->set_board_name($this->board_read_name);
                $this->board_div_content = $this->board_read_object->board_content_div;
                if (!$this->board_list_enabled || !$this->get_board_list_allowed_for_current_user()) {
                    $this->board_read_object->set_all_data_enable(FALSE);
                }
                if (!$this->board_update_enabled || !$this->get_board_update_allowed_for_current_user()) {
                    $this->board_read_object->set_update_enable(FALSE);
                }
                if (!$this->board_delete_enabled || !$this->get_board_delete_allowed_for_current_user()) {
                    $this->board_read_object->set_delete_enable(FALSE);
                }
                break;

            case $this->board_update_url_name:
                $this->board_update_object = new board_update($this, $this->board_update_allowed_levels);
                $this->board_update_object->set_is_enabled($this->board_update_enabled);
                $this->board_update_object->set_board_name($this->board_update_name);
                $this->board_div_content = $this->board_update_object->board_content_div;

                break;

            case $this->board_delete_url_name:
                $this->board_delete_object = new board_delete($this, $this->board_delete_allowed_levels);
                $this->board_delete_object->set_is_enabled($this->board_delete_enabled);
                $this->board_delete_object->set_board_name($this->board_delete_name);
                $this->board_div_content = $this->board_delete_object->board_content_div;
                break;

            case $this->board_list_url_name:
                $this->board_list_object = new board_list($this, $this->board_list_allowed_levels);
                $this->board_list_object->set_is_enabled($this->board_list_enabled);
                $this->board_list_object->set_board_name($this->board_list_name);
                $this->board_div_content = $this->board_list_object->board_content_div;
                if (!$this->board_create_enabled || !$this->get_board_create_allowed_for_current_user()) {
                    $this->board_list_object->set_create_enable(FALSE);
                }
                break;
            case $this->board_search_url_name:
                $this->board_search_object = new board_search($this, $this->board_list_allowed_levels);
                $this->board_search_object->set_is_enabled($this->board_list_enabled);
                $this->board_search_object->set_board_name($this->board_search_url_name);
                $this->board_div_content = $this->board_search_object->board_content_div;
                break;

            default:
                $this->board_inited = FALSE;
                \k1lib\html\html_header_go(url::do_url($this->controller_root_dir . $this->get_board_list_url_name() . "/"));
                return FALSE;
        }
        $this->board_inited = TRUE;
        return $this->board_div_content;
    }

    public function get_board_create_allowed_for_current_user() {
        if (empty(array_key_exists(\k1lib\session\session_plain::get_user_level(), array_flip($this->board_create_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_board_read_allowed_for_current_user() {
        if (empty(array_key_exists(\k1lib\session\session_plain::get_user_level(), array_flip($this->board_read_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_board_update_allowed_for_current_user() {
        if (empty(array_key_exists(\k1lib\session\session_plain::get_user_level(), array_flip($this->board_update_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_board_delete_allowed_for_current_user() {
        if (empty(array_key_exists(\k1lib\session\session_plain::get_user_level(), array_flip($this->board_delete_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_board_list_allowed_for_current_user() {
        if (empty(array_key_exists(\k1lib\session\session_plain::get_user_level(), array_flip($this->board_list_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function read_url_keys_text_for_create($db_table_name, array &$keys_array_to_return = []) {
        if (isset($this->board_create_object)) {
            /**
             * URL key text management
             */
            $related_url_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), "related_url_keys_text", FALSE);
            if (!empty($related_url_keys_text)) {
                $related_table = $db_table_name;
                $related_db_table = new \k1lib\crudlexs\class_db_table($this->db_table->db, $related_table);
                $related_url_keys_array = \k1lib\sql\table_url_text_to_keys($related_url_keys_text, $related_db_table->get_db_table_config());
                /**
                 * lets fix the non-same key name
                 */
//                \k1lib\sql\resolve_fk_real_fields_names($related_url_keys_array, $this->db_table->get_db_table_config());
                $db_table_config = $this->db_table->get_db_table_config();
                foreach ($db_table_config as $field => $field_config) {
                    if (!empty($field_config['refereced_column_config'])) {
                        $fk_field_name = $field_config['refereced_column_config']['field'];
                        foreach ($related_url_keys_array as $field_current => $value) {
                            if (($field_current == $fk_field_name) && ($field != $field_current)) {
                                $related_url_keys_array[$field] = $value;
                                unset($related_url_keys_array[$field_current]);
                            }
                        }
                    }
                }
                $related_url_keys_array = \k1lib\common\clean_array_with_guide($related_url_keys_array, $db_table_config);
                /////
                $keys_array_to_return = $related_url_keys_array;
                $related_url_keys_text_auth_code = md5(\k1lib\K1MAGIC::get_value() . $related_url_keys_text);
                if (isset($_GET['auth-code']) && ($_GET['auth-code'] === $related_url_keys_text_auth_code)) {
                    $this->db_table->set_field_constants($related_url_keys_array);
                    return $related_url_keys_text;
                } else {
                    $this->board_create_object->set_is_enabled(FALSE);
                    DOM_notification::queue_mesasage(board_base_strings::$error_url_keys_no_auth, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
                    return FALSE;
                }
            } else {
//                $this->board_create_object->set_is_enabled(FALSE);
//                DOM_notification::queue_mesasage(board_base_strings::$error_url_keys_no_keys_text, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
                return FALSE;
            }
        }
    }

    public function read_url_keys_text_for_update() {
        if (isset($this->board_update_object)) {
            /**
             * URL key text management
             */
            $update_row_keys_array = $this->board_update_object->update_object->get_row_keys_array();
            $this->db_table->set_field_constants($update_row_keys_array);
            return FALSE;
        }
    }

    public function read_url_keys_text_for_list($db_table_name, $is_required = TRUE) {
        if (isset($this->board_list_object)) {
            /**
             * URL key text management
             */
            $related_url_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), "related_url_keys_text", FALSE);
            if (!empty($related_url_keys_text)) {
                $related_table = $db_table_name;
                $related_db_table = new \k1lib\crudlexs\class_db_table($this->db_table->db, $related_table);
                $related_url_keys_array = \k1lib\sql\table_url_text_to_keys($related_url_keys_text, $related_db_table->get_db_table_config());
                /**
                 * lets fix the non-same key name
                 */
                $db_table_config = $this->db_table->get_db_table_config();
                foreach ($db_table_config as $field => $field_config) {
                    if (!empty($field_config['refereced_column_config'])) {
                        $fk_field_name = $field_config['refereced_column_config']['field'];
                        foreach ($related_url_keys_array as $field_current => $value) {
                            if (($field_current == $fk_field_name) && ($field != $field_current)) {
                                $related_url_keys_array[$field] = $value;
                                unset($related_url_keys_array[$field_current]);
                            }
                        }
                    }
                }
                $related_url_keys_array = \k1lib\common\clean_array_with_guide($related_url_keys_array, $db_table_config);
                /////
                $related_url_keys_text_auth_code = md5(\k1lib\K1MAGIC::get_value() . $related_url_keys_text);
                if (isset($_GET['auth-code']) && ($_GET['auth-code'] === $related_url_keys_text_auth_code)) {
                    $this->db_table->set_query_filter($related_url_keys_array, TRUE);
                    return $related_url_keys_text;
                } else {
                    $this->board_list_object->set_is_enabled(FALSE);
                    DOM_notification::queue_mesasage(board_base_strings::$error_url_keys_no_auth, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
                    return FALSE;
                }
            } else {
                if ($is_required) {
                    $this->board_list_object->set_is_enabled(FALSE);
                    DOM_notification::queue_mesasage(board_base_strings::$error_url_keys_no_keys_text, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
                    return FALSE;
                }
            }
        }
    }

    public function start_board($specific_board_to_start = NULL) {
        $this->board_started = TRUE;
        if ($this->board_inited) {
            if (empty($specific_board_to_start)) {
                $specific_board_to_start = $this->controller_board_url_value;
            }
            switch ($specific_board_to_start) {
                case $this->board_create_url_name:
                    return $this->board_create_object->start_board();
                    break;

                case $this->board_read_url_name:
                    return $this->board_read_object->start_board();
                    break;

                case $this->board_update_url_name:
                    return $this->board_update_object->start_board();
                    break;

                case $this->board_delete_url_name:
                    return $this->board_delete_object->start_board();
                    break;

                case $this->board_list_url_name:
                    return $this->board_list_object->start_board();
                    break;

                case $this->board_search_url_name:
                    return $this->board_search_object->start_board();
                    break;

                default:
                    $this->board_started = FALSE;
                    \k1lib\html\html_header_go($this->controller_root_dir . $this->get_board_list_url_name() . "/");
                    return FALSE;
            }
        } else {
            $this->board_started = FALSE;
            trigger_error(__METHOD__ . ' ' . controller_base_strings::$error_board_not_inited, E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * 
     * @param boolean $do_echo
     * @param string $specific_board_to_exec
     * @return \k1lib\html\div
     */
    public function exec_board($specific_board_to_exec = NULL) {
        $this->board_executed = TRUE;

        if ($this->board_started) {
            if (empty($specific_board_to_exec)) {
                $specific_board_to_exec = $this->controller_board_url_value;
            }
            switch ($specific_board_to_exec) {
                case $this->board_create_url_name:
                    return $this->board_create_object->exec_board();

                case $this->board_read_url_name:
                    return $this->board_read_object->exec_board();

                case $this->board_update_url_name:
                    return $this->board_update_object->exec_board();

                case $this->board_delete_url_name:
                    return $this->board_delete_object->exec_board();

                case $this->board_list_url_name:
                    return $this->board_list_object->exec_board();

                case $this->board_search_url_name:
                    return $this->board_search_object->exec_board();

                default:
                    $this->board_executed = FALSE;
                    \k1lib\html\html_header_go($this->controller_root_dir . $this->get_board_list_url_name() . "/");
                    break;
            }
        } else {
            $this->board_executed = FALSE;
            trigger_error(__METHOD__ . ' ' . controller_base_strings::$error_board_not_started, E_USER_WARNING);
            return FALSE;
        }
    }

    public function finish_board($do_redirect = TRUE, $custom_redirect = FALSE) {
        $this->board_finished = TRUE;

        if ($this->board_started) {
            switch ($this->controller_board_url_value) {
                case $this->board_create_url_name:
                    return $this->board_create_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_read_url_name:
                    return $this->board_read_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_update_url_name:
                    return $this->board_update_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_delete_url_name:
                    return $this->board_delete_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_list_url_name:
                    return $this->board_list_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_search_url_name:
                    return $this->board_search_object->finish_board($do_redirect, $custom_redirect);

                default:
                    $this->board_finished = FALSE;
                    \k1lib\html\html_header_go($this->controller_root_dir . $this->get_board_list_url_name() . "/");
                    break;
            }
        } else {
            $this->board_finished = FALSE;
            trigger_error(__METHOD__ . ' ' . controller_base_strings::$error_board_not_executed, E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * SIMPLE SETTERS AND GETTERS
     */
    function get_controller_url_value() {
        return $this->controller_url_value;
    }

    function get_controller_root_dir() {
        return $this->controller_root_dir;
    }

    function get_controller_board_url_value() {
        return $this->controller_board_url_value;
    }

    function get_board_list_enabled() {
        return $this->board_list_enabled;
    }

    function get_board_create_enabled() {
        return $this->board_create_enabled;
    }

    function get_board_read_enabled() {
        return $this->board_read_enabled;
    }

    function get_board_update_enabled() {
        return $this->board_update_enabled;
    }

    function get_board_delete_enabled() {
        return $this->board_delete_enabled;
    }

    function set_board_list_enabled($board_list_enabled) {
        if ($board_list_enabled !== FALSE) {
            $this->board_list_enabled = TRUE;
        } else {
            $this->board_list_enabled = FALSE;
        }
    }

    function set_board_create_enabled($board_create_enabled) {
        if ($board_create_enabled !== FALSE) {
            $this->board_create_enabled = TRUE;
        } else {
            $this->board_create_enabled = FALSE;
        }
    }

    function set_board_read_enabled($board_read_enabled) {
        if ($board_read_enabled !== FALSE) {
            $this->board_read_enabled = TRUE;
        } else {
            $this->board_read_enabled = FALSE;
        }
    }

    function set_board_update_enabled($board_update_enabled) {
        if ($board_update_enabled !== FALSE) {
            $this->board_update_enabled = TRUE;
        } else {
            $this->board_update_enabled = FALSE;
        }
    }

    function set_board_delete_enabled($board_delete_enabled) {
        if ($board_delete_enabled !== FALSE) {
            $this->board_delete_enabled = TRUE;
        } else {
            $this->board_delete_enabled = FALSE;
        }
    }

    function get_url_redirect_after_delete() {
        return $this->url_redirect_after_delete;
    }

    function set_url_redirect_after_delete($url_redirect_after_delete) {
        $this->url_redirect_after_delete = $url_redirect_after_delete;
    }

    public function get_state() {
        return $this->db_table->get_state();
    }

    function set_template_place_name_html_title($template_place_name_html_title) {
        $this->template_place_name_html_title = $template_place_name_html_title;
    }

    function get_template_place_name_html_title() {
        return $this->template_place_name_html_title;
    }

    function set_template_place_name_controller_name($template_place_name_controller_name) {
        $this->template_place_name_controller_name = $template_place_name_controller_name;
    }

    function get_template_place_name_controller_name() {
        return $this->template_place_name_controller_name;
    }

    function set_template_place_name_board_name($template_place_name_board_name) {
        $this->template_place_name_board_name = $template_place_name_board_name;
    }

    function get_template_place_name_board_name() {
        return $this->template_place_name_board_name;
    }

    function set_board_create_name($board_new_name) {
        $this->board_create_name = $board_new_name;
    }

    function set_board_read_name($board_view_name) {
        $this->board_read_name = $board_view_name;
    }

    function set_board_update_name($board_update_name) {
        $this->board_update_name = $board_update_name;
    }

    function set_board_delete_name($board_delete_name) {
        $this->board_delete_name = $board_delete_name;
    }

    function set_board_list_name($board_list_name) {
        $this->board_list_name = $board_list_name;
    }

    function get_board_list_url_name() {
        return $this->board_list_url_name;
    }

    function get_board_create_url_name() {
        return $this->board_create_url_name;
    }

    function get_board_read_url_name() {
        return $this->board_read_url_name;
    }

    function get_board_update_url_name() {
        return $this->board_update_url_name;
    }

    function get_board_delete_url_name() {
        return $this->board_delete_url_name;
    }

    function set_board_list_url_name($board_list_url_name) {
        $this->board_list_url_name = $board_list_url_name;
        if ($board_list_url_name === FALSE) {
            $this->set_board_list_enabled(FALSE);
        }
    }

    function set_board_create_url_name($board_create_url_name) {
        $this->board_create_url_name = $board_create_url_name;
        if ($board_create_url_name === FALSE) {
            $this->set_board_create_enabled(FALSE);
        }
    }

    function set_board_read_url_name($board_read_url_name) {
        $this->board_read_url_name = $board_read_url_name;
        if ($board_read_url_name === FALSE) {
            $this->set_board_read_enabled(FALSE);
        }
    }

    function set_board_update_url_name($board_update_url_name) {
        $this->board_update_url_name = $board_update_url_name;
        if ($board_update_url_name === FALSE) {
            $this->set_board_update_enabled(FALSE);
        }
    }

    function set_board_delete_url_name($board_delete_url_name) {
        $this->board_delete_url_name = $board_delete_url_name;
        if ($board_delete_url_name === FALSE) {
            $this->set_board_delete_enabled(FALSE);
        }
    }

    function get_controller_board_allowed_leves() {
        return $this->controller_board_allowed_leves;
    }

    function set_controller_board_allowed_leves($controller_board_allowed_leves) {
        $this->controller_board_allowed_leves = $controller_board_allowed_leves;
    }

    function get_board_list_allowed_levels() {
        return $this->board_list_allowed_levels;
    }

    function get_board_create_allowed_levels() {
        return $this->board_create_allowed_levels;
    }

    function get_board_read_allowed_levels() {
        return $this->board_read_allowed_levels;
    }

    function get_board_update_allowed_levels() {
        return $this->board_update_allowed_levels;
    }

    function get_board_delete_allowed_levels() {
        return $this->board_delete_allowed_levels;
    }

    function set_board_list_allowed_levels($board_list_allowed_levels) {
        $this->board_list_allowed_levels = $board_list_allowed_levels;
    }

    function set_board_create_allowed_levels($board_create_allowed_levels) {
        $this->board_create_allowed_levels = $board_create_allowed_levels;
    }

    function set_board_read_allowed_levels($board_read_allowed_levels) {
        $this->board_read_allowed_levels = $board_read_allowed_levels;
    }

    function set_board_update_allowed_levels($board_update_allowed_levels) {
        $this->board_update_allowed_levels = $board_update_allowed_levels;
    }

    function set_board_delete_allowed_levels($board_delete_allowed_levels) {
        $this->board_delete_allowed_levels = $board_delete_allowed_levels;
    }

    /**
     * ON BOARD
     */
    public function on_board_create() {
        if (isset($this->board_create_object) && $this->board_create_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_create
     */
    public function board_create() {
        if (isset($this->board_create_object) && $this->board_create_object->get_is_enabled()) {
            return $this->board_create_object;
        } else {
            return FALSE;
        }
    }

    public function on_object_create() {
        if (isset($this->board_create_object->create_object)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return creating
     */
    public function object_create() {
        if (isset($this->board_create_object->create_object)) {
            return $this->board_create_object->create_object;
        } else {
            return FALSE;
        }
    }

    public function on_board_read() {
        if (isset($this->board_read_object) && $this->board_read_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_read
     */
    public function board_read() {
        if (isset($this->board_read_object) && $this->board_read_object->get_is_enabled()) {
            return $this->board_read_object;
        } else {
            return FALSE;
        }
    }

    public function on_object_read() {
        if (isset($this->board_read_object->read_object)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return reading
     */
    public function object_read() {
        if (isset($this->board_read_object->read_object)) {
            return $this->board_read_object->read_object;
        } else {
            return FALSE;
        }
    }

    public function on_board_update() {
        if (isset($this->board_update_object) && $this->board_update_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_update
     */
    public function board_update() {
        if (isset($this->board_update_object) && $this->board_update_object->get_is_enabled()) {
            return $this->board_update_object;
        } else {
            return FALSE;
        }
    }

    public function on_object_update() {
        if (isset($this->board_update_object->update_object)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return updating
     */
    public function object_update() {
        if (isset($this->board_update_object->update_object)) {
            return $this->board_update_object->update_object;
        } else {
            return FALSE;
        }
    }

    public function on_board_delete() {
        if (isset($this->board_delete_object) && $this->board_delete_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_delete
     */
    public function board_delete() {
        if (isset($this->board_delete_object) && $this->board_delete_object->get_is_enabled()) {
            return $this->board_delete_object;
        } else {
            return FALSE;
        }
    }

    public function on_board_list() {
        if (isset($this->board_list_object) && $this->board_list_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_list
     */
    public function board_list() {
        if (isset($this->board_list_object) && $this->board_list_object->get_is_enabled()) {
            return $this->board_list_object;
        } else {
            return FALSE;
        }
    }

    public function on_object_list() {
        if (isset($this->board_list_object->list_object)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return listing
     */
    public function object_list() {
        if (isset($this->board_list_object->list_object)) {
            return $this->board_list_object->list_object;
        } else {
            return FALSE;
        }
    }

    public function get_security_no_rules_enable() {
        return $this->security_no_rules_enable;
    }

    public function set_security_no_rules_enable($security_no_rules_enable) {
        $this->security_no_rules_enable = $security_no_rules_enable;
    }

}

// ./src/crudlexs/object_classes/_object_base.php


namespace k1lib\crudlexs;

use \k1lib\common_strings as common_strings;
use \k1lib\urlrewrite\url as url;
use \k1lib\db\security\db_table_aliases as db_table_aliases;
use \k1lib\session\session_plain as session_plain;
use \k1lib\forms\file_uploads as file_uploads;
use k1lib\html\DOM as DOM;
use \k1lib\notifications\on_DOM as DOM_notification;

interface crudlexs_base_interface {

    public function do_html_object();
}

class crudlexs_base {

    const USE_KEY_FIELDS = 1;
    const USE_ALL_FIELDS = 2;
    const USE_LABEL_FIELDS = 3;

    static protected $k1magic_value = null;

    /**
     *
     * @var \k1lib\crudlexs\class_db_table 
     */
    public $db_table;

    /**
     *
     * @var \k1lib\html\div
     */
    protected $div_container;

    /**
     * Unique ID for each object
     * @var string
     */
    protected $object_id = null;

    /**
     * General CSS class
     * @var string
     */
    protected $css_class = null;

    /**
     * If some goes BAD to do not keep going for others methods, you have to put this on FALSE;
     * @var boolean
     */
    private $is_valid = FALSE;

    /**
     * @var string
     */
    protected $notifications_div_id = "k1lib-output";

    static function get_k1magic_value() {
        return self::$k1magic_value;
    }

    static function set_k1magic_value($k1magic_value) {
        self::$k1magic_value = $k1magic_value;
    }

    public function __construct(\k1lib\crudlexs\class_db_table $db_table) {
        $this->db_table = $db_table;
        $this->div_container = new \k1lib\html\div();
        $this->is_valid = TRUE;
    }

    function is_valid() {
        return $this->is_valid;
    }

    function make_invalid() {
        $this->is_valid = FALSE;
    }

    /**
     * Always to create the object you must have a valid DB Table object already 
     * @param \k1lib\crudlexs\class_db_table $db_table DB Table object
     */
    public function __toString() {
        if ($this->get_state()) {
            return "1";
        } else {
            return "0";
        }
    }

    public function get_state() {
        if (empty($this->db_table) || !$this->is_valid()) {
            return FALSE;
        } else {
            if ($this->db_table->get_state() || !$this->is_valid()) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    function get_object_id() {
        return $this->object_id;
    }

    function set_object_id($class_name) {
        if (isset($this->db_table) && key_exists($this->db_table->get_db_table_name(), db_table_aliases::$aliases)) {
            $table_name = db_table_aliases::$aliases[$this->db_table->get_db_table_name()];
        } else if (isset($this->db_table)) {
            $table_name = $this->db_table->get_db_table_name();
        } else {
            $table_name = "no-table";
        }
        return $this->object_id = $table_name . "-" . basename(str_replace("\\", "/", $class_name));
    }

    function get_css_class() {
        return $this->css_class;
    }

    function set_css_class($class_name) {
        $this->css_class = basename(str_replace("\\", "/", $class_name));
    }

    public function get_notifications_div_id() {
        return $this->notifications_div_id;
    }

    public function set_notifications_div_id($notifications_div_id) {
        $this->notifications_div_id = $notifications_div_id;
    }

}

class crudlexs_base_with_data extends crudlexs_base {

    /**
     *
     * @var Array 
     */
    public $db_table_data = FALSE;

    /**
     *
     * @var Boolean 
     */
    protected $db_table_data_keys = FALSE;
    // FILTERS

    /**
     *
     * @var Array 
     */
    public $db_table_data_filtered = FALSE;

    /**
     *
     * @var String
     */
    protected $auth_code = null;
    protected $auth_code_personal = null;
    protected $link_on_field_filter_applied = false;
    protected $back_url;
    protected $row_keys_text = null;
    protected $row_keys_array = null;

    /**
     *
     * @var boolean 
     */
    protected $skip_auto_code_verification = FALSE;

    /**
     *
     * @var boolean 
     */
    protected $skip_blanks_on_filters = FALSE;

    /**
     *
     * @var Boolean
     */
    protected $do_table_field_name_encrypt = FALSE;

    /**
     * If TRUE all file uploads will be represented as links, if OFF images will be images. PDF and others by now allways will be links.
     * @var boolean
     */
    protected $force_file_uploads_as_links = TRUE;
    protected $custom_field_labels = [];
    protected $fields_to_hide = [];

    /**
     * Always to create the object you must have a valid DB Table object already 
     * @param \k1lib\crudlexs\class_db_table $db_table DB Table object
     */
    public function __construct(\k1lib\crudlexs\class_db_table $db_table, $row_keys_text = null, $custom_auth_code = null) {
        $this->back_url = \k1lib\urlrewrite\get_back_url();

        if (!empty($row_keys_text)) {
            $this->row_keys_text = $row_keys_text;
            if (!$this->skip_auto_code_verification) {
                if (isset($_GET['auth-code']) || !empty($custom_auth_code)) {
                    if (!empty($custom_auth_code)) {
                        $auth_code = $custom_auth_code;
                    } else {
                        $auth_code = $_GET['auth-code'];
                    }
                    $auth_expected = md5(\k1lib\K1MAGIC::get_value() . $this->row_keys_text);
                    $auth_personal_expected = md5(session_plain::get_user_hash() . $this->row_keys_text);

                    if (($auth_code === $auth_expected) || ($auth_code === $auth_personal_expected)) {
                        parent::__construct($db_table);
                        $this->auth_code = $auth_expected;
                        $this->auth_code_personal = $auth_personal_expected;
                        $this->row_keys_array = \k1lib\sql\table_url_text_to_keys($this->row_keys_text, $this->db_table->get_db_table_config());
                        $this->db_table->set_query_filter($this->row_keys_array, TRUE);
                        $this->is_valid = TRUE;
                    } else {
                        DOM_notification::queue_mesasage(object_base_strings::$error_bad_auth_code, "alert", $this->notifications_div_id, common_strings::$error);
                        $this->is_valid = FALSE;
                    }
                } else {
                    DOM_notification::queue_mesasage(object_base_strings::$alert_empty_auth_code, "alert", $this->notifications_div_id, common_strings::$alert);
                    $this->is_valid = FALSE;
                }
            } else {
                parent::__construct($db_table);
            }
        } else {
            parent::__construct($db_table);
        }
        $this->set_object_id(get_class($this));
        $this->set_css_class(get_class($this));
    }

    public function get_auth_code() {
        return $this->auth_code;
    }

    public function set_auth_code($row_keys_text) {
        $this->auth_code = md5(\k1lib\K1MAGIC::get_value() . $row_keys_text);
    }

    public function get_auth_code_personal() {
        return $this->auth_code_personal;
    }

    public function set_auth_code_personal($row_keys_text) {
        $this->auth_code_personal = md5(session_plain::get_user_hash() . $row_keys_text);
    }

    public function get_do_table_field_name_encrypt() {
        return $this->do_table_field_name_encrypt;
    }

    public function set_do_table_field_name_encrypt($do_table_field_name_encryp = TRUE) {
        $this->do_table_field_name_encrypt = $do_table_field_name_encryp;
    }

    /**
     * 
     * @return Array Data with data[0] as table fields and data[1..n] for data rows. FALSE on no data.
     */
    public function load_db_table_data($show_rule = null) {
        if ($this->is_valid()) {
            if (!empty($show_rule)) {
                $this->db_table->set_db_table_show_rule($show_rule);
            }
            $this->db_table_data = $this->db_table->get_data();
            if ($this->db_table_data) {
                $this->db_table_data_filtered = $this->db_table_data;
                $this->db_table_data_keys = $this->db_table->get_data_keys();
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function simulate_db_data_with_array(array $data_array) {
        if (array_key_exists(0, $data_array)) {
            $headers_count = count($data_array[0]);
            foreach ($data_array as $row => $row_array) {
                if ($row === 0) {
                    continue;
                }
                if (count($row_array) !== $headers_count) {
                    trigger_error(__METHOD__ . " " . object_base_strings::$error_array_not_compatible, E_USER_WARNING);
                    return FALSE;
                }
            }
            $this->db_table_data = $data_array;
            $this->db_table_data_filtered = $data_array;
            return TRUE;
        }
        trigger_error(__METHOD__ . " " . object_base_strings::$error_array_not_compatible, E_USER_WARNING);
        return FALSE;
    }

    public function simulate_db_data_keys_with_array(array $data_array) {
        if (array_key_exists(0, $data_array)) {
            $headers_count = count($data_array[0]);
            foreach ($data_array as $row => $row_array) {
                if ($row === 0) {
                    continue;
                }
                if (count($row_array) !== $headers_count) {
                    trigger_error(__METHOD__ . " " . object_base_strings::$error_array_not_compatible, E_USER_WARNING);
                    return FALSE;
                }
            }
            $this->db_table_data_keys = $data_array;
            return TRUE;
        }
        trigger_error(__METHOD__ . " " . object_base_strings::$error_array_not_compatible, E_USER_WARNING);
        return FALSE;
    }

    public function apply_label_filter() {
        if (empty($this->db_table_data) || !is_array($this->db_table_data)) {
//            trigger_error(__METHOD__ . " - Can't work with an empty result", E_USER_WARNING);
            return FALSE;
        } else {
            $db_table_config = $this->db_table->get_db_table_config();
            if (isset($this->db_table_data[0]) && (count($this->db_table_data[0]) > 0)) {
                foreach ($this->db_table_data[0] as $index => $field_name) {
                    if (isset($db_table_config[$field_name]['label'])) {
                        $this->db_table_data_filtered[0][$index] = $db_table_config[$field_name]['label'];
                    } else {
                        if (isset($this->custom_field_labels[$field_name])) {
                            $this->db_table_data_filtered[0][$index] = $this->custom_field_labels[$field_name];
                        } else {
                            $this->db_table_data_filtered[0][$index] = $field_name;
                        }
                    }
                }
            } else {
                return FALSE;
            }
            return TRUE;
        }
    }

    public function apply_field_label_filter(array $apply_to = []) {
        if ($this->get_state()) {
            if (empty($this->db_table_data) || !is_array($this->db_table_data)) {
                trigger_error(__METHOD__ . " " . object_base_strings::$error_no_table_data, E_USER_WARNING);
                return FALSE;
            } else {
                $table_config_array = $this->db_table->get_db_table_config();
                foreach ($this->db_table_data as $index => $row_data) {
                    if ($index === 0) {
                        continue;
                    }
                    foreach ($row_data as $field => $value) {
                        if (!empty($apply_to) && !array_key_exists($field, array_flip($apply_to))) {
                            continue;
                        }
                        if (!empty($table_config_array[$field]['refereced_column_config'])) {
                            $refereced_column_config = $table_config_array[$field]['refereced_column_config'];
                            while (!empty($refereced_column_config['refereced_column_config'])) {
                                $refereced_column_config = $refereced_column_config['refereced_column_config'];
                            }
                            $fk_table = $refereced_column_config['table'];
//                            $fk_table_field = $refereced_column_config['field'];
//                            $fk_db_table = new class_db_table($this->db_table->db, $fk_table);
//                            $fk_label_field = $fk_db_table->get_db_table_label_fields();
                            $fk_label_field = \k1lib\sql\get_fk_field_label($this->db_table->db, $fk_table, [$field => $value], $table_config_array);
//                            $this->db_table_data_filtered[$index][$field] = $fk_label_field;
                            if (!empty($fk_label_field)) {
//                                d($this->db_table_data_filtered[$index][$field], TRUE);
                                if (is_object($this->db_table_data_filtered[$index][$field])) {
                                    $this->db_table_data_filtered[$index][$field]->set_value($fk_label_field);
                                } else {
                                    $this->db_table_data_filtered[$index][$field] = $fk_label_field;
                                }
                            }
                        }
                    }
                }

                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    public function apply_file_uploads_filter() {
        if ($this->get_state()) {
            if (empty($this->db_table_data) || !is_array($this->db_table_data)) {
                trigger_error(__METHOD__ . " " . object_base_strings::$error_no_table_data, E_USER_WARNING);
                return FALSE;
            } else {
                $table_config_array = $this->db_table->get_db_table_config();
                $file_upload_fields = [];
                foreach ($table_config_array as $field => $options) {
                    if ($options['validation'] == 'file-upload') {
                        $file_upload_fields[$field] = $options['file-type'];
                        $file_upload_table[$field] = $options['table'];
                    }
                }
                if (!empty($file_upload_fields)) {
                    foreach ($file_upload_fields as $field => $file_type) {
                        switch ($file_type) {
                            case "image":
//                                $div_container = new \k1lib\html\div();

                                $img_tag = new \k1lib\html\img(file_uploads::get_uploads_url($options['table']) . "--fieldvalue--");
                                $img_tag->set_attrib("onClick", "window.open(this.getAttribute('src'),'imgWindow', 'height=1024,width=768,toolbar=0,location=0,menubar=0');", TRUE);
                                $img_tag->set_attrib("class", "k1lib-data-img", TRUE);

                                return $this->apply_html_tag_on_field_filter($img_tag, array_keys($file_upload_fields));

                            default:
                                $link_tag = new \k1lib\html\a(url::do_url(file_uploads::get_uploads_url() . "{$file_upload_table[$field]}/--fieldvalue--"), "--fieldvalue--", "_blank");
                                $link_tag->set_attrib("class", "k1lib-data-link", TRUE);
                                return $this->apply_html_tag_on_field_filter($link_tag, array_keys($file_upload_fields));
                        }
                    }
                }
            }
        } else {
            return FALSE;
        }
    }

    public function apply_link_on_field_filter($link_to_apply, $fields_to_change = null, $custom_field_value = null, $href_target = null) {
        if ($this->get_state()) {
            $this->link_on_field_filter_applied = true;
            $a_tag = new \k1lib\html\a(url::do_url($link_to_apply), "", $href_target);
            $a_tag->set_attrib("class", "k1lib-link-filter", TRUE);
            if (empty($fields_to_change)) {
                $fields_to_change = crudlexs_base::USE_KEY_FIELDS;
            }
            return $this->apply_html_tag_on_field_filter($a_tag, $fields_to_change, $custom_field_value);
        } else {
            return FALSE;
        }
    }

    public function apply_html_tag_on_field_filter(\k1lib\html\tag $tag_object, $fields_to_change = crudlexs_base::USE_KEY_FIELDS, $custom_field_value = null) {
        if ($this->get_state()) {
            if (empty($this->db_table_data) || !is_array($this->db_table_data)) {
//                trigger_error(__METHOD__ . " " . object_base_strings::$error_no_table_data, E_USER_NOTICE);
                return FALSE;
            } else {
                if ($fields_to_change == crudlexs_base::USE_KEY_FIELDS) {
                    $fields_to_change = \k1lib\sql\get_db_table_keys_array($this->db_table->get_db_table_config());
                } elseif ($fields_to_change == crudlexs_base::USE_ALL_FIELDS) {
                    $fields_to_change = $this->db_table_data[0];
                } elseif ($fields_to_change == crudlexs_base::USE_LABEL_FIELDS) {
                    $fields_to_change = \k1lib\sql\get_db_table_label_fields($this->db_table->get_db_table_config());
                    if (empty($fields_to_change)) {
                        $fields_to_change = \k1lib\sql\get_db_table_keys_array($this->db_table->get_db_table_config());
                    }
                } elseif (empty($fields_to_change)) {
                    $fields_to_change = $this->db_table_data[0];
                } else {
                    if (!is_array($fields_to_change) && is_string($fields_to_change)) {
                        $fields_to_change = Array($fields_to_change);
                    }
                }
                $table_constant_fields = $this->db_table->get_constant_fields();
                if (!empty($fields_to_change)) {
                    foreach ($fields_to_change as $field_to_change) {
                        foreach ($this->db_table_data_filtered as $index => $row_data) {
                            if ($index === 0) {
                                continue;
                            }
                            if (!array_key_exists($field_to_change, $row_data)) {
                                trigger_error(__METHOD__ . "The field to change ($field_to_change) do no exist ", E_USER_NOTICE);
                                continue;
                            } else {
                                // Let's clone the $tag_object to reuse it properly
                                $tag_object_original = clone $tag_object;

                                $custom_field_value_original = $custom_field_value;

                                if ($this->skip_blanks_on_filters && empty($row_data[$field_to_change])) {
                                    continue;
                                }

                                $tag_object->set_value($row_data[$field_to_change]);

                                if (is_object($tag_object)) {
                                    $a_tags = [];
                                    $tag_value = null;
                                    if (get_class($tag_object) == "k1lib\html\a") {
                                        $tag_href = $tag_object->get_attribute("href");
                                        $tag_value = $tag_object->get_value();
                                    } elseif (get_class($tag_object) == "k1lib\html\img") {
                                        $tag_href = $tag_object->get_attribute("src");
                                        $tag_value = $tag_object->get_attribute("alt");
                                    } else {
                                        // Let's try to get an A object from this object searching for it
                                        $a_tags = $tag_object->get_elements_by_tag("a");
                                        if (count($a_tags) === 1) {
                                            $tag_href = $a_tags[0]->get_attribute("href");
                                            $tag_value = $a_tags[0]->get_value();
                                        } else {
                                            // TODO: CHECK THIS! - WTF line
//                                    $tag_href = $tag_object->get_value();
                                            $tag_href = NULL;
                                        }
                                    }
                                    if (!empty($this->db_table_data_keys) && !empty($tag_href)) {
                                        if (is_array($custom_field_value)) {
                                            foreach ($custom_field_value as $key => $field_value) {
                                                if (isset($row_data[$field_value])) {
                                                    $custom_field_value[$key] = $this->db_table_data[$index][$field_value];
                                                }
                                                if (isset($table_constant_fields[$field_value])) {
                                                    $custom_field_value[$key] = $table_constant_fields[$field_value];
                                                }
                                            }
                                            $custom_field_value = implode("--", $custom_field_value);
                                        }

                                        $key_array_text = \k1lib\sql\table_keys_to_text($this->db_table_data_keys[$index], $this->db_table->get_db_table_config());
                                        $auth_code = md5(\k1lib\K1MAGIC::get_value() . $key_array_text);

                                         /**
                                         * HREF STR_REPLACE
                                         */
                                        $tag_href = str_replace("--rowkeys--", urlencode($key_array_text), $tag_href);
                                        $tag_href = str_replace("--fieldvalue--", urlencode($row_data[$field_to_change]), $tag_href);
                                        // TODO: Why did I needed this ? WFT Line
                                        $actual_custom_field_value = str_replace("--fieldvalue--", urlencode($row_data[$field_to_change]), $custom_field_value);
                                        $tag_href = str_replace("--customfieldvalue--", urlencode($actual_custom_field_value), $tag_href);
                                        $tag_href = str_replace("--authcode--", $auth_code, $tag_href);
                                        $tag_href = str_replace("--fieldauthcode--", md5(\k1lib\K1MAGIC::get_value() . (($actual_custom_field_value) ? $actual_custom_field_value : $row_data[$field_to_change])), $tag_href);
                                        /**
                                         * VALUE STR_REPLACE
                                         */
                                        $tag_value = str_replace("--rowkeys--", $key_array_text, $tag_value);
                                        $tag_value = str_replace("--fieldvalue--", $row_data[$field_to_change], $tag_value);
                                        $tag_value = str_replace("--customfieldvalue--", $actual_custom_field_value, $tag_value);
                                        $tag_value = str_replace("--authcode--", $auth_code, $tag_value);
                                        $tag_value = str_replace("--fieldauthcode--", md5(\k1lib\K1MAGIC::get_value() . (($actual_custom_field_value) ? $actual_custom_field_value : $row_data[$field_to_change])), $tag_value);

                                        if (get_class($tag_object) == "k1lib\html\a") {
                                            $tag_object->set_attrib("href", $tag_href);
                                            $tag_object->set_value($tag_value);
                                        }
                                        if (get_class($tag_object) == "k1lib\html\img") {
                                            $tag_object->set_attrib("src", $tag_href);
                                        }
                                        // get-elements-by-tags fix
                                        foreach ($a_tags as $a_tag) {
                                            $a_tag->set_attrib("href", $tag_href);
                                            $a_tag->set_value($tag_value);
                                        }
                                    }
                                } else {
                                    trigger_error("Not a HTML_TAG Object", E_USER_WARNING);
                                }
                                $this->db_table_data_filtered[$index][$field_to_change] = $tag_object;
                                // Clean it... $tag_object 
                                unset($tag_object);
                                // Let's clone the original to re use it
                                $tag_object = clone $tag_object_original;
                                $custom_field_value = $custom_field_value_original;
                            }
                        }
                    }
                }
                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    public function get_link_on_field_filter_applied() {
        return $this->link_on_field_filter_applied;
    }

    public function get_back_url() {
        return $this->back_url;
    }

    public function set_back_url($back_url) {
        $this->back_url = $back_url;
    }

    function get_row_keys_text() {
        if (!empty($this->row_keys_text)) {
            return $this->row_keys_text;
        } else {
            return FALSE;
        }
    }

    function get_row_keys_array() {
        if (!empty($this->row_keys_array)) {
            return $this->row_keys_array;
        } else {
            return FALSE;
        }
    }

    public function encrypt_field_name($field_name) {
        // first, we need to know in what position is the field on the table design.
        if (isset($_SESSION['CRUDLEXS-RND']) && !empty($_SESSION['CRUDLEXS-RND'])) {
            $rnd = $_SESSION['CRUDLEXS-RND'];
        } else {
            $rnd = rand(5000, 10000);
            $_SESSION['CRUDLEXS-RND'] = $rnd;
        }
        if (!$this->do_table_field_name_encrypt) {
            return $field_name;
        } else {
            $field_pos = 0;
            if (key_exists($field_name, $this->db_table->get_db_table_config())) {
                foreach ($this->db_table->get_db_table_config() as $field => $config) {
                    if ($field == $field_name) {
                        if ($config['alias']) {
                            return $config['alias'];
                        }
                        break;
                    }
                    $field_pos++;
                }
//            $new_field_name = "k1_" . \k1lib\utils\decimal_to_n36($field_pos);
                $new_field_name = "k1_" . \k1lib\utils\decimal_to_n36($field_pos + $rnd);
                return $new_field_name;
            } else {
                return $field_name;
            }
        }
    }

    public function encrypt_field_names($data_array) {
        $encoded_data_array = [];
        foreach ($data_array as $field => $value) {
            $encoded_data_array[$this->encrypt_field_name($field)] = $value;
        }
        return $encoded_data_array;
    }

    public function decrypt_field_name($encrypted_name) {
        if (strstr($encrypted_name, "k1_") !== FALSE) {
            list($prefix, $n36_number) = explode("_", $encrypted_name);
            if (isset($_SESSION['CRUDLEXS-RND']) && !empty($_SESSION['CRUDLEXS-RND'])) {
                $rnd = $_SESSION['CRUDLEXS-RND'];
            } else {
                trigger_error(__METHOD__ . ' ' . object_base_strings::$error_no_session_random, E_USER_ERROR);
            }
            $field_position = \k1lib\utils\n36_to_decimal($n36_number) - $rnd;
            $fields_from_table_config = array_keys($this->db_table->get_db_table_config());
//            $field_position = \k1lib\utils\n36_to_decimal($n36_number);
            return $fields_from_table_config[$field_position];
        } else {
            foreach ($this->db_table->get_db_table_config() as $field => $config) {
                if ($config['alias'] == $encrypted_name) {
                    return $field;
                }
            }
            return $encrypted_name;
        }
    }

    public function decrypt_field_names($data_array) {
        $decoded_data_array = [];
        foreach ($data_array as $field => $value) {
            $decoded_data_array[$this->decrypt_field_name($field)] = $value;
        }
        return $decoded_data_array;
    }

    public function get_labels_from_data($row = 1) {
        if ($this->db_table_data) {
            $data_label = \k1lib\sql\get_db_table_label_fields_from_row($this->db_table_data_filtered[$row], $this->db_table->get_db_table_config());
            if (!empty($data_label)) {
                return $data_label;
            } else {
                return NULL;
            }
        } else {
            return FALSE;
        }
    }

    public function remove_labels_from_data_filtered($row = 1) {
        if ($this->db_table_data) {
            $label_fields_array = \k1lib\sql\get_db_table_label_fields($this->db_table->get_db_table_config());
            foreach ($label_fields_array as $field) {
                unset($this->db_table_data_filtered[$row][$field]);
            }
        }
    }

    public function get_db_table_data() {
        return $this->db_table_data;
    }

    public function get_db_table_data_keys() {
        return $this->db_table_data_keys;
    }

    public function get_db_table_data_filtered() {
        return $this->db_table_data_filtered;
    }

    /**
     * @return array
     */
    function get_custom_field_labels() {
        return $this->custom_field_labels;
    }

    /**
     * @param array $custom_field_labels
     */
    function set_custom_field_labels(array $custom_field_labels) {
        $this->custom_field_labels = $custom_field_labels;
    }

    /**
     * @return array
     */
    public function get_fields_to_hide() {
        return $this->fields_to_hide;
    }

    /**
     * @param array $fields_to_hide
     */
    public function set_fields_to_hide(array $fields_to_hide) {
        $this->fields_to_hide = $fields_to_hide;
    }

}

// ./src/crudlexs/object_classes/creating.php


namespace k1lib\crudlexs;

use k1lib\notifications\on_DOM as DOM_notification;

/**
 * 
 */
class creating extends crudlexs_base_with_data implements crudlexs_base_interface {

    /**
     * @var Array
     */
    protected $post_incoming_array = [];

    /**
     * @var boolean
     */
    protected $post_data_catched = FALSE;

    /**
     * @var Array
     */
    protected $post_validation_errors = [];

    /**
     * @var array
     */
    protected $post_password_fields = [];
    protected $object_state = "create";

    /**
     *
     * @var Boolean
     */
    protected $enable_foundation_form_check = FALSE;
    protected $show_cancel_button = TRUE;
    protected $inserted_result = NULL;
    protected $inserted = NULL;
    protected $html_form_column_classes = "large-8 medium-10 small-11";
    protected $html_column_classes = "small-12 column";

    public function __construct($db_table, $row_keys_text) {
        parent::__construct($db_table, $row_keys_text);
    }

    /**
     * Override the original function to create an empty array the meets the requiriements for all the metods
     * @return boolean
     */
    public function load_db_table_data($blank_data = FALSE) {
        if (!$blank_data) {
            return parent::load_db_table_data();
        } else {
            $headers_array = [];
            $blank_row_array = [];
            $show_rule = $this->db_table->get_db_table_show_rule();
            foreach ($this->db_table->get_db_table_config() as $field => $config) {
                if (!empty($this->db_table->get_constant_fields()) && array_key_exists($field, $this->db_table->get_constant_fields())) {
                    continue;
                }
                if (($show_rule === NULL) || ($config[$show_rule])) {
                    $headers_array[$field] = $field;
                    $blank_row_array[$field] = "";
                }
            }
            if (!empty($headers_array) && !empty($blank_row_array)) {
                $this->db_table_data[0] = $headers_array;
                $this->db_table_data[1] = $blank_row_array;
                $this->db_table_data_filtered = $this->db_table_data;
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * 
     * @param string $field
     * @param string $value
     * @return boolean
     */
    public function set_post_incomming_value($field, $value) {
        if ($this->post_data_catched && key_exists($field, $this->post_incoming_array)) {
            $this->post_incoming_array[$field] = $value;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Use the $_POST data received by catch_post_data() to put in db_table_data and db_table_data_filtered. THIS HAVE be used before filters.
     * @param int $row_to_put_on
     * @return boolean
     */
    public function put_post_data_on_table_data() {
        if ((empty($this->db_table_data)) || empty($this->post_incoming_array)) {
//            trigger_error(__FUNCTION__ . ": There are not data to work yet", E_USER_WARNING);
            return FALSE;
        }
        foreach ($this->db_table_data[1] as $field => $value) {
            if (isset($this->post_incoming_array[$field])) {
                $this->db_table_data[1][$field] = $this->post_incoming_array[$field];
            }
        }
        $this->db_table_data_filtered = $this->db_table_data;
        return TRUE;
    }

    function do_password_fields_validation() {
        /**
         * PASSWORD CATCH
         */
        $password_fields = [];
        $current = null;
        $new = null;
        $confirm = null;
        // EXTRACT THE PASSWORD DATA
        foreach ($_POST as $field => $value) {
            $actual_password_field = strstr($field, "_password_", TRUE);
            if ($actual_password_field !== FALSE) {
                if (strstr($field, "_password_current") !== FALSE) {
                    $password_fields[$actual_password_field]['current'] = (empty($value)) ? NULL : md5($value);
                }
                if (strstr($field, "_password_new") !== FALSE) {
                    $password_fields[$actual_password_field]['new'] = (empty($value)) ? NULL : md5($value);
                }
                if (strstr($field, "_password_confirm") !== FALSE) {
                    $password_fields[$actual_password_field]['confirm'] = (empty($value)) ? NULL : md5($value);
                }
                unset($_POST[$field]);
                if ($this->do_table_field_name_encrypt) {
                    $this->post_password_fields[] = $this->decrypt_field_name($field);
                } else {
                    $this->post_password_fields[] = $field;
                }
            }
        }
        //  verify
        foreach ($password_fields as $field => $passwords) {
            if (array_key_exists('new', $passwords) && array_key_exists('confirm', $passwords)) {
                if (($passwords['new'] === $passwords['confirm']) && (!empty($passwords['new']))) {
                    $new_password = TRUE;
                } else {
                    $new_password = FALSE;
                }
            }
            if (array_key_exists('current', $passwords) && array_key_exists('new', $passwords) && array_key_exists('confirm', $passwords)) {
                if (empty($passwords['current'])) {
                    $this->post_incoming_array[$field] = $this->db_table_data[1][$this->decrypt_field_name($field)];
                } else {
                    if (($passwords['current'] === $this->db_table_data[1][$this->decrypt_field_name($field)])) {
                        if ($new_password) {
                            $this->post_incoming_array[$field] = $passwords['new'];
                            DOM_notification::queue_mesasage(updating_strings::$password_set_successfully, "success", $this->notifications_div_id);
                        } else {
                            $this->post_validation_errors[$this->decrypt_field_name($field)] = creating_strings::$error_new_password_not_match;
                        }
                    } else {
                        $this->post_validation_errors[$this->decrypt_field_name($field)] = creating_strings::$error_actual_password_not_match;
                    }
                }
            } else if (array_key_exists('new', $passwords) && array_key_exists('confirm', $passwords)) {
                if ($new_password) {
                    $this->post_incoming_array[$field] = $passwords['new'];
                } else {
                    $this->post_incoming_array[$field] = null;
                    if (empty($passwords['new'])) {
                        $this->post_validation_errors[$this->decrypt_field_name($field)] = creating_strings::$error_new_password_not_match;
                    }
                }
            }
        }
    }

    public function get_post_data_catched() {
        return $this->post_data_catched;
    }

    /**
     * Get and check the $_POST data, then remove the non table values. If do_table_field_name_encrypt is TRUE then will decrypt them too.
     * @return boolean
     */
    function catch_post_data() {
        $this->do_file_uploads_validation();
        $this->do_password_fields_validation();
        /**
         * Search util hack
         */
        $post_data_to_use = \k1lib\common\unserialize_var("post-data-to-use");
        $post_data_table_config = \k1lib\common\unserialize_var("post-data-table-config");
        /**
         * lets fix the non-same key name
         */
        $fk_found_array = [];
        $found_fk_key = false;
        if (!empty($post_data_table_config)) {
            foreach ($post_data_table_config as $field => $field_config) {
                if (!empty($field_config['refereced_column_config'])) {
                    $fk_field_name = $field_config['refereced_column_config']['field'];
                    foreach ($post_data_to_use as $field_current => $value) {
                        if (($field_current == $fk_field_name) && ($field != $field_current)) {
                            $fk_found_array[$field] = $value;
                            $found_fk_key = true;
                        }
                    }
                }
            }
        }
        ///
        if (!empty($post_data_to_use)) {
            $_POST = $post_data_to_use;
            \k1lib\common\unset_serialize_var("post-data-to-use");
            \k1lib\common\unset_serialize_var("post-data-table-config");
        }


        $_POST = \k1lib\forms\check_all_incomming_vars($_POST);

        $this->post_incoming_array = array_merge($this->post_incoming_array, $_POST);
        if (isset($this->post_incoming_array['k1magic'])) {
            self::set_k1magic_value($this->post_incoming_array['k1magic']);
            unset($this->post_incoming_array['k1magic']);

            if (!empty($this->post_incoming_array)) {
                if ($this->do_table_field_name_encrypt) {
                    $new_post_data = [];
                    foreach ($this->post_incoming_array as $field => $value) {
                        $decrypt_field_name = $this->decrypt_field_name($field);
                        if (array_key_exists($decrypt_field_name, $fk_found_array)) {
                            $value = $fk_found_array[$decrypt_field_name];
                        }
                        $new_post_data[$decrypt_field_name] = $value;
                    }
                    $this->post_incoming_array = $new_post_data;
                    unset($new_post_data);
                }
                $this->post_incoming_array = \k1lib\common\clean_array_with_guide($this->post_incoming_array, $this->db_table->get_db_table_config());

                // PUT BACK the password data
//                $this->post_incoming_array = array_merge($this->post_incoming_array, $password_array);
                $this->post_data_catched = TRUE;
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Put an input object of certain type depending of the MySQL Table Feld Type on each data row[n]
     * @param Int $row_to_apply
     */
    public function insert_inputs_on_data_row($create_labels_tags_on_headers = TRUE) {
        // Row to apply is constant coz this is CREATE or EDIT and there is allways just 1 set of data to manipulate.
        $row_to_apply = 1;
        /**
         * VALUES
         */
        foreach ($this->db_table_data_filtered[$row_to_apply] as $field => $value) {
            /**
             * Switch on DB field specific TYPES
             */
            switch ($this->db_table->get_field_config($field, 'type')) {
                case 'enum':
                    $input_tag = input_helper::enum_type($this, $field);
                    break;
                case 'text':
                    switch ($this->db_table->get_field_config($field, 'validation')) {
                        case "html":
                            $input_tag = input_helper::text_type($this, $field);
                            break;
                        default:
                            $input_tag = input_helper::text_type($this, $field, FALSE);
                            break;
                    }
                    break;
                default:
                    /**
                     * Switch on K1lib DB Table Config VALIDATION TYPES
                     */
                    switch ($this->db_table->get_field_config($field, 'validation')) {
                        case "boolean":
                            $input_tag = input_helper::boolean_type($this, $field);
                            break;
                        case "file-upload":
                            $input_tag = input_helper::file_upload($this, $field);
                            break;
                        case "password":
                            if (empty($value)) {
                                $input_tag = input_helper::password_type($this, $field, $this->object_state);
                            } else {
                                $input_tag = input_helper::password_type($this, $field, $this->object_state);
                            }
                            break;
                        default:
                            $input_tag = input_helper::default_type($this, $field);
                            break;
                    }
                    break;
            }
            /**
             * LABELS 
             */
            if ($create_labels_tags_on_headers) {
                $label_tag = new \k1lib\html\label($this->db_table_data_filtered[0][$field], $this->encrypt_field_name($field));
                if ($this->db_table->get_field_config($field, 'required') === TRUE) {
                    $label_tag->set_value(" *", TRUE);
                }
                if (isset($this->post_validation_errors[$field])) {
                    $label_tag->set_attrib("class", "is-invalid-label");
                }
                $this->db_table_data_filtered[0][$field] = $label_tag;
            }
            /**
             * ERROR TESTING
             */
            if (isset($this->post_validation_errors[$field])) {
                $div_error = new \k1lib\html\foundation\grid_row(2);

                $div_input = $div_error->cell(1)->large(12);
                $div_message = $div_error->cell(2)->large(12)->end();

                $span_error = $div_message->append_span("clearfix form-error is-visible");
                $span_error->set_value($this->post_validation_errors[$field]);

                $input_tag->append_to($div_input);
                $input_tag->set_attrib("class", "is-invalid-input", TRUE);

                $div_error->link_value_obj($input_tag);
            }
            /**
             * END ERROR TESTING
             */
            if ($this->db_table->get_field_config($field, 'required') === TRUE) {
                if ($this->enable_foundation_form_check) {
                    $input_tag->set_attrib("required", TRUE);
                }
            }
            $input_tag->set_attrib("k1lib-data-type", $this->db_table->get_field_config($field, 'validation'));
            $input_tag->set_attrib("id", $this->encrypt_field_name($field));

            if (isset($div_error)) {
                $this->apply_html_tag_on_field_filter($div_error, $field);
                unset($div_error);
            } else {
                $this->apply_html_tag_on_field_filter($input_tag, $field);
            }

            unset($input_tag);
        }
    }

    /**
     * This will check every data with the db_table_config.
     * @return boolean TRUE on no errors or FALSE is some field has any problem.
     */
    public function do_post_data_validation() {
//        $this->do_file_uploads_validation();
        $validation_result = $this->db_table->do_data_validation($this->post_incoming_array);
        if ($validation_result !== TRUE) {
            $this->post_validation_errors = array_merge($this->post_validation_errors, $validation_result);
        }
        if (empty($this->post_validation_errors)) {
            return TRUE;
        } else {
            if ($this->object_state == "create") {
                foreach ($this->post_password_fields as $field) {
                    $this->db_table_data[1][$field] = null;
                    $this->db_table_data_filtered[1][$field] = null;
                }
            }
            return FALSE;
        }
    }

    public function do_file_uploads_validation() {
        if (!empty($_FILES)) {
            foreach ($_FILES as $encoded_field => $data) {
                $decoded_field = $this->decrypt_field_name($encoded_field);
                if ($data['error'] === UPLOAD_ERR_OK) {
                    $_POST[$decoded_field] = $data;
                } else {
                    if ($data['error'] !== UPLOAD_ERR_NO_FILE) {
                        trigger_error(creating_strings::$error_file_upload . print_r($data, TRUE), E_USER_WARNING);
                    }
                }
            }
        }
    }

    public function enable_foundation_form_check() {
        $this->enable_foundation_form_check = TRUE;
    }

    /**
     * @return \k1lib\html\div
     */
    public function do_html_object() {
        if (!empty($this->db_table_data_filtered)) {
            $this->div_container->set_attrib("class", "k1lib-crudlexs-create");

            /**
             * DIV content
             */
            $this->div_container->set_attrib("class", "k1lib-form-generator " . $this->html_form_column_classes, TRUE);
            $this->div_container->set_attrib("style", "margin:0 auto;", TRUE);

            /**
             * FORM time !!
             */
            $html_form = new \k1lib\html\form();
            $html_form->append_to($this->div_container);
            if ($this->enable_foundation_form_check) {
                $html_form->set_attrib("data-abide", TRUE);
            }

            $form_header = $html_form->append_div("k1lib-form-header");
            $form_body = $html_form->append_div("k1lib-form-body");
            $form_grid = new \k1lib\html\foundation\grid(1, 1, $form_body);
            $form_grid->row(1)->align_center();
            $form_grid->row(1)->cell(1)->large(8)->medium(10)->small(12);
            
            $form_footer = $html_form->append_div("k1lib-form-footer");
            $form_footer->set_attrib("style", "margin-top:0.9em;");
            $form_buttons = $html_form->append_div("k1lib-form-buttons");

            /**
             * Hidden input
             */
            $hidden_input = new \k1lib\html\input("hidden", "k1magic", "123123");
            $hidden_input->append_to($html_form);
            // FORM LAYOUT
            // <div class="grid-x">

            $row_number = 0;
            foreach ($this->db_table_data_filtered[1] as $field => $value) {
                $row_number++;
                $row = new \k1lib\html\foundation\label_value_row($this->db_table_data_filtered[0][$field], $value, $row_number);
                $row->append_to($form_grid->row(1)->cell(1));
            }


            /**
             * BUTTONS
             */
            $submit_button = new \k1lib\html\input("submit", "k1send", creating_strings::$button_submit, "small button fi-check success");
            if ($this->show_cancel_button) {
                $cancel_button = \k1lib\html\get_link_button($this->back_url, creating_strings::$button_cancel, "small");
                $buttons_div = new \k1lib\html\foundation\label_value_row(NULL, "{$cancel_button} {$submit_button}");
            } else {
                $buttons_div = new \k1lib\html\foundation\label_value_row(NULL, "{$submit_button}");
            }

            $buttons_div->append_to($form_buttons);
            $buttons_div->cell(1)->remove_childs();
            $buttons_div->cell(2)->set_class("text-center", TRUE);


            /**
             * Prepare output
             */
            return $this->div_container;
        } else {
            return FALSE;
        }
    }

    /**
     * This uses the post_incoming_array (Please verify it first) to make the insert.
     * NOTE: If the table has multiple KEYS the auto_number HAS to be on the first position, if not, the redirection won't works.
     * @param type $url_to_go
     * @return boolean TRUE on sucess or FALSE on error.
     */
    public function do_insert() {
        $error_data = NULL;
        $this->post_incoming_array = \k1lib\forms\check_all_incomming_vars($this->post_incoming_array);
        $this->inserted_result = $this->db_table->insert_data($this->post_incoming_array, $error_data);
        if ($this->inserted_result !== FALSE) {
            DOM_notification::queue_mesasage(creating_strings::$data_inserted, "success", $this->notifications_div_id);
            $this->inserted = TRUE;
            return TRUE;
        } else {
            if (is_array($error_data) && !empty($error_data)) {
                $this->post_validation_errors = array_merge($this->post_validation_errors, $error_data);
            }
            DOM_notification::queue_mesasage(creating_strings::$data_not_inserted, "warning", $this->notifications_div_id);
            DOM_notification::queue_mesasage(print_r($error_data, TRUE), 'alert', $this->notifications_div_id);
            $this->inserted = FALSE;
            return FALSE;
        }
    }

    public function get_inserted_keys() {
        if (($this->inserted) && ($this->inserted_result !== FALSE)) {
            $last_inserted_id = [];
            if (is_numeric($this->inserted_result)) {
                foreach ($this->db_table->get_db_table_config() as $field => $config) {
                    if ($config['extra'] == 'auto_increment') {
                        $last_inserted_id[$field] = $this->inserted_result;
                    }
                }
            }
            $new_keys_array = \k1lib\sql\get_keys_array_from_row_data(
                    array_merge($last_inserted_id, $this->post_incoming_array, $this->db_table->get_constant_fields())
                    , $this->db_table->get_db_table_config()
            );
            return $new_keys_array;
        } else {
            return FALSE;
        }
    }

    public function get_inserted_data() {
        if (($this->inserted) && ($this->inserted_result !== FALSE)) {
            $last_inserted_id = [];
            if (is_numeric($this->inserted_result)) {
                foreach ($this->db_table->get_db_table_config() as $field => $config) {
                    if ($config['extra'] == 'auto_increment') {
                        $last_inserted_id[$field] = $this->inserted_result;
                    }
                }
            }
            return array_merge($last_inserted_id, $this->post_incoming_array, $this->db_table->get_constant_fields());
        } else {
            return FALSE;
        }
    }

    public function post_insert_redirect($url_to_go = "../", $do_redirect = TRUE) {
        if (($this->inserted) && ($this->inserted_result !== FALSE)) {

            $new_keys_text = \k1lib\sql\table_keys_to_text($this->get_inserted_keys(), $this->db_table->get_db_table_config());

            if (!empty($url_to_go)) {
                $this->set_auth_code($new_keys_text);
                $this->set_auth_code_personal($new_keys_text);
                $url_to_go = str_replace("--rowkeys--", $new_keys_text, $url_to_go);
                $url_to_go = str_replace("--authcode--", $this->get_auth_code(), $url_to_go);
            }
            if ($do_redirect) {
                if ($new_keys_text) {
                    \k1lib\html\html_header_go($url_to_go);
                    exit;
                } else {
                    \k1lib\html\html_header_go("../");
                    exit;
                }
                return TRUE;
            } else {
                return $url_to_go;
            }
        } else {
            return "";
        }
    }

    function get_post_data() {
        return $this->post_incoming_array;
    }

    public function set_post_data(Array $post_incoming_array) {
        $this->post_incoming_array = array_merge($this->post_incoming_array, $post_incoming_array);
    }

    public function set_html_column_classes($html_column_classes) {
        $this->html_column_classes = $html_column_classes;
    }

    public function set_html_form_column_classes($html_form_column_classes) {
        $this->html_form_column_classes = $html_form_column_classes;
    }

    public function &get_post_incoming_array() {
        return $this->post_incoming_array;
    }

    public function get_post_validation_errors() {
        return $this->post_validation_errors;
    }

    public function set_post_validation_errors(Array $errors_array, $append_array = TRUE) {
        if ($append_array) {
            $this->post_validation_errors = array_merge($this->post_validation_errors, $errors_array);
        } else {
            $this->post_validation_errors = $errors_array;
        }
    }

    public function set_show_cancel_button($show_cancel_button) {
        $this->show_cancel_button = $show_cancel_button;
    }

}

// ./src/crudlexs/object_classes/db-table.php


namespace k1lib\crudlexs;

class class_db_table {

    /**
     *
     * @var \PDO
     */
    public $db;
    private $db_table_name = FALSE;
    public $db_table_config = FALSE;
    private $db_table_label_field = FALSE;
    private $db_table_show_rule = NULL;

    /**
     * SQL Values
     */
    private $query_offset = 0;
    private $query_row_count_limit = NULL;
    private $query_where_pairs = NULL;
    private $query_where_custom = NULL;
    private $query_sql = NULL;
    private $query_sql_total_rows = NULL;
    private $query_sql_keys = NULL;
    private $total_rows_filtered_result;
    private $total_rows_result;

    /**
     * GROUP BY
     */

    /**
     * @var array
     */
    private $query_group_by_fields_array = [];

    /**
     * ORDER BY
     */

    /**
     * @var array
     */
    private $query_order_by_order_arry = [];

    /**
     * CUSTOM SQL QUERY
     */
    protected $custom_sql_query_code = NULL;
    protected $custom_query_table_config = [];

    /**
     * @var array Constant fields array
     */
    private $constant_fields = [];

    /**
     * 
     * @param \PDO $db
     * @param string $db_table_name
     */
    public function __construct(\PDO $db, $db_table_name) {
        $this->db = $db;
        // check $db_table_name type
        if (is_string($db_table_name)) {
            $this->db_table_name = $db_table_name;
        } else {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_table_name, E_USER_ERROR);
        }

        $this->db_table_config = $this->_get_db_table_config($db_table_name);
        if ($this->db_table_config) {
            $this->db_table_label_field = $this->_get_db_table_label_fields($this->db_table_config);
        } else {
            
        }
    }

    public function __toString() {
        if ($this->get_state()) {
            return "1";
        } else {
            return "0";
        }
    }

    public function get_state() {
        if ($this->db_table_config) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function set_custom_sql_query($sql_query, $override_db_table_config = FALSE) {
        $this->custom_sql_query_code = $sql_query;
        if ($override_db_table_config) {
            $this->db_table_config = \k1lib\sql\get_db_tables_config_from_sql($this->db, $this->custom_sql_query_code);
        }
//        return $this->db_table_config;
    }

    function get_db_table_name() {
        return $this->db_table_name;
    }

    public function get_db_table_label_fields() {
        return $this->db_table_label_field;
    }

    private static function _get_db_table_label_fields(&$db_table_config) {
        return \k1lib\sql\get_db_table_label_fields($db_table_config);
    }

    public function get_db_table_config($reload = FALSE) {
        if ($reload && !empty($this->db_table_config)) {
            $this->db_table_config = $this->_get_db_table_config($this->db_table_name, TRUE, $reload);
        }
        return $this->db_table_config;
    }

    public function get_db_table_keys_array() {
        return $this->_get_db_table_keys_array($this->get_db_table_config());
    }

    public function get_db_table_field_config($field) {
        return $this->db_table_config[$field];
    }

    public function get_field_config($field, $config_name) {
        return $this->db_table_config[$field][$config_name];
    }

    private function _get_db_table_config($db_table_name, $recursion = TRUE, $use_cache = TRUE) {
        return \k1lib\sql\get_db_table_config($this->db, $db_table_name, $recursion, $use_cache);
    }

    private function _get_db_table_keys_array($db_table_config) {
        return \k1lib\sql\get_db_table_keys_array($db_table_config);
    }

    public function set_query_limit($offset = 0, $row_count = NULL) {
        $this->query_offset = $offset;
        $this->query_row_count_limit = $row_count;
    }

    function get_query_offset() {
        return $this->query_offset;
    }

    function get_query_row_count_limit() {
        return $this->query_row_count_limit;
    }

    public function set_query_filter($filter_array, $exact_filter = FALSE, $do_clean_array = TRUE) {
        if ($do_clean_array) {
            $clean_filter_array = \k1lib\common\clean_array_with_guide($filter_array, $this->db_table_config);
        } else {
            $clean_filter_array = $filter_array;
        }
        if (empty($clean_filter_array)) {
            return FALSE;
        } else {
            $query_where_pairs = "";
            if ($exact_filter) {
                $normal_filter = [];
                $between_filter = [];
                foreach ($clean_filter_array as $field => $value) {
                    if (is_array($value)) {
                        if (count($value) == 2) {
                            if ($value[0] !== NULL AND $value[1] !== NULL) {
                                $between_filter[] = "$field BETWEEN '" . implode("' AND '", $value) . "'";
                            }
                        } else {
                            trigger_error(E_USER_WARNING, "$field is not a valid array for BETWEEN :" . print_r($value, TRUE));
                        }
                    } else {
                        $normal_filter[$field] = $value;
                    }
                }
                if (!empty($normal_filter)) {
                    $query_where_pairs = \k1lib\sql\array_to_sql_set($this->db, $normal_filter, TRUE, TRUE);
                }
                if (!empty($between_filter)) {
                    if (!empty($query_where_pairs)) {
                        $query_where_pairs = $query_where_pairs . " AND " . implode(" AND ", $between_filter);
                    } else {
                        $query_where_pairs = implode(" AND ", $between_filter);
                    }
                }
            } else {
                $doFilter = FALSE;
                foreach ($clean_filter_array as $search_value) {
                    if (!empty($search_value)) {
                        $doFilter = TRUE;
                    }
                }
                $i = 0;
                if ($doFilter) {
                    // and make the conditions, all with LIKE
                    foreach ($clean_filter_array as $field => $search_value) {
                        if (!is_array($search_value)) {
                            // aparently I am trying to re-check the field existence but it was done with $do_clean_array
                            if (isset($this->db_table_config[$field]) && (!empty($search_value))) {
                                $query_where_pairs .= ($i >= 1) ? " AND " : "";
                                $query_where_pairs .= " $field LIKE '{$search_value}'";
                                $i++;
                            }
                        }
                    }
                }
            }
            if (!empty($this->query_where_pairs)) {
                $this->query_where_pairs = "({$this->query_where_pairs}) AND ($query_where_pairs)";
            } else {
                $this->query_where_pairs = $query_where_pairs;
            }
            return TRUE;
        }
    }

    public function set_query_filter_exclude($filter_array, $exact_filter = FALSE, $do_clean_array = TRUE) {
        if ($do_clean_array) {
            $clean_filter_array = \k1lib\common\clean_array_with_guide($filter_array, $this->db_table_config);
        } else {
            $clean_filter_array = $filter_array;
        }
        if (empty($clean_filter_array)) {
            return FALSE;
        } else {
            $query_where_pairs = "";
            if ($exact_filter) {
                $query_where_pairs = \k1lib\sql\array_to_sql_set_exclude($this->db, $clean_filter_array, TRUE, TRUE);
            } else {
                $doFilter = FALSE;
                foreach ($clean_filter_array as $search_value) {
                    if (!empty($search_value)) {
                        $doFilter = TRUE;
                    }
                }
                $i = 0;
                if ($doFilter) {
                    // and make the conditions, all with LIKE
                    foreach ($clean_filter_array as $field => $search_value) {
                        if (isset($this->db_table_config[$field]) && (!empty($search_value))) {
                            $query_where_pairs .= ($i >= 1) ? " AND " : "";
                            $query_where_pairs .= " $field NOT LIKE '%{$search_value}%'";
                            $i++;
                        }
                    }
                }
            }
            if (!empty($this->query_where_pairs)) {
                $this->query_where_pairs = "({$this->query_where_pairs}) AND ($query_where_pairs)";
            } else {
                $this->query_where_pairs = $query_where_pairs;
            }
            return TRUE;
        }
    }

    public function set_field_constants(array $field_constants_array, $use_as_filter = FALSE) {

        if (empty($field_constants_array)) {
            return FALSE;
        } else {
            // DB table constants creation for inserts and updates
            $this->constant_fields = array_merge($this->constant_fields, $field_constants_array);
            if ($use_as_filter) {
                $this->set_query_filter($field_constants_array, TRUE);
            }
            return TRUE;
        }
    }

    function get_constant_fields() {
        return $this->constant_fields;
    }

    public function clear_query_filter() {
        $this->query_where_pairs = "";
    }

    public function generate_sql_query_fields_by_rule($rule) {
        $fields_array = [];
//        if (!empty($this->custom_query_table_config)) {
//            $table_config_bkp = $this->db_table_config;
//            $this->db_table_config = $this->custom_query_table_config;
//        }
        foreach ($this->db_table_config as $field => $config) {
            if (isset($this->constant_fields) && array_key_exists($field, $this->constant_fields)) {
                continue;
            }
            if (isset($config[$rule]) && $config[$rule]) {
                $fields_array[] = $field;
            }
        }
//        if (!empty($this->custom_query_table_config)) {
//            $this->db_table_config = $table_config_bkp;
//        }
        if (!empty($fields_array)) {
            return implode(",", $fields_array);
        } else {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_no_show_rule, E_USER_WARNING);
            return FALSE;
        }
    }

    public function generate_sql_query($wich = 'main') {

        $sql_code = "";

        /**
         * FIELDS
         */
        if (empty($this->db_table_show_rule)) {
            $fields = "*";
        } else {
            switch ($wich) {
                case 'main':
                    $fields = $this->generate_sql_query_fields_by_rule($this->db_table_show_rule);
                    break;
                case 'keys':
                    $db_table_key_fields = \k1lib\sql\get_db_table_keys_array($this->db_table_config);
                    if (!empty($db_table_key_fields)) {
                        $fields = implode(",", $db_table_key_fields);
                    } else {
                        return FALSE;
                    }
                    $fields_to_add = "," . $this->generate_sql_query_fields_by_rule($this->db_table_show_rule);
                    $fields .= $fields_to_add;
                    break;
                default:
                    return FALSE;
            }
        }

        if (empty($fields)) {
            return FALSE;
        } else {
            /**
             * SELECT
             */
            if (!empty($this->custom_sql_query_code)) {
                $sql_code = $this->custom_sql_query_code . " ";
                $this->query_sql_total_rows = \k1lib\sql\get_sql_count_query_from_sql_code($sql_code) . " ";
            } else {
                $sql_code = "SELECT {$fields} FROM {$this->db_table_name} ";
                $this->query_sql_total_rows = "SELECT COUNT(*) as num_rows FROM {$this->db_table_name} ";
            }
            /**
             * WHERE
             */
            if (!empty($this->query_where_pairs)) {
                $sql_code .= "WHERE {$this->query_where_pairs} ";
                $this->query_sql_total_rows .= "WHERE {$this->query_where_pairs} ";
                if (!empty($this->query_where_custom)) {
                    $sql_code .= " {$this->query_where_custom} ";
                    $this->query_sql_total_rows .= " {$this->query_where_pairs} ";
                }
            } else {
                if (!empty($this->query_where_custom)) {
                    $sql_code .= "WHERE {$this->query_where_custom} ";
                    $this->query_sql_total_rows .= "WHERE {$this->query_where_pairs} ";
                }
            }
            /**
             * GROUP BY
             */
            $sql_code .= $this->get_sql_group_by_code();
            /**
             * ORDER BY
             */
            $sql_code .= $this->get_sql_order_by_code();
            /**
             * LIMIT
             */
            $sql_code .= $this->get_sql_limit_code();

            switch ($wich) {
                case 'main':
                    $this->query_sql = $sql_code;
                    break;
                case 'keys':
                    $this->query_sql_keys = $sql_code;
                    break;
            }
            return $sql_code;
        }
    }

    public function get_sql_group_by_code() {
        if (!empty($this->query_group_by_fields_array)) {
            $group_code = "\n\tGROUP BY\n\t\t" . implode(",", $this->query_group_by_fields_array) . " ";
            return $group_code;
        } else {
            return '';
        }
    }

    public function get_sql_order_by_code() {
        $order_array = [];
        if (!empty($this->query_order_by_fields_array)) {
            foreach ($this->query_order_by_fields_array as $index => $field_to_order) {
                $order_type = (key_exists($index, $this->query_order_by_order_arry) ? $this->query_order_by_order_arry[$index] : 'ASC');
                $order_array[$index] = "{$field_to_order} {$order_type}";
            }
            $order_code = "\n\tORDER BY\n\t\t" . implode(",", $order_array) . " ";
            return $order_code;
        } else {
            return "";
        }
    }

    public function get_sql_limit_code() {
        $sql_code = '';
        // WFT
        // TODO: seems to be un unsed this var
//        $sql_code_total_rows = '';
        if (($this->query_offset === 0) && ($this->query_row_count_limit > 0)) {
            $sql_code .= "LIMIT {$this->query_row_count_limit} ";
//            $sql_code_total_rows .= "LIMIT {$this->query_row_count_limit} ";
        } elseif (($this->query_offset > 0) && ($this->query_row_count_limit > 0)) {
            $sql_code .= "LIMIT {$this->query_offset},{$this->query_row_count_limit} ";
//            $sql_code_total_rows .= "LIMIT {$this->query_offset},{$this->query_row_count_limit} ";
        }
        return $sql_code;
    }

    public function generate_sql_query_keys() {
        return $this->generate_sql_query('keys');
    }

    function get_query_sql() {
        if (empty($this->query_sql)) {
            $this->generate_sql_query();
        }
        return $this->query_sql;
    }

    /**
     * Generates the SQL code and make the query with it. Then return the result as an Array.
     * @param boolean $return_all
     * @param boolean $do_fields
     * @return array|boolean
     */
    public function get_data($return_all = TRUE, $do_fields = TRUE) {
        if ($this->generate_sql_query()) {
            $query_result = \k1lib\sql\sql_query($this->db, $this->query_sql, $return_all, $do_fields);

            if (!empty($query_result)) {
                $this->total_rows_filtered_result = count($query_result) - 1;
                return $query_result;
            } else {
                // EMPTY RESULT TO DO NOT BREAK THE FOREACH LOOPS
                return [];
            }
        } else {
            return FALSE;
        }
    }

    public function get_field_operation($field, $operation = 'SUM') {
        if ($this->generate_sql_query()) {
            // take from FROM part
            $sql_last_part_full = strstr($this->query_sql, "FROM", FALSE);
            // remove since ORDER part
            $sql_last_part = strstr($sql_last_part_full, "ORDER BY", TRUE);
            if ($sql_last_part === FALSE) {
                // remove since LIMIT part
                $sql_last_part = strstr($sql_last_part_full, "LIMIT", TRUE);
            }

            $operation_sql = "SELECT {$operation}(`$field`) AS `$field`  {$sql_last_part}";
            $query_result = \k1lib\sql\sql_query($this->db, $operation_sql, FALSE);

            if (!empty($query_result)) {

                return $query_result[$field];
            } else {
                return 0;
            }
        } else {
            return FALSE;
        }
    }

    public function get_data_keys() {
        if ($this->generate_sql_query('keys')) {
            $query_result = \k1lib\sql\sql_query($this->db, $this->query_sql_keys, TRUE, TRUE);
            $just_keys_result = [];
            foreach ($query_result as $row => $data) {
                if ($row === 0) {
                    continue;
                }
                $just_keys_result[$row] = \k1lib\sql\get_keys_array_from_row_data($query_result[$row], $this->db_table_config);
            }

            if (!empty($just_keys_result)) {
                return $just_keys_result;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    function get_total_data_rows() {
        return $this->total_rows_filtered_result;
    }

    function get_total_rows() {
        if ($this->generate_sql_query()) {
            $this->total_rows_result = \k1lib\sql\sql_query($this->db, $this->query_sql_total_rows, FALSE, FALSE);
            if ($this->total_rows_result) {
                return (int) $this->total_rows_result['num_rows'];
            } else {
                return NULL;
            }
        } else {
            return FALSE;
        }
    }

    function get_db_table_show_rule() {
        return $this->db_table_show_rule;
    }

    function set_db_table_show_rule($db_table_show_rule) {
        $this->db_table_show_rule = $db_table_show_rule;
    }

    public function get_enum_options($field) {
        return \k1lib\sql\get_db_table_enum_values($this->db, $this->db_table_name, $field);
    }

    public function do_data_validation(&$data_array_to_validate) {
        $validaton_errors = \k1lib\forms\form_check_values($data_array_to_validate, $this->db_table_config, $this->db);
        if (!is_array($validaton_errors)) {
            return TRUE;
        } else {
            return $validaton_errors;
        }
    }

    public function insert_data(array $data_to_insert, &$error_data = NULL, &$sql_query = NULL) {
        if (empty($data_to_insert)) {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_empty_data_insert, E_USER_WARNING);
            return FALSE;
        }
        $data_to_insert_merged = array_merge($data_to_insert, $this->constant_fields);
        return \k1lib\sql\sql_insert($this->db, $this->db_table_name, $data_to_insert_merged, $error_data, $sql_query);
    }

    /**
     * SQL update method
     * @param array $data_to_update
     * @param array $key_to_update
     * @return boolean
     */
    public function update_data(array $data_to_update, array $key_to_update, &$error_data = NULL, &$sql_query = NULL) {
        if (empty($data_to_update)) {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_empty_data_update, E_USER_WARNING);
            return FALSE;
        }
        if (empty($key_to_update)) {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_empty_data_update_key, E_USER_WARNING);
            return FALSE;
        }
        $data_to_update_merged = array_merge($data_to_update, $this->constant_fields);
        return \k1lib\sql\sql_update($this->db, $this->db_table_name, $data_to_update_merged, $key_to_update, [], $error_data, $sql_query);
    }

    public function delete_data(array $key_to_delete) {

        if (empty($key_to_delete)) {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_empty_data_delete_key, E_USER_WARNING);
            return FALSE;
        }
        return \k1lib\sql\sql_del_row($this->db, $this->db_table_name, $key_to_delete);
    }

    public function set_order_by($field, $order = 'ASC') {
        $this->query_order_by_fields_array[] = $field;
        $this->query_order_by_order_arry[] = $order;
    }

    public function get_query_where_custom() {
        return $this->query_where_custom;
    }

    public function set_query_where_custom($query_where_custom) {
        $this->query_where_custom = $query_where_custom;
    }

    public function set_group_by(array $query_group_by_fields_array) {
        $this->query_group_by_fields_array = $query_group_by_fields_array;
    }

    public function export_to_xl($use_limit = TRUE) {
        $file_name = $this->db_table_name;
        if (!$use_limit) {
            $this->set_query_limit(0, 0);
            $file_description = '-all-data';
        } else {
            $file_description = '-partial';
        }
        $file_name .= $file_description;

        $data = $this->get_data(TRUE, TRUE);
        if (!empty($data)) {
            ob_end_clean();
            header("Content-Disposition: attachment; filename=\"{$file_name}.csv\"");
            header("Content-Type: text/csv;");
            header("Pragma: no-cache");
            header("Expires: 0");
            $out = fopen("php://output", 'w');
            fwrite($out, pack("CCC", 0xef, 0xbb, 0xbf));
            foreach ($data as $data) {
                fputcsv($out, $data, ",");
            }
            fclose($out);
            exit;
        } else {
            return FALSE;
        }
    }

}

// ./src/crudlexs/object_classes/inputs-helper.php


namespace k1lib\crudlexs;

use k1lib\urlrewrite\url as url;

class input_helper {

    static $do_fk_search_tool = TRUE;
    static $url_to_search_fk_data = APP_URL . "general-utils/select-row-keys/";
    static $url_to_send_row_keys_fk_data = APP_URL . "general-utils/send-row-keys/";
    static $main_css = "";
    static private $fk_fields_to_skip = [];
    static public $boolean_true = NULL;
    static public $boolean_false = NULL;

    static function password_type(creating $crudlex_obj, $field, $case = "create") {
        // First we have the CLEAR the password data, we do not need it!
        $field_encrypted = $crudlex_obj->encrypt_field_name($field) . "_password";
        $tag_id = $crudlex_obj->encrypt_field_name($field) . "-reveal";
        $crudlex_obj->db_table_data_filtered[1][$field] = null;

        $div_continer = new \k1lib\html\div();

        $input_tag_new = new \k1lib\html\input("password", $field_encrypted . "_new", NULL, "k1lib-input-insert");
        $input_tag_confirm = new \k1lib\html\input("password", $field_encrypted . "_confirm", NULL, "k1lib-input-insert");

        if ($case == "create") {
            $div_continer->link_value_obj($input_tag_new);
        } elseif ($case == "update") {
            $input_tag_current = new \k1lib\html\input("password", $field_encrypted . "_current", NULL, "k1lib-input-insert");
            $input_tag_current->set_attrib("placeholder", "Current password");
            $div_continer->append_div()->append_child($input_tag_current);
            $div_continer->link_value_obj($input_tag_current);
        }
        $input_tag_new->set_attrib("placeholder", "New password");
        $input_tag_confirm->set_attrib("placeholder", "Confirm password");

        $div_continer->append_div()->append_child($input_tag_new);
        $div_continer->append_div()->append_child($input_tag_confirm);

        return $div_continer;
    }

    /**
     * *
     * @param \k1lib\crudlexs\class_db_table $db_table
     * @param array $db_table_row_data
     * @return \k1lib\html\select
     */
    static function enum_type(creating $crudlex_obj, $field) {
        /**
         * @todo Use FIELD encryption here, I tried but it doesn't work just pasting the normal lines
         */
        $enum_data = $crudlex_obj->db_table->get_enum_options($field);
        $input_tag = new \k1lib\html\select($field);
        $input_tag->append_option("", input_helper_strings::$select_choose_option);

        foreach ($enum_data as $index => $value) {
            // SELETED work around
            if ($crudlex_obj->db_table_data[1][$field] == $value) {
                $selected = TRUE;
            } else {
                $selected = FALSE;
            }
            $input_tag->append_option($index, $value, $selected);
        }
        return $input_tag;
    }

    /**
     * 
     * @param \k1lib\crudlexs\creating $crudlex_obj
     * @param int $row_to_apply
     * @param string $field
     * @return \k1lib\html\textarea
     */
    static function text_type(creating $crudlex_obj, $field, $load_tinymce = TRUE) {
        // Row to apply is constant coz this is CREATE or EDIT and there is allways just 1 set of data to manipulate.
        $field_encrypted = $crudlex_obj->encrypt_field_name($field);

        if (!empty(self::$main_css)) {
            $css_option = "content_css: ['" . self::$main_css . "?' + new Date().getTime()],";
        } else {
            $css_option = "";
        }
        $input_tag = new \k1lib\html\textarea($field_encrypted);
        $input_tag->set_attrib("rows", 5);

        if ($load_tinymce) {
            $html_script = "tinymce.init({ "
                    . "selector: '#$field_encrypted',"
                    . "height: 120,"
                    . "plugins: [ 
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code'
                ],"
                    . $css_option
                    . "body_class: 'html-editor',"
//                . "content_style: 'div {margin: 100px; border: 50px solid red; padding: 3px}',"
                    . "toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',"
                    . "paste_data_images: true,"
                    . ""
                    . "});";
            $script = (new \k1lib\html\script())->set_value($html_script);
            $input_tag->post_code($script->generate());
        }

        return $input_tag;
    }

    static function file_upload(creating $crudlex_obj, $field) {
        $field_encrypted = $crudlex_obj->encrypt_field_name($field);

        $input_tag = new \k1lib\html\input("file", $field_encrypted, "", "k1lib-file-upload");
        if (isset($crudlex_obj->db_table_data[1][$field]['name']) || empty($crudlex_obj->db_table_data[1][$field])) {
            return $input_tag;
        } else {
            $delete_file_link = new \k1lib\html\a("./unlink-uploaded-file/" . $field_encrypted . "/?auth-code=--authcode--&back-url=" . urlencode(\k1lib\urlrewrite\get_back_url()), input_helper_strings::$button_remove);
            $div_container = new \k1lib\html\div(null, "img-delete-link");
            $div_container->append_child($input_tag);
            $div_container->append_child($delete_file_link);
            $div_container->link_value_obj($input_tag);

            return $div_container;
        }
    }

    static function boolean_type(creating $crudlex_obj, $field) {
        /*
          <div class="switch tiny">
          <input class="switch-input" id="tinySwitch" type="checkbox" name="exampleSwitch">
          <label class="switch-paddle" for="tinySwitch">
          <span class="show-for-sr">Tiny Sandwiches Enabled</span>
          </label>
          </div>
         */
        // Row to apply is constant coz this is CREATE or EDIT and there is allways just 1 set of data to manipulate.
//        d(self::$boolean_true, true);
        if (self::$boolean_true === NULL) {
//            d('yes');
            self::$boolean_true = \k1lib\common_strings::$yes;
        }
//        d(self::$boolean_false, true);
        if (self::$boolean_false === NULL) {
//            d('no');
            self::$boolean_false = \k1lib\common_strings::$no;
        }

        $field_encrypted = $crudlex_obj->encrypt_field_name($field);


        $input_div = new \k1lib\html\div();
        $input_div->link_value_obj(new \k1lib\html\span('hidden'));

        $input_yes = new \k1lib\html\input("radio", $field_encrypted, '1');
        $label_yes = new \k1lib\html\label(self::$boolean_true, $field_encrypted);
        $input_yes->post_code($label_yes->generate());
        $input_yes->append_to($input_div);

        if ($crudlex_obj->db_table_data[1][$field] == '1') {
            $input_yes->set_attrib('checked', TRUE);
        }

        $input_no = new \k1lib\html\input("radio", $field_encrypted, '0');
        $label_no = new \k1lib\html\label(self::$boolean_false, $field_encrypted);
        $input_no->post_code($label_no->generate());
        $input_no->append_to($input_div);

        if ($crudlex_obj->db_table_data[1][$field] == '0') {
            $input_no->set_attrib('checked', TRUE);
        }

        return $input_div;
    }

    static function default_type(creating $crudlex_obj, $field) {
        $field_encrypted = $crudlex_obj->encrypt_field_name($field);
        if ((!empty($crudlex_obj->db_table->get_field_config($field, 'refereced_table_name')) && self::$do_fk_search_tool) && (array_search($field, self::$fk_fields_to_skip) === FALSE)) {
            $div_input_group = new \k1lib\html\div("input-group");

            $input_tag = new \k1lib\html\input("text", $field_encrypted, NULL, "k1lib-input-insert input-group-field");
            if (!empty($crudlex_obj->db_table->get_field_config($field, 'placeholder'))) {
                $input_tag->set_attrib("placeholder", $crudlex_obj->db_table->get_field_config($field, 'placeholder'));
            } else {
                $input_tag->set_attrib("placeholder", input_helper_strings::$input_fk_placeholder);
            }
            $input_tag->set_attrib("k1lib-data-group-" . $crudlex_obj->db_table->get_field_config($field, 'refereced_table_name'), TRUE);
            $input_tag->append_to($div_input_group);

            $div_input_group_button = new \k1lib\html\div("input-group-button");
            $div_input_group_button->append_to($div_input_group);

            /**
             * FK TABLE EXTRACTOR
             */
            $refereced_column_config = $crudlex_obj->db_table->get_field_config($field, 'refereced_column_config');
//            while (!empty($refereced_column_config['refereced_column_config'])) {
////                $refereced_column_config = $refereced_column_config['refereced_column_config'];
//            }
            $this_table = $crudlex_obj->db_table->get_db_table_name();
            $this_table_alias = \k1lib\db\security\db_table_aliases::encode($this_table);

            $fk_table = $refereced_column_config['table'];
            $fk_table_alias = \k1lib\db\security\db_table_aliases::encode($fk_table);

//            $crudlex_obj->set_do_table_field_name_encrypt();
            $static_values = $crudlex_obj->db_table->get_constant_fields();
            $static_values_enconded = $crudlex_obj->encrypt_field_names($static_values);

            $search_button = new \k1lib\html\input("button", "search", "&#xf18d;", "button fi-page-search fk-button");
            $search_button->set_attrib("style", "font-family:foundation-icons");

            $url_params = [
                "back-url" => $_SERVER['REQUEST_URI']
            ];
            $url_params = array_merge($static_values_enconded, $url_params);

            $url_to_search_fk_data = url::do_url(self::$url_to_search_fk_data . "{$fk_table_alias}/list/$this_table_alias/", $url_params);
            $search_button->set_attrib("onclick", "javascript:use_select_row_keys(this.form,'{$url_to_search_fk_data}')");

            $search_button->append_to($div_input_group_button);

            $div_input_group->link_value_obj($input_tag);
            return $div_input_group;
        } elseif (strstr("date,date-past,date-future", $crudlex_obj->db_table->get_field_config($field, 'validation')) !== FALSE) {
            $div_input_group = new \k1lib\html\div("input-group");

            $input_tag = new \k1lib\html\input("text", $field_encrypted, NULL, "k1lib-input-insert input-group-field");
            $input_tag->set_attrib("placeholder", input_helper_strings::$input_date_placeholder);
            $input_tag->set_attrib("k1lib-data-datepickup", TRUE);
            $input_tag->append_to($div_input_group);

            $div_input_group_button = new \k1lib\html\div("input-group-button");
            $div_input_group_button->append_to($div_input_group);

            $search_button = new \k1lib\html\a("#", "", "_self", "button fi-calendar");
            $search_button->append_to($div_input_group_button);

            $div_input_group->link_value_obj($input_tag);
            return $div_input_group;
        } else {
            $input_tag = new \k1lib\html\input("text", $field_encrypted, NULL, "k1lib-input-insert");
            $input_tag->set_attrib("placeholder", $crudlex_obj->db_table->get_field_config($field, 'placeholder'));
            return $input_tag;
        }
    }

    public static function get_do_fk_search_tool() {
        return self::$do_fk_search_tool;
    }

    public static function get_fk_fields_to_skip() {
        return self::$fk_fields_to_skip;
    }

    public static function set_do_fk_search_tool($do_fk_search_tool) {
        self::$do_fk_search_tool = $do_fk_search_tool;
    }

    public static function set_fk_fields_to_skip(array $fk_fields_to_skip) {
        self::$fk_fields_to_skip = $fk_fields_to_skip;
    }

}

// ./src/crudlexs/object_classes/listing.php


namespace k1lib\crudlexs;

use k1lib\urlrewrite\url as url;

/**
 * 
 */
class listing extends crudlexs_base_with_data implements crudlexs_base_interface {

    /**
     * @var \k1lib\html\foundation\table_from_data
     */
    public $html_table;

    /**
     * @var int
     */
    protected $total_rows = 0;

    /**
     * @var int
     */
    protected $total_rows_filter = 0;

    /**
     * @var int
     */
    protected $total_pages = 0;

    /**
     * @var int
     */
    static public $characters_limit_on_cell = null;

    /**
     * @var int
     */
    static public $rows_per_page = 25;

    /**
     * @var int
     */
    static public $rows_limit_to_all = 200;

    /**
     *
     * @var array 
     */
    static public $rows_per_page_options = [5, 10, 25, 50, 100, 'all'];

    /**
     * @var int
     */
    protected $actual_page = 1;

    /**
     * @var int
     */
    protected $first_row_number = 1;

    /**
     * @var int
     */
    protected $last_row_number = 1;

    /**
     * @var string
     */
    protected $stat_msg;

    /**
     * @var int
     */
    protected $page_first = false;

    /**
     * @var int
     */
    protected $page_previous = false;

    /**
     * @var int
     */
    protected $page_next = false;

    /**
     * @var int
     */
    protected $page_last = false;

    /**
     * @var boolean
     */
    protected $do_orderby_headers = TRUE;

    public function __construct($db_table, $row_keys_text) {
        parent::__construct($db_table, $row_keys_text);

        $this->skip_blanks_on_filters = TRUE;

        $this->stat_msg = listing_strings::$stats_default_message;
    }

    /**
     * 
     * @return \k1lib\html\div
     */
    public function do_html_object() {
        $table_alias = \k1lib\db\security\db_table_aliases::encode($this->db_table->get_db_table_name());

        $this->div_container->set_attrib("class", "k1lib-crudlexs-list-content scroll-x");
        if ($this->db_table_data) {
            if ($this->do_orderby_headers) {
                $this->do_orderby_headers();
            }
            /**
             * Create the HTML table from DATA lodaed 
             */
            $this->html_table = new \k1lib\html\foundation\table_from_data("k1lib-crudlexs-list responsive-card-table unstriped {$table_alias}");
            $this->html_table->append_to($this->div_container);
            $this->html_table->set_max_text_length_on_cell(self::$characters_limit_on_cell);
            $this->html_table->set_data($this->db_table_data_filtered);
        } else {
            $div_message = new \k1lib\html\p(board_list_strings::$no_table_data, "callout primary");
            $div_message->append_to($this->div_container);
        }
        return $this->div_container;
    }

    /**
     * @return \k1lib\html\foundation\table_from_data
     */
    public function get_html_table() {
        return $this->html_table;
    }

    public function apply_orderby_headers() {
        $table_alias = \k1lib\db\security\db_table_aliases::encode($this->db_table->get_db_table_name());

        $sort_by_name = $table_alias . '-sort-by';
        $sort_mode_name = $table_alias . '-sort-mode';

        if (isset($_GET[$sort_by_name]) && (!empty($_GET[$sort_by_name]))) {
            if (isset($_GET[$sort_mode_name]) && ($_GET[$sort_mode_name] == 'ASC')) {
                $sort_mode = 'ASC';
            } else {
                $sort_mode = 'DESC';
            }
            $field = $this->decrypt_field_name($_GET[$sort_by_name]);
            if (!empty($field)) {
                $this->db_table->set_order_by($field, $sort_mode);
            }
        }
    }

    public function do_orderby_headers() {
        $this->set_do_table_field_name_encrypt();

        $headers = &$this->db_table_data_filtered[0];
        foreach ($headers as $field => $label) {
            $field_encrypted = $this->encrypt_field_name($field);
            $table_alias = \k1lib\db\security\db_table_aliases::encode($this->db_table->get_db_table_name());

            $sort_by_name = $table_alias . '-sort-by';
            $sort_mode_name = $table_alias . '-sort-mode';

            $sort_mode = 'ASC';
            $class_sort_mode = '';
            $class_active = ' non-ordering';

            if (isset($_GET[$sort_by_name]) && ($_GET[$sort_by_name] == $field_encrypted)) {
                $class_active = ' ordering';
                if (isset($_GET[$sort_mode_name]) && ($_GET[$sort_mode_name] == 'ASC')) {
                    $sort_mode = 'DESC';
                    $class_sort_mode = 'fi-arrow-down';
                } else {
                    $class_sort_mode = 'fi-arrow-up';
                }
            }

            $sort_url = url::do_url($_SERVER['REQUEST_URI'], [$sort_by_name => $field_encrypted, $sort_mode_name => $sort_mode]);
            $a = new \k1lib\html\a($sort_url, " $label", NULL, $class_sort_mode . $class_active);
            $headers[$field] = $a;
        }
    }

    /**
     * 
     * @return \k1lib\html\div
     */
    public function do_row_stats($custom_msg = "") {
        $div_stats = new \k1lib\html\div("k1lib-crudlexs-list-stats clearfix");
        if (($this->db_table_data)) {
            if (empty($custom_msg)) {
                $stat_msg = $this->stat_msg;
            } else {
                $stat_msg = $custom_msg;
            }
            $stat_msg = str_replace("--totalrowsfilter--", $this->total_rows_filter, $stat_msg);
            $stat_msg = str_replace("--totalrows--", $this->total_rows, $stat_msg);
            $stat_msg = str_replace("--firstrownumber--", $this->first_row_number, $stat_msg);
            $stat_msg = str_replace("--lastrownumber--", $this->last_row_number, $stat_msg);

            $div_stats->set_value($stat_msg);
        }
        return $div_stats;
    }

    /**
     * 
     * @return \k1lib\html\div
     */
    public function do_pagination() {

        $div_pagination = new \k1lib\html\div("k1lib-crudlexs-list-pagination clearfix", $this->get_object_id() . "-pagination");
        $div_scroller = $div_pagination->append_div("float-left pagination-scroller");
        $div_page_chooser = $div_pagination->append_div("float-left pagination-rows");

        if (($this->db_table_data) && (self::$rows_per_page <= $this->total_rows)) {

            $page_get_var_name = $this->get_object_id() . "-page";
            $rows_get_var_name = $this->get_object_id() . "-rows";

            $this_url = APP_URL . \k1lib\urlrewrite\url::get_this_url() . "#" . $this->get_object_id() . "-pagination";
            if ($this->actual_page > 2) {
                $this->page_first = url::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_first = "#";
            }

            if ($this->actual_page > 1) {
                $this->page_previous = url::do_url($this_url, [$page_get_var_name => ($this->actual_page - 1), $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_previous = "#";
            }

            if ($this->actual_page < $this->total_pages) {
                $this->page_next = url::do_url($this_url, [$page_get_var_name => ($this->actual_page + 1), $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_next = "#";
            }
            if ($this->actual_page <= ($this->total_pages - 2)) {
                $this->page_last = url::do_url($this_url, [$page_get_var_name => $this->total_pages, $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_last = "#";
            }
            /**
             * HTML UL- LI construction
             */
            $ul = (new \k1lib\html\ul("pagination k1lib-crudlexs " . $this->get_object_id()));
            $ul->append_to($div_scroller);

            // First page LI
            $li = $ul->append_li();
//    function append_a($href = NULL, $label = NULL, $target = NULL, $alt = NULL, $class = NULL, $id = NULL) {
            $a = $li->append_a($this->page_first, "‹‹", "_self", "k1lib-crudlexs-first-page");
            if ($this->page_first == "#") {
                $a->set_attrib("class", "disabled");
            }
            // Previuos page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_previous, "‹", "_self", "k1lib-crudlexs-previous-page");
            if ($this->page_previous == "#") {
                $a->set_attrib("class", "disabled");
            }
            /**
             * Page GOTO selector
             */
            $page_selector = new \k1lib\html\select("goto_page", "k1lib-crudlexs-page-goto", $this->get_object_id() . "-page-goto");
            $page_selector->set_attrib("onChange", "use_select_option_to_url_go(this)");
            for ($i = 1; $i <= $this->total_pages; $i++) {
                $option_url = url::do_url($this_url, [$page_get_var_name => $i, $rows_get_var_name => self::$rows_per_page]);
                $option = $page_selector->append_option($option_url, $i, (($this->actual_page == $i) ? TRUE : FALSE));
            }
            $ul->append_li()->append_child($page_selector);
            // Next page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_next, "›", "_self", "k1lib-crudlexs-next-page");
            if ($this->page_next == "#") {
                $a->set_attrib("class", "disabled");
            }
            // Last page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_last, "››", "_self", "k1lib-crudlexs-last-page");
            if ($this->page_last == "#") {
                $a->set_attrib("class", "disabled");
            }
            /**
             * PAGE ROWS selector
             */
            $num_rows_selector = new \k1lib\html\select("goto_page", "k1lib-crudlexs-page-goto", $this->get_object_id() . "-page-rows-goto");
            $num_rows_selector->set_attrib("onChange", "use_select_option_to_url_go(this)");
            foreach (self::$rows_per_page_options as $num_rows) {
                if ($num_rows <= $this->total_rows) {
                    $option_url = url::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => $num_rows]);
                    $option = $num_rows_selector->append_option($option_url, $num_rows, ((self::$rows_per_page == $num_rows) ? TRUE : FALSE));
                } else {
                    break;
                }
            }
            if ($this->total_rows <= self::$rows_limit_to_all) {
                $option_url = url::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => $this->total_rows]);
                $option = $num_rows_selector->append_option($option_url, $this->total_rows, ((self::$rows_per_page == $this->total_rows) ? TRUE : FALSE));
            }
            $label = (new \k1lib\html\label("Show", $this->get_object_id() . "-page-rows-goto"));
            $label->set_attrib("style", "display:inline");
            $label->append_to($div_page_chooser);
            $num_rows_selector->append_to($div_page_chooser);
        }
        return $div_pagination;
    }

    function set_stat_msg($stat_msg) {
        $this->stat_msg = $stat_msg;
    }

    function get_actual_page() {
        return $this->actual_page;
    }

    function set_actual_page($actual_page) {
        $this->actual_page = $actual_page;
    }

    function get_rows_per_page() {
        return self::$rows_per_page;
    }

    function set_rows_per_page($rows_per_page) {
        self::$rows_per_page = $rows_per_page;
    }

    public function load_db_table_data($show_rule = null) {
        // FIRST of all, get TABLE total rows
        $this->total_rows = $this->db_table->get_total_rows();

        // THEN get from GET vars if there is a row per page value
        if (isset($_GET[$this->object_id . "-rows"])) {
            $possible_rows_to_set = $_GET[$this->object_id . "-rows"];
            if ($possible_rows_to_set <= $this->total_rows) {
                self::$rows_per_page = $possible_rows_to_set;
            } else {
                // DO NOTHING
            }
        }
        // now we can know the total pages 
        $this->total_pages = ceil($this->total_rows / self::$rows_per_page);

        // The rows per page have to have a value, if is not set then we have to set it as the total rows
        if (self::$rows_per_page == 0) {
            self::$rows_per_page = $this->total_rows;
        }
        /**
         * Catch the GET value for pagination
         */
        if (isset($_GET[$this->object_id . "-page"])) {
            $possible_page_to_set = $_GET[$this->object_id . "-page"];
            if (($possible_page_to_set >= 1) && ($possible_page_to_set <= $this->total_pages)) {
                $this->actual_page = $possible_page_to_set;
            } else {
                $this->actual_page = 1;
            }
        }
        // SQL Limit time !
        if (self::$rows_per_page !== 0) {
            $offset = ($this->actual_page - 1) * self::$rows_per_page;
            $this->db_table->set_query_limit($offset, self::$rows_per_page);
        }
        if ($this->do_orderby_headers) {
            $this->apply_orderby_headers();
        }

        // GET DATA with a SQL Query
        if (parent::load_db_table_data($show_rule)) {
            $this->total_rows_filter = $this->db_table->get_total_data_rows();
            $this->first_row_number = $this->db_table->get_query_offset() + 1;
            $this->last_row_number = $this->db_table->get_query_offset() + $this->db_table->get_total_data_rows();


            return TRUE;
        } else {
            return FALSE;
        }
    }

    function get_page_first() {
        return $this->page_first;
    }

    function get_page_previous() {
        return $this->page_previous;
    }

    function get_page_next() {
        return $this->page_next;
    }

    function get_page_last() {
        return $this->page_last;
    }

    public function get_do_orderby_headers() {
        return $this->do_orderby_headers;
    }

    public function set_do_orderby_headers($do_orderby_headers) {
        $this->do_orderby_headers = $do_orderby_headers;
    }

}

// ./src/crudlexs/object_classes/reading.php


namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\html\DOM as DOM;
use k1lib\notifications\on_DOM as DOM_notification;

/**
 * 
 */
class reading extends crudlexs_base_with_data implements crudlexs_base_interface {

    private $html_column_classes = "large-4 medium-6 small-12 column";

    public function __construct($db_table, $row_keys_text, $custom_auth_code = "") {
        if (!empty($row_keys_text)) {
            parent::__construct($db_table, $row_keys_text, $custom_auth_code);
        } else {
            DOM_notification::queue_mesasage(object_base_strings::$error_no_row_keys_text, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
        }

        /**
         * Necessary for do not loose the inputs with blank or null data
         */
        $this->skip_blanks_on_filters = TRUE;
    }

    public function do_html_object() {
        if ($this->db_table_data) {
            $this->div_container->set_attrib("class", "row k1lib-crudlexs-" . $this->css_class);
            $this->div_container->set_attrib("id", $this->object_id);

            $table_alias = \k1lib\db\security\db_table_aliases::encode($this->db_table->get_db_table_name());

            $data_group = new \k1lib\html\div("k1lib-data-group");
            $data_group->set_id("{$table_alias}-fields");

            $data_group->append_to($this->div_container);
            $text_fields_div = new \k1lib\html\div("grid-x");

            $data_label = $this->get_labels_from_data(1);
            if (!empty($data_label)) {
                $this->remove_labels_from_data_filtered();
                (new \k1lib\html\h3($data_label, "k1lib-data-group-title " . $this->css_class, "label-field-{$this->object_id}"))->append_to($data_group);
            }
            $labels = $this->db_table_data_filtered[0];
            $values = $this->db_table_data_filtered[1];
            $row = $data_group->append_div("grid-x");

            foreach ($values as $field => $value) {
                if (array_search($field, $this->fields_to_hide) !== FALSE) {
                    continue;
                }
                if (($value !== 0) && ($value !== NULL)) {
                    /**
                     * ALL the TEXT field types are sendend to the last position to show nicely the HTML on it.
                     */
                    $field_type = $this->db_table->get_field_config($field, 'type');
                    $field_alias = $this->db_table->get_field_config($field, 'alias');
                    if ($field_type == 'text') {
                        $div_rows = $text_fields_div->append_div("large-12 column k1lib-data-item");
                    } else {
                        $div_rows = $row->append_div($this->html_column_classes . " k1lib-data-item");
                    }
                    if (!empty($field_alias)) {
                        $div_rows->set_id("{$field_alias}-row");
                    }
                    $label = $div_rows->append_div("k1lib-data-item-label")->set_value($labels[$field]);
                    $value_div = $div_rows->append_div("k1lib-data-item-value")->set_value($value);
                    if (!empty($field_alias)) {
                        $div_rows->set_id("row-{$field_alias}");
                        $label->set_id("label-{$field_alias}");
                        if (method_exists($value, "set_id")) {
                            $value->set_id("value-{$field_alias}");
                        } else {
                            $value_div->set_id("value-{$field_alias}");
                        }
                    }
                }
            }
            $text_fields_div->append_to($data_group);

            return $this->div_container;
        } else {
            return FALSE;
        }
    }

    public function get_html_column_classes() {
        return $this->html_column_classes;
    }

    public function set_html_column_classes($html_column_classes) {
        $this->html_column_classes = $html_column_classes;
    }

}

// ./src/crudlexs/object_classes/search-helper.php


namespace k1lib\crudlexs;

class search_helper extends creating {

    /**
     *
     * @var Array 
     */
    public $db_table_data = FALSE;

    /**
     *
     * @var Boolean 
     */
    protected $db_table_data_keys = FALSE;

    /**
     * @var string
     */
    protected $caller_objetc_id = null;
    protected $search_catch_post_enable = TRUE;
    protected $caller_url = null;

// FILTERS
    public function __construct(\k1lib\crudlexs\class_db_table $db_table) {
        parent::__construct($db_table, FALSE);
        if (isset($_GET['caller-url'])) {
            $this->caller_url = urldecode($_GET['caller-url']);
        } else {
            d("No caller url");
        }
        creating_strings::$button_submit = search_helper_strings::$button_submit;
        creating_strings::$button_cancel = search_helper_strings::$button_cancel;

        $this->show_cancel_button = FALSE;

        $this->set_do_table_field_name_encrypt(TRUE);


        $this->db_table->set_db_table_show_rule("show-search");
    }

    public function do_html_object() {
        if ($this->search_catch_post_enable && $this->catch_post_data()) {
            $this->put_post_data_on_table_data();
            $this->db_table->set_query_filter($this->post_incoming_array, FALSE);
        }
        $this->apply_label_filter();

        $this->insert_inputs_on_data_row();

        $search_html = parent::do_html_object();
        $search_html->get_elements_by_tag("form")[0]->set_attrib("action", $this->caller_url);
        $search_html->get_elements_by_tag("form")[0]->set_attrib("target", "_parent");
        $search_html->get_elements_by_tag("form")[0]->append_child(new \k1lib\html\input("hidden", "from-search", urlencode($this->caller_url)));
        return $search_html;
    }

    function catch_post_data() {
        $search_post = \k1lib\common\unserialize_var(urlencode($this->caller_url));
        if (empty($search_post)) {
            $search_post = [];
        }
        $_POST = array_merge($search_post, $_POST);
        if (parent::catch_post_data()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function set_search_catch_post_enable($search_catch_post_enable) {
        $this->search_catch_post_enable = $search_catch_post_enable;
    }

}

// ./src/crudlexs/object_classes/updating.php


namespace k1lib\crudlexs;

use \k1lib\urlrewrite\url as url;
use k1lib\html\DOM as DOM;
use \k1lib\notifications\on_DOM as DOM_notification;

class updating extends \k1lib\crudlexs\creating {

    protected $update_perfomed = FALSE;
    protected $do_redirect = FALSE;
    protected $updated = NULL;

    public function __construct(\k1lib\crudlexs\class_db_table $db_table, $row_keys_text) {
        if (!empty($row_keys_text)) {
            parent::__construct($db_table, $row_keys_text);
        } else {
            DOM_notification::queue_mesasage(object_base_strings::$error_no_row_keys_text, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
        }

        creating_strings::$button_submit = updating_strings::$button_submit;
        creating_strings::$button_cancel = updating_strings::$button_cancel;

        $this->object_state = "update";
    }

    public function load_db_table_data($blank_data = FALSE) {
        $return_data = parent::load_db_table_data($blank_data);

        $url_action = url::set_url_rewrite_var(url::get_url_level_count(), "url_action", FALSE);
        $url_action_on_encoded_field = url::set_url_rewrite_var(url::get_url_level_count(), "url_action_on_encoded_field", FALSE);
        $url_action_on_field = $this->decrypt_field_name($url_action_on_encoded_field);

        if ($url_action == "unlink-uploaded-file") {
            $this->db_table_data[1][$url_action_on_field] = NULL;
            if ($this->db_table->update_data($this->db_table_data[1], $this->db_table_data_keys[1])) {
                \k1lib\forms\file_uploads::unlink_uploaded_file($this->db_table_data[1][$url_action_on_field], $this->db_table->get_db_table_name());
                DOM_notification::queue_mesasage("File deleted!", 'success');
            } else {
                DOM_notification::queue_mesasage("File could not be deleted, please upload another to replace.", 'alert');
            }
            \k1lib\html\html_header_go(\k1lib\urlrewrite\get_back_url());
        }

        return $return_data;
    }

    public function do_update() {
        //$this->set_back_url("javascript:history.back()");
        $error_data = NULL;

        $this->div_container->set_attrib("class", "k1lib-crudlexs-update");
        $this->post_incoming_array = \k1lib\forms\check_all_incomming_vars($this->post_incoming_array);
        $update_result = $this->db_table->update_data($this->post_incoming_array, $this->db_table_data_keys[1], $error_data);
        if ($update_result !== FALSE) {
            $this->update_perfomed = TRUE;
            $this->do_redirect = TRUE;
            $this->updated = TRUE;
            DOM_notification::queue_mesasage(updating_strings::$data_updated, "success", $this->notifications_div_id);
            return TRUE;
        } else {
            $this->update_perfomed = FALSE;
            $this->updated = FALSE;
            if (is_array($error_data) && !empty($error_data)) {
                $this->post_validation_errors = array_merge($this->post_validation_errors, $error_data);
            } elseif (is_string($error_data)) {
                DOM_notification::queue_mesasage($error_data, "alert", $this->notifications_div_id);
            }
            if (empty($this->post_validation_errors)) {
                $this->do_redirect = TRUE;
            } else {
                $this->do_redirect = FALSE;
            }
            DOM_notification::queue_mesasage(updating_strings::$data_not_updated, "warning", $this->notifications_div_id);
            if (!empty($error_data)) {
                DOM_notification::queue_mesasage(print_r($error_data, TRUE), 'alert', $this->notifications_div_id);
            } else {
//                DOM_notification::queue_mesasage(print_r($this->post_incoming_array, TRUE), 'alert', $this->notifications_div_id);
            }
            return FALSE;
        }
    }

    public function post_update_redirect($url_to_go = "../../", $do_redirect = TRUE) {
        if ($this->update_perfomed || $this->do_redirect) {
            /**
             * Merge the ROW KEYS with all the possible keys on the POST array
             */
            $merged_key_array = array_merge(
                    $this->db_table_data_keys[1]
                    , \k1lib\sql\get_keys_array_from_row_data(
                            $this->post_incoming_array
                            , $this->db_table->get_db_table_config()
                    )
            );
            $row_key_text = \k1lib\sql\table_keys_to_text($merged_key_array, $this->db_table->get_db_table_config());
            if (!empty($url_to_go)) {
                $this->set_auth_code($row_key_text);
                $this->set_auth_code_personal($row_key_text);
                $url_to_go = str_replace("--rowkeys--", $row_key_text, $url_to_go);
                $url_to_go = str_replace("--authcode--", $this->get_auth_code(), $url_to_go);
            }
            if ($do_redirect) {
                \k1lib\html\html_header_go($url_to_go);
                return TRUE;
            } else {
                return $url_to_go;
            }
        } else {
            return "";
        }
    }

}

// ./src/db/classes.php


namespace k1lib\db;

/**
 * 
 */
class handler extends \PDO {

    /**
     * Enable state
     * @var Boolean 
     */
    static protected $enabled = FALSE;
    static protected $db_dsn;
    static protected $db_name;
    static protected $db_user;
    static protected $db_password;
    static protected $db_host;
    static protected $db_port;

    /**
     *  Verbose level for error output
     * @var type 
     */
    static protected $verbose_level = 0;

    /**
     * Enable the engenie
     * @param string $db_name
     * @param string $db_user
     * @param string $db_password
     * @param string $db_host
     * @param integer $db_port
     * @param string $db_type
     */
    static public function enable($db_name, $db_user, $db_password, $db_host = "localhost", $db_port = 3306, $db_type = "mysql", $pdo_string_altern = FALSE) {
        self::$enabled = TRUE;
        self::$db_name = $db_name;
        self::$db_user = $db_user;
        self::$db_password = $db_password;
        self::$db_host = $db_host;
        self::$db_port = $db_port;
        if ($pdo_string_altern) {
            self::$db_dsn = "{$db_type}:dbname={$db_name};host={$db_host}:{$db_port}";
        } else {
            self::$db_dsn = "{$db_type}:dbname={$db_name};host={$db_host};port={$db_port}";
        }
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("DB system is not enabled yet", E_USER_ERROR);
        }
        return self::$enabled;
    }

    public function __construct($options = null) {
        self::is_enabled(true);
        parent::__construct(self::$db_dsn, self::$db_user, self::$db_password, $options);
    }

    static function get_verbose_level() {
        self::is_enabled(true);
        return self::$verbose_level;
    }

    function set_verbose_level($verbose_level) {
        self::is_enabled(true);
        self::$verbose_level = $verbose_level;
        if (self::$verbose_level == 0) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        } elseif (self::$verbose_level > 0) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

    function query($statement) {
        try {
            $result = parent::query($statement);
        } catch (\PDOException $exc) {
            switch (self::$verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_NOTICE);
                    break;
                case 1:
                    trigger_error($exc->getMessage(), E_USER_NOTICE);
                    break;
                case 2:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_NOTICE);
                    break;
                case 3:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_NOTICE);
                    d($exc->getTraceAsString());
                    break;
                default:
                    break;
            }
            $result = FALSE;
        }

        return $result;
    }

    function exec($statement) {
        try {
            $result = parent::exec($statement);
        } catch (\PDOException $exc) {
            switch (self::$verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_WARNING);
                    break;
                case 1:
                    trigger_error($exc->getMessage(), E_USER_WARNING);
                    break;
                case 2:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_WARNING);
                    break;
                case 3:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_WARNING);
                    d($exc->getTraceAsString());
                    break;
                default:
                    break;
            }
            $result = FALSE;
        }

        return $result;
    }

}

// ./src/db/db-security.php


namespace k1lib\db\security;

class db_table_aliases {

    static public $aliases = [];

    static function encode($table_name) {
        if (key_exists($table_name, self::$aliases)) {
            return self::$aliases[$table_name];
        } else {
            return $table_name;
        }
    }

    static function decode($encoded_table_name) {
        $fliped_array = array_flip(self::$aliases);
        if (key_exists($encoded_table_name, $fliped_array)) {
            return $fliped_array[$encoded_table_name];
        } else {
            return $encoded_table_name;
        }
    }

}

// ./src/db/pdo_k1.php

/**
 * New DB class to make easier multiple DB connections
 */

namespace k1lib\db;

/**
 * 
 */
class PDO_k1 extends \PDO {

    /**
     * Enable state
     * @var Boolean 
     */
    protected $enabled = FALSE;
    protected $db_dsn;
    protected $db_name;
    protected $db_user;
    protected $db_password;
    protected $db_host;
    protected $db_port;

    /**
     *  Verbose level for error output
     * @var type 
     */
    protected $verbose_level = 0;

    /**
     * Query the enabled state
     * @return Boolean
     */
    public function is_enabled($show_error = false) {
        if ($show_error && (!$this->enabled)) {
            trigger_error("DB system is not enabled yet", E_USER_ERROR);
        }
        return $this->enabled;
    }

    /**
     * Start the engenie
     * @param string $db_name
     * @param string $db_user
     * @param string $db_password
     * @param string $db_host
     * @param integer $db_port
     * @param string $db_type
     */
    public function __construct($db_name, $db_user, $db_password, $db_host = "localhost", $db_port = 3306, $db_type = "mysql", $pdo_string_altern = FALSE) {
        $this->enabled = TRUE;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
        $this->db_host = $db_host;
        $this->db_port = $db_port;
        if ($pdo_string_altern) {
            $this->db_dsn = "{$db_type}:dbname={$db_name};host={$db_host}:{$db_port}";
        } else {
            $this->db_dsn = "{$db_type}:dbname={$db_name};host={$db_host};port={$db_port}";
        }

        $this->is_enabled(true);
        parent::__construct($this->db_dsn, $this->db_user, $this->db_password);
    }

    function get_verbose_level() {
        $this->is_enabled(true);
        return $this->verbose_level;
    }

    function set_verbose_level($verbose_level) {
        $this->is_enabled(true);
        $this->verbose_level = $verbose_level;
        if ($this->verbose_level == 0) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        } elseif ($this->verbose_level > 0) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

    function query($statement) {
        try {
            $result = parent::query($statement);
        } catch (\PDOException $exc) {
            switch ($this->verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_NOTICE);
                    break;
                case 1:
                    d($statement);
                    trigger_error($exc->getMessage(), E_USER_NOTICE);
                    break;
                case 2:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_NOTICE);
                    break;
                case 3:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_NOTICE);
                    d($exc->getTraceAsString());
                    break;
                default:
                    break;
            }
            $result = FALSE;
        }

        return $result;
    }

    function exec($statement) {
        try {
            $result = parent::exec($statement);
        } catch (\PDOException $exc) {
            switch ($this->verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_WARNING);
                    break;
                case 1:
                    trigger_error($exc->getMessage(), E_USER_WARNING);
                    break;
                case 2:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_WARNING);
                    break;
                case 3:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_WARNING);
                    d($exc->getTraceAsString());
                    break;
                default:
                    break;
            }
            $result = FALSE;
        }

        return $result;
    }

    function get_db_dsn() {
        return $this->db_dsn;
    }

    function get_db_name() {
        return $this->db_name;
    }

    function get_db_user() {
        return $this->db_user;
    }

    function get_db_password() {
        return $this->db_password;
    }

    function get_db_host() {
        return $this->db_host;
    }

    function get_db_port() {
        return $this->db_port;
    }
}
// ./src/encryption/classes.php


namespace k1lib;

class crypt {

    /**
     *
     * @var string 64 character key, set as your own always !!
     */
    static protected $key = "bdb07f99c3de1895cdc8795b5091cf9b9aad67692564d88b87f50c91eba233da";
    static private $cipher = "aes-128-gcm";
    static private $iv_send_lenght = 24;
    static private $tag_send_lenght = 32;

    static function encrypt($value) {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        $ivlen = openssl_cipher_iv_length(static::$cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);
        $iv_64 = bin2hex($iv);
        $ciphertext = openssl_encrypt($value, static::$cipher, static::$key, $options = 0, $iv, $tag);
        $tag_64 = bin2hex($tag);

        $return_value = ($iv_64 . $tag_64 . $ciphertext);
        return $return_value;
    }

    static function decrypt($value) {
        $value = ($value);
        $iv_64 = substr($value, 0, static::$iv_send_lenght);
        $tag_64 = substr($value, static::$iv_send_lenght, static::$tag_send_lenght);
        $iv = hex2bin($iv_64);
        $tag = hex2bin($tag_64);
        $ciphertext = substr($value, static::$iv_send_lenght + static::$tag_send_lenght);
        $original_plaintext = openssl_decrypt($ciphertext, static::$cipher, static::$key, $options = 0, $iv, $tag);

        if (($json_test = json_decode($original_plaintext, TRUE)) !== NULL) {
            $original_plaintext = $json_test;
        }
        return $original_plaintext;
    }

}

// ./src/error_handler/error-handler.php


/**
 * TODO: Make this... 
 */
function k1_deprecated($func_name) {
    trigger_error("Function '" . $func_name . "' do not exist more, please use the new class instead", E_USER_ERROR);
}

// ./src/forms/classes.php


/**
 * Forms related functions, K1.lib.
 * 
 * Common needed actions on forms and special ideas implemented with this lib.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package forms
 */

namespace k1lib\forms;

use k1lib\html as html;

class file_uploads {

    const ERROR_FILE_ALREADY_EXIST = -1;
    const ERROR_FILE_NOT_MOVED_TO_UPLOAD_PATH = -2;

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;
    static private $overwrite_existent = TRUE;

    /**
     * Uploads path to store files 
     * @var char
     */
    static private $path_to_uploads;

    /**
     * Uploads URL to where the files are
     * @var char
     */
    static private $uploads_url;

    /**
     *
     * @var string
     */
    static private $last_error = NULL;

    /**
     * Enable the engenie
     */
    static public function enable($path_to_upload, $uploads_url) {
        self::$enabled = TRUE;
        if (file_exists($path_to_upload)) {
            self::$path_to_uploads = $path_to_upload;
            self::$uploads_url = $uploads_url;
        } else {
            trigger_error("The upload path [{$path_to_upload}] do not exists. Uploads will fail!", E_USER_WARNING);
        }
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("Files uploads are not enabled yet!", E_USER_ERROR);
        }
        return self::$enabled;
    }

    static function place_upload_file($tmp_file, $file_name, $directory = NULL) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        if (!empty($directory)) {
            if (!file_exists(self::$path_to_uploads . $directory)) {
                mkdir(self::$path_to_uploads . $directory);
            }
            $file_name_to_save = self::$path_to_uploads . $directory . '/' . $file_name;
        } else {
            $file_name_to_save = self::$path_to_uploads . $file_name;
        }
//        $file_name_to_save = self::$path_to_uploads . md5($file_name) . ".{$file_extension}";

        if (file_exists($file_name_to_save) && !self::$overwrite_existent) {
            self::$last_error = "File name aready exist and won't be overwriten";
            trigger_error(self::$last_error, E_USER_NOTICE);
            return FALSE;
        } else {
            if (is_uploaded_file($tmp_file)) {
                if (move_uploaded_file($tmp_file, $file_name_to_save) === TRUE) {
                    return $file_name_to_save;
                } else {
                    self::$last_error = "File couldn't be moved to the uplaod directory, check file permissions.";
                    trigger_error(self::$last_error, E_USER_NOTICE);
                    return FALSE;
                }
            } else {
                trigger_error("Possible hack attemp", E_USER_NOTICE);
            }
        }
    }

    static function get_uploaded_file_path($file_name, $directory = NULL) {
        if (!empty($directory)) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $file_name_to_get_directory = self::$path_to_uploads . $directory . '/' . $file_name;
        } else {
            $file_name_to_get_directory = NULL;
        }
        $file_name_to_get = self::$path_to_uploads . $file_name;
//        $file_name_to_get = self::$path_to_uploads . md5($file_name) . ".{$file_extension}";
        if (file_exists($file_name_to_get)) {
            return $file_name_to_get;
        } elseif (!empty($file_name_to_get_directory) || file_exists($file_name_to_get_directory)) {
            return $file_name_to_get_directory;
        } else {
            return FALSE;
        }
    }

    static function get_uploaded_file_url($file_name, $directory = NULL) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (self::get_uploaded_file_path($file_name, $directory)) {
            $file_name_to_get = self::$uploads_url . $directory . '/' . $file_name;
//        $file_name_to_get = self::$uploads_url . md5($file_name) . ".{$file_extension}";
        } else if (self::get_uploaded_file_path($file_name)) {
            $file_name_to_get = self::$uploads_url . $file_name;
//        $file_name_to_get = self::$uploads_url . md5($file_name) . ".{$file_extension}";
        } else {
            return FALSE;
        }
        return $file_name_to_get;
    }

    static function unlink_uploaded_file($file_name, $directory = NULL) {
//        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        $file_name_to_get = self::$path_to_uploads . $file_name;
        $file_name_to_get_with_directory = self::$path_to_uploads . $directory . '/' . $file_name;
//        $file_name_to_get = self::$path_to_uploads . md5($file_name) . ".{$file_extension}";
        if (file_exists($file_name_to_get)) {
            return unlink($file_name_to_get);
        } elseif (file_exists($file_name_to_get_with_directory)) {
            return unlink($file_name_to_get_with_directory);
        } else {
            return FALSE;
        }
    }

    static function get_uploads_url($directory = NULL) {
        if (!empty($directory)) {
            return self::$uploads_url . $directory . '/';
        } else {
            return self::$uploads_url;
        }
    }

    static function get_overwrite_existent() {
        return self::$overwrite_existent;
    }

    static function set_overwrite_existent($overwrite_existent) {
        self::$overwrite_existent = $overwrite_existent;
    }

    static function get_last_error() {
        return self::$last_error;
    }

}

// ./src/forms/functions.php


/**
 * Forms related functions, K1.lib.
 * 
 * Common needed actions on forms and special ideas implemented with this lib.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package forms
 */

namespace k1lib\forms;

use k1lib\html as html;

/**
 * This SHOULD be used always to receive any kind of value from _GET _POST _REQUEST if it will be used on SQL staments.
 * @param String $var Value to check.
 * @param Boolean $request FALSE to check $var as is. Use TRUE if $var become an index as: $_REQUEST[$var]
 * @param Boolean $url_decode TRUE if the data should be URL decoded.
 * @return String Rerturn NULL on error This could be that $var IS NOT String, Number or IS Array.
 */
function check_single_incomming_var($var, $request = FALSE, $url_decode = FALSE) {
    if ((is_string($var) || is_numeric($var)) && !is_array($var)) {
        if (($request == TRUE) && isset($_REQUEST[$var])) {
            $value = $_REQUEST[$var];
        } elseif ($request == FALSE) {
            $value = $var;
        } else {
            $value = NULL;
        }
        if ($value === '') {
            return NULL;
        } elseif (($value === 0)) {
            return 0;
        } elseif (($value === '0')) {
            return '0';
        } else {
//            $value = htmlspecialchars($value);
        }
        if ($url_decode) {
            $value = urldecode($value);
        }
        if (\json_decode($value) === NULL) {
//            $search = ['\\', "\0", "\n", "\r", "'", '"', "\x1a"];
//            $replace = ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'];
//            $value = str_replace($search, $replace, $value);
//            $value = @mysql_real_escape_string($value);
        }
        return $value;
    } else {
        return NULL;
    }
}

/**
 * Prevents SQL injection from any ARRAY, at the same time serialize the var into save_name. This uses check_single_incomming_var() on each array item. Is recursive.
 * @param array $request_array
 * @param string $save_name
 * @return array checked values ready to work
 */
function check_all_incomming_vars($request_array, $save_name = null) {
//checks all the incomming vars
// V0.8 forces the use of an non empty array
//    if (empty($request_array)) {
//        $request_array = $_REQUEST;
//    } else {
    if (!is_array($request_array)) {
        die(__FUNCTION__ . " need an array to work");
    }
//    }
    $form = array();
    foreach ($request_array as $index => $value) {
        if (!is_array($value)) {
            $form[$index] = \k1lib\forms\check_single_incomming_var($value);
        } else {
            $form[$index] = check_all_incomming_vars($value);
        }
    }
    if (!empty($save_name)) {
        \k1lib\common\serialize_var($form, $save_name);
    }
    return $form;
}

/**
 * Get a single value from a serialized var if is an array, this one do not echo erros only return FALSE is there is not stored
 * @param string $form_name
 * @param string $field_name
 * @param string $default
 * @return mixed
 */
function get_form_field_from_serialized($form_name, $field_name, $default = "", $compare = "--FALSE--") {
    if (!is_string($form_name) || empty($form_name)) {
        die(__FUNCTION__ . " form_name should be an non empty string");
    }
    if (empty($field_name)) {
        die(__FUNCTION__ . " field_name should be an non empty string");
    }
    $field_value = "";
    //FORM EXISTS
    if (isset($_SESSION['serialized_vars'][$form_name])) {
        // FIELD EXISTS
        if (isset($_SESSION['serialized_vars'][$form_name][$field_name])) {
            $field_value = $_SESSION['serialized_vars'][$form_name][$field_name];
            if ($compare !== "--FALSE--") {
                if ($field_value === $compare) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } else {
            if ($compare !== "--FALSE--") {
                if ($default === $compare) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                $field_value = $default;
            }
        }
    } else {
        $field_value = FALSE;
//        die(__FUNCTION__ . " serialized var '$form_name' do not exist! ");
    }

    return $field_value;
}

/**
 * Checks with this function if a received value matchs with a item on a ENUM field on a table
 * @param String $value Value to check
 * @param String  $table_name SQL table name
 * @param String $table_field SQL table field to check
 * @param \PDO $db DB connection object
 * @return string
 */
function check_enum_value_type($received_value, $table_name, $table_field, \PDO $db) {
    $options = \k1lib\sql\get_db_table_enum_values($db, $table_name, $table_field);
    $options_fliped = array_flip($options);

    if (!isset($options_fliped[$received_value])) {
        $error_type = print_r($options_fliped, TRUE) . " value: '$received_value'";
//        d($received_value, TRUE);
    } else {
        $error_type = FALSE;
    }
    return $error_type;
}

function check_value_type($value, $type) {

    //dates for use
    $date = date("Y-m-d");
    $day = date("d");
    $month = date("m");
    $year = date("Y");
    //funcitons vars
    $error_type = "";
    $preg_symbols = "\-_@.,!:;#$%&'*\\/+=?^`{\|}()~ÁÉÍÓÚáéíóuñÑ";
    $preg_symbols_html = $preg_symbols . "<>\\\\\"'";
    $preg_file_symbols = "-_.()";

    switch ($type) {
        case 'options':
            trigger_error("This function can't check options type", E_USER_WARNING);
            $error_type = " This vale can't be checked";
            break;
        case 'email':
            $regex = "/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/";
            if (!preg_match($regex, $value)) {
                $error_type = " debe ser un Email valido.";
            }
            break;
        case 'boolean':
            $regex = "/^[01]$/";
            if (!preg_match($regex, $value)) {
                $error_type = "$error_header_msg solo puede valer 0 o 1";
            } else {
                $error_msg = "";
            }
            break;
        case 'boolean-unsigned':
            $regex = "/^[01]$/";
            if (!preg_match($regex, $value)) {
                $error_type = "$error_header_msg solo puede valer 0 o 1";
            } else {
                $error_msg = "";
            }
            break;
        case 'date':
            if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                if (!checkdate($matches['month'], $matches['day'], $matches['year'])) {
                    $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
                }
            } else {
                $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
            }
            break;
        case 'date-past':
            if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                if (checkdate($matches['month'], $matches['day'], $matches['year'])) {
                    $actual_date_number = juliantojd($month, $day, $year);
                    $value_date_number = juliantojd($matches['month'], $matches['day'], $matches['year']);
                    if ($value_date_number >= $actual_date_number) {
                        $error_type = " de fecha no puede ser mayor al dia de hoy: {$date}";
                    }
                } else {
                    $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
                }
            } else {
                $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
            }
            break;
        case 'date-future':
            if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                if (checkdate($matches['month'], $matches['day'], $matches['year'])) {
                    $actual_date_number = juliantojd($month, $day, $year);
                    $value_date_number = juliantojd($matches['month'], $matches['day'], $matches['year']);
                    if ($value_date_number <= $actual_date_number) {
                        $error_type = " de fecha debe ser mayor al dia de hoy: {$date}";
                    }
                } else {
                    $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
                }
            } else {
                $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
            }
            break;
        case 'datetime':
            // TODO
            break;
        case 'time':
            // TODO
            break;
        case 'letters':
            $regex = "/^[a-zA-Z\s]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z sin símbolos";
            }
            break;
        case 'letters-symbols':
            $regex = "/^[a-zA-Z0-9\s{$preg_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z y símbolos: $preg_symbols";
            }
            break;
        case 'password':
            $regex = "/^[a-zA-Z0-9\s{$preg_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z y símbolos: $preg_symbols";
            }
            break;
        case 'decimals-unsigned':
            $regex = "/^[0-9.]*$/";
            if (!(preg_match($regex, $value) && is_numeric($value))) {
                $error_type .= " deber ser solo números y decimales positivos";
            }
            break;
        case 'decimals':
            $regex = "/^[\-0-9.]*$/";
            if (!(preg_match($regex, $value) && is_numeric($value))) {
                $error_type = " debe contener solo números y decimales";
            }
            break;
        case 'numbers-unsigned':
            $regex = "/^[0-9]*$/";
            if (!(preg_match($regex, $value) && is_numeric($value))) {
                $error_type .= " deber ser solo números positivos";
            }
            break;
        case 'numbers':
            $regex = "/^[\-0-9]*$/";
            if (!(preg_match($regex, $value) && is_numeric($value))) {
                $error_type = " debe contener solo números";
            }
            break;
        case 'numbers-symbols':
            $regex = "/^[\-0-9\s{$preg_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " debe contener solo números y símbolos: $preg_symbols";
            }
            break;
        case 'mixed':
            $regex = "/^[\-a-zA-Z0-9\s]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z y números";
            }
            break;
        case 'mixed-symbols':
            $regex = "/^[a-zA-Z0-9\s{$preg_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z, números y símbolos: $preg_symbols";
            }
            break;
        case 'html':
            $regex = "/^[a-zA-Z0-9\s{$preg_symbols_html}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z, números y símbolos: $preg_symbols_html";
            }
            break;
        case 'file-upload':
            $regex = "/^[a-zA-Z0-9\s{$preg_file_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " solo pude contener letras y los siguientes simbolos: $preg_symbols";
            }
            break;
        case 'not-verified':
            break;
        default:
            $error_type = "Not defined VALIDATION on Type '{$type}' from field '{$label}' ";
            break;
    }
    return $error_type;
}

function form_file_upload_handle($file_data, $field_config, $table_name = null) {
    /**
     * File validations with DB Table Config directives
     */
    $file_max_size = $field_config['file-max-size'] + 0;
    $file_type = $field_config['file-type'];
    if (strstr($file_data['type'], $file_type) === FALSE) {
        return "The file type is {$file_data['type']} not {$file_type}";
    }
    if ($file_data['size'] > $file_max_size) {
        return "Size is bigger than " . $file_max_size / 1024 . "k";
    }
    /**
     * ALL ok? then place the file and let it go... let it goooo! (my daughter Allison fault! <3 )
     */
    if (file_uploads::place_upload_file($file_data['tmp_name'], $file_data['name'], $table_name)) {
        return TRUE;
    } else {
        return file_uploads::get_last_error();
    }
//    $file_location = file_uploads::get_uploaded_file_path($file_data['name']);
//    $file_location_url = file_uploads::get_uploaded_file_url($file_data['name']);
}

function form_check_values(&$form_array, $table_array_config, $db = NULL) {
    if (!is_array($form_array)) {
        die(__FUNCTION__ . " need an array to work on \$form_array");
    }
    if (!is_array($table_array_config)) {
        die(__FUNCTION__ . " need an array to work on \$required_array");
    }
    $error_array = array();
    foreach ($form_array as $key => $value) {

        $error_header_msg = "Este campo ";
        $error_msg = "";
        $error_type = "";

        /**
         * FILE UPLOAD HACK
         */
        $do_upload_file = FALSE;
        if (is_array($value)) {
            $do_upload_file = TRUE;
            // Uniques on name name fix
            $value['name'] = time() . '-' . $value['name'];
            $file_data = $value;
            $value = $value['name'];
//            $form_array[$key]['name'] = $value;
            $form_array[$key] = $value;
        }
        /**
         *  TYPE CHECK
         *  -- then See each field value to check if is valid with the table tyoe definition
         */
        // MIN - MAX check
        $min = $table_array_config[$key]['min'];
        $max = $table_array_config[$key]['max'];


        // email | letters (solo letras) | numbers (solo números) | mixed (alfanumerico) | letters-symbols (con símbolos ej. !#()[],.) | numbers-symbols | mixed-symbols - los symbols no lo implementare aun
        // the basic error, if is required on the table definition
        if (($value !== 0) && ($value !== '0') && empty($value)) {
            if ($table_array_config[$key]['required'] === TRUE) {
                $error_msg = "$error_header_msg es requerido.";
            }
        } elseif ((strlen((string) $value) < (int) $min) || (strlen((string) $value) > (int) $max)) {
            $error_msg = "$error_header_msg debe ser de minimo $min y maximo $max caracteres";
        }

        if (($value === 0) || !empty($value)) {
            if ($table_array_config[$key]['validation'] == 'options') {
                $error_type = check_enum_value_type($value, $table_array_config[$key]['table'], $key, $db);
            } else {
                $unsigned_type = ($table_array_config[$key]['unsigned']) ? "-unsigned" : "";
                $error_type = check_value_type($value, $table_array_config[$key]['validation'] . $unsigned_type);
            }
        }
        if ($do_upload_file && empty($error_msg) && empty($error_type)) {
//            d($table_array_config[$key]);
            $file_result = form_file_upload_handle($file_data, $table_array_config[$key], $table_array_config[$key]['table']);
            if ($file_result !== TRUE) {
                $error_array[$key] = $file_result;
            }
            $error_msg = "";
            $error_type = "";
        }
        if (empty($error_type) && !empty($error_msg)) {
            $error_array[$key] = $error_msg;
        } else if (!empty($error_type) && empty($error_msg)) {
            $error_array[$key] = "$error_header_msg $error_type";
        } else if (!empty($error_type) && !empty($error_msg)) {
            $error_array[$key] = "$error_msg - $error_type";
        } else {
//            d("$value is {$table_array_config[$key]['validation']}");
        }
    }

    if (count($error_array) > 0) {
        return $error_array;
    } else {
        return FALSE;
    }
}

function make_form_select_list(&$field_name, &$value, &$table_config_array, &$error_msg = "") {
    global $db;

    /*
     * SELECT LIST
     */
    //ENUM drop list
    $select_data_array = array();
    if ($table_config_array[$field_name]['type'] == "enum") {
        $select_data_array = \k1lib\sql\get_db_table_enum_values($db, $table_config_array[$field_name]['table'], $field_name);
    } elseif ($table_config_array[$field_name]['sql'] != "") {
        $table_config_array[$field_name]['sql'];
        $sql_data = \k1lib\sql\sql_query($db, $table_config_array[$field_name]['sql'], TRUE);
        if (!empty($sql_data)) {
            foreach ($sql_data as $row) {
                $select_data_array[$row['value']] = $row['label'];
            }
        }
    } elseif (!empty($table_config_array[$field_name]['refereced_table_name'])) {
        $select_data_array = \k1lib\forms\get_labels_from_table($db, $table_config_array[$field_name]['refereced_table_name']);
    }
    $label_object = new html\label($table_config_array[$field_name]['label'], $field_name, "right inline");
//    $select_object = new html\select($field_name);

    if (empty($value) && (!$table_config_array[$field_name]['null'])) {
        $value = $table_config_array[$field_name]['default'];
    }

    if (!empty($error_msg)) {
        $select_html = html\select_list_from_array($field_name, $select_data_array, $value, $table_config_array[$field_name]['null'], "error");
        $html_template = html\load_html_template("label_input_combo-error");
        $html_code = sprintf($html_template, $label_object->generate(), $select_html, $error_msg);
    } else {
        $select_html = html\select_list_from_array($field_name, $select_data_array, $value, $table_config_array[$field_name]['null']);
        $html_template = html\load_html_template("label_input_combo");
        $html_code = sprintf($html_template, $label_object->generate(), $select_html);
    }

    return $html_code;
}

function get_labels_from_table($db, $table_name) {

    \k1lib\sql\db_check_object_type($db, __FUNCTION__);

    if (!is_string($table_name) || empty($table_name)) {
        die(__FUNCTION__ . " \$table_name should be an non empty string");
    }
    $table_config_array = \k1lib\sql\get_db_table_config($db, $table_name);
    $label_field = \k1lib\sql\get_db_table_label_fields($table_config_array);
    $table_keys_array = \k1lib\sql\get_db_table_keys($table_config_array);
    if (!empty($table_keys_array)) {
        $table_config_array = array_flip($table_keys_array);
    }
    if (count($table_keys_array) === 1) {
        $key_filed = key($table_keys_array);
        $labels_sql = "SELECT $key_filed as value, $label_field as label FROM $table_name";
        $labels_data = \k1lib\sql\sql_query($db, $labels_sql);
        if (!empty($labels_data) && (count($labels_data) > 0)) {
            $label_array = array();
            foreach ($labels_data as $row) {
                $label_array[$row['value']] = $row['label'];
            }
            return $label_array;
        } else {
            return FALSE;
        }
    } else {
        return FALSE;
    }
}

// ./src/notifications/classes.php


/**
 * On screen solution for show messages to user.
 *
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package notifications
 */

namespace k1lib\notifications;

use k1lib\html\DOM as DOM;

class common_code {

    /**
     *
     * @var Int 
     */
    static protected $data_count = 0;

    /**
     * Stores the SQL data
     * @var Array
     */
    static protected $data = array();

    /**
     * Stores the SQL data
     * @var Array
     */
    static protected $data_titles = array();

    /**
     * Enable the engenie
     */
    static public function test() {
        if (!class_exists('\k1lib\html\DOM', FALSE)) {
            trigger_error(__CLASS__ . " needs \k1lib\html\DOM class", E_USER_ERROR);
        }
    }

    static public function get_data() {
        self::is_enabled(true);
        return self::$data;
    }

    /**
     * 
     * @param string $section
     * @param string $message
     * @param string $type
     * @param string $tag_id
     */
    static protected function _queue_mesasage($section, $message, $type, $tag_id) {
//        self::$data[$section][$tag_id][$type][] = $message;
        $_SESSION['k1lib_notifications'][$section][$tag_id][$type][] = $message;
        if (empty(self::$data)) {
            self::$data = & $_SESSION['k1lib_notifications'];
        }
    }

    static protected function _queue_mesasage_title($section, $title, $type) {
//        self::$data_titles[$section][$type] = $title;
        $_SESSION['k1lib_notifications_titles'][$section][$type] = $title;
        if (empty(self::$data_titles)) {
            self::$data_titles = & $_SESSION['k1lib_notifications_titles'];
        }
    }

    static public function clean_queue() {
        unset($_SESSION['k1lib_notifications']);
        unset($_SESSION['k1lib_notifications_titles']);
    }

}

class on_DOM extends common_code {

    static protected $section_name = 'on_DOM';

    static function queue_mesasage($message, $type = "primary", $tag_id = 'k1lib-output', $title = NULL) {
        parent::test();
        parent::_queue_mesasage(self::$section_name, $message, $type, $tag_id);
        if (!empty($title)) {
            self::queue_title($title, $type);
        }
    }

    static function queue_title($title, $type = "primary") {
        parent::_queue_mesasage_title(self::$section_name, $title, $type);
    }

    static function insert_messases_on_DOM($order = 'asc') {
        if (isset($_SESSION['k1lib_notifications']) && empty(self::$data)) {
            self::$data = & $_SESSION['k1lib_notifications'];
        }
        if (isset($_SESSION['k1lib_notifications_titles']) && empty(self::$data_titles)) {
            self::$data_titles = & $_SESSION['k1lib_notifications_titles'];
        }

        if (isset(self::$data[self::$section_name]) && !empty(self::$data[self::$section_name])) {
            if ($order == 'asc') {
                self::$data[self::$section_name] = array_reverse(self::$data[self::$section_name]);
            }
            $tag_object = DOM::html()->body()->get_element_by_id("k1lib-output");
            foreach (self::$data[self::$section_name] as $tag_id => $types_messages) {
                if ($tag_object->get_attribute("id") != $tag_id) {
                    $tag_object = DOM::html()->body()->get_element_by_id($tag_id);
                    if (empty($tag_object)) {
                        if (DOM::html()->body()->header()) {
                            $tag_object = DOM::html()->body()->header()->append_div(NULL, $tag_id);
                        } else {
                            $tag_object = DOM::html()->body()->append_child_head(new \k1lib\html\div(NULL, $tag_id));
                        }
                    } // else no needed
                } // else no needed
                foreach ($types_messages as $type => $messages) {
                    $call_out = new \k1lib\html\foundation\callout();
                    $call_out->set_class($type);
                    if (isset(self::$data_titles[self::$section_name][$type]) && !empty(self::$data_titles[self::$section_name][$type])) {
                        $call_out->set_title(self::$data_titles[self::$section_name][$type]);
                    }
                    if (count($messages) === 1) {
                        $call_out->set_message($messages[0]);
                        $call_out->append_to($tag_object);
                    } else {
                        $ul = new \k1lib\html\ul();
                        foreach ($messages as $message) {
                            $ul->append_li($message);
                        }
                        $call_out->set_message($ul);
                        $call_out->append_to($tag_object);
                    }
                }
            }
        }
        unset($_SESSION['k1lib_notifications']);
        unset($_SESSION['k1lib_notifications_titles']);
    }

}

// ./src/options/class_standar_options.php


namespace k1lib\options;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of standar-config-class
 *
 * @author J0hnD03
 */
class standar_options_class {

    private $options = array();
    private $option_name;

    function __construct($option_name) {
        $this->option_name = $option_name;
    }

    public function add_option($option_name, $option_value) {
        $this->options[$option_name] = $option_value;
    }

    public function get_option($option_name) {
        if (isset($this->options[$option_name])) {
            return $this->options[$option_name];
        } else {
            return FALSE;
        }
    }

//put your code here
}


// ./src/session/class_session_plain.php


namespace k1lib\session;

use k1lib\notifications\on_DOM as DOM_notifications;
use \k1lib\crudlexs\class_db_table as class_db_table;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\Exception\UnsatisfiedDependencyException;

class session_plain {

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;

    /**
     * If TRUE on IP change the session will be invalidated
     * @var boolean
     */
    static public $use_ip_in_userhash = TRUE;

    /**
     * Logged state
     * @var Boolean
     */
    static private $has_started;

    /**
     * Keeps the logged state
     * @var Boolean
     */
    static private $is_logged;

    /**
     *
     * @var string 
     */
    static private $user_login = null;

    /**
     *
     * @var string 
     */
    static private $user_hash = null;

    /**
     *
     * @var string 
     */
    static private $user_level = null;

    /**
     * the user levels ['user', 'guest'] are defautls
     * @var array
     */
    static private $app_user_levels = ['user', 'guest'];

    /**
     * Session name for the PHP session handler
     * @var String 
     */
    static private $session_name;

    /**
     * User session data
     * @var Array
     */
    static public $session_data;

    /**
     * URL for default login redirection
     * @var string
     */
    static private $log_form_url;

    /**
     * Enable the engenie
     */
    static public function enable() {
        self::$enabled = TRUE;
        self::$has_started = FALSE;
        self::$is_logged = FALSE;
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("Session system is not enabled yet", E_USER_ERROR);
        }
        return self::$enabled;
    }

    static public function get_session_name() {
        self::is_enabled(true);
        return session_name();
    }

    static public function set_session_name($session_name) {
        self::is_enabled(true);
        self::$session_name = $session_name;
    }

    static public function start_session() {
        self::is_enabled(true);
        \session_name(self::$session_name);
        \session_start();
        self::$has_started = TRUE;
        /**
         * TODO: ENCRYPT THIS !!
         */
        if (!isset($_SESSION['k1lib_session']['user_login'])) {
            $_SESSION['k1lib_session']['user_login'] = NULL;
            $_SESSION['k1lib_session']['user_hash'] = NULL;
            $_SESSION['k1lib_session']['user_level'] = 'guest';
            $_SESSION['k1lib_session']['user_data'] = NULL;
        }
    }

    static public function on_session() {
        if (self::is_enabled() && self::$has_started) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static public function end_session() {
        self::is_enabled(true);
        self::$enabled = TRUE;
        self::$has_started = FALSE;
        self::$is_logged = FALSE;

        self::$user_login = NULL;
        self::$user_hash = NULL;
        self::$user_level = NULL;
        self::$session_data = array();

        session_destroy();
        session_unset();
    }

    static public function start_logged_session($login, array $user_data = [], $user_level = 'guest') {
        if (self::is_enabled(true)) {

            $_SESSION['k1lib_session']['user_login'] = $login;
            $_SESSION['k1lib_session']['user_hash'] = self::get_user_hash($login);
            $_SESSION['k1lib_session']['user_level'] = $user_level;
            $_SESSION['k1lib_session']['user_data'] = $user_data;

            return self::load_logged_session();
        } else {
            return FALSE;
        }
    }

    static public function get_user_data() {
        if (self::is_enabled(true)) {
            if (isset($_SESSION['k1lib_session']['user_data'])) {
                return $_SESSION['k1lib_session']['user_data'];
            } else {
                return [];
            }
        }
    }

    static public function is_logged($redirect = FALSE, $where_redirect_to = "") {
        if ((self::is_enabled(true)) && (self::$has_started) && (isset(self::$user_hash))) {
            if (self::$user_hash == self::get_user_hash(self::$user_login)) {
                return TRUE;
            } else {
                self::end_session();
            }
        }
        if (!$redirect) {
            return FALSE;
        } else {
            ob_clean();
            if (empty($where_redirect_to) && !empty(self::$log_form_url)) {
                \k1lib\html\html_header_go(self::$log_form_url);
            } else {
                \k1lib\html\html_header_go($where_redirect_to);
            }
            exit;
        }
    }

    static function set_log_form_url($log_form_url) {
        self::$log_form_url = $log_form_url;
    }

    static public function load_logged_session($redirect = FALSE, $where_redirect_to = "") {
        if ((self::is_enabled(true)) && (self::$has_started)) {
            if ($_SESSION['k1lib_session']['user_hash'] === self::get_user_hash($_SESSION['k1lib_session']['user_login'])) {

                self::$user_login = $_SESSION['k1lib_session']['user_login'];
                self::$user_hash = $_SESSION['k1lib_session']['user_hash'];
                self::$user_level = $_SESSION['k1lib_session']['user_level'];

                self::$session_data = $_SESSION['k1lib_session'];

                return TRUE;
            } else {
                self::$user_login = -1;
                self::$user_level = 'guest';
//                self::$session_data['user_data'] = null;
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    static public function get_user_hash($user_login = null) {
        if (empty($user_login)) {
            $user_login = self::$user_login;
        }
        if (self::$use_ip_in_userhash) {
            return md5($user_login . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . \k1lib\K1MAGIC::get_value());
        } else {
            return md5($user_login . $_SERVER['HTTP_USER_AGENT'] . \k1lib\K1MAGIC::get_value());
        }
    }

    public static function get_use_ip_in_userhash() {
        return self::$use_ip_in_userhash;
    }

    public static function set_use_ip_in_userhash($use_ip_in_userhash) {
        if ($use_ip_in_userhash) {
            self::$use_ip_in_userhash = TRUE;
        } else {
            self::$use_ip_in_userhash = FALSE;
        }
    }

    static function get_user_login() {
        return self::$user_login;
    }

    static function get_user_level() {
        return self::$user_level;
    }

    /**
     * Check if the current user level is present on a array of possible levels
     * @param array Levels to check the current user, use ['leve1','level2','levelN'] to fastest code ;)
     * @return boolean
     */
    static public function check_user_level(array $levels_to_check) {
        self::is_enabled(true);
        $has_access = FALSE;
        foreach ($levels_to_check as $level) {
            if (self::$user_level === $level) {
                return TRUE;
            }
        }
        return $has_access;
    }

    static public function clear_app_user_levels() {
        self::$app_user_levels = [];
    }

    static function get_app_user_levels() {
        return self::$app_user_levels;
    }

    static function set_app_user_levels(array $app_user_levels) {
        self::$app_user_levels = $app_user_levels;
    }

    static public function add_app_user_levels($level_name) {
        self::$app_user_levels[] = $level_name;
    }

    static public function remove_app_user_levels($level_name) {
        if (key_exists($level_name, array_flip(self::$app_user_levels))) {
            unset(self::$app_user_level[array_flip(self::$app_user_levels)[$level_name]]);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Terminal info and unique finger_print
     */

    /**
     * Get a unique ID as CUID
     * @return string
     */
    public static function get_cuid() {
        return \EndyJasmi\Cuid::make();
    }

    /**
     * Get an array with browser parsed info
     * @param string $user_agent
     * @return array
     */
    public static function get_terminal_info_array($user_agent = NULL) {
        if (empty($user_agent)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        $result = new \WhichBrowser\Parser($user_agent);

        $terminar_array = [
            'browser_name' => $result->browser->getName(),
            'browser_version' => $result->browser->getVersion(),
            'os_name' => $result->os->getName(),
            'os_version' => $result->os->getName(),
            'device_type' => $result->device->type,
            'device_manufacturer' => $result->device->getManufacturer(),
            'device_model' => $result->device->getModel(),
        ];

        return $terminar_array;
    }

    /**
     * Get a browser fingerprint
     * @return string
     */
    public static function get_browser_fp($return_array = FALSE, $return_all = FALSE) {
        $headers = getallheaders();
        unset($headers['Cookie']);
        unset($headers['Cache-Control']);
        ksort($headers);

        if ($return_array) {
            return $headers;
        } else {
            $fp = md5(implode('-', $headers)) . \k1lib\MAGIC_VALUE;
            return $fp;
        }
    }

}

class session_db extends session_plain {

    /**
     * @var \k1lib\crudlexs\class_db_table
     */
    private $db_table;

    /**
     * @var \PDO
     */
    private $db_object;

    /**
     * @var array
     */
    protected $user_data = [];

    /**
     * @var string
     */
    protected $user_login_db_table = NULL;

    /**
     * @var string
     */
    protected $user_login_field = NULL;

    /**
     * @var string
     */
    protected $user_login_input_name = NULL;

    /**
     * @var string
     */
    protected $user_login_input_value = NULL;

    /**
     * @var string
     */
    protected $user_password_field = NULL;

    /**
     * @var string
     */
    protected $user_password_input_name = NULL;

    /**
     * @var string
     */
    protected $user_password_input_value = NULL;

    /**
     * @var boolean
     */
    static $user_password_use_md5 = TRUE;

    /**
     * @var string
     */
    protected $user_level_field = NULL;

    /**
     * @var string
     */
    protected $user_remember_me_input = NULL;

    /**
     * @var string
     */
    protected $user_remember_me_value = FALSE;

    /**
     * @var string
     */
    protected $save_cookie_name = NULL;

    /**
     * @var string
     */
    protected $coockie_data = NULL;

    public function __construct(\PDO $db) {
        $this->db_object = $db;
        $this->save_cookie_name = session_plain::get_session_name() . "-store";
    }

    public function set_config($login_db_table, $user_login_field, $user_password_field, $user_level_field = NULL) {
        $this->user_login_db_table = $login_db_table;
        $this->db_table = new \k1lib\crudlexs\class_db_table($this->db_object, $this->user_login_db_table);
        if ($this->db_table->get_state()) {
            $this->user_login_field = $user_login_field;
            $this->user_password_field = $user_password_field;
            $this->user_level_field = $user_level_field;
        }
    }

    public function set_inputs($user_login_input_name, $user_password_input_name, $remember_me_input = NULL) {
        $this->user_login_input_name = $user_login_input_name;
        $this->user_password_input_name = $user_password_input_name;
        $this->user_remember_me_input = $remember_me_input;
    }

    public function catch_post($skip_magic = FALSE) {
        if (isset($_POST['magic_value'])) {
            $magic_test = \k1lib\common\check_magic_value("login_form", $_POST['magic_value']);
            if (($magic_test == TRUE) || ($skip_magic)) {
                unset($_POST['magic_value']);
// the form was correct, so lets try to login

                /**
                 * Check the _GET incomming vars
                 */
                $form_values = \k1lib\forms\check_all_incomming_vars($_POST, "k1lib_login");

                /**
                 * Login fields
                 */
                if (isset($form_values[$this->user_login_input_name]) && isset($form_values[$this->user_password_input_name])) {

                    $this->user_login_input_value = $form_values[$this->user_login_input_name];
                    $this->user_password_input_value = (self::$user_password_use_md5) ? md5($form_values[$this->user_password_input_name]) : $form_values[$this->user_password_input_name];

                    if (isset($form_values[$this->user_remember_me_input])) {
                        $this->user_remember_me_value = $form_values[$this->user_remember_me_input];
                    }
                } else {
                    return NULL;
                }

//                $filter_array = [
//                    $this->user_login_input_name => $this->user_login_input_value,
//                    $this->user_password_input_name => $this->user_password_input_value,
//                ];
//                $this->db_table->set_query_filter($filter_array, TRUE);
                return $form_values;
            } else {
                return FALSE;
            }
        } else {
            DOM_notifications::queue_mesasage("There is not magic present here!", "alert");
            return NULL;
        }
    }

    public function get_remember_me_value() {
        return $this->user_remember_me_value;
    }

    public function set_user_remember_me_value($user_remember_me_value) {
        $this->user_remember_me_value = $user_remember_me_value;
    }

    public function check_login() {
//        /**
//         * SQL check
//         */
        $fielter_array = [
            $this->user_login_field => $this->user_login_input_value,
            $this->user_password_field => $this->user_password_input_value,
        ];
        $this->db_table->set_query_filter($fielter_array, TRUE, FALSE);
        $this->user_data = $this->db_table->get_data(FALSE);
        return $this->user_data;
    }

    public function save_data_to_coockie($path = "/") {
        $data = [
            'db_table_name' => $this->db_table->get_db_table_name(),
            'user_login_field' => $this->user_login_field,
            'user_login_input_value' => $this->user_login_input_value,
            'user_login_input_name' => $this->user_login_input_name,
            'user_password_field' => $this->user_password_field,
            'user_password_input_value' => $this->user_password_input_value,
            'user_password_input_name' => $this->user_password_input_name,
            'user_remember_me_input' => $this->user_remember_me_input,
            'user_level_field' => $this->user_level_field,
            'user_hash' => parent::get_user_hash($this->user_login_input_value),
        ];
        $data_encoded = \k1lib\crypt::encrypt($data);
        if ($this->user_remember_me_value) {
            $coockie_time = time() + (15 * 60 * 60 * 24);
        } else {
            $coockie_time = 0;
        }
        $this->coockie_data = $data_encoded;
        $coockie = setcookie($this->save_cookie_name, $data_encoded, $coockie_time, $path);
    }

    public function load_data_from_coockie($return_coockie_data = FALSE) {
        if (!empty($this->coockie_data)) {
            $_COOKIE[$this->save_cookie_name] = $this->coockie_data;
        }
        if (isset($_COOKIE[$this->save_cookie_name])) {
            $data = \k1lib\crypt::decrypt($_COOKIE[$this->save_cookie_name]);

            if ($return_coockie_data) {
                return $data;
            } else {

                if ($data['user_hash'] === self::get_user_hash($data['user_login_input_value'])) {
                    $this->set_config($data['db_table_name'], $data['user_login_field'], $data['user_password_field'], $data['user_level_field']);
                    $this->set_inputs($data['user_login_input_name'], $data['user_password_input_name'], $data['user_remember_me_input']);
                    $this->user_login_input_value = $data['user_login_input_value'];
                    $this->user_password_input_value = $data['user_password_input_value'];
                    $this->user_remember_me_value = $data['user_remember_me_value'];

                    $user_data = $this->check_login();
                    if ($user_data) {
                        $this->user_data = $user_data;
                        $this->start_logged_session($user_data[$this->user_login_field], $user_data, $user_data[$this->user_level_field]);
                        return $user_data;
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        }
    }

    public function unset_coockie($path = "/") {
        $this->save_cookie_name = session_plain::get_session_name() . "-store";
        if (isset($_COOKIE[$this->save_cookie_name])) {
            unset($_COOKIE[$this->save_cookie_name]);
        }
        setcookie($this->save_cookie_name, '', time() - (60 * 60 * 24), $path);
    }

    public function load_logged_session_db($redirect = FALSE, $where_redirect_to = "") {
        $this->save_cookie_name = session_plain::get_session_name() . "-store";

        if (!parent::load_logged_session($redirect, $where_redirect_to)) {
            $cookie_data = $this->load_data_from_coockie();
            return $cookie_data;
        } else {
            if (isset($_COOKIE[$this->save_cookie_name])) {
                $data = \k1lib\crypt::decrypt($_COOKIE[$this->save_cookie_name]);
                if ($data['user_hash'] === self::get_user_hash($data['user_login_input_value'])) {
                    $this->set_config($data['db_table_name'], $data['user_login_field'], $data['user_password_field'], $data['user_level_field']);
                    $this->set_inputs($data['user_login_input_name'], $data['user_password_input_name'], $data['user_remember_me_input']);
                    $this->user_login_input_value = $data['user_login_input_value'];
                    $this->user_password_input_value = $data['user_password_input_value'];
                    $this->user_remember_me_value = $data['user_remember_me_value'];
                    $user_data = $this->check_login();
                    if ($user_data) {
                        $_SESSION['k1lib_session']['user_data'] = $user_data;
                        $this->user_data = $user_data;
                    }
                }
            }
            return TRUE;
        }
    }

    static function get_user_password_use_md5() {
        return self::$user_password_use_md5;
    }

    static function set_user_password_use_md5($user_password_use_md5) {
        self::$user_password_use_md5 = $user_password_use_md5;
    }

    public function get_user_login_db_table() {
        return $this->user_login_db_table;
    }

    public function get_user_login_field() {
        return $this->user_login_field;
    }

    public function get_user_login_input_name() {
        return $this->user_login_input_name;
    }

    public function get_user_password_field() {
        return $this->user_password_field;
    }

    public function get_user_password_input_name() {
        return $this->user_password_input_name;
    }

    public function get_user_level_field() {
        return $this->user_level_field;
    }

    public function set_user_login_db_table($user_login_db_table) {
        $this->user_login_db_table = $user_login_db_table;
    }

    public function set_user_login_field($user_login_field) {
        $this->user_login_field = $user_login_field;
    }

    public function set_user_login_input_name($user_login_input_name) {
        $this->user_login_input_name = $user_login_input_name;
    }

    public function set_user_password_field($user_password_field) {
        $this->user_password_field = $user_password_field;
    }

    public function set_user_password_input_name($user_password_input_name) {
        $this->user_password_input_name = $user_password_input_name;
    }

    public function set_user_level_field($user_level_field) {
        $this->user_level_field = $user_level_field;
    }

    public function get_user_remember_me_input() {
        return $this->user_remember_me_input;
    }

    public function set_user_remember_me_input($user_remember_me_input) {
        $this->user_remember_me_input = $user_remember_me_input;
    }

    public static function end_session($path = '/') {
//        $this->save_cookie_name = session_plain::get_session_name() . "-store";
        $save_cookie_name = session_plain::get_session_name() . "-store";
        setcookie($save_cookie_name, '', time() - (60 * 60 * 24), $path);
//        if (isset($save_cookie_name)) {
//            unset($save_cookie_name);
//        }
        parent::end_session();
    }

}

class session_browser_fp extends session_db {

    /**
     * @var string
     */
    private static $terminals_table_name = '';

    /**
     * @var string
     */
    private static $mobile_numbers_table_name = '';

    /**
     * @var string
     */
    private static $terminals_unique_table_name = '';

    /**
     * @var string 
     */
    private static $session_terminal_coockie_name;

    /**
     * @var \k1lib\crudlexs\class_db_table
     */
    private static $terminals_table;

    /**
     * @var \k1lib\crudlexs\class_db_table
     */
    private static $mobile_nombers_table;

    /**
     * @var \k1lib\crudlexs\class_db_table
     */
    private static $terminals_unique_table;

    /**
     * @var string
     */
    private static $current_terminal_uuid = NULL;

    /**
     * @var string
     */
    private static $current_browser_fp = NULL;

    /**
     *
     * @var Array
     */
    private static $current_browser_fp_data = [];

    /**
     * 
     * @param string $terminals_table_name
     * @param string $mobile_numbers_table_name
     * @param string $terminals_unique_table_name
     */
    public static function config($terminals_table_name, $mobile_numbers_table_name, $terminals_unique_table_name) {
        self::$terminals_unique_table_name = $terminals_unique_table_name;
        self::$terminals_table_name = $terminals_table_name;
        self::$mobile_numbers_table_name = $mobile_numbers_table_name;
    }

    function __construct(\PDO $db) {
        // Parent assigns the db object to $db_object
        parent::__construct($db);

        /**
         * OPEN FP SYSTEM TABLES
         */
        if (!empty(self::$terminals_table_name)) {
            self::$terminals_table = new class_db_table($db, self::$terminals_table_name);
            if (!self::$terminals_table->get_state()) {
                trigger_error('Terminals Table "' . self::$terminals_table_name . '" not found', E_USER_ERROR);
            } else {
//                d(self::$terminals_table->get_db_table_config());
            }
        } else {
            trigger_error('Terminals Table "' . self::$terminals_table_name . '" not found', E_USER_ERROR);
        }

        if (!empty(self::$mobile_numbers_table_name)) {
            self::$mobile_nombers_table = new class_db_table($db, self::$mobile_numbers_table_name);
            if (!self::$mobile_nombers_table->get_state()) {
                trigger_error('Mobile numbers Table "' . self::$mobile_numbers_table_name . '" not found', E_USER_ERROR);
            } else {
//                d(self::$mobile_nombers_table->get_db_table_config());
            }
        } else {
            trigger_error('Mobile numbers Table "' . self::$mobile_numbers_table_name . '" not found', E_USER_ERROR);
        }
        if (!empty(self::$terminals_unique_table_name)) {
            self::$terminals_unique_table = new class_db_table($db, self::$terminals_unique_table_name);
            if (!self::$terminals_unique_table->get_state()) {
                trigger_error('Unique Terminal-Numbers Table "' . self::$terminals_unique_table_name . '" not found', E_USER_ERROR);
            } else {
//                d(self::$terminals_unique_table->get_db_table_config());
            }
        } else {
            trigger_error('Unique Terminal-Numbers Table "' . self::$terminals_unique_table_name . '" not found', E_USER_ERROR);
        }
    }

    public static function start_session() {
        parent::start_session();
        $terminal_data = FALSE;
        self::$session_terminal_coockie_name = self::get_session_name() . '-bfp-' . md5(self::get_browser_fp());
        /**
         * INIT DATA ON TABLES
         */
        $actual_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        /**
         * FP Cookie SET or READ
         */
        // SET COOKIE
        if (!isset($_GET['bfp'])) {
            if (empty($_COOKIE[self::$session_terminal_coockie_name])) {
                // GET UUID
                $uuid4 = Uuid::uuid4();
                // COOKIE will have value as: uuid,browser_fp
                $cookie_to_set_value = \k1lib\crypt::encrypt($uuid4->toString() . ',' . self::get_browser_fp());
                // Set the COOKIE 1 year from now
                setcookie(self::$session_terminal_coockie_name, $cookie_to_set_value, strtotime('+365 days'), '/');
                // Redirects the browser to the ACTUAL URL with $_GET['bfp']=md5(browser_fp) to test the cookie is really set.
                \k1lib\html\html_header_go(\k1lib\urlrewrite\url::do_url($actual_url, ['bfp' => md5(self::get_browser_fp()), 'last_url' => $actual_url]));
            } else {
                $cookie_value = \k1lib\crypt::decrypt($_COOKIE[self::$session_terminal_coockie_name]);
                if (strstr($cookie_value, ',') !== FALSE) {
                    // Retrive COOKIE data as : $current_terminal_uuid,$current_browser_fp
                    $cookie_data = explode(',', $cookie_value);
                    self::$current_terminal_uuid = $cookie_data[0];
                    self::$current_browser_fp = $cookie_data[1];
                    //check Browser FP integrity
                    if (self::$current_browser_fp == self::get_browser_fp()) {
                        // Let's check if the current UUID exist as terminal on table
                        self::$terminals_table->set_query_filter(['terminal_uuid' => self::$current_terminal_uuid], TRUE);
                        $db_terminal_data = self::$terminals_table->get_data();
                        if ($db_terminal_data) {
                            $terminal_data = TRUE;
                        } else {
                            $terminad_data_array = array_merge(
                                    ['terminal_uuid' => self::$current_terminal_uuid, 'browser_fp' => self::$current_browser_fp]
                                    , self::get_terminal_info_array());
                            $errors = [];
                            if (self::$terminals_table->insert_data($terminad_data_array, $errors)) {
//                            d($errors, true);
                                $terminal_data = TRUE;
                                DOM_notifications::queue_mesasage('Terminal has been created. UUID: ' . self::$current_terminal_uuid, "success");
                            } else {
                                DOM_notifications::queue_mesasage('Terminal data couldn\'t be saved.', "alert");
                            }
                        }
                    } else {
                        trigger_error('Data from COOKIE seems to be from another browser/terminal. Good try.', E_USER_ERROR);
                        exit;
                    }
                } else {
                    setcookie(self::$session_terminal_coockie_name, $cookie_value, strtotime('-365 days'), '/');
                    trigger_error('Your session cookie is rotten and we had to delete it, please, don\'t try to hack us, we make our best to do not let you.', E_USER_ERROR);
                    exit;
                }
            }
        } else {
            /**
             * When $_GET['bfp'] isset means that we need to run a COOKIE test
             */
            if ($_GET['bfp'] != md5(self::get_browser_fp())) {
                trigger_error('Very bad BFP value, so, I dont want to keep going. ' . self::get_browser_fp(), E_USER_ERROR);
                exit;
            } else {
                if (empty($_COOKIE[self::$session_terminal_coockie_name])) {
                    trigger_error('Browser do not accept cookies and is not possible to keep going. Please enable them.', E_USER_ERROR);
                    exit;
                } else {
                    $get_vars = \k1lib\forms\check_all_incomming_vars($_GET);
                    \k1lib\html\html_header_go($get_vars['last_url']);
                }
            }
        }
    }

    public static function end_session($path = '/') {
        setcookie(self::$session_terminal_coockie_name, $cookie_value, strtotime('-365 days'), '/');
        parent::end_session($path);
    }

}

// ./src/sql/classes.php


namespace k1lib\sql;

class common_code {

    /**
     * Enable state
     * @var Boolean 
     */
    static protected $enabled = FALSE;

    /**
     *
     * @var Int 
     */
    static protected $data_count = 0;

    /**
     * Stores the SQL data
     * @var Array
     */
    static protected $data = array();

    /**
     * Enable the engenie
     */
    static public function enable() {
        self::$enabled = TRUE;
    }

    /**
     * Disable the engenie
     */
    static public function disable() {
        self::$enabled = FALSE;
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("SQL Profile system is not enabled yet", E_USER_WARNING);
        }
        return self::$enabled;
    }

    static public function get_data() {
        self::is_enabled(true);
        return self::$data;
    }

    public static function get_data_count(): int {
        return self::$data_count;
    }

}

class profiler extends common_code {
//    use common_code;

    /**
     * Begin a SQL Profile with a SQL query code 
     * @param String $sql_query
     * @return Int Profile ID
     */
    static public function add($sql_query) {
        self::is_enabled(true);
        $sql_md5 = md5($sql_query);
        self::$data_count++;
        self::$data[self::$data_count]['md5'] = $sql_md5;
        self::$data[self::$data_count]['sql'] = $sql_query;
        return self::$data_count;
    }

    /**
     * Begin the time count
     * @param Int $sql_md5 Profile ID
     */
    static public function start_time_count($sql_md5) {
        self::is_enabled(true);
        self::$data[$sql_md5]['start_time'] = microtime(TRUE);
    }

    /**
     * Stop the time count
     * @param Int $sql_md5 Profile ID
     */
    static public function stop_time_count($sql_md5) {
        self::is_enabled(true);
        self::$data[$sql_md5]['stop_time'] = microtime(TRUE);
        self::$data[$sql_md5]['total_time'] = self::$data[self::$data_count]['stop_time'] - self::$data[self::$data_count]['start_time'];
    }

    /**
     * Keep record of cache use of the current query
     * @param Int $sql_md5 Profile ID
     * @param Boolean $is_cached 
     */
    static public function set_is_cached($sql_md5, $is_cached) {
        self::is_enabled(true);
        if (self::is_enabled()) {
            self::$data[$sql_md5]['cache'] = $is_cached;
        }
    }

    /**
     * Filter the data by MD5
     * @param String $md5
     * @return Array
     */
    static public function get_by_md5($md5) {
        self::is_enabled(true);
        $data_filtered = array();
        foreach (self::$data as $id => $profile_data) {
            if (isset($profile_data['md5']) && ($profile_data['md5'] == $md5)) {
                $data_filtered[] = $profile_data;
            }
        }
        return $data_filtered;
    }

    /**
     * Return the total execution time
     * @return float
     */
    static public function get_total_time() {
        $total_time = 0;
        foreach (self::$data as $profile_data) {
            $total_time += $profile_data['total_time'];
        }
        return $total_time;
    }

}

class local_cache extends common_code {

//    use common_code;
    static protected bool $use_memcached = false;

    /**
     * 
     * @var \Memcached
     */
    static object $memcached;
    static protected string $memcached_server = '127.0.0.1';
    static protected int $memcached_port = 11211;
    static protected int $memcached_ttl = 300;
    static protected array $exclude_sql_terms = ['INFORMATION_SCHEMA', 'SHOW FULL COLUMNS', 'UPDATE', 'INSERT', 'DELETE'];

    static protected function connect_memcached() {
        self::$memcached = new \Memcached();
        self::$memcached->addServer(self::$memcached_server, self::$memcached_port);
    }

    /**
     * Put a SQL_RESULT on the LOCAL CACHE
     * @param type $sql_query
     * @param type $sql_result
     */
    static public function add($sql_query, $sql_result) {
        if (self::$use_memcached) {
            if (!self::check_exlusion($sql_query)) {
                return self::$memcached->set(md5($sql_query), $sql_result, self::$memcached_ttl);
            } else {
                return FALSE;
            }
        } else {
            self::is_enabled(true);
            self::$data[md5($sql_query)] = $sql_result;
        }
        self::$data_count++;
    }

    /**
     * Return if the SQL QUERY is on cache or not
     * @param String $sql_query
     * @return Boolean
     */
    static public function is_cached($sql_query) {
        self::is_enabled(true);
        return isset(self::$data[md5($sql_query)]);
    }

    /**
     * Returns a previusly STORED SQL RESULT by SQL QUERY if exist
     * @param String $sql_query
     * @return Array returns FALSE if not exist
     */
    static public function get_result($sql_query) {
        self::is_enabled(true);
        if (self::$use_memcached) {
            if (!self::check_exlusion($sql_query)) {
                $return = self::$memcached->get(md5($sql_query));
                return $return;
            } else {
                return FALSE;
            }
        } else {
            if (isset(self::$data[md5($sql_query)])) {
                return (self::$data[md5($sql_query)]);
            } else {
                return FALSE;
            }
        }
    }

    static protected function check_exlusion($sql_query) {
        foreach (self::$exclude_sql_terms as $term_to_exclude) {
            if (strstr(strtolower($sql_query), strtolower($term_to_exclude)) !== FALSE) {
                return true;
            }
        }
        return false;
    }

    static public function get_exclude_sql_terms(): string {
        return self::$exclude_sql_terms;
    }

    static public function set_exclude_sql_terms(string $exclude_sql_terms): void {
        self::$exclude_sql_terms = $exclude_sql_terms;
    }

    static public function use_memcached() {
        self::$use_memcached = true;
        self::connect_memcached();
    }

    static public function use_localcache() {
        self::$use_memcached = false;
//        self::connect_memcached();
    }

    static public function set_mode($mode): void {
        self::$mode = $mode;
    }

    static public function get_memcached_server() {
        return self::$memcached_server;
    }

    static public function get_memcached_port() {
        return self::$memcached_port;
    }

    static public function get_memcached_ttl() {
        return self::$memcached_ttl;
    }

    static public function set_memcached_server($memcached_server): void {
        self::$memcached_server = $memcached_server;
    }

    static public function set_memcached_port($memcached_port): void {
        self::$memcached_port = $memcached_port;
    }

    static public function set_memcached_ttl($memcached_ttl): void {
        self::$memcached_ttl = $memcached_ttl;
    }

}

// ./src/sql/functions.php


namespace k1lib\sql;

use k1lib\sql\profiler;
use k1lib\sql\local_cache;

/*
 * Autor: Alejandro Trujillo J.
 * Copyright: Klan1 Network - 2010
 *
 * TODO: Make sql functions recognition into the sql builders
 *
 */

/**
 * Get from a DB Table the config matrix for the K1 Function and Objects related
 * @param PDO $db
 * @param array $table
 * @return array
 */
function get_db_table_config(\PDO $db, $table, $recursion = TRUE, $use_cache = TRUE) {

// SQL to get info about a table
    $columns_info_query = "SHOW FULL COLUMNS FROM {$table}";
    $columns_info_result = sql_query($db, $columns_info_query, TRUE, FALSE, $use_cache);
    if (empty($columns_info_result)) {
        trigger_error("The table '$table' do not exist", E_USER_NOTICE);
        return FALSE;
    }
    $dsn_db = get_db_database_name($db);
    $INFORMATION_SCHEMA_query = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '{$dsn_db}'
AND table_name = '{$table}'";
    $INFORMATION_SCHEMA_result = sql_query($db, $INFORMATION_SCHEMA_query, TRUE);
    if (empty($INFORMATION_SCHEMA_result)) {
//        trigger_error("The table '$table' do not exist", E_USER_WARNING);
//        return FALSE;
    }

    $config_array = array();
// run through the result and covert into an array that works for K1.lib
    foreach ($columns_info_result as $field_row) {
        $field_name = $field_row['Field'];
//        unset($field_config['Field']);
        /*
         * SEE EACH VALUE OF $field_config to build a new $key => $value and compile the COMMENT field from the table
         */
        $field_config = $field_row;
        foreach ($field_config as $key => $value) {
// LOWER the $key and $value to AVOID problems
            $key_original = $key;
            $key = strtolower($key);
            $value = strtolower($value);
// create a new pair of data but lowered
            $field_config[$key] = $value;
            /*
             * COMPILE THE COMMENT VALUES TO GET A NEW PAIR OF DATA ON $field_config
             * EACH PARAMETER IS SEPARATED WITH ,
             * PARAMETER AND VALUE SEPARATED :
             * parameter1:value1,...,parameterN:valueN
             */
            if ($key == "comment") {
                if (!empty($value) && (strstr($value, ":") !== FALSE)) {
                    $parameters = explode(",", $field_config['Comment']);
                    if (count($parameters) != 0) {
                        foreach ($parameters as $parameter_value) {
                            list($attrib, $attrib_value) = explode(":", $parameter_value);
                            $field_config[$attrib] = trim($attrib_value);
                            $key = trim($attrib);
                        }
                    }
                }
            }
//then delete the original pair of data
            unset($field_config[$key_original]);
        } // $field_config
        /*
         * DEFAULTS TAKE CARE !!
         */
// MAX - THIS ONE IS ALWAYS AUTO GENERATED FROM FIELD DEFINITION
        $field_type = $field_config['type'];
// manage the unsigned
        if (strstr($field_type, "unsigned") !== FALSE) {
            $field_type = str_replace(" unsigned", "", $field_type);
            $field_config['unsigned'] = TRUE;
        } else {
            $field_config['unsigned'] = FALSE;
        }
        if (strstr($field_type, "(") !== FALSE) {
// extract the number from type definition
            list($field_type, $max_legth) = explode("(", $field_type);
            if (!empty($max_legth)) {
                $max_legth = substr($max_legth, 0, -1);
                if (strstr($max_legth, ",") !== FALSE) {
                    list($number, $decimal) = explode(",", $max_legth);
                    $max_legth = (int) $number + (int) $decimal;
                }
            }
        } else {
            $mysql_max_length_defaults = array(
                'char' => 255,
                'varchar' => 255,
                'text' => (10 * 1204 * 1204), // 10485760 bytes or 110 Megabytes
                'date' => 10,
                'time' => 8,
                'datetime' => 19,
                'timestamp' => 19,
                'tinyint' => 3,
                'smallint' => 5,
                'mediumint' => 7,
                'int' => 10,
                'bigint' => 19,
                'float' => 34,
                'double' => 64,
                'enum' => NULL,
            );
            $max_legth = $mysql_max_length_defaults[$field_type];
        }
        $field_config['type'] = $field_type;
        $field_config['max'] = $max_legth;

// TYPE VALIDATION
        $mysql_default_validation = array(
            'char' => 'mixed-symbols',
            'varchar' => 'mixed-symbols',
            'text' => 'mixed-symbols',
            'date' => 'date',
            'time' => 'time',
            'datetime' => 'datetime',
            'timestamp' => 'numbers',
            'tinyint' => 'numbers',
            'smallint' => 'numbers',
            'mediumint' => 'numbers',
            'int' => 'numbers',
            'bigint' => 'numbers',
            'decimal' => 'decimals',
            'float' => 'decimals',
            'double' => 'decimals',
            'enum' => 'options',
            'set' => 'options',
        );
        if (!isset($field_config['validation'])) {
            $field_config['validation'] = $mysql_default_validation[$field_config['type']];
        }


//ROW attrib_value FIXES
// yes -> TRUE no -> FALSE
        foreach ($field_config as $key => $value) {
//            d("$key -> $value");
            if ($field_config[$key] == "yes") {
                $field_config[$key] = TRUE;
            } elseif ($field_config[$key] == "no") {
                $field_config[$key] = FALSE;
            }
        }
        // IF no label so capitalize the FIELD NAME
        if (!isset($field_config['label'])) {
            $field_config['label'] = get_field_label_default($table, $field_name);
        }
        // ENUM FIX
        if (!isset($field_config['min'])) {
            $field_config['min'] = 0;
            /**
             * TODO: Make option system for this
             */
//            $field_config['min'] = defined(DB_MIN_FIELD_LENGTH) ? DB_MIN_FIELD_LENGTH : FALSE;
        }
        if ($field_config['type'] == "enum") {
            $field_config['min'] = 1;
            $field_config['max'] = 999;
        }
// NEW 2016: REQUIRED-FIELD
        if (!isset($field_config['required'])) {
            if ($field_config['null'] === TRUE) {
                $field_config['required'] = FALSE;
            } else {
                $field_config['required'] = TRUE;
            }
        }
// LABEL-FIELD
        if (!isset($field_config['label-field'])) {
            $field_config['label-field'] = FALSE;
        }
// NEW 2016: ALIAS-FIELD
        if (!isset($field_config['alias'])) {
            $field_config['alias'] = NULL;
        }
// NEW 2016: PLACEHOLDER
        if (!isset($field_config['placeholder'])) {
            $field_config['placeholder'] = NULL;
        }
// NEW 2016: FILE TYPE
        if (!isset($field_config['file-type'])) {
            $field_config['file-type'] = NULL;
        }
// NEW 2016: FILE UPLOAD MAX SIZE
        if (!isset($field_config['file-max-size'])) {
            $field_config['file-max-size'] = NULL;
        } else {
            $field_config['file-max-size'] = strtolower($field_config['file-max-size']);
            $size_unit = substr($field_config['file-max-size'], -1);
            $size_number = substr($field_config['file-max-size'], 0, -1);
            if (!is_numeric($size_unit)) {
                $file_size = NULL;
                if ($size_unit == 'k') {
                    $file_size = $size_number * 1024;
                }
                if ($size_unit == 'm') {
                    $file_size = $size_number * 1024 * 1024;
                }
                if ($size_unit == 'g') {
                    $file_size = $size_number * 1024 * 1024 * 1024;
                }
                $field_config['file-max-size'] = $file_size;
            }
            if (empty($field_config['file-type'])) {
                $field_config['file-type'] = "image";
            }
        }
// show board
        /**
         * Show rules
         */
        $show_array_attribs[] = 'show-create';
        $show_array_attribs[] = 'show-read';
        $show_array_attribs[] = 'show-update';
        $show_array_attribs[] = 'show-list';
        // 2016 with NEW RULES !!
        $show_array_attribs[] = 'show-export';
        foreach ($show_array_attribs as $show_attrib) {
            if (!isset($field_config[$show_attrib])) {
                $field_config[$show_attrib] = TRUE;
            }
        }
        // NEW 2016: show-search - Default as the table option
        if (!isset($field_config['show-search'])) {
            $field_config['show-search'] = (isset($field_config['show-list'])) ? $field_config['show-list'] : FALSE;
        }
        // NEW 2016: show-related - Default as the table option
        if (!isset($field_config['show-related'])) {
            $field_config['show-related'] = (isset($field_config['show-list'])) ? $field_config['show-list'] : FALSE;
        }
//table name for each one, yes! repetitive, but necesary in some cases where i dnot receive the table name !!
        $field_config['table'] = $table;
// SQL for selects
        $field_config['sql'] = "";
// FOREIGN KEYS
        if (!empty($INFORMATION_SCHEMA_result)) {
            foreach ($INFORMATION_SCHEMA_result as $info_row) {
                if (!empty($info_row['POSITION_IN_UNIQUE_CONSTRAINT']) && ($info_row['COLUMN_NAME'] == $field_name)) {
                    $field_config['refereced_table_name'] = $info_row['REFERENCED_TABLE_NAME'];
                    $field_config['refereced_column_name'] = $info_row['REFERENCED_COLUMN_NAME'];
                    if ($recursion) {
                        // RECURSION FIX 
                        $referenced_table_config = get_db_table_config($db, $info_row['REFERENCED_TABLE_NAME'], ($info_row['REFERENCED_TABLE_NAME'] != $table ? $recursion : FALSE));
                        $field_config['refereced_column_config'] = $referenced_table_config[$info_row['REFERENCED_COLUMN_NAME']];
                    } else {
                        $field_config['refereced_column_config'] = FALSE;
                    }
                    break;
                } else {
                    $field_config['refereced_table_name'] = FALSE;
                    $field_config['refereced_column_name'] = FALSE;
                    $field_config['refereced_column_config'] = FALSE;
                }
            }
        }

// use the actual cycle data
        $config_array[$field_name] = $field_config;
    }
    return $config_array;
}

function get_field_label_default($table, $field_name) {
    // Try to remove the table name from the field name. Commonly use on DB design
    if (strtolower(substr($table, -3)) === "ies") {
        $possible_singular_table_name = str_replace("ies", "y", $table);
    } elseif (strtolower(substr($table, -1)) === "s") {
        $possible_singular_table_name = substr($table, 0, -1);
    } else {
        $possible_singular_table_name = $table;
    }
    // Why not changue all the id to ID ?
    $field_name = str_replace('id', 'ID', $field_name);
    // Remove the possible singular name table from field name
    $field_name = str_replace("{$possible_singular_table_name}_", '', $field_name);
    // Better look without the _ character
    $field_name = str_replace('_', ' ', strtoupper(substr($field_name, 0, 1)) . (substr($field_name, 1)));
    return $field_name;
}

/**
 * Run a SQL Query and returns an Array with all the result data
 * @param \PDO $db
 * @param String $sql
 * @param Boolean $return_all
 * @param Boolean $do_fields
 * @return Array NULL on empty result and FALSE on failure.
 * TODO: Fix the NON optional cache isue !!
 */
function sql_query(\PDO $db, $sql, $return_all = TRUE, $do_fields = FALSE, $use_cache = TRUE, &$error_data = null) {
//$query_result = new PDOStatement();
    $queryReturn = NULL;
    if (profiler::is_enabled()) {
        $sql_profile_id = profiler::add($sql);
        profiler::start_time_count($sql_profile_id);
    }
    if (($use_cache) && (local_cache::is_enabled())) {
        $queryReturn = local_cache::get_result($sql);
    }
    if ($queryReturn) {
        if (profiler::is_enabled()) {
            profiler::set_is_cached($sql_profile_id, TRUE);
            profiler::stop_time_count($sql_profile_id);
        }
        return $queryReturn;
    } else {
        if (profiler::is_enabled()) {
            profiler::set_is_cached($sql_profile_id, FALSE);
        }
        $query_result = $db->query($sql);
    }
    $fields = array();
    $i = 1;
    $return = null;
    if ($query_result !== FALSE) {
        if ($query_result->rowCount() > 0) {
            while ($row = $query_result->fetch(\PDO::FETCH_ASSOC)) {
                if ($do_fields && $return_all) {
                    foreach ($row as $key => $value) {
                        $fields[$key] = $key;
                    }
                    $do_fields = FALSE;
                    $queryReturn[0] = $fields;
                }
                $queryReturn[$i] = $row;
                $i++;
            }
            if (isset($queryReturn)) {
                if ($return_all) {
                    if (\k1app\APP_MODE == "web") {
                        local_cache::add($sql, $queryReturn);
                    }
                    $return = $queryReturn;
                } else {
                    if (\k1app\APP_MODE == "web") {
                        local_cache::add($sql, $queryReturn[1]);
                    }
                    $return = $queryReturn[1];
                }
            } else {
//                d($sql);
            }
        } else {
            $return = NULL;
        }
    } else {
        $return = FALSE;
    }
    if (profiler::is_enabled()) {
        profiler::stop_time_count($sql_profile_id);
    }
    return $return;
}

/**
 * 
 * @global type $controller_errors
 * @param \PDO $db
 * @param string $table
 * @param array $data
 * @param array $table_keys
 * @param array $db_table_config
 * @return boolean
 */
function sql_update(\PDO $db, $table, $data, $table_keys = array(), $db_table_config = array(), &$error_data = null, &$sql_query = null) {
    global $controller_errors;

    if (!is_string($table) || empty($table)) {
        die(__FUNCTION__ . ": \$table should be an non empty string");
    }
    if (!is_array($data)) {
        die(__FUNCTION__ . ": need an array to work on \$data");
    }
    if (!is_array($table_keys)) {
        die(__FUNCTION__ . ": need an array to work on \$table_keys");
    }
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }

    if ($db->is_enabled()) {
        if (is_array($data)) {
            if (!is_array(@$data[0])) {
                if (empty($db_table_config)) {
                    $db_table_config = get_db_table_config($db, $table);
                }
                if (empty($table_keys)) {
                    $keys_where_condition = table_keys_to_where_condition($data, $db_table_config);
                } else {
                    $keys_where_condition = array_to_sql_set($db, $table_keys, TRUE, TRUE);
                }
                $data_string = array_to_sql_set($db, $data);
                $update_sql = "UPDATE $table SET $data_string WHERE $keys_where_condition;";
//                $controller_errors[] = $update_sql;
//                $controller_errors[] = print_r($data, TRUE);
            } else {
                die(__FUNCTION__ . ": only can work with a 1 dimension array");
            }
//            d($update_sql);
            $sql_query = $update_sql;
            $update = $db->exec($update_sql);

            if (isset($db->errorInfo()[2]) && !empty($db->errorInfo()[2])) {
//                $regexp = "/\((?:`(\w+)`,?)+\)/ix";
                $regexp = "/FOREIGN KEY \((.*)?\)/i";
                $match = [];
                if (preg_match($regexp, $db->errorInfo()[2], $match)) {
                    $match[1] = str_replace(' ', '', $match[1]);
                    $fk_fields_error = explode(',', str_replace('`', '', $match[1]));
                    if (!empty($fk_fields_error)) {
                        foreach ($fk_fields_error as $value) {
                            $error_data[$value] = 'Key error';
                        }
                    }
                } else {
                    $error_data = "Error on Update stament : ($update_sql) " . $db->errorInfo()[2];
                }
            }

            if ($update) {
                return $update;
            } else {
                return FALSE;
            }
        } else {
            \trigger_error("Has not received an arrany to do his work", E_USER_ERROR);
            exit();
        }
    } else {
        \trigger_error("This App do not support databases", E_USER_ERROR);
        return FALSE;
    }
}

function sql_insert(\PDO $db, $table, $data, &$error_data = null, &$sql_query = null) {
    if ($db->is_enabled()) {
        if (is_array($data)) {
            if (!is_array($data[0])) {
                $data_string = array_to_sql_set($db, $data);
                if ($data_string === false) {
                    \trigger_error("\$data array is invalid", E_USER_WARNING);
                    if (defined('K1APP_VERBOSE') && K1APP_VERBOSE > 0) {
                        d($data, TRUE);
                    }
                    return FALSE;
                }
                $insert_sql = "INSERT INTO $table SET $data_string;";
            } else {
                $data_string = array_to_sql_values($data);
                if ($data_string === false) {
                    \trigger_error("\$data array is invalid", E_USER_WARNING);
                    if (defined('K1APP_VERBOSE') && K1APP_VERBOSE > 0) {
                        d($data, TRUE);
                    }
                    return FALSE;
                }
                $insert_sql = "INSERT INTO $table $data_string;";
            }
//            ($insert_sql);
            $sql_query = $insert_sql;
            $insert = $db->exec($insert_sql);

            if (isset($db->errorInfo()[2]) && !empty($db->errorInfo()[2])) {
//                $regexp = "/\((?:`(\w+)`,?)+\)/ix";
                $regexp = "/FOREIGN KEY \((.*)?\) REFERENCES/i";
                $match = [];
                if (preg_match($regexp, $db->errorInfo()[2], $match)) {
                    $match[1] = str_replace(' ', '', $match[1]);
                    $fk_fields_error = explode(',', str_replace('`', '', $match[1]));
                    if (!empty($fk_fields_error)) {
                        foreach ($fk_fields_error as $value) {
                            $error_data[$value] = 'Key error';
                        }
                    }
                } else {
                    $error_data = "Error on Insert stament : " . $db->errorInfo()[2] . "($insert_sql)";
                }
            }
            if ($insert) {
                $last_insert_sql = "SELECT LAST_INSERT_ID() as 'LAST_ID'";
                $last_insert_result = sql_query($db, $last_insert_sql, FALSE);
                if (isset($last_insert_result['LAST_ID']) && (!empty($last_insert_result['LAST_ID']))) {
                    return $last_insert_result['LAST_ID'];
                } else {
                    return TRUE;
                }
            } else {
                return FALSE;
            }
        } else {
            die(__FUNCTION__ . ": has not received an arrany to do his work");
            exit();
        }
    } else {
        \trigger_error("This App do not support databases", E_USER_WARNING);
        return FALSE;
    }
}

function array_to_sql_values($array) {
    if (is_array($array) && (count($array) > 1)) {
        $first = TRUE;
        $data_string = "";
// construct the field row
        $headers_count = count($array[0]);
        if ($headers_count > 0) {
            $data_string .= "(";
            foreach ($array[0] as $field_name) {
//put the , to the string
                if (!$first) {
                    $data_string .= ", ";
                } else {
                    $first = FALSE;
                }
                $data_string .= trim($field_name);
            }
            $data_string .= ") VALUES ";
        } else {
            \trigger_error("wrong format in array", E_USER_WARNING);
            return false;
        }
// remove the headers to only work with the values - lazzy code :P
        unset($array[0]);
// build the data
        $first_group = TRUE;
        foreach ($array as $values_array) {
            $values_count = count($values_array);
            if (!$first_group) {
                $data_string .= ", ";
            } else {
                $first_group = FALSE;
            }
            if ($values_count == $headers_count) {
                $data_string .= "(";
                $first = TRUE;
                foreach ($values_array as $value) {
//put the , to the string
                    if (!$first) {
                        $data_string .= ", ";
                    } else {
                        $first = FALSE;
                    }
                    $value = \k1lib\forms\check_single_incomming_var($value);
                    if ($value === NULL) {
                        $data_string .= "NULL";
                    } elseif (!is_int($value) && !is_float($value)) {
                        $data_string .= "'{$value}'";
                    } else {
                        $data_string .= "{$value}";
                    }
//                    $data_string .= ( is_numeric($value) ? $value : "'$value'");
                }
                $data_string .= ") ";
            } else {
                \trigger_error("wrong values count of array" . print_r($array, true), E_USER_WARNING);
                return false;
            }
        }
// join to return
        return $data_string;
    } else {
        trigger_error("Bad formated array in " . __FUNCTION__, E_USER_WARNING);
        return false;
    }
}

/**
 * Convert an ARRAY to SQL SET pairs
 * @param Array $array Array to convert
 * @param Bolean $use_nulls If should keep the null data, otherwise those will be skiped
 * @param Bolean $for_where_stament If TRUE will join the pairs with AND, if not, will use coma instead
 * @return type
 */
function array_to_sql_set(\PDO $db, array $array, $use_nulls = true, $for_where_stament = FALSE, $precise = TRUE) {
    if (is_array($array) && (count($array) >= 1)) {

        /**
         * NEW CODE 2016
         */
        $pairs = [];
        foreach ($array as $field => $value) {
            if ($use_nulls === FALSE && $value === NULL) {
                continue;
            }
            if ($precise) {
                if ($value === NULL) {
                    if ($for_where_stament) {
                        $pairs[] = "`{$field}` IS NULL";
                    } else {
                        $pairs[] = "`{$field}` = NULL";
                    }
                } else {
                    $pairs[] = "`{$field}`= " . $db->quote($value);
                }
            } else {
                $pairs[] = "`{$field}` LIKE '%" . $db->quote($value) . "%'";
            }
        }
        if ($for_where_stament) {
            $glue = " AND ";
        } else {
            $glue = ", ";
        }
        $data_string = implode($glue, $pairs);
    } else {
        trigger_error("Bad formated array in " . __FUNCTION__, E_USER_WARNING);
        return false;
    }
    return $data_string;
}

/**
 * Convert an ARRAY to SQL SET pairs with deferent <> or NOT LIKE
 * @param Array $array Array to convert
 * @param Bolean $use_nulls If should keep the null data, otherwise those will be skiped
 * @param Bolean $for_where_stament If TRUE will join the pairs with AND, if not, will use coma instead
 * @return type
 */
function array_to_sql_set_exclude(\PDO $db, array $array, $use_nulls = true, $for_where_stament = FALSE, $precise = TRUE) {
    if (is_array($array) && (count($array) >= 1)) {

        /**
         * NEW CODE 2016
         */
        $pairs = [];
        foreach ($array as $field => $value) {
            if ($use_nulls === FALSE && $value === NULL) {
                continue;
            }
            if ($precise) {
                if ($value === NULL) {
                    $pairs[] = "`{$field}` IS NOT NULL";
                } else {
                    $pairs[] = "`{$field}`<> " . $db->quote($value);
                }
            } else {
                $pairs[] = "`{$field}` NOT LIKE '%" . $db->quote($value) . "%'";
            }
        }
        if ($for_where_stament) {
            $glue = " AND ";
        } else {
            $glue = ", ";
        }
        $data_string = implode($glue, $pairs);
    } else {
        trigger_error("Bad formated array in " . __FUNCTION__, E_USER_ERROR);
        exit();
    }
    return $data_string;
}

function get_db_tables_config_from_sql(\PDO $db, $sql_query) {
    $sql_query = "EXPLAIN " . $sql_query;
    $explainResult = sql_query($db, $sql_query, TRUE);
    if ($explainResult) {
        $presentTablesArray = Array();
        $tableConfig = NULL;
        foreach ($explainResult as $row) {
            if (isset($row['table']) && (!empty($row['table'])) && (!strstr($row['table'], '<')) && ($row['select_type'] != 'DEPENDENT SUBQUERY')) {
                $tableConfig = get_db_table_config($db, $row['table']);
                if (!empty($tableConfig)) {
                    $presentTablesArray = array_merge($presentTablesArray, $tableConfig);
                }
            }
        }
        if (!empty($presentTablesArray)) {
            return $presentTablesArray;
        } else {
            return NULL;
        }
    } else {
        return NULL;
    }
}

function get_sql_count_query_from_sql_code($sql_query) {
    $sql_query_lower = strtolower($sql_query);
    $from_pos = strpos($sql_query_lower, "from");
    $new_sql_with_count = "SELECT count(*) as num_rows " . substr($sql_query, $from_pos);
    return $new_sql_with_count;
}

function get_sql_query_with_new_fields($sql_query, $fields) {
    $sql_query_lower = strtolower($sql_query);
    $from_pos = strpos($sql_query_lower, "from");
    $new_sql_with_new_fields = "SELECT {$fields} " . substr($sql_query, $from_pos);
    return $new_sql_with_new_fields;
}

/**
 * Get the DABASE on USE for a PDO connection
 * @param PDO $db
 * @return string Database name or FALSE on error
 */
function get_db_database_name(\PDO $db) {


    $db_name_sql = "SELECT DATABASE() as DB_NAME;";
    $result = sql_query($db, $db_name_sql, FALSE);
    if (isset($result['DB_NAME'])) {
        return $result['DB_NAME'];
    } else {
        return FALSE;
    }
}

/**
 * Check if the recieved var $db is a PDO object. On error the entire sofware will DIE
 * @param PDO $db
 * @param string $caller
 */
function db_check_object_type(\PDO $db, $caller = "") {
    if (get_class($db) != "PDO") {
        die(__FUNCTION__ . ": \$db is not a PDO object type" . (($caller != "") ? " - called from: $caller" : "" ));
    }
}

function get_db_table_keys($db_table_config) {
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }
    $keys = array();
    foreach ($db_table_config as $field => $config) {
        if ($config['key'] == 'pri') {
            $keys[$field] = 'pri';
        }
    }
    if (empty($keys)) {
        return FALSE;
    } else {
        return $keys;
    }
}

function get_db_table_keys_array($db_table_config) {
    if (!is_array($db_table_config)) {
        trigger_error(__FUNCTION__ . ": need an array to work on \$db_table_config", E_USER_ERROR);
    }
    $keys = array();
    foreach ($db_table_config as $field => $config) {
        if ($config['key'] == 'pri') {
            $keys[] = $field;
        }
    }
    if (empty($keys)) {
        return FALSE;
    } else {
        return $keys;
    }
}

/**
 * Get the FIELD with label-field:yes comment on $position order
 * @param Array $db_table_config
 * @param Integer $position If this is -1 will return the last field found
 * @return String Label field name
 */
function get_db_table_label_fields($db_table_config) {
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }
    $p = 0;
    $labels_fields = [];
    foreach ($db_table_config as $field => $config) {
        if (($config['label-field'])) {
            $labels_fields[] = $field;
        }
    }
    if (!empty($labels_fields)) {
        return $labels_fields;
    } else {
        return NULL;
    }
}

function get_db_table_label_fields_from_row($row_data, $db_table_config) {
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }
    $p = 0;
    $labels = [];
    foreach ($db_table_config as $field => $config) {
        if (($config['label-field'])) {
            $labels[] = $row_data[$field];
        }
    }
    if (!empty($labels)) {
        return implode(" ", $labels);
    } else {
        return NULL;
    }
}

function resolve_fk_real_field_name(&$data_array_to_modify, $field_to_resolve, $table_config_array) {
    if (!empty($table_config_array[$field_to_resolve]['refereced_column_config'])) {
        $refereced_column_config = $table_config_array[$field_to_resolve]['refereced_column_config'];
        while (!empty($refereced_column_config['refereced_column_config'])) {
            $refereced_column_config = $refereced_column_config['refereced_column_config'];
        }
        $new_data_array_to_modify = [];
        foreach ($data_array_to_modify as $key => $value) {
            if ($key == $field_to_resolve) {
                $new_data_array_to_modify[$refereced_column_config['field']] = $value;
            } else {
                $new_data_array_to_modify[$key] = $value;
            }
        }
        $data_array_to_modify = $new_data_array_to_modify;
    }
}

function resolve_fk_real_fields_names(&$data_array_to_modify, $table_config_array) {
    foreach ($data_array_to_modify as $field => $value) {
        resolve_fk_real_field_name($data_array_to_modify, $field, $table_config_array);
    }
}

function get_fk_field_label(\PDO $db, $fk_table_name, array $url_key_array = [], $source_table_config = []) {
    foreach ($url_key_array as $url_key_index => $url_key_value) {
        
    }
    resolve_fk_real_field_name($url_key_array, $url_key_index, $source_table_config);

    if (!is_string($fk_table_name)) {
        trigger_error("\$fk_table_name must to be a String", E_USER_ERROR);
    }
    $fk_table_config = get_db_table_config($db, $fk_table_name);
    $fk_table_label_fields = get_db_table_label_fields($fk_table_config);

    if (!empty($fk_table_label_fields)) {
        $fk_table_label_fields_text = implode(",", $fk_table_label_fields);
        $fk_where_condition = table_keys_to_where_condition($url_key_array, $fk_table_config);
        if (!empty($fk_where_condition)) {
            $fk_sql_query = "SELECT {$fk_table_label_fields_text} FROM $fk_table_name WHERE $fk_where_condition";
            $sql_result = sql_query($db, $fk_sql_query, FALSE);
            return implode(" ", $sql_result);
        } else {
            return NULL;
        }
    } else {
        return NULL;
    }
}

function get_db_table_refereces($db_table_config) {
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }
    $keys = array();
    foreach ($db_table_config as $field => $config) {
        if (!empty($config['refereced_table_name'])) {
            $keys[$field]['refereced_table_name'] = $config['refereced_table_name'];
            $keys[$field]['refereced_column_name'] = $config['refereced_column_name'];
            $keys[$field]['refereced_column_config'] = $config['refereced_column_config'];
        }
    }
    if (empty($keys)) {
        return FALSE;
    } else {
        return $keys;
    }
}

/**
 * From an array returns a text as key1--key2--keyN with ONLY the key fields 
 * @param Array $row_data
 * @param Array $db_table_config
 * @return Array FALSE on error
 */
function table_keys_to_text($row_data, $db_table_config) {
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }
    $table_keys_array = \k1lib\sql\get_db_table_keys($db_table_config);
    $table_keys_values = array();
    foreach ($row_data as $column_name => $value) {
        if (isset($table_keys_array[$column_name]) && (!empty($table_keys_array[$column_name]))) {
            $table_keys_values[] = $value;
        }
    }
    $table_keys_text = implode("--", $table_keys_values);
    return $table_keys_text;
}

function get_keys_array_from_row_data($row_data, $db_table_config) {
    $key_fields_array = get_db_table_keys($db_table_config);
    $keys_array = \k1lib\common\clean_array_with_guide($row_data, $key_fields_array);
    if (!empty($keys_array)) {
        return $keys_array;
    } else {
        return[];
    }
}

function table_url_text_to_keys($url_text, $db_table_config) {
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }
    $url_text_array = explode("--", $url_text);
    $url_text_array_count = count($url_text_array);

    $table_keys_array = \k1lib\sql\get_db_table_keys($db_table_config);
    $table_keys_count = count($table_keys_array);
// elements count check
    if ($url_text_array_count != $table_keys_count) {
        trigger_error(__FUNCTION__ . ": The count of recived keys ({$url_text_array_count}) as text to not match with the \$db_table_config ({$table_keys_count})", E_USER_ERROR);
    } else {
//lets do the array using the url_text and $table_keys
        $key_data = array();
        $i = 0;
        foreach ($table_keys_array as $key_name => $noused) {
            $key_data[$key_name] = $url_text_array[$i];
            $i++;
        }
    }
// data type check
    $errors = \k1lib\forms\form_check_values($key_data, $db_table_config);
    if (!empty($errors)) {
        d($key_data);
        d($errors);
        trigger_error("Value types on the received \$url_text do not match with \$db_table_config", E_USER_ERROR);
    }
    return $key_data;
}

function table_keys_to_where_condition(&$row_data, $db_table_config, $use_table_name = FALSE) {
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }

    $table_keys_array = \k1lib\sql\get_db_table_keys($db_table_config);
    if (empty($table_keys_array)) {
        die(__FUNCTION__ . ": The is no PRI on the \$db_table_config");
    }

    $key_values = array();
    foreach ($table_keys_array as $column_name => $noused) {
        if (isset($row_data[$column_name])) {
            $key_values[$column_name] = $row_data[$column_name];
        }
    }

    $first_value = TRUE;
    $where_condition = "";
    foreach ($key_values as $key => $value) {
//        if ($db_table_config[$key]['type'])
        if (!$first_value) {
            $where_condition .= " AND ";
        }
        if ($use_table_name) {
            $where_condition .= "{$db_table_config[$key]['table']}.$key = '$value'";
        } else {
            $where_condition .= "$key = '$value'";
        }
        $first_value = FALSE;
    }
    return $where_condition;
}

function sql_del_row(\PDO $db, $table, $key_array) {
    $key_sql_set = array_to_sql_set($db, $key_array, TRUE, TRUE);
    $sql = "DELETE FROM `$table` WHERE $key_sql_set";
//    echo $sql;
    $exec = $db->exec($sql);
//    d($exec);
    if ($exec > 0 && $exec !== FALSE) {
        return $exec;
    } else {
        return FALSE;
    }
}

function sql_check_id(\PDO $db, $table, $key_name, $key_value, $use_cache = FALSE) {

    $sql = "SELECT COUNT(*) AS num_keys FROM `$table` WHERE `$key_name` = " . ( is_numeric($key_value) ? $key_value : "'$key_value'") . " ";
    if ($use_cache) {
        $sql_count = sql_query_cached($db, $sql, FALSE);
    } else {
        $sql_count = sql_query($db, $sql, FALSE);
    }
    if ($sql_count['num_keys'] > 0) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function sql_value_increment(\PDO $db, $table, $key_name, $key_value, $field_name, $step = 1) {

    $sql = "SELECT `$field_name` FROM `$table` WHERE `$key_name` = " . ( is_numeric($key_value) ? $key_value : "'$key_value'") . " ";
    if ($sql_result = sql_query($db, $sql, FALSE)) {
// make the 'step' increment on the field to rise
        $sql_result[$field_name] += $step;
// add to the data array the key command to use the sql_update function
        $sql_result["$key_name:key"] = $key_value;
        if (sql_update($db, $table, $sql_result)) {
            return $sql_result[$field_name];
        } else {
            return FALSE;
        }
    } else {
        \trigger_error("The value to increment coundn't be query", E_USER_WARNING);
        d($sql);
    }
}

function sql_count($data_array) {
    $count = count($data_array) - 1;
    return ($count >= 0) ? $count : 0;
}

function sql_table_count(\PDO $db, $table, $condition = "", $use_memcache = FALSE, $expire_time = 60) {

    $sql = "SELECT COUNT(*) AS counted FROM `$table` " . ( ($condition != "") ? "WHERE $condition" : "");
    if ($use_memcache) {
        $result = sql_query_cached($db, $sql, FALSE, FALSE, $expire_time);
    } else {
        $result = sql_query($db, $sql, FALSE);
    }
    if ($result) {
        return $result['counted'];
    } else {
        FALSE;
    }
}

function get_db_table_enum_values(\PDO $db, $table, $field) {

    $dsn_db = get_db_database_name($db);
    $enum_sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$dsn_db}' AND TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$field}'";
    $enum_result = sql_query($db, $enum_sql, FALSE);
    $type = $enum_result['COLUMN_TYPE'];
    $matches = array();
    preg_match('/^enum\((.*)\)$/', $type, $matches);
    $i = 0;
    if (!empty($matches[1])) {
        foreach (explode(',', $matches[1]) as $value) {
            $enum[trim($value, "'")] = trim($value, "'");
        }
    } else {
        $enum = [];
    }
    return $enum;
}

function table_traduce_enum_to_index(\PDO $db, &$query_result, $db_table_config) {
    if (!is_array($query_result)) {
        die(__FUNCTION__ . ": need an array to work on \$query_result");
    }
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }
// now go one by one row on the result
    foreach ($query_result as $column => $value) {
        if ($db_table_config[$column]['type'] == 'enum') {
            $enum_values_array = get_db_table_enum_values($db, $db_table_config[$column]['table'], $column);
            if (count($enum_values_array) > 0) {
                $enum_values_array = array_flip($enum_values_array);
                $query_result[$column] = $enum_values_array[$value];
            }
        }
    }
}

function get_table_definition_as_array(\PDO $db, $table_name) {
    $definition = \k1lib\sql\sql_query($db, "SHOW CREATE TABLE {$table_name}", FALSE);
    $definition_array = explode("\n", $definition['Create Table']);
    // REMOVE THE 'CREATE TABLE PART'
    unset($definition_array[0]);
    // REMOVE THE LAST LINE 'ENGINIE=
    unset($definition_array[count($definition_array)]);
    // REMOVE PRIMARY KEY LINE
    unset($definition_array[count($definition_array)]);
    $definition_array_clean = [];
    foreach ($definition_array as $row => $text) {
        $text = substr($text, 3, -1);
        $field_name = substr($text, 0, strpos($text, "`"));
        $field_definition = substr($text, strpos($text, "`") + 2);
        $definition_array_clean[$field_name] = str_replace(strstr($text, "COMMENT"), "", $field_definition);
//        $definition_array_clean[$field_name]['definition'] = str_replace(strstr($text, "COMMENT"), "", $field_definition);
//        $definition_array_cl´ean[$field_name]['comment'] = strstr($text, "COMMENT");
    }
    return ($definition_array_clean);
}

function get_table_data_as_key_value_pair(\PDO $db, $table_name) {
    $sql_query = "SELECT * FROM $table_name";
    $sql_result = sql_query($db, $sql_query);
    if ($sql_result) {
        $new_pair_array = [];
        foreach ($sql_result as $row => $data) {
            if (count($data) == 2) {
                $new_pair_array[current($data)] = next($data);
            }
        }
        return $new_pair_array;
    } else {
        return FALSE;
    }
}

// ./src/templates/class_temply.php


namespace k1lib\templates;

class temply {

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;

    /**
     * URL data array
     * @var Array
     */
    static private $output_places;

    /**
     * Enable the engenie
     */
    static public function enable($app_mode) {
        self::$enabled = TRUE;
        self::$output_places = array();
        if ($app_mode == "web") {
            \ob_start('\k1lib\templates\temply::parse_template_places');
//            \ob_start();
        }
    }

    static public function end($app_mode) {
        if ($app_mode == "web") {
            \ob_end_flush();
        }
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
        self::is_enabled(true);
        return self::$output_places;
    }

    static public function is_place_registered($place_name) {
        self::is_enabled(true);

        if (!is_string($place_name)) {
            \trigger_error("The place name HAS to be a string", E_USER_ERROR);
        }
        if (isset(self::$output_places[$place_name])) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static public function register_place($place_name) {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        if (!is_string($place_name)) {
            \trigger_error("The place name HAS to be a string", E_USER_ERROR);
        }
        self::$output_places[$place_name] = array();
    }

    /**
     * set the value for a place name
     * @global array self::$output_places
     * @param string $place
     * @param string $value
     * @return none
     */
    static public function set_place_value($place_name, $value) {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);

        if (empty($value)) {
            return FALSE;
        }

        if (!is_string($place_name)) {
            trigger_error("The OUTPUT PLACE '{$place_name}' couldn't be registered with a value diferent a string " . __FUNCTION__, E_USER_WARNING);
        }
        if (!is_string($value)) {
            if (!is_object($value)) {
                trigger_error("The OUTPUT VALUE '$place_name'->$value couldn't be used " . __FUNCTION__, E_USER_ERROR);
            } elseif (strstr(get_class($value), 'k1lib\html\\') === false) {
                trigger_error("The OUTPUT VALUE as object diferent from html couldn't be used, now is (" . get_class($value) . ") " . __FUNCTION__, E_USER_ERROR);
            }
        }

        if (isset(self::$output_places[$place_name])) {
            self::$output_places[$place_name][] = $value;
        } else {
            die("The OUTPUT PLACE '{$place_name}' is not defined yet " . __FUNCTION__);
        }
    }

    /**
     * get the value for a place name
     * @global array self::$output_places
     * @param string $place
     * @param string $value
     * @return none
     */
    static public function get_place_value($place_name) {
        self::is_enabled(true);

        if (!is_string($place_name)) {
            trigger_error("The OUTPUT PLACE '{$place_name}' couldn't be registered with a value diferent a string " . __FUNCTION__, E_USER_WARNING);
        }
        if (isset(self::$output_places[$place_name]) && (count(self::$output_places[$place_name]) > 0)) {
            return implode("\n", self::$output_places[$place_name]);
        } else {
            return false;
        }
    }

    /**
     * output the place name string on the template
     * Rev 1: Now register the place name if is not registererd
     */
    static public function set_template_place($place_name) {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        //check if the place name exist
        if (!self::is_place_registered($place_name)) {
            self::register_place($place_name);
        }
        // only strings for the place name
        if (!is_string($place_name)) {
            \trigger_error("The place name HAS to be a string", E_USER_ERROR);
        }
        // prints the place html code
        return self::convert_place_name($place_name) . "\n";
    }

    /**
     * convert a place name to the way k1.lib handle the space names
     * @param string $place_name
     * @return type string
     */
    static public function convert_place_name($place_name) {
        self::is_enabled(true);
        if (!is_string($place_name)) {
            \trigger_error("The place name HAS to be a string", E_USER_ERROR);
        }
        return "<!-- K1_TEMPLATE_PLACE_" . strtoupper($place_name) . "-->";
    }

    static public function register_header($url, $relative = FALSE, $type = "auto") {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        if (!is_string($url)) {
            \trigger_error("The URL HAS to be a string", E_USER_ERROR);
        }
        if ($type == "auto") {
            $file_extension = \k1lib\common\get_file_extension($url);
        } else {
            $file_extension = $type;
        }
        if (!$file_extension) {
            return FALSE;
        }
        switch ($file_extension) {
            case 'js':
                $code = "<script src=\"%url%\"></script>";
                break;
            case 'css':
                $code = "<link href=\"%url%\" rel=\"stylesheet\" />";
                break;
            default:
                \trigger_error("no extension detected on [$url] ", E_USER_ERROR);
                return FALSE;
                break;
        }
        if ($relative) {
            $code = str_replace("%url%", \k1lib\urlrewrite\url::do_url($url, [], FALSE), $code);
        } else {
            $code = str_replace("%url%", $url, $code);
        }
        return self::set_place_value("header", $code);
    }

    static public function register_footer($url, $relative = FALSE, $type = "auto") {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        if (!is_string($url)) {
            \trigger_error("The URL HAS to be a string", E_USER_ERROR);
        }
        if ($type == "auto") {
            $file_extension = \k1lib\common\get_file_extension($url);
        } else {
            $file_extension = $type;
        }
        if (!$file_extension) {
            return FALSE;
        }
        switch ($file_extension) {
            case 'js':
                $code = "<script src=\"%url%\"></script>";
                break;
            case 'css':
                $code = "<link href=\"%url%\" rel=\"stylesheet\">";
                break;
            default:
                \trigger_error("no extension detected on [$url] ", E_USER_ERROR);
                return FALSE;
                break;
        }
        if ($relative) {
            $code = str_replace("%url%", \k1lib\urlrewrite\url::do_url($url, [], FALSE), $code);
        } else {
            $code = str_replace("%url%", $url, $code);
        }
        return self::set_place_value("footer", $code);
    }

    static public function parse_template_places($buffer) {
        self::is_enabled(true);

        if (!isset($buffer)) {
            \trigger_error("The BUFFER is empty", E_USER_ERROR);
        }
        if (count(self::$output_places) > 0) {
            foreach (self::$output_places as $place_name => $place_data) {
                $template_place_name = self::convert_place_name($place_name);
                $place_code = "\n";
                foreach ($place_data as $place_value) {
                    $place_code .= "\t" . $place_value . "\n";
                }
                $buffer = str_replace($template_place_name, $place_code, $buffer);
            }
        }
        return $buffer;
    }

    static public function load_template($template_name, $path_to_use) {
        self::is_enabled(true);
        if (is_string($template_name)) {
            if ($template_to_load = self::template_exist($template_name, $path_to_use)) {
                return $template_to_load;
            } else {
                trigger_error("Template ($template_name) do not exist", E_USER_ERROR);
            }
        } else {
            trigger_error("The template names value only can be string", E_USER_ERROR);
        }
    }

    static public function template_exist($template_name, $path_to_use) {
        self::is_enabled(true);
        if (is_string($template_name)) {
            // Try with subfolder scheme
            $template_to_load = $path_to_use . "/{$template_name}.php";
            if (file_exists($template_to_load)) {
                return $template_to_load;
            } else {
                trigger_error("Template ($template_to_load) is not on disk", E_USER_ERROR);
            }
        }
        return FALSE;
    }

    static public function load_view($view_name, $view_path, $js_auto_load = TRUE) {
        trigger_error("Do not use me " . __METHOD__, E_USER_WARNING);
        self::is_enabled(true);
        if (is_string($view_name)) {
            // Try with subfolder scheme
            $view_subfix = $view_path . "/{$view_name}";
            $js_view_subfix = $view_path . "/{$view_name}";

            $view_to_load = $view_subfix . "/index.php";

            if (file_exists($view_to_load)) {
                // JS Auto load
                if ($js_auto_load) {
                    $last_controles_name = basename($view_name);
                    $js_to_load = "{$js_view_subfix}/js/{$last_controles_name}.js";
                    $js_file_to_check = "{$view_subfix}/js/{$last_controles_name}.js";
                    if (file_exists($js_file_to_check)) {
                        self::register_header($js_to_load);
                    }
                }
                return $view_to_load;
            } else {
// Try with single file scheme
                $view_to_load = $view_subfix . ".php";
                if (file_exists($view_to_load)) {
                    // JS Auto load
                    if ($js_auto_load) {
                        $last_controles_name = basename(($view_name));
                        $js_to_load = dirname($js_view_subfix) . "/js/{$last_controles_name}.js";
                        $js_file_to_check = dirname($view_subfix) . "/js/{$last_controles_name}.js";
                        if (file_exists($js_file_to_check)) {
                            self::register_header($js_to_load);
                        }
                    }
                    return $view_to_load;
                } else {
//                    trigger_error(__METHOD__ . " : The view '{$view_to_load}' could not be found", E_USER_NOTICE);
                    return FALSE;
                }
            }
        } else {
            trigger_error("The view name value only can be string", E_USER_ERROR);
            exit;
        }
    }

}

// ./src/templates/template.php


namespace k1lib\html;

class template {

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;
    static private $template_path = NULL;
    static private $templates_loaded = [];

//    static private $js_path = NULL;
//    static private $css_path = NULL;
//    static private $images_path = NULL;

    /**
     * Enable the engenie
     */
//    static public function enable($app_url, $template_path, $css_path = 'css/', $js_path = 'js/', $images_path = 'imgages/') {
    static public function enable($template_path) {
        self::$enabled = TRUE;
        if (file_exists($template_path)) {
            self::$template_path = $template_path;
//            if (file_exists($template_path . $css_path)) {
//                self::$css_path = $template_path . $css_path;
//            }
//            if (file_exists($template_path . $js_path)) {
//                self::$js_path = $template_path . $js_path;
//            }
//            if (file_exists($template_path . $images_path)) {
//                self::$images_path = $template_path . $images_path;
//            }
        } else {
            self::error_500('The template path do not exist: ' . $template_path);
        }
    }

    static public function error_500($error_message) {
        header($_SERVER["SERVER_PROTOCOL"] . " 500 Internal Server Error", true, 500);
        DOM::start();
        DOM::html()->body()->append_h1('500 Internal error');
        DOM::html()->body()->append_p($error_message);
        echo DOM::generate();
        trigger_error('App error fired', E_USER_NOTICE);
        exit;
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            self::error_500('Template load system is not enabled yet: ' . $template_path);
        }
        return self::$enabled;
    }

    static public function load_template($template_name) {
//        d($template_name);
        if (!array_key_exists($template_name, self::$templates_loaded)) {
            self::$templates_loaded[$template_name] = TRUE;
//            d(self::$templates_loaded);
            $template_name = str_replace('/', DIRECTORY_SEPARATOR, $template_name);
            self::is_enabled(true);
            if (is_string($template_name)) {
                $template_to_load = self::template_exist($template_name);
                if ($template_to_load) {
                    include $template_to_load;
                } else {
                    trigger_error("Template ($template_name) do not exist", E_USER_ERROR);
                }
            } else {
                trigger_error("The template names value only can be string", E_USER_ERROR);
            }
        }
    }

    static public function template_exist($template_name) {
        self::is_enabled(true);
        if (is_string($template_name)) {
            // Try with subfolder scheme
            $template_to_load = self::$template_path . "/{$template_name}.php";
            if (file_exists($template_to_load)) {
                return $template_to_load;
            } else {
                trigger_error("Template ($template_to_load) is not on disk", E_USER_ERROR);
            }
        }
        return FALSE;
    }

}

// ./src/urlrewrite/class_url_manager.php


namespace k1lib\urlrewrite;

use \k1lib_common;
use \k1lib\api\api;

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
    static private $api_mode = FALSE;

    static function set_api_mode() {
        self::$api_mode = TRUE;
    }

    static function get_api_mode() {
        return self::$api_mode;
    }

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
                        if (self::$api_mode === TRUE) {
                            $error = new \k1lib\api\api();
                            $error->send_response(500, ['message' => "The URL value in the level {$level} is empty, the actual URL is bad formed " . __FUNCTION__]);
                        } else {
                            die("The URL value in the level {$level} is empty, the actual URL is bad formed " . __FUNCTION__);
                        }
                    } else {
                        return FALSE;
                    }
                }
            } else {
                if (!$required) {
                    $GLOBALS[$name] = NULL;
                    return FALSE;
                } else {
                    if (self::$api_mode === TRUE) {
                        $error = new \k1lib\api\api();
                        $error->send_response(500, ['message' => "The URL level {$level} requested do not exist and is required"]);
                    } else {
                        trigger_error("The URL level {$level} requested do not exist and is required", E_USER_ERROR);
                    }
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
        $myGET = $_GET;
        foreach ($myGET as $key => $value) {
            $myGET[$key] = urldecode($value);
        }
        $actual_get_vars = \k1lib\forms\check_all_incomming_vars($myGET);
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

    static function set_next_url_level($controller_path, $required_level = FALSE, $level_name = 'default', $return_non_existent_level = FALSE) {
        $next_url_level = self::get_url_level_count();
        // get the base URL to load the next one
        $actual_url = self::get_this_url();
        // get from the URL the next level value :   /$actual_url/next_level_value
        $next_directory_name = self::set_url_rewrite_var($next_url_level, $level_name, $required_level);
        if (!empty($next_directory_name)) {
            $file_to_include = \k1lib\controllers\load_controller($next_directory_name, $controller_path . $actual_url, $return_non_existent_level, self::$api_mode);
            if (!empty($file_to_include)) {
                return $file_to_include;
            } else {
                if ($return_non_existent_level) {
                    return [$level_name => $next_directory_name];
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        }
    }

}

// ./src/urlrewrite/functions.php


namespace k1lib\urlrewrite;

function get_back_url($get_only = FALSE) {
    // TODO: This is kind of dangerous :( take care!
    if (isset($_GET['back-url'])) {
        $back_url = urldecode($_GET['back-url']);
//        $back_url = \k1lib\forms\check_single_incomming_var($_GET['back-url']);
    } elseif (!$get_only && isset($_SERVER['HTTP_REFERER']) && (!empty($_SERVER['HTTP_REFERER']))) {
        $back_url = $_SERVER['HTTP_REFERER'];
    } elseif (!$get_only && isset($_SESSION['K1APP_LAST_URL']) && (!empty($_SESSION['K1APP_LAST_URL']))) {
        $back_url = $_SESSION['K1APP_LAST_URL'];
    } elseif (!$get_only) {
        $back_url = "javascript:history.back();";
    } else {
        $back_url = FALSE;
    }
    return $back_url;
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


// ./src/utils/functions.php


namespace k1lib\utils;

// recibe 69 y retorna 1Z
function decimal_to_n36($number_to_convert) {
    $num_numbers = strlen((string) $number_to_convert);
    $number_to_convert = (float) $number_to_convert;
    $hexChars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $base = strlen($hexChars);
    $dig = array();
    $i = 0;
    do {
        $dig[$i] = $number_to_convert % $base;
        $number_to_convert = ($number_to_convert - $dig[$i]) / $base;
        $i++;
    } while ($number_to_convert != 0);

    $result = "";
    foreach ($dig as $value) {
        $result.= substr($hexChars, $value, 1);
    }
    return strrev($result);
}

// recibe 1Z y retorna 69
function n36_to_decimal($number_to_convert) {

    $number_to_convert = strtoupper($number_to_convert);

    $dig = array(
        "0" => 0,
        "1" => 1,
        "2" => 2,
        "3" => 3,
        "4" => 4,
        "5" => 5,
        "6" => 6,
        "7" => 7,
        "8" => 8,
        "9" => 9,
        "A" => 10,
        "B" => 11,
        "C" => 12,
        "D" => 13,
        "E" => 14,
        "F" => 15,
        "G" => 16,
        "H" => 17,
        "I" => 18,
        "J" => 19,
        "K" => 20,
        "L" => 21,
        "M" => 22,
        "N" => 23,
        "O" => 24,
        "P" => 25,
        "Q" => 26,
        "R" => 27,
        "S" => 28,
        "T" => 29,
        "U" => 30,
        "V" => 31,
        "W" => 32,
        "X" => 33,
        "Y" => 34,
        "Z" => 35,
    );

    $decimal_number = 0;
    for ($i = 0; $i <= (strlen($number_to_convert) - 1); $i++) {
        $digit_to_convert = substr($number_to_convert, $i, 1);
        $digit_value = $dig[$digit_to_convert];
        $decimal_number = $decimal_number + (($digit_value * (pow(36, (strlen($number_to_convert) - 1)-$i))));
//        \d("$digit_to_convert : (($digit_value * (pow(35, (strlen($number_to_convert) - 1)-$i)))) = " . (($digit_value * (pow(36, (strlen($number_to_convert) - 1)-$i)))));
    }

    return $decimal_number;
}

// ./src/xml/functions.php


namespace k1lib\xml;

function do_xml($data_array, $do_download = false, $file_name = null) {
    $headersCode = "";
    $rowsCode = "";
    $xmlTemplate = <<<HTML
<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:html="http://www.w3.org/TR/REC-html40">
    <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
     <Author>Alejandro Trujillo J.</Author>
     <LastAuthor>Alejandro Trujillo J.</LastAuthor>
     <Created>2019-11-07T06:25:24Z</Created>
     <Company>Klan1 Network</Company>
     <Version>16.00</Version>
    </DocumentProperties>
    <OfficeDocumentSettings xmlns="urn:schemas-microsoft-com:office:office">
     <AllowPNG/>
    </OfficeDocumentSettings>
    <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
     <WindowHeight>17440</WindowHeight>
     <WindowWidth>28040</WindowWidth>
     <WindowTopX>7580</WindowTopX>
     <WindowTopY>29460</WindowTopY>
     <ProtectStructure>False</ProtectStructure>
     <ProtectWindows>False</ProtectWindows>
    </ExcelWorkbook>
    <Styles>
     <Style ss:ID="Default" ss:Name="Normal">
      <Alignment ss:Vertical="Bottom"/>
      <Borders/>
      <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#000000"/>
      <Interior/>
      <NumberFormat/>
      <Protection/>
     </Style>
     <Style ss:ID="s62">
      <Alignment ss:Horizontal="Center" ss:Vertical="Bottom"/>
      <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="12" ss:Color="#FFFFFF"
       ss:Bold="1"/>
      <Interior ss:Color="#305496" ss:Pattern="Solid"/>
     </Style>
    </Styles>
    <Worksheet ss:Name="Names">
        <Table>
          %Headers%
          %DataRows%
        </Table>
        <WorksheetOptions 
          xmlns="urn:schemas-microsoft-com:office:excel">
          <Print>
              <ValidPrinterInfo/>
              <HorizontalResolution>300</HorizontalResolution>
              <VerticalResolution>300</VerticalResolution>
          </Print>
          <Selected/>
          <Panes>
              <Pane>
                  <Number>3</Number>
                  <ActiveRow>1</ActiveRow>
              </Pane>
          </Panes>
          <ProtectObjects>False</ProtectObjects>
          <ProtectScenarios>False</ProtectScenarios>
        </WorksheetOptions>
   </Worksheet>
</Workbook>
HTML;
    $numCols = 0;
    $numRows = 0;
    foreach ($data_array as $rowNumber => $rowData) {
        if ($rowNumber == 0) {
            $headersCode .= "\t<Row ss:AutoFitHeight=\"0\">\n";
            foreach ($rowData as $headerName) {
                $headersCode .= "\t\t<Cell ss:StyleID=\"s62\">";
                $headersCode .= "<Data ss:Type=\"String\">{$headerName}</Data>";
                $headersCode .= "</Cell>\n";
                $numCols++;
            }
            $headersCode .= "\t</Row>\n";
            $numRows++;
        } else {
            $rowsCode .= "\t<Row ss:AutoFitHeight=\"0\">\n";
            foreach ($rowData as $dataValue) {
                $rowsCode .= "\t\t<Cell>";
                if (is_numeric($dataValue)) {
                    $rowsCode .= "<Data ss:Type=\"Number\">{$dataValue}</Data>";
                } else {
                    $rowsCode .= "<Data ss:Type=\"String\">{$dataValue}</Data>";
                }
                $rowsCode .= "\t\t</Cell>\n";
            }
            $rowsCode .= "\t</Row>\n";
            $numRows++;
        }
    }
    $xmlTemplate = str_replace("%Headers%", $headersCode, $xmlTemplate);
    $xmlTemplate = str_replace("%NumCols%", $numCols, $xmlTemplate);
    $xmlTemplate = str_replace("%DataRows%", $rowsCode, $xmlTemplate);
    $xmlTemplate = str_replace("%NumRows%", $numRows, $xmlTemplate);

    if ($do_download) {
        ob_clean();
        header('Content-Description: XML document download');
        header('Cache-Control: public');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; charset=utf-8');
//        header("Content-type: text/plain; charset=utf-8\r\n");
//        header("Content-Transfer-Encoding: 8bit");
        header('Content-Disposition: attachment; filename=' . (empty($file_name) ? 'xml_report.xml' : $file_name));
//        header('Content-Length: ' . mb_strlen($xmlTemplate, '8bit'));
        flush();
        echo $xmlTemplate;
        die();
    } else {
        return $xmlTemplate;
    }
}
