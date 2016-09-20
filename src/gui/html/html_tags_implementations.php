<?php

/**
 * HTML Classes for general propouses use
 */

namespace k1lib\html {

    class DOM {

        /**
         * @var \k1lib\html\html_document_tag
         */
        static protected $html;

        static function start($lang = "en") {
            self::$html = new html_document_tag($lang);
        }

        /**
         * @return \k1lib\html\html_document_tag
         */
        static function html() {
            return self::$html;
        }

    }

    class html_document_tag extends html_tag {

        /**
         * @var \k1lib\html\head_tag
         */
        protected $head_tag;

        /**
         * @var \k1lib\html\body_tag
         */
        private $body_tag;

        function __construct($lang = "en") {
            parent::__construct("html", FALSE);
            $this->set_attrib("lang", $lang);
            $this->append_head();
            $this->append_body();
        }

        function append_head() {
            $this->head_tag = new head_tag();
            $this->append_child($this->head_tag);
        }

        function append_body() {
            $this->body_tag = new body_tag();
            $this->append_child($this->body_tag);
        }

        /**
         * @return \k1lib\html\head_tag
         */
        function head() {
            return $this->head_tag;
        }

        /**
         * @return \k1lib\html\body_tag
         */
        function body() {
            return $this->body_tag;
        }

    }

    class head_tag extends html_tag {

        /**
         * @var \k1lib\html\title_tag
         */
        protected $title_tag;

        function __construct() {
            parent::__construct("head", FALSE);
            $this->append_title();
        }

        /**
         * @return \k1lib\html\title_tag
         */
        function append_title() {
            $this->title_tag = new title_tag();
            $this->append_child_head($this->title_tag);
            return $this->title_tag;
        }

        function set_title($document_title) {
            $this->title_tag->set_value($document_title);
        }

        /**
         * @return \k1lib\html\link_tag
         */
        function link_css($href) {
            $new = new link_tag($href);
            $this->append_child_tail($new);
            return $new;
        }
        /**
         * 
         * @return \k1lib\html\meta_tag
         */
        function append_meta($name = "", $content = "") {
            $new = new meta_tag($name, $content);
            $this->append_child_tail($new);
            return $new;
        }

    }

    class title_tag extends html_tag {

        function __construct() {
            parent::__construct("title", FALSE);
        }

    }

    class meta_tag extends html_tag {

        function __construct($name = "", $content = "") {
            parent::__construct("meta", FALSE);
            if (!empty($name)) {
                $this->set_attrib("name", $name);
            }
            if (!empty($content)) {
                $this->set_attrib("content", $content);
            }
        }

    }

    class body_tag extends html_tag {

        function __construct() {
            parent::__construct("body", FALSE);
        }

    }

    class a_tag extends html_tag {

        function __construct($href, $label, $target = "", $alt = "", $class = "", $id = "") {
            parent::__construct("a", FALSE);
            $this->set_attrib("href", $href);
            $this->set_value($label);
            $this->set_attrib("target", $target);
            $this->set_attrib("alt", $alt);
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

    }

    class img_tag extends html_tag {

