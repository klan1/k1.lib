<?php

namespace k1lib\html;

/**
 * FORM
 */
class form extends tag {

    use append_shotcuts;

    function __construct($id = "k1lib-form") {
        parent::__construct("form", FALSE);
        $this->set_id($id);
        $this->set_attrib("name", "k1lib-form");
        $this->set_attrib("method", "post");
        $this->set_attrib("autocomplete", "on");
        $this->set_attrib("enctype", "multipart/form-data");
        $this->set_attrib("novalidate", FALSE);
        $this->set_attrib("target", "_self");
    }

    /**
     * 
     * @param string $label
     * @param boolean $just_return
     * @return input
     */
    function append_submit_button($label = "Enviar", $just_return = FALSE) {
        $button = new input("submit", "submit-it", $label, "button success");
        if (!$just_return) {
            $this->append_child($button);
        }
        return $button;
    }

}

