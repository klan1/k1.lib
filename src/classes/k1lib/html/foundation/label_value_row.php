<?php

namespace k1lib\html\foundation;

class label_value_row extends grid_row {

    function __construct($label, $value, $grid_row = 0, $parent = NULL) {
        parent::__construct(2, $grid_row, $parent);
        $this->set_class('grid-margin-x', TRUE);
        $this->cell(1)->medium(3)->large(3);
        $this->cell(2)->medium(9)->large(9)->end();

        $this->cell(2)->remove_attribute_text("class", "end");

        $input_name = $this->get_name_attribute($value);

        if (method_exists($label, "generate")) {
            $small_label = clone $label;
            $this->cell(1)->append_child($label->set_class("k1lib-label-object right inline hide-for-small-only text-right"));
            $this->cell(1)->append_child($small_label->set_class("k1lib-label-object left show-for-small-only"));
        } else {
            $this->cell(1)->append_child(new \k1lib\html\label($label, $input_name, "k1lib-label-object right inline hide-for-small-only text-right"));
            $this->cell(1)->append_child(new \k1lib\html\label($label, $input_name, "k1lib-label-object left show-for-small-only"));
        }
        $this->cell(2)->set_value($value);
    }

    private function get_name_attribute($tag_object) {
        if (\method_exists($tag_object, "get_elements_by_tag")) {
            if (!isset($tag_object)) {
                $tag_object = new \k1lib\html\input("input", "dummy", NULL);
            }
            $elements = $tag_object->get_elements_by_tag("input");
            if (empty($elements)) {
                $elements = $tag_object->get_elements_by_tag("select");
            }
            if (empty($elements)) {
                $elements = $tag_object->get_elements_by_tag("textarea");
            }
            foreach ($elements as $element) {
                $name = $element->get_attribute("name");
                if ($name) {
                    return $name;
                }
            }
        }
        return NULL;
    }
}
