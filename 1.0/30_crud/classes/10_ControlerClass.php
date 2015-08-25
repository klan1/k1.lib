<?php

namespace k1lib\crud;

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
class general_controller_class {

    use \k1lib\oexec\object_execution_control;

// EXECUTION CONTROL
//    private
// main vars
    private $controllerID;
    private $controllerType;
// actual board or action name
// URL vars
    private $controllerUrlValue = FALSE;
    private $controllerUrlName = FALSE;
    private $controllerUrlLevel;
    private $actualUrlLevel;
    private $nextUrlLevel;
    private $controllerUrlRoot;
// SESSION CONTROL
    private $sessionControl = TRUE;
    private $defaultAuthLevelAccess = '0';

    /**
     * Retorn point related
     */
    private $isReturnPoint;

    /*     * *****************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONSTRUCTION
     * 
     * ***************** */

    public function __construct() {
        $this->phase_contruct();
// see the max URL Level
        $this->controllerType = \k1lib\crud\CONTROLLER_TYPE_PLAIN;
        $this->controllerUrlLevel = \k1lib\urlrewrite\url_manager::get_url_level_count() - 1;
        $this->actualUrlLevel = $this->controllerUrlLevel;
        $this->nextUrlLevel = $this->actualUrlLevel + 1;

        $this->initControllerUrl();
    }

    public function setControllerUrlRoot($controllerUrlRoot) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->controllerUrlRoot = $controllerUrlRoot;
    }

    protected function initControllerUrl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__, TRUE);

        $this->controllerUrlName = "url_controller";
        $this->controllerUrlValue = $this->setUrlLevel($this->controllerUrlName, FALSE);
        $this->setControllerUrlRoot(\k1lib\urlrewrite\url_manager::make_url_from_rewrite());
//        d($this->controllerUrlRoot);
        $this->controllerID = \k1lib\urlrewrite\url_manager::get_this_controller_id();
    }

    public function setUrlLevel($urlLevelName, $required) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);

        if (!is_string($urlLevelName)) {
            die("The \$urlLevelName type has to be a String " . __METHOD__);
        }

        $urlLevelValue = \k1lib\urlrewrite\url_manager::set_url_rewrite_var($this->nextUrlLevel, $urlLevelName, $required);

        $this->actualUrlLevel = $this->nextUrlLevel;
        $this->nextUrlLevel++;
        return $urlLevelValue;
    }

    protected function setControllerType($controllerType) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);
        $this->controllerType = $controllerType;
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_CONFIG
     * 
     * ***************** */

    public function setSessionControl($sessionControl) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->sessionControl = $sessionControl;
    }

    public function getSessionControl() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->sessionControl;
    }

    public function setIsReturnPoint($isReturnPoint) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->isReturnPoint = $isReturnPoint;
    }

    public function setDefaultAuthLevelAccess($defaultAuthLevelAccess) {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        $this->defaultAuthLevelAccess = $defaultAuthLevelAccess;
    }

    public function getDefaultAuthLevelAccess() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->defaultAuthLevelAccess;
    }

    public function getControllerType() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerType;
    }

    public function getControllerUrlRoot() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerUrlRoot;
    }

    public function getControllerUrlValue() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerUrlValue;
    }

    public function getControllerUrlName() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerUrlName;
    }

    public function getControllerID() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);
        return $this->controllerID;
    }

    public function clearReturnPoint() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG, __METHOD__);

        \k1lib\forms\unset_serialize_var("k1_return_point");
        \k1lib\forms\unset_serialize_var("k1_return_point_id");
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_LAUNCHING
     * 
     * ***************** */

    public function getIsReturnPoint() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
        return $this->isReturnPoint;
    }

    /*     * ******************
     * 
     * \k1lib\oexec\OEXEC_PHASE_EXECUTING 
     * 
     * ***************** */

    public function saveReturnPoint() {
        $this->test_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);

        if ($this->getIsReturnPoint() === TRUE) {
            \k1lib\forms\serialize_var(\k1lib\urlrewrite\url_manager::make_url_from_rewrite('this'), "k1_return_point");
            \k1lib\forms\serialize_var($this->getControllerID(), "k1_return_point_id");
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
