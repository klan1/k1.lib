<?php

namespace k1lib\html\foundation;

class grid extends \k1lib\html\div {

    use foundation_methods;

    /**
     * @var \k1lib\html\tag
     */
    protected $parent;

    /**
     * @var grid_cell[]
     */
    protected $rows = [];
    protected $num_rows;
    protected $num_cols;

    public function __construct($num_rows, $num_cols, \k1lib\html\tag $parent = NULL) {
        $this->parent = $parent;

        $this->num_rows = 0;
        $this->num_cols = $num_cols;

        if (empty($this->parent)) {
            parent::__construct();
            for ($row = 1; $row <= $num_rows; $row++) {
                $this->append_row($num_cols, $row, $this);
            }
        } else {
//            $this->append_to($this->parent);
            $this->link_value_obj($parent);
            for ($row = 1; $row <= $num_rows; $row++) {
                $this->append_row($num_cols, $row, $this->parent);
            }
            return $parent;
        }
    }

    /**
     * @param integer $row_number
     * @return \k1lib\html\foundation\grid_row
     */
    public function row($row_number) {
        if (isset($this->rows[$row_number])) {
            return $this->rows[$row_number];
        }
    }

    /**
     * 
     * @param int $num_cols
     * @param int $grid_row
     * @return \k1lib\html\foundation\grid_row
     */
    public function append_row($num_cols = NULL, $grid_row = NULL, $parent = NULL) {
        if ($num_cols === NULL) {
            $num_cols = $this->num_cols;
        }
        $row = new grid_row($num_cols, $grid_row, $parent);
        $this->rows[++$this->num_rows] = $row;
        return $row;
    }
}
