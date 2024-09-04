<?php

/**
 * On screen solution for show messages to user.
 *
 * @author J0hnD03 <alejandro.trujillo@klan1.com>
 * @version 1.0
 * @package notifications
 */

namespace k1lib\html\notifications;

class common_code {

    /**
     *
     * @var int 
     */
    static protected $data_count = 0;

    /**
     * Stores the SQL data
     * @var array
     */
    static protected $data = array();

    /**
     * Stores the SQL data
     * @var array
     */
    static protected $data_titles = array();

    /**
     * Enable the engenie
     */
    static public function test() {
        if (!class_exists('\k1lib\html\DOM', FALSE)) {
            trigger_error(__CLASS__ . " needs \k1lib\html\DOM class", E_USER_ERROR);
        }
    }

    static public function get_data() {
        self::is_enabled(true);
        return self::$data;
    }

    /**
     * 
     * @param string $section
     * @param string $message
     * @param string $type
     * @param string $tag_id
     */
    static protected function _queue_mesasage($section, $message, $type, $tag_id) {
//        self::$data[$section][$tag_id][$type][] = $message;
        $_SESSION['k1lib_notifications'][$section][$tag_id][$type][] = $message;
        if (empty(self::$data)) {
            self::$data = & $_SESSION['k1lib_notifications'];
        }
    }

    static protected function _queue_mesasage_title($section, $title, $type) {
//        self::$data_titles[$section][$type] = $title;
        $_SESSION['k1lib_notifications_titles'][$section][$type] = $title;
        if (empty(self::$data_titles)) {
            self::$data_titles = & $_SESSION['k1lib_notifications_titles'];
        }
    }

    static public function clean_queue() {
        unset($_SESSION['k1lib_notifications']);
        unset($_SESSION['k1lib_notifications_titles']);
    }

}