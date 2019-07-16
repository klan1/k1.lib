<?php
/**
 * New DB class to make easier multiple DB connections
 */

namespace k1lib\db;

/**
 * 
 */
class PDO_k1 extends \PDO {

    /**
     * Enable state
     * @var Boolean 
     */
    protected $enabled = FALSE;
    protected $db_dsn;
    protected $db_name;
    protected $db_user;
    protected $db_password;
    protected $db_host;
    protected $db_port;

    /**
     *  Verbose level for error output
     * @var type 
     */
    protected $verbose_level = 0;

    /**
     * Query the enabled state
     * @return Boolean
     */
    public function is_enabled($show_error = false) {
        if ($show_error && (!$this->enabled)) {
            trigger_error("DB system is not enabled yet", E_USER_ERROR);
        }
        return $this->enabled;
    }

    /**
     * Start the engenie
     * @param string $db_name
     * @param string $db_user
     * @param string $db_password
     * @param string $db_host
     * @param integer $db_port
     * @param string $db_type
     */
    public function __construct($db_name, $db_user, $db_password, $db_host = "localhost", $db_port = 3306, $db_type = "mysql", $pdo_string_altern = FALSE) {
        $this->enabled = TRUE;
        $this->db_name = $db_name;
        $this->db_user = $db_user;
        $this->db_password = $db_password;
        $this->db_host = $db_host;
        $this->db_port = $db_port;
        if ($pdo_string_altern) {
            $this->db_dsn = "{$db_type}:dbname={$db_name};host={$db_host}:{$db_port}";
        } else {
            $this->db_dsn = "{$db_type}:dbname={$db_name};host={$db_host};port={$db_port}";
        }

        $this->is_enabled(true);
        parent::__construct($this->db_dsn, $this->db_user, $this->db_password);
    }

    function get_verbose_level() {
        $this->is_enabled(true);
        return $this->verbose_level;
    }

    function set_verbose_level($verbose_level) {
        $this->is_enabled(true);
        $this->verbose_level = $verbose_level;
        if ($this->verbose_level == 0) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        } elseif ($this->verbose_level > 0) {
            $this->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
    }

    function query($statement) {
        try {
            $result = parent::query($statement);
        } catch (\PDOException $exc) {
            switch ($this->verbose_level) {
                case 0:
                    trigger_error("SQL query error", E_USER_NOTICE);
                    break;
                case 1:
                    d($statement);
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
            switch ($this->verbose_level) {
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
