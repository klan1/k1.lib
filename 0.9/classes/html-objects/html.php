<?php

namespace k1lib\html\classes;

class html_tag {

    private $tag_name = null;
    private $tag_code = "";
    private $is_selfclosed = false;
    private $attributes = array();
    private $attributes_code = "";
    private $has_child = false;
    private $childs = array();
    private $value = "";

    function __construct($tag_name, $selfclosed = true) {
        if (!empty($tag_name) && is_string($tag_name)) {
            $this->tag_name = $tag_name;
        } else {
            trigger_error("TAG has to be string", E_USER_WARNING);
        }

        if (is_bool($selfclosed)) {
            $this->is_selfclosed = $selfclosed;
        } else {
            trigger_error("Self closed value has to be boolean", E_USER_WARNING);
        }
        $this->set_attrib("class", "k1-{$tag_name}-object");
    }

    public function append_child($chlid_object) {
        $this->childs[] = $chlid_object;
        $this->has_child = true;
    }

    public function &get_child($n) {
        if (isset($this->childs[$n])) {
            return $this->childs[$n];
        } else {
            return false;
        }
    }

    public function set_attrib($attribute, $value, $append = false) {
        if (!empty($attribute) && is_string($attribute)) {
            $this->attributes[$attribute] = ($append === true) ? ($this->attributes[$attribute] . " " . $value) : ($value);
        } else {
            trigger_error("HTML ATTRIBUTE has to be string", E_USER_WARNING);
        }
    }

    public function get_attribute($attribute) {
        if (isset($this->attributes[$attribute])) {
            return $this->attributes[$attribute];
        } else {
            return false;
        }
    }

    public function set_value($value) {
        $this->value = $value;
    }

    public function get_value() {
        return $this->value;
    }

    public function generate_attributes_code($do_echo = false) {
        if ($this->is_selfclosed) {
            $this->set_attrib("value", $this->value);
        }

        $attributes_count = count($this->attributes);
        $current_attribute = 0;
        $attributes_code = "";
        foreach ($this->attributes as $attribute => $value) {
            $current_attribute++;
            if (!is_bool($value)) {
                if (!empty($value)) {
                    $attributes_code .= "{$attribute}=\"{$value}\"";
                }
            } else {
                if ($value === true) {
                    $attributes_code .= "{$attribute}";
                }
            }
            $attributes_code .= ($current_attribute < $attributes_count) ? " " : "";
        }
        $this->attributes_code = $attributes_code;
        if ($do_echo) {
            echo $this->attributes_code;
        } else {
            return $this->attributes_code;
        }
    }

    public function generate_tag($do_echo = false, $with_childs = true, $n_childs = 0) {
        $html_code = "\n<{$this->tag_name} ";
        $html_code .= $this->generate_attributes_code();
        if ($this->is_selfclosed) {
            $html_code .= " /";
        }
        $html_code .= ">";
        if (($with_childs) && (count($this->childs) >= 1)) {
            //lets move with index numbers begining from 0
            $n_childs = (($n_childs === 0) ? count($this->childs) : $n_childs) - 1;
//            d($this->childs,true);
            foreach ($this->childs as $index => &$child_object) {
                if ($index > $n_childs) {
                    break;
                }
                $html_code .= "\t" . $child_object->generate_tag();
            }
        }
        if (!$this->is_selfclosed) {
            $html_code .= $this->get_value();
            $html_code .= $this->generate_close_tag();
        }

        $this->tag_code = $html_code;

        if ($do_echo) {
            echo $html_code;
        } else {
            return $html_code;
        }
    }

    public function generate_close_tag($do_echo = false) {
        $html_code = "</{$this->tag_name}>";
        if ($do_echo) {
            echo $html_code;
        } else {
            return $html_code;
        }
    }

    public function get_tag_code() {
        return $this->tag_code;
    }

}

class table_tag extends html_tag {

//    private $data_array = array();

