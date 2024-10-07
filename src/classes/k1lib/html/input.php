<?php

namespace k1lib\html;

class input extends tag {

    use append_shotcuts;

    /**
     * @param String $type Should be HTML standars: text, button.... 
     * @param String $name
     * @param String $value <TAG value='$value' />
     * @param String $class
     * @param String $id
     */
    function __construct($type, $name, $value, $class = NULL, $id = NULL) {
        parent::__construct("input", TRUE);
        $this->set_attrib("type", $type);
        $this->set_attrib("name", $name);
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

