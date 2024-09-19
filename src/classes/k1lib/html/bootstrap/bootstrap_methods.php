<?php

namespace k1lib\html\bootstrap;

use k1lib\html\button;
use k1lib\html\div;

trait bootstrap_methods {

    protected $general = NULL;
    protected $small = NULL;
    protected $medium = NULL;
    protected $large = NULL;
    protected $xlarge = NULL;
    protected $xxlarge = NULL;

    /**
     * Will search for the $text as sm-1, md-12 as: /({$text}-[0-9]+)/
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
        $close_button = new button(NULL, "btn-close");
        $close_button->set_attrib('data-bs-dismiss', 'alert');
        $close_button->set_attrib("aria-label", "Close");
        $this->append_child_tail($close_button);
    }

    /**
     * @return div
     */
    public function align_center() {
        $this->set_attrib("class", "align-center", TRUE);
        return $this;
    }

    /**
     * @return div
     */
    public function align_left() {
        $this->set_attrib("class", "align-left", TRUE);
        return $this;
    }

    /**
     * @return div
     */
    public function align_right() {
        $this->set_attrib("class", "align-right", TRUE);
        return $this;
    }

    /**
     * @return div
     */
    public function align_justify() {
        $this->set_attrib("class", "align-justify", TRUE);
        return $this;
    }

    /**
     * @return div
     */
    public function general($cols, $clear = FALSE) {
        $this->general = $cols;

        if ($clear) {
            $this->set_attrib("class", "col-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "col", $cols);
        }

        return $this;
    }
    /**
     * @return div
     */
    public function small($cols, $clear = FALSE) {
        $this->small = $cols;

        if ($clear) {
            $this->set_attrib("class", "col-sm-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "col-sm", $cols);
        }

        return $this;
    }

    /**
     * @return div
     */
    public function medium($cols, $clear = FALSE) {
        $this->medium = $cols;

        if ($clear) {
            $this->set_attrib("class", "col-md-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "col-md", $cols);
        }

        return $this;
    }

    /**
     * @return div
     */
    public function large($cols, $clear = FALSE) {
        $this->large = $cols;

        if ($clear) {
            $this->set_attrib("class", "col-lg-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "col-lg", $cols);
        }

        return $this;
    }
    /**
     * @return div
     */
    public function xlarge($cols, $clear = FALSE) {
        $this->xlarge = $cols;

        if ($clear) {
            $this->set_attrib("class", "col-xlg-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "col-xlg", $cols);
        }

        return $this;
    }
    /**
     * @return div
     */
    public function xxlarge($cols, $clear = FALSE) {
        $this->xxlarge = $cols;

        if ($clear) {
            $this->set_attrib("class", "col-xxlg-{$cols}", (!$clear));
        } else {
            $this->replace_attribute_number("class", "col-xxlg", $cols);
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
