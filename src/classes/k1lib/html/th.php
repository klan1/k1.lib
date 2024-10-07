<?php

namespace k1lib\html;

class th extends tag {

    use append_shotcuts;

    /**
     * @param String $value <TAG>$value</TAG>
     * @param String $class
     * @param String $id
     */
    function __construct($value, $class = NULL, $id = NULL) {
        parent::__construct("th", FALSE);
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

