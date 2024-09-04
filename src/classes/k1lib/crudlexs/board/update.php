<?php

namespace k1lib\crudlexs\board;

use k1lib\urlrewrite\url as url;
use k1lib\session\session_plain as session_plain;
use k1lib\html\DOM as DOM;
use k1lib\html\notifications\on_DOM as DOM_notification;

class update extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\updating
     */
    public $update_object;
    private $row_keys_text;

    public function __construct(\k1lib\crudlexs\controller\base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-update";
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), 'row-keys-text', FALSE);
            $this->update_object = new \k1lib\crudlexs\object\updating($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }
        /**
         * IFRAME for KF tool
         */
//        $fk_iframe = new \k1lib\html\iframe('./', 'utility-iframe', "fk-iframe");
//        DOM::html()->body()->content()->append_child_tail($fk_iframe);
        
        if (!empty($this->row_keys_text)) {

            if ($this->update_object->get_state()) {
                $this->update_object->set_back_url(\k1lib\urlrewrite\get_back_url());

                $this->update_object->set_do_table_field_name_encrypt(TRUE);
                $this->controller_object->db_table->set_db_table_show_rule($this->show_rule_to_apply);

                $this->data_loaded = $this->update_object->load_db_table_data();
                $this->update_object->catch_post_data();
                return $this->board_content_div;
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
                DOM_notification::queue_title(board_base_strings::$error_mysql);
                return FALSE;
            }
        } else {
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

        if ($this->update_object->get_state() && !empty($this->row_keys_text)) {

            if ($this->data_loaded) {
                if ($this->update_object->get_post_data_catched()) {
                    $this->update_object->put_post_data_on_table_data();
                    if (!$this->skip_form_action) {
                        if ($this->update_object->do_post_data_validation()) {
                            $this->sql_action_result = $this->update_object->do_update();
                        } else {
                            DOM_notification::queue_mesasage(board_update_strings::$error_form, "alert", $this->notifications_div_id);
                            DOM_notification::queue_title(board_base_strings::$alert_board);
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
                if ($this->controller_object->get_board_delete_enabled() && $this->controller_object->get_board_delete_allowed_for_current_user()) {
                    $delete_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_delete_url_name()}/" . urlencode($this->row_keys_text) . '/';
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
                    $back_link = \k1lib\html\get_link_button(url::do_url(\k1lib\urlrewrite\get_back_url()), board_read_strings::$button_back, "small");
                    $back_link->append_to($this->button_div_tag);
                    $delete_link = \k1lib\html\get_link_button(url::do_url($delete_url, $get_vars), board_read_strings::$button_delete, "small");
                    $delete_link->append_to($this->button_div_tag);
                }

                $update_content_div = $this->update_object->do_html_object();
                $update_content_div->append_to($this->board_content_div);

                return $this->board_content_div;
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_no_data, "alert", $this->notifications_div_id, board_base_strings::$error_mysql);
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
