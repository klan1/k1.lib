<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;

interface board_interface {

    public function start_board();

    public function exec_board();
}

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
    protected $is_enabled = FALSE;

    /**
     *
     * @var boolean
     */
    protected $skip_form_action = FALSE;

    /**
     *
     * @var string
     */
    protected $user_levels_allowed = null;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        $this->controller_object = $controller_object;
        $this->board_content_div = new \k1lib\html\div_tag("board-content");

        $this->user_levels_allowed = $user_levels_allowed;

        if (\k1lib\session\session_plain::is_enabled()) {
            if (!$this->check_user_level_access()) {
                $this->is_enabled = false;
            } else {
                $this->is_enabled = true;
            }
        }

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
        if ($this->is_enabled) {
            $this->is_enabled = $is_enabled;
        }
    }

    public function set_board_name($board_name) {
        if (!empty($board_name)) {
            temply::set_place_value($this->controller_object->get_template_place_name_html_title(), " - {$board_name}");
            temply::set_place_value($this->controller_object->get_template_place_name_board_name(), $board_name);
        }
    }

    function set_user_levels_allowed(array $user_levels_allowed_array) {
        $this->user_levels_allowed = $user_levels_allowed_array;
    }

    function add_user_level_allowed($user_level_allowed) {
        if (!empty($user_level_allowed) && is_string($user_level_allowed)) {
            $this->user_levels_allowed[] = $user_level_allowed;
        }
    }

    function check_user_level_access() {
        if (empty($this->user_levels_allowed)) {
            return TRUE;
        } else {
            if (empty(array_key_exists(\k1lib\session\session_plain::get_user_level(), array_flip($this->user_levels_allowed)))) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

}

class board_base_strings {

    static $alert_board = "Alert";
    static $error_board = "Error message";
    static $error_board_disabled = "This board is disabled or you are not allowed to use it";
    static $error_mysql = "DB error";
    static $error_mysql_table_not_opened = "Can not open the table.";
    static $error_mysql_table_no_data = "Empty query";
    static $error_url_keys_no_auth = "Keys are not valid, so, you can't continue";
    static $error_url_keys_no_keys_text = "You can't use this board without the right url key text";

}