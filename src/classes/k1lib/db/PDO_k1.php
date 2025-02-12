<?php

/**
 * New DB class to make easier multiple DB connections
 */

namespace k1lib\db;

use k1lib\crudlexs\field_config_json;
use k1lib\K1MAGIC;
use k1lib\sql\local_cache;
use k1lib\sql\profiler;
use PDO;
use PDOException;
use PDOStatement;
use const k1app\K1APP_MODE;
use const k1app\K1APP_VERBOSE;
use function d;
use function k1lib\common\clean_array_with_guide;
use function k1lib\forms\check_single_incomming_var;
use function k1lib\forms\form_check_values;

/**
 * 
 */
class PDO_k1 extends PDO {

    /**
     * Enable state
     * @var bool 
     */
    protected $enabled = FALSE;
    protected $db_dsn;
    protected $db_name;
    protected $db_user;
    protected $db_password;
    protected $db_host;
    protected $db_port;

    /**
     *  Verbose level for error output
     * @var type 
     */
    protected $verbose_level = 0;

    /**
     * Query the enabled state
     * @return Boolean
     */
    public function is_enabled($show_error = false) {
        if ($show_error && (!$this->enabled)) {
            trigger_error("DB system is not enabled yet", E_USER_ERROR);
        }
        return $this->enabled;
    }

    /**
     * Start the engenie
     * @param string $db_name
     * @param string $db_user
     * @param string $db_password
     * @param string $db_host
     * @param integer $db_port
     * @param string $db_type
     */
    public function __construct($db_name, $db_user, $db_password,
            $db_host = "localhost", $db_port = 3306, $db_type = "mysql",
            $pdo_string_altern = FALSE) {
        $this->enabled = TRUE;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
        $this->db_host = $db_host;
        $this->db_port = $db_port;
        if ($pdo_string_altern) {
            $this->db_dsn = "{$db_type}:dbname={$db_name};host={$db_host}:{$db_port}";
        } else {
            $this->db_dsn = "{$db_type}:dbname={$db_name};host={$db_host};port={$db_port}";
        }

        $this->is_enabled(true);
        parent::__construct($this->db_dsn, $this->db_user, $this->db_password);
    }

    function get_verbose_level() {
        $this->is_enabled(true);
        return $this->verbose_level;
    }

    function set_verbose_level($verbose_level) {
        $this->is_enabled(true);
        $this->verbose_level = $verbose_level;
        if ($this->verbose_level == 0) {
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
        } elseif ($this->verbose_level > 0) {
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    function query(string $query, ?int $fetchMode = null, ...$fetchModeArgs): PDOStatement|false {
        try {
            $result = parent::query($query, $fetchMode);
        } catch (PDOException $exc) {
            switch ($this->verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_NOTICE);
                    break;
                case 1:
                    //                    d($statement);
                    trigger_error($exc->getMessage(), E_USER_NOTICE);
                    break;
                case 2:
                    trigger_error($query . " | " . $exc->getMessage(),
                            E_USER_NOTICE);
                    break;
                case 3:
                    trigger_error($query . " | " . $exc->getMessage(),
                            E_USER_NOTICE);
                    d($exc->getTraceAsString());
                    break;
                default:
                    break;
            }
            $result = FALSE;
        }

        return $result;
    }

    /**
     * Execute an SQL statement and return the number of affected rows
     * PDO::exec() executes an SQL statement in a single function call, returning the number of rows affected by the statement.
     *
     * @param string $statement The SQL statement to prepare and execute. Data inside the query should be properly escaped .
     * @return bool|int PDO::exec() returns the number of rows that were modified or deleted by the SQL statement you issued. If no rows were affected, PDO::exec() returns `0`.
     *                  The following example incorrectly relies on the return value of PDO::exec(), wherein a statement that affected 0 rows results in a call to die():
     */
    function exec($statement): int|false {
        try {
            $result = parent::exec($statement);
        } catch (PDOException $exc) {
            switch ($this->verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_WARNING);
                    break;
                case 1:
                    trigger_error($exc->getMessage(), E_USER_WARNING);
                    break;
                case 2:
                    trigger_error($statement . " | " . $exc->getMessage(),
                            E_USER_WARNING);
                    break;
                case 3:
                    trigger_error($statement . " | " . $exc->getMessage(),
                            E_USER_WARNING);
                    d($exc->getTraceAsString());
                    break;
                default:
                    break;
            }
            $result = FALSE;
        }

        return $result;
    }

    function get_db_dsn() {
        return $this->db_dsn;
    }

    function get_db_name() {
        return $this->db_name;
    }

    function get_db_user() {
        return $this->db_user;
    }

    function get_db_password() {
        return $this->db_password;
    }

    function get_db_host() {
        return $this->db_host;
    }

    function get_db_port() {
        return $this->db_port;
    }

    /**
     * END OF SQL FUNCTIONS AND EVERYTHING IS MOVED HERE
     */
    /*
     * Autor: Alejandro Trujillo J.
     * Copyright: Klan1 Network - 2010
     *
     * TODO: Make sql functions recognition into the sql builders
     *
     */

