<?php

namespace k1lib\html;

/**
 * Static Class that holds the first tag Object <html></html>. 
 */
class DOM {
//    use append_shotcuts;

    /**
     * @var html_document
     */
    static protected $html_document = NULL;

    static function start($lang = "en") : html_document {
        self::$html_document = new html_document($lang);
        return self::$html_document;
    }

    static function is_started() {
        if (!empty(self::$html_document)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static function end() {
        self::$html_document = NULL;
    }

    /**
     * @return html_document
     */
    static function html_document() : html_document {
        return self::$html_document;
    }

    static function generate() {
        if (!empty(self::$html_document)) {
            self::$html_document->pre_code("<!DOCTYPE html>\n");
            return self::$html_document->generate();
        } else {
            return NULL;
        }
    }

    static function link_html(html_document $html_document_to_link) {
        self::$html_document = $html_document_to_link;
    }
}
