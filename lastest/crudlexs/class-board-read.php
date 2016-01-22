<?php

namespace k1lib\crudlexs;

use k1lib\urlrewrite\url_manager as url_manager;

class board_read extends board_base implements controller_interface {

    /**
     *
     * @var \k1lib\crudlexs\reading
     */
    public $read_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object) {
        $this->controller_object = $controller_object;
    }

    public function start_board() {
        $row_key_text = url_manager::set_url_rewrite_var(url_manager::get_url_level_count(), "row_key_text", FALSE);

        $this->board_content_div = new \k1lib\html\div_tag("board-content");

        $this->read_object = new \k1lib\crudlexs\reading($this->controller_object->db_table, $row_key_text);

        if ($this->read_object->get_state()) {

            /**
             * BACK
             */
            $back_url = "../../{$this->controller_object->get_board_list_url_name()}/";
            $back_link = \k1lib\html\get_link_button($back_url, "Regresar");
            $back_link->append_to($this->board_content_div);
            /**
             * EDIT BUTTON
             */
            $edit_url = "../../{$this->controller_object->get_board_update_url_name()}/{$row_key_text}/?auth-code={$this->read_object->get_auth_code()}";
            $edit_link = \k1lib\html\get_link_button($edit_url, "Editar");
            $edit_link->append_to($this->board_content_div);
            /**
             * DELETE BUTTON
             */
            $delete_url = "../../{$this->controller_object->get_board_delete_url_name()}/{$row_key_text}/?auth-code={$this->read_object->get_auth_code()}";
            $delete_link = \k1lib\html\get_link_button($delete_url, "Borrar");
            $delete_link->append_to($this->board_content_div);

            $this->data_loaded = $this->read_object->load_db_table_data('show-view');
            return $this->board_content_div;
        } else {
            \k1lib\common\show_message("El registro o tabla no se pudo abrir.", "Alerta", "alert");
            return FALSE;
        }
    }

    public function exec_board($do_echo = TRUE) {

        if ($this->data_loaded) {
            $this->read_object->apply_label_filter();

            $span_tag = new \k1lib\html\span_tag("key-field");
            $this->read_object->apply_html_tag_on_field_filter($span_tag, \k1lib\crudlexs\crudlexs_base::USE_KEY_FIELDS);

            $this->read_object->do_html_object()->append_to($this->board_content_div);
        } else {
            \k1lib\common\show_message("El registro no existe.", "Alerta", "alert");
        }




        if ($do_echo) {
            $this->board_content_div->generate_tag(TRUE);
        } else {
            return TRUE;
        }
    }

}
