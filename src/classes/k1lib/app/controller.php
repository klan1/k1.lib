<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage app
 * Application Controller - Base controller class for K1 applications.
 */

namespace k1lib\app;

use k1app\core\config\general;
use k1app\template\mazer\layouts\blank;
use k1app\template\mazer\layouts\sidebar_page;
use k1app\template\mazer\layouts\single_page;
use k1lib\app;
use k1lib\crudlexs\object\base;
use k1lib\notifications\on_DOM as DOM_notifications;
use k1lib\session\app_session;
use const k1app\K1APP_URL;
use function k1lib\forms\check_all_incomming_vars;

/**
 * Application Controller Base.
 * 
 * Provides core controller functionality including POST handling, template management,
 * session control, and notification rendering. All application controllers should extend this class.
 * 
 * @property static string $root_url Application root URL
 * @property static base|blank|single_page|sidebar_page $tpl Current template object
 * @property static app $app Application instance
 * @property static array $POST Processed POST data
 * @property static bool $need_session Whether session is required
 * @property static string $login_url Login page URL for redirect
 */
class controller {

    static protected string $root_url;
    static protected base|blank|single_page|sidebar_page $tpl;
    static protected app $app;
    static array $POST = [];
    static $need_session = false;
    static $login_url;

    /**
     * Pre-process POST data before handling.
     * 
     * Runs check_all_incomming_vars() on $_POST to sanitize and normalize
     * incoming form data before the main POST handler.
     * 
     * @return void
     */
    static function pre_post() {
        self::$POST = check_all_incomming_vars($_POST);
    }

    /**
     * Handle POST request for the controller.
     * 
     * Calls pre_post() to process data, then invokes the controller lifecycle.
     * Override this method in subclasses to handle specific POST operations.
     * 
     * @return void
     */
    static function on_post() {
        static::pre_post();
    }

    /**
     * Retrieve POST data by key or entire POST array.
     * 
     * @param string|null $key Optional key to retrieve specific POST value
     * @return mixed Returns the POST value for the key, entire POST array if no key, or FALSE if empty
     */
    static function POST($key = null) {

        if (!empty($key) && array_key_exists($key, self::$POST)) {
            return self::$POST[$key];
        } else {
            if (count(self::$POST) > 0) {
                return self::$POST;
            } else {
                return false;
            }
        }
    }

    /**
     * Execute the controller lifecycle.
     * 
     * Calls start(), run(), and end() in sequence to complete the request.
     * 
     * @return void
     */
    static function launch() {
        static::start();
        static::run();
        static::end();
    }

    /**
     * Initialize the controller and session if required.
     * 
     * Checks if app_session is needed based on configuration and performs
     * login verification if so.
     * 
     * @return void
     */
    static function start() {
        $app_options = new general();
        if ($app_options->get_option('app_session_needed')) {
            app_session::is_logged(true, K1APP_URL . $app_options->get_option('app_session_login_url'));
        }
    }

    /**
     * Run the controller (override in subclasses).
     * 
     * Override this method to implement specific controller logic.
     * 
     * @return void
     */
    static function run() {
        
    }

    /**
     * Finalize the controller and render output.
     * 
     * Inserts all queued DOM notifications and generates the template output.
     * 
     * @return void
     */
    static function end() {
        DOM_notifications::insert_messases_on_DOM();
    }

    /**
     * Set the template for rendering and configure notifications.
     * 
     * @param base|blank|single_page|sidebar_page $tpl Template object
     * @param string|null $tag_id_override Optional override for notification insertion point
     * @return void
     */
    static function use_tpl(base|blank|single_page|sidebar_page $tpl, $tag_id_override = null) {
        self::$tpl = $tpl;
        self::$app->set_global_tpl($tpl);
        DOM_notifications::set_tpl($tpl, $tag_id_override);
    }

    static function tpl(): base|blank|single_page|sidebar_page {
        return self::$tpl;
    }

    static function link_app(app $app) {
        self::$app = $app;
    }

    static function app(): app {
        return self::$app;
    }

    public static function get_root_url(): string {
        return self::$root_url;
    }

    public static function set_root_url(string $root_url): void {
        self::$root_url = $root_url;
    }
}
