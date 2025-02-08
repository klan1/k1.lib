<?php

namespace k1lib\crudlexs\board;

use k1app\core\template\base as blank_tpl;
use k1lib\common_strings;
use k1lib\crudlexs\controller\base;
use k1lib\crudlexs\object\search_helper;
use k1lib\db\security\db_table_aliases;
use k1lib\html\bootstrap\modal;
use k1lib\html\div;
use k1lib\html\DOM as DOM;
use k1lib\html\iframe;
use k1lib\html\notifications\on_DOM as DOM_notification;

class search extends board_base implements board_interface {

    /**
     *
     * @var \k1lib\crudlexs\search_helper
     */
    public $search_object;
    protected $search_catch_post_enable = TRUE;

    public function __construct(base $controller_object, array $user_levels_allowed = []) {
        parent::__construct($controller_object, $user_levels_allowed);
        if ($this->is_enabled) {
            $this->search_object = new search_helper($this->controller_object->db_table);
            $this->data_loaded = $this->search_object->load_db_table_data(TRUE);
        }
        $this->co()->app->tpl()->q('.section')->set_class('k1lib-board-search', true);
    }

    /**
     * @return div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }
        $blank_tpl = new blank_tpl();

        $this->controller_object->app_controller::use_tpl($blank_tpl);
        DOM::start($blank_tpl);
        /**
         * IFRAME for KF tool
         */
        $fk_iframe = new iframe('', 'utility-iframe mw-100', "fk-iframe");
//        $fk_iframe->set_style($style)

        $modal = new modal(common_strings::$fk_tool_name, $fk_iframe);
        DOM::html_document()->body()->append_child_tail($modal);

        if ($this->search_object->get_state()) {

            $this->search_object->set_search_catch_post_enable($this->search_catch_post_enable);
            $this->search_object->set_html_column_classes("column lg-11 md-11 sm-12");
            $this->search_object->set_html_form_column_classes("lg-11");

            $this->search_object->do_html_object()->append_to($this->board_content_div);

            return $this->board_content_div;
        } else {
            DOM_notification::queue_mesasage(board_base_labels::$error_mysql_table_not_opened, "alert", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_labels::$error_mysql);
            return FALSE;
        }
    }

    /**
     * @return div|boolean
     */
    public function exec_board() {
        if (!$this->is_enabled) {
            return FALSE;
        }

        if ($this->data_loaded) {
            return $this->board_content_div;
        } else {
            DOM_notification::queue_mesasage(board_create_strings::$error_no_blank_data, "alert", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_strings::$alert_board);
            $this->search_object->make_invalid();
            $this->is_enabled = FALSE;
            return FALSE;
        }
    }

    public function finish_board($do_redirect = TRUE, $custom_redirect = FALSE) {
        
    }

    public function set_search_catch_post_enable($search_catch_post_enable) {
        $this->search_catch_post_enable = $search_catch_post_enable;
    }

    function set_object_id($class_name) {
        if (isset($this->db_table) && key_exists($this->db_table->get_db_table_name(), db_table_aliases::$aliases)) {
            $table_name = db_table_aliases::$aliases[$this->db_table->get_db_table_name()];
        } else if (isset($this->db_table)) {
            $table_name = $this->db_table->get_db_table_name();
        } else {
            $table_name = "no-table";
        }
        return $this->object_id = $table_name . "-" . basename(str_replace("\\", "/", $class_name));
    }
}
