<?php

namespace k1lib\html;

/**
 * This is the main object that will holds all the HTML document.
 * <html>  
 *  <head>
 *      <title></title>
 *  </head>
 *  <body>
 *  </body>
 * </html> 
 */
class html extends tag {

    use append_shotcuts;

    /**
     * @var head
     */
    protected $head = NULL;

    /**
     * @var body
     */
    private $body = NULL;

    function __construct($lang = "en") {
        parent::__construct("html", FALSE);
        $this->set_attrib("lang", $lang);
        $this->append_head();
        $this->append_body();
    }

    function append_head() {
        $this->head = new head();
        $this->append_child($this->head);
    }

    function append_body() {
        $this->body = new body();
        $this->append_child($this->body);
    }

    /**
     * @return head
     */
    function head() {
        return $this->head;
    }

    /**
     * @return body
     */
    function body() {
        return $this->body;
    }

}

