<?php

namespace k1lib;

class LANG {

    /**
     *
     * @var string
     */
    static $lang = "en";

    /**
     * 
     * @return string
     */
    public static function get_lang() {
        return self::$lang;
    }

    /**
     * 
     * @param string $lang
     */
    public static function set_lang($lang) {
        self::$lang = $lang;
    }
}
