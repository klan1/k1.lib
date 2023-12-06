<?php

namespace k1lib\api;

use \k1lib\api\api;
use \k1lib\urlrewrite\url;
use \k1lib\crudlexs\class_db_table;
use \k1lib\api\model;

class crud extends api {

    /**
     * @var \k1lib\api\model
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
//        $this->table_model = new model($this->db_table, $this->input_data);
        $this->table_model = new model($this->db_table);
    }

    function exec($send_response = TRUE) {
        return parent::exec($send_response);
    }

}
