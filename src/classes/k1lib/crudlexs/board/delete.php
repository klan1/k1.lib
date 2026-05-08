<?php

namespace k1lib\crudlexs\board;

use k1lib\common_strings;
use k1lib\crudlexs\controller\base;
use k1lib\crudlexs\object\reading;
use k1lib\html\div;
use k1lib\html\notifications\on_DOM as DOM_notification;
use k1lib\urlrewrite\url as url;
use function k1lib\html\html_header_go;
use function k1lib\urlrewrite\get_back_url;

class delete extends board_base implements board_interface {

    protected $redirect_url = null;
    private $row_keys_text;
    private $row_keys_text_array;
    private $read_object;

    public function __construct(base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        $this->redirect_url = (isset($_GET['back-url'])) ? get_back_url() : "{$controller_object->get_controller_root_dir()}{$this->controller_object->get_board_list_url_name()}/";
        if ($this->is_enabled) {
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), 'row-keys-text', FALSE);
            $this->read_object = new reading($this->controller_object->db_table, $this->row_keys_text);
            $this->set_current_object($this->read_object);
            
        }
    }

    function set_redirect_url($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    /**
     * @return div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
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
                $this->row_keys_text_array = $this->controller_object->db_table->db->table_url_text_to_keys($this->row_keys_text, $this->controller_object->db_table->get_db_table_config());
                if ($_GET['auth-code'] === $this->read_object->get_auth_code_personal()) {
                    $this->sql_action_result = $this->controller_object->db_table->delete_data($this->row_keys_text_array);
                    if ($this->sql_action_result) {
                        DOM_notification::queue_mesasage(board_delete_strings::$data_deleted, "success", $this->notifications_div_id);
                        return TRUE;
                    } else {
                        DOM_notification::queue_mesasage(board_delete_strings::$error_no_data_deleted, "alert", $this->notifications_div_id, common_strings::$error);
                        return FALSE;
                    }
                } else if ($_GET['auth-code'] === $this->read_object->get_auth_code()) {
                    DOM_notification::queue_mesasage(board_delete_strings::$error_no_data_deleted_hacker, "alert", $this->notifications_div_id, common_strings::$error_hacker);
                    return FALSE;
                }
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_not_opened, "alert", $this->notifications_div_id, board_base_strings::$error_mysql);
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
            html_header_go($this->redirect_url);
        }
    }
}
