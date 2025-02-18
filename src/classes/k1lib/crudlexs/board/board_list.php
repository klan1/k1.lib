<?php

namespace k1lib\crudlexs\board;

use k1lib\common_strings;
use k1lib\crudlexs\controller\base as controller_base;
use k1lib\crudlexs\object\base;
use k1lib\crudlexs\object\listing;
use k1lib\forms\file_uploads;
use k1lib\html\bootstrap\grid_row;
use k1lib\html\bootstrap\modal;
use k1lib\html\div;
use k1lib\html\DOM as DOM;
use k1lib\html\i;
use k1lib\html\iframe;
use k1lib\html\notifications\on_DOM as DOM_notification;
use k1lib\urlrewrite\url as url;
use const k1app\K1APP_URL;
use function k1lib\common\serialize_var;
use function k1lib\common\unserialize_var;
use function k1lib\common\unset_serialize_var;
use function k1lib\forms\check_all_incomming_vars;
use function k1lib\html\get_link_button;
use function k1lib\html\html_header_go;
use function k1lib\urlrewrite\get_back_url;

class board_list extends board_base implements board_interface {

    const SHOW_BEFORE_TABLE = 1;
    const SHOW_AFTER_TABLE = 2;
    const SHOW_BEFORE_AND_AFTER_TABLE = 3;

    protected $search_enable = TRUE;
    protected $search_catch_post_enable = TRUE;
    protected $create_enable = TRUE;
    protected $export_enable = TRUE;
    protected $pagination_enable = TRUE;
    protected $stats_enable = TRUE;
    protected $where_to_show_stats = self::SHOW_AFTER_TABLE;
    protected $back_enable = TRUE;
    protected $fields_to_change;

    /**
     * Smarty template PATH to use with each data row
     * @var string
     */
    protected string $data_row_template;

    /**
     *
     * @var \k1lib\crudlexs\listing
     */
    public listing $list_object;

