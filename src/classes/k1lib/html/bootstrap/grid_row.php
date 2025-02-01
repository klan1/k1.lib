<?php

namespace k1lib\html\bootstrap;

use k1lib\html\div;
use k1lib\html\bootstrap\grid_cell;

class grid_row extends div {

    use bootstrap_methods;

    /**
     * @var grid_cell[]
     */
    protected $cols;
    protected int $num_cols;

    function __construct($num_cols, $grid_row = NULL, $parent = NULL) {

        $this->parent = $parent;

        parent::__construct("row row-cols-{$num_cols}", NULL);
        if (!empty($this->parent)) {
            $this->append_to($this->parent);
        }

//        if (!empty($grid_row)) {
//            $this->set_attrib("data-grid-row", $grid_row);
//        }

        for ($col = 1; $col <= $num_cols; $col++) {
            $this->cols[$col] = $this->append_cell($col);
        }
    }

    public function copy_clases_to_cols($from) {
        $classes = $this->col($from)->get_attribute('class');
        for ($col = 1; $col <= count($this->cols); $col++) {
            $this->cols[$col]->set_class($classes);
        }
    }

    /**
     * @param integer $col_number
     * @return grid_cell
     */
    public function col($col_number): grid_cell {
        return $this->cell($col_number);
    }

    /**
     * @param integer $col_number
     * @return grid_cell
     */
    public function cell($col_number): grid_cell {
        if (isset($this->cols[$col_number])) {
            return $this->cols[$col_number];
        }
    }

    /**
     * @return div
     */
//    public function expanded() {
//        $this->set_attrib("class", "expanded", TRUE);
//        return $this;
//    }

    /**
     * 
     * @param integer $col_number
     * @param integer $class
     * @param integer $id
     * @return grid_cell
     */
    public function append_cell($col_number = NULL, $class = NULL, $id = NULL) {
        $cell = new grid_cell($col_number, $class, $id);
        $cell->append_to($this);
        return $cell;
    }
}
