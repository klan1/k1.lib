<?php

namespace k1lib\db;

/**
 * 
 */
class handler extends \PDO {

    /**
     * Enable state
     * @var Boolean 
     */
    static protected $enabled = FALSE;
    static protected $db_dsn;
    static protected $db_name;
    static protected $db_user;
    static protected $db_password;
    static protected $db_host;
    static protected $db_port;

    /**
     *  Verbose level for error output
     * @var type 
     */
    static protected $verbose_level = 0;

    /**
     * Enable the engenie
     * @param string $db_name
     * @param string $db_user
     * @param string $db_password
     * @param string $db_host
     * @param integer $db_port
     * @param string $db_type
     */
    static public function enable($db_name, $db_user, $db_password, $db_host = "localhost", $db_port = 3306, $db_type = "mysql", $pdo_string_altern = FALSE) {
        self::$enabled = TRUE;
        self::$db_name = $db_name;
        self::$db_user = $db_user;
        self::$db_password = $db_password;
        self::$db_host = $db_host;
        self::$db_port = $db_port;
        if ($pdo_string_altern) {
            self::$db_dsn = "{$db_type}:dbname={$db_name};host={$db_host}:{$db_port}";
        } else {
            self::$db_dsn = "{$db_type}:dbname={$db_name};host={$db_host};port={$db_port}";
        }
    }

    /**
     * Query the enabled state
     * @return Boolean
     */
    static public function is_enabled($show_error = false) {
        if ($show_error && (!self::$enabled)) {
            trigger_error("DB system is not enabled yet", E_USER_ERROR);
        }
        return self::$enabled;
    }

    public function __construct($options = null) {
        self::is_enabled(true);
        parent::__construct(self::$db_dsn, self::$db_user, self::$db_password, $options);
    }

    static function get_verbose_level() {
        self::is_enabled(true);
        return self::$verbose_level;
    }

    function set_verbose_level($verbose_level) {
        self::is_enabled(true);
        self::$verbose_level = $verbose_level;
        if (self::$verbose_level == 0) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        } elseif (self::$verbose_level > 0) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

    function query($statement) {
        try {
            $result = parent::query($statement);
        } catch (\PDOException $exc) {
            switch (self::$verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_NOTICE);
                    break;
                case 1:
                    trigger_error($exc->getMessage(), E_USER_NOTICE);
                    break;
                case 2:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_NOTICE);
                    break;
                case 3:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_NOTICE);
                    d($exc->getTraceAsString());
                    break;
                default:
                    break;
            }
            $result = FALSE;
        }

        return $result;
    }

    function exec($statement) {
        try {
            $result = parent::exec($statement);
        } catch (\PDOException $exc) {
            switch (self::$verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_WARNING);
                    break;
                case 1:
                    trigger_error($exc->getMessage(), E_USER_WARNING);
                    break;
                case 2:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_WARNING);
                    break;
                case 3:
                    trigger_error($statement . " | " . $exc->getMessage(), E_USER_WARNING);
                    d($exc->getTraceAsString());
                    break;
                default:
                    break;
            }
            $result = FALSE;
        }

        return $result;
    }

}
