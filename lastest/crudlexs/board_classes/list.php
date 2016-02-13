<?php

namespace k1lib\crudlexs;

class board_list extends board_base implements board_interface {

    const SHOW_BEFORE_TABLE = 1;
    const SHOW_AFTER_TABLE = 2;
    const SHOW_BEFORE_AND_AFTER_TABLE = 3;

    protected $search_enable = TRUE;
    protected $create_enable = TRUE;
    protected $export_enable = TRUE;
    protected $pagination_enable = TRUE;
    protected $stats_enable = TRUE;
    protected $where_to_show_stats = self::SHOW_AFTER_TABLE;

    /**
     *
     * @var \k1lib\crudlexs\listing
     */
    public $list_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->list_object = new \k1lib\crudlexs\listing($this->controller_object->db_table, FALSE);
        }
    }

    /**
     * 
     * @return \k1lib\crudlexs\listing
     */
    public function start_board() {
        if (!$this->is_enabled) {
            \k1lib\common\show_message(board_base_strings::$error_board_disabled, board_base_strings::$alert_board, "warning");
            return FALSE;
        } $this_url = urlencode($_SERVER['REQUEST_URI']);

        if ($this->list_object->get_state()) {
            $search_helper = new \k1lib\crudlexs\search_helper($this->controller_object->db_table);


            /**
             * NEW BUTTON
             */
            if ($this->create_enable) {
                $new_link = \k1lib\html\get_link_button("../{$this->controller_object->get_board_create_url_name()}/?back-url={$this_url}", board_list_strings::$button_new);
                $new_link->append_to($this->board_content_div);
            }

            /**
             * Search buttom
             */
            if ($this->search_enable) {
                $search_buttom = new \k1lib\html\a_tag("#", " " . board_list_strings::$button_search, "_self", board_list_strings::$button_search);
                $search_buttom->set_attrib("class", "button fi-page-search");
                $search_buttom->set_attrib("data-open", "search-modal");
                $search_buttom->append_to($this->board_content_div);

                /**
                 * Clear search
                 */
                if (!empty($search_helper->get_post_data())) {
                    $clear_search_buttom = new \k1lib\html\a_tag($_SERVER['REQUEST_URI'], board_list_strings::$button_search_cancel, "_self", board_list_strings::$button_search_cancel);
                    $search_buttom->set_value(" " . board_list_strings::$button_search_modify);
                    $clear_search_buttom->set_attrib("class", "button warning");
                    $clear_search_buttom->append_to($this->board_content_div);
                }
            }
            $search_helper->do_html_object()->append_to($this->board_content_div);

            $this->data_loaded = $this->list_object->load_db_table_data('show-table');
            return TRUE;
        } else {
            \k1lib\common\show_message(board_base_strings::$error_mysql_table_not_opened, board_base_strings::$error_mysql, "alert");
            return FALSE;
        }
    }

    public function exec_board($do_echo = FALSE) {
        if (!$this->is_enabled) {
            return FALSE;
        }
        /**
         * HTML DB TABLE
         */
        $this_url = urlencode($_SERVER['REQUEST_URI']);


        if ($this->data_loaded) {
            $this->list_object->apply_label_filter();
            $this->list_object->apply_field_label_filter();
            if (\k1lib\forms\file_uploads::is_enabled()) {
                $this->list_object->apply_file_uploads_filter();
            }
            // IF NOT previous link applied this will try to apply ONLY on keys if are present on show-table filter
            if (!$this->list_object->get_link_on_field_filter_applied()) {
                $this->list_object->apply_link_on_field_filter("../{$this->controller_object->get_board_read_url_name()}/%row_keys%/?auth-code=%auth_code%&back-url={$this_url}", crudlexs_base::USE_KEY_FIELDS);
            }
            // Show stats BEFORE
            if (($this->stats_enable) && (($this->where_to_show_stats == self::SHOW_BEFORE_TABLE) || ($this->where_to_show_stats == self::SHOW_BEFORE_AND_AFTER_TABLE))) {
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
                $this->list_object->do_pagination()->append_to($this->board_content_div);
            }
            /**
             * HTML OBJECT
             */
            $list_object_div = $this->list_object->do_html_object();
            $list_object_div->append_to($this->board_content_div);
            // Show stats AFTER
            if (($this->stats_enable) && (($this->where_to_show_stats == self::SHOW_AFTER_TABLE) || ($this->where_to_show_stats == self::SHOW_BEFORE_AND_AFTER_TABLE))) {
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
                $this->list_object->do_pagination()->append_to($this->board_content_div);
            }
            if ($do_echo) {
                $this->board_content_div->generate_tag($do_echo);
                return TRUE;
            } else {
                return $list_object_div;
            }
        } else {
            \k1lib\common\show_message(board_base_strings::$error_mysql_table_no_data, board_base_strings::$alert_board, "alert");
            if ($do_echo) {
                $this->board_content_div->generate_tag($do_echo);
                return TRUE;
            } else {
                return $list_object_div;
            }
        }
    }

    function set_where_to_show_stats($where_to_show_stats) {
        $this->where_to_show_stats = $where_to_show_stats;
    }

    function get_search_enable() {
        return $this->search_enable;
    }

    function get_create_enable() {
        return $this->create_enable;
    }

    function get_export_enable() {
        return $this->export_enable;
    }

    function get_pagination_enable() {
        return $this->pagination_enable;
    }

    function get_stats_enable() {
        return $this->stats_enable;
    }

    function set_search_enable($search_enable) {
        $this->search_enable = $search_enable;
    }

    function set_create_enable($create_enable) {
        $this->create_enable = $create_enable;
    }

    function set_export_enable($export_enable) {
        $this->export_enable = $export_enable;
    }

    function set_pagination_enable($pagination_enable) {
        $this->pagination_enable = $pagination_enable;
    }

    function set_stats_enable($stats_enable) {
        $this->stats_enable = $stats_enable;
    }

}

class board_list_strings {

    /**
     * BUTTON LABELS
     */
    static $button_new = "Add new";
    static $button_search = "Search";
    static $button_search_modify = "Modify search";
    static $button_search_cancel = "Cancel search";

}
