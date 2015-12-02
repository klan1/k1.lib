<?php

namespace k1lib\session;

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
     * URL for default login redirection
     * @var string
     */
    static private $log_form_url;

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
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("Session system is not enabled yet", E_USER_ERROR);
        }
        return self::$enabled;
    }

    static public function get_session_name() {
        self::is_enabled(true);
        return session_name();
    }

    static public function set_session_name($session_name) {
        self::is_enabled(true);
        self::$session_name = $session_name;
        \session_name($session_name);
    }

    static public function start_session() {
        self::is_enabled(true);
        \session_start();
        self::$has_started = TRUE;
        /**
         * TODO: ENCRYPT THIS !!
         */
        if (!isset($_SESSION['k1lib_session']['login'])) {
            $_SESSION['k1lib_session']['login'] = NULL;
            $_SESSION['k1lib_session']['user_hash'] = NULL;
            $_SESSION['k1lib_session']['user_level'] = 0;
            $_SESSION['k1lib_session']['user_data'] = NULL;
        }
    }

    static public function on_session() {
        if (self::is_enabled() && self::$has_started) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    static public function end_session() {
        self::is_enabled(true);
        self::$enabled = TRUE;
        self::$has_started = FALSE;
        self::$is_logged = FALSE;
        self::$session_data = array();
        session_destroy();
        session_unset();
    }

    static public function start_logged_session($login, $user_level = 0, $user_data = NULL) {
        if (self::is_enabled(true)) {
            $_SESSION['k1lib_session']['login'] = $login;
            $_SESSION['k1lib_session']['user_hash'] = self::get_client_hash($login);
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
        if ((self::is_enabled(true)) && (self::$has_started) && (isset(self::$session_data['user_hash']))) {
            if (self::$session_data['user_hash'] == self::get_client_hash(self::$session_data['login'])) {
                return TRUE;
            } else {
                self::end_session();
            }
        }
        if (!$redirect) {
            return FALSE;
        } else {
            ob_clean();
            if (empty($where_redirect_to) && !empty(self::$log_form_url)) {
                \k1lib\html\html_header_go(self::$log_form_url);
            } else {
                \k1lib\html\html_header_go($where_redirect_to);
            }
            exit;
        }
    }

    static function set_log_form_url($log_form_url) {
        self::$log_form_url = $log_form_url;
    }

    static public function load_logged_session($redirect = FALSE, $where_redirect_to = "") {
        if ((self::is_enabled(true)) && (self::$has_started)) {
            if ($_SESSION['k1lib_session']['user_hash'] === self::get_client_hash($_SESSION['k1lib_session']['login'])) {
                self::$session_data['login'] = $_SESSION['k1lib_session']['login'];
                self::$session_data['user_hash'] = $_SESSION['k1lib_session']['user_hash'];
                self::$session_data['user_level'] = $_SESSION['k1lib_session']['user_level'];
                self::$session_data['user_data'] = $_SESSION['k1lib_session']['user_data'];
                return TRUE;
            } else {
                self::$session_data['login'] = -1;
                self::$session_data['user_level'] = 0;
//                self::$session_data['user_data'] = null;
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    static public function get_client_hash() {
        self::is_enabled(true);
        if (isset($_SESSION['k1lib_session']['login'])) {
            return md5($_SESSION['k1lib_session']['login'] . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . \k1lib\MAGIC_VALUE);
        } else {
            return FALSE;
        }
    }

    /**
     * Check the current user level VS a comma separated list as 1,2,3,4,5
     * @param string $levels_to_check comma separated list as "1,2,3,4,5"
     * @return boolean
     */
    static public function check_user_level($levels_to_check = "0") {
        self::is_enabled(true);
        if (!isset(self::$session_data['user_level'])) {
            trigger_error("No session to check", E_USER_ERROR);
        }
//    if (empty($levels_to_check) || (!is_string($levels_to_check) && !is_numeric($levels_to_check))) {
        // EMPTY fails with '0'
        if (!is_string($levels_to_check) && !is_numeric($levels_to_check)) {
            die("level_to_check have to be a string or numeric");
        }
        $levels = explode(",", $levels_to_check);
        $has_access = FALSE;
        foreach ($levels as $level) {
            if (self::$session_data['user_level'] == $level) {
                $has_access = TRUE;
            }
        }
        return $has_access;
    }

}
