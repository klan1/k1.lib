<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage crudlexs\object
 * Search helper functionality for filtering and querying database records with URL-based parameters.
 */

namespace k1lib\crudlexs\object;

use k1lib\crudlexs\db_table;
use k1lib\html\div;
use k1lib\html\input;
use function k1lib\common\unserialize_var;

/**
 * Search helper object for filtering database queries.
 * Extends creating to provide search functionality with URL parameter serialization.
 *
 * @package k1lib\crudlexs\object
 */
class search_helper extends creating {

    /**
     * Database table data from search results.
     * @var array|false
     */
    public $db_table_data = FALSE;

    /**
     * Database table data keys.
     * @var bool
     */
    protected $db_table_data_keys = FALSE;

    /**
     * Caller object ID for return URL.
     * @var string|null
     */
    protected $caller_objetc_id = null;

    /**
     * Enable POST data catching for search.
     * @var bool
     */
    protected $search_catch_post_enable = TRUE;

    /**
     * Caller URL for search return.
     * @var string|null
     */
    protected $caller_url = null;

    /**
     * Creates a search helper for the specified database table.
     *
     * @param db_table $db_table The database table object
     */
    public function __construct(db_table $db_table) {
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

    /**
     * Generates the HTML representation of the search helper.
     * Combines parent HTML generation with form action modification for search.
     *
     * @return div The search form container
     */
    public function do_html_object() {
        if ($this->search_catch_post_enable && $this->catch_post_data()) {
            $this->put_post_data_on_table_data();
            $this->db_table->set_query_filter($this->post_incoming_array, FALSE);
        }
        $this->apply_label_filter();

        $this->insert_inputs_on_data_row();

        $div_container = new div('container');

        $search_html = parent::do_html_object();
        $search_html->get_elements_by_tag("form")[0]->set_attrib("action", unserialize_var($this->caller_url . '-url'));
        $search_html->get_elements_by_tag("form")[0]->set_attrib("target", "_parent");
        $search_html->get_elements_by_tag("form")[0]->append_child(new input("hidden", "from-search", urlencode($this->caller_url)));

        $search_html->append_to($div_container);
        return $div_container;
    }

    /**
     * Catches and merges POST data from search with existing form data.
     *
     * @return bool TRUE if post data was successfully caught, FALSE otherwise
     */
    function catch_post_data() {
        $search_post = unserialize_var(urlencode($this->caller_url));
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

    /**
     * Sets whether to enable catching POST data from search.
     *
     * @param bool $search_catch_post_enable TRUE to enable, FALSE to disable
     */
    public function set_search_catch_post_enable($search_catch_post_enable) {
        $this->search_catch_post_enable = $search_catch_post_enable;
    }
}
