<?php

namespace k1lib\crudlexs;

/**
 * 
 */
class creating extends crudlexs_base_with_data implements crudlexs_base_interface {

    /**
     *
     * @var Array
     */
    protected $headers_array = [];

    /**
     *
     * @var Array
     */
    protected $blank_row_array = [];

    /**
     *
     * @var Array
     */
    protected $post_incoming_array = [];

    /**
     *
     * @var Boolean
     */
    protected $do_table_field_name_encrypt = FALSE;

    /**
     *
     * @var Array
     */
    protected $post_validation_errors = [];

    /**
     * Override the original function to create an empty array the meets the requiriements for all the metods
     * @return boolean
     */
    public function load_db_table_data($blank_data = FALSE) {
        if (!$blank_data) {
            return parent::load_db_table_data();
        } else {
            if (!$this->db_table->get_db_table_show_rule()) {
                $this->db_table->set_db_table_show_rule("show-all");
            }
            $show_rule = $this->db_table->get_db_table_show_rule();

            foreach ($this->db_table->get_db_table_config() as $field => $config) {
                if ($config[$show_rule]) {
                    $this->headers_array[] = $field;
                    $this->blank_row_array[$field] = "";
                }
            }
            if (!empty($this->headers_array) && !empty($this->blank_row_array)) {
                $this->db_table_data[0] = $this->headers_array;
                $this->db_table_data[1] = $this->blank_row_array;
                $this->db_table_data_filtered = $this->db_table_data;
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * Use the $_POST data received by catch_post_data() to put in db_table_data and db_table_data_filtered. THIS HAVE be used before filters.
     * @param int $row_to_put_on
     * @return boolean
     */
    public function put_post_data_on_table_data($row_to_put_on = 1) {
        if ((empty($this->db_table_data)) || empty($this->post_incoming_array)) {
            trigger_error(__FUNCTION__ . ": There are not data to work yet", E_USER_WARNING);
            return FALSE;
        }
        foreach ($this->db_table_data[$row_to_put_on] as $field => $value) {
            if (isset($this->post_incoming_array[$field])) {
                $this->db_table_data[$row_to_put_on][$field] = $this->post_incoming_array[$field];
            }
        }
        $this->db_table_data_filtered = $this->db_table_data;
        return TRUE;
    }

    function get_post_data() {
        return $this->post_incoming_array;
    }

    /**
     * Get and check the $_POST data, then remove the non table values. If do_table_field_name_encrypt is TRUE then will decrypt them too.
     * @return boolean
     */
    function catch_post_data() {
        $this->post_incoming_array = \k1lib\forms\check_all_incomming_vars($_POST);
        if (isset($this->post_incoming_array['k1magic'])) {
            self::set_k1magic_value($this->post_incoming_array['k1magic']);
            unset($this->post_incoming_array['k1magic']);
        }

        if (!empty($this->post_incoming_array)) {
            if ($this->do_table_field_name_encrypt) {
                $new_post_data = [];
                foreach ($this->post_incoming_array as $field => $value) {
                    $new_post_data[$this->decrypt_field_name($field)] = $value;
                }
                $this->post_incoming_array = $new_post_data;
                unset($new_post_data);
            }
            $this->post_incoming_array = \k1lib\common\clean_array_with_guide($this->post_incoming_array, $this->db_table->get_db_table_config());
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function get_do_table_field_name_encrypt() {
        return $this->do_table_field_name_encrypt;
    }

    function set_do_table_field_name_encrypt($do_table_field_name_encryp = TRUE) {
        $this->do_table_field_name_encrypt = $do_table_field_name_encryp;
    }

    /**
     * Put an input object of certain type depending of the MySQL Table Feld Type on each data row[n]
     * @param Int $row_to_apply
     */
    public function insert_inputs_on_data_row($row_to_apply = 1, $create_labels_on_headers = TRUE) {

        /**
         * VALUES
         */
        $field_index = 0;
        foreach ($this->db_table_data_filtered[$row_to_apply] as $field => $value) {
            switch ($this->db_table->get_db_table_field_value_config($field, 'type')) {
                case 'enum': {
                        $enum_data = $this->db_table->get_enum_options($field);
                        $input_tag = new \k1lib\html\select_tag($this->encrypt_field_name($field));
                        foreach ($enum_data as $value) {
                            // SELETED work around
                            if ($this->db_table_data[$row_to_apply][$field] == $value) {
                                $selected = TRUE;
                            } else {
                                $selected = FALSE;
                            }
                            $input_tag->append_option($value, $value, $selected);
                        }
                    } break;
                default: {
                        $input_tag = new \k1lib\html\input_tag("text", $this->encrypt_field_name($field), $value, "k1-input-insert");
                        $input_tag->set_attrib("placeholder", "write some");
                    } break;
            }
            /**
             * LABELS 
             */
            if ($create_labels_on_headers) {
                $label_tag = new \k1lib\html\label_tag($this->db_table_data_filtered[0][$field_index], $this->encrypt_field_name($field));
                if (isset($this->post_validation_errors[$field])) {
                    $label_tag->set_attrib("class", "error");
                }
                $this->db_table_data_filtered[0][$field_index] = $label_tag->generate_tag();
            }
            /**
             * ERROR TESTING
             */
            if (isset($this->post_validation_errors[$field])) {

                $span_error = new \k1lib\html\span_tag("error");
                $span_error->set_value($this->post_validation_errors[$field]);
                $input_tag->post_code($span_error->generate_tag());
                $input_tag->set_attrib("class", "error", TRUE);
            }
            /**
             * END ERROR TESTING
             */
            if ($this->db_table->get_db_table_field_value_config($field, 'required') === TRUE) {
                $input_tag->set_attrib("required", TRUE);
            }
            $input_tag->set_attrib("k1-data-type", $this->db_table->get_db_table_field_value_config($field, 'validation'));
            $input_tag->set_attrib("id", $this->encrypt_field_name($field));
            $this->apply_html_tag_on_field_filter($input_tag, $field);
            $field_index++;
            unset($input_tag);
        }
//        $this->apply_html_tag_on_field_filter($input_tag, crudlexs_base::USE_ALL_FIELDS, "name");
    }

    /**
     * This will check every data with the db_table_config.
     * @return boolean TRUE on no errors or FALSE is some field has any problem.
     */
    public function do_post_data_validation() {
        $this->post_validation_errors = $this->db_table->do_data_validation($this->post_incoming_array);
        if (!is_array($this->post_validation_errors)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function encrypt_field_name($field_name) {
        // first, we need to know in what position is the field on the table design.
        if (isset($_SESSION['CRUDLEXS-RND']) && !empty($_SESSION['CRUDLEXS-RND'])) {
            $rnd = $_SESSION['CRUDLEXS-RND'];
        } else {
            $rnd = rand(5000, 10000);
            $_SESSION['CRUDLEXS-RND'] = $rnd;
        }
        if (!$this->do_table_field_name_encrypt) {
            return $field_name;
        } else {
            $field_pos = 0;
            foreach ($this->db_table->get_db_table_config() as $field => $config) {
                if ($field == $field_name) {
                    if ($config['alias']) {
                        return $config['alias'];
                    }
                    break;
                }
                $field_pos++;
            }
//            $new_field_name = "k1_" . \k1lib\utils\decimal_to_n36($field_pos);
            $new_field_name = "k1_" . \k1lib\utils\decimal_to_n36($field_pos + $rnd);
            return $new_field_name;
        }
    }

    public function decrypt_field_name($encrypted_name) {
        if (strstr($encrypted_name, "k1_") !== FALSE) {
            list($prefix, $n36_number) = explode("_", $encrypted_name);
            if (isset($_SESSION['CRUDLEXS-RND']) && !empty($_SESSION['CRUDLEXS-RND'])) {
                $rnd = $_SESSION['CRUDLEXS-RND'];
            } else {
                trigger_error("There is not rand number on session data", E_USER_ERROR);
            }
            $field_position = \k1lib\utils\n36_to_decimal($n36_number) - $rnd;
            $fields_from_table_config = array_keys($this->db_table->get_db_table_config());
//            $field_position = \k1lib\utils\n36_to_decimal($n36_number);
            return $fields_from_table_config[$field_position];
        } else {
            foreach ($this->db_table->get_db_table_config() as $field => $config) {
                if ($config['alias'] == $encrypted_name) {
                    return $field;
                }
            }
            return $encrypted_name;
        }
    }

    public function do_code() {
        if (!empty($this->db_table_data_filtered)) {
            /**
             * Hidden input
             */
            $hidden_input = new \k1lib\html\input_tag("hidden", "k1magic", "123123");
            /**
             * DIV content
             */
            $div_content = new \k1lib\html\div_tag();
            $this->insert_inputs_on_data_row();
            /**
             * FORM time !!
             */
            $html_form = new \k1lib\html\form_tag();
            // FORM LAYOUT
            $row_column_number = 1;
            $key_index = 0;
            foreach ($this->db_table_data_filtered[1] as $field => $value) {
                // <div class="row">
                $row_column = "div_row" . $row_column_number;
                ${$row_column} = new \k1lib\html\div_tag("row");
                $actual_row_column = "div_row_column_" . $row_column_number;
                // <div class="large-12 columns">
                ${$actual_row_column} = new \k1lib\html\div_tag("large-12 columns");
                ${$actual_row_column}->set_value($this->db_table_data_filtered[0][$key_index], TRUE);
                ${$actual_row_column}->set_value($value, TRUE);
                // put on div_row
                ${$row_column}->append_child(${$actual_row_column});
                $div_content->append_child(${$row_column});

                $row_column++;
                $key_index++;
            }

//            $html = \k1lib\html\make_form_label_input_layout($this->db_table_data_filtered[1], $extra_css_clasess, );
//            $div_content->set_value($html);
            /**
             * BUTTONS
             */
            $div_buttons = new \k1lib\html\div_tag("row text-center");
            $submit_button = new \k1lib\html\input_tag("submit", "k1send", "Enviar");
            $submit_button->set_attrib("class", "small button success");
            $div_buttons->append_child($submit_button);
            /**
             * Prepare output
             */
            $html_form->append_child($hidden_input);
            $html_form->append_child($div_content);
            $html_form->append_child($div_buttons);
            return $html_form->generate_tag();
        } else {
            return FALSE;
        }
    }

    public function do_insert($url_to_go = null) {
        if ($this->db_table->insert_data($this->post_incoming_array)) {
            $new_key_text = \k1lib\sql\table_keys_to_text($this->post_incoming_array, $this->db_table->get_db_table_config());
            if (!empty($url_to_go)) {
                $url_to_go = sprintf($url_to_go, $new_key_text);
                $this->set_auth_code($new_key_text);
                \k1lib\html\html_header_go($url_to_go . "?auth-code={$this->get_auth_code()}");
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

class updating extends \k1lib\crudlexs\creating {

    public function do_update($url_to_go = null) {
        if ($this->db_table->update_data($this->post_incoming_array, $this->db_table_data_keys[1])) {
            /**
             * Merge the ROW KEYS with all the possible keys on the POST array
             */
            $merged_key_array = array_merge(
                    $this->db_table_data_keys[1], \k1lib\sql\get_keys_array_from_row_data($this->post_incoming_array
                            , $this->db_table->get_db_table_config())
            );
            $row_key_text = \k1lib\sql\table_keys_to_text($merged_key_array, $this->db_table->get_db_table_config());
            if (!empty($url_to_go)) {
                $url_to_go = sprintf($url_to_go, $row_key_text);
                $this->set_auth_code($row_key_text);
                \k1lib\html\html_header_go($url_to_go . "?auth-code={$this->get_auth_code()}");
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
