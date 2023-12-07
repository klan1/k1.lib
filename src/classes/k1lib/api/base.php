<?php

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
use \k1lib\crudlexs\db_table;

const K1LIB_API_USE_MAGIC_HEADER = TRUE;
const K1LIB_API_DISABLE_MAGIC_HEADER = FALSE;
const K1LIB_API_USE_TOKEN = TRUE;
const K1LIB_API_DISABLE_TOKEN = FALSE;

class base {

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
     * @var \k1lib\crudlexs\db_table
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
            $this->debug_table = new db_table($this->db, $table_name);
        }
    }

    function set_do_send_response($send_response) {
        $this->send_response = $send_response;
    }

}
