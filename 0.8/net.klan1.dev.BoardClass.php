<?php

class net_klan1_dev_BoardGeneral {

    public $controllerObject;
    public $HtmlTableObject;
    private $boardTitle;
    private $singleItemName;
    private $boardDescripction;
    private $boardKeyWords;
    private $boardSqlWhereCondition = "";
    private $boardParameterKeyArray = Array();
    private $fkBoardUrl = null;
    private $fkBoardLabel = null;
    private $formActionUrl = null;
    private $formAfterActionUrl = null;
    private $formMagicValue = null;
    private $formSubmitLabel = null;

    function init(net_klan1_dev_controllerClass &$controllerObject, $boardTitle, $boardDescription = '', $boardKeywords = Array()) {
        $this->controllerObject = &$controllerObject;

        $this->setBoardTitle($boardTitle);
        $this->setBoardDescripction($boardDescription);
        $this->setBoardKeyWords($boardKeywords);

        $this->sectionSlug = &$this->controllerObject->boardTitle;
    }

    public function initFormAction() {
        // MAGIC VALUE
        $this->setFormMagicValue(k1_set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        k1_unset_serialize_var($this->controllerObject->getBoardFormId());
    }

    public function getFormActionUrl() {
        return $this->formActionUrl;
    }

    public function setFormActionUrl($formActionUrl, $formSubmitLabel) {
//        $this->formActionUrl = k1_get_app_link("{$this->controllerObject->getBoardRootUrl()}/$formActionUrl");
//        $formActionUrl = $this->controllerObject->parseUrlTag($formActionUrl);
        $this->formActionUrl = k1_get_app_link("{$this->controllerObject->getBoardRootUrl()}/$formActionUrl");
        $this->formSubmitLabel = $formSubmitLabel;
    }

    public function setFormAfterActionUrl($formAfterActionUrl) {
        $formAfterActionUrl = $this->controllerObject->parseUrlTag($formAfterActionUrl);
        $this->formAfterActionUrl = $formAfterActionUrl;
    }

    public function getFormAfterActionUrl() {
        return $this->formAfterActionUrl;
    }

    public function getFormSubmitLabel() {
        return $this->formSubmitLabel;
    }

    public function getFormMagicValue() {
        return $this->formMagicValue;
    }

    public function setFormMagicValue($formMagicValue) {
        $this->formMagicValue = $formMagicValue;
    }

    public function getSingleItemName() {
        return $this->singleItemName;
    }

    public function setSingleItemName($singleItemName) {
        $this->singleItemName = $singleItemName;
    }

    public function getBoardTitle() {
        return $this->boardTitle;
    }

    public function getBoardDescripction() {
        return $this->boardDescripction;
    }

    public function getBoardKeyWords() {
        return $this->boardKeyWords;
    }

    public function setBoardTitle($boardTitle) {
        $this->boardTitle = $boardTitle;
    }

    public function setBoardDescripction($boardDescripction) {
        $this->boardDescripction = $boardDescripction;
    }

    public function setBoardKeyWords($boardKeyWords) {
        $this->boardKeyWords = $boardKeyWords;
    }

    public function getBoardSqlWherefromParameters($url_parameter = "") {
        if (empty($this->boardSqlWhereCondition)) {
            if (empty($url_parameter)) {
                $url_parameter = $this->controllerObject->getBoardUrlParameterValue();
            }
            $this->boardParameterKeyArray = k1_table_url_text_to_keys($url_parameter, $this->controllerObject->getControllerTableConfig());
            $this->boardSqlWhereCondition = k1_table_keys_to_where_condition($this->boardParameterKeyArray, $this->controllerObject->getControllerTableConfig());
        }
        return $this->boardSqlWhereCondition;
    }

    public function getBoardParameterKeyArray() {
        return $this->boardParameterKeyArray;
    }

    public function getfkBoardUrl() {
        return $this->fkBoardUrl;
    }

    public function setfkBoardUrl($fkBoardUrl) {
        $this->fkBoardUrl = $fkBoardUrl;
    }

    public function getFkBoardLabel() {
        return $this->fkBoardLabel;
    }

    public function setFkBoardLabel($fkBoardLabel) {
        $this->fkBoardLabel = $fkBoardLabel;
    }

    public function getModuleBackLink($customBackLink = "") {
        return $this->controllerObject->getModuleTableListUrl();
//        switch ($this->controllerObject->getControllerType()) {
//            case K1LIB_CONTROLLER_TYPE_MAIN:
//                break;
//            case K1LIB_CONTROLLER_TYPE_FOREIGN:
//                return k1_get_app_link($this->getfkBoardUrl());
//                break;
//            default:
//                die("There is no Section Type Defined on " . __CLASS__);
//        }
    }

    public function getModuleBackButton($customBackLink = null, $buttonText = "Volver", $mini = "true", $icon_pos = "left", $inline = "true") {
        if (empty($customBackLink)) {
            $backUrl = $this->getModuleBackLink();
        } else {
            $backUrl = $customBackLink;
        }
        return k1_get_link_button($backUrl, $buttonText, $mini, $icon_pos, $inline);
    }

}

class net_klan1_dev_BoardNew extends net_klan1_dev_BoardGeneral {

