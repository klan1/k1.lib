<?php

namespace k1lib\app;

use k1app\template\mazer\layouts\blank;

class controller {

    static protected blank $tpl;

    function __construct() {
        
    }

    static function run() {
        echo "Empty controller";
    }

    static function use_tpl($tpl) {
        self::$tpl = $tpl;
    }

    static function tpl() {
        return self::$tpl;
    }
}