    /**
     * Run a SQL Query and returns an Array with all the result data
     * @param string $sql
     * @param bool $return_all
     * @param bool $do_fields
     * @return Array NULL on empty result and FALSE on failure.
     * TODO: Fix the NON optional cache isue !!
     */
    public function sql_query($sql, $return_all = TRUE, $do_fields = FALSE,
            $use_cache = TRUE, &$error_data = null): bool|array {
        //$query_result = new PDOStatement();
        if (profiler::is_enabled()) {
            $sql_profile_id = profiler::add($sql);
            profiler::start_time_count($sql_profile_id);
        }
        if (($use_cache) && (local_cache::is_enabled())) {
            $cacheResult = local_cache::get_result($sql);
        } else {
            $cacheResult = false;
        }

        if (!empty($cacheResult)) {
            if (profiler::is_enabled()) {
                profiler::set_is_cached($sql_profile_id, TRUE);
                profiler::stop_time_count($sql_profile_id);
            }
            return $cacheResult;
        } else {
            if (profiler::is_enabled()) {
                profiler::set_is_cached($sql_profile_id, FALSE);
            }
            $query_result = $this->query($sql);
        }
        $fields = array();
        $i = 1;
        $return = [];
        if ($query_result !== FALSE) {
            if ($query_result->rowCount() > 0) {
                $queryReturn = [];
                while ($row = $query_result->fetch(PDO::FETCH_ASSOC)) {
                    foreach ($row as $key => $value) {
                        // RESULTS WITH STRING NUMBERS WILL BE CONVERTED TO NUMBERS
                        if (is_numeric($value)) {
                            if (!(strlen($value) > 1) && (substr($value, 0, 1) == '0')) {
                                $row[$key] = $value + 0;
                            }
                        }
                        if ($do_fields && $return_all) {
                            $fields[$key] = $key;
                        }
                    }
                    if ($do_fields && $return_all) {
                        $queryReturn[0] = $fields;
                        $do_fields = FALSE;
                    }
                    $queryReturn[$i] = $row;
                    $i++;
                }
                if ($return_all) {
                    if (K1APP_MODE == "web") {
                        local_cache::add($sql, $queryReturn);
                    }
                    $return = $queryReturn;
                } else {
                    if (K1APP_MODE == "web") {
                        local_cache::add($sql, $queryReturn[1]);
                    }
                    $return = $queryReturn[1];
                }
            } else {
                $return = [];
            }
        } else {
            $return = FALSE;
        }
        if (profiler::is_enabled()) {
            profiler::stop_time_count($sql_profile_id);
        }
        return $return;
    }

