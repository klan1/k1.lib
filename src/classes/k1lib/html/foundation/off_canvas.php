<?php

namespace k1lib\html\foundation;

class off_canvas extends \k1lib\html\tag {

    use foundation_methods;
    use \k1lib\html\append_shotcuts;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var \k1lib\html\div
     */
    protected $left = null;

    /**
     * @var menu
     */
    protected $menu_left = null;

    /**
     * @var menu
     */
    protected $menu_left_head = null;

    /**
     * @var menu
     */
    protected $menu_left_tail = null;

    /**
     * @var \k1lib\html\div
     */
    protected $right = null;

    /**
     * @var menu
     */
    protected $rigth_menu = null;

    /**
     * @var \k1lib\html\div
     */
    public $content = null;

    public function __construct(\k1lib\html\body $parent = NULL) {
        $this->parent = $parent;
    }

    /**
     * @return \k1lib\html\div
     */
    public function left() {
        if (empty($this->left)) {
            $this->left = new \k1lib\html\div("off-canvas position-left", 'offCanvasLeft');
            $this->left->set_attrib('data-off-canvas', TRUE);
            $this->left->append_to($this->parent);
        }
        return $this->left;
    }

    /**
     * @return menu
     */
    public function menu_left() {
        if (empty($this->menu_left)) {
            $this->menu_left = new menu('accordion');
            $this->menu_left->set_id('menu-left');
            $this->left->append_child($this->menu_left);
        }
        return $this->menu_left;
    }

    /**
     * @return menu
     */
    public function menu_left_head() {
        if (empty($this->menu_left_head)) {
            $this->menu_left_head = new menu('accordion');
            $this->menu_left_head->set_id('menu-left-head');
            $this->menu_left_head->set_class('head', TRUE);
            $this->left->append_child_head($this->menu_left_head);
        }
        return $this->menu_left_head;
    }

    /**
     * @return menu
     */
    public function menu_left_tail() {
        if (empty($this->menu_left_tail)) {
            $this->menu_left_tail = new menu('accordion');
            $this->menu_left_tail->set_id('menu-left-tail');
            $this->menu_left_tail->set_class('tail', TRUE);
            $this->left->append_child_tail($this->menu_left_tail);
        }
        return $this->menu_left_tail;
    }

    /**
     * @return \k1lib\html\div
     */
    public function right() {
        if (empty($this->right)) {
            $this->right = new \k1lib\html\div("off-canvas position-right", 'offCanvasRight');
            $this->right->set_attrib('data-off-canvas', TRUE);
            $this->right->set_attrib('data-position', 'right');
            $this->right->append_to($this->parent);
        }
        return $this->right;
    }

    /**
     * @return \k1lib\html\div
     */
    public function content() {
        if (empty($this->content)) {
            $this->content = new \k1lib\html\div("off-canvas-content");
            $this->content->set_attrib('data-off-canvas-content', TRUE);
            $this->content->append_to($this->parent);
        }
        return $this->content;
    }
}
