<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;

class board_base {

    /**
     * DB table main object
     * @var \k1lib\crudlexs\controller_base 
     */
    protected $controller_object;

    /**
     *
     * @var \k1lib\html\div_tag;
     */
    public $board_content_div;

    /**
     *
     * @var boolean
     */
    protected $data_loaded = FALSE;

    /**
     *
     * @var boolean
     */
    protected $is_enabled = TRUE;

    /**
     *
     * @var boolean
     */
    protected $skip_form_action = FALSE;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object) {
        $this->controller_object = $controller_object;
        $this->board_content_div = new \k1lib\html\div_tag("board-content");

        /**
         * Search util hack
         */
        $post_data_to_use = \k1lib\common\unserialize_var("post-data-to-use");
        if (!empty($post_data_to_use)) {
            $_POST = $post_data_to_use;
            $this->skip_form_action = TRUE;
            \k1lib\common\unset_serialize_var("post-data-to-use");
        }
    }

    public function start_board() {
        
    }

    public function exec_board() {
        
    }

    function get_is_enabled() {
        return $this->is_enabled;
    }

    function set_is_enabled($is_enabled) {
        $this->is_enabled = $is_enabled;
    }

    public function set_board_name($board_name) {
        if (!empty($board_name)) {
            temply::set_place_value($this->controller_object->get_template_place_name_html_title(), " - {$board_name}");
            temply::set_place_value($this->controller_object->get_template_place_name_board_name(), $board_name);
        }
    }

}

class board_base_strings {

    static $alert_board = "Alert";
    static $error_board = "Error message";
    static $error_board_disabled = "This board is disabled";
    static $error_mysql = "DB error";
    static $error_mysql_table_not_opened = "Can not open the table.";
    static $error_mysql_table_no_data = "Empty query";
    static $error_url_keys_no_auth = "Keys are not valid, so, you can't continue.";
    static $error_url_keys_no_keys_text = "You can't use this board without the right url key text.";

}
