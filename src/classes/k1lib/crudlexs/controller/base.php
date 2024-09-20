<?php

namespace k1lib\crudlexs\controller;

use k1lib\common_strings;
use k1lib\crudlexs\board\board_base_strings;
use k1lib\crudlexs\board\board_list;
use k1lib\crudlexs\board\create;
use k1lib\crudlexs\board\delete;
use k1lib\crudlexs\board\read;
use k1lib\crudlexs\board\search;
use k1lib\crudlexs\board\update;
use k1lib\crudlexs\db_table;
use k1lib\crudlexs\object\creating;
use k1lib\crudlexs\object\listing;
use k1lib\crudlexs\object\reading;
use k1lib\crudlexs\object\updating;
use k1lib\html\div;
use k1lib\html\DOM as DOM;
use k1lib\html\notifications\on_DOM as DOM_notification;
use k1lib\html\script;
use k1lib\html\span;
use k1lib\html\tag;
use k1lib\K1MAGIC;
use k1lib\session\session_plain;
use k1lib\urlrewrite\url as url;
use PDO;
use Ramsey\Uuid\DegradedUuid;
use const k1lib\K1LIB_BASE_PATH;
use function k1lib\common\clean_array_with_guide;
use function k1lib\html\html_header_go;
use function k1lib\sql\table_url_text_to_keys;

class base {

    protected $security_no_rules_enable = FALSE;

    /**
     * DB table main object
     * @var db_table 
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
    protected $controller_board_allowed_leves = [];

    /**
     *
     * @var bool
     */
    protected $board_inited = FALSE;
    protected $board_started = FALSE;
    protected $board_executed = FALSE;

    /**
     *
     * @var div
     */
    public $board_div_content;

    /**
     * 
     * URL MANAGEMENT VALUES
     * 
     */

    /**
     *
     * @var \k1lib\crudlexs\board_list;
     */
    public $board_list_object;
    protected $board_list_url_name = "list";
    protected $board_list_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_create
     */
    public $board_create_object;
    protected $board_create_url_name = "create";
    protected $board_create_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_read
     */
    public $board_read_object;
    protected $board_read_url_name = "read";
    protected $board_read_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_update
     */
    public $board_update_object;
    protected $board_update_url_name = "update";
    protected $board_update_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_delete
     */
    public $board_delete_object;
    protected $board_delete_url_name = "delete";
    protected $board_delete_allowed_levels = [];

    /**
     *
     * @var \k1lib\crudlexs\board_search
     */
    public $board_search_object;
    protected $board_search_url_name = "search";
    protected $board_search_allowed_levels = [];

    /**
     *
     * Board names for html title and controller name tag
     * 
     */
    protected string $title_tag_id = '#k1app-page-title';
    protected string $subtitle_tag_id = '#k1app-page-subtitle';
    public tag $html_title_tag;
    public tag $html_subtitle_tag;

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
    protected $template_place_name_board_name = "board-name";
    protected $board_list_name;
    protected $board_create_name;
    protected $board_read_name;
    protected $board_update_name;
    protected $board_delete_name;
    protected $url_redirect_after_delete = "../../list/";

    /**
     * BOARDS avaliabilty
     */
    protected $board_list_enabled = TRUE;
    protected $board_create_enabled = TRUE;
    protected $board_read_enabled = TRUE;
    protected $board_update_enabled = TRUE;
    protected $board_delete_enabled = TRUE;
    protected $board_finished = FALSE;

