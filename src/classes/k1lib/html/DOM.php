<?php

namespace k1lib\html;

/**
 * K1.lib V1 compatibility layer
 */

/**
 * Static Class that holds the first tag Object <html></html>. 
 */
class DOM {
//    use append_shotcuts;

    /**
     * @var html_document
     */
    static protected html_document $html;

    static function start(html_document $tpl) {
        self::$html = $tpl;
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
     * @return html_document
     */
    static function html(): html_document {
        return self::html_document();
    }

    static function html_document(): html_document {
        return self::$html;
    }

    static function generate() {
        return self::$html->generate();
    }

    static function link_html(html $html_to_link) {
        trigger_error('Do no do this ' . __METHOD__ . ' at ' . __CLASS__, E_USER_ERROR);
    }
}
