<?php

class completeEasyController extends k1_controller_with_dbtables_class {

    private $boardID;
    private $boardFormId;
    private $boardErrorId;
    private $boardFormErrorId;
    private $boardRootUrl = null;
    private $boardUrlValue = false;
    private $boardUrlName = false;
    private $boardUrlParameterValue = null;
    private $boardUrlParameterValueArray = array();
    private $boardUrlParameterName = null;
    private $boardUrlParameterWhereCondition = "";
    private $boardUrlActionValue = false;
    private $boardUrlActionName = false;
    private $boardFkUrlValue = false;
    private $boardFkUrlName = false;
    private $boardLevelAccessArray = Array();
    private $boardAvailabilityArray = Array();
    private $boardViewToLoadArray = Array();
    private $BoardDetailUrl;
    private $BoardTableListUrl;
    private $BoardNewUrl;
    private $BoardEditUrl;
    private $BoardDeleteUrl;
    private $foreignTableLabelField = "";
    private $foreignLabelValue = "";
    private $foreignKeyWhereCondition;

    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(PDO $db) {
        parent::__construct($db);
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setBoardAvailability($boardName, $availabilityOption = true) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);

        $this->boardAvailabilityArray[$boardName] = $availabilityOption;
        if (!isset($this->boardViewToLoadArray[$boardName])) {
            $this->boardViewToLoadArray[$boardName] = $boardName;
        }
        if (!isset($this->boardLevelAccessArray[$boardName])) {
            $this->boardLevelAccessArray[$boardName] = $this->getDefaultAuthLevelAccess();
        }
    }

    public function setBoardLevelAccess($boardName, $accessLevelCSV = true) {
        $this->boardLevelAccessArray [$boardName] = $accessLevelCSV;
    }

    public function setBoardID($boardID) {
        $this->boardID = $boardID;
    }

    public function getControllerUrlRoot() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        if ($this->getControllerType() === K1LIB_CONTROLLER_TYPE_FOREIGN) {
            return (parent::getControllerUrlRoot() . "/{$this->boardFkUrlValue}");
        } else {
            return (parent::getControllerUrlRoot());
        }
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

//    public function setBoardRootUrl($boardRootUrl) {
//        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);
//        $this->boardRootUrl = $boardRootUrl;
//    }

    public function setDefaultBoardUrlValue($defaultBoardUrlValue) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);

        if (is_string($defaultBoardUrlValue)) {
            if (empty($this->boardUrlValue)) {
                if ($this->getControllerType() == K1LIB_CONTROLLER_TYPE_MAIN) {
                    k1_html_header_go("{$this->getControllerUrlRoot()}/{$defaultBoardUrlValue}/");
                } elseif ($this->getControllerType() == K1LIB_CONTROLLER_TYPE_FOREIGN) {
                    k1_html_header_go("{$this->getControllerUrlRoot()}/{$this->getBoardFkUrlValue()}/{$defaultBoardUrlValue}/");
                } else {
                    die("K1LIB_CONTROLLER_TYPE no recognized on " . __METHOD__);
                }
            }
        } else {
            die("\$defaultBoardUrlValue must to be a string on " . __METHOD__);
        }
    }

    public function initBoardUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);
        /**
         * As the URL init has to be initialized left-right we need to init first the FK if is necessary
         */
//        if ($this->getControllerType() === K1LIB_CONTROLLER_TYPE_FOREIGN) {
//            $this->initBoardFkUrl();
//        }
        $this->boardUrlName = "url_board";
        $this->boardUrlValue = $this->setUrlLevel($this->boardUrlName, false);

        $this->boardRootUrl = ($this->getControllerUrlRoot() . "/" . $this->getBoardUrlValue());
        $this->boardID = k1_get_this_controller_id();
// DATA FROM SQL will use this ID
        $this->boardFormId = $this->boardID . "-data";
// CONTROLLER SPECIFIC ERRORS will use this ID
        $this->boardErrorId = $this->boardID . "-errors";
// FORM (POST) SPECIFIC ERRORS will use this ID
        $this->boardFormErrorId = $this->boardID . "-form";
        // Defauts
        // Avialibility of controllers and boards
        if ($this->getBoardAvailability("view-all") !== FALSE) {
            $this->setBoardAvailability("view-all", true);
        }
        if ($this->getBoardAvailability("view") !== FALSE) {
            $this->setBoardAvailability("view", true);
        }
        if ($this->getBoardAvailability("delete") !== FALSE) {
            $this->setBoardAvailability("delete", true);
        }
        if ($this->getBoardAvailability("new") !== FALSE) {
            $this->setBoardAvailability("new", true);
        }
        if ($this->getBoardAvailability("edit") !== FALSE) {
            $this->setBoardAvailability("edit", true);
        }
        // sets de URL on each one with Avaliability arrays
