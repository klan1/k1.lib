<?php

namespace k1lib\app;

use k1app\core\template\base;
use k1app\template\mazer\components\app\main\page_heading\section;
use k1app\template\mazer\components\card;
use k1app\template\mazer\layouts\blank;
use k1app\template\mazer\layouts\sidebar_page;
use k1lib\app\controller;
use k1lib\crudlexs\controller\base as cb;
use k1lib\crudlexs\db_table;
use k1lib\crudlexs\object\base as ob;
use k1lib\html\div;
use k1lib\html\DOM;
use k1lib\urlrewrite\url;
use const k1app\K1APP_BASE_URL;

class controller_crud extends controller {

    /**
     * CRUD REQUISITIES
     */
    static protected $controller_name = "CRUDLEXS Controller";
    static protected $controller_db_table = "";
    static protected div $crud_container;
    static protected section $page_container;
    static protected cb $co;

    static function on_post(): void {
        self::launch();
    }

    static function start_crud($controller_name, $controller_db_table): void {
        self::$controller_name = $controller_name;
        self::$controller_db_table = $controller_db_table;
    }

    static function run_crud($parent_class, base|blank|sidebar_page $tpl, string|null $nav_id = null, $run_as_guest = false): void {
        self::use_tpl($tpl);

        self::$page_container = self::tpl()->page_content()->section();

        /**
         *  LEGACY machete 
         */
        DOM::start(self::$tpl);

        if (method_exists(self::$tpl, 'page_content')) {
            $page_content_obj = self::$tpl->page_content();
            if (method_exists($page_content_obj, 'set_title')) {

                $page_content_obj->set_title(" ");
            }
            if (method_exists($page_content_obj, 'set_subtitle')) {

                $page_content_obj->set_subtitle(" ");
            }
            if (method_exists($page_content_obj, 'set_content_title')) {

                $page_content_obj->set_content_title(" ");
            }
            if (method_exists($page_content_obj, 'set_content')) {

                $page_content_obj->set_content(null);
            }
            if ($tpl->q('.card-header')) {
                $tpl->q('.card-header')->set_class('k1lib-board-title', true);
            }
        }

        self::set_nav_active($nav_id);

        /**
         * ONE LINE config: less codign, more party time!
         * $co = controller_object
         */
        self::$co = new cb(K1APP_BASE_URL, $parent_class, self::$controller_db_table, self::$controller_name);
        self::$co->set_title_tag_id('#k1app-page-title');
        self::$co->set_subtitle_tag_id('.card-title');

        if (self::$co->db_table->get_state() === false) {
            die('DB table did not found: ' . $parent_class);
        }
        self::$co->set_config_from_class(
                '\k1app\table_config\\' . self::$controller_db_table .
                ($run_as_guest ? '_guest' : '')
        );

        /**
         * USE THIS IF THE TABLE NEED THE LOGIN_ID ON EVERY ROW FOR TRACKING
         */
//        self::$co->db_table->set_field_constants(['user_login' => app_session::get_user_login()]);

        static::init_board();

        static::start_board();

        static::exec_board();

        static::finish_board();
    }

    static function init_board(): void {
        self::$crud_container = self::$co->init_board();
    }

    static function start_board(): void {
        self::$co->start_board();

        // LIST
        if (self::$co->on_object_list()) {
            $read_url = url::do_url(
                    self::$co->get_controller_root_dir() . self::$co->get_board_read_url_name() . "/--rowkeys--/",
                    ["auth-code" => "--authcode--"]
            );
            self::$co->board_list()->list_object->apply_link_on_field_filter($read_url, ob::USE_LABEL_FIELDS);
        }
    }

    static function exec_board(): void {
        self::$co->exec_board();

        if (self::$co->on_object_list()) {
            if (self::$co->board_list()->list_object->html_table) {
                self::$co->board_list()->list_object->html_table->set_max_text_length_on_cell(100);
            }
        }
    }

    static function finish_board(): void {
        self::$co->finish_board();

        if (method_exists(self::$tpl, 'page_content')) {
            self::$tpl->page_content()->set_content(self::$crud_container);
        } else {
            self::$tpl->body()->set_value(self::$crud_container);
        }
    }

    static function add_related_table($table_name, $controller_url, $related_title, $return_card_only = false, $set_related_show_all_data = true, $set_related_show_new = true, $no_links = false): div|card|bool {

        if (self::$co->on_board_read()) {
            $page_heading = self::$tpl->q('.page-heading');
            if (!$return_card_only) {
                if (!empty($page_heading)) {
                    if (is_array($page_heading)) {
                        $related_div = $page_heading[0]->append_div("k1lib-crudlexs-related-data");
                    } else {
                        $related_div = $page_heading->append_div("k1lib-crudlexs-related-data");
                    }
                } else {
                    $related_div = self::$tpl->body()->append_div("k1lib-crudlexs-related-data");
                }
            }
//        ->append_div('section k1lib-crudlexs-related-data');;
//            $related_div = self::$crud_container->append_div("k1lib-crudlexs-related-data");
            /**
             * Related list
             */
            $related_db_table = new db_table(self::$app->db(), $table_name);
            self::$co->board_read_object->set_related_show_all_data($set_related_show_all_data);
            self::$co->board_read_object->set_related_show_new($set_related_show_new);
            $related_list = self::$co->board_read_object->create_related_list(
                    $related_db_table,
                    ob::USE_LABEL_FIELDS,
                    $controller_url,
                    ('\k1app\table_config\\' . $table_name)::BOARD_CREATE_URL,
                    ('\k1app\table_config\\' . $table_name)::BOARD_READ_URL,
                    ('\k1app\table_config\\' . $table_name)::BOARD_LIST_URL,
                    /**
                     * TODO: Understand why this should be false
                     */
                    TRUE,
                    FALSE,
                    NULL,
                    $no_links // nolinks
            );
//            $related_list->append_to($related_div);
            $related_card = new card($related_title, $related_list);
            if ($return_card_only) {
                return $related_card;
            }
            $related_card->append_to($related_div);
            return $related_div;
        }
        return false;
    }

    static function end(): void {
        parent::end();
        echo self::$tpl->generate();
    }

    static function set_nav_active($nav_id) {
        if (!empty($nav_id) && method_exists(self::$tpl, 'menu') && self::$tpl->menu()->q($nav_id)) {
            self::$tpl->menu()->q($nav_id)->nav_is_active();
        }
    }
}
