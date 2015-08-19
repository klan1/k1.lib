<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * K1 Lib Controller Class
 *
 * PHP version 5.3
 *
 * LICENSE:  
 *
 * @author     Alejandro Trujillo J. <alejo@klan1.com>
 * @copyright  2015 Klan1 Network
 * @license    http://www.klan1.com/licenses/k1-lib-0.1.txt  Klan1 License 0.1
 * @version    0.9
 * @since      File available since Release 0.8
 */
define("K1LIB_CONTROLLER_TYPE_PLAIN", -1);
define("K1LIB_CONTROLLER_TYPE_MAIN", 1);
define("K1LIB_CONTROLLER_TYPE_FOREIGN", 2);
define("K1LIB_CONTROLLER_TYPE_CUSTOM", 3);

define("K1LIB_BOARD_TYPE_VIEW_ALL", 1);
define("K1LIB_BOARD_TYPE_VIEW", 2);
define("K1LIB_BOARD_TYPE_NEW", 3);
define("K1LIB_BOARD_TYPE_EDIT", 4);
define("K1LIB_BOARD_TYPE_DELETE", 5);

define("K1LIB_OEXEC_PHASE_NONE", 0);
define("K1LIB_OEXEC_PHASE_CONSTRUCTION", 1);
define("K1LIB_OEXEC_PHASE_CONFIG", 2);
define("K1LIB_OEXEC_PHASE_LAUNCHING", 3);
define("K1LIB_OEXEC_PHASE_EXECUTING", 4);
define("K1LIB_OEXEC_PHASE_END", 5);

function parseUrlTag($urlString, completeEasyController $contollerObject) {
    $urlString = str_replace("[controller-key]", $contollerObject->getBoardUrlParameterValue(), $urlString);
    $urlString = str_replace("[controller-fk]", $contollerObject->getBoardFkUrlValue(), $urlString);
    $urlString = str_replace("[board]", $contollerObject->getControllerUrlRoot(), $urlString);
    $urlString = str_replace("[board-edit]", $contollerObject->getBoardEditUrl(), $urlString);
    $urlString = str_replace("[board-view]", $contollerObject->getBoardDetailUrl(), $urlString);
    $urlString = str_replace("[board-view-all]", $contollerObject->getBoardTableListUrl(), $urlString);
    $urlString = str_replace("[board-delete]", $contollerObject->getBoardDeleteUrl(), $urlString);
    return $urlString;
}

trait k1_object_execution_control {

    /**
     * @var Int
     */
    private $currentOExecPhase = 0;

    /**
     *
     * @var Int
     */
    private $lastOExecPhase = 0;

    protected function get_object_exec_phase() {
        return $this->currentOExecPhase;
    }

    private function set_object_exec_phase($OExecPhase, $method = __METHOD__) {
        if (($this->currentOExecPhase + 1) === $OExecPhase) {
            $this->lastOExecPhase = $this->currentOExecPhase;
            $this->currentOExecPhase = $OExecPhase;
        } elseif (($this->currentOExecPhase) === $OExecPhase) {
            trigger_error("{$method} Se esta intentando decalrar la misma fase:{$OExecPhase}", E_USER_WARNING);
        } else {
            trigger_error("{$method} Solo se puede anvazar en las fases en orden. Actual:{$this->currentOExecPhase} peticion:{$OExecPhase}", E_USER_ERROR);
        }
    }

    protected function test_object_exec_phase($requiredOExecPhase, $method, $strict = false, $prerequisite = false) {
        if ($strict) {
            if ($requiredOExecPhase !== $this->get_object_exec_phase()) {
                trigger_error("{$method}() llamado en la fase de ejecucion erronea, llamado en fase " . $this->get_object_exec_phase() . " y se esperaba " . $requiredOExecPhase, E_USER_ERROR);
                die();
            }
        } else {
            if (!$prerequisite) {
                if ($this->get_object_exec_phase() < $requiredOExecPhase) {
                    trigger_error("{$method}() llamado en la fase de ejecucion erronea, llamado en fase " . $this->get_object_exec_phase() . " y se debe ser >= " . $requiredOExecPhase, E_USER_ERROR);
                    die();
                }
            } else {
                if ($this->get_object_exec_phase() >= $requiredOExecPhase) {
                    trigger_error("{$method}() llamado en la fase de ejecucion erronea, llamado en fase " . $this->get_object_exec_phase() . " y debe ser < " . $requiredOExecPhase, E_USER_ERROR);
                    die();
                }
            }
        }
    }

