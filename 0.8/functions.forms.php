<?php

/**
 * Prevents SQL injection from any ARRAY, at the same time serialize the var into save_name
 * @param array $request_array
 * @param string $save_name
 * @return array checked values ready to work
 */
function k1_get_all_request_vars($request_array, $save_name) {
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
            $form[$index] = k1_check_incomming_var($value);
        } else {
            $form[$index] = k1_get_all_request_vas($value);
        }
    }
    k1_serialize_var($form, $save_name);
    return $form;
}

/**
 * Save a var to the selected method
 * @param miexd $var_to_save
 * @param string $save_name
 * @param string $method
 * @return boolean
 */
function k1_serialize_var($var_to_save, $save_name, $method = "session") {
    if (!is_string($save_name) || empty($save_name)) {
        die(__FUNCTION__ . " save_name should be an non empty string");
    }
    if ($method == "session") {
        $_SESSION['serialized_vars'][$save_name] = $var_to_save;
    }
    return true;
}

/**
 * Load the saved_name var from selected method
 * @param string $saved_name
 * @param string $method
 * @return boolean
 */
function k1_unserialize_var($saved_name, $method = "session") {
    if (!is_string($saved_name) || empty($saved_name)) {
        die(__FUNCTION__ . " saved_name should be an non empty string");
    }
    $saved_vars = array();
    if ($method == "session") {
        if (isset($_SESSION['serialized_vars'][$saved_name])) {
            $saved_vars = $_SESSION['serialized_vars'][$saved_name];
        } else {
            $saved_vars = false;
        }
    }
    return $saved_vars;
}

function k1_unset_serialize_var($saved_name, $method = "session") {
    if (!is_string($saved_name) || empty($saved_name)) {
        die(__FUNCTION__ . " saved_name should be an non empty string");
    }
    if (isset($_SESSION['serialized_vars'][$saved_name])) {
        unset($_SESSION['serialized_vars'][$saved_name]);
        return true;
    } else {
        return false;
    }
}

/**
 * Get a single value from a serialized var if is an array, this one do not echo erros only return FALSE is there is not stored
 * @param string $form_name
 * @param string $field_name
 * @param string $default
 * @return mixed
 */
