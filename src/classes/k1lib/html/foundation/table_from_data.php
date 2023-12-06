<?php

namespace k1lib\html\foundation;

class table_from_data extends \k1lib\html\table {

    static public $float_round_default = NULL;

    use foundation_methods;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var array
     */
    protected $data = [];

    /**
     * @var array
     */
    protected $data_original = [];

    /**
     * @var array
     */
    protected $fields_to_hide = [];

    /**
     * @var array
     */
    protected $fields_for_key_array_text = [];

    /**
     * @var boolean 
     */
    protected $has_header = TRUE;

    /**
     * @var integer 
     */
    protected $max_text_length_on_cell = NULL;

    /**
     * @var int
     */
    protected $float_round = NULL;

    function __construct($class = "", $id = "") {

//        $this->parent = $parent;

        parent::__construct($class, $id);
//        $this->append_to($parent);
        $this->set_class($class);
        $this->set_id($id);

        $this->float_round = self::$float_round_default;
    }

    public function set_data(array $data, $has_header = TRUE) {
        $this->data = $data;
        $this->data_original = $data;
        $this->has_header = $has_header;
        return $this;
    }

    public function generate($with_childs = TRUE, $n_childs = 0) {
        $this->use_data();
        return parent::generate($with_childs, $n_childs);
    }

    public function set_fields_to_hide($fields) {
        $this->fields_to_hide = $fields;
    }

    public function use_data() {
        $num_col = 0;
        $num_row = 0;
        $row = 0;
        foreach ($this->data as $row_index => $row_data) {
//            print_r($this->data);
            if ($this->has_header && ($row_index === 0)) {
                $thead = $this->append_thead();
                $tr = $thead->append_tr();
            } else {
                $num_row++;
                if (!isset($tbody)) {
                    $tbody = $this->append_tbody();
                }
                $tr = $tbody->append_tr();
            }
            foreach ($row_data as $field => $col_value) {
                if ($this->has_header && $row !== 0) {
                    $col_value = $this->parse_string_value($col_value, $row);
                }
                // FIELD HIDE
                if (array_search($field, $this->fields_to_hide) !== FALSE) {
                    continue;
                }
                if ($this->has_header && ($row_index === 0)) {
                    $tr->append_th($col_value);
                } else {
                    if (!is_object($col_value)) {
                        if (($this->float_round !== NULL) && is_numeric($col_value) && is_float($col_value + 0)) {
                            $col_value = round($col_value + 0, $this->float_round);
                        } else {
                            if (is_numeric($this->max_text_length_on_cell) && strlen($col_value) > $this->max_text_length_on_cell) {
                                $col_value = substr($col_value, 0, $this->max_text_length_on_cell) . "...";
                            } else {
                                
                            }
                        }
                    } else {
                        if (is_numeric($this->max_text_length_on_cell) && strlen($col_value->get_value()) > $this->max_text_length_on_cell) {
                            $col_value->set_value(substr($col_value->get_value(), 0, $this->max_text_length_on_cell) . "...");
                        } else {
                            
                        }
                    }
                    $last_td = $tr->append_td($col_value);
//                    if ($this->has_header && $row !== 0) {
//                        $last_td->set_attrib('data-label', trim($this->data[0][$field]->value), TRUE);
//                    }
                }
            }
            $row++;
        }
        return $this;
    }

    public function get_fields_for_key_array_text() {
        return $this->fields_for_key_array_text;
    }

    public function set_fields_for_key_array_text(array $fields_for_key_array_text) {
        $this->fields_for_key_array_text = $fields_for_key_array_text;
    }

