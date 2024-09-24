<?php //

/**
 * HTML Related funcions that uses the HTML Classes
 */

namespace k1lib\html;

use k1lib\html as html_classes;

/**
 * Loads files and get contents from templates directory from k1.lib no from the app template directory.
 * Returns FALSE on load error.
 * @param string $function_name
 * @return String
 */
function load_html_template($function_name) {
    $function_name_fixed = \str_replace("\\", "/", $function_name);
    $file_to_load = "/" . \basename($function_name_fixed) . ".html";
    \trigger_error("Please write the file: {$file_to_load} as k1.lib-dom class. From: {$function_name}", E_USER_WARNING);
    return FALSE;
}

/**
 * Using a HTML Template makes the couple of label-inpunt
 * @param string $field_name
 * @param string $value
 * @param string $label
 * @param bool $required
 * @param string $error_msg
 * @param string $label_link 
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
        $a_object = new a($label_link, $label);
        $a_object->set_attrib("class", "search-override-submit", TRUE);
        $label = $a_object->generate();
    }

    $label_object = new html_classes\label($label, $field_name, "right inline");
    $input_object = new html_classes\input("text", $field_name, $value, $required_class);
//    $input_object->set_attrib("required", (!empty($required_class)) ? TRUE : FALSE);

    if (!empty($error_msg)) {
        $input_object->set_attrib("class", "error", TRUE);
        $html_template = load_html_template("label_input_combo-error");
        $html_code = sprintf($html_template, $label_object->generate(), $input_object->generate(), $error_msg);
    } else {
        $html_template = load_html_template("label_input_combo");
        $html_code = sprintf($html_template, $label_object->generate(), $input_object->generate());
    }
    return $html_code;
}

/**
 * Using a HTML Template makes the couple of label-inpunt
 * @param string $field_name
 * @param string $value
 * @param string $label
 * @param bool $required
 * @param string $error_msg
 * @param string $label_link 
 * @return String
 */
function label_text_combo($label, $value) {

//    function \k1lib\html\input_label_combo(&$field_name, &$value, &$table_config_array, &$error_msg = "") {
//    $label_object = new html_classes\label($label, "", "right inline");

    $html_template = load_html_template("label-input-combo");
    $html_code = sprintf($html_template, $label, $value);

    return $html_code;
}

/**
 * Using a HTML Template makes the couple of label-inpunt as 2 columns
 * @param string $field_name
 * @param string $value
 * @param string $label
 * @param bool $required
 * @param string $error_msg
 * @param string $label_link 
 * @return String
 */
function label_text_combo_2columns($label, $value) {

//    function \k1lib\html\input_label_combo(&$field_name, &$value, &$table_config_array, &$error_msg = "") {
//    $label_object = new html_classes\label($label, "", "right inline");

    $html_template = load_html_template("row-2-columns");
    $html_code = sprintf($html_template, $label, $value);

    return $html_code;
}

/**
 * Generate a <SELECT></SELECT> HTML tag object with options from an Array() 
 * @param string $name
 * @param array $data_array
 * @param type $default_value
 * @param bool $allow_empty
 * @param string $class
 * @param string $id
 * @return html_classes\select_tag
 */
function select_list_from_array($name, $data_array, $default_value = "", $allow_empty = FALSE, $class = "", $id = "", $select_message = 'Select an option') {
    $select_object = new html_classes\select($name);
    $select_object->set_attrib("class", $class, TRUE);
    $select_object->set_attrib("id", $id);

    if ($allow_empty) {
        $select_object->append_option("", $select_message);
    }

    foreach ($data_array as $value => $label) {
        $select_object->append_option($value, $label, (($value === $default_value) ? TRUE : FALSE));
    }
    return $select_object;
}

/**
 * Generate a <TABLE></TABLE> HTML tag with data from an Array() 
 * @param array $data_array
 * @param bool $has_header
 * @param string $class
 * @param string $id
 * @return String
 */
function table_from_array(&$data_array, $has_header = TRUE, $class = "", $id = "", $text_limit_to_trim = null) {
    if ((count($data_array) == 0) || (count(current($data_array)) == 0)) {
        trigger_error("Array to build HTML table is empty", E_USER_NOTICE);
        return FALSE;
    }
    $table_object = new html_classes\table($class, $id);

    foreach ($data_array as $row_index => $row_data) {
        if ($has_header && ($row_index === 0)) {
            $thead = $table_object->append_thead();
            $tr = $thead->append_tr();
        } else {
            if (!isset($tbody)) {
                $tbody = $table_object->append_tbody();
            }
            $tr = $tbody->append_tr();
        }
        foreach ($row_data as $col_index => $col_value) {
            if ($has_header && ($row_index === 0)) {
                $tr->append_th($col_value);
            } else {
                if (!is_object($col_value)) {
                    if (is_numeric($col_value)) {
                        if (is_float($col_value)) {
                            $col_value = number_format($col_value, 2);
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
 * @param string $linkTo
 * @param string $label
 * @param bool $mini
 * @param bool $inline
 * @param bool $keep_get_vars
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
        "search" => ["buscar", "search", "locate", "ubicar"],
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
            $button_icon = "bi bi-download";
            $theme = "btn-outline-secondary";
            break;
        case "back":
            $button_icon = "bi bi-arrow-left";
            $theme = "btn-outline-secondary";
            break;
        case "go":
            $button_icon = "bi bi-check";
            $theme = "btn-outline-success";
            break;
        case "cancel":
            $button_icon = "bi bi-x";
            $theme = "btn-outline-secondary";
            break;
        case "ok":
            $button_icon = "bi bi-check";
            $theme = "btn-outline-success";
            break;
        case "view":
            $button_icon = "bi bi-clipboard2";
            $theme = "btn-outline-primary";
            break;
        case "new":
            $button_icon = "bi bi-plus";
            $theme = "btn-outline-success";
            break;
        case "edit":
            $button_icon = "bi bi-pencil-square";
            $theme = "btn-outline-primary";
            break;
        case "delete":
            $button_icon = "bi bi-exclamation-triangle-fill";
            $theme = "btn-outline-warning";
            $js_confirm_dialog = TRUE;
            break;
        case "list":
            $button_icon = "bi bi-grid-3x3";
            $theme = "btn-outline-primary";
            break;
        case "search":
            $button_icon = "bi bi-search";
            $theme = "btn-outline-primary";
            break;
        default:
            $button_icon = "bi bi-link";
            $theme = "btn-outline-secondary";
            break;
    }
    $icon = new \k1lib\html\i(NULL, $button_icon);

    $button_object = new \k1lib\html\a($linkTo, "$icon " . $label, "_self", "btn icon {$theme} {$class}", $id);
//    $button_object->set_attrib("class", "$button_icon", TRUE);
//    $button_object->set_attrib("class", "btn icon $theme", TRUE);
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
    trigger_error("NOOOOOOOoooooo " . __FUNCTION__, E_USER_WARNING);
}

function generate_row_2columns_layout(tag $parent, $row_data, $row_data_headers = null) {
    $row = 0;
    foreach ($row_data as $field => $value) {
        $row++;
        if (isset($row_data_headers) && array_key_exists($field, $row_data_headers)) {
            $field_label = $row_data_headers[$field];
        } else {
            $field_label = $field;
        }
        (new foundation\label_value_row($field_label, $value, $row))->append_to($parent);
    }
}
