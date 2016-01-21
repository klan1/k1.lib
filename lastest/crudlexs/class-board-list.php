<?php

namespace k1lib\crudlexs;

class board_list extends board_base implements board_interface {

    const SHOW_STATS_BEFORE_TABLE = 1;
    const SHOW_STATS_AFTER_TABLE = 2;
    const SHOW_STATS_BEFORE_AND_AFTER_TABLE = 3;

    protected $where_to_show_stats = self::SHOW_STATS_AFTER_TABLE;

    /**
     *
     * @var \k1lib\crudlexs\listing
     */
    public $list_object;

    /**
     * 
     * @param boolean $do_echo
     * @return \k1lib\crudlexs\listing
     */
    public function start_board() {

        $this->board_content_div = new \k1lib\html\div_tag("board-content");

        $this->list_object = new \k1lib\crudlexs\listing($this->controller_object->db_table, FALSE);

        if ($this->list_object->get_state()) {
            $search_helper = new \k1lib\crudlexs\search_helper($this->controller_object->db_table);


            /**
             * NEW BUTTON
             */
            $new_link = \k1lib\html\get_link_button("../{$this->controller_object->get_board_create_url_name()}/", "Nuevo");
            $new_link->append_to($this->board_content_div);

            /**
             * Search buttom
             */
            $search_buttom = new \k1lib\html\a_tag("#", "Buscar", "_self", "Buscar un registro en la tabla");
            $search_buttom->set_attrib("class", "button");
            $search_buttom->set_attrib("data-open", "search-modal");
            $search_buttom->append_to($this->board_content_div);

            /**
             * Clear search
             */
            if (!empty($search_helper->get_post_data())) {
                $clear_search_buttom = new \k1lib\html\a_tag($_SERVER['REQUEST_URI'], "Cancelar busqueda", "_self", "Limpiar la busqueda");
                $search_buttom->set_value("Editar busqueda");
                $clear_search_buttom->set_attrib("class", "button warning");
                $clear_search_buttom->append_to($this->board_content_div);
            }
        } else {
            \k1lib\common\show_message("La tabla no se pudo abrir.", "Alerta", "alert");
            return FALSE;
        }
        $search_helper->do_html_object()->append_to($this->board_content_div);

        $this->data_loaded = $this->list_object->load_db_table_data('show-table');
        return $this->board_content_div;
    }

    public function exec_board($do_echo = FALSE) {
        /**
         * HTML DB TABLE
         */
        if ($this->data_loaded) {
            $this->list_object->apply_label_filter();
            // IF NOT previous link applied this will try to apply ONLY on keys if are present on show-table filter
            if (!$this->list_object->get_link_on_field_filter_applied()) {
                $this->list_object->apply_link_on_field_filter("../{$this->controller_object->get_board_read_url_name()}/%row_key%/?auth-code=%auth_code%", crudlexs_base::USE_KEY_FIELDS);
            }
            // Show stats BEFORE
            if ($this->where_to_show_stats == self::SHOW_STATS_BEFORE_TABLE || $this->where_to_show_stats == self::SHOW_STATS_BEFORE_AND_AFTER_TABLE) {
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
            }
            /**
             * HTML OBJECT
             */
            $this->list_object->do_html_object()->append_to($this->board_content_div);
            // Show stats AFTER
            if ($this->where_to_show_stats == self::SHOW_STATS_AFTER_TABLE || $this->where_to_show_stats == self::SHOW_STATS_BEFORE_AND_AFTER_TABLE) {
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
            }
        } else {
            \k1lib\common\show_message("Sin datos para mostrar", "Alerta", "alert");
        }
        if ($do_echo) {
            $this->board_content_div->generate_tag($do_echo);
        } else {
            return $this->board_content_div->generate_tag($do_echo);
        }
    }

    function set_where_to_show_stats($where_to_show_stats) {
        $this->where_to_show_stats = $where_to_show_stats;
    }

}
