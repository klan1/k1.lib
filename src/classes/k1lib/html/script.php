<?php

namespace k1lib\html;

class script extends tag {

    use append_shotcuts;

    /**
     * Create a SCRIPT html tag with VALUE as data. Use $script->set_value($crs) 
     * for load a file.
     * @param String $class
     * @param String $id
     */
    function __construct($src = NULL) {
        parent::__construct("script", FALSE);
        if (!empty($src)) {
            $this->set_attrib("src", $src);
        }
    }

}

