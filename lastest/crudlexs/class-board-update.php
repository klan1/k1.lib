<?php

namespace k1lib\crudlexs;

use k1lib\urlrewrite\url_manager as url_manager;

class board_update extends board_base implements controller_interface {

    /**
     *
     * @var \k1lib\crudlexs\updating
     */
    public $update_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object) {
        $this->controller_object = $controller_object;
    }

    public function start_board() {

        $row_key_text = url_manager::set_url_rewrite_var(url_manager::get_url_level_count(), "row_key_text", FALSE);

        $this->board_content_div = new \k1lib\html\div_tag("board-content");

        $this->update_object = new \k1lib\crudlexs\updating($this->controller_object->db_table, $row_key_text);

        if ($this->update_object->get_state()) {

            $this->update_object->set_do_table_field_name_encrypt(TRUE);
            $this->controller_object->db_table->set_db_table_show_rule("show-edit");

            $this->data_loaded = $this->update_object->load_db_table_data();
        } else {
            \k1lib\common\show_message(board_base_strings::$error_mysql_table_not_opened, board_base_strings::$error_mysql, "alert");
        }
    }

    public function exec_board($do_echo = TRUE) {
        if ($this->update_object->get_state()) {
            if ($this->data_loaded) {
                if ($this->update_object->catch_post_data(TRUE)) {
                    $this->update_object->put_post_data_on_table_data();
                    if ($this->update_object->do_post_data_validation()) {
                        if (!$this->update_object->do_update("../../{$this->controller_object->get_board_read_url_name()}/%row_key%/")) {
                            \k1lib\common\show_message(board_update_strings::$error_no_inserted, board_base_strings::$error_mysql, "alert");
                        }
                    } else {
                        \k1lib\common\show_message(board_update_strings::$error_form, board_base_strings::$alert_board, "warning");
                    }
                }
                $this->update_object->apply_label_filter();
                $this->update_object->do_html_object()->append_to($this->board_content_div);
            } else {
                \k1lib\common\show_message(board_base_strings::$error_mysql_table_no_data, board_base_strings::$error_mysql, "alert");
            }


            if ($do_echo) {
                $this->board_content_div->generate_tag(TRUE);
            } else {
                return TRUE;
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
