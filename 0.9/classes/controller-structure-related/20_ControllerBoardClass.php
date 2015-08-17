<?php

class k1_board_general_class {

    use k1_object_execution_control;

    public $controllerObject;
    public $HtmlTableObject;
    // Board standar display config
    private $boardInternalName = null;
    private $boardTitle = null;
    private $boardDescripction = null;
    private $boardKeyWords = null;
    private $singleItemName = null;
    // SQL Related
    private $boardSqlWhereCondition = "";
    private $boardParameterKeyArray = Array();
    // FK related
    private $fkBoardUrl = null;
    private $fkBoardLabel = null;
    // Form related
    private $formActionUrl = null;
    private $formAfterActionUrl = Array(
        'delete' => '[board-view-all]',
        'new' => '[board-view]',
        'edit' => '[board-view]',
    );
    private $formMagicValue = null;
    private $formSubmitLabel = null;

    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(k1_general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {
        $this->phase_contruct();
//        $this->phase_config();
        $this->controllerObject = $controllerObject;

        $this->setBoardTitle($boardTitle);
        $this->setBoardDescripction($boardDescription);
        $this->setBoardKeyWords($boardKeywords);

        $this->boardSlug = &$this->controllerObject->boardTitle;
        // TODO: This shouldnt be here but right now it does, is for using the config file model that I should aviod
        $controllerObject->setBoardTableListUrl("view-all");
        $controllerObject->setBoardDetailUrl("view");
        $controllerObject->setBoardNewUrl("new");
        $controllerObject->setBoardEditUrl("edit");
        $controllerObject->setBoardDeleteUrl("delete");
    }

    public function setBoardInternalName($boardInternalName) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->boardInternalName = $boardInternalName;
    }

    public function getBoardSqlWherefromParameters($url_parameter = "") {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        if (empty($this->boardSqlWhereCondition)) {
            if (empty($url_parameter)) {
                $url_parameter = $this->controllerObject->getBoardUrlParameterValue();
            }
            $this->boardParameterKeyArray = k1_table_url_text_to_keys($url_parameter, $this->controllerObject->getControllerTableConfig());
            $this->boardSqlWhereCondition = k1_table_keys_to_where_condition($this->boardParameterKeyArray, $this->controllerObject->getControllerTableConfig());
//            d($this->boardSqlWhereCondition);
        }
        return $this->boardSqlWhereCondition;
    }

    public function setBoardTitle($boardTitle) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->boardTitle = $boardTitle;
    }

    public function setBoardDescripction($boardDescripction) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->boardDescripction = $boardDescripction;
    }

    public function setBoardKeyWords($boardKeyWords) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->boardKeyWords = $boardKeyWords;
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setFormActionUrl($formActionUrl, $formSubmitLabel) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
//        $this->formActionUrl = k1_get_app_link("{$this->controllerObject->getBoardRootUrl()}/$formActionUrl");
//        $formActionUrl = $this->controllerObject->parseUrlTag($formActionUrl);
//        d($this->controllerObject->getBoardRootUrl());
        $this->formActionUrl = k1_get_app_link("{$this->controllerObject->getBoardRootUrl()}/$formActionUrl");
        $this->formSubmitLabel = $formSubmitLabel;
    }

    public function setFormAfterActionUrl($formAfterActionUrl) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        $formAfterActionUrl = parseUrlTag($formAfterActionUrl, $this->controllerObject);
        $this->formAfterActionUrl = $formAfterActionUrl;
    }

    public function getFormAfterActionUrl($source = null) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        if (!empty($source) && isset($this->formAfterActionUrl[$source])) {
            return parseUrlTag($this->formAfterActionUrl[$source], $this->controllerObject);
        } else {
            return $this->controllerObject->getControllerUrlRoot();
        }
    }

    public function setFormMagicValue($formMagicValue) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        $this->formMagicValue = $formMagicValue;
    }

    public function setfkBoardUrl($fkBoardUrl) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        $this->fkBoardUrl = $fkBoardUrl;
    }

    public function setFkBoardLabel($fkBoardLabel) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        $this->fkBoardLabel = $fkBoardLabel;
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    public function initFormAction() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        // MAGIC VALUE
        $this->setFormMagicValue(k1_set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        k1_unset_serialize_var($this->controllerObject->getBoardFormId());
    }

    public function getBoardInternalName() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardInternalName;
    }

    public function getFormActionUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->formActionUrl;
    }

    public function getFormSubmitLabel() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->formSubmitLabel;
    }

    public function getFormMagicValue() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->formMagicValue;
    }

    public function getSingleItemName() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->singleItemName;
    }

    public function setSingleItemName($singleItemName) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        $this->singleItemName = $singleItemName;
    }

    public function getBoardTitle() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardTitle;
    }

    public function getBoardDescripction() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardDescripction;
    }

    public function getBoardKeyWords() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardKeyWords;
    }

    public function getBoardParameterKeyArray() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardParameterKeyArray;
    }

    public function getfkBoardUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->fkBoardUrl;
    }

    public function getFkBoardLabel() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->fkBoardLabel;
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    public function getControllerBackLink($customBackLink = "") {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->controllerObject->getBoardTableListUrl();
//        switch ($this->controllerObject->getControllerType()) {
//            case K1LIB_CONTROLLER_TYPE_MAIN:
//                break;
//            case K1LIB_CONTROLLER_TYPE_FOREIGN:
//                return k1_get_app_link($this->getfkBoardUrl());
//                break;
//            default:
//                die("There is no Board Type Defined on " . __CLASS__);
//        }
    }

    public function getControllerBackButton($customBackLink = null, $buttonText = "Volver", $mini = "true", $icon_pos = "left", $inline = "true") {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        if (empty($customBackLink)) {
            $backUrl = $this->getControllerBackLink();
        } else {
            $backUrl = $customBackLink;
        }
        return k1_get_link_button($backUrl, $buttonText, $mini, $icon_pos, $inline);
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
}

