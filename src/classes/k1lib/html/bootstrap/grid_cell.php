<?php

namespace k1lib\html\bootstrap;

use k1lib\html\div;

class grid_cell extends div {

    use bootstrap_methods;

    protected $row_number = 0;

    /**
     * @param integer $col_number
     * @param integer $class
     * @param integer $id
     *  */
    public function __construct($col_number = NULL, $class = 'col', $id = NULL) {
        parent::__construct($class, NULL);
//        $this->set_attrib("data-grid-cell", $col_number);
    }

    // change the default behaivor of append from FALSE to TRUE
    public function set_class($class, $append = TRUE) {
        parent::set_class($class, $append);
        return $this;
    }

    /**
     * @return div
     */
//    public function end() {
//        $this->set_attrib("class", "", TRUE);
//        return $this;
//    }

    /**
     * @param integer $num_rows
     * @param integer $num_cols
     * @return grid
     */
    public function append_grid($num_rows, $num_cols) {
        $grid = new grid($num_rows, $num_cols, $this);
        return $grid;
    }

    public function append_row($num_cols) {
        $row = new grid_row($num_cols, ++$this->row_number, $this);
        return $row;
    }
}
