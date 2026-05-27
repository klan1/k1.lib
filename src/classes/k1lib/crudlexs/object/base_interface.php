<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage crudlexs\object
 * Base interface for CRUDLEXS objects defining required methods for HTML rendering.
 */

namespace k1lib\crudlexs\object;

/**
 * Interface for CRUD objects that generate HTML representations.
 * All CRUD object types must implement the do_html_object method.
 *
 * @package k1lib\crudlexs\object
 */
interface base_interface {

    /**
     * Generates and returns the HTML representation of the object.
     *
     * @return mixed HTML object representation
     */
    public function do_html_object();
}