    function __construct($class = "", $id = "") {
        parent::__construct("table", false);
//        $this->data_array &= $data_array;
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

    /**
     * 
     * @param String $class
     * @param String $id
     * @return \k1lib\html\classes\thead_tag
     */
    function &append_thead($class = "", $id = "") {
        $child_object = new thead_tag($class, $id);
        parent::append_child($child_object);
        return $child_object;
    }

    /**
     * 
     * @param String $class
     * @param String $id
     * @return \k1lib\html\classes\tbody_tag
     */
    function &append_tbody($class = "", $id = "") {
        $child_object = new tbody_tag($class, $id);
        parent::append_child($child_object);
        return $child_object;
    }

}

class thead_tag extends html_tag {

    function __construct($class = "", $id = "") {
        parent::__construct("thead", false);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

    /**
     * 
     * @param String $class
     * @param String $id
     * @return \k1lib\html\classes\tr_tag
     */
    function append_tr($class = "", $id = "") {
        $child_object = new tr_tag($class, $id);
        parent::append_child($child_object);
        return $child_object;
    }

}

class tbody_tag extends html_tag {

    function __construct($class = "", $id = "") {
        parent::__construct("tbody", false);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

    /**
     * 
     * @param String $class
     * @param String $id
     * @return \k1lib\html\classes\tr_tag
     */
    function append_tr($class = "", $id = "") {
        $child_object = new tr_tag($class, $id);
        parent::append_child($child_object);
        return $child_object;
    }

}

class tr_tag extends html_tag {

    function __construct($class = "", $id = "") {
        parent::__construct("tr", false);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

    /**
     * 
     * @param String $class
     * @param String $id
     * @return \k1lib\html\classes\th_tag
     */
    function append_th($value, $class = "", $id = "") {
        $child_object = new th_tag($value, $class, $id);
        parent::append_child($child_object);
        return $child_object;
    }

    /**
     * 
     * @param String $class
     * @param String $id
     * @return \k1lib\html\classes\td_tag
     */
    function append_td($value, $class = "", $id = "") {
        $child_object = new td_tag($value, $class, $id);
        parent::append_child($child_object);
        return $child_object;
    }

}

class th_tag extends html_tag {

    function __construct($value, $class = "", $id = "") {
        parent::__construct("th", false);
        $this->set_value($value);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

}

class td_tag extends html_tag {

    function __construct($value, $class = "", $id = "") {
        parent::__construct("td", false);
        $this->set_value($value);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

}

class input_tag extends html_tag {

    function __construct($type, $name, $value, $class = "", $id = "") {
        parent::__construct("input", true);
        $this->set_attrib("type", $type);
        $this->set_attrib("name", $name);
        $this->set_value($value);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

}

class label_tag extends html_tag {

    function __construct($label, $for, $class = "", $id = "") {
        parent::__construct("label", false);
        $this->set_value($label);
        $this->set_attrib("for", $for);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

}

class select_tag extends html_tag {

    function __construct($name, $class = "", $id = "") {
        parent::__construct("select", false);
        $this->set_attrib("name", $name);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

    function append_option($value, $label, $selected = false, $class = "", $id = "") {
        $child_object = new option_tag($value, $label, $selected, $class, $id);
        parent::append_child($child_object);
        return $child_object;
    }

}

class option_tag extends html_tag {

    function __construct($value, $label, $selected = false, $class = "", $id = "") {
        parent::__construct("option", false);
        $this->set_value($value);
        $this->set_attrib("label", $label);
        $this->set_attrib("selected", $selected);
        $this->set_attrib("class", $class, true);
        $this->set_attrib("id", $id);
    }

}

class form_tag extends html_tag {

//    use k1_object_execution_control;


    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct($id = "", $action = "") {
        parent::__construct("form", false);
        $this->phase_config();
        $this->set_attrib("action", (!empty($action)) ? $action : "./");
        $this->set_attrib("id", (!empty($id)) ? $id : "k1-form");
        $this->set_attrib("name", (!empty($id)) ? $id : "k1-form");
        $this->set_attrib("method", "post");
        $this->set_attrib("autocomplete", "yes");
        $this->set_attrib("enctype", "application/x-www-form-urlencoded");
        $this->set_attrib("novalidate", false);
        $this->set_attrib("target", "_self");
    }

}
