<?php

namespace k1lib\html;

class strong extends tag {

    use append_shotcuts;

    /**
     * Create a STRONG html tag with VALUE as data. Use $strong->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($value = '', $class = NULL, $id = NULL) {
        parent::__construct("strong", FALSE);
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

