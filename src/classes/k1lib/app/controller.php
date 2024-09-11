<?php

namespace k1lib\app;

use k1app\template\mazer\layouts\blank;
use k1lib\app;
use k1lib\html\notifications\on_DOM;
use function k1lib\forms\check_all_incomming_vars;

class controller {

    static protected blank $tpl;
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
        on_DOM::insert_messases_on_DOM();
    }

    static function use_tpl($tpl) {
        self::$tpl = $tpl;
    }

    static function tpl() {
        return self::$tpl;
    }

    static function link_app(app $app) {
        self::$app = $app;
    }

    static function app(): app {
        return self::$app;
    }
}
