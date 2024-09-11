<?php

namespace k1lib\app;

use k1app\template\mazer\layouts\blank;
use k1lib\app;

class controller {

    static protected blank $tpl;
    static protected app $app;
    static array $POST = [];

    static function on_post() {
        self::$POST = \k1lib\forms\check_all_incomming_vars($_POST);
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

    static function run() {
        
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
