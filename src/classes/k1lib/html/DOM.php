<?php

namespace k1lib\html;

/**
 * Static Class that holds the first tag Object <html></html>. 
 */
class DOM {
//    use append_shotcuts;

    /**
     * @var html
     */
    static protected $html = NULL;

    static function start($lang = "en") {
        self::$html = new html($lang);
    }

    static function is_started() {
        if (!empty(self::$html)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function end() {
        self::$html = NULL;
    }

    /**
     * @return html
     */
    static function html() {
        return self::$html;
    }

    static function generate() {
        if (!empty(self::$html)) {
            self::$html->pre_code("<!DOCTYPE html>\n");
            return self::$html->generate();
        } else {
            return NULL;
        }
    }

    static function link_html(html $html_to_link) {
        self::$html = $html_to_link;
    }
}