    /**
     * One line config for more time to party and less coding :)
     * @param string $app_base_dir Use here \k1app\APP_BASE_URL
     * @param PDO $db DB app object
     * @param string $db_table_name Table to open from the DB
     * @param string $controller_name Name for html title and controller name tag
     * @param string $template_place_name_html_title 
     * @param string $template_place_name_controller_name 
     */
    public function __construct($app_base_dir, PDO $db, $db_table_name, $controller_name) {
        /**
         * URL Management
         */
        // Posible URL ERROR FIX
        if ($app_base_dir == '//') {
            $app_base_dir = '/';
        }
        $this->controller_root_dir = $app_base_dir . url::make_url_from_rewrite('this');
        $this->controller_url_value = url::get_url_level_value('this');
        $this->controller_board_url_value = $this->set_and_get_next_url_value();
        /**
         * DB Table 
         */
        $this->db_table = new db_table($db, $db_table_name);

        /**
         * Controller name for add on <html><title> and controller name tag
         */
        $this->controller_name = $controller_name;
        $this->html_title_tag = DOM::html()->body()->q($this->title_tag_id);
        $this->html_subtitle_tag = DOM::html()->body()->q($this->subtitle_tag_id);

        if (!empty($this->html_title_tag)) {
            $this->html_title_tag->set_value($controller_name);
            DOM::html()->head()->set_title(DOM::html()->head()->get_title() . " | $controller_name");
        }


//        temply::set_place_value($this->template_place_name_html_title, " | $controller_name");
//        temply::set_place_value($this->template_place_name_controller_name, $controller_name);

        /**
         * SET FROM LANG HACK
         */
        $this->board_list_name = controller_base_strings::$board_list_name;
        $this->board_create_name = controller_base_strings::$board_create_name;
        $this->board_read_name = controller_base_strings::$board_read_name;
        $this->board_update_name = controller_base_strings::$board_update_name;
        $this->board_delete_name = controller_base_strings::$board_delete_name;

        if (DOM::html()->body()) {
            $js_file = K1LIB_BASE_PATH . '/../dist/crudlexs/main.js';
            if (file_exists($js_file)) {
                $js_content = file_get_contents($js_file);

                $js_script = new script();
                $js_script->set_value($js_content);

                DOM::html()->body()->append_child_tail($js_script);
            } else {
                d($js_file);
            }
        }
    }

    public function set_title_tag_id($title_tag_id): void {
        $this->title_tag_id = $title_tag_id;
    }

    public function set_subtitle_tag_id($subtitle_tag_id): void {
        $this->subtitle_tag_id = $subtitle_tag_id;
    }

    public function set_config_from_class($class_name = NULL) {
//        $class_name::CONTROLLER_ALLOWED_LEVELS;
        if (!class_exists($class_name)) {
            d("Warning: $class_name do not exist");
        }

        /**
         * ENABLED
         */
        if (defined("{$class_name}::BOARD_CREATE_ENABLED")) {
            $this->set_board_create_enabled($class_name::BOARD_CREATE_ENABLED);
        }
        if (defined("{$class_name}::BOARD_READ_ENABLED")) {
            $this->set_board_read_enabled($class_name::BOARD_READ_ENABLED);
        }
        if (defined("{$class_name}::BOARD_UPDATE_ENABLED")) {
            $this->set_board_update_enabled($class_name::BOARD_UPDATE_ENABLED);
        }
        if (defined("{$class_name}::BOARD_DELETE_ENABLED")) {
            $this->set_board_delete_enabled($class_name::BOARD_DELETE_ENABLED);
        }
        if (defined("{$class_name}::BOARD_LIST_ENABLED")) {
            $this->set_board_list_enabled($class_name::BOARD_LIST_ENABLED);
        }
        /**
         * URLS
         */
        if (defined("{$class_name}::BOARD_CREATE_URL")) {
            $this->set_board_create_url_name($class_name::BOARD_CREATE_URL);
        }
        if (defined("{$class_name}::BOARD_READ_URL")) {
            $this->set_board_read_url_name($class_name::BOARD_READ_URL);
        }
        if (defined("{$class_name}::BOARD_UPDATE_URL")) {
            $this->set_board_update_url_name($class_name::BOARD_UPDATE_URL);
        }
        if (defined("{$class_name}::BOARD_DELETE_URL")) {
            $this->set_board_delete_url_name($class_name::BOARD_DELETE_URL);
        }
        if (defined("{$class_name}::BOARD_LIST_URL")) {
            $this->set_board_list_url_name($class_name::BOARD_LIST_URL);
        }
        /**
         * NAMES
         */
        if (defined("{$class_name}::BOARD_CREATE_NAME")) {
            $this->set_board_create_name($class_name::BOARD_CREATE_NAME);
        }
        if (defined("{$class_name}::BOARD_READ_NAME")) {
            $this->set_board_read_name($class_name::BOARD_READ_NAME);
        }
        if (defined("{$class_name}::BOARD_UPDATE_NAME")) {
            $this->set_board_update_name($class_name::BOARD_UPDATE_NAME);
        }
        if (defined("{$class_name}::BOARD_DELETE_NAME")) {
            $this->set_board_delete_name($class_name::BOARD_DELETE_NAME);
        }
        if (defined("{$class_name}::BOARD_LIST_NAME")) {
            $this->set_board_list_name($class_name::BOARD_LIST_NAME);
        }

        /**
         * ALLOWED LEVELS
         */
        if (defined("{$class_name}::BOARD_CREATE_ALLOWED_LEVELS")) {
            $this->set_board_create_allowed_levels($class_name::BOARD_CREATE_ALLOWED_LEVELS);
        }
        if (defined("{$class_name}::BOARD_READ_ALLOWED_LEVELS")) {
            $this->set_board_read_allowed_levels($class_name::BOARD_READ_ALLOWED_LEVELS);
        }
        if (defined("{$class_name}::BOARD_UPDATE_ALLOWED_LEVELS")) {
            $this->set_board_update_allowed_levels($class_name::BOARD_UPDATE_ALLOWED_LEVELS);
        }
        if (defined("{$class_name}::BOARD_DELETE_ALLOWED_LEVELS")) {
            $this->set_board_delete_allowed_levels($class_name::BOARD_DELETE_ALLOWED_LEVELS);
        }
        if (defined("{$class_name}::BOARD_LIST_ALLOWED_LEVELS")) {
            $this->set_board_list_allowed_levels($class_name::BOARD_LIST_ALLOWED_LEVELS);
        }
//        if (defined("{$class_name}::BOARD_EXPORT_ALLOWED_LEVELS")) {
//            $this->set_board_export_allowed_levels($class_name::BOARD_EXPORT_ALLOWED_LEVELS);
//        }
    }

