<?php

namespace k1lib;

use k1lib\app\config;
use k1lib\db\PDO_k1;
use k1lib\forms\file_uploads;
use k1lib\html\notifications\on_DOM;
use k1lib\session\session_db;
use k1lib\session\session_plain;
use k1lib\urlrewrite\url;
use PDOException;
use const k1app\K1APP_ASSETS_PATH;
use const k1app\K1APP_ASSETS_URL;
use const k1app\K1APP_BASE_URL;
use const k1app\K1APP_CONTROLLERS_PATH;
use const k1app\K1APP_DOMAIN_URL;
use const k1app\K1APP_HOME_URL;
use const k1app\K1APP_ROOT;
use const k1app\K1APP_UPLOADS_PATH;
use const k1app\K1APP_UPLOADS_URL;
use const k1app\K1APP_URL;

class app {

    protected config $config;
    public bool $is_web = false;
    public bool $is_shell = false;
    public bool $is_api = false;
    protected string $script_path;
    static string $base_path;
    static string $base_url;
    protected $app_session;

    /**
     * DB
     */

    /**
     * @var PDO_k1[]
     */
    protected array $db_connections = [];

    /**
     * @param config $app_config
     * @param bool $api_mode
     */
    function __construct(config $app_config, string $script_path, $api_mode = false) {
        $this->config = $app_config;
        $this->is_api = $api_mode;
        $this->script_path = $script_path;
        $this->bootstrap();

        $this->db_connections[1] = null;
    }

    /**
     * @return void
     */
    function bootstrap(): void {
        /**
         * Let's define if is web or shell
         */
        if (array_key_exists('SHELL', $_SERVER)) {
            if ($this->is_api) {
                trigger_error('You can\'t start an API app with shell', E_USER_ERROR);
            }
            $this->is_shell = true;
            define('k1app\K1APP_MODE', 'shel');
        }
        if (array_key_exists('HTTP_HOST', $_SERVER)) {
            if (!$this->is_api) {
                $this->is_web = true;
                define('k1app\K1APP_MODE', 'web');
            } else {
                define('k1app\K1APP_MODE', 'api');
            }
        }

        $this->auto_config();
    }

    /**
     * @return void
     */
    function auto_config(): void {
        /**
         * Genral config about paths
         */
        // AUTO CONFIGURATED PATHS
        define('k1app\K1APP_ROOT', str_replace('\\', '/', dirname($this->script_path)));
        define('k1app\K1APP_DIR', basename(K1APP_ROOT) . '/');
        define('k1app\K1APP_DOMAIN', $_SERVER['HTTP_HOST']);

        define('k1app\K1APP_CONTROLLERS_PATH', K1APP_ROOT . '/src/classes/k1app/controllers/'); // 2.0
        define('k1app\K1APP_CLASSES_PATH', K1APP_ROOT . '/src/classes/'); // 2.0
        define('k1app\K1APP_ASSETS_PATH', K1APP_ROOT . '/assets/'); // 2.0
        define('k1app\K1APP_ASSETS_IMAGES_PATH', K1APP_ASSETS_PATH . 'static/img/'); // 2.0
        define('k1app\K1APP_ASSETS_CSS_PATH', K1APP_ASSETS_PATH . 'static/css/'); // 2.0
        define('k1app\K1APP_ASSETS_JS_PATH', K1APP_ASSETS_PATH . 'static/js/'); // 2.0
        // define('k1app\K1APP_VIEWS_PATH', \k1app\K1APP_ROOT . '/views/');
        // define('k1app\K1APP_VIEWS_CRUD_PATH', \k1app\K1APP_VIEWS_PATH . '/k1lib.crud/');
        define('k1app\K1APP_SETTINGS_PATH', K1APP_ROOT . '/settings/');
        define('k1app\K1APP_UPLOADS_PATH', K1APP_ASSETS_PATH . 'uploads/');
        define('k1app\K1APP_SHELL_SCRIPTS_PATH', K1APP_ASSETS_PATH . '/shell-scripts/');
        // define('k1app\K1APP_TEMPLATES_PATH', \k1app\K1APP_RESOURCES_PATH . '/templates/');
        define('k1app\K1APP_FONTS_PATH', K1APP_ASSETS_PATH . 'fonts/');

        /**
         * COMPOSER
         */
        define('k1app\COMPOSER_PACKAGES_PATH', K1APP_ROOT . 'vendor/');

        // AUTO CONFIGURATED URLS
        if ($this->is_web) {
            /**
             * If this error is trigger you should set by hand the CONST: k1app\K1APP_BASE_URL
             * with your personal configuration.
             */
            $app_base_url = dirname(substr($_SERVER['SCRIPT_FILENAME'], strlen($_SERVER['DOCUMENT_ROOT']))) . '/';
            if ($app_base_url == '//' || $app_base_url == '\/') {
                $app_base_url = '/';
            }
            define('k1app\K1APP_BASE_URL', $app_base_url);

            //    define('k1app\K1APP_DOMAIN_URL', (\k1lib\common\get_http_protocol() . '://') . \k1app\K1APP_DOMAIN);
            define('k1app\K1APP_DOMAIN_URL', '//' . $_SERVER['HTTP_HOST']);

            define('k1app\K1APP_URL', K1APP_DOMAIN_URL . K1APP_BASE_URL);
            define('k1app\K1APP_HOME_URL', K1APP_URL);
            define('k1app\K1APP_ASSETS_URL', K1APP_HOME_URL . 'assets/');
//            define('k1app\K1APP_IMAGES_URL', K1APP_ASSETS_URL . 'images/');
            define('k1app\K1APP_ASSETS_IMAGES_URL', K1APP_ASSETS_URL . 'static/img/'); // 2.0
            define('k1app\K1APP_ASSETS_CSS_URL', K1APP_ASSETS_URL . 'static/css/'); // 2.0
            define('k1app\K1APP_ASSETS_JS_URL', K1APP_ASSETS_URL . 'static/js/'); // 2.0

            define('k1app\K1APP_UPLOADS_URL', K1APP_ASSETS_URL . 'uploads/');
            define('k1app\K1APP_TEMPLATES_URL', K1APP_ASSETS_URL . 'templates/');
            //    define('k1app\K1APP_TEMPLATE_IMAGES_URL', \k1app\K1APP_TEMPLATE_URL . 'img/');

            /**
             * COMPOSER
             */
            define('k1app\COMPOSER_PACKAGES_URL', K1APP_URL . 'vendor/');
        }
    }

