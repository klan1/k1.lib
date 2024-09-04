<?php

namespace k1lib\html;

class iframe extends tag {

    use append_shotcuts;

    function __construct($src, $class = NULL, $id = NULL) {
        parent::__construct("iframe", IS_NOT_SELF_CLOSED);
        $this->set_value($src);
        $this->set_class($class);
        $this->set_id($id);
    }

    public function set_value($value, $append = FALSE) {
        $this->set_attrib("src", $value);
        return $this;
    }

}

