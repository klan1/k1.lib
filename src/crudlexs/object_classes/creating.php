<?php

namespace k1lib\crudlexs;

use k1lib\notifications\on_DOM as DOM_notification;

/**
 * 
 */
class creating extends crudlexs_base_with_data implements crudlexs_base_interface {

    /**
     * @var Array
     */
    protected $post_incoming_array = [];

    /**
     * @var boolean
     */
    protected $post_data_catched = FALSE;

    /**
     * @var Array
     */
    protected $post_validation_errors = [];

    /**
     * @var array
     */
    protected $post_password_fields = [];
    protected $object_state = "create";

    /**
     *
     * @var Boolean
     */
    protected $enable_foundation_form_check = FALSE;
    protected $show_cancel_button = TRUE;
    protected $inserted_result = NULL;
    protected $inserted = NULL;
    protected $html_form_column_classes = "large-8 medium-10 small-11";
    protected $html_column_classes = "small-12 column";

    public function __construct($db_table, $row_keys_text) {
        parent::__construct($db_table, $row_keys_text);
    }

    /**
     * Override the original function to create an empty array the meets the requiriements for all the metods
     * @return boolean
     */
    public function load_db_table_data($blank_data = FALSE) {
        if (!$blank_data) {
            return parent::load_db_table_data();
        } else {
            $headers_array = [];
            $blank_row_array = [];
            $show_rule = $this->db_table->get_db_table_show_rule();
            foreach ($this->db_table->get_db_table_config() as $field => $config) {
                if (!empty($this->db_table->get_constant_fields()) && array_key_exists($field, $this->db_table->get_constant_fields())) {
                    continue;
                }
                if (($show_rule === NULL) || ($config[$show_rule])) {
                    $headers_array[$field] = $field;
                    $blank_row_array[$field] = "";
                }
            }
            if (!empty($headers_array) && !empty($blank_row_array)) {
                $this->db_table_data[0] = $headers_array;
                $this->db_table_data[1] = $blank_row_array;
                $this->db_table_data_filtered = $this->db_table_data;
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    /**
     * 
     * @param string $field
     * @param string $value
     * @return boolean
     */
    public function set_post_incomming_value($field, $value) {
        if ($this->post_data_catched && key_exists($field, $this->post_incoming_array)) {
            $this->post_incoming_array[$field] = $value;
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Use the $_POST data received by catch_post_data() to put in db_table_data and db_table_data_filtered. THIS HAVE be used before filters.
     * @param int $row_to_put_on
     * @return boolean
     */
    public function put_post_data_on_table_data() {
        if ((empty($this->db_table_data)) || empty($this->post_incoming_array)) {
//            trigger_error(__FUNCTION__ . ": There are not data to work yet", E_USER_WARNING);
            return FALSE;
        }
        foreach ($this->db_table_data[1] as $field => $value) {
            if (isset($this->post_incoming_array[$field])) {
                $this->db_table_data[1][$field] = $this->post_incoming_array[$field];
            }
        }
        $this->db_table_data_filtered = $this->db_table_data;
        return TRUE;
    }

    function do_password_fields_validation() {
        /**
         * PASSWORD CATCH
         */
        $password_fields = [];
        $current = null;
        $new = null;
        $confirm = null;
        // EXTRACT THE PASSWORD DATA
        foreach ($_POST as $field => $value) {
            $actual_password_field = strstr($field, "_password_", TRUE);
            if ($actual_password_field !== FALSE) {
                if (strstr($field, "_password_current") !== FALSE) {
                    $password_fields[$actual_password_field]['current'] = (empty($value)) ? NULL : md5($value);
                }
                if (strstr($field, "_password_new") !== FALSE) {
                    $password_fields[$actual_password_field]['new'] = (empty($value)) ? NULL : md5($value);
                }
                if (strstr($field, "_password_confirm") !== FALSE) {
                    $password_fields[$actual_password_field]['confirm'] = (empty($value)) ? NULL : md5($value);
                }
                unset($_POST[$field]);
                if ($this->do_table_field_name_encrypt) {
                    $this->post_password_fields[] = $this->decrypt_field_name($field);
                } else {
                    $this->post_password_fields[] = $field;
                }
            }
        }
        //  verify
        foreach ($password_fields as $field => $passwords) {
            if (array_key_exists('new', $passwords) && array_key_exists('confirm', $passwords)) {
                if (($passwords['new'] === $passwords['confirm']) && (!empty($passwords['new']))) {
                    $new_password = TRUE;
                } else {
                    $new_password = FALSE;
                }
            }
            if (array_key_exists('current', $passwords) && array_key_exists('new', $passwords) && array_key_exists('confirm', $passwords)) {
                if (empty($passwords['current'])) {
                    $this->post_incoming_array[$field] = $this->db_table_data[1][$this->decrypt_field_name($field)];
                } else {
                    if (($passwords['current'] === $this->db_table_data[1][$this->decrypt_field_name($field)])) {
                        if ($new_password) {
                            $this->post_incoming_array[$field] = $passwords['new'];
                            DOM_notification::queue_mesasage(updating_strings::$password_set_successfully, "success", $this->notifications_div_id);
                        } else {
                            $this->post_validation_errors[$this->decrypt_field_name($field)] = creating_strings::$error_new_password_not_match;
                        }
                    } else {
                        $this->post_validation_errors[$this->decrypt_field_name($field)] = creating_strings::$error_actual_password_not_match;
                    }
                }
            } else if (array_key_exists('new', $passwords) && array_key_exists('confirm', $passwords)) {
                if ($new_password) {
                    $this->post_incoming_array[$field] = $passwords['new'];
                } else {
                    $this->post_incoming_array[$field] = null;
                    if (empty($passwords['new'])) {
                        $this->post_validation_errors[$this->decrypt_field_name($field)] = creating_strings::$error_new_password_not_match;
                    }
                }
            }
        }
    }

    public function get_post_data_catched() {
        return $this->post_data_catched;
    }

    /**
     * Get and check the $_POST data, then remove the non table values. If do_table_field_name_encrypt is TRUE then will decrypt them too.
     * @return boolean
     */
    function catch_post_data() {
        $this->do_file_uploads_validation();
        $this->do_password_fields_validation();
        /**
         * Search util hack
         */
        $post_data_to_use = \k1lib\common\unserialize_var("post-data-to-use");
        $post_data_table_config = \k1lib\common\unserialize_var("post-data-table-config");
        /**
         * lets fix the non-same key name
         */
        $fk_found_array = [];
        $found_fk_key = false;
        if (!empty($post_data_table_config)) {
            foreach ($post_data_table_config as $field => $field_config) {
                if (!empty($field_config['refereced_column_config'])) {
                    $fk_field_name = $field_config['refereced_column_config']['field'];
                    foreach ($post_data_to_use as $field_current => $value) {
                        if (($field_current == $fk_field_name) && ($field != $field_current)) {
                            $fk_found_array[$field] = $value;
                            $found_fk_key = true;
                        }
                    }
                }
            }
        }
        ///
        if (!empty($post_data_to_use)) {
            $_POST = $post_data_to_use;
            \k1lib\common\unset_serialize_var("post-data-to-use");
            \k1lib\common\unset_serialize_var("post-data-table-config");
        }


        $_POST = \k1lib\forms\check_all_incomming_vars($_POST);

        $this->post_incoming_array = array_merge($this->post_incoming_array, $_POST);
        if (isset($this->post_incoming_array['k1magic'])) {
            self::set_k1magic_value($this->post_incoming_array['k1magic']);
            unset($this->post_incoming_array['k1magic']);

            if (!empty($this->post_incoming_array)) {
                if ($this->do_table_field_name_encrypt) {
                    $new_post_data = [];
                    foreach ($this->post_incoming_array as $field => $value) {
                        $decrypt_field_name = $this->decrypt_field_name($field);
                        if (array_key_exists($decrypt_field_name, $fk_found_array)) {
                            $value = $fk_found_array[$decrypt_field_name];
                        }
                        $new_post_data[$decrypt_field_name] = $value;
                    }
                    $this->post_incoming_array = $new_post_data;
                    unset($new_post_data);
                }
                $this->post_incoming_array = \k1lib\common\clean_array_with_guide($this->post_incoming_array, $this->db_table->get_db_table_config());

                // PUT BACK the password data
//                $this->post_incoming_array = array_merge($this->post_incoming_array, $password_array);
                $this->post_data_catched = TRUE;
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * Put an input object of certain type depending of the MySQL Table Feld Type on each data row[n]
     * @param Int $row_to_apply
     */
    public function insert_inputs_on_data_row($create_labels_tags_on_headers = TRUE) {
        // Row to apply is constant coz this is CREATE or EDIT and there is allways just 1 set of data to manipulate.
        $row_to_apply = 1;
        /**
         * VALUES
         */
        foreach ($this->db_table_data_filtered[$row_to_apply] as $field => $value) {
            /**
             * Switch on DB field specific TYPES
             */
            switch ($this->db_table->get_field_config($field, 'type')) {
                case 'enum':
                    $input_tag = input_helper::enum_type($this, $field);
                    break;
                case 'text':
                    switch ($this->db_table->get_field_config($field, 'validation')) {
                        case "html":
                            $input_tag = input_helper::text_type($this, $field);
                            break;
                        default:
                            $input_tag = input_helper::text_type($this, $field, FALSE);
                            break;
                    }
                    break;
                default:
                    /**
                     * Switch on K1lib DB Table Config VALIDATION TYPES
                     */
                    switch ($this->db_table->get_field_config($field, 'validation')) {
                        case "boolean":
                            $input_tag = input_helper::boolean_type($this, $field);
                            break;
                        case "file-upload":
                            $input_tag = input_helper::file_upload($this, $field);
                            break;
                        case "password":
                            if (empty($value)) {
                                $input_tag = input_helper::password_type($this, $field, $this->object_state);
                            } else {
                                $input_tag = input_helper::password_type($this, $field, $this->object_state);
                            }
                            break;
                        default:
                            $input_tag = input_helper::default_type($this, $field);
                            break;
                    }
                    break;
            }
            /**
             * LABELS 
             */
            if ($create_labels_tags_on_headers) {
                $label_tag = new \k1lib\html\label($this->db_table_data_filtered[0][$field], $this->encrypt_field_name($field));
                if ($this->db_table->get_field_config($field, 'required') === TRUE) {
                    $label_tag->set_value(" *", TRUE);
                }
                if (isset($this->post_validation_errors[$field])) {
                    $label_tag->set_attrib("class", "is-invalid-label");
                }
                $this->db_table_data_filtered[0][$field] = $label_tag;
            }
            /**
             * ERROR TESTING
             */
            if (isset($this->post_validation_errors[$field])) {
                $div_error = new \k1lib\html\foundation\grid_row(2);

                $div_input = $div_error->cell(1)->large(12);
                $div_message = $div_error->cell(2)->large(12)->end();

                $span_error = $div_message->append_span("clearfix form-error is-visible");
                $span_error->set_value($this->post_validation_errors[$field]);

                $input_tag->append_to($div_input);
                $input_tag->set_attrib("class", "is-invalid-input", TRUE);

                $div_error->link_value_obj($input_tag);
            }
            /**
             * END ERROR TESTING
             */
            if ($this->db_table->get_field_config($field, 'required') === TRUE) {
                if ($this->enable_foundation_form_check) {
                    $input_tag->set_attrib("required", TRUE);
                }
            }
            $input_tag->set_attrib("k1lib-data-type", $this->db_table->get_field_config($field, 'validation'));
            $input_tag->set_attrib("id", $this->encrypt_field_name($field));

            if (isset($div_error)) {
                $this->apply_html_tag_on_field_filter($div_error, $field);
                unset($div_error);
            } else {
                $this->apply_html_tag_on_field_filter($input_tag, $field);
            }

            unset($input_tag);
        }
    }

    /**
     * This will check every data with the db_table_config.
     * @return boolean TRUE on no errors or FALSE is some field has any problem.
     */
    public function do_post_data_validation() {
//        $this->do_file_uploads_validation();
        $validation_result = $this->db_table->do_data_validation($this->post_incoming_array);
        if ($validation_result !== TRUE) {
            $this->post_validation_errors = array_merge($this->post_validation_errors, $validation_result);
        }
        if (empty($this->post_validation_errors)) {
            return TRUE;
        } else {
            if ($this->object_state == "create") {
                foreach ($this->post_password_fields as $field) {
                    $this->db_table_data[1][$field] = null;
                    $this->db_table_data_filtered[1][$field] = null;
                }
            }
            return FALSE;
        }
    }

    public function do_file_uploads_validation() {
        if (!empty($_FILES)) {
            foreach ($_FILES as $encoded_field => $data) {
                $decoded_field = $this->decrypt_field_name($encoded_field);
                if ($data['error'] === UPLOAD_ERR_OK) {
                    $_POST[$decoded_field] = $data;
                } else {
                    if ($data['error'] !== UPLOAD_ERR_NO_FILE) {
                        trigger_error(creating_strings::$error_file_upload . print_r($data, TRUE), E_USER_WARNING);
                    }
                }
            }
        }
    }

    public function enable_foundation_form_check() {
        $this->enable_foundation_form_check = TRUE;
    }

    /**
     * @return \k1lib\html\div
     */
    public function do_html_object() {
        if (!empty($this->db_table_data_filtered)) {
            $this->div_container->set_attrib("class", "k1lib-crudlexs-create");

            /**
             * DIV content
             */
            $this->div_container->set_attrib("class", "k1lib-form-generator " . $this->html_form_column_classes, TRUE);
            $this->div_container->set_attrib("style", "margin:0 auto;", TRUE);

            /**
             * FORM time !!
             */
            $html_form = new \k1lib\html\form();
            $html_form->append_to($this->div_container);
            if ($this->enable_foundation_form_check) {
                $html_form->set_attrib("data-abide", TRUE);
            }

            $form_header = $html_form->append_div("k1lib-form-header");
            $form_body = $html_form->append_div("k1lib-form-body");
            $form_grid = new \k1lib\html\foundation\grid(1, 1, $form_body);
            $form_grid->row(1)->align_center();
            $form_grid->row(1)->cell(1)->large(8)->medium(10)->small(12);
            
            $form_footer = $html_form->append_div("k1lib-form-footer");
            $form_footer->set_attrib("style", "margin-top:0.9em;");
            $form_buttons = $html_form->append_div("k1lib-form-buttons");

            /**
             * Hidden input
             */
            $hidden_input = new \k1lib\html\input("hidden", "k1magic", "123123");
            $hidden_input->append_to($html_form);
            // FORM LAYOUT
            // <div class="row">

            $row_number = 0;
            foreach ($this->db_table_data_filtered[1] as $field => $value) {
                $row_number++;
                $row = new \k1lib\html\foundation\label_value_row($this->db_table_data_filtered[0][$field], $value, $row_number);
                $row->append_to($form_grid->row(1)->cell(1));
            }


            /**
             * BUTTONS
             */
            $submit_button = new \k1lib\html\input("submit", "k1send", creating_strings::$button_submit, "small button fi-check success");
            if ($this->show_cancel_button) {
                $cancel_button = \k1lib\html\get_link_button($this->back_url, creating_strings::$button_cancel, "small");
                $buttons_div = new \k1lib\html\foundation\label_value_row(NULL, "{$cancel_button} {$submit_button}");
            } else {
                $buttons_div = new \k1lib\html\foundation\label_value_row(NULL, "{$submit_button}");
            }

            $buttons_div->append_to($form_buttons);
            $buttons_div->cell(1)->remove_childs();
            $buttons_div->cell(2)->set_class("text-center", TRUE);


            /**
             * Prepare output
             */
            return $this->div_container;
        } else {
            return FALSE;
        }
    }

    /**
     * This uses the post_incoming_array (Please verify it first) to make the insert.
     * NOTE: If the table has multiple KEYS the auto_number HAS to be on the first position, if not, the redirection won't works.
     * @param type $url_to_go
     * @return boolean TRUE on sucess or FALSE on error.
     */
    public function do_insert() {
        $error_data = NULL;
        $this->post_incoming_array = \k1lib\forms\check_all_incomming_vars($this->post_incoming_array);
        $this->inserted_result = $this->db_table->insert_data($this->post_incoming_array, $error_data);
        if ($this->inserted_result !== FALSE) {
            DOM_notification::queue_mesasage(creating_strings::$data_inserted, "success", $this->notifications_div_id);
            $this->inserted = TRUE;
            return TRUE;
        } else {
            if (is_array($error_data) && !empty($error_data)) {
                $this->post_validation_errors = array_merge($this->post_validation_errors, $error_data);
            }
            DOM_notification::queue_mesasage(creating_strings::$data_not_inserted, "warning", $this->notifications_div_id);
            DOM_notification::queue_mesasage(print_r($error_data, TRUE), 'alert', $this->notifications_div_id);
            $this->inserted = FALSE;
            return FALSE;
        }
    }

    public function get_inserted_keys() {
        if (($this->inserted) && ($this->inserted_result !== FALSE)) {
            $last_inserted_id = [];
            if (is_numeric($this->inserted_result)) {
                foreach ($this->db_table->get_db_table_config() as $field => $config) {
                    if ($config['extra'] == 'auto_increment') {
                        $last_inserted_id[$field] = $this->inserted_result;
                    }
                }
            }
            $new_keys_array = \k1lib\sql\get_keys_array_from_row_data(
                    array_merge($last_inserted_id, $this->post_incoming_array, $this->db_table->get_constant_fields())
                    , $this->db_table->get_db_table_config()
            );
            return $new_keys_array;
        } else {
            return FALSE;
        }
    }

    public function get_inserted_data() {
        if (($this->inserted) && ($this->inserted_result !== FALSE)) {
            $last_inserted_id = [];
            if (is_numeric($this->inserted_result)) {
                foreach ($this->db_table->get_db_table_config() as $field => $config) {
                    if ($config['extra'] == 'auto_increment') {
                        $last_inserted_id[$field] = $this->inserted_result;
                    }
                }
            }
            return array_merge($last_inserted_id, $this->post_incoming_array, $this->db_table->get_constant_fields());
        } else {
            return FALSE;
        }
    }

    public function post_insert_redirect($url_to_go = "../", $do_redirect = TRUE) {
        if (($this->inserted) && ($this->inserted_result !== FALSE)) {

            $new_keys_text = \k1lib\sql\table_keys_to_text($this->get_inserted_keys(), $this->db_table->get_db_table_config());

            if (!empty($url_to_go)) {
                $this->set_auth_code($new_keys_text);
                $this->set_auth_code_personal($new_keys_text);
                $url_to_go = str_replace("--rowkeys--", $new_keys_text, $url_to_go);
                $url_to_go = str_replace("--authcode--", $this->get_auth_code(), $url_to_go);
            }
            if ($do_redirect) {
                if ($new_keys_text) {
                    \k1lib\html\html_header_go($url_to_go);
                    exit;
                } else {
                    \k1lib\html\html_header_go("../");
                    exit;
                }
                return TRUE;
            } else {
                return $url_to_go;
            }
        } else {
            return "";
        }
    }

    function get_post_data() {
        return $this->post_incoming_array;
    }

    public function set_post_data(Array $post_incoming_array) {
        $this->post_incoming_array = array_merge($this->post_incoming_array, $post_incoming_array);
    }

    public function set_html_column_classes($html_column_classes) {
        $this->html_column_classes = $html_column_classes;
    }

    public function set_html_form_column_classes($html_form_column_classes) {
        $this->html_form_column_classes = $html_form_column_classes;
    }

    public function &get_post_incoming_array() {
        return $this->post_incoming_array;
    }

    public function get_post_validation_errors() {
        return $this->post_validation_errors;
    }

    public function set_post_validation_errors(Array $errors_array, $append_array = TRUE) {
        if ($append_array) {
            $this->post_validation_errors = array_merge($this->post_validation_errors, $errors_array);
        } else {
            $this->post_validation_errors = $errors_array;
        }
    }

    public function set_show_cancel_button($show_cancel_button) {
        $this->show_cancel_button = $show_cancel_button;
    }

}
