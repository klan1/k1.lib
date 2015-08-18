<?php

/*
 * Autor: Alejandro Trujillo J.
 * Copyright: Klan1 Network - 2010
 *
 * TODO: Make sql functions recognition into the sql builders
 *
 */

function k1_get_tables_config_from_sql($sqlQuery) {
    global $db;
    $sqlQuery = "EXPLAIN " . $sqlQuery;
    $explainResult = k1_sql_query($db, $sqlQuery, true);
    if ($explainResult) {
        $presentTablesArray = Array();
        $tableConfig = null;
        foreach ($explainResult as $row) {
            if (isset($row['table']) && (!empty($row['table'])) && (!strstr($row['table'], '<')) && ($row['select_type'] != 'DEPENDENT SUBQUERY')) {
                $tableConfig = k1_get_table_config($db, $row['table']);
                if (!empty($tableConfig)) {
                    $presentTablesArray = array_merge($presentTablesArray, $tableConfig);
                }
            }
        }
        if (!empty($presentTablesArray)) {
            return $presentTablesArray;
        } else {
            return null;
        }
    } else {
        return null;
    }
}

/**
 * Get the DABASE on USE for a PDO connection
 * @param PDO $db
 * @return string Database name or FALSE on error
 */
function k1_get_db_database_name($db) {
    k1_db_check_object_type($db, __FUNCTION__);

    $db_name_sql = "SELECT DATABASE() as DB_NAME;";
    $result = k1_sql_query($db, $db_name_sql, false);
    if (isset($result['DB_NAME'])) {
        return $result['DB_NAME'];
    } else {
        return false;
    }
}

/**
 * Check if the recieved var $db is a PDO object. On error the entire sofware will DIE
 * @param PDO $db
 * @param string $caller
 */
function k1_db_check_object_type($db, $caller = "") {
    if (get_class($db) != "PDO") {
        die(__FUNCTION__ . ": \$db is not a PDO object type" . (($caller != "") ? " - called from: $caller" : "" ));
    }
}

/**
 * Get from a DB Table the config matrix for the K1 Function and Objects related
 * @param PDO $db
 * @param array $table
 * @return array
 */
