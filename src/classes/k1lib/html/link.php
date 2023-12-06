<?php

namespace k1lib\html;

class link extends tag {

    use append_shotcuts;

    function __construct($href) {
        parent::__construct("link");
        if (!empty($href)) {
            $this->set_attrib("rel", "stylesheet");
            if (strtolower(substr($href, -4)) == '.css') {
                $this->set_attrib("type", "text/css");
            }
            $this->set_attrib("href", $href);
        }
    }

}

