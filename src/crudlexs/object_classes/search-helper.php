<?php

namespace k1lib\crudlexs;

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

// FILTERS
    public function __construct(\k1lib\crudlexs\class_db_table $db_table, $caller_object_id) {
        parent::__construct($db_table, FALSE);

        $this->caller_objetc_id = $caller_object_id;

        $this->set_object_id(get_class($this));
        $this->set_css_class(get_class($this));

        creating_strings::$button_submit = search_helper_strings::$button_submit;
        creating_strings::$button_cancel = search_helper_strings::$button_cancel;

        $this->show_cancel_button = FALSE;

        $this->set_do_table_field_name_encrypt(TRUE);


        $last_show_rule = $this->db_table->get_db_table_show_rule();
        $this->db_table->set_db_table_show_rule("show-search");
        $this->load_db_table_data(TRUE);
        $this->db_table->set_db_table_show_rule($last_show_rule);
    }

    public function do_html_object() {
        if ($this->search_catch_post_enable && $this->catch_post_data()) {
            $this->put_post_data_on_table_data();
            $this->db_table->set_query_filter($this->post_incoming_array, FALSE);
        }
        $this->apply_label_filter();

        input_helper::$do_fk_search_tool = FALSE;
        $this->insert_inputs_on_data_row();

        $div_callout = new \k1lib\html\div("reveal", "search-modal");
        $div_callout->set_attrib("data-reveal", TRUE);
        $div_callout->append_child(parent::do_html_object());
        return $div_callout;
    }

    function catch_post_data() {
        $serialize_name = $this->caller_objetc_id . "-post";
        $saved_post_data = \k1lib\common\unserialize_var($serialize_name);

        if (parent::catch_post_data()) {
            \k1lib\common\serialize_var($this->post_incoming_array, $serialize_name);
            if (key_exists($this->caller_objetc_id . "-page", $_GET)) {
                $_GET[$this->caller_objetc_id . "-page"] = 1;
            }
            return TRUE;
        } else {
            if (!empty($saved_post_data)) {
                if (key_exists($this->caller_objetc_id . "-page", $_GET)) {
                    $this->post_incoming_array = $saved_post_data;
                    return TRUE;
                } else {
                    \k1lib\common\unset_serialize_var($serialize_name);
                }
            }
            return FALSE;
        }
    }

    public function set_search_catch_post_enable($search_catch_post_enable) {
        $this->search_catch_post_enable = $search_catch_post_enable;
    }

}
