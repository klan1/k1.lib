<?php

namespace k1lib\crudlexs\object;

use \k1lib\db\security\db_table_aliases as db_table_aliases;

class base {

    const USE_KEY_FIELDS = 1;
    const USE_ALL_FIELDS = 2;
    const USE_LABEL_FIELDS = 3;

    static protected $k1magic_value = null;

    /**
     *
     * @var \k1lib\crudlexs\db_table 
     */
    public $db_table;

    /**
     *
     * @var \k1lib\html\div
     */
    protected $div_container;

    /**
     * Unique ID for each object
     * @var string
     */
    protected $object_id = null;

    /**
     * General CSS class
     * @var string
     */
    protected $css_class = null;

    /**
     * If some goes BAD to do not keep going for others methods, you have to put this on FALSE;
     * @var bool
     */
    private $is_valid = FALSE;

    /**
     * @var string
     */
    protected $notifications_div_id = "k1lib-output";

    static function get_k1magic_value() {
        return self::$k1magic_value;
    }

    static function set_k1magic_value($k1magic_value) {
        self::$k1magic_value = $k1magic_value;
    }

    public function __construct(\k1lib\crudlexs\db_table $db_table) {
        $this->db_table = $db_table;
        $this->div_container = new \k1lib\html\div();
        $this->is_valid = TRUE;
    }

    function is_valid() {
        return $this->is_valid;
    }

    function make_invalid() {
        $this->is_valid = FALSE;
    }

    /**
     * Always to create the object you must have a valid DB Table object already 
     * @param \k1lib\crudlexs\db_table $db_table DB Table object
     */
    public function __toString() {
        if ($this->get_state()) {
            return "1";
        } else {
            return "0";
        }
    }

    public function get_state() {
        if (empty($this->db_table) || !$this->is_valid()) {
            return FALSE;
        } else {
            if ($this->db_table->get_state() || !$this->is_valid()) {
                return TRUE;
            } else {
                return FALSE;
            }
        }
    }

    function get_object_id() {
        return $this->object_id;
    }

    function set_object_id($class_name) {
        if (isset($this->db_table) && key_exists($this->db_table->get_db_table_name(), db_table_aliases::$aliases)) {
            $table_name = db_table_aliases::$aliases[$this->db_table->get_db_table_name()];
        } else if (isset($this->db_table)) {
            $table_name = $this->db_table->get_db_table_name();
        } else {
            $table_name = "no-table";
        }
        return $this->object_id = $table_name . "-" . basename(str_replace("\\", "/", $class_name));
    }

    function get_css_class() {
        return $this->css_class;
    }

    function set_css_class($class_name) {
        $this->css_class = basename(str_replace("\\", "/", $class_name));
    }

    public function get_notifications_div_id() {
        return $this->notifications_div_id;
    }

    public function set_notifications_div_id($notifications_div_id) {
        $this->notifications_div_id = $notifications_div_id;
    }
}
