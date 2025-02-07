<?php

namespace k1lib\html;

class link extends tag {

    use append_shotcuts;

    public function __construct($href, $rel = 'stylesheet', $type = 'text/css') {
        parent::__construct("link");
        if (!empty($href)) {
            $this->set_attrib("rel", $rel);
            if (strtolower(substr($href, -4)) == '.css') {
                $this->set_attrib("type", "text/css");
            } else {
                $this->set_attrib("type", $type);
            }
            $this->set_attrib("href", $href);
        }
    }
}
