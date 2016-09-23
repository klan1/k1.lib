<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\urlrewrite\url as url;

class board_delete extends board_base implements board_interface {

    protected $redirect_url = null;
    private $row_keys_text;
    private $row_keys_text_array;
    private $read_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        $this->redirect_url = (isset($_GET['back-url'])) ? \k1lib\urlrewrite\get_back_url() : "{$controller_object->get_controller_root_dir()}{$this->controller_object->get_board_list_url_name()}/";
        if ($this->is_enabled) {
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), "row_keys_text", FALSE);
            $this->read_object = new \k1lib\crudlexs\reading($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    function set_redirect_url($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    /**
     * @return \k1lib\html\div|boolean
     */ public function start_board() {
        if (!$this->is_enabled) {
            \k1lib\common\show_message(board_base_strings::$error_board_disabled, board_base_strings::$alert_board, "warning");
            return FALSE;
        }
        return $this->board_content_div;
    }

    /**
     * @return boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }
        if (!empty($this->row_keys_text)) {
            if ($this->read_object->load_db_table_data()) {
                $this->row_keys_text_array = \k1lib\sql\table_url_text_to_keys($this->row_keys_text, $this->controller_object->db_table->get_db_table_config());
                if ($_GET['auth-code'] === $this->read_object->get_auth_code_personal()) {
                    $this->sql_action_result = $this->controller_object->db_table->delete_data($this->row_keys_text_array);
                    if ($this->sql_action_result) {
                        return TRUE;
                    } else {
                        \k1lib\common\show_message(board_delete_strings::$error_no_data_deleted, \k1lib\common_strings::$error, "alert");
                        return FALSE;
                    }
                } else if ($_GET['auth-code'] === $this->read_object->get_auth_code()) {
                    \k1lib\common\show_message(board_delete_strings::$error_no_data_deleted_hacker, \k1lib\common_strings::$error_hacker, "alert");
                    return FALSE;
                }
            } else {
                \k1lib\common\show_message(board_base_strings::$error_mysql_table_not_opened, board_base_strings::$error_mysql, "alert");
                $this->is_enabled = FALSE;
                return FALSE;
            }
        }
    }

    public function get_row_keys_text() {
        return $this->row_keys_text;
    }

    public function get_row_keys_text_array() {
        return $this->row_keys_text_array;
    }

    public function finish_board() {
        if ($this->sql_action_result) {
            \k1lib\html\html_header_go($this->redirect_url);
        }
    }

}
