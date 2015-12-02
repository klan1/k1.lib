<?php

namespace k1lib\crud;

use k1lib\session\session_plain as k1lib_session;

class completeEasyController extends controller_with_dbtables_class {

    private $boardID;
    private $boardFormId;
    private $boardErrorId;
    private $boardFormErrorId;
    private $boardRootUrl = NULL;
    private $boardUrlValue = FALSE;
    private $boardUrlName = FALSE;
    private $boardUrlParameterValue = NULL;
    private $boardUrlParameterValueArray = array();
    private $boardUrlParameterName = NULL;
    private $boardUrlParameterWhereCondition = "";
    private $boardUrlActionValue = FALSE;
    private $boardUrlActionName = FALSE;
    private $boardFkUrlValue = FALSE;
    private $boardFkUrlName = FALSE;
    private $board_fk_extra_url_value = FALSE;
    private $board_fk_extra_url_value_pos = NULL;
    private $board_fk_extra_table_config = FALSE;
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
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(\PDO $db) {
        parent::__construct($db);
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setBoardAvailability($boardName, $availabilityOption = TRUE) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);

        $this->boardAvailabilityArray[$boardName] = $availabilityOption;
        if (!isset($this->boardViewToLoadArray[$boardName])) {
            $this->boardViewToLoadArray[$boardName] = $boardName;
        }
        if (!isset($this->boardLevelAccessArray[$boardName])) {
            $this->boardLevelAccessArray[$boardName] = $this->getDefaultAuthLevelAccess();
        }
    }

    public function setBoardViewToLoad($boardName, $BoardViewToLoadOption) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->boardViewToLoadArray[$boardName] = $BoardViewToLoadOption;
    }

    public function setBoardLevelAccess($boardName, $accessLevelCSV = TRUE) {
        $this->boardLevelAccessArray [$boardName] = $accessLevelCSV;
    }

    public function setBoardID($boardID) {
        $this->boardID = $boardID;
    }

    public function getControllerUrlRoot() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        if ($this->getControllerType() === \k1lib\crud\CONTROLLER_TYPE_FOREIGN) {
            return (parent::getControllerUrlRoot() . "/{$this->boardFkUrlValue}");
        } else {
            return (parent::getControllerUrlRoot());
        }
    }

    function set_board_fk_extra_url_value($board_fk_extra_url_value, $pos = "pre") {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->board_fk_extra_url_value = $board_fk_extra_url_value;
        $this->board_fk_extra_url_value_pos = $pos;
    }

    function set_board_fk_extra_table_config($board_fk_extra_table_config) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->board_fk_extra_table_config = $board_fk_extra_table_config;
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

//    public function setBoardRootUrl($boardRootUrl) {
//        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);
//        $this->boardRootUrl = $boardRootUrl;
//    }
    function get_board_fk_extra_url_value() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);
        return $this->board_fk_extra_url_value;
    }

    function get_board_fk_extra_url_value_pos() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);
        return $this->board_fk_extra_url_value_pos;
    }

    function get_board_fk_extra_table_config() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);
        return $this->board_fk_extra_table_config;
    }

    public function setDefaultBoardUrlValue($defaultBoardUrlValue) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);

        if (is_string($defaultBoardUrlValue)) {
            if (empty($this->boardUrlValue)) {
                \k1lib\html\html_header_go("{$this->getControllerUrlRoot()}{$defaultBoardUrlValue}/");
            }
        } else {
            trigger_error("\$defaultBoardUrlValue must to be a string on " . __METHOD__, E_USER_ERROR);
        }
    }

    public function initBoardUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);
        /**
         * As the URL init has to be initialized left-right we need to init first the FK if is necessary
         */
//        if ($this->getControllerType() === \k1lib\crud\CONTROLLER_TYPE_FOREIGN) {
//            $this->initBoardFkUrl();
//        }
        $this->boardUrlName = "url_board";
        $this->boardUrlValue = $this->setUrlLevel($this->boardUrlName, FALSE);

        $this->boardRootUrl = ($this->getControllerUrlRoot() . "/" . $this->getBoardUrlValue());
        $this->boardID = \k1lib\urlrewrite\url_manager::get_this_controller_id();
// DATA FROM SQL will use this ID
        $this->boardFormId = $this->boardID . "-data";
