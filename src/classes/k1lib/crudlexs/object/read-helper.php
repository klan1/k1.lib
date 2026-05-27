<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage crudlexs\object
 * Helper functions for type-specific field value processing during read operations.
 */

namespace k1lib\crudlexs\object;

use k1lib\common_strings;

/**
 * Helper class for processing field values during read operations.
 * Provides static methods for formatting different data types.
 *
 * @package k1lib\crudlexs\object
 */
class read_helper
{

    /**
     * Boolean true label for translations.
     * @var string|null
     */
    static public $boolean_true = NULL;

    /**
     * Boolean false label for translations.
     * @var string|null
     */
    static public $boolean_false = NULL;

    /**
     * Processes password field type values.
     *
     * @param mixed $value The field value
     * @return mixed The processed value
     */
    static function password_type($value)
    {
        return $value;
    }

    /**
     * Processes enum field type values.
     *
     * @param mixed $value The field value
     * @return mixed The processed value
     */
    static function enum_type($value)
    {
        return $value;
    }

    /**
     * Processes text field type values.
     *
     * @param mixed $value The field value
     * @return mixed The processed value
     */
    static function text_type($value)
    {
        return $value;
    }

    /**
     * Processes file upload field type values.
     *
     * @param mixed $value The field value
     * @return mixed The processed value
     */
    static function file_upload($value)
    {
        return $value;
    }

    /**
     * Processes boolean field type values with translation.
     *
     * @param mixed $value The field value
     * @return string Translated "Yes" or "No" based on value
     */
    static function boolean_type($value)
    {

        $t = \k1lib\lang\translator::getInstance();
        if (self::$boolean_true === NULL) {
            self::$boolean_true = $t->t('k1lib', '', 'Yes');
        }
        if (self::$boolean_false === NULL) {
            self::$boolean_false = $t->t('k1lib', '', 'No');
        }
        return $value;
    }

    /**
     * Default processor for field type values.
     *
     * @param mixed $value The field value
     * @return mixed The processed value
     */
    static function default_type($value)
    {
        return $value;
    }
}
