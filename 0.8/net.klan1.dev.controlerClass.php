<?php

define("K1LIB_CONTROLLER_TYPE_MAIN", 1);
define("K1LIB_CONTROLLER_TYPE_FOREIGN", 2);
define("K1LIB_CONTROLLER_TYPE_CUSTOM", 3);

define("K1LIB_SECTION_TYPE_VIEW_ALL", 1);
define("K1LIB_SECTION_TYPE_VIEW", 2);
define("K1LIB_SECTION_TYPE_NEW", 3);
define("K1LIB_SECTION_TYPE_EDIT", 4);
define("K1LIB_SECTION_TYPE_DELETE", 5);

class net_klan1_dev_controllerClass {

    // main vars
    private $controllerType = "";
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
    public $dbTableForeignName;
    // actual section or action name

    private $moduleUrlValue = false;
    private $moduleUrlName = false;
    private $boardID;
    private $boardFormId;
    private $boardErrorId;
    private $boardFormErrorId;
    private $boardRootUrl = null;
    private $boardUrlValue = false;
    private $boardUrlName = false;
    private $boardFkUrlValue = false;
    private $boardFkUrlValueArray = array();
    private $boardFkUrlName = false;
    private $boardUrlParameterValue = false;
    private $boardUrlParameterValueArray = array();
    private $boardUrlParameterName = false;
    private $boardUrlParameterWhereCondition = "";
    private $boardUrlActionValue = false;
    private $boardUrlActionName = false;
    private $boardAvailabilityArray = Array();
    // when is "foreign" table type
    public $foreignKeyWhereCondition;
    public $foreignTableLabelColumn;
    public $foreignLabelValue;
    // URL vars
    public $controllerUrlLevel;
    public $actualUrlLevel;
    public $nextUrlLevel;
    private $moduleUrlRoot;
    private $moduleDetailUrl;
    public $moduleTableListUrl;
    public $moduleNewUrl;
    public $moduleEditUrl;
    public $moduleDeleteUrl;

    function __construct(PDO &$db) {
        // Check DB Type
        if (get_class($db) == "PDO") {
            $this->db = $db;
        } else {
            die("\$db is not a PDO object type - called from: " . __CLASS__);
        };
        // see the max URL Level
        $this->controllerUrlLevel = k1_get_url_level_count() - 1;
        $this->actualUrlLevel = $this->controllerUrlLevel;
        $this->nextUrlLevel = $this->actualUrlLevel + 1;
    }

    public function getBoardAvailabilityArray() {
        return $this->boardAvailabilityArray;
    }

    public function setBoardAvailabilityArray($boardAvailabilityArray) {
        $this->boardAvailabilityArray = $boardAvailabilityArray;
    }

    public function getDbTableMainName() {
        return $this->dbTableMainName;
    }

    public function setDbTableMainName($dbTableMainName) {
        $this->dbTableMainName = $dbTableMainName;
    }

    public function getDbTableMainSQLFilter() {
        return $this->dbTableMainSQLFilter;
    }

    public function setDbTableMainSQLFilter($dbTableMainSQLFilter) {
        $this->dbTableMainSQLFilter = $dbTableMainSQLFilter;
    }

    public function getForeignKeyWhereCondition() {
        return $this->foreignKeyWhereCondition;
    }

