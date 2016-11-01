<?php

namespace k1lib\crudlexs;

class class_db_table {

    /**
     *
     * @var \PDO
     */
    public $db;
    private $db_table_name = FALSE;
    private $db_table_config = FALSE;
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
     * ORDER BY
     */

    /**
     * @var array
     */
    private $query_order_by_fields_array = [];

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
        if ($reload) {
            $this->db_table_config = $this->_get_db_table_config($this->db_table_name, TRUE, FALSE);
        }
        return $this->db_table_config;
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
                $query_where_pairs = \k1lib\sql\array_to_sql_set($this->db, $clean_filter_array, TRUE, TRUE);
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
                            $query_where_pairs .= " $field LIKE '%{$search_value}%'";
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
        if (($this->query_offset === 0) && ($this->query_row_count_limit > 0)) {
            $sql_code .= "LIMIT {$this->query_row_count_limit} ";
            $sql_code_total_rows .= "LIMIT {$this->query_row_count_limit} ";
        } elseif (($this->query_offset > 0) && ($this->query_row_count_limit > 0)) {
            $sql_code .= "LIMIT {$this->query_offset},{$this->query_row_count_limit} ";
            $sql_code_total_rows .= "LIMIT {$this->query_offset},{$this->query_row_count_limit} ";
        }
        return $sql_code;
    }

    public function generate_sql_query_keys() {
        return $this->generate_sql_query('keys');
    }

    function get_query_sql() {
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
            $sql_last_part = strstr($this->query_sql, "FROM", FALSE);
            $operation_sql = "SELECT {$operation}(`$field`) AS `$field`  {$sql_last_part}";
            d($operation_sql);
            $query_result = \k1lib\sql\sql_query($this->db, $operation_sql, FALSE);

            if (!empty($query_result)) {

                return $query_result[$field];
            } else {
                // EMPTY RESULT TO DO NOT BREAK THE FOREACH LOOPS
                return [];
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
                return $this->total_rows_result['num_rows'];
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

    public function insert_data(array $data_to_insert, &$error_data = NULL) {
        if (empty($data_to_insert)) {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_empty_data_insert, E_USER_WARNING);
            return FALSE;
        }
        $data_to_insert = array_merge($data_to_insert, $this->constant_fields);
        return \k1lib\sql\sql_insert($this->db, $this->db_table_name, $data_to_insert, $error_data);
    }

    /**
     * SQL update method
     * @param array $data_to_update
     * @param array $key_to_update
     * @return boolean
     */
    public function update_data(array $data_to_update, array $key_to_update, &$error_data = NULL) {
        if (empty($data_to_update)) {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_empty_data_update, E_USER_WARNING);
            return FALSE;
        }
        if (empty($key_to_update)) {
            trigger_error(__METHOD__ . ' ' . db_table_strings::$error_empty_data_update_key, E_USER_WARNING);
            return FALSE;
        }
        $data_to_update = array_merge($data_to_update, $this->constant_fields);
        return \k1lib\sql\sql_update($this->db, $this->db_table_name, $data_to_update, $key_to_update, [], $error_data);
    }

    public function delete_data(Array $key_to_delete) {

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

}
