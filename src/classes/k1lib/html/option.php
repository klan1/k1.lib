<?php

namespace k1lib\html;

class option extends tag {

    use append_shotcuts;

    /**
     * @param string $value <TAG value='$value' />
     * @param string $label <TAG>$label</TAG>
     * @param bool $selected
     * @param string $class
     * @param string $id
     */
    function __construct($value, $label, $selected = FALSE, $class = NULL, $id = NULL) {
        parent::__construct("option", FALSE);
        $this->set_value($label);
        $this->set_attrib("value", $value);
        if ($selected) {
            $this->set_attrib("selected", $selected);
        }
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }
}
