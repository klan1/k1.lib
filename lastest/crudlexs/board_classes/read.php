<?php

namespace k1lib\crudlexs;

use \k1lib\urlrewrite\url as url;

class board_read extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\reading
     */
    public $read_object;
    private $row_keys_text;
    // Buttons enable
    protected $back_enable = TRUE;
    protected $all_data_enable = TRUE;
    protected $update_enable = TRUE;
    protected $delete_enable = TRUE;
    protected $use_label_as_title_enabled = TRUE;
    protected $related_use_rows_key_text = TRUE;
    protected $related_use_show_rule = "show-related";
    //RELATED CONFIG
    protected $related_show_new = TRUE;
    protected $related_show_all_data = TRUE;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-read";
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), "row_keys_text", FALSE);
            $this->read_object = new \k1lib\crudlexs\reading($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    /**
     * @return \k1lib\html\div_tag|boolean
     */
    public function start_board() {
        if (!$this->is_enabled) {
            \k1lib\common\show_message(board_base_strings::$error_board_disabled, board_base_strings::$alert_board, "warning");
            return FALSE;
        }
        if (!empty($this->row_keys_text)) {
            if ($this->read_object->get_state()) {
                /**
                 * BACK
                 */
                if ($this->back_enable && (isset($_GET['back-url']))) {
                    $back_url = \k1lib\urlrewrite\get_back_url();
                    $back_link = \k1lib\html\get_link_button($back_url, board_read_strings::$button_back);
                    $back_link->append_to($this->board_content_div);
                }
                /**
                 * ALL DATA
                 */
                if ($this->all_data_enable) {
                    $all_data_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_list_url_name()}/";
                    $all_data_link = \k1lib\html\get_link_button(
                            url::do_url($all_data_url, [], TRUE,['no-rules'])
                            , board_read_strings::$button_all_data
                    );
                    $all_data_link->append_to($this->board_content_div);
                }
                /**
                 * EDIT BUTTON
                 */
                if ($this->update_enable) {
                    $edit_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_update_url_name()}/{$this->row_keys_text}/";
                    $get_vars = [
                        "auth-code" => $this->read_object->get_auth_code(),
                        "back-url" => $_SERVER['REQUEST_URI'],
                    ];
                    $edit_link = \k1lib\html\get_link_button(url::do_url($edit_url, $get_vars), board_read_strings::$button_edit);
                    $edit_link->append_to($this->board_content_div);
                }
                /**
                 * DELETE BUTTON
                 */
                if ($this->delete_enable) {
                    $delete_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_delete_url_name()}/{$this->row_keys_text}/";
                    if (\k1lib\urlrewrite\get_back_url(TRUE)) {
                        $get_vars = [
                            "auth-code" => $this->read_object->get_auth_code_personal(),
                            "back-url" => \k1lib\urlrewrite\get_back_url(TRUE),
                        ];
                    } else {
                        $get_vars = [
                            "auth-code" => $this->read_object->get_auth_code_personal(),
                        ];
                    }
                    $delete_link = \k1lib\html\get_link_button(url::do_url($delete_url, $get_vars), board_read_strings::$button_delete);
                    $delete_link->append_to($this->board_content_div);
                }

                $this->data_loaded = $this->read_object->load_db_table_data($this->show_rule_to_apply);
                return $this->board_content_div;
            } else {
                \k1lib\common\show_message(board_base_strings::$error_mysql_table_not_opened, board_base_strings::$error_mysql, "alert");
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div_tag|boolean
     */
    public function exec_board($do_echo = TRUE) {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->read_object->get_state() && !empty($this->row_keys_text)) {
            if ($this->data_loaded) {
                if ($this->use_label_as_title_enabled) {
                    $data_label = $this->read_object->get_labels_from_data(1);
                    if (!empty($data_label)) {
//                        $this->read_object->remove_labels_from_data_filtered();
//                        $this->controller_object->board_read_object->set_board_name($data_label);
                    }
                }
                if ($this->apply_label_filter) {
                    $this->read_object->apply_label_filter();
                }
                if ($this->apply_field_label_filter) {
                    $this->read_object->apply_field_label_filter();
                }
//                $this->read_object->set_use_read_custom_template();
                if (\k1lib\forms\file_uploads::is_enabled()) {
                    $this->read_object->apply_file_uploads_filter();
                }

//                $this->board_content_div->set_attrib("class", "row", TRUE);

                $span_tag = new \k1lib\html\span_tag("key-field");
                $this->read_object->apply_html_tag_on_field_filter($span_tag, \k1lib\crudlexs\crudlexs_base::USE_KEY_FIELDS);

                $this->read_object->do_html_object()->append_to($this->board_content_div);

                if ($do_echo) {
                    $this->board_content_div->generate_tag(TRUE);
                    return TRUE;
                } else {
                    return $this->board_content_div;
                }
            } else {
                \k1lib\common\show_message(board_base_strings::$error_mysql_table_no_data, board_base_strings::$error_mysql, "alert");
                $this->read_object->make_invalid();
                $this->is_enabled = FALSE;
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function finish_board() {
        // (:    
    }

    public function set_related_use_rows_key_text($related_use_rows_key_text) {
        $this->related_use_rows_key_text = $related_use_rows_key_text;
    }

    public function set_related_use_show_rule($related_use_show_rule) {
        $this->related_use_show_rule = $related_use_show_rule;
    }

    public function get_related_show_new() {
        return $this->related_show_new;
    }

    public function get_related_show_all_data() {
        return $this->related_show_all_data;
    }

    /**
     * 
     * @param \k1lib\crudlexs\class_db_table $db_table
     * @param array $field_links_array
     * @param string $title
     * @param string $board_root
     * @param string $board_create
     * @param string $board_read
     * @param boolean $show_create
     * @return \k1lib\html\div_tag|boolean
     */
    public function create_related_list(class_db_table $db_table, $field_links_array, $title, $board_root, $board_create, $board_read, $board_list, $use_back_url = FALSE) {

        $detail_div = new \k1lib\html\div_tag();

        if ($this->is_enabled && $this->read_object->is_valid()) {
            $current_row_keys_text = $this->controller_object->board_read_object->read_object->get_row_keys_text();
            $current_row_keys_text_auth_code = md5(\k1lib\K1MAGIC::get_value() . $current_row_keys_text);

            /**
             * Clients list
             */
            if ($db_table->get_state()) {
                if ($this->related_use_rows_key_text) {
                    $current_row_keys_array = $this->controller_object->board_read_object->read_object->get_row_keys_array();
                    $db_table->set_query_filter($current_row_keys_array, TRUE, FALSE);
                }

                /**
                 * LIST OBJECT must be created here to know if ther is data or not to show
                 * all data button.
                 */
                $related_table_list = new \k1lib\crudlexs\listing($db_table, FALSE);
                $actual_rows_per_page = listing::$rows_per_page;
                listing::$rows_per_page = 5;
                $data_loaded = $related_table_list->load_db_table_data($this->related_use_show_rule);
                if ($data_loaded) {

                    $related_table_list->apply_label_filter();
                    $related_table_list->apply_field_label_filter();
                    if (\k1lib\forms\file_uploads::is_enabled()) {
//                    $related_table_list->set
                        $related_table_list->apply_file_uploads_filter();
                    }
                    $get_vars = [
                        "auth-code" => "--authcode--",
                        "back-url" => $_SERVER['REQUEST_URI'],
                    ];
                    $read_row_url = url::do_url(APP_URL . $board_root . "/" . $board_read . "/--rowkeys--/", $get_vars);
                    $related_table_list->apply_link_on_field_filter($read_row_url, $field_links_array);
                }


                $detail_div->set_attrib("class", "k1app-related-list {$db_table->get_db_table_name()}-realted-list");

                $related_title = new \k1lib\html\h4_tag("sub-title");
                $related_title->set_value($title);
                $related_title->append_to($detail_div);

                if ($this->related_show_all_data && $data_loaded) {
                    $get_vars = [
                        "auth-code" => $current_row_keys_text_auth_code,
                        "back-url" => $_SERVER['REQUEST_URI'],
                    ];
                    $all_data_url = url::do_url(APP_URL . $board_root . "/" . $board_list . "/{$current_row_keys_text}/", $get_vars, FALSE);
                    $all_data_button = \k1lib\html\get_link_button($all_data_url, board_read_strings::$button_all_data, "tiny");
                    $related_title->set_value($all_data_button->generate_tag(), TRUE);
                }
                if ($this->related_show_new) {
                    if ($use_back_url) {
                        $get_vars = [
                            "auth-code" => $current_row_keys_text_auth_code,
                            "back-url" => $_SERVER['REQUEST_URI'],
                        ];
                        $create_url = url::do_url(APP_URL . $board_root . "/" . $board_create . "/{$current_row_keys_text}/", $get_vars, TRUE);
                    } else {
                        $get_vars = [
                            "auth-code" => $current_row_keys_text_auth_code,
//                        "back-url" => $_SERVER['REQUEST_URI'],
                        ];
                        $create_url = url::do_url(APP_URL . $board_root . "/" . $board_create . "/{$current_row_keys_text}/", $get_vars, TRUE, ['back-url'], FALSE);
                    }
                    $new_related_button = \k1lib\html\get_link_button($create_url, board_list_strings::$button_new, "tiny");
                    $related_title->set_value($new_related_button->generate_tag(), TRUE);
                }

                $related_table_list->do_html_object()->append_to($detail_div);
                $related_table_list->do_pagination()->append_to($detail_div);
                $related_table_list->do_row_stats()->append_to($detail_div);
                listing::$rows_per_page = $actual_rows_per_page;
            } else {
                return false;
            }
        }
        $this->set_related_show_new(TRUE);
        return $detail_div;
    }

    function get_back_enable() {
        return $this->back_enable;
    }

    function get_update_enable() {
        return $this->update_enable;
    }

    function get_delete_enable() {
        return $this->delete_enable;
    }

    function set_all_data_enable($all_data_enable) {
        $this->all_data_enable = $all_data_enable;
    }

    function set_back_enable($back_enable) {
        $this->back_enable = $back_enable;
    }

    function set_update_enable($update_enable) {
        $this->update_enable = $update_enable;
    }

    function set_delete_enable($delete_enable) {
        $this->delete_enable = $delete_enable;
    }

    function set_use_label_as_title_enabled($use_label_as_title_enabled) {
        $this->use_label_as_title_enabled = $use_label_as_title_enabled;
    }

    public function set_related_show_new($related_show_new) {
        $this->related_show_new = $related_show_new;
    }

    public function set_related_show_all_data($related_show_all_data) {
        $this->related_show_all_data = $related_show_all_data;
    }

}
