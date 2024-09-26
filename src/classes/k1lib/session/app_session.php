<?php

namespace k1lib\session;

use EndyJasmi\Cuid;
use k1lib\K1MAGIC;
use WhichBrowser\Parser;
use function getallheaders;
use function k1lib\html\html_header_go;

class app_session {

    /**
     * Enable state
     * @var bool 
     */
    static protected $enabled = FALSE;

    /**
     * If TRUE on IP change the session will be invalidated
     * @var bool
     */
    static public $use_ip_in_userhash = TRUE;

    /**
     * Logged state
     * @var bool
     */
    static protected $has_started;

    /**
     * Keeps the logged state
     * @var bool
     */
    static protected $is_logged;

    /**
     *
     * @var string 
     */
    static protected int|string $user_login = -1;

    /**
     *
     * @var string 
     */
    static protected bool|string $user_hash = false;

    /**
     *
     * @var string 
     */
    static protected bool|string $user_level = 'guest';

    /**
     * the user levels ['user', 'guest'] are defautls
     * @var array
     */
    static protected $app_user_levels = ['user', 'guest'];

    /**
     * Session name for the PHP session handler
     * @var string 
     */
    static protected $session_name;

    /**
     * User session data
     * @var array
     */
    static public array $session_data = [];

    /**
     * URL for default login redirection
     * @var string
     */
    static protected $log_form_url;

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
    }

    static public function reset_session_data() {
        self::$user_login = -1;
        self::$user_hash = false;
        self::$user_level = 'guest';
        self::$session_data = [];
        $_SESSION['k1lib_session']['user_login'] = false;
        $_SESSION['k1lib_session']['user_hash'] = false;
        $_SESSION['k1lib_session']['user_level'] = 'guest';
        $_SESSION['k1lib_session']['user_data'] = [];
    }

    static public function start_session() {
        self::is_enabled(true);

        ini_set('session.use_strict_mode', 1);
        if (isset($_COOKIE[self::$session_name])) {
            session_id($_COOKIE[self::$session_name]);
        }
        session_name(self::$session_name);
        session_start();
        // Do not allow to use too old session ID
//        if (!empty($_SESSION['deleted_time']) && $_SESSION['deleted_time'] < time() - 180) {
//            session_destroy();
//            session_start();
//        }
        self::$has_started = TRUE;
        /**
         * TODO: ENCRYPT THIS !!
         */
        if (!isset($_SESSION['k1lib_session']['user_login'])) {
            self::reset_session_data();
        } else {
            self::load_logged_session();
        }
    }

    static function regenerate_id() {
        // Call session_create_id() while session is active to 
        // make sure collision free.
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        // WARNING: Never use confidential strings for prefix!
        $newid = session_create_id('k1app-');
        // Set deleted timestamp. Session data must not be deleted immediately for reasons.
        $_SESSION['deleted_time'] = time();
        // Finish session
        session_commit();
        // Make sure to accept user defined session ID
        // NOTE: You must enable use_strict_mode for normal operations.
        ini_set('session.use_strict_mode', 0);
        // Set new custom session ID
        session_id($newid);
        // Start with custom session ID
        session_start();
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

        session_commit();

        self::regenerate_id();
        self::reset_session_data();
    }

    static public function start_logged_session($login, array $user_data = [], $user_level = 'guest') {
        if (self::is_enabled(true)) {

            $_SESSION['k1lib_session']['user_login'] = $login;
            $_SESSION['k1lib_session']['user_hash'] = self::get_user_hash($login);
            $_SESSION['k1lib_session']['user_level'] = $user_level;
            $_SESSION['k1lib_session']['user_data'] = $user_data;

            return self::load_logged_session();
        } else {
            return FALSE;
        }
    }

    static public function get_user_data() {
        if (self::is_enabled(true)) {
            if (!empty($_SESSION['k1lib_session']['user_data'])) {
                return $_SESSION['k1lib_session']['user_data'];
            } else {
                return [];
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
                html_header_go(self::$log_form_url);
            } else {
                html_header_go($where_redirect_to);
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
                self::$user_hash = false;
                self::$user_level = 'guest';
                self::$session_data = [];
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
        if (self::$use_ip_in_userhash) {
            return md5($user_login . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . K1MAGIC::get_value());
        } else {
            return md5($user_login . $_SERVER['HTTP_USER_AGENT'] . K1MAGIC::get_value());
        }
    }

    public static function get_use_ip_in_userhash() {
        return self::$use_ip_in_userhash;
    }

    public static function set_use_ip_in_userhash($use_ip_in_userhash) {
        if ($use_ip_in_userhash) {
            self::$use_ip_in_userhash = TRUE;
        } else {
            self::$use_ip_in_userhash = FALSE;
        }
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
            if (self::$user_level === $level) {
                return TRUE;
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

    /**
     * Terminal info and unique finger_print
     */

    /**
     * Get a unique ID as CUID
     * @return string
     */
    public static function get_cuid() {
        return Cuid::make();
    }

    /**
     * Get an array with browser parsed info
     * @param string $user_agent
     * @return array
     */
    public static function get_terminal_info_array($user_agent = NULL) {
        if (empty($user_agent)) {
            $user_agent = $_SERVER['HTTP_USER_AGENT'];
        }
        $result = new Parser($user_agent);

        $terminar_array = [
            'browser_name' => $result->browser->getName(),
            'browser_version' => $result->browser->getVersion(),
            'os_name' => $result->os->getName(),
            'os_version' => $result->os->getName(),
            'device_type' => $result->device->type,
            'device_manufacturer' => $result->device->getManufacturer(),
            'device_model' => $result->device->getModel(),
        ];

        return $terminar_array;
    }

    /**
     * Get a browser fingerprint
     * @return string
     */
    public static function get_browser_fp($magic_value, $return_array = false) {
        $headers = getallheaders();
        $headers['client_ip'] = $_SERVER['REMOTE_ADDR'];
        unset($headers['Cookie']);
        unset($headers['Cache-Control']);
        ksort($headers);

        if ($return_array) {
            return $headers;
        } else {
            $fp = md5(implode('-', $headers)) . $magic_value;
            return $fp;
        }
    }
}
