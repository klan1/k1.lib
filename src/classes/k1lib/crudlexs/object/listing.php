<?php

namespace k1lib\crudlexs\object;

use k1lib\crudlexs\board\board_list_strings;
use k1lib\urlrewrite\url as url;

/**
 * 
 */
class listing extends base_with_data implements base_interface {

    /**
     * @var \k1lib\html\foundation\table_from_data
     */
    public $html_table;

    /**
     * @var bool
     */
    public $data_loaded = false;

    /**
     * @var int
     */
    protected $total_rows = 0;

    /**
     * @var int
     */
    protected $total_rows_filter = 0;

    /**
     * @var int
     */
    protected $total_pages = 0;

    /**
     * @var int
     */
    static public $characters_limit_on_cell = null;

    /**
     * @var int
     */
    static public $rows_per_page = 25;

    /**
     * @var int
     */
    static public $rows_limit_to_all = 200;

    /**
     *
     * @var array 
     */
    static public $rows_per_page_options = [5, 10, 25, 50, 100, 'all'];

    /**
     * @var int
     */
    protected $actual_page = 1;

    /**
     * @var int
     */
    protected $first_row_number = 1;

    /**
     * @var int
     */
    protected $last_row_number = 1;

    /**
     * @var string
     */
    protected $stat_msg;

    /**
     * @var int
     */
    protected $page_first = false;

    /**
     * @var int
     */
    protected $page_previous = false;

    /**
     * @var int
     */
    protected $page_next = false;

    /**
     * @var int
     */
    protected $page_last = false;

    /**
     * @var bool
     */
    protected $do_orderby_headers = TRUE;

    public function __construct($db_table, $row_keys_text) {
        parent::__construct($db_table, $row_keys_text);

        $this->skip_blanks_on_filters = TRUE;

        $this->stat_msg = listing_strings::$stats_default_message;
    }

    /**
     * 
     * @return \k1lib\html\div
     */
    public function do_html_object() {
        $table_alias = \k1lib\db\security\db_table_aliases::encode($this->db_table->get_db_table_name());

        $this->div_container->set_attrib("class", "k1lib-crudlexs-list-content scroll-x");
        if ($this->db_table_data) {
            if ($this->do_orderby_headers) {
                $this->do_orderby_headers();
            }
            /**
             * Create the HTML table from DATA lodaed 
             */
            $this->html_table = new \k1lib\html\foundation\table_from_data("k1lib-crudlexs-list responsive-card-table unstriped {$table_alias}");
            $this->html_table->append_to($this->div_container);
            $this->html_table->set_max_text_length_on_cell(self::$characters_limit_on_cell);
            $this->html_table->set_data($this->db_table_data_filtered);
        } else {
            $div_message = new \k1lib\html\p(board_list_strings::$no_table_data, "callout primary");
            $div_message->append_to($this->div_container);
        }
        return $this->div_container;
    }

    /**
     * @return \k1lib\html\foundation\table_from_data
     */
    public function get_html_table() {
        return $this->html_table;
    }

    public function apply_orderby_headers() {
        $table_alias = \k1lib\db\security\db_table_aliases::encode($this->db_table->get_db_table_name());

        $sort_by_name = $table_alias . '-sort-by';
        $sort_mode_name = $table_alias . '-sort-mode';

        if (isset($_GET[$sort_by_name]) && (!empty($_GET[$sort_by_name]))) {
            if (isset($_GET[$sort_mode_name]) && ($_GET[$sort_mode_name] == 'ASC')) {
                $sort_mode = 'ASC';
            } else {
                $sort_mode = 'DESC';
            }
            $field = $this->decrypt_field_name($_GET[$sort_by_name]);
            if (!empty($field)) {
                $this->db_table->set_order_by($field, $sort_mode);
            }
        }
    }

    public function do_orderby_headers() {
        $this->set_do_table_field_name_encrypt();

        $headers = &$this->db_table_data_filtered[0];
        foreach ($headers as $field => $label) {
            $field_encrypted = $this->encrypt_field_name($field);
            $table_alias = \k1lib\db\security\db_table_aliases::encode($this->db_table->get_db_table_name());

            $sort_by_name = $table_alias . '-sort-by';
            $sort_mode_name = $table_alias . '-sort-mode';

            $sort_mode = 'ASC';
            $class_sort_mode = '';
            $class_active = ' non-ordering';

            if (isset($_GET[$sort_by_name]) && ($_GET[$sort_by_name] == $field_encrypted)) {
                $class_active = ' ordering';
                if (isset($_GET[$sort_mode_name]) && ($_GET[$sort_mode_name] == 'ASC')) {
                    $sort_mode = 'DESC';
                    $class_sort_mode = 'fi-arrow-down';
                } else {
                    $class_sort_mode = 'fi-arrow-up';
                }
            }

            $sort_url = url::do_url($_SERVER['REQUEST_URI'], [$sort_by_name => $field_encrypted, $sort_mode_name => $sort_mode]);
            $a = new \k1lib\html\a($sort_url, " $label", NULL, $class_sort_mode . $class_active);
            $headers[$field] = $a;
        }
    }

