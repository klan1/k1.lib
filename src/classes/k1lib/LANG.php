<?php

/**
 * k1.lib Language Management
 *
 * Provides language/locale detection and switching functionality for
 * internationalized applications.
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib;

/**
 * Language management class.
 *
 * Handles application-wide language/locale settings and provides
 * methods to get and set the current language.
 */
class LANG {

    /**
     * Current application language code.
     *
     * @var string Language code (e.g., "en", "es")
     */
    static $lang = "en";

    /**
     * Get the current application language.
     *
     * @return string The current language code
     */
    public static function get_lang() {
        return self::$lang;
    }

    /**
     * Set the application language.
     *
     * @param string $lang Language code to set
     * @return void
     */
    public static function set_lang($lang) {
        self::$lang = $lang;
    }
}