function k1_get_form_field_from_serialized($form_name, $field_name, $default = "", $compare = "--false--") {
    if (!is_string($form_name) || empty($form_name)) {
        die(__FUNCTION__ . " form_name should be an non empty string");
    }
    if (!is_string($field_name) || empty($field_name)) {
        die(__FUNCTION__ . " field_name should be an non empty string");
    }
    $field_value = "";
    if (isset($_SESSION['serialized_vars'][$form_name])) {
        if (isset($_SESSION['serialized_vars'][$form_name][$field_name])) {
            $field_value = $_SESSION['serialized_vars'][$form_name][$field_name];
            if ($compare !== "--false--") {
                if ($field_value === $compare) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            if ($compare !== "--false--") {
                if ($default === $compare) {
                    return true;
                } else {
                    return false;
                }
            } else {
                $field_value = $default;
            }
        }
    } else {
        $field_value = false;
//        die(__FUNCTION__ . " serialized var '$form_name' do not exist! ");
    }

    return $field_value;
}

function k1_form_check_values($form_array, $table_array_config, &$db = null) {
    if (!is_array($form_array)) {
        die(__FUNCTION__ . " need an array to work on \$form_array");
    }
    if (!is_array($table_array_config)) {
        die(__FUNCTION__ . " need an array to work on \$required_array");
    }
    $error_array = array();
    foreach ($form_array as $key => $value) {
        //get the field label from the table config array
        $label = $table_array_config[$key]['label'];

        $error_header_msg = "El campo: '{$label}'";
        $error_msg = "";
        $error_type = "";

        /**
         *  TYPE CHECK
         *  -- then See each field value to check if is valid with the table tyoe definition
         */
        // MIN - MAX check
        $min = $table_array_config[$key]['min'];
        $max = $table_array_config[$key]['max'];


        // email | letters (solo letras) | numbers (solo numeros) | mixed (alfanumerico) | letters-simbols (con simbolos ej. !#()[],.) | numbers-simbols | mixed-simbols - los simbols no lo implementare aun
        // the basic error, if is required on the table definition
        if (($value !== 0) && ($value !== '0') && empty($value)) {
            if ($table_array_config[$key]['null'] === false) {
                $error_msg = "$error_header_msg es requerido " . var_dump($value, true);
            }
        } elseif ((strlen((string) $value) < (int) $min) || (strlen((string) $value) > (int) $max)) {
            $error_msg = "$error_header_msg debe ser de minimo $min y maximo $max caracteres";
        }
        if (($value === 0) || !empty($value)) {

            $preg_simbols = "-_@.,!#$%&'*\/+=?^`{\|}~";
            switch ($table_array_config[$key]['validation']) {
                case 'options':
                    $options = k1_get_table_enum_values($db, $table_array_config[$key]['table'], $key);
                    $options_fliped = array_flip($options);
                    if (!isset($options_fliped[$value])) {
                        $error_type = print_r($options_fliped, true) . " value: '$value'";
                        d($value, true);
                    }
                    break;
                case 'email':
                    $regex = "/[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,3})$/";
                    if (!preg_match($regex, $value)) {
                        $error_type = " Email invalido";
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
                case 'date':
                    global $date, $month, $day, $year;
                    if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                        if (!checkdate($matches['month'], $matches['day'], $matches['year'])) {
                            $error_type = " valores de fecha in validos";
                        }
                    } else {
                        $error_type = " formato de fecha invalida";
                    }
                    break;
                case 'date-past':
                    global $date, $month, $day, $year;
                    if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                        if (checkdate($matches['month'], $matches['day'], $matches['year'])) {
                            $actual_date_number = juliantojd($month, $day, $year);
                            $value_date_number = juliantojd($matches['month'], $matches['day'], $matches['year']);
                            if ($value_date_number > $actual_date_number) {
                                $error_type = " La fecha no puede ser mayor al dia de hoy: {$date}";
                            }
                        } else {
                            $error_type = " valores de fecha in validos";
                        }
                    } else {
                        $error_type = " formato de fecha invalida";
                    }
                    break;
                case 'date-future':
                    global $date, $month, $day, $year;
                    if (preg_match("/(?P<year>[0-9]{4})[\/-](?P<month>[0-9]{2})[\/-](?P<day>[0-9]{2})/", $value, $matches)) {
                        if (checkdate($matches['month'], $matches['day'], $matches['year'])) {
                            $actual_date_number = juliantojd($month, $day, $year);
                            $value_date_number = juliantojd($matches['month'], $matches['day'], $matches['year']);
                            if ($value_date_number <= $actual_date_number) {
                                $error_type = " La fecha debe ser mayor al dia de hoy: {$date}";
                            }
                        } else {
                            $error_type = " valores de fecha in validos";
                        }
                    } else {
                        $error_type = " formato de fecha invalida";
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
                        $error_type = " deber ser solo letras de la a-z y A-Z sin simbolos";
                    }
                    break;
                case 'letters-simbols':
                    $regex = "/^[a-zA-Z\s{$preg_simbols}]*$/";
                    if (!preg_match($regex, $value)) {
                        $error_type = " deber ser solo letras de la a-z y A-Z y simbolos: $preg_simbols";
                    }
                    break;
                case 'decimals':
                    $regex = "/^[\-0-9.]*$/";
                    if (!(preg_match($regex, $value) && is_numeric($value))) {
                        $error_type = " debe contener solo numeros y decimales";
                    } elseif ($table_array_config[$key]['unsigned']) {
                        if (($value + 0) < 0) {
                            $error_type .= " deber ser solo numeros y decimales positivos";
                        }
                    }
                    break;
                case 'numbers':
                    $regex = "/^[\-0-9]*$/";
                    if (!(preg_match($regex, $value) && is_numeric($value))) {
                        $error_type = " debe contener solo numeros";
                    } elseif ($table_array_config[$key]['unsigned']) {
                        if (($value + 0) < 0) {
                            $error_type .= " deber ser solo numeros positivos";
                        }
                    }
                    break;
                case 'numbers-simbols':
                    $regex = "/^[\-0-9\s{$preg_simbols}]*$/";
                    if (!preg_match($regex, $value)) {
                        $error_type = " debe contener solo numeros y simbolos: $preg_simbols";
                    }
                    break;
                case 'mixed':
                    $regex = "/^[\-a-zA-Z0-9\s]*$/";
                    if (!preg_match($regex, $value)) {
                        $error_type = " deber ser solo letras de la a-z y A-Z y numeros";
                    }
                    break;
                case 'mixed-simbols':
                    $regex = "/^[a-zA-Z0-9\s{$preg_simbols}]*$/";
                    if (!preg_match($regex, $value)) {
                        $error_type = " deber ser solo letras de la a-z y A-Z, numeros y simbolos: $preg_simbols";
                    }
                    break;
                default:
                    $error_type = "Not defined VALIDATION on Type '{$table_array_config[$key]['type']}' from field '{$label}' ";
                    break;
            }
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
        return false;
    }
}

/*
 * funcion para crear arreglo guia
 */

function k1_make_guide_array($data_array) {
    $guide_array = array();
    foreach ($data_array as $key => $value) {
        if (!is_array($value)) {
            $guide_array[] = $key;
        } else {
            $guide_array[$key] = k1_make_guide_array($value);
        }
    }
    return $guide_array;
}

function k1_clean_array_with_guide($array_to_clean, $guide_array) {
    foreach ($array_to_clean as $clean_key => $clean_value) {
        if (!isset($guide_array[$clean_key])) {
            unset($array_to_clean[$clean_key]);
        }
    }
    return $array_to_clean;
}

function k1_make_form_input_from_serialized($table_name, $field_name, $table_config_array, $mode = "view", $data_theme = 'b', $url_key_array = Array()) {
    global $form_errors;
    global $db;
    if (!is_string($table_name) || empty($table_name)) {
        die(__FUNCTION__ . " \$table_name should be an non empty string");
    }
    $value = k1_get_form_field_from_serialized($table_name, $field_name);
    // php Function apply from SQL 
    if (strstr($value, "function-") !== false) {
        $function_to_execute = strstr($value, "k1");
        eval("\$value = $function_to_execute;");
    }
    if (!isset($table_config_array[$field_name])) {
        if (empty($value)) {
            $value = "";
        }
        //die(__FUNCTION__ . " There is no field config for '$field_name' on config table");
        $html_code = <<<HTML
<div class="ui-field-contain">
    <label for="$field_name">{$field_name}</label>
    <span id='$field_name'>{$value}</span>
</div>
HTML;
        return $html_code;
    }
    $html_code = "";
    if (isset($form_errors[$field_name])) {
//            $data_theme = "d";
        $error_msg = "\n\t<div class='ui-body ui-body-d ui-corner-all'>{$form_errors[$field_name]}</div>";
    } else {
        $error_msg = "";
    }
    if ($mode == "edit") {
        /*
         * NORMAL INPUT TEXT
         */
        $type_comparation = strstr("char,varchar,text,date,datetime,tinyint,smallint,mediumint,int,bigint,float,double'", $table_config_array[$field_name]['type']);
        if (($type_comparation !== false) && ($table_config_array[$field_name]['sql'] == "")) {

            if ($table_config_array[$field_name]['null']) {
                $required_class = "class='required'";
            } else {
                $required_class = "";
            }
            $html_code = <<<HTML
   {$error_msg}
   <div class="ui-field-contain" data-theme='{$data_theme}'>
        <label for='{$field_name}'>{$table_config_array[$field_name]['label']}</label>
        <input type="text" name='{$field_name}' id='{$field_name}' value='{$value}' $required_class >
    </div>
HTML;
            return $html_code;
        } elseif (($table_config_array[$field_name]['type'] == "enum") || ($table_config_array[$field_name]['sql'] != "")) {
            /*
             * SELECT LIST
             */
            //ENUM drop list
            $select_data_array = array();
            if ($table_config_array[$field_name]['type'] == "enum") {
                $select_data_array = k1_get_table_enum_values($db, $table_config_array[$field_name]['table'], $field_name);
            } elseif ($table_config_array[$field_name]['sql'] != "") {
                $table_config_array[$field_name]['sql'];
                $sql_data = k1_sql_query($db, $table_config_array[$field_name]['sql'], true);
                if (!empty($sql_data)) {
                    foreach ($sql_data as $row) {
                        $select_data_array[$row['value']] = $row['label'];
                    }
                }
            } elseif (!empty($table_config_array[$field_name]['refereced_table_name'])) {
                $select_data_array = k1_get_labels_from_table($db, $table_config_array[$field_name]['refereced_table_name']);
            }
            $html_code = <<<HTML
    {$error_msg}
    <div class="ui-field-contain" data-theme='{$data_theme}'>
        <label for='{$field_name}'>{$table_config_array[$field_name]['label']}</label> 
HTML;

            $html_code .= "\n\t<select name='{$field_name}' id='{$field_name}' >\n";
            // if config says null so lets put the null value
            $select_data_array;
            if ($table_config_array[$field_name]['null']) {
                $i = 0;
                $new_data_array = array();
                foreach ($select_data_array as $key => $value) {
                    if ($i === 0) {
                        $new_data_array[''] = 'Seleccione un valor';
                    }
                    $new_data_array[$key] = $value;
                }
                unset($select_data_array);
                $select_data_array = $new_data_array;
            }
            $select_data_array;
            if (empty($value) && (!$table_config_array[$field_name]['null'])) {
                $value = $table_config_array[$field_name]['default'];
            }
            foreach ($select_data_array as $enum_key => $enum_value) {
                if (strtolower($enum_key) == strtolower($value)) {
                    $option_select = "selected";
                } else {
                    $option_select = "";
                }
                if ($table_config_array[$field_name]['type'] == "enum") {
                    $html_code .= "\n\t\t<option value='$enum_value' $option_select>$enum_value</option>";
                } else {
                    $html_code .= "\n\t\t<option value='$enum_key' $option_select>$enum_value</option>";
                }
            }
            $html_code .= "\n\t</select>\n</div>";
        } else {
            d($field_name);
        }
        return $html_code;
    } elseif (($mode == "view") || ($mode == "none")) {
        // only works for view mode or non-defined with on empty values
        if (($mode == "view") || (!empty($value) && ($mode == "none"))) {
            if (is_numeric($value)) {
//                $value = number_format($value);
            }
            // checks if the actual field is a foreign key to get his label from his table
            if (!empty($table_config_array[$field_name]['refereced_table_name'])) {
                $foreign_table = $table_config_array[$field_name]['refereced_table_name'];
                $foreign_table_key = $table_config_array[$field_name]['refereced_column_name'];
                $fk_label = k1_get_fk_field_label($foreign_table_key, $foreign_table, $url_key_array);
                if (!empty($fk_label)) {
                    $value = "{$fk_label} (ID:$value)";
                } else {
                    $value = "$value (FK)";
                }
            }
            $html_code = <<<HTML
<div class="ui-field-contain" data-theme='{$data_theme}'>
    <label for="$field_name">{$table_config_array[$field_name]['label']}</label>
    <span id='$field_name'>{$value}</span>
</div>
HTML;
            return $html_code;
        } else {
            return "";
        }
    }
}

function k1_get_labels_from_table(&$db, $table_name) {

    k1_db_check_object_type($db, __FUNCTION__);

    if (!is_string($table_name) || empty($table_name)) {
        die(__FUNCTION__ . " \$table_name should be an non empty string");
    }
    $table_config_array = k1_get_table_config($db, $table_name);
    $label_field = k1_get_table_label($table_config_array);
    $table_keys_array = k1_get_table_keys($table_config_array);
    if (!empty($table_keys_array)) {
        $table_config_array = array_flip($table_keys_array);
    }
    if (count($table_keys_array) === 1) {
        $key_filed = key($table_keys_array);
        $labels_sql = "SELECT $key_filed as value, $label_field as label FROM $table_name";
        $labels_data = k1_sql_query($db, $labels_sql);
        if (!empty($labels_data) && (count($labels_data) > 0)) {
            $label_array = array();
            foreach ($labels_data as $row) {
                $label_array[$row['value']] = $row['label'];
            }
            return $label_array;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

?>