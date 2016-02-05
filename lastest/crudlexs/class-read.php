<?php

namespace k1lib\crudlexs;

/**
 * 
 */
class reading extends crudlexs_base_with_data implements crudlexs_base_interface {

    public function __construct($db_table, $row_keys_text) {
        if (!empty($row_keys_text)) {
            parent::__construct($db_table, $row_keys_text);
        } else {
            \k1lib\common\show_message("The keys can't be empty", "Error", "alert");
        }
    }

    public function do_html_object($extra_css_clasess = "") {
        if ($this->db_table_data) {
            $this->div_container->set_attrib("class", "k1-crudlexs-read row");
            $html = \k1lib\html\make_row_2columns_layout($this->db_table_data_filtered[1], $extra_css_clasess, $this->db_table_data_filtered[0]);
            $this->div_container->set_value($html);
            return $this->div_container;
        } else {
            return FALSE;
        }
    }

}
