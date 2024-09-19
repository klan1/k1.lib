<?php

namespace k1lib\html;

/**
 * NAV
 */
class nav extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("nav", FALSE);
        $this->set_attrib('aria-label', $value);
        $this->set_class($class);
        $this->set_id($id);
    }
}
