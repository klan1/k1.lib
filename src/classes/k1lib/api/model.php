<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * K1 API model - fast api for all
 *
 * PHP version 7.1 - 7.2
 *
 * LICENSE:  
 *
 * @author          Alejandro Trujillo J. <alejo@klan1.com>
 * @copyright       2015-2023 Klan1 Network SAS
 * @license         Apache 2.0
 * @version         1.0
 * @since           File available since Release 0.8
 */
/*
 * App run time vars
 */

namespace k1lib\api;

use k1lib\crudlexs\db_table;

class model {

    /**
     * @var db_table
     */
    private db_table|bool|null $db_table;
    private $errors = FALSE;

    function __construct(db_table|bool|null $db_table = NULL, $data = NULL) {
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
            $data_keys = $this->db_table->db->get_keys_array_from_row_data($data_array, $this->db_table->get_db_table_config());
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
