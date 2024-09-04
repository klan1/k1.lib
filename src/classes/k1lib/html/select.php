<?php

namespace k1lib\html;

class select extends tag {

    use append_shotcuts;

    /**
     * @param String $name
     * @param String $class
     * @param String $id
     */
    function __construct($name, $class = NULL, $id = NULL) {
        parent::__construct("select", FALSE);
        $this->set_attrib("name", $name);
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

    /**
     * Chains a new <OPTION> HTML TAG
     * @param String $value
     * @param String $label
     * @param Boolean $selected
     * @param String $class
     * @param String $id
     * @return option
     */
    function append_option($value, $label, $selected = FALSE, $class = NULL, $id = NULL) {
        $child_object = new option($value, $label, $selected, $class, $id);
        $this->append_child($child_object);
        return $child_object;
    }

    function set_value($value, $append = FALSE) {
        $selected = $this->get_elements_by_attrib("selected");
        if (!empty($selected)) {
            $selected[0]->remove_attrib("selected");
        }
        $targuet_tag = $this->get_elements_by_attrib_value("value", $value);
        if (isset($targuet_tag[0])) {
            $targuet_tag[0]->set_attrib("selected", TRUE);
        }
    }

}