    /**
     * Get from a DB Table the config matrix for the K1 Function and Objects related
     * @param PDO $db
     * @param array $table
     * @return array
     */
    public function get_db_table_config($table, $recursion = TRUE,
            $use_cache = TRUE) {

        // SQL to get info about a table
        $columns_info_query = "SHOW FULL COLUMNS FROM `{$table}`";
        $error = '';
        $columns_info_result = $this->sql_query($columns_info_query, TRUE,
                FALSE, $use_cache, $error);
        if ($columns_info_result === false) {
            trigger_error("The table '$table' do not exist", E_USER_ERROR);
            return FALSE;
        }
        $dsn_db = $this->get_db_name();
        $INFORMATION_SCHEMA_query = "SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = '{$dsn_db}'
AND table_name = '{$table}'";
        $INFORMATION_SCHEMA_result = $this->sql_query($INFORMATION_SCHEMA_query,
                TRUE, FALSE, $use_cache);
        if (empty($INFORMATION_SCHEMA_result)) {
            //        trigger_error("The table '$table' do not exist", E_USER_WARNING);
            //        return FALSE;
        }

        $config_array = array();
        // run through the result and covert into an array that works for K1.lib
        foreach ($columns_info_result as $field_row) {
            $field_name = $field_row['Field'];
            //        unset($field_config['Field']);
            /*
             * SEE EACH VALUE OF $field_config to build a new $key => $value and compile the COMMENT field from the table
             */
            $field_config = $field_row;
            foreach ($field_config as $key => $value) {
                // LOWER the $key and $value to AVOID problems
                $key_original = $key;
                $value_original = $value;
                $key = (!empty($key)) ? strtolower($key) : $key;
                $value = (!empty($value)) ? strtolower($value) : $value;
                // create a new pair of data but lowered
                $field_config[$key] = $value;
                /*
                 * COMPILE THE COMMENT VALUES TO GET A NEW PAIR OF DATA ON $field_config
                 * EACH PARAMETER IS SEPARATED WITH ,
                 * PARAMETER AND VALUE SEPARATED :
                 * parameter1:value1,...,parameterN:valueN
                 */
                if ($key == "comment") {
                    if (!empty($value)) {
                        /**
                         * 2025: JSON field config
                         */
                        $json_data = json_decode($value_original, TRUE);
                        if (!empty($json_data)) {
                            if (
                                    ((float) $json_data['schema-version'] >= field_config_json::SCHEMA_VERSION) &&
                                    ($json_data['schema-title'] == field_config_json::SCHEMA_TITLE)
                            ) {
                                $field_config = array_merge($field_config, $json_data['config']);
                            }
                        } else {
                            if ((strstr($value, ":") !== FALSE)) {
                                $parameters = explode(",", $field_config['Comment']);
                                if (count($parameters) != 0) {
                                    foreach ($parameters as $parameter_value) {
                                        list($attrib, $attrib_value) = explode(":",
                                                $parameter_value);
                                        $key = trim($attrib);
                                        $field_config[$attrib] = trim($attrib_value);
                                    }
                                }
                            }
                        }
                    }
                }
                //then delete the original pair of data
                unset($field_config[$key_original]);
            } // $field_config
            /*
             * DEFAULTS TAKE CARE !!
             */
            // MAX - THIS ONE IS ALWAYS AUTO GENERATED FROM FIELD DEFINITION
            $field_type = $field_config['type'];
            // manage the unsigned
            if (strstr($field_type, "unsigned") !== FALSE) {
                $field_type = str_replace(" unsigned", "",
                        $field_type);
                $field_config['unsigned'] = TRUE;
            } else {
                $field_config['unsigned'] = FALSE;
            }
            if (strstr($field_type, "(") !== FALSE) {
                // extract the number from type definition
                list($field_type, $max_legth) = explode("(", $field_type);
                if (!empty($max_legth)) {
                    $max_legth = substr($max_legth, 0, -1);
                    if (strstr($max_legth, ",") !== FALSE) {
                        list($number, $decimal) = explode(",", $max_legth);
                        $max_legth = (int) $number + (int) $decimal;
                    }
                }
            } else {
                $mysql_max_length_defaults = sql_defaults::get_mysql_max_length_defaults();
                $max_legth = $mysql_max_length_defaults[$field_type];
            }
            $field_config['type'] = $field_type;
            $field_config['max'] = $max_legth;

            // TYPE VALIDATION
            $mysql_default_validation = sql_defaults::get_mysql_default_validation();
            if (!isset($field_config['validation'])) {
                $field_config['validation'] = $mysql_default_validation[$field_config['type']];
            }


            //ROW attrib_value FIXES
            // yes -> TRUE no -> FALSE
            foreach ($field_config as $key => $value) {
                //            d("$key -> $value");
                if ($field_config[$key] == "yes") {
                    $field_config[$key] = TRUE;
                } elseif ($field_config[$key] == "no") {
                    $field_config[$key] = FALSE;
                }
            }
            // IF no label so capitalize the FIELD NAME
            if (!isset($field_config['label'])) {
                $field_config['label'] = $this->get_field_label_default($table,
                        $field_name);
            }
            // ENUM FIX
            if (!isset($field_config['min'])) {
                $field_config['min'] = 0;
                /**
                 * TODO: Make option system for this
                 */
                //            $field_config['min'] = defined(DB_MIN_FIELD_LENGTH) ? DB_MIN_FIELD_LENGTH : FALSE;
            }
            if ($field_config['type'] == "enum") {
                $field_config['min'] = 1;
                $field_config['max'] = 999;
            }
            // NEW 2016: REQUIRED-FIELD
            if (!isset($field_config['required'])) {
                if ($field_config['null'] === TRUE) {
                    $field_config['required'] = FALSE;
                } else {
                    $field_config['required'] = TRUE;
                }
            }
            // LABEL-FIELD
            if (!isset($field_config['label-field'])) {
                $field_config['label-field'] = FALSE;
            }
            // NEW 2016: ALIAS-FIELD
            if (!isset($field_config['alias'])) {
                $field_config['alias'] = NULL;
            }
            // NEW 2016: PLACEHOLDER
            if (!isset($field_config['placeholder'])) {
                $field_config['placeholder'] = NULL;
            }
            // NEW 2025: ICON
            if (!isset($field_config['icon'])) {
                $field_config['icon'] = NULL;
            }
            // NEW 2016: FILE TYPE
            if (!isset($field_config['file-type'])) {
                $field_config['file-type'] = NULL;
            }
            // NEW 2016: FILE UPLOAD MAX SIZE
            if (!isset($field_config['file-max-size'])) {
                $field_config['file-max-size'] = NULL;
            } else {
                $field_config['file-max-size'] = strtolower($field_config['file-max-size']);
                $size_unit = substr($field_config['file-max-size'],
                        -1);
                $size_number = substr($field_config['file-max-size'],
                        0, -1);
                if (!is_numeric($size_unit)) {
                    $file_size = NULL;
                    if ($size_unit == 'k') {
                        $file_size = $size_number * 1024;
                    }
                    if ($size_unit == 'm') {
                        $file_size = $size_number * 1024 * 1024;
                    }
                    if ($size_unit == 'g') {
                        $file_size = $size_number * 1024 * 1024 * 1024;
                    }
                    $field_config['file-max-size'] = $file_size;
                }
                if (empty($field_config['file-type'])) {
                    $field_config['file-type'] = "image/jpge";
                }
            }
            // show board
            /**
             * Show rules
             */
            $show_array_attribs[] = 'show-create';
            $show_array_attribs[] = 'show-read';
            $show_array_attribs[] = 'show-update';
            $show_array_attribs[] = 'show-list';
            // 2016 with NEW RULES !!
            $show_array_attribs[] = 'show-export';
            foreach ($show_array_attribs as $show_attrib) {
                if (!isset($field_config[$show_attrib])) {
                    $field_config[$show_attrib] = TRUE;
                }
            }
            // NEW 2016: show-search - Default as the table option
            if (!isset($field_config['show-search'])) {
                $field_config['show-search'] = (isset($field_config['show-list'])) ? $field_config['show-list'] : FALSE;
            }
            // NEW 2016: show-related - Default as the table option
            if (!isset($field_config['show-related'])) {
                $field_config['show-related'] = (isset($field_config['show-list'])) ? $field_config['show-list'] : FALSE;
            }
            //table name for each one, yes! repetitive, but necesary in some cases where i dnot receive the table name !!
            $field_config['table'] = $table;
            // SQL for selects
            $field_config['sql'] = "";
            // FOREIGN KEYS
            if (!empty($INFORMATION_SCHEMA_result)) {
                foreach ($INFORMATION_SCHEMA_result as $info_row) {
                    if (!empty($info_row['POSITION_IN_UNIQUE_CONSTRAINT']) && ($info_row['COLUMN_NAME'] == $field_name)) {
                        $field_config['refereced_table_name'] = $info_row['REFERENCED_TABLE_NAME'];
                        $field_config['refereced_column_name'] = $info_row['REFERENCED_COLUMN_NAME'];
                        if ($recursion) {
                            // RECURSION FIX 
                            $referenced_table_config = $this->get_db_table_config($info_row['REFERENCED_TABLE_NAME'],
                                    ($info_row['REFERENCED_TABLE_NAME'] != $table ? $recursion : FALSE));
                            $field_config['refereced_column_config'] = $referenced_table_config[$info_row['REFERENCED_COLUMN_NAME']];
                        } else {
                            $field_config['refereced_column_config'] = FALSE;
                        }
                        break;
                    } else {
                        $field_config['refereced_table_name'] = FALSE;
                        $field_config['refereced_column_name'] = FALSE;
                        $field_config['refereced_column_config'] = FALSE;
                    }
                }
            }

            // use the actual cycle data
            $config_array[$field_name] = $field_config;
        }
        return $config_array;
    }

