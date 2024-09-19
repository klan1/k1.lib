<?php

namespace k1lib\html\bootstrap;

use k1lib\html\div;

class label_value_row extends div
{
    use bootstrap_methods;

    public function __construct($label, $value)
    {
        parent::__construct();
//        $this->set_class('grid-margin-x', TRUE);
        $this->medium(6)->large(12);
        $this->medium(6)->large(12);

        $form_group = $this->append_div('form-group');

        if (is_object($value) && is_subclass_of($value, 'k1lib\html\tag')) {
            $input_name = $this->get_name_attribute($value);
            $label_tag = new \k1lib\html\label($label, $input_name, "k1lib-label-object");
        } else {
            $label_tag = new \k1lib\html\label($label, null, "k1lib-label-object");

        }
        $form_group->append_child_head($label_tag);
        $form_group->set_value($value);
    }

    private function get_name_attribute($tag_object)
    {
        if (\method_exists($tag_object, "get_elements_by_tag")) {
            if (!isset($tag_object)) {
                $tag_object = new \k1lib\html\input("input", "dummy", null);
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
        return null;
    }
}
