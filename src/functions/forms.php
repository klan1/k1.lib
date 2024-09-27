<?php

/**
 * Forms related functions, K1.lib.
 * 
 * Common needed actions on forms and special ideas implemented with this lib.
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package forms
 */

namespace k1lib\forms;

use k1lib\db\PDO_k1;
use function k1lib\common\serialize_var;

/**
 * This SHOULD be used always to receive any kind of value from _GET _POST _REQUEST if it will be used on SQL staments.
 * @param string $var Value to check.
 * @param bool $request FALSE to check $var as is. Use TRUE if $var become an index as: $_REQUEST[$var]
 * @param bool $url_decode TRUE if the data should be URL decoded.
 * @return String Rerturn NULL on error This could be that $var IS NOT String, Number or IS Array.
 */
function check_single_incomming_var($var, $request = FALSE, $url_decode = FALSE) {
    if ((is_string($var) || is_numeric($var)) && !is_array($var)) {
        if (($request == TRUE) && isset($_REQUEST[$var])) {
            $value = $_REQUEST[$var];
        } elseif ($request == FALSE) {
            $value = $var;
        } else {
            $value = NULL;
        }
        if ($value === '') {
            return NULL;
        } elseif (($value === 0)) {
            return 0;
        } elseif (($value === '0')) {
            return '0';
        } else {
//            $value = htmlspecialchars($value);
        }
        if ($url_decode) {
            $value = urldecode($value);
        }
        if (\json_decode($value) === NULL) {
//            $search = ['\\', "\0", "\n", "\r", "'", '"', "\x1a"];
//            $replace = ['\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'];
//            $value = str_replace($search, $replace, $value);
//            $value = @mysql_real_escape_string($value);
        }
        return $value;
    } else {
        return NULL;
    }
}

/**
 * Prevents SQL injection from any ARRAY, at the same time serialize the var into save_name. This uses check_single_incomming_var() on each array item. Is recursive.
 * @param array $request_array
 * @param string $save_name
 * @return array checked values ready to work
 */
function check_all_incomming_vars($request_array, $save_name = null) {
//checks all the incomming vars
// V0.8 forces the use of an non empty array
//    if (empty($request_array)) {
//        $request_array = $_REQUEST;
//    } else {
    if (!is_array($request_array)) {
        die(__FUNCTION__ . " need an array to work");
    }
//    }
    $form = array();
    foreach ($request_array as $index => $value) {
        if (!is_array($value)) {
            $form[$index] = check_single_incomming_var($value);
        } else {
            $form[$index] = check_all_incomming_vars($value);
        }
    }
    if (!empty($save_name)) {
        serialize_var($form, $save_name);
    }
    return $form;
}

/**
 * Get a single value from a serialized var if is an array, this one do not echo erros only return FALSE is there is not stored
 * @param string $form_name
 * @param string $field_name
 * @param string $default
 * @return mixed
 */