    public function get_field_label_default($table, $field_name) {
        // Try to remove the table name from the field name. Commonly use on DB design
        if (strtolower(substr($table, -3)) === "ies") {
            $possible_singular_table_name = str_replace("ies", "y", $table);
        } elseif (strtolower(substr($table, -1)) === "s") {
            $possible_singular_table_name = substr($table, 0, -1);
        } else {
            $possible_singular_table_name = $table;
        }
        // Why not changue all the id to ID ?
        $field_name = str_replace('id', 'ID', $field_name);
        // Remove the possible singular name table from field name
        $field_name = str_replace("{$possible_singular_table_name}_", '',
                $field_name);
        // Better look without the _ character
        $field_name = str_replace('-', ' ',
                strtoupper(substr($field_name, 0, 1)) . (substr($field_name, 1)));
        $field_name = str_replace('_', ' ',
                strtoupper(substr($field_name, 0, 1)) . (substr($field_name, 1)));
        return $field_name;
    }

    /**
     * 
     * @global type $controller_errors
     * @param \PDO $db
     * @param string $table
     * @param array $data
     * @param array $table_keys
     * @param array $db_table_config
     * @return boolean
     */
    public function sql_update($table, $data, $table_keys = array(),
            $db_table_config = array(), &$error_data = null, &$sql_query = null) {
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

        if ($this->is_enabled()) {
            if (is_array($data)) {
                if (!is_array(@$data[0])) {
                    if (empty($db_table_config)) {
                        $db_table_config = $this->get_db_table_config($table);
                    }
                    if (empty($table_keys)) {
                        $keys_where_condition = $this->table_keys_to_where_condition($data,
                                $db_table_config);
                    } else {
                        $keys_where_condition = $this->array_to_sql_set($table_keys,
                                TRUE, TRUE);
                    }
                    $data_string = $this->array_to_sql_set($data);
                    $update_sql = "UPDATE `$table` SET $data_string WHERE $keys_where_condition;";
                    //                $controller_errors[] = $update_sql;
                    //                $controller_errors[] = print_r($data, TRUE);
                } else {
                    die(__FUNCTION__ . ": only can work with a 1 dimension array");
                }
                //            d($update_sql);
                $sql_query = $update_sql;
                $update = $this->exec($update_sql);

                if (isset($this->errorInfo()[2]) && !empty($this->errorInfo()[2])) {
                    //                $regexp = "/\((?:`(\w+)`,?)+\)/ix";
                    $regexp = "/FOREIGN KEY \((.*)?\)/i";
                    $match = [];
                    if (preg_match($regexp, $this->errorInfo()[2], $match)) {
                        $match[1] = str_replace(' ', '', $match[1]);
                        $fk_fields_error = explode(',',
                                str_replace('`', '', $match[1]));
                        if (!empty($fk_fields_error)) {
                            foreach ($fk_fields_error as $value) {
                                $error_data[$value] = 'Key error';
                            }
                        }
                    } else {
                        $error_data = "Error on Update stament : ($update_sql) " . $this->errorInfo()[2];
                    }
                }

                if ($update) {
                    return $update;
                } else {
                    return FALSE;
                }
            } else {
                \trigger_error("Has not received an arrany to do his work",
                        E_USER_ERROR);
                exit();
            }
        } else {
            \trigger_error("This App do not support databases", E_USER_ERROR);
            return FALSE;
        }
    }

