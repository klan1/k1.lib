<?php

namespace k1lib\crud;

class board_general_class {

    use \k1lib\oexec\object_execution_control;

    public $controllerObject;
    public $HtmlTableObject;
    // Board standar display config
    private $boardInternalName = NULL;
    private $boardTitle = NULL;
    private $boardDescripction = NULL;
    private $boardKeyWords = NULL;
    private $singleItemName = NULL;
    // SQL Related
    private $boardSqlWhereCondition = "";
    private $boardParameterKeyArray = Array();
    // FK related
    private $fkBoardUrl = NULL;
    private $fkBoardLabel = NULL;
    // Form related
    private $formActionUrl = NULL;
    private $formAfterActionUrl = Array(
        'delete' => '[board-view-all]',
        'new' => '[board-view]',
        'edit' => '[board-view]',
    );
    private $formMagicValue = NULL;
    private $formSubmitLabel = NULL;

    /**
     * 
     */
    /*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {
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
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->boardInternalName = $boardInternalName;
    }

    public function getBoardSqlWherefromParameters($url_parameter = "") {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        if (empty($this->boardSqlWhereCondition)) {
            if (empty($url_parameter)) {
                $url_parameter = $this->controllerObject->getBoardUrlParameterValue();
            }
            $this->boardParameterKeyArray = \k1lib\sql\table_url_text_to_keys($url_parameter, $this->controllerObject->getControllerTableConfig());
            $this->boardSqlWhereCondition = \k1lib\sql\table_keys_to_where_condition($this->boardParameterKeyArray, $this->controllerObject->getControllerTableConfig());
//            d($this->boardSqlWhereCondition);
        }
        return $this->boardSqlWhereCondition;
    }

    public function setBoardTitle($boardTitle) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->boardTitle = $boardTitle;
    }

    public function setBoardDescripction($boardDescripction) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->boardDescripction = $boardDescripction;
    }

    public function setBoardKeyWords($boardKeyWords) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->boardKeyWords = $boardKeyWords;
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setFormActionUrl($formActionUrl, $formSubmitLabel) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
//        $this->formActionUrl = \k1lib\urlrewrite\url_manager::get_app_link("{$this->controllerObject->getBoardRootUrl()}/$formActionUrl");
//        $formActionUrl = $this->controllerObject->parseUrlTag($formActionUrl);
//        d($this->controllerObject->getBoardRootUrl());
        $this->formActionUrl = \k1lib\urlrewrite\url_manager::get_app_link("{$this->controllerObject->getBoardRootUrl()}/$formActionUrl");
        $this->formSubmitLabel = $formSubmitLabel;
    }

    public function setFormAfterActionUrl($formAfterActionUrl, $source) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $formAfterActionUrl = \k1lib\crud\parseUrlTag($formAfterActionUrl, $this->controllerObject);
        $this->formAfterActionUrl[$source] = $formAfterActionUrl;
    }

    public function getFormAfterActionUrl($source = NULL) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        if (!empty($source) && isset($this->formAfterActionUrl[$source])) {
            return \k1lib\crud\parseUrlTag($this->formAfterActionUrl[$source], $this->controllerObject);
        } else {
            return $this->controllerObject->getControllerUrlRoot();
        }
    }

    public function setFormMagicValue($formMagicValue) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->formMagicValue = $formMagicValue;
    }

    public function setfkBoardUrl($fkBoardUrl) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->fkBoardUrl = $fkBoardUrl;
    }

    public function setFkBoardLabel($fkBoardLabel) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->fkBoardLabel = $fkBoardLabel;
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    public function initFormAction() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        // MAGIC VALUE
        $this->setFormMagicValue(\k1lib\common\set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        \k1lib\common\unset_serialize_var($this->controllerObject->getBoardFormId());
    }

    public function getBoardInternalName() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardInternalName;
    }

    public function getFormActionUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->formActionUrl;
    }

    public function getFormSubmitLabel() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->formSubmitLabel;
    }

    public function getFormMagicValue() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->formMagicValue;
    }

    public function getSingleItemName() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->singleItemName;
    }

    public function setSingleItemName($singleItemName) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        $this->singleItemName = $singleItemName;
    }

    public function getBoardTitle() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardTitle;
    }

    public function getBoardDescripction() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardDescripction;
    }

    public function getBoardKeyWords() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardKeyWords;
    }

    public function getBoardParameterKeyArray() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->boardParameterKeyArray;
    }

    public function getfkBoardUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->fkBoardUrl;
    }

    public function getFkBoardLabel() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->fkBoardLabel;
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    public function getControllerBackLink($customBackLink = "") {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        return $this->controllerObject->getBoardTableListUrl();
//        switch ($this->controllerObject->getControllerType()) {
//            case \k1lib\crud\CONTROLLER_TYPE_MAIN:
//                break;
//            case \k1lib\crud\CONTROLLER_TYPE_FOREIGN:
//                return \k1lib\urlrewrite\url_manager::get_app_link($this->getfkBoardUrl());
//                break;
//            default:
//                die("There is no Board Type Defined on " . __CLASS__);
//        }
    }

    public function getControllerBackButton($customBackLink = NULL, $buttonText = "Volver", $mini = TRUE, $inline = TRUE) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        if (empty($customBackLink)) {
            $backUrl = $this->getControllerBackLink();
        } else {
            $backUrl = $customBackLink;
        }
        return \k1lib\html\get_link_button($backUrl, $buttonText, $mini, $inline);
    }

    public function getControllerSubmitButton($buttonText = "Enviar", $mini = TRUE, $inline = TRUE) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        $label = $this->getFormSubmitLabel();
        // BLANK input name for not problems 
        $button_object = new \k1lib\html\input_tag("submit", "submit", $buttonText, "button success fi-check");

        if ($inline) {
            $button_object->set_attrib("class", "inline", TRUE);
        }
        if ($mini) {
            $button_object->set_attrib("class", "tiny", TRUE);
        }
        $button_object->set_attrib("id", "send-data");
        return $button_object->generate_tag();
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

class BoardNew extends board_general_class {
    /*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {
        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
    }

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

    function initFormAction() {
        parent::initFormAction();

        if ($this->controllerObject->getControllerType() == \k1lib\crud\CONTROLLER_TYPE_FOREIGN) {
            $this->boardParameterKeyArray = $this->controllerObject->getBoardFkUrlValueArray();
//            \d($this->boardParameterKeyArray);
            if (!empty($this->boardParameterKeyArray)) {
                \k1lib\common\serialize_var($this->boardParameterKeyArray, "{$this->controllerObject->getBoardFormId()}-fkData");
                \k1lib\common\serialize_var($this->boardParameterKeyArray, $this->controllerObject->getBoardFormId());
            }
        }
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    function makeDoAction() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
        /**
         *  BOARD WHEN EDIT FORM IS SUBMITED
         */
        // check and serialize post vars
        $controller_errors = array();
        $fkData = \k1lib\common\unserialize_var("{$this->controllerObject->getBoardFormId()}-fkData");
        if (!empty($fkData)) {
            $_POST = $fkData + $_POST;
        }
        $form_vars = \k1lib\forms\check_all_incomming_vars($_POST, $this->controllerObject->getBoardFormId());

