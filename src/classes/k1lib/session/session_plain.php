<?php

namespace k1lib\session;

class session_plain {

    /**
     * Enable state
     * @var bool 
     */
    static private $enabled = FALSE;

    /**
     * If TRUE on IP change the session will be invalidated
     * @var bool
     */
    static public $use_ip_in_userhash = TRUE;

    /**
     * Logged state
     * @var bool
     */
    static private $has_started;

    /**
     * Keeps the logged state
     * @var bool
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
     * @var string 
     */
    static private $session_name;

    /**
     * User session data
     * @var array
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
    }

    static public function start_session() {
        self::is_enabled(true);
        \session_name(self::$session_name);
        \session_start();
        self::$has_started = TRUE;
        /**
         * TODO: ENCRYPT THIS !!
         */
        if (!isset($_SESSION['k1lib_session']['user_login'])) {
            $_SESSION['k1lib_session']['user_login'] = NULL;
            $_SESSION['k1lib_session']['user_hash'] = NULL;
            $_SESSION['k1lib_session']['user_level'] = 'guest';
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
            if (isset($_SESSION['k1lib_session']['user_data'])) {
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
                self::$user_level = 'guest';
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
        if (self::$use_ip_in_userhash) {
            return md5($user_login . $_SERVER['REMOTE_ADDR'] . $_SERVER['HTTP_USER_AGENT'] . \k1lib\K1MAGIC::get_value());
        } else {
            return md5($user_login . $_SERVER['HTTP_USER_AGENT'] . \k1lib\K1MAGIC::get_value());
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
        return \EndyJasmi\Cuid::make();
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
        $result = new \WhichBrowser\Parser($user_agent);

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
    public static function get_browser_fp($return_array = FALSE, $return_all = FALSE) {
        $headers = getallheaders();
        unset($headers['Cookie']);
        unset($headers['Cache-Control']);
        ksort($headers);

        if ($return_array) {
            return $headers;
        } else {
            $fp = md5(implode('-', $headers)) . \k1lib\MAGIC_VALUE;
            return $fp;
        }
    }

}
