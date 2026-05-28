<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage api
 * CRUD API implementation providing RESTful endpoints for database table operations.
 */

namespace k1lib\api;

use k1lib\api\model;
use k1lib\crudlexs\db_table;
use k1lib\urlrewrite\url;
use function k1lib\forms\check_single_incomming_var;

/**
 * CRUD API controller.
 * Handles GET, POST, PUT, DELETE operations for database tables.
 *
 * @package k1lib\api
 */
class crud extends api {

    /**
     * The data model for table operations.
     * @var model
     */
    private $table_model;

    /**
     * Database table object.
     * @var db_table
     */
    public $db_table;

    /**
     * Database table name.
     * @var string
     */
    private $db_table_name;

    /**
     * Key fields for table operations.
     * @var array
     */
    private $db_table_keys_fields;

    /**
     * Data key for single record operations.
     * @var string|null
     */
    private $data_key = NULL;

    /**
     * Array of data key values.
     * @var array
     */
    private $data_keys_array = [];

    /**
     * Key field data as associative array.
     * @var array
     */
    private $keyfield_data_array = [];

    /**
     * Controller action (get-one, get-all, etc).
     * @var string|null
     */
    private $controler_action = NULL;

    /**
     * Current page number for listing.
     * @var int
     */
    private $get_list_page = 1;

    /**
     * Page size for listing.
     * @var int
     */
    private $get_list_page_size = 20;

    /**
     * Query filter for listing.
     * @var array
     */
    private $get_query_filter = [];

    /**
     * Order by configuration.
     * @var array
     */
    private $orderby = [];

    /**
     * Registered response data.
     * @var array
     */
    private $register_response_data = [];

    /**
     * Creates a CRUD API instance.
     *
     * @param bool $use_token Whether to use token authentication
     * @param bool $use_magic_header Whether to use magic header authentication
     */
    function __construct($use_token = FALSE, $use_magic_header = FALSE) {
        parent::__construct($use_token, $use_magic_header);

        /**
         * POSSIBLE GETS
         */
        if (array_key_exists('page', $_GET)) {
            $this->get_list_page = check_single_incomming_var($_GET['page']);
        }
        if (array_key_exists('page-size', $_GET)) {
            $this->get_list_page_size = check_single_incomming_var($_GET['page-size']);
        }
        if (array_key_exists('get-query-filter', $_GET)) {
            $this->get_query_filter = json_decode(check_single_incomming_var($_GET['get-query-filter'], false, true), TRUE);
        }
        if (array_key_exists('keys-fields', $_GET)) {
            $this->db_table_keys_fields = explode(',', check_single_incomming_var($_GET['keys-fields']));
        }
        if (array_key_exists('order-by', $_GET)) {
            $this->orderby = check_single_incomming_var($_GET['order-by']);
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

    /**
     * Handles POST requests for updating existing records.
     */
    function post(): void {
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

    /**
     * Handles PUT requests for creating new records.
     */
    function put(): void {
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

    /**
     * Handles DELETE requests for removing records.
     */
    function delete(): void {
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

    /**
     * Sets the database table name and initializes the model.
     *
     * @param string $db_table_name The table name
     */
    function set_db_table_name($db_table_name) {
        $this->db_table_name = $db_table_name;
        $this->db_table = new db_table($this->db, $this->db_table_name);
//        echo " | set_db_table_name: " . print_r($this->input_data, TRUE) . " | ";
//        $this->table_model = new model($this->db_table, $this->input_data);
        $this->table_model = new model($this->db_table);
    }

    /**
     * Executes the API request.
     *
     * @param bool $send_response Whether to send response or return data
     * @return mixed
     */
    function exec($send_response = TRUE) {
        return parent::exec($send_response);
    }
}
