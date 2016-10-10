<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;

/**
 * 
 */
class reading extends crudlexs_base_with_data implements crudlexs_base_interface {

    private $html_column_classes = "large-4 medium-6 small-12 column";

    public function __construct($db_table, $row_keys_text, $custom_auth_code = "") {
        if (!empty($row_keys_text)) {
            parent::__construct($db_table, $row_keys_text, $custom_auth_code);
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


            $data_group = new \k1lib\html\div("k1-data-group");

            $data_group->append_to($this->div_container);
            $text_fields_div = new \k1lib\html\div("row");

            $data_label = $this->get_labels_from_data(1);
            if (!empty($data_label)) {
                $this->remove_labels_from_data_filtered();
                (new \k1lib\html\h3($data_label, "k1-data-group-title " . $this->css_class, $this->object_id))->append_to($data_group);
            }
            $labels = $this->db_table_data_filtered[0];
            $values = $this->db_table_data_filtered[1];
            $row = $data_group->append_div("row");

            foreach ($values as $field => $value) {
                if (!empty($value)) {
                    /**
                     * ALL the TEXT field types are sendend to the last position to show nicely the HTML on it.
                     */
                    $field_type = $this->db_table->get_field_config($field, 'type');
                    if ($field_type == 'text') {
                        $div_rows = $text_fields_div->append_div("large-12 column k1-data-item");
                        $last_div_row = $div_rows;
                    } else {
                        $div_rows = $row->append_div($this->html_column_classes . " k1-data-item");
                    }

                    $div_rows->append_div("k1-data-item-label")->set_value($labels[$field]);
                    $div_rows->append_div("k1-data-item-value")->set_value($value);
                }
            }
            $text_fields_div->append_to($data_group);

            return $this->div_container;
        } else {
            return FALSE;
        }
    }

    public function get_html_column_classes() {
        return $this->html_column_classes;
    }

    public function set_html_column_classes($html_column_classes) {
        $this->html_column_classes = $html_column_classes;
    }

}
