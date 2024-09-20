<?php

namespace k1lib\crudlexs\board;

use k1lib\crudlexs\controller\base;
use k1lib\html\div;
use k1lib\html\DOM as DOM;
use k1lib\html\notifications\on_DOM as DOM_notification;
use k1lib\session\session_plain;
use function k1lib\common\unserialize_var;

class board_base {

    /**
     * DB table main object
     * @var base 
     */
    protected $controller_object;

    /**
     * @var \k1lib\html\div;
     */
    public $board_content_div;

    /**
     * @var bool
     */
    protected $data_loaded = FALSE;

    /**
     * @var bool
     */
    protected $is_enabled = FALSE;

    /**
     * @var bool
     */
    protected $skip_form_action = FALSE;

    /**
     * @var string
     */
    protected $user_levels_allowed = NULL;

    /**
     * @var mixed 
     */
    protected $sql_action_result = NULL;

    /**
     * @var string
     */
    protected $show_rule_to_apply = NULL;

    /**
     * @var bool
     */
    protected $apply_label_filter = TRUE;

    /**
     * @var bool
     */
    protected $apply_field_label_filter = TRUE;

    /**
     * @var string
     */
    protected $button_div_id = "k1lib-crudlexs-buttons mb-3";

    /**
     * @var string
     */
    protected $notifications_div_id = "k1lib-output";

    /**
     * @var div
     */
    protected $button_div_tag;

    public function __construct(base $controller_object, array $user_levels_allowed = []) {
        $this->controller_object = $controller_object;
        $this->board_content_div = new div("board-content");

        $this->user_levels_allowed = $user_levels_allowed;

        if (session_plain::is_enabled()) {
            if (!$this->check_user_level_access()) {
                $this->is_enabled = false;
            } else {
                $this->is_enabled = true;
            }
        }

        /**
         * Search util hack
         */
        $post_data_to_use = unserialize_var("post-data-to-use");
//        $post_data_table_config = \k1lib\common\unserialize_var("post-data-table-config");

        if (!empty($post_data_to_use)) {
//            $_POST = $post_data_to_use;
            $this->skip_form_action = TRUE;
//            \k1lib\common\unset_serialize_var("post-data-to-use");
//            \k1lib\common\unset_serialize_var("post-data-table-config");
        }
    }

    public function start_board() {
        if (!$this->is_enabled) {
            DOM_notification::queue_mesasage(board_base_strings::$error_board_disabled, "warning", $this->notifications_div_id);
            DOM_notification::queue_title(board_base_strings::$alert_board);
            return FALSE;
        }
        $this->button_div_tag = $this->board_content_div->append_div($this->button_div_id);
        return TRUE;
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
            $head = DOM::html_document()->head();
            $current_html_title = $head->get_title();
            $head->set_title($current_html_title . " - " . $board_name);

            $this->controller_object->html_subtitle_tag->set_value("{$board_name}", TRUE);
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
            if (empty(array_key_exists(session_plain::get_user_level(), array_flip($this->user_levels_allowed)))) {
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    public function get_show_rule_to_apply() {
        return $this->show_rule_to_apply;
    }

    public function set_show_rule_to_apply($show_rule_to_apply) {
        $this->show_rule_to_apply = $show_rule_to_apply;
    }

    public function get_apply_field_label_filter() {
        return $this->apply_field_label_filter;
    }

    public function set_apply_field_label_filter($apply_field_label_filter) {
        $this->apply_field_label_filter = $apply_field_label_filter;
    }

    public function get_apply_label_filter() {
        return $this->apply_label_filter;
    }

    public function set_apply_label_filter($apply_label_filter) {
        $this->apply_label_filter = $apply_label_filter;
    }

    public function get_sql_action_result() {
        return $this->sql_action_result;
    }

    public function get_button_div_id() {
        return $this->button_div_id;
    }

    public function set_button_div_id($button_div_id) {
        $this->button_div_id = $button_div_id;
    }

    /**
     * @return div
     */
    public function button_div_tag() {
        return $this->button_div_tag;
    }

    public function set_button_div_tag(div $button_div_tag) {
        $this->button_div_tag = $button_div_tag;
    }

    /**
     * @return div
     */
    public function board_content_div() {
        return $this->board_content_div;
    }

    public function set_board_content_div(div $board_content_div) {
        $this->board_content_div = $board_content_div;
    }

    public function get_notifications_div_id() {
        return $this->notifications_div_id;
    }

    public function set_notifications_div_id($notifications_div_id) {
        $this->notifications_div_id = $notifications_div_id;
    }

    function set_skip_form_action($skip_form_action) {
        $this->skip_form_action = $skip_form_action;
    }
}
