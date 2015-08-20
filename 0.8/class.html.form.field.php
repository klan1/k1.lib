<?php

class k1_form_field {

    var $form_name;
    var $data_array;
    var $fields_labels;
    var $fields_required;
    var $fields_errors;
    var $fields_to_edit;

    function k1_form_field($form_name) {
        $this->form_name = $form_name;
        $this->auto_assing_arrays();
    }

    private function auto_assing_arrays() {
        $auto_name_array[] = "fields_label";
        $auto_name_array[] = "fields_required";
        $auto_name_array[] = "fields_errors";
        $auto_name_array[] = "fields_to_edit";
       
        /**
         * FIELDS LABELS
         */
        foreach ($auto_name_array as $value) {
            $var_name = $form_name . "_" - $value;
            if (isset(${$var_name}) && (is_array(${$var_name}))) {
                $this->{$value} = ${$var_name};
            }
        }
    }

    function assing_label_array() {
        
    }

    function do_html() {
        $value = k1_get_form_field_from_serialized($form_name, $field_name, $default);
        if ($required) {
            $required_class = "class='required";
            if ($required !== true) {
                $required_class .= " $required'";
            } else {
                $required_class .= "'";
            }
        } else {
            $required_class = "";
        }
        $html_code = <<<HTML
    
   <li data-role='fieldcontain' data-theme='{$data_theme}'>
        <label for='{$field_name}'>{$label}</label>
        <input type='text' name='{$field_name}' id='{$field_name}' value='{$value}' $required_class />
    </li>
HTML;
        return $html_code;
    }

}

?>