// CONTROLLER SPECIFIC ERRORS will use this ID
        $this->boardErrorId = $this->boardID . "-errors";
// FORM (POST) SPECIFIC ERRORS will use this ID
        $this->boardFormErrorId = $this->boardID . "-form";
        // Defauts
        // Avialibility of controllers and boards
        if ($this->getBoardAvailability("view-all") !== FALSE) {
            $this->setBoardAvailability("view-all", TRUE);
        }
        if ($this->getBoardAvailability("view") !== FALSE) {
            $this->setBoardAvailability("view", TRUE);
        }
        if ($this->getBoardAvailability("delete") !== FALSE) {
            $this->setBoardAvailability("delete", TRUE);
        }
        if ($this->getBoardAvailability("new") !== FALSE) {
            $this->setBoardAvailability("new", TRUE);
        }
        if ($this->getBoardAvailability("edit") !== FALSE) {
            $this->setBoardAvailability("edit", TRUE);
        }
        // sets de URL on each one with Avaliability arrays
//        $this->setBoardTableListUrl("view-all");
//        $this->setBoardDetailUrl("view");
//        $this->setBoardNewUrl("new");
//        $this->setBoardEditUrl("edit");
//        $this->setBoardDeleteUrl("delete");
    }

    public function initBoardFkUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__, TRUE);

        $this->boardFkUrlName = "foreing_key";
        $this->boardFkUrlValue = $this->setUrlLevel($this->boardFkUrlName, FALSE);

        if ($this->boardFkUrlValue !== FALSE) {
//            $this->boardFkUrlValue = $this->boardFkUrlValue . "--" . $this->board_fk_extra_url_value;
            $this->boardFkUrlValue = $this->boardFkUrlValue;
            /**
             * This code is for complete the PK array with info comming from a GET var as text, 
             * this should be managed from the ccontroller config file
             */
            if (!empty($this->board_fk_extra_url_value) && ($this->board_fk_extra_url_value_pos == "pre")) {
                // Put togeter the TABLE KEY TEXT
                $boardFkUrlValue = $this->boardFkUrlValue . "--" . $this->board_fk_extra_url_value;
                //  Puting togeter the Table Config array from the FKTable and the Extra Table
                $board_fk_config_with_extra = $this->board_fk_extra_table_config + $this->dbTableForeignObject->getTableFieldConfig();
                $board_fk_config_with_extra = \k1lib\common\organize_array_with_guide($board_fk_config_with_extra, $this->dbTableMainObject->getTableFieldConfig());

                $this->boardFkUrlValueArray = \k1lib\sql\table_url_text_to_keys($boardFkUrlValue, $board_fk_config_with_extra);
                $this->foreignKeyWhereCondition = \k1lib\sql\table_keys_to_where_condition($this->boardFkUrlValueArray, $board_fk_config_with_extra);
            } elseif (!empty($this->board_fk_extra_url_value) && ($this->board_fk_extra_url_value_pos == "post")) {
                trigger_error("Not implemented PRE position yet.", E_USER_ERROR);
            } else {
                $this->boardFkUrlValueArray = \k1lib\sql\table_url_text_to_keys(
                        $this->boardFkUrlValue
                        , $this->dbTableForeignObject->getTableFieldConfig()
                );
                $this->foreignKeyWhereCondition = \k1lib\sql\table_keys_to_where_condition(
                        $this->boardFkUrlValueArray
                        , $this->dbTableForeignObject->getTableFieldConfig()
                );
            }
//            d($this->boardFkUrlValue);
//            d($this->boardFkUrlValueArray);
//            d($this->foreignKeyWhereCondition);
//            oh yeah optmization !!
//  //          $this->setControllerUrlRoot($this->getControllerUrlRoot() . "/{$this->boardFkUrlValue}");
        } else {
            die("No se obtuvo el ID de la tabla auxiliar");
        }
