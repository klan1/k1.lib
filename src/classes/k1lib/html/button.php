<?php

namespace k1lib\html;

class button extends tag {

    use append_shotcuts;

    function __construct($value = NULL, $class = NULL, $id = NULL, $type = "button") {
        parent::__construct("button", FALSE);
        $this->set_value($value);
        $this->set_class($class);
        $this->set_id($id);
        if (!empty($type)) {
            $this->set_attrib("type", $type);
        }
    }

}

