<?php

namespace k1lib\html\foundation;

class top_bar extends bar {

    /**
     * @var menu
     */
    protected $menu_left = null;

    /**
     * @var \k1lib\html\span
     */
    protected $title = null;

    function __construct($id = NULL) {
        parent::__construct('top', $id);

        $this->menu_left = new menu('dropdown');
        $this->menu_left->append_to($this->left);
        $this->title = $this->menu_left->add_menu_item(NULL, NULL);
        $this->title->set_class('menu-text');
    }

    /**
     * @return \k1lib\html\span
     */
    public function title() {
        return $this->title;
    }

    /**
     * @return menu
     */
    public function menu_left() {
        return $this->menu_left;
    }
}
