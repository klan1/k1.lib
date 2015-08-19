<?php
/*
* TODO: converto all this to mysqli extension wthout function_name_asigner
* 
* NOTES:
* 
* It is possible to convert integer and float columns back to PHP numbers by setting the MYSQLI_OPT_INT_AND_FLOAT_NATIVE connection option, 
* if using the mysqlnd library. If set, the mysqlnd library will check the result set meta data column types and convert numeric SQL columns to PHP 
* numbers, if the PHP data type value range allows for it. This way, for example, SQL INT columns are returned as integers.
* 
*/
// class 	sql; 
// class 	cs_db_manager extends sql;
// class 	sql_query;
// array function function_name_asigner($type)

function function_name_asigner($type) {
    $array = array();
    switch ($type) {
        case 'mysql': {
                $array['connect'] = 'mysql_connect';
                $array['connect_p'] = 'mysql_pconnect';
                $array['close'] = 'mysql_close';
                $array['errno'] = 'mysql_errno';
                $array['error'] = 'mysql_error';
                $array['fetch_array'] = 'mysql_fetch_array';
                $array['fetch_row'] = 'mysql_fetch_row';
                $array['field_name'] = 'mysql_field_name';
                $array['free_result'] = 'mysql_free_result';
                $array['insert_id'] = 'mysql_insert_id';
                $array['num_rows'] = 'mysql_num_rows';
                $array['num_fields'] = 'mysql_num_fields';
                $array['select_db'] = 'mysql_select_db';
                $array['query'] = 'mysql_query';
                $array['list_tables'] = 'mysql_list_tables';
                return $array;
            }
        case 'mssql': {
                $array['connect'] = 'mssql_connect';
                $array['close'] = 'mssql_close';
                $array['errno'] = 'mssql_get_last_message';
                $array['fetch_array'] = 'mssql_fetch_row';
                $array['field_name'] = 'mssql_field_name';
                $array['free_result'] = 'mssql_free_result';
                $array['num_rows'] = 'mssql_num_rows';
                $array['num_fields'] = 'mssql_num_fields';
                $array['select_db'] = 'mssql_select_db';
                $array['query'] = 'mssql_query';
                return $array;
            }
        case 'odbc': {
                $array['connect'] = 'odbc_connect';
                $array['close'] = 'odbc_close';
                $array['fetch_array'] = 'odbc_fetch_row';
                $array['fetch_row'] = 'odbc_fetch_row';
                $array['field_name'] = 'odbc_field_name';
                $array['free_result'] = 'odbc_free_result';
                $array['num_rows'] = 'odbc_num_rows';
                $array['num_fields'] = 'odbc_num_fields';
                $array['select_db'] = 'NA';
                $array['query'] = 'odbc_exec';
                $array['list_tables'] = 'odbc_tables';
                return $array;
            }
    }
}

class sql {

    var $db_type;   // Type de DB que se va a usar
    var $db_funtions; // arreglo de nombre de funciones
    var $link;    // Vinculo simbolico con el servior de DB
    var $text_query;  // Alanacena el ultimo Query a el motor SQL
    var $result_link;  // Vinculo simbolico a un resultado del motor SQL
    var $data;    // Matriz que almacena todo el resultado en forma de tabla
    var $num_fields;  // Numero de columnas de la matriz respuesta
    var $num_rows;  // Numero de registros de la matriz respuesta

    function sql($cs_db_manager) {
        if ($cs_db_manager->link == "") {
            carvac_err('0205');
        } else {
            $this->link = & $cs_db_manager->link;
            $this->db_funtions = function_name_asigner($cs_db_manager->db_type);
        }
    }