//        $this->setBoardTableListUrl("view-all");
//        $this->setBoardDetailUrl("view");
//        $this->setBoardNewUrl("new");
//        $this->setBoardEditUrl("edit");
//        $this->setBoardDeleteUrl("delete");
    }

    public function initBoardFkUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);

        $this->boardFkUrlName = "foreing_key";
        $this->boardFkUrlValue = $this->setUrlLevel($this->boardFkUrlName, false);


        if ($this->boardFkUrlValue !== false) {
//    $foreign_table = "empresas";
//    $foreign_table_config = k1_get_table_config($db, $foreign_table);
            $this->boardFkUrlValueArray = k1_table_url_text_to_keys(
                    $this->boardFkUrlValue
                    , $this->dbTableForeignObject->getTableFieldConfig()
            );
            $this->foreignKeyWhereCondition = k1_table_keys_to_where_condition(
                    $this->boardFkUrlValueArray
                    , $this->dbTableForeignObject->getTableFieldConfig()
            );
//            oh yeah optmization !!
//  //          $this->setControllerUrlRoot($this->getControllerUrlRoot() . "/{$this->boardFkUrlValue}");
        } else {
            die("No se obtuvo el ID de la tabla auxiliar");
        }
//        $this->boardFkUrlValueArray = k1_table_url_text_to_keys($this->boardFkUrlValue, $this->dbTableForeignObject->getTableFieldConfig());
    }

    public function getBoardAvailability($boardName) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);

        if (isset($this->boardAvailabilityArray[$boardName])) {
            return $this->boardAvailabilityArray[$boardName];
        } else {
            return null;
        }
    }

    public function getBoardLevelAccess($boardName) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);

        if (isset($this->boardLevelAccessArray [$boardName])) {
            return $this->boardLevelAccessArray [$boardName];
        } else {
            return null;
        }
    }

    public function getDefaultBoardUrlValue() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->defaultBoardUrlValue;
    }

    public function getBoardUrlName() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlName;
    }

    public function getBoardUrlValue() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlValue;
    }

    public function getBoardFkUrlName() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardFkUrlName;
    }

    public function getBoardFkUrlValue() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        if (!empty($this->boardFkUrlValue)) {
            return $this->boardFkUrlValue;
        } else {
            return null;
        }
    }

    public function getBoardFkUrlValueArray() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        if (!empty($this->boardFkUrlValueArray)) {
            return $this->boardFkUrlValueArray;
        } else {
            return null;
        }
    }

    public function getForeignKeyWhereCondition() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->foreignKeyWhereCondition;
    }

    public function getBoardFormId() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardFormId;
    }

    public function getBoardErrorId() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardErrorId;
    }

    public function getBoardFormErrorId() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardFormErrorId;
    }

    public function getBoardUrlParameterName() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlParameterName;
    }

    public function getBoardUrlParameterValueArray() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlParameterValueArray;
    }

    public function getBoardUrlParameterValue() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlParameterValue;
    }

    public function getFkTabletLabelField() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        if ($this->foreignTableLabelField === "") {
            $tableConfig = $this->dbTableForeignObject->getTableFieldConfig();
            $this->foreignTableLabelField = k1_get_table_label($tableConfig);
        }
        return $this->foreignTableLabelField;
    }

    public function getFkLabelValue() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        if ($this->foreignLabelValue === "") {
            $tableConfig = $this->dbTableMainObject->getTableFieldConfig();
            foreach ($tableConfig as $field_name => $field_config) {
                if (!empty($field_config['refereced_table_name'])) {
                    $foreign_table = $field_config['refereced_table_name'];
                    $foreign_table_key = $field_config['refereced_column_name'];
                    $fk_label = k1_get_fk_field_label($foreign_table_key, $foreign_table, $this->getBoardFkUrlValueArray());
                    if (!empty($fk_label)) {
                        $this->foreignLabelValue = $fk_label;
                    } else {
                        $this->foreignLabelValue = null;
                    }
                }
            }
            if ($this->foreignLabelValue === "") {
                $this->foreignLabelValue = false;
            }
        }
        return $this->foreignLabelValue;
    }

    public function getBoardUrlParameterWhereCondition() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlParameterWhereCondition;
    }

    public function getBoardUrlActionValue() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlActionValue;
    }

    public function getBoardUrlActionName() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlActionName;
    }

    public function getBoardAvailabilityArray() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardAvailabilityArray;
    }

    public function getViewToInclude() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        $boardViewToLoadValue = $this->boardViewToLoadArray[$this->getBoardUrlValue()];
        if ($boardViewToLoadValue != $this->getBoardUrlValue()) {
            return k1_load_view($boardViewToLoadValue, APP_VIEWS_PATH);
        } else {
            return k1_load_view($boardViewToLoadValue, APP_VIEWS_GENERAL_PATH);
        }
    }

    public function setBoardViewToLoad($boardName, $BoardViewToLoadOption) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        $this->boardViewToLoadArray[$boardName] = $BoardViewToLoadOption;
    }

    public function getBoardViewToLoad($boardName) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardViewToLoadArray[$boardName];
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    public function getBoardID() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->boardID;
    }

    public function getBoardRootUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        /*
         * WTF fix ?? --> FOUND !!
         */
