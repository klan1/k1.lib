<?php

namespace k1lib\crudlexs;

/**
 * 
 */
class listing extends crudlexs_base_with_data implements crudlexs_base_interface {

    protected $total_rows = 0;
    protected $total_rows_filter = 0;
    protected $rows_per_page = 20;
    protected $actual_page = 1;
    protected $first_row_number = 1;
    protected $last_row_number = 1;
    protected $stat_msg = "Showing %s of %s (%s to %s)";

    public function __construct($db_table, $row_keys_text) {
        parent::__construct($db_table, $row_keys_text);
        $this->div_container->set_attrib("class", "k1-crudlexs-table");
    }

    public function do_html_object() {
        if ($this->db_table_data) {
            $html_table = \k1lib\html\table_from_array($this->db_table_data_filtered, TRUE, "scroll");
            $this->div_container->append_child($html_table);
            return $this->div_container;
        } else {
            return FALSE;
        }
    }

    public function do_row_stats() {
        $div_stats = new \k1lib\html\div_tag("k1-crudlexs-table-stats");
        $div_stats->set_value(
                sprintf(
                        $this->stat_msg
                        , $this->total_rows_filter
                        , $this->total_rows
                        , $this->first_row_number
                        , $this->last_row_number
                )
        );
        return $div_stats;
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
        return $this->rows_per_page;
    }

    function set_rows_per_page($rows_per_page) {
        $this->rows_per_page = $rows_per_page;
    }

    public function load_db_table_data($show_rule = null) {
        if ($this->rows_per_page !== 0) {
            $offset = ($this->actual_page - 1) * $this->rows_per_page;
            $this->db_table->set_query_limit($offset, $this->rows_per_page);
        }
        if (parent::load_db_table_data($show_rule)) {
            $this->total_rows = $this->db_table->get_total_rows();
            $this->total_rows_filter = $this->db_table->get_total_data_rows();
            $this->first_row_number = $this->db_table->get_query_offset() + 1;
            $this->last_row_number = $this->db_table->get_query_offset() + $this->db_table->get_total_data_rows();
            return TRUE;
        } else {
            return FALSE;
        }
    }

}
