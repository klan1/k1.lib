<?php

namespace k1lib\html\foundation;

class callout extends \k1lib\html\div {

    use foundation_methods;

    /**
     * @var grid_cell[]
     */
    protected $cols = [];
    protected $title = "";
    protected $message = "no message";
    protected $margin = '';

    function __construct($message = NULL, $title = NULL, $closable = TRUE, $type = "primary") {
        $this->message = $message;
        $this->title = $title;

        parent::__construct("callout", NULL);
        $this->set_attrib("data-closable", TRUE);
        if ($closable) {
            $close_button = new \k1lib\html\button(NULL, "close-button");
            $close_button->set_attrib("data-close", TRUE);
            $close_button->set_attrib("aria-label", "Close reveal");
            $close_button->append_span()->set_attrib("aria-hidden", TRUE)->set_value("&times;");
            $this->append_child_tail($close_button);
        }

        $this->set_class($type);
    }

    public function set_class($class, $append = FALSE) {
        if ($append === FALSE) {
            $class = "callout {$class}";
        }
        parent::set_class($class, $append);
    }

    public function set_margin($margin) {
        $this->margin = $margin;
    }

    public function get_message() {
        return $this->message;
    }

    public function set_message($message) {
        $this->message = $message;
    }

    function get_title() {
        return $this->title;
    }

    function set_title($title) {
        $this->title = $title;
    }

    public function generate($with_childs = \TRUE, $n_childs = 0) {
        if (!empty($this->title)) {
            $h6 = new \k1lib\html\h6($this->title);
        } else {
            $h6 = "";
        }

        $this->set_value("{$h6}{$this->message}");

        if (!empty($this->margin)) {
            $this->set_attrib("style", "margin: {$this->margin}");
        }

        return parent::generate($with_childs, $n_childs);
    }
}
