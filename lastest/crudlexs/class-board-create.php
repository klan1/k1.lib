<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;

class board_create extends board_base implements controller_interface {

    /**
     *
     * @var \k1lib\crudlexs\creating
     */
    public $create_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object) {
        $this->controller_object = $controller_object;
    }

    public function start_board($do_echo = TRUE) {
        $this->board_content_div = new \k1lib\html\div_tag("board-content");

        $this->create_object = new \k1lib\crudlexs\creating($this->controller_object->db_table, FALSE);
        $this->create_object->enable_foundation_form_check();

        if ($this->create_object->get_state()) {

            $this->create_object->set_do_table_field_name_encrypt(TRUE);
            $this->controller_object->db_table->set_db_table_show_rule("show-new");
            $this->data_loaded = $this->create_object->load_db_table_data(TRUE);
        } else {
            \k1lib\common\show_message("La tabla no se pudo abrir.", "Alerta", "alert");
            return FALSE;
        }
    }

    public function exec_board($do_echo = TRUE) {

        if ($this->data_loaded) {
            if ($this->create_object->catch_post_data(TRUE)) {
                $this->create_object->put_post_data_on_table_data();
                if ($this->create_object->do_post_data_validation()) {
                    if ($this->create_object->do_insert("../view/%row_key%/")) {
                        \k1lib\common\show_message("Todo correcto.", "Info: ", "success");
//                    \k1lib\html\html_header_go(url_manager::make_url_from_rewrite(-1) . "/list/");
                    } else {
                        \k1lib\common\show_message("No se pudo insertar los datos.", "ALERTA:", "alert");
                    }
                } else {
                    \k1lib\common\show_message("Hay errores que debes corregir", "ALERTA:", "warning");
                }
            }
            $this->create_object->apply_label_filter();
            $this->create_object->do_html_object()->append_to($this->board_content_div);
        } else {
            \k1lib\common\show_message("No se ha podido obtener la informacion necesaria.", "Alerta", "alert");
        }
        if ($do_echo) {
            $this->board_content_div->generate_tag(TRUE);
        } else {
            return $this->board_content_div->generate_tag();
        }
    }

}
