<?php

namespace k1lib\app;

use k1app\core\template\base;
use k1app\template\mazer\layouts\blank;
use k1app\template\mazer\layouts\sidebar_page;
use k1lib\app\controller;
use k1lib\crudlexs\controller\base as cb;
use k1lib\crudlexs\db_table;
use k1lib\crudlexs\object\base as ob;
use k1lib\html\div;
use k1lib\html\DOM;
use k1lib\session\session_db;
use k1lib\urlrewrite\url;
use const k1app\K1APP_BASE_URL;

class controller_crud extends controller {

    /**
     * CRUD REQUISITIES
     */
    static protected $controller_name = "CRUDLEXS Controller";
    static protected $controller_db_table = "";
    static protected div $crud_container;
    static protected cb $co;

    static function on_post() {
        self::launch();
    }

    static function start_crud($controller_name, $controller_db_table) {
        self::app()->start_session_db(1);
        self::$controller_name = $controller_name;
        self::$controller_db_table = $controller_db_table;
    }

    static function run_crud($parent_class, base|blank|sidebar_page $tpl, string $nav_id) {
//        $tpl = new my_sidebar_page();
        self::use_tpl($tpl);

        /**
         *  LEGACY machete 
         */
        DOM::start(self::$tpl);

        if (method_exists(self::$tpl, 'page_content')) {
            self::$tpl->page_content()->set_title(" ");
            self::$tpl->page_content()->set_subtitle(" ");
            self::$tpl->page_content()->set_content_title(null);
            self::$tpl->page_content()->set_content(null);
        }

        if (method_exists(self::$tpl, 'menu') && self::$tpl->menu()->q($nav_id)) {
            self::$tpl->menu()->q($nav_id)->nav_is_active();
        }

        /**
         * ONE LINE config: less codign, more party time!
         * $co = controller_object
         */
        self::$co = new cb(K1APP_BASE_URL, $parent_class, self::$controller_db_table, self::$controller_name);
        self::$co->set_title_tag_id('#k1app-page-title');
        self::$co->set_subtitle_tag_id('#k1app-page-subtitle');

        if (self::$co->db_table->get_state() === false) {
            die('DB table did not found: ' . $parent_class);
        }
        self::$co->set_config_from_class('\k1app\table_config\\' . self::$controller_db_table);

        /**
         * USE THIS IF THE TABLE NEED THE LOGIN_ID ON EVERY ROW FOR TRACKING
         */
        self::$co->db_table->set_field_constants(['user_login' => session_db::get_user_login()]);

        static::init_board();

        static::start_board();

        static::exec_board();

        static::finish_board();
    }

    static function init_board() {
        self::$crud_container = self::$co->init_board();
    }

    static function start_board() {
        self::$co->start_board();

        // LIST
        if (self::$co->on_object_list()) {
            $read_url = url::do_url(self::$co->get_controller_root_dir() . self::$co->get_board_read_url_name() . "/--rowkeys--/", ["auth-code" => "--authcode--"]);
            self::$co->board_list()->list_object->apply_link_on_field_filter($read_url, ob::USE_LABEL_FIELDS);
        }
    }

    static function exec_board() {
        self::$co->exec_board();

        if (self::$co->on_object_list()) {
            self::$co->board_list()->list_object->html_table->set_max_text_length_on_cell(100);
        }
    }

    static function finish_board() {
        self::$co->finish_board();

        if (method_exists(self::$tpl, 'page_content')) {
            self::$tpl->page_content()->set_content(self::$crud_container);
        } else {
            self::$tpl->body()->set_value(self::$crud_container);
        }
    }

    static function add_related_table($table_name, $controller_url, $related_title) {
        if (self::$co->on_board_read()) {
            $related_div = self::$crud_container->append_div("k1lib-crudlexs-related-data");
            /**
             * Related list
             */
            $related_db_table = new db_table(self::$app->db(), $table_name);
            self::$co->board_read_object->set_related_show_all_data(TRUE);
            self::$co->board_read_object->set_related_show_new(TRUE);
            $related_list = self::$co->board_read_object->create_related_list(
                    $related_db_table,
                    NULL,
                    $related_title,
                    $controller_url,
                    ('\k1app\table_config\\' . $table_name)::BOARD_CREATE_URL,
                    ('\k1app\table_config\\' . $table_name)::BOARD_READ_URL,
                    ('\k1app\table_config\\' . $table_name)::BOARD_LIST_URL,
                    FALSE
            );
            $related_list->append_to($related_div);
        }
    }

    static function end() {
        parent::end();
        echo self::$tpl->generate();
    }
}
