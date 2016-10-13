<?php

namespace k1lib\db\security;

class db_table_aliases {

    static public $aliases = [];

    static function encode($table_name) {
        if (key_exists($table_name, self::$aliases)) {
            return self::$aliases[$table_name];
        } else {
            return $table_name;
        }
    }

    static function decode($encoded_table_name) {
        $fliped_array = array_flip(self::$aliases);
        if (key_exists($encoded_table_name, $fliped_array)) {
            return $fliped_array[$encoded_table_name];
        } else {
            return $encoded_table_name;
        }
    }

}
