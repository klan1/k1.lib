<?php

namespace k1lib\app;

class controller {

//    static protected $template;

    function __construct() {
        
    }

    static function run() {
        echo "Empty controller";
    }

    static function use_tpl($tpl) {
        self::$template = $tpl;
    }

    static function tpl() {
        return self::$tpl;
    }
}
