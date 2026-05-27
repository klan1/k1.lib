<?php

/**
 * SQL defaults for k1lib database operations
 *
 * @license Apache-2.0
 * @package k1lib
 *
 * @author      j0hnd003
 */

namespace k1lib\db;

/**
 * SQL default values and validation rules
 *
 * @package k1lib\db
 */
class sql_defaults {

    /**
     * @var array MySQL max length defaults by type
     */
    static protected $mysql_max_length_defaults = array(
        'char' => 255,
        'varchar' => 255,
        'text' => (10 * 1204 * 1204), // 10485760 bytes or 110 Megabytes
        'date' => 10,
        'time' => 8,
        'datetime' => 19,
        'timestamp' => 19,
        'tinyint' => 3,
        'smallint' => 5,
        'mediumint' => 7,
        'int' => 10,
        'bigint' => 19,
        'float' => 34,
        'double' => 64,
        'enum' => NULL,
        'point' => 9999,
    );
    /**
     * @var array MySQL default validation rules by type
     */
    static protected $mysql_default_validation = array(
        'char' => 'mixed-symbols',
        'varchar' => 'mixed-symbols',
        'text' => 'mixed-symbols',
        'date' => 'date',
        'time' => 'time',
        'datetime' => 'datetime',
        'timestamp' => 'numbers',
        'tinyint' => 'numbers',
        'smallint' => 'numbers',
        'mediumint' => 'numbers',
        'int' => 'numbers',
        'bigint' => 'numbers',
        'decimal' => 'decimals',
        'float' => 'decimals',
        'double' => 'decimals',
        'enum' => 'options',
        'set' => 'options',
        'point' => 'mixed-symbols',
    );
    /**
     * @var array k1lib field config options defaults
     */
    static protected $k1lib_field_config_options_defaults = [
        'label' => null,
        'alias' => null,
        'validation' => null,
        'placeholder' => null,
        'icon' => null,
        'show-create' => TRUE,
        'show-read' => TRUE,
        'show-update' => TRUE,
        'show-list' => TRUE,
        'show-related' => TRUE,
        'show-search' => TRUE,
        'show-export' => TRUE,
        'label-field' => null,
        'file-max-size' => null,
        'file-type' => null,
        'min' => 1,
        'sql' => null,
    ];

    /**
     * @return array
     */
    public static function get_mysql_max_length_defaults() {
        return self::$mysql_max_length_defaults;
    }

    /**
     * @return array
     */
    public static function get_mysql_default_validation() {
        return self::$mysql_default_validation;
    }

    /**
     * @return array
     */
    public static function get_k1lib_field_config_options_defaults() {
        return self::$k1lib_field_config_options_defaults;
    }
}
