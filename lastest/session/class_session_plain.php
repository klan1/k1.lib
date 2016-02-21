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
     *
     * @var string 
     */
    static private $user_login = null;

    /**
     *
     * @var string 
     */
    static private $user_hash = null;

    /**
     *
     * @var string 
     */
    static private $user_level = null;

    /**
     * the user levels ['user', 'guest'] are defautls
     * @var array
     */
    static private $app_user_levels = ['user', 'guest'];

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
        if (!isset($_SESSION['k1lib_session']['user_login'])) {
            $_SESSION['k1lib_session']['user_login'] = NULL;
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

        self::$user_login = NULL;
        self::$user_hash = NULL;
        self::$user_level = NULL;
        self::$session_data = array();

        session_destroy();
        session_unset();
    }

    static public function start_logged_session($login, $user_data = NULL, $user_level = 'guest') {
        if (self::is_enabled(true)) {

            $_SESSION['k1lib_session']['user_login'] = $login;
            $_SESSION['k1lib_session']['user_hash'] = self::get_user_hash($login);
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

    static public function get_user_data() {
        if (self::is_enabled(true)) {
            if (isset($_SESSION['k1lib_session']['user_data'])) {
                return $_SESSION['k1lib_session']['user_data'];
            } else {
                return FALSE;
            }
        }
    }

    static public function is_logged($redirect = FALSE, $where_redirect_to = "") {
        if ((self::is_enabled(true)) && (self::$has_started) && (isset(self::$user_hash))) {
            if (self::$user_hash == self::get_user_hash(self::$user_login)) {
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
            if ($_SESSION['k1lib_session']['user_hash'] === self::get_user_hash($_SESSION['k1lib_session']['user_login'])) {

                self::$user_login = $_SESSION['k1lib_session']['user_login'];
                self::$user_hash = $_SESSION['k1lib_session']['user_hash'];
                self::$user_level = $_SESSION['k1lib_session']['user_level'];

                self::$session_data = $_SESSION['k1lib_session'];

                return TRUE;
            } else {
                self::$user_login = -1;
                self::$user_level = 0;
//                self::$session_data['user_data'] = null;
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

    static public function get_user_hash($user_login = null) {
        if (empty($user_login)) {
            $user_login = self::$user_login;
        }
        return md5($user_login . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . \k1lib\K1MAGIC::get_value());
    }

    static function get_user_login() {
        return self::$user_login;
    }

    static function get_user_level() {
        return self::$user_level;
    }

    /**
     * Check if the current user level is present on a array of possible levels
     * @param array Levels to check the current user, use ['leve1','level2','levelN'] to fastest code ;)
     * @return boolean
     */
    static public function check_user_level(array $levels_to_check) {
        self::is_enabled(true);

        $has_access = FALSE;
        foreach ($levels_to_check as $level) {
            if (self::$user_level == $level) {
                $has_access = TRUE;
            }
        }
        return $has_access;
    }

    static public function clear_app_user_levels() {
        self::$app_user_levels = [];
    }

    static function get_app_user_levels() {
        return self::$app_user_levels;
    }

    static function set_app_user_levels(array $app_user_levels) {
        self::$app_user_levels = $app_user_levels;
    }

    static public function add_app_user_levels($level_name) {
        self::$app_user_levels[] = $level_name;
    }

    static public function remove_app_user_levels($level_name) {
        if (key_exists($level_name, array_flip(self::$app_user_levels))) {
            unset(self::$app_user_level[array_flip(self::$app_user_levels)[$level_name]]);
            return TRUE;
        } else {
            return FALSE;
        }
    }

}

class login_manager {

    /**
     *
     * @var \k1lib\crudlexs\class_db_table
     */
    protected $db_table;
    protected $user_login_field = NULL;
    protected $user_password_field = NULL;
    protected $user_level_field = NULL;
    protected $user_name_field = NULL;
    protected $user_email_field = NULL;

    public function __construct($db_table, $user_login_field, $user_password_field, $user_level_field = NULL, $user_name_field = NULL, $user_email_field = NULL) {
        
    }

}
