<?php

namespace k1lib\html;

class textarea extends tag {

    use append_shotcuts;

    /**
     * 
     * @param string $name
     * @param string $class
     * @param string $id
     */
    function __construct($name, $class = NULL, $id = NULL) {
        parent::__construct("textarea", FALSE);
        $this->set_attrib("name", $name);
        $this->set_class($class, TRUE);
        $this->set_id($id);
        $this->set_attrib("rows", 10);
    }

}

