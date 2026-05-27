<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage crudlexs\object
 * 
 * Provides read-only display functionality for database records with formatted HTML output.
 */

namespace k1lib\crudlexs\object;

use k1lib\common_strings;
use k1lib\crudlexs\board\read;
use k1lib\db\security\db_table_aliases;
use k1lib\html\div;
use k1lib\html\h3;
use k1lib\notifications\on_DOM as DOM_notification;

/**
 * Reading object for displaying record data in a formatted view.
 * Renders database table data as read-only HTML with labels and values.
 *
 * @package k1lib\crudlexs\object
 */
class reading extends base_with_data implements base_interface {

    /**
     * Parent board instance for coordination.
     * @var read
     */
    public $parent_board;

    /**
     * CSS classes for HTML column layout.
     * @var string
     */
    private $html_column_classes = "col-md-6 col-12";

    /**
     * Creates a reading object for displaying record data.
     *
     * @param string $db_table Database table name
     * @param string $row_keys_text Row key identifier string
     * @param string $custom_auth_code Optional authentication code
     */
    public function __construct($db_table, $row_keys_text, $custom_auth_code = "") {
        if (!empty($row_keys_text)) {
            parent::__construct($db_table, $row_keys_text, $custom_auth_code);
        } else {
            DOM_notification::queue_mesasage(object_base_strings::$error_no_row_keys_text, "alert", $this->notifications_div_id, common_strings::$error);
        }

        /**
         * Necessary for do not loose the inputs with blank or null data
         */
        $this->skip_blanks_on_filters = TRUE;
    }

    /**
     * Generates the HTML representation of the reading object.
     * Displays database record data as read-only HTML with field labels and values.
     *
     * @return div|false Returns div container with formatted data or FALSE if no data
     */
    public function do_html_object() {
        if ($this->db_table_data) {
            $this->div_container->set_attrib("class", "row k1lib-crudlexs-" . $this->css_class);
            $this->div_container->set_attrib("id", $this->object_id);

            $table_alias = db_table_aliases::encode($this->db_table->get_db_table_name());

            $data_group = new div("k1lib-data-group");
            $data_group->set_id("{$table_alias}-fields");

            $data_group->append_to($this->div_container);
            $text_fields_div = new div("row");

            $data_label = $this->get_labels_from_data(1);
            if (!empty($data_label)) {
                $this->remove_labels_from_data_filtered();
                $this->parent_board->set_board_name($data_label);
//                (new h3($data_label, "k1lib-data-group-title " . $this->css_class, "label-field-{$this->object_id}"))->append_to($data_group);
            }
            $labels = $this->db_table_data_filtered[0];
            $values = $this->db_table_data_filtered[1];
            $row = $data_group->append_div("row");

            foreach ($values as $field => $value) {
                if (array_search($field, $this->fields_to_hide) !== FALSE) {
                    continue;
                }
                if (($value !== 0) && ($value !== NULL)) {
                    /**
                     * ALL the TEXT field types are sendend to the last position to show nicely the HTML on it.
                     */
                    $field_type = $this->db_table->get_field_config($field, 'type');
                    $field_alias = $this->db_table->get_field_config($field, 'alias');
                    if ($field_type == 'text') {
                        $div_rows = $text_fields_div->append_div("col-md-6 col-12 k1lib-data-item");
                    } else {
                        $div_rows = $row->append_div($this->html_column_classes . " k1lib-data-item");
                    }
                    if (!empty($field_alias)) {
                        $div_rows->set_id("{$field_alias}-row");
                    }
                    $form_group = $div_rows->append_div('form-group');
                    $label = $form_group->append_label($labels[$field], null, "k1lib-data-item-label");
                    $value_div = $form_group->append_h6($value, "k1lib-data-item-value");
                    if (!empty($field_alias)) {
                        $form_group->set_id("row-{$field_alias}");
                        $label->set_id("label-{$field_alias}");
                        if (method_exists($value, "set_id")) {
                            $value->set_id("value-{$field_alias}");
                        } else {
                            $value_div->set_id("value-{$field_alias}");
                        }
                    }
                }
            }
            $text_fields_div->append_to($data_group);

            return $this->div_container;
        } else {
            return FALSE;
        }
    }

    /**
     * Gets the HTML column classes for layout configuration.
     *
     * @return string CSS column classes
     */
    public function get_html_column_classes() {
        return $this->html_column_classes;
    }

    /**
     * Sets the HTML column classes for layout configuration.
     *
     * @param string $html_column_classes CSS column classes
     * @return void
     */
    public function set_html_column_classes($html_column_classes) {
        $this->html_column_classes = $html_column_classes;
    }
}
