<?php

namespace k1lib\crudlexs;

class search_helper extends creating {

    /**
     *
     * @var Array 
     */
    protected $db_table_data = FALSE;

    /**
     *
     * @var Boolean 
     */
    protected $db_table_data_keys = FALSE;

// FILTERS
    public function __construct(\k1lib\crudlexs\class_db_table $db_table) {
        parent::__construct($db_table, FALSE);

        $last_show_rule = $this->db_table->get_db_table_show_rule();
        $this->db_table->set_db_table_show_rule("show-search");
        $this->load_db_table_data(TRUE);
        $this->db_table->set_db_table_show_rule($last_show_rule);
    }

    public function do_code() {
        if ($this->catch_post_data()) {
            $this->put_post_data_on_table_data();
            $this->db_table->set_query_filter($this->post_incoming_array, FALSE);
        }
        $this->apply_label_filter();


        $div_callout = new \k1lib\html\div_tag("reveal","search-modal");
        $div_callout->set_attrib("data-reveal", TRUE);
        $div_callout->set_value(parent::do_code());
        return $div_callout->generate_tag();
    }

}
