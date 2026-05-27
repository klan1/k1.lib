<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage db\security
 * Database table name aliasing for security and URL encoding purposes.
 */

namespace k1lib\db\security;

/**
 * Database table alias manager.
 * Provides encoding and decoding of table names for security.
 *
 * @package k1lib\db\security
 */
class db_table_aliases {

    /**
     * Array of table name aliases.
     * @var array
     */
    static public $aliases = [];

    /**
     * Encodes a table name to its alias if available.
     *
     * @param string $table_name The original table name
     * @return string The alias if exists, otherwise the original table name
     */
    static function encode($table_name) {
        if (key_exists($table_name, self::$aliases)) {
            return self::$aliases[$table_name];
        } else {
            return $table_name;
        }
    }

    /**
     * Decodes an alias back to the original table name.
     *
     * @param string $encoded_table_name The encoded table name
     * @return string The original table name if exists, otherwise the encoded name
     */
    static function decode($encoded_table_name) {
        $fliped_array = array_flip(self::$aliases);
        if (key_exists($encoded_table_name, $fliped_array)) {
            return $fliped_array[$encoded_table_name];
        } else {
            return $encoded_table_name;
        }
    }
}
