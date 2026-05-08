<?php

namespace k1lib\crudlexs\object;

use k1lib\common_strings;

class read_helper
{

    //    static $do_fk_search_tool = TRUE;
    //    static $url_to_search_fk_data = \k1app\K1APP_URL . "general-utils/select-row-keys/";
    //    static $url_to_send_row_keys_fk_data = \k1app\K1APP_URL . "general-utils/send-row-keys/";
    //    static $main_css = "";
    //    static private $fk_fields_to_skip = [];
    //    static public $boolean_true = NULL;
    //    static public $boolean_false = NULL;

    static function password_type($value)
    {
        return $value;
    }

    static function enum_type($value)
    {
        return $value;
    }

    static function text_type($value)
    {
        return $value;
    }

    static function file_upload($value)
    {
        return $value;
    }

    static function boolean_type($value)
    {

        // Row to apply is constant coz this is CREATE or EDIT and there is allways just 1 set of data to manipulate.
        $t = \k1lib\lang\translator::getInstance();
        if (self::$boolean_true === NULL) {
            self::$boolean_true = $t->t('k1lib', '', 'Yes');
        }
        if (self::$boolean_false === NULL) {
            self::$boolean_false = $t->t('k1lib', '', 'No');
        }
        return $value;
    }

    static function default_type($value)
    {
        return $value;
    }
}