    public function phase_contruct() {
        $this->set_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);
    }

    public function phase_config() {
        $this->set_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG);
    }

    public function phase_launching() {
        $this->set_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
    }

    public function phase_executing() {
        $this->set_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);
    }

    public function phase_end() {
        $this->set_object_exec_phase(K1LIB_OEXEC_PHASE_END, __METHOD__);
    }

}

class k1_general_controller_class {

    use k1_object_execution_control;

// EXECUTION CONTROL
//    private
// main vars
    private $controllerID;
    private $controllerType = K1LIB_CONTROLLER_TYPE_PLAIN;
// actual board or action name
// URL vars
    private $controllerUrlValue = false;
    private $controllerUrlName = false;
    private $controllerUrlLevel;
    private $actualUrlLevel;
    private $nextUrlLevel;
    private $controllerUrlRoot;
// SESSION CONTROL
    private $sessionControl = true;
    private $defaultAuthLevelAccess = '0';

    /**
     * Retorn point related
     */
    private $isReturnPoint;

    /*     * *****************
     * 
     * K1LIB_OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    public function __construct() {
        $this->phase_contruct();
// see the max URL Level
        $this->controllerUrlLevel = k1_get_url_level_count() - 1;
        $this->actualUrlLevel = $this->controllerUrlLevel;
        $this->nextUrlLevel = $this->actualUrlLevel + 1;

        $this->initControllerUrl();
    }

    public function setControllerUrlRoot($controllerUrlRoot) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->controllerUrlRoot = $controllerUrlRoot;
    }

    protected function initControllerUrl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__, TRUE);

        $this->controllerUrlName = "url_controller";
        $this->controllerUrlValue = $this->setUrlLevel($this->controllerUrlName, false);
        $this->setControllerUrlRoot(k1_make_url_from_rewrite());
//        d($this->controllerUrlRoot);
        $this->controllerID = k1_get_this_controller_id();
    }

    public function setUrlLevel($urlLevelName, $required) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);

        if (!is_string($urlLevelName)) {
            die("The \$urlLevelName type has to be a String " . __METHOD__);
        }

        $urlLevelValue = k1_set_url_rewrite_var($this->nextUrlLevel, $urlLevelName, $required);

        $this->actualUrlLevel = $this->nextUrlLevel;
        $this->nextUrlLevel++;
        return $urlLevelValue;
    }

    protected function setControllerType($controllerType) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->controllerType = $controllerType;
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setSessionControl($sessionControl) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        $this->sessionControl = $sessionControl;
    }
    public function getSessionControl() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->sessionControl;
    }

    public function setIsReturnPoint($isReturnPoint) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        $this->isReturnPoint = $isReturnPoint;
    }

    public function setDefaultAuthLevelAccess($defaultAuthLevelAccess) {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        $this->defaultAuthLevelAccess = $defaultAuthLevelAccess;
    }

    public function getDefaultAuthLevelAccess() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->defaultAuthLevelAccess;
    }

    public function getControllerType() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerType;
    }

    public function getControllerUrlRoot() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerUrlRoot;
    }

    public function getControllerUrlValue() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerUrlValue;
    }

    public function getControllerUrlName() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerUrlName;
    }

    public function getControllerID() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerID;
    }

    public function clearReturnPoint() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_CONFIG, __METHOD__);

        k1_unset_serialize_var("k1_return_point");
        k1_unset_serialize_var("k1_return_point_id");
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */


    public function getIsReturnPoint() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->isReturnPoint;
    }

    /*     * ******************
     * 
     * K1LIB_OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    public function saveReturnPoint() {
        $this->test_object_exec_phase(K1LIB_OEXEC_PHASE_EXECUTING, __METHOD__);

        if ($this->getIsReturnPoint() === true) {
            k1_serialize_var(k1_make_url_from_rewrite('this'), "k1_return_point");
            k1_serialize_var($this->getControllerID(), "k1_return_point_id");
        }
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
