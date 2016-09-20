<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;

/**
 * 
 */
class creating extends crudlexs_base_with_data implements crudlexs_base_interface {

    /**
     *
     * @var Array
     */
    protected $post_incoming_array = [];

    /**
     *
     * @var Array
     */
    protected $post_validation_errors = [];

    /**
     *
     * @var Boolean
     */
    protected $enable_foundation_form_check = FALSE;
    protected $show_cancel_button = TRUE;
    protected $inserted_result = NULL;
    protected $inserted = NULL;
    protected $html_form_column_classes = "large-5 medium-7 small-11";
    protected $html_column_classes = "small-12 column";

    public function __construct($db_table, $row_keys_text) {
        parent::__construct($db_table, $row_keys_text);

        $this->set_object_id(get_class($this));
        $this->set_css_class(get_class($this));
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
     * Use the $_POST data received by catch_post_data() to put in db_table_data and db_table_data_filtered. THIS HAVE be used before filters.
     * @param int $row_to_put_on
     * @return boolean
     */
    public function put_post_data_on_table_data($row_to_put_on = 1) {
        if ((empty($this->db_table_data)) || empty($this->post_incoming_array)) {
//            trigger_error(__FUNCTION__ . ": There are not data to work yet", E_USER_WARNING);
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

    /**
     * Get and check the $_POST data, then remove the non table values. If do_table_field_name_encrypt is TRUE then will decrypt them too.
     * @return boolean
     */
    function catch_post_data() {
        $this->do_file_uploads_validation();
        $this->post_incoming_array = array_merge($this->post_incoming_array, $_POST);
        if (isset($this->post_incoming_array['k1magic'])) {
            self::set_k1magic_value($this->post_incoming_array['k1magic']);
            unset($this->post_incoming_array['k1magic']);

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
        } else {
            return FALSE;
        }
    }

    /**
     * Put an input object of certain type depending of the MySQL Table Feld Type on each data row[n]
     * @param Int $row_to_apply
     */
    public function insert_inputs_on_data_row($row_to_apply = 1, $create_labels_on_headers = TRUE) {
        /**
         * VALUES
         */
        foreach ($this->db_table_data_filtered[$row_to_apply] as $field => $value) {
            /**
             * Switch on DB field specific TYPES
             */
            switch ($this->db_table->get_field_config($field, 'type')) {
                case 'enum':
                    $input_tag = input_helper::enum_type($this, $row_to_apply, $field);
                    break;
                case 'text':
                    switch ($this->db_table->get_field_config($field, 'validation')) {
                        case "html":
                            $input_tag = input_helper::text_type($this, $row_to_apply, $field);
                            break;
                        default:
                            $input_tag = input_helper::text_type($this, $row_to_apply, $field, FALSE);
                            break;
                    }
                    break;
                default:
                    /**
                     * Switch on K1lib DB Table Config VALIDATION TYPES
                     */
                    switch ($this->db_table->get_field_config($field, 'validation')) {
                        case "file-upload":
                            $input_tag = input_helper::file_upload($this, $field);
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
            if ($create_labels_on_headers) {
                $label_tag = new \k1lib\html\label($this->db_table_data_filtered[0][$field], $this->encrypt_field_name($field));
                if ($this->db_table->get_field_config($field, 'required') === TRUE) {
                    $label_tag->set_value(" *", TRUE);
                }
                if (isset($this->post_validation_errors[$field])) {
                    $label_tag->set_attrib("class", "is-invalid-label");
                }
                $this->db_table_data_filtered[0][$field] = $label_tag->generate();
            }
            /**
             * ERROR TESTING
             */
            if (isset($this->post_validation_errors[$field])) {

                $span_error = new \k1lib\html\span("form-error is-visible");
                $span_error->set_value($this->post_validation_errors[$field]);
                $input_tag->post_code($span_error->generate());
                $input_tag->set_attrib("class", "is-invalid-input", TRUE);
            }
            /**
             * END ERROR TESTING
             */
            if ($this->db_table->get_field_config($field, 'required') === TRUE) {
                if ($this->enable_foundation_form_check) {
                    $input_tag->set_attrib("required", TRUE);
                }
            }
            $input_tag->set_attrib("k1-data-type", $this->db_table->get_field_config($field, 'validation'));
            $input_tag->set_attrib("id", $this->encrypt_field_name($field));

            $this->apply_html_tag_on_field_filter($input_tag, $field);

            unset($input_tag);
        }
    }

    /**
     * This will check every data with the db_table_config.
     * @return boolean TRUE on no errors or FALSE is some field has any problem.
     */
    public function do_post_data_validation() {
//        $this->do_file_uploads_validation();
        $this->post_validation_errors = $this->db_table->do_data_validation($this->post_incoming_array);
        if (!is_array($this->post_validation_errors)) {
            return TRUE;
        } else {
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

    public function encrypt_field_name($field_name) {
        if ($this->do_table_field_name_encrypt) {
// first, we need to know in what position is the field on the table design.
            if (isset($_SESSION['CRUDLEXS-RND']) && !empty($_SESSION['CRUDLEXS-RND'])) {
                $rnd = $_SESSION['CRUDLEXS-RND'];
            } else {
                $rnd = rand(5000, 10000);
                $_SESSION['CRUDLEXS-RND'] = $rnd;
            }
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
        } else {
            return($field_name);
        }
    }

    public function decrypt_field_name($encrypted_name) {
        if (strstr($encrypted_name, "k1_") !== FALSE) {
            list($prefix, $n36_number) = explode("_", $encrypted_name);
            if (isset($_SESSION['CRUDLEXS-RND']) && !empty($_SESSION['CRUDLEXS-RND'])) {
                $rnd = $_SESSION['CRUDLEXS-RND'];
            } else {
                trigger_error(__METHOD__ . ' ' . object_base_strings::$error_no_session_random, E_USER_ERROR);
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

    public function enable_foundation_form_check() {
        $this->enable_foundation_form_check = TRUE;
    }

    /**
     * @return \k1lib\html\div
     */
    public function do_html_object() {
        if (!empty($this->db_table_data_filtered)) {
            $this->div_container->set_attrib("class", "k1-crudlexs-create");

            /**
             * DIV content
             */
            $this->div_container->set_attrib("class", "k1-form-generator " . $this->html_form_column_classes, TRUE);
            $this->div_container->set_attrib("style", "margin:0 auto;", TRUE);

            /**
             * FORM time !!
             */
            $html_form = new \k1lib\html\form();
            $html_form->append_to($this->div_container);
            if ($this->enable_foundation_form_check) {
                $html_form->set_attrib("data-abide", TRUE);
            }

            $form_header = $html_form->append_div("k1-form-header row");
            $form_body = $html_form->append_div("k1-form-body row");
            $form_footer = $html_form->append_div("k1-form-footer row");
            $form_footer->set_attrib("style", "margintop-5px;");
            $form_buttons = $html_form->append_div("k1-form-buttons row");

            /**
             * Hidden input
             */
            $hidden_input = new \k1lib\html\input("hidden", "k1magic", "123123");
            $hidden_input->append_to($html_form);
            // FORM LAYOUT
            $html = "";
            if ($this->use_create_custom_template) {
                /**
                 * LOAD the custom HTMLtemplate 
                 */
                $possible_read_template = "create-templates/" . $this->db_table->get_db_table_name();
                $template_file_path = temply::load_view($possible_read_template, APP_VIEWS_PATH);

                if ($template_file_path) {
                    ob_start();
                    include $template_file_path;
                    $html = ob_get_contents();
                    ob_end_clean();

                    foreach ($this->db_table_data_filtered[1] as $field => $value) {
                        if (temply::is_place_registered("{$field}-label")) {
                            temply::set_place_value("{$field}-label", $this->db_table_data_filtered[0][$field]);
                        }
                        if (temply::is_place_registered($field)) {
                            temply::set_place_value($field, $value);
                        }
                    }
                }
                $form_body->set_value($html);
            }

            if (empty($html)) {
// <div class="row">

                $row_column_number = 1;
//                d($this->db_table_data_filtered);
                foreach ($this->db_table_data_filtered[1] as $field => $value) {
// Variable variables names
                    $row_column = "div_row" . $row_column_number;

// <div class="large-12 columns">

                    $field_type = $this->db_table->get_field_config($field, 'type');
                    $field_validation = $this->db_table->get_field_config($field, 'validation');
                    if ($field_type == 'text' && $field_validation == 'html') {
                        $input_div = $form_footer->append_div("large-12 column end");
                        $last_non_text_div = FALSE;
                    } else {
                        $input_div = $form_body->append_div($this->html_column_classes);
                        $last_normal_div = $input_div;
                    }
                    $input_div->set_value($this->db_table_data_filtered[0][$field], TRUE);
                    $input_div->set_value($value, TRUE);
// put on div_row
                    $row_column++;
                }
                $last_normal_div->set_attrib("class", "end", TRUE);
            }

            /**
             * BUTTONS
             */
            $div_buttons = $form_buttons->append_div("row text-center k1-form-buttons");
            if ($this->show_cancel_button) {
                $cancel_button = \k1lib\html\get_link_button($this->back_url, creating_strings::$button_cancel, "small");
                $cancel_button->append_to($div_buttons);
            }
            $submit_button = new \k1lib\html\input("submit", "k1send", creating_strings::$button_submit, "small button fi-check success");
            $submit_button->append_to($div_buttons);

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
        $this->post_incoming_array = \k1lib\forms\check_all_incomming_vars($this->post_incoming_array);
        $this->inserted_result = $this->db_table->insert_data($this->post_incoming_array);
        if ($this->inserted_result !== FALSE) {
            $this->inserted = TRUE;
            return TRUE;
        } else {
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
                } else {
                    \k1lib\html\html_header_go("../");
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

}
