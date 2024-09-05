<?php

namespace k1lib\html;

class label extends tag {

    use append_shotcuts;

    /**
     * @param string $label <TAG>$value</TAG>
     * @param string $for
     * @param string $class
     * @param string $id
     */
    function __construct($label, $for, $class = NULL, $id = NULL) {
        parent::__construct("label", FALSE);
        $this->set_value($label);
        if (!empty($for)) {
            $this->set_attrib("for", $for);
        }
        $this->set_class($class, TRUE);
        $this->set_id($id);
    }

}

