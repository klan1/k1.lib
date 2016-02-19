<?php

namespace k1lib\crudlexs;

class input_helper {

    static $do_fk_search_tool = TRUE;
    static $url_to_search_fk_data = APP_URL . "utils/select-row-keys/";

    /**
     * *
     * @param \k1lib\crudlexs\class_db_table $db_table
     * @param array $db_table_row_data
     * @return \k1lib\html\select_tag
     */
    static function enum_type(creating $crudlex_obj, $row_to_apply, $field) {
        $enum_data = $crudlex_obj->db_table->get_enum_options($field);
        $input_tag = new \k1lib\html\select_tag($field);
        $input_tag->append_option("", creating_strings::$select_choose_option);

        foreach ($enum_data as $index => $value) {
            // SELETED work around
            if ($crudlex_obj->db_table_data[$row_to_apply][$field] == $value) {
                $selected = TRUE;
            } else {
                $selected = FALSE;
            }
            $input_tag->append_option($index, $value, $selected);
        }
        return $input_tag;
    }

    static function file_upload(creating $crudlex_obj, $field) {
        $field_encrypted = $crudlex_obj->encrypt_field_name($field);

        $input_tag = new \k1lib\html\input_tag("file", $field_encrypted, "", "k1-file-upload");
        if (isset($crudlex_obj->db_table_data[1][$field]['name']) || empty($crudlex_obj->db_table_data[1][$field])) {
            return $input_tag;
        } else {
            $delete_file_link = new \k1lib\html\a_tag("./unlink-uploaded-file/" . $field_encrypted . "/?auth-code=%auth_code%", "Remove %field_value%");

            $div_container = new \k1lib\html\div_tag();
            $div_container->append_child($input_tag);
            $div_container->append_child($delete_file_link);
            $div_container->link_value_obj($input_tag);

            return $div_container;
        }
    }

    static function default_type(creating $crudlex_obj, $field) {
        $field_encrypted = $crudlex_obj->encrypt_field_name($field);
        if (!empty($crudlex_obj->db_table->get_field_config($field, 'refereced_table_name'))) {
            $div_input_group = new \k1lib\html\div_tag("input-group");

            $input_tag = new \k1lib\html\input_tag("text", $field_encrypted, NULL, "k1-input-insert input-group-field");
            $input_tag->set_attrib("placeholder", "Use the reference ID");
            $input_tag->set_attrib("k1-data-group-" . $crudlex_obj->db_table->get_field_config($field, 'refereced_table_name'), TRUE);
            $input_tag->append_to($div_input_group);

            $div_input_group_button = new \k1lib\html\div_tag("input-group-button");
            $div_input_group_button->append_to($div_input_group);

            /**
             * FK TABLE EXTRACTOR
             */
            $refereced_column_config = $crudlex_obj->db_table->get_field_config($field, 'refereced_column_config');
//            while (!empty($refereced_column_config['refereced_column_config'])) {
////                $refereced_column_config = $refereced_column_config['refereced_column_config'];
//            }
            $this_table = $crudlex_obj->db_table->get_db_table_name();
            $this_table_alias = \k1lib\db\security\db_table_aliases::encode($this_table);

            $fk_table = $refereced_column_config['table'];
            $fk_table_alias = \k1lib\db\security\db_table_aliases::encode($fk_table);

//            $crudlex_obj->set_do_table_field_name_encrypt();
            $static_values = $crudlex_obj->db_table->get_constant_fields();
            $static_values_enconded = $crudlex_obj->encrypt_field_names($static_values);
            $static_values_enconded_as_get_text = \k1lib\common\array_to_url_parameters($static_values_enconded);

            $back_url = urlencode($_SERVER['REQUEST_URI']);


            $search_button = new \k1lib\html\input_tag("button", "search", "&#xf18d;", "button fi-page-search");
            $search_button->set_attrib("style", "font-family:foundation-icons");

            if (self::$do_fk_search_tool) {
                $url_to_search_fk_data = self::$url_to_search_fk_data . "{$fk_table_alias}/list/$this_table_alias/?back-url={$back_url}&{$static_values_enconded_as_get_text}";
                $search_button->set_attrib("onclick", "javascript:use_select_row_keys(this.form,'{$url_to_search_fk_data}')");
            } else {
                $url_to_search_fk_data = "#";
                $search_button->set_attrib("onclick", "javascript:alert('Search on another table is not possible here, use the Key value to search')");
            }

            $search_button->append_to($div_input_group_button);

            $div_input_group->link_value_obj($input_tag);
            return $div_input_group;
        } elseif (strstr("date,date-past,date-future", $crudlex_obj->db_table->get_field_config($field, 'validation')) !== FALSE) {
            $div_input_group = new \k1lib\html\div_tag("input-group");

            $input_tag = new \k1lib\html\input_tag("text", $field_encrypted, NULL, "k1-input-insert input-group-field datepicker");
            $input_tag->set_attrib("placeholder", "Click here to pick a date");
            $input_tag->set_attrib("k1-data-datepickup", TRUE);
            $input_tag->append_to($div_input_group);

            $div_input_group_button = new \k1lib\html\div_tag("input-group-button");
            $div_input_group_button->append_to($div_input_group);

            $search_button = new \k1lib\html\a_tag("#", "", "_self", "Search", "button fi-calendar");
            $search_button->append_to($div_input_group_button);

            $div_input_group->link_value_obj($input_tag);
            return $div_input_group;
        } else {
            $input_tag = new \k1lib\html\input_tag("text", $field_encrypted, NULL, "k1-input-insert");
            $input_tag->set_attrib("placeholder", $crudlex_obj->db_table->get_field_config($field, 'placeholder'));
            return $input_tag;
        }
    }

}
