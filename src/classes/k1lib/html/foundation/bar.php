<?php

namespace k1lib\html\foundation;

class bar extends \k1lib\html\div {

    /**
     * @var string
     */
    protected $type;

    /**
     * @var \k1lib\html\div
     */
    protected $left = null;

    /**
     * @var \k1lib\html\div
     */
    protected $right = null;

    function __construct($type, $id = NULL) {
        $this->type = $type;
        parent::__construct("{$type}-bar", $id);
        $this->left = new \k1lib\html\div("{$type}-bar-left");
        $this->right = new \k1lib\html\div("{$type}-bar-right");

        $this->left->append_to($this);
        $this->right->append_to($this);
    }

    /**
     * @return \k1lib\html\div
     */
    public function left() {
        return $this->left;
    }

    /**
     * @return \k1lib\html\div
     */
    public function right() {
        if (empty($this->right)) {
            $this->right = new \k1lib\html\div("{$this->type}-bar-right");
        }
        return $this->right;
    }
}