function get_form_field_from_serialized($form_name, $field_name, $default = "", $compare = "--FALSE--") {
    if (!is_string($form_name) || empty($form_name)) {
        die(__FUNCTION__ . " form_name should be an non empty string");
    }
    if (empty($field_name)) {
        die(__FUNCTION__ . " field_name should be an non empty string");
    }
    $field_value = "";
    //FORM EXISTS
    if (isset($_SESSION['serialized_vars'][$form_name])) {
        // FIELD EXISTS
        if (isset($_SESSION['serialized_vars'][$form_name][$field_name])) {
            $field_value = $_SESSION['serialized_vars'][$form_name][$field_name];
            if ($compare !== "--FALSE--") {
                if ($field_value === $compare) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        } else {
            if ($compare !== "--FALSE--") {
                if ($default === $compare) {
                    return TRUE;
                } else {
                    return FALSE;
                }
            } else {
                $field_value = $default;
            }
        }
    } else {
        $field_value = FALSE;
//        die(__FUNCTION__ . " serialized var '$form_name' do not exist! ");
    }

    return $field_value;
}

/**
 * Checks with this function if a received value matchs with a item on a ENUM field on a table
 * @param string $value Value to check
 * @param string  $table_name SQL table name
 * @param string $table_field SQL table field to check
 * @param PDO_k1 $db DB connection object
 * @return string
 */
function check_enum_value_type($received_value, $table_name, $table_field, PDO_k1 $db) {
    $options = $db->get_db_table_enum_values($table_name, $table_field);
    $options_fliped = array_flip($options);

    if (!isset($options_fliped[$received_value])) {
        $error_type = print_r($options_fliped, TRUE) . " value: '$received_value'";
//        d($received_value, TRUE);
    } else {
        $error_type = FALSE;
    }
    return $error_type;
}

function check_value_type($value, $type) {

    //dates for use
    $date = date("Y-m-d");
    $day = date("d");
    $month = date("m");
    $year = date("Y");
    //funcitons vars
    $error_type = "";
    $preg_symbols = "\-_@.,!:;#$%&'*\\/+=?^`{\|}()~ÁÉÍÓÚáéíóuñÑ";
    $preg_symbols_html = $preg_symbols . "<>\\\\\"'";
    $preg_file_symbols = "-_.()";

    switch ($type) {
        case 'options':
            trigger_error("This function can't check options type", E_USER_WARNING);
            $error_type = " This vale can't be checked";
            break;
        case 'email':
            $regex = "/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/";
            if (!preg_match($regex, $value)) {
                $error_type = " debe ser un Email valido.";
            }
            break;
        case 'boolean':
            $regex = "/^[01]$/";
            if (!preg_match($regex, $value)) {
                $error_type = "$error_header_msg solo puede valer 0 o 1";
            } else {
                $error_msg = "";
            }
            break;
        case 'boolean-unsigned':
            $regex = "/^[01]$/";
            if (!preg_match($regex, $value)) {
                $error_type = "$error_header_msg solo puede valer 0 o 1";
            } else {
                $error_msg = "";
            }
            break;
        case 'date':
            if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                if (!checkdate($matches['month'], $matches['day'], $matches['year'])) {
                    $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
                }
            } else {
                $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
            }
            break;
        case 'date-past':
            if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                if (checkdate($matches['month'], $matches['day'], $matches['year'])) {
                    $actual_date_number = juliantojd($month, $day, $year);
                    $value_date_number = juliantojd($matches['month'], $matches['day'], $matches['year']);
                    if ($value_date_number >= $actual_date_number) {
                        $error_type = " de fecha no puede ser mayor al dia de hoy: {$date}";
                    }
                } else {
                    $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
                }
            } else {
                $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
            }
            break;
        case 'date-future':
            if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                if (checkdate($matches['month'], $matches['day'], $matches['year'])) {
                    $actual_date_number = juliantojd($month, $day, $year);
                    $value_date_number = juliantojd($matches['month'], $matches['day'], $matches['year']);
                    if ($value_date_number <= $actual_date_number) {
                        $error_type = " de fecha debe ser mayor al dia de hoy: {$date}";
                    }
                } else {
                    $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
                }
            } else {
                $error_type = " contiene un formato de fecha invalida, debe ser AAAA-MM-DD.";
            }
            break;
        case 'datetime':
            // TODO
            break;
        case 'time':
            // TODO
            break;
        case 'letters':
            $regex = "/^[a-zA-Z\s]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z sin símbolos";
            }
            break;
        case 'letters-symbols':
            $regex = "/^[a-zA-Z0-9\s{$preg_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z y símbolos: $preg_symbols";
            }
            break;
        case 'password':
            $regex = "/^[a-zA-Z0-9\s{$preg_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z y símbolos: $preg_symbols";
            }
            break;
        case 'decimals-unsigned':
            $regex = "/^[0-9.]*$/";
            if (!(preg_match($regex, $value) && is_numeric($value))) {
                $error_type .= " deber ser solo números y decimales positivos";
            }
            break;
        case 'decimals':
            $regex = "/^[\-0-9.]*$/";
            if (!(preg_match($regex, $value) && is_numeric($value))) {
                $error_type = " debe contener solo números y decimales";
            }
            break;
        case 'numbers-unsigned':
            $regex = "/^[0-9]*$/";
            if (!(preg_match($regex, $value) && is_numeric($value))) {
                $error_type .= " deber ser solo números positivos";
            }
            break;
        case 'numbers':
            $regex = "/^[\-0-9]*$/";
            if (!(preg_match($regex, $value) && is_numeric($value))) {
                $error_type = " debe contener solo números";
            }
            break;
        case 'numbers-symbols':
            $regex = "/^[\-0-9\s{$preg_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " debe contener solo números y símbolos: $preg_symbols";
            }
            break;
        case 'mixed':
            $regex = "/^[\-a-zA-Z0-9\s]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z y números";
            }
            break;
        case 'mixed-symbols':
            $regex = "/^[a-zA-Z0-9\s{$preg_symbols}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z, números y símbolos: $preg_symbols";
            }
            break;
        case 'html':
            $regex = "/^[a-zA-Z0-9\s{$preg_symbols_html}]*$/";
            if (!preg_match($regex, $value)) {
                $error_type = " deber ser solo letras de la a-z y A-Z, números y símbolos: $preg_symbols_html";
            }
            break;
        case 'file-upload':
            /**
             * TODO: fix this
             */
