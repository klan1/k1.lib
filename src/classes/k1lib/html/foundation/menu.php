<?php

namespace k1lib\html\foundation;

class menu extends \k1lib\html\ul {

    protected $type = '';
    protected $is_vertical = false;
    protected $menu_class = '';
    protected $nested_class = '';
    protected $data_attribute = '';

    function __construct($type = 'menu', $sub_class = NULL, $vertical = FALSE) {
        $this->type = $type;
        $this->is_vertical = $vertical;
        switch ($type) {
            case 'menu':
                if ($vertical) {
                    $this->menu_class = 'menu vertical';
                } else {
                    $this->menu_class = 'menu';
                }
                $this->nested_class = '';
                $this->data_attribute = '';

                break;
            case 'dropdown':
                if ($vertical) {
                    $this->menu_class = 'dropdown menu vertical';
                } else {
                    $this->menu_class = 'dropdown menu';
                }
                $this->nested_class = 'menu';
                $this->data_attribute = 'data-dropdown-menu';

                break;
            case 'drilldown':
                $vertical = TRUE;
                $this->menu_class = 'vertical menu drilldown';
                $this->nested_class = 'menu vertical nested';
                $this->data_attribute = 'data-drilldown';

                break;
            case 'accordion':
                $vertical = TRUE;
                $this->menu_class = 'vertical menu accordion-menu';
                $this->nested_class = 'menu vertical nested';
                $this->data_attribute = 'data-accordion-menu';

                break;

            default:
                break;
        }
        if (!empty($sub_class)) {
            $this->menu_class = $sub_class;
        }
        parent::__construct($this->menu_class, NULL);
        if (empty($sub_class)) {
            $this->set_attrib($this->data_attribute, TRUE);
        }
    }

    /**
     * @param string $href
     * @param string $label
     * @param string $id
     * @param string $where
     * @return \k1lib\html\li
     */
    function add_menu_item($href, $label, $id = NULL, $where_id = NULL) {
        if (!empty($where_id)) {
            $parent = $this->get_element_by_id($where_id);
//            d($parent);
        }
        if (empty($parent)) {
            $li = $this->append_li();
            $li->set_id($id);
            if (!empty($href)) {
                $a = $li->append_a($href, $label);
                $li->link_value_obj($a);
            } else {
                $li->set_value($label);
            }
        } else {
            $ul = new menu($this->type, $this->nested_class, $this->is_vertical);
            $parent->append_child($ul);
            $li = $ul->add_menu_item($href, $label, $id);
        }
        return $li;
    }

    /**
     * @param string $href
     * @param string $label
     * @return menu
     */
    function add_sub_menu($href, $label, $id = NULL, $where_id = NULL) {
        if (!empty($where_id)) {
            $parent = $this->get_element_by_id($where_id);
        }
        if (empty($parent)) {
            $li = $this->add_menu_item($href, $label, $id);
            $li->unlink_value_obj();
        } else {
            $ul = new menu($this->type, $this->nested_class, $this->is_vertical);
            $parent->append_child($ul);
            $li = $ul->add_menu_item($href, $label, $id);
        }
        $li->set_class("has-submenu", TRUE);
        $ul = new menu($this->type, $this->nested_class, $this->is_vertical);
        $li->append_child($ul);
        return $ul;
    }

    function set_active($id) {
        $tag = $this->get_element_by_id($id);
        if (!empty($tag)) {
            $tag->unlink_value_obj();
            $tag->set_class('active', TRUE);
        }
    }
}
