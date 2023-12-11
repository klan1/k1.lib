<?php

namespace k1lib\sql;

class common {

    /**
     * Enable state
     * @var Boolean 
     */
    static protected $enabled = FALSE;

    /**
     *
     * @var Int 
     */
    static protected $data_count = 0;

    /**
     * Stores the SQL data
     * @var Array
     */
    static protected $data = array();

    /**
     * Enable the engenie
     */
    static public function enable() {
        self::$enabled = TRUE;
    }

    /**
     * Disable the engenie
     */
    static public function disable() {
        self::$enabled = FALSE;
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("SQL " . __CLASS__ . " system is not enabled yet", E_USER_WARNING);
        }
        return self::$enabled;
    }

    static public function get_data() {
        self::is_enabled(true);
        return self::$data;
    }

    public static function get_data_count(): int {
        return self::$data_count;
    }
}
