<?php

namespace k1lib\html;

class tr extends tag {

    use append_shotcuts;

    /**
     * @param string $class
     * @param string $id
     */
    function __construct($class = NULL, $id = NULL) {
        parent::__construct("tr", FALSE);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <TH> HTML TAG
     * @param string $value <TAG>$value</TAG>
     * @param string $class
     * @param string $id
     * @return th
     */
    function append_th($value, $class = NULL, $id = NULL) {
        $child_object = new th($value, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

    /**
     * Chains a new <TD> HTML TAG
     * @param string $value <TAG>$value</TAG>
     * @param string $class
     * @param string $id
     * @return td
     */
    function append_td($value, $class = NULL, $id = NULL) {
        $child_object = new td($value, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

}