//            $regex = "/^[a-zA-Z0-9\s{$preg_file_symbols}]*$/";
//            if (!preg_match($regex, $value)) {
//                $error_type = " solo pude contener letras y los siguientes simbolos: $preg_symbols";
//            }
            break;
        case 'not-verified':
            break;
        default:
            $error_type = "Not defined VALIDATION on Type '{$type}' from field '{$label}' ";
            break;
    }
    return $error_type;
}

function form_file_upload_handle($file_data, $field_config, $table_name = null) {
    /**
     * File validations with DB Table Config directives
     */
    $file_max_size = $field_config['file-max-size'] + 0;
    $file_type = $field_config['file-type'];
    if (strstr($file_data['type'], $file_type) === FALSE) {
        return "The file type is {$file_data['type']} not {$file_type}";
    }
    if ($file_data['size'] > $file_max_size) {
        return "Size is bigger than " . $file_max_size / 1024 . "k";
    }
    /**
     * ALL ok? then place the file and let it go... let it goooo! (my daughter Allison fault! <3 )
     */
    if (file_uploads::place_upload_file($file_data['tmp_name'], $file_data['name'], $table_name)) {
        return TRUE;
    } else {
        return file_uploads::get_last_error();
    }
//    $file_location = file_uploads::get_uploaded_file_path($file_data['name']);
//    $file_location_url = file_uploads::get_uploaded_file_url($file_data['name']);
}

function form_check_values(&$form_array, $table_array_config, PDO_k1 $db = NULL) {
    if (!is_array($form_array)) {
        die(__FUNCTION__ . " need an array to work on \$form_array");
    }
    if (!is_array($table_array_config)) {
        die(__FUNCTION__ . " need an array to work on \$required_array");
    }
    $error_array = array();
    foreach ($form_array as $key => $value) {

        $error_header_msg = "Este campo ";
        $error_msg = "";
        $error_type = "";

        /**
         * FILE UPLOAD HACK
         */
        $do_upload_file = FALSE;
        if (is_array($value)) {
            $do_upload_file = TRUE;
            // Uniques on name name fix
            $value['name'] = time() . '-' . $value['name'];
            $file_data = $value;
            $value = $value['name'];
//            $form_array[$key]['name'] = $value;
            $form_array[$key] = $value;
        }
        /**
         *  TYPE CHECK
         *  -- then See each field value to check if is valid with the table tyoe definition
         */
        // MIN - MAX check
        $min = $table_array_config[$key]['min'];
        $max = $table_array_config[$key]['max'];


        // email | letters (solo letras) | numbers (solo números) | mixed (alfanumerico) | letters-symbols (con símbolos ej. !#()[],.) | numbers-symbols | mixed-symbols - los symbols no lo implementare aun
        // the basic error, if is required on the table definition
        if (($value !== 0) && ($value !== '0') && empty($value)) {
            if ($table_array_config[$key]['required'] === TRUE) {
                $error_msg = "$error_header_msg es requerido.";
            }
        } elseif ((strlen((string) $value) < (int) $min) || (strlen((string) $value) > (int) $max)) {
            $error_msg = "$error_header_msg debe ser de minimo $min y maximo $max caracteres";
        }

        if (($value === 0) || !empty($value)) {
            if ($table_array_config[$key]['validation'] == 'options') {
                $error_type = check_enum_value_type($value, $table_array_config[$key]['table'], $key, $db);
            } else {
                $unsigned_type = ($table_array_config[$key]['unsigned']) ? "-unsigned" : "";
                $error_type = check_value_type($value, $table_array_config[$key]['validation'] . $unsigned_type);
            }
        }
        if ($do_upload_file && empty($error_msg) && empty($error_type)) {
//            d($table_array_config[$key]);
            $file_result = form_file_upload_handle($file_data, $table_array_config[$key], $table_array_config[$key]['table']);
            if ($file_result !== TRUE) {
                $error_array[$key] = $file_result;
            }
            $error_msg = "";
            $error_type = "";
        }
        if (empty($error_type) && !empty($error_msg)) {
            $error_array[$key] = $error_msg;
        } else if (!empty($error_type) && empty($error_msg)) {
            $error_array[$key] = "$error_header_msg $error_type";
        } else if (!empty($error_type) && !empty($error_msg)) {
            $error_array[$key] = "$error_msg - $error_type";
        } else {
//            d("$value is {$table_array_config[$key]['validation']}");
        }
    }

    if (count($error_array) > 0) {
        return $error_array;
    } else {
        return FALSE;
    }
}