    /**
     * 
     * @return \k1lib\html\div
     */
    public function do_row_stats($custom_msg = "") {
        $div_stats = new \k1lib\html\div("k1lib-crudlexs-list-stats clearfix");
        if (($this->db_table_data)) {
            if (empty($custom_msg)) {
                $stat_msg = $this->stat_msg;
            } else {
                $stat_msg = $custom_msg;
            }
            $stat_msg = str_replace("--totalrowsfilter--", $this->total_rows_filter, $stat_msg);
            $stat_msg = str_replace("--totalrows--", $this->total_rows, $stat_msg);
            $stat_msg = str_replace("--firstrownumber--", $this->first_row_number, $stat_msg);
            $stat_msg = str_replace("--lastrownumber--", $this->last_row_number, $stat_msg);

            $div_stats->set_value($stat_msg);
        }
        return $div_stats;
    }

    /**
     * 
     * @return \k1lib\html\div
     */
    public function do_pagination() {

        $div_pagination = new \k1lib\html\div("k1lib-crudlexs-list-pagination clearfix", $this->get_object_id() . "-pagination");
        $div_scroller = $div_pagination->append_div("float-left pagination-scroller");
        $div_page_chooser = $div_pagination->append_div("float-left pagination-rows");

        if (($this->db_table_data) && (self::$rows_per_page <= $this->total_rows)) {

            $page_get_var_name = $this->get_object_id() . "-page";
            $rows_get_var_name = $this->get_object_id() . "-rows";

            $this_url = APP_URL . \k1lib\urlrewrite\url::get_this_url() . "#" . $this->get_object_id() . "-pagination";
            if ($this->actual_page > 2) {
                $this->page_first = url::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_first = "#";
            }

            if ($this->actual_page > 1) {
                $this->page_previous = url::do_url($this_url, [$page_get_var_name => ($this->actual_page - 1), $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_previous = "#";
            }

            if ($this->actual_page < $this->total_pages) {
                $this->page_next = url::do_url($this_url, [$page_get_var_name => ($this->actual_page + 1), $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_next = "#";
            }
            if ($this->actual_page <= ($this->total_pages - 2)) {
                $this->page_last = url::do_url($this_url, [$page_get_var_name => $this->total_pages, $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_last = "#";
            }
            /**
             * HTML UL- LI construction
             */
            $ul = (new \k1lib\html\ul("pagination k1lib-crudlexs " . $this->get_object_id()));
            $ul->append_to($div_scroller);

            // First page LI
            $li = $ul->append_li();
//    function append_a($href = NULL, $label = NULL, $target = NULL, $alt = NULL, $class = NULL, $id = NULL) {
            $a = $li->append_a($this->page_first, "‹‹", "_self", "k1lib-crudlexs-first-page");
            if ($this->page_first == "#") {
                $a->set_attrib("class", "disabled");
            }
            // Previuos page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_previous, "‹", "_self", "k1lib-crudlexs-previous-page");
            if ($this->page_previous == "#") {
                $a->set_attrib("class", "disabled");
            }
            /**
             * Page GOTO selector
             */
            $page_selector = new \k1lib\html\select("goto_page", "k1lib-crudlexs-page-goto", $this->get_object_id() . "-page-goto");
            $page_selector->set_attrib("onChange", "use_select_option_to_url_go(this)");
            for ($i = 1; $i <= $this->total_pages; $i++) {
                $option_url = url::do_url($this_url, [$page_get_var_name => $i, $rows_get_var_name => self::$rows_per_page]);
                $option = $page_selector->append_option($option_url, $i, (($this->actual_page == $i) ? TRUE : FALSE));
            }
            $ul->append_li()->append_child($page_selector);
            // Next page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_next, "›", "_self", "k1lib-crudlexs-next-page");
            if ($this->page_next == "#") {
                $a->set_attrib("class", "disabled");
            }
            // Last page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_last, "››", "_self", "k1lib-crudlexs-last-page");
            if ($this->page_last == "#") {
                $a->set_attrib("class", "disabled");
            }
            /**
             * PAGE ROWS selector
             */
            $num_rows_selector = new \k1lib\html\select("goto_page", "k1lib-crudlexs-page-goto", $this->get_object_id() . "-page-rows-goto");
            $num_rows_selector->set_attrib("onChange", "use_select_option_to_url_go(this)");
            foreach (self::$rows_per_page_options as $num_rows) {
                if ($num_rows <= $this->total_rows) {
                    $option_url = url::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => $num_rows]);
                    $option = $num_rows_selector->append_option($option_url, $num_rows, ((self::$rows_per_page == $num_rows) ? TRUE : FALSE));
                } else {
                    break;
                }
            }
            if ($this->total_rows <= self::$rows_limit_to_all) {
                $option_url = url::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => $this->total_rows]);
                $option = $num_rows_selector->append_option($option_url, $this->total_rows, ((self::$rows_per_page == $this->total_rows) ? TRUE : FALSE));
            }
            $label = (new \k1lib\html\label("Show", $this->get_object_id() . "-page-rows-goto"));
            $label->set_attrib("style", "display:inline");
            $label->append_to($div_page_chooser);
            $num_rows_selector->append_to($div_page_chooser);
        }
        return $div_pagination;
    }

    function set_stat_msg($stat_msg) {
        $this->stat_msg = $stat_msg;
    }

    function get_actual_page() {
        return $this->actual_page;
    }

    function set_actual_page($actual_page) {
        $this->actual_page = $actual_page;
    }

    function get_rows_per_page() {
        return self::$rows_per_page;
    }

    function set_rows_per_page($rows_per_page) {
        self::$rows_per_page = $rows_per_page;
    }

    public function load_db_table_data($show_rule = null) {
        // FIRST of all, get TABLE total rows
        $this->total_rows = $this->db_table->get_total_rows();

        // THEN get from GET vars if there is a row per page value
        if (isset($_GET[$this->object_id . "-rows"])) {
            $possible_rows_to_set = $_GET[$this->object_id . "-rows"];
            if ($possible_rows_to_set <= $this->total_rows) {
                self::$rows_per_page = $possible_rows_to_set;
            } else {
                // DO NOTHING
            }
        }
        // now we can know the total pages 
        if (!is_numeric(self::$rows_per_page)) {
            self::$rows_per_page = 0;
            $this->total_pages = 1;
        } else {
            $this->total_pages = ceil($this->total_rows / self::$rows_per_page);
        }

        // The rows per page have to have a value, if is not set then we have to set it as the total rows
        if (self::$rows_per_page == 0) {
            self::$rows_per_page = $this->total_rows;
        }
        /**
         * Catch the GET value for pagination
         */
        if (isset($_GET[$this->object_id . "-page"])) {
            $possible_page_to_set = $_GET[$this->object_id . "-page"];
            if (($possible_page_to_set >= 1) && ($possible_page_to_set <= $this->total_pages)) {
                $this->actual_page = $possible_page_to_set;
            } else {
                $this->actual_page = 1;
            }
        }
        // SQL Limit time !
        if (self::$rows_per_page !== 0) {
            $offset = ($this->actual_page - 1) * self::$rows_per_page;
            $this->db_table->set_query_limit($offset, self::$rows_per_page);
        }
        if ($this->do_orderby_headers) {
            $this->apply_orderby_headers();
        }

        // GET DATA with a SQL Query
        if (parent::load_db_table_data($show_rule)) {
            $this->total_rows_filter = $this->db_table->get_total_data_rows();
            $this->first_row_number = $this->db_table->get_query_offset() + 1;
            $this->last_row_number = $this->db_table->get_query_offset() + $this->db_table->get_total_data_rows();
            $this->data_loaded = true;
            return TRUE;
        } else {
            $this->data_loaded = false;
            return FALSE;
        }
    }

    function get_page_first() {
        return $this->page_first;
    }

    function get_page_previous() {
        return $this->page_previous;
    }

    function get_page_next() {
        return $this->page_next;
    }

    function get_page_last() {
        return $this->page_last;
    }

    public function get_do_orderby_headers() {
        return $this->do_orderby_headers;
    }

    public function set_do_orderby_headers($do_orderby_headers) {
        $this->do_orderby_headers = $do_orderby_headers;
    }
}
