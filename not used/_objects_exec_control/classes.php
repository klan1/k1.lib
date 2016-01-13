<?php

namespace k1lib\oexec;


trait object_execution_control {

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

    protected function test_object_exec_phase($requiredOExecPhase, $method, $strict = FALSE, $prerequisite = FALSE) {
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
        $this->set_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONSTRUCTION, __METHOD__);
    }

    public function phase_config() {
        $this->set_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_CONFIG);
    }

    public function phase_launching() {
        $this->set_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_LAUNCHING, __METHOD__);
    }

    public function phase_executing() {
        $this->set_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_EXECUTING, __METHOD__);
    }

    public function phase_end() {
        $this->set_object_exec_phase(\k1lib\oexec\OEXEC_PHASE_END, __METHOD__);
    }

}
