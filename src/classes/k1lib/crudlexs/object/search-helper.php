<?php

namespace k1lib\crudlexs\object;

class search_helper extends creating {

    /**
     *
     * @var Array 
     */
    public $db_table_data = FALSE;

    /**
     *
     * @var Boolean 
     */
    protected $db_table_data_keys = FALSE;

    /**
     * @var string
     */
    protected $caller_objetc_id = null;
    protected $search_catch_post_enable = TRUE;
    protected $caller_url = null;

// FILTERS
    public function __construct(\k1lib\crudlexs\class_db_table $db_table) {
        parent::__construct($db_table, FALSE);
        if (isset($_GET['caller-id'])) {
            $this->caller_url = urldecode($_GET['caller-id']);
        } else {
            d("No caller ID");
        }
        creating_strings::$button_submit = search_helper_strings::$button_submit;
        creating_strings::$button_cancel = search_helper_strings::$button_cancel;

        $this->show_cancel_button = FALSE;

        $this->set_do_table_field_name_encrypt(TRUE);


        $this->db_table->set_db_table_show_rule("show-search");
    }

    public function do_html_object() {
        if ($this->search_catch_post_enable && $this->catch_post_data()) {
            $this->put_post_data_on_table_data();
            $this->db_table->set_query_filter($this->post_incoming_array, FALSE);
        }
        $this->apply_label_filter();

        $this->insert_inputs_on_data_row();

        $search_html = parent::do_html_object();
        $search_html->get_elements_by_tag("form")[0]->set_attrib("action", $_SERVER['HTTP_REFERER']);
        $search_html->get_elements_by_tag("form")[0]->set_attrib("target", "_parent");
        $search_html->get_elements_by_tag("form")[0]->append_child(new \k1lib\html\input("hidden", "from-search", urlencode($this->caller_url)));
        return $search_html;
    }

    function catch_post_data() {
        $search_post = \k1lib\common\unserialize_var(urlencode($this->caller_url));
        if (empty($search_post)) {
            $search_post = [];
        }
        $_POST = array_merge($search_post, $_POST);
        if (parent::catch_post_data()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function set_search_catch_post_enable($search_catch_post_enable) {
        $this->search_catch_post_enable = $search_catch_post_enable;
    }

}
