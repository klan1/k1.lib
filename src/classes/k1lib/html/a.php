<?php

namespace k1lib\html;

class a extends tag {

    use append_shotcuts;

    function __construct($href, $label, $target = NULL, $class = NULL, $id = NULL) {
        parent::__construct("a", FALSE);
        if (!empty($href)) {
            $this->set_attrib("href", $href);
        }
        if (!empty($label)) {
            $this->set_value($label);
        }
        if (!empty($target)) {
            $this->set_attrib("target", $target);
        }
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