    public function __construct(controller_base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->show_rule_to_apply = "show-list";
            $this->list_object = new listing($this->controller_object->db_table, FALSE);
            $this->list_object->set_do_table_field_name_encrypt(TRUE);
            $this->set_current_object($this->list_object);
        }
        $this->fields_to_change = base::USE_KEY_FIELDS;
    }

    /**
     * @return div|boolean
     */
    public function start_board() {
        /**
         * Individual TPL for data rows
         * Listing object will handle it
         */
        if (!empty($this->data_row_template)) {
            $this->list_object->set_data_row_template($this->data_row_template);
        }

        /**
         * URL serialization for tools use
         */
        $this_url = K1APP_URL . url::get_this_url();
        serialize_var($this_url, url::get_this_controller_id() . '-url');

        if (!parent::start_board()) {
            return FALSE;
        }

        if ($this->list_object->get_state()) {

            /**
             * BACK
             */
            $back_url = get_back_url();
            if ($this->back_enable && (isset($_GET['back-url']))) {
                $back_link = get_link_button($back_url, board_read_strings::$button_back, 'btn-sm');
                $back_link->append_to($this->button_div_tag);
            }
            /**
             * NEW BUTTON
             */
            $related_url_keys_text = url::get_url_level_value_by_name("related_url_keys_text");
            if (empty($related_url_keys_text)) {
                $related_url_keys_text = "";
                $new_link = get_link_button(
                        url::do_url(
                                "../{$this->controller_object->get_board_create_url_name()}/" . urlencode($related_url_keys_text),
                                ['cancel-url' => K1APP_URL . url::get_this_url()]
                        )
                        , board_list_strings::$button_new, 'btn-sm');
            } else {
                $related_url_keys_text .= "/";
                $new_link = get_link_button(url::do_url("../../{$this->controller_object->get_board_create_url_name()}/" . urlencode($related_url_keys_text)), board_list_strings::$button_new, 'btn-sm');
            }
            if ($this->create_enable) {
//                $new_link = \k1lib\html\get_link_button(url::do_url("../{$this->controller_object->get_board_create_url_name()}/" . $related_url_keys_text), board_list_strings::$button_new);
//                $new_link = \k1lib\html\get_link_button("../{$this->controller_object->get_board_create_url_name()}/?back-url={$this_url}", board_list_strings::$button_new);
                $new_link->append_to($this->button_div_tag);
            }

            /**
             * Search
             */
            if ($this->search_enable) {
                $controller_id = url::get_this_controller_id();

                if (isset($_GET['clear-search']) && $_GET['clear-search'] == 1) {
                    unset_serialize_var($controller_id);
                    unset($_GET['clear-search']);
                    $next_url = url::do_url(K1APP_URL . url::get_this_url(), [], TRUE, array_keys($_GET));
                    html_header_go($next_url);
                    exit;
                }
                /**
                 * SERACH BUTTON AND MODAL
                 */
                $search_iframe = new iframe(url::do_url(
                                $this->controller_object->get_controller_root_dir() . "search/?just-controller=1&caller-id=" . $controller_id)
                        , 'utility-iframe mw-100', "search-iframe"
                );
                $search_iframe->set_attrib('width', '100%');
                $search_iframe->set_attrib('height', '1200px');

                $modal = new modal(board_list_strings::$button_search, $search_iframe, common_strings::$button_cancel, NULL);

                DOM::html_document()->body()->append_child_tail($modal);

                $search_buttom = get_link_button('#', board_list_strings::$button_search, 'btn-sm', 'search-button');
                $search_buttom->set_attrib('data-bs-toggle', 'modal');
                $search_buttom->set_attrib('data-bs-target', '#staticBackdrop"');
                $search_buttom->append_to($this->button_div_tag);

                /**
                 * LOAD SEARCH DATA FROM SESSION
                 */
                $search_session_data = unserialize_var($controller_id);
                if (!empty($search_session_data) && empty($_POST)) {
                    $_POST = $search_session_data;
                }

                if (isset($_POST) && isset($_POST['from-search']) && (urldecode($_POST['from-search']) == $controller_id)) {
//                    if ($this->)
                    /**
                     * decrypt post field names
                     */
                    $incomming_search_data = check_all_incomming_vars($_POST);
                    if ($this->list_object->get_do_table_field_name_encrypt()) {
                        $search_data = $this->list_object->decrypt_field_names($incomming_search_data);
                    } else {
                        $search_data = $incomming_search_data;
                    }
                    $this->controller_object->db_table->set_query_filter($search_data);
                    $search_post = serialize_var($_POST, $controller_id);
                    /**
                     * Clear search
                     */
                    $clear_search_buttom = get_link_button(
                            url::do_url($_SERVER['REQUEST_URI'], ['clear-search' => 1]),
                            board_list_strings::$button_search_cancel, 'btn-warning btn-sm', 'search-button');

                    $search_buttom->set_value(( new i(null, 'bi bi-search')) . ' ' . board_list_strings::$button_search_modify);
                    $clear_search_buttom->append_to($this->button_div_tag);
                } else {
                    $search_post = unserialize_var($controller_id);
                }
            }

            $this->data_loaded = $this->list_object->load_db_table_data($this->show_rule_to_apply);
            return $this->board_content_div;
        } else {
            DOM_notification::queue_mesasage(board_base_strings::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_strings::$error_mysql);
            $this->list_object->make_invalid();
            $this->is_enabled = FALSE;

            return FALSE;
        }
    }

    /**
     * @return div|boolean
     */
    public function exec_board() {
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
            if (file_uploads::is_enabled()) {
                $this->list_object->apply_file_uploads_filter();
            }
            // IF NOT previous link applied this will try to apply ONLY on keys if are present on show-list filter
            if (!$this->list_object->get_link_on_field_filter_applied()) {
                $get_vars = [
                    "auth-code" => "--authcode--",
                    "back-url" => urlencode($_SERVER['REQUEST_URI'])
                ];
                $this->list_object->apply_link_on_field_filter(
                        url::do_url("../{$this->controller_object->get_board_read_url_name()}/--rowkeys--/", $get_vars),
                        $this->fields_to_change
                );
            }
            // Show stats BEFORE
            if (($this->stats_enable) && (($this->where_to_show_stats == self::SHOW_BEFORE_TABLE) || ($this->where_to_show_stats == self::SHOW_BEFORE_AND_AFTER_TABLE))) {
                $this->list_object->do_pagination()->append_to($this->board_content_div);
                $this->list_object->do_row_stats()->append_to($this->board_content_div);
            }
            /**
             * HTML OBJECT
             */
            $list_content_div = $this->list_object->do_html_object();
            $list_content_div->append_to($this->board_content_div);
            // Show stats AFTER
            if (($this->stats_enable) && (($this->where_to_show_stats == self::SHOW_AFTER_TABLE) || ($this->where_to_show_stats == self::SHOW_BEFORE_AND_AFTER_TABLE))) {
                $grid_row = new grid_row(3, 1);

                $grid_row->set_class('mt-3', TRUE);
                $grid_row->cell(1)->small(4);
                $grid_row->cell(2)->small(5);
                $grid_row->cell(3)->small(3);

                $grid_row->cell(1)->append_child($this->list_object->do_row_stats());
                $grid_row->cell(2)->append_child($this->list_object->do_pagination());
                $grid_row->cell(3)->append_child($this->list_object->do_show_rows_per_page());
                $this->board_content_div->append_child($grid_row);
            }

            return $this->board_content_div;
        } else {
            $this->list_object->do_html_object()->append_to($this->board_content_div);
            return $this->board_content_div;
        }
    }

    public function finish_board() {
        
    }

    public function set_search_catch_post_enable($search_catch_post_enable) {
        $this->search_catch_post_enable = $search_catch_post_enable;
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

    public function set_fields_to_change($fields_to_change): void {
        $this->fields_to_change = $fields_to_change;
    }

    /**
     * Set the Smarty template PATH to use with each data row
     * @param string $data_row_template
     * @return void
     */
    public function set_data_row_template(string $data_row_template): void {
        $this->data_row_template = $data_row_template;
    }
}
