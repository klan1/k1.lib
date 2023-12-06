<?php

namespace k1lib\html;

/**
 * P
 */
class fieldset extends tag {

    use append_shotcuts;

    function __construct($legend) {
        parent::__construct("fieldset", FALSE);
        $this->set_class("fieldset");
        $legend = new legend($legend);
        $this->append_child($legend);
    }

}

