<?php

namespace k1lib\crudlexs\object;

use k1lib\urlrewrite\url as url;

class input_helper {

    static $do_fk_search_tool = TRUE;
    static $url_to_search_fk_data = APP_URL . "general-utils/select-row-keys/";
    static $url_to_send_row_keys_fk_data = APP_URL . "general-utils/send-row-keys/";
    static $main_css = "";
    static private $fk_fields_to_skip = [];
    static public $boolean_true = NULL;
    static public $boolean_false = NULL;

    static function password_type(creating $crudlex_obj, $field, $case = "create") {
        // First we have the CLEAR the password data, we do not need it!
        $field_encrypted = $crudlex_obj->encrypt_field_name($field) . "_password";
        $tag_id = $crudlex_obj->encrypt_field_name($field) . "-reveal";
        $crudlex_obj->db_table_data_filtered[1][$field] = null;

        $div_continer = new \k1lib\html\div();

        $input_tag_new = new \k1lib\html\input("password", $field_encrypted . "_new", NULL, "k1lib-input-insert");
        $input_tag_confirm = new \k1lib\html\input("password", $field_encrypted . "_confirm", NULL, "k1lib-input-insert");

        if ($case == "create") {
            $div_continer->link_value_obj($input_tag_new);
        } elseif ($case == "update") {
            $input_tag_current = new \k1lib\html\input("password", $field_encrypted . "_current", NULL, "k1lib-input-insert");
            $input_tag_current->set_attrib("placeholder", "Current password");
            $div_continer->append_div()->append_child($input_tag_current);
            $div_continer->link_value_obj($input_tag_current);
        }
        $input_tag_new->set_attrib("placeholder", "New password");
        $input_tag_confirm->set_attrib("placeholder", "Confirm password");

        $div_continer->append_div()->append_child($input_tag_new);
        $div_continer->append_div()->append_child($input_tag_confirm);

        return $div_continer;
    }

    /**
     * *
     * @param \k1lib\crudlexs\db_table $db_table
     * @param array $db_table_row_data
     * @return \k1lib\html\select
     */
    static function enum_type(creating $crudlex_obj, $field) {
        /**
         * @todo Use FIELD encryption here, I tried but it doesn't work just pasting the normal lines
         */
        $enum_data = $crudlex_obj->db_table->get_enum_options($field);
        $input_tag = new \k1lib\html\select($field);
        $input_tag->append_option("", input_helper_strings::$select_choose_option);

        foreach ($enum_data as $index => $value) {
            // SELETED work around
            if ($crudlex_obj->db_table_data[1][$field] == $value) {
                $selected = TRUE;
            } else {
                $selected = FALSE;
            }
            $input_tag->append_option($index, $value, $selected);
        }
        return $input_tag;
    }

    /**
     * 
     * @param \k1lib\crudlexs\creating $crudlex_obj
     * @param int $row_to_apply
     * @param string $field
     * @return \k1lib\html\textarea
     */
    static function text_type(creating $crudlex_obj, $field, $load_tinymce = TRUE) {
        // Row to apply is constant coz this is CREATE or EDIT and there is allways just 1 set of data to manipulate.
        $field_encrypted = $crudlex_obj->encrypt_field_name($field);

        if (!empty(self::$main_css)) {
            $css_option = "content_css: ['" . self::$main_css . "?' + new Date().getTime()],";
        } else {
            $css_option = "";
        }
        $input_tag = new \k1lib\html\textarea($field_encrypted);
        $input_tag->set_attrib("rows", 5);

        if ($load_tinymce) {
            $html_script = "tinymce.init({ "
                    . "selector: '#$field_encrypted',"
                    . "height: 120,"
                    . "plugins: [ 
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code'
                ],"
                    . $css_option
                    . "body_class: 'html-editor',"
//                . "content_style: 'div {margin: 100px; border: 50px solid red; padding: 3px}',"
                    . "toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',"
                    . "paste_data_images: true,"
                    . ""
                    . "});";
            $script = (new \k1lib\html\script())->set_value($html_script);
            $input_tag->post_code($script->generate());
        }