    function query($text_query = '', $do_matrix = 0) {
        if ($text_query == '') {
            $text_query = $this->text_query;
        }
        //$text_query = $text_query.';';
        $this->text_query = $text_query;
        if ($this->link == "") {
            carvac_err('0204');
            return 0;
        } else {
            $this->num_rows = 0;
            $this->num_fields = 0;
            $this->data = array();
            switch ($text_query) {
                case "LIST_TABLES":
                    switch ($this->db_type) {
                        case "odbc":
                            $this->result_link = $this->db_funtions['list_tables']($this->link);
                            break;
                        default:
                            $this->result_link = $this->db_funtions['list_tables']($this->db, $this->link);
                            break;
                    }
                    break;
                default:
                    switch ($this->db_type) {
                        case "odbc":
                            $this->result_link = $this->db_funtions['query']($this->link, $text_query);
                            break;
                        default:
                            $this->result_link = $this->db_funtions['query']($text_query, $this->link);
                            break;
                    }
                    break;
            }
            if ($this->result_link != "") {
                if (($do_matrix == 1) || (strtoupper(substr($text_query, 0, 6)) == "SELECT") || (strtoupper(substr($text_query, 0, 4)) == "SHOW")) {
                    $this->num_rows = $this->db_funtions['num_rows']($this->result_link);
                    if ($this->num_rows == 0) {
                        return 0;
                    }
                    $this->num_fields = $this->db_funtions['num_fields']($this->result_link);
                    // Pone los nombres de los FIELDS en cada columna del arreglo $this->data
                    switch ($this->db_type) {
                        case "odbc":
                            for ($i = 1; $i <= ($this->num_fields); $i++) {
                                $this->data[0][$i - 1] = $this->db_funtions['field_name']($this->result_link, $i);
                            }
                            // Seccion para evitar el error de conexcion con las DBF que no deveulven tama�o de resultado
                            if ($this->num_rows == -1) {
                                $nulo = 1;
                                for ($y = 1; $y <= $this->num_fields; $y++) {
                                    $this->data[1][$y - 1] = odbc_result($this->result_link, $this->data[0][$y - 1]);
                                    $this->data[1][$this->data[0][$y - 1]] = odbc_result($this->result_link, $this->data[0][$y - 1]);
                                    if ($this->data[1][$y - 1] != '') {
                                        $nulo = 0;
                                    }
                                }
                                if ($nulo == 1) {
                                    $this->num_rows = 0;
                                    return 0;
                                } else {
                                    $this->num_rows = 1;
                                }
                            } else {
                                $i = 1;
                                do {
                                    for ($y = 1; $y <= $this->num_fields; $y++) {
                                        $this->data[$i][$y] = odbc_result($this->result_link, $this->data[0][$y]);
                                    }
                                    $i++;
                                } while (odbc_next_result($this->result_link));
                            }
                            break;
                        default:
                            // Pone los datos de las ROWS en cada fila del arreglo $this->data
                            for ($i = 0; $i <= ($this->num_fields - 1); $i++) {
                                $this->data[0][$i] = $this->db_funtions['field_name']($this->result_link, $i);
                            }
                            // Pone los datos de las ROWS en cada fila del arreglo $this->data
                            for ($i = 1; $i <= $this->num_rows; $i++) {
                                $this->data[$i] = $this->db_funtions['fetch_array']($this->result_link);
                            }
                            break;
                    }

                    //$this->db_funtions['free_result']($this->result_link);
                }
                return 1;
            } else {
                if (isset($this->db_funtions['errno'])) {
                    if ($this->db_funtions['errno']($this->link) != 0) {
                        echo "\n<p>" . $this->db_funtions['errno']($this->link) . " -> " . $this->db_funtions['error']($this->link) . "</p><p>{$this->text_query}</p>\n";
                    }
                } else {
                    echo "\n<p>{$this->text_query}</p>\n";
                }
                carvac_err('0206', 'db_lib');
                return 0;
            }
        }
    }

    function clear() {
        $this->data = '';
    }

}

class cs_db_manager extends sql {

    var $last_id;   // Ultimo ID por autoincremento
    var $server;         // Servidor de DB
    var $db;              // Base de datos a abrir
    var $port;            // Puerto de conexion
    var $user;           // Usuario para la DB
    var $password;       // Constrasena para DB

