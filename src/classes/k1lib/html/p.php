<?php

namespace k1lib\html;

/**
 * P
 */
class p extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("p", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

