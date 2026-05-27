<?php

/**
 * Standard options management class
 *
 * @license Apache-2.0
 * @package k1lib
 *
 * @author      J0hnD03
 */

namespace k1lib\options;

/**
 * Options storage and retrieval
 *
 * @package k1lib\options
 */
class standar_options {

    /** @var array */
    private $options = array();
    /** @var string */
    private $option_name;

    /**
     * @param string $option_name
     */
    function __construct($option_name) {
        $this->option_name = $option_name;
    }

    /**
     * @param string $option_name
     * @param mixed $option_value
     * @return void
     */
    public function add_option($option_name, $option_value) {
        $this->options[$option_name] = $option_value;
    }

    /**
     * @param string $option_name
     * @return mixed
     */
    public function get_option($option_name) {
        if (isset($this->options[$option_name])) {
            return $this->options[$option_name];
        } else {
            return FALSE;
        }
    }
}