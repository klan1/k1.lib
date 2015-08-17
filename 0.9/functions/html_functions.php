<?php

namespace k1lib\html\functions {

    use k1lib\html\classes as html_classes;

    function label_input_text_combo($field_name, $value, $label, $required = false, $error_msg = "") {

//    function \k1lib\html\functions\input_label_combo(&$field_name, &$value, &$table_config_array, &$error_msg = "") {
        if ($required) {
            $required_class = "required";
        } else {
            $required_class = "";
        }
        $label_object = new html_classes\label_tag($label, $field_name, "right inline");
        $input_object = new html_classes\input_tag("text", $field_name, $value, $required_class);
        $input_object->set_attrib("required", (!empty($required_class)) ? TRUE : FALSE);

        if (!empty($error_msg)) {
            $input_object->set_attrib("class", "error", true);
            $html_template = load_html_template("label_input_combo-error");
            $html_code = sprintf($html_template, $label_object->generate_tag(), $input_object->generate_tag(), $error_msg);
        } else {
            $html_template = load_html_template("label_input_combo");
            $html_code = sprintf($html_template, $label_object->generate_tag(), $input_object->generate_tag());
        }
        return $html_code;
    }

    function load_html_template($function_name) {
        $function_name = \str_replace("\\", "/", $function_name);
        $file_to_load = \k1lib\TEMPLATES_PATH . "/" . \basename($function_name) . ".html";
        if (\file_exists($file_to_load)) {
            $template_content = \file_get_contents($file_to_load);
            return $template_content;
        }
        \trigger_error("No se ha podido cargar: {$file_to_load}", E_USER_WARNING);
        return false;
    }

    function select_list_from_array($name, $data_array, $default_value = "", $allow_empty = false, $class = "", $id = "") {
        $select_object = new html_classes\select_tag($name);
        $select_object->set_attrib("class", $class, true);
        $select_object->set_attrib("id", $id);

        if ($allow_empty) {
            $select_object->append_option("", "Seleccione una opcion");
        }

        foreach ($data_array as $value => $label) {
            $select_object->append_option($value, $label, (($value === $default_value) ? TRUE : FALSE));
        }
        return $select_object->generate_tag();
    }

}