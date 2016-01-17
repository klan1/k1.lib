<?php

namespace k1lib\crudlexs;

/**
 * 
 */
class reading extends crudlexs_base_with_data implements crudlexs_base_interface {

    public function do_html_object($extra_css_clasess = "") {
        if ($this->db_table_data) {
            $div_container = new \k1lib\html\div_tag("row");
            $callout_div = new \k1lib\html\div_tag("callout gray");
            $callout_div->append_to($div_container);
            
            $html = \k1lib\html\make_row_2columns_layout($this->db_table_data_filtered[1], $extra_css_clasess, $this->db_table_data_filtered[0]);
            $callout_div->set_value($html);
            return $div_container;
        } else {
            return FALSE;
        }
    }

}
