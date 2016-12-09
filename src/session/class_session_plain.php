<?php

namespace k1lib\session;

use k1lib\notifications\on_DOM as DOM_notifications;

class session_plain {

    /**
     * Enable state
     * @var Boolean 
     */
    static private $enabled = FALSE;

    /**
     * If TRUE on IP change the session will be invalidated
     * @var boolean
     */
    static public $use_ip_in_userhash = TRUE;

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

}

class session_db extends session_plain {

    /**
     * @var \k1lib\crudlexs\class_db_table
     */
    private $db_table;

    /**
     * @var \PDO
     */
    private $db_object;

    /**
     * @var array
     */
    protected $user_data = [];

    /**
     * @var string
     */
    protected $user_login_db_table = NULL;

    /**
     * @var string
     */
    protected $user_login_field = NULL;

    /**
     * @var string
     */
    protected $user_login_input_name = NULL;

    /**
     * @var string
     */
    protected $user_login_input_value = NULL;

    /**
     * @var string
     */
    protected $user_password_field = NULL;

    /**
     * @var string
     */
    protected $user_password_input_name = NULL;

    /**
     * @var string
     */
    protected $user_password_input_value = NULL;

    /**
     * @var boolean
     */
    static $user_password_use_md5 = TRUE;

    /**
     * @var string
     */
    protected $user_level_field = NULL;

    /**
     * @var string
     */
    protected $user_remember_me_input = NULL;

    /**
     * @var string
     */
    protected $user_remember_me_value = FALSE;

    /**
     * @var string
     */
    protected $save_cookie_name = NULL;

    /**
     * @var string
     */
    protected $coockie_data = NULL;

    public function __construct(\PDO $db) {
        $this->db_object = $db;
        $this->save_cookie_name = session_plain::get_session_name() . "-store";
    }

    public function set_config($login_db_table, $user_login_field, $user_password_field, $user_level_field = NULL) {
        $this->user_login_db_table = $login_db_table;
        $this->db_table = new \k1lib\crudlexs\class_db_table($this->db_object, $this->user_login_db_table);
        if ($this->db_table->get_state()) {
            $this->user_login_field = $user_login_field;
            $this->user_password_field = $user_password_field;
            $this->user_level_field = $user_level_field;
        }
    }

    public function set_inputs($user_login_input_name, $user_password_input_name, $remember_me_input = NULL) {
        $this->user_login_input_name = $user_login_input_name;
        $this->user_password_input_name = $user_password_input_name;
        $this->user_remember_me_input = $remember_me_input;
    }

    public function catch_post() {
        if (isset($_POST['magic_value'])) {
            $magic_test = \k1lib\common\check_magic_value("login_form", $_POST['magic_value']);
            if ($magic_test == TRUE) {
                unset($_POST['magic_value']);
                // the form was correct, so lets try to login

                /**
                 * Check the _GET incomming vars
                 */
                $form_values = \k1lib\forms\check_all_incomming_vars($_POST, "k1lib_login");

                /**
                 * Login fields
                 */
                if (isset($form_values[$this->user_login_input_name]) && isset($form_values[$this->user_password_input_name])) {

                    $this->user_login_input_value = $form_values[$this->user_login_input_name];
                    $this->user_password_input_value = (self::$user_password_use_md5) ? md5($form_values[$this->user_password_input_name]) : $form_values[$this->user_password_input_name];

                    if (isset($form_values[$this->user_remember_me_input])) {
                        $this->user_remember_me_value = $form_values[$this->user_remember_me_input];
                    }
                } else {
                    return NULL;
                }

//                $filter_array = [
//                    $this->user_login_input_name => $this->user_login_input_value,
//                    $this->user_password_input_name => $this->user_password_input_value,
//                ];
//                $this->db_table->set_query_filter($filter_array, TRUE);
                return $form_values;
            } else {
                return FALSE;
            }
        } else {
            DOM_notifications::queue_mesasage("There is not magic present here!", "alert");
            return NULL;
        }
    }

    public function get_remember_me_value() {
        return $this->user_remember_me_value;
    }

    public function set_user_remember_me_value($user_remember_me_value) {
        $this->user_remember_me_value = $user_remember_me_value;
    }

    public function check_login() {
//        /**
//         * SQL check
//         */
        $fielter_array = [
            $this->user_login_field => $this->user_login_input_value,
            $this->user_password_field => $this->user_password_input_value,
        ];
        $this->db_table->set_query_filter($fielter_array, TRUE);
        $this->user_data = $this->db_table->get_data(FALSE);
        return $this->user_data;
    }

