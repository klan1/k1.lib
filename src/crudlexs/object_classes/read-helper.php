<?php

namespace k1lib\crudlexs;

class read_helper {

//    static $do_fk_search_tool = TRUE;
//    static $url_to_search_fk_data = APP_URL . "general-utils/select-row-keys/";
//    static $url_to_send_row_keys_fk_data = APP_URL . "general-utils/send-row-keys/";
//    static $main_css = "";
//    static private $fk_fields_to_skip = [];
//    static public $boolean_true = NULL;
//    static public $boolean_false = NULL;

    static function password_type($value) {
        return $value;
    }

    static function enum_type($value) {
        return $value;
    }

    static function text_type($value) {
        return $value;
    }

    static function file_upload($value) {
        return $value;
    }

    static function boolean_type($value) {

        // Row to apply is constant coz this is CREATE or EDIT and there is allways just 1 set of data to manipulate.
//        d(self::$boolean_true, true);
        if (self::$boolean_true === NULL) {
//            d('yes');
            self::$boolean_true = \k1lib\common_strings::$yes;
        }
//        d(self::$boolean_false, true);
        if (self::$boolean_false === NULL) {
//            d('no');
            self::$boolean_false = \k1lib\common_strings::$no;
        }
        return $value;
    }

    static function default_type($value) {
        return $value;
    }
}
