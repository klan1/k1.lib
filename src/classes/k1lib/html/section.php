<?php

namespace k1lib\html;

class section extends tag {

    use append_shotcuts;

    function __construct($id = NULL, $class = NULL) {
        parent::__construct("section", FALSE);
        if (!empty($id)) {
            $this->set_attrib("id", $id);
        }
        if (!empty($class)) {
            $this->set_attrib("class", $class);
        }
    }

}

