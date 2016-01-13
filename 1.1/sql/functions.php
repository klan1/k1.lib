<?php

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
function get_db_table_config(\PDO $db, $table, $recursion = 1) {

// SQL to get info about a table
    $columns_info_query = "SHOW FULL COLUMNS FROM {$table}";
    $columns_info_result = sql_query($db, $columns_info_query, TRUE);
    if (empty($columns_info_query)) {
        trigger_error("The table '$table' do not exist", E_USER_WARNING);
        return FALSE;
    }
    $dsn_db = get_db_database_name($db);
    $INFORMATION_SCHEMA_query = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '{$dsn_db}'
AND table_name = '{$table}'";
    $INFORMATION_SCHEMA_result = sql_query($db, $INFORMATION_SCHEMA_query, TRUE);
    if (empty($INFORMATION_SCHEMA_result)) {
        trigger_error("The table '$table' do not exist", E_USER_WARNING);
        return FALSE;
    }

    $config_array = array();
// run through the result and covert into an array that works for K1.lib
    foreach ($columns_info_result as $field_row) {
        $field_name = $field_row['Field'];
//        unset($field_row['Field']);
        /*
         * SEE EACH VALUE OF $field_row to build a new $key => $value and compile the COMMENT field from the table
         */
        foreach ($field_row as $key => $value) {
// LOWER the $key and $value to AVOID problems
            $key_original = $key;
            $key = strtolower($key);
            $value = strtolower($value);
// create a new pair of data but lowered
            $field_row[$key] = $value;
            /*
             * COMPILE THE COMMENT VALUES TO GET A NEW PAIR OF DATA ON $field_row
             * EACH PARAMETER IS SEPARATED WITH ,
             * PARAMETER AND VALUE SEPARATED :
             * parameter1:value1,...,parameterN:valueN
             */
            if ($key == "comment") {
                if (!empty($value) && (strstr($value, ":") !== FALSE)) {
                    $parameters = explode(",", $field_row['Comment']);
                    if (count($parameters) != 0) {
                        foreach ($parameters as $parameter_value) {
                            list($attrib, $attrib_value) = explode(":", $parameter_value);
                            $field_row[$attrib] = trim($attrib_value);
                            $key = trim($attrib);
                        }
                    }
                }
            }
//then delete the original pair of data
            unset($field_row[$key_original]);
        } // $field_row
// FOREIGN KEYS
        foreach ($INFORMATION_SCHEMA_result as $info_row) {
            if (!empty($info_row['POSITION_IN_UNIQUE_CONSTRAINT']) && ($info_row['COLUMN_NAME'] == $field_name)) {
                $field_row ['refereced_table_name'] = $info_row['REFERENCED_TABLE_NAME'];
                $field_row ['refereced_column_name'] = $info_row['REFERENCED_COLUMN_NAME'];
                if ($recursion > 0) {
                    $referenced_table_config = get_db_table_config($db, $info_row['REFERENCED_TABLE_NAME'], $recursion - 1);
                    $field_row ['refereced_column_config'] = $referenced_table_config[$info_row['REFERENCED_COLUMN_NAME']];
                } else {
                    $field_row ['refereced_column_config'] = FALSE;
                }
                break;
            } else {
                $field_row ['refereced_table_name'] = FALSE;
                $field_row ['refereced_column_name'] = FALSE;
                $field_row ['refereced_column_config'] = FALSE;
            }
        }
        /*
         * DEFAULTS TAKE CARE !!
         */
// MAX - THIS ONE IS ALWAYS AUTO GENERATED FROM FIELD DEFINITION
        $field_type = $field_row['type'];
// manage the unsigned
        if (strstr($field_type, "unsigned") !== FALSE) {
            $field_type = str_replace(" unsigned", "", $field_type);
            $field_row['unsigned'] = TRUE;
        } else {
            $field_row['unsigned'] = FALSE;
        }
        if (strstr($field_type, "(") !== FALSE) {
// extract the number from type definition
            list($field_type, $max_legth) = explode("(", $field_type);
            if (!empty($max_legth)) {
                $max_legth = substr($max_legth, 0, -1);
                if (strstr($max_legth, ",") !== FALSE) {
                    list($number, $decimal) = explode(",", $max_legth);
                    $max_legth = $number + $decimal;
                }
            }
        } else {
            $mysql_max_length_defaults = array(
                'char' => 255,
                'varchar' => 255,
                'text' => 9999,
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
        $field_row['type'] = $field_type;
        $field_row['max'] = $max_legth;

// TYPE VALIDATION
        $mysql_default_validation = array(
            'char' => 'mixed-simbols',
            'varchar' => 'mixed-simbols',
            'text' => 'mixed-simbols',
            'date' => 'date',
            'time' => 'time',
            'datetime' => 'datetime',
            'timestamp' => 'datetime',
            'tinyint' => 'numbers',
            'smallint' => 'numbers',
            'mediumint' => 'numbers',
            'int' => 'numbers',
            'bigint' => 'numbers',
            'float' => 'decimals',
            'double' => 'numbers',
            'enum' => 'options',
            'set' => 'options',
        );
        if (!isset($field_row['validation'])) {
            $field_row['validation'] = $mysql_default_validation[$field_row['type']];
        }


//ROW attrib_value FIXES
// yes -> TRUE no -> FALSE
        foreach ($field_row as $key => $value) {
//            d("$key -> $value");
            if ($field_row[$key] == "yes") {
                $field_row[$key] = TRUE;
            } elseif ($field_row[$key] == "no") {
                $field_row[$key] = FALSE;
            }
        }
// IF no label so capitalize the FIELD NAME
        if (!isset($field_row['label'])) {
            $field_row['label'] = strtoupper(substr($field_name, 0, 1)) . (substr($field_name, 1));
        }
        if (!isset($field_row['min'])) {
            $field_row['min'] = FALSE;
            /**
             * TODO: Make option system for this
             */
//            $field_row['min'] = defined(DB_MIN_FIELD_LENGTH) ? DB_MIN_FIELD_LENGTH : FALSE;
        }
// REQUIRED-FIELD
        if ($field_row['null'] === TRUE) {
            $field_row['required'] = FALSE;
        } else {
            $field_row['required'] = TRUE;
        }
// LABEL-FIELD
        if (!isset($field_row['label-field'])) {
            $field_row['label-field'] = FALSE;
        }
// LINK-FIELD
        if (!isset($field_row['link-field'])) {
            $field_row['link-field'] = FALSE;
        }
// ALIAS-FIELD
        if (!isset($field_row['alias'])) {
            $field_row['alias'] = FALSE;
        }
// show board
        $show_array_attribs[] = 'show-table';
        $show_array_attribs[] = 'show-new';
        $show_array_attribs[] = 'show-edit';
        $show_array_attribs[] = 'show-view';
//there is not show-all defined
        if (!isset($field_row['show-all'])) {
            //FIX: should be FALSE
            $field_row['show-all'] = TRUE;
        }
        foreach ($show_array_attribs as $show_attrib) {
            if (!isset($field_row[$show_attrib])) {
                if ($field_row['show-all']) {
                    $field_row[$show_attrib] = TRUE;
                } else {
                    $field_row[$show_attrib] = FALSE;
                }
            }
        }
//table name for each one, yes! repetitive, but necesary in some cases where i dnot receive the table name !!
        $field_row['table'] = $table;

// ENUM FIX
        if ($field_row['type'] == "enum") {
            $field_row['min'] = 1;
            $field_row['max'] = 999;
        }

// SQL for selects
        $field_row['sql'] = "";

// use the actual cycle data
        $config_array[$field_name] = $field_row;
    }
    return $config_array;
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
function sql_query(\PDO $db, $sql, $return_all = TRUE, $do_fields = FALSE, $use_cache = TRUE) {
//$query_result = new PDOStatement();
    $queryReturn = NULL;
    if (profiler::is_enabled()) {
        $sql_profile_id = profiler::add($sql);
        profiler::start_time_count($sql_profile_id);
    }
    if (($use_cache) && (local_cache::is_enabled()) && (local_cache::is_cached($sql))) {
        $queryReturn = local_cache::get_result($sql);
        profiler::set_is_cached($sql_profile_id, TRUE);
    } else {
        profiler::set_is_cached($sql_profile_id, FALSE);
        $query_result = $db->query($sql);
    }
    if (profiler::is_enabled()) {
        profiler::stop_time_count($sql_profile_id);
    }

    if (!empty($queryReturn)) {
        return $queryReturn;
    }
    $fields = array();
    $i = 1;
    if ($query_result !== FALSE) {
        if ($query_result->rowCount() > 0) {
            while ($row = $query_result->fetch(\PDO::FETCH_ASSOC)) {
                if ($do_fields && $return_all) {
                    foreach ($row as $key => $value) {
                        $fields[] = $key;
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
//                        $k1_sql_cache[$sql_md5] = $queryReturn;
                    }
                    return $queryReturn;
                } else {
                    if (\k1app\APP_MODE == "web") {
                        local_cache::add($sql, $queryReturn[1]);
//                        $k1_sql_cache[$sql_md5] = $queryReturn[1];
                    }
                    return $queryReturn[1];
                }
            } else {
//                d($sql);
            }
        } else {
            return NULL;
        }
    } else {
        return FALSE;
    }
}

function sql_update(\PDO $db, $table, $data, $table_keys = array(), $db_table_config = array()) {
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

    if (\k1lib\db\handler::is_enabled()) {
        if (is_array($data)) {
            if (!is_array(@$data[0])) {
                if (empty($db_table_config)) {
                    $db_table_config = get_db_table_config($db, $table);
                }
                if (empty($table_keys)) {
                    $keys_where_condition = table_keys_to_where_condition($data, $db_table_config);
                } else {
                    $keys_where_condition = table_keys_to_where_condition($table_keys, $db_table_config);
                }
                $data_string = array_to_sql_set($data);
                $update_sql = "UPDATE $table SET $data_string WHERE $keys_where_condition;";
//                $controller_errors[] = $update_sql;
//                $controller_errors[] = print_r($data, TRUE);
            } else {
                die(__FUNCTION__ . ": only can work with a 1 dimension array");
            }
//show-message($insert_sql);
            $update = $db->exec($update_sql);
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

function sql_insert(\PDO $db, $table, $data) {
    if (\k1lib\db\handler::is_enabled()) {
        if (is_array($data)) {
            if (!@is_array($data[0])) {
                $data_string = array_to_sql_set($data);
                $insert_sql = "INSERT INTO $table SET $data_string;";
            } else {
                $data_string = array_to_sql_values($data);
                $insert_sql = "INSERT INTO $table $data_string;";
            }
            $insert = $db->exec($insert_sql) or ( trigger_error("Error on Insert stament : " . $db->errorInfo(), E_USER_WARNING));
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
        \trigger_error("This App do not support databases", E_USER_ERROR);
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
            \trigger_error("wrong format in array", E_USER_ERROR);
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
                \trigger_error("wrong values count of array" . print_r($array, true), E_USER_ERROR);
                exit();
            }
        }
// join to return
        return $data_string;
    } else {
        trigger_error("Bad formated array in " . __FUNCTION__, E_USER_ERROR);
        exit();
    }
}

/**
 * Convert an ARRAY to SQL SET pairs
 * @param Array $array Array to convert
 * @param Bolean $use_nulls If should keep the null data, otherwise those will be skiped
 * @param Bolean $for_where_stament If TRUE will join the pairs with AND, if not, will use coma instead
 * @return type
 */
function array_to_sql_set($array, $use_nulls = true, $for_where_stament = FALSE, $precise = TRUE) {
    if (is_array($array) && (count($array) >= 1)) {
        $first = TRUE;
        $data_string = "";
        foreach ($array as $field => $value) {
            if ($use_nulls === FALSE && $value === NULL) {
                continue;
            }

            //put the , to the string
            if (!$first) {
                if ($for_where_stament) {
                    $data_string .= " AND ";
                } else {
                    $data_string .= ", ";
                }
            } else {
                $first = FALSE;
            }
            $field = trim($field);
            $value = \k1lib\forms\check_single_incomming_var($value);
            if ($precise) {
                if ($value === NULL) {
                    $data_string .= "`$field` = NULL";
                } elseif (!is_int($value) && !is_float($value)) {
                    $data_string .= "`$field` = '{$value}'";
                } else {
                    $data_string .= "`{$field}` = {$value}";
                }
            } else {
                $data_string .= "`{$field}` LIKE '%{$value}%'";
            }
        }
    } else {
        trigger_error("Bad formated array in " . __FUNCTION__, E_USER_ERROR);
        exit();
    }
    return $data_string;
}

function get_tables_config_from_sql($sqlQuery) {
    global $db;
    $sqlQuery = "EXPLAIN " . $sqlQuery;
    $explainResult = sql_query($db, $sqlQuery, TRUE);
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
function get_db_table_label_field($db_table_config, $position = 1) {
    if (!is_array($db_table_config)) {
        die(__FUNCTION__ . ": need an array to work on \$db_table_config");
    }
    $p = 0;
    if ($position == -1) {
        $db_table_config = array_reverse($db_table_config);
        $position = 1;
    }
    foreach ($db_table_config as $field => $config) {
        if (($config['label-field'])) {
            $p++;
            if ($p == $position) {
                return $field;
            }
        }
    }
    return NULL;
}

function get_db_table_labels(&$array_data, $db_table_config) {
    
}

function get_fk_field_label($table_name, $url_key_array = Array(), $last_position = 1) {
    if (!is_string($table_name)) {
        trigger_error("\$table_name must to be a String", E_USER_ERROR);
    }
    if (!is_array($url_key_array)) {
        trigger_error("Need an array to work on \$url_key_array", E_USER_ERROR);
    }
    global $db;
    $fkTableConfig = get_db_table_config($db, $table_name);
    $fkTableLabelField = get_db_table_label_field($fkTableConfig, $last_position);

    if (!empty($fkTableLabelField)) {
        $fkWhereCondition = table_keys_to_where_condition($url_key_array, $fkTableConfig);
        $fkSqlQuery = "SELECT {$fkTableLabelField} FROM $table_name WHERE $fkWhereCondition";
        $sql_result = sql_query($db, $fkSqlQuery, FALSE);
        return $sql_result[$fkTableLabelField];
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
function table_keys_to_text(&$row_data, $db_table_config) {
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

function get_keys_array_from_row_data(&$row_data, $db_table_config) {
    $key_fields_array = get_db_table_keys($db_table_config);
    $keys_array = \k1lib\common\clean_array_with_guide($row_data, $key_fields_array);
    if (!empty($keys_array)) {
        return $keys_array;
    } else {
        return false;
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
//    d($db_table_config);
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
    $key_sql_set = array_to_sql_set($key_array, TRUE, TRUE);
    $sql = "DELETE FROM `$table` WHERE $key_sql_set";
    if ($db->exec($sql) !== FALSE) {
        return TRUE;
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
    foreach (explode(',', $matches[1]) as $value) {
        $enum[trim($value, "'")] = trim($value, "'");
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
