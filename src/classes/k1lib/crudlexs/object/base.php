<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage crudlexs\object
 * Base classes for CRUD operations providing common functionality for database table interactions.
 */

namespace k1lib\crudlexs\object;

use \k1lib\db\security\db_table_aliases as db_table_aliases;

/**
 * Base class for CRUD objects providing common functionality.
 * Defines constants for field selection modes and common properties.
 *
 * @package k1lib\crudlexs\object
 */
class base {

    /**
     * Use only key fields for operations.
     */
    const USE_KEY_FIELDS = 1;

    /**
     * Use all fields for operations.
     */
    const USE_ALL_FIELDS = 2;

    /**
     * Use only label fields for display.
     */
    const USE_LABEL_FIELDS = 3;

    /**
     * K1 magic value for hashing.
     * @var mixed
     */
    static protected $k1magic_value = null;

    /**
     * Database table object.
     * @var \k1lib\crudlexs\db_table
     */
    public $db_table;

    /**
     * Controller ID for object identification.
     * @var string
     */
    protected string $controller_id;

    /**
     * HTML div container for the object.
     * @var \k1lib\html\div
     */
    protected $div_container;

    /**
     * Unique ID for each object instance.
     * @var string
     */
    protected $object_id = null;

    /**
     * General CSS class for styling.
     * @var string
     */
    protected $css_class = null;

    /**
     * Validity state of the object.
     * @var bool
     */
    private $is_valid = FALSE;

    /**
     * HTML element ID for notifications.
     * @var string
     */
    protected $notifications_div_id = "k1lib-output";

    /**
     * Gets the K1 magic value used for hashing.
     *
     * @return mixed The magic value
     */
    static function get_k1magic_value() {
        return self::$k1magic_value;
    }

    /**
     * Sets the K1 magic value for hashing operations.
     *
     * @param mixed $k1magic_value The magic value to set
     */
    static function set_k1magic_value($k1magic_value) {
        self::$k1magic_value = $k1magic_value;
    }

    /**
     * Creates a base CRUD object with the specified database table.
     *
     * @param \k1lib\crudlexs\db_table $db_table Database table object
     */
    public function __construct(\k1lib\crudlexs\db_table $db_table) {
        $this->db_table = $db_table;
        $this->div_container = new \k1lib\html\div();
        $this->is_valid = TRUE;
    }

    /**
     * Checks if the object is valid.
     *
     * @return bool TRUE if valid, FALSE otherwise
     */
    function is_valid() {
        return $this->is_valid;
    }

    /**
     * Marks the object as invalid.
     */
    function make_invalid() {
        $this->is_valid = FALSE;
    }

    /**
     * Returns string representation of object state.
     *
     * @return string "1" if state is valid, "0" otherwise
     */
    public function __toString() {
        if ($this->get_state()) {
            return "1";
        } else {
            return "0";
        }
    }

    /**
     * Gets the current state of the object.
     *
     * @return bool TRUE if both database table and object are valid
     */
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

    /**
     * Gets the unique object identifier.
     *
     * @return string The object ID
     */
    function get_object_id() {
        return $this->object_id;
    }

    /**
     * Sets the unique object identifier.
     *
     * @param string $class_name Class name for ID generation
     * @return string The generated object ID
     */
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

    /**
     * Gets the CSS class for styling.
     *
     * @return string The CSS class
     */
    function get_css_class() {
        return $this->css_class;
    }

    /**
     * Sets the CSS class for styling.
     *
     * @param string $class_name The CSS class name
     */
    function set_css_class($class_name) {
        $this->css_class = basename(str_replace("\\", "/", $class_name));
    }

    /**
     * Gets the notifications div ID.
     *
     * @return string The notifications div ID
     */
    public function get_notifications_div_id() {
        return $this->notifications_div_id;
    }

    /**
     * Sets the notifications div ID.
     *
     * @param string $notifications_div_id The div ID
     */
    public function set_notifications_div_id($notifications_div_id) {
        $this->notifications_div_id = $notifications_div_id;
    }

    /**
     * Sets the controller ID for object identification.
     *
     * @param string $controller_id The controller ID
     */
    public function set_controller_id(string $controller_id): void {
        $this->controller_id = $controller_id;
    }
}
