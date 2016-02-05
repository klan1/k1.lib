<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\urlrewrite\url_manager as url_manager;

class board_delete extends board_base implements controller_interface {

    protected $redirect_url = null;
    private $row_keys_text;
    private $read_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object) {
        parent::__construct($controller_object);
        $this->redirect_url = (isset($_GET['back-url'])) ? \k1lib\urlrewrite\get_back_url() : "../../{$this->controller_object->get_board_list_url_name()}/";
        if ($this->is_enabled) {
            $this->row_keys_text = url_manager::set_url_rewrite_var(url_manager::get_url_level_count(), "row_keys_text", FALSE);
            $this->read_object = new \k1lib\crudlexs\reading($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    function set_redirect_url($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    public function start_board() {
        if (!$this->is_enabled) {
            $this->read_object->make_invalid();
            \k1lib\common\show_message(board_base_strings::$error_board_disabled, board_base_strings::$alert_board, "warning");
            return FALSE;
        }
        return TRUE;
    }

    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }
        if (!empty($this->row_keys_text)) {
            if ($this->read_object->load_db_table_data()) {
                $row_key_text_array = \k1lib\sql\table_url_text_to_keys($this->row_keys_text, $this->controller_object->db_table->get_db_table_config());

                if ($this->controller_object->db_table->delete_data($row_key_text_array)) {
                    \k1lib\html\html_header_go($this->redirect_url);
                    return TRUE;
                } else {
                    \k1lib\common\show_message(board_delete_strings::$error_no_data_deleted, "Error: ", "alert");
                    return FALSE;
                }
            } else {
                \k1lib\common\show_message(board_base_strings::$error_mysql_table_not_opened, board_base_strings::$error_mysql, "alert");
                return FALSE;
            }
        }
    }

}

class board_delete_strings {

    static $error_no_data_deleted = "The record to delete can't be deleted";

}
