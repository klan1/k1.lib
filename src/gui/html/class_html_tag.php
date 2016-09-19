<?php

/**
 * HTML Classes for general purposes use
 */

namespace k1lib\html {

    /**
     * HTML Tag abstraction
     */
    class html_tag_base {

        /** @var String */
        protected $tag_name = NULL;

        /** @var Boolean */
        protected $is_selfclosed = FALSE;

        /** @var Array */
        protected $attributes = array();

        /** @var String */
        protected $attributes_code = "";

        /** @var String */
        protected $tag_code = "";

        /** @var String */
        protected $pre_code = "";

        /** @var String */
        protected $post_code = "";

        /** @var String */
        protected $value = "";

        /** @var Boolean */
        protected $has_child = FALSE;

        /** @var Array */
        protected $childs_head = array();

        /** @var Array */
        protected $childs = array();

        /** @var Array */
        protected $childs_tail = array();
        protected $child_level = 0;

        /**
         * @var html_tag;
         */
        protected $linked_html_obj = null;

        public function __toString() {
            return $this->generate_tag();
        }

        /**
         * Chains an HTML tag into the actual HTML tag on MAIN collection, by default will put on last 
         * position but with $put_last_position = FALSE will be the on first position
         * @param html_tag $child_object
         * @return \k1lib\html\html_tag 
         */
        public function append_child($child_object, $put_last_position = TRUE) {
            if ($put_last_position) {
                $this->childs[] = $child_object;
            } else {
                array_unshift($this->childs, $child_object);
            }
            $this->has_child = TRUE;
            return $child_object;
        }

        /**
         * Chains an HTML tag into the actual HTML tag on TAIL collection, by default will put on last 
         * position but with $put_last_position = FALSE will be the on first position
         * @param html_tag $child_object
         * @return \k1lib\html\html_tag 
         */
        public function append_child_tail($child_object, $put_last_position = TRUE) {
            if ($put_last_position) {
                $this->childs_tail[] = $child_object;
            } else {
                array_unshift($this->childs_tail, $child_object);
            }
            $this->has_child = TRUE;
            return $child_object;
        }

        /**
         * Chains an HTML tag into the actual HTML tag on HEAD collection, by default will put on last 
         * position but with $put_last_position = FALSE will be the on first position
         * @param html_tag $child_object
         * @return \k1lib\html\html_tag 
         */
        public function append_child_head($child_object, $put_last_position = TRUE) {
            if ($put_last_position) {
                $this->childs_head[] = $child_object;
            } else {
                array_unshift($this->childs_head, $child_object);
            }
            $this->has_child = TRUE;
            return $child_object;
        }

        /**
         * Chains THIS HTML tag to a another HTML tag
         * @param html_tag $child_object
         * @return \k1lib\html\html_tag 
         */
        public function append_to($html_object) {
            $html_object->append_child($this);
            return $this;
        }

        /**
         * Add free TEXT before the generated TAG
         * @param String $pre_code
         */
        function pre_code($pre_code) {
            $this->pre_code = $pre_code;
        }

        /**
         * Add free TEXT after the generated TAG
         * @param String $post_code
         */
        function post_code($post_code) {
            $this->post_code = $post_code;
        }

        /**
         * Set the VALUE for the TAG, as <TAG value="$value" /> or <TAG>$value</TAG>
         * @param String $value
         * @return \k1lib\html\html_tag
         */
        public function set_value($value, $append = false) {
            if (is_object($value)) {
                if (is_object($this->value)) {
                    $this->value .= $value;
                } elseif (!empty($this->value)) {
                    $this->value .= $value;
                } else {
                    $this->value = $value;
                }
            } else {
                if (empty($this->linked_html_obj)) {
                    $this->value = (($append === TRUE) && (!empty($this->value)) ) ? ($this->value . " " . $value) : ($value);
                } else {
                    $this->linked_html_obj->value = (($append === TRUE) && (!empty($this->linked_html_obj->value)) ) ? ($this->linked_html_obj->value . " " . $value) : ($value);
                }
            }
            return $this;
        }

