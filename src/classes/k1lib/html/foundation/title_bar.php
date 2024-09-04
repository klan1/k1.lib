<?php

namespace k1lib\html\foundation;

class title_bar extends bar {

    /**
     * @var \k1lib\html\button
     */
    protected $left_button = null;

    /**
     * @var \k1lib\html\span
     */
    protected $title = null;

    function __construct($id = NULL) {
        parent::__construct('title', $id);
        $this->left_button = new \k1lib\html\button(NULL, "menu-icon");
        $this->left_button->append_to($this->left);

        $this->title = new \k1lib\html\span("title-bar-title");
        $this->title->append_to($this->left);
    }

    /**
     * @return \k1lib\html\span
     */
    public function title() {
        return $this->title;
    }

    /**
     * @return \k1lib\html\button
     */
    public function left_button() {
        return $this->left_button;
    }
}
