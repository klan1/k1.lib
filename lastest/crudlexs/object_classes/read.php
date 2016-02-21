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
            \k1lib\common\show_message(object_base_strings::$error_no_row_keys_text, common_strings::$error, "alert");
        }

        $this->set_object_id(get_class($this));
        $this->set_css_class(get_class($this));

        /**
         * Necessary for do not loose the inputs with blank or null data
         */
        $this->skip_blanks_on_filters = TRUE;
    }

    public function do_html_object() {
        if ($this->db_table_data) {
            $this->div_container->set_attrib("class", "row k1-crudlexs-" . $this->css_class);
            $this->div_container->set_attrib("id", $this->object_id);

            /**
             * LOAD the custom HTMLtemplate 
             */
            $possible_read_template = "read-templates/" . $this->db_table->get_db_table_name();
            $template_file_path = temply::load_view($possible_read_template, APP_VIEWS_PATH);
            $html = "";
            if ($template_file_path && $this->use_read_custom_template) {
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
            /**
             * NO template !?? so lets do the default
             */
            if (empty($html)) {
                $data_group = new \k1lib\html\div_tag("k1-data-group");

                $data_group->append_to($this->div_container);
                $text_fields_div = new \k1lib\html\div_tag("row");

                $data_label = $this->get_labels_from_data(1);
                if (!empty($data_label)) {
                    $this->remove_labels_from_data_filtered();
                    (new \k1lib\html\h3_tag($data_label, "k1-data-group-title " . $this->css_class, $this->object_id))->append_to($data_group);
                }
                $labels = $this->db_table_data_filtered[0];
                $values = $this->db_table_data_filtered[1];
                $row = $data_group->append_div("row");

                foreach ($values as $field => $value) {
                    
                    /**
                     * ALL the TEXT field types are sendend to the last position to show nicely the HTML on it.
                     */
                    $field_type = $this->db_table->get_field_config($field, 'type');
                    if ($field_type == 'text') {
                        $div_rows = $text_fields_div->append_div("large-12 column k1-data-item");
                    } else {
                        $div_rows = $row->append_div("large-4 medium-6 small-12 column k1-data-item");
                    }

                    $div_rows->append_div("k1-data-item-label")->set_value($labels[$field]);
                    $div_rows->append_div("k1-data-item-value")->set_value($value);
                }
                $text_fields_div->append_to($data_group);
                
                $div_rows->set_attrib("class", 'end', TRUE);

//              $this->controller_object->board_read_object->set_board_name($data_label);
//              $html = \k1lib\html\make_row_2columns_layout($this->db_table_data_filtered[1], $extra_css_clasess, $this->db_table_data_filtered[0]);
            }
            $this->div_container->set_value($html);
            return $this->div_container;
        } else {
            return FALSE;
        }
    }

}
