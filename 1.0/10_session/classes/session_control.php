<?php

namespace k1lib\session\classes;

class session_plain {

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;

    /**
     * Logged state
     * @var Boolean
     */
    static private $has_started;

    /**
     * Keeps the logged state
     * @var Boolean
     */
    static private $is_logged;

    /**
     * Session name for the PHP session handler
     * @var String 
     */
    static private $session_name;

    /**
     * User session data
     * @var Array
     */
    static public $session_data;

    /**
     * Enable the engenie
     */
    static public function enable() {
        self::$enabled = TRUE;
        self::$has_started = FALSE;
        self::$is_logged = FALSE;
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled() {
        return self::$enabled;
    }

    static public function get_session_name() {
        return session_name();
    }

    static public function set_session_name($session_name) {
        self::$session_name = $session_name;
        \session_name($session_name);
    }

    static public function start_session() {
        \session_start();
        self::$has_started = TRUE;
        /**
         * TODO: ENCRYPT THIS !!
         */
        if (!isset($_SESSION['k1lib_session']['user_id'])) {
            $_SESSION['k1lib_session']['user_id'] = NULL;
            $_SESSION['k1lib_session']['user_hash'] = NULL;
            $_SESSION['k1lib_session']['user_level'] = 0;
            $_SESSION['k1lib_session']['user_data'] = NULL;
        }
    }

    static public function on_session() {
        if (self::$has_started) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static public function end_session() {
        self::$enabled = TRUE;
        self::$has_started = FALSE;
        self::$is_logged = FALSE;
        self::$session_data = array();
        session_destroy();
        session_unset();
    }

    static public function start_logged_session($user_id, $user_level = 0, $user_data = NULL) {
        if (self::is_enabled()) {
            $_SESSION['k1lib_session']['user_id'] = $user_id;
            $_SESSION['k1lib_session']['user_hash'] = self::get_client_hash($user_id);
            $_SESSION['k1lib_session']['user_level'] = $user_level;
            if (is_array($user_data)) {
                $_SESSION['k1lib_session']['user_data'] = $user_data;
            } else {
                trigger_error("user_data HAVE TO BE an ARRAY" . __FUNCTION__, E_USER_ERROR);
            }
            return self::load_logged_session();
        } else {
            return FALSE;
        }
    }

    static public function is_logged($redirect = FALSE, $where_redirect_to = "") {
        if ((self::is_enabled()) && (self::$has_started) && (isset(self::$session_data['user_hash']))) {
            if (self::$session_data['user_hash'] == self::get_client_hash(self::$session_data['user_id'])) {
                return TRUE;
            } else {
                self::end_session();
            }
        }
        if (!$redirect) {
            return FALSE;
        } else {
            ob_clean();
            \k1lib\html\html_header_go($where_redirect_to);
            exit;
        }
    }

    static public function load_logged_session($redirect = FALSE, $where_redirect_to = "") {
        if ((self::is_enabled()) && (self::$has_started)) {
            if ($_SESSION['k1lib_session']['user_hash'] === self::get_client_hash($_SESSION['k1lib_session']['user_id'])) {
                self::$session_data['user_id'] = $_SESSION['k1lib_session']['user_id'];
                self::$session_data['user_hash'] = $_SESSION['k1lib_session']['user_hash'];
                self::$session_data['user_level'] = $_SESSION['k1lib_session']['user_level'];
                self::$session_data['user_data'] = $_SESSION['k1lib_session']['user_data'];
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    static public function get_client_hash() {
        if (isset($_SESSION['k1lib_session']['user_id'])) {
            return md5($_SESSION['k1lib_session']['user_id'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . \k1lib\MAGIC_VALUE);
        } else {
            return FALSE;
        }
    }

}
