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

const K1LIB_API_USE_MAGIC_HEADER = TRUE;
const K1LIB_API_DISABLE_MAGIC_HEADER = FALSE;
const K1LIB_API_USE_TOKEN = TRUE;
const K1LIB_API_DISABLE_TOKEN = FALSE;

class api {

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
    protected $read_input_return_array = TRUE;
    protected $reponse_data = [];

    public function __construct($use_magic_header = FALSE, $use_token = FALSE) {
        ob_end_clean();
        $this->use_magic_header = $use_magic_header;
        $this->use_token = $use_token;

        $this->request_method = $_SERVER['REQUEST_METHOD'];
    }

    public function exec() {
        // Clear all possible previous output buffer
        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $this->get();
                break;
            case 'POST':
                $this->post();
                break;
            case 'PUT':
                $this->put();
                break;
            case 'DELETE':
                $this->delete();
                break;
        }
    }

    protected function read_input() {
        $input_data = json_decode(file_get_contents("php://input"), $this->read_input_return_array);
        if ($input_data) {
            /**
             * TODO: implements token
             */
            if ($this->use_token) {
                $this->token = (isset($_GET['token']) ? \k1lib\forms\check_single_incomming_var($_GET['token']) : NULL);
//                unset($input_data['token']);
                if (empty($this->token)) {
                    $this->send_response(401, [], ['message' => 'Invalid token']);
                    return FALSE;
                }
            }
            /**
             * TODO: implements the magic header
             */
            if ($this->use_magic_header) {
                $http_headers = getallheaders();
                $this->magic_header = $http_headers['X-Magic-Value'];
                if (empty($this->magic_header)) {
                    $this->send_response(401, [], ['message' => 'Invalid Magic Header', $http_headers]);
                    return FALSE;
                }
            }
            // IF EVERYTHING ITS OK, THEN LOAD THE DATA
            $this->input_data = $input_data;
        }
    }

    public function get_input() {
        return $this->input_data;
    }

    public function send_response($code, $data, $error = null) {
        if ($code >= 200 && $code <= 299) {
            $this->reponse_data['status'] = 'success';
        } elseif ($code >= 400) {
            $this->reponse_data['status'] = 'error';
        }
        $this->reponse_data['data'] = $data;
        if ($error) {
            $this->reponse_data['extra'] = $error;
        }
        http_response_code($code);
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=utf-8");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Last-Modified: {now} GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        echo json_encode($this->reponse_data);
        exit;
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

}
