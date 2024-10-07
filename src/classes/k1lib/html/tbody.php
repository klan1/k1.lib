<?php

namespace k1lib\html;

class tbody extends tag {

    use append_shotcuts;

    /**
     * @param String $class
     * @param String $id
     */
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("tbody", FALSE);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <TR> HTML TAG
     * @param String $class
     * @param String $id
     * @return tr
     */
    function append_tr($class = NULL, $id = NULL) {
        $child_object = new tr($class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

