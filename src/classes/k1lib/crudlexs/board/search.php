<?php

namespace k1lib\crudlexs\board;

use k1lib\common_strings;
use k1lib\crudlexs\controller\base;
use k1lib\crudlexs\object\search_helper;
use k1lib\db\security\db_table_aliases;
use k1lib\html\a;
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
    }

    /**
     * @return div|boolean
     */
    public function start_board() {
        if (!parent::start_board()) {
            return FALSE;
        }
        /**
         * IFRAME for KF tool
         */
        $fk_iframe = new iframe('', 'utility-iframe', "fk-iframe");
        DOM::html_document()->body()->content()->append_child_tail($fk_iframe);
        
        if ($this->search_object->get_state()) {
            $close_search_buttom = new a(NULL, " " . common_strings::$button_cancel, "_parent");
            $close_search_buttom->set_id("close-search-button");
            $close_search_buttom->set_attrib("class", "button warning fi-page-close");
            $close_search_buttom->set_attrib("onClick", "parent.close_search();");
            $close_search_buttom->append_to($this->button_div_tag);

            $this->search_object->set_search_catch_post_enable($this->search_catch_post_enable);
            $this->search_object->set_html_column_classes("column large-11 medium-11 small-12");
            $this->search_object->set_html_form_column_classes("large-11");

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
