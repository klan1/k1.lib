<?php

namespace k1lib\db\security;

class db_table_aliases {

    static public $aliases = [
        "agencies" => "table0",
        "users" => "table1",
        "locations" => "table2",
        "departments" => "table3",
        "job_titles" => "table4",
        "clients" => "table5",
        "contacts" => "table6",
        "contracts" => "table7",
        "projects" => "table8",
        "task_orders" => "table9",
    ];

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
