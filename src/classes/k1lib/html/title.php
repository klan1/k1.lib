<?php

namespace k1lib\html;

class title extends tag {

    use append_shotcuts;

    function __construct() {
        parent::__construct("title", FALSE);
    }

}