//function make_form_select_list(&$field_name, &$value, &$table_config_array, &$error_msg = "") {
//    global $db;
//
//    /*
//     * SELECT LIST
//     */
//    //ENUM drop list
//    $select_data_array = array();
//    if ($table_config_array[$field_name]['type'] == "enum") {
//        $select_data_array = $db->get_db_table_enum_values($table_config_array[$field_name]['table'], $field_name);
//    } elseif ($table_config_array[$field_name]['sql'] != "") {
//        $table_config_array[$field_name]['sql'];
//        $sql_data = $db->sql_query($table_config_array[$field_name]['sql'], TRUE);
//        if (!empty($sql_data)) {
//            foreach ($sql_data as $row) {
//                $select_data_array[$row['value']] = $row['label'];
//            }
//        }
//    } elseif (!empty($table_config_array[$field_name]['refereced_table_name'])) {
//        $select_data_array = \k1lib\forms\get_labels_from_table($table_config_array[$field_name]['refereced_table_name']);
//    }
//    $label_object = new html\label($table_config_array[$field_name]['label'], $field_name, "right inline");
////    $select_object = new html\select($field_name);
//
//    if (empty($value) && (!$table_config_array[$field_name]['null'])) {
//        $value = $table_config_array[$field_name]['default'];
//    }
//
//    if (!empty($error_msg)) {
//        $select_html = html\select_list_from_array($field_name, $select_data_array, $value, $table_config_array[$field_name]['null'], "error");
//        $html_template = html\load_html_template("label_input_combo-error");
//        $html_code = sprintf($html_template, $label_object->generate(), $select_html, $error_msg);
//    } else {
//        $select_html = html\select_list_from_array($field_name, $select_data_array, $value, $table_config_array[$field_name]['null']);
//        $html_template = html\load_html_template("label_input_combo");
//        $html_code = sprintf($html_template, $label_object->generate(), $select_html);
//    }
//
//    return $html_code;
//}

//function get_labels_from_table($table_name) {
//
//    $db->db_check_object_type(__FUNCTION__);
//
//    if (!is_string($table_name) || empty($table_name)) {
//        die(__FUNCTION__ . " \$table_name should be an non empty string");
//    }
//    $table_config_array = $db->get_db_table_config($table_name);
//    $label_field = $db->get_db_table_label_fields($table_config_array);
//    $table_keys_array = $db->get_db_table_keys($table_config_array);
//    if (!empty($table_keys_array)) {
//        $table_config_array = array_flip($table_keys_array);
//    }
//    if (count($table_keys_array) === 1) {
//        $key_filed = key($table_keys_array);
//        $labels_sql = "SELECT $key_filed as value, $label_field as label FROM $table_name";
//        $labels_data = $db->sql_query($labels_sql);
//        if (!empty($labels_data) && (count($labels_data) > 0)) {
//            $label_array = array();
//            foreach ($labels_data as $row) {
//                $label_array[$row['value']] = $row['label'];
//            }
//            return $label_array;
//        } else {
//            return FALSE;
//        }
//    } else {
//        return FALSE;
//    }
//}
