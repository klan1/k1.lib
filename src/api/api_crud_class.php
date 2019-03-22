<?php

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
    private $controler_action = NULL;
    private $get_list_page = 1;
    private $get_list_page_size = 20;
    private $get_query_filter = [];

    /**
     * @var array
     */
    private $register_response_data = [];

    function __construct($use_token = FALSE, $use_magic_header = FALSE) {
        parent::__construct($use_token, $use_magic_header);

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
        /**
         * POSSIBLE GETS
         */
        if (array_key_exists('page', $_GET)) {
            $this->get_list_page = \k1lib\forms\check_single_incomming_var($_GET['page']);
        }
        if (array_key_exists('page_size', $_GET)) {
            $this->get_list_page_size = \k1lib\forms\check_single_incomming_var($_GET['page_size']);
        }
        if (array_key_exists('get_query_filter', $_GET)) {
            $this->get_query_filter = json_decode(\k1lib\forms\check_single_incomming_var($_GET['page_size']), TRUE);
        }
    }

    function get() {
        parent::get();
        $custom_key_array = [];
        if (!empty($this->data_key)) {
            $data_keys_array = explode('-', $this->data_key);
            foreach ($data_keys_array as $key => $value) {
                $custom_key_array[$this->db_table_keys_fields[$key]] = $value;
            }
        }
        switch ($this->controler_action) {
            case 'get-one':
                $table_data = $this->table_model->get_data($custom_key_array);
                $extra_data = ['data-type' => 'single', $custom_key_array];
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
                $query_filter = array_merge($custom_key_array, $this->get_query_filter);
                $table_data = $this->table_model->get_all_data($this->get_list_page, $this->get_list_page_size, $query_filter);
                $extra_data = [
                    'data-type' => 'multiple',
                    'pagination_url' => ['previos' => $previuos_page, 'next' => $next_page],
                    'pagination_data' => ['previos_page' => $previuos_page_num, 'next_page' => $next_page_num, 'page_size' => $this->get_list_page_size],
                    $custom_key_array
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
        $this->db_table_keys_fields = $db_table_keys_fields;
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
    }

    function set_db_table_name($db_table_name) {
        $this->db_table_name = $db_table_name;
        $this->db_table = new class_db_table($this->db, $this->db_table_name);
        $this->table_model = new api_model($this->db_table);
    }

    function exec($send_response = TRUE) {
        return parent::exec($send_response);
    }

}
