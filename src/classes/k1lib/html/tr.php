<?php

namespace k1lib\html;

class tr extends tag {

    use append_shotcuts;

    /**
     * @param String $class
     * @param String $id
     */
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("tr", FALSE);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <TH> HTML TAG
     * @param String $value <TAG>$value</TAG>
     * @param String $class
     * @param String $id
     * @return th
     */
    function append_th($value, $class = NULL, $id = NULL) {
        $child_object = new th($value, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

    /**
     * Chains a new <TD> HTML TAG
     * @param String $value <TAG>$value</TAG>
     * @param String $class
     * @param String $id
     * @return td
     */
    function append_td($value, $class = NULL, $id = NULL) {
        $child_object = new td($value, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