        return $input_tag;
    }

    static function file_upload(creating $crudlex_obj, $field) {
        $field_encrypted = $crudlex_obj->encrypt_field_name($field);

        $input_tag = new \k1lib\html\input("file", $field_encrypted, "", "k1lib-file-upload");
        if (isset($crudlex_obj->db_table_data[1][$field]['name']) || empty($crudlex_obj->db_table_data[1][$field])) {
            return $input_tag;
        } else {
            $delete_file_link = new \k1lib\html\a("./unlink-uploaded-file/" . $field_encrypted . "/?auth-code=--authcode--&back-url=" . urlencode(\k1lib\urlrewrite\get_back_url()), input_helper_strings::$button_remove);
            $div_container = new \k1lib\html\div(null, "img-delete-link");
            $div_container->append_child($input_tag);
            $div_container->append_child($delete_file_link);
            $div_container->link_value_obj($input_tag);

            return $div_container;
        }
    }

    static function boolean_type(creating $crudlex_obj, $field) {
        /*
          <div class="switch tiny">
          <input class="switch-input" id="tinySwitch" type="checkbox" name="exampleSwitch">
          <label class="switch-paddle" for="tinySwitch">
          <span class="show-for-sr">Tiny Sandwiches Enabled</span>
          </label>
          </div>
         */
        // Row to apply is constant coz this is CREATE or EDIT and there is allways just 1 set of data to manipulate.
//        d(self::$boolean_true, true);
        if (self::$boolean_true === NULL) {
//            d('yes');
            self::$boolean_true = \k1lib\common_strings::$yes;
        }
//        d(self::$boolean_false, true);
        if (self::$boolean_false === NULL) {
//            d('no');
            self::$boolean_false = \k1lib\common_strings::$no;
        }

        $field_encrypted = $crudlex_obj->encrypt_field_name($field);


        $input_div = new \k1lib\html\div();
        $input_div->link_value_obj(new \k1lib\html\span('hidden'));

        $input_yes = new \k1lib\html\input("radio", $field_encrypted, '1');
        $label_yes = new \k1lib\html\label(self::$boolean_true, $field_encrypted);
        $input_yes->post_code($label_yes->generate());
        $input_yes->append_to($input_div);

        if ($crudlex_obj->db_table_data[1][$field] == '1') {
            $input_yes->set_attrib('checked', TRUE);
        }

        $input_no = new \k1lib\html\input("radio", $field_encrypted, '0');
        $label_no = new \k1lib\html\label(self::$boolean_false, $field_encrypted);
        $input_no->post_code($label_no->generate());
        $input_no->append_to($input_div);

        if ($crudlex_obj->db_table_data[1][$field] == '0') {
            $input_no->set_attrib('checked', TRUE);
        }

        return $input_div;
    }

    static function default_type(creating $crudlex_obj, $field) {
        $field_encrypted = $crudlex_obj->encrypt_field_name($field);
        if ((!empty($crudlex_obj->db_table->get_field_config($field, 'refereced_table_name')) && self::$do_fk_search_tool) && (array_search($field, self::$fk_fields_to_skip) === FALSE)) {
            $div_input_group = new \k1lib\html\div("input-group");

            $input_tag = new \k1lib\html\input("text", $field_encrypted, NULL, "k1lib-input-insert input-group-field");
            if (!empty($crudlex_obj->db_table->get_field_config($field, 'placeholder'))) {
                $input_tag->set_attrib("placeholder", $crudlex_obj->db_table->get_field_config($field, 'placeholder'));
            } else {
                $input_tag->set_attrib("placeholder", input_helper_strings::$input_fk_placeholder);
            }
            $input_tag->set_attrib("k1lib-data-group-" . $crudlex_obj->db_table->get_field_config($field, 'refereced_table_name'), TRUE);
            $input_tag->append_to($div_input_group);

            $div_input_group_button = new \k1lib\html\div("input-group-button");
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

            $search_button = new \k1lib\html\input("button", "search", "&#xf18d;", "button fi-page-search fk-button");
            $search_button->set_attrib("style", "font-family:foundation-icons");

            $url_params = [
                "back-url" => $_SERVER['REQUEST_URI']
            ];
            $url_params = array_merge($static_values_enconded, $url_params);

            $url_to_search_fk_data = url::do_url(self::$url_to_search_fk_data . "{$fk_table_alias}/list/$this_table_alias/", $url_params);
            $search_button->set_attrib("onclick", "javascript:use_select_row_keys(this.form,'{$url_to_search_fk_data}')");

            $search_button->append_to($div_input_group_button);

            $div_input_group->link_value_obj($input_tag);
            return $div_input_group;
        } elseif (strstr("date,date-past,date-future", $crudlex_obj->db_table->get_field_config($field, 'validation')) !== FALSE) {
            $div_input_group = new \k1lib\html\div("input-group");

            $input_tag = new \k1lib\html\input("text", $field_encrypted, NULL, "k1lib-input-insert input-group-field");
            $input_tag->set_attrib("placeholder", input_helper_strings::$input_date_placeholder);
            $input_tag->set_attrib("k1lib-data-datepickup", TRUE);
            $input_tag->append_to($div_input_group);

            $div_input_group_button = new \k1lib\html\div("input-group-button");
            $div_input_group_button->append_to($div_input_group);

            $search_button = new \k1lib\html\a("#", "", "_self", "button fi-calendar");
            $search_button->append_to($div_input_group_button);

            $div_input_group->link_value_obj($input_tag);
            return $div_input_group;
        } else {
            $input_tag = new \k1lib\html\input("text", $field_encrypted, NULL, "k1lib-input-insert");
            $input_tag->set_attrib("placeholder", $crudlex_obj->db_table->get_field_config($field, 'placeholder'));
            return $input_tag;
        }
    }

    public static function get_do_fk_search_tool() {
        return self::$do_fk_search_tool;
    }

    public static function get_fk_fields_to_skip() {
        return self::$fk_fields_to_skip;
    }

    public static function set_do_fk_search_tool($do_fk_search_tool) {
        self::$do_fk_search_tool = $do_fk_search_tool;
    }

    public static function set_fk_fields_to_skip(array $fk_fields_to_skip) {
        self::$fk_fields_to_skip = $fk_fields_to_skip;
    }

}
