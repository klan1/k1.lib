<?php

namespace k1lib\session;

use k1lib\crudlexs\db_table;
use k1lib\crypt;
use k1lib\html\notifications\on_DOM as DOM_notifications;
use PDO;
use function k1lib\common\check_magic_value;
use function k1lib\forms\check_all_incomming_vars;

class session_db {

    /**
     * @var db_table
     */
    static private $db_table;

    /**
     * @var PDO
     */
    static private $db_object;

    /**
     * @var array
     */
    static protected $user_data = [];

    /**
     * @var string
     */
    static protected $user_login_db_table = NULL;

    /**
     * @var string
     */
    static protected $user_login_field = NULL;

    /**
     * @var string
     */
    static protected $user_login_input_name = NULL;

    /**
     * @var string
     */
    static protected $user_login_input_value = NULL;

    /**
     * @var string
     */
    static protected $user_password_field = NULL;

    /**
     * @var string
     */
    static protected $user_password_input_name = NULL;

    /**
     * @var string
     */
    static protected $user_password_input_value = NULL;

    /**
     * @var bool
     */
    static $user_password_use_md5 = TRUE;

    /**
     * @var string
     */
    static protected $user_level_field = NULL;

    /**
     * @var string
     */
    static protected $user_remember_me_input = NULL;

    /**
     * @var string
     */
    static protected $user_remember_me_value = FALSE;

    /**
     * @var string
     */
    static protected $save_cookie_name = NULL;

    /**
     * @var string
     */
    static protected $coockie_data = NULL;

    static function init(PDO $db) {
        self::$db_object = $db;
        self::$save_cookie_name = app_session::get_session_name() . "-store";
    }

    static function set_config($login_db_table, $user_login_field, $user_password_field, $user_level_field = NULL) {
        self::$user_login_db_table = $login_db_table;
        self::$db_table = new db_table(self::$db_object, self::$user_login_db_table);
        if (self::$db_table->get_state()) {
            self::$user_login_field = $user_login_field;
            self::$user_password_field = $user_password_field;
            self::$user_level_field = $user_level_field;
        } else {
            trigger_error('Users table for login do not exist. ' . __CLASS__, E_USER_ERROR);
        }
    }

    static function set_inputs($user_login_input_name, $user_password_input_name, $remember_me_input = NULL) {
        self::$user_login_input_name = $user_login_input_name;
        self::$user_password_input_name = $user_password_input_name;
        self::$user_remember_me_input = $remember_me_input;
    }

    static function catch_post($skip_magic = FALSE) {
        if (isset($_POST['magic_value'])) {
            $magic_test = check_magic_value("login_form", $_POST['magic_value']);
            if (($magic_test == TRUE) || ($skip_magic)) {
                unset($_POST['magic_value']);
// the form was correct, so lets try to login

                /**
                 * Check the _GET incomming vars
                 */
                $form_values = check_all_incomming_vars($_POST, "k1lib_login");

                /**
                 * Login fields
                 */
                if (isset($form_values[self::$user_login_input_name]) && isset($form_values[self::$user_password_input_name])) {

                    self::$user_login_input_value = $form_values[self::$user_login_input_name];
                    self::$user_password_input_value = (self::$user_password_use_md5) ? md5($form_values[self::$user_password_input_name]) : $form_values[self::$user_password_input_name];

                    if (isset($form_values[self::$user_remember_me_input])) {
                        self::$user_remember_me_value = $form_values[self::$user_remember_me_input];
                    }
                } else {
                    return NULL;
                }

//                $filter_array = [
//                    self::$user_login_input_name => self::$user_login_input_value,
//                    self::$user_password_input_name => self::$user_password_input_value,
//                ];
//                self::$db_table->set_query_filter($filter_array, TRUE);
                return $form_values;
            } else {
                return FALSE;
            }
        } else {
            DOM_notifications::queue_mesasage("There is not magic present here!", "danger");
            return NULL;
        }
    }

//    static function get_remember_me_value() {
//        return self::$user_remember_me_value;
//    }
//    static function set_user_remember_me_value($user_remember_me_value) {
//        self::$user_remember_me_value = $user_remember_me_value;
//    }

    /**
     * Use the POST data to query con users table the user info if the password
     * was correct
     * @param string $login_type [text_plain|hash_md5]
     * @return type
     */
    static function check_login($login_type = 'text_plain') {
//        /**
//         * SQL check
//         */
        if ($login_type == 'text_plain') {
            $fielter_array = [
                self::$user_login_field => self::$user_login_input_value,
                self::$user_password_field => self::$user_password_input_value,
            ];
            self::$db_table->set_query_filter($fielter_array, TRUE, FALSE);
            self::$user_data = self::$db_table->get_data(FALSE);
        } elseif ($login_type == 'hash_md5') {
            // TODO: MD5 hash autentication
            die('Not yet ' . __METHOD__);
        }
        return self::$user_data;
    }

    static function save_data_to_coockie($path = "/", $user_data = []) {
        $data = [
            'db_table_name' => self::$db_table->get_db_table_name(),
            'user_login_field' => self::$user_login_field,
            'user_login_input_value' => self::$user_login_input_value,
            'user_login_input_name' => self::$user_login_input_name,
            'user_password_field' => self::$user_password_field,
            'user_password_input_value' => self::$user_password_input_value,
            'user_password_input_name' => self::$user_password_input_name,
            'user_remember_me_input' => self::$user_remember_me_input,
            'user_remember_me_value' => self::$user_remember_me_value,
            'user_level_field' => self::$user_level_field,
            'user_data' => $user_data,
            'user_hash' => app_session::get_user_hash(self::$user_login_input_value),
        ];
        $data_encoded = crypt::encrypt($data);
        if (self::$user_remember_me_value) {
            $coockie_time = time() + (15 * 60 * 60 * 24);
        } else {
            $coockie_time = 0;
        }
        self::$coockie_data = $data_encoded;
        setcookie(self::$save_cookie_name, $data_encoded, $coockie_time, $path);
    }

