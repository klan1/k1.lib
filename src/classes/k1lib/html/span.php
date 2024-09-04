<?php

namespace k1lib\html;

class span extends tag {

    use append_shotcuts;

    /**
     * Create a SPAN html tag with VALUE as data. Use $span->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("span", FALSE);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