class net_klan1_dev_BoardNew extends k1_board_general_class {
    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(k1_general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {
        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_CONFIG
     * 
     * ***************** */
    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    function initFormAction() {
        parent::initFormAction();

        if ($this->controllerObject->getControllerType() == K1LIB_CONTROLLER_TYPE_FOREIGN) {
            $this->boardParameterKeyArray = k1_table_url_text_to_keys($this->controllerObject->getBoardFkUrlValue(), $this->controllerObject->getControllerFkTableConfig());
            if (!empty($this->boardParameterKeyArray)) {
                k1_serialize_var($this->boardParameterKeyArray, "{$this->controllerObject->getBoardFormId()}-fkData");
                k1_serialize_var($this->boardParameterKeyArray, $this->controllerObject->getBoardFormId());
            }
        }
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    function makeDoAction() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
        /**
         *  BOARD WHEN EDIT FORM IS SUBMITED
         */
        // check and serialize post vars
        $controller_errors = array();
        $fkData = k1_unserialize_var("{$this->controllerObject->getBoardFormId()}-fkData");
        if (!empty($fkData)) {
            $_POST = $fkData + $_POST;
        }
        $form_vars = k1_get_all_request_vars($_POST, $this->controllerObject->getBoardFormId());
        $form_errors = array();
        if (isset($form_vars['magic_value'])) {
            //Magic test 
            $magic_test = k1_check_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}", $form_vars['magic_value']);

            if ($magic_test == true) {
                //remove the magic value from the data array
                unset($form_vars['magic_value']);
                //php check for the data
                $form_errors = k1_form_check_values($form_vars, $this->controllerObject->getControllerTableConfig(), $this->controllerObject->db);
                if ($form_errors === false) {
                    if (k1_sql_insert($this->controllerObject->db, $this->controllerObject->getDbTableMainName(), $form_vars)) {
//                        k1_html_header_go(k1_make_url_from_rewrite(-2));
                        // TODO: implement the after action behavior
                        // UNSET ALL FOR NO FUTURE PROBLEMS ;)
                        k1_unset_serialize_var($this->controllerObject->getBoardFormId());
                        k1_unset_serialize_var($this->controllerObject->getBoardErrorId());
                        k1_unset_serialize_var($this->controllerObject->getBoardFormErrorId());
                        k1_unset_serialize_var("{$this->controllerObject->getBoardFormId()}-fkData");
                        k1_html_header_go($this->controllerObject->getControllerUrlRoot() . "/view-all");
                    } else {
                        $do_check = true;
                        $controller_errors[] = "No se ha podido insertar el registro.";
                        $db_error = $this->controllerObject->db->errorInfo();
                        $controller_errors[] = $db_error[2];
//                        die;
                    }
                } else {
//                    d($form_errors);
//                    die('si errores');

                    $do_check = true;
                }
            } else {
                $do_check = true;
                $controller_errors[] = "Bad Magic !!";
            }
        } else {
            $do_check = true;
            $controller_errors[] = "No Magic !!";
        }
        if ($do_check) {
            k1_serialize_var($controller_errors, $this->controllerObject->getBoardErrorId());
            k1_serialize_var($form_errors, $this->controllerObject->getBoardFormErrorId());
            $form_check_url = "{$this->controllerObject->getBoardRootUrl()}/check";
//            d($form_check_url);
            k1_html_header_go($form_check_url);
            exit;
        }
    }

    function makeCheckAction() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);

