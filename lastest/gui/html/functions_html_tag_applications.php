<?php

/**
 * HTML Related funcions that uses the HTML Classes
 */

namespace k1lib\html;

use k1lib\html as html_classes;

/**
 * Loads files and get contents from templates directory from k1.lib no from the app template directory.
 * Returns FALSE on load error.
 * @param String $function_name
 * @return String
 */
function load_html_template($function_name) {
    $function_name_fixed = \str_replace("\\", "/", $function_name);
    $file_to_load = \k1lib\HTML_TEMPLATES_PATH . "/" . \basename($function_name_fixed) . ".html";
    if (\file_exists($file_to_load)) {
        $template_content = \file_get_contents($file_to_load);
        return $template_content;
    }
    \trigger_error("No se ha podido cargar: {$file_to_load}", E_USER_WARNING);
    return FALSE;
}

/**
 * Using a HTML Template makes the couple of label-inpunt
 * @param String $field_name
 * @param String $value
 * @param String $label
 * @param Boolean $required
 * @param String $error_msg
 * @param String $label_link 
 * @return String
 */
function label_input_text_combo($field_name, $value, $label, $required = FALSE, $error_msg = "", $label_link = null) {

//    function \k1lib\html\input_label_combo(&$field_name, &$value, &$table_config_array, &$error_msg = "") {
    if ($required) {
        $required_class = "";
    } else {
        $required_class = "";
    }
    if ($label_link == TRUE) {
        $a_object = new a_tag($label_link, $label);
        $a_object->set_attrib("class", "search-override-submit", TRUE);
        $label = $a_object->generate_tag();
    }

    $label_object = new html_classes\label_tag($label, $field_name, "right inline");
    $input_object = new html_classes\input_tag("text", $field_name, $value, $required_class);
//    $input_object->set_attrib("required", (!empty($required_class)) ? TRUE : FALSE);

    if (!empty($error_msg)) {
        $input_object->set_attrib("class", "error", TRUE);
        $html_template = load_html_template("label_input_combo-error");
        $html_code = sprintf($html_template, $label_object->generate_tag(), $input_object->generate_tag(), $error_msg);
    } else {
        $html_template = load_html_template("label_input_combo");
        $html_code = sprintf($html_template, $label_object->generate_tag(), $input_object->generate_tag());
    }
    return $html_code;
}

/**
 * Using a HTML Template makes the couple of label-inpunt
 * @param String $field_name
 * @param String $value
 * @param String $label
 * @param Boolean $required
 * @param String $error_msg
 * @param String $label_link 
 * @return String
 */
function label_text_combo($label, $value) {

//    function \k1lib\html\input_label_combo(&$field_name, &$value, &$table_config_array, &$error_msg = "") {
//    $label_object = new html_classes\label_tag($label, "", "right inline");

    $html_template = load_html_template("label-input-combo");
    $html_code = sprintf($html_template, $label, $value);

    return $html_code;
}

/**
 * Using a HTML Template makes the couple of label-inpunt as 2 columns
 * @param String $field_name
 * @param String $value
 * @param String $label
 * @param Boolean $required
 * @param String $error_msg
 * @param String $label_link 
 * @return String
 */
function label_text_combo_2columns($label, $value) {

//    function \k1lib\html\input_label_combo(&$field_name, &$value, &$table_config_array, &$error_msg = "") {
//    $label_object = new html_classes\label_tag($label, "", "right inline");

    $html_template = load_html_template("row-2-columns");
    $html_code = sprintf($html_template, $label, $value);

    return $html_code;
}

/**
 * Generate a <SELECT></SELECT> HTML tag object with options from an Array() 
 * @param String $name
 * @param Array $data_array
 * @param type $default_value
 * @param Boolean $allow_empty
 * @param String $class
 * @param String $id
 * @return html_classes\select_tag
 */
function select_list_from_array($name, $data_array, $default_value = "", $allow_empty = FALSE, $class = "", $id = "") {
    $select_object = new html_classes\select_tag($name);
    $select_object->set_attrib("class", $class, TRUE);
    $select_object->set_attrib("id", $id);

    if ($allow_empty) {
        $select_object->append_option("", "Seleccione una opcion");
    }

    foreach ($data_array as $value => $label) {
        $select_object->append_option($value, $label, (($value === $default_value) ? TRUE : FALSE));
    }
    return $select_object;
}

/**
 * Generate a <TABLE></TABLE> HTML tag with data from an Array() 
 * @param Array $data_array
 * @param Boolean $has_header
 * @param String $class
 * @param String $id
 * @return String
 */
