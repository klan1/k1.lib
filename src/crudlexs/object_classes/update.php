<?php

namespace k1lib\crudlexs;

use \k1lib\urlrewrite\url as url;
use \k1lib\html\DOM as DOM;
use \k1lib\notifications\on_DOM as DOM_notification;

class updating extends \k1lib\crudlexs\creating {

    protected $update_perfomed = FALSE;
    protected $updated = NULL;

    public function __construct(\k1lib\crudlexs\class_db_table $db_table, $row_keys_text) {
        if (!empty($row_keys_text)) {
            parent::__construct($db_table, $row_keys_text);
        } else {
            DOM_notification::queue_mesasage(object_base_strings::$error_no_row_keys_text, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
        }

        creating_strings::$button_submit = updating_strings::$button_submit;
        creating_strings::$button_cancel = updating_strings::$button_cancel;

        $this->object_state = "update";
    }

    public function load_db_table_data($blank_data = FALSE) {
        $return_data = parent::load_db_table_data($blank_data);

        $url_action = url::set_url_rewrite_var(url::get_url_level_count(), "url_action", FALSE);
        $url_action_on_encoded_field = url::set_url_rewrite_var(url::get_url_level_count(), "url_action_on_encoded_field", FALSE);
        $url_action_on_field = $this->decrypt_field_name($url_action_on_encoded_field);

        if ($url_action == "unlink-uploaded-file") {
            \k1lib\forms\file_uploads::unlink_uploaded_file($this->db_table_data[1][$url_action_on_field]);
            $this->db_table_data[1][$url_action_on_field] = NULL;
            $this->db_table->update_data($this->db_table_data[1], $this->db_table_data_keys[1]);
            \k1lib\html\html_header_go(\k1lib\urlrewrite\get_back_url());
        }

        return $return_data;
    }

    public function do_update() {
        //$this->set_back_url("javascript:history.back()");
        $error_data = NULL;

        $this->div_container->set_attrib("class", "k1lib-crudlexs-update");
        $this->post_incoming_array = \k1lib\forms\check_all_incomming_vars($this->post_incoming_array);
        $update_result = $this->db_table->update_data($this->post_incoming_array, $this->db_table_data_keys[1], $error_data);
        if ($update_result !== FALSE) {
            $this->update_perfomed = TRUE;
            $this->updated = TRUE;
            if ($this->object_state == 'update') {
                DOM_notification::queue_mesasage(updating_strings::$data_updated, "success", $this->notifications_div_id);
            }
            return TRUE;
        } else {
            if (is_array($error_data) && !empty($error_data)) {
                $this->post_validation_errors = array_merge($this->post_validation_errors, $error_data);
            } else {
                $this->update_perfomed = TRUE;
            }

            $this->updated = FALSE;
            if ($this->object_state == 'update') {
                DOM_notification::queue_mesasage(updating_strings::$data_not_updated, "warning", $this->notifications_div_id);
            }
            return FALSE;
        }
    }

    public function post_update_redirect($url_to_go = "../../", $do_redirect = TRUE) {
        if (!empty($this->update_perfomed)) {
            /**
             * Merge the ROW KEYS with all the possible keys on the POST array
             */
            $merged_key_array = array_merge(
                    $this->db_table_data_keys[1]
                    , \k1lib\sql\get_keys_array_from_row_data(
                            $this->post_incoming_array
                            , $this->db_table->get_db_table_config()
                    )
            );
            $row_key_text = \k1lib\sql\table_keys_to_text($merged_key_array, $this->db_table->get_db_table_config());
            if (!empty($url_to_go)) {
                $this->set_auth_code($row_key_text);
                $this->set_auth_code_personal($row_key_text);
                $url_to_go = str_replace("--rowkeys--", $row_key_text, $url_to_go);
                $url_to_go = str_replace("--authcode--", $this->get_auth_code(), $url_to_go);
            }
            if ($do_redirect) {
                \k1lib\html\html_header_go($url_to_go);
                exit;
                return TRUE;
            } else {
                return $url_to_go;
            }
        } else {
            return "";
        }
    }

}
