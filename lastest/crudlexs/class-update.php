<?php

namespace k1lib\crudlexs;

class updating extends \k1lib\crudlexs\creating {

    public function __construct(\k1lib\crudlexs\class_db_table $db_table, $row_keys_text) {
        if (!empty($row_keys_text)) {
            parent::__construct($db_table, $row_keys_text);
            if ($this->get_state()) {
                $this->set_back_link("javascript:history.back()");
                $this->div_container->set_attrib("class", "k1-crudlexs-update");
            } else {
                
            }
        } else {
            \k1lib\common\show_message("The keys can't be empty", "Error", "alert");
        }
        creating_strings::$button_submit = updating_strings::$button_submit;
    }

    public function do_update($url_to_go = null) {
        if ($this->db_table->update_data($this->post_incoming_array, $this->db_table_data_keys[1])) {
            /**
             * Merge the ROW KEYS with all the possible keys on the POST array
             */
            $merged_key_array = array_merge(
                    $this->db_table_data_keys[1]
                    , \k1lib\sql\get_keys_array_from_row_data(
                            $this->post_incoming_array
                            , $this->db_table->get_db_table_config())
            );
            $row_key_text = \k1lib\sql\table_keys_to_text($merged_key_array, $this->db_table->get_db_table_config());
            if (!empty($url_to_go)) {
                $url_to_go = str_replace("%row_key%", $row_key_text, $url_to_go);
                $this->set_auth_code($row_key_text);
                \k1lib\html\html_header_go($url_to_go . "?auth-code={$this->get_auth_code()}");
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

class updating_strings {

    static $button_submit = "Update";

}