        function __construct($src = "", $alt = "", $class = "", $id = "") {
            parent::__construct("img", FALSE);
            $this->set_attrib("src", $src);
            $this->set_attrib("alt", $alt);
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

        function set_value($value, $append = FALSE) {
            $this->set_attrib("alt", $value, $append);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

    }

    class ul_tag extends html_tag {

        /**
         * Create a UL html tag.
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("ul", FALSE);
//        $this->data_array &= $data_array;
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

        function set_value($value, $append = false) {
//            parent::set_value($value, $append);
        }

        /**
         * 
         * @param string $class
         * @param string $id
         * @return \k1lib\html\li_tag
         */
        function &append_li($class = "", $id = "") {
            $new = new li_tag($value, $class, $id);
            $this->append_child($new);
            return $new;
        }

    }

    class ol_tag extends html_tag {

        /**
         * Create a UL html tag.
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("ol", FALSE);
//        $this->data_array &= $data_array;
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

        function set_value($value, $append = false) {
//            parent::set_value($value, $append);
        }

        /**
         * 
         * @param string $class
         * @param string $id
         * @return \k1lib\html\li_tag
         */
        function &append_li($value = "", $class = "", $id = "") {
            $new = new li_tag($value, $class, $id);
            $this->set_value($value);
            $this->append_child($new);
            return $new;
        }

    }

    class link_tag extends html_tag {

        function __construct($href) {
            parent::__construct("link");
            $this->set_attrib("rel", "stylesheet");
            $this->set_attrib("type", "text/css");
            $this->set_attrib("href", $href);
        }

    }

    class li_tag extends html_tag {

        /**
         * Create a LI html tag with VALUE as data. Use $div->set_value($data)
         * @param String $class
         * @param String $id
         */
        function __construct($value = "", $class = "", $id = "") {
            parent::__construct("li", FALSE);
//        $this->data_array &= $data_array;
            $this->set_value($value);
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

        /**
         * 
         * @param string $class
         * @param string $id
         * @return \k1lib\html\ul_tag
         */
        function &append_ul($class = "", $id = "") {
            $new = new ul_tag($class, $id);
            $this->append_child($new);
            return $new;
        }

        /**
         * 
         * @param string $class
         * @param string $id
         * @return \k1lib\html\div_tag
         */
        function &append_ol($class = "", $id = "") {
            $new = new ol_tag($class, $id);
            $this->append_child($new);
            return $new;
        }

    }

    class script_tag extends html_tag {

        /**
         * Create a SCRIPT html tag with VALUE as data. Use $script->set_value($data)
         * @param String $class
         * @param String $id
         */
        function __construct($src = "") {
            parent::__construct("script", FALSE);
            if (!empty($src)) {
                $this->set_attrib("src", $src);
            }
        }

    }

    class small_tag extends html_tag {

        /**
         * Create a SMALL html tag with VALUE as data. Use $small->set_value($data)
         * @param String $class
         * @param String $id
         */
        function __construct($class = "", $id = "") {
            parent::__construct("small", FALSE);
//        $this->data_array &= $data_array;
            $this->set_class($class, TRUE);
            $this->set_id($id);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

        /**
         * Chains a new <THEAD> HTML TAG
         * @param String $class
         * @param String $id
         * @return \k1lib\html\thead_tag
         */
        function &append_thead($class = "", $id = "") {
            $child_object = new thead_tag($class, $id);
            $this->append_child($child_object);
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
            $this->append_child($child_object);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

        /**
         * Chains a new <TR> HTML TAG
         * @param String $class
         * @param String $id
         * @return \k1lib\html\tr_tag
         */
        function append_tr($class = "", $id = "") {
            $child_object = new tr_tag($class, $id);
            $this->append_child($child_object);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

        /**
         * Chains a new <TR> HTML TAG
         * @param String $class
         * @param String $id
         * @return \k1lib\html\tr_tag
         */
        function append_tr($class = "", $id = "") {
            $child_object = new tr_tag($class, $id);
            $this->append_child($child_object);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
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
            $this->append_child($child_object);
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
            $this->append_child($child_object);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

    }

    class textarea_tag extends html_tag {

        /**
         * 
         * @param string $name
         * @param string $class
         * @param string $id
         */
        function __construct($name, $class = "", $id = "") {
            parent::__construct("textarea", FALSE);
            $this->set_attrib("name", $name);
            $this->set_class($class, TRUE);
            $this->set_id($id);
            $this->set_attrib("rows", 10);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
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
            $this->append_child($child_object);
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
            $this->set_class($class, TRUE);
            $this->set_id($id);
        }

    }

    /**
     * FORM
     */
    class form_tag extends html_tag {

        function __construct($id = "k1-form") {
            parent::__construct("form", FALSE);
            $this->set_id($id);
            $this->set_attrib("name", "k1-form");
            $this->set_attrib("method", "post");
            $this->set_attrib("autocomplete", "yes");
            $this->set_attrib("enctype", "multipart/form-data");
            $this->set_attrib("novalidate", FALSE);
            $this->set_attrib("target", "_self");
        }

        /**
         * 
         * @param string $label
         * @param boolean $just_return
         * @return \k1lib\html\input_tag
         */
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

        function __construct($value = "", $class = "", $id = "") {
            parent::__construct("p", FALSE);
            $this->set_value($value);
            $this->set_class($class);
            $this->set_id($id);
        }

    }

    /**
     * h1
     */
    class h1_tag extends html_tag {

        function __construct($value = "", $class = "") {
            parent::__construct("h1", FALSE);
            $this->set_value($value);
            $this->set_class($class);
        }

    }

    /**
     * h2
     */
    class h2_tag extends html_tag {

        function __construct($value = "", $class = "") {
            parent::__construct("h2", FALSE);
            $this->set_value($value);
            $this->set_class($class);
        }

    }

    /**
     * h3
     */
    class h3_tag extends html_tag {

        function __construct($value = "", $class = "") {
            parent::__construct("h3", FALSE);
            $this->set_value($value);
            $this->set_class($class);
        }

    }

    /**
     * h4
     */
    class h4_tag extends html_tag {

        function __construct($value = "", $class = "") {
            parent::__construct("h4", FALSE);
            $this->set_value($value);
            $this->set_class($class);
        }

    }

    /**
     * h5
     */
    class h5_tag extends html_tag {

        function __construct($value = "", $class = "") {
            parent::__construct("h5", FALSE);
            $this->set_value($value);
            $this->set_class($class);
        }

    }

    /**
     * P
     */
    class fieldset_tag extends html_tag {

        function __construct($legend) {
            parent::__construct("fieldset", FALSE);
            $this->set_class("fieldset");
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