<?php

namespace k1lib\crud\classes;

class k1_controller_with_dbtables_class extends k1_general_controller_class {

// TODO: make this private... some day :P
    public $db;
// table objects references 
    /**
     * @var net_klan1_dev_dbTableClass
     */
    public $dbTableMainObject;
    private $dbTableMainName;
    private $dbTableMainSQLFilter = null;

    /**
     * @var net_klan1_dev_dbTableClass
     */
    public $dbTableForeignObject;
    private $dbTableForeignName;

// when is "foreign" table type

    /*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */
    function __construct(\PDO &$db) {
        parent::__construct();
        $this->db = $db;
        // TODO: check is useless !! I have to check if the DB is already connectedÏ
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setDbTableMainSQLFilter($dbTableMainSQLFilter) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->dbTableMainSQLFilter = $dbTableMainSQLFilter;
    }

    public function setDbTableMainName($dbTableMainName) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->dbTableMainName = $dbTableMainName;
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    public function setDbTable($tableName, $tableType, $initController = true) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);

        switch ($tableType) {
            case \k1lib\crud\CONTROLLER_TYPE_MAIN: // case "main":
                $this->setControllerType(\k1lib\crud\CONTROLLER_TYPE_MAIN);
                $this->dbTableMainObject = new \k1lib\crud\classes\net_klan1_dev_dbTableClass($this->db, $tableName);
                $this->dbTableMainName = $tableName;

                break;
            case \k1lib\crud\CONTROLLER_TYPE_FOREIGN: // case "foreign":
                if ($this->getControllerType() === \k1lib\crud\CONTROLLER_TYPE_MAIN) {
                    $this->setControllerType(\k1lib\crud\CONTROLLER_TYPE_FOREIGN);
                    $this->dbTableForeignObject = new net_klan1_dev_dbTableClass($this->db, $tableName);
                    $this->dbTableForeignName = $tableName;
//foreingKey URL must be setted before set the tables
                } else {
                    die("You can't set a table as FOREIGN whitout set the MAIN table first");
                }
                break;
            default:
                die("\$tableType: $tableType is not correct");
                break;
        }
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    public function getDbTableMainName() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->dbTableMainName;
    }

    public function getDbTableMainSQLFilter() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->dbTableMainSQLFilter;
    }

    public function getControllerTableConfig() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->dbTableMainObject->getTableFieldConfig();
    }

    public function getControllerFkTableConfig() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->dbTableForeignObject->getTableFieldConfig();
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_END 
     * 
     * ***************** */

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_NONE
     * 
     * ***************** */
}

/*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONFIG
     * 
     * ***************** */
/*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */


    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_END 
     * 
     * ***************** */

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_NONE
     * 
     * ***************** */