    function cs_db_manager($db_type = '', $db_settings = '') {
        if (count($db_settings) == 5) {
            $this->server = $db_settings['db_server'];
            $this->db = $db_settings['db_db'];
            $this->port = $db_settings['db_port'];
            $this->user = $db_settings['db_user'];
            $this->password = $db_settings['db_password'];
        } elseif ($db_settings != '') {
            carvac_err('0201', 'cs_db_manager');
        }
        if ($db_type != '') {
            $this->db_type = $db_type;
        } else {
            carvac_err('0201', 'db_manger::init');
            return 0;
        }
        $this->db_funtions = function_name_asigner($this->db_type);
    }

    function open($db = "", $persistent = 0) {
        if ($this->server != '') {
            if ($this->port != '') {
                $L_port = ":{$this->port }";
            }
            if ($persistent) {
                $this->link = $this->db_funtions['connect_p']("$this->server" . $L_port, $this->user, $this->password);
            } else {
                $this->link = $this->db_funtions['connect']("$this->server" . $L_port, $this->user, $this->password);
            }
        } else {
            carvac_err('0201', "cs_db_manager::open ({$this->db_type})");
        }

        if ($this->link != "") {
            if ($db != "") {
                $this->select_db($db);
                $this->db = $db;
            }
            if (($db == "") && ($this->db != "")) {
                $this->select_db($this->db);
            }
            return true;
        } else {
            carvac_err('0202');
            return 0;
        }
    }

    function select_db($db) {
        if ($this->db_funtions['select_db'] != 'NA') {
            if ($this->db_funtions['select_db']($db, $this->link)) {
                return true;
            } else {
                carvac_err('0203');
                return 0;
            }
        } else {
            return 1;
        }
    }

    function close() {
        $this->db_funtions['close']($this->link);
        return true;
    }

    function insert($table, &$data_matrix) {
        $list_of_fields = "";
        $list_of_data = "";
        if (($table == "") || ($data_matrix == '')) {
            return 0;
        }
        $num_fields = count($data_matrix[0]) - 1;
        $num_rows = count($data_matrix) - 1;
        reset($data_matrix);
        // Genera la lista de FIELDS
        for ($field = 0; $field <= $num_fields; $field++) {
            $list_of_fields .= str_replace("'", "`", $data_matrix[0][$field]);
            if ($field != $num_fields) {
                $list_of_fields .= ",";
            }
        }
        next($data_matrix);
        // Genera la lista de VALUES (a1,b1,c1,...),(a2,b2,c2,...),(a3,b3,c3,...),.............
        for ($row = 1; $row <= $num_rows; $row++) {
            $current = current($data_matrix);
            // convierte un ROW a una lista de VALUES (aX,bX,cX,...)
            $list_of_data .= "(";
            for ($field = 0; $field <= $num_fields; $field++) {
                if (!empty($current[$field])) {
                    $list_of_data .= "\"" . $current[$field] . "\"";
                } else {
                    $list_of_data .= "NULL";
                }
                if ($field != $num_fields) {
                    $list_of_data .= ",";
                }
            }
            $list_of_data .= ")";
            if ($row != $num_rows) {
                $list_of_data .= ",";
            }
            next($data_matrix);
        }
        $insert_string = "INSERT INTO $table ($list_of_fields) VALUES $list_of_data";
        $result_state = $this->query($insert_string);
        if (isset($this->db_funtions['last_id'])) {
            $this->last_id = $this->db_funtions['last_id']($this->result_link);
        } else {
            $this->last_id = -1;
        }
        return $result_state;
    }

    function update($table, &$data_matrix, $conditions) {
        $list_of_fields = "";
        $list_of_data = "";
        $sets = "";

        if (($table == "") || ($data_matrix == "")) {
            return 0;
        }
        $num_fields = count($data_matrix[0]) - 1;
        for ($i = 0; $i <= $num_fields; $i++) {
            $data_matrix[0][$i] = str_replace("'", "`", $data_matrix[0][$i]);

            if ($data_matrix[1][$i] != "") {
                $sets .= "{$data_matrix[0][$i]} = '{$data_matrix[1][$i]}' ";
            } else {
                $sets .= "{$data_matrix[0][$i]} = NULL ";
            }
            if ($i != $num_fields) {
                $sets .= ", ";
            }
        }
        if ((substr($sets, strlen($sets) - 2, 2)) == ", ") {
            $sets = substr($sets, 0, strlen($sets) - 2);
        }
        $update_string = "UPDATE $table SET $sets WHERE $conditions";
        return $this->query($update_string);
    }

}

