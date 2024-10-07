<?php

namespace k1lib\html;

class table extends tag {

    use append_shotcuts;

//    private $data_array = array();

    /**
     * @param String $class
     * @param String $id
     */
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("table", FALSE);
//        $this->data_array &= $data_array;
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <THEAD> HTML TAG
     * @param String $class
     * @param String $id
     * @return thead
     */
    function append_thead($class = NULL, $id = NULL) {
        $child_object = new thead($class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

    /**
     * Chains a new <TBODY> HTML TAG
     * @param String $class
     * @param String $id
     * @return tbody
     */
    function append_tbody($class = NULL, $id = NULL) {
        $child_object = new tbody($class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