function k1_get_table_config(PDO $db, $table, $recursion = 1) {
    k1_db_check_object_type($db, __FUNCTION__);
    // SQL to get info about a table
    $columns_info_query = "SHOW FULL COLUMNS FROM {$table}";
    $columns_info_result = k1_sql_query($db, $columns_info_query, true);
    if (empty($columns_info_query)) {
        die(__FUNCTION__ . ": The table '$table' do not exist");
    }
    $dsn_db = k1_get_db_database_name($db);
    $INFORMATION_SCHEMA_query = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '{$dsn_db}'
AND table_name = '{$table}'";
    $INFORMATION_SCHEMA_result = k1_sql_query($db, $INFORMATION_SCHEMA_query, true);
    if (empty($INFORMATION_SCHEMA_result)) {
        die(__FUNCTION__ . ": The table '$table' do not exist - $INFORMATION_SCHEMA_query");
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
                if (!empty($value) && (strstr($value, ":") !== false)) {
                    $parameters = explode(",", $field_row['Comment']);
                    if (count($parameters) != 0) {
                        foreach ($parameters as $parameter_value) {
                            list($attrib, $attrib_value) = explode(":", $parameter_value);
                            $field_row[$attrib] = $attrib_value;
                            $key = $attrib;
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
                    $referenced_table_config = k1_get_table_config($db, $info_row['REFERENCED_TABLE_NAME'], $recursion - 1);
                    $field_row ['refereced_column_config'] = $referenced_table_config[$info_row['REFERENCED_COLUMN_NAME']];
                } else {
                    $field_row ['refereced_column_config'] = false;
                }
                break;
            } else {
                $field_row ['refereced_table_name'] = false;
                $field_row ['refereced_column_name'] = false;
                $field_row ['refereced_column_config'] = false;
            }
        }
        /*
         * DEFAULTS TAKE CARE !!
         */
        // MAX - THIS ONE IS ALWAYS AUTO GENERATED FROM FIELD DEFINITION
        $field_type = $field_row['type'];
        // manage the unsigned
        if (strstr($field_type, "unsigned") !== false) {
            $field_type = str_replace(" unsigned", "", $field_type);
            $field_row['unsigned'] = true;
        } else {
            $field_row['unsigned'] = false;
        }
        if (strstr($field_type, "(") !== false) {
            // extract the number from type definition
            list($field_type, $max_legth) = explode("(", $field_type);
            if (!empty($max_legth)) {
                $max_legth = substr($max_legth, 0, -1);
                if (strstr($max_legth, ",") !== false) {
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
                'enum' => null,
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
        );
        if (!isset($field_row['validation'])) {
            $field_row['validation'] = $mysql_default_validation[$field_row['type']];
        }


        //ROW attrib_value FIXES
        // yes -> true no -> false
        foreach ($field_row as $key => $value) {
//            d("$key -> $value");
            if ($field_row[$key] == "yes") {
                $field_row[$key] = true;
            } elseif ($field_row[$key] == "no") {
                $field_row[$key] = false;
            }
        }
        // IF no label so capitalize the FIELD NAME
        if (!isset($field_row['label'])) {
            $field_row['label'] = strtoupper(substr($field_name, 0, 1)) . (substr($field_name, 1));
        }
        if (!isset($field_row['min'])) {
            $field_row['min'] = defined(DB_MIN_FIELD_LENGTH) ? DB_MIN_FIELD_LENGTH : false;
        }
        // LABEL-FIELD
        if (!isset($field_row['label-field'])) {
            $field_row['label-field'] = false;
        }
        // LINK-FIELD
        if (!isset($field_row['link-field'])) {
            $field_row['link-field'] = false;
        }
        // show board
        $show_array_attribs[] = 'show-table';
        $show_array_attribs[] = 'show-new';
        $show_array_attribs[] = 'show-edit';
        $show_array_attribs[] = 'show-view';
        //there is not show-all defined
        if (!isset($field_row['show-all'])) {
            $field_row['show-all'] = true;
        }
        foreach ($show_array_attribs as $show_attrib) {
            if (!isset($field_row[$show_attrib])) {
                if ($field_row['show-all']) {
                    $field_row[$show_attrib] = true;
                } else {
                    $field_row[$show_attrib] = false;
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

function k1_get_table_keys(&$table_config_array) {
    if (!is_array($table_config_array)) {
        die(__FUNCTION__ . ": need an array to work on \$table_config_array");
    }
    $keys = array();
    foreach ($table_config_array as $field => $config) {
        if ($config['key'] == 'pri') {
            $keys[$field] = 'pri';
        }
    }
    if (empty($keys)) {
        return false;
    } else {
        return $keys;
    }
}

/**
 * From V0.8 depreated no you have to use k1_get_field_label() instead -->> WHHHYYYYY ????
 * @param type $table_config_array
 * @return null
 */
function k1_get_table_label(&$table_config_array) {
//    d("From V0.8 depreated no you have to use k1_get_field_label() instead"); -->> WHHHYYYYY ????
    if (!is_array($table_config_array)) {
        die(__FUNCTION__ . ": need an array to work on \$table_config_array");
    }
    foreach ($table_config_array as $field => $config) {
        if ($config['label-field']) {
            return $field;
        }
    }
    return null;
}

function k1_get_fk_field_label($fkFieldName, $table_name, $url_key_array = Array()) {
    if (!is_string($fkFieldName)) {
        trigger_error(__FUNCTION__ . ": \$fkFieldName must to be a String", E_USER_ERROR);
    }
    if (!is_string($table_name)) {
        trigger_error(__FUNCTION__ . ": \$table_name must to be a String", E_USER_ERROR);
    }
    if (!is_array($url_key_array)) {
        trigger_error(__FUNCTION__ . ": need an array to work on \$url_key_array", E_USER_ERROR);
    }
    global $db;
    $fkTableConfig = k1_get_table_config($db, $table_name);
    $fkTableLabelField = k1_get_table_label($fkTableConfig);

    if (!empty($fkTableLabelField)) {
        $fkWhereCondition = k1_table_keys_to_where_condition($url_key_array, $fkTableConfig);
        $fkSqlQuery = "SELECT {$fkTableLabelField} FROM $table_name WHERE $fkWhereCondition";
        $sql_result = k1_sql_query($db, $fkSqlQuery, false);
        return $sql_result[$fkTableLabelField];
    } else {
        return null;
    }
}

function k1_get_table_refereces(&$table_config_array) {
    if (!is_array($table_config_array)) {
        die(__FUNCTION__ . ": need an array to work on \$table_config_array");
    }
    $keys = array();
    foreach ($table_config_array as $field => $config) {
        if (!empty($config['refereced_table_name'])) {
            $keys[$field]['refereced_table_name'] = $config['refereced_table_name'];
            $keys[$field]['refereced_column_name'] = $config['refereced_column_name'];
            $keys[$field]['refereced_column_config'] = $config['refereced_column_config'];
        }
    }
    if (empty($keys)) {
        return false;
    } else {
        return $keys;
    }
}

function k1_table_keys_to_text(&$row_data, &$table_config_array) {
    if (!is_array($table_config_array)) {
        die(__FUNCTION__ . ": need an array to work on \$table_config_array");
    }
    $table_keys_array = k1_get_table_keys($table_config_array);
    $table_keys_values = array();
    foreach ($row_data as $column_name => $value) {
        if (isset($table_keys_array[$column_name]) && (!empty($table_keys_array[$column_name]))) {
            $table_keys_values[] = $value;
        }
    }
    $table_keys_text = implode("--", $table_keys_values);
    return $table_keys_text;
}

function k1_table_url_text_to_keys($url_text, $table_config_array) {
    if (!is_array($table_config_array)) {
        die(__FUNCTION__ . ": need an array to work on \$table_config_array");
    }
    $url_text_array = explode("--", $url_text);
    $url_text_array_count = count($url_text_array);

    $table_keys_array = k1_get_table_keys($table_config_array);
    $table_keys_count = count($table_keys_array);
    // elements count check
    if ($url_text_array_count != $table_keys_count) {
        die(__FUNCTION__ . ": The count of recived keys as text to not match with the \$table_config_array");
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
    $errors = k1_form_check_values($key_data, $table_config_array);
    if (!empty($errors)) {
        d($key_data);
        d($errors);
        trigger_error(__FUNCTION__ . ": Value types on the received \$url_text do not match with \$table_config_array", E_USER_ERROR);
    }
    return $key_data;
}

function k1_table_keys_to_where_condition(&$row_data, $table_config_array) {
    if (!is_array($table_config_array)) {
        die(__FUNCTION__ . ": need an array to work on \$table_config_array");
    }

    $table_keys_array = k1_get_table_keys($table_config_array);
    if (empty($table_keys_array)) {
        die(__FUNCTION__ . ": The is no PRI on the \$table_config_array");
    }

    $key_values = array();
    foreach ($table_keys_array as $column_name => $noused) {
        if (isset($row_data[$column_name])) {
            $key_values[$column_name] = $row_data[$column_name];
        }
    }

    $first_value = true;
    $where_condition = "";
    foreach ($key_values as $key => $value) {
//        if ($table_config_array[$key]['type'])
        if (!$first_value) {
            $where_condition .= " AND ";
        }
        $where_condition .= "$key = '$value'";
        $first_value = false;
    }
    return $where_condition;
}

function k1_sql_del_row($db, $table, $key_name, $key_value) {
    $sql = "DELETE FROM `$table` WHERE `$key_name` = " . ( is_numeric($key_value) ? $key_value : "'$key_value'") . " ";
    if ($db->exec($sql) !== false) {
        return true;
    } else {
        return false;
    }
}

function k1_sql_check_id($db, $table, $key_name, $key_value, $use_cache = false) {
    k1_db_check_object_type($db, __FUNCTION__);
    $sql = "SELECT COUNT(*) AS num_keys FROM `$table` WHERE `$key_name` = " . ( is_numeric($key_value) ? $key_value : "'$key_value'") . " ";
    if ($use_cache) {
        $sql_count = k1_sql_query_cached($db, $sql, false);
    } else {
        $sql_count = k1_sql_query($db, $sql, false);
    }
    if ($sql_count['num_keys'] > 0) {
        return true;
    } else {
        return false;
    }
}

function k1_sql_value_increment($db, $table, $key_name, $key_value, $field_name, $step = 1) {
    k1_db_check_object_type($db, __FUNCTION__);
    $sql = "SELECT `$field_name` FROM `$table` WHERE `$key_name` = " . ( is_numeric($key_value) ? $key_value : "'$key_value'") . " ";
    if ($sql_result = k1_sql_query($db, $sql, false)) {
        // make the 'step' increment on the field to rise
        $sql_result[$field_name] += $step;
        // add to the data array the key command to use the sql_update function
        $sql_result["$key_name:key"] = $key_value;
        if (k1_sql_update($db, $table, $sql_result)) {
            return $sql_result[$field_name];
        } else {
            return false;
        }
    } else {
        k1_show_error("The value to increment coundn't be query" . __FUNCTION__);
        d($sql);
    }
}

function k1_sql_count($data_array) {
    $count = count($data_array) - 1;
    return ($count >= 0) ? $count : 0;
}

function k1_sql_table_count($db, $table, $condition = "", $use_memcache = false, $expire_time = 60) {
    k1_db_check_object_type($db, __FUNCTION__);
    $sql = "SELECT COUNT(*) AS counted FROM `$table` " . ( ($condition != "") ? "WHERE $condition" : "");
    if ($use_memcache) {
        $result = k1_sql_query_cached($db, $sql, false, false, $expire_time);
    } else {
        $result = k1_sql_query($db, $sql, false);
    }
    if ($result) {
        return $result['counted'];
    } else {
        false;
    }
}

function k1_sql_query_cached($db, $sql, $return_all = true, $do_fields = false, $expiration_time = 1800) {
    k1_db_check_object_type($db, __FUNCTION__);
    global $db_query_cached_total, $db_query_cached_true, $db_query_cached_false, $memcache, $memcache_connected;
    $db_query_cached_total++;
    $sql_md5 = md5($sql);
    if ($memcache_connected) {
        $memcache_result = $memcache->get($sql_md5);
        if (!$memcache_result) {
            $db_query_cached_false++;
            $sql_result = k1_sql_query($db, $sql, $return_all, $do_fields);
            if ($sql_result == null) {
                $sql_result = "r::e";
            }
            $memcache->set($sql_md5, $sql_result, false, $expiration_time) or die("Failed to save data at the server");
        } else {
            $db_query_cached_true++;
            if ($memcache_result != "r::e") {
                $sql_result = $memcache_result;
            } else {
                $sql_result = null;
            }
        }
    } else {
        $sql_result = k1_sql_query($db, $sql, $return_all, $do_fields);
        $db_query_cached_false++;
    }
    return $sql_result;
}

/**
 * 
 * @global type $db_querys
 * @global type $sql_profiles
 * @global type $k1_sql_cache
 * @param type $db
 * @param type $sql
 * @param type $return_all
 * @param boolean $do_fields
 * @return null|boolean PDO::query() returns a PDOStatement object, or FALSE on failure.
 * TODO: Fix the NON optional cache isue !!
 */
function k1_sql_query(PDO $db, $sql, $return_all = true, $do_fields = false) {
    //$query_result = new PDOStatement();
    if (APP_MODE == "web") {
        global $db_querys, $sql_profiles, $k1_sql_cache;
        $db_querys++;
        $sql_md5 = md5($sql);
        $queryReturn = null;
        if (SQL_PROFILE) {
            $sql_profiles[$db_querys]['md5'] = $sql_md5;
            $sql_profiles[$db_querys]['sql'] = $sql;
            $sql_start_time = microtime(true);
        }
        if (isset($k1_sql_cache[$sql_md5]) && (!empty($k1_sql_cache[$sql_md5]))) {
            $queryReturn = $k1_sql_cache[$sql_md5];
            $sql_profiles[$db_querys]['cache'] = "yes";
        } else {
            $sql_profiles[$db_querys]['cache'] = "no";
            $query_result = $db->query($sql) or ( (K1_DEBUG) ? d(print_r($db->errorInfo(), true) . "SQL: $sql") : "SQL Error" );
        }
        if (SQL_PROFILE) {
            $sql_stop_time = microtime(true);
            $sql_profiles[$db_querys]['time'] = ($sql_stop_time - $sql_start_time);
        }

        if (!empty($queryReturn)) {
            return $queryReturn;
        }
    } else {
        $query_result = $db->query($sql) or ( (K1_DEBUG) ? d(print_r($db->errorInfo(), true) . "SQL: $sql") : "SQL Error" );
    }
    $fields = array();
    $i = 1;
    if ($query_result !== false) {
        if ($query_result->rowCount() > 0) {
            while ($row = $query_result->fetch(PDO::FETCH_ASSOC)) {
                if ($do_fields && $return_all) {
                    foreach ($row as $key => $value) {
                        $fields[] = $key;
                    }
                    $do_fields = false;
                    $queryReturn[0] = $fields;
                }
                $queryReturn[$i] = $row;
                $i++;
            }
            if (isset($queryReturn)) {
                if ($return_all) {
                    if (APP_MODE == "web") {
                        $k1_sql_cache[$sql_md5] = $queryReturn;
                    }
                    return $queryReturn;
                } else {
                    if (APP_MODE == "web") {
                        $k1_sql_cache[$sql_md5] = $queryReturn[1];
                    }
                    return $queryReturn[1];
                }
            } else {
//                d($sql);
            }
        } else {
            return null;
        }
    } else {
        return false;
    }
}

function k1_sql_update($db, $table, $data, $table_keys = array(), $table_config_array = array()) {
    global $controller_errors;
    k1_db_check_object_type($db, __FUNCTION__);
    if (!is_string($table) || empty($table)) {
        die(__FUNCTION__ . ": \$table should be an non empty string");
    }
    if (!is_array($data)) {
        die(__FUNCTION__ . ": need an array to work on \$data");
    }
    if (!is_array($table_keys)) {
        die(__FUNCTION__ . ": need an array to work on \$table_keys");
    }
    if (!is_array($table_config_array)) {
        die(__FUNCTION__ . ": need an array to work on \$table_config_array");
    }

    if (USE_DB) {
        if (is_array($data)) {
            if (!is_array(@$data[0])) {
                if (empty($table_config_array)) {
                    $table_config_array = k1_get_table_config($db, $table);
                }
                if (empty($table_keys)) {
                    $keys_where_condition = k1_table_keys_to_where_condition($data, $table_config_array);
                } else {
                    $keys_where_condition = k1_table_keys_to_where_condition($table_keys, $table_config_array);
                }
                $data_string = k1_array_to_sql_set($data);
                $update_sql = "UPDATE $table SET $data_string WHERE $keys_where_condition;";
//                $controller_errors[] = $update_sql;
//                $controller_errors[] = print_r($data, true);
            } else {
                die(__FUNCTION__ . ": only can work with a 1 dimension array");
            }
            //show-message($insert_sql);
            $update = $db->exec($update_sql) or ( k1_show_error($db->errorInfo()));
            if ($update) {
                return $update;
            } else {
                if (K1_DEBUG) {
                    k1_show_error($update_sql);
                }
                return false;
            }
        } else {
            die(__FUNCTION__ . ": has not received an arrany to do his work");
            exit();
        }
    } else {
        k1_show_error("This App do not support databases");
        return FALSE;
    }
}

function k1_sql_insert(PDO $db, $table, $data) {
    /*
     * TODO: make this secure with $data values confirmation through k1_form_check_values()
     */
    /*
     * TODO: make data verification over the foreign keys to show more precise errors
     */
    global $form_errors, $controller_errors;
    if (USE_DB) {
        if (is_array($data)) {
            if (!@is_array($data[0])) {
                $data_string = k1_array_to_sql_set($data);
                $insert_sql = "INSERT INTO $table SET $data_string;";
            } else {
                $data_string = k1_array_to_sql_values($data);
                $insert_sql = "INSERT INTO $table $data_string;";
            }
            $form_errors[] = $insert_sql;
            $insert = $db->exec($insert_sql) or ( $controller_errors[] = ($db->errorInfo()));
            $controller_errors[] = $insert_sql;
            if ($insert) {
                $last_insert_sql = "SELECT LAST_INSERT_ID() as 'LAST_ID'";
                $last_insert_result = k1_sql_query($db, $last_insert_sql, false);
                if (isset($last_insert_result['LAST_ID']) && (!empty($last_insert_result['LAST_ID']))) {
                    return $last_insert_result['LAST_ID'];
                } else {
                    return true;
                }
            } else {
                return false;
            }
        } else {
            die(__FUNCTION__ . ": has not received an arrany to do his work");
            exit();
        }
    } else {
        k1_show_error("This App do not support databases");
        return FALSE;
    }
}

function k1_array_to_sql_values($array) {
    if (is_array($array) && (count($array) > 1)) {
        $first = true;
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
                    $first = false;
                }
                $data_string .= trim($field_name);
            }
            $data_string .= ") VALUES ";
        } else {
            k1_show_error("wrong format in array on " . __FUNCTION__);
        }
// remove the headers to only work with the values - lazzy code :P
        unset($array[0]);
// build the data
        $first_group = true;
        foreach ($array as $values_array) {
            $values_count = count($values_array);
            if (!$first_group) {
                $data_string .= ", ";
            } else {
                $first_group = false;
            }
            if ($values_count == $headers_count) {
                $data_string .= "(";
                $first = true;
                foreach ($values_array as $value) {
                    //put the , to the string
                    if (!$first) {
                        $data_string .= ", ";
                    } else {
                        $first = false;
                    }
                    $value = k1_check_incomming_var($value);
                    $data_string .= ( is_numeric($value) ? $value : "'$value'");
                }
                $data_string .= ") ";
            } else {
                k1_show_error("wrong values count of array on " . __FUNCTION__ . d($array));
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

function k1_array_to_sql_set($array) {
    if (is_array($array) && (count($array) >= 1)) {
        $first = true;
        $data_string = "";
        foreach ($array as $field => $value) {
            // ZERO FIX !!
            if (($value !== 0) && empty($value)) {
                continue;
            }
//put the , to the string
            if (!$first) {
                $data_string .= ", ";
            } else {
                $first = false;
            }
            $field = trim($field);
            $value = k1_check_incomming_var($value);
            if (!is_int($value) && !is_float($value)) {
                $value = "'$value'";
            }
            $data_string .= "`$field` = " . $value;
        }
    } else {
        trigger_error("Bad formated array in " . __FUNCTION__, E_USER_ERROR);
        exit();
    }
    return $data_string;
}

function k1_get_table_enum_values($db, $table, $field) {
    k1_db_check_object_type($db, __FUNCTION__);
    $dsn_db = k1_get_db_database_name($db);
    $enum_sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$dsn_db}' AND TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$field}'";
    $enum_result = k1_sql_query($db, $enum_sql, false);
    $type = $enum_result['COLUMN_TYPE'];
    $matches = array();
    preg_match('/^enum\((.*)\)$/', $type, $matches);
    $i = 0;
    foreach (explode(',', $matches[1]) as $value) {
        $enum[trim($value, "'")] = trim($value, "'");
    }
    return $enum;
}

function k1_table_traduce_enum_to_index($db, &$query_result, &$table_config_array) {
    if (!is_array($query_result)) {
        die(__FUNCTION__ . ": need an array to work on \$query_result");
    }
    if (!is_array($table_config_array)) {
        die(__FUNCTION__ . ": need an array to work on \$table_config_array");
    }
// now go one by one row on the result
    foreach ($query_result as $column => $value) {
        if ($table_config_array[$column]['type'] == 'enum') {
            $enum_values_array = k1_get_table_enum_values($db, $table_config_array[$column]['table'], $column);
            if (count($enum_values_array) > 0) {
                $enum_values_array = array_flip($enum_values_array);
                $query_result[$column] = $enum_values_array[$value];
            }
        }
    }
}
