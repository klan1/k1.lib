<?php

namespace k1lib\html;

class li extends tag {

    use append_shotcuts;

    /**
     * Create a LI html tag with VALUE as data. Use $div->set_value($data)
     * @param String $class
     * @param String $id
     */
    function __construct($value = NULL, $class = NULL, $id = NULL) {
        parent::__construct("li", FALSE);
//        $this->data_array &= $data_array;
        $this->set_value($value);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return ul
     */
    function append_ul($class = NULL, $id = NULL) {
        $new = new ul($class, $id);
        $this->append_child($new);
        return $new;
    }

    /**
     * 
     * @param string $class
     * @param string $id
     * @return div
     */
    function append_ol($class = NULL, $id = NULL) {
        $new = new ol($class, $id);
        $this->append_child($new);
        return $new;
    }

}