//        return $this->getControllerUrlRoot() . "/" . $this->boardUrlParameterValue;
//        d($this->getControllerUrlRoot());
//        d($this->boardRootUrl);
        $boardRootURL = null;

        if (!empty($this->getBoardUrlParameterValue())) {
            return $this->boardRootUrl . "/" . $this->getBoardUrlParameterValue();
        } else {
            return $this->boardRootUrl;
        }
    }

    public function initBoardUrlAction() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
        $this->boardUrlActionName = "url_BoardAction";
        $this->boardUrlActionValue = $this->setUrlLevel($this->boardUrlActionName, false);
    }

    public function initBoardUrlParameter() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);

        $this->boardUrlParameterName = "url_BoardParameter";
        $this->boardUrlParameterValue = $this->setUrlLevel($this->boardUrlParameterName, false);
        /**
         * WTF fix !!
          $this->setBoardRootUrl($this->getBoardRootUrl() . "/" . $this->boardUrlParameterValue);
         */
        $this->boardID = k1_get_this_controller_id();
        // DATA FROM SQL will use this ID
        $this->boardFormId = $this->boardID . "-data";
// CONTROLLER SPECIFIC ERRORS will use this ID
        $this->boardErrorId = $this->boardID . "-errors";
// FORM (POST) SPECIFIC ERRORS will use this ID
        $this->boardFormErrorId = $this->boardID . "-form";
//        d($this->boardID);
        $this->boardUrlParameterValueArray = k1_table_url_text_to_keys($this->boardUrlParameterValue, $this->dbTableMainObject->getTableFieldConfig());
        $this->boardUrlParameterWhereCondition = k1_table_keys_to_where_condition($this->boardUrlParameterValueArray, $this->dbTableMainObject->getTableFieldConfig());
//        d($this->dbTableMainObject->getTableFieldConfig());
    }

    /**
     * BOARD: VIEW ALL 
     */
    public function setBoardTableListUrl($BoardTableListUrl) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        if (isset($this->boardAvailabilityArray[$BoardTableListUrl]) && ($this->boardAvailabilityArray[$BoardTableListUrl] == true)) {
            $this->BoardTableListUrl = $this->getControllerUrlRoot() . "/{$BoardTableListUrl}/";
        } else {
            $this->BoardTableListUrl = null;
        }
    }

    public function getBoardTableListUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->BoardTableListUrl;
    }

    /**
     * BOARD: VIEW
     */
    public function setBoardDetailUrl($BoardDetailUrl) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        if (isset($this->boardAvailabilityArray[$BoardDetailUrl]) && ($this->boardAvailabilityArray[$BoardDetailUrl] == true)) {
            $this->BoardDetailUrl = $this->getControllerUrlRoot() . "/{$BoardDetailUrl}/%s";
        } else {
            trigger_error("DetailURL is no set by AvaliavilityArray command");
            $this->BoardDetailUrl = null;
        }
    }

    public function getBoardDetailUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
//        trigger_error("local value: : " . $this->BoardDetailUrl);
        return $this->BoardDetailUrl;
    }

    public function getBoardDetailLink() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return k1_get_app_link($this->BoardDetailUrl);
    }

    public function getBoardDetailButton($buttonText = "Ver") {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return k1_get_link_button($this->getBoardDetailLink(), $buttonText);
    }

    /**
     * BOARD : NEW
     */
    public function setBoardNewUrl($BoardNewUrl) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        if (isset($this->boardAvailabilityArray[$BoardNewUrl]) && ($this->boardAvailabilityArray[$BoardNewUrl] == true)) {
            $this->BoardNewUrl = $this->getControllerUrlRoot() . "/{$BoardNewUrl}/";
        } else {
            $this->BoardNewUrl = null;
        }
    }

    public function getBoardNewUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->BoardNewUrl;
    }

    public function getBoardNewLink() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return k1_get_app_link($this->BoardNewUrl);
    }

    public function getBoardNewButton($buttonText = "Nuevo ítem") {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return k1_get_link_button($this->getBoardNewLink(), $buttonText);
    }

    /**
     * BOARD : EDIT
     */
    public function setBoardEditUrl($BoardEditUrl) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        if (isset($this->boardAvailabilityArray[$BoardEditUrl]) && ($this->boardAvailabilityArray[$BoardEditUrl] == true)) {
            $this->BoardEditUrl = $this->getControllerUrlRoot() . "/{$BoardEditUrl}/%s";
        } else {
            $this->BoardEditUrl = null;
        }
    }

    public function getBoardEditUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->BoardEditUrl;
    }

    /**
     * BOARD:  DELETE
     */
    public function setBoardDeleteUrl($BoardDeleteUrl) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);

        if (isset($this->boardAvailabilityArray[$BoardDeleteUrl]) && ($this->boardAvailabilityArray[$BoardDeleteUrl] == true)) {
            $this->BoardDeleteUrl = $this->getControllerUrlRoot() . "/{$BoardDeleteUrl}/%s";
        } else {
            $this->BoardDeleteUrl = null;
        }
    }

    public function getBoardDeleteUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->BoardDeleteUrl;
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_END 
     * 
     * ***************** */

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_NONE
     * 
     * ***************** */

    public function checkBoardLevelAccess($boardName) {
        return k1_check_user_level($this->getBoardLevelAccess($boardName));
    }

}