    public function set_and_get_next_url_value() {
        $next_url_level = url::get_url_level_count();
        $controller_url_value = "controller_url_{$next_url_level}";
        return url::set_url_rewrite_var($next_url_level, $controller_url_value, FALSE);
    }

    /**
     * @param string $specific_board_to_init 
     * @return div|boolean
     */
    public function init_board($specific_board_to_init = NULL) {
        if ($this->security_no_rules_enable === FALSE) {
            if (isset($_GET['no-rules'])) {
                unset($_GET['no-rules']);
            }
        }
        if (empty($specific_board_to_init)) {
            $specific_board_to_init = ($this->controller_board_url_value) ? $this->controller_board_url_value : "no-url";
        }
        switch ($specific_board_to_init) {
            case $this->board_create_url_name:
                $this->board_create_object = new create($this, $this->board_create_allowed_levels);
                $this->board_create_object->set_is_enabled($this->board_create_enabled);
                $this->board_create_object->set_board_name($this->board_create_name);
                $this->board_div_content = $this->board_create_object->board_content_div;

                break;

            case $this->board_read_url_name:
                $this->board_read_object = new read($this, $this->board_read_allowed_levels);
                $this->board_read_object->set_is_enabled($this->board_read_enabled);
                $this->board_read_object->set_board_name($this->board_read_name);
                $this->board_div_content = $this->board_read_object->board_content_div;
                if (!$this->board_list_enabled || !$this->get_board_list_allowed_for_current_user()) {
                    $this->board_read_object->set_all_data_enable(FALSE);
                }
                if (!$this->board_update_enabled || !$this->get_board_update_allowed_for_current_user()) {
                    $this->board_read_object->set_update_enable(FALSE);
                }
                if (!$this->board_delete_enabled || !$this->get_board_delete_allowed_for_current_user()) {
                    $this->board_read_object->set_delete_enable(FALSE);
                }
                break;

            case $this->board_update_url_name:
                $this->board_update_object = new update($this, $this->board_update_allowed_levels);
                $this->board_update_object->set_is_enabled($this->board_update_enabled);
                $this->board_update_object->set_board_name($this->board_update_name);
                $this->board_div_content = $this->board_update_object->board_content_div;

                break;

            case $this->board_delete_url_name:
                $this->board_delete_object = new delete($this, $this->board_delete_allowed_levels);
                $this->board_delete_object->set_is_enabled($this->board_delete_enabled);
                $this->board_delete_object->set_board_name($this->board_delete_name);
                $this->board_div_content = $this->board_delete_object->board_content_div;
                break;

            case $this->board_list_url_name:
                $this->board_list_object = new board_list($this, $this->board_list_allowed_levels);
                $this->board_list_object->set_is_enabled($this->board_list_enabled);
                $this->board_list_object->set_board_name($this->board_list_name);
                $this->board_div_content = $this->board_list_object->board_content_div;
                if (!$this->board_create_enabled || !$this->get_board_create_allowed_for_current_user()) {
                    $this->board_list_object->set_create_enable(FALSE);
                }
                break;
            case $this->board_search_url_name:
                $this->board_search_object = new search($this, $this->board_list_allowed_levels);
                $this->board_search_object->set_is_enabled($this->board_list_enabled);
                $this->board_search_object->set_board_name($this->board_search_url_name);
                $this->board_div_content = $this->board_search_object->board_content_div;
                break;

            default:
                $this->board_inited = FALSE;
                html_header_go(url::do_url($this->controller_root_dir . $this->get_board_list_url_name() . "/"));
                return FALSE;
        }
        $this->board_inited = TRUE;
        return $this->board_div_content;
    }