    public function setDbTable($tableName, $tableType, $initController = true) {

        switch ($tableType) {
            case K1LIB_CONTROLLER_TYPE_MAIN: // case "main":
                $this->controllerType = K1LIB_CONTROLLER_TYPE_MAIN;
                $this->dbTableMainObject = new net_klan1_dev_dbTableClass($this->db, $tableName);
                $this->dbTableMainName = $tableName;

                break;
            case K1LIB_CONTROLLER_TYPE_FOREIGN: // case "foreign":
                if (get_class($this->dbTableMainObject) === "net_klan1_dev_dbTableClass") {
                    $this->controllerType = K1LIB_CONTROLLER_TYPE_FOREIGN;
                    $this->dbTableForeignObject = new net_klan1_dev_dbTableClass($this->db, $tableName);
                    $this->dbTableForeignName = $tableName;
                    //foreingKey URL must be setted before set the tables
                    if ($this->boardFkUrlValue !== false) {
                        //    $foreign_table = "empresas";
                        //    $foreign_table_config = k1_get_table_config($db, $foreign_table);
                        $this->boardFkUrlValueArray = k1_table_url_text_to_keys(
                                $this->boardFkUrlValue
                                , $this->dbTableForeignObject->getTableConfig()
                        );
                        $this->foreignKeyWhereCondition = k1_table_keys_to_where_condition(
                                $this->boardFkUrlValueArray
                                , $this->dbTableForeignObject->getTableConfig()
                        );
                    } else {
                        die("No se obtuvo el ID de la tabla auxiliar");
                    }
                } else {
                    die("You can't set a table as FOREIGN whitout set the MAIN table first");
                }
                break;
            default:
                die("\$tableType: $tableType is not correct");
                break;
        }
    }

    public function getModuleUrlRoot() {
        return $this->moduleUrlRoot;
    }

    public function getBoardRootUrl() {
        if ($this->controllerType == K1LIB_CONTROLLER_TYPE_MAIN) {
            if ($this->getBoardUrlActionValue()) {
                $this->boardRootUrl = k1_make_url_from_rewrite(-1);
            } else {
                $this->boardRootUrl = k1_make_url_from_rewrite();
            }
        } elseif ($this->controllerType == K1LIB_CONTROLLER_TYPE_FOREIGN) {
            if ($this->getBoardUrlActionValue()) {
                $this->boardRootUrl = k1_make_url_from_rewrite(-1);
            } else {
                $this->boardRootUrl = k1_make_url_from_rewrite();
            }
        } else {
            die("K1LIB_CONTROLLER_TYPE no recognized on " . __METHOD__);
        }

        return $this->boardRootUrl;
    }

    public function setDefaultBoardUrlValue($defaultBoardUrlValue) {
        if (is_string($defaultBoardUrlValue)) {
            if (empty($this->boardUrlValue)) {
                if ($this->controllerType == K1LIB_CONTROLLER_TYPE_MAIN) {
                    k1_html_header_go("{$this->moduleUrlRoot}/{$defaultBoardUrlValue}/");
                } elseif ($this->controllerType == K1LIB_CONTROLLER_TYPE_FOREIGN) {
                    k1_html_header_go("{$this->moduleUrlRoot}/{$this->boardFkUrlValue}/{$defaultBoardUrlValue}/");
                } else {
                    die("K1LIB_CONTROLLER_TYPE no recognized on " . __METHOD__);
                }
            }
        } else {
            die("\$defaultBoardUrlValue must to be a string on " . __METHOD__);
        }
    }

    public function initBoardUrl() {
        $this->boardUrlName = "url_board";
        $this->boardUrlValue = $this->setUrlLevel($this->boardUrlName, false);
        $this->boardID = k1_get_this_controller_id();
        // DATA FROM SQL will use this ID
        $this->boardFormId = $this->boardID . "-data";
        // CONTROLLER SPECIFIC ERRORS will use this ID
        $this->boardErrorId = $this->boardID . "-errors";
// FORM (POST) SPECIFIC ERRORS will use this ID
        $this->boardFormErrorId = $this->boardID . "-form";
    }

    public function getBoardFormId() {
        return $this->boardFormId;
    }

    public function getBoardErrorId() {
        return $this->boardErrorId;
    }

    public function getBoardFormErrorId() {
        return $this->boardFormErrorId;
    }

    public function getModuleUrlValue() {
        return $this->moduleUrlValue;
    }

    public function getModuleUrlName() {
        return $this->moduleUrlName;
    }

    public function initModuleUrl() {
        $this->moduleUrlName = "url_module";
        $this->moduleUrlValue = $this->setUrlLevel($this->moduleUrlName, false);
        $this->moduleUrlRoot = k1_make_url_from_rewrite();
    }

