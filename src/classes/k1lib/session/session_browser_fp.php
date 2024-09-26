<?php

namespace k1lib\session;

use k1lib\crudlexs\db_table as db_table;
use k1lib\crudlexs\db_table as db_table2;
use k1lib\crypt;
use k1lib\html\notifications\on_DOM as DOM_notifications;
use k1lib\urlrewrite\url;
use PDO;
use Ramsey\Uuid\Uuid;
use function k1lib\forms\check_all_incomming_vars;
use function k1lib\html\html_header_go;

class session_browser_fp extends session_db {

    /**
     * @var string
     */
    private static $terminals_table_name = '';

    /**
     * @var string
     */
    private static $mobile_numbers_table_name = '';

    /**
     * @var string
     */
    private static $terminals_unique_table_name = '';

    /**
     * @var string 
     */
    private static $session_terminal_coockie_name;

    /**
     * @var db_table2
     */
    private static $terminals_table;

    /**
     * @var db_table2
     */
    private static $mobile_nombers_table;

    /**
     * @var db_table2
     */
    private static $terminals_unique_table;

    /**
     * @var string
     */
    private static $current_terminal_uuid = NULL;

    /**
     * @var string
     */
    private static $current_browser_fp = NULL;

    /**
     *
     * @var array
     */
    private static $current_browser_fp_data = [];

    /**
     * 
     * @param string $terminals_table_name
     * @param string $mobile_numbers_table_name
     * @param string $terminals_unique_table_name
     */
    public static function config($terminals_table_name, $mobile_numbers_table_name, $terminals_unique_table_name) {
        self::$terminals_unique_table_name = $terminals_unique_table_name;
        self::$terminals_table_name = $terminals_table_name;
        self::$mobile_numbers_table_name = $mobile_numbers_table_name;
    }

    function __construct(PDO $db) {
        // Parent assigns the db object to $db_object
        parent::__construct($db);

        /**
         * OPEN FP SYSTEM TABLES
         */
        if (!empty(self::$terminals_table_name)) {
            self::$terminals_table = new db_table($db, self::$terminals_table_name);
            if (!self::$terminals_table->get_state()) {
                trigger_error('Terminals Table "' . self::$terminals_table_name . '" not found', E_USER_ERROR);
            } else {
//                d(self::$terminals_table->get_db_table_config());
            }
        } else {
            trigger_error('Terminals Table "' . self::$terminals_table_name . '" not found', E_USER_ERROR);
        }

        if (!empty(self::$mobile_numbers_table_name)) {
            self::$mobile_nombers_table = new db_table($db, self::$mobile_numbers_table_name);
            if (!self::$mobile_nombers_table->get_state()) {
                trigger_error('Mobile numbers Table "' . self::$mobile_numbers_table_name . '" not found', E_USER_ERROR);
            } else {
//                d(self::$mobile_nombers_table->get_db_table_config());
            }
        } else {
            trigger_error('Mobile numbers Table "' . self::$mobile_numbers_table_name . '" not found', E_USER_ERROR);
        }
        if (!empty(self::$terminals_unique_table_name)) {
            self::$terminals_unique_table = new db_table($db, self::$terminals_unique_table_name);
            if (!self::$terminals_unique_table->get_state()) {
                trigger_error('Unique Terminal-Numbers Table "' . self::$terminals_unique_table_name . '" not found', E_USER_ERROR);
            } else {
//                d(self::$terminals_unique_table->get_db_table_config());
            }
        } else {
            trigger_error('Unique Terminal-Numbers Table "' . self::$terminals_unique_table_name . '" not found', E_USER_ERROR);
        }
    }

    public static function start_session() {
        parent::start_session();
        $terminal_data = FALSE;
        self::$session_terminal_coockie_name = self::get_session_name() . '-bfp-' . md5(self::get_browser_fp());
        /**
         * INIT DATA ON TABLES
         */
        $actual_url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        /**
         * FP Cookie SET or READ
         */
        // SET COOKIE
        if (!isset($_GET['bfp'])) {
            if (empty($_COOKIE[self::$session_terminal_coockie_name])) {
                // GET UUID
                $uuid4 = Uuid::uuid4();
                // COOKIE will have value as: uuid,browser_fp
                $cookie_to_set_value = crypt::encrypt($uuid4->toString() . ',' . self::get_browser_fp());
                // Set the COOKIE 1 year from now
                setcookie(self::$session_terminal_coockie_name, $cookie_to_set_value, strtotime('+365 days'), '/');
                // Redirects the browser to the ACTUAL URL with $_GET['bfp']=md5(browser_fp) to test the cookie is really set.
                html_header_go(url::do_url($actual_url, ['bfp' => md5(self::get_browser_fp()), 'last_url' => $actual_url]));
            } else {
                $cookie_value = crypt::decrypt($_COOKIE[self::$session_terminal_coockie_name]);
                if (strstr($cookie_value, ',') !== FALSE) {
                    // Retrive COOKIE data as : $current_terminal_uuid,$current_browser_fp
                    $cookie_data = explode(',', $cookie_value);
                    self::$current_terminal_uuid = $cookie_data[0];
                    self::$current_browser_fp = $cookie_data[1];
                    //check Browser FP integrity
                    if (self::$current_browser_fp == self::get_browser_fp()) {
                        // Let's check if the current UUID exist as terminal on table
                        self::$terminals_table->set_query_filter(['terminal_uuid' => self::$current_terminal_uuid], TRUE);
                        $db_terminal_data = self::$terminals_table->get_data();
                        if ($db_terminal_data) {
                            $terminal_data = TRUE;
                        } else {
                            $terminad_data_array = array_merge(
                                    ['terminal_uuid' => self::$current_terminal_uuid, 'browser_fp' => self::$current_browser_fp]
                                    , self::get_terminal_info_array());
                            $errors = [];
                            if (self::$terminals_table->insert_data($terminad_data_array, $errors)) {
//                            d($errors, true);
                                $terminal_data = TRUE;
                                DOM_notifications::queue_mesasage('Terminal has been created. UUID: ' . self::$current_terminal_uuid, "success");
                            } else {
                                DOM_notifications::queue_mesasage('Terminal data couldn\'t be saved.', "alert");
                            }
                        }
                    } else {
                        trigger_error('Data from COOKIE seems to be from another browser/terminal. Good try.', E_USER_ERROR);
                        exit;
                    }
                } else {
                    setcookie(self::$session_terminal_coockie_name, $cookie_value, strtotime('-365 days'), '/');
                    trigger_error('Your session cookie is rotten and we had to delete it, please, don\'t try to hack us, we make our best to do not let you.', E_USER_ERROR);
                    exit;
                }
            }
        } else {
            /**
             * When $_GET['bfp'] isset means that we need to run a COOKIE test
             */
            if ($_GET['bfp'] != md5(self::get_browser_fp())) {
                trigger_error('Very bad BFP value, so, I dont want to keep going. ' . self::get_browser_fp(), E_USER_ERROR);
                exit;
            } else {
                if (empty($_COOKIE[self::$session_terminal_coockie_name])) {
                    trigger_error('Browser do not accept cookies and is not possible to keep going. Please enable them.', E_USER_ERROR);
                    exit;
                } else {
                    $get_vars = check_all_incomming_vars($_GET);
                    html_header_go($get_vars['last_url']);
                }
            }
        }
    }

    public static function end_session($path = '/') {
        setcookie(self::$session_terminal_coockie_name, $cookie_value, strtotime('-365 days'), '/');
        parent::end_session($path);
    }

}