    public function get_board_create_allowed_for_current_user() {
        if (empty(array_key_exists(session_plain::get_user_level(), array_flip($this->board_create_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_board_read_allowed_for_current_user() {
        if (empty(array_key_exists(session_plain::get_user_level(), array_flip($this->board_read_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_board_update_allowed_for_current_user() {
        if (empty(array_key_exists(session_plain::get_user_level(), array_flip($this->board_update_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_board_delete_allowed_for_current_user() {
        if (empty(array_key_exists(session_plain::get_user_level(), array_flip($this->board_delete_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function get_board_list_allowed_for_current_user() {
        if (empty(array_key_exists(session_plain::get_user_level(), array_flip($this->board_list_allowed_levels)))) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function read_url_keys_text_for_create($db_table_name, array &$keys_array_to_return = []) {
        if (isset($this->board_create_object)) {
            /**
             * URL key text management
             */
            $related_url_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), "related_url_keys_text", FALSE);
            if (!empty($related_url_keys_text)) {
                $related_table = $db_table_name;
                $related_db_table = new db_table($this->db_table->db, $related_table);
                $related_url_keys_array = table_url_text_to_keys($related_url_keys_text, $related_db_table->get_db_table_config());
                /**
                 * lets fix the non-same key name
                 */
//                \k1lib\sql\resolve_fk_real_fields_names($related_url_keys_array, $this->db_table->get_db_table_config());
                $db_table_config = $this->db_table->get_db_table_config();
                foreach ($db_table_config as $field => $field_config) {
                    if (!empty($field_config['refereced_column_config'])) {
                        $fk_field_name = $field_config['refereced_column_config']['field'];
                        foreach ($related_url_keys_array as $field_current => $value) {
                            if (($field_current == $fk_field_name) && ($field != $field_current)) {
                                $related_url_keys_array[$field] = $value;
                                unset($related_url_keys_array[$field_current]);
                            }
                        }
                    }
                }
                $related_url_keys_array = clean_array_with_guide($related_url_keys_array, $db_table_config);
                /////
                $keys_array_to_return = $related_url_keys_array;
                $related_url_keys_text_auth_code = md5(K1MAGIC::get_value() . $related_url_keys_text);
                if (isset($_GET['auth-code']) && ($_GET['auth-code'] === $related_url_keys_text_auth_code)) {
                    $this->db_table->set_field_constants($related_url_keys_array);
                    return $related_url_keys_text;
                } else {
                    $this->board_create_object->set_is_enabled(FALSE);
                    DOM_notification::queue_mesasage(board_base_strings::$error_url_keys_no_auth, "alert", $this->notifications_div_id, common_strings::$error);
                    return FALSE;
                }
            } else {
//                $this->board_create_object->set_is_enabled(FALSE);
//                DOM_notification::queue_mesasage(board_base_strings::$error_url_keys_no_keys_text, "alert", $this->notifications_div_id, \k1lib\common_strings::$error);
                return FALSE;
            }
        }
    }

    public function read_url_keys_text_for_update() {
        if (isset($this->board_update_object)) {
            /**
             * URL key text management
             */
            $update_row_keys_array = $this->board_update_object->update_object->get_row_keys_array();
            $this->db_table->set_field_constants($update_row_keys_array);
            return FALSE;
        }
    }

    public function read_url_keys_text_for_list($db_table_name, $is_required = TRUE) {
        if (isset($this->board_list_object)) {
            /**
             * URL key text management
             */
            $related_url_keys_text = url::set_url_rewrite_var(url::get_url_level_count(), "related_url_keys_text", FALSE);
            if (!empty($related_url_keys_text)) {
                $related_table = $db_table_name;
                $related_db_table = new db_table($this->db_table->db, $related_table);
                $related_url_keys_array = table_url_text_to_keys($related_url_keys_text, $related_db_table->get_db_table_config());
                /**
                 * lets fix the non-same key name
                 */
                $db_table_config = $this->db_table->get_db_table_config();
                foreach ($db_table_config as $field => $field_config) {
                    if (!empty($field_config['refereced_column_config'])) {
                        $fk_field_name = $field_config['refereced_column_config']['field'];
                        foreach ($related_url_keys_array as $field_current => $value) {
                            if (($field_current == $fk_field_name) && ($field != $field_current)) {
                                $related_url_keys_array[$field] = $value;
                                unset($related_url_keys_array[$field_current]);
                            }
                        }
                    }
                }
                $related_url_keys_array = clean_array_with_guide($related_url_keys_array, $db_table_config);
                /////
                $related_url_keys_text_auth_code = md5(K1MAGIC::get_value() . $related_url_keys_text);
                if (isset($_GET['auth-code']) && ($_GET['auth-code'] === $related_url_keys_text_auth_code)) {
                    $this->db_table->set_query_filter($related_url_keys_array, TRUE);
                    return $related_url_keys_text;
                } else {
                    $this->board_list_object->set_is_enabled(FALSE);
                    DOM_notification::queue_mesasage(board_base_strings::$error_url_keys_no_auth, "alert", $this->notifications_div_id, common_strings::$error);
                    return FALSE;
                }
            } else {
                if ($is_required) {
                    $this->board_list_object->set_is_enabled(FALSE);
                    DOM_notification::queue_mesasage(board_base_strings::$error_url_keys_no_keys_text, "alert", $this->notifications_div_id, common_strings::$error);
                    return FALSE;
                }
            }
        }
    }

    public function start_board($specific_board_to_start = NULL) {
        $this->board_started = TRUE;
        if ($this->board_inited) {
            if (empty($specific_board_to_start)) {
                $specific_board_to_start = $this->controller_board_url_value;
            }
            switch ($specific_board_to_start) {
                case $this->board_create_url_name:
                    return $this->board_create_object->start_board();
                    break;

                case $this->board_read_url_name:
                    return $this->board_read_object->start_board();
                    break;

                case $this->board_update_url_name:
                    return $this->board_update_object->start_board();
                    break;

                case $this->board_delete_url_name:
                    return $this->board_delete_object->start_board();
                    break;

                case $this->board_list_url_name:
                    return $this->board_list_object->start_board();
                    break;

                case $this->board_search_url_name:
                    return $this->board_search_object->start_board();
                    break;

                default:
                    $this->board_started = FALSE;
                    html_header_go($this->controller_root_dir . $this->get_board_list_url_name() . "/");
                    return FALSE;
            }
        } else {
            $this->board_started = FALSE;
            trigger_error(__METHOD__ . ' ' . controller_base_strings::$error_board_not_inited, E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * 
     * @param boolean $do_echo
     * @param string $specific_board_to_exec
     * @return div
     */
    public function exec_board($specific_board_to_exec = NULL) {
        $this->board_executed = TRUE;

        if ($this->board_started) {
            if (empty($specific_board_to_exec)) {
                $specific_board_to_exec = $this->controller_board_url_value;
            }
            switch ($specific_board_to_exec) {
                case $this->board_create_url_name:
                    return $this->board_create_object->exec_board();

                case $this->board_read_url_name:
                    return $this->board_read_object->exec_board();

                case $this->board_update_url_name:
                    return $this->board_update_object->exec_board();

                case $this->board_delete_url_name:
                    return $this->board_delete_object->exec_board();

                case $this->board_list_url_name:
                    return $this->board_list_object->exec_board();

                case $this->board_search_url_name:
                    return $this->board_search_object->exec_board();

                default:
                    $this->board_executed = FALSE;
                    html_header_go($this->controller_root_dir . $this->get_board_list_url_name() . "/");
                    break;
            }
        } else {
            $this->board_executed = FALSE;
            trigger_error(__METHOD__ . ' ' . controller_base_strings::$error_board_not_started, E_USER_WARNING);
            return FALSE;
        }
    }

    public function finish_board($do_redirect = TRUE, $custom_redirect = FALSE) {
        $this->board_finished = TRUE;

        if ($this->board_started) {
            switch ($this->controller_board_url_value) {
                case $this->board_create_url_name:
                    return $this->board_create_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_read_url_name:
                    return $this->board_read_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_update_url_name:
                    return $this->board_update_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_delete_url_name:
                    return $this->board_delete_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_list_url_name:
                    return $this->board_list_object->finish_board($do_redirect, $custom_redirect);

                case $this->board_search_url_name:
                    return $this->board_search_object->finish_board($do_redirect, $custom_redirect);

                default:
                    $this->board_finished = FALSE;
                    html_header_go($this->controller_root_dir . $this->get_board_list_url_name() . "/");
                    break;
            }
        } else {
            $this->board_finished = FALSE;
            trigger_error(__METHOD__ . ' ' . controller_base_strings::$error_board_not_executed, E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * SIMPLE SETTERS AND GETTERS
     */
    function get_controller_url_value() {
        return $this->controller_url_value;
    }

    function get_controller_root_dir() {
        return $this->controller_root_dir;
    }

    function get_controller_board_url_value() {
        return $this->controller_board_url_value;
    }

    function get_board_list_enabled() {
        return $this->board_list_enabled;
    }

    function get_board_create_enabled() {
        return $this->board_create_enabled;
    }

    function get_board_read_enabled() {
        return $this->board_read_enabled;
    }

    function get_board_update_enabled() {
        return $this->board_update_enabled;
    }

    function get_board_delete_enabled() {
        return $this->board_delete_enabled;
    }

    function set_board_list_enabled($board_list_enabled) {
        if ($board_list_enabled !== FALSE) {
            $this->board_list_enabled = TRUE;
        } else {
            $this->board_list_enabled = FALSE;
        }
    }

    function set_board_create_enabled($board_create_enabled) {
        if ($board_create_enabled !== FALSE) {
            $this->board_create_enabled = TRUE;
        } else {
            $this->board_create_enabled = FALSE;
        }
    }

    function set_board_read_enabled($board_read_enabled) {
        if ($board_read_enabled !== FALSE) {
            $this->board_read_enabled = TRUE;
        } else {
            $this->board_read_enabled = FALSE;
        }
    }

    function set_board_update_enabled($board_update_enabled) {
        if ($board_update_enabled !== FALSE) {
            $this->board_update_enabled = TRUE;
        } else {
            $this->board_update_enabled = FALSE;
        }
    }

    function set_board_delete_enabled($board_delete_enabled) {
        if ($board_delete_enabled !== FALSE) {
            $this->board_delete_enabled = TRUE;
        } else {
            $this->board_delete_enabled = FALSE;
        }
    }

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

    function set_template_place_name_board_name($template_place_name_board_name) {
        $this->template_place_name_board_name = $template_place_name_board_name;
    }

    function get_template_place_name_board_name() {
        return $this->template_place_name_board_name;
    }

    function set_board_create_name($board_new_name) {
        $this->board_create_name = $board_new_name;
    }

    function set_board_read_name($board_view_name) {
        $this->board_read_name = $board_view_name;
    }

    function set_board_update_name($board_update_name) {
        $this->board_update_name = $board_update_name;
    }

    function set_board_delete_name($board_delete_name) {
        $this->board_delete_name = $board_delete_name;
    }

    function set_board_list_name($board_list_name) {
        $this->board_list_name = $board_list_name;
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
        if ($board_list_url_name === FALSE) {
            $this->set_board_list_enabled(FALSE);
        }
    }

    function set_board_create_url_name($board_create_url_name) {
        $this->board_create_url_name = $board_create_url_name;
        if ($board_create_url_name === FALSE) {
            $this->set_board_create_enabled(FALSE);
        }
    }

    function set_board_read_url_name($board_read_url_name) {
        $this->board_read_url_name = $board_read_url_name;
        if ($board_read_url_name === FALSE) {
            $this->set_board_read_enabled(FALSE);
        }
    }

    function set_board_update_url_name($board_update_url_name) {
        $this->board_update_url_name = $board_update_url_name;
        if ($board_update_url_name === FALSE) {
            $this->set_board_update_enabled(FALSE);
        }
    }

    function set_board_delete_url_name($board_delete_url_name) {
        $this->board_delete_url_name = $board_delete_url_name;
        if ($board_delete_url_name === FALSE) {
            $this->set_board_delete_enabled(FALSE);
        }
    }

    function get_controller_board_allowed_leves() {
        return $this->controller_board_allowed_leves;
    }

    function set_controller_board_allowed_leves($controller_board_allowed_leves) {
        $this->controller_board_allowed_leves = $controller_board_allowed_leves;
    }

    function get_board_list_allowed_levels() {
        return $this->board_list_allowed_levels;
    }

    function get_board_create_allowed_levels() {
        return $this->board_create_allowed_levels;
    }

    function get_board_read_allowed_levels() {
        return $this->board_read_allowed_levels;
    }

    function get_board_update_allowed_levels() {
        return $this->board_update_allowed_levels;
    }

    function get_board_delete_allowed_levels() {
        return $this->board_delete_allowed_levels;
    }

    function set_board_list_allowed_levels($board_list_allowed_levels) {
        $this->board_list_allowed_levels = $board_list_allowed_levels;
    }

    function set_board_create_allowed_levels($board_create_allowed_levels) {
        $this->board_create_allowed_levels = $board_create_allowed_levels;
    }

    function set_board_read_allowed_levels($board_read_allowed_levels) {
        $this->board_read_allowed_levels = $board_read_allowed_levels;
    }

    function set_board_update_allowed_levels($board_update_allowed_levels) {
        $this->board_update_allowed_levels = $board_update_allowed_levels;
    }

    function set_board_delete_allowed_levels($board_delete_allowed_levels) {
        $this->board_delete_allowed_levels = $board_delete_allowed_levels;
    }

    /**
     * ON BOARD
     */
    public function on_board_create() {
        if (isset($this->board_create_object) && $this->board_create_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_create
     */
    public function board_create() {
        if (isset($this->board_create_object) && $this->board_create_object->get_is_enabled()) {
            return $this->board_create_object;
        } else {
            return FALSE;
        }
    }

    public function on_object_create() {
        if (isset($this->board_create_object->create_object)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return creating
     */
    public function object_create() {
        if (isset($this->board_create_object->create_object)) {
            return $this->board_create_object->create_object;
        } else {
            return FALSE;
        }
    }

    public function on_board_read() {
        if (isset($this->board_read_object) && $this->board_read_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_read
     */
    public function board_read() {
        if (isset($this->board_read_object) && $this->board_read_object->get_is_enabled()) {
            return $this->board_read_object;
        } else {
            return FALSE;
        }
    }

    public function on_object_read() {
        if (isset($this->board_read_object->read_object)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return reading
     */
    public function object_read() {
        if (isset($this->board_read_object->read_object)) {
            return $this->board_read_object->read_object;
        } else {
            return FALSE;
        }
    }

    public function on_board_update() {
        if (isset($this->board_update_object) && $this->board_update_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_update
     */
    public function board_update() {
        if (isset($this->board_update_object) && $this->board_update_object->get_is_enabled()) {
            return $this->board_update_object;
        } else {
            return FALSE;
        }
    }

    public function on_object_update() {
        if (isset($this->board_update_object->update_object)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return updating
     */
    public function object_update() {
        if (isset($this->board_update_object->update_object)) {
            return $this->board_update_object->update_object;
        } else {
            return FALSE;
        }
    }

    public function on_board_delete() {
        if (isset($this->board_delete_object) && $this->board_delete_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_delete
     */
    public function board_delete() {
        if (isset($this->board_delete_object) && $this->board_delete_object->get_is_enabled()) {
            return $this->board_delete_object;
        } else {
            return FALSE;
        }
    }

    public function on_board_list() {
        if (isset($this->board_list_object) && $this->board_list_object->get_is_enabled()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return board_list
     */
    public function board_list() {
        if (isset($this->board_list_object) && $this->board_list_object->get_is_enabled()) {
            return $this->board_list_object;
        } else {
            return FALSE;
        }
    }

    public function on_object_list() {
        if (isset($this->board_list_object->list_object)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @return listing
     */
    public function object_list() {
        if (isset($this->board_list_object->list_object)) {
            return $this->board_list_object->list_object;
        } else {
            return FALSE;
        }
    }

    public function get_security_no_rules_enable() {
        return $this->security_no_rules_enable;
    }

    public function set_security_no_rules_enable($security_no_rules_enable) {
        $this->security_no_rules_enable = $security_no_rules_enable;
    }
}