        /**
         * FK SEARCH SYSTEM
         */
        if (isset($_GET['search_in'])) {
            $search_in = \k1lib\forms\check_single_incomming_var($_GET['search_in']);
            if ($search_in !== NULL) {
                $search_url = $search_in . "/?from=" . urlencode(\k1lib\urlrewrite\url_manager::get_app_link($this->controllerObject->getBoardRootUrl()));
                \k1lib\html\html_header_go($search_url);
            }
        }
        /**
         * END FK SEARCH SYSTEM
         */
        $form_errors = array();
        if (isset($form_vars['magic_value'])) {
            //Magic test 
            $magic_test = \k1lib\common\check_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}", $form_vars['magic_value']);

            if ($magic_test == TRUE) {
                //remove the magic value from the data array
//                unset($form_vars['magic_value']);
                //php check for the data
                $form_vars = \k1lib\common\clean_array_with_guide($form_vars, $this->controllerObject->getControllerTableConfig());
                $form_errors = \k1lib\forms\form_check_values($form_vars, $this->controllerObject->getControllerTableConfig(), $this->controllerObject->db);
                if ($form_errors === FALSE) {
                    $last_inserted_id = \k1lib\sql\sql_insert($this->controllerObject->db, $this->controllerObject->getDbTableMainName(), $form_vars);

                    if ($last_inserted_id) {
//                        \k1lib\html\html_header_go(\k1lib\urlrewrite\url_manager::make_url_from_rewrite(-2));
                        // TODO: implement the after action behavior
                        // UNSET ALL FOR NO FUTURE PROBLEMS ;)
                        \k1lib\common\unset_serialize_var($this->controllerObject->getBoardFormId());
                        \k1lib\common\unset_serialize_var($this->controllerObject->getBoardErrorId());
                        \k1lib\common\unset_serialize_var($this->controllerObject->getBoardFormErrorId());
                        \k1lib\common\unset_serialize_var("{$this->controllerObject->getBoardFormId()}-fkData");
//                        \d($this->getFormAfterActionUrl('new'));
//                        exit;
                        if (is_numeric($last_inserted_id)) {
                            $after_action_url = sprintf($this->getFormAfterActionUrl('new'), $last_inserted_id);
                        } else {
                            $new_text_key = \k1lib\sql\table_keys_to_text($form_vars, $this->controllerObject->getControllerTableConfig());
                            $after_action_url = sprintf($this->getFormAfterActionUrl('new'), $new_text_key);
                        }
                        \k1lib\html\html_header_go($after_action_url);
//                        \k1lib\html\html_header_go($this->controllerObject->getControllerUrlRoot() . "/view-all");
                    } else {
                        $do_check = TRUE;
                        $controller_errors[] = "No se ha podido insertar el registro.";
                        $db_error = $this->controllerObject->db->errorInfo();
                        $controller_errors[] = $db_error[2];
//                        die;
                    }
                } else {
//                    d($form_errors);
//                    die('si errores');

                    $do_check = TRUE;
                }
            } else {
                $do_check = TRUE;
                $controller_errors[] = "Bad Magic !!";
            }
        } else {
            $do_check = TRUE;
            $controller_errors[] = "No Magic !!";
        }
        if ($do_check) {
            \k1lib\common\serialize_var($controller_errors, $this->controllerObject->getBoardErrorId());
            \k1lib\common\serialize_var($form_errors, $this->controllerObject->getBoardFormErrorId());
            $form_check_url = "{$this->controllerObject->getBoardRootUrl()}/check";
//            d($form_check_url);
            \k1lib\html\html_header_go($form_check_url);
            exit;
        }
    }

    function makeCheckAction() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);

        $this->setFormMagicValue(\k1lib\common\set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        $this->setFormActionUrl("do", "Insertar");

        /**
         * TODO: DO NOT use global here!!
         */
        global $form_errors, $controller_errors;
        $controller_errors = \k1lib\common\unserialize_var($this->controllerObject->getBoardErrorId());
        if (isset($_GET['silent']) && $_GET['silent'] == '1') {
            $form_errors = [];
        } else {
            $form_errors = \k1lib\common\unserialize_var($this->controllerObject->getBoardFormErrorId());
        }

        /**
         * FK SEARCH SYSTEM
         */
        $search_util_result = \k1lib\common\unserialize_var("search-util-result");
        if ($search_util_result !== FALSE) {
            $form_data = \k1lib\common\unserialize_var($this->controllerObject->getBoardFormId());
            $form_data = $search_util_result + $form_data;
            \k1lib\common\serialize_var($form_data, $this->controllerObject->getBoardFormId());
        }
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

class BoardViewAll extends board_general_class {
    /*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {

        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $this->HtmlTableObject = new \k1lib\html\html_table_with_table_config($this->controllerObject->getBoardID());
        $this->controllerObject->setUrlLevel("table-action", FALSE);
        $this->controllerObject->setUrlLevel("page-number", FALSE);
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setBoardMode($mode = null, $parameter1 = null, $parameter2 = null, \k1lib\db\handler $db = null) {
        if (empty($mode)) {
            $mode = "table";
        }
        switch ($mode) {
            case "table":
                $this->setBoardTableMode($parameter1);
                break;
            case "sql":
                $this->setSqlMode($parameter1, $parameter2, $db);
                break;
            default:
                trigger_error("You hasn't choose a valid table type", E_USER_ERROR);
                break;
        }
    }

    public function setBoardTableMode($sql_filer) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);

        $tableSQLFilter = $this->controllerObject->getDbTableMainSQLFilter();

        if (!empty($tableSQLFilter)) {
            $tableSQLFilter = $tableSQLFilter . " AND ($sql_filer)";
        } else {
            $tableSQLFilter = $sql_filer;
        }

        switch ($this->controllerObject->getControllerType()) {
            case \k1lib\crud\CONTROLLER_TYPE_MAIN:
                $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()}";
                if ($tableSQLFilter) {
                    $sql_query .= " WHERE $tableSQLFilter";
                }
                break;
            case \k1lib\crud\CONTROLLER_TYPE_FOREIGN:
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

    public function setSqlMode($sql, $tablesToGetConfig, \k1lib\db\handler $db) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $tablesToGetConfigArray = explode(",", $tablesToGetConfig);
        $tablesConfigArray = Array();
        foreach ($tablesToGetConfigArray as $tableName) {
            $configTable = \k1lib\sql\get_table_config($db, $tableName);
        }
        $sql = sprintf($sql, $this->controllerObject->getForeignKeyWhereCondition());
        $this->HtmlTableObject->modeSQL($sql, $this->controllerObject->getControllerTableConfig());
    }

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

    /**
     * 
     * @param type $sql
     * @param type $tablesToGetConfig
     * 
     * TODO: This wont work as it should !!
     */
}

class BoardDelete extends board_general_class {
    /*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {

        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $sql_query = "DELETE FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $record_deleted = \k1lib\sql\sql_query($this->controllerObject->db, $sql_query, FALSE);
        if ($record_deleted === NULL) {
            \k1lib\html\html_header_go($this->getFormAfterActionUrl('delete'));
//            \k1lib\html\html_header_go($this->controllerObject->getBoardRootUrl() . "/view-all");
        } else {
            global $controller_errors;
            $controller_errors[] = "No se pude borrar el registro, posiblemente esta en uso";
        }
    }

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
}

class BoardView extends board_general_class {
    /*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {

        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);
        $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $sql_result = \k1lib\sql\sql_query($this->controllerObject->db, $sql_query, FALSE);
        \k1lib\common\serialize_var($sql_result, $this->controllerObject->getBoardId());
    }

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

    public function getBoardEditLink() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->controllerObject->getBoardEditUrl();
    }

    public function getBoardEditButton($buttonText = "Editar") {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return \k1lib\html\get_link_button((sprintf($this->getBoardEditLink(), $this->controllerObject->getBoardUrlParameterValue())), $buttonText);
    }

    public function getBoardDeleteLink() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->controllerObject->getBoardDeleteUrl();
    }

    public function getBoardDeleteButton($buttonText = "Borrar") {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return \k1lib\html\get_link_button((sprintf($this->getBoardDeleteLink(), $this->controllerObject->getBoardUrlParameterValue())), $buttonText);
    }

    public function getControllerFKDetatilLink() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        $fkBoardUrl = $this->getfkBoardUrl();
        if ($fkBoardUrl) {
            return \k1lib\urlrewrite\url_manager::get_app_link($this->getfkBoardUrl());
        } else {
            return FALSE;
        }
    }

    public function getControllerFKDetatilButton() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
//        d($url);
//        d($this->controllerObject->getBoardUrlParameterValue());
        $controllerFKDetatilLink = $this->getControllerFKDetatilLink();
        if (!empty($controllerFKDetatilLink)) {
            $url = (sprintf($this->getControllerFKDetatilLink(), "{$this->controllerObject->getBoardUrlParameterValue()}"));
            return \k1lib\html\get_link_button($url, $this->getFkBoardLabel());
        }
    }

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
}

class BoardEdit extends board_general_class {
    /*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    function __construct(general_controller_class $controllerObject, $boardTitle, $boardInternalName, $boardDescription = '', $boardKeywords = Array()) {

        parent::__construct($controllerObject, $boardTitle, $boardDescription, $boardKeywords);


        // URL ID change protection
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    function initFormAction() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        parent::initFormAction();
        $sql_query = "SELECT * FROM {$this->controllerObject->getDbTableMainName()} WHERE " . $this->getBoardSqlWherefromParameters();
        $sql_result = \k1lib\sql\sql_query($this->controllerObject->db, $sql_query, FALSE);
        \k1lib\common\serialize_var($sql_result, $this->controllerObject->getBoardFormId() . "-sqlResult");
        \k1lib\common\serialize_var($sql_result, $this->controllerObject->getBoardFormId() . "-sqlOriginalResult");
        \k1lib\common\serialize_var($this->getBoardParameterKeyArray(), "{$this->controllerObject->getBoardFormId()}-tableKeys");
    }

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

    function makeDoAction() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
        /**
         *  BOARD WHEN EDIT FORM IS SUBMITED
         */
        // check and serialize post vars

        $form_vars = \k1lib\forms\check_all_incomming_vars($_POST, $this->controllerObject->getBoardFormId() . "-sqlResult");

        /**
         * FK SEARCH SYSTEM
         */
        $search_in = \k1lib\forms\check_single_incomming_var($_GET['search_in']);
        if ($search_in !== NULL) {
            $search_url = $search_in . "/?from=" . urlencode(\k1lib\urlrewrite\url_manager::get_app_link($this->controllerObject->getBoardRootUrl()));
            \k1lib\html\html_header_go($search_url);
        }

        if (isset($form_vars['magic_value'])) {
            //Magic test 

            $magic_test = \k1lib\common\check_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}", $form_vars['magic_value']);
            if ($magic_test == TRUE) {
                $form_vars = \k1lib\common\clean_array_with_guide($form_vars, $this->controllerObject->getControllerTableConfig());
                $form_errors = \k1lib\forms\form_check_values($form_vars, $this->controllerObject->getControllerTableConfig(), $this->controllerObject->db);
                $do_check = FALSE;
                if ($form_errors === FALSE) {
                    //unserialize the keys array to append to the data array arrived by POST
                    $table_keys_array = \k1lib\common\unserialize_var("{$this->controllerObject->getBoardFormId()}-tableKeys");
                    if ($table_keys_array === FALSE) {
                        $controller_errors[] = "Haz intentado actualizar otro registro moficando el codigo HTML " . "{$this->controllerObject->getBoardFormId()}-tableKeys";
                        $do_check = TRUE;
                    } else {
                        $record_updated = \k1lib\sql\sql_update($this->controllerObject->db, $this->controllerObject->getDbTableMainName(), $form_vars, $table_keys_array, $this->controllerObject->getControllerTableConfig());
                        if ($record_updated !== FALSE) {
                            $actionUrl = sprintf($this->getFormAfterActionUrl('edit'), $this->controllerObject->getBoardUrlParameterValue());

                            // UNSET ALL FOR NO FUTURE PROBLEMS ;)
                            \k1lib\common\unset_serialize_var($this->controllerObject->getBoardFormId());
                            \k1lib\common\unset_serialize_var($this->controllerObject->getBoardErrorId());
                            \k1lib\common\unset_serialize_var($this->controllerObject->getBoardFormErrorId());
                            \k1lib\common\unset_serialize_var($this->controllerObject->getBoardFormId() . "-sqlResult");
                            \k1lib\common\unset_serialize_var($this->controllerObject->getBoardFormId() . "-sqlOriginalResult");
                            \k1lib\common\unset_serialize_var($this->controllerObject->getBoardFormId() . "-tableKeys");
                            \k1lib\html\html_header_go($actionUrl, TRUE);
                        } else {
                            $controller_errors[] = "No se pudo hacer el update, Posiblemente los datos no han cambiado";
                            $do_check = TRUE;
                        }
                    }
                } else {
                    $do_check = TRUE;
                }
            } else {
                $do_check = TRUE;
                $controller_errors[] = "Bad Magic !!";
            }
        } else {
            $do_check = TRUE;
            $controller_errors[] = "No Magic !!";
        }
        if ($do_check) {
            \k1lib\common\serialize_var($controller_errors, $this->controllerObject->getBoardErrorId());
            \k1lib\common\serialize_var($form_errors, $this->controllerObject->getBoardFormErrorId());
            $form_check_url = "{$this->controllerObject->getBoardRootUrl()}/check";
//            echo \k1lib\html\get_link_button($form_check_url, $form_check_url);
            \k1lib\html\html_header_go($form_check_url);
        }
    }

    function makeCheckAction() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__, TRUE);
//        $this->initFormAction();
        $this->setFormMagicValue(\k1lib\common\set_magic_value("k1-form-{$this->controllerObject->getBoardFormId()}"));
        /**
         * TODO: DO NOT use global here!!
         */
        global $form_errors, $controller_errors;
        $controller_errors = \k1lib\common\unserialize_var($this->controllerObject->getBoardErrorId());

        if (isset($_GET['silent']) && $_GET['silent'] == '1') {
            $form_errors = [];
        } else {
            $form_errors = \k1lib\common\unserialize_var($this->controllerObject->getBoardFormErrorId());
        }

        /**
         * FK SEARCH SYSTEM
         */
        $search_util_result = \k1lib\common\unserialize_var("search-util-result");
        if ($search_util_result !== FALSE) {
            $form_data = \k1lib\common\unserialize_var($this->controllerObject->getBoardFormId() . "-sqlResult");
            $form_data = $search_util_result + $form_data;
            \k1lib\common\serialize_var($form_data, $this->controllerObject->getBoardFormId() . "-sqlResult");
        }
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
