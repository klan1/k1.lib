<?php

class net_klan1_dev_dbTableClass {

    public $db;
    private $tableName = "";
    private $table_config_array = "";
    private $table_label_field = "";

    public function __construct(PDO &$db, $tableName) {

        // Check DB Type
        if (get_class($db) == "PDO") {
            $this->db = $db;
        } else {
            die("\$db is not a PDO object type - called from: " . __CLASS__);
        };

        // check $tableName type
        if (is_string($tableName)) {
            $this->tableName = $tableName;
        } else {
            die("The table name has to be a String");
        }

        $this->table_config_array = $this->_getTableConfig($tableName);
        $this->table_label_field = $this->_getTableLabel($this->table_config_array);
    }

    public function getTableLabel() {
        return $this->$table_label_field;
    }

    public static function _getTableLabel(&$table_config_array) {
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

    public function getTableConfig() {
        return $this->table_config_array;
    }

    public function _getTableConfig($table, $recursion = 1) {
        // SQL to get info about a table
        $columns_info_query = "SHOW FULL COLUMNS FROM {$table}";
        $columns_info_result = k1_sql_query($this->db, $columns_info_query, true);
        if (empty($columns_info_query)) {
            die(__FUNCTION__ . ": The table '$table' do not exist");
        }
        $dsn_db = k1_get_db_database_name($this->db);
        $INFORMATION_SCHEMA_query = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '{$dsn_db}'
AND table_name = '{$table}'";
        $INFORMATION_SCHEMA_result = k1_sql_query($this->db, $INFORMATION_SCHEMA_query, true);
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
                if ($key == "null") {
                    $value = ($value == "yes") ? true : false;
                }
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
            }
//            d($field_row);
            //// $field_row
            // FOREIGN KEYS
            foreach ($INFORMATION_SCHEMA_result as $info_row) {
                if (!empty($info_row['POSITION_IN_UNIQUE_CONSTRAINT']) && ($info_row['COLUMN_NAME'] == $field_name)) {
                    $field_row ['refereced_table_name'] = $info_row['REFERENCED_TABLE_NAME'];
                    $field_row ['refereced_column_name'] = $info_row['REFERENCED_COLUMN_NAME'];
                    if ($recursion > 0) {
                        $referenced_table_config = $this->_getTableConfig($info_row['REFERENCED_TABLE_NAME'], $recursion - 1);
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
            // show section
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

}
