<?php

namespace k1lib\crudlexs;

use k1lib\urlrewrite\url as url;
use k1lib\session\session_plain as session_plain;

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
            $this->show_rule_to_apply = "show-update";
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), "row_keys_text", FALSE);
            $this->update_object = new \k1lib\crudlexs\updating($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    /**
     * @return \k1lib\html\div|boolean
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
                $this->controller_object->db_table->set_db_table_show_rule($this->show_rule_to_apply);

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
     * @return \k1lib\html\div|boolean
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
                            $this->sql_action_result = $this->update_object->do_update();
                        } else {
                            \k1lib\common\show_message(board_update_strings::$error_form, board_base_strings::$alert_board, "warning");
                        }
                    }
                }
                if ($this->apply_label_filter) {
                    $this->update_object->apply_label_filter();
                }
                $this->update_object->insert_inputs_on_data_row();

                /**
                 * DELETE BUTTON
                 */
                if ($this->controller_object->get_board_delete_enabled()) {
                    $delete_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_delete_url_name()}/{$this->row_keys_text}/";
                    if (\k1lib\urlrewrite\get_back_url(TRUE)) {
                        $get_vars = [
                            "auth-code" => md5(session_plain::get_user_hash() . $this->row_keys_text),
                            "back-url" => \k1lib\urlrewrite\get_back_url(TRUE),
                        ];
                    } else {
                        $get_vars = [
                            "auth-code" => md5(session_plain::get_user_hash() . $this->row_keys_text),
                        ];
                    }
                    $delete_link = \k1lib\html\get_link_button(url::do_url($delete_url, $get_vars), board_read_strings::$button_delete, "small");
                    $delete_link->append_to($this->board_content_div);
                }

                $update_content_div = $this->update_object->do_html_object();
                $update_content_div->append_to($this->board_content_div); 
                
                if ($do_echo) {
                    $this->board_content_div->generate(TRUE);
                    return TRUE;
                } else {
                    return $update_content_div;
                }
            } else {
                \k1lib\common\show_message(board_base_strings::$error_mysql_table_no_data, board_base_strings::$error_mysql, "alert");
                $this->update_object->make_invalid();
                $this->is_enabled = FALSE;
                return FALSE;
            }
        }
    }

    public function finish_board($do_redirect = TRUE, $custom_redirect = FALSE) {
        if ($this->sql_action_result !== NULL) {
            if ($custom_redirect === FALSE) {
                if (isset($_GET['back-url'])) {
                    $url_to_go = urldecode($_GET['back-url']);
                } else {
                    $get_params = [
                        "auth-code" => "--authcode--"
                    ];
                    $url_to_go = url::do_url("{$this->controller_object->get_controller_root_dir()}{$this->controller_object->get_board_read_url_name()}/--rowkeys--/?", $get_params);
                }
            } else {
                $url_to_go = url::do_url($custom_redirect);
            }
            $this->update_object->post_update_redirect($url_to_go, $do_redirect);
        }
    }

}