        /**
         * Links the value of the current object to a child one. The current WON't be used and the value will be placed on the link object.
         * @param \k1lib\html\html_tag $obj_to_link
         */
        public function link_value_obj(html_tag $obj_to_link) {
            $this->linked_html_obj = $obj_to_link;
        }

        /**
         * Constructor with $tag_name and $selfclosed options for beginning
         * @param String $tag_name
         * @param Boolean $selfclosed Is self closed as <tag /> or tag closed one <tag></tag>
         */
        function __construct($tag_name, $selfclosed = TRUE) {
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
//            $this->set_attrib("class", "k1-{$tag_name}-object");
        }

        /**
         * Return the reference for chained HTML tag object
         * @param Int $n Index beginning from 0
         * @return html_tag Returns FALSE if is not set
         */
        public function &get_child($n) {
            if (isset($this->childs[$n])) {
                return $this->childs[$n];
            } else {
                return FALSE;
            }
        }

        /**
         * Set an attribute with its value always overwriting if $append is not set TRUE to append old value with the recieved one.
         * @param String $attribute
         * @param String $value
         * @param Boolean $append
         * @return \k1lib\html\html_tag
         */
        public function set_attrib($attribute, $value, $append = FALSE) {
            if (!empty($attribute) && is_string($attribute)) {
                if (empty($this->linked_html_obj)) {
                    $this->attributes[$attribute] = (($append === TRUE) && (!empty($this->attributes[$attribute])) ) ? ($this->attributes[$attribute] . " " . $value) : ($value);
                } else {
                    $this->linked_html_obj->set_attrib($attribute, $value, $append);
                }
            } else {
                trigger_error("HTML ATTRIBUTE has to be string", E_USER_WARNING);
            }
            return $this;
        }

        /**
         * Shortcut for $html->set_attrib("id",$id);
         * @param string $id
         */
        public function set_id($id) {
            if (!empty($id)) {
                $this->set_attrib("id", $id);
            }
        }

        /**
         * Shortcut for $html->set_attrib("class",$class);
         * @param string $class
         */
        public function set_class($class) {
            if (!empty($class)) {
                $this->set_attrib("class", $class);
            }
        }

        /**
         * If the attribute was set returns its value
         * @param String $attribute
         * @return String Returns FALSE if is not set
         */
        public function get_attribute($attribute) {
            if (isset($this->attributes[$attribute])) {
                return $this->attributes[$attribute];
            } else {
                return FALSE;
            }
        }

        /**
         * Gets the VALUE for the TAG, as <TAG value="$value" /> or <TAG>$value</TAG>
         * @return String
         */
        public function get_value() {
            if (is_object($this->value)) {
                return $this->value->generate_tag();
            } else {
                return $this->value;
            }
        }

        /**
         * VALUE for the TAG, as <TAG attribute1="value1" .. attributeN="valueN" /> or <TAG attribute1="value1" .. attributeN="valueN">$value</TAG>
         * @param Boolean $do_echo
         * @return string Returns FALSE if is not attributes to generate
         */
        protected function generate_attributes_code() {
            if ($this->is_selfclosed && !empty($this->value)) {
                $this->set_attrib("value", $this->value);
            }

            $attributes_count = count($this->attributes);
            $current_attribute = 0;
            $attributes_code = "";

            if ($attributes_count != 0) {
                foreach ($this->attributes as $attribute => $value) {
                    $current_attribute++;
                    if ($value !== TRUE && $value !== FALSE) {
                        $attributes_code .= "{$attribute}=\"{$value}\"";
                    } else {
                        if ($value === TRUE) {
                            $attributes_code .= "{$attribute}";
                        }
                    }
                    $attributes_code .= ($current_attribute < $attributes_count) ? " " : "";
                }
                $this->attributes_code = $attributes_code;
                return " " . $this->attributes_code;
            } else {
                return "";
            }
        }