    public function getBoardFkUrlName() {
        return $this->boardFkUrlName;
    }

    public function getBoardFkUrlValue() {
        if (!empty($this->boardFkUrlValue))
            return $this->boardFkUrlValue;
        else
            return null;
    }

    public function getBoardFkUrlValueArray() {
        if (!empty($this->boardFkUrlValueArray)) {
            return $this->boardFkUrlValueArray;
        } else {
            return null;
        }
    }

    public function initBoardFkUrl() {
        $this->boardFkUrlName = "foreing_key";
        $this->boardFkUrlValue = $this->setUrlLevel($this->boardFkUrlName, false);
//        $this->boardFkUrlValueArray = k1_table_url_text_to_keys($this->boardFkUrlValue, $this->dbTableForeignObject->getTableConfig());
    }

    public function getBoardUrlParameterName() {
        return $this->boardUrlParameterName;
    }

    public function getBoardUrlParameterValueArray() {
        return $this->boardUrlParameterValueArray;
    }

    public function getBoardUrlParameterValue() {
        return $this->boardUrlParameterValue;
    }

    public function initBoardUrlParameter() {
        $this->boardUrlParameterName = "url_BoardParameter";
        $this->boardUrlParameterValue = $this->setUrlLevel($this->boardUrlParameterName, false);
        $this->boardUrlParameterValueArray = k1_table_url_text_to_keys($this->boardUrlParameterValue, $this->dbTableMainObject->getTableConfig());
        $this->boardUrlParameterWhereCondition = k1_table_keys_to_where_condition($this->boardUrlParameterValueArray, $this->dbTableMainObject->getTableConfig());
    }

    public function getBoardUrlParameterWhereCondition() {
        return $this->boardUrlParameterWhereCondition;
    }

    public function getBoardUrlActionValue() {
        return $this->boardUrlActionValue;
    }

    public function getBoardUrlActionName() {
        return $this->boardUrlActionName;
    }

    public function initBoardUrlAction() {
        $this->boardUrlActionName = "url_BoardAction";
        $this->boardUrlActionValue = $this->setUrlLevel($this->boardUrlActionName, false);
    }

    public function setAuthLevelAccess($levels) {
        return true;
    }

    public function setUrlLevel($urlLevelName, $required) {
        if (!is_string($urlLevelName)) {
            die("The \$urlLevelName type has to be a String " . __METHOD__);
        }

        $urlLevelValue = k1_define_url_rewrite_var($this->nextUrlLevel, $urlLevelName, $required);
        $this->actualUrlLevel = $this->nextUrlLevel;
        $this->nextUrlLevel++;
        return $urlLevelValue;
    }

    public function getControllerType() {
        return $this->controllerType;
    }

    public function getControllerTableConfig() {
        return $this->dbTableMainObject->getTableConfig();
    }

    public function getControllerFkTableConfig() {
        return $this->dbTableForeignObject->getTableConfig();
    }

    public function getDefaultBoardUrlValue() {
        return $this->defaultBoardUrlValue;
    }

    public function getBoardUrlName() {
        return $this->boardUrlName;
    }

    public function getBoardUrlValue() {
        return $this->boardUrlValue;
    }

    public function getModuleDetailUrl() {
        return $this->moduleDetailUrl;
    }

    public function getModuleTableListUrl() {
        return $this->moduleTableListUrl;
    }

    public function getModuleNewUrl() {
        return $this->moduleNewUrl;
    }

    public function getModuleEditUrl() {
        return $this->moduleEditUrl;
    }

    public function getModuleDeleteUrl() {
        return $this->moduleDeleteUrl;
    }

    public function getModuleDetailLink() {
        return k1_get_app_link($this->moduleDetailUrl);
    }

    public function getModuleDetailButton($buttonText = "Ver") {
        return k1_get_link_button($this->getModuleDetailLink(), $buttonText);
    }