class slq_query {

    // Cuerpo del QUERY
    var $fields;       //	fields [ (index) ] { [(fields)] | [(alias)] | [(group_priority)] | [(order_priority)] } = string
    var $tables;       //	tables[ (index) ] = string
    var $where_conditions;   // where_conditions[ (index) ]  { [(contidion)] | [(contidition_value)] }  = string
    var $having_conditions;  // having_conditions[ (index) ] = string
    // OPCIONES de SELECT
    var $select_options;    //	string
    // Condiciones del QUERY
    var $limit;       //	limit['offset'], limit['rows']
    //RESULTADO
    var $query_code;    //	string

    function sql_query() {
        $fields = '';
        $tables = '';
        $where_conditions = '';
        $having_conditions = '';
        $select_options = '';
        $group_by = '';
        $order_by = '';
        $limit['offset'] = '';
        $limit['rows'] = '';
    }

    function do_query_code() {
        $num_fields = count($this->fields);
        $num_act_field = 1;
        $num_table = count($this->tables);
        $num_where_condition = count($this->where_conditions);
        $num_having_condition = count($this->having_conditions);

        $group_array = '';
        $order_array = '';

        $fields_string = '';

        // FIELDS ($fields_string)
        if ($num_field > 0) {
            // Recorre la matriz de fields
            do {
                $field = current($this->fields);
                // Hace campo por campo
                $fields_string .= $field['field'];
                if (isset($field['alias'])) {
                    $fields_string .= " AS {$field['field']}";
                }
                if ($num_act_field < $num_field) {
                    $fields_string .= ",";
                }

                // Si esta definido la prioridad de grupo
                if (isset($field['group_priority'])) {
                    if (isset($field['alias'])) {
                        $group_array[$field['group_priority']] = $field['alias'];
                    } elseif ($field['field'] != '') {
                        $group_array[$field['group_priority']] = $field['field'];
                    }
                }
                // Si esta definido la prioridad de ordenamiento
                if (isset($field['order_priority'])) {
                    if (isset($field['alias'])) {
                        $order_array[$field['order_priority']] = $field['alias'];
                    } elseif ($field['field'] != '') {
                        $order_array[$field['order_priority']] = $field['field'];
                    }
                }
                $num_act_field++;
            } while (next($this->fields));
        } else {
            echo "/nNo fields for query <p>/n";
            return 0;
        }

        // FROM ($fields_string)
        if ($num_field > 0) {
            // Recorre la matriz de fields
            do {
                $field = current($this->fields);
                // Hace campo por campo
                $fields_string .= $field['field'];
                if (isset($field['alias'])) {
                    $fields_string .= " AS {$field['field']}";
                }
                if ($num_act_field < $num_field) {
                    $fields_string .= ",";
                }

                // Si esta definido la prioridad de grupo
                if (isset($field['group_priority'])) {
                    if (isset($field['alias'])) {
                        $group_array[$field['group_priority']] = $field['alias'];
                    } elseif ($field['field'] != '') {
                        $group_array[$field['group_priority']] = $field['field'];
                    }
                }
                // Si esta definido la prioridad de ordenamiento
                if (isset($field['order_priority'])) {
                    if (isset($field['alias'])) {
                        $order_array[$field['order_priority']] = $field['alias'];
                    } elseif ($field['field'] != '') {
                        $order_array[$field['order_priority']] = $field['field'];
                    }
                }
                $num_act_field++;
            } while (next($this->fields));
        } else {
            echo "/nNo fields for query <p>/n";
            return 0;
        }


        return $this->query_code;
    }

}

?>