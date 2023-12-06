<?php

namespace k1lib\html;

class pre extends tag {

    use append_shotcuts;

    function __construct($value, $class = NULL, $id = NULL) {
        parent::__construct("pre", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
    }

}

