<?php

namespace k1lib\html;

class input extends tag {

    use append_shotcuts;

    /**
     * @param string $type Should be HTML standars: text, button.... 
     * @param string $name
     * @param string $value <TAG value='$value' />
     * @param string $class
     * @param string $id
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