//        $this->boardFkUrlValueArray = \k1lib\sql\table_url_text_to_keys($this->boardFkUrlValue, $this->dbTableForeignObject->getTableFieldConfig());
    }

    public function getBoardAvailability($boardName) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);

        if (isset($this->boardAvailabilityArray[$boardName])) {
            return $this->boardAvailabilityArray[$boardName];
        } else {
            return NULL;
        }
    }

    public function getBoardLevelAccess($boardName) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);

        if (isset($this->boardLevelAccessArray [$boardName])) {
            return $this->boardLevelAccessArray [$boardName];
        } else {
            return NULL;
        }
    }

    public function getDefaultBoardUrlValue() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->defaultBoardUrlValue;
    }

    public function getBoardUrlName() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlName;
    }

    public function getBoardUrlValue() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlValue;
    }

    public function getBoardFkUrlName() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardFkUrlName;
    }

    public function getBoardFkUrlValue() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        if (!empty($this->boardFkUrlValue)) {
            return $this->boardFkUrlValue;
        } else {
            return NULL;
        }
    }

    public function getBoardFkUrlValueArray() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        if (!empty($this->boardFkUrlValueArray)) {
            return $this->boardFkUrlValueArray;
        } else {
            return NULL;
        }
    }

    public function getForeignKeyWhereCondition() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->foreignKeyWhereCondition;
    }

    public function getBoardFormId() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardFormId;
    }

    public function getBoardErrorId() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardErrorId;
    }

    public function getBoardFormErrorId() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardFormErrorId;
    }

    public function getBoardUrlParameterName() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlParameterName;
    }

    public function getBoardUrlParameterValueArray() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlParameterValueArray;
    }

    public function getBoardUrlParameterValue() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlParameterValue;
    }

    public function getFkTabletLabelField() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        if ($this->foreignTableLabelField === "") {
            $tableConfig = $this->dbTableForeignObject->getTableFieldConfig();
            $this->foreignTableLabelField = \k1lib\sql\get_table_label($tableConfig);
        }
        return $this->foreignTableLabelField;
    }

    public function getFkLabelValue() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        if (empty($this->foreignLabelValue)) {
//            \d($this->dbTableForeignObject->getTableFieldConfig());
            $fk_label = \k1lib\sql\get_fk_field_label($this->get_dbTableForeignName(), $this->getBoardFkUrlValueArray(), -1);
            if (!empty($fk_label)) {
                $this->foreignLabelValue = $fk_label;
            } else {
                $this->foreignLabelValue = NULL;
                return FALSE;
            }
        }
        return $this->foreignLabelValue;
    }

    public function getBoardUrlParameterWhereCondition() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlParameterWhereCondition;
    }

    public function getBoardUrlActionValue() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlActionValue;
    }

    public function getBoardUrlActionName() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardUrlActionName;
    }

    public function getBoardAvailabilityArray() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardAvailabilityArray;
    }

    public function getViewToInclude() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        $boardViewToLoadValue = $this->boardViewToLoadArray[$this->getBoardUrlValue()];
        if ($boardViewToLoadValue != $this->getBoardUrlValue()) {
            return \k1lib\templates\temply::load_view($boardViewToLoadValue, APP_VIEWS_PATH);
        } else {
            return \k1lib\templates\temply::load_view($boardViewToLoadValue, APP_VIEWS_CRUD_PATH);
        }
    }

    public function getBoardViewToLoad($boardName) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardViewToLoadArray[$boardName];
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    public function getBoardID() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->boardID;
    }

    public function getBoardRootUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        /*
         * WTF fix ?? --> FOUND !!
         */
//        return $this->getControllerUrlRoot() . "/" . $this->boardUrlParameterValue;
//        d($this->getControllerUrlRoot());
//        d($this->boardRootUrl);
        $boardRootURL = NULL;

        if (!empty($this->getBoardUrlParameterValue())) {
            return $this->boardRootUrl . "/" . $this->getBoardUrlParameterValue();
        } else {
            return $this->boardRootUrl;
        }
    }

    public function initBoardUrlAction() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
        $this->boardUrlActionName = "url_BoardAction";
        $this->boardUrlActionValue = $this->setUrlLevel($this->boardUrlActionName, FALSE);
    }

    public function initBoardUrlParameter() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);

        $this->boardUrlParameterName = "url_BoardParameter";
        $this->boardUrlParameterValue = $this->setUrlLevel($this->boardUrlParameterName, FALSE);
        /**
         * WTF fix !!
          $this->setBoardRootUrl($this->getBoardRootUrl() . "/" . $this->boardUrlParameterValue);
         */
        $this->boardID = \k1lib\urlrewrite\url_manager::get_this_controller_id();
        // DATA FROM SQL will use this ID
        $this->boardFormId = $this->boardID . "-data";
