<?php

namespace k1lib\html;

/**
 * HTML Tag abstraction
 */
class tag {

    use append_shotcuts;

    /** @var String */
    protected $tag_id = 0;

    /** @var String */
    protected $tag_name = NULL;

    /** @var Boolean */
    protected $is_self_closed = FALSE;

    /** @var Boolean */
    protected $is_inline = FALSE;

    /** @var Boolean */
    protected $inside_inline = FALSE;

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

    /** @var String */
    protected $post_value = "";

    /** @var String */
    protected $pre_value = "";

    /** @var Boolean */
    protected $has_child = FALSE;

    /** @var Array */
    protected $childs_head = array();

    /** @var tag[] */
    protected $childs = array();

    /** @var Array */
    protected $childs_tail = array();

    /** @var Integer */
    protected $child_level = 0;

    /** @var tag */
    protected $parent = NULL;

    /** @var boolean */
    static protected $use_log = FALSE;

    /** @var tag; */
    protected $this_link = NULL;

    /**
     * Constructor with $tag_name and $self_closed options for beginning
     * @param String $tag_name
     * @param Boolean $self_closed Is self closed as <tag> or tag closed one <tag></tag>
     */
    function __construct($tag_name, $self_closed = IS_SELF_CLOSED) {
        if (!empty($tag_name) && is_string($tag_name)) {
            $this->tag_name = $tag_name;
        } else {
            trigger_error("TAG has to be string", E_USER_WARNING);
        }

        if (is_bool($self_closed)) {
            $this->is_self_closed = $self_closed;
        } else {
            trigger_error("Self closed value has to be boolean", E_USER_WARNING);
        }
//            $this->set_attrib("class", "k1lib-{$tag_name}-object");
// GET the global tag ID and catalog the object
        $this->tag_id = tag_catalog::increase($this);
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} was created");
        }
    }

    function __clone() {
        $this->tag_id = tag_catalog::increase($this);
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} was cloned");
        }
    }

    /**
     * Remove the tag Object from the Array catalog, this will disable the 
     * Object to be found or generated on chain actions.     
     */
    function decatalog() {
// Itself from Catalog
        tag_catalog::decatalog($this->tag_id);
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} was decataloged");
        }
// His childs
        if ($this->has_child) {
            foreach ($this->childs as $child_object) {
                $child_object->decatalog();
            }
        }
// Inline objects
        foreach ($this->get_inline_tags() as $tag) {
            $tag->decatalog();
        }
    }

    /**
     * Get the catalog index (an unique id) for this tag Object or NULL if the 
     * Object has been decataloged
     * @return integer|NULL
     */
    function get_tag_id() {
        if (tag_catalog::index_exist($this->tag_id)) {
            return $this->tag_id;
        } else {
            NULL;
        }
    }

    /**
     * Whatever or not EVERY tag Object created will use the log system
     * @return boolean
     */
    static function get_use_log() {
        return self::$use_log;
    }

    static function set_use_log($use_log) {
        self::$use_log = $use_log;
    }

    /**
     * Return the parent tag Object.
     * @return \k1lib\html\tag|NULL
     */
    function get_parent() {
        return $this->parent;
    }

    /**
     * Chains the parent tag Object
     * @param \k1lib\html\tag $parent
     */
    function set_parent(tag $parent) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is child of [{$parent->get_tag_name()}] ID:{$parent->tag_id} ");
        }
        $this->parent = $parent;
    }

    /**
     * When the tag Object is used as string, maybe as inline on text it
     * will be returned as {{ID:1..}} to converted when the container Object is
     * generated 
     * @return string
     */
    public function __toString() {
        if ($this->get_tag_id()) {
            if (html::get_use_log()) {
                tag_log::log("[{$this->get_tag_name()}] is returned for inline use");
            }
            return "{{ID:" . $this->get_tag_id() . "}}";
        } else {
            return "";
        }
    }

