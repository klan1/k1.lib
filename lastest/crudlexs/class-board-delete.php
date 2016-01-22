<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\urlrewrite\url_manager as url_manager;

class board_delete extends board_base implements controller_interface {

    protected $redirect_url = null;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object) {
        $this->controller_object = $controller_object;
        $this->redirect_url = "../../{$this->controller_object->get_board_list_url_name()}/";
    }

    function set_redirect_url($redirect_url) {
        $this->redirect_url = $redirect_url;
    }

    public function start_board() {

        /**
         * GET the row KEY from the URL
         */
// This will work because the URL internal index is from 0
        $next_url_level = url_manager::get_url_level_count();
// get from the URL the next level value :   /$actual_url/next_level_value
        $row_key_text = url_manager::set_url_rewrite_var($next_url_level, "row_key_text", FALSE);
        $row_key_text_array = \k1lib\sql\table_url_text_to_keys($row_key_text, $this->controller_object->db_table->get_db_table_config());

// Init sending the DB Table object that ha
        $html_db_row_view = new \k1lib\crudlexs\reading($this->controller_object->db_table, $row_key_text);

        if (isset($_GET['auth-code'])) {
            $delete_auth = $_GET['auth-code'];
            $auth_expected = md5(\k1lib\MAGIC_VALUE . $row_key_text);
            if ($delete_auth === $auth_expected) {

                if ($this->controller_object->db_table->delete_data($row_key_text_array)) {
                    \k1lib\html\html_header_go($this->redirect_url);
                } else {
                    \k1lib\common\show_message("El registro a borrar no existe.", "Error: ", "alert");
                }
            } else {
                \k1lib\common\show_message("La autorizacion no es valida.", "Error: ", "alert");
            }
        } else {

            \k1lib\common\show_message("No puedo borrar un registro sin autorizacion.", "Error: ", "alert");
        }
    }

    public function exec_board() {
        return TRUE;
    }

}
