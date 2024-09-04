<?php

namespace k1lib\html;

/**
 * h4
 */
class h4 extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("h4", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

