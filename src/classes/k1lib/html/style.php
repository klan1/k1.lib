<?php

namespace k1lib\html;

class style extends tag {

    use append_shotcuts;

    /**
     * Create a SCRIPT html tag with VALUE as data. Use $style->set_value($crs) 
     * for load a file.
     * @param String $class
     * @param String $id
     */
    function __construct($style = NULL) {
        parent::__construct("style", FALSE);
        if (!empty($style)) {
            $this->set_value($style);
        }
    }

}

