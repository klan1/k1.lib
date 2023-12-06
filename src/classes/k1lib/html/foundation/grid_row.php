<?php

namespace k1lib\html\foundation;

class grid_row extends \k1lib\html\div {

    use foundation_methods;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var grid_cell[]
     */
    protected $cols = [];

    function __construct($num_cols, $grid_row = NULL, $parent = NULL) {

        $this->parent = $parent;

        parent::__construct("grid-x row-{$grid_row}", NULL);
        if (!empty($this->parent)) {
            $this->append_to($this->parent);
        }

        if (!empty($grid_row)) {
            $this->set_attrib("data-grid-row", $grid_row);
        }

        for ($col = 1; $col <= $num_cols; $col++) {
            $this->num_cols[$col] = $this->append_cell($col);
        }
    }

    /**
     * @param integer $col_number
     * @return \k1lib\html\foundation\grid_cell
     */
    public function col($col_number) {
        return $this->cell($col_number);
    }

    /**
     * @param integer $col_number
     * @return \k1lib\html\foundation\grid_cell
     */
    public function cell($col_number) {
        if (isset($this->num_cols[$col_number])) {
            return $this->num_cols[$col_number];
        }
    }

    /**
     * @return \k1lib\html\div
     */
    public function expanded() {
        $this->set_attrib("class", "expanded", TRUE);
        return $this;
    }

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
