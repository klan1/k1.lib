<?php

namespace k1lib\html\bootstrap;

trait bootstrap_methods {

    protected $small = NULL;
    protected $medium = NULL;
    protected $large = NULL;

    /**
     * Will search for the $text as small-1, medium-12 as: /({$text}-[0-9]+)/
     * and replace the number part with the new number
     * @param type $attribute
     * @param type $text
     * @param type $new_number
     * @return type
     */
    public function replace_attribute_number($attribute, $text, $new_number) {
        $attribute_value = $this->get_attribute($attribute);
        $text_regexp = "/({$text}-[0-9]+)/";
        $regexp_match = [];
        if (preg_match($text_regexp, $attribute_value, $regexp_match)) {
            $string_new = str_replace($regexp_match[1], "{$text}-{$new_number}", $attribute_value);
            $this->set_attrib($attribute, $string_new);
            return $string_new;
        } else {
            $this->set_attrib($attribute, $attribute_value . " {$text}-{$new_number}");
            return $attribute_value . " {$text}-{$new_number}";
        }
    }

    public function remove_attribute_text($attribute, $text) {
        $attribute_value = $this->get_attribute($attribute);
        $text_regexp = "/(\s*$text\s*)/";
        $regexp_match = [];
        if (preg_match($text_regexp, $attribute_value, $regexp_match)) {
            $string_new = str_replace($regexp_match[1], "", $attribute_value);
            $this->set_attrib($attribute, $string_new);
            return $string_new;
        } else {
            return $attribute_value;
        }
    }

    public function append_close_button() {
        $close_button = new \k1lib\html\button(NULL, "close-button");
        $close_button->set_attrib("data-close", TRUE);
        $close_button->set_attrib("aria-label", "Close reveal");
        $close_button->append_span()->set_attrib("aria-hidden", TRUE)->set_value("&times;");
        $this->append_child_tail($close_button);
    }

    /**
     * @return \k1lib\html\div
     */
    public function align_center() {
        $this->set_attrib("class", "align-center", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function align_left() {
        $this->set_attrib("class", "align-left", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function align_right() {
        $this->set_attrib("class", "align-right", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function align_justify() {
        $this->set_attrib("class", "align-justify", TRUE);
        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function small($cols, $clear = FALSE) {
        $this->small = $cols;

        if ($clear) {
            $this->set_attrib("class", "cell small-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "small", $cols);
        }

        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function medium($cols, $clear = FALSE) {
        $this->medium = $cols;

        if ($clear) {
            $this->set_attrib("class", "cell medium-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "medium", $cols);
        }

        return $this;
    }

    /**
     * @return \k1lib\html\div
     */
    public function large($cols, $clear = FALSE) {
        $this->large = $cols;

        if ($clear) {
            $this->set_attrib("class", "cell large-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "large", $cols);
        }

        return $this;
    }

    public function get_small() {
        return $this->small;
    }

    public function get_medium() {
        return $this->medium;
    }

    public function get_large() {
        return $this->large;
    }
}
