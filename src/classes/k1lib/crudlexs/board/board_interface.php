<?php

/**
 * @license Apache-2.0
 * @package k1lib
 * @subpackage crudlexs\board
 * Interface defining the lifecycle methods for CRUD board implementations.
 */

namespace k1lib\crudlexs\board;

/**
 * Interface for CRUD board implementations.
 * Defines the lifecycle methods for board operations.
 *
 * @package k1lib\crudlexs\board
 */
interface board_interface {

    /**
     * Initializes the board and its resources.
     *
     * @return mixed
     */
    public function start_board();

    /**
     * Executes the main board logic.
     *
     * @return mixed
     */
    public function exec_board();

    /**
     * Finalizes the board and cleans up resources.
     *
     * @return mixed
     */
    public function finish_board();
}
