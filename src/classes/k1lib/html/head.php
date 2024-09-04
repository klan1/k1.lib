<?php

namespace k1lib\html;

/**
 *  <head>
 *      <title></title>
 *  </head>
 */
class head extends tag {

    use append_shotcuts;

    /**
     * @var title
     */
    protected $title;

    function __construct() {
        parent::__construct("head", FALSE);
        $this->append_title();
    }

    /**
     * @return title
     */
    function append_title() {
        $this->title = new title();
        $this->append_child_head($this->title);
        return $this->title;
    }

    function set_title($document_title) {
        $this->title->set_value($document_title);
    }

    public function get_title() {
        return $this->title->get_value();
    }

    /**
     * @return link
     */
    function link_css($href = NULL) {
        $new = new link($href);
        $this->append_child_tail($new);
        return $new;
    }

    /**
     * 
     * @return meta
     */
    function append_meta($name = NULL, $content = NULL) {
        $new = new meta($name, $content);
        $this->append_child_tail($new);
        return $new;
    }

}