        $this->setFormMagicValue(k1_set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        $this->setFormActionUrl("do", "Insertar");

        /**
         * TODO: DO NOT use global here!!
         */
        global $form_errors, $controller_errors;
        $controller_errors = k1_unserialize_var($this->controllerObject->getBoardErrorId());
        $form_errors = k1_unserialize_var($this->controllerObject->getBoardFormErrorId());
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
}

class net_klan1_dev_BoardViewAll extends k1_board_general_class {
    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(k1_general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {

        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $this->HtmlTableObject = new html_table_with_table_config($this->controllerObject->getBoardID());
        $this->controllerObject->setUrlLevel("table-action", false);
        $this->controllerObject->setUrlLevel("page-number", false);
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setBoardTableMode() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);

        $tableSQLFilter = $this->controllerObject->getDbTableMainSQLFilter();

        switch ($this->controllerObject->getControllerType()) {
            case K1LIB_CONTROLLER_TYPE_MAIN:
                $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()}";
                if ($tableSQLFilter) {
                    $sql_query .= " WHERE $tableSQLFilter";
                }
                break;
            case K1LIB_CONTROLLER_TYPE_FOREIGN:
                $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()} WHERE {$this->controllerObject->getForeignKeyWhereCondition()}";
                if ($tableSQLFilter) {
                    $sql_query .= " AND $tableSQLFilter";
                }
                break;
            default:
                die("There is no Board Type Defined on " . __CLASS__);
        }

        // SQL for the list table
        // HTML Table Object
        $this->HtmlTableObject->modeSQL($sql_query, $this->controllerObject->getControllerTableConfig());
    }

    public function setSqlMode($sql, $tablesToGetConfig) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        $tablesToGetConfigArray = explode(",", $tablesToGetConfig);
        $tablesConfigArray = Array();
        foreach ($tablesToGetConfigArray as $tableName) {
            $configTable = k1_get_table_config($db, $tableName);
        }
        $this->HtmlTableObject->modeSQL($sql, $this->controllerObject->getControllerTableConfig());
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */


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

    /**
     * 
     * @param type $sql
     * @param type $tablesToGetConfig
     * 
     * TODO: This wont work as it should !!
     */
}

