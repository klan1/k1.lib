<?php

namespace k1lib\crudlexs;

/**
 * 
 */
class reading extends crudlexs_base_with_data implements crudlexs_base_interface {

    public function do_code($extra_css_clasess = "") {
        if ($this->db_table_data) {
            $html = \k1lib\html\make_row_2columns_layout($this->db_table_data_filtered[1], $extra_css_clasess, $this->db_table_data_filtered[0]);
            return $html;
        } else {
            return FALSE;
        }
    }

}
