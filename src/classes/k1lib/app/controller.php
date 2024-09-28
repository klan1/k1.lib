<?php

namespace k1lib\app;

use k1app\template\mazer\layouts\blank;
use k1app\template\mazer\layouts\sidebar_blank;
use k1app\template\mazer\layouts\single_page;
use k1lib\app;
use k1lib\html\notifications\on_DOM as DOM_notifications;
use function k1lib\forms\check_all_incomming_vars;

class controller {

    static protected string $root_url;

    static protected blank|sidebar_blank|single_page $tpl;
    static protected app $app;
    static array $POST = [];
    

    static function on_post() {
        self::$POST = check_all_incomming_vars($_POST);
    }

    static function POST($key = null) {

        if (!empty($key) && array_key_exists($key, self::$POST)) {
            return self::$POST[$key];
        } else {
            if (count(self::$POST) > 0) {
                return self::$POST;
            } else {
                return false;
            }
        }
    }

    static function launch() {
        static::start();
        static::run();
        static::end();
    }

    static function start() {
        
    }

    static function run() {
        
    }

    static function end() {
        DOM_notifications::insert_messases_on_DOM();
    }

    static function use_tpl($tpl, $tag_id_override = null) {
        self::$tpl = $tpl;
        DOM_notifications::set_tpl($tpl, $tag_id_override);
    }

    static function tpl() {
        return self::$tpl;
    }

    public function set_tpl($tpl, $tag_id_override = null) {
        self::$tpl = $tpl;
        DOM_notifications::set_tpl($tpl, $tag_id_override);
    }

    static function link_app(app $app) {
        self::$app = $app;
    }

    static function app(): app {
        return self::$app;
    }
    public static function get_root_url(): string {
        return self::$root_url;
    }

    public static function set_root_url(string $root_url): void {
        self::$root_url = $root_url;
    }




}
