<?php

namespace k1lib\html;

class div extends tag {

    use append_shotcuts;

    /**
     * Create a DIV html tag with VALUE as data. Use $div->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("div", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

