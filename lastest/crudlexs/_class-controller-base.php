<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\urlrewrite\url_manager as url_manager;

interface controller_interface {

    public function start_board();

    public function exec_board();
}

interface board_interface {

    public function set_board_name($board_name);

    public function start_board();

    public function exec_board();
}

class controller_base {

    /**
     * DB table main object
     * @var \k1lib\crudlexs\class_db_table 
     */
    public $db_table;

    /**
     * Controller name for add on <html><title> and controller name tag
     * @var string 
     */
    protected $controller_name;

    /**
     * URL value after the domain
     * @var string
     */
    protected $controller_root_dir;

    /**
     * THIS controller URL value
     * @var string
     */
    protected $controller_url_value;

    /**
     * URL value for the board asked to show
     * @var string
     */
    protected $controller_board_url_value;

    /**
     *
     * @var boolean
     */
    protected $board_started = FALSE;

    /**
     *
     * @var \k1lib\crudlexs\board_list;
     */
    public $board_list_object;
    protected $board_list_url_name = "list";

    /**
     *
     * @var \k1lib\crudlexs\board_create
     */
    public $board_create_object;
    protected $board_create_url_name = "create";

    /**
     *
     * @var \k1lib\crudlexs\board_read
     */
    public $board_read_object;
    protected $board_read_url_name = "read";

    /**
     *
     * @var \k1lib\crudlexs\board_update
     */
    public $board_update_object;
    protected $board_update_url_name = "update";

    /**
     *
     * @var \k1lib\crudlexs\board_delete
     */
    public $board_delete_object;
    protected $board_delete_url_name = "delete";

    /**
     * 
     * URL MANAGEMENT VALUES
     * 
     */
    /**
     *
     * Board names for html title and controller name tag
     * 
     */

    /**
     * Template name set for HTML-TITLE on the header.php
     * @var type 
     */
    protected $template_place_name_html_title = "html-title";

    /**
     * Template name set for CONTROLER-NAME on the header.php
     * @var type 
     */
    protected $template_place_name_controller_name = "controller-name";
    protected $board_list_name = "";
    protected $board_create_name = "";
    protected $board_read_name = "";
    protected $board_update_name = "";
    protected $board_delete_name = "";
    protected $url_redirect_after_delete = "../../list/";

    /**
     * One line config for more time to party and less coding :)
     * @param string $app_base_dir Use here \k1app\APP_BASE_URL
     * @param \PDO $db DB app object
     * @param string $db_table_name Table to open from the DB
     * @param string $controller_name Name for html title and controller name tag
     * @param string $template_place_name_html_title 
     * @param string $template_place_name_controller_name 
     */
    public function __construct($app_base_dir, \PDO $db, $db_table_name, $controller_name) {
        /**
         * URL Management
         */
        $this->controller_root_dir = $app_base_dir . url_manager::make_url_from_rewrite('this');
        $this->controller_url_value = url_manager::get_url_level_value('this');
        $this->controller_board_url_value = $this->set_and_get_next_url_value();
        /**
         * DB Table 
         */
        $this->db_table = new \k1lib\crudlexs\class_db_table($db, $db_table_name);

        /**
         * Controller name for add on <html><title> and controller name tag
         */
        $this->controller_name = $controller_name;
        temply::set_place_value($this->template_place_name_html_title, " | $controller_name");
        temply::set_place_value($this->template_place_name_controller_name, $controller_name);
    }

    public function set_and_get_next_url_value() {
        $next_url_level = url_manager::get_url_level_count();
        $controller_url_value = "controller_url_{$next_url_level}";
        return url_manager::set_url_rewrite_var($next_url_level, $controller_url_value, FALSE);
    }

    public function start_board($specific_board_to_start = NULL) {
        if (empty($specific_board_to_start)) {
            $specific_board_to_start = $this->controller_board_url_value;
        }
        $this->board_started = TRUE;
        switch ($specific_board_to_start) {
            case $this->board_create_url_name:
                $this->board_create_object = new board_create($this);
                return $this->board_create_object->start_board();

            case $this->board_read_url_name:
                $this->board_read_object = new board_read($this);
                return $this->board_read_object->start_board();

            case $this->board_update_url_name:
                $this->board_update_object = new board_update($this);
                return $this->board_update_object->start_board();

            case $this->board_delete_url_name:
                $this->board_delete_object = new board_delete($this);
                return $this->board_delete_object->start_board();

            case $this->board_list_url_name:
                $this->board_list_object = new board_list($this);
                return $this->board_list_object->start_board();

            default:
                $this->board_started = FALSE;
                \k1lib\html\html_header_go($this->controller_root_dir . $this->get_board_list_url_name() . "/");
                return FALSE;
        }
    }

