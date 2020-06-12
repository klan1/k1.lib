<?php

namespace k1lib\crudlexs;

use \k1lib\urlrewrite\url as url;
use k1lib\notifications\on_DOM as DOM_notification;

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
    protected $related_do_clean_array_on_query_filter = FALSE;
    protected $related_use_rows_key_text = TRUE;
    protected $related_use_show_rule = "show-related";
    //RELATED CONFIG

    /**
     * @var listing
     */
    protected $related_list = NULL;
    protected $related_show_new = TRUE;
    protected $related_show_all_data = TRUE;
    protected $related_rows_to_show = 5;
    protected $related_edit_url = NULL;
    protected $related_apply_filters = TRUE;
    protected $related_custom_field_labels = [];
    protected $related_do_pagination = TRUE;
    //RELATED HTML OBJECTS

    /**
     * @var  \k1lib\html\a
     */
    protected $related_html_object_show_new = NULL;

    /**
     * @var \k1lib\html\a
     */
    protected $related_html_object_show_all_data = NULL;

    /**
     * @var \k1lib\html\foundation\table_from_data
     */
    protected $related_html_table_object = NULL;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-read";
            $this->row_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), 'row-keys-text', FALSE);
            $this->read_object = new \k1lib\crudlexs\reading($this->controller_object->db_table, $this->row_keys_text);
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }

        if (!empty($this->row_keys_text)) {
            if ($this->read_object->get_state()) {
                /**
                 * BACK
                 */
                if ($this->back_enable && (isset($_GET['back-url']))) {
                    $back_url = \k1lib\urlrewrite\get_back_url();
                    $back_link = \k1lib\html\get_link_button($back_url, board_read_strings::$button_back, "small");
                    $back_link->append_to($this->button_div_tag);
                }
                /**
                 * ALL DATA
                 */
                if ($this->all_data_enable) {
                    $all_data_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_list_url_name()}/";
                    $all_data_link = \k1lib\html\get_link_button(
                            url::do_url($all_data_url, [], TRUE, ['no-rules'])
                            , board_read_strings::$button_all_data
                            , "small"
                    );
                    $all_data_link->append_to($this->button_div_tag);
                }
                /**
                 * EDIT BUTTON
                 */
                if ($this->update_enable) {
                    $edit_url = $this->controller_object->get_controller_root_dir() . "{$this->controller_object->get_board_update_url_name()}/{$this->row_keys_text}/";
                    $get_vars = [
                        "auth-code" => $this->read_object->get_auth_code(),
//                        "back-url" => $_SERVER['REQUEST_URI'],
                    ];
                    $edit_link = \k1lib\html\get_link_button(url::do_url($edit_url, $get_vars), board_read_strings::$button_edit, "small");
                    $edit_link->append_to($this->button_div_tag);
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
                    $delete_link = \k1lib\html\get_link_button(url::do_url($delete_url, $get_vars), board_read_strings::$button_delete, "small");
                    $delete_link->append_to($this->button_div_tag);
                }

                $this->data_loaded = $this->read_object->load_db_table_data($this->show_rule_to_apply);
                return $this->board_content_div;
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
                DOM_notification::queue_title(board_base_strings::$error_mysql);
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div|boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->read_object->get_state() && !empty($this->row_keys_text)) {
            if ($this->data_loaded) {
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

//                $this->board_content_div->set_attrib("class", "grid-x", TRUE);

                $span_tag = new \k1lib\html\span("key-field");
                $this->read_object->apply_html_tag_on_field_filter($span_tag, \k1lib\crudlexs\crudlexs_base::USE_KEY_FIELDS);

                $read_content_div = $this->read_object->do_html_object();
                $read_content_div->append_to($this->board_content_div);

                return $this->board_content_div;
            } else {
                DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_no_data, "alert", $this->notifications_div_id);
                DOM_notification::queue_title(board_base_strings::$error_mysql);
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

    public function set_related_do_clean_array_on_query_filter($related_do_clean_array_on_query_filter) {
        $this->related_do_clean_array_on_query_filter = $related_do_clean_array_on_query_filter;
    }

    public function get_related_do_clean_array_on_query_filter() {
        return $this->related_do_clean_array_on_query_filter;
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
     * @return \k1lib\html\div|boolean
     */
    public function create_related_list(class_db_table $db_table, $field_links_array, $title, $board_root, $board_create, $board_read, $board_list, $use_back_url = FALSE, $clear_url = FALSE, $custom_key_array = NULL) {

        $table_alias = \k1lib\db\security\db_table_aliases::encode($db_table->get_db_table_name());
        $detail_div = new \k1lib\html\div();

        $this->related_list = $this->do_related_list($db_table, $field_links_array, $board_root, $board_read, $use_back_url, $clear_url, $custom_key_array);


        if (!empty($this->related_list)) {
            $current_row_keys_text = $this->read_object->get_row_keys_text();
            $current_row_keys_text_auth_code = md5(\k1lib\K1MAGIC::get_value() . $current_row_keys_text);

            $detail_div->set_class("k1lib-related-data-list {$table_alias}");
            $related_title = $detail_div->append_h4($title, "{$table_alias}");
            $detail_div->append_div("related-messaje");

            $get_vars = [
                "auth-code" => $current_row_keys_text_auth_code,
                "back-url" => $_SERVER['REQUEST_URI'],
            ];

            if (isset($data_loaded) && $data_loaded) {
                $all_data_url = url::do_url(APP_URL . $board_root . "/" . $board_list . "/{$current_row_keys_text}/", $get_vars, FALSE);
                $this->related_html_object_show_all_data = \k1lib\html\get_link_button($all_data_url, board_read_strings::$button_all_data, "tiny");
                if ($this->related_show_all_data) {
                    $related_title->set_value($this->related_html_object_show_all_data, TRUE);
                }
            }
            if ($use_back_url) {
                $create_url = url::do_url(APP_URL . $board_root . "/" . $board_create . "/{$current_row_keys_text}/", $get_vars, TRUE);
            } else {
                $get_vars = [
                    "auth-code" => $current_row_keys_text_auth_code,
                ];
                $create_url = url::do_url(APP_URL . $board_root . "/" . $board_create . "/{$current_row_keys_text}/", $get_vars, TRUE, ['back-url'], FALSE);
            }
            $this->related_html_object_show_new = \k1lib\html\get_link_button($create_url, board_list_strings::$button_new, "tiny");

            if ($this->related_show_new) {
                $related_title->set_value($this->related_html_object_show_new, TRUE);
            }

            $this->related_list->do_html_object()->append_to($detail_div);
            $this->related_html_table_object = $this->related_list->get_html_table();
            if ($db_table->get_total_rows() > $this->related_rows_to_show && $this->related_do_pagination) {
                $this->related_list->do_pagination()->append_to($detail_div);
                $this->related_list->do_row_stats()->append_to($detail_div);
            }

//            listing::$rows_per_page = $actual_rows_per_page;
        }
// TODO: NONSENSE line !
//        $this->set_related_show_new(TRUE);
        return $detail_div;
    }

    /**
     * @param \k1lib\crudlexs\class_db_table $db_table
     * @param array $field_links_array
     * @param string $board_root
     * @param string $board_read
     * @param boolean $clear_url
     * @return \k1lib\crudlexs\listing|boolean
     */
    public function do_related_list(class_db_table $db_table, $field_links_array, $board_root, $board_read, $use_back_url, $clear_url = FALSE, $custom_key_array = []) {

        $table_alias = \k1lib\db\security\db_table_aliases::encode($db_table->get_db_table_name());

        if ($this->is_enabled && $this->read_object->is_valid()) {

            /**
             * Clients list
             */
            if ($db_table->get_state()) {
                if ($this->related_use_rows_key_text) {
                    if (count($custom_key_array) < 1) {
                        $current_row_keys_array = $this->read_object->get_row_keys_array();
                        /**
                         * lets fix the non-same key name
                         */
                        $db_table_config = $db_table->get_db_table_config();
                        foreach ($db_table_config as $field => $field_config) {
                            if (!empty($field_config['refereced_column_config'])) {
                                $fk_field_name = $field_config['refereced_column_config']['field'];
                                foreach ($current_row_keys_array as $field_current => $value) {
                                    if ($field_current == $field) {
                                        unset($current_row_keys_array[$field_current]);
                                        $current_row_keys_array[$fk_field_name] = $value;
                                    }
                                }
                            }
                        }
                    } else {
                        $current_row_keys_array = $custom_key_array;
                    }
                    $db_table->set_field_constants($current_row_keys_array);
                    $db_table->set_query_filter($current_row_keys_array, TRUE, $this->related_do_clean_array_on_query_filter);
                }

                /**
                 * LIST OBJECT must be created here to know if ther is data or not to show
                 * all data button.
                 */
                $this->related_list = new \k1lib\crudlexs\listing($db_table, FALSE);

                if (!empty($this->related_custom_field_labels)) {
                    $this->related_list->set_custom_field_labels($this->related_custom_field_labels);
                }

                $this->related_list->set_rows_per_page($this->related_rows_to_show);
                $data_loaded = $this->related_list->load_db_table_data($this->related_use_show_rule);
                if ($data_loaded) {
                    if ($this->related_apply_filters) {
                        $this->related_apply_filters();
                        $this->related_apply_link_read_field($field_links_array, $board_root, $board_read, $use_back_url, $clear_url);
                    }
                }
                return $this->related_list;
            } else {
                trigger_error("DB Table couldn't be opened : " . $db_table->get_db_table_name(), E_USER_NOTICE);
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    public function related_apply_link_read_field($field_links_array, $board_root, $board_read, $use_back_url, $clear_url = FALSE) {
        if ($this->related_list->get_db_table_data()) {
            if ($clear_url) {
                $get_vars = [];
            } else {

                $get_vars = [
                    "auth-code" => "--authcode--",
                ];
                if ($use_back_url) {
                    $get_vars["back-url"] = $_SERVER['REQUEST_URI'];
                }
            }
            $link_row_url = url::do_url(APP_URL . $board_root . "/" . $board_read . "/--rowkeys--/", $get_vars);
            $this->related_list->apply_link_on_field_filter($link_row_url, $field_links_array);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function related_apply_filters() {
        if ($this->related_list->get_db_table_data()) {
            $this->related_list->apply_label_filter();
            $this->related_list->apply_field_label_filter();
            if (\k1lib\forms\file_uploads::is_enabled()) {
//                    $this->related_list->set
                $this->related_list->apply_file_uploads_filter();
            }
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function set_related_edit_url($related_edit_url) {
        $this->related_edit_url = $related_edit_url;
    }

    public function get_related_rows_to_show() {
        return $this->related_rows_to_show;
    }

    public function set_related_rows_to_show($related_rows_to_show) {
        $this->related_rows_to_show = $related_rows_to_show;
    }

    /**
     * @return \k1lib\html\a
     */
    public function get_related_html_object_show_new() {
        return $this->related_html_object_show_new;
    }

    /**
     * @return \k1lib\html\a
     */
    public function get_related_html_object_show_all_data() {
        return $this->related_html_object_show_all_data;
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

    public function set_related_custom_field_labels($related_custom_field_labels) {
        $this->related_custom_field_labels = $related_custom_field_labels;
    }

    function set_delete_enable($delete_enable) {
        $this->delete_enable = $delete_enable;
    }

    public function set_related_show_new($related_show_new) {
        $this->related_show_new = $related_show_new;
    }

    public function set_related_show_all_data($related_show_all_data) {
        $this->related_show_all_data = $related_show_all_data;
    }

    public function set_related_do_pagination($related_do_pagination) {
        $this->related_do_pagination = $related_do_pagination;
    }

    /**
     * @return \k1lib\html\foundation\table_from_data
     */
    public function get_related_html_table_object() {
        return $this->related_html_table_object;
    }

    public function set_related_apply_filters($related_apply_filters) {
        $this->related_apply_filters = $related_apply_filters;
    }

    /**
     * @return listing
     */
    public function get_related_list() {
        return $this->related_list;
    }

}
