<?php

namespace k1lib\html;

class small extends tag {

    use append_shotcuts;

    /**
     * Create a SMALL html tag with VALUE as data. Use $small->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("small", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