    public function getModuleNewLink() {
        return k1_get_app_link($this->moduleNewUrl);
    }

    public function getModuleNewButton($buttonText = "Nuevo ítem") {
        return k1_get_link_button($this->getModuleNewLink(), $buttonText);
    }

    public function setModuleDetailUrl($moduleDetailUrl) {
        if (isset($this->boardAvailabilityArray[$moduleDetailUrl]) && ($this->boardAvailabilityArray[$moduleDetailUrl] == true)) {
            $this->moduleDetailUrl = k1_make_url_from_rewrite(-1) . "/{$moduleDetailUrl}/%s";
        } else {
            $this->moduleDetailUrl = null;
        }
    }

    public function setModuleTableListUrl($moduleTableListUrl) {
        if (isset($this->boardAvailabilityArray[$moduleTableListUrl]) && ($this->boardAvailabilityArray[$moduleTableListUrl] == true)) {
            $this->moduleTableListUrl = k1_make_url_from_rewrite(-1) . "/{$moduleTableListUrl}/";
        } else {
            $this->moduleTableListUrl = null;
        }
    }

    public function setModuleNewUrl($moduleNewUrl) {
        if (isset($this->boardAvailabilityArray[$moduleNewUrl]) && ($this->boardAvailabilityArray[$moduleNewUrl] == true)) {
            $this->moduleNewUrl = k1_make_url_from_rewrite(-1) . "/{$moduleNewUrl}/";
        } else {
            $this->moduleNewUrl = null;
        }
    }

    public function setModuleEditUrl($moduleEditUrl) {
        if (isset($this->boardAvailabilityArray[$moduleEditUrl]) && ($this->boardAvailabilityArray[$moduleEditUrl] == true)) {
            $this->moduleEditUrl = k1_make_url_from_rewrite(-1) . "/{$moduleEditUrl}/%s";
        } else {
            $this->moduleEditUrl = null;
        }
    }

    public function setModuleDeleteUrl($moduleDeleteUrl) {

        if (isset($this->boardAvailabilityArray[$moduleDeleteUrl]) && ($this->boardAvailabilityArray[$moduleDeleteUrl] == true)) {
            $this->moduleDeleteUrl = k1_make_url_from_rewrite(-1) . "/{$moduleDeleteUrl}/%s";
        } else {
            $this->moduleDeleteUrl = null;
        }
    }

    public function getBoardID() {
        return $this->boardID;
    }

    public function setBoardID($boardID) {
        $this->boardID = $boardID;
    }

    function parseUrlTag($urlString) {
        $urlString = str_replace("[controller-key]", $this->getBoardUrlParameterValue(), $urlString);
        $urlString = str_replace("[controller-fk]", $this->getBoardFkUrlValue(), $urlString);
        $urlString = str_replace("[board]", $this->getModuleUrlRoot(), $urlString);
        $urlString = str_replace("[board-edit]", $this->getModuleEditUrl(), $urlString);
        $urlString = str_replace("[board-view]", $this->getModuleDetailUrl(), $urlString);
        $urlString = str_replace("[board-delete]", $this->getModuleDeleteUrl(), $urlString);
        return $urlString;
    }

}

function K1_parseUrlTag($urlString, net_klan1_dev_controllerClass $controllerObject) {
    $urlString = str_replace("[controller-key]", $controllerObject->getBoardUrlParameterValue(), $urlString);
    $urlString = str_replace("[controller-fk]", $controllerObject->getBoardFkUrlValue(), $urlString);
    $urlString = str_replace("[board]", $controllerObject->getModuleUrlRoot(), $urlString);
    $urlString = str_replace("[board-edit]", $controllerObject->getModuleEditUrl(), $urlString);
    $urlString = str_replace("[board-view]", $controllerObject->getModuleDetailUrl(), $urlString);
    $urlString = str_replace("[board-delete]", $controllerObject->getModuleDeleteUrl(), $urlString);
    return $urlString;
}
