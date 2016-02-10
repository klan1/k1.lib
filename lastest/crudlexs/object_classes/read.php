<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;

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
        $this->skip_blanks_on_filters = TRUE;
    }

    public function do_html_object($extra_css_clasess = "") {
        if ($this->db_table_data) {
            $this->div_container->set_attrib("class", "k1-crudlexs-read row");

            $possible_read_template = "read-templates/" . $this->db_table->get_db_table_name();
            $template_file_path = temply::load_view($possible_read_template, APP_VIEWS_PATH);
            $html = "";
            if ($template_file_path) {
                ob_start();
                include $template_file_path;
                $html = ob_get_contents();
                ob_end_clean();

                if ($template_file_path) {
                    foreach ($this->db_table_data_filtered[1] as $field => $value) {
                        if (temply::is_place_registered("{$field}-label")) {
                            temply::set_place_value("{$field}-label", $this->db_table_data_filtered[0][$field]);
                        }
                        if (temply::is_place_registered($field)) {
                            temply::set_place_value($field, $value);
                        }
                    }
                }
            }
            if (empty($html)) {
                $html = \k1lib\html\make_row_2columns_layout($this->db_table_data_filtered[1], $extra_css_clasess, $this->db_table_data_filtered[0]);
            }
            $this->div_container->set_value($html);
            return $this->div_container;
        } else {
            return FALSE;
        }
    }

}
