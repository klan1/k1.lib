<?php

namespace k1lib\html\foundation;

class top_bar_ extends \k1lib\html\tag {

    use foundation_methods;
    use \k1lib\html\append_shotcuts;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var \k1lib\html\ul
     */
    protected $menu_left;

    /**
     * @var \k1lib\html\ul
     */
    protected $menu_right;

    function __construct(\k1lib\html\tag $parent) {

        $this->parent = $parent;
        $this->init_title_bar();

        parent::__construct("div", FALSE);
        $this->set_class("top-bar hide-for-print", TRUE);
        $this->set_id("responsive-menu");
        $this->append_to($parent);

        $left = $this->append_div("top-bar-left");

        $this->menu_left = new \k1lib\html\ul("dropdown menu", "k1lib-menu-left");
        $this->menu_left->append_to($left);
        $this->menu_left->set_attrib("data-dropdown-menu", TRUE);

        $li = $this->menu_left->append_li(NULL, "menu-text k1lib-title-container hide-for-small-only");
        $li->append_span("k1lib-title-1");
        $li->append_span("k1lib-title-2");
        $li->append_span("k1lib-title-3");

        $right = $this->append_div("top-bar-right");

        $this->menu_right = new \k1lib\html\ul("dropdown menu", "k1lib-menu-right");
        $this->menu_right->append_to($right);
        $this->menu_right->set_attrib("data-dropdown-menu", TRUE);
    }

    /**
     * @param string $href
     * @param string $label
     * @param string $class
     * @param string $id
     * @return \k1lib\html\a
     */
    function add_button($href, $label, $class = NULL, $id = NULL) {
        $a = new \k1lib\html\a($href, $label, "_self", "button $class", $id);
        $this->menu_right->append_li()->append_child($a);
        return $a;
    }

    /**
     * @param string $href
     * @param string $label
     * @return \k1lib\html\li
     */
    function add_menu_item($href, $label, \k1lib\html\tag $where = NULL) {
        if (empty($where)) {
            $li = $this->menu_left->append_li();
            $li->append_a($href, $label);
        } else {
            $li = $where->append_li();
            $li->append_a($href, $label);
        }
        return $li;
    }

    /**
     * @param string $href
     * @param string $label
     * @return \k1lib\html\li
     */
    function add_sub_menu(\k1lib\html\li $where) {
        $sub_ul = $where->append_ul("menu vertical");
        return $sub_ul;
    }

    function set_title($number, $value, $append = FALSE) {
        $elements = $this->parent->get_elements_by_class("k1lib-title-{$number}");
        foreach ($elements as $element) {
            $element->set_value($value, $append);
        }
    }

    function init_title_bar() {
        $title = $this->parent->append_div("title-bar")
                ->set_attrib("data-responsive-toggle", "responsive-menu")
                ->set_attrib("data-hide-for", "medium");
        $title->append_child((new \k1lib\html\button(NULL, "menu-icon"))->set_attrib("data-toggle", TRUE));

        $title_bar_title = $title->append_h1(NULL, "title-bar-title k1lib-title-container");
        $title_bar_title->set_attrib("style", "font-size:inherit;display:inline");
        $title_bar_title->append_span("k1lib-title-1");
        $title_bar_title->append_span("k1lib-title-2");
        $title_bar_title->append_span("k1lib-title-3");
    }

    /**
     * @return div
     */
    function menu_left() {
        return $this->menu_left;
    }

    /**
     * @return div
     */
    function menu_right() {
        return $this->menu_right;
    }

    /**
     * @return tag
     */
    function get_parent() {
        return $this->parent;
    }
}
