<?php

/**
 * HTML Classes for general propouses use
 */

namespace k1lib\html {

    class html_dummy_class extends html_tag_base {

        /**
         * 
         * @param string $html_code
         */
        public function __construct($html_code) {
            $this->value = $html_code;
        }

    }

    class a_tag extends html_tag {

        /**
         * 
         * @param String $src
         * @param String $label <TAG>$label</TAG>
         * @param String $target
         * @param String $alt
         * @param String $class
         * @param String $id
         */
        function __construct($href, $label, $target = "", $alt = "", $class = "", $id = "") {
            parent::__construct("a", FALSE);
//        $this->data_array &= $data_array;
            $this->set_attrib("href", $href);
            $this->set_value($label);
            $this->set_attrib("target", $target);
            $this->set_attrib("alt", $alt);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

    }

    class div_tag extends html_tag {

        /**
         * Create a DIV html tag with VALUE as data. Use $div->set_value($data)
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("div", FALSE);
//        $this->data_array &= $data_array;
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

    }

    class span_tag extends html_tag {

        /**
         * Create a SPAN html tag with VALUE as data. Use $span->set_value($data)
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("span", FALSE);
//        $this->data_array &= $data_array;
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

    }

    class table_tag extends html_tag {

//    private $data_array = array();
        /**
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("table", FALSE);
//        $this->data_array &= $data_array;
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

        /**
         * Chains a new <THEAD> HTML TAG
         * @param String $class
         * @param String $id
         * @return \k1lib\html\thead_tag
         */
        function &append_thead($class = "", $id = "") {
            $child_object = new thead_tag($class, $id);
            parent::append_child($child_object);
            return $child_object;
        }

        /**
         * Chains a new <TBODY> HTML TAG
         * @param String $class
         * @param String $id
         * @return \k1lib\html\tbody_tag
         */
        function &append_tbody($class = "", $id = "") {
            $child_object = new tbody_tag($class, $id);
            parent::append_child($child_object);
            return $child_object;
        }

    }

    class thead_tag extends html_tag {

        /**
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("thead", FALSE);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

        /**
         * Chains a new <TR> HTML TAG
         * @param String $class
         * @param String $id
         * @return \k1lib\html\tr_tag
         */
        function append_tr($class = "", $id = "") {
            $child_object = new tr_tag($class, $id);
            parent::append_child($child_object);
            return $child_object;
        }

    }

    class tbody_tag extends html_tag {

        /**
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("tbody", FALSE);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

        /**
         * Chains a new <TR> HTML TAG
         * @param String $class
         * @param String $id
         * @return \k1lib\html\tr_tag
         */
        function append_tr($class = "", $id = "") {
            $child_object = new tr_tag($class, $id);
            parent::append_child($child_object);
            return $child_object;
        }

    }

    class tr_tag extends html_tag {

        /**
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("tr", FALSE);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

        /**
         * Chains a new <TH> HTML TAG
         * @param String $value <TAG>$value</TAG>
         * @param String $class
         * @param String $id
         * @return \k1lib\html\th_tag
         */
        function append_th($value, $class = "", $id = "") {
            $child_object = new th_tag($value, $class, $id);
            parent::append_child($child_object);
            return $child_object;
        }

        /**
         * Chains a new <TD> HTML TAG
         * @param String $value <TAG>$value</TAG>
         * @param String $class
         * @param String $id
         * @return \k1lib\html\td_tag
         */
        function append_td($value, $class = "", $id = "") {
            $child_object = new td_tag($value, $class, $id);
            parent::append_child($child_object);
            return $child_object;
        }

    }

    class th_tag extends html_tag {

        /**
         * @param String $value <TAG>$value</TAG>
         * @param String $class
         * @param String $id
         */
        function __construct($value, $class = "", $id = "") {
            parent::__construct("th", FALSE);
            $this->set_value($value);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

    }

    class td_tag extends html_tag {

        /**
         * @param String $value <TAG>$value</TAG>
         * @param String $class
         * @param String $id
         */
        function __construct($value, $class = "", $id = "") {
            parent::__construct("td", FALSE);
            $this->set_value($value);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

    }

    class input_tag extends html_tag {

        /**
         * @param String $type Should be HTML standars: text, button.... 
         * @param String $name
         * @param String $value <TAG value='$value' />
         * @param String $class
         * @param String $id
         */
        function __construct($type, $name, $value, $class = "", $id = "") {
            parent::__construct("input", TRUE);
            $this->set_attrib("type", $type);
            $this->set_attrib("name", $name);
            $this->set_value($value);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

    }

    class label_tag extends html_tag {

        /**
         * @param String $label <TAG>$value</TAG>
         * @param String $for
         * @param String $class
         * @param String $id
         */
        function __construct($label, $for, $class = "", $id = "") {
            parent::__construct("label", FALSE);
            $this->set_value($label);
            $this->set_attrib("for", $for);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

    }

    class select_tag extends html_tag {

        /**
         * @param String $name
         * @param String $class
         * @param String $id
         */
        function __construct($name, $class = "", $id = "") {
            parent::__construct("select", FALSE);
            $this->set_attrib("name", $name);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

        /**
         * Chains a new <OPTION> HTML TAG
         * @param String $value
         * @param String $label
         * @param Boolean $selected
         * @param String $class
         * @param String $id
         * @return \k1lib\html\option_tag
         */
        function append_option($value, $label, $selected = FALSE, $class = "", $id = "") {
            $child_object = new option_tag($value, $label, $selected, $class, $id);
            parent::append_child($child_object);
            return $child_object;
        }

    }

    class option_tag extends html_tag {

        /**
         * @param String $value <TAG value='$value' />
         * @param String $label <TAG>$label</TAG>
         * @param Boolean $selected
         * @param String $class
         * @param String $id
         */
        function __construct($value, $label, $selected = FALSE, $class = "", $id = "") {
            parent::__construct("option", FALSE);
            $this->set_value($label);
            $this->set_attrib("value", $value);
            $this->set_attrib("selected", $selected);
            $this->set_attrib("class", $class, TRUE);
            $this->set_attrib("id", $id);
        }

    }

    /**
     * FORM
     */
    class form_tag extends html_tag {

        function __construct($id = "k1-form") {
            parent::__construct("form", FALSE);
            $this->set_attrib("id", $id);
            $this->set_attrib("name", "k1-form");
            $this->set_attrib("method", "post");
            $this->set_attrib("autocomplete", "yes");
            $this->set_attrib("enctype", "application/x-www-form-urlencoded");
            $this->set_attrib("novalidate", FALSE);
            $this->set_attrib("target", "_self");
        }

        function append_submit_button($label = "Enviar", $just_return = FALSE) {
            $button = new input_tag("submit", "submit-it", $label, "button success");
            if (!$just_return) {
                $this->append_child($button);
            }
            return $button;
        }

    }

    /**
     * P
     */
    class p_tag extends html_tag {

        function __construct($class = "") {
            parent::__construct("p", FALSE);
            $this->set_attrib("class", $class);
        }

    }

    /**
     * P
     */
    class fieldset_tag extends html_tag {

        function __construct($legend) {
            parent::__construct("fieldset", FALSE);
            $this->set_attrib("class", "fieldset");
            $legend = new legend_tag($legend);
            $this->append_child($legend);
        }

    }

    class legend_tag extends html_tag {

        function __construct($value) {
            parent::__construct("legend", FALSE);
            $this->set_value($value);
        }

    }

}