<?php

namespace k1lib\html;

class meta extends tag {

    use append_shotcuts;

    function __construct($name = NULL, $content = NULL) {
        parent::__construct("meta", TRUE);
        if (!empty($name)) {
            $this->set_attrib("name", $name);
        }
        if (!empty($content)) {
            $this->set_attrib("content", $content);
        }
    }

}