class net_klan1_dev_BoardDelete extends k1_board_general_class {
    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(k1_general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {

        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $sql_query = "DELETE FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $record_deleted = k1_sql_query($this->controllerObject->db, $sql_query, false);
        if ($record_deleted === null) {
            k1_html_header_go($this->getFormAfterActionUrl('delete'));
//            k1_html_header_go($this->controllerObject->getBoardRootUrl() . "/view-all");
        } else {
            global $controller_errors;
            $controller_errors[] = "No se pude borrar el registro, posiblemente esta en uso";
        }
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_CONFIG
     * 
     * ***************** */
    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */


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
}

class net_klan1_dev_BoardView extends k1_board_general_class {
    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(k1_general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {

        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $sql_result = k1_sql_query($this->controllerObject->db, $sql_query, false);
        k1_serialize_var($sql_result, $this->controllerObject->getBoardId());
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_CONFIG
     * 
     * ***************** */
    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    public function getBoardEditLink() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->controllerObject->getBoardEditUrl();
    }

    public function getBoardEditButton($buttonText = "Editar") {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return k1_get_link_button((sprintf($this->getBoardEditLink(), $this->controllerObject->getBoardUrlParameterValue())), $buttonText);
    }

    public function getBoardDeleteLink() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->controllerObject->getBoardDeleteUrl();
    }

    public function getBoardDeleteButton($buttonText = "Borrar") {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return k1_get_link_button((sprintf($this->getBoardDeleteLink(), $this->controllerObject->getBoardUrlParameterValue())), $buttonText);
    }

    public function getControllerFKDetatilLink() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        $fkBoardUrl = $this->getfkBoardUrl();
        if ($fkBoardUrl) {
            return k1_get_app_link($this->getfkBoardUrl());
        } else {
            return false;
        }
    }

    public function getControllerFKDetatilButton() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
//        d($url);
//        d($this->controllerObject->getBoardUrlParameterValue());
        $controllerFKDetatilLink = $this->getControllerFKDetatilLink();
        if (!empty($controllerFKDetatilLink)) {
            $url = (sprintf($this->getControllerFKDetatilLink(), "{$this->controllerObject->getBoardUrlParameterValue()}"));
            return k1_get_link_button($url, $this->getFkBoardLabel());
        }
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */


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
}

class net_klan1_dev_BoardEdit extends k1_board_general_class {
    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(k1_general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {

        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);


        // URL ID change protection
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    function initFormAction() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        parent::initFormAction();
        $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $sql_result = k1_sql_query($this->controllerObject->db, $sql_query, false);
        k1_serialize_var($sql_result, $this->controllerObject->getBoardFormId() . "-sqlResult");
        k1_serialize_var($sql_result, $this->controllerObject->getBoardFormId() . "-sqlOriginalResult");
        k1_serialize_var($this->getBoardParameterKeyArray(), "{$this->controllerObject->getBoardFormId()}-tableKeys");
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    function makeDoAction() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
        /**
         *  BOARD WHEN EDIT FORM IS SUBMITED
         */
        // check and serialize post vars

        $form_vars = k1_get_all_request_vars($_POST, $this->controllerObject->getBoardFormId() . "-sqlResult");
        if (isset($form_vars['magic_value'])) {
            //Magic test 

            $magic_test = k1_check_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}", $form_vars['magic_value']);
            if ($magic_test == true) {
                //remove the magic value from the data array
                unset($form_vars['magic_value']);
                //php check for the data
                $form_errors = k1_form_check_values($form_vars, $this->controllerObject->getControllerTableConfig(), $this->controllerObject->db);
                $do_check = false;
                if ($form_errors === false) {
                    //unserialize the keys array to append to the data array arrived by POST
                    $table_keys_array = k1_unserialize_var("{$this->controllerObject->getBoardFormId()}-tableKeys");
                    if ($table_keys_array === false) {
                        $controller_errors[] = "Haz intentado actualizar otro registro moficando el codigo HTML " . "{$this->controllerObject->getBoardFormId()}-tableKeys";
                        $do_check = true;
                    } else {
                        $record_updated = k1_sql_update($this->controllerObject->db, $this->controllerObject->getDbTableMainName(), $form_vars, $table_keys_array, $this->controllerObject->getControllerTableConfig());
                        if ($record_updated !== false) {
                            $actionUrl = sprintf($this->getFormAfterActionUrl('edit'), $this->controllerObject->getBoardUrlParameterValue());

                            // UNSET ALL FOR NO FUTURE PROBLEMS ;)
                            k1_unset_serialize_var($this->controllerObject->getBoardFormId());
                            k1_unset_serialize_var($this->controllerObject->getBoardErrorId());
                            k1_unset_serialize_var($this->controllerObject->getBoardFormErrorId());
                            k1_unset_serialize_var($this->controllerObject->getBoardFormId() . "-sqlResult");
                            k1_unset_serialize_var($this->controllerObject->getBoardFormId() . "-sqlOriginalResult");
                            k1_unset_serialize_var($this->controllerObject->getBoardFormId() . "-tableKeys");
                            k1_html_header_go($actionUrl, true);
                        } else {
                            $controller_errors[] = "No se pudo hacer el update, Posiblemente los datos no han cambiado";
                            $do_check = true;
                        }
                    }
                } else {
                    $do_check = true;
                }
            } else {
                $do_check = true;
                $controller_errors[] = "Bad Magic !!";
            }
        } else {
            $do_check = true;
            $controller_errors[] = "No Magic !!";
        }
        if ($do_check) {
            k1_serialize_var($controller_errors, $this->controllerObject->getBoardErrorId());
            k1_serialize_var($form_errors, $this->controllerObject->getBoardFormErrorId());
            $form_check_url = "{$this->controllerObject->getBoardRootUrl()}/check";
            k1_html_header_go($form_check_url);
        }
    }

    function makeCheckAction() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
//        $this->initFormAction();
        $this->setFormMagicValue(k1_set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        /**
         * TODO: DO NOT use global here!!
         */
        global $form_errors, $controller_errors;
        $controller_errors = k1_unserialize_var($this->controllerObject->getBoardErrorId());
        $form_errors = k1_unserialize_var($this->controllerObject->getBoardFormErrorId());
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
}