    public function exec_board($do_echo = TRUE, $specific_board_to_exec = NULL) {

        if ($this->board_started) {
            if (empty($specific_board_to_exec)) {
                $specific_board_to_exec = $this->controller_board_url_value;
            }

            switch ($specific_board_to_exec) {
                case $this->board_create_url_name:
                    return $this->board_create_object->exec_board($do_echo);

                case $this->board_read_url_name:
                    return $this->board_read_object->exec_board($do_echo);

                case $this->board_update_url_name:
                    return $this->board_update_object->exec_board($do_echo);

                case $this->board_delete_url_name:
                    return $this->board_delete_object->exec_board($do_echo);

                case $this->board_list_url_name:
                    return $this->board_list_object->exec_board($do_echo);

                default:
                    break;
            }
        } else {
            trigger_error("The board hasn't started yet.", E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * SIMPLE SETTERS AND GETTERS
     */
    function get_url_redirect_after_delete() {
        return $this->url_redirect_after_delete;
    }

    function set_url_redirect_after_delete($url_redirect_after_delete) {
        $this->url_redirect_after_delete = $url_redirect_after_delete;
    }

    public function get_state() {
        return $this->db_table->get_state();
    }

    function set_template_place_name_html_title($template_place_name_html_title) {
        $this->template_place_name_html_title = $template_place_name_html_title;
    }

    function get_template_place_name_html_title() {
        return $this->template_place_name_html_title;
    }

    function set_template_place_name_controller_name($template_place_name_controller_name) {
        $this->template_place_name_controller_name = $template_place_name_controller_name;
    }

    function get_template_place_name_controller_name() {
        return $this->template_place_name_controller_name;
    }

    function set_board_create_name($board_new_name) {
        $this->board_create_name = $board_new_name;
        if (isset($this->board_create_object)) {
            $this->board_create_object->set_board_name($board_new_name);
        }
    }

    function set_board_read_name($board_view_name) {
        $this->board_read_name = $board_view_name;
        if (isset($this->board_read_object)) {
            $this->board_read_object->set_board_name($board_view_name);
        }
    }

    function set_board_update_name($board_update_name) {
        $this->board_update_name = $board_update_name;
        if (isset($this->board_update_object)) {
            $this->board_update_object->set_board_name($board_update_name);
        }
    }

    function set_board_delete_name($board_delete_name) {
        $this->board_delete_name = $board_delete_name;
        if (isset($this->board_delete_object)) {
            $this->board_delete_object->set_board_name($board_delete_name);
        }
    }

    function set_board_list_name($board_list_name) {
        $this->board_list_name = $board_list_name;
        if (isset($this->board_list_object)) {
            $this->board_list_object->set_board_name($board_list_name);
        }
    }

    function get_board_list_url_name() {
        return $this->board_list_url_name;
    }

    function get_board_create_url_name() {
        return $this->board_create_url_name;
    }

    function get_board_read_url_name() {
        return $this->board_read_url_name;
    }

    function get_board_update_url_name() {
        return $this->board_update_url_name;
    }

    function get_board_delete_url_name() {
        return $this->board_delete_url_name;
    }

    function set_board_list_url_name($board_list_url_name) {
        $this->board_list_url_name = $board_list_url_name;
    }

    function set_board_create_url_name($board_create_url_name) {
        $this->board_create_url_name = $board_create_url_name;
    }

    function set_board_read_url_name($board_read_url_name) {
        $this->board_read_url_name = $board_read_url_name;
    }

    function set_board_update_url_name($board_update_url_name) {
        $this->board_update_url_name = $board_update_url_name;
    }

    function set_board_delete_url_name($board_delete_url_name) {
        $this->board_delete_url_name = $board_delete_url_name;
    }

}
