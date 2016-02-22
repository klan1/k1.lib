<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;

class board_create extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\creating
     */
    public $create_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-create";
            $this->create_object = new \k1lib\crudlexs\creating($this->controller_object->db_table, FALSE);
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

        $this->create_object->enable_foundation_form_check();

        if ($this->create_object->get_state()) {
            $this->create_object->set_back_url(\k1lib\urlrewrite\get_back_url());

            $this->create_object->set_do_table_field_name_encrypt(TRUE);
            $this->controller_object->db_table->set_db_table_show_rule($this->show_rule_to_apply);
            $this->data_loaded = $this->create_object->load_db_table_data(TRUE);
            return $this->board_content_div;
        } else {
            \k1lib\common\show_message(board_base_labels::$error_mysql_table_not_opened, board_base_labels::$error_mysql, "alert");
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

        if ($this->data_loaded) {
            if ($this->create_object->catch_post_data(TRUE)) {
                $this->create_object->put_post_data_on_table_data();
                if (!$this->skip_form_action) {
                    if ($this->create_object->do_post_data_validation()) {
                        if (isset($_GET['back-url'])) {
                            $url_to_go = \k1lib\urlrewrite\get_back_url();
                        } else {
                            $back_url = (isset($_GET['back-url'])) ? "&back-url=" . urlencode(\k1lib\urlrewrite\get_back_url()) : "";
                            $url_to_go = "{$this->controller_object->get_controller_root_dir()}{$this->controller_object->get_board_read_url_name()}/%row_keys%/?auth-code=%auth_code%{$back_url}";
                        }
                        $this->sql_action_result = $this->create_object->do_insert($url_to_go, FALSE);
                    } else {
                        \k1lib\common\show_message(board_create_strings::$error_form, board_base_strings::$alert_board, "warning");
                    }
                }
            }
            if (empty($this->sql_action_result)) {

                $this->create_object->apply_label_filter();
                $this->create_object->insert_inputs_on_data_row();

                $this->create_object->set_use_create_custom_template();

                $this->create_object->do_html_object()->append_to($this->board_content_div);

                if ($do_echo) {
                    $this->board_content_div->generate_tag(TRUE);
                    return TRUE;
                } else {
                    return $this->board_content_div;
                }
            }
        } else {
            \k1lib\common\show_message(board_create_strings::$error_no_blank_data, board_base_strings::$alert_board, "alert");
            $this->create_object->make_invalid();
            $this->is_enabled = FALSE;
            return FALSE;
        }
    }

    public function finish_board() {
        if ($this->sql_action_result !== FALSE && $this->sql_action_result !== NULL) {
            \k1lib\html\html_header_go($this->sql_action_result);
        } elseif ($this->sql_action_result === FALSE) {
            \k1lib\common\show_message(board_create_strings::$error_no_inserted, board_base_strings::$error_mysql, "alert");
        }
    }

}
