<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\urlrewrite\url as url;

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
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        parent::start_board();
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
     * @return \k1lib\html\div|boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->data_loaded) {
            if ($this->create_object->catch_post_data(TRUE)) {
                $this->create_object->put_post_data_on_table_data();
                if (!$this->skip_form_action) {
                    if ($this->create_object->do_post_data_validation()) {
                        $this->sql_action_result = $this->create_object->do_insert();
                    } else {
                        \k1lib\common\show_message(board_create_strings::$error_form, board_base_strings::$alert_board, "warning");
                    }
                }
            }
            if (empty($this->sql_action_result)) {
                if ($this->apply_label_filter) {
                    $this->create_object->apply_label_filter();
                }
                $this->create_object->insert_inputs_on_data_row();

                $create_content_div = $this->create_object->do_html_object();
                $create_content_div->append_to($this->board_content_div);

                return $this->board_content_div;
            }
        } else {
            \k1lib\common\show_message(board_create_strings::$error_no_blank_data, board_base_strings::$alert_board, "alert");
            $this->create_object->make_invalid();
            $this->is_enabled = FALSE;
            return FALSE;
        }
    }

    public function finish_board($do_redirect = TRUE, $custom_redirect = FALSE) {
        if ($this->sql_action_result !== NULL) {
            if ($custom_redirect === FALSE) {

                if (isset($_GET['back-url'])) {
                    $get_params = [];
                    $url_to_go = \k1lib\urlrewrite\get_back_url();
                } else {
                    $get_params = [
//                        "back-url" => \k1lib\urlrewrite\get_back_url(),
                        "auth-code" => "--authcode--"
                    ];
                    $url_to_go = "{$this->controller_object->get_controller_root_dir()}{$this->controller_object->get_board_read_url_name()}/--rowkeys--/";
                }
                $url_to_go = url::do_url($url_to_go, $get_params);
            } else {
                $url_to_go = url::do_url($custom_redirect);
            }
            $this->create_object->post_insert_redirect($url_to_go, $do_redirect);
        }
    }

}
