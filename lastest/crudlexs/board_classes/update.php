<?php

namespace k1lib\crudlexs;

use k1lib\urlrewrite\url_manager as url_manager;

class board_update extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\updating
     */
    public $update_object;
    private $row_keys_text;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->row_keys_text = url_manager::set_url_rewrite_var(url_manager::get_url_level_count(), "row_keys_text", FALSE);
            $this->update_object = new \k1lib\crudlexs\updating($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    /**
     * @return \k1lib\html\div_tag|boolean
     */
    public function start_board() {
        if (!$this->is_enabled) {
            \k1lib\common\show_message(board_base_strings::$error_board_disabled, board_base_strings::$alert_board, "warning");
            return FALSE;
        }
        if (!empty($this->row_keys_text)) {

            if ($this->update_object->get_state()) {
                $this->update_object->set_back_url(\k1lib\urlrewrite\get_back_url());

                $this->update_object->set_do_table_field_name_encrypt(TRUE);
                $this->controller_object->db_table->set_db_table_show_rule("show-update");

                $this->data_loaded = $this->update_object->load_db_table_data();
                return $this->board_content_div;
            } else {
                \k1lib\common\show_message(board_base_strings::$error_mysql_table_not_opened, board_base_strings::$error_mysql, "alert");
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div_tag|boolean
     */
    public function exec_board($do_echo = TRUE) {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->update_object->get_state() && !empty($this->row_keys_text)) {

            if ($this->data_loaded) {
                if ($this->update_object->catch_post_data(TRUE)) {
                    $this->update_object->put_post_data_on_table_data();
                    if (!$this->skip_form_action) {
                        if ($this->update_object->do_post_data_validation()) {
                            $back_url = (isset($_GET['back-url'])) ? "&back-url=" . urlencode(\k1lib\urlrewrite\get_back_url()) : "";
                            $url_to_go = "{$this->controller_object->get_controller_root_dir()}{$this->controller_object->get_board_read_url_name()}/%row_keys%/?auth-code=%auth_code%{$back_url}";
                            if (!$this->update_object->do_update($url_to_go)) {
                                \k1lib\html\html_header_go($url_to_go);
                            }
                        } else {
                            \k1lib\common\show_message(board_update_strings::$error_form, board_base_strings::$alert_board, "warning");
                        }
                    }
                }
                $this->update_object->apply_label_filter();
                $this->update_object->insert_inputs_on_data_row();
                $this->update_object->set_use_create_custom_template();

                $this->update_object->do_html_object()->append_to($this->board_content_div);
                if ($do_echo) {
                    $this->board_content_div->generate_tag(TRUE);
                    return TRUE;
                } else {
                    return $this->board_content_div;
                }
            } else {
                \k1lib\common\show_message(board_base_strings::$error_mysql_table_no_data, board_base_strings::$error_mysql, "alert");
                $this->update_object->make_invalid();
                $this->is_enabled = FALSE;
                return FALSE;
            }
        }
    }

}

class board_update_strings {

    static $button_submit = "Update";
    static $error_no_inserted = "Data hasn't benn updated. Did you leave all unchaged ?";
    static $error_form = "Please correct the marked errors.";
    static $error_no_blank_data = "Please correct the marked errors.";

}
