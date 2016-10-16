<?php

namespace k1lib\crudlexs;

use k1lib\templates\temply as temply;
use k1lib\html\DOM as DOM;

interface board_interface {

    public function start_board();

    public function exec_board();

    public function finish_board();
}

class board_base {

    /**
     * DB table main object
     * @var \k1lib\crudlexs\controller_base 
     */
    protected $controller_object;

    /**
     * @var \k1lib\html\div;
     */
    public $board_content_div;

    /**
     * @var boolean
     */
    protected $data_loaded = FALSE;

    /**
     * @var boolean
     */
    protected $is_enabled = FALSE;

    /**
     * @var boolean
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
     * @var boolean
     */
    protected $apply_label_filter = TRUE;

    /**
     * @var boolean
     */
    protected $apply_field_label_filter = TRUE;

    /**
     * @var string
     */
    protected $button_div_id = "k1lib-crudlexs-buttons";

    /**
     * @var string
     */
    protected $notifications_div_id = "k1lib-output";

    /**
     * @var \k1lib\html\div
     */
    protected $button_div_tag;

    public function __construct(\k1lib\crudlexs\controller_base $controller_object, array $user_levels_allowed = []) {
        $this->controller_object = $controller_object;
        $this->board_content_div = new \k1lib\html\div("board-content");

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
        $this->button_div_tag = $this->board_content_div->append_div($this->button_div_id);
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
            $head = DOM::html()->head();
            $current_html_title = $head->get_title();
            $head->set_title($current_html_title . " - " . $board_name);

            if ($this->controller_object->html_top_bar) {
                $this->controller_object->html_top_bar->set_title(3, " - {$board_name}", TRUE);
            }
//            temply::set_place_value($this->controller_object->get_template_place_name_html_title(), " - {$board_name}");
//            temply::set_place_value($this->controller_object->get_template_place_name_board_name(), $board_name);
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
     * @return \k1lib\html\div
     */
    public function button_div_tag() {
        return $this->button_div_tag;
    }

    public function set_button_div_tag(\k1lib\html\div $button_div_tag) {
        $this->button_div_tag = $button_div_tag;
    }

    /**
     * @return \k1lib\html\div
     */
    public function board_content_div() {
        return $this->board_content_div;
    }

    public function set_board_content_div(\k1lib\html\div $board_content_div) {
        $this->board_content_div = $board_content_div;
    }

    public function get_notifications_div_id() {
        return $this->notifications_div_id;
    }

    public function set_notifications_div_id($notifications_div_id) {
        $this->notifications_div_id = $notifications_div_id;
    }

}