// CONTROLLER SPECIFIC ERRORS will use this ID
        $this->boardErrorId = $this->boardID . "-errors";
// FORM (POST) SPECIFIC ERRORS will use this ID
        $this->boardFormErrorId = $this->boardID . "-form";
//        d($this->boardID);
        $this->boardUrlParameterValueArray = \k1lib\sql\table_url_text_to_keys($this->boardUrlParameterValue, $this->dbTableMainObject->getTableFieldConfig());
        $this->boardUrlParameterWhereCondition = \k1lib\sql\table_keys_to_where_condition($this->boardUrlParameterValueArray, $this->dbTableMainObject->getTableFieldConfig());
//        d($this->dbTableMainObject->getTableFieldConfig());
    }

    /**
     * BOARD: VIEW ALL 
     */
    public function setBoardTableListUrl($BoardTableListUrl) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        if (isset($this->boardAvailabilityArray[$BoardTableListUrl]) && ($this->boardAvailabilityArray[$BoardTableListUrl] == TRUE)) {
            $this->BoardTableListUrl = $this->getControllerUrlRoot() . "/{$BoardTableListUrl}/";
        } else {
            $this->BoardTableListUrl = NULL;
        }
    }

    public function getBoardTableListUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->BoardTableListUrl;
    }

    /**
     * BOARD: VIEW
     */
    public function setBoardDetailUrl($BoardDetailUrl) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        if (isset($this->boardAvailabilityArray[$BoardDetailUrl]) && ($this->boardAvailabilityArray[$BoardDetailUrl] == TRUE)) {
            $this->BoardDetailUrl = $this->getControllerUrlRoot() . "/{$BoardDetailUrl}/%s";
        } else {
            trigger_error("DetailURL is no set by AvaliavilityArray command");
            $this->BoardDetailUrl = NULL;
        }
    }

    public function getBoardDetailUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
//        trigger_error("local value: : " . $this->BoardDetailUrl);
        return $this->BoardDetailUrl;
    }

    public function getBoardDetailLink() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return \k1lib\urlrewrite\url_manager::get_app_link($this->BoardDetailUrl);
    }

    public function getBoardDetailButton($buttonText = "Ver") {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return \k1lib\html\get_link_button($this->getBoardDetailLink(), $buttonText);
    }

    /**
     * BOARD : NEW
     */
    public function setBoardNewUrl($BoardNewUrl) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        if (isset($this->boardAvailabilityArray[$BoardNewUrl]) && ($this->boardAvailabilityArray[$BoardNewUrl] == TRUE)) {
            $this->BoardNewUrl = $this->getControllerUrlRoot() . "/{$BoardNewUrl}/";
        } else {
            $this->BoardNewUrl = NULL;
        }
    }

    public function getBoardNewUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->BoardNewUrl;
    }

    public function getBoardNewLink() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return \k1lib\urlrewrite\url_manager::get_app_link($this->BoardNewUrl);
    }

    public function getBoardNewButton($buttonText = "Nuevo ítem") {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return \k1lib\html\get_link_button($this->getBoardNewLink(), $buttonText);
    }

    /**
     * BOARD : EDIT
     */
    public function setBoardEditUrl($BoardEditUrl) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        if (isset($this->boardAvailabilityArray[$BoardEditUrl]) && ($this->boardAvailabilityArray[$BoardEditUrl] == TRUE)) {
            $this->BoardEditUrl = $this->getControllerUrlRoot() . "/{$BoardEditUrl}/%s";
        } else {
            $this->BoardEditUrl = NULL;
        }
    }

    public function getBoardEditUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->BoardEditUrl;
    }

    /**
     * BOARD:  DELETE
     */
    public function setBoardDeleteUrl($BoardDeleteUrl) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);

        if (isset($this->boardAvailabilityArray[$BoardDeleteUrl]) && ($this->boardAvailabilityArray[$BoardDeleteUrl] == TRUE)) {
            $this->BoardDeleteUrl = $this->getControllerUrlRoot() . "/{$BoardDeleteUrl}/%s";
        } else {
            $this->BoardDeleteUrl = NULL;
        }
    }

    public function getBoardDeleteUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->BoardDeleteUrl;
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

    public function checkBoardLevelAccess($boardName) {
        return k1lib_session::check_user_level($this->getBoardLevelAccess($boardName));
    }

}
