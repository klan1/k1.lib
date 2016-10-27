<?php

namespace k1lib;

class K1MAGIC {

    /**
     *
     * @var string
     */
    static public $value = "98148ef8279164d12b65ec8c9ba76c7e";

    /**
     * 
     * @return string
     */
    public static function get_value() {
        return self::$value;
    }

    /**
     * Set the value, please use an MD5 value here to increase security.
     * @param string
     */
    public static function set_value($value) {
        self::$value = $value;
    }

}

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

class PROFILER {

    static private $init = 0;
    static private $finish = 0;
    static private $run_time = 0;

    static function start() {
        self::$init = microtime(TRUE);
    }

    static function end() {
        self::$finish = microtime(TRUE);
        self::$run_time = round((microtime(TRUE) - self::$init), 5);
        return self::$run_time;
    }

}
