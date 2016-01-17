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
    private $db_table_show_rule = FALSE;

    /**
     * SQL Values
     */
    private $query_offset = 0;
    private $query_row_count_limit = null;
    private $query_where_pairs = null;
    private $query_sql = null;
    private $query_sql_keys = null;

// Definitions
//    private $mysql_max_length_defaults = array(
//        'char' => 255,
//        'varchar' => 255,
//        'text' => 9999,
//        'date' => 10,
//        'time' => 8,
//        'datetime' => 19,
//        'timestamp' => 19,
//        'tinyint' => 3,
//        'smallint' => 5,
//        'mediumint' => 7,
//        'int' => 10,
//        'bigint' => 19,
//        'float' => 34,
//        'double' => 64,
//        'enum' => NULL,
//    );
//    private $mysql_default_validation = array(
//        'char' => 'mixed-simbols',
//        'varchar' => 'mixed-simbols',
//        'text' => 'mixed-simbols',
//        'date' => 'date',
//        'time' => 'time',
//        'datetime' => 'datetime',
//        'timestamp' => 'datetime',
//        'tinyint' => 'numbers',
//        'smallint' => 'numbers',
//        'mediumint' => 'numbers',
//        'int' => 'numbers',
//        'bigint' => 'numbers',
//        'float' => 'decimals',
//        'double' => 'numbers',
//        'enum' => 'options',
//    );
//    private $show_array_attribs = Array(
//        'show-table',
//        'show-new',
//        'show-edit',
//        'show-view',
//    );

    public function __construct(\PDO $db, $db_table_name) {
        $this->db = $db;
        // check $db_table_name type
        if (is_string($db_table_name)) {
            $this->db_table_name = $db_table_name;
        } else {
            trigger_error("The table name has to be a String", E_USER_ERROR);
        }

        $this->db_table_config = $this->_get_db_table_config($db_table_name);
        if ($this->db_table_config) {
            $this->db_table_label_field = $this->_get_db_table_label_field($this->db_table_config);
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

    function get_db_table_name() {
        return $this->db_table_name;
    }

    public function get_db_table_label_field() {
        return $this->db_table_label_field;
    }

    private static function _get_db_table_label_field(&$db_table_config) {
        return \k1lib\sql\get_db_table_label_field($db_table_config);
    }

    public function get_db_table_config() {
        return $this->db_table_config;
    }

    public function get_db_table_field_config($field) {
        return $this->db_table_config[$field];
    }

    public function get_db_table_field_value_config($field, $config_name) {
        return $this->db_table_config[$field][$config_name];
    }

    private function _get_db_table_config($db_table_name, $recursion = 1) {
        return \k1lib\sql\get_db_table_config($this->db, $db_table_name, $recursion);
    }

    public function set_query_limit($offset = 0, $row_count = null) {
        $this->query_offset = $offset;
        $this->query_row_count_limit = $row_count;
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
            $this->query_where_pairs = "";
            if ($exact_filter) {
                $this->query_where_pairs = \k1lib\sql\array_to_sql_set($clean_filter_array, FALSE, TRUE);
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
                            $this->query_where_pairs .= ($i >= 1) ? " AND " : "";
                            $this->query_where_pairs .= " $field LIKE '%$search_value%'";
                            $i++;
                        }
                    }
                }
            }
            return TRUE;
        }
    }

    public function generate_sql_query_fields_by_rule($rule) {
        $fields_array = [];
        foreach ($this->db_table_config as $field => $config) {
            if (isset($config[$rule]) && $config[$rule]) {
                $fields_array[] = $field;
            }
        }
        if (!empty($fields_array)) {
            return implode(",", $fields_array);
        } else {
            trigger_error("Rule is not avaliable", E_USER_WARNING);
            return FALSE;
        }
    }

    public function generate_sql_query() {
        if (empty($this->db_table_show_rule)) {
            $fields = "*";
        } else {
            $fields = $this->generate_sql_query_fields_by_rule($this->db_table_show_rule);
        }
        if (empty($fields)) {
            return FALSE;
        } else {
            $this->query_sql = "SELECT {$fields} FROM {$this->db_table_name} ";

            if (!empty($this->query_where_pairs)) {
                $this->query_sql .= "WHERE {$this->query_where_pairs} ";
            }
            if (($this->query_offset === 0) && ($this->query_row_count_limit > 0)) {
                $this->query_sql .= "LIMIT {$this->query_row_count_limit} ";
            }
            if (($this->query_offset > 0) && ($this->query_row_count_limit > 0)) {
                $this->query_sql .= "LIMIT {$this->query_offset},{$this->query_row_count_limit} ";
            }
            return $this->query_sql;
        }
    }

    public function generate_sql_query_keys() {
        $db_table_key_fields = \k1lib\sql\get_db_table_keys_array($this->db_table_config);
        if (!empty($db_table_key_fields)) {
            $fields = implode(",", $db_table_key_fields);
        } else {
            return FALSE;
        }
        $this->query_sql_keys = "SELECT {$fields} FROM {$this->db_table_name} ";

        if (!empty($this->query_where_pairs)) {
            $this->query_sql_keys .= "WHERE {$this->query_where_pairs} ";
        }
        if (($this->query_offset === 0) && ($this->query_row_count_limit > 0)) {
            $this->query_sql_keys .= "LIMIT {$this->query_row_count_limit} ";
        }
        if (($this->query_offset > 0) && ($this->query_row_count_limit > 0)) {
            $this->query_sql_keys .= "LIMIT {$this->query_offset},{$this->query_row_count_limit} ";
        }
        return $this->query_sql_keys;
    }

    function get_query_sql() {
        return $this->query_sql;
    }

    public function get_data($return_all = TRUE, $do_fields = TRUE) {
        if ($this->generate_sql_query()) {
            $query_result = \k1lib\sql\sql_query($this->db, $this->query_sql, $return_all, $do_fields);

            if (!empty($query_result)) {
                return $query_result;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function get_data_keys() {
        if ($this->generate_sql_query_keys()) {
            $query_result = \k1lib\sql\sql_query($this->db, $this->query_sql_keys, TRUE, TRUE);
            if (!empty($query_result)) {
                return $query_result;
            } else {
                return FALSE;
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

    public function do_data_validation($data_array_to_validate) {
        $validaton_errors = \k1lib\forms\form_check_values($data_array_to_validate, $this->db_table_config, $this->db);
        if (!is_array($validaton_errors)) {
            return TRUE;
        } else {
            return $validaton_errors;
        }
    }

    public function insert_data(Array $data_to_insert) {
        if (empty($data_to_insert)) {
            trigger_error("Data to insert can't be empty", E_USER_WARNING);
            return FALSE;
        }
        return \k1lib\sql\sql_insert($this->db, $this->db_table_name, $data_to_insert);
    }

    public function update_data(Array $data_to_update, $key_to_update) {
        if (empty($data_to_update)) {
            trigger_error("Data to update can't be empty", E_USER_WARNING);
            return FALSE;
        }
        if (empty($key_to_update)) {
            trigger_error("Key to update can't be empty", E_USER_WARNING);
            return FALSE;
        }
        return \k1lib\sql\sql_update($this->db, $this->db_table_name, $data_to_update, $key_to_update);
    }

    public function delete_data(Array $key_to_delete) {

        if (empty($key_to_delete)) {
            trigger_error("Key to delete can't be empty", E_USER_WARNING);
            return FALSE;
        }
        return \k1lib\sql\sql_del_row($this->db, $this->db_table_name, $key_to_delete);
    }

}
