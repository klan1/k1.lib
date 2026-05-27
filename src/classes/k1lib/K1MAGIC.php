<?php

/**
 * k1.lib Magic Token Management
 *
 * Provides application-wide magic token/signature validation for
 * security checks and request verification.
 *
 * @license Apache-2.0
 * @package k1lib
 */

namespace k1lib;

/**
 * Magic token management class.
 *
 * Manages an application-wide security token/value used for validation
 * and signature checks. Should be set to a secure random value.
 */
class K1MAGIC {

    /**
     * Magic token value for application validation.
     *
     * @var string MD5 hash or secure token value
     */
    static public $value = "98148ef8279164d12b65ec8c9ba76c7e";

    /**
     * Get the current magic token value.
     *
     * @return string The current magic token
     */
    public static function get_value() {
        return self::$value;
    }

    /**
     * Set the magic token value.
     *
     * @param string $value Token value (recommended: MD5 or secure hash)
     * @return void
     */
    public static function set_value($value) {
        self::$value = $value;
    }
}