    /**
     * @return void
     */
    function run_controllers(): void {
        file_uploads::enable(K1APP_UPLOADS_PATH, K1APP_UPLOADS_URL);
        file_uploads::set_overwrite_existent(false);

        url::enable();
        $controller_full = url::get_controller_path_from_url(K1APP_CONTROLLERS_PATH);
        $controller = str_replace('/', '\\', substr($controller_full, strlen(K1APP_CONTROLLERS_PATH), -4));
        $controlle_root_url = K1APP_URL . str_replace('\\', '/', $controller . '/');
        
        $class = 'k1app\controllers\\' . $controller;
        $class::set_root_url($controlle_root_url);
        $class::link_app($this);

        // TODO: complete this cases
        if ($this->is_web) {
            switch ($_SERVER['REQUEST_METHOD']) {
                case 'GET':
                    $class::launch();
                    break;
                case 'POST':
                    $class::on_post();
                    break;
                case 'PUT':
                    $class::on_post();
                    break;
                case 'DELETE':
                    $class::on_post();
                    break;
                default :
            }
        } else if ($this->is_api) {
            $class::launch();
        } else {
            die('not yet');
        }
    }

    function start_session() {
        session_plain::enable();
        session_plain::set_session_name($this->config->get_option('app_session_name'));
        session_plain::set_use_ip_in_userhash($this->config->get_option('app_session_use_ip_in_userhash'));
        session_plain::set_app_user_levels($this->config->get_option('app_session_levels'));
        // TODO: manage non DB session
        //session_plain::start_session();;
    }

    function start_session_db(int $db_index) {
        session_db::set_session_name($this->config->get_option('app_session_name'));
        $this->app_session = new session_db($this->db($db_index));
        $this->app_session->start_session();
        $this->app_session->load_logged_session_db();
    }

    function end_session() {
        $this->app_session->unset_coockie(K1APP_BASE_URL);
        session_db::end_session();

        $this->app_session = new session_plain();
        $this->start_session();

        on_DOM::queue_mesasage("Bye!", "success");
    }

    function db($index = 1) {
        if ($index === 1) {
            if (empty($this->db_connections[1])) {

                try {
                    /**
                     * @var PDO_k1 
                     */
                    $this->db_connections[1] = new PDO_k1(
                            $this->config->get_option('db_name'),
                            $this->config->get_option('db_user'),
                            $this->config->get_option('db_password'),
                            $this->config->get_option('db_host'),
                            $this->config->get_option('db_port'),
                            $this->config->get_option('db_type')
                    );
                    $this->db_connections[1]->set_verbose_level($this->config->get_option('app_verbose_level'));
                } catch (PDOException $e) {
                    trigger_error($e->getMessage(), E_USER_ERROR);
                }
                $this->db_connections[1]->exec('set names utf8');
            }
        }
        return $this->db_connections[$index];
    }
}