    public function save_data_to_coockie($path = "/") {
        $data = [
            'db_table_name' => $this->db_table->get_db_table_name(),
            'user_login_field' => $this->user_login_field,
            'user_login_input_value' => $this->user_login_input_value,
            'user_login_input_name' => $this->user_login_input_name,
            'user_password_field' => $this->user_password_field,
            'user_password_input_value' => $this->user_password_input_value,
            'user_password_input_name' => $this->user_password_input_name,
            'user_remember_me_input' => $this->user_remember_me_input,
            'user_level_field' => $this->user_level_field,
            'user_hash' => parent::get_user_hash($this->user_login_input_value),
        ];
        $data_encoded = \k1lib\crypt::encrypt($data);
        if ($this->user_remember_me_value) {
            $coockie_time = time() + (15 * 60 * 60 * 24);
        } else {
            $coockie_time = time() + (10);
        }
        $this->coockie_data = $data_encoded;
        $coockie = setcookie($this->save_cookie_name, $data_encoded, $coockie_time, $path);
    }

    public function load_data_from_coockie($return_coockie_data = FALSE) {
        if (!empty($this->coockie_data)) {
            $_COOKIE[$this->save_cookie_name] = $this->coockie_data;
        }
        if (isset($_COOKIE[$this->save_cookie_name])) {

            $data = \k1lib\crypt::decrypt($_COOKIE[$this->save_cookie_name]);

            if ($return_coockie_data) {
                return $data;
            } else {

                if ($data['user_hash'] === self::get_user_hash($data['user_login_input_value'])) {
                    $this->set_config($data['db_table_name'], $data['user_login_field'], $data['user_password_field'], $data['user_level_field']);
                    $this->set_inputs($data['user_login_input_name'], $data['user_password_input_name'], $data['user_remember_me_input']);
                    $this->user_login_input_value = $data['user_login_input_value'];
                    $this->user_password_input_value = $data['user_password_input_value'];
                    $this->user_remember_me_value = $data['user_remember_me_value'];

                    $user_data = $this->check_login();
                    if ($user_data) {
                        $this->user_data = $user_data;
                        $this->start_logged_session($user_data[$this->user_login_field], $user_data, $user_data[$this->user_level_field]);
                        return $user_data;
                    } else {
                        return FALSE;
                    }
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        }
    }

    public function unset_coockie($path = "/") {
        if (isset($_COOKIE[$this->save_cookie_name])) {
            unset($_COOKIE[$this->save_cookie_name]);
        }
        setcookie($this->save_cookie_name, '', time() - (60 * 60 * 24), $path);
    }

    public function load_logged_session_db($redirect = FALSE, $where_redirect_to = "") {
        if (!parent::load_logged_session($redirect, $where_redirect_to)) {
            $cookie_data = $this->load_data_from_coockie();
            return $cookie_data;
        } else {
            return TRUE;
        }
    }

    static function get_user_password_use_md5() {
        return self::$user_password_use_md5;
    }

    static function set_user_password_use_md5($user_password_use_md5) {
        self::$user_password_use_md5 = $user_password_use_md5;
    }

    public function get_user_login_db_table() {
        return $this->user_login_db_table;
    }

    public function get_user_login_field() {
        return $this->user_login_field;
    }

    public function get_user_login_input_name() {
        return $this->user_login_input_name;
    }

    public function get_user_password_field() {
        return $this->user_password_field;
    }

    public function get_user_password_input_name() {
        return $this->user_password_input_name;
    }

    public function get_user_level_field() {
        return $this->user_level_field;
    }

    public function set_user_login_db_table($user_login_db_table) {
        $this->user_login_db_table = $user_login_db_table;
    }

    public function set_user_login_field($user_login_field) {
        $this->user_login_field = $user_login_field;
    }

    public function set_user_login_input_name($user_login_input_name) {
        $this->user_login_input_name = $user_login_input_name;
    }

    public function set_user_password_field($user_password_field) {
        $this->user_password_field = $user_password_field;
    }

    public function set_user_password_input_name($user_password_input_name) {
        $this->user_password_input_name = $user_password_input_name;
    }

    public function set_user_level_field($user_level_field) {
        $this->user_level_field = $user_level_field;
    }

    public function get_user_remember_me_input() {
        return $this->user_remember_me_input;
    }

    public function set_user_remember_me_input($user_remember_me_input) {
        $this->user_remember_me_input = $user_remember_me_input;
    }

}