    function __construct(net_klan1_dev_controllerClass &$controllerObject, $boardTitle, $boardDescription = '', $boardKeywords = Array()) {

        $this->init($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
    }

    function initFormAction() {
        parent::initFormAction();

        if ($this->controllerObject->getControllerType() == K1LIB_CONTROLLER_TYPE_FOREIGN) {
            $this->boardParameterKeyArray = k1_table_url_text_to_keys($this->controllerObject->getBoardFkUrlValue(), $this->controllerObject->getControllerFkTableConfig());
            if (!empty($this->boardParameterKeyArray)) {
                k1_serialize_var($this->boardParameterKeyArray, "FkData");
                k1_serialize_var($this->boardParameterKeyArray, $this->controllerObject->getBoardFormId());
            }
        }
    }

    function makeDoAction() {
        /**
         *  SECTION WHEN EDIT FORM IS SUBMITED
         */
        // check and serialize post vars
        $fkData = k1_unserialize_var("FkData");
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
                        k1_html_header_go(k1_make_url_from_rewrite(-2));
                    } else {
                        $do_check = true;
                        $controller_errors[] = "No se ha podido insertar el registro, posiblemente la mesa ya existe.";
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
            k1_html_header_go($form_check_url);
        }
    }

    function makeCheckAction() {
        $this->setFormMagicValue(k1_set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        $this->setFormActionUrl("do", "Insertar");

        /**
         * TODO: DO NOT use global here!!
         */
        global $form_errors, $controller_errors;
        $controller_errors = k1_unserialize_var($this->controllerObject->getBoardErrorId());
        $form_errors = k1_unserialize_var($this->controllerObject->getBoardFormErrorId());
    }

}

class net_klan1_dev_BoardViewAll extends net_klan1_dev_BoardGeneral {

    function __construct(net_klan1_dev_controllerClass &$controllerObject, $boardTitle, $boardDescription = '', $boardKeywords = Array()) {

        $this->init($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $this->HtmlTableObject = new k1_HtmlTableV3($this->controllerObject->getBoardID());
        $this->controllerObject->setUrlLevel("table-action", false);
        $this->controllerObject->setUrlLevel("page-number", false);
    }

    public function setControllerTableMode() {

        $tableSQLFilter = $this->controllerObject->getDbTableMainSQLFilter();

        switch ($this->controllerObject->getControllerType()) {
            case K1LIB_CONTROLLER_TYPE_MAIN:
                $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()}";
                if ($tableSQLFilter) {
                    $sql_query .= " WHERE $tableSQLFilter";
                }
                break;
            case K1LIB_CONTROLLER_TYPE_FOREIGN:
                $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()} WHERE {$this->controllerObject->foreignKeyWhereCondition}";
                if ($tableSQLFilter) {
                    $sql_query .= " AND $tableSQLFilter";
                }
                break;
            default:
                die("There is no Section Type Defined on " . __CLASS__);
        }

        // SQL for the list table
        // HTML Table Object
        $this->HtmlTableObject->modeSQL($sql_query, $this->controllerObject->getControllerTableConfig());
    }

    public function setSqlMode($sql, $tablesToGetConfig) {
        $tablesToGetConfigArray = explode(",", $tablesToGetConfig);
        $tablesConfigArray = Array();
        foreach ($tablesToGetConfigArray as $tableName) {
            $configTable = k1_get_table_config($db, $tableName);
        }
        $this->HtmlTableObject->modeSQL($sql, $this->controllerObject->getControllerTableConfig());
    }

}

class net_klan1_dev_BoardDelete extends net_klan1_dev_BoardGeneral {