//    /**
//     * Wherever the tag Object is used as string, will be returned as 
//     * the generated tag
//     * @return string
//     */
//    public function __toString() {
//        return $this->generate();
//    }

    /**
     * Chains an HTML tag into the actual HTML tag on MAIN collection, by default will put on last 
     * position but with $put_last_position = FALSE will be the on first position
     * @param tag $child_object
     * @return tag 
     */
    public function append_child(tag $child_object, $put_last_position = TRUE, $tag_position = APPEND_ON_MAIN) {
        $child_object->set_parent($this);
        if ($put_last_position) {
            switch ($tag_position) {
                case APPEND_ON_HEAD:
                    $this->childs_head[] = $child_object;
                    break;
                case APPEND_ON_TAIL:
                    $this->childs_tail[] = $child_object;
                    break;
                default:
                    $this->childs[] = $child_object;
                    break;
            }
        } else {
            switch ($tag_position) {
                case APPEND_ON_HEAD:
                    array_unshift($this->childs_head, $child_object);
                    break;
                case APPEND_ON_TAIL:
                    array_unshift($this->childs_tail, $child_object);
                    break;
                default:
                    array_unshift($this->childs, $child_object);
                    break;
            }
        }
        $this->has_child = TRUE;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} appends [{$child_object->get_tag_name()}] ID:{$child_object->tag_id} ");
        }
        return $child_object;
    }

    /**
     * Chains an HTML tag into the actual HTML tag on HEAD collection, by default will put on last 
     * position but with $put_last_position = FALSE will be the on first position
     * @param tag $child_object
     * @return tag 
     */
    public function append_child_head(tag $child_object, $put_last_position = TRUE) {
        $this->append_child($child_object, $put_last_position, APPEND_ON_HEAD);
        return $child_object;
    }

    /**
     * Chains an HTML tag into the actual HTML tag on TAIL collection, by default will put on last 
     * position but with $put_last_position = FALSE will be the on first position
     * @param tag $child_object
     * @return tag 
     */
    public function append_child_tail(tag $child_object, $put_last_position = TRUE) {
        $this->append_child($child_object, $put_last_position, APPEND_ON_TAIL);
        return $child_object;
    }

    /**
     * Chains THIS HTML tag to a another HTML tag
     * @param tag $child_object
     * @return tag 
     */
    public function append_to($html_object) {
        $this->set_parent($html_object);
        $html_object->append_child($this);
        return $this;
    }

    /**
     * @return tag
     */
    public function remove_childs() {
        foreach ($this->childs as $key => $child) {
            unset($this->childs[$key]);
            $child->decatalog();
        }
        foreach ($this->childs_head as $key => $child) {
            unset($this->childs_head[$key]);
            $child->decatalog();
        }
        foreach ($this->childs_tail as $key => $child) {
            unset($this->childs_tail[$key]);
            $child->decatalog();
        }
        $this->has_child = FALSE;
        return $this;
    }

    /**
     * Add free TEXT before the generated TAG
     * @param String $pre_code
     */
    function pre_code($pre_code) {
        if (substr($pre_code, 0, 1) != "\n") {
            $pre_code = "\n" . $pre_code;
        }
        if (substr($pre_code, -1) != "\n") {
            $pre_code = $pre_code . "\n";
        }
        $this->pre_code = $pre_code;
    }

    /**
     * Add free TEXT after the generated TAG
     * @param String $post_code
     */
    function post_code($post_code) {
        if (substr($post_code, 0, 1) != "\n") {
            $post_code = "\n" . $post_code;
        }
        if (substr($post_code, -1) != "\n") {
            $post_code = $post_code . "\n";
        }
        $this->post_code = $post_code;
    }

    /**
     * Add free TEXT before the generated TAG
     * @param String $pre_value
     */
    function pre_value($pre_value) {
        if (substr($pre_value, 0, 1) != "\n") {
            $pre_value = "\n" . $pre_value;
        }
        if (substr($pre_value, -1) != "\n") {
            $pre_value = $pre_value . "\n";
        }
        $this->pre_value = $pre_value;
    }

    /**
     * Add free TEXT after the generated TAG
     * @param String $post_value
     */
    function post_value($post_value) {
        if (substr($post_value, 0, 1) != "\n") {
            $post_value = "\n" . $post_value;
        }
        if (substr($post_value, -1) != "\n") {
            $post_value = $post_value . "\n";
        }
        $this->post_value = $post_value;
    }

    function load_file($file_path, $position = INSERT_ON_VALUE, $include_file = TRUE) {
        if (file_exists($file_path)) {
            if ($include_file) {
                ob_start();
                include $file_path;
                $file_content = ob_get_clean();
            } else {
                $file_content = file_get_contents($file_path);
            }
            if (!empty($file_content)) {
                switch ($position) {
                    case INSERT_ON_PRE_TAG:
                        $this->pre_code($this->pre_code . $file_content);
                        break;
                    case INSERT_ON_AFTER_TAG_OPEN:
                        $this->pre_value($this->pre_value . $file_content);
                        break;
                    case INSERT_ON_VALUE:
                        if (substr($file_content, 0, 1) != "\n") {
                            $file_content = "\n" . $file_content;
                        }
                        if (substr($file_content, -1) != "\n") {
                            $file_content = $file_content . "\n";
                        }
                        $this->set_value($file_content, TRUE);
                        break;
                    case INSERT_ON_BEFORE_TAG_CLOSE:
                        $this->post_value($this->post_value . $file_content);
                        break;
                    case INSERT_ON_POST_TAG:
                        $this->post_code($this->post_code . $file_content);
                        break;
                    default:
                        break;
                }
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            user_error("the file '$file_path' do not exist", E_USER_WARNING);
            return FALSE;
        }
    }

    /**
     * Set the VALUE for the TAG, as <TAG value="$value" /> or <TAG>$value</TAG>
     * @param String $value
     * @return tag
     */
    public function set_value($value, $append = FALSE) {

        if (empty($this->this_link)) {
            if (($value !== FALSE) && ($value !== NULL)) {
                $this->value = ($append === TRUE) ? ($this->value . " " . $value) : ("$value");
            }
        } else {
            $this->this_link->set_value((($append === TRUE) && (!empty($this->this_link->get_value())) ) ? ($this->this_link->get_value() . " " . $value) : ("$value"));
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} set value to: {$value}");
        }
        return $this;
    }

    /**
     * Links the value of the current object to a child one. The current WON't be used and the value will be placed on the link object.
     * @param tag $obj_to_link
     */
    public function link_value_obj(tag $obj_to_link) {
        $this->this_link = $obj_to_link;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is linked to [{$obj_to_link->get_tag_name()}]");
        }
    }

    public function unlink_value_obj() {
        $this->this_link = NULL;
    }

    /**
     * Return the reference for chained HTML tag object
     * @param Int $n Index beginning from 0
     * @return tag Returns FALSE if is not set
     */
    public function get_child($n) {
        if (isset($this->childs[$n])) {
            return $this->childs[$n];
        } else {
            return FALSE;
        }
    }

    /**
     * Return array of reference for chained HTML tags objects
     * @return tag[] Returns [] if is not set
     */
    public function get_childs() {
        if (!empty($this->childs)) {
            return $this->childs;
        } else {
            return [];
        }
    }

    /**
     * Replace current child reference with another one
     * @param type $n
     * @param \k1lib\html\tag $new_object
     */
    public function replace_child($n, tag $new_object) {
        if (array_key_exists($n, $this->childs)) {
            $this->childs[$n] = $new_object;
//            echo "$n exists ! {$this->childs[$n]} {$this->childs[$n]->generate()} <br>";
        }
    }

    /**
     * Set an attribute with its value always overwriting if $append is not set TRUE to append old value with the recieved one.
     * @param String $attribute
     * @param String $value
     * @param Boolean $append
     * @return tag
     */
    public function set_attrib($attribute, $value, $append = FALSE) {
        if (!empty($attribute) && is_string($attribute)) {
            if (empty($this->this_link)) {
                if ($value !== NULL) {
                    if (($append === TRUE) && (!empty($this->attributes[$attribute]))) {
                        $this->attributes[$attribute] = $this->attributes[$attribute] . " " . $value;
                    } else {
                        $this->attributes[$attribute] = $value;
                    }
                }
            } else {
                $this->this_link->set_attrib($attribute, $value, $append);
            }
        } else {
            trigger_error("HTML ATTRIBUTE has to be string", E_USER_WARNING);
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} new attrib: {$attribute}={$value}");
        }
        return $this;
    }

    public function remove_attrib($attribute) {
        if (isset($this->attributes[$attribute])) {
            unset($this->attributes[$attribute]);
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

    /**
     * Shortcut for $html->set_attrib("id",$id);
     * @param string $id
     * @return tag
     */
    public function set_id($id, $append = FALSE) {
        if (!empty($id)) {
            $this->set_attrib("id", $id, $append);
        }
        return $this;
    }

    /**
     * Shortcut for $html->set_attrib("class",$class);
     * @param string $class
     * @return tag
     */
    public function set_class($class, $append = FALSE) {
        if (!empty($class)) {
            $this->set_attrib("class", $class, $append);
        }
        return $this;
    }

    /**
     * Shortcut for $html->set_attrib("style",$style);
     * @param string $style
     * @return tag
     */
    public function set_style($style, $append = FALSE) {
        if (!empty($style)) {
            $this->set_attrib("style", $style, $append);
        }
        return $this;
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

    public function get_attributes_array() {
        return $this->attributes;
    }

    /**
     * Gets the VALUE for the TAG, as <TAG value="$value" /> or <TAG>$value</TAG>
     * @return String
     */
    public function get_value($current_child_level = 0) {
        if (is_object($this->value)) {
            trigger_error("This shouldn't be used more", E_USER_NOTICE);
            return $this->get_value();
        } else {
            $this->parse_value($current_child_level);
            return $this->value;
        }
    }

    /**
     * Generate inline tag Objects on the value property
     */
    public function parse_value($current_child_level = 0) {
        $value_original = $this->value;
        foreach ($this->get_inline_ids() as $tag_id) {
            if (tag_catalog::index_exist($tag_id)) {
                $tag_string = "{{ID:" . $tag_id . "}}";
                tag_catalog::get_by_index($tag_id)->child_level = $current_child_level + 1;
                tag_catalog::get_by_index($tag_id)->is_inline = TRUE;
                $this->value = str_replace($tag_string, tag_catalog::get_by_index($tag_id)->generate(), $this->value);
            }
        }
        if ($value_original !== $this->value) {
            $this->has_child = TRUE;
        }
    }

    /**
     * Returns an Array with the ID list found on $this->value
     * @return integer[]
     */
    public function get_inline_ids() {
        $regexp = "/\{\{ID:(\d*)\}\}/";
        $matches = [];
        $cataloged = [];
        if (preg_match_all($regexp, $this->value, $matches)) {
            foreach ($matches[1] as $tag_id) {
                if (tag_catalog::index_exist($tag_id)) {
                    $cataloged[] = $tag_id;
                }
            }
        }
        return $cataloged;
    }

    /**
     * Returns an Array with the tag Objects found on $this->value
     * 
     * @return tag[]
     */
// TODO: Fix the error!
    public function get_inline_tags() {
        $regexp = "/\{\{ID:(\d*)\}\}/";
        $matches = [];
        $tags = [];
        if (preg_match_all($regexp, $this->value, $matches)) {
            foreach ($matches[1] as $tag_id) {
                if (tag_catalog::index_exist($tag_id)) {
                    $tags[] = tag_catalog::get_by_index($tag_id);
                }
            }
        }
        return $tags;
    }

    /**
     * VALUE for the TAG, as <TAG attribute1="value1" .. attributeN="valueN" /> or <TAG attribute1="value1" .. attributeN="valueN">$value</TAG>
     * @param Boolean $do_echo
     * @return string Returns FALSE if is not attributes to generate
     */
    protected function generate_attributes_code() {
        // WTF line
        // TODO: Check for numeric value on 0
        if ($this->is_self_closed && ($this->value !== 0) && ($this->value != NULL)) {
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
    public function generate($with_childs = \TRUE, $n_childs = 0) {
        /**
         * Merge the child arrays HEAD, MAIN and TAIL collections
         */
        $this->childs = $this->get_all_childs();

        $object_childs = count($this->childs);

        /**
         * TAB constructor
         */
        $tabs = str_repeat("\t", $this->child_level);
        /**
         * NL manager :)
         */
        $new_line = ($this->child_level >= 1) ? "\n" : "";

        $html_code = "{$new_line}{$tabs}<{$this->tag_name}";
        $html_code .= $this->generate_attributes_code();
        $html_code .= ">";

        $has_childs = FALSE;
        if (!$this->is_self_closed) {
            // VALUE first, then child objects
            $html_code .= $this->pre_value . $this->get_value($this->child_level);
            // Child objetcs generation
            if (($with_childs) && ($object_childs >= 1)) {
                $has_childs = TRUE;
                foreach ($this->childs as $child_object) {
                    if ($child_object->get_tag_id()) {
                        $child_object->child_level = $this->child_level + 1;
                        $html_code .= $child_object->generate();
                    }
                }
            }
            if ($has_childs || $this->has_child) {
                $html_code .= "\n";
            }
            $html_code .= $this->post_value . $this->generate_close();
        }
        // TODO: Fix this!! please no more pre_code and post_code
        $this->tag_code = $this->pre_code . $html_code . $this->post_code;
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] is generated");
        }

        return $this->tag_code;
    }

    /**
     * This will generate the HTML CLOSE TAG 
     * @param Boolean $do_echo Do ECHO action or RETURN HTML
     * @return string Won't return any if is set $do_echo = TRUE
     */
    protected function generate_close() {
        /**
         * TAB constructor
         */
        if (($this->child_level > 0) && $this->has_child) {
            $tabs = str_repeat("\t", $this->child_level);
        } else {
            $tabs = '';
        }
        $html_code = "{$tabs}</{$this->tag_name}>";
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] close tag generated");
        }

        return $html_code;
    }

    /**
     * Returns the tag name. <tag name> or <tag name></tag name>
     * @return string
     */
    public function get_tag_name() {
        return $this->tag_name;
    }

    /**
     * Return the FIRST object found with the $id
     * @param string $id
     * @return tag|NULL
     */
    public function get_element_by_id($id) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by ID='$id'");
        }
        if ($this->get_tag_id()) {
            if ($this->get_attribute("id") == $id) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} has the ID='$id' and is returned");
                }
                return $this;
            } else {
                $inline_tags = $this->get_inline_tags();
                $all_childs = $this->get_all_childs();
                $all_childs = array_merge($inline_tags, $all_childs);
                foreach ($all_childs as $child) {
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by ID='$id' on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                    }
                    $child_search_result = $child->get_element_by_id($id);
                    if (!empty($child_search_result)) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} has child [{$child->get_tag_name()}] ID:{$child->tag_id} with the ID='$id' and is returned");
                        }
                        return $child_search_result;
                    }
                }
            }
        } else {
            return NULL;
        }
    }

    /**
     * Return an Array with all the objects that TAG is $tag_name
     * @param string $tag_name
     * @return tag[]
     */
    public function get_elements_by_tag($tag_name) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by TAG='$tag_name'");
        }
        $tags = [];
        if ($this->get_tag_id()) {
            if ($this->get_tag_name() == $tag_name) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned");
                }
                $tags[] = $this;
            }
            /**
             * Child and inline tags
             */
            $inline_tags = $this->get_inline_tags();
            $all_childs = $this->get_all_childs();
            $all_childs = array_merge($inline_tags, $all_childs);
            foreach ($all_childs as $child) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} looking on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                }
                $child_search_result = $child->get_elements_by_tag($tag_name);
                if (!empty($child_search_result)) {
//                        print_r($child_search_result);
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return child [{$child->get_tag_name()}] ID:{$child->tag_id} results");
                    }
                    $tags = array_merge($tags, $child_search_result);
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return " . count($tags) . " '$tag_name' tags");
        }
        return $tags;
    }

    /**
     * Return an Array with all the objects that has ATTRIBUTE as $attribute_name
     * @param string $attribute_name
     * @param boolean $partial_text_search
     * @return tag[]
     */
    public function get_elements_by_attrib($attribute_name, $partial_text_search = FALSE) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by ATTRIB='$attribute_name'");
        }
        $tags = [];
        if ($this->get_tag_id()) {
            if (array_key_exists($attribute_name, $this->attributes) && !$partial_text_search) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by exact match");
                }
                $tags[] = $this;
            } elseif ($partial_text_search) {
                foreach ($this->attributes as $attribute => $value) {
                    if (strstr($attribute, $attribute_name) !== FALSE) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by partial match");
                        }
                        $tags[] = $this;
                    }
                }
            }
            /**
             * Child and inline tags
             */
            $inline_tags = $this->get_inline_tags();
            $all_childs = $this->get_all_childs();
            $all_childs = array_merge($inline_tags, $all_childs);
            foreach ($all_childs as $child) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} looking on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                }
                $child_search_result = $child->get_elements_by_attrib($attribute_name);
                if (!empty($child_search_result)) {
//                        print_r($child_search_result);
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return child [{$child->get_tag_name()}] ID:{$child->tag_id} results");
                    }
                    $tags = array_merge($tags, $child_search_result);
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return " . count($tags) . " '$attribute_name' attribute");
        }
        return $tags;
    }

    /**
     * Return an Array with all the objects that has ATTRIBUTE as $attribute_name
     * @param string $attribute_name
     * @param boolean $partial_text_search
     * @return tag[]
     */
    public function get_elements_by_attrib_value($attribute_name, $attribute_value, $partial_attribute_text_search = FALSE, $partial_value_text_search = FALSE) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by ATTRIB='$attribute_name' and VALUE='$attribute_value'");
        }
        $tags = [];
        if ($this->get_tag_id()) {
            $tag_has_attribute = $this->get_elements_by_attrib($attribute_name, $partial_attribute_text_search);
            if (!empty($tag_has_attribute) && $partial_attribute_text_search) {
                foreach ($tag_has_attribute as $tag_to_look) {
                    $tag_attributes = $tag_to_look->get_attributes_array();
                    foreach ($tag_attributes as $attribute => $value) {
                        if (strstr($attribute, $attribute_name) !== FALSE) {
                            if ($partial_value_text_search && strstr($value, $attribute_value)) {
                                if (html::get_use_log()) {
                                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by partial text and partial attrib match");
                                }
                                $tags[] = $tag_to_look;
                            } elseif ($attribute_value == $value) {
                                if (html::get_use_log()) {
                                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by exact text and partial attrib match");
                                }
                                $tags[] = $tag_to_look;
                            }
                        }
                    }
                }
            } else if (!empty($tag_has_attribute) && !$partial_attribute_text_search) {
                foreach ($tag_has_attribute as $tag_to_look) {
                    $tag_attribute_value = $tag_to_look->get_attribute($attribute_name);
                    if ($partial_value_text_search && (strstr($tag_attribute_value, $attribute_value) !== FALSE)) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by partial text and exact attrib match");
                        }
                        $tags[] = $tag_to_look;
                    } elseif ($tag_attribute_value == $attribute_value) {
                        if (html::get_use_log()) {
                            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned by exact text and exact attrib match");
                        }
                        $tags[] = $tag_to_look;
                    }
                }
            }

            /**
             * Child and inline tags
             */
            $inline_tags = $this->get_inline_tags();
            $all_childs = $this->get_all_childs();
            $all_childs = array_merge($inline_tags, $all_childs);
            foreach ($all_childs as $child) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} looking on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                }
                $child_search_result = [];
                $child_search_result = $child->get_elements_by_attrib_value($attribute_name, $attribute_value, $partial_attribute_text_search, $partial_value_text_search);
                if (!empty($child_search_result)) {
//                        print_r($child_search_result);
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return child [{$child->get_tag_name()}] ID:{$child->tag_id} results");
                    }
                    $tags = array_merge($tags, $child_search_result);
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return " . count($tags) . " '$attribute_name' attribute");
        }
        return $tags;
    }

    /**
     * Return an Array with all the objects that CLASS is $class_name. 
     * NOTE: This will work ONLY with 1 class at time, or multiple in exact order.
     * @param string $class_name
     * @return tag[]
     */
    public function get_elements_by_class($class_name) {
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will SEARCH by CLASS='$class_name'");
        }
        $classes = [];
        if ($this->get_tag_id()) {
//            if ($this->get_attribute("class") == $class_name) {
            if (strstr($this->get_attribute("class"), $class_name) !== FALSE) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} is returned");
                }
                $classes[] = $this;
            }
            /**
             * Child and inline tags
             */
            $inline_tags = $this->get_inline_tags();
            $all_childs = $this->get_all_childs();
            $all_childs = array_merge($inline_tags, $all_childs);
            foreach ($all_childs as $child) {
                if (html::get_use_log()) {
                    tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} looking on child [{$child->get_tag_name()}] ID:{$child->tag_id}");
                }
                $child_search_result = $child->get_elements_by_class($class_name);
                if (!empty($child_search_result)) {
//                        print_r($child_search_result);
                    if (html::get_use_log()) {
                        tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return child [{$child->get_tag_name()}] ID:{$child->tag_id} results");
                    }
                    $classes = array_merge($classes, $child_search_result);
                }
            }
        }
        if (html::get_use_log()) {
            tag_log::log("[{$this->get_tag_name()}] ID:{$this->tag_id} will return " . count($classes) . " tags with CLASS='$class_name'");
        }
        return $classes;
    }

    /**
     * TRUE if this Objects have child and FALSE if not.
     * @return boolean
     */
    function has_childs() {
        return $this->has_child;
    }

    /**
     * Merge and return the $childs_head, $childs and $childs_tail
     * @return \k1lib\html\tag[]
     */
    protected function get_all_childs() {
        /**
         * Merge the child arrays HEAD, MAIN and TAIL collections
         */
        $merged_childs = [];
        if (!empty($this->childs_head)) {
            foreach ($this->childs_head as $child) {
                $merged_childs[] = $child;
            }
        }
        if (!empty($this->childs)) {
            foreach ($this->childs as $child) {
                $merged_childs[] = $child;
            }
        }
        if (!empty($this->childs_tail)) {
            foreach ($this->childs_tail as $child) {
                $merged_childs[] = $child;
            }
        }
        return $merged_childs;
    }

}