        /**
         * This will generate the HTML TAG with ALL his childs by default. If the TAG is not SELF CLOSED will generate all as <TAG attributeN="valueN">$value</TAG>
         * @param Boolean $do_echo Do ECHO action or RETURN HTML
         * @param Boolean $with_childs
         * @param Int $n_childs
         * @return string Won't return any if is set $do_echo = TRUE
         */
        public function generate_tag($do_echo = \FALSE, $with_childs = \TRUE, $n_childs = 0) {
            /**
             * Merge the child arrays HEAD, MAIN and TAIL collections
             */
            $merged_childs = [];
            if (!empty($this->childs_head)) {
                foreach ($this->childs_head as $index => $child) {
                    $merged_childs[] = $child;
                }
            }
            if (!empty($this->childs)) {
                foreach ($this->childs as $index => $child) {
                    $merged_childs[] = $child;
                }
            }
            if (!empty($this->childs_tail)) {
                foreach ($this->childs_tail as $index => $child) {
                    $merged_childs[] = $child;
                }
            }
            $this->childs = $merged_childs;

            $object_childs = count($this->childs);

            /**
             * TAB constructor
             */
            $tabs = str_repeat("\t", $this->child_level);

            $new_line = ($this->child_level >= 1) ? "\n" : "";

            $html_code = "{$new_line}{$tabs}<{$this->tag_name}";
            $html_code .= $this->generate_attributes_code();
            if ($this->is_selfclosed) {
//                $html_code .= " /";
            }
            $html_code .= ">";

            $has_childs = FALSE;
            if (($with_childs) && ($object_childs >= 1)) {
                $has_childs = TRUE;
                //lets move with index numbers begining from 0
                $n_childs = (($n_childs === 0) ? $object_childs : $n_childs) - 1;
                foreach ($this->childs as $index => &$child_object) {
                    if ($index > $n_childs) {
                        break;
                    }
                    $child_object->child_level = $this->child_level + 1;
                    $html_code .= $child_object->generate_tag();
                }
            }
            if (!$this->is_selfclosed) {
                if ($has_childs && !empty($this->value)) {
                    $html_code .= "\n{$tabs}\t";
                }

                $html_code .= $this->get_value();

                if ($has_childs) {
                    $html_code .= "\n";
                }
                $html_code .= $this->generate_close_tag();
            }

            $this->tag_code = $this->pre_code . $html_code . $this->post_code;

            if ($do_echo) {
                echo $this->tag_code;
            } else {
                return $this->tag_code;
            }
        }

        /**
         * This will generate the HTML CLOSE TAG 
         * @param Boolean $do_echo Do ECHO action or RETURN HTML
         * @return string Won't return any if is set $do_echo = TRUE
         */
        protected function generate_close_tag() {
            /**
             * TAB constructor
             */
            if (count($this->childs)) {
                $tabs = str_repeat("\t", $this->child_level);
            } else {
                $tabs = '';
            }
            $html_code = "{$tabs}</{$this->tag_name}>";

            return $html_code;
        }

        public function get_tag_code() {
            return $this->tag_code;
        }

        public function get_tag_name() {
            return $this->tag_name;
        }

    }

    class html_tag extends html_tag_base {

        /**
         * 
         * @param string $class
         * @param string $id
         * @return \k1lib\html\div_tag
         */
        function &append_div($class = "", $id = "") {
            $new = new div_tag($class, $id);
            $this->append_child($new);
            return $new;
        }

        /**
         * 
         * @param string $class
         * @param string $id
         * @return \k1lib\html\span_tag
         */
        function &append_span($class = "", $id = "") {
            $new = new span_tag($class, $id);
            $this->append_child($new);
            return $new;
        }

        /**
         * 
         * @param string $class
         * @param string $id
         * @return \k1lib\html\p_tag
         */
        function &append_p($value = "", $class = "", $id = "") {
            $new = new p_tag($value, $class, $id);
            $this->append_child($new);
            return $new;
        }

        /**
         * 
         * @param string $href
         * @param string $label
         * @param string $target
         * @param string $alt
         * @param string $class
         * @param string $id
         * @return \k1lib\html\a_tag
         */
        function &append_a($href = "", $label = "", $target = "", $alt = "", $class = "", $id = "") {
            $new = new a_tag($href, $label, $target, $alt, $class, $id);
            $this->append_child($new);
            return $new;
        }

    }

}