    function __construct(net_klan1_dev_controllerClass &$controllerObject, $boardTitle, $boardDescription = '', $boardKeywords = Array()) {

        $this->init($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $sql_query = "DELETE FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $record_deleted = k1_sql_query($this->controllerObject->db, $sql_query, false);
        if ($record_deleted === null) {
            k1_html_header_go(k1_make_url_from_rewrite(-2));
        } else {
            global $controller_errors;
            $controller_errors[] = "No se pude borrar el registro, posiblemente esta en uso";
        }
    }

}

class net_klan1_dev_BoardView extends net_klan1_dev_BoardGeneral {

    function __construct(net_klan1_dev_controllerClass &$controllerObject, $boardTitle, $boardDescription = '', $boardKeywords = Array()) {

        $this->init($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $sql_result = k1_sql_query($this->controllerObject->db, $sql_query, false);
        k1_serialize_var($sql_result, $this->controllerObject->getBoardId());
    }

    public function getModuleEditLink() {
        return $this->controllerObject->getModuleEditUrl();
    }

    public function getModuleEditButton($buttonText = "Editar") {
        return k1_get_link_button((sprintf($this->getModuleEditLink(), $this->controllerObject->getBoardUrlParameterValue())), $buttonText);
    }

    public function getModuleDeleteLink() {
        return $this->controllerObject->getModuleDeleteUrl();
    }

    public function getModuleDeleteButton($buttonText = "Borrar") {
        return k1_get_link_button((sprintf($this->getModuleDeleteLink(), $this->controllerObject->getBoardUrlParameterValue())), $buttonText);
    }

    public function getModuleFKDetatilLink() {
        $fkBoardUrl = $this->getfkBoardUrl();
        if ($fkBoardUrl) {
            return k1_get_app_link($this->getfkBoardUrl());
        } else {
            return false;
        }
    }

    public function getModuleFKDetatilButton() {
//        d($url);
//        d($this->controllerObject->getBoardUrlParameterValue());
        $moduleFKDetatilLink = $this->getModuleFKDetatilLink();
        if (!empty($moduleFKDetatilLink)) {
            $url = (sprintf($this->getModuleFKDetatilLink(), "{$this->controllerObject->getBoardUrlParameterValue()}"));
            return k1_get_link_button($url, $this->getFkBoardLabel());
        }
    }

}

class net_klan1_dev_BoardEdit extends net_klan1_dev_BoardGeneral {

    function __construct(net_klan1_dev_controllerClass &$controllerObject, $boardTitle, $boardDescription = '', $boardKeywords = Array()) {

        $this->init($controllerObject, $boardTitle, $boardDescription, $boardKeywords);


        // URL ID change protection
    }

    function initFormAction() {
        parent::initFormAction();
        $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $sql_result = k1_sql_query($this->controllerObject->db, $sql_query, false);
        k1_serialize_var($sql_result, $this->controllerObject->getBoardFormId() . "-sqlResult");
        k1_serialize_var($sql_result, $this->controllerObject->getBoardFormId() . "-sqlOriginalResult");
        k1_serialize_var($this->getBoardParameterKeyArray(), "{$this->controllerObject->getBoardFormId()}-{$this->controllerObject->getBoardUrlParameterValue()}");
    }

    function makeDoAction() {
        /**
         *  SECTION WHEN EDIT FORM IS SUBMITED
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
                    $table_keys_array = k1_unserialize_var("{$this->controllerObject->getBoardFormId()}-{$this->controllerObject->getBoardUrlParameterValue()}");
                    if ($table_keys_array === false) {
                        $controller_errors[] = "Haz intentado actualizar otro registro moficando el codigo HTML " . "{$this->controllerObject->getBoardFormId()}-{$this->controllerObject->getBoardUrlParameterValue()}";
                        $do_check = true;
                    } else {
                        $record_updated = k1_sql_update($this->controllerObject->db, $this->controllerObject->getDbTableMainName(), $form_vars, $table_keys_array, $this->controllerObject->getControllerTableConfig());
                        if ($record_updated !== false) {
                            $actionUrl = sprintf($this->getFormAfterActionUrl(), $this->controllerObject->getBoardUrlParameterValue());
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
//        $this->initFormAction();
        $this->setFormMagicValue(k1_set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        /**
         * TODO: DO NOT use global here!!
         */
        global $form_errors, $controller_errors;
        $controller_errors = k1_unserialize_var($this->controllerObject->getBoardErrorId());
        $form_errors = k1_unserialize_var($this->controllerObject->getBoardFormErrorId());
    }

}
