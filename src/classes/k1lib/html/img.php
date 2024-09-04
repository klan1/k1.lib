<?php

namespace k1lib\html;

class img extends tag {

    use append_shotcuts;

    function __construct($src = NULL, $alt = "Image", $class = NULL, $id = NULL) {
        parent::__construct("img", TRUE);
        $this->set_attrib("src", $src);
        $this->set_attrib("alt", $alt);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    function set_value($value, $append = FALSE) {
        $this->set_attrib("alt", $value, $append);
    }

}

