<?php

namespace k1lib\crudlexs;

use \k1lib\urlrewrite\url as url;

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
    protected $back_enable = TRUE;

    /**
     *
     * @var \k1lib\crudlexs\listing
     */
    public $list_object;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-list";
            $this->list_object = new \k1lib\crudlexs\listing($this->controller_object->db_table, FALSE);
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

        if ($this->list_object->get_state()) {
            if ($this->search_enable) {
                $search_helper = new \k1lib\crudlexs\search_helper($this->controller_object->db_table, $this->list_object->get_object_id());
            }
            /**
             * BACK
             */
            if ($this->back_enable && (isset($_GET['back-url']))) {
                $back_url = \k1lib\urlrewrite\get_back_url();
                $back_link = \k1lib\html\get_link_button($back_url, board_read_strings::$button_back);
                $back_link->append_to($this->board_content_div);
            }
            /**
             * NEW BUTTON
             */
            $related_url_keys_text = url::get_url_level_value_by_name("related_url_keys_text");
            if (empty($related_url_keys_text)) {
                $related_url_keys_text = "";
                $new_link = \k1lib\html\get_link_button(url::do_url("../{$this->controller_object->get_board_create_url_name()}/" . $related_url_keys_text), board_list_strings::$button_new);
            } else {
                $related_url_keys_text .= "/";
                $new_link = \k1lib\html\get_link_button(url::do_url("../../{$this->controller_object->get_board_create_url_name()}/" . $related_url_keys_text), board_list_strings::$button_new);
            }
            if ($this->create_enable) {
//                $new_link = \k1lib\html\get_link_button(url::do_url("../{$this->controller_object->get_board_create_url_name()}/" . $related_url_keys_text), board_list_strings::$button_new);
//                $new_link = \k1lib\html\get_link_button("../{$this->controller_object->get_board_create_url_name()}/?back-url={$this_url}", board_list_strings::$button_new);
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
                if ($this->search_enable && !empty($search_helper->get_post_data())) {
                    $clear_search_buttom = new \k1lib\html\a_tag(url::do_url($_SERVER['REQUEST_URI']), board_list_strings::$button_search_cancel, "_self", board_list_strings::$button_search_cancel);
                    $search_buttom->set_value(" " . board_list_strings::$button_search_modify);
                    $clear_search_buttom->set_attrib("class", "button warning");
                    $clear_search_buttom->append_to($this->board_content_div);
                }
                $search_helper->do_html_object()->append_to($this->board_content_div);
            }

            $this->data_loaded = $this->list_object->load_db_table_data($this->show_rule_to_apply);
            return $this->board_content_div;
        } else {
            \k1lib\common\show_message(board_base_strings::$error_mysql_table_not_opened, board_base_strings::$error_mysql, "alert");
            $this->list_object->make_invalid();
            $this->is_enabled = FALSE;

            return FALSE;
        }
    }

    /**
     * @return \k1lib\html\div_tag|boolean
     */
    public function exec_board($do_echo = FALSE) {
        if (!$this->is_enabled) {
            return FALSE;
        }
        /**
         * HTML DB TABLE
         */
        if ($this->data_loaded) {
            if ($this->apply_label_filter) {
                $this->list_object->apply_label_filter();
            }
            if ($this->apply_field_label_filter) {
                $this->list_object->apply_field_label_filter();
            }
            if (\k1lib\forms\file_uploads::is_enabled()) {
                $this->list_object->apply_file_uploads_filter();
            }
            // IF NOT previous link applied this will try to apply ONLY on keys if are present on show-list filter
            if (!$this->list_object->get_link_on_field_filter_applied()) {
                $get_vars = [
                    "auth-code" => "--authcode--",
                    "back-url" => $_SERVER['REQUEST_URI'],
                ];
                $this->list_object->apply_link_on_field_filter(url::do_url("../{$this->controller_object->get_board_read_url_name()}/--rowkeys--/", $get_vars), crudlexs_base::USE_KEY_FIELDS);
            }
            // Show stats BEFORE
            if (($this->stats_enable) && (($this->where_to_show_stats == self::SHOW_BEFORE_TABLE) || ($this->where_to_show_stats == self::SHOW_BEFORE_AND_AFTER_TABLE))) {
                $this->list_object->do_pagination()->append_to($this->board_content_div);
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
            }
            /**
             * HTML OBJECT
             */
            $this->list_object->do_html_object()->append_to($this->board_content_div);
            // Show stats AFTER
            if (($this->stats_enable) && (($this->where_to_show_stats == self::SHOW_AFTER_TABLE) || ($this->where_to_show_stats == self::SHOW_BEFORE_AND_AFTER_TABLE))) {
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
                $this->list_object->do_pagination()->append_to($this->board_content_div);
            }
            if ($do_echo) {
                $this->board_content_div->generate_tag($do_echo);
                return TRUE;
            } else {
                return $this->board_content_div;
            }
        } else {
            $this->list_object->do_html_object()->append_to($this->board_content_div);
            if ($do_echo) {
                $this->board_content_div->generate_tag($do_echo);
                return TRUE;
            } else {
                return $this->board_content_div;
            }
        }
    }

    public function finish_board() {
        
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

    public function set_back_enable($back_enable) {
        $this->back_enable = $back_enable;
    }

}
