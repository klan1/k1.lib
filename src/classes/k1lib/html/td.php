<?php

namespace k1lib\html;

class td extends tag {

    use append_shotcuts;

    /**
     * @param string $value <TAG>$value</TAG>
     * @param string $class
     * @param string $id
     */
    function __construct($value, $class = NULL, $id = NULL) {
        parent::__construct("td", FALSE);
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }
}