    public function insert_tag_on_field(\k1lib\html\tag $tag_object, array $fields_to_insert, $tag_attrib_to_use = NULL, $append = FALSE, $respect_blanks = FALSE, $just_replace_attribs = FALSE, $just_this_row = NULL) {
        $row = 0;
//        if ($just_replace_attribs) {
//            echo "child call - row_key:$just_this_row<br>";
//        } else {
//            echo "normal call <br>";
//        }
        foreach ($this->data_original as $row_index => $row_data) {
            $row++;
            if ($just_this_row !== NULL && $just_this_row != $row_index) {
//                echo "child: $row_index:$just_this_row <br>";
                continue;
            }
//            else {
//                if ($just_this_row !== NULL) {
//                    echo "this is the ROW: $row_index:$just_this_row <br>";
//                }
//            }
            // NOT on the HEADERS
            if ($this->has_header && $row == 1) {
                continue;
            }
            $col = 0;
            foreach ($row_data as $field => $col_value) {
                $col++;
                if (empty($this->data_original[$row_index][$field]) && $respect_blanks) {
                    continue;
                }
                // FIELD HIDE, don't waste CPU power ;)
                if (array_search($field, $this->fields_to_hide) !== FALSE) {
                    continue;
                }
                // Field to insert
                if (array_search($field, $fields_to_insert) !== FALSE) {
                    // CLONE the TAG object to apply on each field necessary
                    if (!$just_replace_attribs) {
                        $tag_object_copy = clone $tag_object;
                    } else {
                        $tag_object_copy = $tag_object;
                    }

                    // IF the value is empty, we have to put the field value on it
                    if (empty($tag_attrib_to_use)) {
                        if (empty($tag_object_copy->get_value())) {
                            $tag_object_childs = $tag_object_copy->get_childs();
                            if (!empty($tag_object_childs)) {
//                                echo "childs!  [$row_index][$field] <br>";
                                foreach ($tag_object_childs as $child_key => $tag_object_child) {
//                                    echo "$tag_object $tag_object_child <br>";
                                    $tag_object_child_copy = clone $tag_object_child;
                                    $this->insert_tag_on_field($tag_object_child_copy, $fields_to_insert, $tag_attrib_to_use, $append, $respect_blanks, TRUE, $row_index);
                                    $tag_object_copy->replace_child($child_key, $tag_object_child_copy);
//                                    echo "$tag_object_copy $tag_object_child_copy <br>";
                                }
                            } else {
                                $tag_object_copy->set_value($this->parse_string_value($col_value, $row_index));
                            }
                        } else {
                            $tag_object_copy->set_value($this->parse_string_value($tag_object_copy->get_value(), $row_index));
                        }
                    } else {
                        $tag_object_copy->set_attrib($tag_attrib_to_use, $this->parse_string_value($col_value, $row_index));
                    }
                    foreach ($tag_object_copy->get_attributes_array() as $attribute => $value) {
                        if ($attribute == $tag_attrib_to_use) {
                            continue;
                        }
                        $tag_object_copy->set_attrib($attribute, $this->parse_string_value($value, $row_index));
                    }
                    if (!$just_replace_attribs) {
                        $this->data[$row_index][$field] = $tag_object_copy;
                    }
                }
            }
            if ($just_this_row !== NULL && $just_this_row == $row_index) {
//                echo "END child: $just_this_row <br>";
                break;
            }
        }
        return $this;
    }

    protected function parse_string_value($value, $row) {
        foreach ($this->get_fields_on_string($value) as $field) {
            if (array_key_exists($field, $this->data_original[$row])) {
                /**
                 * AUTH-CODE 
                 */
                $key_array = [];
                foreach ($this->fields_for_key_array_text as $field_for_key_array_text) {
                    $key_array[] = $this->data_original[$row][$field_for_key_array_text];
                }
                $key_array_text = implode("--", $key_array);
                if (!empty($key_array_text)) {
                    $auth_code = md5(\k1lib\K1MAGIC::get_value() . $key_array_text);
                } else {
                    $auth_code = NULL;
                }
                if (strstr($value, "--authcode--") !== FALSE) {
                    $value = str_replace("--authcode--", $auth_code, $value);
                }
                /**
                 * {{field:NAME}}
                 */
                $field_tag = "{{field:" . $field . "}}";
                $value = str_replace($field_tag, rawurlencode($this->data_original[$row][$field]), $value);
            }
        }
        foreach ($this->get_raw_fields_on_string($value) as $field) {
            if (array_key_exists($field, $this->data_original[$row])) {
                /**
                 * AUTH-CODE 
                 */
                $key_array = [];
                foreach ($this->fields_for_key_array_text as $field_for_key_array_text) {
                    $key_array[] = $this->data_original[$row][$field_for_key_array_text];
                }
                $key_array_text = implode("--", $key_array);
                if (!empty($key_array_text)) {
                    $auth_code = md5(\k1lib\K1MAGIC::get_value() . $key_array_text);
                } else {
                    $auth_code = NULL;
                }
                if (strstr($value, "--authcode--") !== FALSE) {
                    $value = str_replace("--authcode--", $auth_code, $value);
                }
                /**
                 * {{field:NAME}}
                 */
                $field_tag = "{{field-raw:" . $field . "}}";
                $value = str_replace($field_tag, $this->data_original[$row][$field], $value);
            }
        }
        return $value;
    }

    protected function get_fields_on_string($value) {
        $pattern = "/{{field:(\w+)}}/";
        $matches = [];
        $fields = [];
        if (preg_match_all($pattern, $value, $matches)) {
            foreach ($matches[1] as $field) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    protected function get_raw_fields_on_string($value) {
        $pattern = "/{{field-raw:(\w+)}}/";
        $matches = [];
        $fields = [];
        if (preg_match_all($pattern, $value, $matches)) {
            foreach ($matches[1] as $field) {
                $fields[] = $field;
            }
        }
        return $fields;
    }

    public function hide_fields(array $fields) {
        $this->fields_to_hide = $fields;
        return $this;
    }

    public function has_header() {
        return $this->has_header;
    }

    public function get_max_text_length_on_cell() {
        return $this->max_text_length_on_cell;
    }

    public function set_max_text_length_on_cell($max_text_length_on_cell) {
        $this->max_text_length_on_cell = $max_text_length_on_cell;
        return $this;
    }

    public function set_float_round($round_places) {
//        if (is_int($round_places)) {
        $this->float_round = $round_places;
//        }
    }

    public function get_float_round() {
        return $this->float_round;
    }
}
