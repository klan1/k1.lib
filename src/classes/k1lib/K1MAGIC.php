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