    static function load_data_from_coockie($do_credential_checks = true, $return_coockie_data = FALSE) {
        if (!empty(self::$coockie_data)) {
            $_COOKIE[self::$save_cookie_name] = self::$coockie_data;
        }
        if (isset($_COOKIE[self::$save_cookie_name])) {

            $data = crypt::decrypt($_COOKIE[self::$save_cookie_name]);

            if ($return_coockie_data) {
                return $data;
            } else {

                if ($data['user_hash'] === app_session::get_user_hash($data['user_login_input_value'])) {
                    self::set_config($data['db_table_name'], $data['user_login_field'], $data['user_password_field'], $data['user_level_field']);
                    self::set_inputs($data['user_login_input_name'], $data['user_password_input_name'], $data['user_remember_me_input']);
                    self::$user_login_input_value = $data['user_login_input_value'];
                    self::$user_password_input_value = $data['user_password_input_value'];
                    self::$user_remember_me_value = $data['user_remember_me_value'];

                    if ($do_credential_checks) {

                        $user_data = self::check_login();
                        if ($user_data) {
                            self::$user_data = $user_data;
                            app_session::start_logged_session(
                                    $user_data[self::$user_login_field],
                                    $user_data,
                                    $user_data[self::$user_level_field]
                            );
                            return $user_data;
                        } else {
                            return FALSE;
                        }
                    } else {
                        app_session::start_logged_session(
                                $data['user_data'][self::$user_login_field],
                                $data['user_data'],
                                $data['user_data'][self::$user_level_field]
                        );
                    }
                } else {
                    return FALSE;
                }
            }
        } else {
            return FALSE;
        }
    }

    static function load_logged_session_db($redirect = FALSE, $where_redirect_to = "") {
        self::$save_cookie_name = app_session::get_session_name() . "-store";

        if (!app_session::load_logged_session($redirect, $where_redirect_to)) {
            $cookie_data = self::load_data_from_coockie();
            return $cookie_data;
        } else {
            if (isset($_COOKIE[self::$save_cookie_name])) {
                $data = crypt::decrypt($_COOKIE[self::$save_cookie_name]);
                if ($data['user_hash'] === app_session::get_user_hash($data['user_login_input_value'])) {
                    self::set_config($data['db_table_name'], $data['user_login_field'], $data['user_password_field'], $data['user_level_field']);
                    self::set_inputs($data['user_login_input_name'], $data['user_password_input_name'], $data['user_remember_me_input']);
                    self::$user_login_input_value = $data['user_login_input_value'];
                    self::$user_password_input_value = $data['user_password_input_value'];
                    self::$user_remember_me_value = $data['user_remember_me_value'];
                    $user_data = self::check_login();
                    if ($user_data) {
                        $_SESSION['k1lib_session']['user_data'] = $user_data;
                        self::$user_data = $user_data;
                    }
                }
            }
            return TRUE;
        }
    }

    static function unset_coockie($path = "/") {
        self::$save_cookie_name = app_session::get_session_name() . "-store";
        if (isset($_COOKIE[self::$save_cookie_name])) {
            unset($_COOKIE[self::$save_cookie_name]);
        }
        setcookie(self::$save_cookie_name, '', time() - (60 * 60 * 24), $path);
    }

    static function end_session($path = '/') {
//        self::$save_cookie_name = app_session::get_session_name() . "-store";
        $save_cookie_name = app_session::get_session_name() . "-store";
        setcookie($save_cookie_name, '', time() - (60 * 60 * 24), $path);
//        if (isset($save_cookie_name)) {
//            unset($save_cookie_name);
//        }
        app_session::end_session();
    }

    ////
//    static function get_user_password_use_md5() {
//        return self::$user_password_use_md5;
//    }
//
//    static function set_user_password_use_md5($user_password_use_md5) {
//        self::$user_password_use_md5 = $user_password_use_md5;
//    }
//
//    static function get_user_login_db_table() {
//        return self::$user_login_db_table;
//    }
//
//    static function get_user_login_field() {
//        return self::$user_login_field;
//    }
//
//    static function get_user_login_input_name() {
//        return self::$user_login_input_name;
//    }
//
//    static function get_user_password_field() {
//        return self::$user_password_field;
//    }
//
//    static function get_user_password_input_name() {
//        return self::$user_password_input_name;
//    }
//
//    static function get_user_level_field() {
//        return self::$user_level_field;
//    }
//
//    static function set_user_login_db_table($user_login_db_table) {
//        self::$user_login_db_table = $user_login_db_table;
//    }
//
//    static function set_user_login_field($user_login_field) {
//        self::$user_login_field = $user_login_field;
//    }
//
//    static function set_user_login_input_name($user_login_input_name) {
//        self::$user_login_input_name = $user_login_input_name;
//    }
//
//    static function set_user_password_field($user_password_field) {
//        self::$user_password_field = $user_password_field;
//    }
//
//    static function set_user_password_input_name($user_password_input_name) {
//        self::$user_password_input_name = $user_password_input_name;
//    }
//
//    static function set_user_level_field($user_level_field) {
//        self::$user_level_field = $user_level_field;
//    }
//
//    static function get_user_remember_me_input() {
//        return self::$user_remember_me_input;
//    }
//
//    static function set_user_remember_me_input($user_remember_me_input) {
//        self::$user_remember_me_input = $user_remember_me_input;
//    }
}
