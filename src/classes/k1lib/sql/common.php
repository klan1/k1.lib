<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage sql
 * Common SQL utilities trait providing shared functionality for profiler and cache.
 */

namespace k1lib\sql;

/**
 * Common SQL utilities trait.
 * Provides shared enable/disable and data storage functionality.
 *
 * @package k1lib\sql
 */
trait common {

    /**
     * Enable state for the SQL utility.
     * @var bool
     */
    static protected $enabled = FALSE;

    /**
     * Counter for data entries.
     * @var int
     */
    static protected $data_count = 0;

    /**
     * SQL data storage array.
     * @var array
     */
    static protected $data = array();

    /**
     * Enables the SQL utility engine.
     */
    static public function enable() {
        self::$enabled = TRUE;
    }

    /**
     * Disables the SQL utility engine.
     */
    static public function disable() {
        self::$enabled = FALSE;
    }

    /**
     * Queries the enabled state.
     *
     * @param bool $show_error If TRUE, triggers error when disabled
     * @return bool TRUE if enabled, FALSE otherwise
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("SQL " . __CLASS__ . " system is not enabled yet", E_USER_WARNING);
        }
        return self::$enabled;
    }

    /**
     * Gets all stored SQL data.
     *
     * @return array The stored data
     */
    static public function get_data() {
        self::is_enabled(true);
        return self::$data;
    }

    /**
     * Gets the count of stored data entries.
     *
     * @return int The data count
     */
    public static function get_data_count(): int {
        return self::$data_count;
    }
}