function table_from_array(&$data_array, $has_header = TRUE, $class = "", $id = "", $text_limit_to_trim = null) {
    if ((count($data_array) == 0) || (count(current($data_array)) == 0)) {
        trigger_error("Array to build HTML table is empty", E_USER_NOTICE);
        return FALSE;
    }
    $table_object = new html_classes\table_tag($class, $id);

    foreach ($data_array as $row_actual_index => $row_data) {
        if ($has_header && ($row_actual_index === 0)) {
            $thead = $table_object->append_thead();
            $tr = $thead->append_tr();
        } else {
            if (!isset($tbody)) {
                $tbody = $table_object->append_tbody();
            }
            $tr = $tbody->append_tr();
        }
        foreach ($row_data as $col_index => $col_value) {
            if ($has_header && ($row_actual_index === 0)) {
                $tr->append_th($col_value);
            } else {
                if (!is_object($col_value)) {
                    if (is_numeric($col_value)) {
                        if (is_float($col_value)) {
                            $col_value = number_format($col_value,2);
                        } else {
                            $col_value = number_format($col_value);
                        }
                    }
                    if (is_numeric($text_limit_to_trim) && strlen($col_value) > $text_limit_to_trim) {
                        $col_value = substr($col_value, 0, $text_limit_to_trim) . "...";
                    }
                } else {
                    if (is_numeric($text_limit_to_trim) && strlen($col_value->get_value()) > $text_limit_to_trim) {
                        $col_value->set_value(substr($col_value->get_value(), 0, $text_limit_to_trim) . "...");
                    }
//                    d($col_value->get_value());
                }
                $tr->append_td($col_value);
            }
        }
    }
//        \var_dump($table_object);
    return $table_object;
}

/**
 * 
 * @param String $linkTo
 * @param String $label
 * @param Boolean $mini
 * @param Boolean $inline
 * @param Boolean $keep_get_vars
 * @return \k1lib\html\a_tag
 */
function get_link_button($linkTo, $label, $class = "", $id = "") {
    if ($linkTo == NULL) {
        return NULL;
    } elseif (!(is_string($linkTo) && is_string($label))) {
        die(__FUNCTION__ . " The parameters are not string");
    }

    $possible_strings = [
        "export" => ["exportar", "export",],
        "back" => ["back", "volver", "atras", "retroceder", "regresar"],
        "go" => ["ir", "go"],
        "ok" => ["aceptar", "si", "yes", "accept",],
        "cancel" => ["cancelar", "cancel", "no", "not"],
        "view" => ["ver", "mostrar", "view", "show"],
        "new" => ["agregar", "nuev", "new", "add", "añadir", "crear", "generar"],
        "edit" => ["edit", "editar", "cambiar", "change"],
        "delete" => ["delete", "borrar", "eliminar", "suprimir", "quitar", "cancelar"],
        "list" => ["lista", "list", "all data", "view data", "data", "todos"],
    ];

    $label_low = strtolower($label);
    $possible_action = "";
    foreach ($possible_strings as $possible_action_loop => $words) {
        foreach ($words as $word) {
            if (strstr($label_low, $word) !== FALSE) {
                $possible_action = $possible_action_loop;
                break 2;
            }
        }
    }

    $js_confirm_dialog = FALSE;
    switch ($possible_action) {
        case "export":
            $button_icon = "fi-download";
            $theme = "secondary";
            break;
        case "back":
            $button_icon = "fi-arrow-left";
            $theme = "secondary";
            break;
        case "go":
            $button_icon = "fi-check";
            $theme = "success";
            break;
        case "cancel":
            $button_icon = "fi-x";
            $theme = "alert";
            break;
        case "ok":
            $button_icon = "fi-check";
            $theme = "success";
            break;
        case "view":
            $button_icon = "fi-clipboard-notes";
            $theme = "";
            break;
        case "new":
            $button_icon = "fi-plus";
            $theme = "success";
            break;
        case "edit":
            $button_icon = "fi-clipboard-pencil";
            $theme = "";
            break;
        case "delete":
            $button_icon = "fi-page-delete";
            $theme = "alert";
            $js_confirm_dialog = TRUE;
            break;
        case "list":
            $button_icon = "fi-list";
            $theme = "primary";
            break;
        default:
            $button_icon = "fi-widget";
            $theme = "secondary";
            break;
    }

    $button_object = new \k1lib\html\a_tag($linkTo, " " . $label, "_self", "Button", "button {$class}", $id);
    $button_object->set_attrib("class", "$button_icon", TRUE);
    $button_object->set_attrib("class", "$theme", TRUE);
    if ($js_confirm_dialog) {
        $button_object->set_attrib("onclick", "return confirm('Esta seguro que desea hacer esto ?\\n\\nEsta accion no se podra deshacer.')");
    }

    return $button_object;
}

function make_form_label_input_layout($row_data, $extra_css_clasess = "", $row_data_headers = null) {
    $form_layout = "";
    $index = 0;
    foreach ($row_data as $field => $value) {
        $form_layout .= label_text_combo($row_data_headers[$index], $value);
        $index++;
    }
//    return $div_row;
    return $form_layout;
}

function make_row_2columns_layout($row_data, $extra_css_clasess = "", $row_data_headers = null) {
    $form_layout = "";
    $index = 0;
    foreach ($row_data as $field => $value) {
        if (isset($row_data_headers) && array_key_exists($field, $row_data_headers)) {
            $form_layout .= label_text_combo_2columns($row_data_headers[$field], $value);
            $index++;
        } else {
            $form_layout .= label_text_combo_2columns($field, $value);
        }
    }
//    return $div_row;
    return $form_layout;
}
