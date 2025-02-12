<?php

namespace k1lib\html\bootstrap;

use k1lib\html\div;
use k1lib\html\input as input_tag;

class input_text_with_icon extends div {

    use bootstrap_methods;

    private input_tag $input;

    public function __construct($name, $value, $icon = null, $position = 'left') {
        parent::__construct('form-group position-relative has-icon-' . $position);

        $this->input = new input_tag('text', $name, $value, 'form-control');
        $this->input->append_to($this);

        $this->append_div('form-control-icon')->append_i(NULL, $icon);
    }

    public function input(): input_tag {
        return $this->input;
    }
}
