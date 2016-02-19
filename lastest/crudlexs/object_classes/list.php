<?php

namespace k1lib\crudlexs;

use k1lib\urlrewrite\url_manager as url_manager;

/**
 * 
 */
class listing extends crudlexs_base_with_data implements crudlexs_base_interface {

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
    static public $rows_per_page = 25;

    /**
     * @var int
     */
    static public $rows_limit_to_all = 200;

    /**
     *
     * @var array 
     */
    static public $rows_per_page_options = [5, 10, 25, 50, 100, "all"];

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
     * @var int
     */
    protected $stat_msg = "Showing %s of %s (%s to %s)";

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

    public function __construct($db_table, $row_keys_text) {
        parent::__construct($db_table, $row_keys_text);

        $this->set_object_id(get_class($this));
        $this->set_css_class(get_class($this));

        $this->skip_blanks_on_filters = TRUE;
    }

    /**
     * 
     * @return \k1lib\html\div_tag
     */
    public function do_html_object() {
        $this->div_container->set_attrib("class", "k1-crudlexs-table");
        $this->div_container->set_attrib("id", $this->object_id);
        if ($this->db_table_data) {
            $html_table = \k1lib\html\table_from_array($this->db_table_data_filtered, TRUE, "scroll");
            $this->div_container->append_child($html_table);
        } else {
            $div_message = new \k1lib\html\p_tag("No data to show", "callout primary");
            $div_message->append_to($this->div_container);
        }
        return $this->div_container;
    }

    /**
     * 
     * @return \k1lib\html\div_tag
     */
    public function do_row_stats() {
        $div_stats = new \k1lib\html\div_tag("k1-crudlexs-table-stats");
        if (($this->db_table_data)) {

            $div_stats->set_value(
                    sprintf(
                            $this->stat_msg
                            , $this->total_rows_filter
                            , $this->total_rows
                            , $this->first_row_number
                            , $this->last_row_number
                    )
            );
        }
        return $div_stats;
    }

    /**
     * 
     * @return \k1lib\html\div_tag
     */
    public function do_pagination() {

        $div_pagination = new \k1lib\html\div_tag("k1-crudlexs-table-pagination", $this->get_object_id() . "-pagination");
        $div_scroller = $div_pagination->append_div("float-left pagination-scroller");
        $div_page_chooser = $div_pagination->append_div("float-left pagination-rows");

        if (($this->db_table_data) && (self::$rows_per_page <= $this->total_rows)) {

            $page_get_var_name = $this->get_object_id() . "-page";
            $rows_get_var_name = $this->get_object_id() . "-rows";

            $this_url = \k1lib\urlrewrite\url_manager::get_this_url(APP_URL) . "#" . $this->get_object_id() . "-pagination";
            if ($this->actual_page > 2) {
                $this->page_first = url_manager::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_first = "#";
            }

            if ($this->actual_page > 1) {
                $this->page_previous = url_manager::do_url($this_url, [$page_get_var_name => ($this->actual_page - 1), $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_previous = "#";
            }

            if ($this->actual_page < $this->total_pages) {
                $this->page_next = url_manager::do_url($this_url, [$page_get_var_name => ($this->actual_page + 1), $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_next = "#";
            }
            if ($this->actual_page <= ($this->total_pages - 2)) {
                $this->page_last = url_manager::do_url($this_url, [$page_get_var_name => $this->total_pages, $rows_get_var_name => self::$rows_per_page]);
            } else {
                $this->page_last = "#";
            }
            /**
             * HTML UL- LI construction
             */
            $ul = (new \k1lib\html\ul_tag("pagination k1lib-crudlexs " . $this->get_object_id()));
            $ul->append_to($div_scroller);

            // First page LI
            $li = $ul->append_li();
            $a = $li->append_a($this->page_first, "‹‹", "_self", "First page", "k1lib-crudlexs-first-page");
            if ($this->page_first == "#") {
                $a->set_attrib("class", "disabled");
            }
            // Previuos page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_previous, "‹", "_self", "Previous page", "k1lib-crudlexs-previous-page");
            if ($this->page_previous == "#") {
                $a->set_attrib("class", "disabled");
            }
            /**
             * Page GOTO selector
             */
            $page_selector = new \k1lib\html\select_tag("goto_page", "k1-crudlexs-page-goto", $this->get_object_id() . "-page-goto");
            $page_selector->set_attrib("onChange", "use_select_option_to_url_go(this)");
            for ($i = 1; $i <= $this->total_pages; $i++) {
                $option_url = url_manager::do_url($this_url, [$page_get_var_name => $i, $rows_get_var_name => self::$rows_per_page]);
                $option = $page_selector->append_option($option_url, $i, (($this->actual_page == $i) ? TRUE : FALSE));
            }
            $ul->append_li()->append_child($page_selector);
            // Next page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_next, "›", "_self", "Next page", "k1lib-crudlexs-next-page");
            if ($this->page_next == "#") {
                $a->set_attrib("class", "disabled");
            }
            // Last page LI
            $li = $ul->append_li("");
            $a = $li->append_a($this->page_last, "››", "_self", "Last page", "k1lib-crudlexs-last-page");
            if ($this->page_last == "#") {
                $a->set_attrib("class", "disabled");
            }
            /**
             * PAGE ROWS selector
             */
            $num_rows_selector = new \k1lib\html\select_tag("goto_page", "k1-crudlexs-page-goto", $this->get_object_id() . "-page-rows-goto");
            $num_rows_selector->set_attrib("onChange", "use_select_option_to_url_go(this)");
            foreach (self::$rows_per_page_options as $num_rows) {
                if ($num_rows <= $this->total_rows) {
                    $option_url = url_manager::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => $num_rows]);
                    $option = $num_rows_selector->append_option($option_url, $num_rows, ((self::$rows_per_page == $num_rows) ? TRUE : FALSE));
                } else {
                    break;
                }
            }
            if ($this->total_rows <= self::$rows_limit_to_all) {
                $option_url = url_manager::do_url($this_url, [$page_get_var_name => 1, $rows_get_var_name => $this->total_rows]);
                $option = $num_rows_selector->append_option($option_url, $this->total_rows, ((self::$rows_per_page == $this->total_rows) ? TRUE : FALSE));
            }
            $label = (new \k1lib\html\label_tag("Show", $this->get_object_id() . "-page-rows-goto"));
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
        $this->total_pages = ceil($this->total_rows / self::$rows_per_page);

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
        // SQL Query
        if (parent::load_db_table_data($show_rule)) {
            $this->total_rows_filter = $this->db_table->get_total_data_rows();
            $this->first_row_number = $this->db_table->get_query_offset() + 1;
            $this->last_row_number = $this->db_table->get_query_offset() + $this->db_table->get_total_data_rows();


            return TRUE;
        } else {
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

}