    public function sql_insert($table, $data, &$error_data = null,
            &$sql_query = null) {
        if ($this->is_enabled()) {
            if (is_array($data)) {
                if (!is_array($data[0])) {
                    $data_string = $this->array_to_sql_set($data);
                    if ($data_string === false) {
                        \trigger_error("\$data array is invalid", E_USER_WARNING);
                        if (defined('K1APP_VERBOSE') && K1APP_VERBOSE > 0) {
                            d($data, TRUE);
                        }
                        return FALSE;
                    }
                    $insert_sql = "INSERT INTO $table SET $data_string;";
                } else {
                    $data_string = $this->array_to_sql_values($data);
                    if ($data_string === false) {
                        \trigger_error("\$data array is invalid", E_USER_WARNING);
                        if (defined('K1APP_VERBOSE') && K1APP_VERBOSE > 0) {
                            d($data, TRUE);
                        }
                        return FALSE;
                    }
                    $insert_sql = "INSERT INTO $table $data_string;";
                }
                //            ($insert_sql);
                $sql_query = $insert_sql;
                $insert = $this->exec($insert_sql);

                if (isset($this->errorInfo()[2]) && !empty($this->errorInfo()[2])) {
                    //                $regexp = "/\((?:`(\w+)`,?)+\)/ix";
                    $regexp = "/FOREIGN KEY \((.*)?\) REFERENCES/i";
                    $match = [];
                    if (preg_match($regexp, $this->errorInfo()[2], $match)) {
                        $match[1] = str_replace(' ', '', $match[1]);
                        $fk_fields_error = explode(',',
                                str_replace('`', '', $match[1]));
                        if (!empty($fk_fields_error)) {
                            foreach ($fk_fields_error as $value) {
                                $error_data[$value] = 'Key error';
                            }
                        }
                    } else {
                        $error_data = "Error on Insert stament : " . $this->errorInfo()[2] . "($insert_sql)";
                    }
                }
                if ($insert) {
                    $last_insert_sql = "SELECT LAST_INSERT_ID() as 'LAST_ID'";
                    $last_insert_result = $this->sql_query($last_insert_sql,
                            FALSE);
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
            \trigger_error("This App do not support databases", E_USER_WARNING);
            return FALSE;
        }
    }

    public function array_to_sql_values($array) {
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
                    $data_string .= '`' . trim($field_name) . '`';
                }
                $data_string .= ") VALUES ";
            } else {
                \trigger_error("wrong format in array", E_USER_WARNING);
                return false;
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
                        $value = check_single_incomming_var($value);
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
                    \trigger_error("wrong values count of array" . print_r($array,
                                    true), E_USER_WARNING);
                    return false;
                }
            }
            // join to return
            return $data_string;
        } else {
            trigger_error("Bad formated array in " . __FUNCTION__,
                    E_USER_WARNING);
            return false;
        }
    }

    /**
     * Convert an ARRAY to SQL SET pairs
     * @param array $array Array to convert
     * @param Bolean $use_nulls If should keep the null data, otherwise those will be skiped
     * @param Bolean $for_where_stament If TRUE will join the pairs with AND, if not, will use coma instead
     * @return type
     */
    public function array_to_sql_set(array $array, $use_nulls = true,
            $for_where_stament = FALSE, $precise = TRUE) {
        if (is_array($array) && (count($array) >= 1)) {

            /**
             * NEW CODE 2016
             */
            $pairs = [];
            foreach ($array as $field => $value) {
                if ($use_nulls === FALSE && $value === NULL) {
                    continue;
                }
                if ($precise) {
                    if ($value === NULL) {
                        if ($for_where_stament) {
                            $pairs[] = "`{$field}` IS NULL";
                        } else {
                            $pairs[] = "`{$field}` = NULL";
                        }
                    } else {
                        $pairs[] = "`{$field}`= " . $this->quote($value);
                    }
                } else {
                    $pairs[] = "`{$field}` LIKE '%" . $this->quote($value) . "%'";
                }
            }
            if ($for_where_stament) {
                $glue = " AND ";
            } else {
                $glue = ", ";
            }
            $data_string = implode($glue, $pairs);
        } else {
            trigger_error("Bad formated array in " . __FUNCTION__,
                    E_USER_WARNING);
            return false;
        }
        return $data_string;
    }

    /**
     * Convert an ARRAY to SQL SET pairs with deferent <> or NOT LIKE
     * @param array $array Array to convert
     * @param Bolean $use_nulls If should keep the null data, otherwise those will be skiped
     * @param Bolean $for_where_stament If TRUE will join the pairs with AND, if not, will use coma instead
     * @return type
     */
    function array_to_sql_set_exclude(array $array, $use_nulls = true,
            $for_where_stament = FALSE, $precise = TRUE) {
        if (is_array($array) && (count($array) >= 1)) {

            /**
             * NEW CODE 2016
             */
            $pairs = [];
            foreach ($array as $field => $value) {
                if ($use_nulls === FALSE && $value === NULL) {
                    continue;
                }
                if ($precise) {
                    if ($value === NULL) {
                        $pairs[] = "`{$field}` IS NOT NULL";
                    } else {
                        $pairs[] = "`{$field}`<> " . $this->quote($value);
                    }
                } else {
                    $pairs[] = "`{$field}` NOT LIKE '%" . $this->quote($value) . "%'";
                }
            }
            if ($for_where_stament) {
                $glue = " AND ";
            } else {
                $glue = ", ";
            }
            $data_string = implode($glue, $pairs);
        } else {
            trigger_error("Bad formated array in " . __FUNCTION__, E_USER_ERROR);
            exit();
        }
        return $data_string;
    }

    function get_db_tables_config_from_sql($sql_query) {
        $sql_query = "EXPLAIN " . $sql_query;
        $explainResult = $this->sql_query($sql_query, TRUE);
        if ($explainResult) {
            $presentTablesArray = array();
            $tableConfig = NULL;
            foreach ($explainResult as $row) {
                if (isset($row['table']) && (!empty($row['table'])) && (!strstr($row['table'],
                                '<')) && ($row['select_type'] != 'DEPENDENT SUBQUERY')) {
                    $tableConfig = $this->get_db_table_config($row['table']);
                    if (!empty($tableConfig)) {
                        $presentTablesArray = array_merge($presentTablesArray,
                                $tableConfig);
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

    function get_sql_count_query_from_sql_code($sql_query) {
        $sql_query_lower = strtolower($sql_query);
        $from_pos = strpos($sql_query_lower, "from");
        $new_sql_with_count = "SELECT count(*) as num_rows " . substr($sql_query,
                        $from_pos);
        return $new_sql_with_count;
    }

    function get_sql_query_with_new_fields($sql_query, $fields) {
        $sql_query_lower = strtolower($sql_query);
        $from_pos = strpos($sql_query_lower, "from");
        $new_sql_with_new_fields = "SELECT {$fields} " . substr($sql_query,
                        $from_pos);
        return $new_sql_with_new_fields;
    }

//    /**
//     * Check if the recieved var $db is a PDO object. On error the entire sofware will DIE
//     * @param PDO $db
//     * @param string $caller
//     */
//    function db_check_object_type($caller = "") {
//        if (get_class($db) != "PDO") {
//            die(__FUNCTION__ . ": \$db is not a PDO object type" . (($caller != "") ? " - called from: $caller" : ""));
//        }
//    }

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
            trigger_error(__FUNCTION__ . ": need an array to work on \$db_table_config",
                    E_USER_ERROR);
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
     * @param array $db_table_config
     * @param integer $position If this is -1 will return the last field found
     * @return String Label field name
     */
    public function get_db_table_label_fields($db_table_config) {
        if (!is_array($db_table_config)) {
            die(__FUNCTION__ . ": need an array to work on \$db_table_config");
        }
        $p = 0;
        $labels_fields = [];
        foreach ($db_table_config as $field => $config) {
            if (($config['label-field'])) {
                $labels_fields[] = $field;
            }
        }
        if (!empty($labels_fields)) {
            return $labels_fields;
        } else {
            return NULL;
        }
    }

    public function get_db_table_label_fields_from_row($row_data,
            $db_table_config) {
        if (!is_array($db_table_config)) {
            die(__FUNCTION__ . ": need an array to work on \$db_table_config");
        }
        $p = 0;
        $labels = [];
        foreach ($db_table_config as $field => $config) {
            if (($config['label-field']) && key_exists($field, $row_data)) {
                $labels[] = $row_data[$field];
            }
        }
        if (!empty($labels)) {
            return implode(" ", $labels);
        } else {
            return NULL;
        }
    }

    public function resolve_fk_real_field_name(&$data_array_to_modify,
            $field_to_resolve, $table_config_array) {
        if (!empty($table_config_array[$field_to_resolve]['refereced_column_config'])) {
            $refereced_column_config = $table_config_array[$field_to_resolve]['refereced_column_config'];
            while (!empty($refereced_column_config['refereced_column_config'])) {
                $refereced_column_config = $refereced_column_config['refereced_column_config'];
            }
            $new_data_array_to_modify = [];
            foreach ($data_array_to_modify as $key => $value) {
                if ($key == $field_to_resolve) {
                    $new_data_array_to_modify[$refereced_column_config['field']] = $value;
                } else {
                    $new_data_array_to_modify[$key] = $value;
                }
            }
            $data_array_to_modify = $new_data_array_to_modify;
        }
    }

    function resolve_fk_real_fields_names(&$data_array_to_modify,
            $table_config_array) {
        foreach ($data_array_to_modify as $field => $value) {
            resolve_fk_real_field_name($data_array_to_modify, $field,
                    $table_config_array);
        }
    }

    function make_name_fields_sql_safe(array &$array = []) {
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                $array[$key] = "`$value`";
            }
        }
        return $array;
    }

    function get_fk_field_label($fk_table_name, array $url_key_array = [],
            $source_table_config = []) {
        foreach ($url_key_array as $url_key_index => $url_key_value) {
            
        }
        $this->resolve_fk_real_field_name($url_key_array, $url_key_index,
                $source_table_config);

        if (!is_string($fk_table_name)) {
            trigger_error("\$fk_table_name must to be a String", E_USER_ERROR);
        }
        $fk_table_config = $this->get_db_table_config($fk_table_name);
        $fk_table_label_fields = $this->get_db_table_label_fields($fk_table_config);

        if (!empty($fk_table_label_fields)) {
            $fk_table_label_fields_text = implode(",",
                    $this->make_name_fields_sql_safe($fk_table_label_fields));
            $fk_where_condition = $this->table_keys_to_where_condition($url_key_array,
                    $fk_table_config);
            if (!empty($fk_where_condition)) {
                $fk_sql_query = "SELECT {$fk_table_label_fields_text} FROM `$fk_table_name` WHERE $fk_where_condition";
                $sql_result = $this->sql_query($fk_sql_query, FALSE);
                return implode(" ", $sql_result);
            } else {
                return NULL;
            }
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
     * @param array $row_data
     * @param array $db_table_config
     * @return Array FALSE on error
     */
    public function table_keys_to_text($row_data, $db_table_config) {
        if (!is_array($db_table_config)) {
            die(__FUNCTION__ . ": need an array to work on \$db_table_config");
        }
        $table_keys_array = $this->get_db_table_keys($db_table_config);
        $table_keys_values = array();
        foreach ($row_data as $column_name => $value) {
            if (isset($table_keys_array[$column_name]) && (!empty($table_keys_array[$column_name]))) {
                $table_keys_values[] = $value;
            }
        }
        $table_keys_text = implode("--", $table_keys_values);
        return $table_keys_text;
    }

    function get_keys_array_from_row_data($row_data, $db_table_config) {
        $key_fields_array = $this->get_db_table_keys($db_table_config);
        $keys_array = clean_array_with_guide($row_data, $key_fields_array);
        if (!empty($keys_array)) {
            return $keys_array;
        } else {
            return [];
        }
    }

    public function table_url_text_to_keys($url_text, $db_table_config) {
        if (!is_array($db_table_config)) {
            die(__FUNCTION__ . ": need an array to work on \$db_table_config");
        }
        $url_text_array = explode("--", $url_text);
        $url_text_array_count = count($url_text_array);

        $table_keys_array = $this->get_db_table_keys($db_table_config);
        $table_keys_count = count($table_keys_array);
        // elements count check
        if ($url_text_array_count != $table_keys_count) {
            trigger_error(__FUNCTION__ . ": The count of recived keys ({$url_text_array_count}) as text to not match with the \$db_table_config ({$table_keys_count})",
                    E_USER_ERROR);
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
        $errors = form_check_values($key_data, $db_table_config);
        if (!empty($errors)) {
            d($key_data);
            d($errors);
            trigger_error("Value types on the received \$url_text do not match with \$db_table_config",
                    E_USER_ERROR);
        }
        return $key_data;
    }

    function table_keys_to_where_condition(&$row_data, $db_table_config,
            $use_table_name = FALSE) {
        if (!is_array($db_table_config)) {
            die(__FUNCTION__ . ": need an array to work on \$db_table_config");
        }

        $table_keys_array = $this->get_db_table_keys($db_table_config);
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
                $where_condition .= "`{$db_table_config[$key]['table']}`.`$key` = '$value'";
            } else {
                $where_condition .= "`$key` = '$value'";
            }
            $first_value = FALSE;
        }
        return $where_condition;
    }

    function sql_del_row($table, $key_array) {
        $key_sql_set = $this->array_to_sql_set($key_array, TRUE, TRUE);
        $sql = "DELETE FROM `$table` WHERE $key_sql_set";
        //    echo $sql;
        $exec = $this->exec($sql);
        //    d($exec);
        if ($exec > 0 && $exec !== FALSE) {
            return $exec;
        } else {
            return FALSE;
        }
    }

    function sql_check_id($table, $key_name, $key_value, $use_cache = FALSE) {

        $sql = "SELECT COUNT(*) AS num_keys FROM `$table` WHERE `$key_name` = " . (is_numeric($key_value) ? $key_value : "'$key_value'") . " ";
        if ($use_cache) {
            $sql_count = sql_query_cached($db, $sql, FALSE);
        } else {
            $sql_count = $this->sql_query($sql, FALSE);
        }
        if ($sql_count['num_keys'] > 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function sql_value_increment($table, $key_name, $key_value, $field_name,
            $step = 1) {

        $sql = "SELECT `$field_name` FROM `$table` WHERE `$key_name` = " . (is_numeric($key_value) ? $key_value : "'$key_value'") . " ";
        if ($sql_result = $this->sql_query($sql, FALSE)) {
            // make the 'step' increment on the field to rise
            $sql_result[$field_name] += $step;
            // add to the data array the key command to use the sql_update function
            $sql_result["$key_name:key"] = $key_value;
            if ($this->sql_update($table, $sql_result)) {
                return $sql_result[$field_name];
            } else {
                return FALSE;
            }
        } else {
            \trigger_error("The value to increment coundn't be query",
                    E_USER_WARNING);
            d($sql);
        }
    }

    function sql_count($data_array) {
        $count = count($data_array) - 1;
        return ($count >= 0) ? $count : 0;
    }

    function sql_table_count($table, $condition = "", $use_memcache = FALSE,
            $expire_time = 60) {

        $sql = "SELECT COUNT(*) AS counted FROM `$table` " . (($condition != "") ? "WHERE $condition" : "");
        if ($use_memcache) {
            $result = $this->sql_query_cached($sql, FALSE, FALSE, $expire_time);
        } else {
            $result = $this->sql_query($sql, FALSE);
        }
        if ($result) {
            return $result['counted'];
        } else {
            FALSE;
        }
    }

    function get_db_table_enum_values($table, $field) {

        $dsn_db = $this->get_db_name();
        $enum_sql = "SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '{$dsn_db}' AND TABLE_NAME = '{$table}' AND COLUMN_NAME = '{$field}'";
        $enum_result = $this->sql_query($enum_sql, FALSE);
        $type = $enum_result['COLUMN_TYPE'];
        $matches = array();
        preg_match('/^enum\((.*)\)$/', $type, $matches);
        $i = 0;
        if (!empty($matches[1])) {
            foreach (explode(',', $matches[1]) as $value) {
                $enum[trim($value, "'")] = trim($value, "'");
            }
        } else {
            $enum = [];
        }
        return $enum;
    }

    function table_traduce_enum_to_index(&$query_result, $db_table_config) {
        if (!is_array($query_result)) {
            die(__FUNCTION__ . ": need an array to work on \$query_result");
        }
        if (!is_array($db_table_config)) {
            die(__FUNCTION__ . ": need an array to work on \$db_table_config");
        }
        // now go one by one row on the result
        foreach ($query_result as $column => $value) {
            if ($db_table_config[$column]['type'] == 'enum') {
                $enum_values_array = $this->get_db_table_enum_values($db,
                        $db_table_config[$column]['table'], $column);
                if (count($enum_values_array) > 0) {
                    $enum_values_array = array_flip($enum_values_array);
                    $query_result[$column] = $enum_values_array[$value];
                }
            }
        }
    }

    function get_table_definition_as_array($table_name) {
        $definition = $this->sql_query("SHOW CREATE TABLE `{$table_name}`",
                FALSE);
        $definition_array = explode("\n", $definition['Create Table']);
        // REMOVE THE 'CREATE TABLE PART'
        unset($definition_array[0]);
        // REMOVE THE LAST LINE 'ENGINIE=
        unset($definition_array[count($definition_array)]);
        // REMOVE PRIMARY KEY LINE
        unset($definition_array[count($definition_array)]);
        $definition_array_clean = [];
        foreach ($definition_array as $row => $text) {
            $text = substr($text, 3, -1);
            $field_name = substr($text, 0,
                    strpos($text, "`"));
            $field_definition = substr($text,
                    strpos($text, "`") + 2);
            $definition_array_clean[$field_name] = str_replace(strstr($text,
                            "COMMENT"), "", $field_definition);
            //        $definition_array_clean[$field_name]['definition'] = str_replace(strstr($text, "COMMENT"), "", $field_definition);
            //        $definition_array_cl´ean[$field_name]['comment'] = strstr($text, "COMMENT");
        }
        return ($definition_array_clean);
    }

    function get_table_data_as_key_value_pair($table_name) {
        $sql_query = "SELECT * FROM $table_name";
        $sql_result = $this->sql_query($sql_query);
        if ($sql_result) {
            $new_pair_array = [];
            foreach ($sql_result as $row => $data) {
                if (count($data) == 2) {
                    $new_pair_array[current($data)] = next($data);
                }
            }
            return $new_pair_array;
        } else {
            return FALSE;
        }
    }

    function generate_auth_code($row_keys_text) {
        return md5(K1MAGIC::get_value() . $row_keys_text);
    }
}
