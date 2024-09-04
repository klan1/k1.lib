<?php

namespace k1lib\session;

use k1lib\html\notifications\on_DOM as DOM_notifications;

class session_db extends session_plain {

    /**
     * @var \k1lib\crudlexs\db_table
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
     * @var bool
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
        $this->db_table = new \k1lib\crudlexs\db_table($this->db_object, $this->user_login_db_table);
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

    public function catch_post($skip_magic = FALSE) {
        if (isset($_POST['magic_value'])) {
            $magic_test = \k1lib\common\check_magic_value("login_form", $_POST['magic_value']);
            if (($magic_test == TRUE) || ($skip_magic)) {
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
        $this->db_table->set_query_filter($fielter_array, TRUE, FALSE);
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
            'user_remember_me_value' => $this->user_remember_me_value,
            'user_level_field' => $this->user_level_field,
            'user_hash' => parent::get_user_hash($this->user_login_input_value),
        ];
        $data_encoded = \k1lib\crypt::encrypt($data);
        if ($this->user_remember_me_value) {
            $coockie_time = time() + (15 * 60 * 60 * 24);
        } else {
            $coockie_time = 0;
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
        $this->save_cookie_name = session_plain::get_session_name() . "-store";
        if (isset($_COOKIE[$this->save_cookie_name])) {
            unset($_COOKIE[$this->save_cookie_name]);
        }
        setcookie($this->save_cookie_name, '', time() - (60 * 60 * 24), $path);
    }

    public function load_logged_session_db($redirect = FALSE, $where_redirect_to = "") {
        $this->save_cookie_name = session_plain::get_session_name() . "-store";

        if (!parent::load_logged_session($redirect, $where_redirect_to)) {
            $cookie_data = $this->load_data_from_coockie();
            return $cookie_data;
        } else {
            if (isset($_COOKIE[$this->save_cookie_name])) {
                $data = \k1lib\crypt::decrypt($_COOKIE[$this->save_cookie_name]);
                if ($data['user_hash'] === self::get_user_hash($data['user_login_input_value'])) {
                    $this->set_config($data['db_table_name'], $data['user_login_field'], $data['user_password_field'], $data['user_level_field']);
                    $this->set_inputs($data['user_login_input_name'], $data['user_password_input_name'], $data['user_remember_me_input']);
                    $this->user_login_input_value = $data['user_login_input_value'];
                    $this->user_password_input_value = $data['user_password_input_value'];
                    $this->user_remember_me_value = $data['user_remember_me_value'];
                    $user_data = $this->check_login();
                    if ($user_data) {
                        $_SESSION['k1lib_session']['user_data'] = $user_data;
                        $this->user_data = $user_data;
                    }
                }
            }
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

    public static function end_session($path = '/') {
//        $this->save_cookie_name = session_plain::get_session_name() . "-store";
        $save_cookie_name = session_plain::get_session_name() . "-store";
        setcookie($save_cookie_name, '', time() - (60 * 60 * 24), $path);
//        if (isset($save_cookie_name)) {
//            unset($save_cookie_name);
//        }
        parent::end_session();
    }

}
